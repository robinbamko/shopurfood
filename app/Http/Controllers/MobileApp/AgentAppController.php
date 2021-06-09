<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\MobileApp;
	use App\Http\Controllers\Controller;
	use App\Http\Models;
	use App\MobileModel;
	use App\Home;
	use App\User;
	use App\Agent;
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
	use Session;
	use Twilio;
	
	class AgentAppController extends Controller {
		
		private $empty_data;
		public function __construct()
		{	
			parent::__construct();
			$this->user = new User;
			$this->agent = new Agent;
			$this->empty_data = array();
			/*  get general details */
			$details = DB::table('gr_general_setting')->first();
			if(!empty($details))
			{
				$this->site_name = $details->gs_sitename;
			}
			else
			{
				$this->site_name = "ePickMeUp";
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
				View::share("SITENAME","ePickMeUp");
				View::share("FOOTERNAME","ePickMeUp");
			}
			/*LOGO DETAILS */
			$logo_settings_details = MobileModel::get_logo_settings_details();
			$path = url('').'/public/images/noimage/default_image_logo.jpg';
			$this->ios_splash_img_agent = $this->andr_splash_img_agent = '';
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
				if($logo_set_val->andr_splash_img_agent != '')
				{
					$filename = public_path('images/logo/').$logo_set_val->andr_splash_img_agent;
					if(file_exists($filename))
					{
						$this->andr_splash_img_agent = url('').'/public/images/logo/'.$logo_set_val->andr_splash_img_agent;
					}
				}
				if($logo_set_val->ios_splash_img_agent != '')
				{
					$filename = public_path('images/logo/').$logo_set_val->ios_splash_img_agent;
					if(file_exists($filename))
					{
						$this->ios_splash_img_agent = url('').'/public/images/logo/'.$logo_set_val->ios_splash_img_agent;
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
			/* EOF GENERAL DETAILS */
			$admin_det = get_admin_details();  /* get admin details*/
			$this->admin_id  = $admin_det->id;
		}
		
		/* ========================== AGENT SECTION STARTS HERE ================================ */
		public function agent_registration(Request $request) {
			$lang 		= Input::get('lang');
			$agent_fname= Input::get('agent_fname');
			$agent_lname= Input::get('agent_lname');
			$agent_email= Input::get('agent_email');
			$agent_password	= Input::get('agent_password');
			$agent_phone1 	= Input::get('agent_phone1');
			$admin_lang = (!empty($this->admin_default_lang))?$this->admin_default_lang:$this->default_mob_lang_code;
			/* -------------CHECKING LANGUAGE PARAMETER------------------ */
			//CHECK ADMIN LANGUAGE AND RECEIVED Language ARE DEFAULT LANGUAGE THEN DONT ADD SUFFIX ELSE ADD SUFFIX
			/*VALIDATION STARS HERE */ 
			$name_req  = MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$lname_req = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LAST_NAME','Please enter last name!');
			$email_req = MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$pass_req  = MobileModel::get_lang_text($lang,'API_PASSWORD_REQUIRED','The password field is required!');
			$phone_req = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$min_err  = MobileModel::get_lang_text($lang,'API_PASSWORD_RULES','Password min. length should be 6!');
			$email_exist = MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
			$ph_exist = MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
			$validator = Validator::make($request->all(),
			['agent_fname' 	=> 'required',
			'agent_lname'	=> 'required',
			'agent_email'	=> ['required','email',
			Rule::unique('gr_agent')->where(function ($query) use ($request) {
				return $query->where('gr_agent.agent_status','<>','2');
			}),
			],
			'agent_password' => 'required|min:6',
			'agent_phone1'	=> ['required',
			Rule::unique('gr_agent')->where(function ($query) use ($request) {
				return $query->where('gr_agent.agent_status','<>','2');
			}),
			]
			],
			['agent_fname.required' 	=> $name_req,
			'agent_lname.required'		=> $lname_req,
			'agent_email.required' 	=> $email_req,
			'agent_email.email' 		=> $email_req,
			'agent_email.unique' 		=> $email_exist,
			'agent_password.required' 	=> $pass_req,
			'agent_password.min' 		=> $min_err,
			'agent_phone1.required' 	=> $phone_req,
			'agent_phone1.unique' 		=> $ph_exist
			]
			);
			if($validator->fails())
			{
				$msge = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
			if($this->general_setting->gs_password_protect==1)
			{
				$uppercase = preg_match('@[A-Z]@', $agent_password);
				$lowercase = preg_match('@[a-z]@', $agent_password);
				$number    = preg_match('@[0-9]@', $agent_password);
				$splChar   = preg_match('@[\W_]@', $agent_password);
				
				if(!$uppercase || !$lowercase || !$number || !$splChar || strlen($agent_password) < 6) 
				{
					$msge=MobileModel::get_lang_text($lang,'API_PROTECT_PASSWORD_RULES','Password should be atleast one lower case, upper case, number and min.length 6!');
					return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}			
			
			$agent_status = 1;
			
			/*INSERT INTO TABLE */
			$user = Agent::create([
			'agent_fname' 		=> $agent_fname,
			'agent_lname' 		=> $agent_lname,
			'agent_email' 		=> $agent_email,
			'agent_password' 	=> md5($agent_password),
			'agent_phone1'		=> $agent_phone1,
			'agent_status' 		=> $agent_status,
			'mer_paynamics_status'=>'Unpublish',
			'mer_paymaya_status'=>'Unpublish',
			'mer_netbank_status'=>'Unpublish',
			'agent_created_at' => date('Y-m-d')
            ]);
			$lastinsertid = DB::getPdo()->lastInsertId();
			
			$token = JWTAuth::fromUser($user);
			/*MAIL FUNCTION */
			$send_mail_data = array('name'		 	=> $agent_fname.' '.$agent_lname,
			'password' 		=> $agent_password,
			'email' 		=> $agent_email,
			"agent_phone"	=>$agent_phone1,
			'lang'			=>$lang.'_mob_lang',
			'onlyLang'		=>$lang,
			'itunes_url'	=>$this->general_setting->gs_apple_appstore_url,
			'playstore_url'=>$this->general_setting->gs_playstore_url);
			Mail::send('email.mobileagentregister', $send_mail_data, function($message) use($send_mail_data)
			{
				$email               = $send_mail_data['email'];
				$name                = $send_mail_data['name'];
				$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'API_REGISTERED_DETAILS','Your Registration Details!');
				$message->to($email, $name)->subject($subject);
			});
			/* EOF MAIL FUNCTION */ 
			$msge = MobileModel::get_lang_text($lang,'API_REGISTERED_SUCCESSFULLY','Registered successfully!');
			$data = ['token'		=>$token,
			"agent_id"	=>intval($lastinsertid),
			"agent_name"=>ucfirst($agent_fname).' '.$agent_lname,
			"agent_email"=>$agent_email,
			"agent_phone"=>$agent_phone1,
			"agent_status"=>intval($agent_status)];
			return Response::make(json_encode(array('code'=>200,'message'=>$msge,
			'data'	=> $data
			),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		
		/*  home page */
		public function home(Request $request)
		{
			$lang = $request->lang;
			/*$cus_name = JWTAuth::user()->agent_fname;*/
			$greet = MobileModel::get_lang_text($lang,'API_WELCOME_TO','Welcome To ');
			$encode = [ 'code' => 200,
			'message'  => $greet.$this->site_name,
			'data'	 => ['splash_screen_android' => $this->andr_splash_img_agent,
			'splash_screen_ios' 	=> $this->ios_splash_img_agent]
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			
		}
		
		public function agent_forgot_password(Request $request){
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$email_req_err_msg=MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$validator = Validator::make($request->all(), 
			['agent_email' => 'required|string|email'],
			['agent_email.required'=>$email_req_err_msg,
			'agent_email.email'=>$valid_email_err_msg
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$LoginRes = MobileModel::checkUserMailExist($request->agent_email);
			if(count($LoginRes)==1)
			{
				foreach($LoginRes as $Login) { }
				$agent_id = $Login->agent_id;
				$passwordIs = rand_password();
				$agent_password = array( 'agent_password' => md5($passwordIs) );
				DB::table('gr_agent')->where('agent_id', '=', $agent_id)->update($agent_password);
				
				/*MAIL FUNCTION */
				$data = array('ForgotEmail' 	=> $request->agent_email,
				'ForgotPassword'  => $passwordIs,
				'lang'=>$lang.'_mob_lang',
				'onlyLang'=>$lang);
				Mail::send('email.mobile_agent_forgetpwd', $data, function($message) use($data)
				{
					$email               = $data['ForgotEmail'];
					$name                = $data['ForgotEmail'];
					$subject = MobileModel::get_lang_text($data['onlyLang'],'API_FORGOT_PASSWORD_DETAILS','Forgot password details!');
					$message->to($email, $name)->subject($subject);
				});
				$msge=MobileModel::get_lang_text($lang,'API_FORGOT_PASSWORD_SENT','Password sent to your mail!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				/* EOF MAIL FUNCTION */ 
			}
			else{
				$msge = MobileModel::get_lang_text($lang,'API_INVALID_EMAIL','Invalid Email ID!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		public function agent_reset_password(Request $request)
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
				[ 'old_password' => 'required|min:6',
				'new_password' => 'required|min:6|regex:/(^[A-Za-z0-9!@$%^&*() ]+$)+/'
				],
				['old_password.required'=>$oldpwd_req_err_msg,
				'old_password.min'  	=>$old_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.required'=>$newpwd_req_err_msg,
				'new_password.min'  	=>$new_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.regex'	=>$pwd_regex_err_msg
				]
				);
			}
			else{
				$validator = Validator::make($request->all(), 
				[ 'old_password' => 'required|min:6',
				'new_password' => 'required|min:6'],
				['old_password.required'=>$oldpwd_req_err_msg,
				'old_password.min'		=>$old_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.required'=>$newpwd_req_err_msg,
				'new_password.min'		=>$new_pwd_text.' '.$pwd_min_6_err_msg
				]
				);
			}
			
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$user = JWTAuth::user();
				//echo $user->agent_password.'<hr>';
				if($user->agent_password != md5($old_password))
				{
					$msg = MobileModel::get_lang_text($lang,'API_INCORRECT_PASSWORD','Your old password does not match with our records! Please try again!');
					return Response::make(json_encode(array('code'=>400,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				elseif($old_password == $new_password){
					$msg = MobileModel::get_lang_text($lang,'API_NEW_PASS_NOT_SAME_CURRENT_PASS','New Password cannot be same as your current password. Please choose a different password!');
					return Response::make(json_encode(array('code'=>400,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
					}else{
					
					$insertArr = array('agent_password' => md5($new_password));
					$update = updatevalues('gr_agent',$insertArr,['agent_id'=>$user->agent_id]);
					$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
					return Response::make(json_encode(array('code'=>200,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
		}
		//Get Agent Profile
		public function agent_my_account(Request $request)
		{
			
			$agent = JWTAuth::user();
            // print_r($user);exit;
			$msge = 'Fetched Agent profile details succesfully!';
			
			if($agent->agent_img != ''){
				$filename = public_path('images/agent/').$agent->agent_img;
				
				if(file_exists($filename)){
					$agent_avatar = url('public/images/agent/'.$agent->agent_img );
					}else{
					$agent_avatar = url('public/images/noimage/default_user_image.jpg');
				}
				}else{
				$agent_avatar = url('public/images/noimage/default_user_image.jpg');
			}
			$data = ["agent_id"		=>intval($agent->agent_id),
			"agent_name"	=>ucfirst($agent->agent_fname),
			"agent_lame"	=>ucfirst($agent->agent_lname),
			"agent_email"	=>$agent->agent_email,
			"agent_phone"	=>$agent->agent_phone1,
			"agent_phone2"	=>$agent->agent_phone2,
			"agent_location"=>$agent->agent_location,
			"agent_latitude"=>$agent->agent_latitude,
			"agent_longitude"=>$agent->agent_longitude,
			"agent_avatar"	=>$agent_avatar];
			return Response::make(json_encode(array('code'		=>200,
			'message'	=>$msge,
			'data'		=> $data
			),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		//Agent Update Profile
		public function agent_update_account(Request $request){
			$agent = JWTAuth::user();
			//print_r($agent); exit;
			$agent_id 		= $agent->agent_id;
			$agent_fname 	= $request->agent_fname;
			$agent_lame 	= $request->agent_lame;
			$agent_email 	= $request->agent_email;
			$agent_phone1 	= $request->agent_phone1;
			$agent_phone2 	= $request->agent_phone2;
			$agent_location = $request->agent_location;
			$agent_image 	= $request->agent_img;
			$agent_latitude = $request->agent_latitude;
			$agent_longitude = $request->agent_longitude;
			$available_status = $request->available_status;
			$lang 			= $request->lang;
			
			$agent_name_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg = MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$valid_phone_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$valid_phone2_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM2','Please enter valid phone number2!');
			$addr_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$agent_img_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_SELECT_IMAGE','Please select image!');
			$agent_img_dimen_err_msg = MobileModel::get_lang_text($lang,'API_AGENT_IMAGE_DIMEN_VAL','Image Should be Width=300, Height=300!');
			$availstatus_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_AVAILABLE','Please enter available status');
			$valid_availstatus_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_VALID_AVAILABLE_STATUS','Please enter valid available status! It should be 1/2');
			
			$validator = Validator::make($request->all(),[  'agent_fname'		=> 'required',
			'agent_email' 		=> 'required|string|email',
			'agent_phone1' 		=> 'required|only_cnty_code',
			//'agent_phone2' 	=> 'sometimes|only_cnty_code',
			'agent_location'	=> 'required',
			'agent_latitude'	=> 'required',
			'agent_longitude'	=> 'required',
			'agent_img' 		=> 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'available_status'  => 'required|numeric'
			],
			[
			'agent_fname.required'		=> $agent_name_req_err_msg,
			'agent_email.required'		=> $email_req_err_msg,
			'agent_email.email'			=> $valid_email_err_msg,
			'agent_phone1.required'		=> $valid_phone_err_msg,
			'agent_phone1.only_cnty_code'=> $valid_phone_err_msg,
			//'agent_phone2.only_cnty_code'=>$valid_phone2_err_msg,
			'agent_location.required'	=> $addr_req_err_msg,
			'agent_latitude.required'	=> $lati_req_err_msg,
			'agent_longitude.required'	=> $longi_req_err_msg,
			'agent_img.sometimes'		=> $agent_img_req_err_msg,
			//'agent_img.dimensions'		=> $agent_img_dimen_err_msg,
			'available_status.required' => $availstatus_req_err_msg,
			'available_status.numeric'	=> $valid_availstatus_err_msg	
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,'message'=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$check_already_exsist = DB::table('gr_agent')->where('agent_email','=',$agent_email)->where('agent_id','!=',$agent_id)->where('agent_status','!=','2')->count();
			$check_phoneNumber_already_exsist = DB::table('gr_agent')->where('agent_phone1','=',$agent_phone1)->where('agent_id','!=',$agent_id)->where('agent_status','!=','2')->count();
			if($check_already_exsist > 0 )
			{
				$msg = MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist >  0 )
			{
				$msg = MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($available_status < 1 && $available_status > 2){
				$msg = MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($agent->agent_phone1 != $agent_phone1 && $agent->agent_email != $agent_email ){
				$msg = MobileModel::get_lang_text($lang,'API_YOU_CANNOT_CHANGE','Sorry! You cannot change mobile number and email at a time.Update one by one!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$insertArr = array(	'agent_fname' 		=> $agent_fname,
			'agent_lname' 		=> $agent_lame,
			'agent_email' 		=> $agent_email,
			'agent_phone1' 		=> $agent_phone1,
			'agent_phone2' 		=> $agent_phone2,
			'agent_location' 	=> $agent_location,
			'agent_latitude' 	=> $agent_latitude,
			'agent_longitude' 	=> $agent_longitude,
			'agent_avail_status' => $available_status
			);
			if($request->hasFile('agent_img')) {
				$agent_image = 'agent'.time().'.'.request()->agent_img->getClientOriginalExtension();
				$destinationPath = public_path('images/agent');
				$agent = Image::make(request()->agent_img->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image,80);
				$insertArr['agent_img']=$agent_image;
			}
			/*----------------------CHECK PHONE NUMBER IS NEW  ------------*/
			if($agent->agent_phone1 != $agent_phone1)
			{
				$otp = mt_rand(100000, 999999);
				try{
					Twilio::message($agent_phone1, $otp);
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$insertArr['code'] = 200;
					$insertArr['message']= $msge;
					$insertArr['data']	 = ['otp' => $otp];
					return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				catch (\Exception $e)
				{		
					$encode = array('code'=> 400,'message' => $e->getMessage(),'data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			elseif($agent->agent_email != $agent_email)
			{
				$code = mt_rand(100000, 999999);
				$send_mail_data = ['code' => $code,
				'lang'=>$lang.'_mob_lang',
				'onlyLang'=>$lang,
				'agent_email'=>$agent_email
				];
				$send = Mail::send('email.mobile_email_verification', $send_mail_data, function($message) use($send_mail_data)
				{	
					$msg = MobileModel::get_lang_text($send_mail_data['lang'],'API_CNFRM_MAIL','Confirm Your Mail');
					$message->to($send_mail_data['agent_email'])->subject($msg);
				});
				$msge = MobileModel::get_lang_text($lang,'API_VERICODE_SENT_MAIL','Verification code sent to your email. Please enter verification code');
				$insertArr['code'] = 200;
				$insertArr['message']= $msge;
				$insertArr['data']	 = ['otp' => $code];
				return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			} 
			else 
			{
				$update = updatevalues('gr_agent',$insertArr,['agent_id' =>$agent_id]);
				$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
				return Response::make(json_encode(array('code'=>200,'message'=>$msg),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		public function agent_update_account_otp(Request $request){
			$agent = JWTAuth::user();
			//print_r($agent); exit;
			$agent_id 		= $agent->agent_id;
			$agent_fname 	= $request->agent_fname;
			$agent_lame 	= $request->agent_lame;
			$agent_email 	= $request->agent_email;
			$agent_phone1 	= $request->agent_phone1;
			$agent_phone2 	= $request->agent_phone2;
			$agent_location = $request->agent_location;
			$agent_image 	= $request->agent_img;
			$agent_latitude = $request->agent_latitude;
			$agent_longitude = $request->agent_longitude;
			$available_status = $request->available_status;
			$lang 			= $request->lang;
			
			$agent_name_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$email_req_err_msg = MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.');
			$valid_email_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$valid_phone_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$valid_phone2_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM2','Please enter valid phone number2!');
			$addr_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$agent_img_req_err_msg = MobileModel::get_lang_text($lang,'API_PLS_SELECT_IMAGE','Please select image!');
			$agent_img_dimen_err_msg = MobileModel::get_lang_text($lang,'API_AGENT_IMAGE_DIMEN_VAL','Image Should be Width=300, Height=300!');
			$availstatus_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_AVAILABLE','Please enter available status');
			$valid_availstatus_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_VALID_AVAILABLE_STATUS','Please enter valid available status! It should be 1/2');
			
			$validator = Validator::make($request->all(),[  'agent_fname'		=> 'required',
			'agent_email' 		=> 'required|string|email',
			'agent_phone1' 		=> 'required|only_cnty_code',
			'agent_location'	=> 'required',
			'agent_latitude'	=> 'required',
			'agent_longitude'	=> 'required',
			//'agent_img' 		=> 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300',
			'available_status'  => 'required|numeric'
			],
			[
			'agent_fname.required'		=> $agent_name_req_err_msg,
			'agent_email.required'		=> $email_req_err_msg,
			'agent_email.email'			=> $valid_email_err_msg,
			'agent_phone1.required'		=> $valid_phone_err_msg,
			'agent_phone1.only_cnty_code'=> $valid_phone_err_msg,
			'agent_location.required'	=> $addr_req_err_msg,
			'agent_latitude.required'	=> $lati_req_err_msg,
			'agent_longitude.required'	=> $longi_req_err_msg,
			//'agent_img.required'		=> $agent_img_req_err_msg,
			//'agent_img.dimensions'		=> $agent_img_dimen_err_msg,
			'available_status.required' => $availstatus_req_err_msg,
			'available_status.numeric'	=> $valid_availstatus_err_msg	
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,'message'=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$check_already_exsist = DB::table('gr_agent')->where('agent_email','=',$agent_email)->where('agent_id','!=',$agent_id)->where('agent_status','!=','2')->count();
			$check_phoneNumber_already_exsist = DB::table('gr_agent')->where('agent_phone1','=',$agent_phone1)->where('agent_id','!=',$agent_id)->where('agent_status','!=','2')->count();
			if($check_already_exsist > 0 )
			{
				$msg = MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist >  0 )
			{
				$msg = MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($available_status < 1 && $available_status > 2){
				$msg = MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');                                                                      
				return Response::make(json_encode(array('code'=>400,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$current_otp = $request->current_otp;
			$generated_otp = $request->otp;
			$msge = '';
			if($generated_otp =="" || $current_otp =="")
			{
				$msge=MobileModel::get_lang_text($lang,'API_ENTER_YOUR_OTP','Please enter OTP!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
			$insertArr = array(	'agent_fname' 		=> $agent_fname,
			'agent_lname' 	=> $agent_lame,
			'agent_email' 	=> $agent_email,
			'agent_phone1' 	=> $agent_phone1,
			'agent_phone2' 	=> $agent_phone2,
			'agent_location' => $agent_location,
			'agent_latitude' => $agent_latitude,
			'agent_longitude' => $agent_longitude,
			'agent_avail_status' => $available_status
			);
			if($request->agent_img!='') {
				$insertArr['agent_img']=$request->agent_img;
			}
			if($generated_otp == $current_otp){
				$update = updatevalues('gr_agent',$insertArr,['agent_id' =>$agent_id]);
				$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
				return Response::make(json_encode(array('code'=>200,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else 
			{
				if($agent->agent_phone1 != $agent_phone1)
				{
					$msge=MobileModel::get_lang_text($lang,'API_INVALID_OTP','Invalid OTP');
				}
				elseif($agent->agent_email != $agent_email)
				{
					$msge=MobileModel::get_lang_text($lang,'API_INVALID_VERIFICATION_CODE','Invalid verification code');
				}
				$insertArr['code'] = 400;
				$insertArr['message'] = $msge;
				$insertArr['data']   = ['otp' => $generated_otp];
				return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		//***** Agent Get Payment Setting  ****//
		public function agent_get_payment_settings()
		{
			$agent = JWTAuth::user();
			$agent_id = $agent->agent_id;
			$msge = 'Fetched Agent payments details succesfully!';
			$data  = ['mer_paynamics_status'		=>$agent->mer_paynamics_status,
			'mer_paynamics_clientid'	=>($agent->mer_paynamics_clientid == '') ? '' : $agent->mer_paynamics_clientid,
			'mer_paynamics_secretid'	=> ($agent->mer_paynamics_secretid == '') ? '' :$agent->mer_paynamics_secretid,
			'mer_paymaya_status'		=> $agent->mer_paymaya_status,
			'mer_paymaya_clientid'		=> ($agent->mer_paymaya_clientid == '') ? '' : $agent->mer_paymaya_clientid,
			'mer_paymaya_secretid'		=> ($agent->mer_paymaya_secretid == '') ? '' : $agent->mer_paymaya_secretid,
			'mer_netbank_status'		=>$agent->mer_netbank_status,
			'mer_bank_name'				=> ($agent->mer_bank_name == '') ? '' : $agent->mer_bank_name,
			'mer_branch'				=> ($agent->mer_branch == '') ? '' : $agent->mer_branch,
			'mer_bank_accno'			=> ($agent->mer_bank_accno == '') ? '' : $agent->mer_bank_accno,
			'mer_ifsc'					=> ($agent->mer_ifsc == '') ? '' : $agent->mer_ifsc  
			];
			$outputArray = array( 'code'					=> 200,
			'message'					=> $msge,
			'data'					=> $data	
			);
			return Response::make(json_encode($outputArray,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		//***** Agent Update Payment Setting  ****//
		public function agent_update_payment_setting(Request $request)
		{
			$agent = JWTAuth::user();
            $lang = $request->lang;           
			$mer_paynamics_status 	= $request->mer_paynamics_status;		
			$mer_paynamics_clientid = $request->mer_paynamics_clientid;		
			$mer_paynamics_secretid = $request->mer_paynamics_secretid;		
			
			$mer_paymaya_status 	= $request->mer_paymaya_status;		
			$mer_paymaya_clientid 	= $request->mer_paymaya_clientid;		
			$mer_paymaya_secretid 	= $request->mer_paymaya_secretid;		
			
			$mer_netbank_status 	= $request->mer_netbank_status;		
			$mer_bank_name 			= $request->mer_bank_name;		
			$mer_branch 			= $request->mer_branch;		
			$mer_bank_accno 		= $request->mer_bank_accno;		
			$mer_ifsc 				= $request->mer_ifsc;		
			
			
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
			
			if($mer_paynamics_status=='Unpublish' && $mer_paymaya_status=='Unpublish' && $mer_netbank_status=='Unpublish')
			{
				$msg = MobileModel::get_lang_text($lang,'API_PUBLISH_ATLEAST','Please enter atleast one payment gateway details with publish status!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if( $mer_paynamics_status == 'Publish')
			{
				$validator = Validator::make($request->all(),['mer_paynamics_clientid' => 'required',
				'mer_paynamics_secretid' => 'required'
				],[
				'mer_paynamics_clientid.required' => $agent_paynmincs_client_req_err_msg,
				'mer_paynamics_secretid.required' => $agent_paynmincs_secret_req_err_msg,
				
				]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				}else{
				$mer_paynamics_clientid	= '';
				$mer_paynamics_secretid = '';
			}
			
			
			if($mer_paymaya_status=='Publish')
			{
				$validator = Validator::make($request->all(),[ 	'mer_paymaya_clientid' => 'required',
				'mer_paymaya_secretid' => 'required'
				],[ 
				'mer_paymaya_clientid.required' => $agent_paymaya_secret_req_err_msg,
				'mer_paymaya_secretid.required' => $agent_paymaya_status_req_err_msg
				]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				}else{
				$mer_paymaya_clientid	= '';
				$mer_paymaya_secretid = '';
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
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				}else{
				$mer_bank_name  = '';
				$mer_branch    	= '';
				$mer_bank_accno	= '';
				$mer_ifsc    	= '';
			}
			
			$agent_payment_details = array('mer_paynamics_status'	=> $mer_paynamics_status,
			'mer_paynamics_clientid'=> $mer_paynamics_clientid,
			'mer_paynamics_secretid'=> $mer_paynamics_secretid,
			'mer_paynamics_mode'	=> 'Live',
			'mer_paymaya_status'	=> $mer_paymaya_status,
			'mer_paymaya_clientid'	=> $mer_paymaya_clientid,
			'mer_paymaya_secretid'	=> $mer_paymaya_secretid,
			'mer_paymaya_mode'		=> 'Live',
			'mer_netbank_status'	=> $mer_netbank_status,
			'mer_bank_name'			=> $mer_bank_name,
			'mer_branch'			=> $mer_branch,
			'mer_bank_accno'		=> $mer_bank_accno,
			'mer_ifsc'				=> $mer_ifsc
			);
			/** Update Agent Payment Details **/
			DB::table('gr_agent')->where('agent_id', '=', $agent->agent_id)->update($agent_payment_details);
			
			$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
			return Response::make(json_encode(array('code'=>200,'message'=>$msg),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		/************** ADD DELIVERY BOY ******************/
		public function agent_add_deliveryboy(Request $request)
		{
            $agent = JWTAuth::user();
            $lang = $request->lang; 
            $deliver_fname  = Input::get('deliver_fname');
            $deliver_lname  = Input::get('deliver_lname');
            $deliver_email  = Input::get('deliver_email');
            $deliver_phone1 = Input::get('deliver_phone1');
            $deliver_phone2	= Input::get('deliver_phone2');
            $deliver_status = '1'; 
            $deli_fname_req_err_msg			 = MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');            
            $deli_email_req_err_msg          = MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required!');
            $deli_email_valid_req_err_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			
            $deli_phone1_req_err_msg 		 = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!'); 
            $deli_basefare_req_err_msg		 = MobileModel::get_lang_text($lang,'API_PLS_BASE_FARE','Please Enter Base Fare'); 
            $deli_resTime_req_err_msg		 = MobileModel::get_lang_text($lang,'API_PLS_RES_TIME','Please Enter Response Time'); 
            $deli_fareType_req_err_msg		 = MobileModel::get_lang_text($lang,'API_PLS_FARE_TYPE','Please select Fare Type'); 
            $deli_perkm_charge_req_err_msg	 = MobileModel::get_lang_text($lang,'API_PLS_PER_KM_CHARGE','Please Per Kilometer Charge'); 
            $deli_permin_charge_req_err_msg	 = MobileModel::get_lang_text($lang,'API_PLS_PER_MIN_CHARGE','Please Enter Per Minute Charge'); 
            $deli_order_limit_req_err_msg	 = MobileModel::get_lang_text($lang,'API_PLS_ORDER_LIMIT','Please Enter Number of order limit ');
			
            $validator = Validator::make($request->all(),[ 
			'deliver_fname' 		=>'required',
			'deliver_email' 		=>'required|email',
			'deliver_phone1' 		=>'required|only_cnty_code',
			'deliver_base_fare' 	=>'required|numeric',
			'deliver_response_time'	=>'required',
			'deliver_order_limit' 	=>'required|integer',
			'deliver_fare_type' 	=>'required',
			],[
			'deliver_fname.required' 		=>$deli_fname_req_err_msg,
			'deliver_email.required' 		=>$deli_email_req_err_msg,
			'deliver_email.email' 			=>$deli_email_valid_req_err_msg,
			'deliver_phone1.required' 		=>$deli_phone1_req_err_msg,
			'deliver_phone1.only_cnty_code'	=>$deli_phone1_req_err_msg,
			'deliver_base_fare.required' 	=>$deli_basefare_req_err_msg,
			'deliver_response_time.required'=>$deli_resTime_req_err_msg,
			'deliver_order_limit.required' 	=>$deli_order_limit_req_err_msg,
			'deliver_fare_type.required' 	=>$deli_fareType_req_err_msg,
			]
			);
            if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
            /*If Fare type Per km required validation*/
            if(Input::get('deliver_fare_type')=='per_km')
            {
            	$validator = Validator::make($request->all(),['deliver_perkm_charge' =>'required'],['deliver_perkm_charge.required' =>$deli_perkm_charge_req_err_msg]);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
            
			/*If Fare type Per min required validation*/
            if(Input::get('deliver_fare_type')=='per_min')
            {
            	$validator = Validator::make($request->all(),
				['deliver_permin_charge' =>'required'],
				['deliver_permin_charge.required' =>$deli_permin_charge_req_err_msg]);
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			
            /*Check delivery boy email exist or not */
			$check_already_exsist = DB::table('gr_delivery_member')->where('deliver_email','=',$deliver_email)->where('deliver_status','!=','2')->count();
			/*Check delivery boy phone number exist or not */
			$check_phoneNumber_already_exsist = DB::table('gr_delivery_member')->where('deliver_phone1','=',$deliver_phone1)->where('deliver_status','!=','2')->count();		
			if (!filter_var(Input::get('deliver_email'), FILTER_VALIDATE_EMAIL))
			{
				$msge=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($check_already_exsist > 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist > 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
			$passwordIs = rand_password();
			
			$data = array(
			'deliver_agent_id'		=>$agent->agent_id,
			'deliver_fname'			=>Input::get('deliver_fname'),
			'deliver_lname'			=>Input::get('deliver_lname'),
			'deliver_email'			=>Input::get('deliver_email'),
			"deliver_password"		=>md5($passwordIs),
			'deliver_phone1'		=>Input::get('deliver_phone1'),
			//'deliver_phone2'		=>Input::get('deliver_phone2'),
			'deliver_response_time' =>date('H:i:s',strtotime(Input::get('deliver_response_time'))),
			'deliver_status'        =>'1',
			'deliver_base_fare'     =>Input::get('deliver_base_fare'),
			'deliver_fare_type'     =>Input::get('deliver_fare_type'),
			'deliver_currency_code' =>$this->general_setting->gs_currency_code,
			'deliver_perkm_charge'  =>Input::get('deliver_perkm_charge'),
			'deliver_permin_charge' =>Input::get('deliver_permin_charge'), 
			'deliver_availfrom'     =>Input::get('deliver_availfrom'),
			'deliver_order_limit'   =>Input::get('deliver_order_limit'),
			'deliver_read_status'   =>'0',
			'deliver_created_at'    => date('Y-m-d H:i:s'),
			'deliver_updated_at'    => date('Y-m-d H:i:s'),
			);
			/*INSERT DELIVERY BOY DETAILS*/
			$res=insertvalues('gr_delivery_member',$data);
			if($res)
			{
				
				$lastinsertid = DB::getPdo()->lastInsertId();	
				
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => Input::get('deliver_fname').' '.Input::get('deliver_lname'),
				'password' 		=> $passwordIs,
				'email' 		=> Input::get('deliver_email'),
				'lang'			=>$lang.'_mob_lang',
				'onlyLang'		=>$lang,
				'itunes_url'	=>$this->general_setting->gs_apple_appstore_url,
				'playstore_url'=>$this->general_setting->gs_playstore_url
				);
				Mail::send('email.mobile_agent_delboy_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('deliver_email');
					$name                = Input::get('deliver_fname').' '.Input::get('deliver_lname');
					$subject =MobileModel::get_lang_text(Input::get('lang'),'API_REGISTERED_DETAILS','Your Registration Details');
					
					$message->to($email, $name)->subject($subject);
				});
				/* EOF MAIL FUNCTION */ 
				$msge=MobileModel::get_lang_text($lang,'API_REGISTERED_SUCCESSFULLY','Registered successfully!');
				$data = ["agent_id"			=>$agent->agent_id,
				"deliver_fname"		=>ucfirst($deliver_fname).' '.$deliver_lname,
				"deliver_email"		=>$deliver_email,
				"deliver_phone"		=>$deliver_phone1,
				"deliver_status"    =>intval($deliver_status)];
				$outputArray = array(
				'code'			=>200,
				'message'		=>$msge,
				'data'			=> $data
				);
				return Response::make(json_encode($outputArray,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
		}
		public function agent_manage_deliveryboy(Request $request){
			$lang = $request->lang; 
			$page_no 	= $request->page_no;
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$delboyList = DB::table('gr_delivery_member')
			->select('deliver_id',
			'deliver_fname',
			'deliver_lname',
			'deliver_email',
			'deliver_phone1',
			'deliver_status'
			)
			->where('deliver_status','<>','2')
			->orderBy('deliver_id','DESC')
			->paginate(10,['*'],'delboy_page',$page_no);
			//print_r($delboyList); exit;
			if(count($delboyList) > 0){
				foreach($delboyList as $p){
					if($p->deliver_status == 1)
					{
						$statusLink = 'Unblock';
					}
					else
					{
						$statusLink = 'Block';
					}
					$delBoyArray[] = array("delboy_id"=>$p->deliver_id,"delboy_name"=>ucfirst($p->deliver_fname).' '.$p->deliver_lname,"delboy_email"=>$p->deliver_email,"delboy_phone1"=>$p->deliver_phone1,'code'=>$statusLink,'statusInt'=>$p->deliver_status);
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['delBoyList'=>$delBoyArray];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data'=>$data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else 
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		public function block_unblock_delete(Request $request) 
		{
			$lang 		= $request->lang; 
			$delboy_id 	= $request->delboy_id; 
			$action	 	= $request->action; 
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$delboyId_req_err_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_DELBOY_ID','Please enter delivery boy ID!');
			$delboyId_valid_err_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_VALID_DELBOY_ID','Please enter valid delivery boy ID!');
            $action_req_err_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_ACTION_ID','Please enter action ID!');
			$action_valid_err_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_VALID_ACTION_ID','Please enter valid action ID! It should be 1/2/3'); 
			$validator = Validator::make($request->all(),
			['delboy_id' =>'required|integer',
			'action' =>'required|numeric'],
			['delboy_id.required' =>$delboyId_req_err_msg,
			'delboy_id.integer' =>$delboyId_valid_err_msg,
			'action.required' 	=>$action_req_err_msg,
			'action.numeric' 	=>$action_valid_err_msg
			]);
            if($validator->fails())
            {
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($delboy_id <= 0 )
			{
				return Response::make(json_encode(array('code'=>400,"message"=>$delboyId_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($action < 0 || $action > 2)
			{
				return Response::make(json_encode(array('code'=>400,"message"=>$action_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$update = ['deliver_status' => $action];
			$where = ['deliver_id' => $delboy_id];
			$a = updatevalues('gr_delivery_member',$update,$where);
			
			if($action == 1) //Active
			{	
				$msge=MobileModel::get_lang_text($lang,'API_UNBLOCK_SUCCESS','Unblocked successfully!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			elseif($action == 2) //Delete
			{	
				$msge=MobileModel::get_lang_text($lang,'API_DELETE_SUCCESS','Deleted successfully!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			elseif($action == 0)   //block
			{	
				$msge=MobileModel::get_lang_text($lang,'API_BLOCK_SUCCESS','Blocked successfully!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		} 
		/*Edit Delivery boy details*/
		public function agent_edit_deliveryboy(Request $request)
		{
            $agent = JWTAuth::user();
            $lang = $request->lang; 
			$delivery_boy_id= Input::get('delivery_boy_id');
            $deliver_fname	= Input::get('deliver_fname');
            $deliver_lname 	= Input::get('deliver_lname');
            $deliver_email 	= Input::get('deliver_email');
            $deliver_phone1	= Input::get('deliver_phone1');
			
			$delboyId_req_err_msg	 		 = MobileModel::get_lang_text($lang,'API_ENTER_DELBOY_ID','Please enter delivery boy ID!');
			$delboyId_valid_err_msg	 		 = MobileModel::get_lang_text($lang,'API_ENTER_VALID_DELBOY_ID','Please enter valid delivery boy ID!');       
            $deli_fname_req_err_msg			 = MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');            
            $deli_email_req_err_msg          = MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required!');
            $deli_email_valid_req_err_msg	 = MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			
            $deli_phone1_req_err_msg 		 = MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!'); 
            $deli_basefare_req_err_msg		 = MobileModel::get_lang_text($lang,'API_PLS_BASE_FARE','Please Enter Base Fare'); 
            $deli_resTime_req_err_msg		 = MobileModel::get_lang_text($lang,'API_PLS_RES_TIME','Please Enter Response Time'); 
            $deli_fareType_req_err_msg		 = MobileModel::get_lang_text($lang,'API_PLS_FARE_TYPE','Please select Fare Type'); 
            $deli_perkm_charge_req_err_msg	 = MobileModel::get_lang_text($lang,'API_PLS_PER_KM_CHARGE','Please Per Kilometer Charge'); 
            $deli_permin_charge_req_err_msg	 = MobileModel::get_lang_text($lang,'API_PLS_PER_MIN_CHARGE','Please Enter Per Minute Charge'); 
            $deli_order_limit_req_err_msg	 = MobileModel::get_lang_text($lang,'API_PLS_ORDER_LIMIT','Please Enter Number of order limit ');
			
            $validator = Validator::make($request->all(),[ 'delivery_boy_id'		=> 'required|integer',
			'deliver_fname' 		=> 'required',
			'deliver_email' 		=> 'required|email',
			'deliver_phone1' 		=> 'required|only_cnty_code',
			'deliver_base_fare' 	=> 'required|numeric',
			'deliver_response_time'	=> 'required',
			'deliver_order_limit' 	=> 'required|integer',
			'deliver_fare_type' 	=> 'required',
			],[ 'delivery_boy_id.required'		=> $delboyId_req_err_msg,
			],[ 'delivery_boy_id.integer'		=> $delboyId_valid_err_msg,
			'deliver_fname.required' 		=> $deli_fname_req_err_msg,
			'deliver_email.required' 		=> $deli_email_req_err_msg,
			'deliver_email.email' 			=> $deli_email_valid_req_err_msg,
			'deliver_phone1.required' 		=> $deli_phone1_req_err_msg,
			'deliver_phone1.only_cnty_code'	=> $deli_phone1_req_err_msg,
			'deliver_base_fare.required' 	=> $deli_basefare_req_err_msg,
			'deliver_response_time.required'=> $deli_resTime_req_err_msg,
			'deliver_order_limit.required' 	=> $deli_order_limit_req_err_msg,
			'deliver_fare_type.required' 	=> $deli_fareType_req_err_msg,
			] 
			);
            if($validator->fails())
            {
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
            if(Input::get('deliver_fare_type')=='per_km')
            {
            	$validator = Validator::make($request->all(),
				['deliver_perkm_charge' =>'required'],
				['deliver_perkm_charge.required' =>$deli_perkm_charge_req_err_msg]);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			
            
            if(Input::get('deliver_fare_type')=='per_min')
            {
            	$validator = Validator::make($request->all(),
				['deliver_permin_charge' =>'required'],
				['deliver_permin_charge.required' =>$deli_permin_charge_req_err_msg]);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			
            /*Check edit delivery boy email exist or not*/
			$check_already_exsist = DB::table('gr_delivery_member')->where('deliver_email','=',$deliver_email)->where('deliver_status','!=','2')->where('deliver_id','!=',$delivery_boy_id)->count();
			/*Check edit delivery boy Phone no exist or not*/
			$check_phoneNumber_already_exsist = DB::table('gr_delivery_member')->where('deliver_phone1','=',$deliver_phone1)->where('deliver_status','!=','2')->where('deliver_id','!=',$delivery_boy_id)->count();	
			/*Validate email id*/
			if (!filter_var(Input::get('deliver_email'), FILTER_VALIDATE_EMAIL)) {
				$msge=MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($check_already_exsist > 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($check_phoneNumber_already_exsist > 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
			$passwordIs = rand_password();
			
			$data = array(
			'deliver_agent_id'       =>$agent->agent_id,
			'deliver_fname'	         =>Input::get('deliver_fname'),
			'deliver_lname'			 =>Input::get('deliver_lname'),
			'deliver_email'			 =>Input::get('deliver_email'),
			'deliver_phone1'         =>Input::get('deliver_phone1'),
			'deliver_phone2'         =>Input::get('deliver_phone2'),
			'deliver_response_time'  =>date('H:i:s',strtotime(Input::get('deliver_response_time'))),
			'deliver_base_fare'      =>Input::get('deliver_base_fare'),
			'deliver_fare_type'      =>Input::get('deliver_fare_type'),
			'deliver_perkm_charge'   =>Input::get('deliver_perkm_charge'),
			'deliver_permin_charge'  =>Input::get('deliver_permin_charge'),
			'deliver_availfrom'      =>Input::get('deliver_availfrom'),
			'deliver_order_limit'    =>Input::get('deliver_order_limit'),
			'deliver_read_status'    =>'0',
			'deliver_created_at'     => date('Y-m-d H:i:s'),
			'deliver_updated_at'     => date('Y-m-d H:i:s')
			);
			
			$res=updatevalues('gr_delivery_member',$data,['deliver_id'=>$delivery_boy_id]);
			if($res)
			{
				$lastinsertid = DB::getPdo()->lastInsertId();	
				
				$msge=MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
				$data = ["agent_id"         =>$agent->agent_id,
				"deliver_fname"    =>ucfirst($deliver_fname).' '.$deliver_lname,
				"deliver_email"    =>$deliver_email,
				"deliver_phone"	   =>$deliver_phone1,
				"deliver_id"	   =>$delivery_boy_id];
				$outputarray = array('code'			=>200, 
				'message'   =>$msge,
				'data'		=> $data
				);
				return Response::make(json_encode($outputarray,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		public function new_order_agent(Request $request){
			$lang = $request->lang; 
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			/*GET CUSTOMER DETAIL WITH LOCATION, ORDER ID, ORDER DATE, RESTAURANT/STORE NAME WITH LOCATION, VIEW INVOICE LINK, SCHEDULE, ACTION TO ACCEPT/REJECT.*/
			$st_store_name = ($lang == $admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$group_res = DB::table('gr_order')
			->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_transaction_id',
			'gr_order.ord_pay_type',
			DB::raw('SUM(gr_order.ord_grant_total) As order_amount'),
			'gr_order.ord_payment_status',
			'gr_store.id as storeId',
			'gr_store.'.$st_store_name.' as storeName',
			'gr_store.st_address',
			'gr_store.st_logo',
			'gr_store.st_type',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			'gr_store.st_delivery_time',
			'gr_store.st_delivery_duration',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname'
			)
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
			->where('ord_task_status','=','1')
			->where('ord_agent_id','=',$agent_id)
			->where('ord_agent_acpt_status','=','0')
			->where('ord_delivery_memid','=',NULL)// ord_delivery_memid IS NULL 
			->where('ord_status','>=','4')
			->where('ord_self_pickup','!=','1')
			->where('ord_cancel_status','=','0')
			->groupBy('ord_rest_id','ord_transaction_id')
			->orderBy('ord_taskassigned_date','desc')
			->get();
			if(count($group_res) > 0){
				$newOrderArray = array();
				foreach($group_res as $gres){
					
					/*WE WILL DISPLAY DELIVERY FEE AND WALLET AMOUNT FOR INDIVIDUAL STORE ORDER. IF ORDER HAS FAILED, DEL BOY CAN'T GET DELIVREY FEE AND REMAINING AMOUNT */
					$cal = Agent::individual_st_amt($gres->ord_transaction_id,$gres->order_amount);
					//echo $walletFee; echo $delFee; exit;
					$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
					$totalReceivable = $gres->order_amount-$explode[1]+$explode[0];
					if($gres->ord_pre_order_date!=NULL)
					{
						$schedule = date('m/d/Y H:i:s',strtotime($gres->ord_pre_order_date));
					}
					else {
						if($gres->st_delivery_duration=='hours'){
							$newtimestamp = date('Y-m-d H:i',strtotime('+'.$gres->st_delivery_time.' hour',strtotime($gres->ord_date)));
						}
						else {
							$newtimestamp = date('Y-m-d H:i',strtotime('+'.$gres->st_delivery_time.' minutes',strtotime($gres->ord_date)));
						}
						$schedule = date('m/d/Y H:i:s',strtotime($newtimestamp));
					}
					$st_logo = '';
					if($gres->st_type == '1') //restaurant image
					{
						$st_logo = MobileModel::get_image_restaurant($gres->st_logo,"logo");
					}
					elseif($gres->st_type == '2') //grocery image
					{
						$st_logo = MobileModel::get_image_store($gres->st_logo,"logo");
					}
					$newOrderArray[] = array('cusName'			=>$gres->ord_shipping_cus_name,
					'cusAddress'		=>$gres->ord_shipping_address,
					'cusAddress1'		=>$gres->ord_shipping_address1,
					'cusMobile1'		=>$gres->ord_shipping_mobile,
					'cusMobile2'		=>$gres->ord_shipping_mobile1,
					'cusEmail'			=>$gres->order_ship_mail,
					'cusLatitude'		=>$gres->order_ship_latitude,
					'cusLongitude'		=>$gres->order_ship_longitude,
					'orderId'			=>$gres->ord_transaction_id,
					'orderDate'		=>date('m/d/Y H:i:s',strtotime($gres->ord_date)),
					'orderSchedule'	=>$schedule,
					'orderPayType'		=>$gres->ord_pay_type,
					'orderAmount' 		=> $gres->order_amount,
					'delFee' 			=> number_format($explode[0],2),
					'walletFee' 		=> number_format($explode[1],2),
					'totalReceivableAmount'=> ($gres->ord_pay_type == "COD") ? number_format($totalReceivable,2) : "0.00",
					//'grandTotal'=>$orderAmount_cal,
					'storeId'			=>$gres->storeId,
					'storeLogo'		=>$st_logo,
					'storeName'		=>$gres->storeName,
					'storeAddress'		=>$gres->st_address,
					'storeLatitude'	=>$gres->st_latitude,
					'storeLongitude'	=>$gres->st_longitude,
					'storeDelivery_time'=>$gres->st_delivery_time,
					'storeDelivery_duration'=>$gres->st_delivery_duration,
					'preOrderDate'		=>$gres->ord_pre_order_date,
					'merchantName'		=>$gres->mer_fname.' '.$gres->mer_lname
					);
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['newOrderList'=>$newOrderArray];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
		}
		public function new_order_action(Request $request){
			$agent = JWTAuth::user();
			$agent_id = $agent->agent_id;
			$agent_name = $agent->agent_fname.' '.$agent->agent_lname;
            $lang = $request->lang; 
			$storeId	= Input::get('storeId');
            $orderId	= Input::get('orderId');
            $status		= Input::get('status');
            $reason		= Input::get('reason');
			
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');       
            $storeId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store/restaurant ID!');            
            $storeId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
            $orderStatus_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_AXPTRJCT_STATUS','Please enter valid status! It should be 1 or 2');
            $reason_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_REJECT_REASON','Please enter reject reason!');
			
			
            $validator = Validator::make($request->all(),[ 'storeId'	=> 'required|integer',
			'orderId'	=> 'required',
			'status'	=> 'required|integer',
			'reason'	=> 'required_if:status,2' 
			],[ 'storeId.required'	=> $storeId_req_err_msg,
			'storeId.integer'	=> $storeId_valid_err_msg,
			'orderId.required'	=> $orderId_req_err_msg,
			//'orderId.integer'	=> $orderId_valid_err_msg,
			'status.required'	=> $orderStatus_valid_err_msg,
			'status.integer'	=> $orderStatus_valid_err_msg,
			'reason.required_if'=> $reason_req_err_msg
			] 
			);
            if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$mer_id  = '';
			$get_store_exists = DB::table('gr_store')->select('st_mer_id')->where('id','=',$storeId)->where('st_status','=','1')->first();
			if(empty($get_store_exists)==true){
				return Response::make(json_encode(array('code'=>400,"message"=>$storeId_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$mer_id = $get_store_exists->st_mer_id;
			}
			$check_orderId_exist = DB::table('gr_order')->where('ord_transaction_id','=',$orderId)->count();
			if($check_orderId_exist <= 0 ){
				return Response::make(json_encode(array('code'=>400,"message"=>$orderId_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($status<='0' || $status> '2'){
				return Response::make(json_encode(array('code'=>400,"message"=>$orderStatus_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$msg_status = $message_link = $suxes_msg = '';
			if($status=='1'){ 
				$insertArr = array('ord_agent_acpt_status' => '1');
				$msg_status = MobileModel::get_lang_text($lang,'API_ACCEPTS','accepts');
				$message_link = 'delivery-track-order/'.base64_encode($orderId).'/'.base64_encode($mer_id);
				$suxes_msg = MobileModel::get_lang_text($lang,'API_OR_ACCEPT','Order has been accepted');
			}
			else { 
				//$insertArr = array('ord_agent_acpt_status' => '2','ord_task_status' => '0','ord_agent_id'=>NULL,'ord_delmgr_id'=>'0','ord_taskassigned_date'=>NULL);
				$insertArr = array('ord_agent_acpt_status' => '2','ord_agent_rjct_reason'=>$reason,'ord_agent_rejected_at'=>date('Y-m-d H:i:s'));
				$data = array(
				'store_id'		=> $storeId,
				'order_id'		=> $orderId,
				'agent_id'		=> $agent_id,
				'reason'		=> $reason,
				'rejected_at'	=> date('Y-m-d H:i:s')
				);
				$res=insertvalues('gr_order_reject_history',$data);
				$msg_status = MobileModel::get_lang_text($lang,'API_REJECTS','rejects');
				$message_link = 'delivery-track-order/'.base64_encode($orderId).'/'.base64_encode($mer_id);
				$suxes_msg = MobileModel::get_lang_text($lang,'API_OR_REJECT','Order has been rejected');
			}
			$update = updatevalues('gr_order',$insertArr,['ord_rest_id'=>$storeId,'ord_transaction_id'=>$orderId]);
			/* send notification to delivery manager */
			$details = Agent::get_dm_details($orderId,$storeId,$lang);
			if(empty($details) === false)
			{
				$got_message = MobileModel::get_lang_text($lang,'API_AGENT_CH_STATUS',':agent_name :status the order (:transaction_id) in :store_name');
				$searchReplaceArray = array(':agent_name' => ucfirst($agent_name),':store_name' => $details->s_name,':transaction_id' => $orderId,':status' => $msg_status);
				$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
				push_notification($agent_id,$details->ord_delmgr_id,'gr_agent','gr_delivery_manager',$result,$orderId,$message_link);	
			}
			/* send notification to delivery manager ends */
			return Response::make(json_encode(array('code'=>200,"message"=>$suxes_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		public function order_management(Request $request){
			$lang = $request->lang; 
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			$from_date = '';
			$to_date   = '';
			$page_no = $request->page_no;
			$ord_status = $request->order_status;
			$msg = MobileModel::get_lang_text($lang,'API_FILL_VA_DATE','Fill date in valid fomat(Y-m-d)');
			if($request->from_date!='')
			{
				$validator = Validator::make($request->all(),
				[ 'from_date'	=> 'required|date|date_format:Y-m-d'],
				[
				'from_date.date' => $msg,
				'from_date.date_format' => $msg]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				$from_date = date("Y-m-d", strtotime($request->from_date));
			}
			if($request->to_date!=''){
				$validator = Validator::make($request->all(),
				[ 'to_date'	=> 'required|date|date_format:Y-m-d'],
				[
				'to_date.date' => $msg,
				'to_date.date_format' => $msg]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				$to_date   = date("Y-m-d", strtotime($request->to_date));
			}
           	$route = \Request::segment(3);
           	$search_id = '';
           	if($route == "agent_delivered_orders")
           	{
           		$ord_status  = '8';
           		$search_id   = $request->search_text;
			}
			
			/*GET CUSTOMER DETAIL WITH LOCATION, ORDER ID, ORDER DATE, RESTAURANT/STORE NAME WITH LOCATION, VIEW INVOICE LINK, SCHEDULE, ACTION TO ACCEPT/REJECT.*/
			$st_store_name = ($lang == $admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$sql = DB::table('gr_order')
			->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.ord_cus_id',
			'gr_order.ord_status',
			'gr_order.ord_pay_type',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_delivered_on',
			'gr_order.ord_transaction_id',
			DB::raw('SUM(gr_order.ord_grant_total) As order_amount'),
			'gr_order.ord_delmgr_id',
			'gr_store.id as storeId',
			'gr_store.'.$st_store_name.' as storeName',
			'gr_store.st_address',
			'gr_store.st_logo',
			'gr_store.st_type',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			'gr_store.st_delivery_time',
			'gr_store.st_delivery_duration',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname'
			)
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
			->where('ord_task_status','=','1')
			->where('ord_agent_id','=',$agent_id)
			->where('ord_agent_acpt_status','=','1')
			->where('ord_cancel_status','=','0')
			//->where('ord_delivery_memid','!=',NULL)// ord_delivery_memid IS NULL 
			->where('ord_status','>=','4')
			->where('ord_self_pickup','!=','1')
			->orderBy('ord_agent_accept_on','desc');
			if ($from_date != '')
			{
				$sql->whereDate('gr_order.ord_date', '>=' , $from_date);
			}
			if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to_date);
			}
			if($ord_status != '') {
				if($ord_status <= 8)
				{
					$sql->where('gr_order.ord_status', '=' , $ord_status);
				}
			}
			if($search_id != '')
			{
				$sql->where('gr_order.ord_transaction_id', '=' , $search_id);
			}
			$sql->groupBy('ord_rest_id','ord_transaction_id');
			$group_res=$sql->paginate(10,['*'],'order_page',$page_no);
			
			if(count($group_res) > 0){
				$newOrderArray = array();
				foreach($group_res as $gres)
				{
					/*WE WILL DISPLAY DELIVERY FEE AND WALLET AMOUNT FOR INDIVIDUAL STORE ORDER. IF ORDER HAS FAILED, DEL BOY CAN'T GET DELIVREY FEE AND REMAINING AMOUNT */
					$cal = Agent::individual_st_amt($gres->ord_transaction_id,$gres->order_amount);
					//echo $walletFee; echo $delFee; exit;
					$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
					$totalReceivable = $gres->order_amount-$explode[1]+$explode[0];
					if($gres->ord_pre_order_date!=NULL)
					{
						$schedule = date('m/d/Y H:i:s',strtotime($gres->ord_pre_order_date));
					}
					else {
						if($gres->st_delivery_duration=='hours'){
							$newtimestamp = date('Y-m-d H:i',strtotime('+'.$gres->st_delivery_time.' hour',strtotime($gres->ord_date)));
						}
						else {
							$newtimestamp = date('Y-m-d H:i',strtotime('+'.$gres->st_delivery_time.' minutes',strtotime($gres->ord_date)));
						}
						$schedule = date('m/d/Y H:i:s',strtotime($newtimestamp));
					}
					$agent_details = Delivery_person::get_basic_details('gr_delivery_manager',['dm_id' => $gres->ord_delmgr_id],'dm_cust_data_protect');
					$arr = array();	
					if($ord_status == '8' && (empty($agent_details) === false))
					{
						if($agent_details->dm_cust_data_protect == '0')	/* if customer data protection in enable, no need to show customer address */
						{
							$arr['cusId']		= $gres->ord_cus_id;
							$arr['cusName']		= $gres->ord_shipping_cus_name;
							$arr['cusAddress']	= $gres->ord_shipping_address;
							$arr['cusAddress1']	= $gres->ord_shipping_address1;
							$arr['cusMobile1']	= $gres->ord_shipping_mobile;
							$arr['cusMobile2']	= $gres->ord_shipping_mobile1;
							$arr['cusEmail']	= $gres->order_ship_mail;
							$arr['cusLatitude']	= $gres->order_ship_latitude;
							$arr['cusLongitude']= $gres->order_ship_longitude;
						}
						$arr['ord_delivered_on'] = $gres->ord_delivered_on;
					}
					else
					{
						$arr['cusId']		= $gres->ord_cus_id;
						$arr['cusName']		= $gres->ord_shipping_cus_name;
						$arr['cusAddress']	= $gres->ord_shipping_address;
						$arr['cusMobile1']	= $gres->ord_shipping_mobile;
						$arr['cusMobile2']	= $gres->ord_shipping_mobile1;
						$arr['cusEmail']	= $gres->order_ship_mail;
						$arr['cusLatitude']	= $gres->order_ship_latitude;
						$arr['cusLongitude']= $gres->order_ship_longitude;
					}
					
					$text = '';
					switch($gres->ord_status)
					{
						case 4:
						$text = MobileModel::get_lang_text($lang,'API_TO_DELIVER','Preparing to delivery');
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
						$text = MobileModel::get_lang_text($lang,'API_DISPATCH','Dispatched');
					}
					$st_logo = '';
					if($gres->st_type == '1') //restaurant image
					{
						$st_logo = MobileModel::get_image_restaurant($gres->st_logo,"logo");
					}
					elseif($gres->st_type == '2') //grocery image
					{
						$st_logo = MobileModel::get_image_store($gres->st_logo,"logo");
					}
					$newOrderArray[] = array_merge($arr,array('orderId'=>$gres->ord_transaction_id,
					'orderDate'		=>date('m/d/Y H:i:s',strtotime($gres->ord_date)),
					'orderSchedule'	=>$schedule,
					'payType'			=>$gres->ord_pay_type,
					'orderStatus'		=>$text,
					'totalReceivableAmount'	=> ($gres->ord_pay_type == "COD") ? number_format($totalReceivable,2) : "0.00",
					'storeId'			=>$gres->storeId,
					'storeName'		=>$gres->storeName,
					'storeLogo'		=>$st_logo,
					'storeAddress'		=>$gres->st_address,
					'storeLatitude'	=>$gres->st_latitude,
					'storeLongitude'	=>$gres->st_longitude,
					'storeDelivery_time'=>$gres->st_delivery_time,
					'storeDelivery_duration'=>$gres->st_delivery_duration,
					'preOrderDate'		=>$gres->ord_pre_order_date,
					'merchantName'		=>$gres->mer_fname.' '.$gres->mer_lname
					));
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['newOrderList'=>$newOrderArray];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,
				'data'=>$data
				),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		
		/************************* GET DELIVERY BOY LIST WITH DETAILS BY CHECKING WORKING HOURS AND AVAILABLE ****************/
		public function agent_get_deliveryboy_list(Request $request){
			$lang = $request->lang; 
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			$current_time = date('H:i');
			$current_day=date('l');
			//$page_no
			//$reassign = $request->reassign;
			//DB::connection()->enableQueryLog();
			$group_res = DB::table('gr_delivery_member')
			->select('gr_delivery_member.deliver_id',
			'gr_delivery_member.deliver_fname',
			'gr_delivery_member.deliver_lname',
			'gr_delivery_member.deliver_email',
			'gr_delivery_member.deliver_phone1',
			'gr_delivery_member.deliver_phone2',
			'gr_delivery_member.deliver_location',
			'gr_delivery_member.deliver_currency_code',
			'gr_delivery_member.deliver_base_fare',
			'gr_delivery_member.deliver_fare_type',
			'gr_delivery_member.deliver_perkm_charge',
			'gr_delivery_member.deliver_permin_charge',
			'gr_delivery_member.deliver_vehicle_details',
			'gr_delivery_member.deliver_order_limit',
			DB::Raw("IF((SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=gr_delivery_member.deliver_id AND `dw_date`='$current_day' AND dw_status='1' AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC('$current_time') AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC('$current_time'))>0,'Avail','Busy') as availability")							
			)
			//SELECT * FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`='12' AND `dw_date`='Wednesday' AND dw_status='1' AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC('09:01') AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC('09:59')
			->where('deliver_status','=','1')
			->where('deliver_agent_id','=',$agent_id)
			->where('deliver_avail_status','=','1')
			->whereRaw('(SELECT count(*) FROM gr_order WHERE ord_status>=5 AND ord_status<8 AND ord_delivery_memid=gr_delivery_member.deliver_id)<gr_delivery_member.deliver_order_limit')
			->get();
			/*$query = DB::getQueryLog(); 
				print_r($query);
			exit; */
			//print_r($group_res);
			if(count($group_res) > 0 ){
				$delBoyArray = array();
				foreach($group_res as $gres){
					if($gres->availability=='Avail'){
						$delBoyArray[] = array( 'delBoyId'			=>$gres->deliver_id,
						'delBoyName'		=>$gres->deliver_fname.' '.$gres->deliver_lname,
						'delBoyAddress'	=>$gres->deliver_location,
						'delBoyMobile1'	=>$gres->deliver_phone1,
						'delBoyMobile2'	=>$gres->deliver_phone2,
						'delBoyEmail'		=>$gres->deliver_email,
						'delBoyCurrency'	=>$gres->deliver_currency_code,
						'delBoyBaseFare'	=>$gres->deliver_base_fare,
						'delBoyFareType'	=>$gres->deliver_fare_type,
						'delBoyPerKMCharge'=>$gres->deliver_perkm_charge,
						'delBoyPerMinCharge'=>($gres->deliver_permin_charge == '') ? '' : $gres->deliver_permin_charge,
						'delBoyVehicle'	=>$gres->deliver_vehicle_details,
						'delBoyOrderLimit'	=>$gres->deliver_order_limit
						);
					}
				}
				if(count($delBoyArray) > 0 ){
					$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
					$data = ['delBoyArray'=>$delBoyArray];
					return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data'=>$data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				else {
					$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
					return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			else {
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		/*ASSIGN ORDER TO DELIVERY BOY*/
		public function assign_order(Request $request){
			$agent = JWTAuth::user();
			$agent_id = $agent->agent_id;
            $lang = $request->lang; 
			$storeId	= Input::get('storeId');
            $orderId	= Input::get('orderId');
            $delBoyId	= Input::get('delBoyId');
			
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');       
            $storeId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store/restaurant ID!');            
            $storeId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
			$delboyId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_DELBOY_ID','Please enter delivery boy ID!');            
            $delboyId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_DELBOY_ID','Please enter valid delivery boy ID!');
			
			
            $validator = Validator::make($request->all(),[ 'storeId'	=> 'required|integer',
			'orderId'	=> 'required',
			'delBoyId'	=> 'required|integer'
			],[ 'storeId.required'	=> $storeId_req_err_msg,
			'storeId.integer'	=> $storeId_valid_err_msg,
			'orderId.required'	=> $orderId_req_err_msg,
			//'orderId.integer'	=> $orderId_valid_err_msg,
			'delBoyId.required'	=> $delboyId_req_err_msg,
			'delBoyId.integer'	=> $delboyId_valid_err_msg
			] 
			);
            if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$get_store_exists = DB::table('gr_store')->where('id','=',$storeId)->where('st_status','=','1')->first();
			if(empty($get_store_exists)==true)
			{
				return Response::make(json_encode(array('code'=>400,"message"=>$storeId_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$check_orderId_exist = DB::table('gr_order')->where('ord_transaction_id','=',$orderId)->where('ord_rest_id','=',$storeId)->where('ord_agent_id','=',$agent_id)->count();
			if($check_orderId_exist <= 0 )
			{
				return Response::make(json_encode(array('code'=>400,"message"=>$orderId_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$check_delBoyId_exist = DB::table('gr_delivery_member')->where('deliver_id','=',$delBoyId)->where('deliver_agent_id','=',$agent_id)->count();
			if($check_delBoyId_exist <= 0 )
			{
				return Response::make(json_encode(array('code'=>400,"message"=>$delboyId_valid_err_msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$insertArr = array('ord_delivery_memid'		=>$delBoyId,
			'ord_delboy_act_status' => 0,
			'ord_delboy_assigned_on' => date('Y-m-d H:i:s')
			);
			updatevalues('gr_order',$insertArr,['ord_rest_id'=>$storeId,'ord_transaction_id'=>$orderId]);
			/* send notification to delivery boy */
			$got_message = MobileModel::get_lang_text($lang,'API_OR_ASSIGN','Order(:transaction_id) has been assigned');
			$searchReplaceArray = array(':transaction_id' => $orderId);
			$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);			 
			//1)delivery boy
			push_notification($agent_id,$delBoyId,'gr_agent','gr_delivery_member',$result,$orderId,'');
			/* send notification ends */
			$msg = MobileModel::get_lang_text($lang,'API_OR_ASSI_SUX','Order assigned successfully!');;
			return Response::make(json_encode(array('code'=>200,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		/* REJECTED ORDER */
		public function rejected_order_bydelBoy(Request $request){
			$lang = $request->lang; 
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			/*GET CUSTOMER DETAIL WITH LOCATION, ORDER ID, ORDER DATE, RESTAURANT/STORE NAME WITH LOCATION, VIEW INVOICE LINK, SCHEDULE, ACTION TO ACCEPT/REJECT.*/
			$st_store_name = ($lang == $admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$group_res = DB::table('gr_order')
			->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_transaction_id',
			'gr_order.ord_delboy_rjct_reason',
			'gr_order.ord_delboy_rjct_time',
			'gr_store.id as storeId',
			'gr_store.'.$st_store_name.' as storeName',
			'gr_store.st_address',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			'gr_store.st_delivery_time',
			'gr_store.st_delivery_duration',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_delivery_member.deliver_id',
			'gr_delivery_member.deliver_fname',
			'gr_delivery_member.deliver_lname',
			'gr_delivery_member.deliver_email',
			'gr_delivery_member.deliver_phone1',
			'gr_delivery_member.deliver_phone2'
			)
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
			->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_order.ord_delivery_memid')
			->where('ord_task_status','=','1')
			->where('ord_agent_id','=',$agent_id)
			->where('ord_agent_acpt_status','=','1')
			->where('ord_delboy_act_status','=','2')
			//->where('ord_delivery_memid','!=',NULL)// ord_delivery_memid IS NULL 
			->where('ord_status','>=','4')
			->where('ord_self_pickup','!=','1')
			->groupBy('ord_rest_id','ord_transaction_id')
			->get();
			if(count($group_res) > 0){
				$newOrderArray = array();
				foreach($group_res as $gres){
					if($gres->ord_pre_order_date!=NULL)
					{
						$schedule = date('m/d/Y H:i:s',strtotime($gres->ord_pre_order_date));
					}
					else {
						if($gres->st_delivery_duration=='hours'){
							$newtimestamp = date('Y-m-d H:i',strtotime('+'.$gres->st_delivery_time.' hour',strtotime($gres->ord_date)));
						}
						else {
							$newtimestamp = date('Y-m-d H:i',strtotime('+'.$gres->st_delivery_time.' minutes',strtotime($gres->ord_date)));
						}
						$schedule = date('m/d/Y H:i:s',strtotime($newtimestamp));
					}
					$newOrderArray[] = array('cusName'			=>$gres->ord_shipping_cus_name,
					'cusAddress'		=>$gres->ord_shipping_address,
					'cusAddress1'		=>$gres->ord_shipping_address1,
					'cusMobile1'		=>$gres->ord_shipping_mobile,
					'cusMobile2'		=>$gres->ord_shipping_mobile1,
					'cusEmail'			=>$gres->order_ship_mail,
					'cusLatitude'		=>$gres->order_ship_latitude,
					'cusLongitude'		=>$gres->order_ship_longitude,
					'orderId'			=>$gres->ord_transaction_id,
					'orderDate'		=>date('m/d/Y H:i:s',strtotime($gres->ord_date)),
					'orderSchedule'	=>$schedule,
					'storeId'			=>$gres->storeId,
					'storeName'		=>$gres->storeName,
					'storeAddress'		=>$gres->st_address,
					'storeLatitude'	=>$gres->st_latitude,
					'storeLongitude'	=>$gres->st_longitude,
					'storeDelivery_time'=>$gres->st_delivery_time,
					'storeDelivery_duration'=>$gres->st_delivery_duration,
					'preOrderDate'		=>$gres->ord_pre_order_date,
					'merchantName'		=>$gres->mer_fname.' '.$gres->mer_lname,
					'deliver_id'		=>$gres->deliver_id,
					'delBoyName'		=>$gres->deliver_fname.' '.$gres->deliver_lname,
					'delBoyEmail'		=>$gres->deliver_email,
					'delBoyMobile1'	=>$gres->deliver_phone1,
					'delBoyMobile2'	=>$gres->deliver_phone2,
					'delBoyRejectedReason'=>$gres->ord_delboy_rjct_reason,
					'delBoyRejectedAt'	=>date('m/d/Y H:i:s',strtotime($gres->ord_delboy_rjct_time))
					
					);
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['newOrderList'=>$newOrderArray];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data'=>$data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		public function agent_get_deliveryboy_reassign(Request $request){
			$lang 			= $request->lang; 
			$agent	 		= JWTAuth::user();
			$agent_id		= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			$orderId 		= $request->orderId;
			$storeId 		= $request->storeId;
			$current_time 	= date('H:i');
			$current_day	= date('l');
			$page_no 		= $request->page_no;
			$delBoys =  DB::table('gr_order_reject_history') ->where('store_id', $storeId)->where('order_id',$orderId)->where('delboy_id','!=','0')->where('agent_id','=','0')->get();
			$delBoyListArray = array();
			if(count($delBoys) > 0 ){
				foreach($delBoys as $delBoy){
					array_push($delBoyListArray,$delBoy->delboy_id);
				}
			}
			$sql = DB::table('gr_delivery_member')
			->select('gr_delivery_member.deliver_id',
			'gr_delivery_member.deliver_fname',
			'gr_delivery_member.deliver_lname',
			'gr_delivery_member.deliver_email',
			'gr_delivery_member.deliver_phone1',
			'gr_delivery_member.deliver_phone2',
			'gr_delivery_member.deliver_location',
			'gr_delivery_member.deliver_currency_code',
			'gr_delivery_member.deliver_base_fare',
			'gr_delivery_member.deliver_fare_type',
			'gr_delivery_member.deliver_perkm_charge',
			'gr_delivery_member.deliver_permin_charge',
			'gr_delivery_member.deliver_vehicle_details',
			'gr_delivery_member.deliver_order_limit',
			DB::Raw("IF((SELECT count(*) FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`=gr_delivery_member.deliver_id AND `dw_date`='$current_day' AND dw_status='1' AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC('$current_time') AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC('$current_time'))>0,'Avail','Busy') as availability")
			)
			//SELECT * FROM `gr_deliver_working_hrs` WHERE `dw_deliver_id`='12' AND `dw_date`='Wednesday' AND dw_status='1' AND TIME_TO_SEC(`dw_from`) <= TIME_TO_SEC('09:01') AND TIME_TO_SEC(`dw_to`) >= TIME_TO_SEC('09:59')
			->where('deliver_status','=','1');
			if(count($delBoyListArray) > 0)
			{
				$q = $sql->whereNotIn('deliver_id',$delBoyListArray);
			}
			$q = $sql->where('deliver_agent_id','=',$agent_id)->where('deliver_avail_status','=','1');
			$group_res = $sql->paginate(10,['*'],'delboy_page',$page_no);
			
			/*$query = DB::getQueryLog(); 
				print_r($query);
			exit; */
			//print_r($group_res);
			if(count($group_res) > 0 ){
				$delBoyArray = array();
				foreach($group_res as $gres){
					if($gres->availability=='Avail'){
						$delBoyArray[] = array( 'delBoyId'			=>$gres->deliver_id,
						'delBoyName'		=>$gres->deliver_fname.' '.$gres->deliver_lname,
						'delBoyAddress'		=>$gres->deliver_location,
						'delBoyMobile1'		=>$gres->deliver_phone1,
						'delBoyMobile2'		=>$gres->deliver_phone2,
						'delBoyEmail'		=>$gres->deliver_email,
						'delBoyCurrency'	=>$gres->deliver_currency_code,
						'delBoyBaseFare'	=>$gres->deliver_base_fare,
						'delBoyFareType'	=>$gres->deliver_fare_type,
						'delBoyPerKMCharge'=>$gres->deliver_perkm_charge,
						'delBoyPerMinCharge'=>$gres->deliver_permin_charge,
						'delBoyVehicle'		=>$gres->deliver_vehicle_details,
						'delBoyOrderLimit'	=>$gres->deliver_order_limit
						);
					}
				}
				if(count($delBoyArray) > 0 ){
					$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
					$data = ['delBoyArray'=>$delBoyArray];
					return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data'=>$data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				else {
					$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
					return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			else {
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		/* dashboard */
		public function dashboard(Request $request)
		{
			$lang 			= $request->lang;
			$details	 	= JWTAuth::user();
			$agent_id		= $details->agent_id;
			$total_orders 	= Agent::get_orders_count($agent_id);
			$new_orders 	= Agent::get_orders_count($agent_id,'0','4');
			//print_r($new_orders); exit;
			$pending_orders = Agent::get_orders_count($agent_id,'1','',[4,5,6,7]);
			$delivered_orders = Agent::get_orders_count($agent_id,'1','8');
			/*return response()->json(array('status' => 200,'message' => 'Details available',
				'total_orders'	=> $total_orders,
				'new_orders'	=> $new_orders,
				'processing_orders'	=> $pending_orders,
			'delivered_orders'	=> $delivered_orders));*/
			$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
			$data = ['total_orders'		=> $total_orders,
			'new_orders' 		=> $new_orders,
			'processing_orders'	=> $pending_orders, 
			'delivered_orders'	=> $delivered_orders];
			return Response::make(json_encode(array('code' => 200,'message' =>$msge,'data' => $data
			),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}	
		public function agent_details(){
			$user = JWTAuth::user();
			$agent_array = array("agent_id"		=>intval($user->agent_id),
			"agent_name"	=>ucfirst($user->agent_fname).' '.$user->agent_lname,
			"agent_email"	=>$user->agent_email,
			"agent_phone"	=>$user->agent_phone1,
			"agent_status"	=>intval($user->agent_status)
			);
			return $agent_array;
		}
		
		public function earning_report(Request $request){
			$lang = $request->lang; 
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$agent_curr	= $agent->agent_currency_code;
			$admin_default_lang = $this->admin_default_lang;
			
			$from_date = '';
			$to_date   = '';
			$validator = Validator::make($request->all(),['from_date'=> 'sometimes|nullable|date|date_format:Y-m-d']);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($request->from_date!='')
			{
				$from_date = date("Y-m-d", strtotime($request->from_date));
				$from_minus_date=date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $from_date) ) ));
			}
			if($request->to_date!='')
			{
				//after:'.$opportunity->created_at;
				$validator = Validator::make($request->all(),[ 'to_date'=> 'sometimes|nullable|date|date_format:Y-m-d|after:'.$from_minus_date.'']);
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				$to_date   = date("Y-m-d", strtotime($request->to_date));
			}
			$get_total_earnings = Agent::earning_report($agent_id);
			$grand_order = 0;
			$grand_commission   = 0;
			if(count($get_total_earnings) > 0 )
			{
				foreach($get_total_earnings as $earnings)
				{
					$grand_commission += $earnings->ae_total_amount;
					$grand_order += $earnings->ae_order_total;
				}
			}
			//DB::connection()->enableQueryLog();
			
            $get_earning_details = Agent::earning_report($agent_id,$from_date,$to_date);
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			if(count($get_earning_details) <= 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$msg_delivered 		= MobileModel::get_lang_text($lang,'API_DELIVER','Delivered!');
				$msg_delivery_failed= MobileModel::get_lang_text($lang,'API_FAIL','Delivery Failed!');
				$page_total_commission = 0.00;
				foreach($get_earning_details as $details)
				{
					$arr[] = ['order_delivered_date' 	=> $details->ae_updated_at,
					'order_id' 				=> $details->ae_transaction_id,
					'order_currency'			=> $details->ae_ord_currency,
					'commission_amt'			=> $details->ae_total_amount,
					'order_amt'				=> $details->ae_order_total,
					'order_delivered_status'	=> ($details->ae_ord_status==1)?$msg_delivered:$msg_delivery_failed
					];
					$page_total_commission +=$details->ae_total_amount;
					
				}
				
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['reportArray'			=> $arr,
				'currency_code'		=> $agent_curr,
				'page_commission'		=> number_format($page_total_commission,2),
				'grand_commission_total'=> number_format($grand_commission,2),
				'grand_order_total'		=> number_format($grand_order,2),
				];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
		}
		public function commission_tracking(Request $request){
			$lang = $request->lang;
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			$page_no = $request->page_no;
			$pagenum_valid_err_msg = MobileModel::get_lang_text($lang,'API_PAGE_NUM_RULES','Page number should be a number!');
			$validator = Validator::make($request->all(),['page_no'=>'sometimes|nullable|integer'],['page_no.required' => $pagenum_valid_err_msg]); 
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$sql = 	DB::table('gr_agent_earnings')
			->select('ae_agent_id',
			DB::Raw('CONCAT(if(gr_agent.agent_fname is null,'',gr_agent.agent_fname)," ",if(gr_agent.agent_lname is null,'',gr_agent.agent_lname)) AS agent_name'),
			'gr_agent.agent_email',
			DB::Raw('COUNT(*) AS total_orders'),
			DB::Raw('SUM(ae_order_total) AS total_order_amt'),
			DB::Raw('SUM(ae_rcd_amt) AS total_online_amt'),
			DB::Raw('SUM(gr_agent_earnings.ae_total_amount) AS total_commission_amt'),
			//DB::Raw('SUM(Case When ae_pay_type="COD" Then ae_order_total Else 0 End) AS total_order_amt'),
			DB::Raw('(SELECT SUM(commission_paid) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_rcvd_amt'),
			DB::Raw('(SELECT SUM(amount_received) FROM gr_agent_commission WHERE agent_id = gr_agent_earnings.ae_agent_id AND commission_status="2") AS total_paid_amt'),
			'ae_ord_currency',
			'gr_agent.mer_paymaya_clientid',
			'gr_agent.mer_paymaya_secretid',
			'gr_agent.mer_paymaya_status'
			);
			
			$q = $sql->leftJoin('gr_agent','gr_agent.agent_id','=','gr_agent_earnings.ae_agent_id');
			$q = $sql->where('gr_agent.agent_status','<>','2');
			$q = $sql->where('gr_agent_earnings.ae_agent_id','=',$agent_id);
			$posts =  $q->paginate(10,['*'],'commission_list',$page_no);
			$commission_list = array();
			if(count($posts) > 0 ){
				$admin_payment_settings = DB::table('gr_payment_setting')->first();
				if(empty($admin_payment_settings) === false){
					$paymaya_status = $admin_payment_settings->paymaya_status;
					$paymaya_client_id = $admin_payment_settings->paymaya_client_id;
					$paymaya_secret_id = $admin_payment_settings->paymaya_secret_id;
				}
				else {
					$paymaya_status = '';
					$paymaya_client_id = '';
					$paymaya_secret_id = '';
				}
				foreach ($posts as $post)
				{
					$nestedData = array();
					if($post->total_paid_amt > 0)
					{
						$view_transaxn = '1';
					}
					else
					{
						$view_transaxn = '0';
					}
					//$nestedData['agentName'] = $post->agent_name;
					//$nestedData['agentEmail'] = $post->agent_email;
					$nestedData['agentId'] 		 = $post->ae_agent_id;
					$nestedData['totalOrders']	 = $post->total_orders;
					$nestedData['totalOrderAmt'] = number_format($post->total_order_amt,2);
					$nestedData['totalRcvdAmt']  = number_format($post->total_rcvd_amt,2);
					//$nestedData['totalRcvdAmtOnline'] = number_format($post->total_online_amt,2);
					$nestedData['totComisonAmt'] = number_format($post->total_commission_amt,2);
					$nestedData['paidAmt'] 		 = number_format($post->total_paid_amt,2);
					$nestedData['receivedAmt'] 	 = number_format($post->total_rcvd_amt,2);
					//START
					$step1 = $post->total_order_amt-$post->total_paid_amt;
					$step2 = $post->total_commission_amt-$post->total_rcvd_amt;
					$balAmtToPay = number_format($step1-$step2,2);
					//END
					//$balAmtToPay = ($post->total_order_amt-$post->total_paid_amt)-($post->total_commission_amt-$post->total_rcvd_amt);
					$nestedData['balAmtToReceive'] = ($balAmtToPay < 0) ? number_format(abs($balAmtToPay),2) : '0';
					$nestedData['balAmtToPay'] = ($balAmtToPay > 0) ? number_format(abs($balAmtToPay),2) : '0';
					$nestedData['viewTransaxn'] = $view_transaxn;
					$commission_list[] = $nestedData;
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['commission_list' 	=>$commission_list,
				'paymaya_status'	=>$paymaya_status,
				'paymaya_client_id'	=>$paymaya_client_id,
				'paymaya_secret_id'	=>$paymaya_secret_id];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,
				'data'	=> $data
				),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else {
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		public function commission_transaxn(Request $request){
			$lang 		= $request->lang;
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$admin_default_lang = $this->admin_default_lang;
			$from_date  = $request->from_date;
			$to_date	= $request->to_date;
			$page_no 	= $request->page_no;
			
			$validator = Validator::make($request->all(),['from_date'=> 'sometimes|nullable|date|date_format:Y-m-d']);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			if($request->from_date!='')
			{
				$from_date = date("Y-m-d", strtotime($request->from_date));
				$from_minus_date=date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $from_date) ) ));
			}
			if($request->to_date!='')
			{
				//after:'.$opportunity->created_at;
				$validator = Validator::make($request->all(),[ 'to_date'=> 'sometimes|nullable|date|date_format:Y-m-d|after:'.$from_minus_date.'']);
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				$to_date   = date("Y-m-d", strtotime($request->to_date));
			}
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			$sql = DB::table('gr_agent_commission')
			->select(
			'gr_agent_commission.amount_received',
			'gr_agent_commission.commission_paid',
			'gr_agent_commission.transaction_id',
			'gr_agent_commission.pay_type',
			'gr_agent_commission.commission_date',
			'gr_agent_commission.commission_currency'
			)
			->where('gr_agent_commission.agent_id','=',$agent_id)
			->where('gr_agent_commission.commission_status','=','2')
			->orderby('gr_agent_commission.commission_date','DESC');
			if($from_date!='')
            {
                $commission_qry = $sql->whereRaw('DATE(commission_date) >= "'.$from_date.'"');
			}
            if($to_date!='')
            {
                $commission_qry = $sql->whereRaw('DATE(commission_date) <= "'.$to_date.'"');
			}
			$commission_qry = $sql->paginate(10,['*'],'commission_transaction',$page_no);
			
			$commission_list = array();
			if(count($commission_qry) > 0){
				foreach($commission_qry as $comlist){
					$nestedData = array();
					$nestedData['transaction_id'] = $comlist->transaction_id;
					if($comlist->pay_type=='0') { 
						$nestedData['pay_type'] = MobileModel::get_lang_text($lang,'API_NETBANKING','Net Banking');
						}elseif($comlist->pay_type=='1') {
						$nestedData['pay_type'] = MobileModel::get_lang_text($lang,'API_PAYMAYA','Paymaya');
						}elseif($comlist->pay_type=='2') {
						$nestedData['pay_type'] = MobileModel::get_lang_text($lang,'API_PAYNAMICS','Paynamics');
					}
					$nestedData['commission_received'] = number_format($comlist->commission_paid,2);
					$nestedData['paid_order_amount'] = number_format($comlist->amount_received,2);
					if($comlist->commission_date!='0000-00-00 00:00:00') {
						$nestedData['paid_date'] = date('m/d/Y H:i:s',strtotime($comlist->commission_date));
						}else{
						$nestedData['paid_date'] = '-';
					}
					$commission_list[] = $nestedData;
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['transaction_list'=>$commission_list];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				
			}
			else{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		public function agent_pay_request(Request $request){
			$lang 			= $request->lang;
			$agent	 		= JWTAuth::user();
			
			$agent_id		= $agent->agent_id;
			$agent_fname 	= $agent->agent_fname;
			$agent_lname 	= $agent->agent_lname;
			$amount = $request->amount;
			$amount_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_AMOUNT','Please enter amount');
			$amount_valid_err_msg = MobileModel::get_lang_text($lang,'API_INVALID_AMT','Invalid Amount');
			$validator = Validator::make($request->all(),['amount'=>'required|numeric'],
			['amount.required' => $amount_req_err_msg,
			'amount.numeric'=>$amount_valid_err_msg]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			/* -------------- GET ALL DELIVERY MANAGER ID, NAME AND EMAIL -------------*/
			$delmgr_qry = DB::table('gr_delivery_manager')->select('dm_name','dm_email','dm_id')->where('dm_status','=','1')->get();
			$delmgrArray = array();
			if(count($delmgr_qry) > 0 ){
				foreach($delmgr_qry as $delmgr){
					$update = DB::table('gr_delmgr_notification')->insert(['no_delmgr_id' => $delmgr->dm_id,'no_agent_id'=>$agent_id,'no_status' => '1','submit_by'=>$agent_id ]);
					$send_mail_data = array('name' 			=> $delmgr->dm_name,
					'amount' 		=> $amount,
					'dm_name' 	=> $agent_fname.' '.$agent_lname,
					'agent_email'	=>$delmgr->dm_email,
					'lang'		=>$lang.'_mob_lang',
					'onlyLang'	=>$lang,
					'default_currency'=>Session::get('default_currency_code'));
					Mail::send('email.mobile_payrequest_to_delmgr', $send_mail_data, function($message) use($send_mail_data)
					{
						$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'SEND_PAY_REQ','Pay Request');
						$message->to($send_mail_data['agent_email'], $send_mail_data['name'])->subject($subject);
					});
				}
			}
			$send_mail_data = array('name' 			=> 'Admin',
			'amount' 		=> $amount,
			'dm_name' 		=> $agent_fname.' '.$agent_lname,
			'agent_email'	=> $this->general_setting->gs_email,
			'lang'			=> $lang.'_mob_lang',
			'onlyLang'		=> $lang,
			'default_currency'=>Session::get('default_currency_code'));
			Mail::send('email.mobile_payrequest_to_delmgr', $send_mail_data, function($message) use($send_mail_data)
			{
				$subject = MobileModel::get_lang_text($send_mail_data['onlyLang'],'SEND_PAY_REQ','Pay Request');
				$message->to($send_mail_data['agent_email'], $send_mail_data['name'])->subject($subject);
			});
			$msge=MobileModel::get_lang_text($lang,'API_MAIL_SENT_SUCCESSFULLY','Mail sent successfully!');
			return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		public function agent_commission_payment(Request $request){
			$lang = $request->lang;
			$agent	 	= JWTAuth::user();
			$agent_id	= $agent->agent_id;
			$paid = $request->paid;
			$amount_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_AMOUNT','Please enter amount');
			$amount_valid_err_msg = MobileModel::get_lang_text($lang,'API_INVALID_AMT','Invalid Amount');
			$cursym_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_CURRENCY_SYMBOL','Please enter currency symbol');
			$pmtstatus_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYMENT_STATUS','Please enter payment status');
			$pmtstatus_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_VALID_PAYMENT_STATUS','Please enter valid payment status');
			$transaxnid_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_TRANSAXN_ID','Please enter transaction ID');
			$paytype_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYTYPE','Please enter pay type');
			$pmtId_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAYMENT_ID','Please enter payment ID');
			$rcptNum_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_RECEIPT_NUM','Please enter receipt number');
			$paidTime_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_PAID_TIME','Please enter paid time');
			$last4_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_LAST4','Please enter last 4');
			$cardType_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_CARD_TYPE','Please enter card type');
			$masked_valid_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_MASKED_CARD','Please enter masked card');
			
			$validator = Validator::make($request->all(),
			[
			'paid'				=>'required|numeric',
			'currency_symbol'	=>'required',
			'status'			=>'required|integer',
			'transaction_id'	=>'required',
			'pay_type'			=>'required|integer',
			'paymaya_pmtId' 	=> 'required_if:pay_type,1',
			'paymaya_receiptnum' => 'required_if:pay_type,1',
			'paymaya_paid_time' => 'required_if:pay_type,1',
			'paymaya_last4' 	=> 'required_if:pay_type,1',
			'paymaya_cardtype' => 'required_if:pay_type,1',
			'paymaya_maskedcard' => 'required_if:pay_type,1'
			],
			[
			'paid.required' 			=> $amount_req_err_msg, 
			'paid.numeric'				=> $amount_valid_err_msg,
			'currency_symbol.required' => $cursym_req_err_msg,
			'status.required'			=> $pmtstatus_req_err_msg,
			'status.integer'			=> $pmtstatus_valid_err_msg,
			'transaction_id.required'	=> $transaxnid_req_err_msg,
			'pay_type.required'		=> $paytype_req_err_msg,
			'paymaya_pmtId.required'	=> $pmtId_valid_err_msg,
			'paymaya_receiptnum.required'=> $rcptNum_valid_err_msg,
			'paymaya_paid_time.required'=> $paidTime_valid_err_msg,
			'paymaya_last4.required'	=> $last4_valid_err_msg,
			'paymaya_cardtype.required'=> $cardType_valid_err_msg,
			'paymaya_maskedcard.required'=>$masked_valid_err_msg,
			]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			
			$insertArr = [	
			'agent_id' 				=> $agent_id,
			'amount_received'	 	=> $request->paid,
			'commission_currency'	=> $request->currency_symbol,
			'commission_status'		=> '2',
			'transaction_id' 		=> $request->transaction_id,
			'pay_type' 				=> $request->pay_type,
			'paymaya_pmtId'			=> $request->paymaya_pmtId,
			'paymaya_receiptnum'	=> $request->paymaya_receiptnum,
			'paymaya_paid_time'		=> $request->paymaya_paid_time,
			'paymaya_last4'			=> $request->paymaya_last4,
			'paymaya_cardtype'		=> $request->paymaya_cardtype,
			'paymaya_maskedcard'	=> $request->paymaya_maskedcard,
			'commission_date'		=> date('Y-m-d H:i:s')];
			$insert = insertvalues('gr_agent_commission',$insertArr);
			/**delete notification **/
			DB::table('gr_delmgr_notification')->where(['no_status' => '1','no_delmgr_id' => Session::get('DelMgrSessId'), 'submit_by'=>$agent_id])->delete();
			$msge = MobileModel::get_lang_text($lang,'API_PAY_PAID_SUX','Payment paid successfully!');
			return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		public function logout(Request $request)
		{
			$lang= $request->lang;
			$validator = Validator::make($request->all(),[ 'token'	=> 'required']);
            if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			//$this->validate($request, ['token' => 'required']);
			//echo 'here';
			//exit;
			try {
				JWTAuth::invalidate($request->token);
				$msge = MobileModel::get_lang_text($lang,'API_LOGOUT_SUXES','User logged out successfully!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				} catch (JWTException $exception) {
				$msge = MobileModel::get_lang_text($lang,'API_CANNOT_LOGOUT','Sorry, the user cannot be logged out!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
	}
?>