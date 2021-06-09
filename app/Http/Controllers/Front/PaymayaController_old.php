<?php 
namespace App\Http\Controllers;
namespace App\Http\Controllers\Front;
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
use App\Home;
use Config;
use Aceraven777\PayMaya\PayMayaSDK;
use Aceraven777\PayMaya\API\Checkout;
use Aceraven777\PayMaya\Model\Checkout\Item;
use App\Libraries\PayMaya\User as PayMayaUser;
use Aceraven777\PayMaya\Model\Checkout\ItemAmount;
use Aceraven777\PayMaya\Model\Checkout\ItemAmountDetails;
use Aceraven777\PayMaya\Model\Checkout\Address;


class PaymayaController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function paymaya_checkout(Request $request)
	{	
		/*PayMayaSDK::getInstance()->initCheckout(
			env('PAYMAYA_PUBLIC_KEY'),
			env('PAYMAYA_SECRET_KEY'),
			(\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
		);*/
		Session::forget('sess_checkoutId');
		/*PayMayaSDK::getInstance()->initCheckout(
				'pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz',
				'sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',
				(\App::environment('production') ? 'SANDBOX' : 'SANDBOX')
			);*/
		PayMayaSDK::getInstance()->initCheckout(
				'pk-nRO7clSfJrojuRmShqRbihKPLdGeCnb9wiIWF8meJE9',
				'sk-jZK0i8yZ30ph8xQSWlNsF9AMWfGOd3BaxJjQ2CDCCZb',
				(\App::environment('production') ? 'SANDBOX' : 'SANDBOX')
			);
			
		$sample_item_name = 'Item - 111';
		$sample_total_price = 123.64;

		$sample_user_phone = '+19516238477';
		$sample_user_email = 'nagoor@pofitec.com';
		
		

		// Item
		$itemAmountDetails = new ItemAmountDetails();
		$itemAmountDetails->tax = "0.32";
		$itemAmountDetails->shippingFee = "5.00";
		$itemAmountDetails->subtotal = "24.32";
		
		$itemAmount = new ItemAmount();
		$itemAmount->currency = "PHP";
		$itemAmount->value = '8.00';//$itemAmountDetails->subtotal;
		$itemAmount->details = $itemAmountDetails;
		
		$itemTotalAmount = new ItemAmount();
		$itemTotalAmount->currency = "PHP";
		$itemTotalAmount->value = "24.32";
		$itemTotalAmount->details = $itemAmountDetails;
		
		
		$item = new Item();
		$item->name = $sample_item_name;
		$item->amount = $itemAmount;
		$item->quantity = 2;
		$item->totalAmount = $itemTotalAmount;
		
		
							
		// Checkout
		$itemCheckout = new Checkout();

		$user = new PayMayaUser();
		$user->contact->phone = $sample_user_phone;
		$user->contact->phone2 = $sample_user_phone;
		$user->contact->email = $sample_user_email;
		$address = new Address();
		$address->line1 = 'Coimbatore, Tamil Nadu 641012, India';
		//$address->line2 = 'coimbatore2';
		//$address->city = 'Coimbatore';
		//$address->state = 'Tamil Nadu';
		//$address->zipCode = '641008';
		//$address->countryCode = 'IN';
		
		$user->shippingAddress = $address;
		
		$itemAmount = new ItemAmount();
		$itemAmount->currency = "PHP";
		$itemAmount->value = "123.64";
		
		$itemAmountDetails = new ItemAmountDetails();
		$itemAmountDetails->tax = "0.00";
		$itemAmountDetails->shippingFee = "5.00";
		$itemAmountDetails->subtotal = "123.64";
		
		
		$itemAmount->details = $itemAmountDetails;

				
		$sample_reference_number = '1234567890';
		$itemCheckout->buyer = $user->buyerInfo();
		$itemCheckout->items = array($item);
		$itemCheckout->totalAmount = $itemAmount;
		$itemCheckout->requestReferenceNumber = $sample_reference_number;
		$itemCheckout->redirectUrl = array(
											"success" => url('paymaya_success'),
											"failure" => url('paymaya_failure'),
											"cancel" => url('paymaya_failure'),
		);
		//echo "<pre>"; print_r($itemCheckout);  exit;
		if ($itemCheckout->execute() === false) {
			$error = $itemCheckout::getError();
			print_r($error); exit;
			//return redirect()->back()->withErrors(['message' => $error['message']]);
		}
		
		if ($itemCheckout->retrieve() === false) {
			$error = $itemCheckout::getError();
			print_r($error); exit;
			//return redirect()->back()->withErrors(['message' => $error['message']]);
		}

		Session::put('sess_checkoutId',$itemCheckout->id);
		//echo $itemCheckout->url; exit;https://sandbox-checkout-v2.paymaya.com/checkout?id=c5817c09-5e5d-43d8-a23e-9adacae7830e
		// "https://sandbox-checkout-v2.paymaya.com/checkout?id=4cf05e7b-158c-46f1-a09f-ae52d7673ba9" got url from postman
		return redirect()->to($itemCheckout->url);
	}
	public function paymaya_checkout_new(Request $request)
	{
		Session::forget('sess_checkoutId');
		PayMayaSDK::getInstance()->initCheckout(
			env('PAYMAYA_PUBLIC_KEY'),
			env('PAYMAYA_SECRET_KEY'),
			(\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
		);
		$validator = Validator::make($request->all(), ['ord_self_pickup' => 'required']);
		if($request->ord_self_pickup==0)
		{
			$validator = Validator::make($request->all(), [
															'name' => 'required',
															'lname' => 'required',
															'mail' => 'required',
															'phone1' => 'required',
															'phone2' =>'required',
															'lat' =>'required',
															'long' =>'required',
															'address' =>'required',
															'paymentMode' =>'required']);
		}
		if ($validator->fails()) 
		{
			echo 'here'; exit;
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
			$phone2  	= ($request->phone2 == '') 	? $profile_address->cus_phone2 	: $request->phone2;
			$cus_address= ($request->address == '') ? $profile_address->cus_address : $request->address;
			$latitude 	= ($request->lat == '') 	? $profile_address->cus_latitude : $request->lat;
			$longitude  = ($request->long == '') 	? $profile_address->cus_longitude : $request->long;
			$wallet_used  = $request->use_wallet;
			$sample_reference_number = 'Paymaya'.rand();
			$total_amt_to_pay = $wallet_amt = $ch_price = $grand_sub_total = $grand_tax = 0;
			$shippingFee = ($request->ord_self_pickup==0) ? Config::get('delivery_fee') : '0.00';
			
			/** calculate wallet **/
			if($wallet_used == 1)
			{
				$wallet_amt = $request->wallet_amt;				
			}
			$itemArray = array();
			$item = array();
			$get_cart_details = Home::get_products_incart();
			if(count($get_cart_details)  > 0 )
			{	
				foreach($get_cart_details as $key=>$value)
				{	
					foreach($value as $pdtDetail)
					{
						$sub_total = $pdtDetail->cart_total_amt;
						$cart_tax = $pdtDetail->cart_tax;
						$grand_sub_total +=$sub_total;
						$grand_tax +=$cart_tax;
						$choices = json_decode($pdtDetail->cart_choices_id,true);
						$ch_array = array();
						
						if(count($choices) > 0)
						{
							foreach($choices as $ch)
							{
								$ch_price += number_format(($ch['choice_price'] * $pdtDetail->cart_quantity),2);
							}
						}
						
						if($request->ord_self_pickup == 1)	/* Self pickup */
						{
							$total_amt_to_pay = $grand_sub_total  - $wallet_amt;
						}
						else
						{
							$total_amt_to_pay = $grand_sub_total   + Config::get('delivery_fee') - $wallet_amt;	
						}
						/*-------- START ITEM DETAILS ----- */
						// Item
						$itemAmountDetails = new ItemAmountDetails();
						$itemAmountDetails->tax = number_format($cart_tax,2);
						$itemAmountDetails->subtotal = number_format($sub_total,2);
						
						$itemAmount = new ItemAmount();
						$itemAmount->currency = "PHP";
						$itemAmount->value = number_format($pdtDetail->cart_unit_amt,2);//$itemAmountDetails->subtotal;
						$itemAmount->details = $itemAmountDetails;
						
						$itemTotalAmount = new ItemAmount();
						$itemTotalAmount->currency = "PHP";
						$itemTotalAmount->value = number_format($sub_total-$cart_tax,2);
						$itemTotalAmount->details = $itemAmountDetails;
						
						
						$item = new Item();
						$item->name = $pdtDetail->item_name;
						$item->code 		= $pdtDetail->pro_item_code;
						$item->description 	= $pdtDetail->contains_name;
						$item->amount = $itemAmount;
						$item->quantity = $pdtDetail->cart_quantity;
						$item->totalAmount = $itemTotalAmount;	
						array_push($itemArray,$item);						
						/*-------- EOF   ITEM DETAILS ----- */
						$cartarray = ['cart_transaction_id' => $sample_reference_number];
						$update = updatevalues('gr_cart_save',$cartarray,['cart_id' => $pdtDetail->cart_id]);
					}
				}
			}
			
			/* ----------------------- EOF DETAILS FROM DATABASE ------------------ */
			// Checkout
			$itemCheckout = new Checkout();

			$user = new PayMayaUser();
			$user->firstName = $name;
			$user->middleName='SelfPickup-'.$request->ord_self_pickup;
			$user->lastName = $lname;
			$user->contact->phone = $phone1;
			$user->contact->email = $mail;
			$address = new Address();
			$address->line1 = $cus_address;
			$address->line2 = $latitude.'`'.$longitude;
			$address->city = $phone2;
			$user->shippingAddress = $address;
			
			$itemAmountDetails = new ItemAmountDetails();
			$itemAmountDetails->tax = number_format($grand_tax,2);
			$itemAmountDetails->shippingFee = number_format($shippingFee,2);
			$itemAmountDetails->subtotal = number_format($grand_sub_total,2);
			$itemAmountDetails->discount = number_format($wallet_amt, 2, '.', '');
			
			$itemAmount = new ItemAmount();
			$itemAmount->currency = "PHP";
			$itemAmount->value = number_format($total_amt_to_pay,2);
			$itemAmount->details = $itemAmountDetails;

			$itemCheckout->buyer = $user->buyerInfo();
			//$itemCheckout->items = array($item);
			$itemCheckout->items = $itemArray;
			$itemCheckout->totalAmount = $itemAmount;
			$itemCheckout->requestReferenceNumber = $sample_reference_number;
			$itemCheckout->redirectUrl = array(
												"success" => url('paymaya_success'),
												"failure" => url('paymaya_failure'),
												"cancel" => url('paymaya_failure'),
			);
			//echo "<pre>"; print_r($itemCheckout);  exit;
			if ($itemCheckout->execute() === false) {
				$error = $itemCheckout::getError();
				print_r($error); exit;
				//return redirect()->back()->withErrors(['message' => $error['message']]);
			}
			
			if ($itemCheckout->retrieve() === false) {
				$error = $itemCheckout::getError();
				print_r($error); exit;
				//return redirect()->back()->withErrors(['message' => $error['message']]);
			}

			Session::put('sess_checkoutId',$itemCheckout->id);
			return redirect()->to($itemCheckout->url);
		}
	}
	public function paymaya_checkout3(Request $request)
	{	//Session::forget('sess_checkoutId');
		//instead of sandbox = PRODUCTION
				/*PayMayaSDK::getInstance()->initCheckout(
					'pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz',
					'sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',
					(\App::environment('production') ? 'SANDBOX' : 'SANDBOX')
				);*/
		PayMayaSDK::getInstance()->initCheckout(
			env('PAYMAYA_PUBLIC_KEY'),
			env('PAYMAYA_SECRET_KEY'),
			(\App::environment('production') ? 'PRODUCTION' : 'SANDBOX')
		);

		$validator = Validator::make($request->all(), ['ord_self_pickup' => 'required']);
		if($request->ord_self_pickup==0)
		{
			$validator = Validator::make($request->all(), [
															'name' => 'required',
															'lname' => 'required',
															'mail' => 'required',
															'phone1' => 'required',
															'phone2' =>'required',
															'lat' =>'required',
															'long' =>'required',
															'address' =>'required',
															'paymentMode' =>'required']);
		}
		if ($validator->fails()) 
		{
			
			echo 'here'; exit;
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
			$phone2  	= ($request->phone2 == '') 	? $profile_address->cus_phone2 	: $request->phone2;
			$cus_address  	= ($request->address == '') ? $profile_address->cus_address : $request->address;
			$latitude 	= ($request->lat == '') 	? $profile_address->cus_latitude : $request->lat;
			$longitude  = ($request->long == '') 	? $profile_address->cus_longitude : $request->long;
			$wallet_used  = $request->use_wallet;	
			$get_cart_details = Home::get_products_incart();
			$total_amt_to_pay = $wallet_amt = 0;
			$itemArray = array();
			$item = array();
			/** calculate wallet **/
			if($wallet_used == 1)
			{
				$wallet_amt = $request->wallet_amt;				
			}
			$sample_reference_number = '1234567890';//'Paymaya'.rand();
			if(count($get_cart_details)  > 0 )
			{	
				foreach($get_cart_details as $key=>$value)
				{	
					foreach($value as $pdtDetail)
					{	
						$overall_amt_withtax = $overall_admin_amt = 0;
						$cartarray = ['cart_transaction_id' => $sample_reference_number];
						$update = updatevalues('gr_cart_save',$cartarray,['cart_id' => $pdtDetail->cart_id]);
							
						/* product cart    transaction_id*/
						if($pdtDetail->cart_type=='1')	
						{	
							$itemAmountDetails = new ItemAmountDetails();
							//$itemAmountDetails->shippingFee = ($request->ord_self_pickup==0) ? Config::get('delivery_fee') : '0.00';
							$itemAmountDetails->subtotal = number_format($pdtDetail->cart_total_amt, 2, '.', '');
							//$itemAmountDetails->tax = number_format($pdtDetail->cart_tax, 2, '.', '');

							$itemAmount = new ItemAmount();
							$itemAmount->currency = "PHP";
							if($pdtDetail->pro_has_discount=='yes')
							{
								$itemAmount->value = number_format($pdtDetail->pro_discount_price, 2, '.', '');;
							}
							else
							{
								$itemAmount->value = number_format($pdtDetail->pro_original_price, 2, '.', '');;
							}
							$itemAmount->details = $itemAmountDetails;
							
							$itemTotalAmount = new ItemAmount();
							$itemTotalAmount->currency = "PHP";
							$itemTotalAmount->value = $pdtDetail->cart_total_amt;
							$itemTotalAmount->details = $itemAmountDetails;
							
							//$commission = number_format((($pdtDetail->cart_total_amt * $pdtDetail->mer_commission) / 100),2);
							//$sub_total = ($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity);
							/** calculate overall merchant amt **/
							//$overall_amt_withtax +=	$pdtDetail->cart_total_amt;
							//$overall_admin_amt +=	$commission;
							/** calculate overall merchant amt ends **/
							$total_amt_to_pay += $pdtDetail->cart_total_amt;
							/* --------store item details in object----------*/
							$item = new Item();
							$item->name 		= $pdtDetail->item_name;
							//$item->code 		= $pdtDetail->pro_item_code;
							//$item->description 	= $pdtDetail->contains_name;
							$item->quantity 	= $pdtDetail->cart_quantity;
							$item->amount 		= $itemAmount;
							$item->totalAmount 	= $itemTotalAmount;
							array_push($itemArray,$item);
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
													'choice_price'	=> number_format(($ch['choice_price'] * $pdtDetail->cart_quantity),2)
												  ];
 									$ch_price += number_format(($ch['choice_price'] * $pdtDetail->cart_quantity),2);
								}
							}

							$sub_total = (($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity) + $ch_price);
							$total_amt = $sub_total + $pdtDetail->cart_tax;
							$commission = number_format((($total_amt * $pdtDetail->mer_commission) / 100),2);
							/** calculate overall merchant amt  **/
							$overall_amt_withtax += $total_amt;
							$overall_admin_amt 	 +=	$commission;
							/** calculate overall merchant amt ends **/	
							$total_amt_to_pay += $total_amt;

							$itemAmountDetails = new ItemAmountDetails();
							$itemAmountDetails->shippingFee = ($request->ord_self_pickup==0) ? Config::get('delivery_fee') : '0.00';
							$itemAmountDetails->subtotal = number_format($total_amt, 2, '.', '');
							//$itemAmountDetails->tax = number_format($pdtDetail->cart_tax, 2, '.', '');

							/*$itemAmount = new ItemAmount();
							$itemAmount->currency = "PHP";
							$itemAmount->value = $total_amt;
							$itemAmount->details = $itemAmountDetails;*/
							
							$itemAmount = new ItemAmount();
							$itemAmount->currency = "PHP";
							if($pdtDetail->pro_has_discount=='yes')
							{
								$itemAmount->value = number_format($pdtDetail->pro_discount_price, 2, '.', '');;
							}
							else
							{
								$itemAmount->value = number_format($pdtDetail->pro_original_price, 2, '.', '');;
							}
							$itemAmount->details = $itemAmountDetails;
							
							$itemTotalAmount = new ItemAmount();
							$itemTotalAmount->currency = "PHP";
							$itemTotalAmount->value = $pdtDetail->cart_total_amt;
							$itemTotalAmount->details = $itemAmountDetails;
							
							/*---------------------store items in object-------------------------*/
							
							$item = new Item();
							$item->name 		= $pdtDetail->item_name;
							//$item->code 		= $pdtDetail->pro_item_code;
							//$item->description 	= $pdtDetail->contains_name;
							$item->quantity 	= $pdtDetail->cart_quantity;
							$item->amount 		= $itemAmount;
							$item->totalAmount 	= $itemTotalAmount;
							//array_push($itemArray,$item);
						}
					}
				}
				
				//print_r($item); exit;
				if($request->ord_self_pickup == 1)	/* Self pickup */
				{
					$total_amt_to_pay = $total_amt_to_pay  - $wallet_amt;
				}
				else
				{
					$total_amt_to_pay = $total_amt_to_pay   + Config::get('delivery_fee') - $wallet_amt;	
				}
				$itemAmountDetails = new ItemAmountDetails();
				//$itemAmountDetails->shippingFee = ($request->ord_self_pickup==0) ? Config::get('delivery_fee') : '0.00';
				$itemAmountDetails->subtotal = number_format($total_amt_to_pay, 2, '.', '');
				$itemAmountDetails->discount = number_format($wallet_amt, 2, '.', '');

				$itemAmount = new ItemAmount();
				$itemAmount->currency = "PHP";
				$itemAmount->value = $total_amt_to_pay;
				$itemAmount->details = $itemAmountDetails;

				$user = new PayMayaUser();
				//$user->firstName = $name;
				//$user->middleName='WalletAmount-'.$wallet_amt;
				//$user->lastName = $lname;
				$user->contact->phone = $phone1;
				$user->contact->email = $mail;
				$address = new Address();
				$address->line1 = $cus_address;
				//$address->line2 = 'coimbatore2';
				//$address->city = 'Coimbatore';
				//$address->state = 'Tamil Nadu';
				//$address->zipCode = '641008';
				//$address->countryCode = 'IN';
				
				$user->shippingAddress = $address;
					
				// Checkout
				
				$itemCheckout = new Checkout();
				$itemCheckout->buyer = $user->buyerInfo();
				//$itemCheckout->items = array($item);
				$itemCheckout->items = array($item);
				$itemCheckout->totalAmount = $itemAmount;
				$itemCheckout->requestReferenceNumber = $sample_reference_number;
				$itemCheckout->redirectUrl = array(
													"success" => url('paymaya_success'),
													"failure" => url('paymaya_failure'),
													"cancel" => url('paymaya_failure'),
												   );
				//echo "<pre>"; print_r($itemCheckout);  exit;
				if ($itemCheckout->execute() === false)
				{
					$error = $itemCheckout::getError();
					echo "exe<pre>"; print_r($error); exit;
					return redirect()->back()->withErrors(['message' => $error['message']]);
				}
		
				if ($itemCheckout->retrieve() === false)
				{
					$error = $itemCheckout::getError();
					echo "retrive<pre>"; print_r($error); exit;
					return redirect()->back()->withErrors(['message' => $error['message']]);
				}
				Session::put('sess_checkoutId',$itemCheckout->id);
				return redirect()->to($itemCheckout->url);
			}
		}
	}

	/* Checkout success */
	public function checkout_success(Request $request)
	{	
		/*PayMayaSDK::getInstance()->initCheckout(
					'pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz',
					'sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',
					(\App::environment('production') ? 'SANDBOX' : 'SANDBOX')
				);
		*/
		PayMayaSDK::getInstance()->initCheckout(env('PAYMAYA_PUBLIC_KEY'),env('PAYMAYA_SECRET_KEY'),(\App::environment('production') ? 'PRODUCTION' : 'SANDBOX'));
		$transaction_id = Session::get('sess_checkoutId');
		if (!$transaction_id) {
			return Redirect::to('/')->withErrors(['message' => 'Transaction Id Missing']);
		}
		$itemCheckout = new Checkout();
		$itemCheckout->id = $transaction_id;
		$checkout = $itemCheckout->retrieve();
		if($checkout['paymentStatus'] == "PAYMENT_SUCCESS")
		{
			$cus_id		= Session::get('customer_id');
			/** update wallet amount for customer **/
			$updat = updatevalues('gr_customer',['used_wallet' => DB::raw('"used_wallet"+'.$checkout['totalAmount']['details']['discount'])],['cus_id' => $cus_id]);

			/** Add order details **/
			$requestReferenceNumber = $checkout['requestReferenceNumber'];
			
			$total = $wallet_amt = 0;
			$shipping_latlong = explode('`',$checkout['buyer']['shippingAddress']['line2']);
			$cartSaveDet = DB::table('gr_cart_save')->where('cart_transaction_id',$requestReferenceNumber)->where('cart_cus_id',$cus_id)->get();
			if(count($cartSaveDet) > 0 )
			{
				foreach($cartSaveDet as $cartDet)
				{
					$overall_amt_withtax = $overall_admin_amt = 0;
					$selfPickupStatus = str_replace('SelfPickup-','',$checkout['buyer']['middleName']);
					$mer_det = DB::table('gr_store')->select('st_mer_id')->where('id',$cartDet->cart_st_id)->first();
					$mer_commission = DB::table('gr_merchant')->select('mer_commission')->where('id',$mer_det->st_mer_id)->first()->mer_commission;
					$choices = json_decode($cartDet->cart_choices_id,true);
					$ch_array = array();
					$ch_price = 0;
					if(count($choices) > 0)
					{
						foreach($choices as $ch)
						{
							$ch_array[] = ['choice_id'		 => $ch['choice_id'],
											'choice_price'	=> number_format(($ch['choice_price'] * $cartDet->cart_quantity),2)
										  ];
							$ch_price += number_format(($ch['choice_price'] * $cartDet->cart_quantity),2);
						}
					}
					if($cartDet->cart_type=='2') { $ord_type = 'Item'; } else { $ord_type = 'Product'; }
					$sub_total = (($cartDet->cart_unit_amt * $cartDet->cart_quantity) + $ch_price);
					$total_amt = $sub_total + $cartDet->cart_tax;
					$commission = number_format((($total_amt * $mer_commission) / 100),2);
					$overall_amt_withtax +=	$cartDet->cart_total_amt;
					$overall_admin_amt +=	$commission;
					$wallet_amt = $checkout['totalAmount']['details']['discount'];
					$insertArr = array('ord_cus_id' 			=> $cus_id,
								  'ord_shipping_cus_name' 	=> ucfirst($checkout['buyer']['firstName']).' '.$checkout['buyer']['lastName'],
								  'ord_shipping_address'  	=> $checkout['buyer']['shippingAddress']['line1'],
								  'ord_shipping_mobile'	  	=> $checkout['buyer']['contact']['phone'],
								  'ord_shipping_mobile1'	=> $checkout['buyer']['shippingAddress']['city'],
								  'order_ship_mail'			=> $checkout['buyer']['contact']['email'],
								  'order_ship_latitude'		=> $shipping_latlong[0],
								  'order_ship_longitude'	=> $shipping_latlong[1],
								  'ord_merchant_id'			=> $mer_det->st_mer_id,
								  'ord_rest_id'				=> $cartDet->cart_st_id,
								  'ord_pro_id'				=> $cartDet->cart_item_id,
								  'ord_had_choices'			=> $cartDet->cart_had_choice,
								  'ord_choices'				=> json_encode($ch_array),
								  'ord_choice_amount'		=> $ch_price,
								  'ord_quantity'			=> $cartDet->cart_quantity,
								  'ord_currency'			=> $cartDet->cart_currency,
								  'ord_unit_price'			=> $cartDet->cart_unit_amt,
								  'ord_sub_total'			=> $sub_total,
								  'ord_tax_amt'				=> ($cartDet->cart_tax != '') ? $cartDet->cart_tax : 0,
								  'ord_has_coupon'			=> "No",
								  'ord_coupon_amt'			=> 0,
								  'ord_delivery_fee'		=> $checkout['totalAmount']['details']['shippingFee'],
								  'ord_grant_total'			=> $cartDet->cart_total_amt,
								  'ord_wallet'				=> $wallet_amt,
								  'ord_type'				=> $ord_type,
								  'ord_pay_type'			=> "Paymaya",
								  'ord_transaction_id'		=> $requestReferenceNumber,
								  'ord_payment_status'		=> "Success",
								  'ord_status'				=> 1,
								  'ord_date'				=> date('Y-m-d H:i:s'),
								  'ord_admin_amt'			=> $commission,
								  'ord_self_pickup'			=> $selfPickupStatus,
								  'ord_task_status'			=> '0',
								  );
					$insert = insertvalues('gr_order',$insertArr);
					$total += $sub_total;
					$delete = deletecart($cartDet->cart_id);
					/** update quantity in product table **/
					$pro_no_of_purchase = DB::table('gr_product')->select('pro_no_of_purchase')->where('pro_id',$cartDet->cart_item_id)->first()->pro_no_of_purchase;
					$update = update_quantity(($pro_no_of_purchase + $cartDet->cart_quantity),$cartDet->cart_item_id);
					
					/** Calculate merchant amount **/
					$get_details = Home::merchant_orderDetails($mer_det->st_mer_id);

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
									'or_mer_amt'	=>	$mer_amt];
						$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $mer_det->st_mer_id]);
					}
					else 		//add
					{
						/** insert in merchant overall table **/
						$array = [	'or_mer_id'		=>	$mer_det->st_mer_id,
									'or_total_order' => 1,
									'or_admin_amt'	=> $overall_admin_amt,
									'or_coupon_amt' => $wallet_amt,
									'or_mer_amt'	=>	$overall_amt_withtax];
						$update = insertvalues('gr_merchant_overallorder',$array);
					}
					/** Calculate merchant amount ends  **/
				}
				/** add wallet amount for referrel **/
				$refer_details = Home::refer_status(Session::get('customer_mail'));
				if(empty($refer_details) === false)
				{
					if($refer_details->referral_id != '')
					{	
						/* Update referel wallet */
						$user = get_user(['cus_id' => $refer_details->referral_id,'cus_status' => '1']);
						$offer_amt = number_format((($total * $refer_details->re_offer_percent)/100),2);
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
				if($selfPickupStatus==0)
				{
					$CheckShExists = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->first();
					if(empty($CheckShExists) === false)
					{
	
						$gr_shipping_array = ['sh_cus_fname' 	=> ucfirst($checkout['buyer']['firstName']),
											  'sh_cus_lname' 	=> $checkout['buyer']['lastName'],
											  'sh_location'  	=> $checkout['buyer']['shippingAddress']['line1'],
											  'sh_phone1'	  	=> $checkout['buyer']['contact']['phone'],
											  'sh_phone2'		=> $checkout['buyer']['shippingAddress']['city'],
											  'sh_cus_email'	=> $checkout['buyer']['contact']['email'],
											  'sh_latitude'		=> $shipping_latlong[0],
											  'sh_longitude'	=> $shipping_latlong[1],
											  ];
						DB::table('gr_shipping')->where(['sh_cus_id' => $cus_id])->update($gr_shipping_array);
					}
					else
					{
						$gr_shipping_array = ['sh_cus_id'		=> $cus_id,
											  'sh_cus_fname' 	=> ucfirst($checkout['buyer']['firstName']),
											  'sh_cus_lname' 	=> $checkout['buyer']['lastName'],
											  'sh_location'  	=> $checkout['buyer']['shippingAddress']['line1'],
											  'sh_phone1'	  	=> $checkout['buyer']['contact']['phone'],
											  'sh_phone2'		=> $checkout['buyer']['shippingAddress']['city'],
											  'sh_cus_email'	=> $checkout['buyer']['contact']['email'],
											  'sh_latitude'		=> $shipping_latlong[0],
											  'sh_longitude'	=> $shipping_latlong[1],
											  ];
						$update = insertvalues('gr_shipping',$gr_shipping_array);
					}
				}
				
				/* EOF UPDATING SHIPPING ADDRESS*/ 
				/** mail function **/
				$transaction_id = $requestReferenceNumber;
				$get_order_details = Home::get_order_details($transaction_id);  
				$customerDet 	   = Home::get_customer_details($transaction_id);
				//print_r($get_order_details); exit;  
				if(empty($get_order_details) === false)
				{
					 $send_mail_data = array('order_details'	=> $get_order_details,
	                                         'customerDet'		=> $customerDet,
	                                         'transaction_id'	=> $transaction_id,
											 'self_pickup'		=> $selfPickupStatus,
											 );
					 /** mail to customer **/
					$buyer_email = $checkout['buyer']['contact']['email'];
					Mail::send('email.order_mail_customer', $send_mail_data, function($message) use($buyer_email)
                    {	
                    	$message->to($buyer_email)->subject("Your order is successful");
                    });
                    /* Mail to admin */
                    $admin_mail = Config::get('admin_mail');
                    Mail::send('email.order_mail_admin', $send_mail_data, function($message) use($admin_mail)
                    {	
                    	$message->to($admin_mail)->subject("Received New Order");
                    });
                    /* Mail to merchant */
                    foreach($get_order_details as $key=>$itemsDet)
                    {	$explodeRest = explode('`',$key);
                    	$mer_mail = $explodeRest[1];
                    	$send_mail_data = array('order_details'		=> $itemsDet,
	                                         	'customerDet'		=> $customerDet,
	                                         	'transaction_id'	=> $transaction_id,
	                                         	'store_name' 		=> $explodeRest[0],
												'self_pickup'		=> $selfPickupStatus,
												);
	                    Mail::send('email.order_mail_merchant', $send_mail_data, function($message) use($mer_mail)
	                    {	
	                    	$message->to($mer_mail)->subject("Received New Order");
	                    });
                	}
           		}
             	/* eof mail function */ 
			}
			Session::flash('success','Order placed successfully');
			return Redirect::to('/');
		}
		else
		{
			return Redirect::to('paymaya_failure');
		}
	}
	public function checkout_failure(Request $request)
	{
		//print_r($request->all());
		PayMayaSDK::getInstance()->initCheckout('pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz','sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',(\App::environment('production') ? 'SANDBOX' : 'SANDBOX'));
		$transaction_id = Session::get('sess_checkoutId');
		if (!$transaction_id) {
			return Redirect::to('/')->withErrors(['message' => 'Transaction Id Missing']);
		}
		return Redirect::to('/')->withErrors(['message' => 'Payment Failure']);
		/*$itemCheckout = new Checkout();
		
		$itemCheckout->id = $transaction_id;
		$itemCheckout->retrieve();
		
		$error = $itemCheckout::getError();
			echo "retrive failure<pre>"; print_r($error); exit;
		if ($itemCheckout->retrieve() === false)
		{
			$error = $itemCheckout::getError();
			echo "retrive failure<pre>"; print_r($error); exit;
			//return redirect()->back()->withErrors(['message' => $error['message']]);
		}*/
	}
}