<?php
	
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Admin;
	use Illuminate\Support\Facades\URL;
	use App\Http\Controllers\Controller;
	
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Input;
	
	
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
	
	
	
	
	class PaymentController extends Controller
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
		
		public function payWithpaypal(Request $request)
		{
			$payer = new Payer();
			$payer->setPaymentMethod('paypal');
			$item_1 = new Item();
			$item_1->setName('Item 1') /** item name **/
			->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(25.00); /** unit price **/
			$item_list = new ItemList();
			$item_list->setItems(array($item_1));
			$amount = new Amount();
			$amount->setCurrency('USD')
            ->setTotal(25.00);
			$transaction = new Transaction();
			$transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');
			$redirectUrl = new RedirectUrls();
			$redirectUrl->setReturnUrl(URL::route('status')) /** Specify return URL **/
			->setCancelUrl(URL::route('status'));
			$payment = new Payment();
			$payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrl)
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
			print_r($_REQUEST); exit;
			try {
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
			catch (PayPal\Exception\PPConnectionException $pce) {
				// Don't spit out errors or use "exit" like this in production code
				echo '<pre>';print_r(json_decode($pce->getData()));exit;
			}
			
		}
	}
