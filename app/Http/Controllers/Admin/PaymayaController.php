<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Admin;
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\URL;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Auth;
	use Validator;
	use Session;
	use Mail;
	use View;
	use Lang;
	use Redirect;
	use App\Home;
	use App\Reports;
	use Config;
	use paypal_class;
	
	
	/*use Aceraven777\PayMaya\PayMayaSDK;
		use Aceraven777\PayMaya\API\Checkout;
		use Aceraven777\PayMaya\Model\Checkout\Item;
		use App\Libraries\PayMaya\User as PayMayaUser;
		use Aceraven777\PayMaya\Model\Checkout\ItemAmount;
		use Aceraven777\PayMaya\Model\Checkout\ItemAmountDetails;
	use Aceraven777\PayMaya\Model\Checkout\Address;*/
	
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
			$this->setAdminLanguage();
			/** PayPal api context **/
			$paypal_conf = \Config::get('paypal');
			$this->_api_context = new ApiContext(new OAuthTokenCredential(
			$paypal_conf['client_id'],
			$paypal_conf['secret'])
			
			);
			$this->_api_context->setConfig($paypal_conf['settings']);
			
		}
		
		public function commission_payment(Request $request)
		{
			Session::forget('sess_checkoutId');
			Session::forget('sess_mer_clientId');
			Session::forget('sess_mer_secretId');
			Session::put('sess_mer_clientId',$request->client_id);
			Session::put('sess_mer_secretId',$request->secret_id);
			
			/*echo "clientid". $request->client_id;
				echo "Secretid". $request->secret_id;
				
				echo "merchantid". $merchant_id;
			exit;*/
			$merchant_id=$request->merchant_id;
			$merchant_det = DB::table('gr_merchant')->where('id',$merchant_id)->first();
			//echo "total".$request->amt_to_pay;
			Session::put('merchantidpaypal',$merchant_id);
			Session::put('paypaltotal',$request->amt_to_pay);
			
			if(empty($merchant_det)===false)
			{
				$total_amt_to_pay=$request->amt_to_pay;
				if(Session::get('default_currency_code')=='USD'){
					$converted_amount = $total_amt_to_pay;
				} else {
					$converted_amount = convertCurrency(Session::get('default_currency_code'),'USD',$request->amt_to_pay);
				}
				
				$payer = new Payer();
				$payer->setPaymentMethod('paypal');
				
				$item_1 = new Item();
				$item_1	->setName('Item 1')
				->setCurrency('USD')
				->setQuantity(1)
				->setPrice($converted_amount);
				
				$item_list = new ItemList();
				$item_list->setItems(array($item_1));
				
				$amount = new Amount();
				$amount	->setCurrency('USD')
				->setTotal($converted_amount);
				
				$transaction = new Transaction();
				$transaction->setAmount($amount)
				->setItemList($item_list)
				->setDescription('Your transaction description');
				
				$redirectUrl = new RedirectUrls();
				$redirectUrl->setReturnUrl(URL::route('paymaya_commision_success'))
				->setCancelUrl(URL::route('paymaya_commision_failure'));
				
				$payment = new Payment();
				$payment->setIntent('Sale')
				->setPayer($payer)
				->setRedirectUrls($redirectUrl)
				->setTransactions(array($transaction));
				
				try {
					$payment->create($this->_api_context);
					Session::put('paypalid',$request->secret_id);
					} catch (\PayPal\Exception\PPConnectionException $ex) {
					if (\Config::get('app.debug')) {
						\Session::put('error', 'Connection timeout');
						return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'Payment Failure']);
						} else {
						\Session::put('error', 'Some error occur, sorry for inconvenient');
						return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'Payment Failure']);
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
				
				/*$sample_item_name = 'Commission Payment';
					$sample_total_price = $request->amt_to_pay;
					//echo Session::get('default_currency_code').','.'PHP'.','.$request->amt_to_pay; exit;
					$converted_amount = convertCurrency(Session::get('default_currency_code'),'PHP',$request->amt_to_pay);
					$sample_user_phone = $merchant_det->mer_phone;
					$sample_user_email = $merchant_det->mer_email;
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
					$user->firstName = $merchant_det->mer_fname;
					$user->middleName='merchantId-'.$merchant_id.'-'.$request->amt_to_pay;
					$user->lastName = $merchant_det->mer_lname;
					$user->contact->phone = $sample_user_phone;
					$user->contact->email = $sample_user_email;
					
					$address = new Address();
					$address->line1 = $merchant_det->mer_location;
					$user->shippingAddress = $address;
					
					
					$sample_reference_number = 'mer-'.rand();
					$itemCheckout->buyer = $user->buyerInfo();
					
					$itemCheckout->items = array($item);
					$itemCheckout->totalAmount = $itemAmount;
					$itemCheckout->requestReferenceNumber = $sample_reference_number;
					$itemCheckout->redirectUrl = array("success" => url('paymaya_commision_success'),
					"failure" => url('paymaya_commision_failure'),
					"cancel" => url('paymaya_commision_failure'));
					
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
					
				Session::put('sess_checkoutId',$itemCheckout->id);*/
				return redirect()->to('paymaya_commision_success');
			}
			else
			{
				//Session::flash('message','No merchant found');
				return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'No merchant found']);
			}
			
		}
		
		/* Checkout success */
		public function checkout_success(Request $request)
		{
			$insertArr = ['commision_admin_id'	=> '1',
            'commission_mer_id' 	=> session::get('merchantidpaypal'),
            'commission_paid'	 	=> session::get('paypaltotal'),
            'commission_currency'	=> Session::get('default_currency_symbol'),
            'mer_commission_status'	=> '2',
            'mer_transaction_id' 	=> 'paypal'.rand(),
            'mer_paymaya_pmtId'		=> session::get('paypal_payment_id'),
            'commission_date'		=> date('Y-m-d H:i:s'),
            'pay_type'				=> 2
			];
			$insert = insertvalues('gr_merchant_commission',$insertArr);
			DB::table('gr_notification')->where(['no_status' => '1','no_mer_id' => session::get('merchantidpaypal')])->delete();
			
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_SUXES')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_SUXES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAID_SUXES');
			Session::flash('message',$msg);
			return Redirect::to('/admin-commission-tracking');
			
		}
		public function checkout_failure(Request $request)
		{
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_FAILURE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_FAILURE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYMENT_FAILURE');
			
			return Redirect::to('paymaya_failure')->withErrors(['errors' => $msg]);
			
		}
		
		/*USER CANCEL PAYMENT */
		public function cancel_payment(Request $request)
		{
			
			Session::forget('sess_checkoutId');
			Session::forget('sess_cus_clientId');
			Session::forget('sess_cus_secretId');
			Session::put('sess_cus_clientId',$request->client_id);
			Session::put('sess_cus_secretId',$request->secret_id);
			
			//PayMayaSDK::getInstance()->initCheckout($request->client_id,$request->secret_id,Config::get('env.PAYMAYA_MODE'));
			
			$customer_id=$request->customer_id;
			$order_id=$request->order_id;
			Session::put('cancelpaypalorderid',$order_id);
			$merchant_det = DB::table('gr_customer')->where('cus_id',$customer_id)->first();
			
			
			//Here add session for cancel payment insertion
			if(empty($merchant_det)===false)
			{
				$sample_item_name = 'Cancel Payment';
				$sample_total_price = $request->amt_to_pay;
				$converted_amount = convertCurrency(Session::get('default_currency_code'),'PHP',$request->amt_to_pay);
				$sample_user_phone = $merchant_det->cus_phone1;
				$sample_user_email = $merchant_det->cus_email;
				
				//Here store session data
				Session::put('cancelpaypalcusotmername',$merchant_det->cus_fname);
				Session::put('cancelpaypalamount',$request->amt_to_pay);
				
				/*
					$itemAmountDetails = new ItemAmountDetails();
					$itemAmountDetails->tax = "0.00";
					$itemAmountDetails->subtotal = $converted_amount;
					
					$itemAmount = new ItemAmount();
					$itemAmount->currency = "PHP";
					$itemAmount->value = $converted_amount;
					$itemAmount->details = $itemAmountDetails;
					
					$item = new Item();
					$item->name = $sample_item_name;
					$item->quantity = '1';
					$item->amount = $itemAmount;
					$item->totalAmount = $itemAmount;
					
					// Checkout
					$itemCheckout = new Checkout();
					$user = new PayMayaUser();
					$user->firstName = $merchant_det->cus_fname;
					$user->middleName='merchantId-'.$order_id.'-'.$request->amt_to_pay;
					$user->lastName = $merchant_det->cus_lname;
					$user->contact->phone = $sample_user_phone;
					$user->contact->email = $sample_user_email;
					
					$address = new Address();
					$address->line1 = $merchant_det->cus_address;
					$user->shippingAddress = $address;
					
					
					$sample_reference_number = 'cus-'.rand();
					$itemCheckout->buyer = $user->buyerInfo();
					
					$itemCheckout->items = array($item);
					$itemCheckout->totalAmount = $itemAmount;
					$itemCheckout->requestReferenceNumber = $sample_reference_number;
					$itemCheckout->redirectUrl = array("success" => url('paymaya_cancel_success'),
					"failure" => url('paymaya_cancel_failure'),
					"cancel" => url('paymaya_cancel_failure'));
					
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
				}*/
				
				//Here integrate to paypal
				$total_amt_to_pay=$request->amt_to_pay;
				if(Session::get('default_currency_code')=='USD'){
					$converted_amount = $total_amt_to_pay;
					} else {
					$converted_amount = convertCurrency(Session::get('default_currency_code'),'USD',$request->amt_to_pay);
				}
				$payer = new Payer();
				$payer->setPaymentMethod('paypal');
				
				$item_1 = new Item();
				$item_1	->setName('Item 1')
				->setCurrency('USD')
				->setQuantity(1)
				->setPrice($converted_amount);
				
				$item_list = new ItemList();
				$item_list->setItems(array($item_1));
				
				$amount = new Amount();
				$amount	->setCurrency('USD')
				->setTotal($converted_amount);
				
				$transaction = new Transaction();
				$transaction->setAmount($amount)
				->setItemList($item_list)
				->setDescription('Your transaction description');
				
				$redirectUrl = new RedirectUrls();
				$redirectUrl->setReturnUrl(URL::route('paymaya_cancel_success'))
				->setCancelUrl(URL::route('paymaya_cancel_failure'));
				
				$payment = new Payment();
				$payment->setIntent('Sale')
				->setPayer($payer)
				->setRedirectUrls($redirectUrl)
				->setTransactions(array($transaction));
				try {
					$payment->create($this->_api_context);
					Session::put('paypalid',$request->secret_id);
					
					} catch (\PayPal\Exception\PPConnectionException $ex) {
					if (\Config::get('app.debug')) {
						\Session::put('error', 'Connection timeout');
						return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'Payment Failure']);
						} else {
						\Session::put('error', 'Some error occur, sorry for inconvenient');
						return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'Payment Failure']);
					}
				}
				foreach ($payment->getLinks() as $link)
				{
					if($link->getRel() == 'approval_url') {
						$redirect_url = $link->getHref();
						break;
					}
				}
				Session::put('cancel_paypal_payment_id', $payment->getId());
				if (isset($redirect_url)) {
					/** redirect to paypal **/
					return Redirect::away($redirect_url);
				}
				\Session::put('error', 'Unknown error occurred');
				return redirect()->to('paymaya_commision_success');
				
				
			}
			else
			{
				Session::flash('message',"No Customer found");
				return Redirect::to('manage-cancelled-order')->withErrors(['errors' => "No Customer found"]);
			}
			
		}

		/* Refund to customer  via paypal email */
		public function paypal_details_refund(Request $request)
		{
			$ord_id 	= $request->ord_id;
			$amt 		= $request->amount;
			$client_id 	= $request->client_id;
			$refence_number = 'PAYPAL-'.mt_rand(100000, 999999);
			Session::put('cancelpaypalorderid',$ord_id);
			Session::put('cancelpaypalamount',$amt);
						
			require 'paypal/paypal_new/paypal.class.php';
			 $p             = new paypal_class;
			 $this_script = url('');
			 $p->add_field('business',$client_id);
	        $p->add_field('return', $this_script . '/paymaya_cancel_success');
	        $p->add_field('cancel_return', $this_script . '/paymaya_cancel_failure');
	        $p->add_field('notify_url', $this_script . '/paymaya_cancel_success');
	        $p->add_field('item_name', "commission"); 
	        $p->add_field('_token', $request->session()->token()); 
	        $p->add_field('amount', $amt);
	        $p->add_field('quantity', "1");
	        $p->add_field('custom', "commission");
	        $p->add_field('item_number', $refence_number);
	        $p->add_field('currency_code', "USD");
	        $p->submit_paypal_post();
	        
		}

		/* Checkout success */
		public function cancel_success(Request $request)
		{	//print_r($_REQUEST); exit;
			$order_id =  session::pull('cancelpaypalorderid');
			
			$paidAmount = session::pull('cancelpaypalamount');
			$requestreferencenumber= $_REQUEST['txn_id'];
			
			$insertArr = [	'ord_cancel_paidamt'			=> $paidAmount,
			'ord_cancel_paytype'			=> 'Paypal',
			'ord_cancelpaid_transid' 		=> $requestreferencenumber,
			'cancel_paid_date'				=> date('Y-m-d H:i:s'),
			'ord_cancel_paymaya_pmtId' 		=> $requestreferencenumber,
			//'ord_cancel_paymaya_receiptnum' 	=> $checkout['receiptNumber'],
			'ord_cancel_paymaya_paid_time' 	=> date('Y-m-d H:i:s'),
			//'ord_cancel_paymaya_maskedcard' => $checkout['paymentDetails']['maskedCardNumber'],
			'ord_cancel_payment_status'		=>'1' //success
			
			];
			//print_r($insertArr); echo $order_id; exit;
			DB::table('gr_order')->where('ord_id',$order_id)->update($insertArr);
			/* send mail to customer */
			$get_order_details = Reports::get_cancel_or_details($order_id);
			//print_r($get_order_details); exit;
			if(!empty($get_order_details))
			{ 
				$send_mail_data = array('order_details' => $get_order_details,'transaction_id' 	=> $get_order_details->ord_transaction_id);
				$mail = $get_order_details->order_ship_mail;
				Mail::send('email.refund_mail', $send_mail_data, function($message) use($mail)
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_OR_REFUNDED');
					$message->to($mail)->subject($msg);
				});
			}
			Session::flash('message','Cancellation amount paid successfully');
			//			return Redirect::to('manage-cancelled-order');
			$msg = '';//(Lang::has(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRNXN_MISSING');
			return Redirect::to('manage-cancelled-order');
			
			
		}
		public function cancel_failure(Request $request)
		{
			
			//		Session::flash('message','Payment Failure');
			//		return Redirect::to('manage-cancelled-order')->withErrors(['errors' => 'Payment Failure']);
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_FAILURE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_FAILURE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYMENT_FAILURE');
			
			return Redirect::to('manage-cancelled-order')->withErrors(['errors' => $msg]);
			
			
		}
		/* EOF USER CANCEL PAYMENT */
		
		
		/* USER failed PAYMENT */
		public function failed_payment(Request $request)
		{
			Session::forget('sess_checkoutId');
			Session::forget('sess_cus_clientId');
			Session::forget('sess_cus_secretId');
			Session::put('sess_cus_clientId',$request->client_id);
			Session::put('sess_cus_secretId',$request->secret_id);
			/*print_r($request->all()); exit;Array ( [_token] => 29ufcHJkJGyGhX9KxaINjVJekL1PAte6CuzNUyNf [amt_to_pay] => 15.5 [client_id] => client id [secret_id] => secret id [customer_id] => 2 )*/
			PayMayaSDK::getInstance()->initCheckout($request->client_id,$request->secret_id,Config::get('env.PAYMAYA_MODE'));
			//echo env('PAYMAYA_PUBLIC_KEY').'/'.Session::get('default_currency_code').','.'PHP'.','.$request->amt_to_pay; exit;
			$customer_id = $request->customer_id;
			$order_id = $request->trans_id;
			$st_id = $request->st_id;
			$merchant_det = DB::table('gr_customer')->where('cus_id',$customer_id)->where('cus_status','1')->first();
			if(empty($merchant_det)===false)
			{
				$sample_item_name = 'Cancel Payment';
				$sample_total_price = $request->amt_to_pay;
				$converted_amount = convertCurrency(Session::get('default_currency_code'),'PHP',$request->amt_to_pay);
				$sample_user_phone = $merchant_det->cus_phone1;
				$sample_user_email = $merchant_det->cus_email;
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
				$item->quantity = '1';
				$item->amount = $itemAmount;
				$item->totalAmount = $itemAmount;
				
				// Checkout
				$itemCheckout = new Checkout();
				$user = new PayMayaUser();
				$user->firstName = $merchant_det->cus_fname;
				$user->middleName='merchantId-'.$order_id.'-'.$st_id.'-'.$request->amt_to_pay;
				$user->lastName = $merchant_det->cus_lname;
				$user->contact->phone = $sample_user_phone;
				$user->contact->email = $sample_user_email;
				
				$address = new Address();
				$address->line1 = $merchant_det->cus_address;
				$user->shippingAddress = $address;
				
				
				$sample_reference_number = 'cus-'.rand();
				$itemCheckout->buyer = $user->buyerInfo();
				
				$itemCheckout->items = array($item);
				$itemCheckout->totalAmount = $itemAmount;
				$itemCheckout->requestReferenceNumber = $sample_reference_number;
				$itemCheckout->redirectUrl = array("success" => url('paymaya_failed_success'),
                "failure" => url('paymaya_failed_failure'),
                "cancel" => url('paymaya_failed_failure'));
				
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
				//Session::flash('message',"No Customer found");
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NOCUST_FOUND')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOCUST_FOUND') : trans($this->ADMIN_LANGUAGE.'.ADMIN_NOCUST_FOUND');
				return Redirect::to('manage_failed_orders')->withErrors(['errors' => $msg]);
			}
			
		}
		
		/* Checkout success */
		public function failed_success(Request $request)
		{
			
			PayMayaSDK::getInstance()->initCheckout(Session::get('sess_cus_clientId'),Session::get('sess_cus_secretId'),Config::get('env.PAYMAYA_MODE'));
			$transaction_id = Session::get('sess_checkoutId');
			if (!$transaction_id) {
				//Session::flash('message','Transaction Id Missing');
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRNXN_MISSING');
				return Redirect::to('manage-cancelled-order')->withErrors(['errors' => $msg]);
			}
			$itemCheckout = new Checkout();
			$itemCheckout->id = $transaction_id;
			$checkout = $itemCheckout->retrieve();
			//echo '<pre>'; print_r($checkout); exit;
			if($checkout['paymentStatus'] == "PAYMENT_SUCCESS")
			{
				$merchant_midName = explode('-',$checkout['buyer']['middleName']);
				$order_id = $merchant_midName[1];
				$st_id  = $merchant_midName[2];
				$paidAmount = $merchant_midName[3];
				$insertArr = ['ord_cancel_paidamt'				=> $paidAmount,
                'ord_cancel_paytype'				=> 'PayMaya',
                'ord_cancelpaid_transid' 			=> $checkout['requestReferenceNumber'],
                'cancel_paid_date'				=> date('Y-m-d H:i:s'),
                'ord_cancel_paymaya_pmtId' 		=> $checkout['transactionReferenceNumber'],
                'ord_cancel_paymaya_receiptnum' 	=> $checkout['receiptNumber'],
                'ord_cancel_paymaya_paid_time' 	=> $checkout['paymentDetails']['paymentAt'],
                'ord_cancel_paymaya_last4' 		=> $checkout['paymentDetails']['last4'],
                'ord_cancel_paymaya_cardtype' 	=> $checkout['paymentDetails']['cardType'],
                'ord_cancel_paymaya_maskedcard' 	=> $checkout['paymentDetails']['maskedCardNumber'],
                'ord_cancel_payment_status'		=>'1' //success
				];
				DB::table('gr_order')->where(['ord_transaction_id'=>$order_id,
                'ord_rest_id' => $st_id,
                'ord_status'=>'9'])
                ->update($insertArr);
				/* send mail to customer */
				$get_order_details = Admin::get_failed_or_details($order_id,$st_id);
				if(!empty($get_order_details))
				{
					$send_mail_data = array('order_details'		=> $get_order_details,
                    'transaction_id' 	=> $order_id );
					$mail = $get_order_details[0]->order_ship_mail;
					Mail::send('email.refund_mail', $send_mail_data, function($message) use($mail)
					{
						$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_OR_REFUNDED');
						$message->to($mail)->subject($msg);
					});
				}
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_SUXES')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_SUXES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAID_SUXES');
				Session::flash('message',$msg);
				return Redirect::to('manage_failed_orders');
			}
			else
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_FAILURE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_FAILURE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYMENT_FAILURE');
				Session::flash('message',$msg);
				return Redirect::to('paymaya_failed_failure');
			}
		}
		public function failed_failure(Request $request)
		{
			//print_r($request->all());
			//PayMayaSDK::getInstance()->initCheckout('pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz','sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',(\App::environment('production') ? 'SANDBOX' : 'SANDBOX'));
			
			PayMayaSDK::getInstance()->initCheckout(Session::get('sess_cus_clientId'),Session::get('sess_cus_secretId'),Config::get('env.PAYMAYA_MODE'));
			$transaction_id = Session::get('sess_checkoutId');
			if (!$transaction_id) {
				//Session::flash('message','Transaction Id Missing');
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRNXN_MISSING') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRNXN_MISSING');
				return Redirect::to('manage_failed_orders')->withErrors(['errors' => $msg]);
			}
			$itemCheckout = new Checkout();
			$itemCheckout->id = $transaction_id;
			//$itemCheckout->execute();
			$checkout = $itemCheckout->retrieve();
			//Session::flash('message','Payment Failure \n Error Code: '.$checkout['paymentStatus']);
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PMTERR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PMTERR_CODE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PMTERR_CODE');
			return Redirect::to('manage_failed_orders')->withErrors(['errors' => $msg.' : '.$checkout['paymentStatus']]);
		}
		/* EOF USER failed PAYMENT */
	

	}	