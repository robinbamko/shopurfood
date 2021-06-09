<?php 
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\MobileApp;
	use App\Http\Controllers\Controller;
	use App\Http\Models;
	use App\MobileModel;
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
	//use Image;
	use Intervention\Image\ImageManagerStatic as Image;
	use Tymon\JWTAuth\Exceptions\JWTException;
	use Twilio;
	use Session;
	use File;
	class DeliveryAppController extends Controller
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
				$this->default_curr_code = $default->co_curcode;
			}
            else
            {
				Session::put('default_currency_code', 'USD');
				Session::put('default_currency_symbol', '$');
				$this->default_curr_code = "USD";
			}
			
			View::share("LOGOPATH",$path);
			$admin_det = get_admin_details();  /* get admin details*/
			$this->admin_id  = $admin_det->id;
			
		}
		
		/** send notification starts **/
		
		
		// function makes curl request to firebase servers
		
		private  function sendPushNotification($fields,$key) {
			
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
		
		
		/* forget password */
		public function delivery_forgot_password(Request $request){
			$lang = $request->lang;
			/* ----------VALIDATION STARTS HERE--------------- */
			$validator = Validator::make($request->all(), 
			['delivery_email' => ['required','string','email',
			Rule::exists('gr_delivery_member','deliver_email')->where(function ($query) {
				$query->where('deliver_status','=','1');
			})]],
			['delivery_email.required'	=>	MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required.'),
			'delivery_email.email'		=>	MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!'),
			'delivery_email.exists'		=> MobileModel::get_lang_text($lang,'API_MAIL_NT_EXIST','Email not exists')]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$LoginRes = MobileModel::check_id_exist($request->delivery_email,'gr_delivery_member','deliver_email','deliver_status','deliver_id');
			//print_r($LoginRes); exit;
			if(empty($LoginRes) === false){
				
				$agent_id 	= $LoginRes->deliver_id;
				$passwordIs = rand_password();
				$agent_password = array('deliver_password' => md5($passwordIs),'deliver_decrypt_password' => $passwordIs);
				DB::table('gr_delivery_member')->where('deliver_id', '=', $agent_id)->update($agent_password);
				
				/*MAIL FUNCTION */
				$data = array('ForgotEmail' => $request->delivery_email,
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
		
		/*  home page */
		public function home(Request $request)
		{	
			$lang = $request->lang;
			/*$cus_name = JWTAuth::user()->deliver_fname;*/
			$greet = MobileModel::get_lang_text($lang,'API_WELCOME_TO','Welcome To ');
			$get_details = MobileModel::get_logo_settings_details();
			$andr_splash = $andr_logo = $ios_splash = $ios_login_logo = $ios_signup_logo = $ios_frpw_logo = '';
			if(count($get_details) > 0)
			{
				foreach($get_details as $details)
				{	
					if($details->andr_splash_img_delivery != '')
					{
						$filename = public_path('images/logo/').$details->andr_splash_img_delivery;
						if(file_exists($filename))
						{
							$andr_splash = url('').'/public/images/logo/'.$details->andr_splash_img_delivery;
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
					if($details->ios_splash_img_delivery != '')
					{
						$filename = public_path('images/logo/').$details->ios_splash_img_delivery;
						if(file_exists($filename))
						{
							$ios_splash = url('').'/public/images/logo/'.$details->ios_splash_img_delivery;
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
		
		/* profile */
		public function delivery_profile(Request $request)
		{
			$lang = $request->lang;
			$deliver_details = JWTAuth::user();
			//print_r($deliver_details); exit;
			$agent_avatar = url('public/images/noimage/default_user_image.jpg');
			$licence = '';
			$addr_proof = '';
			if($deliver_details->deliver_profile_image != '')
			{
				$filename = public_path('images/delivery_person/').$deliver_details->deliver_profile_image;
				
				if(file_exists($filename)){
					$agent_avatar = url('public/images/delivery_person/'.$deliver_details->deliver_profile_image );
				}
			}
			if($deliver_details->deliver_licence != '')
			{
				$filename = public_path('images/delivery_person/').$deliver_details->deliver_licence;
				
				if(file_exists($filename)){
					$licence = url('public/images/delivery_person/'.$deliver_details->deliver_licence);
				}
			}
			if($deliver_details->deliver_address_proof != '')
			{
				$filename = public_path('images/delivery_person/').$deliver_details->deliver_address_proof;
				
				if(file_exists($filename)){
					$addr_proof = url('public/images/delivery_person/'.$deliver_details->deliver_address_proof );
				}
			}
			$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
			$addi_charge = '';
			if($deliver_details->deliver_fare_type == 'per_km')
			{
				$addi_charge =  $deliver_details->deliver_currency_code.' '.$deliver_details->deliver_perkm_charge;
			}
			else
			{
				$addi_charge = $deliver_details->deliver_currency_code.' '.$deliver_details->deliver_permin_charge;
			}
			$data = ["delivery_id"			=>intval($deliver_details->deliver_id),
			"delivery_fname"		=>ucfirst($deliver_details->deliver_fname),
			"delivery_lname"		=>ucfirst($deliver_details->deliver_lname),
			"delivery_email"		=>$deliver_details->deliver_email,
			"delivery_password"		=>$deliver_details->deliver_decrypt_password,
			"delivery_phone"		=>$deliver_details->deliver_phone1,
			//"delivery_phone2"	=>($deliver_details->deliver_phone2 == '') ? '' : $deliver_details->deliver_phone2,
			"delivery_profile"		=>$agent_avatar,
			"delivery_licence"		=>$licence,
			"delivery_licence_name"	=>$deliver_details->deliver_licence,
			"delivery_address_proof"	=>$addr_proof,
			"delivery_address_proof_name"	=>$deliver_details->deliver_address_proof,
			"delivery_response_time"=>$deliver_details->deliver_response_time,
			"delivery_base_fare"	=>$deliver_details->deliver_currency_code.' '.$deliver_details->deliver_base_fare,
			"delivery_fare_type"	=>$deliver_details->deliver_fare_type,
			"delivery_km_Ormin_charge"	=>$addi_charge,
			"delivery_location"		=>($deliver_details->deliver_location == '') ? '' : $deliver_details->deliver_location,
			"delivery_latitude"		=>($deliver_details->deliver_latitude == '') ? '' :$deliver_details->deliver_latitude ,
			"delivery_longitude"	=>($deliver_details->deliver_longitude == '') ? '' : $deliver_details->deliver_longitude,
			"delivery_status"		=> ($deliver_details->deliver_avail_status == 1) ? MobileModel::get_lang_text($lang,'API_AVAIL','Available') : MobileModel::get_lang_text($lang,'API_UNAVAIL','Busy'),
			"delivery_vehicle"		=> $deliver_details->deliver_vehicle_details,
			"order_limit"			=> $deliver_details->deliver_order_limit];
			$encode = ['code'=>200,'message'=>$msge,'data' => $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		
		/* edit profile */
		public function update_profile(Request $request)
		{
			$details 			= JWTAuth::user();
			$deliver_id 		= $details->deliver_id;
			$old_mail	 		= $details->deliver_email;
			$old_phone	 		= $details->deliver_phone1;
			$old_profile	 	= $details->deliver_profile_image;
			$old_licence	 	= $details->deliver_licence;
			$old_addr_pr	 	= $details->deliver_address_proof;
			$delivery_fname 	= $request->delivery_fname;
			$delivery_lname 	= $request->delivery_lname;
			$delivery_email 	= $request->delivery_email;
			$delivery_phone1 	= $request->delivery_phone;
			//$delivery_phone2 	= $request->delivery_phone2;
			$delivery_location 	= $request->delivery_location;
			$delivery_image 	= $request->delivery_img;
			$delivery_latitude 	= $request->delivery_latitude;
			$delivery_longitude = $request->delivery_longitude;
			$lang 				= $request->lang;
			$delivery_status	= $request->delivery_status;
			$delivery_vehicle	= $request->delivery_vehicle;
			//$order_limit 		= $request->order_limit;
			$licence 			= $request->licence;
			$address_proof 		= $request->address_proof;
			
			$agent_name_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$addr_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$agent_img_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_SELECT_IMAGE','Please select image!');
			$agent_img_val_err_msg	= MobileModel::get_lang_text($lang,'API_UP_VALID_IMG','Upload valid image');
			$email_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required!');
			$valid_email_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$unique_email_err_msg	= MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
			$valid_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$unique_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
			$licence_err_msg		= MobileModel::get_lang_text($lang,'API_UPLOAD_LICENCE','Please Upload licence image');
			$licence_val_err_msg	= MobileModel::get_lang_text($lang,'API_UPLOAD_ADDR_PROOF','Please upload valid licence image');
			$addr_proof_err_msg		= MobileModel::get_lang_text($lang,'API_UPLOAD_ADDR_PROOF','Please Upload address proof image');
			$addr_val_proof_err_msg	= MobileModel::get_lang_text($lang,'API_UPLOAD_VALID_ADDR_PROOF','Please upload valid address proof image');
			$max_size_err_msg	= MobileModel::get_lang_text($lang,'API_MAX_SIZE_2MB','Maximum uploaded size 2MB');
			
			
			$validator = Validator::make($request->all(),[  'delivery_fname'		=> 'required',
			'delivery_email' 		=> ['required','email',
			Rule::unique('gr_delivery_member','deliver_email')->where(function ($query) use ($deliver_id) {
				return $query->where('deliver_id','!=',$deliver_id)->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			'delivery_phone' 		=> ['required',
			Rule::unique('gr_delivery_member','deliver_phone1')->where(function ($query) use ($deliver_id) {
				return $query->where('deliver_id','!=',$deliver_id)->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'delivery_phone2' 	=> 'sometimes|only_cnty_code',
			'delivery_location'	=> 'required',
			'delivery_latitude'	=> 'required',
			'delivery_longitude'=> 'required',
			//'delivery_img' 		=> 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'delivery_status' 		=> 'required|numeric',
			'delivery_vehicle' 		=> 'required',
			'licence' 				=> 'required|mimes:jpeg,png,jpg,pdf|max:2048',
			'address_proof' 		=> 'required|mimes:jpeg,png,jpg,pdf|max:2048',
			//'order_limit'	 		=> 'required|numeric|max:10',
			],
			[
			'delivery_fname.required'		=> $agent_name_req_err_msg,
			'delivery_email.required'		=> $email_req_err_msg,
			'delivery_email.email'			=> $valid_email_err_msg,
			'delivery_email.unique'			=> $unique_email_err_msg,
			'delivery_phone.required'		=> $valid_phone_err_msg,
			'delivery_phone.only_cnty_code'=> $valid_phone_err_msg,
			'delivery_phone.unique'		=> $unique_phone_err_msg,
			//'delivery_phone2.only_cnty_code'=>$valid_phone2_err_msg,
			'delivery_location.required'	=> $addr_req_err_msg,
			'delivery_latitude.required'	=> $lati_req_err_msg,
			'delivery_longitude.required'	=> $longi_req_err_msg,
			'licence.required'				=> $licence_err_msg,
			'licence.mimes'					=> $licence_val_err_msg,
			'address_proof.required'		=> $addr_proof_err_msg,
			'address_proof.mimes'			=> $addr_val_proof_err_msg,
			'licence.max'					=> $max_size_err_msg,
			'address_proof.max'				=> $max_size_err_msg,
			/*'delivery_img.sometimes'		=> $agent_img_req_err_msg,
				'delivery_img.image'			=> $agent_img_val_err_msg,
			'delivery_img.mimes'			=> $agent_img_val_err_msg,*/
			
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$insertArr = array(	'deliver_fname' 		=> $delivery_fname,
			'deliver_lname' 		=> $delivery_lname,
			'deliver_email' 		=> $delivery_email,
			'deliver_phone1' 		=> $delivery_phone1,
			//'delivery_phone2' 	=> $delivery_phone2,
			'deliver_location' 		=> $delivery_location,
			'deliver_latitude' 		=> $delivery_latitude,
			'deliver_longitude' 	=> $delivery_longitude,               
			'deliver_avail_status' => $delivery_status,               
			'deliver_vehicle_details' => $delivery_vehicle,               
			//'deliver_order_limit' 	=> $order_limit,               
			);
			
			if($request->hasFile('delivery_img')) {
				/* delete old image */
				$image_path = public_path('images/delivery_person/').$old_profile;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				$agent_image 	= 'deliver_'.$deliver_id.'.'.request()->delivery_img->getClientOriginalExtension();
				$destinationPath = public_path('images/delivery_person');
				$agent 			= Image::make(request()->delivery_img->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image,80);
				$insertArr['deliver_profile_image'] = $agent_image;
			}
			if($request->hasFile('licence')) {
				/* delete old image */
				$image_path = public_path('images/delivery_person/').$old_licence;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				/*$agent_image1 	= 'deliver_licence_'.$deliver_id.'.'.request()->licence->getClientOriginalExtension();
					$destinationPath = public_path('images/delivery_person');
					$agent 			= Image::make(request()->licence->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image1,80);*/
				$file = $request->file('licence');
				$agent_image1='licence_'.$deliver_id.'.'.$file->getClientOriginalExtension();
                $file->move(public_path('images/delivery_person'), $agent_image1);
				$insertArr['deliver_licence'] = $agent_image1;
			}
			if($request->hasFile('address_proof')) {
				/* delete old image */
				$image_path = public_path('images/delivery_person/').$old_addr_pr;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				/*$agent_image2 	= 'deliver_proof_'.$deliver_id.'.'.request()->address_proof->getClientOriginalExtension();
					$destinationPath = public_path('images/delivery_person');
					$agent 			= Image::make(request()->address_proof->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image2,80);*/
				$file = $request->file('address_proof');
				$agent_image2='address_proof_'.$deliver_id.'.'.$file->getClientOriginalExtension();
                $file->move(public_path('images/delivery_person'), $agent_image2);
				$insertArr['deliver_address_proof'] = $agent_image2;
			}
			/*----------------------CHECK PHONE NUMBER IS NEW  ------------*/
			if($old_phone != $delivery_phone1)
			{
				$otp = mt_rand(100000, 999999);
				try{
					Twilio::message($delivery_phone1, $otp);
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$insertArr['code'] = 201;
					$insertArr['message']= $msge;
					$insertArr['data']	 = ['otp' => $otp];
					return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
				catch (\Exception $e)
				{		
					/*----- hide for testing twilio in test account.And enable it while uses live twilio account -------*/
					/*$encode = array('code'=> 400,'message' => $e->getMessage(),'data' => $this->empty_data);
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");*/
					$msge=MobileModel::get_lang_text($lang,'API_OTP_SENT_TOUR_MOBILE','OTP sent to your mobile. Please enter otp');
					$insertArr['code'] = 201;
					$insertArr['message']= $msge;
					$insertArr['data']	 = ['otp' => $otp];
					return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			elseif($old_mail != $delivery_email)
			{
				$code = mt_rand(100000, 999999);
				$send_mail_data = ['code' 		=> $code,
									'lang'		=>$lang.'_mob_lang',
									'onlyLang'	=>$lang,
									'agent_email'=>$delivery_email
									];
				$send = Mail::send('email.mobile_email_verification', $send_mail_data, function($message) use($send_mail_data)
				{	
					$msg = MobileModel::get_lang_text($send_mail_data['lang'],'API_CNFRM_MAIL','Confirm Your Mail');
					$message->to($send_mail_data['agent_email'])->subject($msg);
				});
				$msge = MobileModel::get_lang_text($lang,'API_VERICODE_SENT_MAIL','Verification code sent to your email. Please enter verification code');
				$insertArr['code'] = 201;
				$insertArr['message']= $msge;
				$insertArr['data']	 = ['otp' => $code];
				return Response::make(json_encode($insertArr,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			} 
			else 
			{
				$update = updatevalues('gr_delivery_member',$insertArr,['deliver_id' =>$deliver_id]);
				
				$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
				$encode = [ 'code' => 200,'message' => $msg,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
		}
		
		public function update_profile_withOtp(Request $request)
		{
			$details 			= JWTAuth::user();
			$deliver_id 		= $details->deliver_id;
			$old_mail	 		= $details->deliver_email;
			$old_phone	 		= $details->deliver_phone1;
			$old_profile 		= $details->deliver_profile_image;
			$old_licence 		= $details->deliver_licence;
			$old_addr_pr 		= $details->deliver_address_proof;
			$delivery_fname 	= $request->delivery_fname;
			$delivery_lname 	= $request->delivery_lname;
			$delivery_email 	= $request->delivery_email;
			$delivery_phone1 	= $request->delivery_phone;
			//$delivery_phone2 	= $request->delivery_phone2;
			$delivery_location 	= $request->delivery_location;
			$delivery_image 	= $request->delivery_img;
			$delivery_latitude 	= $request->delivery_latitude;
			$delivery_longitude = $request->delivery_longitude;
			$lang 				= $request->lang;
			$delivery_status	= $request->delivery_status;
			$delivery_vehicle	= $request->delivery_vehicle;
			//$order_limit 		= $request->order_limit;
			$licence 			= $request->licence;
			$address_proof 		= $request->address_proof;
			$current_otp 		= $request->current_otp;
			$generated_otp 		= $request->otp;
			
			$agent_name_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_NAME','Please enter name!');
			$addr_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_ADDRESS','Please enter address!');
			$lati_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LATITUDE','Please enter latitude!');
			$longi_req_err_msg		= MobileModel::get_lang_text($lang,'API_PLS_ENTER_LONGITUDE','Please enter longitude!');
			$agent_img_req_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_SELECT_IMAGE','Please select image!');
			$agent_img_val_err_msg	= MobileModel::get_lang_text($lang,'API_UP_VALID_IMG','Upload valid image');
			$otp_req_msg			= MobileModel::get_lang_text($lang,'API_ENTER_YOUR_OTP','Please enter OTP!');
			$rec_otp_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_RECEIVED_OTP','Please enter received OTP!');
			$otp_same_err_msg		= MobileModel::get_lang_text($lang,'API_INVALID_OTP','Invalid Otp!');
			$email_req_err_msg		= MobileModel::get_lang_text($lang,'API_EMAIL_REQUIRED','The email field is required!');
			$valid_email_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_EMAIL','Enter valid email!');
			$unique_email_err_msg	= MobileModel::get_lang_text($lang,'API_EMAIL_ALREADY_EXISTS','Email Already Exists!');
			$valid_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PLS_ENTER_VALID_PHONENUM','Please enter valid phone number!');
			$unique_phone_err_msg	= MobileModel::get_lang_text($lang,'API_PHONENUM_ALREADY_EXISTS','Phone Number Already Exists!');
			$licence_err_msg		= MobileModel::get_lang_text($lang,'API_UPLOAD_LICENCE','Please Upload licence image');
			$licence_val_err_msg	= MobileModel::get_lang_text($lang,'API_UPLOAD_ADDR_PROOF','Please upload valid licence image');
			$addr_proof_err_msg		= MobileModel::get_lang_text($lang,'API_UPLOAD_ADDR_PROOF','Please Upload address proof image');
			$addr_val_proof_err_msg	= MobileModel::get_lang_text($lang,'API_UPLOAD_VALID_ADDR_PROOF','Please upload valid address proof image');
			$max_size_err_msg	= MobileModel::get_lang_text($lang,'API_MAX_SIZE_2MB','Maximum uploaded size 2MB');
			$validator = Validator::make($request->all(),[  'delivery_fname'		=> 'required',
			'delivery_email' 		=> ['required','email',
			Rule::unique('gr_delivery_member','deliver_email')->where(function ($query) use ($deliver_id) {
				return $query->where('deliver_id','!=',$deliver_id)->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			'delivery_phone' 		=> ['required',
			Rule::unique('gr_delivery_member','deliver_phone1')->where(function ($query) use ($deliver_id) {
				return $query->where('deliver_id','!=',$deliver_id)->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'delivery_phone2' 	=> 'sometimes|only_cnty_code',
			'delivery_location'	=> 'required',
			'delivery_latitude'	=> 'required',
			'delivery_longitude'=> 'required',
			//'delivery_img' 		=> 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'delivery_status' 		=> 'required|numeric',
			'delivery_vehicle' 		=> 'required',
			'licence' 				=> 'required|mimes:jpeg,png,jpg,pdf|max:2048',
			'address_proof' 		=> 'required|mimes:jpeg,png,jpg,pdf|max:2048',
			//'order_limit'	 		=> 'required|numeric|max:10',
			'otp'	 				=> 'required',
			'current_otp'	 		=> 'required|same:otp'
			
			],
			[
			'delivery_fname.required'		=> $agent_name_req_err_msg,
			'delivery_email.required'		=> $email_req_err_msg,
			'delivery_email.email'			=> $valid_email_err_msg,
			'delivery_phone1.required'		=> $valid_phone_err_msg,
			'delivery_phone1.only_cnty_code'=> $valid_phone_err_msg,
			//'delivery_phone2.only_cnty_code'=>$valid_phone2_err_msg,
			'delivery_location.required'	=> $addr_req_err_msg,
			'delivery_latitude.required'	=> $lati_req_err_msg,
			'delivery_longitude.required'	=> $longi_req_err_msg,
			'licence.required'				=> $licence_err_msg,
			'licence.image'					=> $licence_val_err_msg,
			'address_proof.required'		=> $addr_proof_err_msg,
			'address_proof.image'			=> $addr_val_proof_err_msg,
			/*'delivery_img.sometimes'		=> $agent_img_req_err_msg,
				'delivery_img.image'			=> $agent_img_val_err_msg,
			'delivery_img.mimes'			=> $agent_img_val_err_msg,*/
			'otp.required'					=> $otp_req_msg,
			'current_otp.required'			=> $rec_otp_req_err_msg,
			'current_otp.same'				=> $otp_same_err_msg,
			'licence.max'					=> $max_size_err_msg,
			'address_proof.max'				=> $max_size_err_msg,
			
			]
			);
			
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			$insertArr = array(	'deliver_fname' 		=> $delivery_fname,
			'deliver_lname' 		=> $delivery_lname,
			'deliver_email' 		=> $delivery_email,
			'deliver_phone1' 		=> $delivery_phone1,
			//'delivery_phone2' 	=> $delivery_phone2,
			'deliver_location' 		=> $delivery_location,
			'deliver_latitude' 		=> $delivery_latitude,
			'deliver_longitude' 	=> $delivery_longitude,               
			'deliver_avail_status' => $delivery_status,               
			'deliver_vehicle_details' => $delivery_vehicle,               
			//'deliver_order_limit' 	=> $order_limit,               
			);
			
			if($request->hasFile('delivery_img')) {
				/* delete old image */
				$image_path = public_path('images/delivery_person/').$old_profile;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				$agent_image 	= 'deliver_'.$deliver_id.'.'.request()->delivery_img->getClientOriginalExtension();
				$destinationPath = public_path('images/delivery_person');
				$agent 			= Image::make(request()->delivery_img->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image,80);
				$insertArr['deliver_profile_image'] = $agent_image;
			}
			if($request->hasFile('licence')) {
				/* delete old image */
				$image_path = public_path('images/delivery_person/').$old_licence;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				$agent_image1 	= 'licence_'.$deliver_id.'.'.request()->licence->getClientOriginalExtension();
				$destinationPath = public_path('images/delivery_person');
				$agent 			= Image::make(request()->licence->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image1,80);
				$insertArr['deliver_licence'] = $agent_image1;
			}
			if($request->hasFile('address_proof')) {
				/* delete old image */
				$image_path = public_path('images/delivery_person/').$old_addr_pr;  // Value is not URL but directory file path
				if(File::exists($image_path)) 
				{
					$a =   File::delete($image_path);
					
				}
				$agent_image2 	= 'address_proof_'.$deliver_id.'.'.request()->address_proof->getClientOriginalExtension();
				$destinationPath = public_path('images/delivery_person');
				$agent 			= Image::make(request()->address_proof->getRealPath())->resize(300, 300);
				$agent->save($destinationPath.'/'.$agent_image2,80);
				$insertArr['deliver_address_proof'] = $agent_image2;
			}
			$update = updatevalues('gr_delivery_member',$insertArr,['deliver_id' =>$deliver_id]);
			
			$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
			$encode = [ 'code' => 200,'message' => $msg,'data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			
			
		}
		
		/* update working horus */
		public function update_wking_hrs(Request $request)
		{	
			$lang 			= $request->lang;
			$day_from[1]	= $request->mon_from;
			$day_to[1]		= $request->mon_to;
			$day_status[1]	= $request->mon_status;
			$day_from[2]	= $request->tue_from;
			$day_to[2]		= $request->tue_to;
			$day_status[2]	= $request->tue_status;
			$day_from[3]	= $request->wed_from;
			$day_to[3]		= $request->wed_to;
			$day_status[3]	= $request->wed_status;
			$day_from[4]	= $request->thu_from;
			$day_to[4]		= $request->thu_to;
			$day_status[4]	= $request->thu_status;
			$day_from[5]	= $request->fri_from;
			$day_to[5]		= $request->fri_to;
			$day_status[5]	= $request->fri_status;
			$day_from[6]	= $request->sat_from;
			$day_to[6]		= $request->sat_to;
			$day_status[6]	= $request->sat_status;
			$day_from[7]	= $request->sun_from;
			$day_to[7]		= $request->sun_to;
			$day_status[7]	= $request->sun_status;
			$details 		= JWTAuth::user();
			$deliver_id 	= $details->deliver_id;
			$agent_id 		= $details->deliver_agent_id;
			$validate = Validator::make($request->all(),
			[
			'mon_status'	=> 'required',
			'tue_status'	=> 'required',
			'wed_status'	=> 'required',
			'thu_status'	=> 'required',
			'fri_status'	=> 'required',
			'sat_status'	=> 'required',
			'sun_status'	=> 'required',
			'mon_from'	=> 'required_if:mon_status,1|date_format:H:i',
			'mon_to'	=> 'required_if:mon_status,1|date_format:H:i',
			'tue_from'	=> 'required_if:tue_status,1|date_format:H:i',
			'tue_to'	=> 'required_if:tue_status,1|date_format:H:i',
			'wed_from'	=> 'required_if:wed_status,1|date_format:H:i',
			'wed_to'	=> 'required_if:wed_status,1|date_format:H:i',
			'thu_from'	=> 'required_if:thu_status,1|date_format:H:i',
			'thu_to'	=> 'required_if:thu_status,1|date_format:H:i',
			'fri_from'	=> 'required_if:fri_status,1|date_format:H:i',
			'fri_to'	=> 'required_if:fri_status,1|date_format:H:i',
			'sat_from'	=> 'required_if:sat_status,1|date_format:H:i',
			'sat_to'	=> 'required_if:sat_status,1|date_format:H:i',
			'sun_from'	=> 'required_if:sun_status,1|date_format:H:i',
			'sun_to'	=> 'required_if:sun_status,1|date_format:H:i',
			],
			['mon_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_MN_FROM','Enter monday from time'),
			'mon_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_MN_TO','Enter monday to time'),
			'tue_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_TU_FROM','Enter tuesday from time'),
			'tue_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_TU_TO','Enter tuesday to time'),
			'wed_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_WE_FROM','Enter wednesday from time'),
			'wed_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_WE_TO','Enter wednesday to time'),
			'thu_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_TH_FROM','Enter thursday from time'),
			'thu_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_TH_TO','Enter thursday to time'),
			'fri_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_FR_FROM','Enter friday from time'),
			'fri_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_FR_TO','Enter friday to time'),
			'sat_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_SA_FROM','Enter satday from time'),
			'sat_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_SA_TO','Enter satday to time'),
			'sun_from.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_SN_FROM','Enter sunday from time'),
			'sun_to.required_if'	=> MobileModel::get_lang_text($lang,'API_EN_SN_TO','Enter sunday to time'),
			'mon_status.required'	=> MobileModel::get_lang_text($lang,'API_MN_TO','Enter monday available status'),
			'tue_status.required'	=> MobileModel::get_lang_text($lang,'API_TU_TO','Enter tuesday available status'),
			'wed_status.required'	=> MobileModel::get_lang_text($lang,'API_WE_TO','Enter wednesday available status'),
			'thu_status.required'	=> MobileModel::get_lang_text($lang,'API_TH_TO','Enter thursday available status'),
			'fri_status.required'	=> MobileModel::get_lang_text($lang,'API_FR_TO','Enter friday available status'),
			'sat_status.required'	=> MobileModel::get_lang_text($lang,'API_SA_TO','Enter satday available status'),
			'sun_status.required'	=> MobileModel::get_lang_text($lang,'API_SN_TO','Enter sunday available status')]);
			if($validate->fails()){
				$message = $validate->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			/* update working hours */
			//echo $day_status_1; exit;
			$insert_arr = array();
			$msg = '';
			$deliver_id = JWTAuth::user()->deliver_id;
			$check = DB::table('gr_deliver_working_hrs')->where(['dw_deliver_id' => JWTAuth::user()->deliver_id])->count();
			for($i=1;$i<=7;$i++)
			{
				$week_array = ['1' =>'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
				$insert_arr['dw_date'] 		= $week_array[$i];
				$insert_arr['dw_status'] 	= $day_status[$i];
				$insert_arr['dw_from']	 	= ($day_from[$i] == '') ? '0:00' : $day_from[$i];
				$insert_arr['dw_to'] 		= ($day_to[$i] == '') ? '0:00' : $day_to[$i];
				$insert_arr['dw_deliver_id']= $deliver_id;
				$insert_arr['dw_agent_id'] 	= $agent_id;
				$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
				if($check > 0)
				{ //echo "in"; 
					updatevalues('gr_deliver_working_hrs',$insert_arr,['dw_deliver_id' => $deliver_id,'dw_date' => $week_array[$i]]);
				}
				else
				{	//echo "else"; exit;
					insertvalues('gr_deliver_working_hrs',$insert_arr);	
					$msg = MobileModel::get_lang_text($lang,'API_SAVE_SUXES','Saved successfully!');
				}				
				//print_r($insert_arr); exit;
			}
			
			$encode = [ 'code' => 200,'message' => $msg,'data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
		/* view working horus */
		public function view_wk_hrs(Request $request)
		{
			$lang = $request->lang;
			$get_details = DB::table('gr_deliver_working_hrs')->where('dw_deliver_id','=',JWTAuth::user()->deliver_id)->get();
			if(count($get_details) > 0)
			{
				$i = 1;
				$wk_array = array();
				foreach($get_details as $details)
				{
					$week_array = ['1' =>'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
					$wk_array[] = array($week_array[$i] => ['status' => ($details->dw_status == 0) ? MobileModel::get_lang_text($lang,'API_NT_AVAIL','Not Available') : MobileModel::get_lang_text($lang,'API_AVAIL','Available'),
					'form_time'	=> $details->dw_from,
					'to_time'	=> $details->dw_to]);
					$i++;
				}
				$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
				$encode = ['code'=>200,
				'message'=>$msge,
				'data'  => ['working_hours' => $wk_array]
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge = MobileModel::get_lang_text($lang,'API_DETAILS_NT_AVAIL','Details Not Available');
				$encode = ['code'=>400,'message'=> $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
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
			$old_pwd_text 		= MobileModel::get_lang_text($lang,'API_OLD','Old');
			$new_pwd_text 		= MobileModel::get_lang_text($lang,'API_NEW','New');
			if($this->general_setting->gs_password_protect==1)
			{
				$validator = Validator::make($request->all(), 
				[ 'old_password' => 'required|min:6',
				'new_password' => 'required|min:6|regex:/(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$)+/'
				],
				[ 'old_password.required'=>$oldpwd_req_err_msg,
				'old_password.min'   =>$old_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.required'=>$newpwd_req_err_msg,
				'new_password.min'  =>$new_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.regex'=>$pwd_regex_err_msg
				]
				);
			}
			else{
				$validator = Validator::make($request->all(), 
				[ 'old_password' 	=> 'required|min:6', 
				'new_password' 	=> 'required|min:6'],
				[ 'old_password.required'	=>$oldpwd_req_err_msg,
				'old_password.min'		=>$old_pwd_text.' '.$pwd_min_6_err_msg,
				'new_password.required'	=> $newpwd_req_err_msg,
				'new_password.min'=>$new_pwd_text.' '.$pwd_min_6_err_msg
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
				if($user->deliver_password != md5($old_password))
				{
					$msg 	= MobileModel::get_lang_text($lang,'API_INCORRECT_PASSWORD','Your old password does not match with our records! Please try again!');
					$encode = [ 'code' => 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				elseif($old_password == $new_password){
					$msg 	= MobileModel::get_lang_text($lang,'API_NEW_PASS_NOT_SAME_CURRENT_PASS','New Password cannot be same as your current password. Please choose a different password!');
					$encode = [ 'code' => 400,'message' => $msg,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}else{
					
					$insertArr = array('deliver_password' => md5($new_password),'deliver_decrypt_password' => $new_password);
					$update = updatevalues('gr_delivery_member',$insertArr,['deliver_id'=>$user->deliver_id]);
					$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
					$data = ["delivery_id"		=>intval($user->deliver_id),
					"delivery_name"		=> ucfirst($user->deliver_fname).' '.$user->deliver_lname,
					"delivery_email"	=> $user->deliver_email,
					"delivery_phone"	=> $user->deliver_phone1];
					$encode = ['code'		=> 200,
					"message"	=> $msg,
					'data'		=> $data
					];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				}
			}
			
		}
		
		/* new orders/ accepted orders/ delivered orders */
		public function new_orders(Request $request)
		{	
			$lang = $request->lang;
			$page = ($request->page_no == '') ? 1 : $request->page_no;
			$from_date		= date('Y-m-d H:i:s',strtotime($request->from));
			$to_date		= ($request->to == '') ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime($request->to));
			$search			= '';
			$deliver_id = JWTAuth::user()->deliver_id;
			$agent_id 	= JWTAuth::user()->deliver_agent_id;
			$route = \Request::segment(3);
			$where = array(); $act_status = '';
			$orderby = '';
			if($route == "new_orders")
			{	
				$where = [4];
				$act_status = 0;
				$orderby = 'ord_accepted_on';
			}
			elseif($route == "assigned_orders")
			{	
				$where = [5,6,7];
				$act_status = 1;
				$orderby = 'ord_dispatched_on';
			}
			elseif($route == "delivered_orders")
			{
				$where 		= [8];		
				$act_status = 1;
				$orderby 	= 'ord_delivered_on';
				$search		= $request->search_text; //only for delivered orders
			}
			/* get new orders */
			$st_store_name = ($lang == $this->admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$group_res = Delivery_person::get_order_details($lang,$this->admin_default_lang,$agent_id,$deliver_id,$act_status,$page,$from_date,$to_date,$search,$where,$orderby,$this->agent_module);
			$max_count = 0;
			//print_r($group_res); exit;
			if(count($group_res) > 0)
			{
				$max_count = $group_res->lastPage();
				$newOrderArray = array();
				$text = '' ; 
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
					switch($gres->ord_status)
					{
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
						$text = MobileModel::get_lang_text($lang,'API_DISPATCH','Dispatched');
					}
					/* calculate order receivable amount */
					$cal = Delivery_person::get_receivable_amount($gres->ord_transaction_id,$gres->order_amount);
					//echo $walletFee; echo $delFee; exit;
					$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
					$totalReceivable = $gres->order_amount-$explode[1]+$explode[0];
					$basic_details = Delivery_person::get_basic_details('gr_delivery_manager',['dm_id' => $gres->ord_delmgr_id],'dm_cust_data_protect');
					$arr = array();	
					if($route == "delivered_orders" && (empty($basic_details) === false))
					{	
						if($basic_details->dm_cust_data_protect == '1')	/* if customer data protection in enable, no need to show customer address */
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
						$arr['delivered_on']	= $gres->ord_delivered_on;
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
					$st_logo = '';
					if($gres->st_type == '1') //restaurant image
					{
						$st_logo = MobileModel::get_image_restaurant($gres->st_logo,"logo");
					}
					elseif($gres->st_type == '2') //grocery image
					{
						$st_logo = MobileModel::get_image_store($gres->st_logo,"logo");
					}
					
					$newOrderArray[] = array_merge($arr,array('orderId'					=> $gres->ord_transaction_id,
					'payType'					=> $gres->ord_pay_type,
					'orderCurrency'			=> $gres->ord_currency,
					'totalReceivableAmount'	=> ($gres->ord_pay_type == "COD") ? number_format($totalReceivable,2) : "0.00",
					'orderDate'				=> date('m/d/Y H:i:s',strtotime($gres->ord_date)),
					'orderSchedule'			=> $schedule,
					'orderStatus'				=> $text,
					'storeId'					=> $gres->storeId,
					'storeLogo'				=> $st_logo,
					'storeName'				=> $gres->storeName,
					'storeAddress'				=> $gres->st_address,
					'storeLatitude'			=> $gres->st_latitude,
					'storeLongitude'			=> $gres->st_longitude,
					'storeDelivery_time'		=> $gres->st_delivery_time,
					'storeDelivery_duration'	=> $gres->st_delivery_duration,
					'preOrderDate'				=> ($gres->ord_pre_order_date != '') ? $gres->ord_pre_order_date : '',
					'merchantName'				=> $gres->mer_fname.' '.$gres->mer_lname
					)
					);
					
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['newOrderList'=>$newOrderArray,'max_page_count' => $max_count];
				$encode = ['code'=>200,"message"=>$msge,'data'=>$data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* accept /reject orders */
		public function accept_order(Request $request)
		{
			$lang 		= $request->lang;
			$agent 		= JWTAuth::user();
			$agent_id 	= $agent->deliver_agent_id;
			$delivery_id = $agent->deliver_id;
			$delivery_name = $agent->deliver_fname.' '.$agent->deliver_lname;
            $lang 		= $request->lang; 
			$storeId	= Input::get('store_id');
            $orderId	= Input::get('order_id');
            $status		= Input::get('status');
            $reason		= Input::get('reason');
            $est_hrs	= Input::get('estimated_arrival_inHours');
            $est_mins	= Input::get('estimated_arrival_inMins');
           	$msg = '';
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');       
            $storeId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store/restaurant ID!');            
            $storeId_not_exist		= MobileModel::get_lang_text($lang,'API_ST_ID_NT_EXIST','Store id not exist');            
            $storeId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
            $orderStatus_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_AXPTRJCT_STATUS','Please enter valid status! It should be 1 or 2');
            $orderId_nt_exist	= MobileModel::get_lang_text($lang,'API_OR_ID_NT_EXIST','Order id not exist');
            $reason_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_REJECT_REASON','Please enter reject reason!');
            $time_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTR_EST','Enter estimated arrival time');
            $validator = Validator::make($request->all(),
			[ 'store_id'	=> ['required',
			'integer',
			Rule::exists('gr_store','id')->where(function ($query) { $query->where('st_status','=','1'); })
			],
			'order_id'	=> ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function ($query) use($orderId) { $query->where('ord_transaction_id','=',$orderId); })
			],
			'status'	=> ['required','integer',Rule::in([1,2])],
			'reason'	=> 'required_if:status,2', 
			'estimated_arrival_inHours'	=> 'required_if:status,1', 
			'estimated_arrival_inMins'	=> 'required_if:status,1' 
			],
			[ 'store_id.required'	=> $storeId_req_err_msg,
			'store_id.integer'	=> $storeId_valid_err_msg,
			'store_id.exists'	=> $storeId_not_exist,
			'order_id.required'	=> $orderId_req_err_msg,
			'order_id.exists'	=> $orderId_nt_exist,
			'status.required'	=> $orderStatus_valid_err_msg,
			'status.integer'	=> $orderStatus_valid_err_msg,
			'reason.required_if'=> $reason_req_err_msg,
			'estimated_arrival_inHours.required_if'=> $time_req_err_msg,
			'estimated_arrival_inMins.required_if'=> $time_req_err_msg
			] 
			);
            if($validator->fails())
            {
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			if($status==1)	//accept order
			{
				$insertArr = array('ord_delboy_act_status' 	=> '1',
				'ord_status'			=>'5',
				'ord_dispatched_on' 	=> date('Y-m-d H:i:s'),
				'ord_estimated_arrival_hrs' => $est_hrs,
				'ord_estimated_arrival_mins' => $est_mins);
				$msg = MobileModel::get_lang_text($lang,'API_OR_ACCEPT','Order has been accepted');
				
				/* send notification */
				$got_message = MobileModel::get_lang_text($lang,'API_TAKEN_BY',':transaction_id was taken by :del_boy_name');
				$searchReplaceArray = array(':del_boy_name' => ucfirst($delivery_name),':transaction_id' => $orderId);
				$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
				$details = Delivery_person::get_basic_details('gr_order',['ord_transaction_id' =>$orderId,'ord_rest_id' => $storeId],['ord_merchant_id','ord_delmgr_id','ord_cus_id']);
				//1)admin
				$message_link = 'admin-track-order/'.base64_encode($orderId);
				push_notification($delivery_id,$this->admin_id,'gr_delivery_member','gr_admin',$result,$orderId,$message_link);
				//2)merchant
				if(empty($details) === false)
				{
					$message_link = 'mer-order-details/'.base64_encode($orderId);
					push_notification($delivery_id,$details->ord_merchant_id,'gr_delivery_member','gr_merchant',$result,$orderId,$message_link);
				}
				//3)delivery manager
				if(empty($details) === false)
				{
					$message_link = 'delivery-track-order/'.base64_encode($orderId).'/'.base64_encode($details->ord_merchant_id);
					push_notification($delivery_id,$details->ord_delmgr_id,'gr_delivery_member','gr_delivery_manager',$result,$orderId,$message_link);	
				}
				//4)customer
				if(empty($details) === false)
				{
					$message_link = 'track-order/'.base64_encode($orderId);
					push_notification($delivery_id,$details->ord_cus_id,'gr_delivery_member','gr_customer',$result,$orderId,$message_link);	
					/* send notification to customer mobile */
					$req_details = Delivery_person::get_basic_details('gr_customer',['cus_id' =>$details->ord_cus_id],['cus_ios_fcm_id','cus_andr_fcm_id']);
					if($req_details->cus_andr_fcm_id != '' || $req_details->cus_ios_fcm_id != '')
					{
						if($req_details->cus_andr_fcm_id !='')
						{
							$parse_fcm=json_decode($req_details->cus_andr_fcm_id,true);
							$reg_id = array();
							if(count($parse_fcm) > 0 )
							{
								foreach($parse_fcm as $parsed)
								{ 
									array_push($reg_id,$parsed['fcm_id']);						
								}
							}
							$json_data = [
											"registration_ids" => $reg_id,
											"notification" => ["body" => $result,"title" => "Order Notification"],
											"data"			=> ['order_id' => $orderId,'store_id' => $storeId]
										];
							$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_CUS);
								
						}
						if($req_details->cus_ios_fcm_id !='')
						{
							$parse_fcm=json_decode($req_details->cus_ios_fcm_id,true);
							$reg_id = array();
							if(count($parse_fcm) > 0 )
							{
								foreach($parse_fcm as $parsed)
								{ 
									array_push($reg_id,$parsed['fcm_id']);						
								}
							}
							$json_data = [
											"registration_ids" => $reg_id,
											"notification" => ["body" => $result,"title" => "Order Notification","sound"=> "default"],
											"data"			=> ['order_id' => $orderId,'store_id' => $storeId]
										];
							$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_CUS);
								
						}
					}
					/* send notification to customer mobile ends */
				}
				//5)agent
				if($this->agent_module=='1'){
					if(empty($details) === false)
					{
						push_notification($delivery_id,$agent_id,'gr_delivery_member','gr_agent',$result,$orderId,'');	
					}
				}
				/* send notification ends  */
				

			}
			else 	//reject order
			{
				$insertArr = array('ord_delboy_act_status' 	=> '2',
				'ord_delboy_rjct_reason'=>	$reason,
				'ord_delboy_rjct_time'	=>	date('Y-m-d H:i:s')
				);
				$data = array(
				'store_id'		=> $storeId,
				'order_id'		=> $orderId,
				'delboy_id'		=> $delivery_id,
				'reason'		=> $reason,
				'rejected_at'	=> date('Y-m-d H:i:s')
				);
				$res=insertvalues('gr_order_reject_history',$data);
				$msg = MobileModel::get_lang_text($lang,'API_OR_REJECT','Order has been rejected');
				/* send notification */
				$got_message = MobileModel::get_lang_text($lang,'API_TAKEN_BY',':transaction_id was taken by :del_boy_name');
				$searchReplaceArray = array(':del_boy_name' => ucfirst($delivery_name),':transaction_id' => $orderId);
				$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
				if($this->agent_module == 1)
				{
					push_notification($delivery_id,$agent_id,'gr_delivery_member','gr_agent',$result,$orderId,'');
				}
				/* send notification ends */
			}
			$update = updatevalues('gr_order',$insertArr,['ord_rest_id'=>$storeId,'ord_transaction_id'=>$orderId]);
			$encode = [ 'code' => 200,'message' => $msg,'data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
		/* accept order management */
		public function order_management(Request $request)
		{
			$lang = $request->lang; 
			$page = ($request->page_no == '') ? 1 : $request->page_no;
			$details	 	= JWTAuth::user();
			$agent_id		= $details->deliver_agent_id;
			$deliver_id		= $details->deliver_id;
			$admin_default_lang = $this->admin_default_lang;
			$from_date		= ($request->from != '') ?date('Y-m-d H:i:s',strtotime($request->from)):'';
			$to_date		= ($request->to == '') ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',strtotime($request->to));
			$search			= $request->search_text; 
			/*GET CUSTOMER DETAIL WITH LOCATION, ORDER ID, ORDER DATE, RESTAURANT/STORE NAME WITH LOCATION, VIEW INVOICE LINK, SCHEDULE, ACTION TO ACCEPT/REJECT.*/
			$group_res = Delivery_person::get_order_details($lang,$admin_default_lang,$agent_id,$deliver_id,'1',$page,$from_date,$to_date,$search,'','ord_accepted_on',$this->agent_module);
			$max_count = 0;
			if(count($group_res) > 0)
			{
				$max_count = $group_res->lastPage;
				$newOrderArray = array();
				$text = '';
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
					switch($gres->ord_status)
					{
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
						$text = MobileModel::get_lang_text($lang,'API_DISPATCH','Dispatched');
					}		
					/* calculate order receivable amount */
					$cal = Delivery_person::get_receivable_amount($gres->ord_transaction_id,$gres->order_amount);
					//echo $walletFee; echo $delFee; exit;
					$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
					$totalReceivable = $gres->order_amount-$explode[1]+$explode[0];			
					$newOrderArray[] = array('cusId'		=> $gres->ord_cus_id,
					'cusName'		=>$gres->ord_shipping_cus_name,
					'cusAddress'	=>$gres->ord_shipping_address,
					'cusMobile1'	=>$gres->ord_shipping_mobile,
					'cusMobile2'	=>$gres->ord_shipping_mobile1,
					'cusEmail'		=>$gres->order_ship_mail,
					'cusLatitude'	=>$gres->order_ship_latitude,
					'cusLongitude'	=>$gres->order_ship_longitude,
					'orderId'		=>$gres->ord_transaction_id,
					'payType'		=>$gres->ord_pay_type,
					'ordStatus'	=>$text,
					'ordCurrency'	=>$gres->ord_currency,
					'totalReceivableAmount'	=>($gres->ord_pay_type == "COD") ? number_format($totalReceivable,2) : "0.00",
					'orderDate'	=>date('m/d/Y H:i:s',strtotime($gres->ord_date)),
					'orderSchedule'=> $schedule,
					'storeId'		=>$gres->storeId,
					'storeName'	=>$gres->storeName,
					'storeAddress'	=>$gres->st_address,
					'storeLatitude'=>$gres->st_latitude,
					'storeLongitude'=>$gres->st_longitude,
					'storeDelivery_time'=>$gres->st_delivery_time,
					'storeDelivery_duration'=>$gres->st_delivery_duration,
					'preOrderDate'	=>$gres->ord_pre_order_date,
					'merchantName'	=>$gres->mer_fname.' '.$gres->mer_lname
					);
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['OrderList'=>$newOrderArray,'max_page_count' => $max_count];
				$encode = ['code'=>200,"message"=>$msge,'data'=>$data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* change order status */
		public function change_order_status(Request $request)
		{
			$lang 		= $request->lang;
			$status 	= $request->status;
			$order_id 	= $request->order_id;
			//$cus_id		= $request->customer_id;  
			$store_id 	= $request->store_id;
			$reason 	= $request->reason;
			/*$latitude 	= $request->latitude;
			$longitude 	= $request->longitude;*/
			//$pay_type 	= $request->pay_type;
			$otp 		= $request->otp;
			//$received_amt	= $request->received_amt;
			$details	 	= JWTAuth::user();
			$agent_id		= $details->deliver_agent_id;
			$deliver_id		= $details->deliver_id;
			$fare_charge	= ($details->deliver_fare_type == 'per_km') ? $details->deliver_perkm_charge : $details->deliver_permin_charge;
			//$travel_duration = $request->travel_duration;
			//$travel_distance = $request->travel_distance;
			$arr = array();
			$msg = '';
			//$get_details = Delivery_person::get_otp_status($order_id,$deliver_id,$store_id);
			//print_r($get_details);  exit;
			
			/* validation */
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_OR_ID_NT_EXIST','Order id does not exist');       
            $storeId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store/restaurant ID!');            
            $storeId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid store/restaurant ID!');
            $orderStatus_valid_err_msg	= MobileModel::get_lang_text($lang,'API_SL_OR_STATUS','Please select order status');
            $reason_req_err_msg	= MobileModel::get_lang_text($lang,'API_FAIL_REASON','Please enter delivery failed reason');
            $dur_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTR_TRAVEL_DUR','Please enter travel duration');
            $dis_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTR_TRAVEL_DIS','Please enter travel distance');
			$validator = Validator::make($request->all(),[ 'store_id'	=> ['required',
			Rule::exists('gr_order','ord_rest_id')->where(function ($query) use($deliver_id,$order_id) { $query->where('ord_delivery_memid','=',$deliver_id)->where('ord_transaction_id','=',$order_id); })
			],
			'order_id'	=> ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function ($query) use($deliver_id) { $query->where('ord_delivery_memid','=',$deliver_id); })
			],
			/*'customer_id'	=> ['required',
				Rule::exists('gr_order','ord_cus_id')->where(function ($query) use($deliver_id) { $query->where('ord_delivery_memid','=',$deliver_id); })
			],*/
			'status'	=> ['required','integer',
			Rule::in([6,7,8,9])],
			//'latitude'	=> 'required',
			//'longitude'	=> 'required',
			//'pay_type'		=> 'required', 
			//'received_amt'	=> 'required_if:pay_type,COD|required_if:status,9', 
			'reason'	=> 'required_if:status,9', 
			'otp'		=> 'required_if:status,8',
			//'travel_duration'	=> 'required_if:status,8|required_if:status,9',
			//'travel_distance'	=> 'required_if:status,8|required_if:status,9',
			
			],[ 'store_id.required'	=> $storeId_req_err_msg,
			'store_id.exists'	=> $storeId_valid_err_msg,
			'order_id.required'	=> $orderId_req_err_msg,
			'order_id.exists'	=> $orderId_valid_err_msg,
			//'orderId.integer'	=> $orderId_valid_err_msg,
			'status.required'	=> $orderStatus_valid_err_msg,
			'status.integer'	=> $orderStatus_valid_err_msg,
			'reason.required_if'	=> $reason_req_err_msg,
			'travel_duration.required_if'	=> $dur_req_err_msg,
			'travel_distance.required_if'	=> $dis_req_err_msg,
			] 
			);
			/*$validator->sometimes('received_amt', 'required', function($request) {
				return ($request->pay_type == 'COD' && $request->status == '8');
			});*/
			
            if($validator->fails())
            {
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			/*  get old order status */
			$get_status = get_details('gr_order',['ord_transaction_id' => $order_id,'ord_delivery_memid' => $deliver_id,'ord_rest_id' =>$store_id],'ord_status');
			$got_message = '';
			$notification = '';
			$code = 200;
			$travel_duration = 0;
			$travel_distance = 0;
			if($get_status->ord_status >= $status)
			{
				$encode = [ 'code' => 400,'message' => MobileModel::get_lang_text($lang,'API_CANT_CH_PREVIOUS','Can\'t change to previous status'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 
			if(($get_status->ord_status == '5' && $status > 6) || ($get_status->ord_status == '6' && $status > 7)) //can update status one by one
			{
				$encode = [ 'code' => 400,'message' => MobileModel::get_lang_text($lang,'API_UPDATE_STATUS_ONE','Please update status one by one'),'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			} 
			if($status == 6)	/* delivery boy pickup the item and started to deliver  */
			{
				$arr = ['ord_status' => $status,'ord_started_on' => date('Y-m-d H:i:s')];
				$msg = MobileModel::get_lang_text($lang,'API_OR_START','Started to deliver');
				$got_message = MobileModel::get_lang_text($lang,'API_OUT_DELI','Order(:transaction_id) is out of delivery');
			}
			elseif($status == 7)	/* delivery boy arrived to customer location */
			{ 
				$arr = ['ord_status' => $status,'ord_arrived_on' => date('Y-m-d H:i:s')];
				$msg = MobileModel::get_lang_text($lang,'API_OR_ARRIVED','Arrived to customer location');
				$got_message = MobileModel::get_lang_text($lang,'API_ARR','Order(:transaction_id) has been arrived');
			}
			elseif($status == 8)	/* order is delivered  */
			{	
				/* check otp */
				//echo $order_id."/".$cus_id."/".$deliver_id."/".$store_id;
				$get_details = Delivery_person::get_otp_status($order_id,$deliver_id,$store_id);
				//print_r($get_details);  exit;
				
				if(empty($get_details) === false)
				{	
					/* get agent fare details */
					$agent_details = Delivery_person::get_basic_details('gr_delivery_member',['deliver_id' => $deliver_id],['deliver_base_fare','deliver_fare_type','deliver_perkm_charge','deliver_permin_charge']);			
					/*  get delivery fee and wallet */
					$cal = Delivery_person::get_receivable_amount($order_id,$get_details->order_amount);
					//echo $walletFee; echo $delFee; exit;
					$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
					$totalReceivable = $get_details->order_amount-$explode[1]+$explode[0];
					// echo $get_details->order_amount; 
					// echo $totalReceivable;
					if($get_details->ord_otp == $otp)
					{	
						/*if($totalReceivable == $received_amt)
						{*/
						$arr = ['ord_status' 		=> $status,
						'ord_delivered_on' => date('Y-m-d H:i:s')];
						$msg = MobileModel::get_lang_text($lang,'API_OR_DELIVERED','Order delivered successfully');
						$got_message = MobileModel::get_lang_text($lang,'API_DELI','Order(:transaction_id) has been delivered');
						$get_order_details = Delivery_person::get_ord_details($order_id,$store_id,$deliver_id,$lang);  
						$get_cus_details = Delivery_person::get_customer_details($order_id);
						
						//print_r($get_cus_details); exit;
						/* send  mail to customer */
						$cus_mail = $get_cus_details->order_ship_mail;
						$admin_mail = $this->admin_mail;
						$mer_mail = $get_details->mer_email; 
						//echo $cus_mail.'/'.$admin_mail.'/'.$mer_mail; exit;
						$send_mail_data = array('order_details'		=> $get_order_details,
						'transaction_id'		=> $order_id,
						'lang'					=> $lang    
						);							
						Mail::send('email.mobile_order_delivered_customer', $send_mail_data, function($message) use($cus_mail,$lang)
						{	
							$msg = MobileModel::get_lang_text($lang,'API_OR_HAS_DELIVER','Order has been delivered');
							$message->to($cus_mail)->subject($msg);
						});
						/* send mail to admin */
						$send_mail_data = array('order_details'		=> $get_order_details,
						'transaction_id'		=> $order_id,
						'customer_details'		=> $get_cus_details ,
						'lang'					=> $lang              	
						);
						Mail::send('email.mobile_order_delivered_admin', $send_mail_data, function($message) use($admin_mail,$lang)
						{	
							$msg = MobileModel::get_lang_text($lang,'API_OR_HAS_DELIVER','Order has been delivered');
							$message->to($admin_mail)->subject($msg);
						});
						/* send mail to merchant */
						$send_mail_data = array('order_details'		=> $get_order_details,
						'transaction_id'		=> $order_id,
						'customer_details'		=> $get_cus_details,
						'lang'					=> $lang               	
						);
						Mail::send('email.mobile_order_delivered_admin', $send_mail_data, function($message) use($mer_mail,$lang)
						{	
							$msg = MobileModel::get_lang_text($lang,'API_OR_HAS_DELIVER','Order has been delivered');
							$message->to($mer_mail)->subject($msg);
						});
						
						/* add commission to delivery boy */
						$total_comm = 0;
						
						$dist = GetDrivingDistance($get_order_details[0]->st_latitude,$get_cus_details->order_ship_latitude,$get_order_details[0]->st_longitude,$get_cus_details->order_ship_longitude);
						//print_r($dist); exit;
						if(!empty($dist))
						{
							$travel_distance = round(($dist['distance'] / 1000),2);
							$travel_duration = round($dist['time'] / 60);
						}
						if($details->deliver_fare_type == 'per_km')
						{
							$total_comm = $fare_charge * $travel_distance;
						}
						elseif($details->deliver_fare_type == 'per_min')
						{
							$total_comm = $fare_charge * $travel_duration;
						}	
						$insertArr = ['de_deliver_id' => $deliver_id,
						'de_agent_id'	  => $agent_id,
						'de_transaction_id'	=> $order_id,
						'de_shop_id'	=> $store_id,
						'de_merchant_id'	=>	$get_details->ord_merchant_id,
						'de_base_fare'	=> $details->deliver_base_fare,
						'de_unit_type'	=> $details->deliver_fare_type,
						'de_unit_fare'	=> ($details->deliver_fare_type == 'per_km') ? $details->deliver_perkm_charge : $details->deliver_permin_charge,
						'de_travel_duration'=>$travel_duration,
						'de_travel_distance'=>$travel_distance,
						'de_total_amount' => ($details->deliver_base_fare + $total_comm),
						'de_ord_sub_total'	=> $get_details->order_amount,
						'de_ord_wallet'		=> $explode[1],
						'de_ord_delfee'		=> $explode[0],
						'de_order_total'	=> $totalReceivable,
						'de_ord_status'		=> '1',
						'de_rcd_amt'		=> ($get_details->ord_pay_type =="COD") ? "0.00" : $totalReceivable,
						'de_ord_currency'	=> $get_details->ord_currency,
						'de_ord_currcode'	=> $this->default_curr_code,
						'de_pay_type'		=> $get_details->ord_pay_type,
						'de_updated_at'		=> date('Y-m-d H:i:s')
						];
						//print_r($insertArr); exit;
						insertvalues('gr_delivery_person_earnings',$insertArr);
						/*  add commission to agent */								
						
						if(empty($agent_details) === false && $this->agent_module==1)
						{	
							$total_comm = 0;
							$fare_charge = ($agent_details->agent_fare_type == 'per_km') ? $agent_details->agent_perkm_charge : $agent_details->agent_permin_charge;
							if($agent_details->agent_fare_type == 'per_km')
							{
								$total_comm = $fare_charge * $travel_distance;
							}
							elseif($agent_details->agent_fare_type == 'per_min')
							{
								$total_comm = $fare_charge * $travel_duration;
							}
							$insertArr = ['ae_agent_id'	=>$agent_id,
							'ae_deliver_id'	=> $deliver_id,
							'ae_transaction_id'	=> $order_id,
							'ae_shop_id'	=> $store_id,
							'ae_merchant_id'	=>$get_details->ord_merchant_id,
							'ae_base_fare'	=>	$agent_details->agent_base_fare,
							'ae_unit_type'	=> $agent_details->agent_fare_type,
							'ae_unit_fare'	=> ($agent_details->agent_fare_type == 'per_km') ? $agent_details->agent_perkm_charge : $agent_details->agent_permin_charge,
							'ae_travel_duration' => $travel_duration,
							'ae_travel_distance' => $travel_distance,
							'ae_total_amount'	=> ($agent_details->agent_base_fare + $total_comm),
							'ae_ord_sub_total'	=> $get_details->order_amount,
							'ae_ord_wallet'		=> $explode[1],
							'ae_ord_delfee'		=> $explode[0],
							'ae_order_total'	=> $totalReceivable,
							'ae_ord_status'		=> '1',
							'ae_rcd_amt'		=>	($get_details->ord_pay_type =="COD") ? "0.00" : $totalReceivable,
							'ae_ord_currency'	=> $get_details->ord_currency,
							'ae_pay_type'		=> $get_details->ord_pay_type,
							'ae_updated_at'		=> date('Y-m-d H:i:s')
							];
							insertvalues('gr_agent_earnings',$insertArr);
						}
						
						/*}
							else
							{
							return response()->json(array('code'=>400,"message"=>MobileModel::get_lang_text($lang,'API_INVALID_AMT','Invalid Amount')));
						}*/
					}
					else
					{
						$encode = ['code'=>400,"message"=>MobileModel::get_lang_text($lang,'API_WR_OTP','Wrong otp'),'data' => $this->empty_data];
						return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
					}
				}
				else
				{
					$encode = ['code'=>400,"message"=>MobileModel::get_lang_text($lang,'API_NT_OTP_SENT','Otp not sent'),'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
			}
			elseif($status == 9) 	/* order delivery failed */
			{	
				$code = 400;
				$arr = ['ord_status' 		=> $status,
				'ord_failed_reason'	=> $reason,
				'ord_failed_on' 	=> date('Y-m-d H:i:s')];
				$msg = MobileModel::get_lang_text($lang,'API_OR_FAILED','Order failed');
				$got_message = MobileModel::get_lang_text($lang,'API_DEL_FAIL','Order(:transaction_id) has been failed');
				$get_details = Delivery_person::get_otp_status($order_id,$deliver_id,$store_id);
				$get_order_details = Delivery_person::get_ord_details($order_id,$store_id,$deliver_id,$lang);  
				$get_cus_details = Delivery_person::get_customer_details($order_id);
				$provide_commission	= Delivery_person::get_basic_details('gr_delivery_manager',['dm_id'=> $get_details->ord_delmgr_id],'dm_commission_status');
				//print_r($get_cus_details); exit;
				/* send  mail to customer */
				$cus_mail = $get_cus_details->order_ship_mail;
				$admin_mail = $this->admin_mail;
				$mer_mail = $get_details->mer_email;
				$send_mail_data = array('order_details'		=> $get_order_details,
				'transaction_id'		=> $order_id,
				'lang'					=> $lang,
				'reason'				=> $reason   
				);							
                Mail::send('email.mobile_order_failed_customer', $send_mail_data, function($message) use($cus_mail,$lang)
                {	
                	$msg = MobileModel::get_lang_text($lang,'API_OR_HAS_FAIL','Order has been failed');
                	$message->to($cus_mail)->subject($msg);
				});
                /* send mail to admin */
                $send_mail_data = array('order_details'		=> $get_order_details,
				'transaction_id'		=> $order_id,
				'customer_details'		=> $get_cus_details ,
				'lang'					=> $lang,
				'reason'				=> $reason              	
				);
                Mail::send('email.mobile_order_failed_admin', $send_mail_data, function($message) use($admin_mail,$lang)
                {	
                	$msg = MobileModel::get_lang_text($lang,'API_OR_HAS_FAIL','Order has been failed');
                	$message->to($admin_mail)->subject($msg);
				});
                /* send mail to merchant */
                $send_mail_data = array('order_details'		=> $get_order_details,
				'transaction_id'		=> $order_id,
				'customer_details'		=> $get_cus_details,
				'lang'					=> $lang,
				'reason'				=> $reason               	
				);
                Mail::send('email.mobile_order_failed_admin', $send_mail_data, function($message) use($mer_mail,$lang)
                {	
                	$msg = MobileModel::get_lang_text($lang,'API_OR_HAS_FAIL','Order has been failed');
                	$message->to($mer_mail)->subject($msg);
				});
                /* get agent fare details */
				$agent_details = Delivery_person::get_basic_details('gr_agent',['agent_id' => $agent_id],['agent_base_fare','agent_fare_type','agent_perkm_charge','agent_permin_charge']);	
                /*  get delivery fee and wallet */
				$cal = Delivery_person::get_receivable_amount($order_id,$get_details->order_amount);
				//echo $walletFee; echo $delFee; exit;
				$explode = explode('~',$cal); /* $explode[0] = del_fee,$explode[1] = wallet */
				$totalReceivable = $get_details->order_amount-$explode[1]+$explode[0];
				
                /* If delveiry manager accept to provide commission even delivery failed, update commission for delivery boy and agent */
                if($provide_commission->dm_commission_status == '1')
                {	

					$dist = GetDrivingDistance($get_order_details[0]->st_latitude,$get_cus_details->order_ship_latitude,$get_order_details[0]->st_longitude,$get_cus_details->order_ship_longitude);
					//print_r($dist); exit;
					if(!empty($dist))
					{
						$travel_distance = round(($dist['distance'] / 1000),2);
						$travel_duration = round($dist['time'] / 60);
					}
                	/* add commission to delivery boy */
					$total_comm = 0;
					if($details->deliver_fare_type == 'per_km')
					{
						$total_comm = $fare_charge * $travel_distance;
					}
					elseif($details->deliver_fare_type == 'per_min')
					{
						$total_comm = $fare_charge * $travel_duration;
					}	
					$insertArr = ['de_deliver_id' => $deliver_id,
					'de_agent_id'	  => $agent_id,
					'de_transaction_id'	=> $order_id,
					'de_shop_id'	=> $store_id,
					'de_merchant_id'	=>	$get_details->ord_merchant_id,
					'de_base_fare'	=> $details->deliver_base_fare,
					'de_unit_type'	=> $details->deliver_fare_type,
					'de_unit_fare'	=> ($details->deliver_fare_type == 'per_km') ? $details->deliver_perkm_charge : $details->deliver_permin_charge,
					'de_travel_duration'=>$travel_duration,
					'de_travel_distance'=>$travel_distance,
					'de_total_amount' => ($details->deliver_base_fare + $total_comm),
					'de_ord_sub_total'	=> $get_details->order_amount,
					'de_ord_wallet'		=> $explode[1],
					'de_ord_delfee'		=> $explode[0],
					'de_order_total'	=> $totalReceivable,
					'de_ord_status'		=> '2',
					'de_rcd_amt'		=> ($get_details->ord_pay_type =="COD") ? "0.00" : $totalReceivable,
					'de_ord_currency'	=> $get_details->ord_currency,
					'de_ord_currcode'	=> $this->default_curr_code,
					'de_pay_type'		=> $get_details->ord_pay_type,
					'de_updated_at'		=> date('Y-m-d H:i:s')
					];
					insertvalues('gr_delivery_person_earnings',$insertArr);
					/*  add commission to agent */								
					
					if(empty($agent_details) === false && $this->agent_module==1)
					{	
						$total_comm = 0;
						$fare_charge = ($agent_details->agent_fare_type == 'per_km') ? $agent_details->agent_perkm_charge : $agent_details->agent_permin_charge;
						if($agent_details->agent_fare_type == 'per_km')
						{
							$total_comm = $fare_charge * $travel_distance;
						}
						elseif($agent_details->agent_fare_type == 'per_min')
						{
							$total_comm = $fare_charge * $travel_duration;
						}
						$insertArr = ['ae_agent_id'	=>$agent_id,
						'ae_deliver_id'	=> $deliver_id,
						'ae_transaction_id'	=> $order_id,
						'ae_shop_id'	=> $store_id,
						'ae_merchant_id'	=>$get_details->ord_merchant_id,
						'ae_base_fare'	=>	$agent_details->agent_base_fare,
						'ae_unit_type'	=> $agent_details->agent_fare_type,
						'ae_unit_fare'	=> ($agent_details->agent_fare_type == 'per_km') ? $agent_details->agent_perkm_charge : $agent_details->agent_permin_charge,
						'ae_travel_duration' => $travel_duration,
						'ae_travel_distance' => $travel_distance,
						'ae_total_amount'	=> ($agent_details->agent_base_fare + $total_comm),
						'ae_ord_sub_total'	=> $get_details->order_amount,
						'ae_ord_wallet'		=> $explode[1],
						'ae_ord_delfee'		=> $explode[0],
						'ae_order_total'	=> $totalReceivable,
						'ae_ord_status'		=> '2',
						'ae_rcd_amt'		=> ($get_details->ord_pay_type =="COD") ? "0.00" : $totalReceivable,
						'ae_ord_currency'	=> $get_details->ord_currency,
						'ae_pay_type'		=> $get_details->ord_pay_type,
						'ae_updated_at'		=> date('Y-m-d H:i:s')
						];
						insertvalues('gr_agent_earnings',$insertArr);
					}
				}
			}
			/*$arr['ord_deliver_latitude'] = $latitude;
			$arr['ord_deliver_longitude'] = $longitude;*/
			updatevalues('gr_order',$arr,['ord_transaction_id' => $order_id,'ord_rest_id' => $store_id,'ord_delivery_memid' => $deliver_id]);
			/* send notification */	
			$searchReplaceArray = array(':transaction_id' => $order_id);
			$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);			 
			$req_details = Delivery_person::get_related_details($order_id,$store_id);
			//1)admin
			$message_link = 'admin-track-order/'.base64_encode($order_id);
			push_notification($deliver_id,$this->admin_id,'gr_delivery_member','gr_admin',$result,$order_id,$message_link);
			
			if(empty($req_details) === false)
			{
				//2)merchant
				$message_link1 = 'mer-order-details/'.base64_encode($order_id);
				push_notification($deliver_id,$req_details->ord_merchant_id,'gr_delivery_member','gr_merchant',$result,$order_id,$message_link1);
				
				//3)delivery manager
				
				$message_link2 = 'delivery-track-order/'.base64_encode($order_id).'/'.base64_encode($req_details->ord_merchant_id);
				push_notification($deliver_id,$req_details->ord_delmgr_id,'gr_delivery_member','gr_delivery_manager',$result,$order_id,$message_link2);	
				
				//4)customer
				
				$message_link3 = 'track-order/'.base64_encode($order_id);
				push_notification($deliver_id,$req_details->ord_cus_id,'gr_delivery_member','gr_customer',$result,$order_id,$message_link3);	
				/* send notification to customer mobile */
				
				if($req_details->cus_andr_fcm_id != '' || $req_details->cus_ios_fcm_id != '')
				{
					if($req_details->cus_andr_fcm_id !='')
					{
						$parse_fcm=json_decode($req_details->cus_andr_fcm_id,true);
						$reg_id = array();
						if(count($parse_fcm) > 0 )
						{
							foreach($parse_fcm as $parsed)
							{ 
								array_push($reg_id,$parsed['fcm_id']);						
							}
						}
						$json_data = [
										"registration_ids" => $reg_id,
										"notification" => ["body" => $result,"title" => "Order Notification"],
										"data"		=> ['order_id' => $order_id,'store_id' => $store_id]
									];
						$notify = $this->sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_CUS);
							
					}
					if($req_details->cus_ios_fcm_id !='')
					{
						$parse_fcm=json_decode($req_details->cus_ios_fcm_id,true);
						$reg_id = array();
						if(count($parse_fcm) > 0 )
						{
							foreach($parse_fcm as $parsed)
							{ 
								array_push($reg_id,$parsed['fcm_id']);						
							}
						}
						$json_data = [
										"registration_ids" => $reg_id,
										"notification" => ["body" => $result,"title" => "Order Notification","sound"=> "default"],
										"data"			=> ['order_id' => $order_id,'store_id' => $store_id]
									];
						$notify = $this->sendPushNotification($json_data,IOS_FIREBASE_API_KEY_CUS);
							
					}
				}
				/* send notification to customer mobile ends */
			}
			//5)agent
			if($this->agent_module == 1)
			{
				push_notification($deliver_id,$agent_id,'gr_delivery_member','gr_agent',$result,$order_id,'');	
			}
			
			/* send notification ends  */
			
			$encode = ['code'=>$code,"message"=>$msg,'data' => $this->empty_data];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
		/* send otp */
		public function send_otp(Request $request)
		{
			$lang 			= $request->lang;
			$cus_id 		= $request->customer_id;
			$cus_phone 		= $request->customer_phone;
			$ord_id 		= $request->order_id;
			$details	 	= JWTAuth::user();
			$agent_id		= $details->deliver_agent_id;
			$deliver_id		= $details->deliver_id;
			$otp = mt_rand(100000, 999999);
			$text  =  MobileModel::get_lang_text($lang,'API_OTP_IS','Your OTP is');
			$msg = $text.$otp;
			$validator = Validator::make($request->all(),[ 'order_id'	=> 'required',
			'customer_id'	=> ['required',
			Rule::exists('gr_order','ord_cus_id')->where(function ($query) use($deliver_id) {
				$query->where('ord_delivery_memid','=',$deliver_id);
			})],
			'customer_phone'	=> ['required'
			/*Rule::exists('gr_order','ord_shipping_mobile')->where(function ($query) use($deliver_id) {
				$query->where('ord_delivery_memid','=',$deliver_id);
			})*/]]
			);
			if($validator->fails())
            {
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			try{
				Twilio::message($cus_phone, $msg);
				updatevalues('gr_order',['ord_otp' => $otp],['ord_transaction_id' => $ord_id,'ord_cus_id' => $cus_id,'ord_delivery_memid' => $deliver_id]);
				$encode = ['code' 	=> 200,
				'message'	=> MobileModel::get_lang_text($lang,'API_OTP_SENT_SUXES','OTP sent successfully!'),
				'data'  => ['otp'		=> $otp]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
				
			}
			catch (\Exception $e)
			{		
				/*----- hide for testing twilio in test account.And enable it while uses live twilio account -------*/
				/*$jsonArr = array('code' => 400,'message'=>$e->getMessage(),'data' => $this->empty_data);
				return Response::make(json_encode($jsonArr,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");*/
				updatevalues('gr_order',['ord_otp' => $otp],['ord_transaction_id' => $ord_id,'ord_cus_id' => $cus_id,'ord_delivery_memid' => $deliver_id]);
				$encode = ['code' 	=> 200,
				'message'	=> MobileModel::get_lang_text($lang,'API_OTP_SENT_SUXES','OTP sent successfully!'),
				'data'  => ['otp'		=> $otp]];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		/* dashboard */
		public function dashboard(Request $request)
		{
			$lang 			= $request->lang;
			$details	 	= JWTAuth::user();
			$agent_id		= $details->deliver_agent_id;
			$deliver_id		= $details->deliver_id;
			$wk_hrs_details = DB::table('gr_deliver_working_hrs')->where('dw_deliver_id','=',$deliver_id)->count();
			$wk_hr_status	= ($wk_hrs_details == 0) ? 'Not updated' : 'Updated';
			$total_orders 	= Delivery_person::get_orders_count($agent_id,$deliver_id,'','','',$this->agent_module);
			$new_orders 	= Delivery_person::get_orders_count($agent_id,$deliver_id,'0',4,'',$this->agent_module);
			$pending_orders = Delivery_person::get_orders_count($agent_id,$deliver_id,'1','',[5,6,7],$this->agent_module);
			$delivered_orders = Delivery_person::get_orders_count($agent_id,$deliver_id,'1',8,'',$this->agent_module);
			$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
			$data = ['total_orders'		=> $total_orders,
			'new_orders'		=> $new_orders,
			'processing_orders'	=> $pending_orders,
			'delivered_orders'	=> $delivered_orders,
			'wk_hr_status'		=> $wk_hr_status,
			];
			$encode = ['code' => 200,'message' 			=> $msge,'data' => $data
			];
			return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}	
		
		/* invoice detail */
		public function invoice_detail(Request $request)
		{
			$lang 			= $request->lang;
			$order_id 		= $request->order_id;
			$store_id 		= $request->store_id;
			$details	 	= JWTAuth::user();
			$agent_id		= $details->deliver_agent_id;
			$deliver_id		= $details->deliver_id;
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');
			$validator = Validator::make($request->all(),['order_id'	=> ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function ($query) use($deliver_id) {
				$query->where('ord_delivery_memid','=',$deliver_id);
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
			$Invoice_Order = Delivery_person::get_invoice($order_id,$store_id,$deliver_id,$lang);
			//print_r($Invoice_Order); 
			if(count($Invoice_Order)>0)
			{	
				$Order = $Invoice_Order[0];
				/* get total amount */
				$totalOrderAmount = DB::table('gr_order')->select(DB::raw('SUM(gr_order.ord_grant_total) As total_order_amount'),'gr_order.ord_wallet','gr_order.ord_delivery_fee')
				->where('ord_self_pickup','!=','1')
				->where('ord_transaction_id','=',$order_id)
				//->where('ord_rest_id','=',$gres->storeId)
				->groupBy('ord_transaction_id')
				->first();
				//echo $totalOrderAmount->total_order_amount; exit;
				/*print_r($totalOrderAmount);
				exit;         stdClass Object([total_order_amount] => 36.70 [ord_wallet] => 0.00 [ord_delivery_fee] => 5.00 )*/
				
				/*WE WILL DISPLAY DELIVERY FEE AND WALLET AMOUNT FOR INDIVIDUAL STORE ORDER. IF ORDER HAS FAILED, DEL BOY CAN'T GET DELIVREY FEE AND REMAINING AMOUNT */
				if(empty($totalOrderAmount)==true){
					$orderAmount_cal = $orderWallet_cal = $walletFee = $delFee = $orderDeliveryFee_cal = 0;
				}
				else{	
					$orderAmount_cal = $totalOrderAmount->total_order_amount;
					$orderWallet_cal = $totalOrderAmount->ord_wallet;
					$orderDeliveryFee_cal = $totalOrderAmount->ord_delivery_fee;
					if($orderDeliveryFee_cal > 0 ) 
					{ $delFee = $Order->order_amount * ($orderDeliveryFee_cal/$orderAmount_cal); }
					else 
					{ $delFee= 0; }
					if($orderWallet_cal > 0 ) 
					{ $walletFee = $Order->order_amount * ($orderWallet_cal/$orderAmount_cal); } 
					else 
					{ $walletFee = 0; } 
				}
				//echo $walletFee; echo $delFee; exit;
				$totalReceivable = $Order->order_amount-$walletFee+$delFee;
				
				$order_date = $Order->ord_date;
				
				$paytype = $Order->ord_pay_type;
				if($Order->ord_shipping_cus_name!='' && $Order->ord_shipping_address!=''  && $Order->ord_shipping_mobile!='' && $Order->ord_self_pickup!=1)
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
				if($Order->ord_status == '8' && $Order->dm_cust_data_protect == '1') //if customer data protection is enable, no need to show customer details
				{
					$customerDetailArray = (object)[];
				}
				else
				{
					$customerDetailArray = array('customeName'	=>$OrderCustomerName,
											'customerAddress1'	=>$OrderCustomerAddress,
											'customerAddress2'	=>$OrderCustomerAddress1,
											'customerMobile'	=>$OrderCustomerMobile,
											'customerEmail'		=>$OrderCustomerEmail
											);
				}
				
				
				$order_detailArray =array();
				$sub_total=$grand_total=$tax_total=$shipping_total=0;
				foreach($Invoice_Order as $Order_sub)
				{
					
					$ordersArray = array();
					$calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
					$sub_total +=$calc_sub_total;
					$shipping_total =$Order_sub->ord_delivery_fee;
					$cancelled_reason = '';
					$ordersArray['store_name'] 		= $Order_sub->st_store_name;
					$ordersArray['store_location'] 		= $Order_sub->st_address;
					$ordersArray['item_name'] 		= $Order_sub->pro_item_name;
					if($Order_sub->ord_spl_req != ''){
						$ordersArray['specialRequest'] = $Order_sub->ord_spl_req;
					}
					else {
						$ordersArray['specialRequest'] = '';
					}
					$ordersArray['ord_quantity'] 	= $Order_sub->ord_quantity;
					$ordersArray['ord_unit_price'] 	= $Order_sub->ord_unit_price;
					$ordersArray['ord_tax_amt'] 	= $Order_sub->ord_tax_amt;
					$ordersArray['sub_total'] 		= $calc_sub_total;
					$ordersArray['ord_currency'] 	= $Order_sub->ord_currency;
					if($Order_sub->ord_pre_order_date != '')
					{
						$ordersArray['pre_order_date'] = date('m/d/Y H:i:s',strtotime($Order_sub->ord_pre_order_date));
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
									$sub_total +=$choice['choice_price'];  $grand_total +=$choice['choice_price']; 
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
				'customerDetailArray'	=> $customerDetailArray,
				'order_detailArray' 	=> $order_detailArray,
				'grand_sub_total'		=> $sub_total,
				'delivery_fee' 			=> number_format($delFee,2),
				'wallet_used'			=> number_format($walletFee,2),
				'totalReceivableAmount'	=> number_format($totalReceivable,2)];
				$encode = ['code' => 200,'message' => $msge,
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
				if($ph_type == 'android' && $andr_device_id != '' && $user->deliver_andr_fcm_id!=''){ 
					$andr_fcm_arr = json_decode($user->deliver_andr_fcm_id,true);
					//print_r($andr_fcm_arr); exit;
					$newArray = removeElementWithValue($andr_fcm_arr, "device_id", $andr_device_id);
					$updatableArray = array('deliver_andr_fcm_id' =>json_encode($newArray));
					updatevalues('gr_delivery_member',$updatableArray,['deliver_status' => '1','deliver_email' => $user->deliver_email]);
				}
				if($ph_type == 'ios' && $ios_device_id != '' && $user->deliver_ios_fcm_id != ''){
					$ios_fcm_arr = json_decode($user->deliver_ios_fcm_id,true);
					$newArray = removeElementWithValue($ios_fcm_arr, "device_id", $ios_device_id);
					$updatableArray = array('deliver_ios_fcm_id' =>json_encode($newArray));
					updatevalues('gr_delivery_member',$updatableArray,['deliver_status' => '1','deliver_email' => $user->deliver_email]);
				}
				JWTAuth::invalidate($request->token);
				$msge = MobileModel::get_lang_text($lang,'API_LOGOUT_SUXES','User logged out successfully!');
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				} catch (JWTException $exception) {
				$msge = MobileModel::get_lang_text($lang,'API_CANNOT_LOGOUT','Sorry, the user cannot be logged out!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		/* earning report */
		public function earning_report(Request $request)
		{
			$lang 		= $request->lang;
			$det 		= JWTAuth::user();
			$deliver_id = $det->deliver_id;
			$deliver_curr = $det->deliver_currency_code;
			$from_date = '';
			$to_date   = '';
			$date_err_msg = MobileModel::get_lang_text($lang,'API_FILL_VA_DATE','Fill date in valid fomat(Y-m-d)');
			$validator = Validator::make($request->all(),['from_date'=> 'sometimes|nullable|date|date_format:Y-m-d'],
			['from_date.date_format' => $date_err_msg,
			'from_date.date' => $date_err_msg,
			]
			);
			if($validator->fails()){
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			if($request->from_date!=''){
				$from_date = date("Y-m-d", strtotime($request->from_date));
				$from_minus_date=date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $from_date) ) ));
			}
			if($request->to_date!=''){
				//after:'.$opportunity->created_at;
				$validator = Validator::make($request->all(),[ 'to_date'=> 'sometimes|date|date_format:Y-m-d|after:'.$from_minus_date.''],
				['to_date.date_format' => $date_err_msg,
				'to_date.date' => $date_err_msg,
				]);
				if($validator->fails()){
					$message = $validator->messages()->first();
					$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
					return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
				}
				$to_date   = date("Y-m-d", strtotime($request->to_date));
			}
			$get_total_earnings = Delivery_person::get_earning_details($deliver_id);
			$total_order_amount = 0;
			$total_commission   = 0;
			if(count($get_total_earnings) > 0 )
			{
				foreach($get_total_earnings as $earnings)
				{
					$total_order_amount +=$earnings->de_order_total;
					$total_commission +=$earnings->de_total_amount;
				}
			}
			$get_earning_details = Delivery_person::get_earning_details($deliver_id,$from_date,$to_date);
			$suc_msg = MobileModel::get_lang_text($lang,'API_OR_ST8','Delivered');
			$fail_msg = MobileModel::get_lang_text($lang,'API_FAILED','Failed');
			if(count($get_earning_details) <= 0)
			{
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				$encode = [ 'code' => 400,'message' => $msge,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			else
			{	
				$arr = array();
				$page_total_commission = 0;
				foreach($get_earning_details as $details)
				{
					$arr[] = ['order_delivered_date'=> $details->de_updated_at,
					'order_id' 			=> $details->de_transaction_id,
					'order_amount' 		=> number_format($details->de_order_total,2),
					'order_currency'		=> $details->de_ord_currency,
					'order_commission'		=> number_format($details->de_total_amount,2),
					'order_status'			=> ($details->de_ord_status == '1') ? $suc_msg : $fail_msg
					];
					$page_total_commission +=$details->de_total_amount;
				}
				$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
				$data = ["currency_code"			=> $deliver_curr,
				"grant_total_commission" => number_format($total_commission,2),
				"grant_total_order_amount" => number_format($total_order_amount,2),
				"reports" 				=>	$arr,
				"page_commission" 		=>	number_format($page_total_commission,2)];
				$encode = ['code'					=>	200,
				"message"				=>	$msge,
				'data'					=> $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		
		/* order tracking  */
		public function order_tracking(Request $request)
		{
			$lang 		= $request->lang;
			$order_id 	= $request->order_id;
			$latitude 	= $request->latitude;
			$longitude 	= $request->longitude;
			$store_id 	= $request->store_id;
			$deliver_id	= JWTAuth::user()->deliver_id;
			$orderId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_ORDER_ID','Please enter order ID!');
			$orderId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_ORDER_ID','Please enter valid order ID!');
			$stId_req_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_STOREREST_ID','Please enter store id!');
			$stId_valid_err_msg	= MobileModel::get_lang_text($lang,'API_ENTER_VALID_STOREREST_ID','Please enter valid stroe ID!');
			$validator = Validator::make($request->all(),['order_id' => ['required',
			Rule::exists('gr_order','ord_transaction_id')->where(function($query) use($deliver_id){ $query->where('ord_delivery_memid','=',$deliver_id);})
			],
			'store_id'	=> 'required|integer'],
			
			['order_id.required' => $orderId_req_err_msg,
			'order_id.exists'	=> $orderId_valid_err_msg,
			'store_id.required' => $stId_req_err_msg,
			'stroe_id.integer'	=> 	$stId_valid_err_msg
			]
			);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			$get_cus_details = Delivery_person::get_customer_details($order_id);
			if(empty($get_cus_details) === true)
			{
				$message = MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No Records Found');
				$encode = [ 'code' => 400,'message' => $message,'data' => $this->empty_data];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json"); 
			}
			else
			{
				$cus_id = $get_cus_details->ord_cus_id;
				$cus_details = ['cusId'			=> $get_cus_details->ord_cus_id,
				'cusName'		=>$get_cus_details->ord_shipping_cus_name,
				'cusAddress'	=>$get_cus_details->ord_shipping_address,
				'cusMobile1'	=>$get_cus_details->ord_shipping_mobile,
				'cusMobile2'	=>$get_cus_details->ord_shipping_mobile1,
				'cusEmail'		=>$get_cus_details->order_ship_mail,
				'cusLatitude'	=>$get_cus_details->order_ship_latitude,
				'cusLongitude'	=>$get_cus_details->order_ship_longitude];
				$store_details = Delivery_person::get_basic_details('gr_store',['id' => $store_id],['st_latitude','st_longitude']);
				$store = array();
				if(!empty($store_details))
				{
					$store = ['store_id'	 => $store_id,
					'store_latitude' => $store_details->st_latitude,
					'store_longitude' => $store_details->st_longitude];
				}
				//print_r($store_details); exit;
				$get_or_status = MobileModel::get_or_status($cus_id,$order_id,$store_id,$lang,$this->admin_default_lang);
				$pay_type = '';
				$or_details = array();
				if(count($get_or_status)>0)
				{
					$cancel_array = $status_array = array();
					foreach($get_or_status as $or)
					{
						array_push($cancel_array,$or->ord_cancel_status);
						array_push($status_array,$or->ord_status);
						if($or->ord_cancel_status == 0)
						{
							$final_status = $or->ord_status;
							$final_id	  = $or->ord_id;
						}
						$pay_type = $or->ord_pay_type;
					}
					if(!in_array('0',$cancel_array))	/* all items cancelled by merchant */
					{						
						$or_details[] = ['ord_stage'	=> 3,
						'ord_title'	=> MobileModel::get_lang_text($lang,'API_ORDER_CANCELLED','Order Cancelled!')
						];
					}
					elseif($final_status == '9')	/* all items cancelled by user */
					{	
						$or_details[] = ['ord_stage'	=> 9,
						'ord_title'	=> MobileModel::get_lang_text($lang,'API_FAIL','Delivery failed!')
						// 'ord_timing'	=> $get_status->$st
						];
					}
					else
					{	 
						$get_status = MobileModel::get_or_status($cus_id,$order_id,$store_id,$lang,$this->admin_default_lang,$final_id);
						
						if(empty($get_status) === false)
						{						
							/*$msg_array = ['1' => 'Order Placed','Order Confirmed','','Order Processed','Order Dispatched','Order Picked','Order Arrived','Order Delivered'];*/
							$msg_array = ['1' => 'New Order','Accepted','','Prepare to Deliver','Dispatched','Started','Arrived','Delivered'];
							$time_arr = ['1' => 'ord_placed_on','ord_accepted_on','','ord_prepared_on','ord_dispatched_on','ord_started_on','ord_arrived_on','ord_delivered_on'];
							//echo $msg_array[1];
							for($i=1;$i<=8;$i++)
							{ 
								if($i != 3)
								{	
									$st = $time_arr[$i];
									$or_details[] = ['ord_stage'	=> $i,
									'ord_title'	=> MobileModel::get_lang_text($lang,'API_OR_ST'.$i,$msg_array[$i]),
									'ord_timing'	=> ($get_status->$st != '') ? $get_status->$st : '',
									'stage_completed' => ($get_status->ord_status < $i) ? 'No' : 'Yes'
									];
								}
							}
							//exit;
						}
					}
				}
				$msge = MobileModel::get_lang_text($lang,'API_DET_AVAIL','Details Available');
				$data = ["customer_details"	=> $cus_details,
				"store_details"		=> $store,
				"order_id"	=> $order_id,
				"order_status_details"	=> $or_details,
				"pay_type"	=> $pay_type,
				"latitude"	=> $latitude,
				"longitude"	=> $longitude];
				$encode = ['code'=>200,"message"=>$msge,'data' => $data
				];
				return Response::make(json_encode($encode,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
		}
		//***** DELIVERY BOY Get Payment Setting  ****//
		public function get_payment_settings()
		{
			$delBoyDet = JWTAuth::user();
			$deliver_id	= $delBoyDet->deliver_id;
			$msge = 'Fetched Agent payments details succesfully!';
			$data  = [	'deliver_stripe_status'		=> $delBoyDet->deliver_stripe_status,
			'deliver_stripe_clientid'	=> ($delBoyDet->deliver_stripe_clientid == '') ? '' : $delBoyDet->deliver_stripe_clientid,
			'deliver_stripe_secretid'	=> ($delBoyDet->deliver_stripe_secretid == '') ? '' :$delBoyDet->deliver_stripe_secretid,
			'deliver_paypal_status'		=> $delBoyDet->deliver_paypal_status,
			'deliver_paypal_clientid'	=> ($delBoyDet->deliver_paypal_clientid == '') ? '' : $delBoyDet->deliver_paypal_clientid,
			'deliver_paypal_secretid'	=> ($delBoyDet->deliver_paypal_secretid == '') ? '' : $delBoyDet->deliver_paypal_secretid,
			'deliver_netbank_status'	=> $delBoyDet->deliver_netbank_status,
			'deliver_bank_name'			=> ($delBoyDet->deliver_bank_name == '') ? '' : $delBoyDet->deliver_bank_name,
			'deliver_branch'			=> ($delBoyDet->deliver_branch == '') ? '' : $delBoyDet->deliver_branch,
			'deliver_bank_accno'		=> ($delBoyDet->deliver_bank_accno == '') ? '' : $delBoyDet->deliver_bank_accno,
			'deliver_ifsc'				=> ($delBoyDet->deliver_ifsc == '') ? '' : $delBoyDet->deliver_ifsc  
			];
			$outputArray = array( 'code'					=> 200,
			'message'					=> $msge,
			'data'					=> $data	
			);
			return Response::make(json_encode($outputArray,JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
		}
		//***** DELIVERY BOY Update Payment Setting  ****//
		public function update_payment_setting(Request $request)
		{
			$delBoyDet = JWTAuth::user();
			$deliver_id	= $delBoyDet->deliver_id;
            $lang = $request->lang;           
			$deliver_stripe_status 	= $request->deliver_stripe_status;		
			$deliver_stripe_clientid= $request->deliver_stripe_clientid;		
			$deliver_stripe_secretid= $request->deliver_stripe_secretid;		
			
			$deliver_paypal_status 	= $request->deliver_paypal_status;		
			$deliver_paypal_clientid= $request->deliver_paypal_clientid;		
			$deliver_paypal_secretid= $request->deliver_paypal_secretid;		
			
			$deliver_netbank_status = $request->deliver_netbank_status;		
			$deliver_bank_name 		= $request->deliver_bank_name;		
			$deliver_branch 		= $request->deliver_branch;		
			$deliver_bank_accno 	= $request->deliver_bank_accno;		
			$deliver_ifsc 			= $request->deliver_ifsc;		
			
			
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

        
			if($deliver_paypal_status=='Unpublish' && $deliver_netbank_status=='Unpublish')

			{
				$msg = MobileModel::get_lang_text($lang,'API_FILL_PAYPAL_NET_DETAILS','Please Fill Paypal or Net Banking Details');
				return Response::make(json_encode(array('code'=>400,"message"=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if( $deliver_stripe_status == 'Publish')
			{
				$validator = Validator::make($request->all(),['deliver_stripe_clientid' => 'required',
				'deliver_stripe_secretid' => 'required'
				],[
				'deliver_stripe_clientid.required' => $agent_paynmincs_client_req_err_msg,
				'deliver_stripe_secretid.required' => $agent_paynmincs_secret_req_err_msg,
				
				]
				);
				
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}else{
				$deliver_stripe_clientid	= '';
				$deliver_stripe_secretid = '';
			}
			
			
			if($deliver_paypal_status=='Publish')
			{
				$validator = Validator::make($request->all(),[ 	'deliver_paypal_clientid' => 'required',
				'deliver_paypal_secretid' => 'required'
				],[ 
				'deliver_paypal_clientid.required' => $agent_paymaya_secret_req_err_msg,
				'deliver_paypal_secretid.required' => $agent_paymaya_status_req_err_msg
				]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}else{
				$deliver_paypal_clientid	= '';
				$deliver_paypal_secretid = '';
			}
			
			if($deliver_netbank_status=='Publish')
			{
				$validator = Validator::make($request->all(),[ 
				'deliver_bank_name'  => 'required',
				'deliver_branch'	 => 'required',
				'deliver_bank_accno' => 'required',
				'deliver_ifsc' 		 => 'required'
				],[ 
				'deliver_bank_name.required' => $agent_bank_name_req_err_msg,
				'deliver_branch.required'	 => $agent_branch_req_err_msg,
				'deliver_bank_accno.required'=> $agent_accNo_req_err_msg,
				'deliver_ifsc.required' 	 => $agent_ifsc_req_err_msg
				]
				);
				if($validator->fails()){
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				}else{
				$deliver_bank_name  = '';
				$deliver_branch    	= '';
				$deliver_bank_accno	= '';
				$deliver_ifsc    	= '';
			}
			
			$agent_payment_details = array('deliver_stripe_status'	=> $deliver_stripe_status,
			'deliver_stripe_clientid'=> $deliver_stripe_clientid,
			'deliver_stripe_secretid'=> $deliver_stripe_secretid,
			'deliver_paypal_status'	=> $deliver_paypal_status,
			'deliver_paypal_clientid'	=> $deliver_paypal_clientid,
			'deliver_paypal_secretid'	=> $deliver_paypal_secretid,
			'deliver_netbank_status'	=> $deliver_netbank_status,
			'deliver_bank_name'			=> $deliver_bank_name,
			'deliver_branch'			=> $deliver_branch,
			'deliver_bank_accno'		=> $deliver_bank_accno,
			'deliver_ifsc'				=> $deliver_ifsc
			);
			/** Update Agent Payment Details **/
			DB::table('gr_delivery_member')->where('deliver_id', '=', $deliver_id)->update($agent_payment_details);
			
			$msg = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');;
			return Response::make(json_encode(array('code'=>200,'message'=>$msg,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		public function commission_tracking(Request $request){
			$lang = $request->lang;
			$delBoyDet 	= JWTAuth::user();
			$deliver_id	= $delBoyDet->deliver_id;
			$admin_default_lang = $this->admin_default_lang;
			$page_no = $request->page_no;
			$pagenum_valid_err_msg = MobileModel::get_lang_text($lang,'API_PAGE_NUM_RULES','Page number should be a number!');
			$validator = Validator::make($request->all(),['page_no'=>'sometimes|nullable|integer'],['page_no.required' => $pagenum_valid_err_msg]); 
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$sql = 	DB::table('gr_delivery_person_earnings')
			->select('de_deliver_id',
			
			DB::Raw("CONCAT(if(gr_delivery_member.deliver_fname is null,'',gr_delivery_member.deliver_fname),' ',if(gr_delivery_member.deliver_lname is null,'',gr_delivery_member.deliver_lname)) AS delboy_name"),
			
			'gr_delivery_member.deliver_email',
			DB::Raw('COUNT(*) AS total_orders'),
			DB::Raw('SUM(de_order_total) AS total_order_amt'),
			DB::Raw('SUM(de_rcd_amt) AS total_online_amt'),
			DB::Raw('SUM(gr_delivery_person_earnings.de_total_amount) AS total_commission_amt'),
			DB::Raw('(SELECT SUM(commission_paid) FROM gr_delboy_commission WHERE delboy_id = gr_delivery_person_earnings.de_deliver_id AND commission_status="2") AS total_rcvd_amt'),
			DB::Raw('(SELECT SUM(amount_received) FROM gr_delboy_commission WHERE delboy_id = gr_delivery_person_earnings.de_deliver_id AND commission_status="2") AS total_paid_amt'),
			'de_ord_currency',
			'de_ord_currcode',
			'gr_delivery_member.deliver_paypal_clientid',
			'gr_delivery_member.deliver_paypal_secretid',
			'gr_delivery_member.deliver_paypal_status'
			);
			
			$q = $sql->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_delivery_person_earnings.de_deliver_id');
			$q = $sql->where('gr_delivery_member.deliver_status','<>','2');
			$q = $sql->where('gr_delivery_person_earnings.de_deliver_id','=',$deliver_id);
			$posts =  $q->paginate(10,['*'],'commission_list',$page_no);
			$commission_list = array();
			if(count($posts) > 0 ){
				$admin_payment_settings = DB::table('gr_payment_setting')->first();
				if(empty($admin_payment_settings) === false){
					$paymaya_status = ($admin_payment_settings->paymaya_status == 1) ? 'Publish' : 'Unpublish';
					$paymaya_client_id = $admin_payment_settings->paymaya_client_id;
					$paymaya_secret_id = $admin_payment_settings->paymaya_secret_id;
					$net_bank_status   = $admin_payment_settings->netbank_status;
					$net_acc_no 	   = $admin_payment_settings->bank_accno;
					$net_bank_name 	   = $admin_payment_settings->bank_name;
					$net_branch 	   = $admin_payment_settings->branch;
					$net_ifsc  		   = $admin_payment_settings->ifsc;
				}
				else {
					$paymaya_status = 0;
					$paymaya_client_id = '';
					$paymaya_secret_id = '';
					$net_bank_status = 0;
					$net_acc_no = '';
					$net_bank_name = '';
					$net_branch = '';
					$net_ifsc = '';
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
					$nestedData['delboyId'] 	 = $post->de_deliver_id;
					$nestedData['totalOrders']	 = $post->total_orders;
					$nestedData['currency']		 = $post->de_ord_currency;
					$nestedData['currency_code'] = $post->de_ord_currcode;
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
					$nestedData['balAmtToReceive'] = ($balAmtToPay < 0) ? abs($balAmtToPay) : '0';
					$nestedData['balAmtToPay'] = ($balAmtToPay > 0) ? $balAmtToPay : '0';
					$nestedData['viewTransaxn'] = $view_transaxn;
					$commission_list[] = $nestedData;
				}
				$msge=MobileModel::get_lang_text($lang,'API_RECORDS_FOUND','Records found!');
				$data = ['commission_list' 	=>$commission_list,
				'paypal_status'	=>$paymaya_status,
				'paypal_client_id'	=>$paymaya_client_id,
				'paypal_secret_id'	=>$paymaya_secret_id,
				'net_bank_status'	=> $net_bank_status,
				'net_acc_no'	=> $net_acc_no,
				'net_bank_name'	=> $net_bank_name,
				'net_branch'	=> $net_branch,
				'net_ifsc'		=> $net_ifsc,
				];
				return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data'	=> $data),JSON_PRETTY_PRINT))->header('Content-Type',"application/json");
			}
			else {
				$msge=MobileModel::get_lang_text($lang,'API_NO_RECORDS_FOUND','No records found!');
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		public function commission_transaxn(Request $request){
			$lang 		= $request->lang;
			$delBoyDet 	= JWTAuth::user();
			$deliver_id	= $delBoyDet->deliver_id;
			$admin_default_lang = $this->admin_default_lang;
			$from_date  = $request->from_date;
			$to_date	= $request->to_date;
			$page_no 	= $request->page_no;
			
			$validator = Validator::make($request->all(),['from_date'=> 'sometimes|nullable|date|date_format:Y-m-d']);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			if($request->from_date!='')
			{
				$from_date = date("Y-m-d", strtotime($request->from_date));
				$from_minus_date=date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $from_date) ) ));
			}
			if($request->to_date!='')
			{
				//after:'.$opportunity->created_at;
				$validator = Validator::make($request->all(),[ 'to_date'=> 'sometimes|nullable|date|date_format:Y-m-d|after_or_equal:'.$from_date.'']);
				if($validator->fails())
				{
					$message = $validator->messages()->first();
					return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
				}
				$to_date   = date("Y-m-d", strtotime($request->to_date));
			}
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			$sql = DB::table('gr_delboy_commission')
			->select(
			'gr_delboy_commission.amount_received',
			'gr_delboy_commission.commission_paid',
			'gr_delboy_commission.transaction_id',
			'gr_delboy_commission.pay_type',
			'gr_delboy_commission.commission_date',
			'gr_delboy_commission.commission_currency'
			)
			->where('gr_delboy_commission.delboy_id','=',$deliver_id)
			->where('gr_delboy_commission.commission_status','=','2')
			->orderby('gr_delboy_commission.commission_date','DESC');
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
						$nestedData['pay_type'] = MobileModel::get_lang_text($lang,'API_PAYMAYA','Paypal');
						}elseif($comlist->pay_type=='2') {
						$nestedData['pay_type'] = MobileModel::get_lang_text($lang,'API_PAYNAMICS','Stripe');
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
				return Response::make(json_encode(array('code'=>400,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
		}
		
		public function pay_request(Request $request){
			$lang 			= $request->lang;
			$delBoyDet 	= JWTAuth::user();
			$deliver_id	= $delBoyDet->deliver_id;
			$delboy_fname 	= $delBoyDet->deliver_fname;
			$delboy_lname 	= $delBoyDet->deliver_lname;
			$amount = $request->amount;
			$amount_req_err_msg = MobileModel::get_lang_text($lang,'API_ENTER_AMOUNT','Please enter amount');
			$amount_valid_err_msg = MobileModel::get_lang_text($lang,'API_INVALID_AMT','Invalid Amount');
			$validator = Validator::make($request->all(),['amount'=>'required|numeric'],
			['amount.required' => $amount_req_err_msg,
			'amount.numeric'=>$amount_valid_err_msg]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			/* -------------- GET ALL DELIVERY MANAGER ID, NAME AND EMAIL -------------*/
			$delmgr_qry = DB::table('gr_delivery_manager')->select('dm_name','dm_email','dm_id')->where('dm_status','=','1')->get();
			$delmgrArray = array();
			if(count($delmgr_qry) > 0 ){
				foreach($delmgr_qry as $delmgr){
					$update = DB::table('gr_delmgr_notification')->insert(['no_delmgr_id' => $delmgr->dm_id,'no_delboy_id'=>$deliver_id,'no_status' => '1','submit_by'=>$deliver_id ]);
					$send_mail_data = array('name' 			=> $delmgr->dm_name,
					'amount' 		=> $amount,
					'dm_name' 	=> $delboy_fname.' '.$delboy_lname,
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
			'dm_name' 		=> $delboy_fname.' '.$delboy_lname,
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
			return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		public function commission_payment(Request $request){
			$lang = $request->lang;
			$delBoyDet 	= JWTAuth::user();
			$deliver_id	= $delBoyDet->deliver_id;
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
			'pay_type'			=>'required|integer'
			],
			[
			'paid.required' 			=> $amount_req_err_msg, 
			'paid.numeric'				=> $amount_valid_err_msg,
			'currency_symbol.required' 	=> $cursym_req_err_msg,
			'status.required'			=> $pmtstatus_req_err_msg,
			'status.integer'			=> $pmtstatus_valid_err_msg,
			'transaction_id.required'	=> $transaxnid_req_err_msg,
			'pay_type.required'		=> $paytype_req_err_msg
			]
			); 
			if($validator->fails()){
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			
			$insertArr = [	
			'delboy_id' 			=> $deliver_id,
			'amount_received'	 	=> $request->paid,
			'commission_currency'	=> $request->currency_symbol,
			'commission_status'		=> '2',
			'transaction_id' 		=> $request->transaction_id,
			'pay_type' 				=> $request->pay_type,
			'commission_date'		=> date('Y-m-d H:i:s')];
			$insert = insertvalues('gr_delboy_commission',$insertArr);
			/**delete notification **/
			DB::table('gr_delmgr_notification')->where(['no_status' => '1','no_delmgr_id' => Session::get('DelMgrSessId'),'no_delboy_id'=>$deliver_id,'submit_by'=>$deliver_id])->delete();
			$msge = MobileModel::get_lang_text($lang,'API_PAY_PAID_SUX','Payment paid successfully!');
			return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
		/* update delviery boy */
		public function update_location(Request $request)
		{
			$lang = $request->lang;
			$lat  = $request->deliver_latitude;
			$long = $request->deliver_longitude;
			$id = JWTAuth::user()->deliver_id;
			$validator = Validator::make($request->all(),['deliver_latitude' => 'required','deliver_longitude' => 'required']);
			if($validator->fails())
			{
				$message = $validator->messages()->first();
				return Response::make(json_encode(array('code'=>400,"message"=>$message,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
			}
			updatevalues('gr_delivery_member',['deliver_latitude' => $lat,'deliver_longitude' => $long],['deliver_id' => $id]);
			$msge = MobileModel::get_lang_text($lang,'API_UPDATE_SUXES','Updated successfully!');
			return Response::make(json_encode(array('code'=>200,"message"=>$msge,'data' => $this->empty_data),JSON_PRETTY_PRINT|JSON_FORCE_OBJECT))->header('Content-Type',"application/json");
		}
		
	}	