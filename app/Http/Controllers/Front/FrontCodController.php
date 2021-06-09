<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Front;
	
	use App\Http\Controllers\Controller;
	
	use Stripe;
	use Stripe_Token;
	use Stripe_Customer;
	use Stripe_Charge;
	
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
	use App\Home;
	use Config;
	
	class FrontCodController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
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
		
		public function cod_checkout(Request $request)
		{
			/*print_r($request->all());
			exit;*/
			/*Array ( [_token] => tROnLL8UVFVmn0CnDiHLUgcSpAvHoziFMvJlCBPc [ord_self_pickup] => 0 [check_addr] => [name] => Mohammed [lname] => Haathim [mail] => suganya.t@pofitec.com [phone1] => 228508457854 [phone2] => +11234 [address] => Coimbatore, Tamil Nadu 641008, India [sh_building_no] => 30/2, Asad Nagar, Karumbukadai [lat] => 11.0168445 [long] => 76.95583209999995 [paymentMode] => cod [card_no] => [ccExpiryMonth] => [ccExpiryYear] => [publishkey] => pk_test_NeODEgdtsqSMsBpnR9aF5vcv [cvvNumber] => [final_del_fee] => 5.00 [wallet_amt] => 0.00 [wallet_used_total] => 114 )*/
			$validator 		  = Validator::make($request->all(), ['ord_self_pickup' => 'required']);
			$admin_det = get_admin_details();
			$admin_id  = $admin_det->id;
			$request->ord_self_pickup = (int)$request->ord_self_pickup;
			if($request->ord_self_pickup==0)
			{
				$validator 		  = Validator::make($request->all(), [
				'name' 	=> 'required',
				'lname' => 'required',
				'mail'	 => 'required',
				'phone1' => 'required|only_cnty_code',
				//'phone2' =>'required|only_cnty_code',
				'lat' 	=>'required',
				'long' 	=>'required',
				'address'	=>'required',
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
				$name  		= ($request->name == '' )	? $profile_address->cus_fname 	: mysql_escape_special_chars($request->name);
				$lname  	= ($request->lname == '' ) 	? $profile_address->cus_lname 	: mysql_escape_special_chars($request->lname) ;
				$mail  		= ($request->mail == '') 	? $profile_address->cus_email 	: mysql_escape_special_chars($request->mail);
				$phone1 	= ($request->phone1 == '') 	? $profile_address->cus_phone1 	: mysql_escape_special_chars($request->phone1);
				$phone2  	= ($request->phone2 == '') 	? $profile_address->cus_phone2 	: mysql_escape_special_chars($request->phone1);
				$address  	= ($request->address == '') ? $profile_address->cus_address : mysql_escape_special_chars($request->address);
				$latitude 	= ($request->lat == '') 	? $profile_address->cus_latitude : mysql_escape_special_chars($request->lat);
				$longitude  = ($request->long == '') 	? $profile_address->cus_longitude : mysql_escape_special_chars($request->long);
				$wallet_used  = mysql_escape_special_chars($request->use_wallet);
				$get_cart_details = Home::get_products_incart();
				$total = $wallet_amt = $cart_wallet_amt =  0;
				$del_fee = Input::get('final_del_fee');
				/** calculate wallet **/
				if($wallet_used == 1)
				{
					$wallet_amt = $request->wallet_amt;
					$cart_wallet_amt = $request->wallet_amt;
					
				}
				if(count($get_cart_details)  > 0 )
				{
					$transaction_id = "COD-".rand();
					$merchantIdArray = array();
					$merchantGrpArray = array();
					foreach($get_cart_details as $key=>$value)
					{
						$overall_amt_withtax = $overall_admin_amt = 0;
						foreach($value as $pdtDetail)
						{
							
							/* product cart*/
							if($pdtDetail->cart_type=='1')
							{
								$commission = (($pdtDetail->cart_total_amt * $pdtDetail->mer_commission) / 100);
								$sub_total = ($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity);
								/** calculate overall merchant amt **/
								$overall_amt_withtax +=	$pdtDetail->cart_total_amt;
								$overall_admin_amt +=	$commission;
								array_push($merchantIdArray,$pdtDetail->mer_id);
								/** calculate overall merchant amt ends **/
								$insertArr = ['ord_cus_id' 				=> $cus_id,
								'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
								'ord_shipping_address'  	=> $address,
								'ord_shipping_address1'  	=> mysql_escape_special_chars($request->sh_building_no),
								'ord_shipping_mobile'	  	=> $phone1,
								'ord_shipping_mobile1'		=> $phone2,
								'order_ship_mail'			=> $mail,
								'order_ship_latitude'		=> $latitude,
								'order_ship_longitude'		=> $longitude,
								'ord_merchant_id'			=> $pdtDetail->mer_id,
								'ord_rest_id'				=> $pdtDetail->pro_store_id,
								'ord_pro_id'				=> $pdtDetail->pro_id,
								'ord_had_choices'			=> "No",
								'ord_choices'				=> '',
								'ord_choice_amount'			=> 0,
								'ord_spl_req'				=> $pdtDetail->cart_spl_req,
								'ord_quantity'				=> $pdtDetail->cart_quantity,
								'ord_currency'				=> $pdtDetail->cart_currency,
								'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
								'ord_sub_total'				=> $sub_total,
								'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
								'ord_has_coupon'			=> "No",
								'ord_coupon_amt'			=> 0,
								'ord_delivery_fee'			=> $del_fee,
								'ord_grant_total'			=> $pdtDetail->cart_total_amt,
								'ord_refund_status'			=> $pdtDetail->refund_status,
								'ord_mer_cancel_status'		=> $pdtDetail->cancel_status,
								'ord_wallet'				=> $cart_wallet_amt,
								'ord_type'					=> "Product",
								'ord_pay_type'				=> "COD",
								'ord_transaction_id'		=> $transaction_id,
								'ord_payment_status'		=> "Success",
								'ord_status'				=> 1,
								'ord_date'					=> date('Y-m-d H:i:s'),
								'ord_admin_amt'				=> $commission,
								'ord_self_pickup'			=> $request->ord_self_pickup,
								'ord_task_status'			=> '0',
								];
								$insert = insertvalues('gr_order',$insertArr);
								$total += $sub_total;
								/** delete cart **/
								$delete = deletecart($pdtDetail->cart_id);
								/** update quantity in product table **/
								$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
								
							}
							/* item cart */
							elseif($pdtDetail->cart_type=='2')
							{
								array_push($merchantIdArray,$pdtDetail->mer_id);
								$choices = json_decode($pdtDetail->cart_choices_id,true);
								$ch_array = array();
								$ch_price = 0;
								if(count($choices) > 0)
								{
									foreach($choices as $ch)
									{
										$ch_array[] = ['choice_id'		 => $ch['choice_id'],
										'choice_price'	=> ($ch['choice_price'] * $pdtDetail->cart_quantity)
										];
										$ch_price += ($ch['choice_price'] * $pdtDetail->cart_quantity);
									}
								}
								$sub_total = (($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity) + $ch_price);
								$total_amt = $sub_total + $pdtDetail->cart_tax;
								$commission = (($total_amt * $pdtDetail->mer_commission) / 100);
								$tempcommission=($total_amt * $pdtDetail->mer_commission) / 100;
								/** calculate overall merchant amt  **/
								$overall_amt_withtax += $total_amt;
								
								$overall_admin_amt 	 +=	$tempcommission;
								/** calculate overall merchant amt ends **/
								$insertArr = ['ord_cus_id'				=> $cus_id,
								'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
								'ord_shipping_address'  	=> $address,
								'ord_shipping_address1'  	=> $request->sh_building_no,
								'ord_shipping_mobile'	  	=> $phone1,
								'ord_shipping_mobile1'		=> $phone2,
								'order_ship_mail'			=> $mail,
								'order_ship_latitude'		=> $latitude,
								'order_ship_longitude'		=> $longitude,
								'ord_merchant_id'			=> $pdtDetail->mer_id,
								'ord_rest_id'				=> $pdtDetail->pro_store_id,
								'ord_pro_id'				=> $pdtDetail->pro_id,
								'ord_had_choices'			=> $pdtDetail->cart_had_choice,
								'ord_choices'				=> json_encode($ch_array),
								'ord_choice_amount'			=> $ch_price,
								'ord_spl_req'				=> $pdtDetail->cart_spl_req,
								'ord_quantity'				=> $pdtDetail->cart_quantity,
								'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
								'ord_currency'				=> $pdtDetail->cart_currency,
								'ord_sub_total'				=> $sub_total,
								'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
								'ord_has_coupon'			=> "No",
								'ord_coupon_amt'			=> 0,
								'ord_wallet'				=> $cart_wallet_amt,
								'ord_delivery_fee'			=> $del_fee,
								'ord_grant_total'			=> $total_amt,
								'ord_refund_status'			=> $pdtDetail->refund_status,
								'ord_mer_cancel_status'		=> $pdtDetail->cancel_status,
								'ord_type'					=> "Item",
								'ord_pay_type'				=> "COD",
								'ord_transaction_id'		=> $transaction_id,
								'ord_pre_order_date'		=> $pdtDetail->cart_pre_order,
								'ord_payment_status'		=> "Success",
								'ord_status'				=> 1,
								'ord_date'					=> date('Y-m-d H:i:s'),
								'ord_admin_amt'				=> $commission,
								'ord_self_pickup'			=> $request->ord_self_pickup,
								'ord_task_status'			=> '0',
								];
								//print_r($insertArr); exit;
								$insert = insertvalues('gr_order',$insertArr);
								$total += $sub_total;
								/** delete cart **/
								$delete = deletecart($pdtDetail->cart_id);
								/** update quantity in product table **/
								$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
							}
							
							
						}
						/** Calculate merchant amount **/
						$get_details = Home::merchant_orderDetails($pdtDetail->mer_id);
						
						if(empty($get_details) === false)		//Update
						{
							$order_count = $get_details->or_total_order + 1;
							$wallet_amt1 = $get_details->or_coupon_amt + $wallet_amt;
							$admin_amt	 = ($get_details->or_admin_amt + $overall_admin_amt);
							$mer_amt	 = $get_details->or_mer_amt + $overall_amt_withtax;
							
							/** update in merchant overall table **/
							$array =[	'or_total_order' => $order_count,
										'or_admin_amt'	=> $admin_amt,
										'or_coupon_amt' => $wallet_amt1,
										/*'or_mer_amt'	=>	$mer_amt ------ merchant amount will be added after merchant once order*/
									];
							$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $pdtDetail->mer_id]);
						}
						else 		//add
						{
							/** insert in merchant overall table **/
							$array =[	'or_mer_id'		=>	$pdtDetail->mer_id,
										'or_total_order'=> 1,
										'or_admin_amt'	=> $overall_admin_amt,
										'or_coupon_amt' => $wallet_amt,
										/*'or_mer_amt'	=>	$overall_amt_withtax ------ merchant amount will be added after merchant once order */
									];
							$update = insertvalues('gr_merchant_overallorder',$array);
						}
						/** Calculate merchant amount ends  **/
					}
					
					
					
					/* ---------- SEND NOTIFICATION TO MERCHANT ----------------*/
					$got_message = (Lang::has(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED')) ? trans(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NEW_ORDER_PLACED') ;
					$message = str_replace(':transaction_id', $transaction_id, $got_message);
					$message = str_replace(':customer_name', ucfirst($name).' '.$lname, $message);
					
					$unique_merchant = array_unique($merchantIdArray);
					$message_link = 'mer-order-details/'.base64_encode($transaction_id);
					if(count($unique_merchant) > 0 ){
						foreach($unique_merchant as $uniMerchant){
							$noti_id = push_notification($cus_id,$uniMerchant,'gr_customer','gr_merchant',$message,$transaction_id,$message_link);
							//echo $noti_id; exit;
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
									//echo $parsed['fcm_id'].'<br>';
									$json_data = ["registration_ids" => $reg_id,
												"data" => ["transaction_id" => $transaction_id,'type' => 'New order', 'notification_id'=> $noti_id,"body" => $message, "title" 		=> "Order Notification"] 
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
													"notification" => ["transaction_id" => $transaction_id,'type' => 'New order', 'notification_id'=> $noti_id, "body" => $message,	"title"=> "Order Notification","sound"	=> "default"]
												];
									$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
										
								}
								
							}
							/* send notification to mobile ends */
						}
					}
					/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
					$message_link = 'admin-track-order/'.base64_encode($transaction_id);
					push_notification($cus_id,$admin_id,'gr_customer','gr_admin',$message,$transaction_id,$message_link);
					/* ---------- SEND NOTIFICATION TO ADMIN END----------------*/
					
					/* update used wallet amount for customer */
					if($wallet_used == 1)
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
					if($request->ord_self_pickup==0)
					{
						$CheckShExists = DB::table('gr_shipping')->where('sh_cus_id','=',Session::get('customer_id'))->first();
						if(empty($CheckShExists) === false)
						{
							$gr_shipping_array = ['sh_cus_fname' 	=> ucfirst($name),
							'sh_cus_lname' 	=> $lname,
							'sh_location'  	=> $address,
							'sh_building_no' 	=> $request->sh_building_no,
							'sh_phone1'	  	=> $phone1,
							'sh_phone2'		=> $phone2,
							'sh_cus_email'	=> $mail,
							'sh_latitude'		=> $latitude,
							'sh_longitude'	=> $longitude,
							];
							DB::table('gr_shipping')->where(['sh_cus_id' => $cus_id])->update($gr_shipping_array);
						}
						else
						{
							$gr_shipping_array = ['sh_cus_id'		=> $cus_id,
							'sh_cus_fname' 	=> ucfirst($name),
							'sh_cus_lname' 	=> $lname,
							'sh_location'  	=> $address,
							'sh_building_no'=> $request->sh_building_no,
							'sh_phone1'	  	=> $phone1,
							'sh_phone2'		=> $phone2,
							'sh_cus_email'	=> $mail,
							'sh_latitude'	=> $latitude,
							'sh_longitude'	=> $longitude,
							];
							$update = insertvalues('gr_shipping',$gr_shipping_array);
						}
					}
					
					/* EOF UPDATING SHIPPING ADDRESS*/
					/** mail function **/
					$get_order_details = Home::get_order_details($transaction_id);
					$customerDet 	   = Home::get_customer_details($transaction_id);
					//print_r($get_order_details); exit;
					if(empty($get_order_details) === false)
					{
						
						$send_mail_data = array('order_details'	=> $get_order_details,
						'customerDet'		=> $customerDet,
						'transaction_id'	=> $transaction_id,
						'self_pickup'		=> $request->ord_self_pickup,
						);
						// mail to customer
						Mail::send('email.order_mail_customer', $send_mail_data, function($message) use($mail)
						{
							$sub_data = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_SUCCSESS');
							
							$message->to($mail)->subject($sub_data);
						});
						// Mail to admin
						$admin_mail = Config::get('admin_mail');
						Mail::send('email.order_mail_admin', $send_mail_data, function($message) use($admin_mail)
						{
							$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
							$message->to($admin_mail)->subject($ord_msg);
						});
						//Mail to merchant
						foreach($get_order_details as $key=>$itemsDet)
						{	$explodeRest = explode('`',$key);
							$mer_mail = $explodeRest[1];
							$send_mail_data = array('order_details'		=> $itemsDet,
							'customerDet'		=> $customerDet,
							'transaction_id'	=> $transaction_id,
							'store_name' 		=> $explodeRest[0],
							'self_pickup'		=> $request->ord_self_pickup,
							);
							Mail::send('email.order_mail_merchant', $send_mail_data, function($message) use($mer_mail)
							{
								$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
								$message->to($mer_mail)->subject($ord_msg);
							});
						}
					}
					
					$msg = (lang::has(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_OREDER_SUCCSESS');
					Session::flash('success',$msg);
					return Redirect::to('my-orders');
				}
				else
				{
					$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_NO_ITEM')) ? trans(Session::get('front_lang_file').'.ADMIN_NO_ITEM') : trans($this->FRONT_LANGUAGE.'.ADMIN_NO_ITEM');
					return Redirect::back()->with('errors',$msg);
				}
			}
		}
		//PAYNAMICS CHECKOUT
		public function paynamics_checkout(Request $request)
		{
			
			$admin_det = get_admin_details();
			$admin_id  = $admin_det->id;
			$validator 		  = Validator::make($request->all(), ['ord_self_pickup' => 'required']);
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
				'paymentMode' =>'required',
				'card_no' => 'required',
				'ccExpiryMonth' => 'required',
				'ccExpiryYear' => 'required',
				'cvvNumber' => 'required',
                ]);
			}
			else{
				$validator 	= Validator::make($request->all(), [
				'paymentMode' 	=>'required',
				'card_no' 		=> 'required',
				'ccExpiryMonth' => 'required',
				'ccExpiryYear' 	=> 'required',
				'cvvNumber' 	=> 'required',
				]);
				
			}
			if ($validator->fails()) 
			{
				return Redirect::back()->withErrors($validator)->withInput();
			} 
			else
			{	
				/*START*/
				$cus_id		= Session::get('customer_id');
				
				$profile_address = DB::table('gr_customer')->select('cus_fname','cus_lname','cus_email','cus_phone1','cus_phone2','cus_address','cus_latitude','cus_longitude','cus_paynamics_clientid','cus_paynamics_secretid')->where('cus_id',$cus_id)->first();
				
				$name  		= ($request->name == '' )	? $profile_address->cus_fname 	: mysql_escape_special_chars($request->name);
				$lname  	= ($request->lname == '' ) 	? $profile_address->cus_lname 	: mysql_escape_special_chars($request->lname);
				$mail  		= ($request->mail == '') 	? $profile_address->cus_email 	: mysql_escape_special_chars($request->mail);
				$phone1 	= ($request->phone1 == '') 	? $profile_address->cus_phone1 	: mysql_escape_special_chars($request->phone1);
				$phone2  	= ($request->phone2 == '') 	? $profile_address->cus_phone2 	: mysql_escape_special_chars($request->phone1);
				$address  	= ($request->address == '') ? $profile_address->cus_address : mysql_escape_special_chars($request->address);
				$latitude 	= ($request->lat == '') 	? $profile_address->cus_latitude : mysql_escape_special_chars($request->lat);
				$longitude  = ($request->long == '') 	? $profile_address->cus_longitude : mysql_escape_special_chars($request->long);
				/* END */
				
				$wallet_used  = mysql_escape_special_chars($request->use_wallet);	
				$get_cart_details = Home::get_products_incart();
				$total = $wallet_amt = $cart_wallet_amt =  0;
				$del_fee = Input::get('final_del_fee');
				$wallet_amt=0;
				if($wallet_used == 1)
				{
					$wallet_amt = $request->wallet_amt;
					$cart_wallet_amt = $request->wallet_amt;
				}
				$total_amt_to_pay=$request->wallet_used_total;//-$wallet_amt;
				
				if($request->ord_self_pickup == 1)	/* Self pickup */
				{
					$selfpickupstatus=1;
					$total_amt_to_pay = $total_amt_to_pay  - $del_fee;
				}
				else
				{
					$selfpickupstatus=0;
				}
				/** calculate wallet **/
				if($wallet_used == 1)
				{
					$wallet_amt = $request->wallet_amt;
					
				}
				if(count($get_cart_details)  > 0 )
				{
					$Paymentgatewayamount=$total_amt_to_pay;
					$transaction_id = "STRIPE-".rand();
					$merchantIdArray = array();
					
					//Here pay stripe
					
					//Here details get by admin table
					$getAdminCurrency = Session::get('default_currency_code');
					$stripedetails=DB::table('gr_payment_setting')->select('paynamics_secret_id','paynamics_client_id')->first();
					$Paymentgatewayamount=round($Paymentgatewayamount);
					$amt_to_pay = $Paymentgatewayamount;
					if($getAdminCurrency!='USD'){
						$amt_to_pay = convertCurrency($getAdminCurrency,'USD',$Paymentgatewayamount);
					}
					
					require_once('./stripe/lib/Stripe.php');
					$secret_key = $stripedetails->paynamics_secret_id;
					$publishable_key = $stripedetails->paynamics_client_id;
					
					$stripe = array("secret_key" => $secret_key, "publishable_key" => $publishable_key);
					Stripe::setApiKey($stripe['secret_key']);
					$token = Input::get('stripeToken');
					try {
						Stripe_Charge::create ( array (
                        "amount" => round($amt_to_pay*100),   //Stripe payment only accept round integer 
                        "currency" => "USD",
                        "source" => $token, // obtained with Stripe.js
                        "description" => "Order Checkout."
						));
					}
					catch ( \Exception $e ) {
						
						$body = $e->getJsonBody();
						$err  = $body['error'];
						Session::flash ('message', "Your payment is Failed".$err['message']);
						return Redirect::back ();
					}
					foreach($get_cart_details as $key=>$value)
					{
						$overall_amt_withtax = $overall_admin_amt = 0;
						foreach($value as $pdtDetail)
						{
							
							array_push($merchantIdArray,$pdtDetail->mer_id);
							/* product cart*/
							if($pdtDetail->cart_type=='1')
							{
								
								$commission = (($pdtDetail->cart_total_amt * $pdtDetail->mer_commission) / 100);
								$sub_total = ($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity);
								/** calculate overall merchant amt **/
								$overall_amt_withtax +=	$pdtDetail->cart_total_amt;
								$overall_admin_amt +=	$commission;
								/** calculate overall merchant amt ends **/
								$insertArr = ['ord_cus_id' 				=> $cus_id,
								'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
								'ord_shipping_address'  	=> $address,
								'ord_shipping_address1'  	=> mysql_escape_special_chars($request->sh_building_no),
								'ord_shipping_mobile'	  	=> $phone1,
								'ord_shipping_mobile1'		=> $phone2,
								'order_ship_mail'			=> $mail,
								'order_ship_latitude'		=> $latitude,
								'order_ship_longitude'		=> $longitude,
								'ord_merchant_id'			=> $pdtDetail->mer_id,
								'ord_rest_id'				=> $pdtDetail->pro_store_id,
								'ord_pro_id'				=> $pdtDetail->pro_id,
								'ord_had_choices'			=> "No",
								'ord_choices'				=> '',
								'ord_choice_amount'			=> 0,
								'ord_quantity'				=> $pdtDetail->cart_quantity,
								'ord_currency'				=> $pdtDetail->cart_currency,
								'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
								'ord_sub_total'				=> $sub_total,
								'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
								'ord_has_coupon'			=> "No",
								'ord_coupon_amt'			=> 0,
								'ord_delivery_fee'			=> $del_fee,
								'ord_grant_total'			=> $pdtDetail->cart_total_amt,
								'ord_refund_status'			=> $pdtDetail->refund_status,
								'ord_mer_cancel_status'		=> $pdtDetail->cancel_status,
								'ord_wallet'				=> $cart_wallet_amt,
								'ord_type'					=> "Product",
								'ord_pay_type'				=> "STRIPE",
								'ord_transaction_id'		=> $transaction_id,
								'ord_payment_status'		=> "Success",
								'ord_status'				=> 1,
								'ord_date'					=> date('Y-m-d H:i:s'),
								'ord_admin_amt'				=> $commission,
								'ord_self_pickup'			=> $request->ord_self_pickup,
								'ord_task_status'			=> '0',
								];
							}
							/* item cart */
							elseif($pdtDetail->cart_type=='2')
							{
								
								$choices = json_decode($pdtDetail->cart_choices_id,true);
								$ch_array = array();
								$ch_price = 0;
								if(count($choices) > 0)
								{
									foreach($choices as $ch)
									{
										$ch_array[] = ['choice_id'		 => $ch['choice_id'],
										'choice_price'	=> ($ch['choice_price'] * $pdtDetail->cart_quantity)
										];
										$ch_price += ($ch['choice_price'] * $pdtDetail->cart_quantity);
									}
								}
								$sub_total = (($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity) + $ch_price);
								$total_amt = $sub_total + $pdtDetail->cart_tax;
								$commission = (($total_amt * $pdtDetail->mer_commission) / 100);
								/** calculate overall merchant amt  **/
								
								if($overall_amt_withtax!="")
								$overall_amt_withtax += $total_amt;
								else
								$overall_amt_withtax = $total_amt;
								
								if($overall_admin_amt!="")
								$overall_admin_amt 	 +=	$commission;
								else
								$overall_admin_amt =$commission;
								
								
								
								
								/** calculate overall merchant amt ends **/
								$insertArr = ['ord_cus_id'				=> $cus_id,
								'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
								'ord_shipping_address'  	=> $address,
								'ord_shipping_address1'  	=> $request->sh_building_no,
								'ord_shipping_mobile'	  	=> $phone1,
								'ord_shipping_mobile1'		=> $phone2,
								'order_ship_mail'			=> $mail,
								'order_ship_latitude'		=> $latitude,
								'order_ship_longitude'		=> $longitude,
								'ord_merchant_id'			=> $pdtDetail->mer_id,
								'ord_rest_id'				=> $pdtDetail->pro_store_id,
								'ord_pro_id'				=> $pdtDetail->pro_id,
								'ord_had_choices'			=> $pdtDetail->cart_had_choice,
								'ord_choices'				=> json_encode($ch_array),
								'ord_choice_amount'			=> $ch_price,
								'ord_quantity'				=> $pdtDetail->cart_quantity,
								'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
								'ord_currency'				=> $pdtDetail->cart_currency,
								'ord_sub_total'				=> $sub_total,
								'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
								'ord_has_coupon'			=> "No",
								'ord_coupon_amt'			=> 0,
								'ord_wallet'				=> $cart_wallet_amt,
								'ord_delivery_fee'			=> $del_fee,
								'ord_grant_total'			=> $total_amt,
								'ord_refund_status'			=> $pdtDetail->refund_status,
								'ord_mer_cancel_status'		=> $pdtDetail->cancel_status,
								'ord_type'					=> "Item",
								'ord_pay_type'				=> "STRIPE",
								'ord_transaction_id'		=> $transaction_id,
								'ord_pre_order_date'		=> $pdtDetail->cart_pre_order,
								'ord_payment_status'		=> "Success",
								'ord_status'				=> 1,
								'ord_date'					=> date('Y-m-d H:i:s'),
								'ord_admin_amt'				=> $commission,
								'ord_self_pickup'			=> $request->ord_self_pickup,
								'ord_task_status'			=> '0',
								];
							}
							//print_r($insertArr); exit;
                            $insert = insertvalues('gr_order',$insertArr);
                            $total += $sub_total;
                            /** delete cart **/
                            $delete = deletecart($pdtDetail->cart_id);
							
							
                            /** update quantity in product table **/ 
							$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
							
							/** Calculate merchant amount **/
							$refundstatus=DB::table('gr_merchant')->select('refund_status')->where('id',$pdtDetail->mer_id)->first();
							/*if($refundstatus->refund_status=='No')
							{*/
							
						}
						/* inga */ 
						$get_details = Home::merchant_orderDetails($pdtDetail->mer_id);
						if(empty($get_details) === false)		//Update
						{
							
							$order_count = $get_details->or_total_order + 1;
							$admin_amt	 = $get_details->or_admin_amt + $overall_admin_amt;
							$wallet_amt1	 = $get_details->or_coupon_amt + $wallet_amt; 
							$mer_amt	 = $get_details->or_mer_amt + $overall_amt_withtax;
							
							/** update in merchant overall table **/
							$array = [	'or_total_order' => $order_count,
							'or_admin_amt'	=> $admin_amt,
							'or_coupon_amt' => $wallet_amt1,
							/*'or_mer_amt'	=>	$mer_amt ------ merchant amount will be added after merchant once order*/
							];
							$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $pdtDetail->mer_id]);
							
						}
						else 		//add
						{
							
							/** insert in merchant overall table **/
							$array = [	'or_mer_id'		=>	$pdtDetail->mer_id,
							'or_total_order' => 1,
							'or_admin_amt'	=> $overall_admin_amt,
							'or_coupon_amt' => $wallet_amt,
							/*'or_mer_amt'	=>	$overall_amt_withtax ------ merchant amount will be added after merchant once order*/
							];
							$update = insertvalues('gr_merchant_overallorder',$array);
						}
						/** Calculate merchant amount ends  **/
						/* } */
					}
					/* ---------- SEND NOTIFICATION TO MERCHANT ----------------*/
					$got_message = (Lang::has(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED')) ? trans(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NEW_ORDER_PLACED') ;
					$message = str_replace(':transaction_id', $transaction_id, $got_message);
					$message = str_replace(':customer_name', ucfirst($name).' '.$lname, $message);
					
					$unique_merchant = array_unique($merchantIdArray);
					$message_link = 'mer-order-details/'.base64_encode($transaction_id);
					if(count($unique_merchant) > 0 ){
						foreach($unique_merchant as $uniMerchant){
							$noti_id = push_notification($cus_id,$uniMerchant,'gr_customer','gr_merchant',$message,$transaction_id,$message_link);
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
													"data" => ["transaction_id" => $transaction_id,
																'type'			=> 'New order',
																'notification_id'=> $noti_id,
																"body" 			=> $message,
																"title" 		=> "Order Notification"]
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
													"notification" => ["transaction_id" => $transaction_id,
																		'type'			=> 'New order',
																		'notification_id'=> $noti_id,
																		"body" 			=> $message,
																		"title" 		=> "Order Notification",
																		"sound"				=> "default"]
													];
									$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
										
								}
								
							}
							/* send notification to mobile ends */
						}
					}
					/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
					$message_link = 'admin-track-order/'.base64_encode($transaction_id);
					push_notification($cus_id,$admin_id,'gr_customer','gr_admin',$message,$transaction_id,$message_link);
					/* ---------- SEND NOTIFICATION TO ADMIN end ----------------*/
					
					/* update used wallet amount for customer */
					if($wallet_used == 1)
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
					if($request->ord_self_pickup==0)
					{
						
						$CheckShExists = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->first();
						if(empty($CheckShExists) === false)
						{
							$gr_shipping_array = ['sh_cus_fname' 	=> ucfirst($name),
							'sh_cus_lname' 	=> $lname,
							'sh_location'  	=> $address,
							'sh_building_no'=> $request->sh_building_no,
							'sh_phone1'	  	=> $phone1,
							'sh_phone2'		=> $phone2,
							'sh_cus_email'	=> $mail,
							'sh_latitude'	=> $latitude,
							'sh_longitude'	=> $longitude,
							];
							DB::table('gr_shipping')->where(['sh_cus_id' => $cus_id])->update($gr_shipping_array);
						}
						else
						{
							
							$gr_shipping_array = ['sh_cus_id'		=> $cus_id,
							'sh_cus_fname' 	=> ucfirst($name),
							'sh_cus_lname' 	=> $lname,
							'sh_location'  	=> $address,
							'sh_building_no'=> $request->sh_building_no,
							'sh_phone1'	  	=> $phone1,
							'sh_phone2'		=> $phone2,
							'sh_cus_email'	=> $mail,
							'sh_latitude'	=> $latitude,
							'sh_longitude'	=> $longitude,
							];
							$update = insertvalues('gr_shipping',$gr_shipping_array);
						}
					}
					
					
					
					// EOF UPDATING SHIPPING ADDRESS
					// mail function
					$get_order_details = Home::get_order_details($transaction_id);
					$customerDet 	   = Home::get_customer_details($transaction_id);
					if(empty($get_order_details) === false)
					{
						
						$send_mail_data = array('order_details'	=> $get_order_details,
						'customerDet'		=> $customerDet,
						'transaction_id'	=> $transaction_id,
						'self_pickup'		=> $request->ord_self_pickup,);
						
						// mail to customer
						Mail::send('email.order_mail_customer', $send_mail_data, function($message) use($mail)
						{
							$sub_data = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_SUCCSESS');
							$message->to($mail)->subject($sub_data);
						});
						// Mail to admin
						$admin_mail = Config::get('admin_mail');
						Mail::send('email.order_mail_admin', $send_mail_data, function($message) use($admin_mail)
						{
							$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
							
							$message->to($admin_mail)->subject($ord_msg);
						});
						// Mail to merchant
						foreach($get_order_details as $key=>$itemsDet)
						{
							$explodeRest = explode('`',$key);
							$mer_mail = $explodeRest[1];
							$send_mail_data = array('order_details'		=> $itemsDet,
							'customerDet'		=> $customerDet,
							'transaction_id'	=> $transaction_id,
							'store_name' 		=> $explodeRest[0],
							'self_pickup'		=> $request->ord_self_pickup,);
							Mail::send('email.order_mail_merchant', $send_mail_data, function($message) use($mer_mail)
							{
								$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
								$message->to($mer_mail)->subject($ord_msg);
							});
						}
					}
					/* eof mail function */
					
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_OREDER_SUCCSESS');
					Session::flash('success',$msg);
					return Redirect::to('my-orders');
				}
				else
				{
					$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_NO_ITEM')) ? trans(Session::get('front_lang_file').'.ADMIN_NO_ITEM') : trans($this->FRONT_LANGUAGE.'.ADMIN_NO_ITEM');
					return Redirect::back()->with('errors',$msg);
				}
			}	
		}
		
		/* wallet checkout*/
		public function wallet_checkout(Request $request)
		{
			$admin_det = get_admin_details();
			$admin_id  = $admin_det->id;
			$validator 		  = Validator::make($request->all(), ['ord_self_pickup' => 'required']);
			if($request->ord_self_pickup==0)
			{
				$validator 	= Validator::make($request->all(), [
				'name' 	=> 'required',
				'lname' => 'required',
				'mail' 	=> 'required',
				'phone1'=> 'required',
				//'phone2'=> 'required',
				'lat' 	=> 'required',
				'long' 	=> 'required',
				'address' =>'required',
				]);
			}
			if ($validator->fails()) 
			{
				return Redirect::back()->withErrors($validator)->withInput();
			} 
			else
			{	
				$cus_id		= Session::get('customer_id');
				$profile_address = DB::table('gr_customer')->select('cus_fname','cus_lname','cus_email','cus_phone1','cus_phone2','cus_address','cus_latitude','cus_longitude')->where('cus_id',$cus_id)->first();
				$name  		= ($request->name == '' )	? $profile_address->cus_fname 	: $request->name ;
				$lname  	= ($request->lname == '' ) 	? $profile_address->cus_lname 	: $request->lname ;
				$mail  		= ($request->mail == '') 	? $profile_address->cus_email 	: $request->mail;
				$phone1 	= ($request->phone1 == '') 	? $profile_address->cus_phone1 	: $request->phone1;
				$phone2  	= ($request->phone2 == '') 	? $profile_address->cus_phone2 	: $request->phone1;
				$address  	= ($request->address == '') ? $profile_address->cus_address : $request->address;
				$latitude 	= ($request->lat == '') 	? $profile_address->cus_latitude : $request->lat;
				$longitude  = ($request->long == '') 	? $profile_address->cus_longitude : $request->long;
				$wallet_used  = $request->use_wallet;	 
				$get_cart_details = Home::get_products_incart();
				$total = $wallet_amt = $cart_wallet_amt =  0;
				$del_fee = Input::get('final_del_fee');
				/** calculate wallet **/
				if($wallet_used == 1)
				{
					$wallet_amt = $request->wallet_amt; 
					$cart_wallet_amt = $request->wallet_amt;
					/** update wallet amount for customer **/
					$updat = updatevalues('gr_customer',['used_wallet' => DB::Raw('used_wallet+'.$wallet_amt)],['cus_id' => $cus_id]);
					//echo $updat; exit;
				}
				//exit;
				if(count($get_cart_details)  > 0 )
				{	
					$transaction_id = "WALLET-".rand();
					$merchantIdArray = array();
					foreach($get_cart_details as $key=>$value)
					{	
						$overall_amt_withtax = $overall_admin_amt = 0;
						foreach($value as $pdtDetail)
						{
							array_push($merchantIdArray,$pdtDetail->mer_id);
							
							/* product cart*/
							if($pdtDetail->cart_type=='1')	
							{	
								$commission = (($pdtDetail->cart_total_amt * $pdtDetail->mer_commission) / 100);
								$sub_total = ($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity);
								/** calculate overall merchant amt **/
								$overall_amt_withtax +=	$pdtDetail->cart_total_amt;
								$overall_admin_amt +=	$commission;
								/** calculate overall merchant amt ends **/
								$insertArr = ['ord_cus_id' 				=> $cus_id,
								'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
								'ord_shipping_address'  	=> $address,
								'ord_shipping_address1'  	=> $request->sh_building_no,
								'ord_shipping_mobile'	  	=> $phone1,
								'ord_shipping_mobile1'		=> $phone2,
								'order_ship_mail'			=> $mail,
								'order_ship_latitude'		=> $latitude,
								'order_ship_longitude'		=> $longitude,
								'ord_merchant_id'			=> $pdtDetail->mer_id,
								'ord_rest_id'				=> $pdtDetail->pro_store_id,
								'ord_pro_id'				=> $pdtDetail->pro_id,
								'ord_had_choices'			=> "No",
								'ord_choices'				=> '',
								'ord_choice_amount'			=> 0,
								'ord_quantity'				=> $pdtDetail->cart_quantity,
								'ord_currency'				=> $pdtDetail->cart_currency,
								'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
								'ord_sub_total'				=> $sub_total,
								'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
								'ord_has_coupon'			=> "No",
								'ord_coupon_amt'			=> 0,
								'ord_delivery_fee'			=> $del_fee,
								'ord_grant_total'			=> $pdtDetail->cart_total_amt,
								'ord_wallet'				=> $cart_wallet_amt,
								'ord_type'					=> "Product",
								'ord_pay_type'				=> "WALLET",
								'ord_transaction_id'		=> $transaction_id,
								'ord_payment_status'		=> "Success",
								'ord_status'				=> 1,
								'ord_date'					=> date('Y-m-d H:i:s'),
								'ord_admin_amt'				=> $commission,
								'ord_self_pickup'			=> $request->ord_self_pickup,
								'ord_task_status'			=> '0',
								];
								$insert = insertvalues('gr_order',$insertArr);
								$total += $sub_total;
								/** delete cart **/
								$delete = deletecart($pdtDetail->cart_id);
								/** update quantity in product table **/
								$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
								
							}
							/* item cart */
							elseif($pdtDetail->cart_type=='2') 	
							{	
								$choices = json_decode($pdtDetail->cart_choices_id,true);
								$ch_array = array();
								$ch_price = 0;
								if(count($choices) > 0)
								{
									foreach($choices as $ch)
									{
										$ch_array[] = ['choice_id'		 => $ch['choice_id'],
										'choice_price'	=> ($ch['choice_price'] * $pdtDetail->cart_quantity)
										];
										$ch_price += ($ch['choice_price'] * $pdtDetail->cart_quantity);
									}
								}
								$sub_total = (($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity) + $ch_price);
								$total_amt = $sub_total + $pdtDetail->cart_tax;
								$commission = (($total_amt * $pdtDetail->mer_commission) / 100);
								/** calculate overall merchant amt  **/
								$overall_amt_withtax += $total_amt;
								$overall_admin_amt 	 +=	$commission;
								/** calculate overall merchant amt ends **/
								$insertArr = ['ord_cus_id'				=> $cus_id,
								'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
								'ord_shipping_address'  	=> $address,
								'ord_shipping_address1'  	=> $request->sh_building_no,
								'ord_shipping_mobile'	  	=> $phone1,
								'ord_shipping_mobile1'	=> $phone2,
								'order_ship_mail'			=> $mail,
								'order_ship_latitude'		=> $latitude,
								'order_ship_longitude'	=> $longitude,
								'ord_merchant_id'			=> $pdtDetail->mer_id,
								'ord_rest_id'				=> $pdtDetail->pro_store_id,
								'ord_pro_id'				=> $pdtDetail->pro_id,
								'ord_had_choices'			=> $pdtDetail->cart_had_choice,
								'ord_choices'				=> json_encode($ch_array),
								'ord_choice_amount'		=> $ch_price,
								'ord_quantity'			=> $pdtDetail->cart_quantity,
								'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
								'ord_currency'			=> $pdtDetail->cart_currency,
								'ord_sub_total'			=> $sub_total,
								'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
								'ord_has_coupon'			=> "No",
								'ord_coupon_amt'			=> 0,
								'ord_wallet'				=> $cart_wallet_amt,
								'ord_delivery_fee'		=> $del_fee,
								'ord_grant_total'			=> $total_amt,
								'ord_type'				=> "Item",
								'ord_pay_type'			=> "WALLET",
								'ord_transaction_id'		=> $transaction_id,
								'ord_pre_order_date'		=> $pdtDetail->cart_pre_order,
								'ord_payment_status'		=> "Success",
								'ord_status'				=> 1,
								'ord_date'				=> date('Y-m-d H:i:s'),
								'ord_admin_amt'			=> $commission,
								'ord_self_pickup'			=> $request->ord_self_pickup,
								'ord_task_status'			=> '0',
								];
								$insert = insertvalues('gr_order',$insertArr);
								$total += $sub_total;
								/** delete cart **/
								$delete = deletecart($pdtDetail->cart_id);
								/** update quantity in product table **/
								$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
							}
							
							
						}
						/* inga */ 
						/** Calculate merchant amount **/
						$get_details = Home::merchant_orderDetails($pdtDetail->mer_id);
						
						if(empty($get_details) === false)		//Update
						{
							$order_count = $get_details->or_total_order + 1;
							$wallet_amt	 = $get_details->or_coupon_amt + $wallet_amt;
							$admin_amt	 = ($get_details->or_admin_amt + $overall_admin_amt);
							$mer_amt	 = $get_details->or_mer_amt + $overall_amt_withtax;
							
							/** update in merchant overall table **/
							$array = [	'or_total_order' => $order_count,
							'or_admin_amt'	=> $admin_amt,
							'or_coupon_amt' => $wallet_amt,
							//'or_mer_amt'	=>	$mer_amt
							]; 
							$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $pdtDetail->mer_id]);
						}
						else 		//add
						{
							/** insert in merchant overall table **/
							$array = [	'or_mer_id'		=>	$pdtDetail->mer_id,
							'or_total_order' => 1,
							'or_admin_amt'	=> $overall_admin_amt,
							'or_coupon_amt' => $wallet_amt,
							//'or_mer_amt'	=>	$overall_amt_withtax
							];
							$update = insertvalues('gr_merchant_overallorder',$array);
						}
						/** Calculate merchant amount ends  **/
					}
					
					/* ---------- SEND NOTIFICATION TO MERCHANT ----------------*/
					$got_message = (Lang::has(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED')) ? trans(Session::get('front_lang_file').'.ADMIN_NEW_ORDER_PLACED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NEW_ORDER_PLACED') ;
					$message = str_replace(':transaction_id', $transaction_id, $got_message);
					$message = str_replace(':customer_name', ucfirst($name).' '.$lname, $message);
					
					$unique_merchant = array_unique($merchantIdArray);
					$message_link = 'mer-order-details/'.base64_encode($transaction_id);
					if(count($unique_merchant) > 0 ){
						foreach($unique_merchant as $uniMerchant){
							$noti_id = push_notification($cus_id,$uniMerchant,'gr_customer','gr_merchant',$message,$transaction_id,$message_link);
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
													"data" => ["transaction_id" => $transaction_id,
																'type'			=> 'New order',
																'notification_id'=> $noti_id,
																"body" 			=> $message,
																"title" 		=> "Order Notification"]
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
													"notification" => ["transaction_id" => $transaction_id,
																		'type'			=> 'New order',
																		'notification_id'=> $noti_id,
																		"body" 			=> $message,
																		"title" 		=> "Order Notification",
																		"sound"			=> "default"]
													];
									$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
										
								}
								
							}
							/* send notification to mobile ends */
						}
					}
					/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
					$message_link = 'admin-track-order/'.base64_encode($transaction_id);
					push_notification($cus_id,$admin_id,'gr_customer','gr_admin',$message,$transaction_id,$message_link);
					
					/* ---------- SEND NOTIFICATION TO ADMIN END----------------*/
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
					if($request->ord_self_pickup==0)
					{
						$CheckShExists = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->first();
						if(empty($CheckShExists) === false)
						{
							$gr_shipping_array = ['sh_cus_fname' 	=> ucfirst($name),
							'sh_cus_lname' 	=> $lname,
							'sh_location'  	=> $address,
							'sh_building_no' 	=> $request->sh_building_no,
							'sh_phone1'	  	=> $phone1,
							'sh_phone2'		=> $phone2,
							'sh_cus_email'	=> $mail,
							'sh_latitude'		=> $latitude,
							'sh_longitude'	=> $longitude,
							];
							DB::table('gr_shipping')->where(['sh_cus_id' => $cus_id])->update($gr_shipping_array);
						}
						else
						{
							$gr_shipping_array = ['sh_cus_id'		=> $cus_id,
							'sh_cus_fname' 	=> ucfirst($name),
							'sh_cus_lname' 	=> $lname,
							'sh_location'  	=> $address,
							'sh_building_no'  => $request->sh_building_no,
							'sh_phone1'	  	=> $phone1,
							'sh_phone2'		=> $phone2,
							'sh_cus_email'	=> $mail,
							'sh_latitude'		=> $latitude,
							'sh_longitude'	=> $longitude,
							];
							$update = insertvalues('gr_shipping',$gr_shipping_array);
						}
					}
					
					/* EOF UPDATING SHIPPING ADDRESS*/ 
					/** mail function **/
					$get_order_details = Home::get_order_details($transaction_id);  
					$customerDet 	   = Home::get_customer_details($transaction_id);
					//print_r($get_order_details); exit;  
					if(empty($get_order_details) === false)
					{
						$send_mail_data = array('order_details'	=> $get_order_details,
						'customerDet'		=> $customerDet,
						'transaction_id'	=> $transaction_id,
						'self_pickup'		=> $request->ord_self_pickup,
						);
						/** mail to customer **/
						
						
						Mail::send('email.order_mail_customer', $send_mail_data, function($message) use($mail)
						{
							$sub_data = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_SUCCSESS');
							
							$message->to($mail)->subject($sub_data);
						});
						/* Mail to admin */
						$admin_mail = Config::get('admin_mail');
						Mail::send('email.order_mail_admin', $send_mail_data, function($message) use($admin_mail)
						{
							$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
							$message->to($admin_mail)->subject($ord_msg);
						});
						/* Mail to merchant */
						foreach($get_order_details as $key=>$itemsDet)
						{	$explodeRest = explode('`',$key);
							$mer_mail = $explodeRest[1];
							$send_mail_data = array('order_details'		=> $itemsDet,
							'customerDet'		=> $customerDet,
							'transaction_id'	=> $transaction_id,
							'store_name' 		=> $explodeRest[0],
							'self_pickup'		=> $request->ord_self_pickup,
							);
							Mail::send('email.order_mail_merchant', $send_mail_data, function($message) use($mer_mail)
							{
								$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
								$message->to($mer_mail)->subject($ord_msg);
							});
						}
					}
					/* eof mail function */
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_OREDER_SUCCSESS');
					
					Session::flash('success',$msg);
					return Redirect::to('/');
				}
				else
				{
					$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_NO_ITEM')) ? trans(Session::get('front_lang_file').'.ADMIN_NO_ITEM') : trans($this->FRONT_LANGUAGE.'.ADMIN_NO_ITEM');
					
					return Redirect::back()->with('errors',$msg);
				}
			}	
			
		}
		
		/* test case*/
		public function TestOrder(Request $request)
		{		
			$transaction_id = "COD-525360399";
			$get_order_details = Home::get_order_details($transaction_id);  
			$customerDet 	   = Home::get_customer_details($transaction_id);
			$mail = "suganya.t@pofitec.com";
			//print_r($get_order_details); exit;  
			if(empty($get_order_details) === false)
			{
				$send_mail_data = array('order_details'	=> $get_order_details,
				'customerDet'		=> $customerDet,
				'transaction_id'	=> $transaction_id);
				/** mail to customer **/
				Mail::send('email.order_mail_customer', $send_mail_data, function($message) use($mail)
				{
					$sub_data = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_SUCCSESS');
					
					$message->to($mail)->subject($sub_data);
				});
				/* Mail to admin */
				$admin_mail = Config::get('admin_mail');
				Mail::send('email.order_mail_admin', $send_mail_data, function($message) use($admin_mail)
				{
					$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
					$message->to($admin_mail)->subject($ord_msg);
				});
				/* Mail to merchant */
				foreach($get_order_details as $key=>$itemsDet)
				{	$explodeRest = explode('`',$key);
					$mer_mail = $explodeRest[1];
					$send_mail_data = array('order_details'		=> $itemsDet,
					'customerDet'		=> $customerDet,
					'transaction_id'	=> $transaction_id,
					'store_name' 		=> $explodeRest[0]);
					Mail::send('email.order_mail_merchant', $send_mail_data, function($message) use($mer_mail)
					{
						$ord_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW')) ? trans(Session::get('front_lang_file').'.FRONT_RECEIVED_NEW') : trans($this->FRONT_LANGUAGE.'.FRONT_RECEIVED_NEW');
						$message->to($mer_mail)->subject($ord_msg);
					});
				}
			}
			/* eof mail function */
            $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS')) ? trans(Session::get('front_lang_file').'.FRONT_OREDER_SUCCSESS') : trans($this->FRONT_LANGUAGE.'.FRONT_OREDER_SUCCSESS');
			
			Session::flash('success',$msg);
			return Redirect::to('/');
			
		}
	}		