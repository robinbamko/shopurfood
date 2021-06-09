<?php
	namespace App\Http\Controllers\Admin;
	use Illuminate\Http\Request;
	use PayPal\Api\Amount;
	use PayPal\Api\Details;
	use PayPal\Api\Item;
	use PayPal\Api\ItemList;
	use PayPal\Api\Payer;
	use PayPal\Api\Payment;
	use PayPal\Api\RedirectUrls;
	use PayPal\Api\Transaction;
	
	class PaymentController extends Controller
	{
		private $_api_context;
		public function __construct()
		{
			/** PayPal api context **/
			$paypal_conf = \Config::get('paypal');
			$this->_api_context = new ApiContext(new OAuthTokenCredential(
			$paypal_conf['client_id'],
			$paypal_conf['secret'])
			);
			$this->_api_context->setConfig($paypal_conf['settings']);
		}
		
		public function payWithpaypal(Request $request)
		{
			Session::forget('sess_checkoutId');
			Session::forget('sess_mer_clientId');
			Session::forget('sess_mer_secretId');
			
			
			/*print_r($request->all()); exit;Array ( [_token] => 29ufcHJkJGyGhX9KxaINjVJekL1PAte6CuzNUyNf [amt_to_pay] => 13.87 [client_id] => 123456 [secret_id] => 3652 [merchant_id] => 63 )*/
			//echo env('PAYMAYA_PUBLIC_KEY')
			
			Session::put('sess_mer_clientId',$request->client_id);
			Session::put('sess_mer_secretId',$request->secret_id);
			
			//PayMayaSDK::getInstance()->initCheckout($request->client_id,$request->secret_id,Config::get('env.PAYMAYA_MODE'));
			$merchant_id=$request->merchant_id;
			$merchant_det = DB::table('gr_merchant')->where('id',$merchant_id)->where('mer_status','1')->first();
			
			
			$payer = new Payer();
			$payer->setPaymentMethod('paypal');
			$item_1 = new Item();
			$item_1->setName('Item 1') /** item name **/
			->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(25); /** unit price **/
			$item_list = new ItemList();
			$item_list->setItems(array($item_1));
			$amount = new Amount();
			$amount->setCurrency('USD')
            ->setTotal(25);
			$transaction = new Transaction();
			$transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');
			$redirect_urls = new RedirectUrls();
			$redirect_urls->setReturnUrl(URL::route('status')) /** Specify return URL **/
			->setCancelUrl(URL::route('status'));
			$payment = new Payment();
			$payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
			/** dd($payment->create($this->_api_context));exit; **/
			try {
				$payment->create($this->_api_context);
				} catch (\PayPal\Exception\PPConnectionException $ex) {
				if (\Config::get('app.debug')) {
					\Session::put('error', 'Connection timeout');
					return Redirect::route('paywithpaypal');
					} else {
					\Session::put('error', 'Some error occur, sorry for inconvenient');
					return Redirect::route('paywithpaypal');
				}
			}
			foreach ($payment->getLinks() as $link) {
				if ($link->getRel() == 'approval_url') {
					$redirect_url = $link->getHref();
					break;
				}
			}
			/** add payment ID to session **/
			Session::put('paypal_payment_id', $payment->getId());
			if (isset($redirect_url)) {
				/** redirect to paypal **/
				return Redirect::away($redirect_url);
			}
			\Session::put('error', 'Unknown error occurred');
			return Redirect::route('paywithpaypal');
		}
		
		public function getPaymentStatus()
		{
			/** Get the payment ID before session clear **/
			$payment_id = Session::get('paypal_payment_id');
			/** clear the session payment ID **/
			Session::forget('paypal_payment_id');
			if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
				\Session::put('error', 'Payment failed');
				return Redirect::route('/');
			}
			$payment = Payment::get($payment_id, $this->_api_context);
			$execution = new PaymentExecution();
			$execution->setPayerId(Input::get('PayerID'));
			/**Execute the payment **/
			$result = $payment->execute($execution, $this->_api_context);
			if ($result->getState() == 'approved') {
				\Session::put('success', 'Payment success');
				return Redirect::route('/');
			}
			\Session::put('error', 'Payment failed');
			return Redirect::route('/');
		}
	}
