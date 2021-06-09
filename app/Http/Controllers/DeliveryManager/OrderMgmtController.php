<?php
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\DeliveryManager;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	
	use Validator;
	
	use Session;
	
	use Mail;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use App\Reports;
	
	use App\Settings;
	
	use Excel;
	
	use Response;
	
	use File;
	
	use Image;
	use Lzq\Mqtt\SamMessage;
	use Lzq\Mqtt\SamConnection;
	use DateTime;
	class OrderMgmtController extends Controller
	{
		
		public function __construct(){
			parent::__construct();
			$this->setLanguageLocalDeliveryManager();
			if(Session::has('DelMgrSessId') == 1){ } else{ return Redirect::to('delivery-manager-login'); }
		}
		
		/** send notification starts**/
		
		
		// function makes curl request to firebase servers
		
		private  function sendPushNotification($fields,$key) { 
			
			$data = json_encode($fields);
			//FCM API end-point
			$url = 'https://fcm.googleapis.com/fcm/send';
			//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
			$server_key = $key;
			//header with content_type api key
			$headers = array(
			'Content-Type:application/json',
			'Authorization:key='.$server_key
			);
			//CURL request to route notification to FCM connection server (provided by Google)
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$result = curl_exec($ch);
			
			if ($result === FALSE) {
				die('Oops! FCM Send Error: ' . curl_error($ch));
			}
			curl_close($ch);
			
			return $result;
			
		}
		/** send notification  ends**/
		
		public function deals_all_orders(){
			DB::table('gr_order')->where('gr_order.ord_payment_status','=','Success')->where('gr_order.ord_status','=','4')->where('gr_order.ord_task_status','=','1')->where('gr_order.ord_agent_acpt_status','=','1')->where('gr_order.ord_delmgr_id','=',Session::get('DelMgrSessId'))->where('gr_order.ord_agent_acpt_read_status','=','0')->where('gr_order.ord_self_pickup','!=','1')->update(['ord_agent_acpt_read_status' => '1']);
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$from_date  	= Input::get('from_date');
			$to_date		= Input::get('to_date');
			$orderdetails 	= Reports::getassigned_reports($from_date, $to_date);
			$page_title		= (Lang::has(Session::get('DelMgr_lang_file').'.DELIVERY_ORDER_MGMT')) ? trans(Session::get('DelMgr_lang_file').'.DELIVERY_ORDER_MGMT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELIVERY_ORDER_MGMT');
			return view('DeliveryManager.reports.manage_orders')->with('pagetitle', $page_title)->with('orderdetails', $orderdetails)->with('from_date', $from_date)->with('to_date', $to_date);
		}
		
		public function manage_orders(){
			/*DB::table('gr_order')->where('gr_order.ord_payment_status','=','Success')->where('gr_order.ord_status','=','4')->where('gr_order.ord_task_status','=','1')->where('gr_order.ord_agent_acpt_status','=','1')->where('gr_order.ord_delmgr_id','=',Session::get('DelMgrSessId'))->where('gr_order.ord_agent_acpt_read_status','=','0')->where('gr_order.ord_self_pickup','!=','1')->update(['ord_agent_acpt_read_status' => '1']);*/
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$from_date  	= Input::get('from_date');
			$to_date		= Input::get('to_date');
			$orderdetails 	= Reports::getassigned_reports1($from_date, $to_date);
			//echo '<pre>'; print_r($orderdetails); exit;
			$page_title		= (Lang::has(Session::get('DelMgr_lang_file').'.DELIVERY_ORDER_MGMT')) ? trans(Session::get('DelMgr_lang_file').'.DELIVERY_ORDER_MGMT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELIVERY_ORDER_MGMT');
			return view('DeliveryManager.reports.manage_orders1')->with('pagetitle', $page_title)->with('orderdetails', $orderdetails)->with('from_date', $from_date)->with('to_date', $to_date);
		}
		public function InvoiceOrder($id,$merchant_id){
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$id = base64_decode($id);
			$merchant_id = base64_decode($merchant_id);
			
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELIVERY_INVOICE_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELIVERY_INVOICE_DETAILS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELIVERY_INVOICE_DETAILS');
			$allchoices = array();
			$Invoice_Order = DB::table('gr_order')->select( 'gr_order.ord_id',
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.order_ship_mail',
			'gr_store.st_store_name',
			'gr_store.st_address',
			'gr_product.pro_item_name',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_spl_req',
			'gr_order.ord_choices',
			'gr_order.ord_pay_type',
			'gr_order.ord_date',
			'gr_order.ord_transaction_id',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_had_choices',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_customer.cus_address',
			'gr_customer.cus_phone1',
			'gr_customer.cus_email'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product', 'gr_order.ord_pro_id', '=', 'gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$id)
			->where('gr_order.ord_status','>','3')
			->where('gr_order.ord_merchant_id','=',$merchant_id)->get();
			if(count($Invoice_Order)>0)
			{
				foreach($Invoice_Order as $orders)
				{
					$choices=array();
					$splitted_choice=json_decode($orders->ord_choices, true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choice[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								$choices[$choices_name]=$choice['choice_price'];
							}
						}
					}
					$allchoices[$orders->ord_id]=$choices;
				}
			}
			
			//TRACK ORDER DETAILS
			//$storewise_details   = Reports::track_reports($id);
			return view ('DeliveryManager.reports.InvoiceOrder')->with('Invoice_Order',$Invoice_Order)->with('choices',$allchoices)->with('pagetitle',$pagetitle);//->with('storewise_details',$storewise_details);
			
		}
		
		public function TrackOrder($id,$merchant_id)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$id 		= base64_decode($id);
			$mer_id 	= base64_decode($merchant_id);
			$pagetitle 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELIVERY_TRACK_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.DELIVERY_TRACK_ORDER') : trans($this->DELMGR_OUR_LANGUAGE.'.DELIVERY_TRACK_ORDER');
			$customer_details = DB::table('gr_order')->select('ord_transaction_id','ord_date','ord_pre_order_date','ord_shipping_cus_name','ord_shipping_address','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail','ord_shipping_address1')->where('ord_transaction_id',$id)->first();
			$storewise_details = Reports::delivery_track_reports($id,$mer_id);
			return view('DeliveryManager.reports.order-tracking')->with('storewise_details', $storewise_details)->with('customer_details',$customer_details)->with('pagetitle', $pagetitle);
		}
		/*-----------------------AGENT IS ENABLED -------------------*/
		public function AssignDeliveryBoy(Request $request)
		{
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGN_AGENT');	
			return view('DeliveryManager.assign-order')->with('ord_transaction_id', $request->ord_transaction_id)->with('pagetitle', $pagetitle);
		}
		/* ---------------------- AGENT IS DISABLED -----------------*/
		public function AssignDeliveryBoy1(Request $request)
		{
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGN_AGENT');	
			return view('DeliveryManager.assign-order1')->with('ord_transaction_id', $request->ord_transaction_id)->with('pagetitle', $pagetitle);
		}
		
		public function agent_list_ajax(Request $request)
		{
			
			$columns = array(
			0 => 'agent_id',
			1 => 'agent_id',
			2 => 'agent_fname',
			3 => 'agent_email',
			4 => 'agent_phone1',
			5 => 'agent_base_fare',
			6 => 'suxes_order',
			7 => 'failed_order',
			8 => 'pending_order'
			);
			/*To get Total count */
			$totalData 		= DB::table('gr_agent')->select('agent_id')->where('agent_status','<>','2')->where('agent_avail_status','=','1')->count();
			//			print_R($totalData);
			//			exit;
			$totalFiltered 	= $totalData;
			/*EOF get Total count */
			$limit	= $request->input('length');
			$start	= $request->input('start');
			$order 	= $columns[$request->input('order.0.column')];
			$dir 	= $request->input('order.0.dir');
			$current_time 	= date('H:i');
			$current_day	= date('l');
			
			//if(empty($request->input('search.value')))
			$agentName_search 	= trim($request->agentName_search);
			$agentEmail_search 	= trim($request->agentEmail_search);
			$agentPhone_search 	= trim($request->agentPhone_search);
			$publish_search 	= trim($request->publish_search);
			if($agentName_search=='' && $agentEmail_search=='' && $agentPhone_search=='' && $publish_search=='')
			{
				//DB::connection()->enableQueryLog();
				$posts = DB::table('gr_agent')
				->select('agent_id',
				'agent_fname',
				'agent_lname',
				'agent_email',
				'agent_phone1',
				'agent_status',
				'agent_base_fare',
				'agent_fare_type',
				'agent_currency_code',
				DB::Raw('(SELECT count(*) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_count'),
				DB::Raw('(SELECT count(*) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id` AND deliver_avail_status="1") as avail_delboy'),
				/*DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`= ? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_agent_id=`gr_agent`.`agent_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT SUM(deliver_order_limit) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_order_limit'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
				)
				->where('agent_status','<>','2')
				->where('agent_avail_status','=','1')
				->orderBy($order,$dir)->skip($start)->take($limit)->get();
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
			}
			else {
				
				$sql = DB::table('gr_agent')
				->select('agent_id',
				'agent_fname',
				'agent_lname',
				'agent_email',
				'agent_phone1',
				'agent_status',
				'agent_base_fare',
				'agent_fare_type',
				'agent_currency_code',
				DB::Raw('(SELECT count(*) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_count'),
				DB::Raw('(SELECT count(*) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id` AND deliver_avail_status="1") as avail_delboy'),
				/*DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_agent_id=`gr_agent`.`agent_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT SUM(deliver_order_limit) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_order_limit'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
				)
				->where('agent_status','<>','2')
				->where('agent_avail_status','=','1');
				if($agentName_search != '')
				{
					/*$q = $sql->whereRaw("CONCAT(if(agent_fname is null,'',agent_fname),' ',if(agent_lname is null,'',agent_lname)) like '%".$agentName_search."%'");*/
					$q = $sql->whereRaw("CONCAT(if(agent_fname is null,'',agent_fname),' ',if(agent_lname is null,'',agent_lname)) like ?", ['%'.$agentName_search.'%']);
				}
				if($agentEmail_search != '')
				{
					/*$q = $sql->whereRaw("agent_email like '%".$agentEmail_search."%'");*/
					$q = $sql->whereRaw("agent_email like ?", ['%'.$agentEmail_search.'%']);
				}
				if($agentPhone_search != '')
				{
					/*$q = $sql->whereRaw("agent_phone1 like '%".$agentPhone_search."%'");*/
					$q = $sql->whereRaw("agent_phone1 like ?", ['%'.$agentPhone_search.'%']);
				}
				if($publish_search != '')
				{
					$q = $sql->where('agent_status',$publish_search);
				}
				$totalFiltered = $q->count();
				//DB::connection()->enableQueryLog();
				$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts =  $q->get();
				
				
			}
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				foreach ($posts as $post)
				{
					if($post->agent_fare_type == 'per_km')
					{
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM');
					}
					else
					{
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN');
					}
					
					if($post->delboy_count <= 0 ){
						$disable	='disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NO_DELBOY_AVAILABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NO_DELBOY_AVAILABLE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_NO_DELBOY_AVAILABLE');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					elseif($post->avail_delboy <= 0){
						$disable	='disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ALL_DELBOY_BUSY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ALL_DELBOY_BUSY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ALL_DELBOY_BUSY');
						$iClass		= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					elseif($post->delboy_induty <= '0'){
						$disable	= 'disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NOTAVAIL_ATTHIS_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NOTAVAIL_ATTHIS_TIME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_NOTAVAIL_ATTHIS_TIME');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					elseif($post->pending_order_delboy  >= $post->delboy_order_limit){
						$disable	= 'disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EXCESS_ORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EXCESS_ORDER_LIMIT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_EXCESS_ORDER_LIMIT');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					else{
						$disable='';
						$name 	= 'name="chk"';
						$iClass = '';
					}
					
					$nestedData['checkBox'] 	= '<input type="radio" class="checkboxclass" '.$name.' value="'.$post->agent_id.'" '.$disable.'>'.$iClass;
					$nestedData['SNo'] 			= ++$snoCount;
					$nestedData['delboyName'] 	= $post->agent_fname.' '.$post->agent_lname;
					$nestedData['delboyEmail'] 	= $post->agent_email;
					$nestedData['delboyPhone'] 	= $post->agent_phone1;
					$nestedData['Edit'] 		= $post->agent_base_fare.' '.$post->agent_currency_code.' '.$fareType;
					$nestedData['suxes_order'] 	= $post->suxes_order;
					$nestedData['failed_order'] = $post->failed_order;
					$nestedData['pending_order']= $post->pending_order;
					$data[] = $nestedData;
					
				}
			}
			
			$json_data = array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $data
			);
			
			echo json_encode($json_data);
		}
		
		
		public function delboy_list_ajax(Request $request)
		{
			
			$columns = array(
			0 => 'deliver_id',
			1 => 'deliver_id',
			2 => 'deliver_fname',
			3 => 'deliver_email',
			4 => 'deliver_phone1',
			5 => 'deliver_base_fare',
			6 => 'suxes_order',
			7 => 'failed_order',
			8 => 'pending_order'
			);
			/*To get Total count */
			$totalData 		= DB::table('gr_delivery_member')->select('deliver_id')->where('deliver_status','<>','2')->where('deliver_avail_status','=','1')->count();
			//			print_R($totalData);
			//			exit;
			$totalFiltered 	= $totalData;
			/*EOF get Total count */
			$limit	= $request->input('length');
			$start	= $request->input('start');
			$order 	= $columns[$request->input('order.0.column')];
			$dir 	= $request->input('order.0.dir');
			$current_time 	= date('H:i');
			$current_day	= date('l');
			
			//if(empty($request->input('search.value')))
			$delboyName_search 	= trim($request->delboyName_search);
			$delboyEmail_search 	= trim($request->delboyEmail_search);
			$delboyPhone_search 	= trim($request->delboyPhone_search);
			$publish_search 	= trim($request->publish_search);
			if($delboyName_search=='' && $delboyEmail_search=='' && $delboyPhone_search=='' && $publish_search=='')
			{     
				//DB::connection()->enableQueryLog();
				$posts = DB::table('gr_delivery_member')
				->select('deliver_id',
				'deliver_fname',
				'deliver_lname',
				'deliver_email',
				'deliver_phone1',
				'deliver_status',
				'deliver_base_fare',
				'deliver_fare_type',
				'deliver_currency_code',
				'deliver_order_limit AS delboy_order_limit',
				/*DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
				)
				->where('deliver_status','<>','2')
				->where('deliver_avail_status','=','1')
				->orderBy($order,$dir)->skip($start)->take($limit)->get();
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
			}
			else {
				
				$sql = DB::table('gr_delivery_member')
				->select('deliver_id',
				'deliver_fname',
				'deliver_lname',
				'deliver_email',
				'deliver_phone1',
				'deliver_status',
				'deliver_base_fare',
				'deliver_fare_type',
				'deliver_currency_code',
				'deliver_order_limit AS delboy_order_limit',
				/*DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
				)
				->where('deliver_status','<>','2')
				->where('deliver_avail_status','=','1');
				if($delboyName_search != '')
				{
					/*$q = $sql->whereRaw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) like '%".$delboyName_search."%'");*/
					$q = $sql->whereRaw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) like ?", ['%'.$delboyName_search.'%']);
				}
				if($delboyEmail_search != '')
				{
					/*$q = $sql->whereRaw("deliver_email like '%".$delboyEmail_search."%'");*/
					$q = $sql->whereRaw("deliver_email like ?", ['%'.$delboyEmail_search.'%']);
				}
				if($delboyPhone_search != '')
				{
					/*$q = $sql->whereRaw("deliver_phone1 like '%".$delboyPhone_search."%'");*/
					$q = $sql->whereRaw("deliver_phone1 like ?", ['%'.$delboyPhone_search.'%']);
				}
				if($publish_search != '')
				{
					$q = $sql->where('deliver_status',$publish_search);
				}
				$totalFiltered = $q->count();
				//DB::connection()->enableQueryLog();
				$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts =  $q->get();
				
				
			}
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				foreach ($posts as $post)
				{
					if($post->deliver_fare_type == 'per_km')
					{
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM');
					}
					else
					{
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN');
					}
					
					if($post->delboy_induty <= '0'){
						$disable	= 'disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NOTAVAIL_ATTHIS_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NOTAVAIL_ATTHIS_TIME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_NOTAVAIL_ATTHIS_TIME');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					elseif($post->pending_order_delboy  >= $post->delboy_order_limit){
						$disable	= 'disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EXCESS_ORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EXCESS_ORDER_LIMIT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_EXCESS_ORDER_LIMIT');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					else{
						$disable='';
						$name 	= 'name="chk"';
						$iClass = '';
					}
					
					$nestedData['checkBox'] 	= '<input type="radio" class="checkboxclass" '.$name.' value="'.$post->deliver_id.'" '.$disable.'>'.$iClass;
					$nestedData['SNo'] 			= ++$snoCount;
					$nestedData['delboyName'] 	= $post->deliver_fname.' '.$post->deliver_lname;
					$nestedData['delboyEmail'] 	= $post->deliver_email;
					$nestedData['delboyPhone'] 	= $post->deliver_phone1;
					$nestedData['Edit'] 		= $post->deliver_base_fare.' '.$post->deliver_currency_code.' '.$fareType;
					$nestedData['suxes_order'] 	= $post->suxes_order;
					$nestedData['failed_order'] = $post->failed_order;
					$nestedData['pending_order']= $post->pending_order;
					$data[] = $nestedData;
					
				}
			}
			
			$json_data = array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $data
			);
			
			echo json_encode($json_data);
		}
		
		
		public function assign_order_to_agent(Request $request)
		{
			$ord_transaction_id = explode(",",$request->ord_transaction_id);
			$agent_id	 = $request->agent_id;
			$got_message = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_OR_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DEL_OR_ASSIGNED') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_OR_ASSIGNED');
			if(count($ord_transaction_id) > 0 )
			{
				foreach($ord_transaction_id as $ordtransid)
				{
					$split_value = explode('`',$ordtransid);
					$updatable_trans_id = $split_value[0];
					$merchant_id = $split_value[1];
					DB::table('gr_order')->where('ord_transaction_id', $updatable_trans_id)->where('ord_merchant_id',$merchant_id)->update(['ord_task_status' => '1','ord_agent_id'=>$agent_id,'ord_delmgr_id'=>Session::get('DelMgrSessId'),'ord_taskassigned_date'=>date('Y-m-d H:i:s')]);
					
					/* notification to agent */					
					$searchReplaceArray = array(':transaction_id' => $updatable_trans_id);
					$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);	
					push_notification(Session::get('DelMgrSessId'),$agent_id,'gr_delivery_manager','gr_agent',$result,$updatable_trans_id,'');
				}
			}
		}
		
		
		public function assign_order_to_delboy(Request $request)
		{
			$ord_transaction_id = explode(",",$request->ord_transaction_id);
			$delboy_id	 = $request->delboy_id;
			$got_message = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_OR_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DEL_OR_ASSIGNED') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_OR_ASSIGNED');
			if(count($ord_transaction_id) > 0 )
			{
				foreach($ord_transaction_id as $ordtransid)
				{
					$split_value = explode('`',$ordtransid);
					$updatable_trans_id = $split_value[0];
					$merchant_id = $split_value[1];
					DB::table('gr_order')->where('ord_transaction_id', $updatable_trans_id)->where('ord_merchant_id',$merchant_id)->update(['ord_task_status' => '1','ord_agent_id'=>'0','ord_delivery_memid'=>$delboy_id,'ord_agent_acpt_status'=>'1','ord_agent_acpt_read_status'=>'1','ord_delmgr_id'=>Session::get('DelMgrSessId'),'ord_delboy_act_status'=>0,'ord_taskassigned_date'=>date('Y-m-d H:i:s'),'ord_delboy_assigned_on'=>date('Y-m-d H:i:s')]);
					
					/* notification to agent */					
					$searchReplaceArray = array(':transaction_id' => $updatable_trans_id);
					$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);	
					push_notification(Session::get('DelMgrSessId'),$delboy_id,'gr_delivery_manager','gr_delivery_member',$result,$updatable_trans_id,'');
					$del_person_details = DB::table('gr_delivery_member')->select('deliver_andr_fcm_id','deliver_ios_fcm_id')->where('deliver_id','=',$delboy_id)->first();
					if(empty($del_person_details) === false)
					{
						/* send notification to delivery person  mobile */
						if($del_person_details->deliver_andr_fcm_id !='')
						{
							$parse_fcm=json_decode($del_person_details->deliver_andr_fcm_id,true);
							$reg_id = array();
							if(count($parse_fcm) > 0 )
							{
								foreach($parse_fcm as $parsed)
								{ 
									array_push($reg_id,$parsed['fcm_id']);						
								}
							}
									$json_data = [	"registration_ids" => $reg_id,
													"notification" => ["body" => $result,"title" => "Order Notification"]
												];
									$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_DEL);
								
						}
						if($del_person_details->deliver_ios_fcm_id !='')
						{
							$parse_fcm=json_decode($del_person_details->deliver_ios_fcm_id,true);
							$reg_id = array();
							if(count($parse_fcm) > 0 )
							{
								foreach($parse_fcm as $parsed)
								{ 
									array_push($reg_id,$parsed['fcm_id']);						
								}
							}
									$json_data = [	"registration_ids" => $reg_id,
													"notification" => ["body" => $result,"title" => "Order Notification","sound"				=> "default"]
												];
									$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_DEL);
								
						}
						/* send notification to delivery person mobile ends */
					}
					
					
				}
			}
		}
		
		/*public function store_delboy_location(Request $request){
			
			teasfsadfsd
		}*/
		
		public function auto_reject(){
			/********************* CHECK ASSIGNED ORDER THAT NOT RESPOND ON PARTICULAR RESPONSE TIME ***********************/
			$response_qry = DB::table('gr_order')->select('gr_order.ord_rest_id','gr_order.ord_transaction_id','gr_order.ord_delivery_memid','gr_order.ord_taskassigned_date','gr_delivery_member.deliver_response_time')
							->Join('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_order.ord_delivery_memid')
							->where('gr_order.ord_status','=','4')
							->where('gr_order.ord_delboy_act_status','=','0')
							->get();
			//print_r($response_qry); exit;
			$reason = 'Exceeds Respose Time - Cron';
			if(count($response_qry) > 0){
				foreach($response_qry as $resp){
				
					$assigned_date = $resp->ord_taskassigned_date;
					$today_date = date('Y-m-d H:i:s');
					$response_time = $resp->deliver_response_time;
					$actual_time =  date('Y-m-d H:i',strtotime('+'.date('H',strtotime($response_time)).' hour +'.date('i',strtotime($response_time)).' minutes',strtotime($assigned_date)));
					//echo $actual_time.'<br>'.$today_date; exit;
					if(strtotime($today_date) > strtotime($actual_time)){
						$insertArr = array(	'ord_delboy_act_status' => '2',
								'ord_delboy_rjct_reason'=>	$reason,
								'ord_delboy_rjct_time'	=>	date('Y-m-d H:i:s')
								);
						//print_r($insertArr); exit;
						$update = updatevalues('gr_order',$insertArr,['ord_rest_id'=>$resp->ord_rest_id,'ord_transaction_id'=>$resp->ord_transaction_id]);
						
						$data = array(	'store_id'	=> $resp->ord_rest_id,
										'order_id'	=> $resp->ord_transaction_id,
										'delboy_id'	=> $resp->ord_delivery_memid,
										'reason'	=> $reason,
										'rejected_at'	=> date('Y-m-d H:i:s')
										);
						$res=insertvalues('gr_order_reject_history',$data);
					}
				}
			}
		}
		/* --------- CRON JOB Command : wget http://edisonqa.mytaxisoft.com/orders-auto-allocation ------------------*/

		public function auto_allocation(Request $request){
			$current_time = date('H:i');
			$current_day=date('l');

			/* ******************* GET DELIVERY BOY KILO METER RANGE FROM ADMIN SETTINGS ------------*/
			$delboy_kmRange = DB::table('gr_general_setting')->select('gs_delivery_kmrange')->first();
			if(empty($delboy_kmRange)===true){
				$delboyRadius = '10';
			}else{
				$delboyRadius = $delboy_kmRange->gs_delivery_kmrange;
			}
			/******** GET DELIVERY MANAGER ************/
			$auto_delmgrs = DB::table('gr_delivery_manager')->select('dm_id')->where('dm_status','=','1')->where('dm_delivery_type','=','auto')->get();
			$delmgr_array = array();
			if(count($auto_delmgrs) > 0){
				foreach($auto_delmgrs as $delmgr){
					array_push($delmgr_array,$delmgr->dm_id);
				}
			}
			/****************** GET LAST ASSIGNED DELIVER MEMBER AND DELIVERY BOY ***************/
			$checkRoundRobin_exist = DB::table('round_robin')->where('id','=','1')->first();
			if(empty($checkRoundRobin_exist)===true){
				$delMgrIndex = 0;
				$delBoyIndex = 0;
			}
			else{
				if($checkRoundRobin_exist->del_mgr_index=='') { $delMgrIndex = 0; } else { $delMgrIndex = $checkRoundRobin_exist->del_mgr_index; }
				if($checkRoundRobin_exist->del_boy_index=='') { $delBoyIndex = 0; } else { $delBoyIndex = $checkRoundRobin_exist->del_boy_index; }
			}
			/*************** GET NEW ORDER *******************************/ 
			$orderdetails = Reports::newgetall_unassignedOrders();
			if(count($orderdetails) > 0 ){
				foreach($orderdetails as $orderdetail){
					$storeId = $orderdetail->ord_rest_id;
					$order_id = $orderdetail->ord_transaction_id;
					$st_latitude=$orderdetail->st_latitude;
					$st_longitude=$orderdetail->st_longitude;
					$assigned_status = $orderdetail->assigned_status;
					if($delMgrIndex >= count($delmgr_array)) { $delMgrIndex = 0; } 
					$exist_sql = DB::table('gr_order')->select('gr_order.ord_id')
					->where('gr_order.ord_transaction_id','=',$order_id)
					->where('gr_order.ord_rest_id','=',$storeId)
					->where('gr_order.ord_status','>=','4')
					->where('gr_order.ord_delboy_act_status','=','1')
					->where('gr_order.ord_task_status','=','1');
					
					$result = $exist_sql->count();
					//echo $result;
					if($result <= 0 ){
						if($assigned_status=='newOrder'){
						
							//echo 'new<br>';
							/*NEED TO COUNT NEWLY ASSIGNED ORDER IMPORTANT*/
							//DB::connection()->enableQueryLog();
							$sql = DB::table('gr_delivery_member')
							->select('deliver_id',
							'deliver_agent_id',
							'deliver_latitude',
							'deliver_longitude',
							/*DB::raw('(SELECT lat_lng_distance('.$st_latitude.','.$st_longitude.',gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) AS distance')*/DB::raw('(SELECT lat_lng_distance(?,?,gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) AS distance', [$st_latitude,$st_longitude]),							
							/*DB::raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboyInDuty')*/DB::raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboyInDuty', [$current_day,$current_time,$current_time]),
							DB::raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 4 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id`) AS totalOrders'),
							'deliver_order_limit'
							)
							->where('deliver_avail_status','=','1')
							/*->whereRaw('(SELECT count(*) FROM `gr_agent` WHERE `agent_id`=`gr_delivery_member`.`deliver_agent_id` AND `agent_status`="1") > 0')*/
							/*->whereRaw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) > 0')*/
							->whereRaw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) > 0', [$current_day,$current_time,$current_time]) //delboyinduty
							->whereRaw('(SELECT count(*) FROM gr_order WHERE ord_status >= 4 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id`) < (deliver_order_limit )') //avail delboy
							/*->whereRaw('(SELECT lat_lng_distance('.$st_latitude.','.$st_longitude.',gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) <= '.$delboyRadius.'')*/
							->whereRaw('(SELECT lat_lng_distance(?,?,gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) <= ?', [$st_latitude,$st_longitude,$delboyRadius])
							->where('deliver_status','=','1')
							->whereNotNull('deliver_latitude')
							->whereNotNull('deliver_longitude')
							->orderBy('distance','asc');
							/*delivered time and most delivered,, same customer -- same delivery boy(Preference)*/
							$delBoys =  $sql->first();
							//$query = DB::getQueryLog();
							//echo '<pre>';print_r($query);
							///exit;
						}
						else{
							//echo 'already rejected<br>';
							/* NEED TO CHECK ALREADY REJECTED THIS ORDER OR NOT */
							$sql = DB::table('gr_delivery_member')
							->select('deliver_id',
							'deliver_agent_id',
							'deliver_latitude',
							'deliver_longitude',
							/*DB::raw('(SELECT lat_lng_distance('.$st_latitude.','.$st_longitude.',gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) AS distance')*/
							DB::raw('(SELECT lat_lng_distance(?,?,gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) AS distance', [$st_latitude,$st_longitude]),
							/*DB::raw('(SELECT count(*) FROM `gr_agent` WHERE `agent_id`=`gr_delivery_member`.`deliver_agent_id` AND `agent_status`="1") AS agent_status'),*/
							/*DB::raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboyInDuty')*/
							DB::raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboyInDuty', [$current_day,$current_time,$current_time]),
							DB::raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 4 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id`) AS totalOrders'),
							'deliver_order_limit'
							)
							->where('deliver_avail_status','=','1')
							/*->whereRaw('(SELECT count(*) FROM `gr_agent` WHERE `agent_id`=`gr_delivery_member`.`deliver_agent_id` AND `agent_status`="1") > 0')*/
							/*->whereRaw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) > 0')*/
							->whereRaw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`=? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) > 0', [$current_day,$current_time,$current_time]) //delboyinduty
							->whereRaw('(SELECT count(*) FROM gr_order WHERE ord_status >= 4 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id` ) < (deliver_order_limit)') //avail delboy
							/*->whereRaw('(SELECT lat_lng_distance('.$st_latitude.','.$st_longitude.',gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) <= '.$delboyRadius.'')*/
							->whereRaw('(SELECT lat_lng_distance(?,?,gr_delivery_member.deliver_latitude,gr_delivery_member.deliver_longitude)) <= ?', [$st_latitude,$st_longitude,$delboyRadius])
							->whereRaw('(SELECT count(*) FROM `gr_order_reject_history` WHERE `store_id`='.$storeId.' AND `order_id`="'.$order_id.'" AND agent_id=0 AND delboy_id=gr_delivery_member.deliver_id) <= 0')
							->where('deliver_status','=','1')
							->whereNotNull('deliver_latitude')
							->whereNotNull('deliver_longitude')
							->orderBy('distance','asc');
							//$delBoys =  $sql->limit(1)->get();
							$delBoys =  $sql->first();
						}
						if(empty($delBoys)===true){
							//print_r($delBoys);
						}else{
							$delMgr_id = $delmgr_array[$delMgrIndex];
							DB::table('gr_order')->where('ord_transaction_id','=',$order_id)->where('ord_rest_id','=',$storeId)->update(['ord_task_status' => '1','ord_agent_id'=>$delBoys->deliver_agent_id,'ord_delmgr_id'=>$delMgr_id,'ord_delivery_memid'=>$delBoys->deliver_id,'ord_taskassigned_date'=>date('Y-m-d H:i:s'),'ord_delboy_assigned_on'=>date('Y-m-d H:i:s'),'deliver_assignedby'=>'Cron','ord_agent_acpt_status'=>'1','ord_agent_acpt_read_status'=>'1','ord_delboy_act_status'=>'0']);
							
							/*SEND NOTIFICATION SECTION*/
							$del_person_details = DB::table('gr_delivery_member')->select('deliver_andr_fcm_id','deliver_ios_fcm_id')->where('deliver_id','=',$delBoys->deliver_id)->first();
							$got_message = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_OR_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DEL_OR_ASSIGNED') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_OR_ASSIGNED');
							$searchReplaceArray = array(':transaction_id' => $order_id);
							$notification_msg = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);	

							if(empty($del_person_details) === false)
							{
								/* send notification to delivery person  mobile */
								if($del_person_details->deliver_andr_fcm_id !='')
								{
									$parse_fcm=json_decode($del_person_details->deliver_andr_fcm_id,true);
									$reg_id = array();
									if(count($parse_fcm) > 0 )
									{
										foreach($parse_fcm as $parsed)
										{ 
											array_push($reg_id,$parsed['fcm_id']);						
										}
									}
											$json_data = [
															"registration_ids" => $reg_id,
															"notification" => ["body" => $notification_msg,"title" => "Order Notification"]
															];
											$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_DEL);
										
								}
								if($del_person_details->deliver_ios_fcm_id !='')
								{
									$parse_fcm=json_decode($del_person_details->deliver_ios_fcm_id,true);
									$reg_id = array();
									if(count($parse_fcm) > 0 )
									{
										foreach($parse_fcm as $parsed)
										{ 
											array_push($reg_id,$parsed['fcm_id']);						
										}
									}
											$json_data = [
															"registration_ids" => $reg_id,
															"notification" => ["body" => $notification_msg,"title" => "Order Notification","sound"	=> "default"]
															];
											$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_DEL);
										
								}
								/* send notification to delivery person mobile ends */
							}
							/*EOF NOTIFICATION SECTION */
							/****************** UPDATE LAST ASSIGNED DELIVER MEMBER AND DELIVERY BOY ***************/
							$checkRoundRobin_exist = DB::table('round_robin')->where('id','=','1')->first();
							if(empty($checkRoundRobin_exist)===true){
								DB::table('round_robin')->insert(['id' => '1', 'del_mgr_index' => $delMgrIndex, 'del_boy_index'=>$delBoyIndex ]);
							}else{
								DB::table('round_robin')->where('id','=','1')->update(['del_mgr_index' => $delMgrIndex,'del_boy_index'=>$delBoyIndex]);
							}
							$delMgrIndex++;
						}
					}
				}
			}
		}
		/* THSI BELOW METHOD IS USED ROUND ROBIN METHOD. BUT PROBLEM OCCURED WHEN   SEARCHING NEAR BY STORE . IF YOU WANT TO USE THIS METHOD JUST DELETE FROM CONFUSED ARRAY TO SPLIT DEL MEMID */
		public function auto_allocation2(Request $request){
			
			$current_time = date('H:i');
			$current_day=date('l');
			/* ******************* GET DELIVERY BOY KILO METER RANGE FROM ADMIN SETTINGS ------------*/
			$delboy_kmRange = DB::table('gr_general_setting')->select('gs_delivery_kmrange')->first();
			if(empty($delboy_kmRange)===true){
				$delboyRadius = '10';
				}else{
				$delboyRadius = $delboy_kmRange->gs_delivery_kmrange;
			}
			/******** GET DELIVERY MANAGER ************/
			$auto_delmgrs = DB::table('gr_delivery_manager')->select('dm_id')->where('dm_status','=','1')->where('dm_delivery_type','=','auto')->get();
			$delmgr_array = array();
			if(count($auto_delmgrs) > 0){
				foreach($auto_delmgrs as $delmgr){
					array_push($delmgr_array,$delmgr->dm_id);
				}
			}
			echo '<pre>'; print_r($delmgr_array); echo '<hr>';
			/********* GET DELIVERY BOY ***********/
			/*GET DELIVERY BOY LIST -- HERE WE HAVE TO USE NEAR BY LOCATION CALCULATION.. (**** IMPORTANT ****)*/
			//DB::connection()->enableQueryLog();
			
			//$query = DB::getQueryLog();
			//print_r($query);
			$delBoyArray = array();
			if(count($delBoys ) > 0 ){
				foreach($delBoys as $delBoy){
					array_push($delBoyArray,$delBoy->deliver_id.'`'.$delBoy->deliver_agent_id.'`'.$delBoy->deliver_latitude.'`'.$delBoy->deliver_longitude);
				}
			}
			echo '<pre>'; print_r($delBoyArray); echo '<hr>';
			/****************** GET LAST ASSIGNED DELIVER MEMBER AND DELIVERY BOY ***************/
			$checkRoundRobin_exist = DB::table('round_robin')->where('id','=','1')->first();
			if(empty($checkRoundRobin_exist)===true){
				$delMgrIndex = 0;
				$delBoyIndex = 0;
			}
			else{
				if($checkRoundRobin_exist->del_mgr_index=='') { $delMgrIndex = 0; } else { $delMgrIndex = $checkRoundRobin_exist->del_mgr_index; }
				if($checkRoundRobin_exist->del_boy_index=='') { $delBoyIndex = 0; } else { $delBoyIndex = $checkRoundRobin_exist->del_boy_index; }
			}
			
			/*************** GET NEW ORDER *******************************/ 
			$orderdetails = Reports::newgetall_unassignedOrders();
			if(count($orderdetails) > 0 ){
				foreach($orderdetails as $orderdetail){
					$storeId = $orderdetail->ord_rest_id;
					$order_id = $orderdetail->ord_transaction_id;
					$st_latitude=$orderdetail->st_latitude;
					$st_longitude=$orderdetail->st_longitude;
					//IF INCREMENT COUNT EXISTS ACTUAL COUNT RESET TO ZERO AGAIN
					if($delBoyIndex >= count($delBoyArray)) { $delBoyIndex = 0; } 
					if($delMgrIndex >= count($delmgr_array)) { $delMgrIndex = 0; } 
					//GET REJECTED AGENTS FOR THIS ORDER  
					if($orderdetail->assigned_status=='newOrder'){
						$rejectdelboyArray = array();
						}else{
						$rejected_delboys =  DB::table('gr_order_reject_history')->where('store_id', $storeId)->where('order_id',$order_id)->where('agent_id','=','0')->where('delboy_id','!=','0')->get();
						$rejectdelboyArray = array();
						if(count($rejected_delboys) > 0 ){
							foreach($rejected_delboys as $rjct_delboy){
								array_push($rejectdelboyArray,$rjct_delboy->delboy_id);
							}
						}
					}
					/*CHECK THIS ORDER HAS ASSIGNED OR NOT */
					$exist_sql = DB::table('gr_order')->select('gr_order.ord_id')
					->where('gr_order.ord_transaction_id','=',$order_id)
					->where('gr_order.ord_rest_id','=',$storeId)
					->where('gr_order.ord_status','>=','4')
					->where('gr_order.ord_delboy_act_status','=','1')
					->where('gr_order.ord_task_status','=','1');
					
					$result = $exist_sql->count();
					if($result <= 0 ){
						$confusedArray = array();
						if(count($delBoyArray) > 0 ){
							foreach($delBoyArray as $dba){
								$splitDelMemId = explode('`',$dba);
								$delMem_id = $splitDelMemId[0];
								$agent_id = $splitDelMemId[1];
								$delBoy_latitude = $splitDelMemId[2];
								$delBoy_longitude = $splitDelMemId[3];
								if(in_array($splitDelMemId[0],$rejectdelboyArray)){
								}
								else{
									$distance_finder =DB::select("SELECT lat_lng_distance('".$delBoy_latitude."','".$delBoy_longitude."','".$st_latitude."','".$st_longitude."') AS distance");
									if($distance_finder[0]->distance <= $delboyRadius){
										$confusedArray[$delMem_id] = $distance_finder[0]->distance;
									}
								}
							}
						}
						//echo '<pre>';print_r($confusedArray);echo '<hr>';
						$minDistance = min($confusedArray);
						$minDistanceDelBoy = array_search(min($confusedArray),$confusedArray);
						echo 'Min Distance'.$minDistance;
						echo 'Min Distance DelBoy'.$minDistanceDelBoy.'<br>';
						/*$splitDelMemId = explode('`',$delBoyArray[$delBoyIndex]);
							if(in_array($splitDelMemId[0],$rejectdelboyArray)){
							}
							else{
							
							
							$delMem_id = $splitDelMemId[0];
							$agent_id = $splitDelMemId[1];
							$delBoy_latitude = $splitDelMemId[2];
							$delBoy_longitude = $splitDelMemId[3];
							$delMgr_id = $delmgr_array[$delMgrIndex];
							echo $delMem_id.'/'.$delMgr_id.'<bR>';
							$distance_finder =DB::select("SELECT lat_lng_distance('".$delBoy_latitude."','".$delBoy_longitude."','".$st_latitude."','".$st_longitude."') AS distance");
							//print_r($distance_finder);
							if($distance_finder[0]->distance <= $delboyRadius){
							$confusedArray[] = $distance_finder[0]->distance;
							//DB::table('gr_order')->where('ord_transaction_id','=',$order_id)->where('ord_rest_id','=',$storeId)->update(['ord_task_status' => '1','ord_agent_id'=>$agent_id,'ord_delmgr_id'=>$delMgr_id,'ord_delivery_memid'=>$delMem_id,'ord_taskassigned_date'=>date('Y-m-d H:i:s'),'deliver_assignedby'=>'Cron','ord_agent_acpt_status'=>'1','ord_agent_acpt_read_status'=>'1','ord_delboy_act_status'=>'0']);
							$delMgrIndex++;
							}
							$delBoyIndex++;
						}*/
					}
					//echo '<pre>';print_r($confusedArray);
				}	
				
				/****************** UPDATE LAST ASSIGNED DELIVER MEMBER AND DELIVERY BOY ***************/
				/*$checkRoundRobin_exist = DB::table('round_robin')->where('id','=','1')->first();
					if(empty($checkRoundRobin_exist)===true){
					DB::table('round_robin')->insert(['id' => '1', 'del_mgr_index' => $delMgrIndex, 'del_boy_index'=>$delBoyIndex ]);
					}
					else{
					DB::table('round_robin')->where('id','=','1')->update(['del_mgr_index' => $delMgrIndex,'del_boy_index'=>$delBoyIndex]);
				}*/
			}
			
		}
	}
	
?>