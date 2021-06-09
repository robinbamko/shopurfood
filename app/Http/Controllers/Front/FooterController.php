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
	
	class FooterController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function cms($id){
			$id = (int)$id;
            $page_title = 'page_title_'.session::get('front_lang_code');
            $des = 'description_'.Session::get('front_lang_code');
			$sel = DB::table('gr_cms')->select($page_title . ' as cp_title',$des . ' as cp_description')
            ->where('id',$id)->get();
			
			
			return view('Front.cms_page',['result'=>$sel]);
		}
		
		public function contact_us()
		{
			return view('Front.contact_us');
		}
		public function contact_us_message()
		{
			$name = mysql_escape_special_chars(Input::get('name'));
			$email = mysql_escape_special_chars(Input::get('email'));
			$phone = mysql_escape_special_chars(Input::get('phone'));
			$message = mysql_escape_special_chars(Input::get('message'));
			
			/*MAIL FUNCTION */
            $send_mail_data = array('name' => $name,
			'phone' => $phone,
			'email' => $email,
			'cusmessage' => $message
			);
			$admin_mail = Config::get('admin_mail');
			Mail::send('email.contactdetails', $send_mail_data, function($message) use($admin_mail)
			{
				$email               = Input::get('email');
				$name                = Input::get('name');
				$subject = (Lang::has(Session::get('front_lang_file').'.FRONT_CONTACT_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CONTACT_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CONTACT_DETAILS');
				$message->to($admin_mail, $name)->subject($subject);
			});
			
			//MAIL TO CUSTOMER
			$admin_mail = Config::get('admin_mail');
			$customer_mail = Input::get('email');
			Mail::send('email.customer_contactdetails', $send_mail_data, function($message) use($admin_mail)
			{
				$email               = $admin_mail;
				$name                = $admin_mail;
				$subject = (Lang::has(Session::get('front_lang_file').'.FRONT_CONTACT_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CONTACT_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CONTACT_DETAILS');
				$message->to( Input::get('email'), Input::get('name'))->subject($subject);
			});
			
			/* EOF MAIL FUNCTION */ 
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CONTACTUS_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CONTACTUS_SUCCESS') : trans($this->FRONT_LANGUAGE.'.FRONT_CONTACTUS_SUCCESS');
			Session::flash('success',$msg);
			return Redirect::to('contact-us');
		}
		
		
		public function test()
		{
			$get_time = \DB::table('gr_general_setting')->select('gs_abandoned_mail','gs_mail_after')->first();
			if(empty($get_time) === false)
			{
				if($get_time->gs_abandoned_mail == '1')
				{   
					$now = date('Y-m-d H:i:s');
					// DB::connection()->enableQueryLog();
					$get_cart = \DB::table('gr_cart_save')->select('cus_email','cus_id')
					//->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
					//->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
					->Join('gr_customer','gr_customer.cus_id','=','gr_cart_save.cart_cus_id')
					->where('gr_customer.cus_status','=','1')
					//->where('gr_product.pro_status','=', '1')
					//->where('gr_store.st_status' ,'=','1')
					//->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
					->whereRaw('gr_cart_save.cart_updated_at < DATE_SUB(NOW(), INTERVAL '.$get_time->gs_mail_after.' HOUR)') 
					->groupBy('cus_email')                
					->get(); 
					//$query = DB::getQueryLog();
					//print_r($get_cart);
					if(count($get_cart) > 0)
					{
						foreach($get_cart as $cart)
						{
							$get_cart = \DB::table('gr_cart_save')->select('gr_product.pro_item_name','gr_product.pro_images','cart_total_amt','cart_type','cart_currency')
							->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
							->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
							->where('gr_product.pro_status','=', '1')
							->where('gr_store.st_status' ,'=','1')
							->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
							->whereRaw('gr_cart_save.cart_updated_at < DATE_SUB(NOW(), INTERVAL '.$get_time->gs_mail_after.' HOUR)') 
							->where('cart_cus_id','=',$cart->cus_id)
							//->groupBy('cart_cus_id')                
							->get(); 
							print_r($get_cart);
						}
					}
					exit;
				}
			}
		}
	}		