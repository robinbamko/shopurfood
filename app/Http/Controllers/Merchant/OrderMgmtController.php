<?php
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Merchant;
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
	use App\MerchantReports;
	use App\Settings;
	use Excel;
	use Response;
	use File;
	use Image;
	use App\Home;
	
	class OrderMgmtController extends Controller
	{
		
		public function __construct(){
			parent::__construct();
			// set admin Panel language
			$this->setLanguageLocaleMerchant();
		}
		
		public function deals_all_orders()
		{
			if(Session::has('merchantid') == 1)
			{
				$from_date = Input::get('from_date');
				$to_date   = Input::get('to_date');
				$ord_status   = Input::get('ord_status');
				
				$orderdetails   = MerchantReports::getall_dealreports($from_date, $to_date,$ord_status);
				//print_r($orderdetails); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_MGMT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ORDER_MGMT');       
				return view('sitemerchant.reports.manage_orders')->with('pagetitle',$page_title)->with('orderdetails',$orderdetails)->with('from_date',$from_date)->with('to_date',$to_date)->with('ord_status',$ord_status);
				} else {
				return Redirect::to('merchant-login');
			}
		}
		public function InvoiceOrder($id)
		{
			$id=base64_decode($id);
			DB::table('gr_order')->where('ord_transaction_id', $id)->where('ord_merchant_id','=',Session::get('merchantid'))->update(['ord_merchant_viewed' => '1']);
			$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INVOICE_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVOICE_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INVOICE_DETAILS');
			$allchoices = array();
			//DB::connection()->enableQueryLog();
			$Invoice_Order = DB::table('gr_order')
			->select('gr_order.ord_id',
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.order_ship_mail',
			'gr_store.st_store_name',
			'gr_product.pro_item_name',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_pay_type',
			'gr_order.ord_date',
			'gr_order.ord_spl_req',
			'gr_order.ord_transaction_id',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_had_choices',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_order.ord_self_pickup',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_customer.cus_address',
			'gr_customer.cus_phone1',
			'gr_customer.cus_email'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_merchant_id','=',Session::get('merchantid'))
			->where('gr_order.ord_transaction_id','=',$id)->get();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			$get_payment = '';//DB::table('tb_admin_vendor_payment_setting')->get();
			/* Start-Sathyaseelan getting choices */
			if(count($Invoice_Order)>0)
			{
				foreach($Invoice_Order as $orders)
				{
					$choices = array();
					$splitted_choice=json_decode($orders->ord_choices,true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choices[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								$choices[$choices_name]=$choice['choice_price'];
							}
						}
					}
					$allchoices[$orders->ord_id] = $choices;
				}
			}
			//TRACK ORDER DETAILS
			$storewise_details   = MerchantReports::track_reports($id);
			//print_r($Invoice_Order);echo '<hr>';print_r($choices); exit;
			/* End-Sathyaseelan getting choices */
			// echo print_r($choices);die;
			return view ('sitemerchant.reports.InvoiceOrder')->with('Invoice_Order',$Invoice_Order)->with('choices',$allchoices)->with('pagetitle',$pagetitle)->with('storewise_details',$storewise_details);
		}
		public function order_detail($id)
		{
			$ord_transaction_id = base64_decode($id);
			DB::table('gr_order')->where('ord_transaction_id', $ord_transaction_id)->where('ord_merchant_id','=',Session::get('merchantid'))->update(['ord_merchant_viewed' => '1']);
			$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ORDER_DETAILS');
			$pagetitle .= ' - '.$ord_transaction_id;
			
			
			$choices=array();
			//DB::connection()->enableQueryLog();// ord_status
			$Invoice_Order = DB::table('gr_order')
			->select('gr_order.ord_id',
			'gr_order.ord_merchant_id',
			'gr_product.pro_item_name',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_status',
			'gr_order.ord_pay_type',
			'gr_order.ord_had_choices',
			'gr_order.ord_currency',
			'gr_order.ord_cancel_status',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_failed_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_self_pickup',
			'gr_order.ord_grant_total'
			)
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$ord_transaction_id)
			->where('gr_order.ord_merchant_id','=',Session::get('merchantid'))
			->get();
			//$query = DB::getQueryLog();
			//print_r($query);
			//print_r($Invoice_Order);
			//exit; 
			$get_payment = '';//DB::table('tb_admin_vendor_payment_setting')->get();
			/* Start-Sathyaseelan getting choices */
			if(count($Invoice_Order)>0)
			{
				foreach($Invoice_Order as $orders)
				{
					$splitted_choice=json_decode($orders->ord_choices,true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choices[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								$choices[$choices_name]=$choice['choice_price'];
							}
						}
					}
					
				}
			}
			$status_qry= DB::table('gr_order')->select('ord_id',DB::raw("sum(case when ord_status = '3' AND ord_cancel_status='1' then 1 else 0 end) as rejected, sum(case when ord_status != '3' AND ord_cancel_status='1' then 1 else 0 end) as cancelled, count(*) as totals,sum(case when ord_status = '9' then 1 else 0 end) as failed"))->where('ord_transaction_id',$ord_transaction_id)->where('ord_merchant_id',Session::get('merchantid'))->groupBy('ord_transaction_id')->first();
			$ord_status_chart = DB::table('gr_order')->select('ord_status','ord_self_pickup','ord_pre_order_date')->where('ord_status','!=','3')->where('ord_cancel_status','!=','1')->where('ord_transaction_id',$ord_transaction_id)->where('ord_merchant_id',Session::get('merchantid'))->first();
			$merchant_payment_det = DB::table('gr_customer')->select('cus_paynamics_status','cus_paymaya_status','cus_netbank_status')->where('cus_id','=',Session::get('customer_id'))->first();
			return view ('sitemerchant.reports.order_details')->with('Invoice_Order',$Invoice_Order)->with('choices',$choices)->with('pagetitle',$pagetitle)->with('merchant_payment_det',$merchant_payment_det)->with('status_qry',$status_qry)->with('ord_status_chart',$ord_status_chart)->with('ord_transaction_id',$id);
		}
		public function TrackOrder($id)
		{
			$id=base64_decode($id);
			DB::table('gr_order')->where('ord_transaction_id', $id)->where('ord_merchant_id','=',Session::get('merchantid'))->update(['ord_merchant_viewed' => '1']);
			$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_TRACK_ORDER')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRACK_ORDER') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_TRACK_ORDER');
			$customer_details = DB::table('gr_order')->select('ord_transaction_id','ord_date','ord_pre_order_date','ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail')->where('ord_transaction_id',$id)->first();
			$storewise_details   = MerchantReports::track_reports($id);
			return view ('sitemerchant.reports.order-tracking')->with('storewise_details',$storewise_details)->with('customer_details',$customer_details)->with('pagetitle',$pagetitle);
			
		}
		public function rejectStatus(Request $request)
		{
			/*print_r($request->all()); exit;Array ( [reason] => test [orderId] => 1 ) */
			/*START HERE */
			$order_id=$request->orderId;
			$Reason = mysql_escape_special_chars($request->reason);
			$ord_grand = DB::table('gr_order')->select('ord_grant_total','ord_merchant_id','ord_pay_type','ord_transaction_id','ord_admin_amt','order_ship_mail','ord_cus_id','ord_merchant_id','gr_merchant.mer_fname','gr_merchant.mer_lname','ord_refund_status','ord_quantity','ord_pro_id','ord_delivery_fee','gr_customer.cus_andr_fcm_id','gr_customer.cus_ios_fcm_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
			->leftJoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
			->where('ord_id','=',$order_id)->first();
			if(empty($ord_grand)===false)
			{
				$ord_transaction_id=$ord_grand->ord_transaction_id;
				//echo $ord_transaction_id.'<br>';
				DB::table('gr_order')->where('ord_id', $order_id)->update(['ord_status' => '3','ord_cancel_status' => '1','ord_cancel_reason'=>$Reason,'ord_reject_reason'=>$Reason,'ord_cancel_date'=>date('Y-m-d H:i:s')]);
				
				$get_total_order_count = get_total_order_count($ord_transaction_id);
				$get_total_cancelled_count = get_total_cancelled_count($ord_transaction_id);
				$cancel_amt = 0;
				if($get_total_cancelled_count==$get_total_order_count)
				{
					$add_delfee_status=1;
					$cancel_amt = $ord_grand->ord_grant_total + $ord_grand->ord_delivery_fee;
					$del_fee_overall = 0;
					$mer_amount = $ord_grand->ord_grant_total-$ord_grand->ord_admin_amt+$ord_grand->ord_delivery_fee;
				}
				else
				{
					$add_delfee_status=0;
					$cancel_amt = $ord_grand->ord_grant_total;
					$del_fee_overall = $ord_grand->ord_delivery_fee;
					$mer_amount = $ord_grand->ord_grant_total-$ord_grand->ord_admin_amt;
				}
				//echo 'Cancel Amount'.$cancel_amt; exit;
				/** update reject status **/
				/** while user cancel the item, admin detect commission from merchant amount **/
				/* if refund status is no, no need to add cancellation amount */
				
				DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_status' => '3','ord_cancel_status' => '1','ord_cancel_reason'=>$Reason,'ord_reject_reason'=>$Reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_cancel_amt' => $ord_grand->ord_grant_total]);
				
				$Getorderdetails=DB::table('gr_order')->where('ord_id', $request->orderId)->select('*')->where('ord_merchant_id',Session::get('merchantid'))->first();
				$get_details = Home::merchant_orderDetails(Session::get('merchantid'));
				//$mer_amt	 = $get_details->or_mer_amt + ( $Getorderdetails->ord_grant_total-$Getorderdetails->ord_admin_amt);
				$mer_amt	 = $get_details->or_mer_amt + ( $mer_amount);
				DB::table('gr_merchant_overallorder')->where('or_mer_id', $ord_grand->ord_merchant_id)->update(['or_mer_amt' => $mer_amt,'or_cancel_amt' => DB::raw('or_cancel_amt+'.($cancel_amt))]);
				
				updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
				/*if($ord_grand->ord_refund_status == 'Yes')
					{
					DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_status' => '3','ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_reject_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_cancel_amt' => $cancel_amt]);
					$Getorderdetails=DB::table('gr_order')->where('ord_id', $request->orderId)->select('*')->where('ord_merchant_id',Session::get('merchantid'))->first();
					$get_details = Home::merchant_orderDetails(Session::get('merchantid'));
					$mer_amt	 = $get_details->or_mer_amt + ( $Getorderdetails->ord_grant_total-$Getorderdetails->ord_admin_amt);
					DB::table('gr_merchant_overallorder')->where('or_mer_id', $ord_grand->ord_merchant_id)->update(['or_mer_amt' => $mer_amt,'or_cancel_amt' => DB::raw('or_cancel_amt+'.($cancel_amt))]);
					
					updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
					}
					else
					{
					DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_status' => '3','ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_reject_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_rejected_on' => date('Y-m-d H:i:s')]);
					updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
				}*/
			}
			$customerMail = $ord_grand->order_ship_mail;
			$customerId   = $ord_grand->ord_cus_id;
			$merchantId   = $ord_grand->ord_merchant_id;
			$merchantName = ucfirst($ord_grand->mer_fname).' '.$ord_grand->mer_lname;
			$admin_det = get_admin_details();
			$admin_id  = $admin_det->id;
			/*ENDS HERE*/
			//echo $request->id.'/'.$request->status;
			//CANCELLED BY TRANSACTION ID
			/*$getAll = DB::table('gr_order')->select('ord_id','ord_cus_id','order_ship_mail')->where('ord_id', $request->orderId)->where('ord_merchant_id',Session::get('merchantid'))->get();
				$customerMail = '';
				if(count($getAll) > 0 )
				{
				$i=1;
				foreach($getAll as $geta)
				{	
				$customerMail =  $geta->order_ship_mail;
				if(count($getAll)==$i)
				{
				$add_delfee_status=1;
				}
				else
				{
				$add_delfee_status=0;
				}
				DB::table('gr_order')->where('ord_id', $geta->ord_id)->where('ord_merchant_id',Session::get('merchantid'))->update(['add_delfee_status'=>$add_delfee_status,'ord_status' => '3','ord_reject_reason'=>$request->reason,'ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s')]);
				$i++;
				}
				}
				//echo $request->orderId; exit;
				DB::connection()->enableQueryLog();
				$updateCancelAmount = DB::table('gr_order')->select(DB::Raw('SUM(ord_grant_total) as grand_total'),DB::Raw('SUM(ord_admin_amt) as grand_commission'),'ord_delivery_fee','ord_merchant_id')->where('ord_transaction_id', $request->orderId)->where('ord_merchant_id',Session::get('merchantid'))->groupBy('ord_transaction_id')->first();
				$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				if(empty($updateCancelAmount)===false)
				{
				$totalCancelAmount = $updateCancelAmount->grand_total;
				DB::table('gr_merchant_overallorder')->where('or_mer_id', $updateCancelAmount->ord_merchant_id)->update(['or_cancel_amt' => DB::raw('or_cancel_amt+'.$totalCancelAmount)]);
			}*/
			/** mail function ord_self_pickup  customerMail**/
			$get_item_details = MerchantReports::get_rejected_details_byOrderId(Session::get('merchantid'),$request->orderId);
			$productNameArray = array();
			if(count($get_item_details) > 0 )
			{
				foreach($get_item_details as $item_detail){
					array_push($productNameArray,ucfirst($item_detail->item_name));
				}
			}
			
			/* ---------- SEND NOTIFICATION TO CUSTOMER ----------------*/
			
			$got_message = (Lang::has(Session::get('mer_lang_file').'.MER_REJECT_NOTIFICATION')) ? trans(Session::get('mer_lang_file').'.MER_REJECT_NOTIFICATION') : trans($this->MER_OUR_LANGUAGE.'.MER_REJECT_NOTIFICATION') ;
			$message = str_replace(':transaction_id', $ord_transaction_id, $got_message);
			$message = str_replace(':item_name', implode(',',$productNameArray), $message);
			$message = str_replace(':merchant_name', ucfirst($merchantName), $message);
			
			$message_link = 'order-details/'.base64_encode($ord_transaction_id);
			push_notification($merchantId,$customerId,'gr_merchant','gr_customer',$message,$ord_transaction_id,$message_link); 
			/* send notification to customer mobile */
			if($ord_grand->cus_andr_fcm_id != '' || $ord_grand->cus_ios_fcm_id != '')
			{
				if($ord_grand->cus_andr_fcm_id !='')
				{
					$parse_fcm=json_decode($ord_grand->cus_andr_fcm_id,true);
					$reg_id = array();
					if(count($parse_fcm) > 0 )
					{
						foreach($parse_fcm as $parsed)
						{ 
							array_push($reg_id,$parsed['fcm_id']);						
						}
					}
							$json_data = ["registration_ids" => $reg_id,
											"notification" => ["body" => $message,"title" => "Order Rejected Notification"]
										];
							$notify =sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_CUS);
						
				}
				if($ord_grand->cus_ios_fcm_id !='')
				{
					$parse_fcm=json_decode($ord_grand->cus_ios_fcm_id,true);
					$reg_id = array();
					if(count($parse_fcm) > 0 )
					{
						foreach($parse_fcm as $parsed)
						{ 
							array_push($reg_id,$parsed['fcm_id']);						
						}
					}
							$json_data = ["registration_ids" => $reg_id,
											"notification" => ["body" => $message,"title" => "Order Rejected Notification","sound"				=> "default"]
										];
							$notify =sendPushNotification($json_data,IOS_FIREBASE_API_KEY_CUS);
						
				}
			}
			/* send notification to customer mobile ends */
			/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
			$message_link = 'admin-track-order/'.base64_encode($ord_transaction_id);
			push_notification($merchantId,$admin_id,'gr_merchant','gr_admin',$message,$ord_transaction_id,$message_link);
			
			//1) MAIL TO CUSTOMER
			$send_mail_data = array('order_details'	=> $get_item_details,'transaction_id'=>$request->orderId);
			Mail::send('email.reject_mail_customer', $send_mail_data, function($message) use($customerMail)
			{
				$message->to($customerMail)->subject((Lang::has(Session::get('mer_lang_file').'.ADMIN_REJECT_OR')) ? trans(Session::get('mer_lang_file').'.ADMIN_REJECT_OR') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REJECT_OR'));
			});
			//2) MAIL TO ADMIN
			$adminMail = $this->admin_mail;
			
			Mail::send('email.reject_mail_admin', $send_mail_data, function($message) use($adminMail)
			{
				$message->to($adminMail)->subject((Lang::has(Session::get('mer_lang_file').'.ADMIN_REJECT_OR_DETAIL')) ? trans(Session::get('mer_lang_file').'.ADMIN_REJECT_OR_DETAIL') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REJECT_OR_DETAIL'));
			});
			//3) MAIL TO MERCHANT
			$mer_mail = Session::get('mer_email');
			Mail::send('email.reject_mail_merchant', $send_mail_data, function($message) use($mer_mail)
			{
				$message->to($mer_mail)->subject((Lang::has(Session::get('mer_lang_file').'.ADMIN_REJECT_OR_DETAIL')) ? trans(Session::get('mer_lang_file').'.ADMIN_REJECT_OR_DETAIL') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REJECT_OR_DETAIL'));
			});
			
			/** eof mail function **/
			Session::flash('message',(Lang::has(Session::get('mer_lang_file').'.MER_STATUS_SUXS')) ? trans(Session::get('mer_lang_file').'.MER_STATUS_SUXS') : trans($this->MER_OUR_LANGUAGE.'.MER_STATUS_SUXS'));
		}
		public function changeStatus(Request $request)
		{
            //echo $request->id.'/'.$request->status;
			$status = $request->status;
			$updateArr = array();
			if($status == '2') 
			{
				$updateArr = ['ord_status' => $request->status,'ord_accepted_on' => date('Y-m-d H:i:s')];
			}
			elseif($status == '3')
			{
				$updateArr = ['ord_status' => $request->status,'ord_rejected_on' => date('Y-m-d H:i:s')];
			}
			elseif($status == '4')
			{
				$updateArr = ['ord_status' => $request->status,'ord_prepared_on' => date('Y-m-d H:i:s')];
			}
			elseif($status == '8')
			{
				$updateArr = ['ord_status' => $request->status,'ord_delivered_on' => date('Y-m-d H:i:s')];
			}
			else
			{
				$updateArr = ['ord_status' => $request->status];
			}
			
			//This is add by karthik on 29012019 for comit and rollback
			//Here merchant amount is add/updated when change status to prepare to delivery
			
			DB::beginTransaction();
			try{
				
				$Getorderdetails=DB::table('gr_order')->where('ord_id', $request->id)->select('ord_admin_amt','ord_self_pickup','ord_wallet','ord_grant_total')
				->where('ord_merchant_id',Session::get('merchantid'))->first();
				
				DB::table('gr_order')->where('ord_id', $request->id)
				->where('ord_merchant_id',Session::get('merchantid'))
				->update($updateArr);
				
				if(($status == '4' && $Getorderdetails->ord_self_pickup=='0') || ($status =='8' && $Getorderdetails->ord_self_pickup=='1'))
				{					
					$get_details = Home::merchant_orderDetails(Session::get('merchantid'));
					
					if(empty($get_details) === false)		//Update
					{
						
						$order_count = $get_details->or_total_order + 1;
						$admin_amt	 = $get_details->or_admin_amt + $Getorderdetails->ord_admin_amt; //ord_admin_amt
						$wallet_amt	 = $get_details->or_coupon_amt + $Getorderdetails->ord_wallet;
						$mer_amt	 = $get_details->or_mer_amt + ( $Getorderdetails->ord_grant_total-$Getorderdetails->ord_admin_amt);
						
						// update in merchant overall table 
						$array = [	'or_mer_amt'	=>	$mer_amt];
						$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => Session::get('merchantid')]);
						
					}
					
				}	
				Session::flash('message',(Lang::has(Session::get('mer_lang_file').'.MER_STATUS_SUXS')) ? trans(Session::get('mer_lang_file').'.MER_STATUS_SUXS') : trans($this->MER_OUR_LANGUAGE.'.MER_STATUS_SUXS'));
				DB::commit();
			}
			catch(\Exception $e){
				DB::rollBack();
				return $e->getMessage();
			}
			
		}
		
	}
