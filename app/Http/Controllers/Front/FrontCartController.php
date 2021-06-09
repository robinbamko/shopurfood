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
	
	class FrontCartController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
		}
		public function cart_details()
		{
			update_cart_price();
			$pdtsInCart = Home::get_products_incart();
			$count = 0;
			foreach($pdtsInCart as $pdtcount)
			{
				$count +=count($pdtcount);
			}
			$shippingDet = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id','=',Session::get('customer_id'))->first();
			$delivery_fee_set = DB::table('gr_general_setting')->select('gs_delivery_fee_status','gs_delivery_fee','gs_del_fee_type','gs_km_fee')->first();
			$merchant_payment_det = DB::table('gr_customer')->select('cus_paynamics_status','cus_paymaya_status','cus_netbank_status')->where('cus_id','=',Session::get('customer_id'))->first();
			return view('Front.cart_listings')->with(['pdtsInCart'=>$pdtsInCart,'pdtCount'=>$count,'delivery_fee_set'=>$delivery_fee_set,'merchant_payment_det'=>$merchant_payment_det,'shippingDet' => $shippingDet]);
		}
		/** REMOVE FROM CART **/
		//	public function remove_from_cart(Request $request)
		//	{
		//		$cart_id = $request->cart_id;
		//		$store_id = $request->store_id;
		//		DB::table('gr_cart_save')->where('cart_id', '=', $cart_id)->delete();
		//
		//		$cart_count = cart_count(Session::get('customer_id'));
		//		$st_item_count = cart_count(Session::get('customer_id'),$store_id);
		//
		//        $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_IT_REMOVE')) ? trans(Session::get('front_lang_file').'.FRONT_IT_REMOVE') : trans($this->FRONT_LANGUAGE.'.FRONT_IT_REMOVE');
		//
		//        /* get store/restaurant added item amount */
		//		$store_total_amt = Home::get_shop_total_amt($store_id);
		//
		//		return $msg.'`'.$cart_count.'`'.$st_item_count.'`'.$store_total_amt;
		//
		//	}
		public function remove_from_cart(Request $request)
		{
			$cart_id = $request->cart_id;
			$store_id = $request->store_id;
			DB::table('gr_cart_save')->where('cart_id', '=', $cart_id)->delete();
			
			$cart_count = cart_count(Session::get('customer_id'));
			$st_item_count = cart_count(Session::get('customer_id'),$store_id);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_IT_REMOVE')) ? trans(Session::get('front_lang_file').'.FRONT_IT_REMOVE') : trans($this->FRONT_LANGUAGE.'.FRONT_IT_REMOVE');
			/* get store/restaurant added item amount */
			$store_total_amt = Home::get_shop_total_amt($store_id);
			
			return $msg.'`'.$cart_count.'`'.$st_item_count.'`'.$store_total_amt;
			
		}
		public function remove_all_item(Request $request){
			DB::table('gr_cart_save')->where('cart_cus_id', '=', Session::get('customer_id'))->delete();
			return Redirect::to('cart');
		}
		public function clearSavecart(Request $request){
			//print_r($request->all());
			//exit;
			DB::connection()->enableQueryLog();
			DB::table('gr_cart_save')->whereIn('cart_st_id', $request->list_values)->where('cart_cus_id','=',Session::get('customer_id'))->delete();
			$query = DB::getQueryLog();
			print_r($query);
		}
		public function update_cart_choice(Request $request)
		{
			//print_r($request->all()); //6~6.00,5~7.00/2
			//echo $request->checkedVals.'/'.$request->cart_id;
			$cart_details = DB::table('gr_cart_save')->where('cart_id', '=', $request->cart_id)->first();
			$ch_array = array();
			$choice_total = 0;
			$choice_html = '';
			$exist_cart_id = 0;
			$pro_currency = '$';
			if(empty($cart_details)===false)
			{
				$quantity = $cart_details->cart_quantity;
				$cart_unit_amt = $cart_details->cart_unit_amt;
				$cart_tax = $cart_details->cart_tax;
				$checkedValues = explode(',',$request->checkedVals);
				if($request->checkedVals!='')
				{	
					foreach($checkedValues as $checkedValue)
					{
						$splitChecked = explode('~',$checkedValue);
						$ch_array[] = array("choice_id" => $splitChecked[0],"choice_price" => $splitChecked[1]);
						$choice_total +=$quantity*$splitChecked[1];
						$pro_currency = $splitChecked[2];
						$choice_html .= $splitChecked[3].'&nbsp;'.$splitChecked[2].'&nbsp;'.$splitChecked[1];
						if(count($checkedValues) > 1 && ($checkedValue != end($checkedValues)))
						{
							$choice_html .= '&nbsp;+&nbsp;';
						}
					}
					
					$cart_had_choice = 'Yes';
				}
				else
				{
					$cart_had_choice = 'No';
				}
				
				/** check item with choice already added **/
				//exit;
				$check 	= check_cart(Session::get('customer_id'),$cart_details->cart_st_id,$cart_details->cart_item_id,json_encode($ch_array),$pro_currency); 
				
				if(!empty($check))
				{
					
					$exist_choices = json_decode($check->cart_choices_id,true);				
					if(count($exist_choices) > 0 )				{
						$quantity = $quantity + $check->cart_quantity;				
						$actualPrice = $exist_choices[0]['choice_price'];				
						$choice_total = $quantity*$actualPrice;				
					}
					$exist_cart_id = $check->cart_id;
					// $quantity = $quantity + $check->cart_quantity;
				}
				
				$cart_total_amt = ($quantity*$cart_unit_amt)+$cart_tax+$choice_total;
				$insert_arr = [	'cart_had_choice'	=> $cart_had_choice,
				'cart_total_amt' 	=> $cart_total_amt,
				'cart_choices_id' 	=> json_encode($ch_array),
				'cart_updated_at' 	=> date('Y-m-d H:i:s'),
				'cart_quantity'		=> $quantity
				];
				if($exist_cart_id != '0')
				{
					$insert = updatevalues('gr_cart_save',$insert_arr,['cart_id' => $exist_cart_id]);
					/* delete old cart */
					DB::table('gr_cart_save')->where(['cart_id' => $request->cart_id])->delete(); 
				}
				else
				{
					$insert = updatevalues('gr_cart_save',$insert_arr,['cart_id' => $request->cart_id]);
				}
				
				
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CH_UPDATED')) ? trans(Session::get('front_lang_file').'.FRONT_CH_UPDATED') : trans($this->FRONT_LANGUAGE.'.FRONT_CH_UPDATED');
				
				/* get store/restaurant added item amount */
				$store_total_amt = Home::get_shop_total_amt($cart_details->cart_st_id);
				if($insert)
				{	$array = json_encode(array('item_total_amt' => $cart_total_amt,
					'tax' 	   => $cart_tax,
					'msg'     => $msg,
					'store_total_amount' => $store_total_amt,
					'store_id'	=>	$cart_details->cart_st_id,
					'choice_list' => $choice_html,
					'exist_cart_id'	=> $exist_cart_id,
					'quantity' 		=> $quantity ));
					return $array;
				}
				else
				{	
					
					
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_IT_NOT_UPDATE')) ? trans(Session::get('front_lang_file').'.FRONT_IT_NOT_UPDATE') : trans($this->FRONT_LANGUAGE.'.FRONT_IT_NOT_UPDATE');
					Session::flash('val_errors',$msg);
					return Redirect::back();
				}	
				
			}
		}
		public function update_cart_items(Request $request)
		{
			$quantity = $request->qty;
			$cart_details = DB::table('gr_cart_save')->select('cart_item_id','cart_unit_amt','cart_choices_id',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),'cart_st_id')
			->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->where(['gr_cart_save.cart_cus_id' => Session::get('customer_id'),'gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1','cart_id'=> $request->cart_id])
			->whereRaw('(gr_product.pro_quantity-gr_product.pro_no_of_purchase)>='.$quantity.'')
			->first();
			//return $cart_details->stock; exit;
			$ch_array = array();
			$choice_total = 0;
			if(empty($cart_details)===false)
			{	
				$cart_unit_amt = $cart_details->cart_unit_amt;
				$tax_calculation = DB::table('gr_product')->select('pro_had_tax','pro_tax_percent')->where('pro_id', '=', $cart_details->cart_item_id)->first();
				if(empty($tax_calculation)===false)
				{
					if($tax_calculation->pro_had_tax=='Yes')
					{
						$tax_percent = $tax_calculation->pro_tax_percent;
						$cart_tax = (($quantity*$cart_unit_amt)*$tax_percent)/100;
					}
					else
					{
						$cart_tax = 0;
					}
				}
				else
				{
					$cart_tax = 0;
				}
				
				
				$checkedValues = json_decode($cart_details->cart_choices_id,true);
				if($cart_details->cart_choices_id!='[]')
				{
					foreach($checkedValues as $checkedValue)
					{
						$ch_price = $checkedValue['choice_price'];
						$choice_total +=$quantity*$ch_price;
					}
					
					$cart_had_choice = 'Yes';
				}
				else
				{
					$cart_had_choice = 'No';
				}
				$cart_total_amt = ($quantity*$cart_unit_amt)+$cart_tax+$choice_total;
				$insert_arr = [	'cart_quantity'=>$quantity,
				'cart_tax'	=> $cart_tax,
				'cart_had_choice'	=> $cart_had_choice,
				'cart_total_amt' 	=> $cart_total_amt,
				'cart_updated_at' 	=> date('Y-m-d H:i:s')];
				//print_r($insert_arr); echo $request->cart_id; exit;
				$insert = updatevalues('gr_cart_save',$insert_arr,['cart_id' => $request->cart_id]);
				
				
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_IT_UPDATED')) ? trans(Session::get('front_lang_file').'.FRONT_IT_UPDATED') : trans($this->FRONT_LANGUAGE.'.FRONT_IT_UPDATED');
				
				/* get store/restaurant added item amount */
				$store_total_amt = Home::get_shop_total_amt($cart_details->cart_st_id);
				//return Redirect::to('cart');
				if($insert)
				{	$array = json_encode(array('item_total_amt' => $cart_total_amt,
					'tax' 	   => $cart_tax,
					'msg'     => $msg,
					'store_total_amount' => $store_total_amt,
					'store_id'	=>	$cart_details->cart_st_id,
					'action'  => 'success'));
					return $array;
				}
				else
				{	
					
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_IT_NOT_UPDATE')) ? trans(Session::get('front_lang_file').'.FRONT_IT_NOT_UPDATE') : trans($this->FRONT_LANGUAGE.'.FRONT_IT_NOT_UPDATE');
                    Session::flash('val_errors',$msg);
					return Redirect::back();
				}		
			}
			else
			{
				return $array = json_encode(array('action'  => 'failed'));
			}
		}
		/** checkout **/
		public function checkout(Request $request)
		{
			update_cart_price();
			$get_cart_details = Home::get_products_incart();
			//echo '<pre>'; print_r($get_cart_details); exit;
			if(count($get_cart_details)==0)
			{
				return Redirect::to('/');
			}
			$shipping_details = DB::table('gr_shipping')
			->select('sh_cus_fname','sh_cus_lname','sh_cus_email','sh_building_no','sh_phone1','sh_phone2','sh_location','sh_latitude','sh_longitude')
			->Join('gr_customer','gr_customer.cus_id','=','gr_shipping.sh_cus_id')
			->where('sh_cus_id',Session::get('customer_id'))
			->first();
			$delivery_fee_set = DB::table('gr_general_setting')->select('gs_delivery_fee_status','gs_delivery_fee','gs_del_fee_type','gs_km_fee')->first();
			$paymentgatewaydetails=DB::table('gr_payment_setting')->select('paynamics_client_id as cus_paynamics_clientid', 'paynamics_secret_id as cus_paynamics_secretid', 'paymaya_client_id as cus_paymaya_clientid', 'paymaya_secret_id as cus_paymaya_secretid')->first();
			/*echo '<pre>';
				print_r($paymentgatewaydetails);
				exit;
				$aymentgatewaydetails=DB::table('gr_customer')->select('cus_paynamics_secretid', 'cus_paynamics_clientid','cus_paymaya_clientid', 'cus_paymaya_secretid')->where('cus_id',session::get('customer_id'))->first()
			*/
			//This is just a Temprory
			
			$customer_details = DB::table('gr_customer')->select('gr_customer.cus_wallet','gr_customer.used_wallet','gr_customer.cus_paynamics_status','gr_customer.cus_paymaya_status','gr_customer.cus_netbank_status','mail_verify','cus_email','cus_phone1')->where(['cus_id' => Session::get('customer_id')])->first();
			
			$get_payment_details = get_payment();
			return view('Front.checkout')->with(['get_cart_details' => $get_cart_details,'shipping_details' => $shipping_details,'get_payment_details' => $get_payment_details,'paymentgatewaydetails' => $paymentgatewaydetails,'customer_details' => $customer_details,'delivery_fee_set'=>$delivery_fee_set]);
			
		}
		public function save_pre_order(Request $request)
		{	
			$selectedDate = $request->selectedDate;
			$store_id = $request->store_id;
			$selDay = date('l',strtotime($selectedDate));
			$selTime = date('h:ia',strtotime($selectedDate));
			$checkRestExist = DB::table('gr_res_working_hrs')->where('wk_res_id','=',$store_id)->where('wk_date','=',$selDay)->first();
			if(empty($checkRestExist) === false )
			{
				$dbFromTime = $checkRestExist->wk_start_time;
				$dbToTime = $checkRestExist->wk_end_time;
				if(strtotime($dbFromTime) <= strtotime($selTime) && strtotime($dbToTime) >= strtotime($selTime))
				{
					$choice_det = array('cart_pre_order' => date('Y-m-d H:i:s',strtotime($selectedDate)),'cart_updated_at' 	=> date('Y-m-d H:i:s'));
					DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
					//echo 'success';
					$msg = '1`~';
					$msg2= (Lang::has(Session::get('front_lang_file').'.FRONT_PRE_OR_UPDATED')) ? trans(Session::get('front_lang_file').'.FRONT_PRE_OR_UPDATED') : trans($this->FRONT_LANGUAGE.'.FRONT_PRE_OR_UPDATED');
					$msg = $msg.$msg2;
					Session::flash('success',$msg);
				}
				else
				{
					$choice_det = array('cart_pre_order'=>null,'cart_updated_at' 	=> date('Y-m-d H:i:s'));
					DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
					$msg = '0`~';
					$msg .= (Lang::has(Session::get('front_lang_file').'.FRONT_SL_OTHER_TIME')) ? trans(Session::get('front_lang_file').'.FRONT_SL_OTHER_TIME') : trans($this->FRONT_LANGUAGE.'.FRONT_SL_OTHER_TIME');
					
					//Session::flash('val_errors',$msg);
				}
			}
			else
			{
				$choice_det = array('cart_pre_order'=>null,'cart_updated_at' => date('Y-m-d H:i:s'));
				DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
				$msg = '0`~';
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_TIME_NOT_AVAIL')) ? trans(Session::get('front_lang_file').'.FRONT_TIME_NOT_AVAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_TIME_NOT_AVAIL');
				
				//Session::flash('val_errors',$msg);
			}
			echo $msg;
			//exit;
			//echo $selectedDate.'/'.$store_id;
			
			//DB::connection()->enableQueryLog();
			
			//$query = DB::getQueryLog();
			//print_r($query);
		}
		public function save_check_pre_order(Request $request){
			$selectedDateArray = $request->selectedDate;
			$store_idArray = $request->store_id;
			$success_count = 0;
			$store_count = count($store_idArray);
			$error_array = array();
			for($i=0;$i<$store_count;$i++){
				$selectedDate = $selectedDateArray[$i];
				$store_id = $store_idArray[$i];
				$selDay = date('l',strtotime($selectedDate));
				$selTime = date('h:ia',strtotime($selectedDate));
				$res_array = array();
				$checkRestExist = DB::table('gr_res_working_hrs')->where('wk_res_id','=',$store_id)->where('wk_date','=',$selDay)->first();
				if(empty($checkRestExist) === false )
				{
					$dbFromTime = $checkRestExist->wk_start_time;
					$dbToTime = $checkRestExist->wk_end_time;
					if(strtotime($dbFromTime) <= strtotime($selTime) && strtotime($dbToTime) >= strtotime($selTime))
					{
						$choice_det = array('cart_pre_order' => date('Y-m-d H:i:s',strtotime($selectedDate)),'cart_updated_at' 	=> date('Y-m-d H:i:s'));
						DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
						$success_count++;
						$msg2= (Lang::has(Session::get('front_lang_file').'.FRONT_PRE_OR_UPDATED')) ? trans(Session::get('front_lang_file').'.FRONT_PRE_OR_UPDATED') : trans($this->FRONT_LANGUAGE.'.FRONT_PRE_OR_UPDATED');
						$error_array[] = array('store_id'=>$store_id,'status'=>'success','message'=>$msg2);
					}
					else
					{
						$choice_det = array('cart_pre_order'=>null,'cart_updated_at' 	=> date('Y-m-d H:i:s'));
						DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
						$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_SL_OTHER_TIME')) ? trans(Session::get('front_lang_file').'.FRONT_SL_OTHER_TIME') : trans($this->FRONT_LANGUAGE.'.FRONT_SL_OTHER_TIME');
						//Session::flash('val_errors',$msg);
						$error_array[] = array('store_id'=>$store_id,'status'=>'failed','message'=>$msg);
					}
				}
				else
				{
					$choice_det = array('cart_pre_order'=>null,'cart_updated_at' => date('Y-m-d H:i:s'));
					DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_TIME_NOT_AVAIL')) ? trans(Session::get('front_lang_file').'.FRONT_TIME_NOT_AVAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_TIME_NOT_AVAIL');
					$error_array[] = array('store_id'=>$store_id,'status'=>'failed','message'=>$msg);
				}
			}
			return json_encode($error_array);
		}
		public function remove_pre_order(Request $request)
		{	
			$store_id = $request->store_id;
			$choice_det = array('cart_pre_order'=>null,'cart_updated_at' 	=> date('Y-m-d H:i:s'));
			DB::table('gr_cart_save')->where('cart_st_id','=',$store_id)->where('cart_cus_id','=',Session::get('customer_id'))->update($choice_det);
			
		}
		
		public function cart_update_chk_qty(Request $request)
		{
			$pro_id = Input::get('pro_id');
			$shop_id = Input::get('st_id');
			$check_cart = DB::table('gr_cart_save')->selectRaw('SUM(cart_quantity) as added_qty')->where(['cart_st_id' => $shop_id,'cart_item_id' => $pro_id,'cart_cus_id' => Session::get('customer_id')])->pluck('added_qty');
			return $check_cart; 
			
		}
		
		/* update special request  */
		public function update_spl_request(Request $request)
		{
			$cart_id = $request->cart_id;
			$content = $request->request_content;
			$array = ['cart_spl_req' => $content];
			$update = updatevalues('gr_cart_save',$array,['cart_id' => $cart_id]);
			echo "success";
		}
	}	