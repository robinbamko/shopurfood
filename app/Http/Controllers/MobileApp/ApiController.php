<?php
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\MobileApp;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Http\Requests;
	use Config;
	use JWTAuth;
	use JWTAuthException;
	use App\User;
	use App\Agent;
	use App\Merchant;
	use App\MobileModel;
	use DB;
	use View;
	use Illuminate\Validation\Rule;
	use Validator;
	use Tymon\JWTAuth\Exceptions\JWTException;
	use Illuminate\Support\Facades\Mail;
	use Response;
	class ApiController extends Controller
	{
		private $empty_data;
		public function __construct()
		{	
			parent::__construct();
			$this->user = new User;
			$this->agent = new Agent;
			$this->merchant = new Merchant;
			$this->empty_data = array();
			/*  get general details */
			$details = DB::table('gr_general_setting')->first();
			if(!empty($details))
			{
				$this->site_name = $details->gs_sitename;
			}
			else
			{
				$this->site_name = "Shopurfood";
			}
			/*LANGUAGE SETTINGS */
			$this->default_mob_lang_code  = 'en';
			$this->admin_default_lang = DB::table('gr_language')->select('lang_code')->where('default_lang','1')->where('status','1')->first()->lang_code;
			/* EOF LANGUAGE SETTINGS */
			
			/*GENERAL DETAILS*/
			$this->general_setting = DB::table('gr_general_setting')->first();
			if(!empty($this->general_setting)){
				View::share("SITENAME",$this->general_setting->gs_sitename);
				View::share("FOOTERNAME",$this->general_setting->footer_text);	 
				}else{
				View::share("SITENAME","Shopurfood");
				View::share("FOOTERNAME","Shopurfood");
			}
			/*LOGO DETAILS */
			$logo_settings_details = DB::table('gr_logo_settings')->get();
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
			View::share("LOGOPATH",$path);
			/* EOF GENERAL DETAILS */
		}
		function whatever($array, $key, $val) 
		{
				if(!empty($array))
				{
					foreach ($array as  $item) 
			       if ((isset($item[$key])) && $item[$key] == $val)
			           return true;
					return false;
				}
				else
				{
					return false;
				}
		}
		public function userLogin(Request $request){
			//print_r($request->all()); exit;
			Config::set('jwt.user', 'App\User'); 
			Config::set('auth.providers.users.model', \App\User::class);
			Config::set('jwt.userpassword','cus_password');
			$login_id = $request->login_id; 
			$password = $request->cus_password; 
			$lang 	  = $request->lang;
			$cus_email= $request->login_id;
			$andr_fcm_id = $request->andr_fcm_id;
			$andr_device_id = $request->andr_device_id;
			$ios_fcm_id  = $request->ios_fcm_id;
			$ios_device_id  = $request->ios_device_id;
			$ph_type     = $request->type; // use android or ios
			/* ----------VALIDATION STARTS HERE--------------- */
			
			$pwd_req_err_msg=MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$login_id))
			{	
				$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required|string|email', 'cus_password' => 'required'],
				[ 'login_id.required'=>$email_req_err_msg,'login_id.email'=>$valid_email_err_msg,'cus_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ['code' => 400,'message'  => $message,'data'=>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			elseif(preg_match('/^[0-9+]+/i', $login_id)){
				$phone_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required', 'cus_password' => 'required'],
				[ 'login_id.required'=>$phone_req_err_msg,'cus_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}else{
					$cus_email_res = DB::table('gr_customer')->select('cus_email')->where('cus_phone1','=',$login_id)->where('cus_status','=','1')->first();
					if(empty($cus_email_res)===true){
						$message = MobileModel::get_lang_text($lang,'API_PHONENUM_DOESNOT_EXISTS','Phone Number doesn\'t Exists!');
						$encode = ['code' => 400,'message'  => $message,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
						}else{
						$cus_email = $cus_email_res->cus_email;
					}
				}
			}
			else{
				$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required|string|email', 'cus_password' => 'required'],
				[ 'login_id.required'=>$email_req_err_msg,'login_id.email'=>$valid_email_err_msg,'cus_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = [ 'code' => 400,'message'  => $message,'data'=>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			$credentials = $request->only('cus_password');
			$credentials['cus_email'] = trim($cus_email);
			$credentials['cus_status'] = '1';
			$token = null;
			try {
				if (!$token = JWTAuth::attempt($credentials)) {
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data'=>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
				} catch (JWTAuthException $e) {
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
				$encode = ['code' => 400,'message'  => $msg,'data'=>$this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				
			}
			$user = JWTAuth::user();
			$cus_logged_ip = $request->ip();
			$cus_loggedin_time = date('Y-m-d H:i:s');
			$fcm_id_array = array();
			$update_andr_arr =$new_arr=  array();
			$update_ios_arr = array();
			if($ph_type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
			{	
				$andr_fcm_arr = json_decode($user->cus_andr_fcm_id,true);
				if($this->whatever($andr_fcm_arr,'device_id',$andr_device_id))
				{

				}
				else
				{
					$andr_fcm_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
				}
				
				$unique_array_res = $andr_fcm_arr;
				
				foreach($unique_array_res as $arr)
				{
					
					if($arr['device_id'] == $andr_device_id)
					{ 
						$update_andr_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					}
					else
					{  
						$update_andr_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
					}
				}
				
				$fcm_id_array['cus_andr_fcm_id'] = json_encode($update_andr_arr);
				
			}
			elseif($ph_type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
			{	
				$ios_fcm_arr = json_decode($user->cus_ios_fcm_id,true);
				
				if($this->whatever($ios_fcm_arr,'device_id',$ios_device_id))
				{

				}
				else
				{
					$ios_fcm_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
				}
				
				$unique_array_res = $ios_fcm_arr;
				
				foreach($unique_array_res as $arr)
				{
					
					if($arr['device_id'] == $ios_device_id)
			{
						$update_ios_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
			}
					else
			{
						$update_ios_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
					}
				}
				$fcm_id_array['cus_ios_fcm_id'] = json_encode($update_ios_arr);	
			}
			$updatableArray = array_merge(array('cus_logged_ip'		=>$cus_logged_ip,
			'cus_loggedin_time'	=>$cus_loggedin_time),$fcm_id_array);
			updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $cus_email]);
			/* ------------------RETRIVE USER DETAILS----------------------- */
			
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			$data = ['token'			=> $token,
			"user_id"		=> intval($user->cus_id),
			"user_name"		=> ucfirst($user->cus_fname),
			"user_email"	=> $user->cus_email,
			"user_phone"	=> $user->cus_phone1,
			"user_login_type"=>intval($user->cus_login_type),
			"user_status"	=> intval($user->cus_status)];
			$encode = ['code'			=> 200,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		public function userLogin_email(Request $request){
			//print_r($request->all()); exit;
			Config::set('jwt.user', 'App\User'); 
			Config::set('auth.providers.users.model', \App\User::class);
			Config::set('jwt.userpassword','cus_password');
			$email = $request->cus_email; 
			$password = $request->cus_password; 
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$pwd_req_err_msg=MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			
			$validator = Validator::make($request->all(), 
			[ 'cus_email' => 'required|string|email', 'cus_password' => 'required'],
			[ 'cus_email.required'=>$email_req_err_msg,'cus_email.email'=>$valid_email_err_msg,'cus_password.required'=>$pwd_req_err_msg]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			$credentials = $request->only('cus_email', 'cus_password');
			$credentials['cus_status'] = '1';
			$token = null;
			try {
				if (!$token = JWTAuth::attempt($credentials)) {
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
				} catch (JWTAuthException $e) {
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
		    	$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				
			}
			$cus_logged_ip = $request->ip();
			$cus_loggedin_time = date('Y-m-d H:i:s');
			$updatableArray = array('cus_logged_ip'=>$cus_logged_ip,'cus_loggedin_time'=>$cus_loggedin_time);
			updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $email]);
			/* ------------------RETRIVE USER DETAILS----------------------- */
			$user = JWTAuth::user();
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			$data = ['token'			=> $token,
			"user_id"		=> intval($user->cus_id),
			"user_name"		=> ucfirst($user->cus_fname),
			"user_email"	=> $user->cus_email,
			"user_phone"	=> $user->cus_phone1,
			"user_login_type"=> intval($user->cus_login_type),
			"user_status"	=> intval($user->cus_status)];
			$encode = ['code'			=>200,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			
		}
		
		public function agent_login(Request $request){
			Config::set('jwt.user', 'App\Agent'); 
			Config::set('auth.providers.users.model', \App\Agent::class);
			Config::set('jwt.userpassword','agent_password');
			
			$login_id = $request->login_id; 
			$password = $request->agent_password; 
			$agent_email = $request->login_id; 
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			
			$pwd_req_err_msg=MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$login_id))
			{
				$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required|string|email', 'agent_password' => 'required'],
				[ 'login_id.required'=>$email_req_err_msg,'login_id.email'=>$valid_email_err_msg,'agent_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ['code'=>400,'message'=>$message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					
				}	
			}
			elseif(preg_match('/^[0-9+]+/i', $login_id)){
				$phone_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required', 'agent_password' => 'required'],
				[ 'login_id.required'=>$phone_req_err_msg,'agent_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}else{
					$agent_email_res = DB::table('gr_agent')->select('agent_email')->where('agent_phone1','=',$login_id)->where('agent_status','=','1')->first();
					if(empty($agent_email_res)===true){
						$message = MobileModel::get_lang_text($lang,'API_PHONENUM_DOESNOT_EXISTS','Phone Number doesn\'t Exists!');
						$encode = ["code"=>400,"message"=>$message,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
						}else{
						$agent_email = $agent_email_res->agent_email;
					}
				}
				}else{
				$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required|string|email', 'agent_password' => 'required'],
				[ 'login_id.required'=>$email_req_err_msg,'login_id.email'=>$valid_email_err_msg,'agent_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}	
			}
			$credentials = $request->only('agent_password');
			$credentials['agent_email'] = trim($agent_email);
			$credentials['agent_status'] = '1';
			$token = null;
			try {
				if (!$token = JWTAuth::attempt($credentials)) {
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
			} 
			catch (JWTAuthException $e) {
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
				$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
			}
			/* ------------------RETRIVE USER DETAILS----------------------- */
			$user = JWTAuth::user();
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			$data = ['token'			=> $token,
			"agent_id"		=> intval($user->agent_id),
			"agent_name"	=> ucfirst($user->agent_fname).' '.$user->agent_lname,
			"agent_email"	=> $user->agent_email,
			"agent_phone"	=> $user->agent_phone1,
			"agent_status"	=> intval($user->agent_status)];
			$encode = ['code'			=> 200,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");	
		}
		public function random_password( $length = 8 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		/******************************* FACEBOOK LOGIN ****************************/
		public function facebook_login(Request $request){
			//print_r($request->all()); exit;
			Config::set('jwt.user', 'App\User'); 
			Config::set('auth.providers.users.model', \App\User::class);
			Config::set('jwt.userpassword','cus_password');
			$name  = $request->name; 
			$email = $request->email; 
			$facebook_id = $request->facebook_id; 
			$passwordIs = $this->random_password(6);
			$andr_fcm_id = $request->andr_fcm_id;
			$andr_device_id = $request->andr_device_id;
			$ios_fcm_id  = $request->ios_fcm_id;
			$ios_device_id  = $request->ios_device_id;
			$ph_type     = $request->type; // use android or ios
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$name_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$facebook_id_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_FACEBOOK_ID','Please enter Facebook ID!');
			
			$validator = Validator::make($request->all(), 
			[ 'name' => 'required','email' => 'required|string|email', 'facebook_id' => 'required'],
			[ 'name.required'=>$name_req_err_msg,'cus_email.required'=>$email_req_err_msg,'cus_email.email'=>$valid_email_err_msg,'facebook_id.required'=>$facebook_id_err_msg]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = ["code"=>400,"message"=>$message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			//CHECK ALREADY EMAIL AVAILABLE IN CUSTOMER TABLE, IF AVAILABLE THEN UPDATE FACEBOOK ID ELSE INSERT.
			$cus_login_exist = DB::table('gr_customer')->where('cus_email','=',$email)->where('cus_status','!=','2')->first();
			$cus_logged_ip = $request->ip();
			$cus_loggedin_time = date('Y-m-d H:i:s');
			
			
			if(!empty($cus_login_exist)==false){
				$insertArr = array(	'cus_fname' 	=> $name,
				'cus_email' 	=> $email,
				'cus_password' 	=> md5($passwordIs),
				'cus_decrypt_password' 	=> $passwordIs,
				'cus_login_type'=> '3',
				'cus_status' 	=> '1',
				'cus_paynamics_status'	=>'Unpublish',
				'cus_paymaya_status'	=>'Unpublish',
				'cus_netbank_status'	=>'Unpublish',
				'cus_created_date' 		=> date('Y-m-d'),
				'cus_logged_ip'			=>$cus_logged_ip,
				'cus_loggedin_time'		=>$cus_loggedin_time
				);
				if($ph_type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
				{
					$andr_fcm_arr1[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					$insertArr['cus_andr_fcm_id'] = json_encode($andr_fcm_arr1);
				}
				elseif($ph_type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
				{
					$ios_fcm_arr1[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
					$insertArr['cus_ios_fcm_id'] = json_encode($ios_fcm_arr1);	
				}
				
				$insert = insertvalues('gr_customer',$insertArr);
			}
			else{
				$updatableArray = array();
				$updatableArray['cus_password']=md5($passwordIs);
				$updatableArray['cus_decrypt_password']=$passwordIs;
				$updatableArray['cus_facebook_id'] = $facebook_id;
				$updatableArray['cus_logged_ip'] = $cus_logged_ip;
				$updatableArray['cus_loggedin_time'] = $cus_loggedin_time;		
				updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $email]);
			}
			
			$credentials['cus_email'] = $email;
			$credentials['cus_password'] = $passwordIs;
			$credentials['cus_status'] = '1';
			$token = null;
			try {
				if (!$token = JWTAuth::attempt($credentials)) {
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
				} catch (JWTAuthException $e) {
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
		    	$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				
			}
			/*MAIL FUNCTION */
			$send_mail_data = array('name' => $name,'password' => $passwordIs,'email' => $email,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang);
			Mail::send('email.mobilecusregister', $send_mail_data, function($message) use($send_mail_data)
			{
				$email               = $send_mail_data['email'];
				$name                = $send_mail_data['name'];
				$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'API_LOGIN_DETAILS','Your Login Details!');
				$message->to($email, $name)->subject($subject);
			});
			/* EOF MAIL FUNCTION */ 
			/* ------------------RETRIVE USER DETAILS----------------------- */
			$user = JWTAuth::user();
			$updatableArray = array();
			/* update fcm id */
			if($ph_type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
				{	
					$andr_fcm_arr = json_decode($user->cus_andr_fcm_id,true);
					if($this->whatever($andr_fcm_arr,'device_id',$andr_device_id))
					{

					}
					else
					{
						$andr_fcm_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					}
					
					$unique_array_res = $andr_fcm_arr;
					
					if(!empty($unique_array_res))
					{
					foreach($unique_array_res as $arr)
					{
						
						if($arr['device_id'] == $andr_device_id)
						{ 
							$update_andr_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
						}
						else
						{  
							$update_andr_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
						}
					}
					
					}
					$updatableArray['cus_andr_fcm_id'] = json_encode($update_andr_arr);
					updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $user->cus_email]);
				}
				elseif($ph_type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
				{	
					$ios_fcm_arr = json_decode($user->cus_ios_fcm_id,true);
					
					if($this->whatever($ios_fcm_arr,'device_id',$ios_device_id))
					{

					}
					else
					{
						$ios_fcm_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
					}
					
					$unique_array_res = $ios_fcm_arr;
					
					if(!empty($unique_array_res))
					{
					foreach($unique_array_res as $arr)
					{
						
						if($arr['device_id'] == $ios_device_id)
						{
							$update_ios_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
						}
						else
						{
							$update_ios_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
						}
					}
				}
					$updatableArray['cus_ios_fcm_id'] = json_encode($update_ios_arr);
					updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $user->cus_email]);	
				}
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			$data = ['token'			=> $token,
			"user_id"		=> intval($user->cus_id),
			"user_name"		=> ucfirst($user->cus_fname),
			"user_email"	=> $user->cus_email,
			"user_phone"	=> ($user->cus_phone1 != '') ? $user->cus_phone1 : '',
			"user_login_type"=>intval($user->cus_login_type),
			"user_status"	=>intval($user->cus_status)];
			$encode = ['code'			=> 200,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			
		}
		
		/******************************* GOOGLE LOGIN ****************************/
		public function google_login(Request $request){
			//print_r($request->all()); exit;
			Config::set('jwt.user', 'App\User'); 
			Config::set('auth.providers.users.model', \App\User::class);
			Config::set('jwt.userpassword','cus_password');
			$name  = $request->name; 
			$email = $request->email; 
			$google_id = $request->google_id; 
			$passwordIs = $this->random_password(6);
			$andr_fcm_id = $request->andr_fcm_id;
			$andr_device_id = $request->andr_device_id;
			$ios_fcm_id  = $request->ios_fcm_id;
			$ios_device_id  = $request->ios_device_id;
			$ph_type     = $request->type; // use android or ios
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$name_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$google_id_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_GL_ID','Please enter Google ID!');
			
			$validator = Validator::make($request->all(), 
			[ 'name' => 'required','email' => 'required|string|email', 'google_id' => 'required'],
			[ 'name.required'=>$name_req_err_msg,'cus_email.required'=>$email_req_err_msg,'cus_email.email'=>$valid_email_err_msg,'google_id.required'=>$google_id_err_msg]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = ["code"=>400,"message"=>$message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			//CHECK ALREADY EMAIL AVAILABLE IN CUSTOMER TABLE, IF AVAILABLE THEN UPDATE FACEBOOK ID ELSE INSERT.
			$cus_login_exist = DB::table('gr_customer')->where('cus_email','=',$email)->where('cus_status','!=','2')->first();
			$cus_logged_ip = $request->ip();
			$cus_loggedin_time = date('Y-m-d H:i:s');
			
			
			if(!empty($cus_login_exist)==false){
				$insertArr = array(	'cus_fname' 	=> $name,
				'cus_email' 	=> $email,
				'cus_password' 	=> md5($passwordIs),
				'cus_decrypt_password' 	=> $passwordIs,
				'cus_login_type'=> '4',
				'cus_status' 	=> '1',
				'cus_paynamics_status'	=>'Unpublish',
				'cus_paymaya_status'	=>'Unpublish',
				'cus_netbank_status'	=>'Unpublish',
				'cus_created_date' 		=> date('Y-m-d'),
				'cus_logged_ip'			=>$cus_logged_ip,
				'cus_loggedin_time'		=>$cus_loggedin_time
				);
				if($ph_type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
				{
					$andr_fcm_arr1[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					$insertArr['cus_andr_fcm_id'] = json_encode($andr_fcm_arr1);
				}
				elseif($ph_type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
				{
					$ios_fcm_arr1[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
					$insertArr['cus_ios_fcm_id'] = json_encode($ios_fcm_arr1);	
				}
				$insert = insertvalues('gr_customer',$insertArr);
			}
			else{
				$updatableArray = array();
				$updatableArray['cus_password']=md5($passwordIs);
				$updatableArray['cus_decrypt_password']=$passwordIs;
				$updatableArray['cus_google_id'] = $google_id;
				$updatableArray['cus_logged_ip'] = $cus_logged_ip;
				$updatableArray['cus_loggedin_time'] = $cus_loggedin_time;
				updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $email]);
			}
			
			$credentials['cus_email'] = $email;
			$credentials['cus_password'] = $passwordIs;
			$credentials['cus_status'] = '1';
			$token = null;
			try {
				if (!$token = JWTAuth::attempt($credentials)) {
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
				} catch (JWTAuthException $e) {
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
		    	$encode = ['code' => 400,'message'  => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				
			}
			/*MAIL FUNCTION */
			$send_mail_data = array('name' => $name,'password' => $passwordIs,'email' => $email,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang);
			Mail::send('email.mobilecusregister', $send_mail_data, function($message) use($send_mail_data)
			{
				$email               = $send_mail_data['email'];
				$name                = $send_mail_data['name'];
				$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'API_LOGIN_DETAILS','Your Login Details!');
				$message->to($email, $name)->subject($subject);
			});
			/* EOF MAIL FUNCTION */ 
			/* ------------------RETRIVE USER DETAILS----------------------- */
			$user = JWTAuth::user();
			$updatableArray = array();
			/* update fcm id */
			if($ph_type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
				{	
					$andr_fcm_arr = json_decode($user->cus_andr_fcm_id,true);
					if($this->whatever($andr_fcm_arr,'device_id',$andr_device_id))
					{

					}
					else
					{
						$andr_fcm_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					}
					
					$unique_array_res = $andr_fcm_arr;
					
					if(!empty($unique_array_res))
					{
					foreach($unique_array_res as $arr)
					{
						
						if($arr['device_id'] == $andr_device_id)
						{ 
							$update_andr_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
						}
						else
						{  
							$update_andr_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
						}
					}
					
					}
					$updatableArray['cus_andr_fcm_id'] = json_encode($update_andr_arr);
					updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $user->cus_email]);
				}
				elseif($ph_type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
				{	
					$ios_fcm_arr = json_decode($user->cus_ios_fcm_id,true);
					
					if($this->whatever($ios_fcm_arr,'device_id',$ios_device_id))
					{

					}
					else
					{
						$ios_fcm_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
					}
					
					$unique_array_res = $ios_fcm_arr;
					
					if(!empty($unique_array_res))
					{
					foreach($unique_array_res as $arr)
					{
						
						if($arr['device_id'] == $ios_device_id)
						{
							$update_ios_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
						}
						else
						{
							$update_ios_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
						}
					}
				}
					$updatableArray['cus_ios_fcm_id'] = json_encode($update_ios_arr);
					updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $user->cus_email]);	
				}
				
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			$data = ['token'			=>$token,
			"user_id"		=>intval($user->cus_id),
			"user_name"		=>ucfirst($user->cus_fname),
			"user_email"	=>$user->cus_email,
			"user_phone"	=>($user->cus_phone1 != '') ? $user->cus_phone1 : '',
			"user_login_type"=>intval($user->cus_login_type),
			"user_status"	=>intval($user->cus_status)];
			$encode = ['code'			=> 200,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			
		}
		
		/* delivery person login */
		public function delivery_login(Request $request)
		{
			Config::set('jwt.user','App\Delivery_person');
			Config::set('auth.providers.users.model',\App\Delivery_person::class);
			Config::set('jwt.userpassword','deliver_password');
			$login_id   = $request->login_id;
			$delivery_email	= $request->login_id; 
			$password 	= $request->delivery_password; 
			$lang 		= $request->lang;
			$andr_fcm_id = $request->andr_fcm_id;
			$andr_device_id = $request->andr_device_id;
			$ios_fcm_id = $request->ios_fcm_id;
			$ios_device_id  = $request->ios_device_id;
			$type       = $request->type; //use android or ios
			$delivery_location 	= $request->location;
			$delivery_latitude 	= $request->latitude;
			$delivery_longitude = $request->longitude;
			/* -------------------------- VALIDATION STARTS HERE ------------------ */
			$pwd_req_err_msg	=	MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$login_id))
			{
				$email_req_err_msg	=	MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg	=	MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				
				$validator = Validator::make($request->all(),[ 'login_id' => 'required|string|email', 'delivery_password' => 'required','latitude' =>'required','longitude' => 'required','location' => 'required' ], [ 'login_id.required'=>$email_req_err_msg,'login_id.email'=>$valid_email_err_msg,
				'delivery_password.required'=>$pwd_req_err_msg]);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}
				elseif(preg_match('/^[0-9+]+/i', $login_id))
				{
				$phone_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
				$validator = Validator::make($request->all(), 
				[ 'login_id' => 'required', 'delivery_password' => 'required','latitude' =>'required','longitude' => 'required','location' => 'required'],
				[ 'login_id.required'=>$phone_req_err_msg,'delivery_password.required'=>$pwd_req_err_msg]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}else{
					$cus_email_res = DB::table('gr_delivery_member')->select('deliver_email')->where('deliver_phone1','=',$login_id)->where('deliver_status','=','1')->first();
					if(empty($cus_email_res)===true){
						$message = MobileModel::get_lang_text($lang,'API_PHONENUM_DOESNOT_EXISTS','Phone Number doesn\'t Exists!');
						$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
						}else{
						$delivery_email = $cus_email_res->deliver_email;
					}
				}
				}
				else
				{
				$email_req_err_msg	=	MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg	=	MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				
				$validator = Validator::make($request->all(),[ 'login_id' => 'required|string|email', 'delivery_password' => 'required','latitude' =>'required','longitude' => 'required','location' => 'required'], [ 'login_id.required'=>$email_req_err_msg,'login_id.email'=>$valid_email_err_msg,
				'delivery_password.required'=>$pwd_req_err_msg]);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			
			$credentials['deliver_email'] = $delivery_email;
			$credentials['deliver_password'] = $password;
			$credentials['deliver_status'] = '1';
			$token = null;
			/* check credentials */
			//echo Config::get('jwt.userpassword'); exit;
			try
			{
				if(!$token = JWTAuth::attempt($credentials))
				{ 
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
			}
			catch(JWTAuthException $e)
			{
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
		    	$encode = ['code' => 400,'message'  => $msg,'data' =>$this->empty_data];
                return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
			}
			
			$details = JWTAuth::user();
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			/* update location */
			$up_arr = ['deliver_location' 		=> $delivery_location,
					'deliver_latitude' 		=> $delivery_latitude,
					'deliver_longitude' 	=> $delivery_longitude];
			if($type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
			{	
				$andr_fcm_arr = json_decode($details->deliver_andr_fcm_id,true);
				if($this->whatever($andr_fcm_arr,'device_id',$andr_device_id))
				{

				}
				else
				{
					$andr_fcm_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
				}
				
				$unique_array_res = $andr_fcm_arr;
				
				if(!empty($unique_array_res))
				{
				foreach($unique_array_res as $arr)
				{
					
					if($arr['device_id'] == $andr_device_id)
					{ 
						$update_andr_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					}
					else
					{  
						$update_andr_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
					}
				}
				
				}
				$up_arr['deliver_andr_fcm_id'] = json_encode($update_andr_arr);
				
			}
			elseif($type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
			{	
				$ios_fcm_arr = json_decode($details->deliver_ios_fcm_id,true);
				
				if($this->whatever($ios_fcm_arr,'device_id',$ios_device_id))
			{
				}
				else
				{
					$ios_fcm_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
			}
				
				$unique_array_res = $ios_fcm_arr;
				if(!empty($unique_array_res))
				{
				foreach($unique_array_res as $arr)
			{
					if($arr['device_id'] == $ios_device_id)
					{ 
						$update_ios_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
			}
					else
					{  
						$update_ios_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
					}
				}
			}
				$up_arr['deliver_ios_fcm_id'] = json_encode($update_ios_arr);	
			}
			updatevalues('gr_delivery_member',$up_arr,['deliver_id' => $details->deliver_id]);
			$wk_hrs_details = DB::table('gr_deliver_working_hrs')->where('dw_deliver_id','=',$details->deliver_id)->count();
			$wk_hr_status	= ($wk_hrs_details == 0) ? 'Not updated' : 'Updated';
			$document_update_status = 'Not updated';
			if($details->deliver_licence != '' || $details->deliver_address_proof != '')
			{
				$document_update_status = 'Updated';
			}
			$data = ['token'		=> $token,
			"delivery_person_id"	=> intval($details->deliver_id),
			"delivery_person_name"	=> ucfirst($details->deliver_fname).' '.$details->deliver_lname,
			"delivery_person_email"	=> $details->deliver_email,
			"delivery_person_phone"	=> $details->deliver_phone1,
			"delivery_person_status"=> ($details->deliver_avail_status == 1) ? MobileModel::get_lang_text($lang,'API_AVAIL','Available') : MobileModel::get_lang_text($lang,'API_UNAVAIL','Unavailable'),
			'wk_hr_status'			=> $wk_hr_status,
			'uploaded_document_status'	=> $document_update_status
			];
			$encode = ['code'		=> 200,
			'message'				=> $msge,
			'data'					=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			
		}
		
		/* merchant login */
		public function merchant_login(Request $request)
		{
			Config::set('jwt.user','App\Merchant');
			Config::set('auth.providers.users.model',\App\Merchant::class);
			Config::set('jwt.userpassword','mer_password');
			$login_id   	= $request->login_id;
			$mer_email		= $request->login_id; 
			$password 		= $request->merchant_password; 
			$lang 			= $request->lang;
			$andr_fcm_id 	= $request->andr_fcm_id;
			$andr_device_id = $request->andr_device_id;
			$ios_fcm_id 	= $request->ios_fcm_id;
			$ios_device_id  = $request->ios_device_id;
			$type       	= $request->type; //use android or ios
			/* -------------------------- VALIDATION STARTS HERE ------------------ */
			$pwd_req_err_msg	=	MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$login_id))
			{
				$email_req_err_msg	=	MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg	=	MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				
				$validator = Validator::make($request->all(),['login_id' => 'required|string|email',
				'merchant_password' => 'required'],
				['login_id.required'=>$email_req_err_msg,
				'login_id.email'=>$valid_email_err_msg,
				'merchant_password.required'=>$pwd_req_err_msg
				]);
				
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			elseif(preg_match('/^[0-9+]+/i', $login_id))
			{
				$phone_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
				$validator = Validator::make($request->all(),[ 'login_id' => 'required',
				'merchant_password' => 'required'
				],
				['login_id.required'=>$phone_req_err_msg,
				'merchant_password.required'=>$pwd_req_err_msg
				]
				);
				
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				else
				{
					$cus_email_res = DB::table('gr_merchant')->select('mer_email')->where('mer_phone','=',$login_id)->where('mer_status','=','1')->first();
					if(empty($cus_email_res)===true){
						$message = MobileModel::get_lang_text($lang,'API_PHONENUM_DOESNOT_EXISTS','Phone Number doesn\'t Exists!');
						$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}
					else
					{
						$mer_email = $cus_email_res->mer_email;
					}
				}
			}
			else
			{
				$email_req_err_msg	=	MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
				$valid_email_err_msg	=	MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				
				$validator =Validator::make($request->all(),['login_id' 			=> 'required|string|email',
				'merchant_password' 		=> 'required'],
				['login_id.required'		=>$email_req_err_msg,
				'login_id.email'			=>$valid_email_err_msg,
				'merchant_password.required'=>$pwd_req_err_msg
				]
				);
				
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			
			$credentials['mer_email'] = $mer_email;
			$credentials['mer_password'] = $password;
			$credentials['mer_status'] = '1';
			$token = null;
			/* check credentials */
			//echo Config::get('jwt.userpassword'); exit;
			//print_r($credentials); exit;
			try
			{
				if(!$token = JWTAuth::attempt($credentials))
				{ 
					$msg = MobileModel::get_lang_text($lang,'API_INVALID_CREDENTIALS','Invalid Credentials');
					$encode = ['code' => 400,'message'  => $msg,'data' =>$this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
				}
			}
			catch(JWTAuthException $e)
			{
				$msg = MobileModel::get_lang_text($lang,'API_FAILED_TO_CREATE','Failed to create token');
		    	$encode = ['code' => 400,'message'  => $msg,'data' =>$this->empty_data];
                return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT),400)->header('Content-Type',"application/json");
			}
			
			$details = JWTAuth::user();
			$fcm_id_array = array();
			if($type == 'android' && $andr_fcm_id != '' && $andr_device_id != '')
			{	
				$andr_fcm_arr = json_decode($details->mer_andr_fcm_id,true);
				if($this->whatever($andr_fcm_arr,'device_id',$andr_device_id))
				{

				}
				else
				{
					$andr_fcm_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
				}
				
				$unique_array_res = $andr_fcm_arr;
				
				if(!empty($unique_array_res))
				{
				foreach($unique_array_res as $arr)
				{
					
					if($arr['device_id'] == $andr_device_id)
					{ 
						$update_andr_arr[] = array("device_id"=>$andr_device_id,"fcm_id" => $andr_fcm_id);
					}
					else
			{
						$update_andr_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
					}
				}
				
				}
				$fcm_id_array['mer_andr_fcm_id'] = json_encode($update_andr_arr);	
				updatevalues('gr_merchant',$fcm_id_array,['id' => $details->id]);
			}
			elseif($type == 'ios' && $ios_fcm_id != '' && $ios_device_id != '')
			{	
				$ios_fcm_arr = json_decode($details->mer_ios_fcm_id,true);
				
				if($this->whatever($ios_fcm_arr,'device_id',$ios_device_id))
				{

				}
				else
				{
					$ios_fcm_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
				}
				
				$unique_array_res = $ios_fcm_arr;
				
				if(!empty($unique_array_res))
				{
				foreach($unique_array_res as $arr)
				{
					
					if($arr['device_id'] == $ios_device_id)
					{ 
						$update_ios_arr[] = array("device_id"=>$ios_device_id,"fcm_id" => $ios_fcm_id);
			}
					else
			{
						$update_ios_arr[] = array("device_id"=>$arr['device_id'],"fcm_id" => $arr['fcm_id']);
					}
				}
				}
				$fcm_id_array['mer_ios_fcm_id'] = json_encode($update_ios_arr);	
				updatevalues('gr_merchant',$fcm_id_array,['id' => $details->id]);
			}
			$msge=MobileModel::get_lang_text($lang,'API_LOGIN_SUCCESS','Logged In Succesfully!');
			$data = ['token'				=>$token,
			"merchant_id"		=>intval($details->id),
			"merchant_name"		=>ucfirst($details->mer_fname).' '.$details->mer_lname,
			"merchant_email"	=>$details->mer_email,
			"merchant_phone"	=>$details->mer_phone,
			];
			$encode = ['code'					=> 200,
			'message'				=> $msge,
			'data'					=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* currency convertion */
		public function convert_currency(Request $request)
		{
			$from = $request->from;
			$to = $request->to;
			$amt = $request->amount;
			$validator = Validator::make($request->all(),['from' => 'required','to' => 'required','amount' => 'required']);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				$encode = ["code"=>400,"message"=>$message,'data' =>$this->empty_data];
                return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$convert_amt  = convertCurrency($from,$to,$amt);
			$encode = ['code'					=> 200,
			'message'				=> 'Success',
			'data'					=> ['amount' => $convert_amt]
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}

		/* help page */
		public function help(Request $request)
		{
			$lang = $request->lang;

			$content = DB::table('gr_cms')->select('page_title_'.$lang.' as title','description_'.$lang.' as desc')->where('slug','=','help')->where('page_status','!=','2')->first();
			if(empty($content) === false)
			{
				$data = ['title' => ucfirst($content->title),'description' => ucfirst(($content->desc))];
				$encode = [ 'code' => 200,'message' => 'Details Available','data' => ['content' => $data]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$encode = [ 'code' => 400,'message' => 'No Details Available','data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		/* terms page */
		public function terms(Request $request)
		{
			$lang = $request->lang;

			$content = DB::table('gr_cms')->select('page_title_'.$lang.' as title','description_'.$lang.' as desc')->where('slug','=','terms')->where('page_status','!=','2')->first();
			if(empty($content) === false)
			{
				$data = ['title' => ucfirst($content->title),'description' => ucfirst($content->desc)];
				$encode = [ 'code' => 200,'message' => 'Details Available','data' => ['content' => $data]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$encode = [ 'code' => 400,'message' => 'No Details Available','data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
	}	