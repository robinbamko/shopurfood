<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Front;
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\URL;
	use Validator;
	use Session;
	use Mail;
	use View;
	use Lang;
	use Redirect;
	use App\Home;
	use Config;
	
	
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
	
	
	
	class PaymayaController extends Controller
	{
		private $_api_context;
		public function __construct()
		{
			parent::__construct();
			
			/** PayPal api context **/
			$paypal_conf = \Config::get('paypal');
			$this->_api_context = new ApiContext(new OAuthTokenCredential(
			$paypal_conf['client_id'],
			$paypal_conf['secret'])
			
			);
			$this->_api_context->setConfig($paypal_conf['settings']);
		}
		
		/** send notification starts **/
		
        
		// function makes curl request to firebase servers
		
		private  function sendPushNotification($fields,$key) 
		{ 
			
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
		
		public function paymaya_checkout(Request $request)
		{
			
			Session::forget('sess_checkoutId');
			$validator = Validator::make($request->all(), ['ord_self_pickup' => 'required']);
			$request->ord_self_pickup = (int)$request->ord_self_pickup;
			if($request->ord_self_pickup==0)
			{
				$validator = Validator::make($request->all(), [
				'name' => 'required',
				'lname' => 'required',
				'mail' => 'required',
				'phone1' => 'required',
				//'phone2' =>'required',
				'lat' =>'required',
				'long' =>'required',
				'address' =>'required',
				'paymentMode' =>'required'],
				['name.required' => (Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL'),
				'lname.required' => (Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_SECONDNAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_SECONDNAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_SECONDNAME_VAL'),
				'mail.required' => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL'),
				'phone1.required' => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE'),
				//'phone2.required' => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE'),
				'lat.required' => (Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_LAT_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_LAT_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_LAT_VAL'),
				'long.required' => (Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_LONG_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_LONG_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_LONG_VAL'),
				'paymentMode.required' => (Lang::has(Session::get('front_lang_file').'.FRONT_SL_PAY_MODE')) ? trans(Session::get('front_lang_file').'.FRONT_SL_PAY_MODE') : trans($this->FRONT_LANGUAGE.'.FRONT_SL_PAY_MODE'),
				]);
			}
			if ($validator->fails())
			{
				return Redirect::back()->withErrors($validator)->withInput();
			}
			else
			{
				
				$cus_id		= (int)Session::get('customer_id');
				$profile_address = DB::table('gr_customer')->select('cus_fname','cus_lname','cus_email','cus_phone1','cus_phone2','cus_address','cus_latitude','cus_longitude')->where('cus_id',$cus_id)->first();
				$name  		= ($request->name == '' )	? $profile_address->cus_fname 	: mysql_escape_special_chars($request->name) ;
				$lname  	= ($request->lname == '' ) 	? $profile_address->cus_lname 	: mysql_escape_special_chars($request->lname) ;
				$mail  		= ($request->mail == '') 	? $profile_address->cus_email 	: mysql_escape_special_chars($request->mail);
				$phone1 	= ($request->phone1 == '') 	? $profile_address->cus_phone1 	: mysql_escape_special_chars($request->phone1);
				$phone2  	= ($request->phone2 == '') 	? $profile_address->cus_phone2 	: mysql_escape_special_chars($request->phone2);
				$cus_address= ($request->address == '') ? $profile_address->cus_address : mysql_escape_special_chars($request->address);
				$latitude 	= ($request->lat == '') 	? $profile_address->cus_latitude : mysql_escape_special_chars($request->lat);
				$longitude  = ($request->long == '') 	? $profile_address->cus_longitude : mysql_escape_special_chars($request->long);
				
				$wallet_used  =0;
				if(isset($request->use_wallet))
				$wallet_used  = mysql_escape_special_chars($request->use_wallet);
				
				$sample_reference_number = 'Paypal'.rand();
				$total_amt_to_pay = $wallet_amt = $ch_price = $grand_sub_total = $grand_tax = 0;
				//$del_fee = Input::get('final_del_fee');
				$del_fee = $request->final_del_fee;
				$shippingFee = ($request->ord_self_pickup==0) ? $del_fee : '0.00';
				$totalqty=0;
				
				/** calculate wallet **/
				$wallet_amt=$cart_wallet_amt =  0;
				if($wallet_used == 1)
				{
					$wallet_amt = mysql_escape_special_chars($request->wallet_amt);
					$cart_wallet_amt = mysql_escape_special_chars($request->wallet_amt);
				}
				$total_amt_to_pay=mysql_escape_special_chars($request->wallet_used_total);//-$wallet_amt;
				$get_cart_details = Home::get_products_incart();
				
				if(count($get_cart_details)  > 0 )
				{
					$sub_total=0;
					$tax=0;
					foreach($get_cart_details as $key=>$value)
					{
                        foreach($value as $pdtDetail)
                        {
                            $overall_amt_withtax = $overall_admin_amt = 0;
                            $cartarray = ['cart_transaction_id' => $sample_reference_number];
                            $update = updatevalues('gr_cart_save',$cartarray,['cart_id' => $pdtDetail->cart_id]);
                            $sub_total = (($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity) + $ch_price);
                            $tax=$pdtDetail->cart_tax;
						}
					}
                    $selfpickupstatus="";
                    if($request->ord_self_pickup == 1)	/* Self pickup */
                    {
                        $selfpickupstatus=1;
                        $total_amt_to_pay = $total_amt_to_pay  - $del_fee;
						
					}
                    else
                    {
                        $selfpickupstatus=0;
					}
					
				}
				
				$getAdminCurrency = Session::get('default_currency_code');
				$amt_to_pay = $total_amt_to_pay;
				//echo $getAdminCurrency.'<br>';
				if($getAdminCurrency!='USD'){
					$amt_to_pay = convertCurrency($getAdminCurrency,'USD',$total_amt_to_pay);
				}
				//echo $amt_to_pay;
				//exit;
				
				$Shippingdetails = array(
                'name'						=> $name,
                'lname'						=> $lname,
                'mail'						=> $mail,
                'sh_building_no'			=> mysql_escape_special_chars($request->sh_building_no),
                'phone1'					=> $phone1,
                'phone2'					=> $phone2,
                'cus_address'				=> $cus_address,
                'latitude'					=> $latitude,
                'longitude'					=> $longitude,
                'wallet_used'				=> $wallet_used,
                'sample_reference_number'	=> 'paypal'.rand(),
                'total_amt_to_pay'			=> $total_amt_to_pay,
                'shippingFee'				=> $shippingFee,
                'selfpickupstatus'			=> $selfpickupstatus,
                'totalqty'					=> $totalqty,
                'wallet_amt'				=> $cart_wallet_amt
				);
				
				if(Session::has('shippingdetails')){
					$request->session()->pull('shippingdetails', 'default');
				}
				Session::push('shippingdetails', $Shippingdetails);
				
				//Here to add shipping details
				
				$getAdminCurrency = Session::get('default_currency_code');
				$amt_to_pay = $total_amt_to_pay;
				if($getAdminCurrency!='USD'){
					$amt_to_pay = convertCurrency($getAdminCurrency,'USD',$total_amt_to_pay);
				}
				
				$payer = new Payer();
				$payer->setPaymentMethod('paypal');
				$item_1 = new Item();
				$item_1->setName('Item 1')
				->setCurrency('USD')
				->setQuantity(1)
				->setPrice($amt_to_pay);
				
				$item_list = new ItemList();
				$item_list->setItems(array($item_1));
				
				$amount = new Amount();
				$amount->setCurrency('USD')
				->setTotal($amt_to_pay);
				
				$transaction = new Transaction();
				$transaction->setAmount($amount)
				->setItemList($item_list)
				->setDescription('Your transaction description');
				
				$redirectUrl = new RedirectUrls();
				$redirectUrl->setReturnUrl(URL::route('paypalstatus')) /** Specify return URL **/
				->setCancelUrl(url('paypal_pmt_failure'));
				
				$payment = new Payment();
				$payment->setIntent('Sale')
				->setPayer($payer)
				->setRedirectUrls($redirectUrl)
				->setTransactions(array($transaction));
				
				try {
					$payment->create($this->_api_context);
					} catch (\PayPal\Exception\PPConnectionException $ex) {
					if (\Config::get('app.debug')) {
						//\Session::put('error', 'Connection timeout');
						//return Redirect::route('paywithpaypal');
						return Redirect::to('/checkout')->withErrors(['errors' => 'Connection timeout']);
						} else {
						//\Session::put('error', 'Some error occur, sorry for inconvenient');
						//return Redirect::route('paywithpaypal');
						return Redirect::to('/checkout')->withErrors(['errors' => 'Some error occur, sorry for inconvenient']);
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
				//\Session::put('error', 'Unknown error occurred');
				// return Redirect::route('paywithpaypal');
				return Redirect::to('/checkout')->withErrors(['errors' => 'Unknown error occurred']);
			}
		}
		
		
		public function getPaymentStatus()
		{
			$total=0;
			$mer_det="";
			$admin_det = get_admin_details();
			$admin_id  = $admin_det->id;
			try {
				$payment_id = Session::get('paypal_payment_id');
				Session::forget('paypal_payment_id');
				if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
					\Session::put('error', 'Payment failed');
					return Redirect::route('/');
				}
				$payment = Payment::get($payment_id, $this->_api_context);
				$execution = new PaymentExecution();
				$execution->setPayerId(Input::get('PayerID'));
				$result = $payment->execute($execution, $this->_api_context);
				$total="";
				//Here generate transaction id
				
				if ($result->getState() == 'approved') {
					//Here generate transaction id
					$transactionid='paypal'.rand();
					$merchantIdArray = array();
					$cus_id=Session::get('customer_id');
					$get_cart_details = Home::get_products_incart();
					$shippingdetails=Session::get('shippingdetails');
					$shippingdetails=$shippingdetails[0];
					$overall_amt_withtax=0;
					$overall_admin_amt=0;
					$cartSaveDet = DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->get();
					$wallet_used = $shippingdetails['wallet_used'];
					$wallet_amt = $shippingdetails['wallet_amt'];
					
					foreach($cartSaveDet as $cartDet) {
						
						
						//Shipping fee get from config file already defined by admin
						$shippingFee = ($shippingdetails['selfpickupstatus']==0) ? $shippingdetails['shippingFee'] : '0.00';
						$sub_total = (($cartDet->cart_unit_amt * $cartDet->cart_quantity));
						$total_amt = $sub_total + $cartDet->cart_tax;
						$overall_amt_withtax += $cartDet->cart_total_amt;
						
						//Here insert orderdetails
						$mer_det = DB::table('gr_store')->select('st_mer_id','gr_merchant.refund_status','gr_merchant.cancel_status')
						->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
						->where('gr_store.id',$cartDet->cart_st_id)->first();
						$mer_commission = DB::table('gr_merchant')->select('mer_commission')->where('id',$mer_det->st_mer_id)->first()->mer_commission;
						$choices = json_decode($cartDet->cart_choices_id,true);
						$ch_array = array();
						array_push($merchantIdArray,$mer_det->st_mer_id);
						$ch_price = 0;
						$cartDet->cart_total_amt;
						$tempamt=$cartDet->cart_total_amt;
						$commission = (($cartDet->cart_total_amt  * $mer_commission) / 100);
						
						$tempcommissionamt=($cartDet->cart_total_amt  * $mer_commission) / 100;
						
						/*if($overall_admin_amt!="")
							$overall_admin_amt += $tempcommissionamt;
							else
						$overall_admin_amt =$tempcommissionamt;*/
						
						$overall_admin_amt = $tempcommissionamt;
						
						if(count($choices) > 0)
						{
							foreach($choices as $ch)
							{
								$ch_array[] = ['choice_id'		 => $ch['choice_id'],
								'choice_price'	=> ($ch['choice_price'] * $cartDet->cart_quantity)
								];
								$ch_price += ($ch['choice_price'] * $cartDet->cart_quantity);
							}
						}
						if($cartDet->cart_type=='2') { $ord_type = 'Item'; } else { $ord_type = 'Product'; }
						// $mer_det = Home::merchant_orderDetails($cartDet->cart_st_id);
						$insertArr = array('ord_cus_id' => Session::get('customer_id'),
						'ord_shipping_cus_name' 	=> $shippingdetails['name'].' '.$shippingdetails['lname'],
						'ord_shipping_address'  	=> $shippingdetails['cus_address'],
						'ord_shipping_address1'  	=> $shippingdetails['sh_building_no'],
						'ord_shipping_mobile'	  	=> $shippingdetails['phone1'],
						'ord_shipping_mobile1'   	=> $shippingdetails['phone1'],
						'order_ship_mail'			=> $shippingdetails['mail'],
						'order_ship_latitude'		=> $shippingdetails['longitude'],
						'order_ship_longitude'	    => $shippingdetails['longitude'],
						'ord_merchant_id'			=> $mer_det->st_mer_id,
						'ord_rest_id'				=> $cartDet->cart_st_id,
						'ord_pro_id'				=> $cartDet->cart_item_id,
						'ord_had_choices'			=> $cartDet->cart_had_choice,
						'ord_choices'				=> json_encode($ch_array),
						'ord_choice_amount'			=> $ch_price,
						'ord_quantity'			    => $cartDet->cart_quantity,
						'ord_currency'			    => $cartDet->cart_currency,
						'ord_unit_price'			=> $cartDet->cart_unit_amt,
						'ord_sub_total'				=> $sub_total,
						'ord_tax_amt'				=> ($cartDet->cart_tax != '') ? $cartDet->cart_tax : 0,
						'ord_has_coupon'			=> "No",
						'ord_coupon_amt'			=> 0,
						'ord_delivery_fee'		    => $shippingFee,
						'ord_grant_total'			=> $cartDet->cart_total_amt,
						'ord_refund_status'        => $mer_det->refund_status,
						'ord_mer_cancel_status'    => $mer_det->cancel_status,
						'ord_wallet'				=> $wallet_amt,
						'ord_type'				    => $ord_type,
						'ord_pay_type'			    => "PAYPAL",
						'ord_transaction_id'		=> $transactionid,
						'ord_pre_order_date'		=> $cartDet->cart_pre_order,
						'ord_payment_status'		=> "Success",
						'ord_status'				=> 1,
						'ord_date'				    => date('Y-m-d H:i:s'),
						'ord_admin_amt'			=> $commission,
						'ord_self_pickup'			=> $shippingdetails['selfpickupstatus'],
						'ord_task_status'			=> '0',
						);
						//$tempamt=$cartDet->cart_total_amt;
						if($total=="")
						$total=$tempamt;
						else
						$total+=$tempamt;
						
						$insert = insertvalues('gr_order',$insertArr);
						$delete = deletecart($cartDet->cart_id);
						/** update quantity in product table **/
						$pro_no_of_purchase = DB::table('gr_product')->select('pro_no_of_purchase')->where('pro_id',$cartDet->cart_item_id)->first()->pro_no_of_purchase;
						$update = update_quantity(($pro_no_of_purchase + $cartDet->cart_quantity),$cartDet->cart_item_id);
						
						/** Calculate merchant amount **/
						
						$merchant_array[$mer_det->st_mer_id][]=$overall_admin_amt;
						/*if(empty($get_details) === false)		//Update
						{
							$order_count = $get_details->or_total_order + 1;
							$wallet_amt1	 = $get_details->or_coupon_amt + $wallet_amt;
							$admin_amt	 = ($get_details->or_admin_amt + $overall_admin_amt);
							$mer_amt	 = $get_details->or_mer_amt + $overall_amt_withtax;
							
							$array = [	'or_total_order'=> $order_count,
							'or_admin_amt'	=> $admin_amt,
							'or_coupon_amt'	=> $wallet_amt1,
							];
							$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $mer_det->st_mer_id]);
						}
						else 		//add
						{
							$array = [	'or_mer_id'		=>	$mer_det->st_mer_id,
							'or_total_order'=> 1,
							'or_admin_amt'	=> $overall_admin_amt,
							'or_coupon_amt' => $wallet_amt,
							];
							$update = insertvalues('gr_merchant_overallorder',$array);
						}*/
					}
					foreach($merchant_array as $mer_id=>$value){
						echo $mer_id.'='.array_sum($value).'<br>';
						$refundstatus=DB::table('gr_merchant')->select('refund_status')->where('id',$mer_id)->first();
						$get_details = Home::merchant_orderDetails($mer_det->st_mer_id);
						if(empty($get_details) === false)		//Update
						{
							$order_count = $get_details->or_total_order + count($value);
							$wallet_amt1	 = $get_details->or_coupon_amt + $wallet_amt;
							$admin_amt	 = ($get_details->or_admin_amt +array_sum($value));
							
							$array = [	'or_total_order'=> $order_count,
							'or_admin_amt'	=> $admin_amt,
							'or_coupon_amt'	=> $wallet_amt1,
							];
							$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $mer_id]);
						}
						else 		//add
						{
							$array = [	'or_mer_id'		=>	$mer_id,
							'or_total_order'=> count($value),
							'or_admin_amt'	=> array_sum($value),
							'or_coupon_amt' => $wallet_amt,
							];
							$update = insertvalues('gr_merchant_overallorder',$array);
						}
					}

					
					/*if($refundstatus->refund_status=='No')	
					{*/
					
					/** Calculate merchant amount ends  **/
					/*}*/
					
					
					
					/* ---------- SEND NOTIFICATION TO MERCHANT ----------------*/
					$got_message = (Lang::has(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED')) ? trans(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NEW_ORDER_PLACED');
					$message = str_replace(':transaction_id', $transactionid, $got_message);
					$message = str_replace(':customer_name', ucfirst($shippingdetails['name']).' '.$shippingdetails['lname'], $message);
					
					$unique_merchant = array_unique($merchantIdArray);
					$message_link = 'mer-order-details/'.base64_encode($transactionid);
					if(count($unique_merchant) > 0 ){
						foreach($unique_merchant as $uniMerchant){
							$noti_id = push_notification($cus_id,$uniMerchant,'gr_customer','gr_merchant',$message,$transactionid,$message_link);
							/* send notification to mobile */
							$mer_details = get_details('gr_merchant',['id'=>$uniMerchant],('mer_andr_fcm_id,mer_ios_fcm_id'));
							if(empty($mer_details) === false)
							{
								
								if($mer_details->mer_andr_fcm_id !='')
								{
									$parse_fcm=json_decode($mer_details->mer_andr_fcm_id,true);
									$reg_id = array();
									if(count($parse_fcm) > 0 )
									{
										foreach($parse_fcm as $parsed)
										{ 
											array_push($reg_id,$parsed['fcm_id']);						
										}
									}
									$json_data = ["registration_ids" => $reg_id,
													"data"	=> ['transaction_id'	=> $transactionid,
																'type'				=> 'New order',
																'notification_id'	=> $noti_id,
																"body"				=> $message,
																"title" 			=> "Order Notification"]
													];
									$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_MER);
										
								}
								if($mer_details->mer_ios_fcm_id !='')
								{
									$parse_fcm=json_decode($mer_details->mer_ios_fcm_id,true);
									$reg_id = array();
									if(count($parse_fcm) > 0 )
									{
										foreach($parse_fcm as $parsed)
										{ 
											array_push($reg_id,$parsed['fcm_id']);						
										}
									}
									$json_data = ["registration_ids" => $reg_id,
												 "notification"		=> ['transaction_id'	=> $transactionid,
																		'type'				=> 'New order',
																		'notification_id'	=> $noti_id,
																		"body" 				=> $message,
																		"title" 			=> "Order Notification",
																		"sound"				=> "default"
																		]
													];
									$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
										
								}
								
							}
							/* send notification to mobile ends */
						}
					}
					/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
					$message_link = 'admin-track-order/'.base64_encode($transactionid);
					push_notification($cus_id,$admin_id,'gr_customer','gr_admin',$message,$transactionid,$message_link);
					/* ---------- SEND NOTIFICATION TO ADMIN end ----------------*/
					/* update used wallet amount for customer */
					if($shippingdetails['wallet_used'] == 1)
					{
						$updat = updatevalues('gr_customer',['used_wallet' => DB::Raw('used_wallet+'.$wallet_amt)],['cus_id' => $cus_id]);
					}
					/** add wallet amount for referrel **/
					$refer_details = Home::refer_status(Session::get('customer_mail'));
					if(empty($refer_details) === false)
					{
						if($refer_details->referral_id != '')
						{
							/* Update referel wallet */
							$user = get_user(['cus_id' => $refer_details->referral_id,'cus_status' => '1']);
							$offer_amt = (($total * $refer_details->re_offer_percent)/100);
							if(empty($user) === false)
							{
								$wallet_amt =  $offer_amt + $user->cus_wallet;
								/** update refered customer wallet **/
								DB::table('gr_customer')->where(['cus_id' => $refer_details->referral_id])->update(['cus_wallet' => $wallet_amt]);
								
							}
							/* update first purchase status */
							DB::table('gr_referal')->where(['referre_email' => Session::get('customer_mail'),'re_purchased' => '0'])->update(['re_purchased' => '1','re_offer_amt' => $offer_amt]);
						}
					}
					
					/*UPDATING SHIPPING ADDRESS */
					if($shippingdetails['selfpickupstatus']==0)
					{
						$CheckShExists = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->first();
						if(empty($CheckShExists) === false)
						{
							
							$gr_shipping_array = ['sh_cus_fname' 	=> $shippingdetails['name'],
							'sh_cus_lname' 	=> $shippingdetails['lname'],
							'sh_location'  	=> $shippingdetails['cus_address'],
							'sh_building_no'=> $shippingdetails['sh_building_no'],
							'sh_phone1'	  	=> $shippingdetails['phone1'],
							'sh_phone2'		=> $shippingdetails['phone2'],
							//'sh_cus_email'	=> $shippingdetails->name,
							'sh_latitude'		=> $shippingdetails['latitude'],
							'sh_longitude'	=> $shippingdetails['longitude'],
							];
							DB::table('gr_shipping')->where(['sh_cus_id' => $cus_id])->update($gr_shipping_array);
						}
						else
						{
							$gr_shipping_array = ['sh_cus_fname' 	=> $shippingdetails['name'],
							'sh_cus_lname' 	=> $shippingdetails['lname'],
							'sh_location'  	=> $shippingdetails['cus_address'],
							'sh_building_no'=> $shippingdetails['sh_building_no'],
							'sh_phone1'	  	=> $shippingdetails['phone1'],
							'sh_phone2'		=> $shippingdetails['phone2'],
							//'sh_cus_email'	=> $shippingdetails->name,
							'sh_latitude'		=> $shippingdetails['latitude'],
							'sh_longitude'	=> $shippingdetails['longitude'],
							];
							$update = insertvalues('gr_shipping',$gr_shipping_array);
						}
					}
					
				}
				$s_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_OREDER_SUCCSESS');
				Session::flash('success',$s_msg);
				return Redirect::to('my-orders');
			}
			catch (PayPal\Exception\PPConnectionException $pce) {
				$fail = (Lang::has(Session::get('front_lang_file').'.FRONT_PAYMENT_FAILD')) ? trans(Session::get('front_lang_file').'.FRONT_PAYMENT_FAILD') : trans($this->FRONT_LANGUAGE.'.FRONT_PAYMENT_FAILD');
				
				\Session::put('error', $fail);
				return Redirect::route('/');
			}
		}
		
		public function checkout_failure(Request $request)
		{
			/*['name.required' => (Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL'),*/
			
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_PAYMENT_FAILD')) ? trans(Session::get('front_lang_file').'.FRONT_PAYMENT_FAILD') : trans($this->FRONT_LANGUAGE.'.FRONT_PAYMENT_FAILD');
			return Redirect::to('/checkout')->withErrors(['errors' => $msg]);
		}
		
	}	