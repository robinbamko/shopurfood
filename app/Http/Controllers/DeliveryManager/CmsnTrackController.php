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
	use App\DeliveryManager;
	use Response;
	/*-------------------- FOR PAYMAYA  ----------------*/
	use Config;
	/*use Aceraven777\PayMaya\PayMayaSDK;
		use Aceraven777\PayMaya\API\Checkout;
		use Aceraven777\PayMaya\Model\Checkout\Item;
		use App\Libraries\PayMaya\User as PayMayaUser;
		use Aceraven777\PayMaya\Model\Checkout\ItemAmount;
		use Aceraven777\PayMaya\Model\Checkout\ItemAmountDetails;
	use Aceraven777\PayMaya\Model\Checkout\Address;*/
	/* PAYPAL SECTION */
	use PayPal\Api\Amount;
	use PayPal\Api\Details;
	use PayPal\Api\Item;
	use PayPal\Api\ItemList;
	use PayPal\Api\Payer;
	use PayPal\Api\Payment;
	use PayPal\Api\RedirectUrls;
	use PayPal\Api\Transaction;
	use PayPal\Auth\OAuthTokenCredential;
	use PayPal\Rest\ApiContext;
	use PayPal\Api\ExecutePayment;
	use PayPal\Api\PaymentExecution;
	use PayPal\Exception\PayPalConfigurationException;
	use PayPal\Exception\PayPalConnectionException;
	
	class CmsnTrackController extends Controller
	{
		private $_api_context;
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
		}
		/* --------------- IF AGENT MODULE IS ENABLED ---------------- */
		public function commision_list()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_COMMISSION_TRACKING');
			/*DB::enableQueryLog();
				$totalData = DB::table('gr_agent_earnings')
				->select(DB::Raw('CONCAT(agent_fname," ",agent_lname) AS agent_name'),
				'agent_email',
				DB::Raw('COUNT(*)AS total_orders'),
				DB::Raw('SUM(gr_agent_earnings.ae_total_amount) AS total_commission_amt'),
				DB::Raw('(SELECT SUM(ae_order_total) FROM gr_agent_earnings WHERE ae_agent_id = gr_agent_earnings.ae_agent_id AND ae_pay_type="COD") AS total_order_amt'),
				DB::Raw('(SELECT SUM(commission_paid) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_paid_amt')
				)
				->leftJoin('gr_agent','gr_agent.agent_id','=','gr_agent_earnings.ae_agent_id')
				->where('gr_agent.agent_status','<>','2')
				->get();
				$query = DB::getQueryLog();
				print_r($query);
			exit;*/
			$commission_details = array();//MerchantReports::get_commission_list();
			return view('DeliveryManager.reports.Manage_Commission')->with('pagetitle',$pagetitle)->with('commission_list',$commission_details);
		}
		
		public function ajax_commision_list(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$columns = array( 
			0=> 'ae_id',
			1 =>'agent_fname', 
			//1 =>'agentEmail', 
			2 =>'total_orders',
			3=> 'total_order_amt',
			4=> 'total_commission_amt',
			5=> 'total_paid_amt',
			6=> 'total_rcvd_amt',
			7=> 'ae_id',
			8=> 'ae_id',
			9=> 'ae_id',
			10=> 'ae_id'
			//10=> 'ae_id'
			);
			/*To get Total count */
			
			$totalData = DB::table('gr_agent_earnings')
			->select('ae_id')
			->leftJoin('gr_agent','gr_agent.agent_id','=','gr_agent_earnings.ae_agent_id')
			->where('gr_agent.agent_status','<>','2')
			->count();
			$totalFiltered = $totalData; 
			/*EOF get Total count */
			$limit = $request->input('length');
			$start = $request->input('start');
			$draw = $request->input('draw');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');
			
			$agentEmail_search = trim($request->agentEmail_search); 
			//$agentName_search = trim($request->agentName_search); 
			
			$sql = 	DB::table('gr_agent_earnings')
			->select('ae_agent_id','ae_pay_type',
			DB::Raw("CONCAT(if(gr_agent.agent_fname is null,'',gr_agent.agent_fname),' ',if(gr_agent.agent_lname is null,'',gr_agent.agent_lname)) AS agent_name"),
			'gr_agent.agent_email',
			DB::Raw('COUNT(*) AS total_orders'),
			DB::Raw('SUM(gr_agent_earnings.ae_total_amount) AS total_commission_amt'),
			//DB::Raw('SUM(Case When ae_pay_type="COD" Then ae_order_total Else 0 End) AS total_order_amt'),
			DB::Raw('SUM(ae_order_total) AS total_order_amt'),
			DB::Raw('SUM(ae_rcd_amt) AS total_online_amt'),
			DB::Raw('(SELECT SUM(commission_paid) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_paid_amt'),
			DB::Raw('(SELECT SUM(amount_received) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_rcvd_amt'),
			'ae_ord_currency',
			'gr_agent.mer_paymaya_clientid',
			'gr_agent.mer_paymaya_secretid',
			'gr_agent.mer_paymaya_status',
			'gr_agent.mer_netbank_status',
			'gr_agent.mer_bank_accno',
			'gr_agent.mer_bank_accno',
			'gr_agent.mer_branch',
			'gr_agent.mer_ifsc'
			);
			if($agentEmail_search != '')
			{ 
				/*$q = $sql->whereRaw("CONCAT_WS(' ', gr_agent.agent_fname, gr_agent.agent_lname, gr_agent.agent_email) like '%".$agentEmail_search."%'"); */
				$q = $sql->whereRaw("CONCAT_WS(' ', gr_agent.agent_fname, gr_agent.agent_lname, gr_agent.agent_email) like ?", ['%'.$agentEmail_search.'%']); 
			}
			
			$q = $sql->leftJoin('gr_agent','gr_agent.agent_id','=','gr_agent_earnings.ae_agent_id');
			$q = $sql->where('gr_agent.agent_status','<>','2');
			$q = $sql->groupBy('gr_agent_earnings.ae_agent_id');
			//DB::connection()->enableQueryLog();
			$totalFiltered = $q->count();
			$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
			$posts =  $q->get();
			
			$viewText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_TRANSACTION');
			$payThruText = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAY_THRU')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAY_THRU') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAY_THRU');
			$paymayaText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMAYA');
			$payOfflineText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAY_OFFLINE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAY_OFFLINE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAY_OFFLINE');
			$payRequestText = (Lang::has(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ')) ? trans(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ') : trans($this->DELMGR_OUR_LANGUAGE.'.SEND_PAY_REQ');
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				foreach ($posts as $post)
				{
					
					//CHECKING TRANSACTION IS DONE
					if($post->total_paid_amt > 0 || $post->total_rcvd_amt > 0) 
					{
						$view_transaxn = '<a href="'.url('').'/delmgr_view_transaction/'.$post->ae_agent_id.'" class="btn btn-warning btn-sm">'.$viewText.'</a>';
					}
					else
					{
						$view_transaxn = '-';
					}
					$nestedData['SNo'] = ++$snoCount;
					$nestedData['agentName'] = $post->agent_name.'<br>('.$post->agent_email.')';
					//$nestedData['agentEmail'] = $post->agent_email; total_rcvd_amt
					$nestedData['totalOrders'] = $post->total_orders;
					$nestedData['totalOrderAmt'] = number_format($post->total_order_amt,2);
					$nestedData['totalRcvdAmtCOD'] = number_format($post->total_rcvd_amt,2);
					//$nestedData['totalRcvdAmtOnline'] = number_format($post->total_online_amt,2);
					$nestedData['totComisonAmt'] = number_format($post->total_commission_amt,2);
					$nestedData['paidAmt'] = number_format($post->total_paid_amt,2);
					//$step1 = $post->total_order_amt-($post->total_rcvd_amt+$post->total_online_amt);
					$step1 = $post->total_order_amt-$post->total_rcvd_amt;
					$step2 = $post->total_commission_amt-$post->total_paid_amt;
					//$balAmtToPay = number_format($step1-$step2,2);
					$balAmtToPay = ($step1-$step2);
					$nestedData['balAmtToPay'] = ($balAmtToPay < 0) ? number_format(abs($balAmtToPay),2) : '0.00';
					$nestedData['balAmtToReceive'] = ($balAmtToPay > 0) ? number_format(abs($balAmtToPay),2) : '0.00';
					
					if($balAmtToPay < 0)
					{
						$paymayaForm='';
						if($post->mer_paymaya_status=='Publish')
						{
							$paymayaForm='
							<form method="post" action="'.url('').'/paymaya-commission_agent" id="validate_form">
							<input name="_token" type="hidden" value="'.csrf_token().'">
							<input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.number_format(abs($balAmtToPay),2).'">
							<input name="client_id" id="client_id" type="hidden" value="'.$post->mer_paymaya_clientid.'">
							<input name="secret_id" id="secret_id" type="hidden" value="'.$post->mer_paymaya_secretid.'">
							<input name="agent_id" id="agent_id" type="hidden" value="'.$post->ae_agent_id.'">
							<button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.$payThruText.' '.$paymayaText.'">'.$paymayaText.'</button>
							</form>
							';
							
						}
						if($post->mer_netbank_status == "Publish")
						{	
							
							$bank_details = "'".$post->mer_bank_accno.'`'.$post->mer_bank_accno.'`'.$post->mer_branch.'`'.$post->mer_ifsc."'";
							$paymayaForm .=' <a href="javascript:;"  class="btn btn-success btn-sm" onclick="payFun('.$post->ae_agent_id.',\''.abs($balAmtToPay).'\',\''.$post->ae_ord_currency.'\','.$bank_details.')">'.$payOfflineText.'</a>';
						}
						if($post->mer_paymaya_status == 'Unpublish' && $post->mer_netbank_status == 'Unpublish')
						{
							$paymayaForm .= (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_PAY_DET_NOT')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_PAY_DET_NOT') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_PAY_DET_NOT');
						}
						$action = $paymayaForm.'';
						}elseif($balAmtToPay > 0){
						$action = ' <a href="'.url('agent_pay_request').'/'.base64_encode(abs($balAmtToPay)).'/'.base64_encode($post->ae_agent_id).'"  class="btn btn-success btn-sm">'.$payRequestText.'</a>';
					}
					else
					{
						$action='-';
					}	
					
					$nestedData['viewTransaxn'] = $view_transaxn;
					$nestedData['action'] = $action;
					$data[] = $nestedData;
				}
			}
			//print_r($request->input('length')); exit;
			$json_data = array(
			"draw"            => intval($draw),  
			"recordsTotal"    => intval($totalData),  
			"recordsFiltered" => intval($totalFiltered), 
			"data"            => $data   
			);
			
			echo json_encode($json_data); 
		}
		///commission_view_transaction
		
		
		/* --------------- IF AGENT MODULE IS DISABLED ---------------- */
		public function delBoy_commision_list()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_COMMISSION_TRACKING');
			$commission_details = array();
			return view('DeliveryManager.reports.Manage_Commission1')->with('pagetitle',$pagetitle)->with('commission_list',$commission_details);
		}
		
		
			public function ajax_delBoy_commision_list(Request $request)
			{
		
				$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
				$columns = array(
				0 =>'deliver_fname',
				//1 =>'agentEmail',
				1 =>'total_orders',
				2=> 'total_order_amt',
				3=> 'total_commission_amt',
				4=> 'total_paid_amt',
				5=> 'total_rcvd_amt',
				6=> 'de_id',
				7=> 'de_id',
				8=> 'de_id',
				9=> 'de_id',
				10=> 'de_id',
				//10=> 'ae_id'
				);
				/*To get Total count */
		
				$totalData = DB::table('gr_delivery_person_earnings')
							->select('de_id')
							->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_delivery_person_earnings.de_deliver_id')
							->where('gr_delivery_member.deliver_status','<>','2')
							->count();
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$draw = $request->input('draw');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
		
				$delboyEmail_search = trim($request->delboyEmail_search);
				//$agentName_search = trim($request->agentName_search);
		
				$sql = 	DB::table('gr_delivery_person_earnings')
						->select('de_deliver_id','de_pay_type',
								DB::Raw('CONCAT(gr_delivery_member.deliver_fname," ",gr_delivery_member.deliver_lname) AS delboy_name'),
								'gr_delivery_member.deliver_email',
								DB::Raw('COUNT(*) AS total_orders'),
								DB::Raw('SUM(gr_delivery_person_earnings.de_total_amount) AS total_commission_amt'),
								//DB::Raw('SUM(Case When ae_pay_type="COD" Then ae_order_total Else 0 End) AS total_order_amt'),
								DB::Raw('SUM(de_order_total) AS total_order_amt'),
								DB::Raw('SUM(de_rcd_amt) AS total_online_amt'),
								DB::Raw('(SELECT SUM(commission_paid) FROM gr_delboy_commission WHERE delboy_id = gr_delivery_person_earnings.de_deliver_id AND commission_status="2") AS total_paid_amt'),
								DB::Raw('(SELECT SUM(amount_received) FROM gr_delboy_commission WHERE delboy_id = gr_delivery_person_earnings.de_deliver_id AND commission_status="2") AS total_rcvd_amt'),
								'de_ord_currency',
								'gr_delivery_member.deliver_stripe_status',
								'gr_delivery_member.deliver_stripe_clientid',
								'gr_delivery_member.deliver_stripe_secretid',
								'gr_delivery_member.deliver_paypal_status',
								'gr_delivery_member.deliver_paypal_clientid',
								'gr_delivery_member.deliver_paypal_secretid',
								'gr_delivery_member.deliver_netbank_status',
								'gr_delivery_member.deliver_bank_accno',
								'gr_delivery_member.deliver_bank_name',
								'gr_delivery_member.deliver_branch',
								'gr_delivery_member.deliver_ifsc'
								);
				if($delboyEmail_search != '')
				{
					/*$q = $sql->whereRaw("CONCAT_WS(' ', gr_delivery_member.deliver_fname, gr_delivery_member.deliver_lname, gr_delivery_member.deliver_email) like '%".$delboyEmail_search."%'");*/
					$q = $sql->whereRaw("CONCAT_WS(' ', gr_delivery_member.deliver_fname, gr_delivery_member.deliver_lname, gr_delivery_member.deliver_email) like ?", ['%'.$delboyEmail_search.'%']);
				}
		
				$q = $sql->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_delivery_person_earnings.de_deliver_id');
				$q = $sql->where('gr_delivery_member.deliver_status','<>','2');
				$q = $sql->groupBy('gr_delivery_person_earnings.de_deliver_id');
				//DB::connection()->enableQueryLog();
				$totalFiltered = $q->count();
				$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts =  $q->get();
		
				$viewText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_TRANSACTION');
				$payThruText = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAY_THRU')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAY_THRU') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAY_THRU');
				$stripeText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYNAMICS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYNAMICS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAYNAMICS');
				$paymayaText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMAYA');
				$payOfflineText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAY_OFFLINE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAY_OFFLINE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAY_OFFLINE');
				$payRequestText = (Lang::has(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ')) ? trans(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ') : trans($this->DELMGR_OUR_LANGUAGE.'.SEND_PAY_REQ');
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
		
						//CHECKING TRANSACTION IS DONE
						if($post->total_paid_amt > 0 || $post->total_rcvd_amt > 0)
						{
							$view_transaxn = '<a href="'.url('').'/delmgr_view_transaction1/'.$post->de_deliver_id.'" class="btn btn-warning btn-sm">'.$viewText.'</a>';
						}
						else
						{
							$view_transaxn = '-';
						}
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['agentName'] = $post->delboy_name.'<br>('.$post->deliver_email.')';
						//$nestedData['agentEmail'] = $post->agent_email; total_rcvd_amt
						$nestedData['totalOrders'] = $post->total_orders;
						$nestedData['totalOrderAmt'] = number_format($post->total_order_amt,2);
						$nestedData['totalRcvdAmtCOD'] = number_format($post->total_rcvd_amt,2);
						//$nestedData['totalRcvdAmtOnline'] = number_format($post->total_online_amt,2);
						$nestedData['totComisonAmt'] = number_format($post->total_commission_amt,2);
						$nestedData['paidAmt'] = number_format($post->total_paid_amt,2);
						//$step1 = $post->total_order_amt-($post->total_rcvd_amt+$post->total_online_amt);
						$step1 = $post->total_order_amt-$post->total_rcvd_amt;
						$step2 = $post->total_commission_amt-$post->total_paid_amt;
						//$balAmtToPay = number_format($step1-$step2,2);
						$balAmtToPay = ($step1-$step2);
						$nestedData['balAmtToPay'] = ($balAmtToPay < 0) ? number_format(abs($balAmtToPay),2) : '0.00';
						$nestedData['balAmtToReceive'] = ($balAmtToPay > 0) ? number_format(abs($balAmtToPay),2) : '0.00';
						//INITIALLY USED ONLY  if($balAmtToPay < 0) . BUT IT SHOWN WRONG AMOUNT. SO NOW USING abs() FUNCTION
						//if(number_format(abs($balAmtToPay),2) < 0)
							$action='';
						if(number_format($balAmtToPay,2) < 0)
						{
							$paymayaForm='';
							if($post->deliver_paypal_status=='Publish')
							{
								$paymayaForm .='
									<form method="post" action="'.url('').'/paypal-commission-delboy" id="validate_form">
										<input name="_token" type="hidden" value="'.csrf_token().'">
										<input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.number_format(abs($balAmtToPay),2).'">
										<input name="client_id" id="client_id" type="hidden" value="'.$post->deliver_paypal_clientid.'">
										<input name="secret_id" id="secret_id" type="hidden" value="'.$post->deliver_paypal_secretid.'">
										<input name="delboy_id" id="delboy_id" type="hidden" value="'.$post->de_deliver_id.'">
										<button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.$payThruText.' '.$paymayaText.'">'.$paymayaText.'</button>
									</form>
								';
		
							}
							//if($post->deliver_stripe_status=='Publish')
							//{
								// $paymayaForm .='
									// <form method="post" action="'.url('').'/stripe-commission-delboy" id="validate_form">
										// <input name="_token" type="hidden" value="'.csrf_token().'">
										// <input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.number_format(abs($balAmtToPay),2).'">
										// <input name="currency" id="currency" type="hidden" value="'.$post->de_ord_currency.'">
										// <input name="client_id" id="client_id" type="hidden" value="'.$post->deliver_stripe_clientid.'">
										// <input name="secret_id" id="secret_id" type="hidden" value="'.$post->deliver_stripe_secretid.'">
										// <input name="delboy_id" id="delboy_id" type="hidden" value="'.$post->de_deliver_id.'">
										// <button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.$payThruText.' '.$stripeText.'">'.$stripeText.'</button>
									// </form>
								// ';
							//}
							if($post->deliver_netbank_status == "Publish")
							{
								$bank_details = "'".$post->deliver_bank_accno.'`'.$post->deliver_bank_name.'`'.$post->deliver_branch.'`'.$post->deliver_ifsc."'";
								$paymayaForm .=' <a href="javascript:;"  class="btn btn-success btn-sm" onclick="payFun('.$post->de_deliver_id.',\''.abs($balAmtToPay).'\',\''.$post->de_ord_currency.'\','.$bank_details.')">'.$payOfflineText.'</a>';
							}
							//$post->deliver_stripe_status == 'Unpublish' &&
							if(($post->deliver_netbank_status == 'Unpublish' || $post->deliver_netbank_status == '') && ($post->deliver_paypal_status == 'Unpublish' || $post->deliver_paypal_status == ''))
							{
								$paymayaForm .= (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_PAY_DET_NOT')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_PAY_DET_NOT') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_PAY_DET_NOT');
							}
						   $action = $paymayaForm;
						}elseif(number_format($balAmtToPay,2) > 0){
							$action = ' <a href="'.url('delboy_pay_request').'/'.base64_encode(abs($balAmtToPay)).'/'.base64_encode($post->de_deliver_id).'"  class="btn btn-success btn-sm">'.$payRequestText.'</a>';
						}
						else
						{
							$action='-';
						}
		
						$nestedData['viewTransaxn'] = $view_transaxn;
						$nestedData['action'] = $action;
						$data[] = $nestedData;
					}
				}
				//print_r($request->input('length')); exit;
				$json_data = array(
				"draw"            => intval($draw),
				"recordsTotal"    => intval($totalData),
				"recordsFiltered" => intval($totalFiltered),
				"data"            => $data
				);
		
				echo json_encode($json_data);
			}
		
		
		public function ajax_delBoy_commision_list_bk_8_3_19(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$columns = array(
			0=> 'ae_id',
            1 =>'agent_fname',
            //1 =>'agentEmail',
            2 =>'total_orders',
            3=> 'total_order_amt',
            4=> 'total_commission_amt',
            5=> 'total_paid_amt',
            6=> 'total_rcvd_amt',
            7=> 'ae_id',
            8=> 'ae_id',
            9=> 'ae_id',
            10=> 'ae_id'
            //10=> 'ae_id'
			);
			/*To get Total count */
			
			$totalData = DB::table('gr_agent_earnings')
            ->select('ae_id')
            ->leftJoin('gr_agent','gr_agent.agent_id','=','gr_agent_earnings.ae_agent_id')
            ->where('gr_agent.agent_status','<>','2')
            ->count();
			$totalFiltered = $totalData;
			/*EOF get Total count */
			$limit = $request->input('length');
			$start = $request->input('start');
			$draw = $request->input('draw');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');
			
			$agentEmail_search = trim($request->agentEmail_search);
			//$agentName_search = trim($request->agentName_search);
			
			$sql = 	DB::table('gr_agent_earnings')
			->select('ae_agent_id','ae_pay_type',
			DB::Raw("CONCAT(if(gr_agent.agent_fname is null,'',gr_agent.agent_fname),' ',if(gr_agent.agent_lname is null,'',gr_agent.agent_lname)) AS agent_name"),
			'gr_agent.agent_email',
			DB::Raw('COUNT(*) AS total_orders'),
			DB::Raw('SUM(gr_agent_earnings.ae_total_amount) AS total_commission_amt'),
			//DB::Raw('SUM(Case When ae_pay_type="COD" Then ae_order_total Else 0 End) AS total_order_amt'),
			DB::Raw('SUM(ae_order_total) AS total_order_amt'),
			DB::Raw('SUM(ae_rcd_amt) AS total_online_amt'),
			DB::Raw('(SELECT SUM(commission_paid) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_paid_amt'),
			DB::Raw('(SELECT SUM(amount_received) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_rcvd_amt'),
			'ae_ord_currency',
			'gr_agent.mer_paymaya_clientid',
			'gr_agent.mer_paymaya_secretid',
			'gr_agent.mer_paymaya_status',
			'gr_agent.mer_netbank_status',
			'gr_agent.mer_bank_accno',
			'gr_agent.mer_bank_accno',
			'gr_agent.mer_branch',
			'gr_agent.mer_ifsc'
            );
			if($agentEmail_search != '')
			{
				/*$q = $sql->whereRaw("CONCAT_WS(' ', if(gr_agent.agent_fname is null,'',gr_agent.agent_fname), if(gr_agent.agent_lname is null,'',gr_agent.agent_lname), if(gr_agent.agent_email is null,'',gr_agent.agent_email)) like '%".$agentEmail_search."%'");*/
				$q = $sql->whereRaw("CONCAT_WS(' ', if(gr_agent.agent_fname is null,'',gr_agent.agent_fname), if(gr_agent.agent_lname is null,'',gr_agent.agent_lname), if(gr_agent.agent_email is null,'',gr_agent.agent_email)) ?", ['%'.$agentEmail_search.'%']);
			}
			
			$q = $sql->leftJoin('gr_agent','gr_agent.agent_id','=','gr_agent_earnings.ae_agent_id');
			$q = $sql->where('gr_agent.agent_status','<>','2');
			$q = $sql->groupBy('gr_agent_earnings.ae_agent_id');
			//DB::connection()->enableQueryLog();
			$totalFiltered = $q->count();
			$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
			$posts =  $q->get();
			
			$viewText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_TRANSACTION');
			$payThruText = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAY_THRU')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAY_THRU') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAY_THRU');
			$paymayaText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMAYA');
			$payOfflineText = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAY_OFFLINE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAY_OFFLINE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PAY_OFFLINE');
			$payRequestText = (Lang::has(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ')) ? trans(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ') : trans($this->DELMGR_OUR_LANGUAGE.'.SEND_PAY_REQ');
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				foreach ($posts as $post)
				{
					
					//CHECKING TRANSACTION IS DONE
					if($post->total_paid_amt > 0 || $post->total_rcvd_amt > 0)
					{
						$view_transaxn = '<a href="'.url('').'/delmgr_view_transaction/'.$post->ae_agent_id.'" class="btn btn-warning btn-sm">'.$viewText.'</a>';
					}
					else
					{
						$view_transaxn = '-';
					}
					$nestedData['SNo'] = ++$snoCount;
					$nestedData['agentName'] = $post->agent_name.'<br>('.$post->agent_email.')';
					//$nestedData['agentEmail'] = $post->agent_email; total_rcvd_amt
					$nestedData['totalOrders'] = $post->total_orders;
					$nestedData['totalOrderAmt'] = number_format($post->total_order_amt,2);
					$nestedData['totalRcvdAmtCOD'] = number_format($post->total_rcvd_amt,2);
					//$nestedData['totalRcvdAmtOnline'] = number_format($post->total_online_amt,2);
					$nestedData['totComisonAmt'] = number_format($post->total_commission_amt,2);
					$nestedData['paidAmt'] = number_format($post->total_paid_amt,2);
					//$step1 = $post->total_order_amt-($post->total_rcvd_amt+$post->total_online_amt);
					$step1 = $post->total_order_amt-$post->total_rcvd_amt;
					$step2 = $post->total_commission_amt-$post->total_paid_amt;
					//$balAmtToPay = number_format($step1-$step2,2);
					$balAmtToPay = ($step1-$step2);
					$nestedData['balAmtToPay'] = ($balAmtToPay < 0) ? number_format(abs($balAmtToPay),2) : '0.00';
					$nestedData['balAmtToReceive'] = ($balAmtToPay > 0) ? number_format(abs($balAmtToPay),2) : '0.00';
					
					if($balAmtToPay < 0)
					{
						$paymayaForm='';
						if($post->mer_paymaya_status=='Publish')
						{
							$paymayaForm='
							<form method="post" action="'.url('').'/paymaya-commission_agent" id="validate_form">
							<input name="_token" type="hidden" value="'.csrf_token().'">
							<input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.number_format(abs($balAmtToPay),2).'">
							<input name="client_id" id="client_id" type="hidden" value="'.$post->mer_paymaya_clientid.'">
							<input name="secret_id" id="secret_id" type="hidden" value="'.$post->mer_paymaya_secretid.'">
							<input name="agent_id" id="agent_id" type="hidden" value="'.$post->ae_agent_id.'">
							<button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.$payThruText.' '.$paymayaText.'">'.$paymayaText.'</button>
							</form>
							';
							
						}
						if($post->mer_netbank_status == "Publish")
						{
							
							$bank_details = "'".$post->mer_bank_accno.'`'.$post->mer_bank_accno.'`'.$post->mer_branch.'`'.$post->mer_ifsc."'";
							$paymayaForm .=' <a href="javascript:;"  class="btn btn-success btn-sm" onclick="payFun('.$post->ae_agent_id.',\''.abs($balAmtToPay).'\',\''.$post->ae_ord_currency.'\','.$bank_details.')">'.$payOfflineText.'</a>';
						}
						if($post->mer_paymaya_status == 'Unpublish' && $post->mer_netbank_status == 'Unpublish')
						{
							$paymayaForm .= (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_PAY_DET_NOT')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_PAY_DET_NOT') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_PAY_DET_NOT');
						}
						$action = $paymayaForm.'
						';
						}elseif($balAmtToPay > 0){
						$action = ' <a href="'.url('agent_pay_request').'/'.base64_encode(abs($balAmtToPay)).'/'.base64_encode($post->ae_agent_id).'"  class="btn btn-success btn-sm">'.$payRequestText.'</a>';
					}
					else
					{
						$action='-';
					}
					
					$nestedData['viewTransaxn'] = $view_transaxn;
					$nestedData['action'] = $action;
					$data[] = $nestedData;
				}
			}
			//print_r($request->input('length')); exit;
			$json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
			);
			
			echo json_encode($json_data);
		}
		
		
		
		public function commission_view_transaction($vendor_id)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_COMMISSION_TRACKING');
			$pagetitle .= ' - ';
			$pagetitle .=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_TRANSACTION');
			//DB::enableQueryLog();
			$commission_list = DB::table('gr_agent_commission')
			->select(
			'gr_agent_commission.amount_received',
			'gr_agent_commission.commission_paid',
			'gr_agent_commission.transaction_id',
			'gr_agent_commission.pay_type',
			'gr_agent_commission.commission_date',
			'gr_agent_commission.commission_currency'
			)
			->where('gr_agent_commission.agent_id','=',$vendor_id)
			->where('gr_agent_commission.commission_status','=','2')
			->orderby('gr_agent_commission.commission_date','DESC')->paginate(10);
			return view('DeliveryManager.reports.View_Commission_transaction')->with('pagetitle',$pagetitle)->with('commission_list',$commission_list);
		}
		
		public function commission_view_transaction1($vendor_id)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_COMMISSION_TRACKING');
			$pagetitle .= ' - ';
			$pagetitle .=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW_TRANSACTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW_TRANSACTION');
			//DB::enableQueryLog();
			$commission_list = DB::table('gr_delboy_commission')
			->select(
			'gr_delboy_commission.amount_received',
			'gr_delboy_commission.commission_paid',
			'gr_delboy_commission.transaction_id',
			'gr_delboy_commission.pay_type',
			'gr_delboy_commission.commission_date',
			'gr_delboy_commission.commission_currency'
			)
			->where('gr_delboy_commission.delboy_id','=',$vendor_id)
			->where('gr_delboy_commission.commission_status','=','2')
			->orderby('gr_delboy_commission.commission_date','DESC')->paginate(10);
			return view('DeliveryManager.reports.View_Commission_transaction1')->with('pagetitle',$pagetitle)->with('commission_list',$commission_list);
		}
		
		/** send pay request to agent **/ 
		public function pay_request($amount,$agent_id)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$amount = base64_decode($amount);
			$agent_id = base64_decode($agent_id);
			/** update notification count **/
			$update = DB::table('gr_delmgr_notification')->insert(['no_delmgr_id' => Session::get('DelMgrSessId'),'no_agent_id'=>$agent_id,'no_status' => '1','submit_by'=>Session::get('DelMgrSessId') ]);
			/** Send  Mail  **/
			$agent_details = DB::table('gr_agent')->where('agent_id','=',$agent_id)->first();
			$send_mail_data = array('name' => $agent_details->agent_fname.' '.$agent_details->agent_lname, 'amount' => $amount, 'dm_name' => Session::get('dm_name'),'agent_email'=>$agent_details->agent_email, 'lang'=>$this->DELMGR_OUR_LANGUAGE);
			Mail::send('email.pay_request_to_agent', $send_mail_data, function($message) use($send_mail_data)
			{
				$delmgr_details = DB::table('gr_merchant')->where('id','=',Session::get('DelMgrSessId'))->first();
				$subject = (Lang::has(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ')) ? trans(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ') : trans($send_mail_data['lang'].'.SEND_PAY_REQ');
				$message->to($send_mail_data['agent_email'], $send_mail_data['name'])->subject($subject);
			});
			/** Send mail ends **/
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_REQ_SENT_SUXES')) ? trans(Session::get('DelMgr_lang_file').'.DEL_REQ_SENT_SUXES') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_REQ_SENT_SUXES');
			Session::flash('message',$msg);
			return Redirect::back();
		}
		/* ---------------- PAY REQUEST FROM DELIVERY BOY TO DELIVERY MANAGER ------------------- */
		public function pay_request1($amount,$delboy_id)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$amount = base64_decode($amount);
			$delboy_id = base64_decode($delboy_id);
			/** update notification count **/
			$update = DB::table('gr_delmgr_notification')->insert(['no_delmgr_id' => Session::get('DelMgrSessId'),'no_delboy_id'=>$delboy_id,'no_status' => '1','submit_by'=>Session::get('DelMgrSessId') ]);
			/** Send  Mail  **/
    		$delboy_details = DB::table('gr_delivery_member')->where('deliver_id','=',$delboy_id)->first();
    		$send_mail_data = array('name' => $delboy_details->deliver_fname.' '.$delboy_details->deliver_lname, 'amount' => $amount, 'dm_name' => Session::get('dm_name'),'delboy_email'=>$delboy_details->deliver_email, 'lang'=>$this->DELMGR_OUR_LANGUAGE);
			Mail::send('email.pay_request_to_agent', $send_mail_data, function($message) use($send_mail_data)
			{
				$delmgr_details = DB::table('gr_merchant')->where('id','=',Session::get('DelMgrSessId'))->first();
				$subject = (Lang::has(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ')) ? trans(Session::get('DelMgr_lang_file').'.SEND_PAY_REQ') : trans($send_mail_data['lang'].'.SEND_PAY_REQ');
				$message->to($send_mail_data['delboy_email'], $send_mail_data['name'])->subject($subject);
			});
			/** Send mail ends **/
			$get_message = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAY_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAY_NOTIFICATION') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAY_NOTIFICATION');
			$searchReplaceArray = array(':user_name' => Session::get('dm_name'),':amount' => Session::get('del_default_curr_sym').' '.$amount);
			$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$get_message); 
			if(empty($delboy_details) === false)
			{
				/* send notification to delivery person  mobile */
				if($delboy_details->deliver_andr_fcm_id !='')
				{
					$parse_fcm=json_decode($delboy_details->deliver_andr_fcm_id,true);
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
									"notification" 	=> ["body" => $result,"title" => "Pay Request Notification"]
									];
					$notify = sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_DEL);
						
				}
				if($delboy_details->deliver_ios_fcm_id !='')
				{
					$parse_fcm=json_decode($delboy_details->deliver_ios_fcm_id,true);
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
										"notification" 	=> ["body" => $result,"title" => "Pay Request Notification",
										"sound"				=> "default"]
										];
					$notify = sendPushNotification($json_data,IOS_FIREBASE_API_KEY_DEL);
						
				}
				/* send notification to delivery person mobile ends */
			}
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_REQ_SENT_SUXES')) ? trans(Session::get('DelMgr_lang_file').'.DEL_REQ_SENT_SUXES') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_REQ_SENT_SUXES');
			Session::flash('message',$msg);
			return Redirect::back();
		}
		/* OFFLINE PAYMENT */
		public function pay_to_deliverymanager(Request $request){
			$insertArr = [	
			'agent_id' 				=> $request->agent_id,
			'commission_paid'	 	=> $request->agent_balance,
			'commission_currency'	=> $request->agent_curr,
			'commission_status'		=> '2',
			'transaction_id' 		=> $request->trans_id,
			'pay_type' 				=> '0',
			'commission_date'		=> date('Y-m-d H:i:s')];
			$insert = insertvalues('gr_agent_commission',$insertArr);
			/**delete notification **/
			DB::table('gr_delmgr_notification')->where(['no_status' => '1','no_delmgr_id' => Session::get('DelMgrSessId'), 'submit_by'=>$request->agent_id])->delete();
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAYMENT_PAID_SUXES')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAYMENT_PAID_SUXES') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAYMENT_PAID_SUXES');
			Session::flash('message',$msg);
			return Redirect::to('delivery-commission-tracking');
		}
		
		public function pay_to_deliveryboy(Request $request){
			$insertArr = [	
			'delboy_id' 			=> $request->agent_id,
			'commission_paid'	 	=> $request->agent_balance,
			'commission_currency'	=> $request->agent_curr,
			'commission_status'		=> '2',
			'transaction_id' 		=> $request->trans_id,
			'pay_type' 				=> '0',
			'commission_date'		=> date('Y-m-d H:i:s')];
			$insert = insertvalues('gr_delboy_commission',$insertArr);
			/**delete notification **/
			DB::table('gr_delmgr_notification')->where(['no_status' => '1','no_delmgr_id' => Session::get('DelMgrSessId'), 'no_delboy_id'=>$request->agent_id, 'submit_by'=>$request->agent_id])->delete();
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAYMENT_PAID_SUXES')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAYMENT_PAID_SUXES') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAYMENT_PAID_SUXES');
			Session::flash('message',$msg);
			return Redirect::to('deliveryboy-commission-tracking');
		}
		
		/* ---------------------- COMMISSION TO DELIVERY BOY - PAYPAL  ----------------------------*/
		public function commission_paypal_delboy(Request $request){
			/*print_r($request->all());
				exit;
			Array ( [_token] => u1EqvRzyBnHiAR01fAptBH55o7m1wrEB71lwWBAG [amt_to_pay] => 0.00 [client_id] => Acw2kxT_SEDvM1Ph61U2sWmByv7Urm8ckeISw-_Sq3RzQt1QgD3n6v-lIR7mBk2XcVCAM5fpvn3xaY0- [secret_id] => EA7vzEvTJZ5X42WAwCYdjuvYd_cHaZeN2zj4_PcEVHAsf8OIx9gtTcdFbSRt-wkIs5uhUuUVZ8K2WJmQ [delboy_id] => 1 )*/
			Session::forget('paypal_client_id');
			Session::forget('paypal_secret_id');
			Session::forget('paypal_payment_id');
			
			$delboy_id = $request->delboy_id;
			Session::put('paypal_client_id', $request->client_id);
			Session::put('paypal_secret_id', $request->secret_id);
			/** PayPal api context **/
			$paypal_conf = \Config::get('paypal');
			$this->_api_context = new ApiContext(new OAuthTokenCredential($request->client_id,$request->secret_id));
			$this->_api_context->setConfig($paypal_conf['settings']);
			
			$getAdminCurrency = Session::get('default_currency_code');
			$amt_to_pay = $request->amt_to_pay;
			if($getAdminCurrency!='USD'){
				$amt_to_pay = convertCurrency($getAdminCurrency,'USD',$amt_to_pay);
			}
			$payer = new Payer();
			$payer->setPaymentMethod('paypal');
			$item_1 = new Item();
			$item_1	->setName('Commission Payment')
			->setCurrency('USD')
			->setQuantity(1)
			->setPrice($amt_to_pay);
			
			$item_list = new ItemList();
			$item_list->setItems(array($item_1));
			
			$amount = new Amount();
			$amount	->setCurrency('USD')
			->setTotal($amt_to_pay);
			
			$transaction = new Transaction();
			$transaction->setAmount($amount)
			->setItemList($item_list)
			->setDescription('Commission Payment to Delivery boy`'.$delboy_id.'`'.$request->amt_to_pay);
			
			$redirectUrl = new RedirectUrls();
			$redirectUrl->setReturnUrl(url('paypal-commission-success-delboy')) /** Specify return URL **/
			->setCancelUrl(url('paypal-commission-failure-delboy'));
			$payment = new Payment();
			$payment->setIntent('Sale')
			->setPayer($payer)
			->setRedirectUrls($redirectUrl)
			->setTransactions(array($transaction));
			
			try {
				$payment->create($this->_api_context);
				} catch (\PayPal\Exception\PPConnectionException $ex) {
				if (\Config::get('app.debug')) {
					return Redirect::to('/deliveryboy-commission-tracking')->withErrors(['errors' => '']);
					} else {
					return Redirect::to('/deliveryboy-commission-tracking')->withErrors(['errors' => 'Some error occur, sorry for inconvenient']);
				}
			}
			foreach ($payment->getLinks() as $link) {
				if ($link->getRel() == 'approval_url') {
					$redirect_url = $link->getHref();
					break;
				}
			}
			Session::put('paypal_payment_id', $payment->getId());
			if (isset($redirect_url)) {
				/** redirect to paypal **/
				return Redirect::away($redirect_url);
			}
			\Session::put('error', 'Unknown error occurred');
			return redirect()->to('paypal-commission-failure-delboy');
		}
		public function paypal_cmsn_suxes(Request $request)
		{
			/** PayPal api context **/
			$paypal_conf = \Config::get('paypal');
			$this->_api_context = new ApiContext(new OAuthTokenCredential(Session::get('paypal_client_id'),Session::get('paypal_secret_id')));
			$this->_api_context->setConfig($paypal_conf['settings']);
			try {
				$payment_id = Session::get('paypal_payment_id');
				if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
					return Redirect::to('/deliveryboy-commission-tracking')->withErrors(['errors' => 'Payment Failed']);
				}
				$payment = Payment::get($payment_id, $this->_api_context);
				$execution = new PaymentExecution();
				$execution->setPayerId(Input::get('PayerID'));
				$result = $payment->execute($execution, $this->_api_context);
				if ($result->getState() == 'approved') {
					$gotDesc = explode('`',$result->transactions[0]->description);
					$delboy_id = $gotDesc[1];
					$paidAmount = $gotDesc[2];
					$transaction_id = $result->id;
					$insertArr = ['delboy_id' 			=> $delboy_id,
					'commission_paid'	 	=> $paidAmount,
					'commission_currency'	=> Session::get('default_currency_symbol'),
					'commission_status'		=> '2',
					'transaction_id' 		=> $transaction_id,
					'commission_date'		=> date('Y-m-d H:i:s'),
					'pay_type'				=> '1'
					];
					$insert = insertvalues('gr_delboy_commission',$insertArr);
					/**delete notification **/
					DB::table('gr_delmgr_notification')->where(['no_status' => '1','no_delmgr_id' => Session::get('DelMgrSessId'), 'no_delboy_id'=>$request->agent_id, 'submit_by'=>$request->agent_id])->delete();
					$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAYMENT_PAID_SUXES')) ? trans(Session::get('DelMgr_lang_file').'.DEL_PAYMENT_PAID_SUXES') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_PAYMENT_PAID_SUXES');
					Session::flash('message',$msg);
					return Redirect::to('deliveryboy-commission-tracking');
					} else { 
					return Redirect::to('/deliveryboy-commission-tracking')->withErrors(['errors' => 'Payment Failed']);
				}
			}
			catch (PayPal\Exception\PPConnectionException $pce) {
				return Redirect::to('/deliveryboy-commission-tracking')->withErrors(['errors' => 'Payment Failed']);
			}
		}
		public function paypal_cmsn_fail(Request $request)
		{
			return Redirect::to('/deliveryboy-commission-tracking')->withErrors(['errors' => 'Payment Failed']);
		}
		
		/* PAYMAYA PAYMENT */
		public function commission_payment(Request $request)
		{
			Session::forget('sess_checkoutId');
			Session::forget('sess_agent_clientId');
			Session::forget('sess_agent_secretId');
			/*print_r($request->all()); exit;Array ( [_token] => 29ufcHJkJGyGhX9KxaINjVJekL1PAte6CuzNUyNf [amt_to_pay] => 13.87 [client_id] => 123456 [secret_id] => 3652 [merchant_id] => 63 )*/
			//echo env('PAYMAYA_PUBLIC_KEY')
			Session::put('sess_agent_clientId',$request->client_id);
			Session::put('sess_agent_secretId',$request->secret_id);
			
			PayMayaSDK::getInstance()->initCheckout($request->client_id,$request->secret_id,Config::get('env.PAYMAYA_MODE'));
			$agent_id=$request->agent_id;
			$agent_det = DB::table('gr_agent')->where('agent_id',$agent_id)->where('agent_status','1')->first();
			if(empty($agent_det)===false)
			{
				$sample_item_name = 'Commission Payment';
				$sample_total_price = $request->amt_to_pay;
				//echo Session::get('default_currency_code').','.'PHP'.','.$request->amt_to_pay; exit;
				$converted_amount = convertCurrency(Session::get('default_currency_code'),'PHP',$request->amt_to_pay);
				$sample_user_phone = $agent_det->agent_phone1;
				$sample_user_email = $agent_det->agent_email;
				// Item
				$itemAmountDetails = new ItemAmountDetails();
				$itemAmountDetails->tax = "0.00";
				$itemAmountDetails->subtotal = $converted_amount;
				
				$itemAmount = new ItemAmount();
				$itemAmount->currency = "PHP";
				$itemAmount->value = $converted_amount;
				$itemAmount->details = $itemAmountDetails;
				
				$item = new Item();
				$item->name = $sample_item_name;
				$item->amount = $itemAmount;
				$item->quantity = '1';
				$item->totalAmount = $itemAmount;
				
				// Checkout
				$itemCheckout = new Checkout();
				$user = new PayMayaUser();
				$user->firstName = $agent_det->agent_fname;
				$user->middleName='agentId-'.$agent_id.'-'.$request->amt_to_pay;
				$user->lastName = $agent_det->agent_lname;
				$user->contact->phone = $sample_user_phone;
				$user->contact->email = $sample_user_email;
				
				$address = new Address();
				$address->line1 = $agent_det->agent_location;
				$user->shippingAddress = $address;
				
				
				$sample_reference_number = 'mer-'.rand();
				$itemCheckout->buyer = $user->buyerInfo();
				
				$itemCheckout->items = array($item);
				$itemCheckout->totalAmount = $itemAmount;
				$itemCheckout->requestReferenceNumber = $sample_reference_number;
				$itemCheckout->redirectUrl = array("success" => url('agent_commision_success'),
				"failure" => url('agent_commision_failure'),
				"cancel" => url('agent_commision_failure'));
				
				//echo "<pre>"; print_r($itemCheckout); exit;
				if ($itemCheckout->execute() === false) {
					$error = $itemCheckout::getError();
					//print_r($error); exit;
					//Session::flash('message',$error['message']);
					return redirect()->back()->withErrors(['errors' => $error['message']]);
				}
				
				if ($itemCheckout->retrieve() === false) {
					$error = $itemCheckout::getError();
					//print_r($error); exit;
					//Session::flash('message',$error['message']);
					return redirect()->back()->withErrors(['errors' => $error['message']]);
				}
				
				Session::put('sess_checkoutId',$itemCheckout->id);
				return redirect()->to($itemCheckout->url);
			}
			else
			{
				//Session::flash('message','No merchant found');
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_NO_MER_FOUND')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_NO_MER_FOUND') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_NO_MER_FOUND');
				return Redirect::to('/delivery-commission-tracking')->withErrors(['errors' => $msg]);
			}
		}
		/* Checkout success */
		public function checkout_success(Request $request)
		{	
			PayMayaSDK::getInstance()->initCheckout(Session::get('sess_agent_clientId'),Session::get('sess_agent_secretId'),Config::get('env.PAYMAYA_MODE'));
			//PayMayaSDK::getInstance()->initCheckout(env('PAYMAYA_PUBLIC_KEY'),env('PAYMAYA_SECRET_KEY'),Config::get('env.PAYMAYA_MODE'));
			$transaction_id = Session::get('sess_checkoutId');
			if (!$transaction_id) {
				//Session::flash('message','Transaction Id Missing');
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_TRANS_ID_MISS')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_TRANS_ID_MISS') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_TRANS_ID_MISS');
				return Redirect::to('/delivery-commission-tracking')->withErrors(['message' => $msg]);
			}
			$itemCheckout = new Checkout();
			$itemCheckout->id = $transaction_id;
			$checkout = $itemCheckout->retrieve();
			//echo '<pre>'; print_r($checkout); exit;
			if($checkout['paymentStatus'] == "PAYMENT_SUCCESS")
			{
				$agent_midName = explode('-',$checkout['buyer']['middleName']);
				$agent_id = $agent_midName[1];
				$paidAmount = $agent_midName[2];
				$insertArr = ['agent_id' => $agent_id,
				'commission_paid'	 	=> $paidAmount,
				'commission_currency'	=> Session::get('default_currency_symbol'),
				'commission_status'		=> '2',
				'transaction_id' 		=> $checkout['requestReferenceNumber'],
				'paymaya_pmtId'			=> $checkout['transactionReferenceNumber'],
				'paymaya_receiptnum'	=> $checkout['receiptNumber'],
				'paymaya_paid_time'		=> $checkout['paymentDetails']['paymentAt'],
				'paymaya_last4'			=> $checkout['paymentDetails']['last4'],
				'paymaya_cardtype'		=> $checkout['paymentDetails']['cardType'],
				'paymaya_maskedcard'	=> $checkout['paymentDetails']['maskedCardNumber'],
				'commission_date'		=> date('Y-m-d H:i:s'),
				'pay_type'				=> '1'
				];
				$insert = insertvalues('gr_agent_commission',$insertArr);
				/**delete notification **/
				DB::table('gr_delmgr_notification')->where(['no_status' => '1','no_delmgr_id' => Session::get('DelMgrSessId'), 'submit_by'=>$agent_id])->delete();
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_COMM_PAID_SUXES')) ? trans(Session::get('DelMgr_lang_file').'.DEL_COMM_PAID_SUXES') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_COMM_PAID_SUXES');
				Session::flash('message',$msg);
				return Redirect::to('/delivery-commission-tracking');
			}
			else
			{
				//Session::flash('message','Payment Failure');
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_PAY_FAIL')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_PAY_FAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_PAY_FAIL');
				return Redirect::to('agent_commision_failure')->withErrors(['errors' => $msg]);
			}
		}
		public function checkout_failure(Request $request)
		{
			//print_r($request->all());
			//PayMayaSDK::getInstance()->initCheckout('pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz','sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',(\App::environment('production') ? 'SANDBOX' : 'SANDBOX'));
			
			PayMayaSDK::getInstance()->initCheckout(Session::get('sess_agent_clientId'),Session::get('sess_agent_secretId'),Config::get('env.PAYMAYA_MODE'));
			$transaction_id = Session::get('sess_checkoutId');
			if (!$transaction_id) {
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_TRANS_ID_MISS')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_TRANS_ID_MISS') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_TRANS_ID_MISS');
				//Session::flash('message',$msg);
				return Redirect::to('delivery-commission-tracking')->withErrors(['errors' => $msg]);
				//return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'Transaction Id Missing']);
			}
			$itemCheckout = new Checkout();
			$itemCheckout->id = $transaction_id;
			//$itemCheckout->execute();
			$checkout = $itemCheckout->retrieve();
			//Session::flash('message','Payment Failure \n Error Code: '.$checkout['paymentStatus']);
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_PAY_FAIL_CODE')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_PAY_FAIL_CODE') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_PAY_FAIL_CODE');
			return Redirect::to('/delivery-commission-tracking')->withErrors(['errors' => $msg.' '.$checkout['paymentStatus']]);
		}
	}	