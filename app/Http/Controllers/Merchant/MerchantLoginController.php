<?php
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Merchant;
	
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
	
	use App\Merchant;
	class MerchantloginController extends Controller
	{
		
		/*
			|--------------------------------------------------------------------------
			| Default Home Controller
			|--------------------------------------------------------------------------
			|
			| You may wish to use controllers instead of, or in addition to, Closure
			| based routes. That's great! Here is an example controller method to
			| get you started. To route to this controller, just add the route:
			|
			|   Route::get('/', 'HomeController@showWelcome');
			|
		*/
		
		public function __construct(){
			parent::__construct();
			/// set Merchant Panel language
			$this->setLanguageLocaleMerchant();
		}                        
		public function merchant_login()
		{
			if (Session::has('merchantid')) {
				return redirect::to('merchant_dashboard');
				}else{
				return view('sitemerchant.merchant_login');//->with('merchantdetails', $merchantdetails);;
			}
		}
		
		public function merchant_login_check(Request $request)
		{ 
			$inputs = Input::all();
			$email = Input::get('mer_email');
			$pass = Input::get('mer_pass');
			$email_err_msg = (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_VALID_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_VALID_EMAIL') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_VALID_EMAIL');
			$pass_err_msg = (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_YOUR_PASSWORD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_YOUR_PASSWORD') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_YOUR_PASSWORD');
			$this->validate($request,['mer_email' => 'required|email','mer_pass' => 'required' ], [ 'mer_email.email'    => $email_err_msg ,'mer_pass.required' => $pass_err_msg] );
			
			
			//DB::connection()->enableQueryLog();
			$get_details = DB::table('gr_merchant')->select('mer_password','id','mer_email','mer_fname','mer_lname','has_shop','mer_business_type')->where('mer_email', '=', $email)->where('mer_status','=','1')->first(); //->where('mer_password', '=', md5($pass))
			//$query = DB::getQueryLog();
			//print_r($get_details); echo $get_details[0]->mer_email; exit;
			//echo count($get_details); exit;
			
			if(empty($get_details) === false)
			{     
				if($get_details->mer_password != md5($pass))
				{
					$msg = (Lang::has(Session::get('mer_lang_file').'.MER_INVALID_PASS')) ? trans(Session::get('mer_lang_file').'.MER_INVALID_PASS') : trans($this->MER_OUR_LANGUAGE.'.MER_INVALID_PASS');
					return Redirect::to('merchant-login')->withErrors(['pass'=> $msg])->withInput();
				}
				else
				{
					Session::put('merchantid',$get_details->id);
					Session::put('mer_email',$get_details->mer_email);
					Session::put('mer_name',$get_details->mer_fname.' '.$get_details->mer_lname);
					Session::put('mer_has_shop',$get_details->has_shop);
					
					Session::put('mer_business_type',$get_details->mer_business_type);
					if($get_details->has_shop == 1)
					{
						$store = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
						Session::put('shop_id',$store->id);
					}
					
					
					$msg = (Lang::has(Session::get('mer_lang_file').'.MER_LOG_SUCCESS')) ? trans(Session::get('mer_lang_file').'.MER_LOG_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.MER_LOG_SUCCESS');
					Session::flash('message',$msg);
					if($get_details->has_shop==0 && $get_details->mer_business_type=='1')
					{
						return Redirect::to('mer-manage-store');
					}
					elseif($get_details->has_shop==0 && $get_details->mer_business_type=='2')
					{
						return Redirect::to('mer-manage-restaurant');
					}
					else
					{
						return Redirect::to('merchant_dashboard');
					}
				}
			}
			else
			{
				$msg = (Lang::has(Session::get('mer_lang_file').'.MER_INVALID_MAIL')) ? trans(Session::get('mer_lang_file').'.MER_INVALID_MAIL') : trans($this->MER_OUR_LANGUAGE.'.MER_INVALID_MAIL');
				return Redirect::to('merchant-login')->withErrors(['email'=> $msg])->withInput();
			}
			
		}
		
		
		public function merchant_logout()
		{
			
			Session::forget('merchantid');
			Session::forget('mer_email');
			Session::forget('mer_name');
			Session::forget('mer_has_shop');
			$mer_lang_file = Session::get('mer_lang_file');
			
			Session::flush();
			/*
				if (Lang::has(Session::get('mer_lang_file').'.MER_LOGOUT_SUCCESS')!= '')
				{ 
                $session_message =  trans(Session::get('mer_lang_file').'.MER_LOGOUT_SUCCESS');
				}  
				else 
				{ 
                $session_message =  trans($this->MER_OUR_LANGUAGE.'.MER_LOGOUT_SUCCESS');
			}*/
			$msg = (Lang::has(Session::get('mer_lang_file').'.MER_LOGOUT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.MER_LOGOUT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.MER_LOGOUT_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('merchant-login')->with('login_success', $msg);
		}
		
		public function merchant_forgot_check()
		{
			/* print_r($_POST);die; */
			$inputs         = Input::all();
			$merchant_email = Input::get('merchant_email');
			
			$encode_email = base64_encode(base64_encode(base64_encode(($merchant_email))));
			
			$check_valid_email = Merchantadminlogin::checkvalidemail($merchant_email);
			
			if ($check_valid_email) {
				$forgot_check = Merchantadminlogin::forgot_check_details_merchant($merchant_email);
				
				$name = 'merchant';
				
				$send_mail_data = array(
                'name' => $forgot_check[0]->mer_fname,
                'email' => $merchant_email,
                'encodeemail' => $encode_email
				);
				# It will show these lines as error but no issue it will work fine Line no 119 - 122
				Mail::send('emails.merchant_passwordrecoverymail', $send_mail_data, function($message)
				{
					if (Lang::has(Session::get('mer_lang_file').'.MER_PASSWORD_RECOVERY_DETAILS')!= '')
					{ 
						$session_message =  trans(Session::get('mer_lang_file').'.MER_PASSWORD_RECOVERY_DETAILS');
					}  
					else 
					{ 
						$session_message =  trans($this->MER_OUR_LANGUAGE.'.MER_PASSWORD_RECOVERY_DETAILS');
					}
					$message->to(Input::get('merchant_email'))->subject($session_message);
				});
				if (Lang::has(Session::get('mer_lang_file').'.MER_MAIL_SEND_SUCCESSFULLY')!= '')
				{ 
					$session_message =  trans(Session::get('mer_lang_file').'.MER_MAIL_SEND_SUCCESSFULLY');
				}  
				else 
				{ 
					$session_message =  trans($this->MER_OUR_LANGUAGE.'.MER_MAIL_SEND_SUCCESSFULLY');
				}
				#sathyaseelan
				echo $session_message.":0";
				/* return Redirect::to('sitemerchant')->with('login_success', $session_message ); */
				} else {
				if (Lang::has(Session::get('mer_lang_file').'.MER_INVALID_EMAIL')!= '')
				{ 
					$session_message =  trans(Session::get('mer_lang_file').'.MER_INVALID_EMAIL');
				}  
				else 
				{ 
					$session_message =  trans($this->MER_OUR_LANGUAGE.'.MER_INVALID_EMAIL');
				}
				#sathyaseelan
				echo $session_message.":1";
				/*  return Redirect::to('sitemerchant')->with('forgot_error', $session_message); */
				
			}
			
		}
		
		public function forgot_pwd_email($email)
		{
			$merchat_decode_email = base64_decode(base64_decode(base64_decode($email)));
			
			$merchantdetails = Merchantadminlogin::get_merchant_details($merchat_decode_email);
			
			return view('sitemerchant.forgot_pwd_mail')->with('merchantdetails', $merchantdetails);
			
		}
		
		public function forgot_pwd_email_submit()
		{
			$inputs      = Input::all();
			$merchant_id = Input::get('merchant_id');
			$pwd         = Input::get('pwd');
			$confirmpwd  = Input::get('confirmpwd');
			
			Merchantadminlogin::update_newpwd($merchant_id, $confirmpwd);
			if (Lang::has(Session::get('mer_lang_file').'.MER_PASSWORD_CHANGED_SUCCESSFULLY')!= '')
            { 
                $session_message =  trans(Session::get('mer_lang_file').'.MER_PASSWORD_CHANGED_SUCCESSFULLY');
			}  
            else 
            { 
                $session_message =  trans($this->MER_OUR_LANGUAGE.'.MER_PASSWORD_CHANGED_SUCCESSFULLY');
			}
			return Redirect::to('sitemerchant')->with('login_success', $session_message);
			
		}
		
		public function mer_forgot_password()
        {
            $get_details= Merchant::all();
            //print_r($get_details);
            if($get_details === false)
            {
                $msg = (Lang::has(Session::get('mer_lang_file').'.MER_ERROR_MAIL')) ? trans(Session::get('mer_lang_file').'.MER_ERROR_MAIL') : trans($this->MER_OUR_LANGUAGE.'.MER_ERROR_MAIL');
				return Redirect::to('merchant-login')->withErrors(['email'=>$msg])->withInput();
			}
            else{
                //echo 'success';
                return view('sitemerchant.mer_forget_password');
			}
            //return 'sucess';
		}
		
		
		/*Forgot password for merchant*/ 
		
		public function mer_forgot_password_submit(Request $request)
		{
			$email_err_msg = (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_YOUR_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_YOUR_EMAIL') : trans($this->MER_OUR_LANGUAGE_LANGUAGE.'.MER_ENTER_YOUR_EMAIL');     
			$this->validate($request,
            ['frgt_email' => 'Required|email'],
            [ 'frgt_email.required'    => $email_err_msg]);
			
			$frg_email = Input::get('frgt_email');    
			
			$get_email = DB::table('gr_merchant')
			->select('mer_email','id', 'mer_fname')
			->where('mer_email','=', $frg_email)
			->first();     
			if(empty($get_email->mer_email)=== false){
				$forget_email = $get_email->mer_email; 
				$f_name = $get_email->mer_fname;
				}else{
                $msg = (Lang::has(Session::get('mer_lang_file').'.MER_INVALID_MAIL')) ? trans(Session::get('mer_lang_file').'.MER_INVALID_MAIL') : trans($this->MER_LANGUAGE.'.MER_INVALID_MAIL');
				return Redirect::to('mer_forgot_password')->withErrors(['email'=>$msg])->withInput();
			}
			
			
			if(empty($get_email) === false)
			{   
				if($frg_email == $forget_email){                  
                    $rand_password = rand();                    
					/*MAIL FUNCTION */
                    $send_mail_data = array(                        
					
					'name' => $f_name,                         
					'email' => Input::get('frgt_email'),
					'password' => $rand_password,
					);                                                      
                    Mail::send('email.mer_forgot_password_email', $send_mail_data, function($message){
						$email  = Input::get('frgt_email');                                  
						$subject = (Lang::has(Session::get('mer_lang_file').'.FRONT_PASSWORD_RECOVERY')) ? trans(Session::get('mer_lang_file').'.FRONT_PASSWORD_RECOVERY') : trans($this->MER_OUR_LANGUAGE.'.FRONT_PASSWORD_RECOVERY');
						$message->to($email)->subject($subject);
					});
                    
                    $forget_id = DB::table('gr_merchant')
					->select('id')
					->where('mer_email','=', $frg_email)
					->first();
                    $forgot_id =  $forget_id->id;
                    $mer_id =    $forgot_id;
                    $send_forgot_data = array(                                                      
					'mer_email' => Input::get('frgt_email'),
					'mer_password' => md5($rand_password),
					'mer_decrypt_password' => $rand_password,
					);
					$update = updatevalues('gr_merchant',$send_forgot_data,['id' =>$mer_id]);
					$msg = (Lang::has(Session::get('mer_lang_file').'.MER_CHK_MAIL')) ? trans(Session::get('mer_lang_file').'.MER_CHK_MAIL') : trans($this->MER_LANGUAGE.'.MER_CHK_MAIL');
					Session::flash('message',$msg);
					return Redirect::to('merchant-login');
				}
                else{ 
                    $msg = (Lang::has(Session::get('mer_lang_file').'.MER_INVALID_MAIL')) ? trans(Session::get('mer_lang_file').'.MER_INVALID_MAIL') : trans($this->MER_LANGUAGE.'.MER_INVALID_MAIL');
                    return Redirect::to('mer_forgot_password')->withErrors(['email'=>$msg])->withInput();
				}
			}   
			
		}
		
	}
