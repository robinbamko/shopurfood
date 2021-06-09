<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Admin;
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Auth;
	
	use Stripe;
	use Stripe_Token;
	use Stripe_Customer;
	use Stripe_Charge;
	
	use Validator;
	use Session;
	use Mail;
	use View;
	use Lang;
	use Redirect;
	use App\Home;
	use Config;
	use Aceraven777\PayMaya\PayMayaSDK;
	use Aceraven777\PayMaya\API\Checkout;
	use Aceraven777\PayMaya\Model\Checkout\Item;
	use App\Libraries\PayMaya\User as PayMayaUser;
	use Aceraven777\PayMaya\Model\Checkout\ItemAmount;
	use Aceraven777\PayMaya\Model\Checkout\ItemAmountDetails;
	use Aceraven777\PayMaya\Model\Checkout\Address;
	
	
	class StripeController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function stripepay(Request $request)
		{
			$merchant_id=$request->mechantid;
			$paidAmount=$request->amounttopay;
			$paidAmount=round($paidAmount);
			Session::forget('sess_checkoutId');
			Session::forget('sess_mer_clientId');
			Session::forget('sess_mer_secretId');
			Session::put('sess_mer_clientId',$request->client_id);
			Session::put('sess_mer_secretId',$request->secret_id);
			
			$merchant_det = DB::table('gr_merchant')->where('id',$merchant_id)->where('mer_status','1')->first();
			
			if(empty($merchant_det)===false)
			{
				
				require_once('./stripe/lib/Stripe.php');
				$secret_key = $merchant_det->mer_paynamics_secretid;
				$publishable_key = $merchant_det->mer_paynamics_clientid;
				$stripe = array("secret_key" => $secret_key, "publishable_key" => $publishable_key);
				Stripe::setApiKey($stripe['secret_key']);
				$token = $request->stripeToken;
				try {
					Stripe_Charge::create ( array (
                    "amount" => $paidAmount,   //Stripe payment only accept round integer
                    "currency" =>'USD',
                    "source" => $token, // obtained with Stripe.js
                    "description" => "CommissionPaid."
					) );
					$status="2";
					
					} catch ( \Exception $e ) {
					$status="3";
				}
				
				//Here insert
				
				$insertArr = ['commision_admin_id'	=> '1',
                'commission_mer_id' 	=> $merchant_id,
                'commission_paid'	 	=> $paidAmount,
                'commission_currency'	=> Session::get('default_currency_symbol'),
                'mer_commission_status'	=> '2',
                'stripetoken'           =>$token,
                //'stripeid'              => 'Stripe'.rand(),
                'mer_transaction_id' 	=> 'stripe'.rand(),
                'mer_commission_status' => $status,
                'commission_date'		=> date('Y-m-d H:i:s'),
                'pay_type'				=> 3,
                'stripecardno'          =>$request->cardno,
                'stripeexpirymonth'    =>$request->expirymonth,
                'stripeexpiryyear'     =>$request->expiryyear,
                'stripeccvno'          =>$request->ccvno
				];
				$insert = insertvalues('gr_merchant_commission',$insertArr);
				
				if($status==2) {
					DB::table('gr_notification')->where(['no_status' => '1', 'no_mer_id' => $merchant_id])->delete();
					Session::flash('message', 'Commission paid successfully');
				}
				else{
					Session::flash('message', 'Payment Failure');
				}
				echo $status;
				//return Redirect::to('/admin-commission-tracking');
				
			}
			else
			{
				echo "0";
				//Session::flash('message','No merchant found');
				//return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'No merchant found']);
			}
			
		}
		
		public function cancelstripepay(Request $request)
		{
			
			$merchant_id=$request->customerid;
			
			$paidAmount=$request->amttopay;
			$paidAmount=round($paidAmount);
			Session::forget('sess_checkoutId');
			Session::forget('sess_mer_clientId');
			Session::forget('sess_mer_secretId');
			Session::put('sess_mer_clientId',$request->client_id);
			Session::put('sess_mer_secretId',$request->secret_id);
			$merchant_det = DB::table('gr_customer')->where('cus_id',$merchant_id)->where('cus_status','1')->first();
			if(empty($merchant_det)===false)
			{
				require_once('./stripe/lib/Stripe.php');
				$secret_key = $merchant_det->cus_paynamics_secretid;
				$publishable_key = $merchant_det->cus_paynamics_clientid;
				$stripe = array("secret_key" => $secret_key, "publishable_key" => $publishable_key);
				Stripe::setApiKey($stripe['secret_key']);
				$token = $request->stripeToken;
				try {
					Stripe_Charge::create ( array (
                    "amount" => $paidAmount,   //Stripe payment only accept round integer
                    "currency" =>'USD',
                    "source" => $token, // obtained with Stripe.js
                    "description" => "CommissionPaid."
					) );
					$status="2";
					
					} catch ( \Exception $e ) {
					$status="3";
				}
				
				//Here insert
				$insertArr = ['ord_cancel_paidamt'	=> $paidAmount,
                'ord_cancelpaid_transid' 		=> $token,
                'cancel_paid_date'				=> date('Y-m-d H:i:s'),
                'ord_cancel_paymaya_pmtId' 		=> $token,
                'ord_cancel_paymaya_paid_time' 	=> date('Y-m-d H:i:s'),
				];
				DB::table('gr_order')->where('ord_id',$request->orderid)->update($insertArr);
				/**delete notification **/
				Session::flash('message','Cancellation amount paid successfully');
				// return Redirect::to('manage-cancelled-order');
				
				/* $insertArr = ['commision_admin_id'	=> '1',
					'commission_mer_id' 	=> $merchant_id,
					'commission_paid'	 	=> $paidAmount,
					'commission_currency'	=> Session::get('default_currency_symbol'),
					'mer_commission_status'	=> '2',
					'stripetoken'           =>$token,
					'mer_transaction_id' 	=> 'stripe'.rand(),
					'mer_commission_status' => $status,
					'commission_date'		=> date('Y-m-d H:i:s'),
					'pay_type'				=> 3,
					'stripecardno'          =>$request->cardno,
					'stripeexpirymonth'    =>$request->expirymonth,
					'stripeexpiryyear'     =>$request->expiryyear,
					'stripeccvno'          =>$request->ccvno
					];
				$insert = insertvalues('gr_merchant_commission',$insertArr);*/
				
				if($status==2) {
					DB::table('gr_notification')->where(['no_status' => '1', 'no_mer_id' => $merchant_id])->delete();
					Session::flash('message', 'Commission paid successfully');
				}
				else{
					Session::flash('message', 'Payment Failure');
				}
				echo $status;
				//return Redirect::to('/admin-commission-tracking');
				
			}
			else
			{
				echo "0";
				Session::flash('message','No merchant found');
				//return Redirect::to('/admin-commission-tracking')->withErrors(['errors' => 'No merchant found']);
			}
			
		}
		
	}	