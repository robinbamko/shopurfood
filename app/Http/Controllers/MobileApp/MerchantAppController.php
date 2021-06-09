<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\MobileApp;
	use App\Http\Controllers\Controller;
	use App\Http\Models;
	use App\MobileModel;
	use App\Merchant;
	use App\Delivery_person;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Mail;
	use Illuminate\Http\Request;
	use Response;
	use Lang;
	use App;
	use DB;
	use Illuminate\Validation\Rule;
	use Validator;
	use View;
	use JWTAuth;
	use Config;
	use Image;
	use Tymon\JWTAuth\Exceptions\JWTException;
	use Twilio;
	use Session;
	use File;
	class MerchantAppController extends Controller
	{
		private $empty_data;
		public function __construct()
		{
			parent::__construct();
			$this->empty_data = array();
			/*LANGUAGE SETTINGS */
			$this->default_mob_lang_code  = 'en';
			$this->admin_default_lang = DB::table('gr_language')->select('lang_code')->where('default_lang','1')->where('status','1')->first()->lang_code;
			/* EOF LANGUAGE SETTINGS */
			/*GENERAL DETAILS*/
			$this->general_setting = DB::table('gr_general_setting')->first();
			if(!empty($this->general_setting))
			{
				View::share("SITENAME",$this->general_setting->gs_sitename);
				View::share("FOOTERNAME",$this->general_setting->footer_text);
				$this->site_name = $this->general_setting->gs_sitename;	 
				$this->admin_mail = $this->general_setting->gs_email;	 
				$this->agent_module = $this->general_setting->gs_agent_module;	 
			}
			else
			{
				View::share("SITENAME","Shopurfood");
				View::share("FOOTERNAME","Shopurfood");
				$this->site_name = "Shopurfood";
				$this->admin_mail = "admin@gmail.com";
				$this->agent_module = 0;	 
			}
			/*LOGO DETAILS */
			$logo_settings_details = MobileModel::get_logo_settings_details();
			
			$path = url('').'/public/images/noimage/default_image_logo.jpg';
			if(count($logo_settings_details) > 0)
			{
				foreach($logo_settings_details as $logo_set_val){ }
				
				if($logo_set_val->admin_logo != '')
				{
					$filename = public_path('images/logo/').$logo_set_val->admin_logo;
					if(file_exists($filename))
					{
						$path = url('').'/public/images/logo/'.$logo_set_val->admin_logo;
					}
				}
				
			}		
			$default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code','co_cursymbol')->where(['default_counrty' => '1','co_status' => '1'])->first();
            if(empty($default) === false)
            {
				Session::put('default_currency_code', $default->co_curcode);
				Session::put('default_currency_symbol', $default->co_cursymbol);
			}
            else
            {
				Session::put('default_currency_code', $default->co_curcode);
				Session::put('default_currency_symbol', $default->co_cursymbol);
			}
			
			View::share("LOGOPATH",$path);
			$admin_det = get_admin_details();  /* get admin details*/
			$this->admin_id  = $admin_det->id;
		}
		
		/* forget password */
		public function merchant_forgot_password(Request $request){
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$validator = Validator::make($request->all(), 
			['mer_email' => ['required','string','email',
			Rule::exists('gr_merchant')->where(function ($query) {
				$query->where('mer_status','=','1');
			})]],
			['mer_email.required'	=>	MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.'),
			'mer_email.email'		=>	MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!'),
			'mer_email.exists'		=> MobileModel::get_lang_text($lang,'API_MAIL_NT_EXIST','Email not exists')]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$LoginRes = MobileModel::check_id_exist($request->mer_email,'gr_merchant','mer_email','mer_status','id');
			//print_r($LoginRes); exit;
			if(empty($LoginRes) === false){
				
				$agent_id 	= $LoginRes->id;
				$passwordIs = rand_password();
				$agent_password = array('mer_password' => md5($passwordIs),'mer_decrypt_password' => $passwordIs);
				DB::table('gr_merchant')->where('id', '=', $agent_id)->update($agent_password);
				
				/*MAIL FUNCTION */
				$data = array('ForgotEmail' => $request->mer_email,
				'ForgotPassword' => $passwordIs,
				'lang'			=>$lang.'_mob_lang',
				'onlyLang'		=>$lang);
				//echo $data['ForgotEmail']; exit;
				Mail::send('email.mobile_agent_forgetpwd', $data, function($message) use($data)
				{
					$email               = $data['ForgotEmail'];
					$name                = $data['ForgotEmail'];
					$subject = MobileModel::get_lang_text($data['onlyLang'],'API_FORGOT_PASSWORD_DETAILS','Forgot password details!');
					$message->to($email, $name)->subject($subject);
				});
				$msge=MobileModel::get_lang_text($lang,'API_FORGOT_PASSWORD_SENT','Password sent to your mail!');
				$encode = [ 'code' => 200,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				
				/* EOF MAIL FUNCTION */ 
			}
			else{
				$msge=MobileModel::get_lang_text($lang,'API_INVALID_EMAIL','Invalid Email ID!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* home page */
		public function home(Request $request)
		{
			$lang = $request->lang;
			/*$cus_name = JWTAuth::user()->mer_fname;*/
			$greet = MobileModel::get_lang_text($lang,'API_WELCOME_TO','Welcome To ');
			$get_details = MobileModel::get_logo_settings_details();
			$andr_splash = $andr_logo = $ios_splash = $ios_login_logo = $ios_signup_logo = $ios_frpw_logo = '';
			if(count($get_details) > 0)
			{
				foreach($get_details as $details)
				{	
					if($details->andr_splash_img_vendor != '')
					{
						$filename = public_path('images/logo/').$details->andr_splash_img_vendor;
						if(file_exists($filename))
						{
							$andr_splash = url('').'/public/images/logo/'.$details->andr_splash_img_vendor;
						}
					}
					if($details->andr_logo != '')
					{
						$filename = public_path('images/logo/').$details->andr_logo;
						if(file_exists($filename))
						{
							$andr_logo = url('').'/public/images/logo/'.$details->andr_logo;
						}
					}
					if($details->ios_splash_img_vendor != '')
					{
						$filename = public_path('images/logo/').$details->ios_splash_img_vendor;
						if(file_exists($filename))
						{
							$ios_splash = url('').'/public/images/logo/'.$details->ios_splash_img_vendor;
						}
					}
					if($details->ios_login_sc_logo != '')
					{
						$filename = public_path('images/logo/').$details->ios_login_sc_logo;
						if(file_exists($filename))
						{
							$ios_login_logo = url('').'/public/images/logo/'.$details->ios_login_sc_logo;
						}
					}
					if($details->ios_register_sc_logo != '')
					{
						$filename = public_path('images/logo/').$details->ios_register_sc_logo;
						if(file_exists($filename))
						{
							$ios_signup_logo = url('').'/public/images/logo/'.$details->ios_register_sc_logo;
						}
					}
					if($details->ios_forget_pw_logo != '')
					{
						$filename = public_path('images/logo/').$details->ios_forget_pw_logo;
						if(file_exists($filename))
						{
							$ios_frpw_logo = url('').'/public/images/logo/'.$details->ios_forget_pw_logo;
						}
					}
					
					
				}
			}
			$data  = ['splash_screen_android' 		=> $andr_splash,
			'splash_screen_ios' 		=> $ios_splash,
			'logo_android'		 		=> $andr_logo,
			'login_logo_ios' 			=> $ios_login_logo,
			'signup_logo_ios' 			=> $ios_signup_logo,
			'forgot_password_logo_ios' 	=> $ios_frpw_logo,
			
			];
			$encode = [ 'code'		 	=> 200,
			'message'  		=> $greet.$this->site_name,
			'data' 			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* dashboard */
		public function dashboard(Request $request)
		{
			$lang = $request->lang;
			$mer_id = JWTAuth::user()->id;
			$store_details = Merchant::store_details($mer_id,$lang,$this->admin_default_lang);
			$st_name = "";
			if(empty($store_details) === false)
			{
				$st_name = $store_details->store_name;
			}
			$new_order 		= Merchant::get_count2('gr_order',['ord_merchant_id' => $mer_id,'ord_status' => '1','ord_cancel_status' => 0],'ord_id','ord_transaction_id');
			$process_order  = DB::table('gr_order')->select('ord_id')->where('ord_merchant_id','=',$mer_id)->whereIn('ord_status',['2','3','4','5','6','7'])->groupBy('ord_transaction_id')->get()->count();
			$delivered_count = Merchant::get_count2('gr_order',['ord_merchant_id' => $mer_id,'ord_status' => '8','ord_cancel_status' => '0'],'ord_id','ord_transaction_id');
			$total_order	 = Merchant::get_count2('gr_order',['ord_merchant_id' => $mer_id],'ord_id','ord_transaction_id');
			$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
			$encode = [ 'code'		 	=> 200,
						'message'  		=> $msge,
						'data' 			=> ['total_orders' => $total_order,
											'new_order'		=> $new_order,
											'process_order'	=> $process_order,
											'delivered_order' => $delivered_count,
											'store_name'	 => $st_name
											]
						];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* order management */
		public function order_management(Request $request)
		{
			$lang = $request->lang;
			$page = ($request->page_no == '') ? '1' : $request->page_no ;
			$from_date	= ($request->from_date == '') ? '' : date('Y-m-d H:i:s',strtotime($request->from_date));
			$to_date	= ($request->to_date == '') ? '' : date('Y-m-d H:i:s',strtotime($request->to_date));
			$status = $request->status; 
			$cancel_status_code = $request->cancel_status; 
			$search = $request->search_text;
			$mer_id = JWTAuth::user()->id; 
			$route = \Request::segment(3);
			$where = array(); 
			$cancel_status = array(); 
			$orderby = '';
			if($route == "new-orders")
			{
				$where = ['1'];
				$orderby    = 'ord_date';
				$cancel_status = ['0'];
			}
			elseif($route == "preparing-orders")
			{
				$where = ['4','5','6','7'];
				$orderby    = 'ord_accepted_on';
				$cancel_status = ['0'];
			}
			elseif($route == "processing-orders")
			{
				if($status != '' && $status != '3' && $cancel_status_code != '1')
				{
					$where = [$status];
					$cancel_status = ['0'];
				}
				elseif($status == '3') //merchant reject orders
				{
					$where = [$status];
					$cancel_status = ['1'];
				}
				elseif($status == '1' && $cancel_status_code == '1') //user cancel
				{
					$where = ['1'];
					$orderby  = 'ord_cancel_date';
					$cancel_status = ['1'];
				}
				else
				{
					$where = ['2','3','4','5','6','7'];	
					$cancel_status = ['0','1'];
				}
				
				$orderby    = 'ord_accepted_on';
			}
			elseif($route == "cancelled-orders") //user cancel
			{
				$where = ['1'];
				$orderby  = 'ord_cancel_date';
				$cancel_status = ['1'];
			}
			elseif($route == "delivered-orders")
			{
				$where = ['8'];
				$orderby  = 'ord_delivered_on';
				$cancel_status = ['0'];
				
			}
			//print_r($cancel_status); exit;
			$group_res = Merchant::get_order_details($mer_id,$page,$from_date,$to_date,$search,$where,$orderby,$cancel_status);
			//print_r($group_res); exit;
			$order_details = array();
			if(count($group_res) > 0)
			{
				$text = '';
				foreach($group_res as $res)
				{	
					if($res->ord_status =='1' && $res->ord_cancel_status == 1)
					{
						$text = MobileModel::get_lang_text($lang,'API_ORDER_CANCELLED','Order Cancelled!');
					}
					else
					{
						switch($res->ord_status)
						{	
						case 1:
						$text = MobileModel::get_lang_text($lang,'API_OR_ST1','New Order');
						break;
						case 2:
						$text = MobileModel::get_lang_text($lang,'API_OR_ST2','Accepted');
						break;
						case 3:
						$text = MobileModel::get_lang_text($lang,'API_REJECT','Rejected');
						break;
						case 4:
						$text = MobileModel::get_lang_text($lang,'API_TO_DELIVER','Preparing for deliver');
						break;
						case 5:
						$text = MobileModel::get_lang_text($lang,'API_DISPATCH','Dispatched');
						break;
						case 6:
						$text = MobileModel::get_lang_text($lang,'API_START','Started');
						break;
						case 7:
						$text = MobileModel::get_lang_text($lang,'API_ARRIVE','Arrived');
						break;
						case 8:
						$text = MobileModel::get_lang_text($lang,'API_DELIVER','Delivered');
						break;
						case 9:
						$text = MobileModel::get_lang_text($lang,'API_FAIL','Delivery Failed');
						break;
						default:
						$text = MobileModel::get_lang_text($lang,'API_OR_ST1','New Order');
						}
					}
						
					$pending_items = Merchant::pending_item_count($mer_id,$res->ord_transaction_id);
					//echo $pending_items; exit;
					$del_assign_status = ($res->ord_delivery_memid != '' && $res->ord_delboy_act_status =='1') ? TRUE : FALSE;
					$img_arr = explode(',',$res->item_image);
					$item_img_arr = array();
					if(!empty($img_arr))
					{
						foreach($img_arr as $img)
						{
							$item_img_arr[] = MobileModel::get_image_item($img);
						}
					}
					$old_order = FALSE;
					/*if($res->ord_accepted_on != '')
					{
						$now = date('Y-m-d H:i:s');
						if($now > date('Y-m-d H:i:s',strtotime($res->ord_accepted_on. ' +3 day')))
						{
							$old_order = TRUE;	
						}
						
					}*/
					/* To show delivery person arrival status */
					if($res->ord_status >= '4' && ($res->ord_delivery_memid == '' || $res->ord_delivery_memid == 0))
					{
						$text = MobileModel::get_lang_text($lang,'API_NT_ASSIGN','Not Yet Assigned');
					}	
					elseif($res->ord_status >= '4' && ($res->ord_delivery_memid != '' && $res->ord_delboy_act_status != '1'))		
					{
						$text = MobileModel::get_lang_text($lang,'API_ASSIIGN','Assigned');
					}		
					elseif ($res->ord_status >= '5' && ($res->ord_delivery_memid != '' && $res->ord_delboy_act_status == '1')) {
						$ch_text = MobileModel::get_lang_text($lang,'API_ON_THE_WAY',':name is on the way');
						$text = str_replace(':name', ucfirst($res->deliver_name), $ch_text);
					}
					$avatar = url('public/images/noimage/default_user_image.jpg');
					if($res->deliver_profile_image != '')
					{
						$filename = public_path('images/delivery_person/').$res->deliver_profile_image;
						
						if(file_exists($filename)){
							$avatar = url('public/images/delivery_person/'.$res->deliver_profile_image );
						}
					}
					$order_details[] = ['customer_name' 	=> $res->cus_fname,
										'customer_location' => $res->ord_shipping_address,
										'order_id'			=> $res->ord_id,
										'order_transaction_id' => $res->ord_transaction_id,
										'order_date'		=> $res->ord_date,
										'currency_code'		=> $res->ord_currency,
										'order_amount'		=> $res->revenue,
										'pay_type'			=> $res->ord_pay_type,
										'delivered_date'	=> ($res->ord_delivered_on != '') ? $res->ord_delivered_on : '',
										'pre_order_date'	=> ($res->ord_pre_order_date != '') ? $res->ord_pre_order_date : '',
										'order_status'		=> $text,
										'pending_items'		=> $pending_items,
										'item_images'		=> $item_img_arr,
										'assign_status'		=> $del_assign_status,
										'deliver_name'		=> ($res->deliver_name != '') ? $res->deliver_name : '',
										'deliver_phone'		=> ($res->deliver_phone1 != '') ? $res->deliver_phone1 : '',
										'deliver_image'		=> $avatar,
										'old_order_status'	=> 	$old_order
										];
				}
				$msge = MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$encode = [ 'code' => 200,'message' => $msge,'data' => ['order_details' =>$order_details]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* invoice detail */
		public function invoice_detail(Request $request)
		{
			$lang 			= $request->lang;
			$order_id 		= $request->order_id;
			$details	 	= JWTAuth::user();
			$mer_id 		= $details->id;
			
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');
			$validator = Validator::make($request->all(),['order_id'	=> ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function ($query) use($mer_id) {
				$query->where('ord_merchant_id','=',$mer_id);
			})]	
			],[ 'order_id.required'	=> $orderId_req_err_msg,
			'order_id.exists'	=> $orderId_valid_err_msg,
			] 
			);
            if($validator->fails())
            {
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			/* get store details */
			$st_details = get_details('gr_order',['ord_merchant_id' => $mer_id,'ord_transaction_id' => $order_id],'ord_rest_id');
			$Invoice_Order = Merchant::get_invoice($order_id,$st_details->ord_rest_id,$lang);
			//print_r($Invoice_Order); 
			if(count($Invoice_Order)>0)
			{	
				$Order = $Invoice_Order[0];
				
				
				$order_date = $Order->ord_date;
				$self_pickup = ($Order->ord_self_pickup == 1) ? 'Yes' : 'No';
				$paytype = $Order->ord_pay_type;
				if($Order->ord_shipping_cus_name!='' && $Order->ord_shipping_address!=''  && $Order->ord_shipping_mobile!='')
				{
					$OrderCustomerName 		= $Order->ord_shipping_cus_name;
					$OrderCustomerAddress 	= $Order->ord_shipping_address;
					$OrderCustomerAddress1 	= $Order->ord_shipping_address1;
					$OrderCustomerMobile 	= $Order->ord_shipping_mobile;
					$OrderCustomerEmail 	= $Order->order_ship_mail;
				}
				else
				{
					$OrderCustomerName 		= $Order->cus_fname.' '.$Order->cus_lname;
					$OrderCustomerAddress 	= $Order->cus_address;
					$OrderCustomerAddress1 	= '';
					$OrderCustomerMobile 	= $Order->cus_phone1;
					$OrderCustomerEmail 	= $Order->cus_email;
				}
				$customerDetailArray = array('customeName'		=>$OrderCustomerName,
				'customerAddress1'	=>$OrderCustomerAddress,
				'customerAddress2'	=>$OrderCustomerAddress1,
				'customerMobile'	=>$OrderCustomerMobile,
				'customerEmail'	=>$OrderCustomerEmail);
				$grant_total = $Order->order_amount;
				$order_detailArray =array();
				$sub_total=$tax_total=0;
				$currency = '';
				$cal = Delivery_person::get_receivable_amount($order_id,$Order->order_amount);
				$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
				foreach($Invoice_Order as $Order_sub)
				{
					
					$ordersArray = array();
					
					$sub_total+= $Order_sub->ord_sub_total;
					$tax_total+= $Order_sub->ord_tax_amt;
					$cancelled_reason = '';
					$ordersArray['ord_id'] 			= $Order_sub->ord_id;
					$ordersArray['ord_product_id'] 	= $Order_sub->ord_pro_id;
					$ordersArray['store_name'] 		= $Order_sub->st_store_name;
					$ordersArray['item_name'] 		= $Order_sub->pro_item_name;
					if($Order_sub->ord_spl_req != ''){
						$ordersArray['specialRequest'] = $Order_sub->ord_spl_req;
					}
					else {
						$ordersArray['specialRequest'] = '';
					}
					
					$ordersArray['ord_quantity'] 	= $Order_sub->ord_quantity;
					$ordersArray['ord_item_amount'] 	= number_format(($Order_sub->ord_unit_price * $Order_sub->ord_quantity),2);
					$ordersArray['ord_currency'] 	= $Order_sub->ord_currency;
					$ordersArray['sub_total'] 		= $Order_sub->ord_sub_total;
					$ordersArray['tax'] 		= $Order_sub->ord_tax_amt;
					$ordersArray['had_choice'] 		= $Order_sub->ord_had_choices;
					$currency 						= $Order_sub->ord_currency;
					if($Order_sub->ord_pre_order_date != '')
					{
						$ordersArray['pre_order_date'] = $Order_sub->ord_pre_order_date;
					}
					else
					{
						$ordersArray['pre_order_date'] = '-';
					}
					if($Order_sub->pro_type=='1'){
						$ordersArray['pdt_image']	= MobileModel::get_image_product($Order_sub->pro_image);//product
					}
					else { 
						$ordersArray['pdt_image']	= MobileModel::get_image_item($Order_sub->pro_image);
					}
					$text = '';
					if($Order_sub->ord_status == '1' && $Order_sub->ord_cancel_status == 1)
					{
						$text = MobileModel::get_lang_text($lang,'API_ORDER_CANCELLED','Order Cancelled!');
					}
					else
					{
						switch($Order_sub->ord_status)
						{	
							case 1:
							$text = MobileModel::get_lang_text($lang,'API_OR_ST1','New Order');
							break;
							case 2:
							$text = MobileModel::get_lang_text($lang,'API_OR_ST2','Accepted');
							break;
							case 3:
							$text = MobileModel::get_lang_text($lang,'API_REJECT','Rejected');
							break;
							case 4:
							$text = MobileModel::get_lang_text($lang,'API_TO_DELIVER','Preparing for deliver');
							break;
							case 5:
							$text = MobileModel::get_lang_text($lang,'API_DISPATCH','Dispatched');
							break;
							case 6:
							$text = MobileModel::get_lang_text($lang,'API_START','Started');
							break;
							case 7:
							$text = MobileModel::get_lang_text($lang,'API_ARRIVE','Arrived');
							break;
							case 8:
							$text = MobileModel::get_lang_text($lang,'API_DELIVER','Delivered');
							break;
							case 9:
							$text = MobileModel::get_lang_text($lang,'API_FAIL','Delivery Failed');
							break;
							default:
							$text = MobileModel::get_lang_text($lang,'API_OR_ST1','New Order');
						}
					}
					$ordersArray['order_status'] = $text;
					$ordersArray['order_status_code'] = $Order_sub->ord_status;
					if($Order_sub->ord_had_choices=="Yes"){
						$splitted_choice=json_decode($Order_sub->ord_choices,true);
						if(!empty($splitted_choice))
						{
							foreach($splitted_choice as $choice)
							{
								if(!isset($choices[$choice['choice_id']]))
								{
									$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
									$ordersArray['choice'][]=array('choicename'=>$choices_name,'choice_amount'=>number_format($choice['choice_price'],2));
									
									
								}
							}
						}
					}
					array_push($order_detailArray,$ordersArray);
				}	
				//$ordersArray['sub_total'] = $sub_total;
				$msge = MobileModel::get_lang_text($lang,'API_AVAIL_ORDERS','Orders available!');
				$data = ['order_id'				=> $order_id,
				'order_date'			=> $order_date,
				'self_pickup'			=> $self_pickup,
				'customerDetailArray'	=> $customerDetailArray,
				'order_detailArray' 	=> $order_detailArray,
				'currency'				=> $currency,
				'grand_sub_total'		=> number_format($sub_total,2),
				'grand_tax'				=> number_format($tax_total,2),
				'grand_total'			=> number_format($grant_total,2),
				'delivery_fee'			=> number_format($explode[0],2)
				];
				$encode = ['code' => 200,
				'message' => $msge,
				'data' => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msg = MobileModel::get_lang_text($lang,'API_NO_ORDERS','No Orders available!');
				$encode = [ 'code' => 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			
		}
		
		/* Accept / reject order */
		public function accept_item(Request $request)
		{
			$lang 		= $request->lang;
			$order_id 	= $request->ord_id;
			$status 	= $request->status;
			$reason 	= $request->reject_reason;
			$mer_details = JWTAuth::user();
			$mer_id 	= $mer_details->id; 
			$route 		= \Request::segment(3);
			$in = ['2','3'];
			if($route == "change-status")
			{
				$in = ['4','8'];
			}
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_OR_ID_NT_EXIST','Order id does not exist');
			$status_req_err_msg	= MobileModel::get_lang_text($lang,'API_EN_OR_STATUS','API_EN_OR_STATUS');
			$status_va_err_msg	= MobileModel::get_lang_text($lang,'API_EN_VA_OR_STATUS','Enter valid order status');
			$reason_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_REJECT_REASON','Please enter reject reason!');
			$validator = Validator::make($request->all(),['ord_id'	=> ['required',
																		Rule::exists('gr_order')->where(function ($query) use($mer_id) {
																			$query->where('ord_merchant_id','=',$mer_id);
																		})],
														'status'		=> ['required',Rule::in($in)],
														'reject_reason' => 'required_if:status,3'	
														],
														[ 'ord_id.required'	=> $orderId_req_err_msg,
														'ord_id.exists'		=> $orderId_valid_err_msg,
														'status.required'	=> $status_req_err_msg,
														'status.in'			=> $status_va_err_msg,
														'reject_reason.required_if'		=> $reason_req_err_msg,
														] 
										);
			if($validator->fails())
            {
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			
			/* update status */
			if($status == '2')
			{
				updatevalues('gr_order',['ord_status' => $status,'ord_accepted_on' => date('Y-m-d H:i:s')],['ord_id' =>$order_id]);
				$message = MobileModel::get_lang_text($lang,'API_ST_CHANGED','Status changed successfully');
				$encode = [ 'code' => 200,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			elseif($status == '3')
			{	
				$pro_name = ($lang == $this->admin_default_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
				$st_name = ($lang == $this->admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
				$ord_details = DB::table('gr_order')->select('ord_grant_total','ord_pay_type','ord_transaction_id','ord_admin_amt','order_ship_mail','ord_cus_id','ord_merchant_id','ord_refund_status','ord_quantity','ord_pro_id','ord_delivery_fee','gr_product.'.$pro_name.' as item_name','ord_choices','ord_had_choices','gr_store.'.$st_name.' as store_name','gr_product.pro_images','st_type','ord_currency','ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','order_ship_mail','ord_reject_reason','ord_self_pickup','gr_customer.cus_andr_fcm_id','gr_customer.cus_ios_fcm_id')
				->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
				->leftJoin('gr_product','gr_product.pro_id','=','gr_order.ord_pro_id')
				->leftJoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
				->where('ord_id','=',$order_id)
				->first();
				if(empty($ord_details) === false)
				{	
					$ord_transaction_id = $ord_details->ord_transaction_id;
					
					$get_total_order_count = get_total_order_count($ord_transaction_id);
					$get_total_cancelled_count = get_total_cancelled_count($ord_transaction_id)+1;
					$cancel_amt = 0;
					if($get_total_cancelled_count==$get_total_order_count)
					{
						$add_delfee_status=1;
						$cancel_amt = $ord_details->ord_grant_total + $ord_details->ord_delivery_fee;
						$del_fee_overall = 0;
						$mer_amount = $ord_details->ord_grant_total-$ord_details->ord_admin_amt+$ord_details->ord_delivery_fee;
					}
					else
					{
						$add_delfee_status=0;
						$cancel_amt = $ord_details->ord_grant_total;
						$del_fee_overall = $ord_details->ord_delivery_fee;
						$mer_amount = $ord_details->ord_grant_total-$ord_details->ord_admin_amt;
					}
					/** while user cancel the item, admin detect commission from merchant amount **/
					/* if refund status is no, no need to add cancellation amount */
					
					if($ord_details->ord_refund_status == 'Yes')
					{
						DB::table('gr_order')->where('ord_id', $order_id)
																		->update(['add_delfee_status'=>$add_delfee_status,
																		'ord_status' 		=> '3',
																		'ord_cancel_status' => '1',
																		'ord_cancel_reason'	=>$reason,
																		'ord_reject_reason'	=>$reason,
																		'ord_cancel_date'	=>date('Y-m-d H:i:s'),
																		'ord_cancel_amt' 	=> $ord_details->ord_grant_total,
																		'ord_rejected_on' => date('Y-m-d H:i:s')]);
						/** update merchant amount **/
						
						
						DB::table('gr_merchant_overallorder')->where('or_mer_id', $ord_details->ord_merchant_id)
						->update(['or_mer_amt' => DB::raw('or_mer_amt+'.($mer_amount)),
						'or_cancel_amt' => DB::raw('or_cancel_amt+'.($cancel_amt))]);
						
						/*--- update product quantity ---*/
						 updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_details->ord_quantity)],['pro_id' => $ord_details->ord_pro_id]);
					}
					else
					{
						DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_status' => '3','ord_cancel_status' => '1','ord_cancel_reason'=>$reason,'ord_reject_reason'=>$reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_rejected_on' => date('Y-m-d H:i:s')]);
						/*--- update product quantity ---*/
						updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_details->ord_quantity)],['pro_id' => $ord_details->ord_pro_id]);
					}
					$customerMail = $ord_details->order_ship_mail;
					$customerId   = $ord_details->ord_cus_id;
					$merchantId   = $ord_details->ord_merchant_id;
					$merchantName = ucfirst($mer_details->mer_fname).' '.$mer_details->mer_lname;
					$admin_det 	  = get_admin_details();
					$admin_id  	  = $admin_det->id;
					/* ---------- SEND NOTIFICATION TO CUSTOMER ----------------*/
					
					$got_message = MobileModel::get_lang_text($lang,'API_REJECT_NOTIFICATION','The items (:item_name) in order (:transaction_id) has been rejected by :merchant_name');
					$message = str_replace(':transaction_id', $ord_transaction_id, $got_message);
					$message = str_replace(':item_name',ucfirst($ord_details->item_name), $message);
					$message = str_replace(':merchant_name', ucfirst($merchantName), $message);
					
					$message_link = 'order-details/'.base64_encode($ord_transaction_id);
					push_notification($merchantId,$customerId,'gr_merchant','gr_customer',$message,$ord_transaction_id,$message_link); 
					/* send notification to customer mobile */
					if($ord_details->cus_andr_fcm_id != '' || $ord_details->cus_ios_fcm_id != '')
					{
						if($ord_details->cus_andr_fcm_id !='')
						{
							$parse_fcm=json_decode($ord_details->cus_andr_fcm_id,true);
							$reg_id = array();
							if(count($parse_fcm) > 0 )
							{
								foreach($parse_fcm as $parsed)
								{ 
									array_push($reg_id,$parsed['fcm_id']);						
								}
							}
							$json_data = ["registration_ids" => $reg_id,
											"notification" => ["body" => $message,"title" => "Order Rejected Notification"]
										];
							$notify =sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_CUS);
								
						}
						if($ord_details->cus_ios_fcm_id !='')
						{
							$parse_fcm=json_decode($ord_details->cus_ios_fcm_id,true);
							$reg_id = array();
							if(count($parse_fcm) > 0 )
							{
								foreach($parse_fcm as $parsed)
								{ 
									array_push($reg_id,$parsed['fcm_id']);						
								}
							}
							$json_data = ["registration_ids" => $reg_id,
											"notification" => ["body" => $message,
																"title" => "Order Rejected Notification",
																"sound"	=> "default"]
										];
							$notify =sendPushNotification($json_data,IOS_FIREBASE_API_KEY_CUS);
								
						}
					}
					/* send notification to customer mobile ends */
					/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
					$message_link = 'admin-track-order/'.base64_encode($ord_transaction_id);
					push_notification($merchantId,$admin_id,'gr_merchant','gr_admin',$message,$ord_transaction_id,$message_link);
					
					//1) MAIL TO CUSTOMER
					$send_mail_data = array('order_details'	=> $ord_details,
											'transaction_id'=>$ord_transaction_id,
											'lang'			=> $lang.'_mob_lang',
											'reason'		=> $reason);
					$msg = MobileModel::get_lang_text($lang,'API_REJECT_OR','Your Order is Rejected');
					Mail::send('email.mobile_reject_mail_customer', $send_mail_data, function($message) use($customerMail,$msg)
					{
						$message->to($customerMail)->subject($msg);
					});

					//2) MAIL TO ADMIN
					$adminMail = $this->admin_mail;
					$msg1 = MobileModel::get_lang_text($lang,'API_REJECT_OR_DETAIL','Reject Order Details');
					Mail::send('email.mobile_reject_mail_admin', $send_mail_data, function($message) use($adminMail,$msg1)
					{
						$message->to($adminMail)->subject($msg1);
					});

					//3) MAIL TO MERCHANT
					$mer_mail = $mer_details->mer_email; 
					Mail::send('email.mobile_reject_mail_merchant', $send_mail_data, function($message) use($mer_mail,$msg1)
					{
						$message->to($mer_mail)->subject($msg1);
					});
					
					/** eof mail function **/
				}
				$message = MobileModel::get_lang_text($lang,'API_ST_CHANGED','Status changed successfully');
				$encode = [ 'code' => 200,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				
			}
			elseif($status == '4') //preparing to delivery when order has no self pickup
			{	
				DB::table('gr_order')->where(['ord_id' => $order_id,'ord_status' => '2'])->update(['ord_status' => $status,'ord_prepared_on' => date('Y-m-d H:i:s')]);
				$get_details = DB::table('gr_order')->select('ord_self_pickup','ord_grant_total','ord_admin_amt')->where('ord_id','=',$order_id)->first();
				if(!empty($get_details) && $get_details->ord_self_pickup == '0')
				{
					$mer_amt	 = ($get_details->ord_grant_total-$get_details->ord_admin_amt);
					// update in merchant overall table 
					$array = ['or_mer_amt'=>DB::Raw('or_mer_amt+'.$mer_amt)];
					//print_r($array); exit;
					$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $mer_id]);
				}
				$message = MobileModel::get_lang_text($lang,'API_ST_CHANGED','Status changed successfully');
				$encode = [ 'code' => 200,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			elseif($status == '8') //order delivered when order has self pickup
			{
				DB::table('gr_order')->where(['ord_id' => $order_id,'ord_status' => '2'])->update(['ord_status' => $status,'ord_delivered_on' => date('Y-m-d H:i:s')]);
				$get_details = DB::table('gr_order')->select('ord_self_pickup','ord_grant_total','ord_admin_amt')->where('ord_id','=',$order_id)->first();
				if(!empty($get_details) && $get_details->ord_self_pickup == '1')
				{
					$mer_amt	 = ($get_details->ord_grant_total-$get_details->ord_admin_amt);
					// update in merchant overall table 
					$array = ['or_mer_amt'	=>	DB::raw('or_mer_amt+'.($mer_amt))];
					$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $mer_id]);
				}
				$message = MobileModel::get_lang_text($lang,'API_ST_CHANGED','Status changed successfully');
				$encode = [ 'code' => 200,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* commission tracking */
		public function commission_tracking(Request $request)
		{
			$lang = $request->lang;
			$mer_details = JWTAuth::user();
			$mer_id = $mer_details->id;
			$mer_paypal = $mer_details->mer_paymaya_status;
			$mer_netbank = $mer_details->mer_netbank_status;
			$comm_details = Merchant::get_commisssion($mer_id);
			if(empty($comm_details))
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				$payment_err = '';
				if($mer_paypal == 'Unpublish' && $mer_netbank == 'Unpublish')
				{
					$payment_err = 'Before send pay request,please update payment details';
				}
				$data['total_orders'] 		= ($comm_details->or_total_order != '') ? $comm_details->or_total_order : 0;
				$data['currency'] 			= $mer_details->mer_currency_code;
				$data['total_order_amt'] 	= ($comm_details->or_mer_amt != '') ? $comm_details->or_mer_amt : 0.00 ;
				$data['total_admin_amt'] 	= ($comm_details->or_admin_amt != '') ? $comm_details->or_admin_amt : 0.00 ;
				$data['total_rcvd_amt'] 	= number_format($comm_details->paid_commission,2);
				$data['total_cancel_amt'] 	= ($comm_details->or_cancel_amt != '') ? $comm_details->or_cancel_amt : 0.00;
				$data['total_reject_amt'] 	= ($comm_details->or_reject_amt != '') ? $comm_details->or_reject_amt : 0.00;
				$data['bal_to_rcve'] 		= number_format(($comm_details->or_mer_amt - $comm_details->or_cancel_amt - $comm_details->paid_commission - $comm_details->or_reject_amt),2);
				$data['payment_err']		= $payment_err;
				$msge = MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$encode = [ 'code' => 200,'message' => $msge,'data' => $data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		/* transaction history */
		public function transaction_history(Request $request)
		{	
			$lang = $request->lang;
			$from	= date('Y-m-d H:i:s',strtotime($request->from_date));
			$to		= ($request->to_date == '') ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime($request->to_date. ' +1 day'));
			$page	= ($request->page_no == '') ? 1 : $request->page_no; 
			$mer_details = JWTAuth::user();
			$mer_id = $mer_details->id;
			$q = array();
			$sql = DB::table('gr_merchant_commission')
										->select(
										'gr_merchant_commission.mer_commission_id',
										'gr_merchant_commission.commission_paid',
										'gr_merchant_commission.commission_date',
										'gr_merchant_commission.commission_currency',
										'gr_merchant_commission.mer_transaction_id',
										'gr_merchant_commission.pay_type')
										->where('gr_merchant_commission.commission_mer_id','=',$mer_id)
										->orderby('gr_merchant_commission.commission_date','DESC');
			if($from != '' && $to != '')
            {   //echo $from; echo $to; exit;
				$q = $sql->whereBetween('commission_date', array($from, $to));
			}
			$q=	$sql->paginate(10,['*'],'transaction',$page);
			if(count($q) <= 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	$det = array();
				foreach($q as $list)
				{
					$pay_type = '';
					if($list->pay_type == 1)
					{
						$pay_type = "Net Banking";
					}
					elseif($list->pay_type == 2)
					{
						$pay_type = "Paypal";
					}
					elseif($list->pay_type == 3)
					{
						$pay_type = "Stripe";
					}
					$det[] = ['transaction_id' 	=> $list->mer_transaction_id,
								'paid_date'  	=> $list->commission_date,
								'currency' 		=> $list->commission_currency,
								'rcvd_amt' 		=> $list->commission_paid,
								'pay_type' 		=> $pay_type];
					
					//array_push($data,$det);
				}
				$msge = MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$encode = [ 'code' => 200,'message' => $msge,'data' => ["transaction_details"=> $det]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		/* pay request */
		public function pay_request(Request $request){
			$lang 			= $request->lang;
			$amount 		= $request->amount;
			$mer_details 	= JWTAuth::user();
			$mer_id			= $mer_details->deliver_id;
			$fname 			= $mer_details->mer_fname;
			$lname 			= $mer_details->mer_lname;
			$curr 			= $mer_details->mer_currency_code;
			$amount_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_AMOUNT','Please enter amount');
			$amount_valid_err_msg = MobileModel::get_lang_text($lang,'API_INVALID_AMT','Invalid Amount');
			$validator = Validator::make($request->all(),['amount'=>'required'],
			['amount.required' => $amount_req_err_msg,
			'amount.numeric'=>$amount_valid_err_msg]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			/** update notification count **/
    		$update = DB::table('gr_notification')->insert(['no_mer_id' => $mer_id,
			'no_status' => '1' ]);
			$adminMail = $this->admin_mail;
			$send_mail_data = array('name' 			=> 'Admin',
									'amount' 		=> $amount,
									'dm_name' 		=> $fname.' '.$lname,
									'lang'			=>$lang.'_mob_lang',
									'onlyLang'		=>$lang,
									'default_currency'=>$curr);
			Mail::send('email.mobile_payrequest_to_delmgr', $send_mail_data, function($message) use($send_mail_data,$adminMail)
			{
				$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'SEND_PAY_REQ','Pay Request');
				$message->to($adminMail)->subject($subject);
			});
			
			
			$msge=MobileModel::get_lang_text($lang,'API_MAIL_SENT_SUCCESSFULLY','Mail sent successfully!');
			return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
		/* profile*/
		public function my_profile(Request $request)
		{
			$lang = $request->lang;
			$user = JWTAuth::user();
			
			$msge = 'Fetched customer profile details succesfully!';
			$store_det = Merchant::store_details($user->id,$lang,$this->admin_default_lang);
			$available = 'Busy';
			$st_name = "";
			if(empty($store_det) === false)
			{
				$available = ($store_det->store_closed == 'Available') ? 'Available' :'Busy';
				$st_name = $store_det->store_name;
			}
			$licence = '';
			$addr_proof = '';
			if($user->license != '')
			{
				$filename = public_path('images/merchant/').$user->license;
				
				if(file_exists($filename)){
					$licence = url('public/images/merchant/'.$user->license);
				}
			}
			if($user->idproof != '')
			{
				$filename = public_path('images/merchant/').$user->idproof;
				
				if(file_exists($filename)){
					$addr_proof = url('public/images/merchant/'.$user->idproof );
				}
			}
			$data = ["mer_id"		=>intval($user->id),
					"mer_fname"		=>ucfirst($user->mer_fname),
					"mer_lname"		=>ucfirst($user->mer_lname),
					"mer_email"		=>$user->mer_email,
					"mer_phone"		=>$user->mer_phone,
					"mer_commission"=>$user->mer_commission,
					'mer_available_status' => $available,
					//"restaurant_name"	   => $st_name,
					//"idproof_name"	   			=> $user->idproof,
					//"restaurant_licence_name"	=> $user->license,
					//"restaurant_licence"	=> $licence,
					//"idproof"				=> $addr_proof,
					//"cancel_status"			=> $user->cancel_status,
					//"cancellation_policy"	=> $user->mer_cancel_policy,
					];
			$encode = ['code'=>200,'message'=>$msge,'data'	=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* update profile */
		public function update_profile(Request $request)
		{	
			$lang = $request->lang;
			$mer_details = JWTAuth::user();
			$mer_id    = $mer_details->id;
			$mer_fname = $request->mer_fname;
			$mer_lname = $request->mer_lname;
			$mer_email = $request->mer_email;
			$mer_phone = $request->mer_phone;
			//$mer_idproof = $request->id_proof;
			//$mer_licence = $request->restaurant_licence;
			$mer_avail_status = $request->mer_avail_status; // available or busy
			//$cancel_status = $request->mer_cancel_status; 
			//$cancellation_policy = $request->mer_cancellation_policy;
			$cus_name_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$cus_lname_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LAST_NAME','Please enter last name!');
			$mail_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required!');
			$mail_valid_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$mail_uni_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
			$ph_req_err_msg			= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$unique_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
			$licence_err_msg		= MobileModel::get_lang_text($lang,'API_UPLOAD_LICENCE','Please Upload licence image');
			$licence_val_err_msg	= MobileModel::get_lang_text($lang,'API_UPLOAD_ADDR_PROOF','Please upload valid licence image');
			$addr_proof_err_msg		= MobileModel::get_lang_text($lang,'API_VALID_ID','Please Upload Id-Proof');
			$addr_val_proof_err_msg	= MobileModel::get_lang_text($lang,'API_UP_VALID_ID','Please Upload valid Id-Proof');
			$max_size_err_msg	= MobileModel::get_lang_text($lang,'API_MAX_SIZE_2MB','Maximum uploaded size 2MB');
			$sl_avail_err_msg	= MobileModel::get_lang_text($lang,'API_SL_AVAIL_STATUS','Select Available Status');
			$validator = Validator::make($request->all(),['mer_fname' => 'required',
														'mer_lname'	=> 'required',
														'mer_email' => ['required','email',
														Rule::unique('gr_merchant')->where(function ($query) use ($mer_id) {
															return $query->where('id','!=',$mer_id)->where('gr_merchant.mer_status','<>','2');
														}),
														],
														'mer_phone' => ['required',
														Rule::unique('gr_merchant')->where(function ($query) use ($mer_id) {
															return $query->where('id','!=',$mer_id)->where('gr_merchant.mer_status','<>','2');
														}),
														],
														'mer_avail_status' => 'required',
														//'id_proof' 				=> 'required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,max_width=500,min_height=300,max_height=500',
														//'restaurant_licence' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,max_width=500,min_height=300,max_height=500',
														//'mer_cancel_status'	=> 'required',
														//'mer_cancellation_policy'	=> 'required',
														],
														['mer_fname.required' 	=> $cus_name_req_err_msg,
														'mer_lname.required' 	=> $cus_lname_req_err_msg,
														'mer_email.required' 	=> $mail_req_err_msg,
														'mer_email.email' 		=> $mail_valid_err_msg,
														'mer_email.unique' 		=> $mail_uni_err_msg,
														'mer_phone.required' 	=> $ph_req_err_msg,
														'mer_phone.unique' 		=> $unique_phone_err_msg,
														//'mer_avail_status.required' => $sl_avail_err_msg,
														//'restaurant_licence.required'	=> $licence_err_msg,
														//'restaurant_licence.mimes'		=> $licence_val_err_msg,
														//'id_proof.required'		=> $addr_proof_err_msg,
														//'id_proof.mimes'			=> $addr_val_proof_err_msg,
														//'restaurant_licence.max'	=> $max_size_err_msg,
														//'id_proof.max'				=> $max_size_err_msg,
														]);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$store_det = Merchant::store_details($mer_id);
			//print_r($store_det); exit;
			if($mer_avail_status == "Busy")
			{
				
				if(empty($store_det) === false)
				{
					updatevalues('gr_res_working_hrs',['wk_end_time'=>date('h:i a')],['wk_res_id'=>$store_det->id,'wk_date' => date('l')]);
				}
			}
			if($mer_avail_status == "Available")
			{
				if(empty($store_det) === false)
				{
					updatevalues('gr_res_working_hrs',['wk_start_time' => $store_det->old_start_time,'wk_end_time'=>$store_det->old_end_time],['wk_res_id'=>$store_det->id,'wk_date' => date('l')]);
				}
			}
			/* update previous date working time */
			$prev_date = date('Y-m-d', strtotime(' -1 day'));
			$prev_day = date('l',strtotime($prev_date));
			if(empty($store_det) === false)
			{ 
				updatevalues('gr_res_working_hrs',['wk_start_time' => DB::Raw('old_start_time'),'wk_end_time' => DB::Raw('old_end_time')],['wk_res_id'=>$store_det->id,'wk_date' => $prev_day]);
			}
			$data = ['mer_fname' => $mer_fname,
					'mer_lname'	 => $mer_lname,
					'mer_email'	 => $mer_email,
					'mer_phone'	 => $mer_phone
					//'mer_cancel_policy'	=>$cancellation_policy,
					//'cancel_status'		=>$cancel_status
					];
			/*$old_id_proof = $mer_details->idproof;
			$old_licence = $mer_details->license;
			if($request->hasFile('id_proof')) {
				// delete old image *
				$image_path = public_path('images/merchant/').$old_id_proof;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				
				$idproof_image = 'Id_Proof_'.rand().'.'.request()->id_proof->getClientOriginalExtension();
				$destinationPath = public_path('images/merchant');
				$Idproof = Image::make(request()->id_proof->getRealPath())->resize(300, 300);
				$Idproof->save($destinationPath.'/'.$idproof_image,80);
				$data['idproof'] = $idproof_image;
			}
			if($request->hasFile('restaurant_licence')) {
				// delete old image 
				$image_path = public_path('images/merchant/').$old_licence;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				
				
				$license_image = 'Licence_'.rand().'.'.request()->restaurant_licence->getClientOriginalExtension();
				$destinationPath = public_path('images/merchant');
				$License = Image::make(request()->restaurant_licence->getRealPath())->resize(300, 300);
				$License->save($destinationPath.'/'.$license_image,80);
				$data['license'] = $license_image;
			}*/
			updatevalues('gr_merchant',$data,['id' => $mer_id]);
			$msg 	= MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
			$encode = [ 'code' => 200,
						'message' => $msg,
						'data'=> $data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* payment setting view*/
		public function view_payment_setting(Request $request)
		{
			$lang = $request->lang;
			$user = JWTAuth::user();
			$msge = 'Fetched details succesfully!';
			$data = ["net_bank_status"	=>$user->mer_netbank_status,
			"net_acc_no"		=>$user->mer_bank_accno,
			"net_bank_name"		=>$user->mer_bank_name,
			"net_branch_name"	=>$user->mer_branch,
			"net_ifsc"			=>$user->mer_ifsc,
			"paypal_status"		=>$user->mer_paymaya_status,
			"paypal_client_id"	=>$user->mer_paymaya_clientid,
			"paypal_secret_key" =>$user->mer_paymaya_secretid,
			"stripe_status"		=>$user->mer_paynamics_status,
			"stripe_client_id"	=>$user->mer_paynamics_clientid,
			"stripe_secret_key"	=>$user->mer_paynamics_secretid,
			];
			$encode = ['code'=>200,'message'=>$msge,'data'	=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		public function update_payment_setting(Request $request)
		{
			$merDet = JWTAuth::user();
			$mer_id	= $merDet->id;
            $lang = $request->lang;           
			$mer_stripe_status 	= $request->mer_stripe_status;		
			$mer_stripe_clientid= $request->mer_stripe_clientid;		
			$mer_stripe_secretid= $request->mer_stripe_secretid;		
			
			$mer_paypal_status 	= $request->mer_paypal_status;		
			$mer_paypal_clientid= $request->mer_paypal_clientid;		
			$mer_paypal_secretid= $request->mer_paypal_secretid;		
			
			$mer_netbank_status = $request->mer_netbank_status;		
			$mer_bank_name 		= $request->mer_bank_name;		
			$mer_branch 		= $request->mer_branch;		
			$mer_bank_accno 	= $request->mer_bank_accno;		
			$mer_ifsc 			= $request->mer_ifsc;		
			
			
			$agent_paynmincs_client_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYNA_CLIENT','Please Enter paynamics client ID!');
			$agent_paynmincs_secret_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYNA_SECRET','Please Enter paynamics secret ID!');
			$agent_paynmincs_status_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYNA_STATUS','Please select paynamics status!');
			
			$agent_paymaya_client_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYMA_CLIENT','Please Enter paymaya client ID!');
			$agent_paymaya_secret_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYMA_SECRET','Please Enter paymaya secret ID!');
			$agent_paymaya_status_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYMA_STATUS','Please select paymaya status!');
			
			$agent_bank_name_req_err_msg 	= MobileModel::get_lang_text($lang,'API_ENTER_BANK','Please enter bank name!');
			$agent_branch_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_BRANCH','Please enter branch name!');
			$agent_accNo_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_ACCNO','Please enter account number!');
			$agent_ifsc_req_err_msg			= MobileModel::get_lang_text($lang,'API_ENTER_IFSC','Please enter IFSC code!');
			
			if($mer_paypal_status=='Unpublish' && $mer_netbank_status=='Unpublish')
			{
				$msg = MobileModel::get_lang_text($lang,'API_FILL_PAYPAL_NET_DETAILS','Please Fill Paypal or Net Banking Details');
				return Response::make(json_encode(array('code'=>400,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if( $mer_stripe_status == 'Publish')
			{
				$validator = Validator::make($request->all(),['mer_stripe_clientid' => 'required',
				'mer_stripe_secretid' => 'required'
				],[
				'mer_stripe_clientid.required' => $agent_paynmincs_client_req_err_msg,
				'mer_stripe_secretid.required' => $agent_paynmincs_secret_req_err_msg,
				
				]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}else{
				$mer_stripe_clientid	= '';
				$mer_stripe_secretid = '';
			}
			
			
			if($mer_paypal_status=='Publish')
			{
				$validator = Validator::make($request->all(),[ 	'mer_paypal_clientid' => 'required',
				'mer_paypal_secretid' => 'required'
				],[ 
				'mer_paypal_clientid.required' => $agent_paymaya_secret_req_err_msg,
				'mer_paypal_secretid.required' => $agent_paymaya_status_req_err_msg
				]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}else{
				$mer_paypal_clientid	= '';
				$mer_paypal_secretid = '';
			}
			
			if($mer_netbank_status=='Publish')
			{
				$validator = Validator::make($request->all(),[ 
				'mer_bank_name'  => 'required',
				'mer_branch'	 => 'required',
				'mer_bank_accno' => 'required',
				'mer_ifsc' 		 => 'required'
				],[ 
				'mer_bank_name.required' => $agent_bank_name_req_err_msg,
				'mer_branch.required'	 => $agent_branch_req_err_msg,
				'mer_bank_accno.required'=> $agent_accNo_req_err_msg,
				'mer_ifsc.required' 	 => $agent_ifsc_req_err_msg
				]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}else{
				$mer_bank_name  = '';
				$mer_branch    	= '';
				$mer_bank_accno	= '';
				$mer_ifsc    	= '';
			}
			
			$agent_payment_details = array('mer_paynamics_status'	=> $mer_stripe_status,
			'mer_paynamics_clientid'=> $mer_stripe_clientid,
			'mer_paynamics_secretid'=> $mer_stripe_secretid,
			'mer_paymaya_status'	=> $mer_paypal_status,
			'mer_paymaya_clientid'	=> $mer_paypal_clientid,
			'mer_paymaya_secretid'	=> $mer_paypal_secretid,
			'mer_netbank_status'	=> $mer_netbank_status,
			'mer_bank_name'			=> $mer_bank_name,
			'mer_branch'			=> $mer_branch,
			'mer_bank_accno'		=> $mer_bank_accno,
			'mer_ifsc'				=> $mer_ifsc
			);
			/** Update  Payment Details **/
			DB::table('gr_merchant')->where('id', '=', $mer_id)->update($agent_payment_details);
			
			$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
			return Response::make(json_encode(array('code'=>200,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		/* change password */
		public function change_password(Request $request)
		{
			$lang  = $request->lang;
			$old_password = $request->old_password; 
			$new_password = $request->new_password; 
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$oldpwd_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_OLD_PASSWORD','Please enter old password!');
			$newpwd_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_NEW_PASSWORD','Please enter new password!');
			$pwd_min_6_err_msg  = MobileModel::get_lang_text($lang,'API_PASSWORD_RULES','Password min. length should be 6!');
			$pwd_regex_err_msg  = MobileModel::get_lang_text($lang,'API_PROTECT_PASSWORD_RULES','Password should be atleast one lower case, upper case, number and min.length 6!');
			$pwd_same_err_msg  =  MobileModel::get_lang_text($lang,'API_NEW_PASS_NOT_SAME_CURRENT_PASS','New Password cannot be same as your current password. Please choose a different password!');
			$old_pwd_text 		= MobileModel::get_lang_text($lang,'API_OLD','Old');
			$new_pwd_text 		= MobileModel::get_lang_text($lang,'API_NEW','New');
			if($this->general_setting->gs_password_protect==1)
			{
				$validator = Validator::make($request->all(), 
				[ 'old_password' => 'required|min:6',
				'new_password' => 'required|different:old_password|min:6|regex:/(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$)+/'
				],
				[ 'old_password.required'=>$oldpwd_req_err_msg,
				'old_password.min'   =>$old_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.required'=>$newpwd_req_err_msg,
				'new_password.min'  =>$new_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.regex'=>$pwd_regex_err_msg,
				'new_password.different'=>$pwd_same_err_msg
				]
				);
			}
			else{
				$validator = Validator::make($request->all(), 
				[ 'old_password' 	=> 'required|min:6', 
				'new_password' 	=> 'required|min:6|different:old_password'],
				[ 'old_password.required'	=>$oldpwd_req_err_msg,
				'old_password.min'		=>$old_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.required'	=> $newpwd_req_err_msg,
				'new_password.min'=>$new_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.different'=>$pwd_same_err_msg,
				]
				);
			}
			
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode	 = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$user = JWTAuth::user();
				//echo $user->agent_password.'<hr>';
				if($user->mer_password != md5($old_password))
				{
					$msg 	= MobileModel::get_lang_text($lang,'API_INCORRECT_PASSWORD','Your old password does not match with our records! Please try again!');
					$encode = [ 'code' => 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				else{
					
					$insertArr = array('mer_password' => md5($new_password),
					'mer_decrypt_password' => $new_password);
					$update = updatevalues('gr_merchant',$insertArr,['id'=>$user->id]);
					$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
					
					$encode = ['code'		=> 200,
					"message"	=> $msg,
					'data'		=> $this->empty_data
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			
		}
		
		/** stock management and item block **/
		public function stock_management(Request $request)
		{
			$lang = $request->lang;
			$page_no = ($request->page_no == '') ? 1 : $request->page_no;
			$search = $request->search_text;
			$mer_details = JWTAuth::user();
			$mer_id 	= $mer_details->id;
			$get_stock = Merchant::get_stock_details($mer_id,$lang,$this->admin_default_lang,$page_no,$search);
			if(count($get_stock) > 0 )
			{	
				$content = array();
				foreach($get_stock as $stock)
				{	

					if($stock->pro_status == '0')
					{
						$status = "Block";
					}
					elseif($stock->pro_status == '1')
					{
						$status = "Active";
					}
					elseif($stock->pro_status == '2')
					{
						$status = "Delete";
					}
					$content[] = [	'restaurant_id'	=> $stock->pro_store_id,
									'item_id'	=> $stock->pro_id,
									'item_code' => $stock->pro_item_code,
									'item_name'	=> $stock->pro_name,
									'item_image'	=> MobileModel::get_image_item($stock->pro_image),
									'item_quantity' => $stock->pro_quantity,
									'sold_quantity'	=> $stock->pro_no_of_purchase,
									'avail_quantity' => $stock->pro_quantity - $stock->pro_no_of_purchase,
									'item_status'	=> $status,
									'item_status_code'	=> $stock->pro_status,
									];
				}
				$msge = MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$encode = [ 'code' => 200,'message' => $msge,'data' => ['stock_list' => $content]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}

		/* update status and quantity */
		public function update_quantity_andStatus(Request $request)
		{
			$lang  = $request->lang;
			$mer_details = JWTAuth::user();
			$mer_id 	= $mer_details->id;
			$route = \Request::segment(3);
			$item_id = $request->item_id;
			$shop_id = $request->restaurant_id;
			$status = $request->status;
			$quantity = $request->quantity;
			$update  = array();
			$validator = Validator::make($request->all(), 
											[ 'item_id' => ['required',
											Rule::exists('gr_product','pro_id')->where(function ($query) use($shop_id) {
												$query->where('pro_store_id','=',$shop_id);
											})],
											'restaurant_id' => ['required',
											Rule::exists('gr_store','id')->where(function ($query) use($mer_id) {
												$query->where('st_mer_id','=',$mer_id);
											})]
											],
											[ 'item_id.required'	=>'Enter item id',
											'item_id.exists'   		=> 'Item id not exists',
											'restaurant_id.required'  =>'Enter restaurant id',
											'restaurant_id.exists'	=> 'Restaurant id not exists'
											]
											);
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode	 = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($route == "update_status")
			{	

				$validator = Validator::make($request->all(),['status' => 'required|digits_between:0,2']);
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					$encode	 = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				$update = ['pro_status' => $status];
				if($status == 2) //delete
				{
					/* delete choices */
					$delete_ch = DB::table('gr_product_choice')->where(['pc_pro_id' => $item_id])->delete();
					/* delete specifications */
					$delete_spec = DB::table('gr_product_spec')->where(['spec_pro_id' => $item_id])->delete();
				}
			} 
			if($route == "increase_quantity" || $route == "decrease_quantity")
			{
				$validator = Validator::make($request->all(),['quantity' => 'required|integer|min:1']);
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					$encode	 = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				if($route == "increase_quantity")
				{
					$update = ['pro_quantity' => DB::Raw('pro_quantity+'.$quantity)];	
				}
				elseif($route == "decrease_quantity")
				{
					$check_sold = DB::table('gr_product')->select(DB::Raw("IF((SELECT (pro_quantity".-$quantity.")< pro_no_of_purchase),'DISALLOW','ALLOW') as stock"))->where('pro_id','=',$item_id)->first();
					//print_r($check_sold); exit;
					if($check_sold->stock =="DISALLOW")
					{
						$encode	 = [ 'code' => 400,'message' => 'Can\'t decrease the quanity above available quantity','data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}
					$update = ['pro_quantity' => DB::Raw('pro_quantity-'.$quantity)];
				}
				
			}
			$a = updatevalues('gr_product',$update,['pro_id' => $item_id]);
			$encode	 = [ 'code' => 200,'message' => 'Updated successfully!','data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}

		/* notification list */
		public function notification_list(Request $request)
		{
			$mer_details = JWTAuth::user();
			$mer_id 	= $mer_details->id;
			$page_no 	= $request->page_no;
			$get_details = DB::table('gr_general_notification')->select('message','updated_at','order_id','read_status','id')->where(['receiver_id' => $mer_id,'receiver_type' => 'gr_merchant'])->orderby('updated_at','desc')->paginate(10,['*'],'notification',$page_no);
			if(count($get_details) > 0)
			{
				$data = array();
				foreach($get_details as $details)
				{
					$data[] = ['message' 			=> $details->message,
								'transaction_id'	=> $details->order_id,
								'notification_id'	=> $details->id,
								'received_on'		=> $details->updated_at,
								'is_read'			=> $details->read_status
								];
				}
				$encode	 = [ 'code' => 200,'message' => 'Records Found','data' => ['notification_list' => $data]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$encode	 = [ 'code' => 400,'message' => 'No records Found','data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}

		/* make as read notification */
		public function read_notification(Request $request)
		{
			$mer_details = JWTAuth::user();
			$mer_id 	= $mer_details->id;
			$notifi_id 	= $request->id;
			$status 	= $request->read_status;
			$validator = Validator::make($request->all(), 
											[ 'id' => ['required',
											Rule::exists('gr_general_notification')->where(function ($query) use($mer_id) {
												$query->where('receiver_id','=',$mer_id);
											})],
											'read_status' => 'required'
											],
											[ 'id.required'			=> 'Enter notification id',
											'id.exists'   			=> 'Notification id not exists',
											'read_status.required'  => 'Enter read status'
											]
											);
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode	 = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				updatevalues('gr_general_notification',['read_status' => $status],['id' => $notifi_id,'receiver_id' => $mer_id]);
				$encode	 = [ 'code' => 200,'message' => 'Updated successfully','data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}

		/* logout */
		public function logout(Request $request)
		{
			$lang= $request->lang;
			$andr_device_id = $request->andr_device_id;
			$ios_device_id  = $request->ios_device_id;
			$ph_type  = $request->type; //android or ios
			$validator = Validator::make($request->all(),[ 'token'	=> 'required']);
            if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			//$this->validate($request, ['token' => 'required']);
			//echo 'here';
			//exit;
			try {
				$user = JWTAuth::user();
				if($ph_type == 'android' && $andr_device_id != '' && $user->mer_andr_fcm_id!=''){ 
					$andr_fcm_arr = json_decode($user->mer_andr_fcm_id,true);
					//print_r($andr_fcm_arr); exit;
					$newArray = removeElementWithValue($andr_fcm_arr, "device_id", $andr_device_id);
					$updatableArray = array('mer_andr_fcm_id' =>json_encode($newArray));
					updatevalues('gr_merchant',$updatableArray,['mer_status' => '1','mer_email' => $user->mer_email]);
				}
				if($ph_type == 'ios' && $ios_device_id != '' && $user->mer_ios_fcm_id != ''){
					$ios_fcm_arr = json_decode($user->mer_ios_fcm_id,true);
					$newArray = removeElementWithValue($ios_fcm_arr, "device_id", $ios_device_id);
					$updatableArray = array('mer_ios_fcm_id' =>json_encode($newArray));
					updatevalues('gr_merchant',$updatableArray,['mer_status' => '1','mer_email' => $user->mer_email]);
				}
				JWTAuth::invalidate($request->token);
				$msge = MobileModel::get_lang_text($lang,'API_LOGOUT_SUXES','User logged out successfully!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				} catch (JWTException $exception) {
				$msge = MobileModel::get_lang_text($lang,'API_CANNOT_LOGOUT','Sorry, the user cannot be logged out!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
	}
?>