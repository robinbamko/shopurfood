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
	
	class NewOrderMgmtController extends Controller
	{
		
		public function __construct(){
			parent::__construct();
			$this->setLanguageLocalDeliveryManager();
			if(Session::has('DelMgrSessId') == 1){ } else{ return Redirect::to('delivery-manager-login'); }
		}
		
		public function newdeals_all_orders(){
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$from_date  = Input::get('from_date');
			$to_date	= Input::get('to_date');
			$orderdetails = Reports::newgetall_dealreports($from_date, $to_date);
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NEW_ORDER_MGMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NEW_ORDER_MGMT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_NEW_ORDER_MGMT');
			return view('DeliveryManager.neworders.manage_orders')->with('pagetitle', $page_title)->with('orderdetails', $orderdetails)->with('from_date', $from_date)->with('to_date', $to_date);
		}
		public function rejected_order_by_agent(){
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$orderdetails 	= Reports::rejected_order_by_agent();
			$page_title 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_AGENT')) ? ucwords(trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_AGENT')) : ucwords(trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_REJECTED_BY_AGENT'));
			return view('DeliveryManager.neworders.rejected_orders')->with('pagetitle', $page_title)->with('orderdetails', $orderdetails);
		}
		
		public function rejected_order_by_delboy(){
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$orderdetails 	= Reports::rejected_order_by_delboy();
			$page_title 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_DELBOY')) ? ucwords(trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_DELBOY')) : ucwords(trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_REJECTED_BY_DELBOY'));
			return view('DeliveryManager.neworders.rejected_orders1')->with('pagetitle', $page_title)->with('orderdetails', $orderdetails);
		}
		
		public function ReassignDeliveryBoy(Request $request)
		{
			foreach(explode(",",$request->ord_transaction_id) as $transId){
				$splitInput = explode("`",$transId);
				$getstoreId = DB::table('gr_order')->select('ord_rest_id')->where('ord_transaction_id','=',$splitInput[0])->where('ord_merchant_id','=',$splitInput[1])->first();
				if(empty($getstoreId)==false){
					$storeId = $getstoreId->ord_rest_id;
					$update = DB::table('gr_order_reject_history') ->where('store_id', $storeId)->where('order_id',$splitInput[0])->where('agent_id','=',$splitInput[2])->update( [ 'read_status' => '1']); 
				}
			}
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGN_AGENT');	
			return view('DeliveryManager.neworders.reassign-order')->with('ord_transaction_id', $request->ord_transaction_id)->with('pagetitle', $pagetitle);
		}
		
		public function ReassignDeliveryBoy1(Request $request)
		{
			foreach(explode(",",$request->ord_transaction_id) as $transId){
				$splitInput = explode("`",$transId);
				$getstoreId = DB::table('gr_order')->select('ord_rest_id')->where('ord_transaction_id','=',$splitInput[0])->where('ord_merchant_id','=',$splitInput[1])->first();
				if(empty($getstoreId)==false){
					$storeId = $getstoreId->ord_rest_id;
					$update = DB::table('gr_order_reject_history') ->where('store_id', $storeId)->where('order_id',$splitInput[0])->where('delboy_id','=',$splitInput[2])->update( [ 'read_status' => '1']); 
				}
			}
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN_AGENT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGN_AGENT');	
			return view('DeliveryManager.neworders.reassign-order1')->with('ord_transaction_id', $request->ord_transaction_id)->with('pagetitle', $pagetitle);
		}
		
		public function agent_withoutRejected_ajax(Request $request)
		{
			$splitInput = explode('`',$request->ord_transaction_id);
			$getstoreId = DB::table('gr_order')->select('ord_rest_id')->where('ord_transaction_id','=',$splitInput[0])->where('ord_merchant_id','=',$splitInput[1])->first();
			if(empty($getstoreId)==false){
				$storeId = $getstoreId->ord_rest_id;
			}
			//DB::connection()->enableQueryLog();
			$agents =  DB::table('gr_order_reject_history') ->where('store_id', $storeId)->where('order_id',$splitInput[0])->where('agent_id','!=','0')->where('delboy_id','=','0')->get();
			/*$query = DB::getQueryLog();*/
			$agentArray = array();
			if(count($agents) > 0 ){
				foreach($agents as $agent){
					array_push($agentArray,$agent->agent_id);
				}
			}
			
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
			$sql = DB::table('gr_agent')->select('agent_id')->where('agent_status','<>','2');
			if(count($agentArray) > 0)
			{
				$q = $sql->whereNotIn('agent_id',$agentArray);
			}	
			$totalData =  $q->count();
			
			//$totalData = DB::table('gr_agent')->select('agent_id')->where('agent_status','<>','2')->count();
			$totalFiltered = $totalData; 
			/*EOF get Total count */
			$limit 	= $request->input('length');
			$start 	= $request->input('start');
			$order 	= $columns[$request->input('order.0.column')];
			$dir 	= $request->input('order.0.dir');
			
			//if(empty($request->input('search.value')))
			$agentName_search 	= trim($request->agentName_search); 
			$agentEmail_search 	= trim($request->agentEmail_search); 
			$agentPhone_search 	= trim($request->agentPhone_search); 
			$publish_search 	= trim($request->publish_search); 
			$current_time 		= date('H:i');
			$current_day		= date('l');
			
			if($agentName_search=='' && $agentEmail_search=='' && $agentPhone_search=='' && $publish_search=='')
			{    
				//DB::connection()->enableQueryLog();
				$sql = DB::table('gr_agent')->select('agent_id',
				'agent_fname',
				'agent_lname',
				'agent_email',
				'agent_phone1',
				'agent_status',
				'agent_base_fare',
				'agent_fare_type',
				'agent_currency_code',
				DB::Raw('(select count(*) from gr_delivery_member where `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_count'),
				DB::Raw('(SELECT count(*) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id` AND deliver_avail_status="1") as avail_delboy'),
				/*DB::Raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/
				DB::Raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`= ? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_agent_id=`gr_agent`.`agent_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT SUM(deliver_order_limit) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_order_limit'),
				DB::Raw('(select count(ord_transaction_id) from gr_order where `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(select count(ord_transaction_id) from gr_order where `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(select count(ord_transaction_id) from gr_order where `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
				)
				->where('agent_status','<>','2')
				->where('agent_avail_status','=','1');
				
				if(count($agentArray) > 0)
				{
					$q = $sql->whereNotIn('agent_id',$agentArray);
				}	
				$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts =  $q->get();
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
				DB::Raw('(select count(*) from gr_delivery_member where `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_count'),
				DB::Raw('(SELECT count(*) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id` AND deliver_avail_status="1") as avail_delboy'),
				/*DB::Raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/DB::Raw('(SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_agent_id`=`gr_agent`.`agent_id` AND `dw_date`= ? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_agent_id=`gr_agent`.`agent_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT SUM(deliver_order_limit) FROM gr_delivery_member WHERE `deliver_agent_id`=`gr_agent`.`agent_id`) as delboy_order_limit'),
				DB::Raw('(select count(ord_transaction_id) from gr_order where `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(select count(ord_transaction_id) from gr_order where `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(select count(ord_transaction_id) from gr_order where `ord_agent_id`=`gr_agent`.`agent_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
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
				if(count($agentArray) > 0)
				{
					$q = $sql->whereNotIn('agent_id',$agentArray);
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
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM');;
					}
					else
					{
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN');;
					}
					
					if($post->delboy_count <= 0 ){
						$disable	= 'disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NO_DELBOY_AVAILABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NO_DELBOY_AVAILABLE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_NO_DELBOY_AVAILABLE');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
					}
					elseif($post->avail_delboy <= 0){
						$disable	= 'disabled="disabled"';
						$name 		= '';
						$noDelBoy 	= (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ALL_DELBOY_BUSY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ALL_DELBOY_BUSY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ALL_DELBOY_BUSY');
						$iClass 	= ' <i class="fa fa-info-circle tooltip-demo" aria-hidden="true" title="'.$noDelBoy.'"></i>';
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
						$disable	= '';
						$name 		= 'name="chk"';
						$iClass 	= '';
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
		
		public function delboy_withoutRejected_ajax(Request $request)
		{
			$splitInput = explode('`',$request->ord_transaction_id);
			//DB::connection()->enableQueryLog();
			$getstoreId = DB::table('gr_order')->select('ord_rest_id')->where('ord_transaction_id','=',$splitInput[0])->where('ord_merchant_id','=',$splitInput[1])->first();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			if(empty($getstoreId)==false){
				$storeId = $getstoreId->ord_rest_id;
			}
			
			$agents =  DB::table('gr_order_reject_history') ->where('store_id', $storeId)->where('order_id',$splitInput[0])->where('agent_id','=','0')->where('delboy_id','!=','0')->get();
			
			/*$query = DB::getQueryLog();*/
			$agentArray = array();
			if(count($agents) > 0 ){
				foreach($agents as $agent){
					array_push($agentArray,$agent->delboy_id);
				}
			}
			//print_r($agentArray); exit;
			
			
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
			$sql = DB::table('gr_delivery_member')->select('deliver_id')->where('deliver_status','<>','2')->where('deliver_avail_status','=','1');
			if(count($agentArray) > 0)
			{
				$sql = $sql->whereNotIn('deliver_id',$agentArray);
			}	
			$totalData =  $sql->count();
			
			//$totalData = DB::table('gr_agent')->select('agent_id')->where('agent_status','<>','2')->count();
			$totalFiltered = $totalData; 
			/*EOF get Total count */
			$limit 	= $request->input('length');
			$start 	= $request->input('start');
			$order 	= $columns[$request->input('order.0.column')];
			$dir 	= $request->input('order.0.dir');
			
			//if(empty($request->input('search.value')))
			$delboyName_search 	= trim($request->delboyName_search); 
			$delboyEmail_search = trim($request->delboyEmail_search); 
			$delboyPhone_search	= trim($request->delboyPhone_search); 
			$publish_search 	= trim($request->publish_search); 
			$current_time 		= date('H:i');
			$current_day		= date('l');
			
			if($delboyName_search=='' && $delboyEmail_search=='' && $delboyPhone_search=='' && $publish_search=='')
			{    
				//DB::connection()->enableQueryLog();
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
				/*DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/
				DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`= ? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
				DB::Raw('(SELECT count(*) FROM gr_order WHERE ord_status >= 5 AND ord_status < 8 AND ord_delivery_memid=`gr_delivery_member`.`deliver_id` AND ord_agent_acpt_status=1 AND ord_delivery_memid != "" AND ord_delboy_act_status=1) AS pending_order_delboy'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status="8" ) as suxes_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status="9" ) as failed_order'),
				DB::Raw('(SELECT count(ord_transaction_id) FROM gr_order WHERE `ord_delivery_memid`=`gr_delivery_member`.`deliver_id` AND ord_status >="4" AND ord_status <= "7" ) as pending_order')
				)
				->where('deliver_status','<>','2')
				->where('deliver_avail_status','=','1');
				
				if(count($agentArray) > 0)
				{
					$q = $sql->whereNotIn('deliver_id',$agentArray);
				}	
				$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts =  $q->get();
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
				/*DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`="'.$current_day.'" AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC("'.$current_time.'") AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC("'.$current_time.'")) AS delboy_induty')*/
				DB::Raw('(SELECT count(*) FROM gr_deliver_working_hrs WHERE `dw_deliver_id`=`gr_delivery_member`.`deliver_id` AND `dw_date`= ? AND dw_status="1" AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC(?) AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC(?)) AS delboy_induty', [$current_day,$current_time,$current_time]),
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
				if(count($agentArray) > 0)
				{
					$q = $sql->whereNotIn('agent_id',$agentArray);
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
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM');;
					}
					else
					{
						$fareType = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN');;
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
						$disable	= '';
						$name 		= 'name="chk"';
						$iClass 	= '';
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
		
		public function reassign_order_to_agent(Request $request)
		{
			$ord_transaction_id = explode(",",$request->ord_transaction_id);
			$agent_id = $request->agent_id;
			if(count($ord_transaction_id) > 0 )
			{
				foreach($ord_transaction_id as $ordtransid)
				{
					$split_value = explode('`',$ordtransid);
					$updatable_trans_id = $split_value[0];
					$merchant_id = $split_value[1];
					DB::table('gr_order')->where('ord_transaction_id', $updatable_trans_id)->where('ord_merchant_id',$merchant_id)->update(['ord_task_status' => '1','ord_agent_id'=>$agent_id,'ord_delmgr_id'=>Session::get('DelMgrSessId'),'ord_taskassigned_date'=>date('Y-m-d H:i:s'),'ord_agent_acpt_status' => '0','ord_agent_rjct_reason'=>NULL,'ord_agent_rejected_at'=>NULL]);
				}
			}
		}
		
		public function reassign_order_to_delboy(Request $request)
		{
			$ord_transaction_id = explode(",",$request->ord_transaction_id);
			$delboy_id = $request->delboy_id;
			if(count($ord_transaction_id) > 0 )
			{
				foreach($ord_transaction_id as $ordtransid)
				{
					$split_value = explode('`',$ordtransid);
					$updatable_trans_id = $split_value[0];
					$merchant_id = $split_value[1];
					DB::table('gr_order')->where('ord_transaction_id', $updatable_trans_id)->where('ord_merchant_id',$merchant_id)->update(['ord_task_status' => '1','ord_agent_id'=>'0','ord_delivery_memid'=>$delboy_id,'ord_agent_acpt_status'=>'1','ord_agent_acpt_read_status'=>'1','ord_delmgr_id'=>Session::get('DelMgrSessId'),'ord_delboy_act_status'=>0,'ord_taskassigned_date'=>date('Y-m-d H:i:s'),'ord_delboy_assigned_on'=>date('Y-m-d H:i:s'),'ord_agent_rjct_reason'=>NULL,'ord_agent_rejected_at'=>NULL,'ord_delboy_rjct_reason'=>NULL,'ord_delboy_rjct_time'=>NULL]);
				}
			}
		}
	}
?>