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
	
	use Twilio;
	
	use App\Home;
	
	use App\Customer;
	
	class CustomerController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function restaurant_review_submit()
		{
			$rat_val = Input::get('rat_val');
			$rev_comments = Input::get('rev_comments');
			$res_store_id = Input::get('res_store_id');
			$merchant_id = Input::get('res_mer_id');
			$customer_id = Session::get('customer_id');
			$page_id = Input::get('page_id');
			$insertArr = array(
    		'customer_id' => $customer_id,
    		'res_store_id' => $res_store_id,
    		'review_type' => 'restaurant',
            'merchant_id' => $merchant_id,
    		'review_comments' => $rev_comments,
    		'review_rating' => $rat_val,
    		'review_status' => '0',
    		'created_date' => date('Y-m-d H:i')
			);
			
			$insert = insertvalues('gr_review',$insertArr);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.FRONT_REVIEW_ADDED_SUCCESSFULLY');
			Session::flash('success',$msg);
			return Redirect::to('track-order/'.$page_id);
			
		}
		
		public function store_review_submit()
		{
			$rat_val = Input::get('rat_val');
			$rev_comments = Input::get('rev_comments');
			$res_store_id = Input::get('res_store_id');
			$merchant_id = Input::get('res_mer_id');
			$customer_id = Session::get('customer_id');
			$page_id = Input::get('page_id');
			$insertArr = array(
            'customer_id' => $customer_id,
            'res_store_id' => $res_store_id,
            'review_type' => 'store',
			'merchant_id' => $merchant_id,
            'review_comments' => $rev_comments,
            'review_rating' => $rat_val,
            'review_status' => '0',
            'created_date' => date('Y-m-d H:i')
			);
			
			$insert = insertvalues('gr_review',$insertArr);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.FRONT_REVIEW_ADDED_SUCCESSFULLY');
			Session::flash('success',$msg);
			return Redirect::to('track-order/'.$page_id);
		}
		
		public function product_review_submit()
		{   //print_r($_POST); exit;
			$rat_val = Input::get('rat_val');
			$rev_comments = Input::get('rev_comments');
			$product_id = Input::get('product_id');
			$merchant_id = Input::get('res_mer_id');
			$store_id = Input::get('res_store_id');
			$customer_id = Session::get('customer_id');
			$page_id = Input::get('page_id');
			$insertArr = array(
            'customer_id'    => $customer_id,
            'proitem_id'    => $product_id,
            'merchant_id'   => $merchant_id,
            'res_store_id'   => $store_id,
            'review_type'   => 'product',
            'review_comments' => $rev_comments,
            'review_rating' => $rat_val,
            'review_status' => '0',
            'created_date'  => date('Y-m-d H:i')
			);
			//print_r($insertArr); exit;
			$insert = insertvalues('gr_review',$insertArr);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.FRONT_REVIEW_ADDED_SUCCESSFULLY');
			Session::flash('success',$msg);
			return Redirect::to('track-order/'.$page_id);
		}
		
		public function item_review_submit()
		{
			$rat_val = Input::get('rat_val');
			$rev_comments = Input::get('rev_comments');
			$product_id = Input::get('product_id');
			$merchant_id = Input::get('res_mer_id');
			$store_id = Input::get('res_store_id');
			$customer_id = Session::get('customer_id');
			$page_id = Input::get('page_id');
			$insertArr = array(
            'customer_id' => $customer_id,
            'proitem_id' => $product_id,
            'merchant_id' => $merchant_id,
            'res_store_id' => $store_id,
            'review_type' => 'item',
            'review_comments' => $rev_comments,
            'review_rating' => $rat_val,
            'review_status' => '0',
            'created_date' => date('Y-m-d H:i')
			);
			$insert = insertvalues('gr_review',$insertArr);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.FRONT_REVIEW_ADDED_SUCCESSFULLY');
			Session::flash('success',$msg);
			return Redirect::to('track-order/'.$page_id);
		}
		
		public function order_review_submit()
		{
			// print_r($_POST); exit;
			$rat_val = Input::get('rat_val');
			$rev_comments = Input::get('rev_comments');
			$order_id = Input::get('order_id');
			$customer_id = Session::get('customer_id');
			$delivery_id = Input::get('delivery_id');
			$agent_id = Input::get('agent_id');
			$res_store_id = Input::get('ord_rest_id');
			$insertArr = array(
            'customer_id' => $customer_id,
			'res_store_id'=>$res_store_id,
            'order_id' => $order_id,
            'review_type' => 'order',
            'review_comments' => $rev_comments,
            'review_rating' => $rat_val,
            'review_status' => '0',
            'agent_id' => $agent_id,
            'delivery_id' => $delivery_id,
            'created_date' => date('Y-m-d H:i')
			);
			$insert = insertvalues('gr_review',$insertArr);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.FRONT_REVIEW_ADDED_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.FRONT_REVIEW_ADDED_SUCCESSFULLY');
			Session::flash('success',$msg);
			//return Redirect::to('my-orders');
			return Redirect::back();
		}
		
		/*view order review*/ 
		public function view_order_review($id)
		{
			if(Session::has('customer_id'))
			{
				$select = "review_comments,review_rating";
				$details = get_details('gr_review',['comment_id' => base64_decode($id)],$select);
				$page_title = (Lang::has(Session::get('front_lang_file').'.ADMIN_VIEW_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_VIEW_REVIEW') : trans($this->FRONT_LANGUAGE.'.ADMIN_VIEW_REVIEW');
				$id = base64_decode($id); 
				
				return view('Front.view_order_review')->with('pagetitle',$page_title)->with(['detail'=>$details,'id' => $id]);
			}
			else
			{
				return Redirect::to('/');
			}
		}
		/*End view order review*/ 
		
		/** refered wallet amount **/
		public function my_wallet(Request $request)
		{
			$limit = $request->input('length');
			$start = $request->input('start');
			$details2 = Home::get_refered_details(Session::get('customer_id'));// EXIT;
			//echo count($details2); print_r($details2);  exit;
			//$admin_added = Home::get_admin_assigned(Session::get('customer_id'));
			if($request->ajax())
			{  
				$columns = array(
				0 => 'referre_email',
				1 => 'referre_email',
				2 => 'referre_email',
				3 => 'referre_email',
				
				);
				$order = $columns[$request->input('order.0.column')];
				$details1 = Home::get_refered_details(Session::get('customer_id'),$start,$limit,$order);
				$data = array();
				//$i = 1;
				$i = ($start+1);
				foreach($details1 as $det)
				{
					$nestedData['sno']= $i;
					$nestedData['name']  = $det->referre_email;
					$nestedData['offer']  = ($det->re_offer_percent != '') ? $det->re_offer_percent.' %' : '-';
					$nestedData['amount']    = $det->re_offer_amt;
					$i++;
					$data[] = $nestedData;
					
					
				}
				$json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => count($details2),
                "recordsFiltered" => count($details2),
                "data"            => $data
				);
				
				echo json_encode($json_data); exit;
			}
			return view('Front.my_wallet')->with(['refered_details' => $details2]);
		}
		
		public function used_wallet(Request $request)
		{
			$limit = $request->input('length');
			$start = $request->input('start');
			
			if($request->ajax())
			{  
				$details = DB::table('gr_order')->select('ord_currency','ord_wallet','ord_date','ord_transaction_id')
							->where('ord_cus_id','=',Session::get('customer_id'))
							->where('ord_wallet','!=','0')
							->groupBy('ord_transaction_id')
							->get();
				$columns = array(
				0 => 'ord_date',
				1 => 'ord_date',
				2 => 'ord_date',
				3 => 'ord_date',
				
				);
				$dir = $request->input('order.0.dir');
				$order = $columns[$request->input('order.0.column')];
				$details3 = Home::wallet_details(Session::get('customer_id'),$start,$limit,$order,$dir);
				$data = array();
				$i = 1;
				foreach($details3 as $det)
				{
					$nestedData['sno']= $i;
					$nestedData['id']  = $det->ord_transaction_id;
					$nestedData['amount']  = $det->ord_currency.' '.$det->ord_wallet;
					$nestedData['date']    = $det->ord_date;
					$i++;
					$data[] = $nestedData;
					
					
				}
				$json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => count($details),
                "recordsFiltered" => count($details),
                "data"            => $data
				);
				
				echo json_encode($json_data); exit;
			}
			
		}
		
		/** customer profile mobile OTP **/ 
		public function check_mobile_num_is_new()
		{
			$prof_cus_phone = Input::get('cus_phone1');
			$cus_id = Session::get('customer_id');
			$otp = mt_rand(100000, 999999);
			$check_num_is_new = DB::table('gr_customer')->where('cus_id','=',$cus_id)->where('cus_phone1','=',$prof_cus_phone)->first();
			if(empty($check_num_is_new) === true)
			{
				try{
					Twilio::message($prof_cus_phone, $otp);
					$jsonArr = array('msg'=>'New','otp'=>$otp);
					echo json_encode($jsonArr);
					
				}
				catch (\Exception $e)
				{       
					
					$jsonArr = array('msg'=>$e->getMessage());
					echo json_encode($jsonArr);
				}
				
			}
			else
			{
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_SAME_MB_NO')) ? trans(Session::get('front_lang_file').'.FRONT_SAME_MB_NO') : trans($this->FRONT_LANGUAGE.'.FRONT_SAME_MB_NO');
				$jsonArr = array('msg'=>$msg);
				echo json_encode($jsonArr);
			}
		}
		
		/* my review */
		/* my review */
		public function my_review()
		{
			$pro_reviews  = Customer::get_my_review('product');
			$item_reviews = Customer::get_my_review('item');
			$store_reviews = Customer::get_my_review('store');
			$res_reviews  = Customer::get_my_review('restaurant');
			$or_reviews  = Customer::get_my_review('order');
			
			return view('Front.my_review')->with(['pro_reviews' => $pro_reviews,'item_reviews' => $item_reviews,'store_reviews' =>$store_reviews,'res_reviews' =>$res_reviews,'or_reviews' =>$or_reviews]);
		}
	}
