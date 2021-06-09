<?php 
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Front;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	
	use Illuminate\Validation\Rule;
	
	use Validator;
	
	use Session;
	
	use Mail;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use App\Home;
	
	use App\Customer;
	
	use Route;
	
	use Twilio;
	
	use Image;
	
	use Illuminate\Foundation\Auth\ThrottlesLogins;
	
	use Config;
	//use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
	
	class LoginController extends Controller
	{
		/** Add This line on top */
		//use ThrottlesLogins;
		/** This way, you can control the throttling */
		//protected $maxLoginAttempts=3;
		//protected $lockoutTime=300;
		public function __construct()
		{	
			parent::__construct();
		}
		public function check_login(Request $request)
		{	
			//print_r($request->all()); exit;
			$text = mysql_escape_special_chars(Input::get('mail'));
	        $password = mysql_escape_special_chars(Input::get('pwd'));
	        $cus_check = '';
	        $wh = '';
	        if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$text))
			{
				$cus_check = Customer::login_check($text,$password);
				$wh = 'cus_email';
			}
			elseif(preg_match('/^[0-9+]+/i', $text))
			{	
				if($text[0] != '+') /* enter phone num with + */
				{
					return -3;
				}
				$cus_check = Customer::login_ph_check($text,$password);
				$wh = 'cus_phone1';
			}
	        if($cus_check == 0) /* invalid email */
	        {
	            return 0;
			}
	        elseif($cus_check == -1)    /* invalid password */
	        {
				
	            return -1;
			}
	        elseif($cus_check == -4)    /* invalid phone number */
	        {
				
	            return -4;
			}
	        elseif($cus_check == 1) /* login success */
	        {	

				//$this->clearLoginAttempts($request);
				$cus_logged_ip = $request->ip();
				$cus_loggedin_time = date('Y-m-d H:i:s');
				$updatableArray = array('cus_logged_ip'=>$cus_logged_ip,'cus_loggedin_time'=>$cus_loggedin_time);
				updatevalues('gr_customer',$updatableArray,['cus_status' => '1',$wh => $text]);
	        	$details = DB::table('gr_customer')->select('cus_email','cus_fname','cus_id','cus_email')->where(['cus_status' => '1',$wh => $text])->first();
	            Session::put('customer_login',1);
	            Session::put('customer_name',$details->cus_fname);
	            Session::put('customer_id',$details->cus_id);
	            Session::put('customer_mail',$details->cus_email);
	            Session::flash('success',(Lang::has(Session::get('front_lang_file').'.ADMIN_LOGIN_SUCCESS')) ? trans(Session::get('front_lang_file').'.ADMIN_LOGIN_SUCCESS') : trans($this->FRONT_LANGUAGE.'.ADMIN_LOGIN_SUCCESS'));
				if(!session()->has('from'))
	            {
	                Session::put('from',url()->previous());
				}
				if(Session::has('shipping_session') == 1){
					$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',$details->cus_id)->first();
					if(empty($shipAddDet)===false){
						$update = updatevalues('gr_shipping',Session::get('shipping_session'),['sh_cus_id'=>$details->cus_id]);
					}else{
						//DB::connection()->enableQueryLog();
						$shipArray = Session::get('shipping_session');
						$shipArray['sh_cus_id']=$details->cus_id;
						$insert = insertvalues('gr_shipping',$shipArray);
						//$query = DB::getQueryLog();
						//print_r($query);
					}
				}
				else{
					//echo 'dn have';
				}
				
	            return 1;
			}
	        else
	        {
	        	return "fail";
			}
		}
		public function cus_logout()
		{
			Session::forget('customer_login');
			Session::forget('customer_id');
			Session::forget('customer_details');
			Session::forget('customer_mail');
			$msg = 'LoggedOut';
			Session::flash('success',$msg);
			return Redirect::to('/');
		}
		public function cus_signup()
		{
			$cus_fname = Input::get('cus_fname');
			$cus_phone = Input::get('cus_phone');
			$cus_email = Input::get('cus_email');
			$cus_pwd = Input::get('cus_pwd');
			DB::connection()->enableQueryLog();
			$referer_id=0;
			if(Input::get('referer_id')!=''){
				$referer_data = DB::table('gr_referal')->where('referre_email','=',base64_decode(Input::get('referer_id')))->first();
				if(empty($referer_data)===false){
					$referer_id=$referer_data->referral_id;
					}else{
					$jsonArr = array('msg'=>'Sorry! Email not available!');
					echo json_encode($jsonArr);
					exit;
				}
			} 
			//$referer_id = Input::get('referer_id');
			///print_r(Input::get()); exit; 
			$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_status','!=','2')->count();
			// $query = DB::getQueryLog();
			
			$check_phoneNumber_already_exsist = DB::table('gr_customer')->where('cus_phone1','=',$cus_phone)->where('cus_status','!=','2')->count();
			if($check_already_exsist > 0)
			{
				$jsonArr = array('msg'=>'Email Already Exists!');
				echo json_encode($jsonArr);
			}
			elseif($check_phoneNumber_already_exsist > 0)
			{
				$jsonArr = array('msg'=>'Phone Number Already Exists!');
				echo json_encode($jsonArr);
			}
			else{	
				$detArr = array(
				'cus_fname'=>$cus_fname,
				'cus_phone1'=>$cus_phone,
				'cus_email'=>$cus_email,
				'cus_password'=>md5($cus_pwd),
				'cus_decrypt_password'=>$cus_pwd,
				'cus_login_type' => '1',
				'cus_status' => '1',
				'cus_referedBy'=>$referer_id,
				'cus_paynamics_status'=>'Unpublish',
				'cus_paymaya_status'=>'Unpublish',
				'cus_netbank_status'=>'Unpublish',
				'cus_created_date' => date('Y-m-d')
				);
				
				DB::table('gr_customer')->insert($detArr);
				Session::put('customer_login',1);
				$lastinsertid = DB::getPdo()->lastInsertId();
				Session::put('customer_id',$lastinsertid);
				Session::put('customer_mail',$cus_email);
				
				if(Session::has('shipping_session') == 1){
					$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',$lastinsertid)->first();
					if(empty($shipAddDet)===false){
						$update = updatevalues('gr_shipping',Session::get('shipping_session'),['sh_cus_id'=>$lastinsertid]);
					}else{
						$shipArray = Session::get('shipping_session');
						$shipArray['sh_cus_id']=$lastinsertid;
						$insert = insertvalues('gr_shipping',$shipArray);
					}
				}
				
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => $cus_fname,
				'password' => $cus_pwd,
				'email' => $cus_email,
				);
				Mail::send('email.customerregister', $send_mail_data, function($message)
				{
					$email               = Input::get('cus_email');
					$name                = Input::get('cus_fname');
					$subject = (Lang::has(Session::get('front_lang_file').'.FRONT_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				$jsonArr = array('msg'=>'Success');
				echo json_encode($jsonArr);
				
				
                $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REG_SUXUS')) ? trans(Session::get('front_lang_file').'.FRONT_REG_SUXUS') : trans($this->FRONT_LANGUAGE.'.FRONT_REG_SUXUS');
				
                Session::flash('success',$msg);
			}
		}
		public function cus_signup_with_otp()
		{
			$cus_fname = Input::get('cus_fname');
			$cus_phone = Input::get('cus_phone');
			$cus_email = Input::get('cus_email');
			$cus_pwd = Input::get('cus_pwd');
			$referer_id=0;
			if(Input::get('referer_id')!=''){
				$referer_data = DB::table('gr_referal')->where('referre_email','=',base64_decode(Input::get('referer_id')))->first();
				if(empty($referer_data)===false){
					$referer_id=$referer_data->referral_id;
					}else{
					$jsonArr = array('msg'=>'Sorry! Email not available!');
					echo json_encode($jsonArr);
					exit;
				}
			}
			$otp = mt_rand(100000, 999999);
			$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_status','!=','2')->count();
			if($check_already_exsist > 0)
			{
				$jsonArr = array('msg'=>'Customer Already Available!');
				echo json_encode($jsonArr);
				}else{	
				$detArr = array(
				'cus_fname'=>$cus_fname,
				'cus_phone1'=>$cus_phone,
				'cus_email'=>$cus_email,
				'cus_password'=>$cus_pwd,
				'cus_login_type' => '1',
				'cus_status' => '1',
				'cus_referedBy'=>$referer_id,
				'cus_paynamics_status'=>'Unpublish',
				'cus_paymaya_status'=>'Unpublish',
				'cus_netbank_status'=>'Unpublish',
				'cus_created_date' => date('Y-m-d')
				);
				Session::put('customer_details',$detArr);
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => $cus_fname,
				'password' => $cus_pwd,
				'email' => $cus_email,
				);
				/*Mail::send('email.customerregister', $send_mail_data, function($message)
					{
					$email               = Input::get('cus_email');
					$name                = Input::get('cus_fname');
					$subject = (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});*/
				/* EOF MAIL FUNCTION */ 
				try{
					Twilio::message($cus_phone, $otp);
					$jsonArr = array('msg'=>'Success','otp'=>$otp);
					echo json_encode($jsonArr);
					
				}
				catch (\Exception $e)
				{		
					
					$jsonArr = array('msg'=>$e->getMessage());
					echo json_encode($jsonArr);
				}
			}
		}
		public function check_otp()
		{	
			$entered_otp = Input::get('otp');
			$current_otp = Input::get('current_otp');
			
			if($entered_otp == $current_otp){
				$sessionArr = Session::get('customer_details');
				Session::put('customer_login',1);
				$detArr = array(
				'cus_fname'=>$sessionArr['cus_fname'],
				'cus_phone1'=>$sessionArr['cus_phone1'],
				'cus_email'=>$sessionArr['cus_email'],
				'cus_password'=>md5($sessionArr['cus_password']),
				'cus_decrypt_password'=>$sessionArr['cus_password'],
				'cus_login_type' => '1',
				'cus_status' => '1',
				'cus_created_date' => date('Y-m-d')
				);
				$insert = DB::table('gr_customer')->insert($detArr);
				$lastinsertid = DB::getPdo()->lastInsertId();
				
				
				//cus_signup_with_otp
				if(Session::has('shipping_session') == 1){
					$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',$lastinsertid)->first();
					if(empty($shipAddDet)===false){
						$update = updatevalues('gr_shipping',Session::get('shipping_session'),['sh_cus_id'=>$lastinsertid]);
					}else{
						$shipArray = Session::get('shipping_session');
						$shipArray['sh_cus_id']=$lastinsertid;
						$insert = insertvalues('gr_shipping',$shipArray);
					}
				}
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => $sessionArr['cus_fname'],
				'password' => $sessionArr['cus_password'],
				'email' => $sessionArr['cus_email'],
				);
				Mail::send('email.customerregister', $send_mail_data, function($message)
				{
					$sessionArr = Session::get('customer_details');
					$email               = $sessionArr['cus_email'];
					$name                = $sessionArr['cus_fname'];
					$subject = (Lang::has(Session::get('front_lang_file').'.FRONT_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				Session::put('customer_id',$lastinsertid);
				Session::put('customer_mail',$sessionArr['cus_email']);
				
				
                $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REG_SUXUS')) ? trans(Session::get('front_lang_file').'.FRONT_REG_SUXUS') : trans($this->FRONT_LANGUAGE.'.FRONT_REG_SUXUS');
				
                Session::flash('success',$msg);
				echo 'match';
				}else{
				echo 'wrong';
			}
		}
		/** mobile_check_otp **/ 
		public function mobile_check_otp()
		{
			$entered_otp = Input::get('otp');
			$current_otp = Input::get('current_otp');
			if($entered_otp == $current_otp)
			{
				echo 'match';
				}else{
				echo 'wrong';
			}
		}
		/** check user mail/phone number exists for forget password **/
		public function check_user(Request $request)
		{	
			$text = $request->text;  
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$text))
			{ 
				$check = check_user(['cus_email' => $text,'cus_status' => '1']);
				
				if($check > 0)
				{
					echo "Success"; exit;
				}
				else
				{	
					echo "Invalid Email"; exit;
				}
			}
			elseif(preg_match('/^[0-9+]+/i', $text) && (TWILIO_STATUS == 1 ))
			{ 
				$first = $text[0];
				if($first != '+')
				{
					echo "Not Valid";
				}
				$check = check_user(['cus_phone1' => $text,'cus_status' => '1']);
				if($check > 0)
				{
					echo "Success"; exit;
				}
				else
				{
					echo "Invalid Phone"; exit;
				}
			}
			else
			{
				echo "Invalid"; exit;
			}
			
		}
		
		/** user forget password **/
		public function forgot_password(Request $request)
		{
			
			$text = $request->mail;
			
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$text))
			{
				$check = get_user(['cus_email' => $text,'cus_status' => '1']);
				
				
				if(empty($check) === false)
				{
					if($check->cus_email == $text)
					{
						$rand_password = rand_password();
						$name = $check->cus_fname;
						
						/*MAIL FUNCTION */
						$send_mail_data = array(
						
						'name' => $name,                                      
						'email' => $text,
						'password' => $rand_password,
						);                        
						
						Mail::send('email.cus_forgot_password', $send_mail_data, function($message) use ($text)
						{
							$email  = $text;                                  
							$subject = (Lang::has(Session::get('front_lang_file').'.FRONT_PASSWORD_RECOVERY')) ? trans(Session::get('front_lang_file').'.FRONT_PASSWORD_RECOVERY') : trans($this->FRONT_LANGUAGE.'.FRONT_PASSWORD_RECOVERY');
							$message->to($email)->subject($subject);
						});
						
						$send_forgot_data = array(                                                      
						'cus_password' => md5($rand_password)
						
						);     
						
						$update = updatevalues('gr_customer',['cus_password' => md5($rand_password),'cus_decrypt_password' => $rand_password,],['cus_id' =>$check->cus_id]);
						
						$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_GET_PASSWORD')) ? trans(Session::get('front_lang_file').'.FRONT_GET_PASSWORD') : trans($this->FRONT_LANGUAGE.'.FRONT_GET_PASSWORD');
						Session::flash('success',$msg);
						
						return Redirect::to('/');
						
					}
					else
					{	
						$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_INVALID_MAIL')) ? trans(Session::get('front_lang_file').'.FRONT_INVALID_MAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_INVALID_MAIL');
						Session::flash('val_errors',$msg);
						return Redirect::to('/');
						
					}
					}else{
                    $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_INVALID_MAIL')) ? trans(Session::get('front_lang_file').'.FRONT_INVALID_MAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_INVALID_MAIL');
                    Session::flash('val_errors',$msg);
                    return Redirect::to('/');
				}
			}
			elseif(preg_match('/^[0-9+]+/i', $text) && (TWILIO_STATUS == 1 ))
			{	$check = get_user(['cus_phone1' => $text,'cus_status' => '1']);
				if($check->cus_phone1 == $text)
				{	
					$password 	   = rand_password();
					
                    $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_RCE_NW_PW')) ? trans(Session::get('front_lang_file').'.FRONT_RCE_NW_PW') : trans($this->FRONT_LANGUAGE.'.FRONT_RCE_NW_PW');
                    $rand_password = $msg.' '.$password;
					
                    try{
 						Twilio::message($check->cus_phone1, $rand_password);
 						$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_GET_PASSWORD_PH')) ? trans(Session::get('front_lang_file').'.FRONT_GET_PASSWORD_PH') : trans($this->FRONT_LANGUAGE.'.FRONT_GET_PASSWORD_PH');
 						$update = updatevalues('gr_customer',['cus_password' => md5($password),'cus_decrypt_password' => $password,],['cus_id' =>$check->cus_id]);
				        Session::flash('success',$msg);
						return Redirect::to('/');
					}
 					catch (\Exception $e)
					{		
					 	$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_ERROR_SEND')) ? trans(Session::get('front_lang_file').'.FRONT_ERROR_SEND') : trans($this->FRONT_LANGUAGE.'.FRONT_ERROR_SEND');
				        Session::flash('success',$msg);
						return Redirect::to('/');
					}
				}
				else
				{
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_INVALID_PHONE')) ? trans(Session::get('front_lang_file').'.FRONT_INVALID_PHONE') : trans($this->FRONT_LANGUAGE.'.FRONT_INVALID_PHONE');
			        Session::flash('val_errors',$msg);
					return Redirect::to('/');
				}
			}
			else
			{
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_INVALID_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_INVALID_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_INVALID_DETAILS');
				Session::flash('val_errors',$msg);
				return Redirect::to('/');
			}
		}
		
		public function customer_profile()
		{
			if(Session::has('customer_id') == 1)
			{ 
				$cus_id = Session::get('customer_id');
				$sel = DB::table('gr_customer')->where('cus_id',$cus_id)->first();
				return view('Front.customer_profile',['customer_detail'=>$sel,'id'=>$cus_id]);
			}
			else{
				return Redirect::to('/');
			}
		}
		public function customer_profile_update(Request $request)
		{
			
			if(Session::has('customer_id') == 1)
			{ 
				$cus_name_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL');
				$cus_email_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL');
				$cus_valid_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTR_MAIL');
				$cus_unique_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
				$cus_phone_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE');
				$cus_addr_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_ADDR_VAL');
				$cus_pass_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PASS_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PASS_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_PASS_VAL');
				$cus_img_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_VAL');
				$cus_img_dimen_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_IMAGE_dimen_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_IMAGE_dimen_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_dimen_VAL');
				$cus_id = (int)Session::get('customer_id');
				$cus_name = mysql_escape_special_chars(Input::get('cus_name'));
				$cus_email = mysql_escape_special_chars(Input::get('cus_email'));
				$cus_phone1 = mysql_escape_special_chars(Input::get('cus_phone1'));
				$cus_phone2 = mysql_escape_special_chars(Input::get('cus_phone2'));
				$cus_address = mysql_escape_special_chars(Input::get('cus_address'));
				$cus_lat = mysql_escape_special_chars(Input::get('cus_lat'));
				$cus_long = mysql_escape_special_chars(Input::get('cus_long'));
				if($cus_id != '')
				{
					$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_id','!=',$cus_id)->where('cus_status','!=','2')->count();
					$check_phoneNumber_already_exsist = DB::table('gr_customer')->where('cus_phone1','=',$cus_phone1)->where('cus_id','!=',$cus_id)->where('cus_status','!=','2')->count();
					$validator = Validator::make($request->all(), [
					'cus_name' => 'required',
					'cus_email' => 'required',
					'cus_phone1' => 'required|only_cnty_code',
					//'cus_alt_phone' =>'required',
					'cus_address' =>'required',
					'cus_lat' => 'required',
					'cus_long' => 'required',
					'cus_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300'
		        	],[
					'cus_name.required'=>$cus_name_req_err_msg,
					'cus_email.required'=>$cus_email_req_err_msg,
					'cus_email.email'=>$cus_valid_email_err_msg,
					'cus_email.unique'=>$cus_unique_email_err_msg,
					'cus_phone1.only_cnty_code'=>$cus_phone_req_err_msg,
					//'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
					'cus_address.required'=>$cus_addr_req_err_msg,
					'cus_pass.required'=>$cus_pass_req_err_msg,
					'cus_image.required'=>$cus_img_req_err_msg,
					'cus_image.dimensions'=>$cus_img_dimen_err_msg,
                    ]);
					}else{
					$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_status','!=','2')->count();
					$check_phoneNumber_already_exsist = DB::table('gr_customer')->where('cus_phone1','=',$cus_phone1)->where('cus_status','!=','2')->count();
					
					$validator = Validator::make($request->all(), [
					'cus_name' => 'required',
					'cus_email' => 'required|email|unique:gr_customer',
					'cus_phone1' => 'required|only_cnty_code',
					//'cus_alt_phone' =>'required',
					'cus_address' =>'required',
					'cus_lat' => 'required',
					'cus_long' => 'required',
					'cus_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300'
		        	],[
					'cus_name.required'=>$cus_name_req_err_msg,
					'cus_email.required'=>$cus_email_req_err_msg,
					'cus_email.email'=>$cus_valid_email_err_msg,
					'cus_email.unique'=>$cus_unique_email_err_msg,
					'cus_phone1.only_cnty_code'=>$cus_phone_req_err_msg,
					// 'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
					'cus_address.required'=>$cus_addr_req_err_msg,
					'cus_pass.required'=>$cus_pass_req_err_msg,
					'cus_image.required'=>$cus_img_req_err_msg,
					'cus_image.dimensions'=>$cus_img_dimen_err_msg,
                    ]);
				}
				if ($validator->fails()) {
		            return redirect('customer_profile')
					->withErrors($validator)
					->withInput();
					}else{
					
					
					if($check_already_exsist > 0 )
					{
						return redirect()->back()->withErrors(['errors' => 'Email Already Exists!']);
						exit;
					}
					elseif($check_phoneNumber_already_exsist >  0 )
					{
						return redirect()->back()->withErrors(['errors' => 'Phone Number Already Exists!']);
						exit;
					}
					
					
					
					if($request->hasFile('cus_image')) {
						$cus_image = 'customer'.time().'.'.request()->cus_image->getClientOriginalExtension();
						$destinationPath = public_path('images/customer');
						$customer = Image::make(request()->cus_image->getRealPath())->resize(300, 300);
						$customer->save($destinationPath.'/'.$cus_image,80);
						
						$insertArr = array(
						'cus_fname' => $cus_name,
						'cus_email' => $cus_email,
						'cus_phone1' => $cus_phone1,
						'cus_phone2' => $cus_phone2,
						'cus_address' => $cus_address,
						'cus_latitude' => $cus_lat,
						'cus_longitude' => $cus_long,
						'cus_image' => $cus_image,
						);  
						}else{
						$insertArr = array(
						'cus_fname' => $cus_name,
						'cus_email' => $cus_email,
						'cus_phone1' => $cus_phone1,
						'cus_phone2' => $cus_phone2,
						'cus_address' => $cus_address,
						'cus_latitude' => $cus_lat,
						'cus_longitude' => $cus_long,               
						);
					}
					$update = updatevalues('gr_customer',$insertArr,['cus_id' =>$cus_id]);
					$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					Session::flash('success',$msg);
					return Redirect::to('customer_profile');
				}
			}
			else{
				return Redirect::to('/');
			}
		}
		
		public function shipping_address()
		{
			if(Session::has('customer_id') == 1)
			{
				$cus_id = Session::get('customer_id');
				$sel = DB::table('gr_shipping')->where('sh_cus_id',$cus_id)->first();
				return view('Front.shipping_address',['customer_detail'=>$sel,'id'=>$cus_id]);
			}
			else{
				return Redirect::to('/');
			}
		}
		
		public function customer_shipping_update(Request $request)
		{
			if(Session::has('customer_id') == 1)
			{
				$cus_fname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_FIRSTNAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_FIRSTNAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_FIRSTNAME_VAL');
				$cus_lname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_SECONDNAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_SECONDNAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_SECONDNAME_VAL');
				$cus_email_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL');
				$cus_valid_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTR_MAIL');
				$cus_unique_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
				$cus_phone_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE');
				$cus_addr_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_ADDR_VAL');
				
				$cus_lat_req_err_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_LAT_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_LAT_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_LAT_VAL');
				
				$cus_long_req_err_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_LONG_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_LONG_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_LONG_VAL');
				
				$cus_zip_req_err_msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CUSTOMER_ZIP_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_CUSTOMER_ZIP_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_CUSTOMER_ZIP_VAL');
				
				$cus_id = mysql_escape_special_chars(Input::get('sh_cus_id'));
				if($cus_id != '')
				{
					$validator = Validator::make($request->all(), [
					
					'cus_fname' => 'required',
					
					'cus_lname' => 'required',
					
					'cus_email' => 'required|email',//|unique:gr_shipping,sh_cus_email,'.$cus_id.',sh_cus_id',
					
					'prof_cus_phone' => 'required|only_cnty_code',
					
					//'cus_alt_phone' =>'required',
					
					'cus_address' =>'required',
					
					'cus_lat' => 'required',
					
					'cus_long' => 'required',
					
					'sh_zipcode' => 'required',
					
		        	],[
					'cus_fname.required'=>$cus_fname_req_err_msg,
					'cus_lname.required'=>$cus_lname_req_err_msg,
					'cus_email.required'=>$cus_email_req_err_msg,
					'cus_email.email'=>$cus_valid_email_err_msg,
					'cus_email.unique'=>$cus_unique_email_err_msg,
					'prof_cus_phone.required'=>$cus_phone_req_err_msg,
					'prof_cus_phone.only_cnty_code'=>$cus_phone_req_err_msg,
					//'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
					'cus_address.required'=>$cus_addr_req_err_msg,
					'cus_lat.required'=>$cus_lat_req_err_msg,
					'cus_long.required'=>$cus_long_req_err_msg,
					'sh_zipcode.required'=>$cus_zip_req_err_msg,
                    ]);
					}else{
					$validator = Validator::make($request->all(), [
					
					'cus_fname' => 'required',
					
					'cus_lname' => 'required',
					
					'cus_email' => 'required|email',//|unique:gr_customer',
					
					'prof_cus_phone' => 'required',
					
					//'cus_alt_phone' =>'required',
					
					'cus_address' =>'required',
					
					'cus_lat' => 'required',
					
					'cus_long' => 'required',
					
					'sh_zipcode' => 'required'
		            
		        	],[
					'cus_fname.required'=>$cus_fname_req_err_msg,
					'cus_lname.required'=>$cus_lname_req_err_msg,
					'cus_email.required'=>$cus_email_req_err_msg,
					'cus_email.email'=>$cus_valid_email_err_msg,
					// 'cus_email.unique'=>$cus_unique_email_err_msg,
					'prof_cus_phone.required'=>$cus_phone_req_err_msg,
					//'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
					'cus_address.required'=>$cus_addr_req_err_msg,
					'cus_lat.required'=>$cus_lat_req_err_msg,
					'cus_long.required'=>$cus_long_req_err_msg,
					'sh_zipcode.required'=>$cus_zip_req_err_msg,
                    ]);
				}
				if ($validator->fails()) {
		            return redirect('shipping_address')
					->withErrors($validator)
					->withInput();
				}
				else
				{
					$cus_fname = mysql_escape_special_chars(Input::get('cus_fname'));
					$cus_lname = mysql_escape_special_chars(Input::get('cus_lname'));
					$cus_email  = mysql_escape_special_chars(Input::get('cus_email'));
					$sh_building_no  = mysql_escape_special_chars(Input::get('sh_building_no'));
					$prof_cus_phone = mysql_escape_special_chars(Input::get('prof_cus_phone'));
					$cus_alt_phone = mysql_escape_special_chars(Input::get('cus_alt_phone'));
					$cus_address = mysql_escape_special_chars(Input::get('cus_address'));
					$cus_lat = mysql_escape_special_chars(Input::get('cus_lat'));
					$cus_long = mysql_escape_special_chars(Input::get('cus_long'));
					$sh_zipcode = mysql_escape_special_chars(Input::get('sh_zipcode'));
					
					//echo $sh_building_no; exit;
					if($cus_id != ''){
						$insertArr = array(
						'sh_cus_fname' => $cus_fname,
						'sh_cus_lname' => $cus_lname,
						'sh_cus_email' => $cus_email,
						'sh_building_no'=>$sh_building_no,
						'sh_phone1' => $prof_cus_phone,
						'sh_phone2' => $cus_alt_phone,
						'sh_location' => $cus_address,
						'sh_latitude' => $cus_lat,
						'sh_longitude' => $cus_long,
						'sh_zipcode' => $sh_zipcode,
						'sh_cus_id'=>$cus_id             
						);
						$update = updatevalues('gr_shipping',$insertArr,['sh_cus_id' =>$cus_id]);
						$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
						}else{
						$insertArr = array(
						'sh_cus_fname' => $cus_fname,
						'sh_cus_lname' => $cus_lname,
						'sh_cus_email' => $cus_email,
						'sh_building_no'=>$sh_building_no,
						'sh_phone1' => $prof_cus_phone,
						'sh_phone2' => $cus_alt_phone,
						'sh_location' => $cus_address,
						'sh_latitude' => $cus_lat,
						'sh_longitude' => $cus_long,
						'sh_zipcode' => $sh_zipcode,
						'sh_cus_id'=> Session::get('customer_id')           
						);
						$insert = insertvalues('gr_shipping',$insertArr);
						$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('front_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					}
					Session::flash('success',$msg);
					return Redirect::to('shipping_address');
				}
				
			}
			else
			{
				return Redirect::to('/');
			}
		}
		
		public function merchant_signup()
		{
			return view('Front.merchant_signup',[]);
		}
		public function random_password( $length = 8 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		public function merchant_signup_submit(Request $request)
		{
			$mer_fname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_FIRST_NAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_FIRST_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_FIRST_NAME_VAL');
			$mer_lname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_LAST_NAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_LAST_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_LAST_NAME_VAL');
            $mer_email_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL');
            $mer_valid_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTR_MAIL');
            $mer_unique_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
            $mer_phone_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE');
            $mer_bussiness_type_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_BUSINESS_TYPE_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_BUSINESS_TYPE_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_BUSINESS_TYPE_VAL');
            
			
			$validator = Validator::make($request->all(), [
			'mer_fname' => 'required',
			'mer_lname' => 'required',
			'mer_email'  => [
			'required', 
			Rule::unique('gr_merchant')->where(function ($query) use ($request) {
				return $query->where('gr_merchant.mer_status','<>','2');
			}),
			],
			//'mer_email' => 'required|email|unique:gr_merchant',//|unique:gr_customer',
			
			//'mer_phone' => 'required',
			'mer_phone'  => [
			'only_cnty_code', 
			Rule::unique('gr_merchant')->where(function ($query) use ($request) {
				return $query->where('gr_merchant.mer_status','<>','2');
			}),
			],
			//'cus_alt_phone' =>'required',
			
			'mer_business_type' =>'required'
			
			],[
			'mer_fname.required'=>$mer_fname_req_err_msg,
			'mer_lname.required'=>$mer_lname_req_err_msg,
			'mer_email.required'=>$mer_email_req_err_msg,
			'mer_email.email'=>$mer_valid_email_err_msg,
			'mer_email.unique'=>$mer_unique_email_err_msg,
			'mer_phone.only_cnty_code'=>$mer_phone_req_err_msg,
			//'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
			'mer_business_type.required'=>$mer_bussiness_type_req_err_msg,
			
			]);
			
			if ($validator->fails()) {
				
				//print_r($validator->errors()); exit();
				return redirect('merchant_signup')->withErrors($validator->errors())->withInput();
				
				}else{
		    	$passwordIs = $this->random_password(6);
		    	$mer_fname = mysql_escape_special_chars(Input::get('mer_fname'));
		    	$mer_lname = mysql_escape_special_chars(Input::get('mer_lname'));
		    	$mer_email = mysql_escape_special_chars(Input::get('mer_email'));
		    	$mer_phone = mysql_escape_special_chars(Input::get('mer_phone'));
		    	$mer_business_type = mysql_escape_special_chars(Input::get('mer_business_type'));
				
		    	$insertArr = array(
				'mer_fname' => $mer_fname,
				'mer_lname' => $mer_lname,
				'mer_email' => $mer_email,
				'mer_password' => md5($passwordIs),
				'mer_decrypt_password' => $passwordIs,
				'mer_phone' => $mer_phone,
				'mer_business_type' => $mer_business_type,
				'addedby'	=> '1',
				'mer_status' => '0',
				'mer_currency_code'=> Input::get('currency_code'),
				'mer_commission'=> Config::get('COMMON_COMMI'),
				'mer_newly_register'	=>'1'
		    	);
		    	$insert = insertvalues('gr_merchant',$insertArr);
				
		    	//----MAIL FUNCTION 
				$send_mail_data = array('name' => Input::get('mer_fname').' '.Input::get('mer_lname'),
				'password' => $passwordIs,
				'email' => Input::get('mer_email'),
				'commission' => Config::get('COMMON_COMMI')
				);
				Mail::send('email.front_merchant_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('mer_email');
					$name                = Input::get('mer_fname').' '.Input::get('mer_lname');
					$subject = (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
				// EOF MAIL FUNCTION *
		    	$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED')) ? trans(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED') : trans($this->FRONT_LANGUAGE.'.FRONT_MERCHANT_ADDED');
		    	Session::flash('success',$msg);
                return Redirect::to('merchant_signup');
			}
		}
		
		public function mer_signup_with_otp()
		{
			$mer_fname = mysql_escape_special_chars(Input::get('mer_fname'));
			$mer_lname = mysql_escape_special_chars(Input::get('mer_lname'));
			$mer_phone = mysql_escape_special_chars(Input::get('mer_phone'));
			$mer_email = mysql_escape_special_chars(Input::get('mer_email'));
			$business_type = mysql_escape_special_chars(Input::get('business_type'));
			
			$cur_code  = Input::get('cur_code');
			$otp = mt_rand(100000, 999999);
			$passwordIs = $this->random_password(6);
			$check_already_exsist = DB::table('gr_merchant')->where('mer_email','=',$mer_email)->where('mer_status','!=','2')->count();
			if($check_already_exsist > 0)
			{
				$jsonArr = array('msg'=>'Merchant Already Available!');
				echo json_encode($jsonArr);
				}else{	
				$detArr = array(
				'mer_fname'=>$mer_fname,
				'mer_lname'=>$mer_lname,
				'mer_phone'=>$mer_phone,
				'mer_email'=>$mer_email,
				'mer_password' => $passwordIs,				
				'mer_business_type' => $business_type,
				'mer_currency_code'=> $cur_code,
				);
				Session::put('merchant_details',$detArr);
				
				try{
					Twilio::message($mer_phone, $otp);
					$jsonArr = array('msg'=>'Success','otp'=>$otp);
					echo json_encode($jsonArr);
					
				}
				catch (\Exception $e)
				{		
					
					$jsonArr = array('msg'=>$e->getMessage());
					echo json_encode($jsonArr);
				}
			}
		}
		
		public function mer_check_otp()
		{	
			$entered_otp = mysql_escape_special_chars(Input::get('otp'));
			$current_otp = mysql_escape_special_chars(Input::get('current_otp'));
			
			if($entered_otp == $current_otp){
				$sessionArr = Session::get('merchant_details');
				
				$detArr = array(
				'mer_fname'=>$sessionArr['mer_fname'],
				'mer_lname'=>$sessionArr['mer_lname'],
				'mer_phone'=>$sessionArr['mer_phone'],
				'mer_email'=>$sessionArr['mer_email'],
				'mer_password'=>md5($sessionArr['mer_password']),				
				'mer_decrypt_password'=>$sessionArr['mer_password'],				
				'mer_business_type' => $sessionArr['mer_business_type'],
				'addedby'	=> '1',
				'mer_status' => '0',
				'mer_currency_code'=> $sessionArr['mer_currency_code'],
				'mer_commission'=> Config::get('COMMON_COMMI')
				);
				$insert = DB::table('gr_merchant')->insert($detArr);
				$lastinsertid = DB::getPdo()->lastInsertId();
				/*MAIL FUNCTION */			
				
				$send_mail_data = array('name' => $sessionArr['mer_fname'].' '.$sessionArr['mer_lname'],
				
				'password' => $sessionArr['mer_password'],
				'email' => $sessionArr['mer_email'],
				'commission' => Config::get('COMMON_COMMI')
				);				
				
				Mail::send('email.front_merchant_register_email', $send_mail_data, function($message)
				{
					$sessionArr = Session::get('merchant_details');
					$email               = $sessionArr['mer_email'];
					$name       = $sessionArr['mer_fname'].' '.$sessionArr['mer_lname'];				
					
					$subject = (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_REG_DETAILS');
					
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				
				
                $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED')) ? trans(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED') : trans($this->FRONT_LANGUAGE.'.FRONT_MERCHANT_ADDED');
				
                Session::flash('success',$msg);
				echo 'match';
				}else{
				echo 'wrong';
			}
		}

		/* delivery person signup */
		public function delivery_signup()
		{
			return view('Front.delivery_person_signup',[]);
		}

		public function del_signup_with_otp()
		{
			$mer_fname = mysql_escape_special_chars(Input::get('mer_fname'));
			$mer_lname = mysql_escape_special_chars(Input::get('mer_lname'));
			$mer_phone = mysql_escape_special_chars(Input::get('mer_phone'));
			$mer_email = mysql_escape_special_chars(Input::get('mer_email'));
			$business_type = mysql_escape_special_chars(Input::get('vehicle_option'));
			$cur_code  = mysql_escape_special_chars(Input::get('cur_code'));
			//$or_limit  = Input::get('or_limit');

			$mer_fname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_FIRST_NAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_FIRST_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_FIRST_NAME_VAL');
			$mer_lname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_LAST_NAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_LAST_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_LAST_NAME_VAL');
            $mer_email_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL');
            $mer_valid_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTR_MAIL');
            $mer_unique_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
            $mer_phone_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE');
            $mer_bussiness_type_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_SL_VEHICLE_TYPE')) ? trans(Session::get('front_lang_file').'.FRONT_SL_VEHICLE_TYPE') : trans($this->FRONT_LANGUAGE.'.FRONT_SL_VEHICLE_TYPE');
            $mer_or_limit_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_EN_NO_OF_OR_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EN_NO_OF_OR_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EN_NO_OF_OR_LIMIT');
            
			
			$validator = Validator::make($request->all(), [
			'mer_fname' => 'required',
			'mer_lname' => 'required',
			'mer_email'  => [
			'required', 
			Rule::unique('gr_delivery_member','deliver_email')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'mer_email' => 'required|email|unique:gr_merchant',//|unique:gr_customer',
			
			//'mer_phone' => 'required',
			'mer_phone'  => [
			'only_cnty_code', 
			Rule::unique('gr_delivery_member','deliver_phone1')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'mer_or_limit' =>'required',
			
			'vehicle_option' =>'required'
			
			],[
			'mer_fname.required'=>$mer_fname_req_err_msg,
			'mer_lname.required'=>$mer_lname_req_err_msg,
			'mer_email.required'=>$mer_email_req_err_msg,
			'mer_email.email'=>$mer_valid_email_err_msg,
			'mer_email.unique'=>$mer_unique_email_err_msg,
			'mer_phone.only_cnty_code'=>$mer_phone_req_err_msg,
			//'mer_or_limit.required'=>$mer_or_limit_err_msg,
			'vehicle_option.required'=>$mer_bussiness_type_req_err_msg,
			
			]);
			
			if ($validator->fails()) 
			{
				
				//print_r($validator->errors()); exit();
				return redirect('delivery-person-signup')->withErrors($validator->errors())->withInput();
				
				}
			$otp = mt_rand(100000, 999999);
			$passwordIs = $this->random_password(6);
			$check_already_exsist = DB::table('gr_delivery_member')->where('deliver_email','=',$mer_email)->where('deliver_status','!=','2')->count();
			if($check_already_exsist > 0)
			{	
				$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
				$jsonArr = array('msg'=>$msg);
				echo json_encode($jsonArr);
			}
			else
			{	
				$detArr = array('mer_fname'=>$mer_fname,
								'mer_lname'=>$mer_lname,
								'mer_phone'=>$mer_phone,
								'mer_email'=>$mer_email,
								'mer_password' => $passwordIs,				
								'mer_business_type' => $business_type,
								'mer_currency_code'=> $cur_code
								//'order_limit'=> $or_limit,
								);
				Session::put('deliver_details',$detArr);
				
				try{
					Twilio::message($mer_phone, $otp);
					$jsonArr = array('msg'=>'Success','otp'=>$otp);
					echo json_encode($jsonArr);
					
				}
				catch (\Exception $e)
				{		
					
					$jsonArr = array('msg'=>$e->getMessage());
					echo json_encode($jsonArr);
				}
			}
		}

		public function del_check_otp()
		{	
			$entered_otp = mysql_escape_special_chars(Input::get('otp'));
			$current_otp = mysql_escape_special_chars(Input::get('current_otp'));
			
			if($entered_otp == $current_otp){
				$sessionArr = Session::get('deliver_details');
				
				$detArr = array('deliver_fname'			=>$sessionArr['mer_fname'],
								'deliver_lname'			=>$sessionArr['mer_lname'],
								'deliver_phone1'		=>$sessionArr['mer_phone'],
								'deliver_email'			=>$sessionArr['mer_email'],
								'deliver_password'		=>md5($sessionArr['mer_password']),				
								'deliver_decrypt_password'=>$sessionArr['mer_password'],				
								'deliver_vehicle_details' => $sessionArr['mer_business_type'],
								'deliver_added_by'				=> '1',
								'deliver_status' 		=> '0',
								'deliver_currency_code'	=> $sessionArr['mer_currency_code'],
								//'deliver_order_limit'	=> $sessionArr['order_limit'],
								'deliver_created_at' 	=> date('Y-m-d H:i:s'),
								'deliver_updated_at' 	=> date('Y-m-d H:i:s'),
								'deliver_agent_id'		=> 0,
								'deliver_newly_register' => '1'
								);
				$insert = DB::table('gr_delivery_member')->insert($detArr);
				$lastinsertid = DB::getPdo()->lastInsertId();
				/*MAIL FUNCTION */			
				
				$send_mail_data = array('name' => $sessionArr['mer_fname'].' '.$sessionArr['mer_lname'],
										'password' => $sessionArr['mer_password'],
										'email' => $sessionArr['mer_email']
										);				
				
				Mail::send('email.front_delboy_register_email', $send_mail_data, function($message)
				{
					$sessionArr = Session::get('deliver_details');
					$email      = $sessionArr['mer_email'];
					$name       = $sessionArr['mer_fname'].' '.$sessionArr['mer_lname'];				
					
					$subject = (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_REG_DETAILS');
					
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				
				
                $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED')) ? trans(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED') : trans($this->FRONT_LANGUAGE.'.FRONT_MERCHANT_ADDED');
				
                Session::flash('success',$msg);
				echo 'match';
				}else{
				echo 'wrong';
			}
		}

		public function delivery_signup_submit(Request $request)
		{
			$mer_fname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_FIRST_NAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_FIRST_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_FIRST_NAME_VAL');
			$mer_lname_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_MER_LAST_NAME_VAL')) ? trans(Session::get('front_lang_file').'.FRONT_MER_LAST_NAME_VAL') : trans($this->FRONT_LANGUAGE.'.FRONT_MER_LAST_NAME_VAL');
            $mer_email_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL');
            $mer_valid_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTR_MAIL');
            $mer_unique_email_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
            $mer_phone_req_err_msg=(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE');
            $mer_bussiness_type_req_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_SL_VEHICLE_TYPE')) ? trans(Session::get('front_lang_file').'.FRONT_SL_VEHICLE_TYPE') : trans($this->FRONT_LANGUAGE.'.FRONT_SL_VEHICLE_TYPE');
            $mer_or_limit_err_msg=(Lang::has(Session::get('front_lang_file').'.FRONT_EN_NO_OF_OR_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EN_NO_OF_OR_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EN_NO_OF_OR_LIMIT');
            
			
			$validator = Validator::make($request->all(), [
			'mer_fname' => 'required',
			'mer_lname' => 'required',
			'mer_email'  => [
			'required', 
			Rule::unique('gr_delivery_member','deliver_email')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'mer_email' => 'required|email|unique:gr_merchant',//|unique:gr_customer',
			
			//'mer_phone' => 'required',
			'mer_phone'  => [
			'only_cnty_code', 
			Rule::unique('gr_delivery_member','deliver_phone1')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'mer_or_limit' =>'required',
			
			'vehicle_option' =>'required'
			
			],[
			'mer_fname.required'=>$mer_fname_req_err_msg,
			'mer_lname.required'=>$mer_lname_req_err_msg,
			'mer_email.required'=>$mer_email_req_err_msg,
			'mer_email.email'=>$mer_valid_email_err_msg,
			'mer_email.unique'=>$mer_unique_email_err_msg,
			'mer_phone.only_cnty_code'=>$mer_phone_req_err_msg,
			//'mer_or_limit.required'=>$mer_or_limit_err_msg,
			'vehicle_option.required'=>$mer_bussiness_type_req_err_msg,
			
			]);
			
			if ($validator->fails()) {
				
				//print_r($validator->errors()); exit();
				return redirect('delivery-person-signup')->withErrors($validator->errors())->withInput();
				
				}else{
		    	$passwordIs = $this->random_password(6);
		    	$mer_fname = mysql_escape_special_chars(Input::get('mer_fname'));
		    	$mer_lname = mysql_escape_special_chars(Input::get('mer_lname'));
		    	$mer_email = mysql_escape_special_chars(Input::get('mer_email'));
		    	$mer_phone = mysql_escape_special_chars(Input::get('mer_phone'));
		    	$mer_business_type = mysql_escape_special_chars(Input::get('vehicle_option'));
				
		    	$insertArr = array(
				'deliver_fname' => $mer_fname,
				'deliver_lname' => $mer_lname,
				'deliver_email' => $mer_email,
				'deliver_password' => md5($passwordIs),
				'deliver_decrypt_password' => $passwordIs,
				'deliver_phone1' => $mer_phone,
				'deliver_vehicle_details' => $mer_business_type,
				'deliver_added_by'	=> '1',
				'deliver_status' => '0',
				'deliver_currency_code'=> Input::get('currency_code'),
				//'deliver_order_limit' => Input::get('mer_or_limit'),
				'deliver_agent_id'		=> 0,
				'deliver_newly_register' => '1',
				'deliver_created_at' 	=> date('Y-m-d H:i:s'),
				'deliver_updated_at' 	=> date('Y-m-d H:i:s')
		    	);
		    	$insert = insertvalues('gr_delivery_member',$insertArr);
				
		    	//----MAIL FUNCTION 
				$send_mail_data = array('name' => Input::get('mer_fname').' '.Input::get('mer_lname'),
				'password' => $passwordIs,
				'email' => Input::get('mer_email')
				);
				Mail::send('email.front_delboy_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('mer_email');
					$name                = Input::get('mer_fname').' '.Input::get('mer_lname');
					$subject = (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
				// EOF MAIL FUNCTION *
		    	$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED')) ? trans(Session::get('front_lang_file').'.FRONT_MERCHANT_ADDED') : trans($this->FRONT_LANGUAGE.'.FRONT_MERCHANT_ADDED');
		    	Session::flash('success',$msg);
                return Redirect::to('delivery-person-signup');
			}
		}

	}		