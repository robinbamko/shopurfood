<?php 
	//header("Content-Type",'application/json');
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\MobileApp;
	//use App\Http\Resources\UserResource;
	use App\Http\Controllers\Controller;
	use App\Http\Models;
	use App\MobileModel;
	use App\Home;
	use App\User;
	use App\Agent;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Mail;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Validation\Rule;
	use Illuminate\Support\MessageBag;
	use Illuminate\Http\Request;
	use Response;
	use Lang;
	use App;
	use DB;
	use Validator;
	use View;
	use JWTAuth;
	use Config;
	use Tymon\JWTAuth\Exceptions\JWTException;
	use Image;
	use Twilio;
	
	class CustomerAppController extends Controller {
		
		private $empty_data; 
		public function __construct()
		{	
			parent::__construct();
			$this->user = new User;
			$this->agent= new Agent;
			$this->empty_data = array();
			
			/*LANGUAGE SETTINGS */
			$this->default_mob_lang_code  = 'en';
			$this->admin_default_lang = DB::table('gr_language')->select('lang_code')->where('default_lang','1')->where('status','1')->first()->lang_code;
			/* EOF LANGUAGE SETTINGS */
			
			/*GENERAL DETAILS*/
			$this->general_setting = DB::table('gr_general_setting')->first();
			if(!empty($this->general_setting)){
				View::share("SITENAME",$this->general_setting->gs_sitename);
				View::share("FOOTERNAME",$this->general_setting->footer_text);
				Config::set('admin_mail',$this->general_setting->gs_email);	
				$this->site_name = $this->general_setting->gs_sitename; 
				$this->default_curr = $this->general_setting->gs_currency_code; 
				}else{
				View::share("SITENAME","Shopurfood");
				View::share("FOOTERNAME","Shopurfood");
				Config::set('admin_mail','admin@gmail.com');
				$this->site_name = "Shopurfood";
				$this->default_curr = "$";
			}
			$this->country_setting = DB::table('gr_country')->where('default_counrty','1')->first();
			if(!empty($this->country_setting)){
				$this->default_currency_code = $this->country_setting->co_curcode; 
				}else{
				$this->default_currency_code = "USD";
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
			View::share("LOGOPATH",$path);
			/* EOF GENERAL DETAILS */
			$admin_det = get_admin_details();  /* get admin details*/
			$this->admin_id  = $admin_det->id;
			
		}

		function whatever($array, $key, $val) {
			if(!empty($array))
			{
				foreach ($array as $item)
		       if (isset($item[$key]) && $item[$key] == $val)
		           return true;
			}
		   
		   return false;
		}
		
		// function makes curl request to firebase servers
		
		private  function sendPushNotification($fields,$key) 
		{ 
			
			$data = json_encode($fields);
			//FCM API end-point
			$url = 'https://fcm.googleapis.com/fcm/send';
			//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
			$server_key = $key;
			//header with content_type api key
			$headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$server_key
			);
			//CURL request to route notification to FCM connection server (provided by Google)
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$result = curl_exec($ch);
			
			if ($result === FALSE) {
				die('Oops! FCM Send Error: ' . curl_error($ch));
			}
			curl_close($ch);
			
			return $result;
			
		}
		/** send notification  ends**/
		/*------------------------ CUSTOMER REGISTRATION -----------------*/
		public function user_registration(Request $request) {
			$lang 		= Input::get('lang');
			$name 		= Input::get('cus_fname');
			$email 		= Input::get('cus_email');
			$password 	= Input::get('cus_password');
			$phone_num 	= Input::get('cus_phone1');
			$refer_code = Input::get('referral_code');
			$andr_fcm_id = $request->andr_fcm_id;
			$andr_device_id = $request->andr_device_id;
			$ios_fcm_id  = $request->ios_fcm_id;
			$ios_device_id  = $request->ios_device_id;
			$ph_type	  = $request->type;
			$admin_lang = (!empty($this->admin_default_lang))?$this->admin_default_lang:$this->default_mob_lang_code;
			/* -------------VALIDATION STARTS HERE------------------ */
			//CHECK ADMIN LANGUAGE AND RECEIVED Language ARE DEFAULT LANGUAGE THEN DONT ADD SUFFIX ELSE ADD SUFFIX
			$name_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$pass_req_err_msg		= MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			$phoneNumber_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$validator = Validator::make($request->all(), 
			['cus_fname'	=> 'required',
			'cus_email' 	=> 'required|string|email',
			'cus_password'	=> 'required',
			'cus_phone1'	=> 'required'
			],
			['cus_fname.required'	=> $name_req_err_msg,
			'cus_email.required'	=> $email_req_err_msg,
			'cus_email.email'		=> $valid_email_err_msg,
			'cus_password.required'	=> $pass_req_err_msg,
			'cus_phone1.required'	=> $phoneNumber_err_msg
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = ['code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$msge=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				$encode = ['code' => 400,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($this->general_setting->gs_password_protect==1)
			{
				$uppercase = preg_match('@[A-Z]@', $password);
				$lowercase = preg_match('@[a-z]@', $password);
				$number    = preg_match('@[0-9]@', $password);
				$splChar   = preg_match('@[\W_]@', $password);
				
				if(!$uppercase || !$lowercase || !$number || !$splChar || strlen($password) < 6) {
					$msge	= MobileModel::get_lang_text($lang,'API_PROTECT_PASSWORD_RULES','Password should be atleast one lower case, upper case, number and min.length 6!');
					$encode = ['code' => 400,'message'  => $msge,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else{
				if(strlen($password) < 6)
				{
					$msge	= MobileModel::get_lang_text($lang,'API_PASSWORD_RULES','Password min. length should be 6!');
					$encode = ['code' => 400,'message'  => $msge,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			if($refer_code != '')
			{
				$validator 	= Validator::make($request->all(),
				['referral_code' => ['sometimes',
				Rule::exists('gr_referal','re_code')->where(function ($query) use($email) {$query->where('referre_email','=',$email); })
				]
				],
				['referral_code.exists'	=> MobileModel::get_lang_text($lang,'API_INVALID_RE_CODE','Invalid referral code')]
				);
				if($validator->fails())
				{
					$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$email)->where('cus_status','!=','2')->count();
			$check_phoneNumber_already_exsist = DB::table('gr_customer')->where('cus_phone1','=',$phone_num)->where('cus_status','!=','2')->count();
			if($check_already_exsist > 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				$encode = [ 'code' => 400,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist > 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				$encode = [ 'code' => 400,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$cus_logintype = '1';
			$cus_status = '1';
			$otp = mt_rand(100000, 999999);
			if($this->general_setting->otp_verification_status==1){
				try{
					Twilio::message($phone_num, $otp);
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$data = ['otp'			=> $otp,
					'cus_fname'		=> $name,
					'cus_email'		=> $email,
					'cus_password' 	=> $password,
					'cus_phone1'	=> $phone_num];
					$encode = array('code'		=> 201,
					'message'	=> $msge,
					'data'		=> $data
					);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				catch (\Exception $e)
				{	
					/* hide for testing twilio test account. Enable while use twilio live account */	
					/*$encode = array('code'=> 400,'message' => $e->getMessage(),'data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");*/
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$data = ['otp'			=> $otp,
					'cus_fname'		=> $name,
					'cus_email'		=> $email,
					'cus_password' 	=> $password,
					'cus_phone1'	=> $phone_num];
					$encode = array('code'		=> 201,
					'message'	=> $msge,
					'data'		=> $data
					);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			else 
			{
				/* get referrer id */ 
				$re_id = 0;
				$details = DB::table('gr_referal')->select('referral_id')->where('referre_email','=',$email)->first();
				if(empty($details) === false)
				{
					$re_id = $details->referral_id;
				}
				$andr_arr = $ios_arr = array();
				if($ph_type == 'android')
				{	
					$andr_arr[] = array("device_id" => $andr_device_id,"fcm_id" => $andr_fcm_id);
				}
				elseif($ph_type == "ios")
				{
					$ios_arr[] = array("device_id" => $ios_device_id,"fcm_id" => $ios_fcm_id);	
				}
				
				//echo $re_id; exit;
				/*INSERT INTO TABLE */
				$user = User::create([
				'cus_fname' 			=> $name,
				'cus_email' 			=> $email,
				'cus_password' 			=> md5($password),
				'cus_decrypt_password' 	=> $password,
				'cus_phone1'			=> $phone_num,
				'cus_login_type' 		=> $cus_logintype,
				'cus_status' 			=> $cus_status,
				'cus_referedBy'			=> $re_id,
				'cus_andr_fcm_id'		=> json_encode($andr_arr),
				'cus_ios_fcm_id'		=> json_encode($ios_arr),
				'cus_paynamics_status'	=> 'Unpublish',
				'cus_paymaya_status'	=> 'Unpublish',
				'cus_netbank_status'	=> 'Unpublish',
				'cus_created_date' 		=> date('Y-m-d')
				]);
				$lastinsertid = DB::getPdo()->lastInsertId();
				
				/* update referel code used status */
				if($refer_code != '')
				{
					updatevalues('gr_referal',['re_code_used' => '1'],['referre_email' => $email]);
				}
				$token = JWTAuth::fromUser($user);
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => $name,'password' => $password,'email' => $email,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang);
				Mail::send('email.mobilecusregister', $send_mail_data, function($message) use($send_mail_data)
				{
					$email	 = $send_mail_data['email'];
					$name    = $send_mail_data['name'];
					$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'API_REGISTERED_DETAILS','Your Registration Details!');
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				$msge=MobileModel::get_lang_text($lang,'API_REGISTERED_SUCCESSFULLY','Registered successfully!');
				$data = ['token'		=>$token,
				"user_id"		=>intval($lastinsertid),
				"user_name"		=>ucfirst($name),
				"user_email"	=>$email,
				"user_phone"	=>$phone_num,
				"user_login_type"=>intval($cus_logintype),
				"user_status"	=>intval($cus_status)];
				$encode = array('code'=>200,'message'=>$msge,'data'	=> $data);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		public function check_otp(Request $request)
		{	
			$lang = $request->lang;
			$current_otp 	= $request->current_otp;
			$generated_otp	= $request->otp;
			$name 			= $request->cus_fname;
			$email 			= $request->cus_email;
			$password 		= $request->cus_password;
			$phone_num 		= $request->cus_phone1;
			$refer_code 	= $request->referral_code;
			$andr_fcm_id 	= $request->andr_fcm_id;
			$ios_fcm_id 	= $request->ios_fcm_id;
			$cus_logintype 	= '1';
			$cus_status 	= '1';
			
			$otp_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_RECEIVED_OTP','Please enter received OTP!');
			$current_otp_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_YOUR_OTP','Please enter OTP!');
			$validator = Validator::make($request->all(), ['current_otp' => 'required','otp' => 'required'],
			['current_otp.required' => $current_otp_err_msg, 'otp.required'=> $otp_err_msg]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			/*
				if($generated_otp =="" || $current_otp =="")
				{
				$msge=MobileModel::get_lang_text($lang,'API_PARAMETER_MISSING','Parameter missing!');
				$encode = [ 'status' => 400,'message' => $msge];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			*/
			if($generated_otp == $current_otp){
				/* get referrer id */ 
				$re_id = 0;
				$details = DB::table('gr_referal')->select('referral_id')->where('referre_email','=',$email)->first();
				if(empty($details) === false)
				{
					$re_id = $details->referral_id;
				}
				//echo $re_id; exit;
				/*INSERT INTO TABLE */
				$user = User::create([
				'cus_fname' 			=> $name,
				'cus_email' 			=> $email,
				'cus_password' 			=> md5($password),
				'cus_decrypt_password' 	=> $password,
				'cus_phone1'			=> $phone_num,
				'cus_login_type' 		=> $cus_logintype,
				'cus_status' 			=> $cus_status,
				'cus_referedBy'			=> $re_id,
				'cus_andr_fcm_id'		=> $andr_fcm_id,
				'cus_ios_fcm_id'		=> $ios_fcm_id,
				'cus_paynamics_status'	=> 'Unpublish',
				'cus_paymaya_status'	=> 'Unpublish',
				'cus_netbank_status'	=> 'Unpublish',
				'cus_created_date' 		=> date('Y-m-d')
				]);
				$lastinsertid = DB::getPdo()->lastInsertId();
				
				/* update referel code used status */
				if($refer_code != '')
				{
					updatevalues('gr_referal',['re_code_used' => '1'],['referre_email' => $email]);
				}
				$token = JWTAuth::fromUser($user);
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => $name,'password' => $password,'email' => $email,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang);
				Mail::send('email.mobilecusregister', $send_mail_data, function($message) use($send_mail_data)
				{
					$email               = $send_mail_data['email'];
					$name                = $send_mail_data['name'];
					$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'API_REGISTERED_DETAILS','Your Registration Details!');
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				$msge=MobileModel::get_lang_text($lang,'API_REGISTERED_SUCCESSFULLY','Registered successfully!');
				$data = ['token'=>$token,
				"user_id"		=>intval($lastinsertid),
				"user_name"		=>ucfirst($name),
				"user_email"	=>$email,
				"user_phone"	=>$phone_num,
				"user_login_type"=>intval($cus_logintype),
				"user_status"	 =>intval($cus_status)];
				$encode = array('code'=>200,'message'=>$msge,'data'	=> $data);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}else{
				$msge=MobileModel::get_lang_text($lang,'API_INVALID_OTP','Invalid OTP');
				return Response::make(json_encode(array('code'=>400,'message'=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* country list */
		public function country_list(Request $request)
		{
			$lang = $request->lang;
			$co_name = ($lang == $this->admin_default_lang) ? 'co_name' : 'co_name_'.$lang;
			$default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code','co_cursymbol',$co_name)->where(['default_counrty' => '1','co_status' => '1'])->first();
			$country_code = "US";
			$country_dial = "+1";
			$country_name = "United States";
			$cnty_list 	  = array();
			if(empty($default) === false)
			{
				$country_code = $default->co_code;
				$country_dial = $default->co_dialcode;
				$country_name = $default->$co_name;
				$cnty_list[]    = ['country_name' => $country_name,
				'country_code' => $country_code,
				'country_dial' => $country_dial];
			}
			$text 	= MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details available');
			$data   = ['country_details' => $cnty_list];
			return Response::make(json_encode(array('code'=>200,'message'=>$text,'data'=>$data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			
		}
		
		public function logout(Request $request)
		{
			$lang= $request->lang;
			$andr_device_id = $request->andr_device_id;
			$ios_device_id  = $request->ios_device_id;
			$ph_type  = $request->type;
			$validator = Validator::make($request->all(),[ 'token'	=> 'required']);
            if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			try {
				$user = JWTAuth::user();
				if($ph_type == 'android' && $andr_device_id != '' && $user->cus_andr_fcm_id!=''){ 
					$andr_fcm_arr = json_decode($user->cus_andr_fcm_id,true);
					//print_r($andr_fcm_arr); exit;
					$newArray = removeElementWithValue($andr_fcm_arr, "device_id", $andr_device_id);
					$updatableArray = array('cus_andr_fcm_id' =>json_encode($newArray));
					updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $user->cus_email]);
				}
				if($ph_type == 'ios' && $ios_device_id != '' && $user->cus_ios_fcm_id != ''){
					$ios_fcm_arr = json_decode($user->cus_ios_fcm_id,true);
					$newArray = removeElementWithValue($ios_fcm_arr, "device_id", $ios_device_id);
					$updatableArray = array('cus_ios_fcm_id' =>json_encode($newArray));
					updatevalues('gr_customer',$updatableArray,['cus_status' => '1','cus_email' => $user->cus_email]);
				}
				JWTAuth::invalidate($request->token);
				$msge	= MobileModel::get_lang_text($lang,'API_LOGOUT_SUXES','Logged out successfully!');
				$encode = [ 'code' => 200,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				} catch (JWTException $exception) {
				$msge	= MobileModel::get_lang_text($lang,'API_CANNOT_LOGOUT','Sorry, the user cannot be logged out!');
				$encode = [ 'code' => 400,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		public function random_password( $length = 8 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		public function forgot_password(Request $request){
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$email_req_err_msg	= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$validator = Validator::make($request->all(), 
			['cus_email' => 'required|string|email'],
			['cus_email.required'=>$email_req_err_msg,'cus_email.email'=>$valid_email_err_msg]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = ['code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				/*  to set status code response header
				return (new UserResource(User::find(1)))->response()->setStatusCode(202); */
			}
			$LoginRes = MobileModel::checkCustomerMailExist($request->cus_email);
			//print_r($LoginRes); exit;
			if(count($LoginRes)==1){
				foreach($LoginRes as $Login) { }
				$cus_id = $Login->cus_id;
				$passwordIs = $this->random_password(6);
				$cus_password = array( 'cus_password' => md5($passwordIs),'cus_decrypt_password' => $passwordIs);
				DB::table('gr_customer')->where('cus_id', '=', $cus_id)->update($cus_password);
				/*MAIL FUNCTION */
				$data = array('ForgotEmail' => $request->cus_email,'ForgotPassword' => $passwordIs,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang);
				Mail::send('email.mobile_agent_forgetpwd', $data, function($message) use($data)
				{
					$email	= $data['ForgotEmail'];
					$name	= $data['ForgotEmail'];
					$subject= MobileModel::get_lang_text($data['onlyLang'],'API_FORGOT_PASSWORD_DETAILS','Forgot password details!');
					$message->to($email, $name)->subject($subject);
				});
				$msge	= MobileModel::get_lang_text($lang,'API_FORGOT_PASSWORD_SENT','Password sent to your mail!');
				$encode = [ 'code' => 200,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				/* EOF MAIL FUNCTION */ 
			}
			else{
				$msge	= MobileModel::get_lang_text($lang,'API_INVALID_EMAIL','Invalid Email ID!');
				$encode = [ 'code' => 400,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/*  Common details starts */
		/* home */
		public function home(Request $request)
		{
			$lang = $request->lang;
			$type = $request->type;
			//echo $type; exit;
			if($type != 'android' && $type != 'ios')
			{
				$encode = [ 'code' => 400,'message'  => 'Enter valid type','data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");	
			}
			/*$cus_name = JWTAuth::user()->cus_fname; */
			$greet = MobileModel::get_lang_text($lang,'API_WELCOME_TO','Welcome To ');
			$get_details = MobileModel::get_logo_settings_details();
			$andr_splash = $andr_logo = $ios_splash = $ios_login_logo = $ios_signup_logo = $ios_frpw_logo = '';
			if(count($get_details) > 0)
			{
				foreach($get_details as $details)
				{	
					if($details->andr_splash_img_cus != '')
					{
						$filename = public_path('images/logo/').$details->andr_splash_img_cus;
						if(file_exists($filename))
						{
							$andr_splash = url('').'/public/images/logo/'.$details->andr_splash_img_cus;
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

					if($details->ios_splash_img_cus != '')
					{
						$filename = public_path('images/logo/').$details->ios_splash_img_cus;
						if(file_exists($filename))
						{
							$ios_splash = url('').'/public/images/logo/'.$details->ios_splash_img_cus;
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
			if($type == "android")
			{
				if($andr_splash == '' && $andr_logo == '')
				{
					$encode = array('code' => 400,'message' => 'Details not found','data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				else
				{
					$data  = ['splash_screen_android' 		=> $andr_splash,
								'logo_android'		 		=> $andr_logo,
								];
					$encode = ['code' 		=> 200,
					'message'  	=> $greet.$this->site_name,
					'data'		=> $data						
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			elseif($type == "ios")
			{
				if($ios_splash == '' && $ios_login_logo == '' && $ios_signup_logo =='' && $ios_frpw_logo == '')
				{
					$encode = array('code' => 400,'message' => 'Details not found','data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				else
				{
					$data  = ['splash_screen_ios' 		=> $ios_splash,
								'login_logo_ios' 			=> $ios_login_logo,
								'signup_logo_ios' 			=> $ios_signup_logo,
								'forgot_password_logo_ios' 	=> $ios_frpw_logo,
								];
					$encode = ['code' 		=> 200,
					'message'  	=> $greet.$this->site_name,
					'data'		=> $data						
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			
			
		}
		
		/* landing page */
		public function landing_page(Request $request)
		{
			$lang = $request->lang;
			$details = array();
			$get_icon = MobileModel::get_icon(['andr_grocery_icon','andr_restaurant_icon','ios_grocery_icon','ios_restaurant_icon']);
			$grocery_icon_android = $grocery_icon_ios = $restaurant_icon_android = $restaurant_icon_ios = '';
			if(empty($get_icon) === false)
			{	
				$available_msg = MobileModel::get_lang_text($lang,'API_AVAIL','Available');
				$notavail_msg  = MobileModel::get_lang_text($lang,'API_NT_AVAIL','Not Available!');
				
				if($get_icon->andr_grocery_icon != '')
				{
					$filename = public_path('images/logo/').$get_icon->andr_grocery_icon;
					if(file_exists($filename))
					{
						$grocery_icon_android = url('')."/public/images/logo/".$get_icon->andr_grocery_icon;
					}
				}
				if($get_icon->andr_restaurant_icon != '')
				{
					$filename = public_path('images/logo/').$get_icon->andr_restaurant_icon;
					if(file_exists($filename))
					{
						$restaurant_icon_android = url('')."/public/images/logo/".$get_icon->andr_restaurant_icon;
					}
				}
				if($get_icon->ios_grocery_icon != '')
				{
					$filename = public_path('images/logo/').$get_icon->ios_grocery_icon;
					if(file_exists($filename))
					{
						$grocery_icon_ios = url('')."/public/images/logo/".$get_icon->ios_grocery_icon;
					}
				}
				if($get_icon->ios_restaurant_icon != '')
				{
					$filename = public_path('images/logo/').$get_icon->ios_restaurant_icon;
					if(file_exists($filename))
					{
						$restaurant_icon_ios = url('')."/public/images/logo/".$get_icon->ios_restaurant_icon;
					}
				}
				
			}
			$get_banner = MobileModel::get_banner_img();
			if(count($get_banner) <= 0)
			{
				$msge	= MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message'  => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				
				foreach($get_banner as $banner)
				{	
					$type = $android_banner = $ios_banner = '';
					if($banner->banner_type == 1)
					{
						$type = "Grocery";
					}
					elseif($banner->banner_type)
					{
						$type = "Restaurant";
					}
					if($banner->mob_banner_img != '')
					{
						$filename = public_path('images/banner/').$banner->mob_banner_img;
						if(file_exists($filename))
						{
							$android_banner = url('')."/public/images/banner/".$banner->mob_banner_img;
						}
					}
					if($banner->ios_banner_img != '')
					{
						$filename = public_path('images/banner/').$banner->ios_banner_img;
						if(file_exists($filename))
						{
							$ios_banner = url('')."/public/images/banner/".$banner->ios_banner_img;
						}
					}
					$details[] = ['banner_type' 			=> $type,
					'banner_image_android'	=> $android_banner,
					'banner_image_ios'		=> $ios_banner,
					];
				}
				
			}
			$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
			$data   = ['grocery_icon_android'	 	=> $grocery_icon_android,
			'restaurant_icon_android'	=> $restaurant_icon_android,
			'grocery_icon_ios'	 		=> $grocery_icon_ios,
			'restaurant_icon_ios'		=> $restaurant_icon_ios,
			'banner_details' 			=> $details];
			$encode = ['code' 		=> 200,
			'message' 	=> $msge,
			'data'		=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
		}
		
		/* grocery home page */
		public function grocery_home_page(Request $request)
		{
			$user_latitude	 = $request->get('user_latitude'); 
			$user_longitude	 = $request->get('user_longitude');
			$lang 			 = $request->get('lang');
			$lat_err_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longErr_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$validator = Validator::make($request->all(),['user_latitude'  => 'required',
			'user_longitude' => 'required'
			],
			['user_latitude.required'  => $lat_err_req_msg,
			'user_longitude.required' => $longErr_req_msg
			]);
			if($validator->fails())
			{
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$featured_gr = $all_gr = array();
			/* featured store */
			$featured_grocery = MobileModel::get_feat_shops($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2,12);
			if(count($featured_grocery) > 0)
			{
				foreach($featured_grocery as $feat)
				{
					$image = MobileModel::get_image_store($feat->st_logo,"logo");
					$featured_gr[] = ['store_id' 	=> intval($feat->id),
					'store_name' => ucfirst($feat->st_name),
					'st_logo'	=> $image
					];
				}
			}
			/* all stores */
			$all_grocery = MobileModel::all_grocery_list($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2,'','',12);
			//print_r($all_grocery); exit;
			if(count($all_grocery) > 0)
			{
				foreach($all_grocery as $gr)
				{
					$image = MobileModel::get_image_store($gr->st_logo,"logo");
					$all_gr[] = ['store_id' 	=> intval($gr->id),
					'store_name' => ucfirst($gr->st_name),
					'st_logo'	=> $image
					];
				}
			}
			/* category based grocery */
			$cate_grocery = MobileModel::category_based_grocery($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2,3,12);
			//print_r($all_grocery); exit;
			$category_list = $category_array = array();
			if(count($cate_grocery) > 0)
			{
				foreach($cate_grocery as $key=> $value)
				{	$all_grocery_details = array();
					foreach($value as $details)
					{
						$image = MobileModel::get_image_store($details->st_image);
						$all_grocery_details[] = ['store_id' 	=> intval($details->id),
						'store_name' 	=> ucfirst($details->st_name),
						'store_rating'	=> intval($details->avg_val),
						'store_desc'	=> str_limit(str_replace("&nbsp;",' ',strip_tags($details->desc)),50),
						'store_image'	=> $image ];
					}
					$key_values = explode('~`',$key);
					$category_array[] = ['category_id' 		=> intval($key_values[0]),
					'category_name'	=> ucfirst($key_values[1]),
					'store_details'	=> $all_grocery_details
					];
				}	
			}
			/* get all categories list */
			/*$all_categories = MobileModel::get_all_categories($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2);
				if(count($all_categories) > 0)
				{
				foreach($all_categories as $details)
				{	
				$image = MobileModel::get_category_image($details->cate_img);
				$category_list[] = ['category_id'	=> intval($details->cate_id),
				'category_name' => ucfirst($details->category_name),
				'category_image'=> $image]	;
				}
				
				
			}*/
			$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
			$data 	= ['featured_grocery'	  => $featured_gr,
			'all_grocery' 		  => $all_gr,
			'all_grocery_details' => $category_array];
			$encode = [ 'code'			=> 200,
			'message'		=> $msge,
			//'category_list'	  => $category_list,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* restaurant home page */
		public function restaurant_home_page(Request $request)
		{
			$user_latitude	 = $request->get('user_latitude'); 
			$user_longitude	 = $request->get('user_longitude');
			
			$lang 			 = $request->get('lang');
			$lat_err_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longErr_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			
			$validator = Validator::make($request->all(), ['user_latitude' => 'required','user_longitude' => 'required'],['user_latitude.required' => $lat_err_req_msg, 'user_longitude.required' => $longErr_req_msg]);
			if($validator->fails())
			{
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			$featured_res = $all_res = array();
			/* featured store */
			$featured_grocery = MobileModel::get_feat_shops($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1,4);
			$featured_ids = array();
			if(count($featured_grocery) > 0 ){
				foreach($featured_grocery as $featRes){
					array_push($featured_ids,$featRes->id);
				}
			}
			//print_r($featured_grocery); exit;
			/* if count is less than 4, add restaurants based on high ratings */
			if(count($featured_grocery) < 4 )
			{	
				$limit = 4 - count($featured_grocery);
				$featured_rating = MobileModel::get_rating_shops($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1,$limit,$featured_ids);
				//echo "tes"; print_r($featured_rating); exit;
				if(is_object($featured_rating) && is_object($featured_grocery))
				{ 
					$featured_rating = json_decode(json_encode($featured_rating));
					$featured_grocery = json_decode(json_encode($featured_grocery));
					$featured_grocery = array_merge($featured_grocery,$featured_rating);
				}
				//print_r($featured_grocery); exit;
			}
			//print_r($featured_grocery); exit;
			if(count($featured_grocery) > 0)
			{
				foreach($featured_grocery as $feat)
				{
					$image = MobileModel::get_image_restaurant($feat->st_logo,"logo");
					$featured_res[] = ['restaurant_id' 	=> intval($feat->id),
					'restaurant_name' => ucfirst($feat->st_name),
					'restaurant_logo'	=> $image
					];
				}
			}
			/* all stores */
			$all_grocery = MobileModel::all_grocery_list($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1,'','',4);
			//print_r($all_grocery); exit;
			if(count($all_grocery) > 0)
			{
				foreach($all_grocery as $gr)
				{
					$image = MobileModel::get_image_restaurant($gr->st_logo,"logo");
					$all_res[] = ['restaurant_id' 	=> intval($gr->id),
					'restaurant_name' => ucfirst($gr->st_name),
					'restaurant_logo'	=> $image
					];
				}
			}
			/* all stores */
			$cate_restaurant = MobileModel::category_based_grocery($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1,3,4);
			$category_list = $category_array = array();
			if(count($cate_restaurant) > 0)
			{
				foreach($cate_restaurant as $key=> $value)
				{	$all_grocery_details = array();
					foreach($value as $details)
					{	
						$wk_time = MobileModel::get_wk_time($details->id);
						$image = MobileModel::get_image_restaurant($details->st_image);
						$all_grocery_details[] = ['restaurant_id' 	=> intval($details->id),
						'restaurant_name' 	=> ucfirst($details->st_name),
						'restaurant_rating'	=> intval($details->avg_val),
						'restaurant_desc'	=> str_limit(str_replace("&nbsp;",' ',strip_tags($details->desc)),50),
						'today_wking_time'	=> $wk_time,
						'restaurant_status'	=> $details->store_closed,
						'restaurant_image'	=> $image ];
					}
					$key_values = explode('~`',$key);
					$category_array[] = ['category_id' 			=> intval($key_values[0]),
					'category_name'		=> ucfirst($key_values[1]),
					'restaurant_details'	=> $all_grocery_details
					];
				}	
			}
			/* get all categories list */
			/*$all_categories = MobileModel::get_all_categories($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1);
				if(count($all_categories) > 0)
				{
				foreach($all_categories as $details)
				{	
				$image = MobileModel::get_category_image($details->cate_img);
				$category_list[] = ['category_id' 	=> intval($details->cate_id),
				'category_name' => ucfirst($details->category_name),
				'category_image'=> $image]	;
				}
				
				
			}*/
			if(count($featured_res) <= 0 && count($all_res) <= 0 && count($category_array) <= 0 )
			{
				$msge	= MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code'=>400,'message'=>$msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data 	= ['featured_restaurant'	 => $featured_res,
				'all_restaurant'		 => $all_res,
				'all_restaurant_details' => $category_array];
				$encode = [ 'code'			 => 200,
				'message'		 => $msge,
				//'category_list'		 => $category_list,
				'data'			 => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
		}
		
		/* grocery listing page */
		public function all_grocery_lists(Request $request)
		{
			$user_latitude	 = $request->get('user_latitude'); 
			$user_longitude	 = $request->get('user_longitude');
			$lang 			 = $request->get('lang');
			$page 			 = ($request->get('page') != '') ? $request->page : 1;
			$cate_id 		 = $request->get('category_id');
			$lat_err_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longErr_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$validator = Validator::make($request->all(),['user_latitude' => 'required','user_longitude' => 'required'],['user_latitude.required' => $lat_err_req_msg,'user_longitude.required' => $longErr_req_msg]);
			if($validator->fails())
			{
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$route = \Request::segment(3);
			/* check category id exits */
			if($route == "category_based_grocery")
			{
				$catId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_CATEGORY_ID','Please enter category ID!');
				$catId_not_msg = MobileModel::get_lang_text($lang,'API_CAT_DOESNT_EXIST','Category does not exists!');
				$valid_catIdmsg= MobileModel::get_lang_text($lang,'API_VALID_CAT_ID','Enter valid category ID!');
				$validator = Validator::make($request->all(), 
				['category_id' => [	'required',
				'numeric',
				Rule::exists('gr_category','cate_id')->where(function ($query) {
					$query->where('cate_status','=','1')->where('cate_type','=','2');
				})
				]
				],
				['category_id.required' => $catId_req_msg,
				'category_id.exists' 	=> $catId_not_msg,
				'category_id.numeric'	=> $valid_catIdmsg] 														
				);
				if($validator->fails())
				{
					$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}	
			}
			/* category based stores */
			if($cate_id != '')
			{
				$all_grocery = MobileModel::all_grocery_list($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2,$page,$cate_id);	
			}
			else
			{
				/* all stores */
				$all_grocery = MobileModel::all_grocery_list($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2,$page);
			}
			$all_grocery_details = $category_list = array();
			if(count($all_grocery) > 0)
			{
				foreach($all_grocery as $details)
				{
					$image = MobileModel::get_image_store($details->st_logo,"logo");
					$all_grocery_details[] = ['store_id' 	=> intval($details->id),
					'store_name' 	=> ucfirst($details->st_name),
					'store_rating' 	=> intval($details->avg_val),
					'store_desc' 	=> str_replace("&nbsp;",' ',strip_tags(ucfirst($details->desc))),
					'store_image'	=> $image ];
				}	
			}
			/* get all categories list */
			$all_categories = MobileModel::get_all_categories($user_latitude,$user_longitude,$lang,$this->admin_default_lang,2);
			//print_r($all_categories); exit;
			if(count($all_categories) > 0)
			{
				foreach($all_categories as $details)
				{	
					//$image = MobileModel::get_category_image($details->cate_img);
					$category_list[] = ['category_id' 	=> intval($details->cate_id),
					'category_name' => ucfirst($details->category_name)
					]	;
				}
				
			}
			$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
			$data 	= ['category_list'		  => $category_list,
			'all_grocery_details' => $all_grocery_details];
			$encode = [ 'code'			=> 200,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* all restaurant lists  page */
		public function all_restaurant_lists(Request $request)
		{	//print_r($request->all()); exit;
			$user_latitude	 = $request->get('user_latitude'); 
			$user_longitude	 = $request->get('user_longitude');
			$lang 			 = $request->get('lang');
			$page 			 = ($request->get('page') != '') ? $request->page : 1;
			$cate_id 		 = $request->get('category_id');
			$lat_err_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longErr_req_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$validator = Validator::make($request->all(),['user_latitude' => 'required','user_longitude' => 'required'],['user_latitude.required' => $lat_err_req_msg,'user_longitude.required' => $longErr_req_msg]);
			if($validator->fails())
			{
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$route = \Request::segment(3);
			/* check category id exits */
			if($route == "category_based_restaurant")
			{
				$catId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_CATEGORY_ID','Please enter category ID!');
				$catId_not_msg = MobileModel::get_lang_text($lang,'API_CAT_DOESNT_EXIST','Category does not exists!');
				$valid_catIdmsg= MobileModel::get_lang_text($lang,'API_VALID_CAT_ID','Enter valid category ID!');
				$validator = Validator::make($request->all(), 
				['category_id' => [	'required',
				'numeric',
				Rule::exists('gr_category','cate_id')->where(function ($query) {
					$query->where('cate_status','=','1')->where('cate_type','=','1');
				})
				]
				],
				['category_id.required' => $catId_req_msg,
				'category_id.exists'    => $catId_not_msg,
				'category_id.numeric'	=> $valid_catIdmsg] 														);
				if($validator->fails())
				{
					$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}	
			}
			/* category based stores */
			if($cate_id != '')
			{
				$all_grocery = MobileModel::all_grocery_list($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1,$page,$cate_id);	
			}
			else
			{
				/* all stores */
				$all_grocery = MobileModel::all_grocery_list($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1,$page);
			}
			$all_grocery_details = $category_list = array();
			if(count($all_grocery) > 0)
			{
				foreach($all_grocery as $details)
				{	
					$wk_time = MobileModel::get_wk_time($details->id);
					$image = MobileModel::get_image_restaurant($details->st_logo,"logo");
					$all_grocery_details[] = ['restaurant_id' 	=> intval($details->id),
					'restaurant_name' 	=> ucfirst($details->st_name),
					'restaurant_rating'	=> intval($details->avg_val),
					'restaurant_desc'	=> str_replace("&nbsp;",' ',strip_tags(ucfirst($details->desc))),
					'today_wking_time'	=> $wk_time,
					'restaurant_image'	=> $image,
					'restaurant_status'	=> $details->store_closed ];
				}	
			}
			/* get all categories list */
			$all_categories = MobileModel::get_all_categories($user_latitude,$user_longitude,$lang,$this->admin_default_lang,1);
			if(count($all_categories) > 0)
			{
				foreach($all_categories as $details)
				{
					$category_list[] = ['category_id' 	=> intval($details->cate_id),
					'category_name' => ucfirst($details->category_name)]	;
				}
				
			}
			$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
			$code   = 200;
			if(count($all_grocery_details) <= 0)
			{
				$code   = 400;
			}
			$data 	= ['category_list'		  => $category_list,
			'all_restautrant_details' => $all_grocery_details];
			$encode = [ 'code'			=> $code,
			'message'		=> $msge,
			'data'			=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* grocery details */
		public function grocery_details(Request $request)
		{
			$store_id 		 = $request->store_id;
			$lang 			 = $request->lang;
			$review_page 	 = ($request->review_page_no == '') ? 1 : $request->review_page_no;
			$storeId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_STORE_ID','Please enter store ID!');
			$storeId_not_msg = MobileModel::get_lang_text($lang,'API_STORE_DOESNT_EXIST','Store does not exists!');
			$valid_storeIdmsg= MobileModel::get_lang_text($lang,'API_VALID_STORE_ID','Enter valid store ID!');
			$validator = Validator::make($request->all(),
			['store_id' => ['required',
			'numeric',
			Rule::exists('gr_store','id')->where(function ($query) {
				$query->where('st_status','=','1')->where('st_type','=','2');
			})
			]
			],
			['store_id.required' => $storeId_req_msg,
			'store_id.numeric' 	 => $valid_storeIdmsg,
			'store_id.exists'	 => $storeId_not_msg
			]);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$get_restaurant_details = MobileModel::get_shop_details($store_id,$request->lang,$this->admin_default_lang,2);
			if(empty($get_restaurant_details))
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' =>$text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	$review_array = $main_cate_array = array();
				$logo =  MobileModel::get_image_store($get_restaurant_details->st_logo,"logo");
				$image = explode('/**/',$get_restaurant_details->st_banner,-1);
				$banner = array();
				$count = count($image);
				//print_r($image); 
				//echo count($image); 
				if(count($image) > 0)
				{
					for($i=0;$i<$count;$i++)
					{
						$img = MobileModel::get_image_store($image[$i]);
						$banner[$i] = $img;
					}
				}
				else
				{
					$path = url('')."/public/images/noimage/".config::get('no_shop_banner');
				 	
					$banner[0] = $path;
				}
				//exit;
				$store_details = array('store_id'	=>intval($get_restaurant_details->id),
				'store_name'		=>ucfirst($get_restaurant_details->st_name),
				'store_logo'		=> $logo,
				'store_banner'		=> $banner,
				'store_desc'		=> str_replace("&nbsp;",' ',strip_tags($get_restaurant_details->st_desc)),
				'minimum_order'		=> ($get_restaurant_details->st_minimum_order == '') ? 0.00 : $get_restaurant_details->st_minimum_order,
				'store_currency' 	=> $get_restaurant_details->st_currency,
				//'pre_order'		=>	($get_restaurant_details->st_pre_order != '' && $get_restaurant_details->st_pre_order == 0) ? MobileModel::get_lang_text($lang,'API_NO','No') : MobileModel::get_lang_text($lang,'API_YES','Yes'),
				'store_rating'		=> intval($get_restaurant_details->avg_val),
				'cancellation_policy' => $get_restaurant_details->mer_cancel_policy,
				'refund_status'		=> $get_restaurant_details->refund_status,
				'cancel_status'		=> $get_restaurant_details->cancel_status,
				'store_location'	=> $get_restaurant_details->st_address,
				'store_status'		=> $get_restaurant_details->store_closed,
				'delivery_time'		=> $get_restaurant_details->st_delivery_time.' '.$get_restaurant_details->st_delivery_duration
				);
				/* display review details */
				$get_reviews  = MobileModel::get_shop_review($store_id,'store',$review_page);
				if(count($get_reviews) > 0)
				{	
					foreach($get_reviews as $review)
					{
						$review_array[] = array('review_customer_name' => ucfirst($review->cus_fname),
						'review_customer_profile' => MobileModel::get_cus_image($review->cus_image),
						'review_comments'		=>ucfirst(strip_tags($review->review_comments)),
						'review_rating'			=> $review->review_rating);
					}
				}
				/* get category lists */
				$get_category  = MobileModel::get_categories($store_id,2,$request->lang,$this->admin_default_lang);
				if(count($get_category) > 0)
				{
					foreach($get_category as $key => $values)
					{	
						$sub_cate_array = array();
						foreach($values as $details)
						{
							$sub_cate_array[] = array('sub_category_id' 	=> intval($details->pro_sc_id),
							'sub_category_name' 	=> ucfirst($details->sc_name));
						}
						$keyvalues = explode('~~',$key);
						$main_cate_array[]  = array('main_category_id' 	=> intval($keyvalues[0]),
						'main_category_name'=> $keyvalues[1],
						'sub_category_list' => $sub_cate_array);
					}
				}
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data 	= ['store_info' 	=> $store_details,
				'category_list' => $main_cate_array,
				'store_review'	=> $review_array];
				$encode = ['code' => 200,'message' => $msge,'data'	=> $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* restaurant details */
		public function restaurant_details(Request $request)
		{ 
			$user = JWTAuth::user();
			$store_id 		= $request->restaurant_id;
			$lang 			= $request->lang;
			$page_no 	  = ($request->page_no == '') ? 1 : $request->page_no;
			$review_page 	= ($request->review_page_no == '') ? 1 : $request->review_page_no;
			$item_type 	    = $request->item_type;
			$restId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_REST_ID','Please enter restaurant ID!');
			$restId_not_msg = MobileModel::get_lang_text($lang,'API_REST_DOESNT_EXIST','Restaurant does not exists!');
			$valid_restIdmsg= MobileModel::get_lang_text($lang,'API_VALID_REST_ID','Enter valid restaurant ID!');
			
			$validator = Validator::make($request->all(), 
			['restaurant_id' => ['required',
			'numeric',
			Rule::exists('gr_store','id')->where(function ($query) {
				$query->where('st_status','=','1')->where('st_type','=','1');
			})
			]
			],
			['restaurant_id.required'=> $restId_req_msg,
			'restaurant_id.numeric'	 => $valid_restIdmsg,
			'restaurant_id.exists'	 => $restId_not_msg,
			]);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$get_restaurant_details = MobileModel::get_shop_details($store_id,$request->lang,$this->admin_default_lang,1);
			//print_r($get_restaurant_details); exit;
			if(empty($get_restaurant_details))
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	$review_array = $main_cate_array = $item_array = array();
				$logo =  MobileModel::get_image_restaurant($get_restaurant_details->st_logo,"logo");
				$image = explode('/**/',$get_restaurant_details->st_banner,-1);
				$banner = array();
				$count = count($image);
				//print_r($image); 
				//echo count($image); 
				if(count($image) > 0)
				{
					for($i=0;$i<$count;$i++)
					{
						$img = MobileModel::get_image_restaurant($image[$i]);
						$banner[$i] = $img;
					}
				}
				else
				{
					$path = url('')."/public/images/noimage/".config::get('no_shop_banner');
				 	
					$banner[0] = $path;
				}
				$store_details = array('restaurant_id'	=> intval($get_restaurant_details->id),
				'restaurant_name'		=> ucfirst($get_restaurant_details->st_name),
				'restaurant_logo'		=> $logo,
				'restaurant_banner'		=> $banner,
				'restaurant_desc'		=> str_replace("&nbsp;",' ',strip_tags($get_restaurant_details->st_desc)),
				'minimum_order'			=> ($get_restaurant_details->st_minimum_order == '') ? 0.00 : $get_restaurant_details->st_minimum_order,
				'restaurant_currency' 	=> $get_restaurant_details->st_currency,
				'pre_order'				=>	($get_restaurant_details->st_pre_order == '' || $get_restaurant_details->st_pre_order == 0) ? MobileModel::get_lang_text($lang,'API_NO','No') : MobileModel::get_lang_text($lang,'API_YES','Yes'),
				'restaurant_rating'		=> intval($get_restaurant_details->avg_val),
				'cancellation_policy'	 => $get_restaurant_details->mer_cancel_policy,
				'refund_status'			=> $get_restaurant_details->refund_status,
				'cancel_status'			=> $get_restaurant_details->cancel_status,
				'restaurant_location'	=> $get_restaurant_details->st_address,
				'delivery_time'			=> $get_restaurant_details->st_delivery_time.' '.$get_restaurant_details->st_delivery_duration,
				'restaurant_status'		=> $get_restaurant_details->store_closed,
				);
				/* working hours details */
				$wk_hrs = array();
				$wk_hrs_details = DB::table('gr_res_working_hrs')->select('wk_closed','wk_date','wk_start_time','wk_end_time')->where('wk_res_id','=',$store_id)->get();
				if(count($wk_hrs_details) > 0)
				{
					foreach($wk_hrs_details as $details)
					{
						$wk_hrs[] = ['available_status' => ($details->wk_closed == 0)  ? 'Available' : 'Closed',
									 'working_date'		=> $details->wk_date,
									 'working_from_time' => $details->wk_start_time,
									 'working_end_time' => $details->wk_end_time,
									];
					}
				}
				/* display review details */
				$get_reviews  = MobileModel::get_shop_review($store_id,'restaurant',$review_page);
				if(count($get_reviews) > 0)
				{	
					foreach($get_reviews as $review)
					{
						$review_array[] = array('review_customer_name' 		=> ucfirst($review->cus_fname),
						'review_customer_profile' 	=> MobileModel::get_cus_image($review->cus_image),
						'review_comments'			=> ucfirst(strip_tags($review->review_comments)),
						'review_rating'				=> $review->review_rating);
					}
				}
				/* get category lists */
				$get_category  = MobileModel::get_categories($store_id,1,$request->lang,$this->admin_default_lang);
				if(count($get_category) > 0)
				{
					foreach($get_category as $key => $values)
					{	
						$sub_cate_array = array();
						foreach($values as $details)
						{
							$sub_cate_array[] = array('sub_category_id' => intval($details->pro_sc_id),'sub_category_name' => ucfirst($details->sc_name));
						}
						$keyvalues = explode('~~',$key);
						$main_cate_array[]  = array('main_category_id' 	=> intval($keyvalues[0]),
						'main_category_name'=> $keyvalues[1],
						'sub_category_list' => $sub_cate_array);
					}
				}
				//echo "test"; exit;
				/* show all item */
				$get_item_details = MobileModel::get_items($store_id,'','','',2,'',$page_no,$lang,$this->admin_default_lang,$user->cus_id,$item_type); // 2- for product type
				//print_r($get_item_details);
				if(count($get_item_details) > 0)
				{
					$st_name = $main_ca_name = $sub_ca_name = '';
				
				
				foreach($get_item_details as $details)
				{	
					$spl_offers = 'No';
					if(($details->stock > 0) || ($details->stock <= 0 && $this->general_setting->gs_show_inventory == 1))
					{
						
						
						$item_array[] = array('item_id'	=> intval($details->pro_id),
						'item_name' 			=> ucfirst($details->item_name),
						'item_rating' 			=> intval($details->avg_val),
						'item_image'			=> MobileModel::get_image_item($details->image),
						'item_content' 			=> $details->contains,
						'item_desc' 			=> ucfirst(str_replace("&nbsp;",'',strip_tags($details->desc))),
						'item_type'				=> ($details->pro_veg == 1) ? 'Veg' : 'Non_veg',
						'item_currency' 		=> $details->pro_currency,
						'item_original_price' 	=> $details->pro_original_price,
						'item_has_discount'		=> ($details->pro_has_discount == 'yes') ? 'Yes' : 'No',
						'item_discount_price'	=> ($details->pro_discount_price == '') ? '' :$details->pro_discount_price,
						'item_has_choice'		=> ($details->pro_had_choice == 1) ? "Yes" : "No",
						'item_quantity'			=> $details->stock,
						'item_availablity'		=> $details->availablity,
						'item_is_favourite'		=> $details->wishlist
						);
					}
				}
			}
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data 	= ['restaurant_info'	 => $store_details,
							'working_hours'		 => $wk_hrs,
							'category_list'		 => $main_cate_array,
							'item_list'			 => $item_array,
							'restaurant_review'	 => $review_array];
				$encode = ['code' => 200,'message' => $msge,'data' => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* caegory based product list */
		public function category_based_products(Request $request)
		{	
			$store_id	  = $request->store_id;
			$main_cate_id = $request->main_category_id;
			$sub_cate_id  = ($request->sub_category_id == '') ? "" : $request->sub_category_id;
			$sortby 	  = $request->sort_by;
			$text  		  = $request->search_text;
			$item_type	  = $request->item_type; //0 -non-veg; 1 - veg
			$page_no 	  = ($request->page_no == '') ? 1 : $request->page_no;
			$lang 		  = $request->lang;
			
			$storeId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_STORE_ID','Please enter store ID!');
			$storeId_not_msg = MobileModel::get_lang_text($lang,'API_STORE_DOESNT_EXIST','Store does not exists!');
			$valid_storeIdmsg= MobileModel::get_lang_text($lang,'API_VALID_STORE_ID','Enter valid store ID!');
			$catId_req_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_CATEGORY_ID','Please enter category ID!');
			$catId_not_msg 	 = MobileModel::get_lang_text($lang,'API_CAT_DOESNT_EXIST','Category does not exists!');
			$valid_catIdmsg	 = MobileModel::get_lang_text($lang,'API_VALID_CAT_ID','Enter valid category ID!');
			
			$validator = Validator::make($request->all(), ['store_id' => [	'required',
			'numeric',
			Rule::exists('gr_store','id')->where(function ($query) {
				$query->where('st_status','=','1')->where('st_type','=','2');
			})
			],
			'main_category_id' => [	'required',
			'numeric',
			Rule::exists('gr_proitem_maincategory','pro_mc_id')->where(function ($query) {
				$query->where('pro_mc_status','=','1')->where('pro_mc_type','=','2');
			})
			]
			],
			['store_id.required' 		=> $storeId_req_msg,
			'main_category_id.required' => $catId_req_msg,
			'store_id.numeric' 			=> $valid_storeIdmsg,
			'store_id.exists' 			=> $storeId_not_msg,
			'main_category_id.numeric'	=> $valid_catIdmsg,
			'main_category_id.exists' 	=> $catId_not_msg]);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$user 		  = JWTAuth::user();
			$get_item_details = MobileModel::get_items($store_id,$main_cate_id,$sub_cate_id,$sortby,1,$text,$page_no,$lang,$this->admin_default_lang,$user->cus_id,$item_type); //- for product type
			//print_r($get_item_details); exit;
			if(count($get_item_details) <= 0)
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$item_array = array();
				$st_name = $main_ca_name = $sub_ca_name = '';
				foreach($get_item_details as $details)
				{	
					$st_name 		= $get_item_details[0]->store_name;
					$main_ca_name 	= $get_item_details[0]->main_ca_name;
					if($sub_cate_id != '')
					{
						$sub_ca_name	= $get_item_details[0]->sub_ca_name;
					}
					$item_array[] = array('product_id'				=> intval($details->pro_id),
					'product_name' 			=> ucfirst($details->item_name),
					'product_image'			=> MobileModel::get_image_product($details->image),
					'product_content' 			=> $details->contains,
					'product_desc'				=> strip_tags(ucfirst($details->desc)),
					'product_type'				=> ($details->pro_veg == 1) ? 'Veg' : 'Non_veg',
					'product_currency' 		=> $details->pro_currency,
					'product_original_price' 	=> $details->pro_original_price,
					'product_has_discount'		=> ($details->pro_has_discount == 'yes') ? 'Yes' : 'No',
					'product_discount_price'	=> ($details->pro_discount_price == '') ? '' : $details->pro_discount_price,
					'product_quantity'			=> $details->stock,
					'product_availablity'		=> $details->availablity,
					'product_is_favourite'		=> $details->wishlist,
					);
				}
				//print_r($item_array); exit;
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data 	= ['store_id'		   => $store_id,
				'store_name'	   => ucfirst($st_name),
				'main_category_id' => $main_cate_id,
				'main_category_name'=> $main_ca_name,
				'sub_category_id'	=> $sub_cate_id,
				'sub_category_name'	=> $sub_ca_name,
				'product_lists'		=> $item_array];
				$encode = array('code' => 200,'message' => $msge,'data' => $data
				);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* caegory based items list */
		public function category_based_items(Request $request)
		{	
			$store_id	  = $request->restaurant_id;
			$main_cate_id = $request->main_category_id;
			$sub_cate_id  = ($request->sub_category_id == '') ? "" : $request->sub_category_id;
			$sortby 	  = $request->sort_by;
			$text  		  = $request->search_text;
			$item_type	  = $request->item_type; //0 -non-veg; 1 - veg
      $view_all	  = $request->all;
			$page_no 	  = ($request->page_no == '') ? 1 : $request->page_no;
			$lang 		  = $request->lang;
			$user 		  = JWTAuth::user();
			
			$restId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_REST_ID','Please enter restaurant ID!');
			$valid_restIdmsg= MobileModel::get_lang_text($lang,'API_VALID_REST_ID','Enter valid restaurant ID!');
			$restId_not_msg = MobileModel::get_lang_text($lang,'API_REST_DOESNT_EXIST','Restaurant does not exists!');
			$catId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_CATEGORY_ID','Please enter category ID!');
			$catId_not_msg = MobileModel::get_lang_text($lang,'API_CAT_DOESNT_EXIST','Category does not exists!');
			$valid_catIdmsg= MobileModel::get_lang_text($lang,'API_VALID_CAT_ID','Enter valid category ID!');
			
			$validator = Validator::make($request->all(), ['restaurant_id' 	=> ['required',
																				'numeric',
																				Rule::exists('gr_store','id')->where(function ($query) {
																					$query->where('st_status','=','1')->where('st_type','=','1');
																				})
																				],
														'main_category_id' 	=> ['required_if:all,0']
														],
														['restaurant_id.required' 	=> $restId_req_msg,
														'main_category_id.required_if' => $catId_req_msg,
														'restaurant_id.numeric' 	=> $valid_restIdmsg,
														'restaurant_id.exists' 		=> $restId_not_msg,
														'main_category_id.numeric' 	=> $valid_catIdmsg,
														'main_category_id.exists' 	=> $catId_not_msg]
										);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($main_cate_id !='')
			{
				$exists = DB::table('gr_proitem_maincategory')->select('pro_mc_id')->where('pro_mc_status','=','1')->where('pro_mc_type','=','1')->where('pro_mc_id','=',$main_cate_id)->first();
				if(empty($exists))
				{
					$encode = [ 'code' => 400,'message' => "Category does not exists!",'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			
			if($view_all == 1)															
			{
				$get_item_details = MobileModel::get_items($store_id,'','',$sortby,2,$text,$page_no,$lang,$this->admin_default_lang,$user->cus_id,$item_type); // 2- for product type
			}
			else
			{
				$get_item_details = MobileModel::get_items($store_id,$main_cate_id,$sub_cate_id,$sortby,2,$text,$page_no,$lang,$this->admin_default_lang,$user->cus_id,$item_type); // 2- for product type
			}
			
			//print_r($get_item_details); exit;
			if(count($get_item_details) <= 0)
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$item_array = array();
				$st_name = $main_ca_name = $sub_ca_name = '';
				foreach($get_item_details as $details)
				{	
					if(($details->stock > 0) || ($details->stock <= 0 && $this->general_setting->gs_show_inventory == 1))
					{
						$st_name 		= $get_item_details[0]->store_name;
						$main_ca_name 	= $get_item_details[0]->main_ca_name;
						if($sub_cate_id != '')
						{
							$sub_ca_name	= $get_item_details[0]->sub_ca_name;
						}
						$item_array[] = array('item_id'				=> intval($details->pro_id),
						'item_name' 			=> ucfirst($details->item_name),
						'item_rating' 			=> intval($details->avg_val),
						'item_image'			=> MobileModel::get_image_item($details->image),
						'item_content' 		=> $details->contains,
						'item_desc' 			=> ucfirst(str_replace("&nbsp;",'',strip_tags($details->desc))),
						'item_type'			=> ($details->pro_veg == 1) ? 'Veg' : 'Non_veg',
						'item_currency' 		=> $details->pro_currency,
						'item_original_price' 	=> $details->pro_original_price,
						'item_has_discount'	=> ($details->pro_has_discount == 'yes') ? 'Yes' : 'No',
						'item_discount_price'	=> ($details->pro_discount_price == '') ? '' : $details->pro_discount_price,
						'item_has_choice'		=> ($details->pro_had_choice == 1) ? "Yes" : "No",
						'item_quantity'		=> $details->stock,
						'item_availablity'		=> $details->availablity,
						'item_is_favourite'	=> $details->wishlist,
						);
					}
				}
				//print_r($item_array); exit;
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data   = ['restaurant_id'		=> $store_id,
				'restaurant_name'	=> ucfirst($st_name),
				'main_category_id' 	=> $main_cate_id,
				'main_category_name' => ucfirst($main_ca_name),
				'sub_category_id'	=> $sub_cate_id,
				'sub_category_name'	=> ucfirst($sub_ca_name),
				'item_lists'		=> $item_array];
				$encode = array('code' => 200,'message' => $msge,'data'	=> $data
				);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* choice list for item */
		public function choice_list(Request $request)
		{
			$item_id	  	= $request->item_id;
			$lang		  	= $request->lang;
			$itemId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_ITEM_ID','Please enter item ID!');
			$itemId_not_msg = MobileModel::get_lang_text($lang,'API_ITEM_DOESNT_EXIST','Item does not exists!');
			$valid_itemIdmsg= MobileModel::get_lang_text($lang,'API_VALID_ITEM_ID','Enter valid item ID!');
			$validator = Validator::make($request->all(), ['item_id' => ['required',
			'numeric',
			Rule::exists('gr_product','pro_id')->where(function ($query) {
				$query->where('pro_status','=','1')->where('pro_type','=','2');
			})]
			],
			['item_id.required' => $itemId_req_msg,
			'item_id.exists' 	=> $itemId_not_msg,
			'item_id.numeric' 	=> $valid_itemIdmsg
			]
			);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			/*  check product exist */
			$check_exists = MobileModel::check_id_exist($item_id,'gr_product','pro_id','pro_status','pro_had_choice');
			
			if(empty($check_exists))
			{
				$msg = MobileModel::get_lang_text($lang,'API_IT_NT_AVAIL','Item not available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			elseif($check_exists->pro_had_choice == 2)	//item has no choices
			{
				$msg = MobileModel::get_lang_text($lang,'API_HAS_NO_CH','Item has no choice');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			/* get choice list */
			$get_choices = MobileModel::get_choices($item_id,$lang,$this->admin_default_lang);
			if(count($get_choices) <= 0)
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				$ch_array = array();
				foreach($get_choices as $choices)
				{
					$ch_array[] = array('choice_id' => $choices->pc_choice_id,
					'choice_name'	=> ucfirst($choices->choice_name),
					'choice_currency'	=> $choices->pro_currency,
					'choice_price'		=> ($choices->pc_price == '') ? 0.00 : $choices->pc_price
					); 
				}
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data 	= ['item_id'		=> $item_id,
				'item_name'		=> ucfirst($get_choices[0]->pro_name),
				'choice_list'	=>$ch_array];
				$encode = array('code' => 200,'message' => $msge,'data'	=> $data
				);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* product details */
		public function product_details(Request $request)
		{
			$product_id		= $request->product_id;
			$lang 			= $request->lang;
			$review_page 	= ($request->review_page_no == '') ? 1 : $request->review_page_no;
			$user_id 		= JWTAuth::user()->cus_id;
			$pdtId_req_msg	= MobileModel::get_lang_text($lang,'API_ENTER_PDT_ID','Please enter product ID!');
			$pdtId_not_msg 	= MobileModel::get_lang_text($lang,'API_PDT_DOESNT_EXIST','Product does not exists!');
			$valid_pdtIdmsg	= MobileModel::get_lang_text($lang,'API_VALID_PDT_ID','Enter valid product ID!');
			$validator = Validator::make($request->all(), 	['product_id' => [	'required',
			'numeric',
			Rule::exists('gr_product','pro_id')->where(function ($query) {
				$query->where('pro_status','=','1')->where('pro_type','=','1');
			})]
			],
			['product_id.required' 	=> $pdtId_req_msg,
			'product_id.exists'		=> $pdtId_not_msg,
			'product_id.numeric' 	=> $valid_pdtIdmsg
			]
			);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$item_details = MobileModel::get_product_details($product_id,1,$lang,$this->admin_default_lang,$user_id);
			if(empty($item_details) === true)
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				$image = array();
				if($item_details->pro_images != '')
				{
					$images = explode('/**/',$item_details->pro_images);
					if(count($images) > 0)
					{	
						for($i=0;$i<count($images); $i++)
						{
							$image[$i] = MobileModel::get_image_product($images[$i]);
						}
					}
					else
					{
						$image[0] = url('')."/public/images/noimage/".config::get('no_product');
					}
				}
				else
				{
					$image[0] = url('')."/public/images/noimage/".config::get('no_product');
				}
				//print_r($image); exit;
				/* get specification details */
				$spec_array = $review_array = $related_pdt = array();
				$get_spec = MobileModel::get_specification($product_id,$lang,$this->admin_default_lang);
				if(count($get_spec) > 0)
				{
					foreach($get_spec as $spec)
					{
						$spec_array[] = array('specification_title' 		=> $spec->spec_title,
						'specification_description'	=> strip_tags($spec->spec_desc));
					}
				}
				/* display review details */
				$get_reviews  = MobileModel::get_product_review($product_id,'product',$review_page);
				if(count($get_reviews) > 0)
				{	
					foreach($get_reviews as $review)
					{
						$review_array[] = array('review_customer_name' => ucfirst($review->cus_fname),
						'review_customer_profile' => MobileModel::get_cus_image($review->cus_image),
						'review_comments'		=>ucfirst(strip_tags($review->review_comments)),
						'review_rating'			=> $review->review_rating);
					}
				}
				/* get related product details */
				$related_products = MobileModel::get_relatedPdt_details($product_id,1,$lang,$this->admin_default_lang,$user_id);
				if(count($related_products) > 0)
				{	
					foreach($related_products as $details)
					{
						$related_pdt[] = array('product_id'			=> intval($details->pro_id),
						'store_id'					=> intval($details->pro_store_id),
						'product_name' 			=> ucfirst($details->item_name),
						'product_rating' 			=> intval($details->avg_val),
						'product_image'			=> MobileModel::get_image_product($details->image),
						'product_content' 			=> $details->contains,
						'product_desc' 			=> strip_tags(ucfirst($details->desc)),
						'product_type'				=> ($details->pro_veg == 1) ? 'Veg' : 'Non_veg',
						'product_currency' 		=> $details->pro_currency,
						'product_original_price' 	=> $details->pro_original_price,
						'product_has_discount'		=> ($details->pro_has_discount == 'yes') ? 'Yes' : 'No',
						'product_discount_price'	=> ($details->pro_discount_price == '') ? '' : $details->pro_discount_price,
						'product_is_favourite'		=> $details->wishlist
						);
					}
				}
				$discount_percent = 0;
				if($item_details->pro_has_discount == 'yes')
				{
					$discount_percent = (($item_details->pro_original_price - $item_details->pro_discount_price) /$item_details->pro_original_price) * 100 ;
				}
				$product_info  = array('product_id'				=> intval($item_details->pro_id),
				'product_name' 			=> ucfirst($item_details->item_name),
				'product_rating'		=> round($item_details->avg_val),
				'product_image'			=> $image,
				'product_content' 		=> $item_details->contains,
				'product_type'			=> ($item_details->pro_veg == 1) ? 'Veg' : 'Non_veg',
				'product_desc' 			=> strip_tags($item_details->desc),
				'product_currency' 		=> $item_details->pro_currency,
				'product_original_price' 	=> $item_details->pro_original_price,
				'product_has_discount'		=> ($item_details->pro_has_discount == 'yes') ? 'Yes' : 'No',
				'product_discount_price'	=> ($item_details->pro_discount_price == '') ? '' : $item_details->pro_discount_price,
				'product_discount_percent'  => floor($discount_percent),
				'product_has_tax'			=> $item_details->pro_had_tax,
				'product_tax'				=> ($item_details->pro_had_tax == 'Yes') ? $item_details->pro_tax_name.' '.$item_details->pro_tax_percent : 0,
				'product_quantity'			=> $item_details->stock,
				'product_availablity'		=> $item_details->availablity,
				'product_specification'		=> $spec_array,
				'product_is_favourite'		=> $item_details->wishlist			
				);
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data 	= ['product_info'		=> $product_info,
				'product_reviews'	=> $review_array,
				'related_products' 	=> $related_pdt];
				$encode = ['code'	=>	200,'message' => $msge,'data' => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* item details */
		public function item_details(Request $request)
		{
			$product_id		= $request->item_id;
			$lang 			= $request->lang;
			$review_page 	= ($request->review_page_no == '') ? 1 : $request->review_page_no;
			$user_id 		= JWTAuth::user()->cus_id;
			$itemId_req_msg = MobileModel::get_lang_text($lang,'API_ENTER_ITEM_ID','Please enter item ID!');
			$itemId_not_msg = MobileModel::get_lang_text($lang,'API_ITEM_DOESNT_EXIST','Item does not exists!');
			$valid_itemIdmsg= MobileModel::get_lang_text($lang,'API_VALID_ITEM_ID','Enter valid item ID!');
			$validator = Validator::make($request->all(), ['item_id' => ['required',
			'numeric',
			Rule::exists('gr_product','pro_id')->where(function ($query) {
				$query->where('pro_status','=','1')->where('pro_type','=','2');
			})]
			],
			['item_id.required' => $itemId_req_msg,
			'item_id.exists' 	=> $itemId_not_msg,
			'item_id.numeric'	=> $valid_itemIdmsg
			]
			);
			if($validator->fails())
			{	
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$item_details = MobileModel::get_product_details($product_id,2,$lang,$this->admin_default_lang,$user_id);
			if(empty($item_details) === true)
			{
				$text = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				$image = array();
				if($item_details->pro_images != '')
				{
					$images = explode('/**/',$item_details->pro_images);
					if(count($images) > 0)
					{	
						for($i=0;$i<count($images); $i++)
						{
							$image[$i] = MobileModel::get_image_item($images[$i]);
						}
					}
					else
					{
						$image[0] = url('')."/public/images/noimage/".config::get('no_product');
					}
				}
				else
				{
					$image[0] = url('')."/public/images/noimage/".config::get('no_product');
				}
				//print_r($image); exit;
				/* get specification details */
				$spec_array = $review_array = $related_pdt  = array();
				$get_spec = MobileModel::get_specification($product_id,$lang,$this->admin_default_lang);
				if(count($get_spec) > 0)
				{
					foreach($get_spec as $spec)
					{
						$spec_array[] = array('specification_title' 		=> $spec->spec_title,
						'specification_description'	=> strip_tags($spec->spec_desc));
					}
				}
				/* display review details */
				$get_reviews  = MobileModel::get_product_review($product_id,'item',$review_page);
				if(count($get_reviews) > 0)
				{	
					foreach($get_reviews as $review)
					{
						$review_array[] = array('review_customer_name' => ucfirst($review->cus_fname),
						'review_customer_profile' => MobileModel::get_cus_image($review->cus_image),
						'review_comments'		=>ucfirst(strip_tags($review->review_comments)),
						'review_rating'			=> $review->review_rating);
					}
				}
				/* get related item details */
				$related_products = MobileModel::get_relatedPdt_details($product_id,2,$lang,$this->admin_default_lang,$user_id);
				if(count($related_products) > 0)
				{	
					foreach($related_products as $details)
					{
						$related_pdt[] = array('item_id'		=> intval($details->pro_id),
												'restaurant_id'		=> intval($details->pro_store_id),
												'item_name' 			=> ucfirst($details->item_name),
												'item_rating' 			=> intval($details->avg_val),
												'item_image'			=> MobileModel::get_image_item($details->image),
												'item_content'			=> $details->contains,
												'itemt_desc' 			=> ucfirst(str_replace("&nbsp;",'',strip_tags($details->desc))),
												'item_type'			=> ($details->pro_veg == 1) ? 'Veg' : 'Non_veg',
												'item_availablity' 	=> $details->availablity,
												'item_currency' 		=> $details->pro_currency,
												'item_original_price' 	=> $details->pro_original_price,
												'item_has_discount'	=> ($details->pro_has_discount == 'yes') ? 'Yes' : 'No',
												'item_discount_price'	=> ($details->pro_discount_price == '') ? '' : $details->pro_discount_price,
												'item_is_favourite'	=> $details->wishlist
												);
					}
				}
				$choice_array = array();
				if($item_details->pro_had_choice == '1')
				{
					$get_choices = MobileModel::get_choices($product_id,$lang,$this->admin_default_lang);
					foreach($get_choices as $choices)
					{
						$choice_array[] = array('choice_id' => $choices->pc_choice_id,
						'choice_name'	=> ucfirst($choices->choice_name),
						'choice_currency'	=> $choices->pro_currency,
						'choice_price'		=> ($choices->pc_price != '') ? $choices->pc_price : '0.00'); 
					}
				}
				
				$yes = MobileModel::get_lang_text($lang,'API_YES','Yes');
				$no = MobileModel::get_lang_text($lang,'API_NO','No');
				$discount_percent = 0;
				if($item_details->pro_has_discount == 'yes')
				{
					$discount_percent = (($item_details->pro_original_price - $item_details->pro_discount_price) /$item_details->pro_original_price) * 100 ;
				}
				/* get already added cart */
				$cart_details = MobileModel::get_last_cart($user_id,$item_details->pro_id);
				//print_r($cart_details); exit;
				$exist_in_cart = "No";
				if(!empty($cart_details))
				{
					$exist_in_cart = "Yes";						
				}
				$product_info  = array('item_id'				=> intval($item_details->pro_id),
										'item_name' 			=> ucfirst($item_details->item_name),
										'item_rating'			=> round($item_details->avg_val),
										'item_image'			=> $image,
										'item_content' 			=> $item_details->contains,
										'item_type'				=> ($item_details->pro_veg == 1) ? 'Veg' : 'Non_veg',
										'item_desc' 			=> ucfirst(str_replace("&nbsp;",'',strip_tags($item_details->desc))),
										'item_currency' 		=> $item_details->pro_currency,
										'item_original_price' 	=> $item_details->pro_original_price,
										'item_has_discount'		=> ($item_details->pro_has_discount == 'yes') ? 'Yes' : 'No',
										'item_discount_price'	=> ($item_details->pro_discount_price == '') ? '' : $item_details->pro_discount_price,
										'item_discount_percent'  => floor($discount_percent),
										'item_has_choice'	=> ($item_details->pro_had_choice == '1') ? $yes : $no,
										'item_has_tax'			=> $item_details->pro_had_tax,
										'item_tax'				=> ($item_details->pro_had_tax == 'Yes') ? $item_details->pro_tax_name.' '.$item_details->pro_tax_percent : 0,
										'item_quantity'			=> $item_details->stock,
										'item_availablity'		=> $item_details->availablity,
										'item_specificatioon'	=> $spec_array,
										'item_is_favourite'		=> $item_details->wishlist
										);
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$item_cart_amt = cart_amount($user_id);
				$item_cart_qty = cart_qty($user_id);
				$data 	= ['itemt_info'	=> $product_info,
							'choices'		=> $choice_array,
							'item_reviews'  => $review_array,
							'related_items'	=> $related_pdt,
							'exist_in_cart'	=> $exist_in_cart,
							'cart_quantity' => $item_cart_qty,
							'cart_amt' => number_format($item_cart_amt,2)
							];

				$encode = ['code'	=>	200,'message' => $msge,'data' => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}		
		/*  Common details ends */
		//CHANGE PASSWORD
		public function customer_reset_password(Request $request)
		{
			
			$old_password = $request->old_password; 
			$new_password = $request->new_password; 
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$oldpwd_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_OLD_PASSWORD','Please enter old password!');
			$newpwd_req_err_msg=MobileModel::get_lang_text($lang,'API_PLS_ENTER_NEW_PASSWORD','Please enter new password!');
			$pwd_min_6_err_msg =MobileModel::get_lang_text($lang,'API_PASSWORD_RULES','Password min. length should be 6!');
			$pwd_regex_err_msg =MobileModel::get_lang_text($lang,'API_PROTECT_PASSWORD_RULES','Password should be atleast one lower case, upper case, number and min.length 6!');
			$old_pwd_text = MobileModel::get_lang_text($lang,'API_OLD','Old');
			$new_pwd_text = MobileModel::get_lang_text($lang,'API_NEW','New');
			if($this->general_setting->gs_password_protect==1)
			{
				$validator = Validator::make($request->all(), 
				[ 'old_password' => 'required|min:6', 'new_password' => 'required|regex:/(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$)+/'],
				[ 'old_password.required'=>$oldpwd_req_err_msg,'old_password.min'=>$old_pwd_text.' '.$pwd_min_6_err_msg,'new_password.required'=>$newpwd_req_err_msg,'new_password.min'=>$new_pwd_text.' '.$pwd_min_6_err_msg,'new_password.regex'=>$pwd_regex_err_msg]
				);
			}
			else{ 
				$validator = Validator::make($request->all(), 
				[ 'old_password' => 'required|min:6', 'new_password' => 'required|min:6'],
				[ 'old_password.required'=>$oldpwd_req_err_msg,'old_password.min'=>$old_pwd_text.' '.$pwd_min_6_err_msg,'new_password.required'=>$newpwd_req_err_msg,'new_password.min'=>$new_pwd_text.' '.$pwd_min_6_err_msg]
				);
			}
			
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$user = JWTAuth::user();
				
				//echo $user->agent_password.'<hr>';
				if($user->cus_password != md5($old_password))
				{
					$msg = MobileModel::get_lang_text($lang,'API_INCORRECT_PASSWORD','Your old password does not match with our records! Please try again!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				elseif($old_password == $new_password){
					$msg = MobileModel::get_lang_text($lang,'API_NEW_PASS_NOT_SAME_CURRENT_PASS','New Password cannot be same as your current password. Please choose a different password!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}else{
					
					$insertArr = array('cus_password' => md5($new_password),'cus_decrypt_password' =>$new_password );
					$update = updatevalues('gr_customer',$insertArr,['cus_id'=>$user->cus_id]);
					$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
					$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
		}
		//GET CUSTOMER PROFILE
		public function customer_my_account(Request $request)
		{
			
			$user = JWTAuth::user();
			$msge = 'Fetched customer profile details succesfully!';
			
			if($user->cus_image != ''){
				$filename = public_path('images/customer/').$user->cus_image;
				if(file_exists($filename)){
					$user_avatar = url('public/images/customer/'.$user->cus_image );
					}else{
					$user_avatar = url('public/images/noimage/default_user_image.jpg');
				}
				}else{
				$user_avatar = url('public/images/noimage/default_user_image.jpg');
			}
			
			$data = ["user_id"		=>intval($user->cus_id),
					"user_name"		=>ucfirst($user->cus_fname),
					"user_email"	=>$user->cus_email,
					"user_phone"	=>$user->cus_phone1,
					"user_phone2"	=>$user->cus_phone2,
					"user_address"	=>$user->cus_address,
					"user_latitude"	=>$user->cus_latitude,
					"user_longitude"=>$user->cus_longitude,
					"user_avatar"	=>$user_avatar,
					"total_cart_count" => cart_count($user->cus_id)
					];
			$encode = ['code'=>200,'message'=>$msge,'data'	=> $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		//UPDATE CUSTOMER PROFILE
		public function customer_update_account(Request $request){
			$user 	= JWTAuth::user();
			$cus_id 	= $user->cus_id;
			$cus_name 	= $request->cus_name;
			$cus_email 	= $request->cus_email;
			$cus_phone1 = $request->cus_phone1;
			$cus_phone2	= $request->cus_phone2;
			$cus_address= $request->cus_address;
			$cus_lat 	= $request->cus_lat;
			$cus_long 	= $request->cus_long;
			$lang 	= $request->lang;
			
			$cus_name_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$valid_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$valid_phone2_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM2','Please enter valid phone number2!');
			$addr_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$cus_img_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_CUSTOMER_IMAGE','Please enter customer image!');
			$cus_img_dimen_err_msg	= MobileModel::get_lang_text($lang,'API_CUSTOMER_IMAGE_DIMEN_VAL','Customer Image Should be Width=300, Height=300!');
			
			
			$validator = Validator::make($request->all(),[  'cus_name'		=> 'required',
			'cus_email' 	=> 'required|string|email',
			'cus_phone1' 	=> 'required|only_cnty_code',
			'cus_address'	=> 'required',
			'cus_lat'		=> 'required',
			'cus_long'		=> 'required',
			'cus_image' 	=> 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
			],
			[
			'cus_name.required'			=> $cus_name_req_err_msg,
			'cus_email.required'		=> $email_req_err_msg,
			'cus_email.email'			=> $valid_email_err_msg,
			'cus_phone1.required'		=> $valid_phone_err_msg,
			'cus_phone1.only_cnty_code'	=> $valid_phone_err_msg,
			'cus_address.required'		=> $addr_req_err_msg,
			'cus_lat.required'			=> $lati_req_err_msg,
			'cus_long.required'			=> $longi_req_err_msg,
			'cus_image.required'		=> $cus_img_req_err_msg
			//'cus_image.dimensions'		=> $cus_img_dimen_err_msg	
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_id','!=',$cus_id)->where('cus_status','!=','2')->count();
			$check_phoneNumber_already_exsist = DB::table('gr_customer')->where('cus_phone1','=',$cus_phone1)->where('cus_id','!=',$cus_id)->where('cus_status','!=','2')->count();
			if($check_already_exsist > 0 )
			{
				$msg	= MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist >  0 )
			{
				$msg	= MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				$encode	= [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($user->cus_phone1 != $cus_phone1 && $user->cus_email != $cus_email ){
				$msg = MobileModel::get_lang_text($lang,'API_YOU_CANNOT_CHANGE','Sorry! You cannot change mobile number and email at a time.Update one by one!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$insertArr = array(	'cus_fname' 	=> $cus_name,
			'cus_email' 	=> $cus_email,
			'cus_phone1' 	=> $cus_phone1,
			'cus_phone2' 	=> $cus_phone2,
			'cus_address' 	=> $cus_address,
			'cus_latitude' 	=> $cus_lat,
			'cus_longitude'	=> $cus_long,               
			);
			$cus_image = '';
			if($request->hasFile('cus_image')) {
				$cus_image = 'customer'.time().'.'.request()->cus_image->getClientOriginalExtension();
				$destinationPath = public_path('images/customer');
				$customer = Image::make(request()->cus_image->getRealPath())->resize(300, 300);
				$customer->save($destinationPath.'/'.$cus_image,80);
				$insertArr['cus_image']=$cus_image;
			}
			/*----------------------CHECK PHONE NUMBER IS NEW  ------------*/
			if($user->cus_phone1 != $cus_phone1){
				$otp = mt_rand(100000, 999999);
				try{
					Twilio::message($cus_phone1, $otp);
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$insertArr = ['code' => 201,
					'message' => $msge,
					'data'	  => ['cus_image'	=> $cus_image,
					'otp'		=> $otp]
					];
					return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				catch (\Exception $e)
				{		
					/*----- hide for testing twilio in test account.And enable it while uses live twilio account -------*/
					/*$encode = array('code'=> 400,'message' => $e->getMessage(),'data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");*/
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$insertArr = ['code' => 201,
					'message' => $msge,
					'data'	  => ['cus_image'	=> $cus_image,
					'otp'		=> $otp]
					];
					return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				} elseif($user->cus_email != $cus_email){
				$code = mt_rand(100000, 999999);
				$send_mail_data = ['code' => $code,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang,'cus_email'=>$cus_email];
				$send = Mail::send('email.mobile_email_verification', $send_mail_data, function($message) use($send_mail_data)
				{	
					$msg = MobileModel::get_lang_text($send_mail_data['lang'],'API_CNFRM_MAIL','Confirm Your Mail');
					$message->to($send_mail_data['cus_email'])->subject($msg);
				});
				$msge=MobileModel::get_lang_text($lang,'API_VERICODE_SENT_MAIL','Verification code sent to your email. Please enter verification code');
				$insertArr = ['code' => 201,
				'message' => $msge,
				'data'	  => ['cus_image'	=> $cus_image,
				'otp'		=> $code]
				];
				return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				} else {
				
				$update = updatevalues('gr_customer',$insertArr,['cus_id' =>$cus_id]);
				/* get updated image */
				$get_det = DB::table('gr_customer')->select('cus_image','cus_fname','cus_email','cus_phone1','cus_phone2','cus_address','cus_latitude','cus_longitude','cus_id')->where('cus_id','=',$cus_id)->first();
				$user_avatar = url('public/images/noimage/default_user_image.jpg');
				if($get_det->cus_image != '')
				{
					$filename = public_path('images/customer/').$get_det->cus_image;
					if(file_exists($filename))
					{
						$user_avatar = url('public/images/customer/'.$get_det->cus_image );
					}
				}
				
				$msg 	= MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
				$encode = [ 'code' => 200,
				'message' => $msg,
				'data'=> ["user_id"			=>intval($get_det->cus_id),
							"user_name"		=>ucfirst($get_det->cus_fname),
							"user_email"	=>$get_det->cus_email,
							"user_phone"	=>$get_det->cus_phone1,
							"user_phone2"	=>$get_det->cus_phone2,
							"user_address"	=>$get_det->cus_address,
							"user_latitude"	=>$get_det->cus_latitude,
							"user_longitude"=>$get_det->cus_longitude,
							"user_avatar"	=>$user_avatar]
						];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		/*------------------------------ UDATE ACCOUNT WITH OTP -------------------------*/
		public function customer_update_account_with_otp(Request $request){
			$user 	= JWTAuth::user();
			$cus_id 	= $user->cus_id;
			$cus_name 	= $request->cus_name;
			$cus_email 	= $request->cus_email;
			$cus_phone1 = $request->cus_phone1;
			$cus_phone2	= $request->cus_phone2;
			$cus_address= $request->cus_address;
			$cus_lat 	= $request->cus_lat;
			$cus_long 	= $request->cus_long;
			$lang 	= $request->lang;
			
			$cus_name_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$valid_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$valid_phone2_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM2','Please enter valid phone number2!');
			$addr_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$cus_img_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_CUSTOMER_IMAGE','Please enter customer image!');
			$cus_img_dimen_err_msg	= MobileModel::get_lang_text($lang,'API_CUSTOMER_IMAGE_DIMEN_VAL','Customer Image Should be Width=300, Height=300!');
			$otp_err_msg			= MobileModel::get_lang_text($lang,'API_ENTER_RECEIVED_OTP','Please enter received OTP!');
			$current_otp_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_YOUR_OTP','Please enter OTP!');
			
			$validator = Validator::make($request->all(),[  'cus_name'	 	=> 'required',
			'cus_email'  	=> 'required|string|email',
			'cus_phone1' 	=> 'required|only_cnty_code',
			'cus_address'	=> 'required',
			'cus_lat'	 	=> 'required',
			'cus_long'		=> 'required',
			'current_otp' 	=> 'required',
			'otp' 			=> 'required'
			//'cus_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300'
			],
			[
			'cus_name.required'			=> $cus_name_req_err_msg,
			'cus_email.required'		=> $email_req_err_msg,
			'cus_email.email'			=> $valid_email_err_msg,
			'cus_phone1.required'		=> $valid_phone_err_msg,
			'cus_phone1.only_cnty_code'	=> $valid_phone_err_msg,
			'cus_address.required'		=> $addr_req_err_msg,
			'cus_lat.required'			=> $lati_req_err_msg,
			'cus_long.required'			=> $longi_req_err_msg,
			'current_otp.required' 		=> $current_otp_err_msg, 
			'otp.required'				=> $otp_err_msg
			//'cus_image.required'=>$cus_img_req_err_msg,
			//'cus_image.dimensions'=>$cus_img_dimen_err_msg	
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_id','!=',$cus_id)->where('cus_status','!=','2')->count();
			$check_phoneNumber_already_exsist = DB::table('gr_customer')->where('cus_phone1','=',$cus_phone1)->where('cus_id','!=',$cus_id)->where('cus_status','!=','2')->count();
			if($check_already_exsist > 0 )
			{
				$msg	= MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist >  0 )
			{
				$msg	= MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$insertArr = array(	'cus_fname' 	=> $cus_name,
			'cus_email' 	=> $cus_email,
			'cus_phone1' 	=> $cus_phone1,
			'cus_phone2' 	=> $cus_phone2,
			'cus_address' 	=> $cus_address,
			'cus_latitude' 	=> $cus_lat,
			'cus_longitude'	=> $cus_long,               
			);
		
			$cus_image = '';
			if($request->hasFile('cus_image')) {
				$cus_image = 'customer'.time().'.'.request()->cus_image->getClientOriginalExtension();
				$destinationPath = public_path('images/customer');
				$customer = Image::make(request()->cus_image->getRealPath())->resize(300, 300);
				$customer->save($destinationPath.'/'.$cus_image,80);
				$insertArr['cus_image']=$cus_image;
			}
			
			/*----------------------CHECK PHONE NUMBER IS NEW  ------------*/
			$current_otp 	= $request->current_otp;
			$generated_otp 	= $request->otp;
			if($generated_otp =="" || $current_otp =="")
			{
				$msge=MobileModel::get_lang_text($lang,'API_ENTER_RECEIVED_OTP','Please enter received OTP!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($generated_otp == $current_otp){
				$update = updatevalues('gr_customer',$insertArr,['cus_id' =>$cus_id]);
				$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
				$get_det = DB::table('gr_customer')->select('cus_image','cus_fname','cus_email','cus_phone1','cus_phone2','cus_address','cus_latitude','cus_longitude','cus_id')->where('cus_id','=',$cus_id)->first();
				/* get updated image */
				$user_avatar = url('public/images/noimage/default_user_image.jpg');
				if($get_det->cus_image != '')
				{
					$filename = public_path('images/customer/').$get_det->cus_image;
					if(file_exists($filename))
					{
						$user_avatar = url('public/images/customer/'.$get_det->cus_image );
					}
				}
				$encode = [ 'code' => 200,
				'message' => $msg,
				'data' => ["user_id"		=>intval($get_det->cus_id),
							"user_name"		=>ucfirst($get_det->cus_fname),
							"user_email"	=>$get_det->cus_email,
							"user_phone"	=>$get_det->cus_phone1,
							"user_phone2"	=>$get_det->cus_phone2,
							"user_address"	=>$get_det->cus_address,
							"user_latitude"	=>$get_det->cus_latitude,
							"user_longitude"=>$get_det->cus_longitude,
							"user_avatar"	=>$user_avatar]
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				if($user->cus_phone1 != $cus_phone1){
					$msge=MobileModel::get_lang_text($lang,'API_INVALID_OTP','Invalid OTP');
					}elseif($user->cus_email != $cus_email){
					$msge=MobileModel::get_lang_text($lang,'API_INVALID_VERIFICATION_CODE','Invalid verification code');
				}
				$insertArr = ['code' 	  => 400,
				'message' => $msge,
				'data'	  => ['otp' => $generated_otp]
				];
				return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
		}
		//GET SHIPPING ADDRESS
		public function customer_ship_address(Request $request)
		{
			$lang	= $request->lang;
			$user 	= JWTAuth::user();
			$cus_id = $user->cus_id;
			$shipAddressDet = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->first();
			$general_setting = DB::table('gr_general_setting')->select('self_pickup_status')->first();
			if(empty($shipAddressDet)===true){
				$msge	= MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code'=>400,'message'=>$msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else{
				if(empty($general_setting) === false)
				{
					$self_pickup_status = $general_setting->self_pickup_status;
				}
				else
				{
					$self_pickup_status = 0;
				}
				$msge = MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ["user_id"		=> intval($shipAddressDet->sh_cus_id),
				"sh_cus_fname"	=> ucfirst($shipAddressDet->sh_cus_fname),
				"sh_cus_lname"	=> ucfirst($shipAddressDet->sh_cus_lname),
				"sh_cus_email"	=> ($shipAddressDet->sh_cus_email == '') ? '' : $shipAddressDet->sh_cus_email,
				"sh_phone1"		=> ($shipAddressDet->sh_phone1 == '') ? '' : $shipAddressDet->sh_phone1,
				"sh_phone2"		=> ($shipAddressDet->sh_phone2 == '') ? '' : $shipAddressDet->sh_phone2,
				"sh_location"	=> $shipAddressDet->sh_location,
				"sh_location1"	=> ($shipAddressDet->sh_building_no == '') ? '' : $shipAddressDet->sh_building_no,
				"sh_latitude"	=> $shipAddressDet->sh_latitude,
				"sh_longitude"	=> $shipAddressDet->sh_longitude,
				"sh_zipcode"	=> ($shipAddressDet->sh_zipcode == '') ? '' : $shipAddressDet->sh_zipcode,
				"self_pickup_status"	=> $self_pickup_status,
				];
				$encode = ['code'=>200,'message'=>$msge,'data'	=> $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
		}
		//UPDATE SHIPPING ADDRESS
		public function customer_update_shipadd(Request $request){
			$user 	  = JWTAuth::user();
			$cus_id 	  = $user->cus_id;
			$sh_cus_fname = $request->sh_cus_fname;
			$sh_cus_lname = $request->sh_cus_lname;
			$sh_cus_email = $request->sh_cus_email;
			$sh_phone1	  = $request->sh_phone1;
			$sh_phone2	  = $request->sh_phone2;
			$sh_location  = $request->sh_location;
			$sh_location1  = $request->sh_location1;
			$sh_latitude  = $request->sh_latitude;
			$sh_longitude = $request->sh_longitude;
			$sh_zipcode   = $request->sh_zipcode;
			$lang = $request->lang;
			
			$cus_name_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$cus_lname_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LAST_NAME','Please enter lastname!');
			$email_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$valid_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$valid_phone2_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM2','Please enter valid phone number2!');
			$addr_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$zip_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_ZIP','Please enter zip code!');
			
			$validator = Validator::make($request->all(),[  'sh_cus_fname'	=> 'required',
			'sh_cus_lname'	=> 'required',
			'sh_cus_email'	=> 'required|string|email',
			'sh_phone1' 	=> 'required|only_cnty_code',
			//'cus_phone2'	=> 'sometimes|only_cnty_code',
			'sh_location'	=> 'required',
			'sh_latitude'	=> 'required',
			'sh_longitude'	=> 'required',
			'sh_zipcode' 	=> 'required'
			],
			[
			'sh_cus_fname.required'		=> $cus_name_req_err_msg,
			'sh_cus_fname.required'		=> $cus_lname_req_err_msg,
			'sh_cus_email.required'		=> $email_req_err_msg,
			'sh_cus_email.email'		=> $valid_email_err_msg,
			'sh_phone1.required'		=> $valid_phone_err_msg,
			'sh_phone1.only_cnty_code'	=> $valid_phone_err_msg,
			//'cus_phone2.only_cnty_code'=>$valid_phone2_err_msg,
			'sh_location.required'		=> $addr_req_err_msg,
			'sh_latitude.required'		=> $lati_req_err_msg,
			'sh_longitude.required'		=> $longi_req_err_msg,
			'sh_zipcode.required'		=> $zip_req_err_msg
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$insertArr = array(
			'sh_cus_fname' 	=> $sh_cus_fname,
			'sh_cus_lname' 	=> $sh_cus_lname,
			'sh_cus_email' 	=> $sh_cus_email,
			'sh_phone1' 	=> $sh_phone1,
			'sh_phone2' 	=> $sh_phone2,
			'sh_location' 	=> $sh_location,
			'sh_building_no' 	=> $sh_location1,
			'sh_latitude' 	=> $sh_latitude,
			'sh_longitude'	=> $sh_longitude,
			'sh_zipcode' 	=> $sh_zipcode
			); 
			$check_already_exsist = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->count();
			if($check_already_exsist > 0 )
			{
				$update = updatevalues('gr_shipping',$insertArr,['sh_cus_id' =>$cus_id]);
				$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
				$encode = ['code'=> 200,'message' => $msg,'data'=> $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$insertArr['sh_cus_id'] = $cus_id;
				$insert = insertvalues('gr_shipping',$insertArr);
				$msg = MobileModel::get_lang_text($lang,'API_SAVE_SUXES','Saved successfully!');;
				$encode = ['code'=> 200,'message' => $msg,'data'=> $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		//CUSTOMER MY WISHLIST
		public function customer_wishlist(Request $request){
			$lang = $request->lang; //
			$page_no = $request->page_no;
			$user	 = JWTAuth::user();
			$cus_id	 = $user->cus_id;
			$product_details  = MobileModel::get_wishlistdetails($cus_id,$lang,$this->admin_default_lang,$page_no);	
			if(count($product_details)>0) {
				foreach($product_details as $p){
					if($p->pro_type=='1'){
						$imageUrl = MobileModel::get_image_product($p->pro_image);
						}else{
						$imageUrl = MobileModel::get_image_item($p->pro_image);
					}
					$productlist[] = array("product_id"				=>$p->pro_id,
					"product_title"			=>ucfirst(strip_tags($p->pdtname)),
					"pro_has_discount"		=>($p->pro_has_discount == 'yes') ? 'Yes' : 'No',
					"product_discount_price"=>floatval($p->pro_discount_price),
					"product_original_price"=>floatval($p->pro_original_price),
					"product_image"			=>$imageUrl,
					"product_currency_code"	=>$p->pro_currency,
					"availablity"			=>$p->availablity,
					"restaurant_id"			=> $p->pro_store_id);
				}
				$msge = MobileModel::get_lang_text($lang,'API_PRODUCT_AVAIL','Product available!');
				$data = ["product_wish_list"=>$productlist];
				$encode = array('code'=>200,"message"=>$msge,"data"=>$data);
				return Response::make(json_encode($encode, JSON_PRETTY_PRINT))->header('Content-Type', "application/json");
			}
			else{
				$msg = MobileModel::get_lang_text($lang,'API_PRODUCT_NOT_AVAIL','Product not available!');
				$encode = ['code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		//CUSTOMER REVIEWS
		public function view_reivew(Request $request)
		{
			$lang = $request->lang;
			$id   = $request->id;
			$type = $request->type;
			$cus_details = JWTAuth::user();
			$cus_id 	 = $cus_details->cus_id;
			
		}
		
		//ADD OR DELETE WISHLIST
		public function add_to_wishlist(Request $request){
			$lang 		= $request->lang; 
			$product_id = $request->product_id; 
			$user	 	= JWTAuth::user();
			$cus_id	 	= $user->cus_id;
			$get_product_exists = DB::table('gr_product')->where('pro_id','=',$product_id)->where('pro_status','=','1')->get();//CHECK PRODUCT AVAILABLE OR NOT
			if(count($get_product_exists) > 0 ){
				$get_wish_list = MobileModel::get_product_wishlist($product_id,$cus_id); // CHECK PRODUCT ALREADY IN  WISHLIST, IF IT IS IN WISHLIST JUST REMOVE ELSE ADD
				if(!empty($get_wish_list)==false) {
					//insert
					foreach($get_product_exists as $getPdt) { }
					$insertArr 	= array('ws_pro_id'=>$product_id,'ws_type'=>$getPdt->pro_type,'ws_cus_id'=>$cus_id,'ws_date'=>date('Y-m-d'));
					$insert 	= insertvalues('gr_wishlist',$insertArr);
					$msg	= MobileModel::get_lang_text($lang,'API_WISHLIST_ADDED_SUCCESSFULLY','Wish list added successfully!');
					$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				else {
					//delete
					$delete =  DB::table('gr_wishlist')->where('ws_id','=',$get_wish_list->ws_id)->where('ws_cus_id','=',$cus_id)->delete();    
					$msg 	= MobileModel::get_lang_text($lang,'API_WISHLIST_DELETED_SUCCESSFULLY','Wish list deleted successfully!');
					$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else {
				$msg 	= MobileModel::get_lang_text($lang,'API_PRODUCT_NOT_AVAIL','Product not available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		//refer friend
		public function refer_friend(Request $request)
		{
			$lang = $request->lang;
			if($this->general_setting->gs_refer_friend == '1')
			{
				$msg 	= MobileModel::get_lang_text($lang,'API_REFER_FR_GET_OFF','Product not available!');
				$result = str_replace(':percent',$this->general_setting->gs_offer_percentage,$msg); 
				$encode = ['code'=> 200,'message' => $result,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$msg 	= MobileModel::get_lang_text($lang,'API_REFER_FR_NT_AVAIL','Unable to refer a friend');
				$encode = ['code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		//REFER FRIEND SEND MAIL
		public function refer_friend_send_mail(Request $request){
			$lang   = $request->lang; 
			$referral_email	= $request->referral_email; 
			$user	  = JWTAuth::user();
			$cus_name = $user->cus_fname;
			$cus_id	  = $user->cus_id;
			if($this->general_setting->gs_refer_friend=='1'){
				$refer_percentage = $this->general_setting->gs_offer_percentage;
			}
			else {
				$refer_percentage = '0';
			}
			/*VALIDATION STARTS HERE */
			$email_req_err_msg	= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$referral_mail_msg	= MobileModel::get_lang_text($lang,'REFEREL_MAIL','Referral mail ');
			$validator = Validator::make($request->all(),['referral_email'	=> 'required|string|email'],['referral_email.required' => $email_req_err_msg,'referral_email.email' => $valid_email_err_msg]);
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$check = DB::table('gr_customer')->where('cus_email','=',$referral_email)->where('cus_status','=','1')->count();
			$check_referral_exist = DB::table('gr_referal')->where(['referre_email' => $referral_email])->count();
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$code = substr( str_shuffle( $chars ), 0, 8);
			if($check_referral_exist == 0 && $check == 0){
				/*	MAIL FUNCTION */
				$send_mail_data = array('referral_mail_msg'	=> $referral_mail_msg,
				'referral_email'	=> $referral_email,
				'referred_name'		=> $cus_name,
				'lang'				=> $lang.'_mob_lang',
				'onlyLang'			=> $lang,
				'itunes_url'		=> $this->general_setting->gs_apple_appstore_url,
				'playstore_url'		=> $this->general_setting->gs_playstore_url,
				'refer_code'		=> $code
				);
				Mail::send('email.mobileReferEmail', $send_mail_data, function($message) use($send_mail_data){
					$message->to($send_mail_data['referral_email'])->subject($send_mail_data['referral_mail_msg'].' from '.$send_mail_data['referred_name']);
				});
				/* EOF MAIL FUNCTION referrer_name*/ 
				
				$arr = ['referral_id'		=> $cus_id, 
				'referre_email' 	=> $referral_email,
				're_offer_percent' 	=> $refer_percentage,
				're_purchased'		=> '0',
				're_code'			=> $code
				];
				$insert = insertvalues('gr_referal',$arr);
				$msg 	= MobileModel::get_lang_text($lang,'API_MAIL_SENT_SUCCESSFULLY','Mail sent successfully!');
				$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				
			}
			else{
				$msg 	= MobileModel::get_lang_text($lang,'API_REF_EMAIL_ALREADY_EXISTS','Refered Email is Already exists!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
		}
		//GET CUSTOMER PAYMENT SETTINGS
		public function customer_payment_settings(Request $request)
		{
			// paynamics is used instead of stripe, paymaya is used instead of paypal
			$user = JWTAuth::user();
			$cus_id = $user->cus_id;
			$lang = $request->lang;
			$msge = 'Fetched customer payments details succesfully!';
			$payment_err = '';
			$payment_status = 'AVAILABLE';
			if($user->cus_netbank_status == 'Unpublish' && $user->cus_paymaya_status == 'Unpublish')
			{
				$payment_err = MobileModel::get_lang_text($lang,'API_CANCEL_INFO','Please provide Payment Details to get cancelation refund!.You can Skip this step, but you will recive refund in your wallet.');
				$payment_status = 'NOT_AVAILABLE';
			}
			$data 	= ["paypal_status"	=> $user->cus_paymaya_status,
						"paypal_clientId"	=> $user->cus_paymaya_clientid,
						//"paypal_secretId"	=> $user->cus_paymaya_secretid,
						"netBanking_status"	=> $user->cus_netbank_status,
						"netBanking_bankName"=>$user->cus_bank_name,
						"netBanking_branch"	=> $user->cus_branch,
						"netBanking_accNo"	=> $user->cus_bank_accno,
						"netBanking_ifsc"	=> $user->cus_ifsc,
						"payment_status"	=> $payment_status,
						"payment_status_err"=> $payment_err];
			$encode = ['code'				=> 200,
			'message'			=> $msge,
			'data'				=> $data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		//UPDATE PAYMENT SETTINGS
		public function customer_update_pmtSettings(Request $request){
			$user = JWTAuth::user();
			$cus_id 	  		= $user->cus_id;
			$paynamics_status 	= $request->stripe_status;
			$paynamics_clientId	= $request->stripe_clientId;
			$paynamics_secretId = $request->stripe_secretId;
			$paymaya_status	  	= $request->paypal_status;
			$paymaya_clientId	= $request->paypal_clientId;
			//$paymaya_secretId  	= $request->paypal_secretId; // paypal email id is enough for payment
			$netBanking_status  = $request->netBanking_status;
			$netBanking_bankName= $request->netBanking_bankName;
			$netBanking_branch  = $request->netBanking_branch;
			$netBanking_accNo   = $request->netBanking_accNo;
			$netBanking_ifsc   	= $request->netBanking_ifsc;
			$lang = $request->lang;
			/*VALIDATION START HERE */ 
			
			/*if($paymaya_status=='Unpublish' && $netBanking_status=='Unpublish')
			{
				$msg 	= MobileModel::get_lang_text($lang,'API_FILL_PAYPAL_NET_DETAILS','Please Fill Paypal or Net Banking Details');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}*/
			if($paynamics_status=='Publish')
			{
				$paynamics_client_req_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_PAYNA_CLIENT','Please Enter paynamics client ID!');
				$paynamics_secret_req_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_PAYNA_SECRET','Please Enter paynamics secret ID!');
				$validator = Validator::make($request->all(),
				['stripe_clientId'=>'required','stripe_secretId'=>'required'],
				['stripe_clientId.required' => $paynamics_client_req_err_msg, 'stripe_secretId.required' => $paynamics_secret_req_err_msg]
				); 
				if($validator->fails()){
					$message 	= $validator->messages()->first();
					$encode 	= [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else{
				$paynamics_clientId	= '';
				$paynamics_secretId = '';
			}
			
			if($paymaya_status=='Publish')
			{
				$paymaya_client_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_PAYMA_CLIENT','Please Enter paymaya client ID!');
				//$paymaya_secret_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_PAYMA_SECRET','Please Enter paymaya secret ID!');
				$validator = Validator::make($request->all(),
				['paypal_clientId'=>'required'],
				['paypal_clientId.required' => $paymaya_client_req_err_msg]
				); 
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else{
				$paymaya_clientId	= '';
				//$paymaya_secretId  	= '';
			}
			if($netBanking_status=='Publish')
			{
				$netbank_bankname_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_BANK','Please enter bank name!');
				$netbank_branchna_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_BRANCH','Please enter branch name!');
				$netbank_accountn_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ACCNO','Please enter account number!');
				$netbank_ifsscode_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_IFSC','Please enter IFSC code!');
				$validator = Validator::make($request->all(),
				['netBanking_bankName'=>'required','netBanking_branch'=>'required','netBanking_accNo'=>'required','netBanking_ifsc'=>'required'],
				['netBanking_bankName.required' => $netbank_bankname_req_err_msg, 'netBanking_branch.required' => $netbank_branchna_req_err_msg,'netBanking_accNo.required'=>$netbank_accountn_req_err_msg,'netBanking_ifsc.required'=>$netbank_ifsscode_req_err_msg]
				); 
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else{
				$netBanking_bankName= '';
				$netBanking_branch  = '';
				$netBanking_accNo   = '';
				$netBanking_ifsc   	= '';
			}
			// paynamics is used instead of stripe, paymaya is used instead of paypal
			$profile_det = array(
			'cus_paynamics_status'		=> $paynamics_status,
			'cus_paynamics_clientid'	=> $paynamics_clientId,
			'cus_paynamics_secretid'	=> $paynamics_secretId,
			'cus_paynamics_mode'		=> 'Live',
			'cus_paymaya_status'		=> $paymaya_status,
			'cus_paymaya_clientid'		=> $paymaya_clientId,
			//'cus_paymaya_secretid'		=> $paymaya_secretId,
			'cus_paymaya_mode'			=> 'Live',
			'cus_netbank_status'		=> $netBanking_status,
			'cus_bank_name'				=> $netBanking_bankName,
			'cus_branch'				=> $netBanking_branch,
			'cus_bank_accno'			=> $netBanking_accNo,
			'cus_ifsc'					=> $netBanking_ifsc,
			'cus_updated_date' 			=> date('Y-m-d H:i:s')
			);			
			DB::table('gr_customer')->where('cus_id', '=', $cus_id)->update($profile_det);
			$msg = MobileModel::get_lang_text($lang,'API_SAVE_SUXES','Saved successfully!');
			$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		public function product_write_review(Request $request){
			$user = JWTAuth::user();
			$cus_id 	  	 = $user->cus_id;
			$lang			 = $request->lang;
			$product_id 	 = $request->product_id;
			$review_comments = $request->review_comments;
			$review_rating   = $request->review_rating;
			
			$productID_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_PDT_ID','Please enter product ID!');
			$productID_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_PDT_ID','Please enter valid product ID!');
			$comments_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_COMMENTS','Please enter comments!');
			$rating_req_err_msg			= MobileModel::get_lang_text($lang,'API_ENTER_RATING','Please enter ratings!');
			$rating_valid_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_RATING_RULES','Rating should be number,between 1 to 5!');
			$validator = Validator::make($request->all(),
			['product_id'=>'required|integer','review_comments'=>'required','review_rating'=>'required|integer'],
			['product_id.required' 		=> $productID_req_err_msg, 
			'product_id.integer' 		=> $productID_valid_err_msg, 
			'review_comments.required'	=> $comments_req_err_msg, 
			'review_rating.required'	=> $rating_req_err_msg, 
			'review_rating.integer'	=> $rating_valid_err_msg, 
			'review_rating.max'		=> $rating_valid_err_msg
			]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($review_rating > 5)
			{
				$encode = ['code'=>400,"message"=>$rating_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			//CHECK THIS PRODUCT IS PURCHASED BY THIS User
			$check_purchased = DB::table('gr_order')->where('ord_cus_id','=',$cus_id)->where('ord_pro_id','=',$product_id)->count();
			if($check_purchased <= 0){
				$check_purchased_err_msg=MobileModel::get_lang_text($lang,'API_CAN_REVIEW_ONLY_PURCHASED','You can give review, only you are purchased!');
				$encode = ['code'=>400,"message"=>$check_purchased_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			$get_product_exists = DB::table('gr_product')->where('pro_id','=',$product_id)->where('pro_status','=','1')->get();//CHECK PRODUCT AVAILABLE OR NOT
			if(count($get_product_exists) > 0 ){
				$get_review_exists = DB::table('gr_review')->where('proitem_id','=',$product_id)->where('customer_id','=',$cus_id)->first();
				if(!empty($get_review_exists)==false)
				{
					foreach($get_product_exists as $getPdt) { } 
					$merchant_id = DB::table('gr_store')->select('st_mer_id')->where('id','=',$getPdt->pro_store_id)->first()->st_mer_id;
					$review_type = ($getPdt->pro_type==1)?'product':'item';
					$insertArr = array(	'customer_id' 		=> $cus_id,
					'proitem_id' 		=> $product_id,
					'res_store_id' 		=> $getPdt->pro_store_id,
					'merchant_id' 		=> $merchant_id,
					'review_type'		=> $review_type,
					'review_comments' 	=> $review_comments,
					'review_rating' 	=> $review_rating,
					'review_status' 	=> '0',
					'created_date' 		=> date('Y-m-d H:i')
					);
					$insert = insertvalues('gr_review',$insertArr);
					$msg 	= MobileModel::get_lang_text($lang,'API_SAVE_SUXES','Saved successfully!');
					$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					
				}
				else { 
					$msg 	= MobileModel::get_lang_text($lang,'API_REVIEWED_ALREADY','You are already reviewed!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else {
				$msg 	= MobileModel::get_lang_text($lang,'API_PRODUCT_NOT_AVAIL','Product not available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		public function store_write_review(Request $request){
			$user = JWTAuth::user();
			$cus_id 	  	 = $user->cus_id;
			$lang			 = $request->lang;
			$store_id 		 = $request->store_id;
			$review_comments = $request->review_comments;
			$review_rating   = $request->review_rating;
			
			$storeID_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store ID!');
			$storeID_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
			$comments_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_COMMENTS','Please enter comments!');
			$rating_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_RATING','Please enter ratings!');
			$rating_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_RATING_RULES','Rating should be number,between 1 to 5!');
			$validator = Validator::make($request->all(),
			['store_id'=>'required|integer','review_comments'=>'required','review_rating'=>'required|integer'],
			['store_id.required' 		=> $storeID_req_err_msg, 
			'store_id.integer' 		=> $storeID_valid_err_msg, 
			'review_comments.required'	=> $comments_req_err_msg, 
			'review_rating.required'	=> $rating_req_err_msg, 
			'review_rating.integer'	=> $rating_valid_err_msg, 
			'review_rating.max'		=> $rating_valid_err_msg
			]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($review_rating > 5)
			{
				$encode = ['code'=>400,"message"=>$rating_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$get_store_exists = DB::table('gr_store')->select('st_type','st_mer_id')->where('id','=',$store_id)->where('st_status','=','1')->first();//CHECK PRODUCT AVAILABLE OR NOT
			
			if(!empty($get_store_exists)==true){
				$type = ($get_store_exists->st_type == '1') ? 'restaurant' : 'store';
				$get_review_exists = DB::table('gr_review')->where('res_store_id','=',$store_id)
				->where('customer_id','=',$cus_id)
				->where('review_type','=',$type)
				->first();
				if(!empty($get_review_exists)==false)
				{
					$review_type = ($get_store_exists->st_type==1)?'restaurant':'store';
					$insertArr 	 = array('customer_id' => $cus_id,
					'res_store_id' => $store_id,
					'merchant_id' => $get_store_exists->st_mer_id,
					'review_type' => $review_type,
					'review_comments' => $review_comments,
					'review_rating' => $review_rating,
					'review_status' => '0',
					'created_date' => date('Y-m-d H:i')
					);
					$insert = insertvalues('gr_review',$insertArr);
					$msg 	= MobileModel::get_lang_text($lang,'API_SAVE_SUXES','Saved successfully!');
					$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					
				}
				else { 
					$msg = MobileModel::get_lang_text($lang,'API_REVIEWED_ALREADY','You are already reviewed!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			else {
				$msg = MobileModel::get_lang_text($lang,'API_ENTER_NOTAVAIL_STOREREST','Store/Restaurant not available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/*ORDER REVIEW */
		public function order_write_review(Request $request){
			$user 			 = JWTAuth::user();
			$cus_id 	  	 = $user->cus_id;
			$lang			 = $request->lang;
			$order_id 		 = $request->order_id;
			$store_id 		 = $request->store_id;
			$delivery_id 	 = $request->deliver_id;
			$review_comments = $request->review_comments;
			$review_rating   = $request->review_rating;
			
			$orderId_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_exist_err_msg = MobileModel::get_lang_text($lang,'API_OR_ID_NT_EXIST','Order id does not exist');
			$stId_valid_err_msg = MobileModel::get_lang_text($lang,'API_ST_ID_REQ','Store id is required');
			$stId_exists_err_msg = MobileModel::get_lang_text($lang,'API_ST_ID_NT_EXIST','Store id not exist');
			$deId_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_DELBOY_ID','Please enter delivery person ID!');
			$deId_exists_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_VALID_DELBOY_ID','Please enter valid delivery person ID!');
			$comments_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_COMMENTS','Please enter comments!');
			$rating_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_RATING','Please enter ratings!');
			$rating_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_RATING_RULES','Rating should be number,between 1 to 5!');
			$validator = Validator::make($request->all(),['order_id'		=>['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function($query) use($cus_id){ $query->where('ord_cus_id','=',$cus_id)->where('ord_status','=','8');})
			],
			'store_id'			=> ['required',
			Rule::exists('gr_order','ord_rest_id')->where(function($query) use($cus_id,$order_id){ $query->where('ord_cus_id','=',$cus_id)->where('ord_transaction_id','=',$order_id);})
			],
			'deliver_id'			=> ['required',
			Rule::exists('gr_order','ord_delivery_memid')->where(function($query) use($cus_id,$order_id){ $query->where('ord_cus_id','=',$cus_id)->where('ord_transaction_id','=',$order_id);})
			],
			'review_comments'	=>'required',
			'review_rating'		=>'required|integer'
			],
			['order_id.required'=>$orderId_valid_err_msg,
			'order_id.exists'	=> $orderId_exist_err_msg, 
			'store_id.required'	=> $stId_valid_err_msg, 
			'store_id.exists'	=> $stId_exists_err_msg,
			'deliver_id.required'=> $deId_valid_err_msg, 
			'deliver_id.exists'	=> $deId_exists_err_msg, 
			'review_comments.required'	=> $comments_req_err_msg, 
			'review_rating.required'	=> $rating_req_err_msg, 
			'review_rating.integer'	=> $rating_valid_err_msg, 
			'review_rating.max'		=> $rating_valid_err_msg
			]); 
			if($validator->fails()){
				$message	= $validator->messages()->first();
				$encode 	= [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($review_rating > 5)
			{
				$encode = ['code'=>400,"message"=>$rating_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$agent_id = '';
			
			$get_details = DB::table('gr_order')->select('dm_customer_rating','ord_agent_id','gr_order.ord_delmgr_id')
			->leftjoin('gr_delivery_manager','gr_delivery_manager.dm_id','=','gr_order.ord_delmgr_id')
			->where(['ord_transaction_id' => $order_id,
			'ord_rest_id' => $store_id,
			'ord_status'  => '8',
			'ord_delivery_memid' => $delivery_id])
			->get();
			//print_r($get_details); exit;
			if(count($get_details) > 0)
			{
				/* check customer rating status */
				$agent_id 	= $get_details[0]->ord_agent_id;
				$get_review_exists = DB::table('gr_review')->where('res_store_id','=',$store_id)
				->where('customer_id','=',$cus_id)
				->where('order_id','=',$order_id)
				->where('delivery_id','=',$delivery_id)
				->where('review_type','=','order')
				->where('review_status','!=','2')
				->count();
				
				if($get_review_exists > 0) /* already reviewed */
				{
					$msg = MobileModel::get_lang_text($lang,'API_REVIEWED_ALREADY','You are already reviewed!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				elseif($get_details[0]->dm_customer_rating == '0')	/* if rating status is disable,can't write review */
				{
					$msg 	= MobileModel::get_lang_text($lang,'API_CANT_WRITE_REVIEW','Can\'t write review!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				else
				{
					
					$insertArr = array(	'customer_id' 	=> $cus_id,
					'res_store_id'  => $store_id,
					'order_id' 		=> $order_id,
					'agent_id'	 	=> $agent_id,
					'delivery_id' 	=> $delivery_id,
					'review_type' 	=> 'order',
					'review_comments'=>$review_comments,
					'review_rating' => $review_rating,
					'review_status' => '0',
					'created_date' 	=> date('Y-m-d H:i')
					);
					//print_r($insertArr); exit;
					$insert = insertvalues('gr_review',$insertArr);
					$msg = MobileModel::get_lang_text($lang,'API_SAVE_SUXES','Saved successfully!');
					$encode = [ 'code'=> 200,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					
				}
			}
			else
			{
				$msg 	= MobileModel::get_lang_text($lang,'API_CANT_WRITE_REVIEW','Can\'t write review!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
		}
		/* EOF ORDER REVIEW */
		
		public function my_orders(Request $request){
			$user = JWTAuth::user();
			$cus_id 	  	 = $user->cus_id;
			$lang			 = $request->lang;
			$page_no 		 = $request->page_no;
			$order_num 		 = $request->order_num;
			$pagenum_valid_err_msg=MobileModel::get_lang_text($lang,'API_PAGE_NUM_RULES','Page number should be a number!');
			$validator = Validator::make($request->all(),['page_no'=>'sometimes|integer'],['page_no.required' => $pagenum_valid_err_msg]); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$product_order  = MobileModel::getordersdetails($cus_id,$order_num,$page_no,$lang,$this->admin_default_lang);
			//print_r($product_order); exit;
			if(count($product_order) > 0 )
			{
				$orderArray = array();
				foreach($product_order as $pord)
				{
					$store_list = explode("/**/",$pord->store_name_list);
					$list_arr = array();
					if(count($store_list) > 0)
					{
						foreach($store_list as $list)
						{	
							$explode = explode('~',$list);
							$get_active_item = MobileModel::get_activeItem($pord->ord_transaction_id,$explode[1],$cus_id);
							$list_arr[] = ['store_name' => $explode[0],
											'store_id'  => $explode[1],
											'store_location' => $explode[2],
											'canTrack'	=> ($get_active_item > 0) ? TRUE : FALSE
											];
						}
					}
					$orderArray[] = array('orderId'			=>$pord->ord_transaction_id,
											'orderAmount'	=>number_format(($pord->revenue+$pord->ord_delivery_fee - $pord->ord_wallet),2),
											'orderDate'		=>date('m/d/Y',strtotime($pord->ord_date)),
											'ordCurrency'	=>$pord->ord_currency,
											'store_details' => $list_arr,
											'orderTrack'	=> ($pord->active_count > 0) ? TRUE : FALSE );
				}
				$encode = ['code' 		=> 200,
				'message' 	=> "Order Available",
				'data'		=>['orderArray' => $orderArray]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$msg 	= MobileModel::get_lang_text($lang,'API_NO_ORDERS','No Orders available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		public function my_order_details(Request $request){
			$user 		= JWTAuth::user();
			$cus_id		= $user->cus_id;
			$lang		= $request->lang;
			//$page_no	= $request->page_no;
			$order_id	= $request->order_id;
			/****************************** VALIDATION STARTS HERE ****************************************/
			$pagenum_valid_err_msg = MobileModel::get_lang_text($lang,'API_PAGE_NUM_RULES','Page number should be a number!');
			$orderId_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_exist_err_msg = MobileModel::get_lang_text($lang,'API_OR_ID_NT_EXIST','Order id does not exist');
			$validator = Validator::make($request->all(),[
			'order_id'=>['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function($query) use($cus_id){ $query->where('ord_cus_id','=',$cus_id);})
			],
			],
			[
			'order_id.required'	=>$orderId_valid_err_msg,
			'order_id.exists'	=>$orderId_exist_err_msg,
			]); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$payment_err = '';
			$payment_status = 'AVAILABLE';
			if($user->cus_netbank_status == 'Unpublish' && $user->cus_paymaya_status == 'Unpublish')
			{
				$payment_err = MobileModel::get_lang_text($lang,'API_CANCEL_INFO','Please provide Payment Details to get cancelation refund!.You can Skip this step, but you will recive refund in your wallet.');
				$payment_status = 'NOT_AVAILABLE';
			}
			$ord_transaction_id = $order_id;
			$choices=array();
			//DB::connection()->enableQueryLog(); 
			$Invoice_Order = DB::table('gr_order')
			->select('gr_order.ord_id',
			'gr_order.ord_date',
			'gr_order.ord_rest_id',
			'gr_order.ord_pro_id',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_transaction_id',
			'gr_order.ord_merchant_id',
			'gr_store.st_store_name',
			'gr_product.pro_item_code',
			'gr_product.pro_item_name',
			'gr_product.pro_type',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_status',
			'gr_order.ord_pay_type',
			'gr_order.ord_had_choices',
			'gr_order.ord_reject_reason',
			'gr_order.ord_currency',
			'gr_order.ord_cancel_status',
			'gr_order.ord_refund_status',
			'gr_order.ord_mer_cancel_status',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_failed_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_delivered_on',
			'gr_order.ord_delivery_memid',
			'gr_order.ord_self_pickup',
			'gr_order.ord_failed_on',
			DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),
			DB::Raw("IF((SELECT count(comment_id) FROM `gr_review` WHERE `res_store_id`=gr_order.ord_rest_id AND `review_type`='restaurant' AND `customer_id`=gr_order.ord_cus_id)>0,'1','0') as exist_review"),
			DB::Raw("IF((SELECT count(comment_id) FROM `gr_review` WHERE `proitem_id`=gr_order.ord_pro_id AND `review_type`='item' AND `customer_id`=gr_order.ord_cus_id)>0,'1','0') as item_exist_review"),
			DB::Raw("IF((SELECT count(comment_id) FROM `gr_review` WHERE `delivery_id`=gr_order.ord_delivery_memid AND `review_type`='order' AND `customer_id`=gr_order.ord_cus_id AND `res_store_id`=gr_order.ord_rest_id AND `order_id`=gr_order.ord_transaction_id )>0,'1','0') as order_exist_review"),
			'gr_order.ord_grant_total',
			'gr_delivery_manager.dm_customer_rating'
			)
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->leftjoin('gr_delivery_manager','gr_delivery_manager.dm_id','=','gr_order.ord_delmgr_id')
			->where('gr_order.ord_transaction_id','=',$ord_transaction_id)
			->get();
			//$query = DB::getQueryLog();
			//print_r($Invoice_Order);
			//exit; 
			/*START */
			if(count($Invoice_Order)>0)
			{
				$pending_array = array();
				$fulfilled_array = array();
				$cancelled_array = array();
				$exist_store     = 0;
				foreach($Invoice_Order as $Order_sub)
				{	
					$ordersArray = array();
					if($Order_sub->ord_had_choices=="Yes")
					{
						$splitted_choice=json_decode($Order_sub->ord_choices,true);
						if(!empty($splitted_choice))
						{
							foreach($splitted_choice as $choice)
							{
								if(!isset($choices[$choice['choice_id']]))
								{
									$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
									$ordersArray[]=array('choicename'=>$choices_name
									);
								}
							}
						}
					}
					/* cancel array */
					if(($Order_sub->ord_cancel_status == 1 && ($Order_sub->ord_status == '1' || $Order_sub->ord_status =='3')) || $Order_sub->ord_status == '9')
					{	
						$text = MobileModel::get_lang_text($lang,'API_ORDER_CANCELLED','Order Cancelled!');
						$cancel_date = $Order_sub->ord_cancel_date;
						if($Order_sub->ord_status=='3')
						{
							$text = MobileModel::get_lang_text($lang,'API_ORDER_REJECTED','Order Rejected!');
						}
						elseif($Order_sub->ord_status== '9')
						{
							$text = MobileModel::get_lang_text($lang,'API_FAIL','Delivery Failed!');
							$cancel_date = $Order_sub->ord_failed_on;
						}
						
						$cancelled_array[] = ['item_name' => ucfirst($Order_sub->pro_item_name),
						'item_id' 		=> $Order_sub->ord_pro_id,
						'item_amount'	=> $Order_sub->ord_grant_total,
						'item_currency' => $Order_sub->ord_currency,
						'item_image'	=> MobileModel::get_image_item($Order_sub->pro_image),
						'restaurant_id'	=> $Order_sub->ord_rest_id,
						'restaurant_name'	=> ucfirst($Order_sub->st_store_name),
						'already_res_reviewed' => ($Order_sub->exist_review == '1') ? 'Yes' : 'No',
						'order_status'	=> $text,								
						'already_item_reviewed' => ($Order_sub->item_exist_review == '1') ? 'Yes' : 'No',
						'has_choice'	=> $Order_sub->ord_had_choices,
						'choice_list'	=> $ordersArray,
						'order_id'		=> $Order_sub->ord_id,
						'order_date' 	=> $Order_sub->ord_date,
						'pre_order_date' 	=> ($Order_sub->ord_pre_order_date == '') ? '' : $Order_sub->ord_pre_order_date,
						'cancelled_date' 	=> ($cancel_date == '') ? '' : $cancel_date,
						'cancelled_reason'	=> ($Order_sub->ord_cancel_reason == '') ? '' : $Order_sub->ord_cancel_reason,
						'failed_reason'	=> ($Order_sub->ord_failed_reason == '') ? '' : $Order_sub->ord_failed_reason,
						'order_type'	=> $Order_sub->ord_pay_type,
						];
					}
					/* delivered details */
					
					elseif($Order_sub->ord_status == '8')
					{
						$text = MobileModel::get_lang_text($lang,'API_OR_ST8','Delivered');
						$fulfilled_array[] = ['item_name' => ucfirst($Order_sub->pro_item_name),
						'item_id' 		=> $Order_sub->ord_pro_id,
						'item_amount'	=> $Order_sub->ord_grant_total,
						'item_currency' => $Order_sub->ord_currency,
						'item_image'	=> MobileModel::get_image_item($Order_sub->pro_image),
						'restaurant_id'	=> $Order_sub->ord_rest_id,
						'restaurant_name'	=> ucfirst($Order_sub->st_store_name),
						'already_res_reviewed' => ($Order_sub->exist_review == '1') ? 'Yes' : 'No',
						'delivery_id'	=> $Order_sub->ord_delivery_memid,
						'already_order_reviewed' => ($Order_sub->order_exist_review == '1') ? 'Yes' : 'No',
						'can_order_review' => ($Order_sub->dm_customer_rating == '1') ? 'Yes' : 'No',
						'order_status'	=> $text,								
						'already_item_reviewed' => ($Order_sub->item_exist_review == '1') ? 'Yes' : 'No',
						'has_choice'	=> $Order_sub->ord_had_choices,
						'choice_list'	=> $ordersArray,
						'order_id'		=> $Order_sub->ord_id,
						'order_date' 	=> $Order_sub->ord_date,
						'delivered_date'	=> ($Order_sub->ord_delivered_on == '') ? '' :$Order_sub->ord_delivered_on,
						'pre_order_date' 	=> ($Order_sub->ord_pre_order_date == '') ? '' :$Order_sub->ord_pre_order_date,
						'self_pickup' 	=> ($Order_sub->ord_self_pickup == 0) ? 'No' : 'Yes',
						];
					}
					/* processing details */
					elseif($Order_sub->ord_status < '8')
					{	
						$cancellation_policy 	= get_cancellation_policy($Order_sub->ord_merchant_id);
						$policy = '';
						if(empty($cancellation_policy)===false) 
						{ 
							$policy = $cancellation_policy->mer_cancel_policy; 
						}
						$k 	  = 'API_OR_ST'.$Order_sub->ord_status;
						$text = MobileModel::get_lang_text($lang,$k,'Processing');
						$pending_array[] = ['item_name' => ucfirst($Order_sub->pro_item_name),
						'item_id' 		=> $Order_sub->ord_pro_id,
						'item_amount'	=> $Order_sub->ord_grant_total,
						'item_currency' => $Order_sub->ord_currency,
						'item_image'	=> MobileModel::get_image_item($Order_sub->pro_image),
						'restaurant_id'	=> $Order_sub->ord_rest_id,
						'restaurant_name'	=> ucfirst($Order_sub->st_store_name),
						'already_res_reviewed' => ($Order_sub->exist_review == '1') ? 'Yes' : 'No',
						'order_status'	=> $text,								
						'already_item_reviewed' => ($Order_sub->item_exist_review == '1') ? 'Yes' : 'No',
						'has_choice'	=> $Order_sub->ord_had_choices,
						'choice_list'	=> $ordersArray,
						'order_id'		=> $Order_sub->ord_id,
						'order_date' 	=> $Order_sub->ord_date,
						'pre_order_date'=> ($Order_sub->ord_pre_order_date == '') ? '' : $Order_sub->ord_pre_order_date,
						'cancel_status'	=> ($Order_sub->ord_status > '1' || $Order_sub->ord_mer_cancel_status != 'Yes') ? 'Can\'t cancel' : 'Can cancel',
						'cancel_policy'	=> $policy,
						'refund_text'	=> $Order_sub->ord_refund_status,
						'cancellation_status' => $Order_sub->ord_mer_cancel_status,
						'payment_status'	  => $payment_status,
						'payment_status_err'  => $payment_err	
						];
						
					}
					
					
				}
				$data = ['order_transaction_id'	=> $order_id,
				'pending_details'	=> $pending_array,
				'fulfilled_details'	=> $fulfilled_array,
				'cancelled_details'	=> $cancelled_array,
				];
				$encode = ['code' 		=> 200,
				'message' 	=> MobileModel::get_lang_text($lang,'API_AVAIL_ORDERS','Orders available!'),
				'data' 		=> $data							
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msg 	= MobileModel::get_lang_text($lang,'API_NO_ORDERS','No Orders available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			
		}
		public function customer_invoice(Request $request){
			$user = JWTAuth::user();
			$cus_id 	  	 = $user->cus_id;
			$lang			 = $request->lang;
			$order_id 		 = $request->order_id;
			/****************************** VALIDATION STARTS HERE ****************************************/
			$orderId_valid_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$validator = Validator::make($request->all(),['order_id'=>'required'],['order_id:required'=>$orderId_valid_err_msg]); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$currency = '';
			$check_orderId_exist = DB::table('gr_order')->where('ord_transaction_id','=',$order_id)->where('ord_cus_id','=',$cus_id)->count();
			if($check_orderId_exist > 0 ){
				$ord_transaction_id = $order_id;
				$order_date = '';
				$choices=array();
				$selfPickup = 0;
				//DB::connection()->enableQueryLog(); 
				$Invoice_Order = MobileModel::get_invoice($order_id);
				//$query = DB::getQueryLog();
				/* get customer details */
				$customerDetailArray = array();
				$cus_details = DB::table('gr_order')->select('ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_pay_type','ord_self_pickup','order_ship_mail','ord_date')
				->where('ord_transaction_id','=',$order_id)->first();
				if(empty($cus_details) === false)
				{	
					$paytype = $cus_details->ord_pay_type;
					$order_date = $cus_details->ord_date;
					$selfPickup = $cus_details->ord_self_pickup;
					if($cus_details->ord_shipping_cus_name!='' && $cus_details->ord_shipping_address!=''  && $cus_details->ord_shipping_mobile!='')
					{
						$OrderCustomerName 		= $cus_details->ord_shipping_cus_name;
						$OrderCustomerAddress 	= $cus_details->ord_shipping_address;
						$OrderCustomerAddress1 	= $cus_details->ord_shipping_address1;
						$OrderCustomerMobile 	= $cus_details->ord_shipping_mobile;
						$OrderCustomerEmail 	= $cus_details->order_ship_mail;
					}
					else
					{
						$OrderCustomerName 		= $user->cus_fname.' '.$user->cus_lname;
						$OrderCustomerAddress 	= $user->cus_address;
						$OrderCustomerAddress1 	= '';
						$OrderCustomerMobile 	= $user->cus_phone1;
						$OrderCustomerEmail 	= $user->cus_email;
					}
					$customerDetailArray = array('customeName'		=>$OrderCustomerName,
					'customerAddress1'	=>$OrderCustomerAddress,
					'customerAddress2'	=>$OrderCustomerAddress1,
					'customerMobile'	=>$OrderCustomerMobile,
					'customerEmail'	=>$OrderCustomerEmail
					);
				}
				
				$store_array = array();
				//print_r($Invoice_Order); exit;
				$ordersArray = array();
				//$location_arr = array();
				//exit;
				/*START */
				$order_detailArray = array();
				$sub_total=$grand_total=$tax_total=$shipping_total=0;
				if(count($Invoice_Order)>0)
				{	
					foreach($Invoice_Order as $key => $value)
					{	
						
						$sub_total = 0;
						$ex = explode('~`',$key);
						foreach($value as $Order_sub)	
						{
							
							$calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
							$sub_total +=$calc_sub_total;
							$shipping_total =$Order_sub->ord_delivery_fee;
							$cancelled_reason = '';
							$ordersArray['store_name'] = $Order_sub->st_store_name;
							$ordersArray['item_name'] = $Order_sub->pro_item_name;
							$ordersArray['item_content'] = $Order_sub->pro_per_product;
							if($Order_sub->ord_spl_req != ''){
								$ordersArray['specialRequest'] = $Order_sub->ord_spl_req;
							}
							else {
								$ordersArray['specialRequest'] = '';
							}
							$ordersArray['ord_quantity'] 	= $Order_sub->ord_quantity;
							$ordersArray['ord_unit_price'] 	= number_format($Order_sub->ord_unit_price,2);
							$ordersArray['ord_tax_amt'] 	= number_format($Order_sub->ord_tax_amt,2);
							$ordersArray['sub_total'] 		= number_format($calc_sub_total,2);
							$ordersArray['ord_currency'] 	= $Order_sub->ord_currency;
							$currency 						= $Order_sub->ord_currency;
							if($Order_sub->ord_pre_order_date != '')
							{
								$ordersArray['pre_order_date'] = date('m/d/Y H:i:s',strtotime($Order_sub->ord_pre_order_date));
							}
							else
							{
								$ordersArray['pre_order_date'] = '-';
							}
							if($Order_sub->pro_type=='1'){
								$ordersArray['pdt_image']= MobileModel::get_image_product($Order_sub->pro_image);//product
							}
							else { 
								$ordersArray['pdt_image']= MobileModel::get_image_item($Order_sub->pro_image);
							}
							
							
							if($Order_sub->ord_had_choices=="Yes"){
								$splitted_choice=json_decode($Order_sub->ord_choices,true);
								if(!empty($splitted_choice))
								{
									foreach($splitted_choice as $choice)
									{
										if(!isset($choices[$choice['choice_id']]))
										{
											$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
											$ordersArray['choice'][]=array('choicename'=>$choices_name,'choice_amount'=>$choice['choice_price']);
											$sub_total +=$choice['choice_price']; 
											$grand_total +=$choice['choice_price']; 
										}
									}
								}
							}
							else
							{
								$ordersArray['choice'] = array();
							}
							if($ex[0] == $Order_sub->st_store_name)
							{
								array_push($order_detailArray,$ordersArray);
							}
							
							
							if($selfPickup == 0){
								$delivery_fee = $Order_sub->ord_delivery_fee;
							}
							else{
								$delivery_fee = '0.00';
							}
							$walletAmount = $Order_sub->ord_wallet;
						}
						
						
						$store_array[] = ['store_name' => $ex[0],
										'store_location' => $ex[1],
										'item_lists' => $order_detailArray];
						$order_detailArray = array();
						 
						if($selfPickup == 0) 
						{
							$grand_total_text = $sub_total+$shipping_total - $walletAmount;
							} else {
							$grand_total_text = $sub_total - $walletAmount;
						}
						
					}
					
					
				}
				else{
					$msg 	= MobileModel::get_lang_text($lang,'API_NO_ORDERS','No Orders available!');
					$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				//exit;
				$data = ['order_id'				=> $order_id,
				'order_date'			=> $order_date,
				'self_pickup'			=> $selfPickup,
				'customerDetailArray'	=> $customerDetailArray,
				//'store_location'		=> $location_arr,
				'order_detailArray' 	=> $store_array,
				'paytype'				=> $paytype,
				'currency'				=> $currency,
				'grand_sub_total'		=>number_format($sub_total,2),
				'delivery_fee' 			=>number_format($delivery_fee,2),
				'wallet_used'			=>number_format($walletAmount,2),
				'grand_total'			=>number_format($grand_total_text,2)];
				$encode = ['code' => 200,'message' => "Order Available",'data'	=> $data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msg 	= MobileModel::get_lang_text($lang,'API_NO_ORDERS','No Orders available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			exit;
		}
		/*  Add to cart */
		public function add_to_cart(Request $request){
			/*print_r($request->all());//Array( [item_id] => 15 [st_id] => 3 [choices_id] => Array  ([0] => 6 [1] => 5 ) [quantity] => 2 [lang] => en  )
			exit;        */
			$user = JWTAuth::user();
			$cus_id 	= $user->cus_id;
			$item_id 	= $request->item_id;
			$lang		= $request->lang;
			$st_id 		= $request->st_id;
			$choices_id	= $request->choices_id;
			$quantity	= $request->quantity;
			$spl_note	= $request->special_notes;
			$ch_price 	= 0;
			$had_ch 	= 'No';
			$pro_price 	= 'No';
			//print_r($choices_id); exit;
			/****************************** VALIDATION STARTS HERE ****************************************/
			$item_id_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_PDTITEM_ID','Please enter product/item ID!');
			$st_id_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store/restaurant ID!');
			$qty_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_QUANTITY','Please enter Quantity!');
			$item_id_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_PDTITEM','Please enter valid product/item ID!');
			$st_id_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
			$qty_valid_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_VALID_QTY','Please enter valid quantity!');
			$st_exist_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_NOTAVAIL_STOREREST','Store/Restaurant not available!');
			$item_exist_err_msg		= MobileModel::get_lang_text($lang,'API_PRODUCTITEM_NOT_AVAIL','Product/Item not available!');
			
			$validator = Validator::make($request->all(),[
			'item_id'	=> ['required','integer',
			Rule::exists('gr_product','pro_id')->where(function ($query) use($item_id){
				$query->where('pro_id','=',$item_id)->where('pro_status','=','1');
			})],
			'st_id'		=> ['required','integer',
			Rule::exists('gr_store','id')->where(function ($query) use($st_id){
				$query->where('id','=',$st_id)->where('st_status','=','1');
			})],
			'quantity'	=> 'required|integer'
			],
			['item_id.required' => $item_id_req_err_msg,
			'item_id.integer' 	=> $item_id_valid_err_msg,
			'item_id.exists' 	=> $item_exist_err_msg,
			'st_id.required'	=> $st_id_req_err_msg,
			'st_id.exists'		=> $st_exist_err_msg,
			'st_id.integer'		=> $st_id_valid_err_msg,
			'quantity.required'	=> $qty_req_err_msg,
			'quantity.integer'	=> $qty_valid_err_msg
			]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($item_id <= 0 ) { 
				$encode = ['code'=>400,"message"=>$item_id_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 
			if($st_id <= 0 ) {
				$encode = ['code'=>400,"message"=>$st_id_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 
			if($quantity <= 0 ) { 
				$encode = ['code'=>400,"message"=>$qty_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 			
			/* check shipping address is under store delivery radius */
			$get_ship_details = DB::table('gr_shipping')->select('sh_location','sh_latitude','sh_longitude')->where('sh_cus_id','=',$cus_id)->first();
			if(empty($get_ship_details) === true)
			{
				$msg = MobileModel::get_lang_text($lang,'API_FILL_SHIP_ADDR','Please fill shipping address to add items in cart');
				$encode = ['code'=>400,"message"=>$msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				if($get_ship_details->sh_latitude != '' && $get_ship_details->sh_longitude != '')
				{
					$check_radius = MobileModel::check_radius($get_ship_details->sh_latitude,$get_ship_details->sh_longitude,$st_id);
					//print_r($check_radius); exit;
					if(empty($check_radius) === true)
					{
						$msg = MobileModel::get_lang_text($lang,'API_DELIVER_NT_AVAIL','Delivery not available for your shipping location');
						$encode = ['code'=>400,"message"=>$msg,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}
				}
			}
			/* check shipping address is under store delivery radius ends */
			
			$pro_currency='';
			$cart_type = '';
			$spl_req   = '';
			$check_store_exist = DB::table('gr_store')->where('id','=',$st_id)->where('st_status','=','1')->first();
			if(empty($check_store_exist) === false)
			{
				if($check_store_exist->st_type=='1')
				{
					$cart_type = '2'; //item
					$spl_req = $spl_note;
				}else
				{
					$cart_type = '1'; //product						
				}
			}	
			
			/** CHECK AVAILABLE QUANTITY. IF THE STOCK QUANTITY IS 0 THEN DISPLAY NO STOCK AVAIL ELSE ADD INFO YOU CAN ADD UPTO **/
			$available = check_qty($item_id);
			$check_cart = DB::table('gr_cart_save')->selectRaw('SUM(cart_quantity) as added_qty')->where(['cart_st_id' => $st_id,'cart_item_id' => $item_id,'cart_cus_id' => $cus_id])->pluck('added_qty');
			//$total_count = $quantity+$check_cart[0];
			//echo $total_count.' > '.$available->stock.'<br>'.$available->stock.' < '.$quantity;  exit; 2 > 1 & 1 < 1
			if(empty($available) === false)
			{
				if($available->stock < $quantity)
				{	
					if($available->stock > 0 ){
						$stock_notavail_err_msg  = MobileModel::get_lang_text($lang,'API_EXCEEDS_STOCK_LIMIT','Entered quantity that exceeds stock quantity!');
						$stock_notavail_err_msg .= MobileModel::get_lang_text($lang,'API_CANADD_QTY','You can add upto');
						$stock_notavail_err_msg .= $available->stock.' quantity!';
						$encode = ['code'=>400,"message"=>$stock_notavail_err_msg,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
					}
					else{
						$stock_notavail_err_msg=MobileModel::get_lang_text($lang,'API_NO_STOCK_AVAIL','Sorry! No stock available!');
						$encode = ['code'=>400,"message"=>$stock_notavail_err_msg,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
					}
				}
				//CHECK QUANTITY+ALREADY ADDED QUANTITY IN CART.	
				/* Update only current quantity */
				/*if($total_count > $available->stock){
					$stock_notavail_err_msg	 = MobileModel::get_lang_text($lang,'API_EXCEEDS_STOCK_LIMIT','Entered quantity that exceeds stock quantity!');
					$stock_notavail_err_msg .= MobileModel::get_lang_text($lang,'API_ALREADY_YOU_HAVE','Already you have ');
					$stock_notavail_err_msg .= ' '.$check_cart[0];
					$stock_notavail_err_msg .= MobileModel::get_lang_text($lang,'API_QTY_IN_CART','quantity in cart!');
					$encode = ['code'=>400,"message"=>$stock_notavail_err_msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
				} */
			}
			else
			{
				$stock_notavail_err_msg = MobileModel::get_lang_text($lang,'API_NO_STOCK_AVAIL','Sorry! No stock available!');
				$encode = ['code'=>400,"message"=>$stock_notavail_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			$ch_array = array();
			//ADDED TO CHOICE AS DECODE
			if(count($choices_id) > 0)
			{	
				$had_ch 	= 'Yes';
				foreach($choices_id as $ch)
				{
					$get_price = DB::table('gr_product_choice')->select('pc_price')->where(['pc_choice_id' => $ch,'pc_pro_id' => $item_id])->first();
					if(empty($get_price) === false)
					{
						$ch_array[] = array("choice_id" => intval($ch),"choice_price" => $get_price->pc_price);
						$ch_price +=$get_price->pc_price;
					}
				}
			}
			//CHECK ALREADY THIS ITEM ARE IN CART
			$pro_details= DB::table('gr_product')->select('pro_currency','pro_original_price','pro_has_discount','pro_discount_price','pro_had_tax','pro_tax_percent','pro_had_choice')->where('pro_id','=',$item_id)->where('pro_status','=','1')->first();
			$pro_currency= $pro_details->pro_currency;
			$pro_price= $pro_details->pro_original_price;
			if($pro_details->pro_has_discount=='yes') { $pro_price = $pro_details->pro_discount_price; }
			if($pro_details->pro_had_tax=='Yes') { $tax = $pro_details->pro_tax_percent; } else { $tax = 0; }
			
			$check_cart_items	  = check_cart($cus_id,$st_id,$item_id,json_encode($ch_array),$pro_currency);
			//print_r($check_cart_items); exit;
			/* update cart */
			if(empty($check_cart_items) === false)
			{ 	
				//$quantity = $check_cart_items->cart_quantity + $quantity;
				$tax = ((($pro_price * $quantity) * $tax)/100);
				$total_amt = ((($pro_price + $ch_price) * $quantity) + $tax);
				$insert_arr = [	'cart_quantity' 	=> $quantity,
				'cart_unit_amt'		=> $pro_price,
				'cart_currency'		=> $pro_currency,
				'cart_tax'			=> $tax,
				'cart_total_amt' 	=> $total_amt,
				'cart_spl_req'		=> $spl_req,
				'cart_updated_at' 	=> date('Y-m-d H:i:s')];
				
				$insert = updatevalues('gr_cart_save',$insert_arr,['cart_cus_id' => $cus_id,'cart_id' => $check_cart_items->cart_id,'cart_st_id' => $st_id]);
				$msg 	= MobileModel::get_lang_text($lang,'API_CART_UPDATE_SUXUS','Cart updated successfully!');
				$total_cart_count = cart_count($cus_id);
				$total_cart_amount = cart_amount($cus_id);
				$encode = ['code'=> 200,'message' => $msg,
				'data'	   => ['cart_id'		=> $check_cart_items->cart_id,
								'total_cart_count'	=> $total_cart_count,
								'total_cart_amount'	=> $total_cart_amount,
								'cart_quantity' => $quantity,
								'cart_currency' => $pro_currency,
								'cart_total' 	=> number_format($total_amt,2)]
								];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			/*  add cart */
			else {
				$tax = ((($pro_price * $quantity) * $tax)/100);
				$total_amt = ((($pro_price + $ch_price) * $quantity) + $tax);
				$insert_arr = [	'cart_cus_id' 		=> $cus_id,
				'cart_st_id' 		=> $st_id,
				'cart_item_id'		=> $item_id,
				'cart_had_choice'	=> $had_ch,
				'cart_quantity' 	=> $quantity,
				'cart_unit_amt'		=> $pro_price,
				'cart_tax'			=> $tax,
				'cart_total_amt' 	=> $total_amt,
				'cart_currency'		=> $pro_currency,
				'cart_type' 		=> $cart_type, //item cart
				'cart_spl_req'		=> $spl_req, 
				'cart_choices_id' 	=> json_encode($ch_array),
				'cart_updated_at' 	=> date('Y-m-d H:i:s')];
				$insert =  DB::table('gr_cart_save')->insertGetId($insert_arr);
    			$total_cart_count = cart_count($cus_id);
    			$total_cart_amount = cart_amount($cus_id);
				$msg = MobileModel::get_lang_text($lang,'API_CART_ADD_SUXUS','Cart added sucessfully');
				$encode = [ 'code'=> 200,'message' => $msg,
				'data'			=> ['cart_id' 		=> $insert,
				'total_cart_count'	=> $total_cart_count,
				'total_cart_amount'	=> $total_cart_amount,
				'cart_quantity' => $quantity,
				'cart_currency' => $pro_currency,
				'cart_total' 	=> number_format($total_amt,2)]
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		/* repeat order */
		public function repeat_order(Request $request)
		{
			$lang 			= $request->lang;
			$order_id 	  	= $request->order_id;
			$cus_id 		= JWTAuth::user()->cus_id;
			$orderId_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_exist_err_msg = MobileModel::get_lang_text($lang,'API_OR_ID_NT_EXIST','Order id does not exist');
			$validator = Validator::make($request->all(),['order_id'=>['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function($query) use($cus_id){ $query->where('ord_cus_id','=',$cus_id);})
			],
			],
			['order_id.required'	=>$orderId_valid_err_msg,
			'order_id.exists'		=>$orderId_exist_err_msg,
			]); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				//$error = $not_avail_error = array();
				$insert_count = 0;
				$store_name = ($lang == $this->admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
				/* remove already existing cart for customer */
				DB::table('gr_cart_save')->where('cart_cus_id','=',$cus_id)->delete();
				/* get added items */
				/*$details = get_related_details('gr_order',['ord_transaction_id' => $order_id],['ord_pro_id',
					'ord_quantity',
					'ord_type',
					'ord_cus_id',
					'ord_had_choices',
					'ord_unit_price',
					'ord_tax_amt',
					'ord_rest_id',
					'ord_grant_total',
					'ord_currency',
				'ord_choices']);*/
				$details = DB::table('gr_order')->select(	'ord_pro_id',
															'ord_quantity',
															'ord_type',
															'ord_cus_id',
															'ord_had_choices',
															'ord_unit_price',
															'ord_tax_amt',
															'ord_rest_id',
															'ord_grant_total',
															'ord_currency',
															'ord_choices',
															'gr_product.pro_had_choice',
															'gr_product.pro_no_of_purchase',
															'gr_product.pro_quantity',
															'gr_product.pro_has_discount',
															'gr_product.pro_original_price',
															'gr_product.pro_discount_price',
															'gr_product.pro_had_tax',
															'gr_product.pro_tax_percent',
															'gr_product.pro_currency',
															'gr_product.pro_store_id',
															'gr_store.'.$store_name.' as st_name'
														)
				->Join('gr_product','gr_product.pro_id','=','gr_order.ord_pro_id')
				->Join('gr_store','gr_store.id','=','gr_order.ord_rest_id')
				->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
				->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
				->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
				->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
				->where(
				['gr_order.ord_transaction_id' => $order_id,
				'gr_product.pro_status'=>'1',
				'gr_store.st_status'=>'1',
				'gr_merchant.mer_status'=>'1',
				'gr_category.cate_status' => '1',
				'gr_merchant.mer_status'=>'1',
				'gr_proitem_maincategory.pro_mc_status'=>'1',
				'gr_proitem_subcategory.pro_sc_status'=>'1'
				])
				->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
				->get();
				//print_r($details); exit;
				$get_ship_details = DB::table('gr_shipping')->select('sh_location','sh_latitude','sh_longitude')->where('sh_cus_id','=',$cus_id)->first();
				$location_error = array();
				if(count($details) > 0)
				{	
					$exist_or_count = count($details);
					foreach($details as $reorder)
					{	
						$check_radius = MobileModel::check_radius($get_ship_details->sh_latitude,$get_ship_details->sh_longitude,$reorder->pro_store_id);
						//print_r($check_radius); exit;
						if(empty($check_radius) === true)
						{
							$msg = MobileModel::get_lang_text($lang,'API_DEL_NOT_AVAIL','In :store , delivery not available for your shipping location');
							$result = str_replace(':store',$reorder->st_name,$msg);
							array_push($location_error,$result);
						}
						else
						{
							$name = ($lang == $this->admin_default_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
							$grand_choice_price = 0;
							$ch_array = array();
							if($reorder->pro_had_choice=='1')
							{
								$choices = $reorder->ord_choices;
								$choiceArray = json_decode($choices);
								if(count($choiceArray) > 0 ){
									foreach($choiceArray as $choiceElement){
										$choice_id = $choiceElement->choice_id;
										$choice_price = $choiceElement->choice_price;
										$choice_checking = DB::table('gr_choices')->select('ch_name')->where('ch_status','1')->where('ch_id',$choice_id)->first();
										if(empty($choice_checking)===false){
											$choice_price_det = DB::table('gr_product_choice')->select('pc_price')->where('pc_choice_id',$choice_id)->where('pc_pro_id',$reorder->ord_pro_id)->first();
											if(empty($choice_price_det)===false){
												$get_product_price = $choice_price_det->pc_price;
												$ch_array[] = array(	'choice_id' => $choice_id,
												'choice_price'	=> $get_product_price
												);
												$grand_choice_price += $get_product_price;
											}
										}
									}
								}
							}
							
								$availableqty = $reorder->pro_quantity - $reorder->pro_no_of_purchase;
								
								if($availableqty>=$reorder->ord_quantity)
								{
									$ord_quantity=$reorder->ord_quantity;
								}
								else
								{
									$ord_quantity =  $availableqty;
								}
								/*NAGOOR START */
								if($reorder->pro_had_choice=='1' && $reorder->ord_had_choices == 'Yes')
								{ $hadChoice = 'Yes'; } else { $hadChoice = 'No'; }
								$now = date('Y-m-d H:i');
								if($reorder->pro_has_discount=='yes')
								{											
									$unit_price = $reorder->pro_discount_price;
								}
								else
								{
									$unit_price = $reorder->pro_original_price;
								}
								if($reorder->pro_had_tax=='Yes'){
									$single_tax_amount = ($unit_price*$reorder->pro_tax_percent)/100;
									$tax_amount = $ord_quantity*$single_tax_amount;
								}else{
									$tax_amount = 0;
								}
								/* NAGOOR NEVER ENDS*/
								
								if($reorder->ord_type=='Item')
								$cartype=2;
								
			                 	if($reorder->ord_type=='Product')
								$cartype=1;
								
			                 	$insert_arr = [	'cart_cus_id' 		=> $reorder->ord_cus_id,
												'cart_st_id' 		=> $reorder->ord_rest_id,
												'cart_item_id' 		=> $reorder->ord_pro_id,
												'cart_had_choice' 	=> $hadChoice,
												'cart_quantity' 	=> $ord_quantity,
												'cart_unit_amt' 	=> $unit_price,
												'cart_tax' 			=> $tax_amount,
												'cart_total_amt' 	=> ((($unit_price + $grand_choice_price) * $ord_quantity) + $tax_amount),//$reorder->ord_grant_total,
												'cart_currency' 	=> $reorder->pro_currency,
												'cart_type' 		=> $cartype,
												'cart_choices_id' 	=> json_encode($ch_array),
												'cart_updated_at' 	=> date('Y-m-d H:i:s')
												];
			                 	$insert = insertvalues('gr_cart_save', $insert_arr);
			                 	$insert_count++;
						}
					}
					if(empty($location_error) && ($exist_or_count == $insert_count))
					{
						$msg = MobileModel::get_lang_text($lang,'API_CART_ADD_SUXUS','Cart added sucessfully');
						$encode = ['code'=> 200,'message' => $msg,'data'=> $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
					}
					else
					{	
						$code = 200;
						$msg = MobileModel::get_lang_text($lang,'API_CART_ADD_SUXUS','Cart added sucessfully');
						if($insert_count == 0 && !empty($location_error) && $exist_or_count == 1)
						{
							$code = 400;
							$error = array_unique($location_error);
							$msg = $error[0];
						}	
						else
						{
							$code = 400;
							$msg = MobileModel::get_lang_text($lang,'API_NO_ITEM_ADDED','No items will be added in cart!');
						}	         	
						$encode = ['code'		=> $code,
									'message' 	=> $msg,
									'data'		=> $this->empty_data//['errors' => array_unique($location_error)]
								  ];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
					}
					
				}
				else
				{
					$messages = MobileModel::get_lang_text($lang,'API_NT_AVAIL_PRO','Item is not available');
					$encode = ['code'		=> 400,
								'message' 	=> $messages,
								'data'		=> $this->empty_data
								];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
				}
			}
		}
		
		/* increase / decrease cart */
		public function qty_update_cart(Request $request)
		{
			$lang 		= $request->lang;
			$cus_id 	= JWTAuth::user()->cus_id;
			$cart_id 	= $request->cart_id;
			$qty 		= $request->quantity;
			$cart_req 	= MobileModel::get_lang_text($lang,'API_ENTER_CART_ID','Please enter cart ID!');
			$cart_exist = MobileModel::get_lang_text($lang,'API_NT_EXIST_CART_ID','Cart id not exist');
			$qty_req 	= MobileModel::get_lang_text($lang,'API_ENTER_QUANTITY','Please enter Quantity!');
			$validator = Validator::make($request->all(),['cart_id' => ['Required',
			Rule::exists('gr_cart_save','cart_id')->where(function($query) use($cus_id){ $query->where('cart_cus_id','=',$cus_id);})
			],
			'quantity' => 'Required'
			],
			['cart_id.required' => $cart_req,
			'cart_id.exists'   => $cart_exist,
			'quantity.required'=> $qty_req,
			]);
			if($validator->fails())
			{
				$messages = $validator->messages()->first();
				$encode = ['code' => 400,'message' => $messages,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}
			else
			{	
				/* update product price */
				update_cart_price($cus_id);
				$cart_details = DB::table('gr_cart_save')->select('cart_item_id','cart_unit_amt','cart_choices_id',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),'cart_st_id')
				->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
				->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
				->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
				->where(['gr_cart_save.cart_cus_id' => $cus_id,'gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1','cart_id'=> $cart_id])
				//->whereRaw('(gr_product.pro_quantity-gr_product.pro_no_of_purchase)>='.$quantity.'')
				->first();
				//return $cart_details->stock; exit;
				$ch_array = array();
				$choice_total = 0;
				if(empty($cart_details)===false)
				{	
					/*  check quantity */
					if($cart_details->stock < $qty)
					{
						$messages = MobileModel::get_lang_text($lang,'API_QTY_EXCEEDS','We currently only have :qty in stock.Please select a different quantity');
						$msg = str_replace(':qty',$cart_details->stock,$messages);
						$encode = ['code' => 400,'message' => $msg,'data'=> $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
					}
					$cart_unit_amt = $cart_details->cart_unit_amt;
					$tax_calculation = DB::table('gr_product')->select('pro_had_tax','pro_tax_percent')->where('pro_id', '=', $cart_details->cart_item_id)->first();
					if(empty($tax_calculation)===false)
					{
						if($tax_calculation->pro_had_tax=='Yes')
						{
							$tax_percent = $tax_calculation->pro_tax_percent;
							$cart_tax = (($qty*$cart_unit_amt)*$tax_percent)/100;
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
							$choice_total +=$qty*$ch_price;
						}
						
						$cart_had_choice = 'Yes';
					}
					else
					{
						$cart_had_choice = 'No';
					}
					$cart_total_amt = ($qty*$cart_unit_amt)+$cart_tax+$choice_total;
					$insert_arr = [	'cart_quantity'=>$qty,
					'cart_tax'	=> $cart_tax,
					'cart_had_choice'	=> $cart_had_choice,
					'cart_total_amt' 	=> $cart_total_amt,
					'cart_updated_at' 	=> date('Y-m-d H:i:s')];
					//print_r($insert_arr); echo $request->cart_id; exit;
					updatevalues('gr_cart_save',$insert_arr,['cart_id' => $cart_id,'cart_cus_id' => $cus_id]);
					$messages = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully');
					$encode = ['code' => 200,'message' => $messages,'data'=> $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
					
				}
				else
				{
					$messages = MobileModel::get_lang_text($lang,'API_CART_NO_ITEM','No items in cart');
					$encode = ['code' => 400,'message' => $messages,'data'=> $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
				}
				
				
			}
		}
		
		public function cart_restaurant_wise(Request $request){
			$user 		= JWTAuth::user();
			$cus_id		= $user->cus_id;
			$lang 		= $request->lang;
			$cart_details= MobileModel::get_cart_details($cus_id,'',$lang,$this->admin_default_lang);
			
			$cart_array = array();
			$total_amt = 0;
			if(count($cart_details) > 0)
			{
				$sub_total = array();
				foreach($cart_details as $key => $value)
				{
					$key_values = explode('~`',$key);
					$product_details = array();
					
					foreach($value as $details)
					{	
						$sub_total[$key_values[1]][] =$details->cart_total_amt;
						$total_amt +=$details->cart_total_amt;
					}
					if($key_values[2]=='2') { $store_status_text = 'Available'; } else {  $store_status_text = $key_values[5]; }
					$cart_array[] = ['store_id' 			=> intval($key_values[1]),
					'store_name' 			=> ucfirst($key_values[0]),
					'sub_total'				=> number_format(array_sum($sub_total[$key_values[1]]),2),
					'minimum_order_amount'	=> $key_values[4],
					'store_status'		 	=> $store_status_text,
					'validation' 			=> intval($key_values[6])
					];
				}
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$encode = ['code' 			=> 200,
				'message' 			=> $msge,
				'total_cart_count'	=> cart_count($cus_id),
				'total_cart_amount' => $total_amt,
				'cart_details' 		=> $cart_array];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
			}
			else
			{
				$msge	= MobileModel::get_lang_text($lang,'API_NO_ITEMS_IN_CART','No items in cart!');
				$encode = ['code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}
		}
		
		public function cart_detail_byrestID(Request $request){
			$user = JWTAuth::user();
			$cus_id 	 = $user->cus_id;
			$lang 		 = $request->lang;
			$cart_st_id	 = $request->cart_st_id;
			/****************************** VALIDATION STARTS HERE ****************************************/
			$st_id_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store/restaurant ID!');
			$st_id_valid_err_msg= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
			$validator = Validator::make($request->all(),['cart_st_id'=>'required|integer'],['st_id.required'=>$st_id_req_err_msg,'st_id.integer'=>$st_id_valid_err_msg]); 
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($cart_st_id <= 0 ) { 
				$encode = ['code'=>400,"message"=>$st_id_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			} 
			$cart_details = MobileModel::get_cart_details($cus_id,$cart_st_id,$lang,$this->admin_default_lang);
			$cart_array	  = array();
			$total_amt	  = 0;
			if(count($cart_details) > 0)
			{
				foreach($cart_details as $key => $value)
				{
					$key_values = explode('~`',$key);
					$product_details = array();
					foreach($value as $details)
					{	$image = '';
						if($details->cart_type == '1') //product
						{
							$image = MobileModel::get_image_product($details->pro_image);
						}
						elseif($details->cart_type == '2') //item
						{
							$image = MobileModel::get_image_item($details->pro_image);
						}
						$ch_array = array();
						if($details->cart_had_choice == 'Yes')
						{
							$choices = json_decode($details->cart_choices_id,true);
							if(count($choices) > 0)
							{
								foreach($choices as $ch)
								{	
									$name = MobileModel::get_choice_name($ch['choice_id'],$lang,$this->admin_default_lang);
									$ch_array[] = ['choice_id' 		=> intval($ch['choice_id']),
									'choice_name' 	=> $name->ch_name,
									'choice_amount'	=> $ch['choice_price']
									];
								}
							}
						}
						$product_details[] = array('cart_id'		=> intval($details->cart_id),
						'product_id'		=> intval($details->pro_id),
						'product_name'		=> ucfirst($details->item_name),
						'product_image' 	=> $image,	
						'cart_quantity' 	=> $details->cart_quantity,
						'cart_unit_price'	=> $details->cart_unit_amt,
						'cart_tax'			=> $details->cart_tax,
						'cart_has_choice'	=> $details->cart_had_choice,
						'cart_choices'		=> $ch_array,
						'cart_sub_total'	=> number_format($details->cart_total_amt,2),
						'cart_currency'	=> $details->cart_currency,
						'cart_pre_order'	=> ($details->cart_pre_order == '') ? "" : $details->cart_pre_order 
						);
						$total_amt +=$details->cart_total_amt;
					}
					if($key_values[2]=='2') { $store_status_text = 'Available'; } else {  $store_status_text = $key_values[5]; }
					$cart_array[] = ['store_id' 			=> intval($key_values[1]),
					'store_name' 			=> ucfirst($key_values[0]),
					'minimum_order_amount'	=> $key_values[4],
					'store_status'			=> $store_status_text,
					'added_item_details' 	=> $product_details,
					'validation' 			=> intval($key_values[6])
					];
				}
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$encode = ['code' 			=> 200,
				'message' 			=> $msge,
				'total_cart_count'	=> cart_count($cus_id),
				'total_cart_amount' => $total_amt,
				'cart_details' 		=> $cart_array];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
			}
			else
			{
				$msge	= MobileModel::get_lang_text($lang,'API_NO_ITEMS_IN_CART','No items in cart!');
				$encode = ['code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}
		}
		
		/* add choice in existing cart */
		public function add_choice_toCart(Request $request)
		{
			$lang 		= $request->lang;
			$cus_id 	= JWTAuth::user()->cus_id;
			$cart_id 	= $request->cart_id;
			$product_id = $request->product_id;
			$choice_id 	= $request->choice_id;
			$special_request 	= $request->special_request;
			$cart_req 	= MobileModel::get_lang_text($lang,'API_ENTER_CART_ID','Please enter cart ID!');
			$cart_exist = MobileModel::get_lang_text($lang,'API_NT_EXIST_CART_ID','Cart id not exist');
			$pdt_req 	= MobileModel::get_lang_text($lang,'API_VALID_PDT_ID','Enter valid product ID!');
			$pdt_exist 	= MobileModel::get_lang_text($lang,'API_PDT_DOESNT_EXIST','Product does not exists!');
			$ch_req 	= MobileModel::get_lang_text($lang,'API_ENTR_CH_ID','Enter choice id!');
			$validator = Validator::make($request->all(),['cart_id' => ['Required',
																		Rule::exists('gr_cart_save','cart_id')->where(function($query) use($cus_id){ $query->where('cart_cus_id','=',$cus_id);})
																		],
														'product_id' => ['Required',
																		Rule::exists('gr_cart_save','cart_item_id')->where(function($query) use($cus_id){ $query->where('cart_cus_id','=',$cus_id);})
																		],
														'choice_id'	=> 	'Sometimes'	],
														['cart_id.required' => $cart_req,
														'cart_id.exists'   => $cart_exist,
														'product_id.required'	=> $pdt_req,
														'product_id.exists'	=> $pdt_exist,
														'choice_id.Sometimes'	=> $ch_req
														]
														);
			if($validator->fails())
			{
				$messages = $validator->messages()->first();
				$encode = ['code' => 400,'message' => $messages,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
				
			}
			if(!empty($choice_id))
			{
				foreach($choice_id as $ch)
				{
					$count = DB::table('gr_product_choice')->where(['pc_choice_id' => $ch,'pc_pro_id' => $product_id])->get()->count();
					if($count <= 0)
					{
						$messages = MobileModel::get_lang_text($lang,'API_NT_EXIST_CH_ID','Choice id (:id) does not exist for product');
						$msg = str_replace(':id',$ch,$messages);
						$encode = ['code' => 400,'message' => $msg,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
					}
				}
				
			}
			
			/* update product price */
			update_cart_price($cus_id);
			$cart_details = DB::table('gr_cart_save')->where('cart_id', '=', $cart_id)->first();
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
				$st_id = $cart_details->cart_st_id;
				if($choice_id!='')
				{	
					foreach($choice_id as $ch)
					{
						$get_price = DB::table('gr_product_choice')->select('pc_price')->where('pc_pro_id','=',$product_id)->where('pc_choice_id','=',$ch)->first();
						$ch_array[] = ['choice_id' => $ch,'choice_price' => $get_price->pc_price];
						$choice_total += ($quantity*$get_price->pc_price);
					}
					
					$cart_had_choice = 'Yes';
				}
				else
				{
					$cart_had_choice = 'No';
				}
				/** check item with choice already added **/
				$check 	= check_cart($cus_id,$st_id,$product_id,json_encode($ch_array)); 
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
					DB::table('gr_cart_save')->where(['cart_id' => $cart_id])->delete(); 
				}
				else
				{
					$insert = updatevalues('gr_cart_save',$insert_arr,['cart_id' => $cart_id]);
				}
				$messages = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully');
				$encode = ['code' => 200,'message' => $messages,'data'=> $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}

			
		}
		
		/* remove choice from cart */
		public function remove_choice(Request $request)
		{
			$lang 		= $request->lang;
			$cus_id 	= JWTAuth::user()->cus_id;
			$cart_id 	= $request->cart_id;
			$product_id = $request->product_id;
			$choice_id 	= $request->choice_id;
			$cart_req 	= MobileModel::get_lang_text($lang,'API_ENTER_CART_ID','Please enter cart ID!');
			$cart_exist = MobileModel::get_lang_text($lang,'API_NT_EXIST_CART_ID','Cart id not exist');
			$pdt_req 	= MobileModel::get_lang_text($lang,'API_VALID_PDT_ID','Enter valid product ID!');
			$pdt_exist 	= MobileModel::get_lang_text($lang,'API_PDT_DOESNT_EXIST','Product does not exists!');
			$ch_req 	= MobileModel::get_lang_text($lang,'API_ENTR_CH_ID','Enter choice id!');
			$validator = Validator::make($request->all(),['cart_id' => ['Required',
			Rule::exists('gr_cart_save','cart_id')->where(function($query) use($cus_id){ $query->where('cart_cus_id','=',$cus_id);})
			],
			'product_id' => ['Required',
			Rule::exists('gr_cart_save','cart_item_id')->where(function($query) use($cus_id){ $query->where('cart_cus_id','=',$cus_id);})
			],
			'choice_id'	=> 	'Required'	],
			['cart_id.required' => $cart_req,
			'cart_id.exists'   => $cart_exist,
			'product_id.required'	=> $pdt_req,
			'product_id.exists'	=> $pdt_exist,
			'choice_id.required'	=> $ch_req]);
			if($validator->fails())
			{
				$messages = $validator->messages()->first();
				$encode = ['code' => 400,'message' => $messages,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
				
			}
			foreach($choice_id as $ch)
			{
				$count = DB::table('gr_product_choice')->where(['pc_choice_id' => $ch,'pc_pro_id' => $product_id])->get()->count();
				if($count <= 0)
				{
					$messages = MobileModel::get_lang_text($lang,'API_NT_EXIST_CH_ID','Choice id (:id) does not exist for product');
					$msg = str_replace(':id',$ch,$messages);
					$encode = ['code' => 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
				}
			}
			/* update product price */
			update_cart_price($cus_id);
			$exist_cart = DB::table('gr_cart_save')->select('cart_choices_id','cart_had_choice','cart_tax','cart_unit_amt','cart_quantity')->where('cart_id','=',$cart_id)->first();
			$intersect_arr = $update_ch = array();
			$total_tax = $unit_amt = $qty = 0; 
			if(!empty($exist_cart) && $exist_cart->cart_choices_id != '')
			{
				$ch_arr = json_decode($exist_cart->cart_choices_id,true);	
				/* check choice id already exist */		
				foreach($ch_arr as $obj)
				{
					$exist_arr[] = $obj['choice_id'];
				}
				/* get choices after removing given choice */
				$update_ch = array_merge(array_diff($exist_arr, $choice_id), array_diff($choice_id, $exist_arr));
				$total_tax = $exist_cart->cart_tax;
				$unit_amt  = $exist_cart->cart_unit_amt;
				$qty 	   = $exist_cart->cart_quantity;
			}
			/*else
				{
				$update_ch = $choice_id;
			}*/
			$choice_total = 0; $had_choice  = '0';
			if(empty($update_ch) === false)
			{
				foreach($update_ch as $ch)
				{
					$get_price = DB::table('gr_product_choice')->select('pc_price')->where('pc_pro_id','=',$product_id)->where('pc_choice_id','=',$ch)->first();
					$intersect_arr[] = ['choice_id' => $ch,'choice_price' => $get_price->pc_price];
					$choice_total += ($qty*$get_price->pc_price);
				}
				$had_choice  = '1';
			}
			
			$cart_total_amt = ($qty*$unit_amt)+$total_tax+$choice_total;
			$insert_arr = [	'cart_had_choice'	=> $had_choice,
			'cart_total_amt' 	=> $cart_total_amt,
			'cart_choices_id' 	=> json_encode($intersect_arr),
			'cart_updated_at' 	=> date('Y-m-d H:i:s'),
			'cart_quantity'		=> $qty
			];
			updatevalues('gr_cart_save',$insert_arr,['cart_id' => $cart_id,'cart_cus_id' => $cus_id]);
			$messages = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully');
			$encode = ['code' => 200,'message' => $messages,'data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
		}
		
		/* my cart */
		public function my_cart(Request $request)
		{
			$user = JWTAuth::user();
			
			$customer_id = $user->cus_id;
			$lang 		 = $request->lang;
			/* update product price*/
			update_cart_price($customer_id);
			
			$cart_details= MobileModel::get_cart_details($customer_id,'',$lang,$this->admin_default_lang);
			$shippingDet = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id','=',$customer_id)->first();
			$cart_array = array();
			$total_amt = 0;
			$del_fee = "0.00";
			if(count($cart_details) > 0)
			{	
				$pre_error = $min_or_error = $location_arr = $quantity_err = array();
				
				
				if($this->general_setting->gs_delivery_fee_status=='1' && $this->general_setting->gs_del_fee_type == 'common_fee')
				{
					$del_fee = $this->general_setting->gs_delivery_fee;
				}
				
				foreach($cart_details as $key => $value)
				{
					$key_values = explode('~`',$key);
					$exist_store_id = ''; 
					$store_total_amount = 0;
					if($exist_store_id == '' && $exist_store_id != $key_values[1])
					{
						$exist_store_id = $key_values[1];
					}
					/* calculate kilometer based delivery fee */
					if($this->general_setting->gs_delivery_fee_status=='1' && $this->general_setting->gs_del_fee_type == 'km_fee' && !empty($shippingDet))
					{	
						$user_lat = $shippingDet->sh_latitude;
						$user_long= $shippingDet->sh_longitude;
						$kilometer = calculate_distance($user_lat,$user_long,$key_values[9],$key_values[10]);
						//echo $kilometer[0]->distance;
						$st_del_fee = $this->general_setting->gs_km_fee * $kilometer[0]->distance;
						$del_fee+=$st_del_fee;
						
					}
					
					$product_details = array();
					foreach($value as $details)
					{	
						$image = '';
						/* calculate individual store amount */
						if($exist_store_id == $key_values[1])
						{
							$store_total_amount +=$details->cart_total_amt;
						}
						if($details->cart_type == '1') //product
						{
							$image = MobileModel::get_image_product($details->pro_image);
						}
						elseif($details->cart_type == '2') //item
						{
							$image = MobileModel::get_image_item($details->pro_image);
						}
						$ch_array = array();
						if($details->cart_had_choice == 'Yes')
						{
							$choices = json_decode($details->cart_choices_id,true);
							if(count($choices) > 0)
							{
								foreach($choices as $ch)
								{	
									$name = MobileModel::get_choice_name($ch['choice_id'],$lang,$this->admin_default_lang);
									$ch_array[] = ['choice_id' 		=> intval($ch['choice_id']),
									'choice_name' 	=> $name->ch_name,
									'choice_amount'	=> ($ch['choice_price'] == '') ? 0.00 : $ch['choice_price']
									];
								}
							}
						}
						/* get all choices */
						$get_choices = MobileModel::get_choices($details->pro_id,$lang,$this->admin_default_lang);
						$all_ch_array = array();
						foreach($get_choices as $choices)
						{
							$all_ch_array[] = array('choice_id' => $choices->pc_choice_id,
													'choice_name'	=> ucfirst($choices->choice_name),
													'choice_currency'	=> $choices->pro_currency,
													'choice_price'		=> ($choices->pc_price == '') ? 0.00 : $choices->pc_price
													); 
						}
						if($details->stock < $details->cart_quantity)
						{
							$got_message = MobileModel::get_lang_text($lang,'API_QTY_EXCEED_ERR',':item quantity exceeds the availble stock :stock');
							$searchReplaceArray = array(':item' => $details->item_name,':stock' =>$details->stock );
							$quantity_err[] = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
						}

						$product_details[] = array('cart_id'		=> intval($details->cart_id),
													'restaurant_id'		=> intval($details->pro_store_id),
													'product_id'		=> intval($details->pro_id),
													'product_name'		=> ucfirst($details->item_name),
													'available_stock'	=> $details->stock,
													'product_image' 	=> $image,	
													'cart_quantity' 	=> $details->cart_quantity,
													'cart_unit_price'	=> $details->cart_unit_amt,
													'cart_tax'			=> $details->cart_tax,
													'cart_has_choice'	=> $details->cart_had_choice,
													'cart_choices'		=> $ch_array,
													'item_choices'		=> $all_ch_array,
													'cart_sub_total'	=> number_format($details->cart_total_amt,2),
													'cart_currency'		=> $details->cart_currency,
													'cart_pre_order'	=> ($details->cart_pre_order == '') ? "" : $details->cart_pre_order,
													'cart_spl_request'	=> $details->cart_spl_req
													);
						$total_amt +=$details->cart_total_amt;
					}
					if($key_values[2]=='2') 
					{ 
						$store_status_text = 'Available'; 
					} 
					else
					{  
						$store_status_text = $key_values[5]; 
					}
					$pre_or_ststus = "Not Available";
					if($key_values[7] == 1)
					{
						$pre_or_ststus = "Available";
					}
					
					if($key_values[6] == '1' && $details->cart_pre_order == '')
					{
						$got_message = MobileModel::get_lang_text($lang,'API_SL_PRE_OR',':store is closed.Choose pre order date to purchase');
						$searchReplaceArray = array(':store' => $key_values[0]);
						$pre_error[] = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
					}
					elseif($key_values[6] == '2' && $details->cart_pre_order == '')
					{
						$got_message = MobileModel::get_lang_text($lang,'API_SL_CLOSE',':store is closed.You can\'t purchase in this shop');
						$searchReplaceArray = array(':store' => $key_values[0]);
						$pre_error[] = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
					}
					if($store_total_amount < $key_values[4])
					{	
						$bal = number_format(($key_values[4] - $store_total_amount),2);
						$got_message = MobileModel::get_lang_text($lang,'API_BELOW_MIN_OR',':bal is remaining to reach minimum order amount in :store');
						$searchReplaceArray = array(':bal' => $bal,':store' => $key_values[0]);
						$min_or_error[] = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
					}
					array_push($location_arr,['store_name' => ucfirst($key_values[0]),'store_location'=> ucfirst($key_values[8])]);
					$cart_array[] = ['store_id' 			=> intval($key_values[1]),
					'store_name' 			=> ucfirst($key_values[0]),
					'minimum_order_amount'	=> $key_values[4],
					'store_status'		 	=> $store_status_text,
					'pre_order_status'	 	=> $pre_or_ststus,
					'added_item_details' 	=> $product_details];
				}
				$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$payable_amount = $total_amt+$del_fee;
				$payable_amt_usd = $total_amt+$del_fee;
				if($this->default_currency_code!='USD'){
				    $payable_amt_usd = convertCurrency($this->default_currency_code, 'USD', $payable_amount);
				}
				//	$payable_amt_usd = convertCurrency($this->default_currency_code, 'USD', $payable_amount);
				$encode = ['code' 			=> 200,
							'message' 		=> $msge,
							'data'			=> ['total_cart_count'	=> cart_count($customer_id),
												'currency_code'		=> $this->default_curr,
												'cart_sub_total'	=> number_format($total_amt,2),
												'delivery_fee'		=> number_format($del_fee,2),
												'total_cart_amount' => number_format(($payable_amount),2),
												'total_cart_amount_usd' => number_format(($payable_amt_usd),2),
												'cart_details' 		=> $cart_array,
												'pre_order_error'		=> $pre_error,
												'quantity_error'	=> $quantity_err,
												'minimum_order_error'	=> $min_or_error,
												'store_locations'	=> $location_arr,
												]
							];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
			}
			else
			{
				$msge	= MobileModel::get_lang_text($lang,'API_NO_ITEMS_IN_CART','No items in cart!');
				$encode = ['code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}
		}
		/*REMOVE FROM CART */
		public function remove_from_cart(Request $request){
			$user = JWTAuth::user();
			$cus_id 	= $user->cus_id;
			$lang 		= $request->lang;
			$cart_id 	= $request->cart_id;
			/****************************** VALIDATION STARTS HERE ****************************************/
			$cart_id_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_CART_ID','Please enter cart ID!');
			$cart_id_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_CART_ID','Please enter valid cart ID!');
			
			$validator = Validator::make($request->all(),['cart_id'=>'required|integer'],['cart_id.required' => $cart_id_req_err_msg, 'cart_id.integer' => $cart_id_valid_err_msg] ); 
			if($validator->fails()){
				$message 	= $validator->messages()->first();
				$encode 	= [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($cart_id <= 0 ) {
				$encode = ['code'=>400,"message"=>$cart_id_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 
			/* CHECK STORE ID AVAILABLE OR NOT */
			$check_cartid_exist = DB::table('gr_cart_save')->where('cart_id','=',$cart_id)->count();
			if($check_cartid_exist <= 0 ){
				$st_id_notavail_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_CART_ID','Please enter valid cart ID!');
				$encode = [ 'code' => 400,'message' => $st_id_notavail_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$delQuery = DB::table('gr_cart_save')->where('cart_id', '=', $cart_id)->delete();    
			$total_cart_count = cart_count($cus_id);
			$st_id_notavail_err_msg=MobileModel::get_lang_text($lang,'API_DELETE_SUXES','Deleted successfully!');
			$encode = [ 'code' => 200,'message' => $st_id_notavail_err_msg,'data' => ['total_cart_count' =>$total_cart_count ]];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
		public function cancel_order(Request $request){
			$user = JWTAuth::user();
			$cus_id 	= $user->cus_id;
			$cus_name 	= $user->cus_fname.' '.$user->cus_lname;
			$cus_id 	= $user->cus_id;
			$lang 		= $request->lang;
			$reason 	= $request->reason;
			$orderId 	= $request->orderId;
			/****************************** VALIDATION STARTS HERE ****************************************/
			$order_id_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$order_id_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');
			$order_id_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_CANCEL_REASON','Please enter cancel reason!');
			$validator = Validator::make($request->all(),['orderId'=>'required|integer','reason'=>'required'],['orderId.required' => $order_id_req_err_msg, 'orderId.integer' => $order_id_valid_err_msg, 'reason.required'=>$order_id_valid_err_msg] ); 
			if($validator->fails()){
				$message 	= $validator->messages()->first();
				$encode 	= [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($orderId <= 0 ) { 
				$encode = ['code'=>400,"message"=>$order_id_valid_err_msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 
			
			$ord_grand = DB::table('gr_order')->select('ord_grant_total','ord_merchant_id','ord_pay_type','ord_transaction_id','ord_admin_amt','pro_item_name','ord_refund_status','ord_quantity','ord_pro_id','ord_mer_cancel_status','ord_delivery_fee')
			->leftJoin('gr_merchant','gr_order.ord_merchant_id','=','gr_merchant.id')
			->leftJoin('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('ord_id','=',$orderId)->first();
			if(empty($ord_grand)===false)
			{	
				$ord_transaction_id		= $ord_grand->ord_transaction_id;
				//echo $ord_transaction_id.'<br>';
				$get_total_order_count 	= get_total_order_count($ord_transaction_id);
				$get_total_cancelled_count = get_total_cancelled_count($ord_transaction_id)+1;
				if($get_total_cancelled_count==$get_total_order_count)
				{
					$add_delfee_status=1;
					$cancel_amt = $ord_grand->ord_grant_total + $ord_grand->ord_delivery_fee;
					$mer_amount = $ord_grand->ord_grant_total-$ord_grand->ord_admin_amt+$ord_grand->ord_delivery_fee;
				}
				else
				{
					$add_delfee_status=0;
					$cancel_amt = $ord_grand->ord_grant_total;
					$mer_amount = $ord_grand->ord_grant_total-$ord_grand->ord_admin_amt;
				}
				/** update cancel status **/
				/** while user cancel the item, admin detect commission from user's item grant total amount **/
				/* if refund status is no, no need to add cancellation amount */
				$adm_comm_order = $ord_grand->ord_admin_amt;  //update in gr_order table
                $adm_comm_Overallorder = 0.00;  //update in gr_emrchant_overallordere table
                if($ord_grand->ord_pay_type == 'COD')
                {
                	$adm_comm_order = 0.00;
                	$adm_comm_Overallorder = $ord_grand->ord_admin_amt;
				}
				if($ord_grand->ord_refund_status == 'Yes')
				{	
					DB::table('gr_order')->where('ord_id', $orderId)->update(['add_delfee_status'=>$add_delfee_status,'ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_cancel_amt' => ($ord_grand->ord_grant_total- $ord_grand->ord_admin_amt),'ord_admin_amt' => $adm_comm_order]);
					/** update merchant amount **/
					DB::table('gr_merchant_overallorder')->where('or_mer_id', $ord_grand->ord_merchant_id)
					->update(['or_cancel_amt' => DB::raw('or_cancel_amt+'.($cancel_amt-$ord_grand->ord_admin_amt)),
					'or_mer_amt' => DB::raw('or_mer_amt+'.($mer_amount)),
					'or_admin_amt' => DB::raw('or_admin_amt-'.($adm_comm_Overallorder))]);
					/*--- update product quantity ---*/
					updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
				}
				else
				{
					DB::table('gr_order')->where('ord_id', $orderId)->update(['add_delfee_status'=>$add_delfee_status,'ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s')]);
					/*--- update product quantity ---*/
					updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
				}
				
				//GET CUSTOMER DETAILS
				$customerDet = MobileModel::get_cancelled_customer_byOrderId($orderId);
				if(empty($customerDet) === false)
				{
					$customerDetArray[$customerDet->ord_transaction_id]=$customerDet;
				}
				//GET PRODUCT DETAILS
				$storeDet = MobileModel::get_cancelled_store_byOrderId($orderId);
				if(count($storeDet)> 0 )
				{
					foreach($storeDet as $stDet)
					{
						$storeDetArray[$stDet->st_store_name.'`'.$stDet->mer_email][]=MobileModel::get_cancelled_order_byOrderId($orderId,$stDet->id,$lang,$this->admin_default_lang);
					}
				}
				foreach($customerDetArray as $key=>$customerDet)
				{
					$transaction_id = $key;
				}
				
				/* send notification */
				$got_message = MobileModel::get_lang_text($lang,'API_CANCEL_ITEM','The items (:item_name) in order id (:transaction_id) has been canceled by :customer_name');
				$searchReplaceArray = array(':customer_name' => ucfirst($cus_name),':item_name' => $ord_grand->pro_item_name,':transaction_id' => $transaction_id);
				$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
				//1) to merchant
				$message_link = 'mer-order-details/'.base64_encode($transaction_id);
				$noti_id = push_notification($cus_id,$ord_grand->ord_merchant_id,'gr_customer','gr_merchant',$result,$transaction_id,$message_link); 
				$mer_details = get_details('gr_merchant',['id'=>$ord_grand->ord_merchant_id],('mer_andr_fcm_id,mer_ios_fcm_id'));
				if(empty($mer_details) === false)
				{
					
					if($mer_details->mer_andr_fcm_id !='')
					{
						$parse_fcm=json_decode($mer_details->mer_andr_fcm_id,true);
						$reg_id = array();
						if(count($parse_fcm) > 0 )
						{
							foreach($parse_fcm as $parsed)
							{ 
								array_push($reg_id,$parsed['fcm_id']);						
							}
						}
						$json_data = ["registration_ids" => $reg_id,
											 "data"=> ['transaction_id'		=> $transaction_id,
														'type'				=> 'Cancelled order',
														'notification_id'	=> $noti_id,
														"body" 				=> $result,
														"title" 			=> "Order Cancelled Notification"]
											];
								$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_MER);
					}
					if($mer_details->mer_ios_fcm_id !='')
					{
						$parse_fcm=json_decode($mer_details->mer_ios_fcm_id,true);
						$reg_id = array();
						if(count($parse_fcm) > 0 )
						{
							foreach($parse_fcm as $parsed)
							{ 
								array_push($reg_id,$parsed['fcm_id']);						
							}
						}
						
						$json_data = [	"registration_ids" => $reg_id,
												"notification"	=> ['transaction_id'	=> $transaction_id,
																	'type'				=> 'Cancelled order',
																	'notification_id'	=> $noti_id,
																	"body" 				=> $result,
															"title"			 	=> "Order Cancelled Notification",
															"sound"				=> "default"]
											];
								$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
					}
					
				}
				//2)to admin					
				$message_link = 'admin-track-order/'.base64_encode($transaction_id);
				push_notification($cus_id,$this->admin_id,'gr_customer','gr_admin',$result,$transaction_id,$message_link);
				/********************************************************NEED TO WORK MAIL SECTION **********************************/
				if(empty($customerDet) === false)
				{
					/** ------------------ MAIL FUNCTION  ----------------------------- **/
					//1) MAIL TO CUSTOMER
					$customerMail =  $customerDet->order_ship_mail;
					
					$send_mail_data = array('order_details'	=> $storeDetArray,'transaction_id'=>$transaction_id,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang,'customerMail'=>$customerMail);
					Mail::send('email.mobile_cancelMail_customer', $send_mail_data, function($message) use($send_mail_data)
					{	
						
						$subject = MobileModel::get_lang_text($send_mail_data['lang'],'API_CANCELLED_ORDER_DETAILS','Canceled order details!');
						$message->to($send_mail_data['customerMail'])->subject($subject);
					});
					//2) MAIL TO ADMIN
					$adminMail = $this->general_setting->gs_email;
					$send_mail_data = array('order_details'	=> $storeDetArray,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id,'lang'=>$lang.'_mob_lang','onlyLang'=>$lang,'adminMail'=>$adminMail);
					Mail::send('email.mobile_cancelMail_admin', $send_mail_data, function($message) use($send_mail_data)
					{	
						$subject = MobileModel::get_lang_text($send_mail_data['lang'],'API_CANCELLED_ORDER_DETAILS','Canceled order details!');
						$message->to($send_mail_data['adminMail'])->subject($subject);
					});
					//3) MAIL TO MERCHANT
					if(count($storeDetArray) > 0 )
					{
						foreach($storeDetArray as $key=>$itemsDet)
						{
							$explodeRes 	= explode('`',$key);
							$merchantEmail 	= $explodeRes[1];
							
							$send_mail_data = array('order_details'	=> $itemsDet,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id,'storename'=>$explodeRes[0],'lang'=>$lang.'_mob_lang','onlyLang'=>$lang,'merchantEmail'=>$merchantEmail);
							Mail::send('email.mobile_cancelMail_merchant', $send_mail_data, function($message) use($send_mail_data)
							{	
								$subject = MobileModel::get_lang_text($send_mail_data['lang'],'API_CANCELLED_ORDER_DETAILS','Canceled order details!');
								$message->to($send_mail_data['merchantEmail'])->subject($subject);
							});
						}
						
					}
					/** --------------- EOF MAIL FUNCTION **/
				}
				$message= MobileModel::get_lang_text($send_mail_data['lang'],'API_ORDER_CANCELLED_SUXS','Order has been canceled succesfully!');
				$encode = ['code' => 200,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}	
			else {
				$message= MobileModel::get_lang_text($send_mail_data['lang'],'API_OR_ID_NT_EXIST','Order id does not exist');
				$encode = ['code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
			}
		}
		
		/* order route map tracking */
		public function order_tracking(Request $request)
		{
			$lang 		= $request->lang;
			$order_id 	= $request->order_id;
			$store_id 	= $request->store_id;
			$cus_id	= JWTAuth::user()->cus_id;
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');
			$stId_req_err_msg		= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store id!');
			$stId_valid_err_msg		= MobileModel::get_lang_text($lang,'API_ST_ID_NT_EXIST','Store id not exist');
			$validator = Validator::make($request->all(),['order_id' => ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function($query) use($cus_id){ $query->where('ord_cus_id','=',$cus_id);})
			],
			'store_id'=>  ['required',
			Rule::exists('gr_order','ord_rest_id')->where(function($query) use($order_id){ $query->where('ord_transaction_id','=',$order_id);})
			]
			],
			['order_id.required'=> $orderId_req_err_msg,
			'order_id.exists'	=> $orderId_valid_err_msg,
			'store_id.required'=> $stId_req_err_msg,
			'store_id.exists'	=> 	$stId_valid_err_msg
			]
			);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				$encode  = [ 'code' => 400,'message'  => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			
			$del_details = $or_details = array();
			$final_status = $final_id = '';
			$get_or_status = MobileModel::get_or_status($cus_id,$order_id,$store_id,$lang,$this->admin_default_lang);
			//print_r($get_or_status); exit;
			$otp = '';
			$fail_reason = '';
			$st_lat = $st_long = $st_name = $est = '';
			$cus_details = array();
			$est_from = $est_to = '';
			
			if(count($get_or_status)>0)
			{	
				$st_lat = $get_or_status[0]->st_latitude;
				$st_long = $get_or_status[0]->st_longitude;
				$st_name = $get_or_status[0]->store_name;
				$cus_details = ['cus_name' 		=> $get_or_status[0]->ord_shipping_cus_name,
				'cus_address' 	=> $get_or_status[0]->ord_shipping_address,
				'cus_address1' 	=> $get_or_status[0]->ord_shipping_address1,
				'cus_phone' 	=> $get_or_status[0]->ord_shipping_mobile,
				'cus_latitude' 	=> $get_or_status[0]->order_ship_latitude,
				'cus_longitude' => $get_or_status[0]->order_ship_longitude,
				];
				$cancel_array = $status_array = array();
				$final_status = $get_or_status[0]->ord_status;
				$final_id	  = $get_or_status[0]->ord_id;

				foreach($get_or_status as $or)
				{
					array_push($cancel_array,$or->ord_cancel_status);
					array_push($status_array,$or->ord_status);
					if($or->ord_cancel_status == 0)
					{
						$final_status = $or->ord_status;
						$final_id	  = $or->ord_id;
					}
					$otp = $or->ord_otp;
				}
				$get_status = MobileModel::get_or_status($cus_id,$order_id,$store_id,$lang,$this->admin_default_lang,$final_id);
				if(!in_array('0',$cancel_array))	/* all items cancelled by merchant */
				{						
					$or_details[] = ['ord_stage'	=> 3,
									'ord_title'	=> MobileModel::get_lang_text($lang,'API_ORDER_CANCELLED','Order Cancelled!'),
									'ord_timing'	=> ($get_status->ord_rejected_on !='') ? $get_status->ord_rejected_on : '',
									'stage_completed' => 'Yes'
									];
				}
				/* all items cancelled by user */
				/*elseif($final_status == '9')	
				{	
					$or_details[] = ['ord_stage'	=> 9,
									'ord_title'	=> MobileModel::get_lang_text($lang,'API_FAIL','Delivery failed!'),
									'ord_timing'	=> ($get_status->ord_failed_on !='') ? $get_status->ord_failed_on : '',
									'stage_completed' => 'Yes'
									
									];
				}*/
				else
				{	 
					
					$min =  MobileModel::get_lang_text($lang,'API_MIN','mins');
					$hr =  MobileModel::get_lang_text($lang,'API_HR','hrs');
					if(empty($get_status) === false)
					{		
						$add_min = $get_status->ord_estimated_arrival_mins + 10;
						$fail_reason = $get_status->ord_failed_reason;
						if($add_min >= 60)
						{
							$buff_min = $add_min % 60;
							$buff_hrs = $get_status->ord_estimated_arrival_hrs + 1;
						}
						else
						{
							$buff_min = $add_min;
							$buff_hrs = $get_status->ord_estimated_arrival_hrs;
						}	
						
						if($get_status->ord_estimated_arrival_hrs != 0) 				
						{
							$est_from .= $get_status->ord_estimated_arrival_hrs.' '.$hr.' ';
						}
						if($get_status->ord_estimated_arrival_mins != 0)
						{
							$est_from .= $get_status->ord_estimated_arrival_mins.' '.$min;
						}
						if($buff_hrs != 0) 				
						{
							$est_to .= $buff_hrs.' '.$hr.' ';
						}
						if($buff_min != 0)
						{
							$est_to .= $buff_min.' '.$min;
						}
						/*$msg_array = ['1' => 'Order Placed','Order Confirmed','','Order Processed','Order Dispatched','Order Picked','Order Arrived','Order Delivered'];*/
						$msg_array = ['1' => 'New Order','Accepted','','Prepare to Deliver','Dispatched','Started','Arrived','Delivered','Failed'];
						$time_arr = ['1' => 'ord_placed_on','ord_accepted_on','','ord_prepared_on','ord_dispatched_on','ord_started_on','ord_arrived_on','ord_delivered_on','ord_failed_on'];
						//echo $msg_array[1];
						$self_pickup_arr = ['1','2','8'];
						//echo $get_status->ord_self_pickup; exit;
						for($i=1;$i<=9;$i++)
						{ 
							$stage = "Yes";
							if(($get_status->ord_status == '9' && $i == '8') || ($get_status->ord_status < $i))
							{
								$stage = "No";
							}

							if($get_status->ord_self_pickup == '1')
							{
								if((in_array($i,$self_pickup_arr)))
								{
									$st = $time_arr[$i];
									$or_details[] = ['ord_stage'	=> $i,
												'ord_title'	=> MobileModel::get_lang_text($lang,'API_OR_ST'.$i,$msg_array[$i]),
												'ord_timing'	=> ($get_status->$st !='') ? $get_status->$st : '',
												'stage_completed' => $stage
												];
								}
								
							}
							else
							{ 
								if($i != 3)
								{	
									$st = $time_arr[$i];
									$or_details[] = ['ord_stage'	=> $i,
													'ord_title'	=> MobileModel::get_lang_text($lang,'API_OR_ST'.$i,$msg_array[$i]),
													'ord_timing'	=> ($get_status->$st !='') ? $get_status->$st : '',
													'stage_completed' => $stage
													];
								}
							}
							
						}
						//exit;
					}
				}
			}
			
			$de_details = User::get_delboy_details($order_id,$store_id);
			if(empty($de_details) === true)
			{
				$del_details = ['deliver_assigned' => 'No'];
			}
			else
			{	
				$avatar = url('public/images/noimage/default_user_image.jpg');
				if($de_details->deliver_profile_image != '')
				{
					$filename = public_path('images/delivery_person/').$de_details->deliver_profile_image;
					
					if(file_exists($filename)){
						$avatar = url('public/images/delivery_person/'.$de_details->deliver_profile_image );
					}
				}
				$del_details = ['deliver_assigned' 	=> 'Yes',
				'deliver_id'		=> $de_details->ord_delivery_memid,
				'deliver_name'		=> $de_details->del_name,
				'deliver_mobile'	=> $de_details->deliver_phone1,
				'deliver_image'		=> $avatar, 
				
				'deliver_latitude'	=>$de_details->deliver_latitude,
				'deliver_longitude'	=>$de_details->deliver_longitude];				
			}
			$msge	= MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
			$encode = ['code'=>200,"message"=>$msge,
			'data'	=> ["order_id"	=> $order_id,
						"order_otp"	=> $otp,
						"order_failed_reason" => $fail_reason,
						"order_status_details"	=> $or_details,
						"delivery_person_details"	=> $del_details,
						"estimated_arrival_time"	=> $est_from.' - '.$est_to,
						"restaurant_details"	=> ['restaurant_name' =>$st_name,
						'restaurant_latitude' => $st_lat,
						'restaurant_longitude' => $st_long],
						"customer_details"		=> $cus_details
						]	
			];
			
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* get payment methods */
		public function payment_methods(Request $request)
		{
			$lang = $request->lang;
			$payment_details = MobileModel::get_pay_method();
			if(empty($payment_details) === true)
			{
				$text 	= MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details not available');
				$encode = [ 'code' => 400,'message' => $text,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$pay_array = ['cod'		=> $payment_details->cod_status,
				'paypal'	=> $payment_details->paymaya_status,
				'stripe'	=> $payment_details->paynamics_status];
				$text 	= MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details available');
				$encode = array('code' => 200,'message' => $text,
				'data'  => ['payment_methods'	=> $pay_array]);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* COD checkout */
		public function checkout(Request $request)
		{ 	
			$lang 			  	= $request->lang;
			$order_self_pickup 	= $request->ord_self_pickup;
			$available_wallet 	= JWTAuth::user()->cus_wallet;
			$used_wallet 		= JWTAuth::user()->used_wallet;
			//$validator = array();
			if($order_self_pickup == 0)
			{ 
				$validator = Validator::make($request->all(),
				[
				'cus_name' 		=> 'required',
				'cus_last_name'	=> 'required',
				'cus_email'	 	=> 'required',
				'cus_phone1' 	=> 'required|only_cnty_code',
				//'cus_phone2' 	=>'required|only_cnty_code',
				'cus_lat' 	 	=>'required',
				'cus_long' 	 	=>'required',
				'cus_address'	=>'required',
				'cus_address1'	=>'Sometimes',
				'use_wallet'	=>'required',
				'wallet_amt'	=>'required_if:use_wallet,1'
				],
				['cus_name.required' 	=> MobileModel::get_lang_text($lang,'API_ENTER_FNAME','Please Enter Name'),
				'cus_last_name.required'=> MobileModel::get_lang_text($lang,'API_ENTER_LNAME','Please Enter lastname'),
				'cus_email.required' 	=> MobileModel::get_lang_text($lang,'API_ENTER_EMAIL','Please Enter a valid email address'),
				'cus_phone1.required' 	=> MobileModel::get_lang_text($lang,'API_ENTER_PHONE','Please Enter Valid Phone Number'),
				//'cus_phone2.required' 	=> MobileModel::get_lang_text($lang,'API_ENTER_PHONE','Please Enter Valid Phone Number'),
				'cus_lat.required' 		=> MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!'),
				'cus_long.required' 	=> MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!'),
				'cus_address.required' 	=> MobileModel::get_lang_text($lang,'API_ENTER_ADDR','Enter address'),
				'use_wallet.required' 	=> MobileModel::get_lang_text($lang,'API_SL_WALLET_ST','Please select use wallet status'),
				'wallet_amt.required_if' 	=> MobileModel::get_lang_text($lang,'API_EN_WALLET_AMT','Please enter wallet amount')
				]); 
				if($validator->fails()) 
				{
					$message  = $validator->messages()->first();
					$encode   = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				} 
				
			}	
			
			$cus_id		= JWTAuth::user()->cus_id;
			/* update product price*/
			update_cart_price($cus_id);
			$profile_address = DB::table('gr_customer')->select('cus_fname','cus_lname','cus_email','cus_phone1','cus_phone2','cus_address','cus_latitude','cus_longitude')->where('cus_id',$cus_id)->first();
			$name  		= ($request->cus_name == '' )	? $profile_address->cus_fname 	: $request->cus_name ;
			$lname  	= ($request->cus_last_name == '' ) 	? $profile_address->cus_lname 	: $request->cus_last_name ;
			$mail  		= ($request->cus_email == '') 	? $profile_address->cus_email 	: $request->cus_email;
			$phone1 	= ($request->cus_phone1 == '') 	? $profile_address->cus_phone1 	: $request->cus_phone1;
			$phone2  	= ($request->cus_phone2 == '') 	? $profile_address->cus_phone2 	: $request->cus_phone2;
			$address  	= ($request->cus_address == '') ? $profile_address->cus_address : $request->cus_address;
			$latitude 	= ($request->cus_lat == '') 	? $profile_address->cus_latitude : $request->cus_lat;
			$longitude  = ($request->cus_long == '') 	? $profile_address->cus_longitude : $request->cus_long;
			$wallet_used  = $request->use_wallet;	
			/* check self pickup availablity */
			if($order_self_pickup == '1' && $this->general_setting->self_pickup_status == 0)
			{
				$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_SELF_PICK_NT_AVAIL','Self pickup not available!'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
			/*  check minimum order and pre order for added items */
			$cart_det = MobileModel::chk_minOr_preOr($cus_id,$lang,$this->admin_default_lang);
			$grant_cart_total = 0;
			if(count($cart_det) > 0)
			{
				foreach($cart_det as $det)
				{	
					$st_name  = $det->st_name;
					if($det->st_minimum_order > $det->store_total)	/* below minimum order */
					{
						$bal 		 = $det->cart_currency.' '.number_format($det->st_minimum_order - $det->store_total,2);
						$got_message = MobileModel::get_lang_text($lang,'API_BELOW_MIN_OR',':bal is remaining to reach minimum order amount in :store');
						$searchReplaceArray = array(':bal' => $bal,':store' => $st_name);
						$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
						$encode = ['code' => 400,'message' => $result,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
					}
					elseif($det->store_closed == "Closed" && $det->cart_pre_order == '' && $det->st_pre_order == 1 && $det->st_type == '1') /* restaurant closed. pre order mandatory */
					{
						$got_message = MobileModel::get_lang_text($lang,'API_SL_PRE_OR',':store is closed.Choose pre order date to purchase');
						$searchReplaceArray = array(':store' => $st_name);
						$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
						$encode = ['code' => 400,'message' => $result,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
					}
					elseif($det->store_closed == "Closed" && $det->cart_pre_order == '' && $det->st_pre_order == 0 && $det->st_type == '1') /* restaurant closed and pre order not avail for that */
					{
						$got_message = MobileModel::get_lang_text($lang,'API_SL_CLOSE',':store is closed.You can\'t purchase in this shop');
						$searchReplaceArray = array(':store' => $st_name);
						$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
						$encode = ['code' => 400,'message' => $result,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
					}
					$grant_cart_total += $det->store_total;
				}
			}
			else
			{
				$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_CART_NO_ITEM','No items in cart'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
			}
			
			$total = $wallet_amt = 0;
			$merchantIdArray = array();
			$transaction_id = $pay_type = '';
			$insertArr = array();
			/** calculate wallet **/
			if($wallet_used == 1)
			{
				$wallet_amt = $request->wallet_amt; 
				$bal = round(($available_wallet - $used_wallet),2);
				if($bal < $wallet_amt)
				{
					$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_INV_AMT','Invalid Amount.Your wallet balance is ').$bal,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
				}
				
			}
			
			$route = \Request::segment(3);
			/* assign payment type */
			if($route == "cod_checkout")
			{
				$transaction_id = "COD-".rand();
				$pay_type 		= "COD";
			}
			elseif($route == "paypal_checkout")
			{	
				//$transaction_id ='Paypal'.rand();
				$transaction_id =$request->transaction_id;
				$pay_type 		= "PAYPAL";
				$transaxnid_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_TRANSAXN_ID','Please enter transaction ID');
				/*$paytype_req_err_msg 	= MobileModel::get_lang_text($lang,'API_ENTER_PAYTYPE','Please enter pay type');
					$pmtId_valid_err_msg 	= MobileModel::get_lang_text($lang,'API_ENTER_PAYMENT_ID','Please enter payment ID');
					$rcptNum_valid_err_msg 	= MobileModel::get_lang_text($lang,'API_ENTER_RECEIPT_NUM','Please enter receipt number');
					$paidTime_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAID_TIME','Please enter paid time');
					$last4_valid_err_msg 	= MobileModel::get_lang_text($lang,'API_ENTER_LAST4','Please enter last 4');
					$cardType_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_CARD_TYPE','Please enter card type');
				$masked_valid_err_msg 	= MobileModel::get_lang_text($lang,'API_ENTER_MASKED_CARD','Please enter masked card'); */
				$validator = Validator::make($request->all(),['transaction_id' 		=> 'required'
				//'payment_receipt_no' 	=> 'required',
				// 'paid_time'			=> 'required',
				// 'card_last4_digits'	=> 'required',
				// 'card_type'			=> 'required',
				// 'masked_card'			=> 'required'
				],
				['transaction_id.required'		=> $transaxnid_req_err_msg
				// 'payment_receipt_no.required'	=> $rcptNum_valid_err_msg,
				// 'paid_time.required'			=> $paidTime_valid_err_msg,
				// 'card_last4_digits.required'	=> $last4_valid_err_msg,
				// 'card_type.required'			=> $cardType_valid_err_msg,
				// 'masked_card.required'			=> $masked_valid_err_msg
				]
				);
				if($validator->fails())
				{
					$message= $validator->messages()->first();
					$encode	= [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			elseif($route == "stripe_checkout")
			{
				$transaction_id ="STRIPE-".rand();
				$pay_type 		= "STRIPE";
				$transaxnid_req_err_msg = MobileModel::get_lang_text($lang,'API_FILL_CARD_NO','Enter Card No');
				$paytype_req_err_msg 	= MobileModel::get_lang_text($lang,'API_FILL_EX_MNTH','Enter Card Expiry Month');
				$pmtId_valid_err_msg 	= MobileModel::get_lang_text($lang,'API_FILL_EX_YR','Enter Card Expiry Year');
				$rcptNum_valid_err_msg 	= MobileModel::get_lang_text($lang,'API_FILL_CVV','Enter CVV Number');
				
				$validator = Validator::make($request->all(),['card_no' 			=> 'required',
				'ccExpiryMonth' 		=> 'required',
				'ccExpiryYear'		=> 'required',
				'cvvNumber'			=> 'required'],
				['card_no.required'		=> $transaxnid_req_err_msg,
				'ccExpiryMonth.required'	=> $paytype_req_err_msg,
				'ccExpiryYear.required'	=> $pmtId_valid_err_msg,
				'cvvNumber.required'		=> $rcptNum_valid_err_msg
				]
				);
				if($validator->fails())
				{
					$message= $validator->messages()->first();
					$encode	= [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			elseif($route == "wallet_checkout")
			{
				$transaction_id ='WALLET'.rand();
				$pay_type 		= "WALLET";
			}
			$get_cart_details = MobileModel::get_cart_details($cus_id,'',$lang,$this->admin_default_lang);
			$del_fee = "0.00";
			$shippingDet = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id','=',$cus_id)->first();
			if($this->general_setting->gs_delivery_fee_status=='1' && $this->general_setting->gs_del_fee_type == 'common_fee')
			{
				$del_fee = $this->general_setting->gs_delivery_fee;
			}
			
			if(count($get_cart_details)  > 0 )
			{	
				/* calculate kilometer based delivery fee */
				if($this->general_setting->gs_delivery_fee_status=='1' && $this->general_setting->gs_del_fee_type == 'km_fee' && !empty($shippingDet))
				{	
					$user_lat = $shippingDet->sh_latitude;
					$user_long= $shippingDet->sh_longitude;
					foreach($get_cart_details as $key => $value)
					{	
						$key_values = explode('~`',$key);
						$kilometer = calculate_distance($user_lat,$user_long,$key_values[9],$key_values[10]);
						//echo $kilometer[0]->distance;
						$st_del_fee = $this->general_setting->gs_km_fee * $kilometer[0]->distance;
						$del_fee+=$st_del_fee;
					}
					
				}
				//echo $del_fee; exit;
				if($route == "stripe_checkout")
				{
					$getAdminCurrency = $this->default_currency_code;
					$stripedetails=DB::table('gr_payment_setting')->select('paynamics_secret_id','paynamics_client_id')->first();
					$Paymentgatewayamount = $del_fee + $grant_cart_total - $wallet_amt ; 
					$Paymentgatewayamount=round($Paymentgatewayamount);
					$amt_to_pay = $Paymentgatewayamount;
					if($getAdminCurrency!='USD'){
						$amt_to_pay = convertCurrency($getAdminCurrency,'USD',$Paymentgatewayamount);
					}
					//echo $amt_to_pay; exit;
					require_once('./stripe/lib/Stripe.php');
					$secret_key = $stripedetails->paynamics_secret_id;
					$publishable_key = $stripedetails->paynamics_client_id;
					$stripe = array("secret_key" => $secret_key, "publishable_key" => $publishable_key);
					Stripe::setApiKey($secret_key);
					//$token = Input::get('stripeToken');
					
					try 
					{
						$result = Stripe_Token::create(
								array(
									"card" => array(
										"name" =>$cus_fname,
										"number" => $request->card_no,
										"exp_month" => $request->ccExpiryMonth,
										"exp_year" => $request->ccExpiryYear,
										"cvc" => $request->cvvNumber
									)
								)
							);
						$token = $result['id'];

						$results = Stripe_Charge::create ( array (
                        "amount" => round($amt_to_pay*100),   //Stripe payment only accept round integer 
                        "currency" => "USD",
                        "source" => $token, // obtained with Stripe.js
                        "description" => "Order Checkout." 
						));
						$transaction_id = $results->id;
					}
					catch ( \Exception $e ) 
					{	
						$body = $e->getJsonBody();
						$err  = $body['error'];
						if($err['code'] == 'parameter_invalid_integer')
						{
							$message  =MobileModel::get_lang_text($lang,'STRIPE_ERR','Your payment amount is too low.Please add some items or choose another payment options');
						}
						else
						{
							$message  =$err['message'];
						}
						$encode	= [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
					}
				}
				foreach($get_cart_details as $key=>$value)
				{	
					$overall_amt_withtax = $overall_admin_amt = 0;
					
					foreach($value as $pdtDetail)
					{	
						/* product cart*/
						if($pdtDetail->cart_type=='1')	
						{	
							array_push($merchantIdArray,$pdtDetail->mer_id);
							$commission = (($pdtDetail->cart_total_amt * $pdtDetail->mer_commission) / 100);
							$sub_total = ($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity);
							/** calculate overall merchant amt **/
							$overall_amt_withtax +=	$pdtDetail->cart_total_amt;
							$overall_admin_amt +=	$commission;
							/** calculate overall merchant amt ends **/
							$insertArr = ['ord_cus_id' 				=> $cus_id,
							'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
							'ord_shipping_address'  	=> $address,
							'ord_shipping_address1'  	=> $request->cus_address1,
							'ord_shipping_mobile'	  	=> $phone1,
							'ord_shipping_mobile1'	=> $phone2,
							'order_ship_mail'			=> $mail,
							'order_ship_latitude'		=> $latitude,
							'order_ship_longitude'	=> $longitude,
							'ord_merchant_id'			=> $pdtDetail->mer_id,
							'ord_rest_id'				=> $pdtDetail->pro_store_id,
							'ord_pro_id'				=> $pdtDetail->pro_id,
							'ord_had_choices'			=> "No",
							'ord_choices'				=> '',
							'ord_choice_amount'		=> 0,
							'ord_spl_req'				=> $pdtDetail->cart_spl_req,
							'ord_quantity'			=> $pdtDetail->cart_quantity,
							'ord_currency'			=> $pdtDetail->cart_currency,
							'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
							'ord_sub_total'			=> $sub_total,
							'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
							'ord_has_coupon'			=> "No",
							'ord_coupon_amt'			=> 0,
							'ord_delivery_fee'		=> ($order_self_pickup == '1') ? "0.00" : $del_fee,
							'ord_grant_total'		=> $pdtDetail->cart_total_amt,
							'ord_refund_status'		=> $pdtDetail->refund_status,
							'ord_mer_cancel_status'	=> $pdtDetail->cancel_status,
							'ord_wallet'				=> $wallet_amt,
							'ord_type'				=> "Product",
							'ord_pay_type'			=> $pay_type,
							'ord_transaction_id'		=> $transaction_id,
							'ord_payment_status'		=> "Success",
							'ord_status'				=> 1,
							'ord_date'				=> date('Y-m-d H:i:s'),
							'ord_admin_amt'			=> $commission,
							'ord_self_pickup'			=> $order_self_pickup,
							'ord_task_status'			=> '0',
							'ord_placed_on'			=> date('Y-m-d H:i:s')
							];
							/*if($route == "paymaya_checkout")
								{
								array_merge($insertArr,['ord_paymaya_pmtId' 	=> $request->payment_id,
								'ord_paymaya_receiptnum'=> $request->payment_receipt_no,
								'ord_payment_status' 	=> "Success",
								'ord_paymaya_paid_time' => $request->paid_time,
								'ord_paymaya_last4' 	=> $request->card_last4_digits,
								'ord_paymaya_carttype' 	=> $request->card_type,
								'ord_paymaya_maskedcart'=> $request->masked_card]);
							}*/
							$insert = insertvalues('gr_order',$insertArr);
							$total += $sub_total;
							/** delete cart **/
							$delete = deletecart($pdtDetail->cart_id);
							/** update quantity in product table **/
							$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
							
						}
						
						/* item cart */
						elseif($pdtDetail->cart_type=='2') 	
						{	
							array_push($merchantIdArray,$pdtDetail->mer_id);
							$choices = json_decode($pdtDetail->cart_choices_id,true);
							$ch_array = array();
							$ch_price = 0;
							if(count($choices) > 0)
							{
								foreach($choices as $ch)
								{
									$ch_array[] = ['choice_id'		=> $ch['choice_id'],
									'choice_price'	=> ($ch['choice_price'] * $pdtDetail->cart_quantity)
									];
									$ch_price += ($ch['choice_price'] * $pdtDetail->cart_quantity);
								}
							}
							$sub_total 	= (($pdtDetail->cart_unit_amt * $pdtDetail->cart_quantity) + $ch_price);
							$total_amt 	= $sub_total + $pdtDetail->cart_tax;
							$commission = (($total_amt * $pdtDetail->mer_commission) / 100);
							//echo $commission; exit;
							/** calculate overall merchant amt  **/
							$overall_amt_withtax += $total_amt;
							$overall_admin_amt += $commission;
							/** calculate overall merchant amt ends **/
							$insertArr = ['ord_cus_id'				=> $cus_id,
							'ord_shipping_cus_name' 	=> ucfirst($name).' '.$lname,
							'ord_shipping_address'  	=> $address,
							'ord_shipping_mobile'	  	=> $phone1,
							'ord_shipping_mobile1'	=> $phone2,
							'order_ship_mail'			=> $mail,
							'order_ship_latitude'		=> $latitude,
							'order_ship_longitude'	=> $longitude,
							'ord_merchant_id'			=> $pdtDetail->mer_id,
							'ord_rest_id'				=> $pdtDetail->pro_store_id,
							'ord_pro_id'				=> $pdtDetail->pro_id,
							'ord_had_choices'			=> $pdtDetail->cart_had_choice,
							'ord_choices'				=> json_encode($ch_array),
							'ord_choice_amount'		=> $ch_price,
							'ord_spl_req'				=> $pdtDetail->cart_spl_req,
							'ord_quantity'			=> $pdtDetail->cart_quantity,
							'ord_unit_price'			=> $pdtDetail->cart_unit_amt,
							'ord_currency'			=> $pdtDetail->cart_currency,
							'ord_sub_total'			=> $sub_total,
							'ord_tax_amt'				=> ($pdtDetail->cart_tax != '') ? $pdtDetail->cart_tax : 0,
							'ord_has_coupon'			=> "No",
							'ord_coupon_amt'			=> 0,
							'ord_wallet'				=> $wallet_amt,
							'ord_delivery_fee'		=> ($order_self_pickup == '1') ? "0.00" : $del_fee,
							'ord_grant_total'			=> $total_amt,
							'ord_refund_status'		=> $pdtDetail->refund_status,
							'ord_mer_cancel_status'	=> $pdtDetail->cancel_status,
							'ord_type'				=> "Item",
							'ord_pay_type'			=> $pay_type,
							'ord_transaction_id'		=> $transaction_id,
							'ord_pre_order_date'		=> $pdtDetail->cart_pre_order,
							'ord_payment_status'		=> "Success",
							'ord_status'				=> 1,
							'ord_date'				=> date('Y-m-d H:i:s'),
							'ord_admin_amt'			=> $commission,
							'ord_self_pickup'			=> $order_self_pickup,
							'ord_task_status'			=> '0',
							'ord_placed_on'			=> date('Y-m-d H:i:s')
							];
							/*if($route == "paymaya_checkout")
								{
								array_merge($insertArr,['ord_paymaya_pmtId' 	=> $request->payment_id,
								'ord_paymaya_receiptnum'=> $request->payment_receipt_no,
								'ord_payment_status' 	=> $request->payment_status,
								'ord_paymaya_paid_time' => $request->paid_time,
								'ord_paymaya_last4' 	=> $request->card_last4_digits,
								'ord_paymaya_carttype' 	=> $request->card_type,
								'ord_paymaya_maskedcart'=> $request->masked_card]);
							}*/
							$insert = insertvalues('gr_order',$insertArr);
							$total += $sub_total;
							/** delete cart **/
							$delete = deletecart($pdtDetail->cart_id);
							/** update quantity in product table **/
							$update = update_quantity(($pdtDetail->pro_no_of_purchase + $pdtDetail->cart_quantity),$pdtDetail->pro_id);
						}
						
						
						
					}
					/** Calculate merchant amount **/
						$get_details = Home::merchant_orderDetails($pdtDetail->mer_id);
						
						if(empty($get_details) === false)		//Update
						{ 
							$order_count = $get_details->or_total_order + 1;
							$wallet_amt1	 = $get_details->or_coupon_amt + $wallet_amt;
							$admin_amt	 = ($get_details->or_admin_amt + $overall_admin_amt);
							$mer_amt	 = $get_details->or_mer_amt + $overall_amt_withtax;
							
							/** update in merchant overall table **/
							$array = [	'or_total_order'=> $order_count,
							'or_admin_amt'	=> $admin_amt,
							'or_coupon_amt' => $wallet_amt1,
							/*'or_mer_amt'	=>	$mer_amt ------ merchant amount will be added after merchant once order */
							];
							
							$update = updatevalues('gr_merchant_overallorder',$array,['or_mer_id' => $pdtDetail->mer_id]);
						}
						else 		//add
						{	
							
							/** insert in merchant overall table **/
							$array = [	'or_mer_id'		=> $pdtDetail->mer_id,
							'or_total_order'=> 1,
							'or_admin_amt'	=> $overall_admin_amt,
							'or_coupon_amt' => $wallet_amt,
							/*'or_mer_amt'	=> $overall_amt_withtax ------ merchant amount will be added after merchant once order*/
							];
							
							$update = insertvalues('gr_merchant_overallorder',$array);
						}
						/** Calculate merchant amount ends  **/
				}
				if($wallet_used == 1)
				{
					/** update wallet amount for customer **/
					$updat = updatevalues('gr_customer',['used_wallet' => DB::Raw('used_wallet+'.$wallet_amt)],['cus_id' => $cus_id]);
				}
				$re_email = JWTAuth::user()->cus_email;
				/** add wallet amount for referrel **/
				$refer_details = Home::refer_status($re_email,"mobile");
				//print_r($refer_details); exit;
				if(empty($refer_details) === false)
				{
					if($refer_details->referral_id != '')
					{	
						/* Update referel wallet */
						$user = get_user(['cus_id' => $refer_details->referral_id,'cus_status' => '1']);
						$offer_amt = (($total * $refer_details->re_offer_percent)/100);
						if(empty($user) === false)
						{	
							$wallet_amt =  $offer_amt + $user->cus_wallet;
							/** update refered customer wallet **/
							DB::table('gr_customer')->where(['cus_id' => $refer_details->referral_id])->update(['cus_wallet' => $wallet_amt]);
							
						}
						/* update first purchase status */
						DB::table('gr_referal')->where(['referre_email' => $re_email,'re_purchased' => '0','re_code_used'=>'1'])->update(['re_purchased' => '1','re_offer_amt' => $offer_amt,'re_order_id' =>$transaction_id,'re_order_date' => date('Y-m-d H:i:s')]);
					}
				}
				/*UPDATING SHIPPING ADDRESS */
				if($order_self_pickup==0)
				{
					$CheckShExists = DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->first();
					if(empty($CheckShExists) === false)
					{
						$gr_shipping_array = ['sh_cus_fname' 	=> ucfirst($name),
						'sh_cus_lname' 	=> $lname,
						'sh_location'  	=> $address,
						'sh_phone1'	  	=> $phone1,
						'sh_phone2'		=> $phone2,
						'sh_cus_email'	=> $mail,
						'sh_latitude'		=> $latitude,
						'sh_longitude'	=> $longitude,
						];
						DB::table('gr_shipping')->where(['sh_cus_id' => $cus_id])->update($gr_shipping_array);
					}
					else
					{
						$gr_shipping_array = ['sh_cus_id'		=> $cus_id,
						'sh_cus_fname' 	=> ucfirst($name),
						'sh_cus_lname' 	=> $lname,
						'sh_location'  	=> $address,
						'sh_phone1'	  	=> $phone1,
						'sh_phone2'		=> $phone2,
						'sh_cus_email'	=> $mail,
						'sh_latitude'		=> $latitude,
						'sh_longitude'	=> $longitude,
						];
						$update = insertvalues('gr_shipping',$gr_shipping_array);
					}
				}
				
				/* EOF UPDATING SHIPPING ADDRESS*/ 
				/* ---------- SEND NOTIFICATION  ----------------*/
				$got_message = MobileModel::get_lang_text($lang,'API_NEW_ORDER_PLACED','New order ( :transaction_id )  placed by :customer_name!');
				$message = str_replace(':transaction_id', $transaction_id, $got_message);
				$message = str_replace(':customer_name', ucfirst($name).' '.$lname, $message);
				//1) to merchant
				$unique_merchant = array_unique($merchantIdArray);
				$message_link = 'mer-order-details/'.base64_encode($transaction_id);
				if(count($unique_merchant) > 0 ){
					foreach($unique_merchant as $uniMerchant){
						$noti_id = push_notification($cus_id,$uniMerchant,'gr_customer','gr_merchant',$message,$transaction_id,$message_link); 
						/* send notification to mobile */
							$mer_details = get_details('gr_merchant',['id'=>$uniMerchant],('mer_andr_fcm_id,mer_ios_fcm_id'));
							if(empty($mer_details) === false)
							{
								
								if($mer_details->mer_andr_fcm_id !='')
								{
									$parse_fcm=json_decode($mer_details->mer_andr_fcm_id,true);
									$reg_id = array();
									if(count($parse_fcm) > 0 )
									{
										foreach($parse_fcm as $parsed)
										{ 
											array_push($reg_id,$parsed['fcm_id']);						
										}
									}
									$json_data = ["registration_ids" => $reg_id,
														 "data" => ["transaction_id" 	=> $transaction_id,
																		'type'			=> 'New order',
																		'notification_id'=> $noti_id,
																		"body" 			=> $message,
																		"title" 		=> "Order Notification"]
														];
											$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_MER);
								}
								if($mer_details->mer_ios_fcm_id !='')
								{
									$parse_fcm=json_decode($mer_details->mer_ios_fcm_id,true);
									$reg_id = array();
									if(count($parse_fcm) > 0 )
									{
										foreach($parse_fcm as $parsed)
										{ 
											array_push($reg_id,$parsed['fcm_id']);						
										}
									}
									$json_data = ["registration_ids" => $reg_id,
															"notification" 	=> ["transaction_id" => $transaction_id,
																				'type'			=> 'New order',
																				'notification_id'=> $noti_id,
																				"body" 			=> $message,
																		"title" 		=> "Order Notification",
																		"sound"				=> "default"]
															];
											$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
								}
								
							}
							/* send notification to mobile ends */
					}
				}
				//2)to admin					
				$message_link = 'admin-track-order/'.base64_encode($transaction_id);
				push_notification($cus_id,$this->admin_id,'gr_customer','gr_admin',$message,$transaction_id,$message_link);
				/** mail function **/
				//echo $lang; exit;
				$get_order_details = MobileModel::get_order_details($transaction_id,$lang,$this->admin_default_lang);  
				$customerDet 	   = MobileModel::get_customer_details($transaction_id);
				//print_r($get_order_details); exit;  
				if(empty($get_order_details) === false)
				{
					$send_mail_data = array('order_details'	=> $get_order_details,
					'customerDet'		=> $customerDet,
					'transaction_id'	=> $transaction_id,
					'self_pickup'		=> $order_self_pickup,
					'lang'				=> $lang
					);
					/** mail to customer **/
					Mail::send('email.mobile_order_mail_customer', $send_mail_data, function($message) use($mail,$lang)
					{	
						$msg = MobileModel::get_lang_text($lang,'API_OR_IS_SUXES','Your order is successful');
						$message->to($mail)->subject($msg);
					});
					/* Mail to admin */
					$admin_mail = Config::get('admin_mail');
					$msg = MobileModel::get_lang_text($lang,'API_RECEIVE_NW_OR','Received New Order');
					Mail::send('email.mobile_order_mail_admin', $send_mail_data, function($message) use($admin_mail,$msg)
					{	
						$message->to($admin_mail)->subject($msg);
					});
					/* Mail to merchant */
					foreach($get_order_details as $key=>$itemsDet)
					{	$explodeRest = explode('`',$key);
						$mer_mail = $explodeRest[1];
						$send_mail_data = array('order_details'		=> $itemsDet,
						'customerDet'		=> $customerDet,
						'transaction_id'	=> $transaction_id,
						'store_name' 		=> $explodeRest[0],
						'self_pickup'		=> $order_self_pickup,
						'lang'				=> $lang
						);
						Mail::send('email.mobile_order_mail_merchant', $send_mail_data, function($message) use($mer_mail,$msg)
						{	
							$message->to($mer_mail)->subject($msg);
						});
					}
				}
				/* eof mail function */ 
				/*---------------------------- order summary --------------------------------------
	             	$order_summary = MobileModel::get_invoice($transaction_id);
	             	$order_detailArray = $shipping_details = array();
	             	$pay_type = '';
	             	if(count($order_summary) > 0)
	             	{
					$shipping_details = ['ship_cus_name' 	=> $order_summary[0]->ord_shipping_cus_name,
					'ship_cus_address'	=> $order_summary[0]->ord_shipping_address,
					'ship_cus_phone1'	=> $order_summary[0]->ord_shipping_mobile,
					'ship_cus_phone2'	=> $order_summary[0]->ord_shipping_mobile1,
					'ship_cus_mail'		=> $order_summary[0]->order_ship_mail,
					];
					if($order_summary[0]->ord_pay_type == "COD")
					{
					$pay_type = MobileModel::get_lang_text($lang,'API_COD','Cash on Delivery');
					}
					elseif($order_summary[0]->ord_pay_type == "PAYNAMICS")
					{
					$pay_type = MobileModel::get_lang_text($lang,'API_PAYNAMICS','Paynamics');
					}
					if($order_summary[0]->ord_pay_type == "PAYMAYA")
					{
					$pay_type = MobileModel::get_lang_text($lang,'API_PAYMAYA','Paymaya');
					}
					if($order_summary[0]->ord_pay_type == "WALLET")
					{
					$pay_type = MobileModel::get_lang_text($lang,'API_WALLET','Wallet');
					}
					$sub_total = $grand_total = $tax_total = $shipping_total = 0;
					foreach($order_summary as $Order_sub)
					{
					
					$ordersArray = array();
					$calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
					$sub_total +=$calc_sub_total;
					$shipping_total =$Order_sub->ord_delivery_fee;
					$ordersArray['store_name'] 	= ucfirst($Order_sub->st_store_name);
					$ordersArray['item_name'] 	= ucfirst($Order_sub->pro_item_name);
					if($Order_sub->ord_spl_req != ''){
					$ordersArray['specialRequest'] = $Order_sub->ord_spl_req;
					}
					else {
					$ordersArray['specialRequest'] = '';
					}
					if($Order_sub->pro_type=='1')
					{
					$ordersArray['pdt_image']	= MobileModel::get_image_product($Order_sub->pro_image);//product
					}
					else { 
					$ordersArray['pdt_image']	= MobileModel::get_image_item($Order_sub->pro_image);
					}
					$ordersArray['ord_quantity'] 	= $Order_sub->ord_quantity;
					$ordersArray['ord_unit_price'] 	= number_format($Order_sub->ord_unit_price,2);
					$ordersArray['ord_tax_amt'] 	= number_format($Order_sub->ord_tax_amt,2);
					$ordersArray['sub_total'] 		= number_format($calc_sub_total,2);
					$ordersArray['ord_currency'] 	= $Order_sub->ord_currency;
					if($Order_sub->ord_pre_order_date != '')
					{
					$ordersArray['pre_order_date'] 	= date('m/d/Y H:i:s',strtotime($Order_sub->ord_pre_order_date));
					}
					else
					{
					$ordersArray['pre_order_date'] = '-';
					}
					
					
					if($Order_sub->ord_had_choices=="Yes"){
					$splitted_choice=json_decode($Order_sub->ord_choices,true);
					if(!empty($splitted_choice))
					{
					foreach($splitted_choice as $choice)
					{
					if(!isset($choices[$choice['choice_id']]))
					{
					$choices_name	=	DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
					$ordersArray[]=array('choicename'=>$choices_name,'choice_amount'=>$choice['choice_price']);
					$sub_total +=$choice['choice_price'];  $grand_total +=$choice['choice_price']; 
					}
					}
					}
					}
					array_push($order_detailArray,$ordersArray);							
					}	
					$selfPickup = $order_summary[0]->ord_self_pickup;
					if($selfPickup == 0)
					{
					$delivery_fee = $order_summary[0]->ord_delivery_fee;
					}
					else{
					$delivery_fee = '0.00';
					}
					$walletAmount = $order_summary[0]->ord_wallet;
					//$ordersArray['sub_total'] = $sub_total;
					
					if($selfPickup == 0) {
					$grand_total_text = $sub_total+$shipping_total - $walletAmount;
					} else {
					$grand_total_text = $sub_total - $walletAmount;
					}
					/*---------------------------- order summary ends ---------------------------------
					
					$encode = ['code' => 200,
					'message' => MobileModel::get_lang_text($lang,'API_OR_PLACED_SUXES','Order placed successful'),
					'data'	=> $this->empty_data
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
	             	}
	             	else
	             	{
					$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'SOM_WRONG','Something went to wrong..'),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type' ,'application/json');
				}*/
				
				$encode = ['code' => 200,
				'message' => MobileModel::get_lang_text($lang,'API_OR_PLACED_SUXES','Order placed successful'),
				'data'	=> $this->empty_data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
			}
			else
			{
				$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_CART_NO_ITEM','No items in cart'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type' ,'application/json');
			}
			
			
		}
		
		/* add and update pre order in cart */
		public function add_pre_order_date(Request $request)
		{
			$lang = $request->lang;
			$user_id 	= JWTAuth::user()->cus_id;
			$pre_date 	= $request->pre_order_date;
			$store_id  	= $request->store_id;
			$date_req 	= MobileModel::get_lang_text($lang,'API_DT_REQ','Pre order date is required');
			$date_invalid = MobileModel::get_lang_text($lang,'API_INVALID_DATE','Invalid date format.Use format as Y-m-d H:i');
			$st_req 	= MobileModel::get_lang_text($lang,'API_ST_ID_REQ','Store id is required');
			$st_invalid = MobileModel::get_lang_text($lang,'API_ST_ID_NT_EXIST','Store id not exist');
			$validator 	= Validator::make($request->all(),
			['pre_order_date'	=> 'required|date_format:Y-m-d H:i',
			'store_id'			=> ['required',
			Rule::exists('gr_cart_save','cart_st_id')->where(function ($query) use($user_id) {
				$query->where('cart_cus_id','=',$user_id);
			})
			]
			],
			['pre_order_date.required' 		=>$date_req,
			'pre_order_date.date_format' 	=> $date_invalid,
			'store_id.required'			=> $st_req,
			'store_id.exists'				=> $st_invalid
			]);
			
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				$encode = array('code' => 400,'message' => $message,'data' => $this->empty_data);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
			}
			else
			{	
				$now = date('Y-m-d H:i')		;
				if($pre_date < $now)
				{	
					$message = MobileModel::get_lang_text($lang,'API_DONT_PRE_TIME','Not able to choose previous time');
					$encode = array('code' => 400,'message' => $message,'data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
				}
				$pre_or_avail = DB::table('gr_store')->select('st_pre_order')->where(['id' => $store_id,'st_status' => '1'])->first();
				if(empty($pre_or_avail) === true)	
				{
					$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!'),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
				}
				else
				{
					if($pre_or_avail->st_pre_order != '1')
					{
						$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_PRE_OR_NT_AVAIL','Pre order not available!'),'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
					}
				}
				$selDay  = date('l',strtotime($pre_date));
				$selTime = date('h:ia',strtotime($pre_date));
				$checkRestExist = DB::table('gr_res_working_hrs')->where('wk_res_id','=',$store_id)->where('wk_date','=',$selDay)->first();
				if(empty($checkRestExist) === false)
				{
					$dbFromTime = $checkRestExist->wk_start_time;
					$dbToTime = $checkRestExist->wk_end_time;
					if(strtotime($dbFromTime) <= strtotime($selTime) && strtotime($dbToTime) >= strtotime($selTime))
					{
						/* update pre order date to all items which are added in cart under given store */	
						updatevalues('gr_cart_save',['cart_pre_order' => date('Y-m-d H:i:s',strtotime($pre_date)),'cart_updated_at' 	=> date('Y-m-d H:i:s')],['cart_st_id' => $store_id,'cart_cus_id' => $user_id]);
						$encode = ['code' => 200,'message' => MobileModel::get_lang_text($lang,'API_PRE_DATE_SUXES','Pre order date updated successfully!'),'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
					}
					else /* pre order available. but restaurant closed */
					{
						$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_RES_CLOSED','Restaurant closed!. Please select another time'),'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
					}
				}
				else
				{
					$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_PRE_OR_NT_AVAIL','Pre order not available!'),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
				}
				
			}
		}
		
		/* remove pre order */
		public function remove_pre_order(Request $request)
		{
			$lang 		= $request->lang;
			$user_id 	= JWTAuth::user()->cus_id;
			$store_id  	= $request->store_id;
			$st_req 	= MobileModel::get_lang_text($lang,'API_ST_ID_REQ','Store id is required');
			$st_invalid = MobileModel::get_lang_text($lang,'API_ST_ID_NT_EXIST','Store id not exist');
			$validator 	= Validator::make($request->all(),
			['store_id'			=> ['required',
			Rule::exists('gr_cart_save','cart_st_id')->where(function ($query) use($user_id) {
				$query->where('cart_cus_id','=',$user_id);
			})
			]
			],
			['store_id.required'			=> $st_req,
			'store_id.exists'				=> $st_invalid
			]);
			
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				$encode = array('code' => 400,'message' => $message,'data' => $this->empty_data);
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
			}
			else
			{
				/* update pre order date to all items which are added in cart under given store */	
				updatevalues('gr_cart_save',['cart_pre_order' => '','cart_updated_at' 	=> date('Y-m-d H:i:s')],['cart_st_id' => $store_id,'cart_cus_id' => $user_id]);
				$encode = ['code' => 200,'message' => MobileModel::get_lang_text($lang,'API_PRE_DATE_SUXES','Pre order date updated successfully!'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
			}
		}
		
		/* check wallet */
		public function use_wallet_selfOrder(Request $request)
		{
			$lang = $request->lang;
			$cus_id = JWTAuth::user()->cus_id;
			$available_wallet = JWTAuth::user()->cus_wallet;
			$used_wallet = JWTAuth::user()->used_wallet;
			$self_pickup = $request->ord_self_pickup; //0- normal order, 1-self pickup
			$use_wallet = $request->use_wallet;
			$wallet_amt = $request->wallet_amt;
			$del_fee    = $request->delivery_fee;
			$validator = Validator::make($request->all(),
			['use_wallet'	=>'required',
			'ord_self_pickup' => 'required',
			'wallet_amt'	=>'required_if:use_wallet,1',
			'delivery_fee'	=> 'required'
			],
			['use_wallet.required' 	=> MobileModel::get_lang_text($lang,'API_SL_WALLET_ST','Please select use wallet status'),
			'wallet_amt.required_if' 	=> MobileModel::get_lang_text($lang,'API_EN_WALLET_AMT','Please enter wallet amount'),
			'delivery_fee.required' 	=> MobileModel::get_lang_text($lang,'API_EN_DEL_FEE','Enter delivery Fee'),
			'ord_self_pickup.required' 	=> MobileModel::get_lang_text($lang,'API_SELF_PICK_ST_REQ','Self pickup status is required!')
			,
			]);
			/* update product price*/
			update_cart_price($cus_id);
			$total  = DB::table('gr_cart_save')->select(DB::raw('SUM(gr_cart_save.cart_total_amt) As amount'),'cart_currency')
			->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
			->where(['gr_cart_save.cart_cus_id' => $cus_id,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
			->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
			->get();
			if(count($total) <= 0 )
			{
				$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_CART_NO_ITEM','No items in cart!'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
			}
			else
			{
				//$del_fee = "0.00";
				if($self_pickup == '1' && $this->general_setting->self_pickup_status == 0)
				{
					$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_SELF_PICK_NT_AVAIL','Self pickup not available!'),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
				}
				/*if($this->general_setting->gs_delivery_fee_status == '1' && $self_pickup == '0')
					{
					$del_fee = $this->general_setting->gs_delivery_fee;
				}*/
				$bal = round(($available_wallet - $used_wallet),2);
				if(($use_wallet == 1) && $bal != $wallet_amt)
				{
				 	$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_INV_AMT','Invalid Amount.Your wallet balance is ').$bal,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
				}
				
				$payable = $total[0]->amount+$del_fee;
				$curr = $total[0]->cart_currency;
				$diff = $payable - $wallet_amt; 
//200 - 1800 = -1600
				if($diff > 0)
				{
					$payable_amt_usd = $diff;
					if($this->default_currency_code!='USD'){
						$payable_amt_usd = convertCurrency($this->default_currency_code, 'USD', $diff);
					}
					$wallet_used_amount = $wallet_amt;
					$encode = ['code' 		=> 200,
					'message' 	=> MobileModel::get_lang_text($lang,'API_SL_PAY_MODE','Select anyone of payment mode'),
					'data' => ['currency_code'	=> $curr,
								'payable_amount' => number_format($diff,2),
								'payable_amt_usd' => $payable_amt_usd,
								'used_wallet'	=> $wallet_used_amount
								]
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
				}
				else
				{
					$wallet_used_amount =  $payable;
					$encode = ['code' 		=> 200,
					'message' 		=> MobileModel::get_lang_text($lang,'API_NO_NEED_PAY','No need to pay'),
					'data'		 => ['currency_code' => $curr,
									'payable_amount' => "0.00",
									'payable_amt_usd' => "0.00",
									'used_wallet'	 => $wallet_used_amount
									]
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
				}
			}
		}
		
		/* my wallet */
		public function my_wallet(Request $request)
		{
			$lang = $request->lang;
			$results = JWTAuth::user();
			$cus_id = $results->cus_id;
			$page_no = ($request->page_no == '') ? 1 : $request->page_no;
			$details = MobileModel::wallet_details($cus_id,$page_no);
			$ref_details = MobileModel::refered_user_details($cus_id,$page_no);
			//print_r($details); 
			$available_wallet = round(($results->cus_wallet - $results->used_wallet),2);
			$data = array();
			$msg = MobileModel::get_lang_text($lang,'API_REFER_FR_GET_WALL','Refer Your Friends to Get Wallet!');
			if(count($ref_details) > 0)
			{
				foreach($ref_details as $ref)
				{
					$data[] = ['order_id' 		=> $ref->referre_email,
								'order_date' 	=> ($ref->re_offer_percent != '') ? $ref->re_offer_percent : 0,
								'used_amount' 	=> ($ref->re_offer_amt != '') ? $ref->re_offer_amt : "",
								'ord_currency'	=> '',
								'type'		 	=> 'REFERRAL_HISTORY'
								];
				}
				$msg = MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
			}
			if(count($details) > 0)
			{	
				foreach($details as $det)
				{
					$data[] = ['order_id' => $det->ord_transaction_id,
								'order_date' => $det->ord_date,
								'ord_currency' => $det->ord_currency,
								'used_amount'=> $det->ord_wallet,
								'type'		 => 'WALLET_HISTORY'
								];
				}
			}
			
			
			$encode = ['code' => 200,'message' => $msg,
						'data' => ['currency_code'		  	=> $this->default_curr,
									'available_balance'  	=> $available_wallet,
									'used_details' 			=> $data  
									]
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
		}
		
		/*  show refund details */
		public function refund_details(Request $request)
		{
			$lang 		 = $request->lang;
			$order_id 	 = $request->order_id;
			$cus_id 	 = JWTAuth::user()->cus_id;
			
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');
			$validator = Validator::make($request->all(),['order_id' => ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function($query) use($cus_id){ $query->where('ord_cus_id','=',$cus_id);})
			]
			],
			[
			'order_id.required' => $orderId_req_err_msg,
			'order_id.exists'	=> $orderId_valid_err_msg
			]);
			if($validator->fails())
			{
				$encode = ['code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
			}
			$get_details = MobileModel::get_refund($order_id,$lang,$this->admin_default_lang);
			if(count($get_details) <= 0)
			{
				$encode = ['code' => 400,'message' => MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type','application/json');
			}
			else
			{
				$data = array();
				foreach($get_details as $details)
				{
					$ch_array = array();
					if($details->ord_had_choices=="Yes")
					{
						$splitted_choice=json_decode($details->ord_choices,true);
						if(!empty($splitted_choice))
						{
							foreach($splitted_choice as $choice)
							{
								if(!isset($choices[$choice['choice_id']]))
								{
									$choices_name	=	DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
									$ch_array[]=array('choicename'=>$choices_name);									
								}
							}
						}
					}
					$comm = $details->ord_currency.' '.$details->ord_admin_amt;
					if($details->ord_status == '3' || $details->ord_status == '9')
					{
						$comm = "0.00";
					}
					$status = $payment_status = '';
					$refund_amt = '';
					
					if($details->ord_cancel_status == '1' && $details->ord_status == '3')
					{
						$status = MobileModel::get_lang_text($lang,'API_MER_REJECT','Reject by Merchant');
					}
					elseif($details->ord_cancel_status == '1' && $details->ord_status == '1')
					{
						$status = MobileModel::get_lang_text($lang,'API_USER_CANCEL','Cancel by User');
					}
					elseif($details->ord_status == '9')
					{
						$status = MobileModel::get_lang_text($lang,'API_FAIL','Delivery Failed');
					}
					
					
					if($details->ord_pay_type == 'COD')
					{
						$refund_amt = MobileModel::get_lang_text($lang,'API_NO_BAL_TO_RCV','No balance to receive');
						$payment_status = '-';
					}
					else
					{
						$refund_amt = ($details->ord_cancel_paidamt != '') ? $details->ord_currency.' '.$details->ord_cancel_paidamt : '-';
						if($details->ord_cancel_payment_status == '1')
						{
							$payment_status = MobileModel::get_lang_text($lang,'API_SUXES','Success');
						}
						elseif($details->ord_cancel_payment_status == '2')
						{
							$payment_status = MobileModel::get_lang_text($lang,'API_FAILED','Failed');
						}
						else
						{
							$payment_status = MobileModel::get_lang_text($lang,'API_PENDING','Pending');	
						}
					}
					$data[] = ['store_name' => ucfirst($details->st_name),
					'item_name' => ucfirst($details->pro_name),
					'item_has_choice' => $details->ord_had_choices,
					'choice_array' => $ch_array,
					'order_currency' => $details->ord_currency,
					'order_total' => $details->ord_grant_total,
					'commission'  => $comm,
					'cancel_type'  => $status,
					'refund_amount'  => $refund_amt,
					'transaction_id' => ($details->ord_cancelpaid_transid != '') ? $details->ord_cancelpaid_transid : '-',
					'refund_date'	=> ($details->cancel_paid_date != '') ? $details->cancel_paid_date : '-',
					'refund_status' => $payment_status
					];
				}
				$encode = ['code' => 200,
				'message' => MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!'),
				'data' => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type','application/json');
			}
		}
		
		/* customer reviews */
		public function my_reviews(Request $request)
		{
			$lang = $request->lang;
			$page_no = $request->page_no;
			$user	 = JWTAuth::user();
			$cus_id	 = $user->cus_id;
			$product_review = $item_review = $store_review = $restaurant_review = $order_review = array();
			/* product review */
			$pro_reviews  = MobileModel::get_my_review('product',$cus_id,$lang,$this->admin_default_lang,$page_no);
			if(count($pro_reviews)>0) 
			{
				foreach($pro_reviews as $p)
				{
					$imageUrl = MobileModel::get_image_product($p->pro_image);
					$product_review[] = array("product_id"		=>$p->proitem_id,
					"store_id"		=> $p->res_store_id,
					"product_title"	=>ucfirst(strip_tags($p->item_name)),
					"product_image"	=>$imageUrl,
					"review_comments"=>$p->review_comments,
					"review_rating"	=>floatval($p->review_rating),
					"shop_name"		=>$p->shop_name,
					"created_date"	=>$p->created_date);
				}
			}
			/* item review */
			$item_reviews  = MobileModel::get_my_review('item',$cus_id,$lang,$this->admin_default_lang,$page_no);
			if(count($item_reviews)>0)
			{
				foreach($item_reviews as $p){
					$imageUrl = MobileModel::get_image_item($p->pro_image);
					$item_review[] = array("product_id"		=>$p->proitem_id,
					"restaurant_id"	=> $p->res_store_id,
					"item_title"	=>ucfirst(strip_tags($p->item_name)),
					"item_image"	=>$imageUrl,
					"review_comments"=>$p->review_comments,
					"review_rating"	=>floatval($p->review_rating),
					"shop_name"		=>$p->shop_name,
					"created_date"	=>$p->created_date);
				}
			}
			/* store review */
			$store_reviews  = MobileModel::get_my_review('store',$cus_id,$lang,$this->admin_default_lang,$page_no);	
			if(count($store_reviews)>0) 
			{
				foreach($store_reviews as $p)
				{
					$imageUrl = MobileModel::get_image_store($p->st_logo,'logo');
					$store_review[] = array("res_store_id"=>$p->res_store_id,
					"store_image"=>$imageUrl,
					"review_comments"=>$p->review_comments,
					"review_rating"=>floatval($p->review_rating),
					"store_name"=>$p->shop_name,
					"created_date"=>$p->created_date);
				}
			}
			/* restaurant  review */
			$res_reviews  = MobileModel::get_my_review('restaurant',$cus_id,$lang,$this->admin_default_lang,$page_no);	
			if(count($res_reviews)>0) 
			{
				foreach($res_reviews as $p){
					$imageUrl = MobileModel::get_image_restaurant($p->st_logo,'logo');
					$restaurant_review[] = array("res_store_id"=>$p->res_store_id,"restaurant_image"=>$imageUrl,"review_comments"=>$p->review_comments,"review_rating"=>floatval($p->review_rating),"restaurant_name"=>$p->shop_name,"created_date"=>$p->created_date);
				}
			}
			/* order reveiw */
			$or_reviews  = DB::table('gr_review')->select('delivery_id','review_comments','created_date','review_rating','review_status',DB::Raw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) as deliver_name"))
			->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_review.delivery_id')
			->where('review_type','=','order')
			->where('gr_delivery_member.deliver_status','=','1')
			->where('gr_review.customer_id','=',$cus_id)
			->where('review_status','!=','2')
			->paginate(10,['*'],'order_review_page',$page_no);
			if(count($or_reviews)>0) 
			{
				foreach($or_reviews as $p)
				{
					$order_review[] = array(
					"delivery_person_name"	=>$p->deliver_name,
					"review_comments"		=>$p->review_comments,
					"review_rating"		=>floatval($p->review_rating),
					"created_date"			=>$p->created_date);
				}
			}
			if(count($order_review) <= 0 && count($product_review) <= 0 && count($item_review) <= 0 && count($restaurant_review) <= 0 && count($store_review) <= 0)  
			{
				$msg 	= MobileModel::get_lang_text($lang,'API_NO_REVIEWS','No Reviews available!');
				$encode = [ 'code'=> 400,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge 	= MobileModel::get_lang_text($lang,'API_REVIEWS_AVAIL','Reviews available!');
				$data 	= ["rest_review_list"	=>$restaurant_review,
				/*"store_review_list"	=>$store_review,*/
				"item_review_list"	=>$item_review,
				/*"product_review_list"=>$product_review,*/
				"order_review_list"	=>$order_review
				];
				$encode = array('code'=>200,"message"=>$msge,"data"=>$data);
				return Response::make(json_encode($encode, JSON_PRETTY_PRINT))->header('Content-Type', "application/json");
			}
		}
		
		/* get delviery person location */
		public function get_deliver_location(Request $request)
		{
			$lang = $request->lang;
			$id = $request->deliver_id;
			$validator = Validator::make($request->all(),['deliver_id' => 'required']);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			$det = get_details('gr_delivery_member',['deliver_id'=>$id],('deliver_latitude,deliver_longitude'));
			if(empty($det) === false)
			{	
				$encode = ['code' => 200,
				'message' => 'fetched successfully',
				'data' => ['deliver_latitude' => $det->deliver_latitude,
				'deliver_longitude' => $det->deliver_longitude]
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}

		/* check quantity available & payment setting */
		public function check_avail_qty(Request $request)
		{
			$cus_details = JWTAuth::user();
			$cus_id 	 = $cus_details->cus_id;
			$lang = $request->lang;
			$pdt_name = ($lang == $this->admin_default_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$sql = DB::table('gr_cart_save')
					->select('gr_product.'.$pdt_name.' as item_name','gr_cart_save.cart_quantity',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'))
					->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
					->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
					->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
					->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
					->where(['gr_cart_save.cart_cus_id' => $cus_id,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
					->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
					->get();
			$quantity_err = array();
			$payment_err = '';
			$payment_status = 'AVAILABLE';
			if($cus_details->cus_netbank_status == 'Unpublish' && $cus_details->cus_paymaya_status == 'Unpublish')
			{
				$payment_err = MobileModel::get_lang_text($lang,'API_CHECKOUT_INFO','We can\'t find payment details in your account.If order is cancelled,you can get refund through the provided details.You may skip this,if not wish');
				$payment_status = 'NOT_AVAILABLE';
			}
			if(count($sql) > 0)
			{
				foreach($sql as $details)
				{
					if($details->stock < $details->cart_quantity)
						{
							$got_message = MobileModel::get_lang_text($lang,'API_QTY_EXCEED_ERR',':item quantity exceeds the availble stock :stock');
							$searchReplaceArray = array(':item' => $details->item_name,':stock' =>$details->stock );
							$quantity_err[] = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
						}
				}
			}
			if(empty($quantity_err) && $payment_err == '')
			{
				$msge=MobileModel::get_lang_text($lang,'API_SUXES','Success');
				$encode = [ 'code' => 200,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge=MobileModel::get_lang_text($lang,'API_FAILED','Failed');
				$encode = [ 'code' => 400,
							'message' => $msge,
							'data' => ['payment_error' => $payment_err,
										'quantity_error'=> $quantity_err,
										'payment_status' => $payment_status]
						  ];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}

		/* save search location as shipping location */
		public function save_shipping_address(Request $request)
		{
			$lang = $request->lang;
			$cus_details = JWTAuth::user();
			$cus_id = $cus_details->cus_id;
			$location = $request->location;
			$latitude = $request->search_latitude;
			$longitude = $request->search_longitude;
			$zipcode = $request->zipcode;
			$validator = Validator::make($request->all(), ['search_latitude' => 'required',
															'search_longitude' => 'required',
															'location' => 'required',
															'zipcode'	=> 'required'],
															['search_latitude.required' => 'Please enter latitude',
															 'search_longitude.required' => 'Please enter longitude',
															 'location.required'		=> 'Please enter location',
															 'zipcode.required'			=> 'Please enter zipcode'
															 ]);
			if($validator->fails())
			{
				$encode = [ 'code' => 400,'message' => $validator->messages()->first(),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$shippingDet = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id','=',$cus_id)->first();
			$cart_count  = cart_count($cus_id);
			if(empty($shippingDet)===false){
				$user_lat = $shippingDet->sh_latitude;
				$user_long= $shippingDet->sh_longitude;
			}else{
				$user_lat = '0.000';
				$user_long= '0.000';
			}
			if($user_lat != $latitude || $user_long != $longitude)
			{	
				$msg = "Success";
				/* serching and shipping address is vary, clear cart */
				if($cart_count > 0)
				{

					DB::table('gr_cart_save')->where('cart_cus_id',$cus_id)->delete();	
				}
				
				/* update shipping address as search address */
				if(empty($shippingDet) === true)
				{
					DB::table('gr_shipping')->insert(['sh_cus_id' => $cus_id,
														'sh_latitude' => $latitude,
														'sh_longitude' => $longitude,
														'sh_location' => $location,
														'sh_zipcode'	=> $zipcode]);
				}
				else
				{
					DB::table('gr_shipping')->where('sh_cus_id','=',$cus_id)->update(['sh_latitude' => $latitude,
																						'sh_longitude' => $longitude,
																						'sh_location' =>$location,
																						'sh_zipcode'	=> $zipcode]);
				}
				$encode = [ 'code' => 200,
							'message' => $msg,
							'data' => $this->empty_data
						  ];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{
				$encode = [ 'code' => 200,
							'message' => "Success",
							'data' => $this->empty_data
						  ];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}

		
	}
?>

