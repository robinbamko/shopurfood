<?php
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\DeliveryManager;
	
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
	
	use App\DeliveryManager;
	use App\Admin;
	use Image;
	use File;
	
	class DelMgrController extends Controller
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
			|	Route::get('/', 'HomeController@showWelcome');
			|
		*/
		
		public function __construct(){
			parent::__construct();
			/// set Merchant Panel language
			//$this->setLanguageLocalDeliveryManager();
			//if (Session::has('DelMgrSessId')) { } else { return Redirect::to('delivery-manager-login'); } 
		}						 
		public function dmgr_login()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$this->setLanguageLocalDeliveryManager();
			if (Session::has('DelMgrSessId')) {
				return redirect::to('delivery-manager-dashboard');
				}else{
				return view('DeliveryManager.login');
			}
		}
		
		public function dmgr_login_check(Request $request)
		{ 
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$this->setLanguageLocalDeliveryManager();
			$inputs = Input::all();
			$email = Input::get('dm_email');
			$pass = Input::get('dm_password');
			$email_err_msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_EMAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_YOUR_EMAIL');
			if($email=='')
			{
				
				return Redirect::to('merchant-login')->withErrors(['email'=>$email_err_msg])->withInput();
			}
			//$request->validate([ 'merchant email' => 'required|email']);
			$this->validate($request,['dm_email' => 'required|email' ], [ 'dm_email.email'    => $email_err_msg ] );
			if($pass=='')
			{
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_INVALID_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_INVALID_PASS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_INVALID_PASS');
				return Redirect::to('delivery-manager-login')->withErrors(['pass'=> $msg])->withInput();
				
			}
			
			//DB::connection()->enableQueryLog();
			$get_details = DB::table('gr_delivery_manager')->where('dm_email', '=', $email)->where('dm_password', '=', md5($pass))->where('dm_status','=','1')->get();
			//$query = DB::getQueryLog();
			//print_r($get_details); echo $get_details[0]->dm_email; exit;
			//echo count($get_details); exit;
			if(count($get_details) > 0)
			{
				Session::put('DelMgrSessId',$get_details[0]->dm_id);
				Session::put('dm_email',$get_details[0]->dm_email);
				Session::put('dm_name',$get_details[0]->dm_name);
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOG_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOG_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_LOG_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('delivery-manager-dashboard');
			}
			else
			{
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.MER_DELMGR_INVALID_LOGIN')) ? trans(Session::get('DelMgr_lang_file').'.MER_DELMGR_INVALID_LOGIN') : trans($this->DELMGR_OUR_LANGUAGE.'.MER_DELMGR_INVALID_LOGIN');
				return Redirect::to('delivery-manager-login')->withErrors(['pass'=> $msg])->withInput();
			}
			
		}
		
		
		public function dmgr_logout()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			Session::forget('DelMgrSessId');
			Session::forget('dm_email');
			Session::forget('dm_name');
			$DelMgr_lang_file = Session::get('DelMgr_lang_file');
			Session::flush();
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOGOUT_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOGOUT_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_LOGOUT_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('delivery-manager-login')->with('login_success', $msg);
		}
		public function dmgr_dashboard()
		{	
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			//echo 'there'; exit;
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DASHBOARD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DASHBOARD') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_DASHBOARD');
			$agent_count 		= Admin::get_count('gr_agent','agent_status','agent_id');
			$delivery_count 	= Admin::get_count('gr_delivery_member','deliver_status','deliver_id');
			//$order_count 		= DB::table('gr_order')->select(DB::raw('COUNT(DISTINCT ord_transaction_id) As TotalOrder'))->first()->TotalOrder;
			$order_count = DB::table('gr_order')->select('gr_order.ord_id')
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->leftjoin('gr_merchant', 'gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->where('gr_order.ord_status','>=','4')
			->get();
			$order_count = count($order_count);
			$delivered_count	= Admin::get_count2('gr_order',['ord_status' => '8'],'ord_id');
            $failed_count	= Admin::get_count2('gr_order',['ord_status' => '9'],'ord_id');
			$agent_active	= Admin::get_count2('gr_agent',['agent_status' => '1'],'agent_id');
			$agent_deactive	= Admin::get_count2('gr_agent',['agent_status' => '0'],'agent_id');
			$delmem_active	= Admin::get_count2('gr_delivery_member',['deliver_status' => '1'],'deliver_id');
			$delmem_deactive	= Admin::get_count2('gr_delivery_member',['deliver_status' => '0'],'deliver_id');
			//			print_r($delmem_active); exit;
			return view('DeliveryManager.dashboard')->with(['pagetitle' => $page_title,'agent_count' => $agent_count,'delivery_count' => $delivery_count,'order_count' => $order_count,'delivered_count' => $delivered_count,'agent_active'=>$agent_active,'agent_deactive'=>$agent_deactive,'delmem_active'=>$delmem_active,'delmem_deactive'=>$delmem_deactive,'failed_count'=>$failed_count]);
		}
		public function change_password(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$DelMgrSessId  = Session::get('DelMgrSessId');
			//starts here
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CHANGE_PASSWORD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CHANGE_PASSWORD') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CHANGE_PASSWORD');
			//$getvendors = DB::table('gr_merchant')->where('id', '=', $merid)->first();
			if($_POST) { 
				$this->validate($request, 
				[
				'old_pwd'=>'Required',
				'new_pwd'=>'Required',
				'conf_pwd'=>'Required|same:new_pwd'
				],[
				'old_pwd.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_OLDPWD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_OLDPWD') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_OLDPWD'), 
				'new_pwd.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NEWPWD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NEWPWD') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_NEWPWD'),
				'conf_pwd.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_CONPWD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_CONPWD') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_CONPWD')				
				]); 
				$old_pwd      = Input::get('old_pwd');
				$new_pwd      = Input::get('new_pwd');
				$conf_pwd	  = Input::get('conf_pwd');
				//DB::connection()->enableQueryLog();
				$oldpwdcheck = DeliveryManager::check_oldpwd($DelMgrSessId, $old_pwd);
				//$query = DB::getQueryLog();
				//print_r($query);
				//print_r($oldpwdcheck); echo count($oldpwdcheck); exit;
				if (count($oldpwdcheck) > 0) {
					DeliveryManager::update_newpwd($DelMgrSessId, $conf_pwd);
					$message = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PASSWORD_CHANGED_SUCCESSFULLY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PASSWORD_CHANGED_SUCCESSFULLY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PASSWORD_CHANGED_SUCCESSFULLY');
					return Redirect::to('delivery-manager-change-password')->with('message', $message);
				}
				else
				{
					$message = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_OLD_PASSWORD_DONOT_MATCH')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_OLD_PASSWORD_DONOT_MATCH') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_OLD_PASSWORD_DONOT_MATCH');
					return Redirect::to('delivery-manager-change-password')->withErrors(['password_error'=>$message])->withInput();
				}
			}
			else
			{
				return view('DeliveryManager.changePassword')->with('pagetitle',$pagetitle);
			}
		}
		public function dmgr_profile(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$DelMgrSessId  = Session::get('DelMgrSessId');
			//starts here
			$pagetitle = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EDIT_PROFILE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EDIT_PROFILE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_EDIT_PROFILE');
			$getvendors = DB::table('gr_delivery_manager')->where('dm_id', '=', $DelMgrSessId)->first();
			if($_POST) { 
				///print_r($_POST); exit;/*Array ( [_token] => FcrVG3S4njTOXztzXGfIoiT1Ll9ianjsS0pOya1W [dm_name] => John [dm_email] => john@gmail.com [old_email] => john@gmail.com [dm_phone] => 8945612374 [old_profile_foto] => ) */ 
				$this->validate($request, 
				[
				'dm_name'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
				'dm_email'=>'Required|Email',
				'dm_phone'=>'Required',
				'photo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_height = 500,max_width = 500'
				],[
				'dm_name.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NAME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_NAME'),
				'dm_email.email'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_EMAIL'),
				'dm_phone.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PHONE'),
				'photo.image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_height = 500,max_width = 500'=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_IMAGE_VAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_IMAGE_VAL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_IMAGE_VAL')
				]); 
				//echo 'payanamics status';exit;// .$_POST['mer_paynamics_status']; exit;
				if($request->hasFile('photo')!='')
				{ 
					$avatar = $request->file('photo'); 
					$filename = 'manager'.time(). '.' . $avatar->getClientOriginalExtension();
					Image::make($avatar)->resize(300, 300)->save(public_path('/images/delivery_manager/' .$filename ));
				}
				else
				{
					$filename=$_POST['old_profile_foto'];
				}
				$profile_det = array(
				'dm_name'=> mysql_escape_special_chars(Input::get('dm_name')),
				'dm_email'=> mysql_escape_special_chars(Input::get('dm_email')),
				'dm_phone'=> mysql_escape_special_chars(Input::get('dm_phone')),
				'dm_imge'=>$filename
				);			
				//DB::connection()->enableQueryLog();
				DB::table('gr_delivery_manager')->where('dm_id', '=', $DelMgrSessId)->update($profile_det);
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				
				$message = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE_SUCCESS');
				return redirect('delivery-managerprofile')->with('message',$message);
			}
			else
			{
				return view('DeliveryManager.editProfile')->with('pagetitle',$pagetitle)->with('getvendor',$getvendors);
			}
		}
		public function merchant_forgot_check()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
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
					if (Lang::has(Session::get('DelMgr_lang_file').'.dm_passwordWORD_RECOVERY_DETAILS')!= '')
					{ 
						$session_message =  trans(Session::get('DelMgr_lang_file').'.dm_passwordWORD_RECOVERY_DETAILS');
					}  
					else 
					{ 
						$session_message =  trans($this->DELMGR_OUR_LANGUAGE.'.dm_passwordWORD_RECOVERY_DETAILS');
					}
					$message->to(Input::get('merchant_email'))->subject($session_message);
				});
				if (Lang::has(Session::get('DelMgr_lang_file').'.MER_MAIL_SEND_SUCCESSFULLY')!= '')
				{ 
					$session_message =  trans(Session::get('DelMgr_lang_file').'.MER_MAIL_SEND_SUCCESSFULLY');
				}  
				else 
				{ 
					$session_message =  trans($this->DELMGR_OUR_LANGUAGE.'.MER_MAIL_SEND_SUCCESSFULLY');
				}
				#sathyaseelan
				echo $session_message.":0";
				/* return Redirect::to('sitemerchant')->with('login_success', $session_message ); */
				} else {
				if (Lang::has(Session::get('DelMgr_lang_file').'.MER_INVALID_EMAIL')!= '')
				{ 
					$session_message =  trans(Session::get('DelMgr_lang_file').'.MER_INVALID_EMAIL');
				}  
				else 
				{ 
					$session_message =  trans($this->DELMGR_OUR_LANGUAGE.'.MER_INVALID_EMAIL');
				}
				#sathyaseelan
				echo $session_message.":1";
				/*  return Redirect::to('sitemerchant')->with('forgot_error', $session_message); */
				
			}
			
		}
		
		public function forgot_pwd_email($email)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$merchat_decode_email = base64_decode(base64_decode(base64_decode($email)));
			
			$merchantdetails = Merchantadminlogin::get_merchant_details($merchat_decode_email);
			
			return view('sitemerchant.forgot_pwd_mail')->with('merchantdetails', $merchantdetails);
			
		}
		
		public function forgot_pwd_email_submit()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$inputs      = Input::all();
			$merchant_id = Input::get('merchant_id');
			$pwd         = Input::get('pwd');
			$confirmpwd  = Input::get('confirmpwd');
			
			Merchantadminlogin::update_newpwd($merchant_id, $confirmpwd);
			if (Lang::has(Session::get('DelMgr_lang_file').'.dm_passwordWORD_CHANGED_SUCCESSFULLY')!= '')
 			{ 
				$session_message =  trans(Session::get('DelMgr_lang_file').'.dm_passwordWORD_CHANGED_SUCCESSFULLY');
			}  
			else 
			{ 
				$session_message =  trans($this->DELMGR_OUR_LANGUAGE.'.dm_passwordWORD_CHANGED_SUCCESSFULLY');
			}
			return Redirect::to('sitemerchant')->with('login_success', $session_message);
			
		}
		
		public function forgot_password(Request $request)
        {
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$this->setLanguageLocalDeliveryManager();
			$get_details= DeliveryManager::all();
			
			$email_err_msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ERROR_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ERROR_MAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ERROR_MAIL');
			
			if($get_details === false)
            {
                $email_err_msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ERROR_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ERROR_MAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ERROR_MAIL');
				
			}
            else{
                //echo 'success';
                return view('DeliveryManager.forget_password');
                
			}
			
		}
		
		
		/*Forgot password for merchant*/ 
		
		public function forgot_password_submit(Request $request)
		{
			
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$this->setLanguageLocalDeliveryManager();			
			
			$email_err_msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_VALID_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_VALID_EMAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_YOUR_VALID_EMAIL');
			
			$this->validate($request,['frgt_email' => 'Required|email'], [ 'frgt_email.required'    => $email_err_msg]);
			$frg_email = Input::get('frgt_email');    
			
			$get_email = DB::table('gr_delivery_manager')
			->select('dm_email','dm_id', 'dm_name')
			->where('dm_email','=', $frg_email)
			->first(); 
			
			
			
			if(empty($get_email->dm_email)=== false){
				$forget_email = $get_email->dm_email; 
				$fname = $get_email->dm_name;
				}else{                
				
                $msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_INVALID_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_INVALID_MAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_INVALID_MAIL');
				return Redirect::to('delivery-manager-forgot-password')->withErrors(['email'=>$msg])->withInput();
			}
			
			
			if(empty($get_email) === false)
			{   
				if($frg_email == $forget_email){                  
                    $rand_password = rand();                    
					/*MAIL FUNCTION */
                    $send_mail_data = array(   
                    'name' =>  $fname,                                                  
					'email' => Input::get('frgt_email'),
					'password' => $rand_password,
					);                                                      
                    Mail::send('email.delmgr_forgot_password_email', $send_mail_data, function($message){
						$email  = Input::get('frgt_email');                                  
						$subject = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_FORGET_PASS');
						$message->to($email)->subject($subject);
					});
                    
                    $forget_id = DB::table('gr_delivery_manager')
					->select('dm_id')
					->where('dm_email','=', $frg_email)
					->first();
                    $forgot_id =  $forget_id->dm_id;
                    $mer_id =    $forgot_id;
                    $send_forgot_data = array(                                                      
					'dm_email' => Input::get('frgt_email'),
					'dm_password' => md5($rand_password),
					'dm_real_password' => $rand_password,
					);
					$update = updatevalues('gr_delivery_manager',$send_forgot_data,['dm_id' =>$mer_id]);
					
					$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_CHK_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DEL_CHK_MAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_CHK_MAIL');
					
					
					Session::flash('message',$msg);
					return Redirect::to('delivery-manager-login');
					/*return Redirect::to('delivery-manager-login')->withErrors(['pass'=> $msg])->withInput();*/
				}
                else{ 
                    
                    $msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_INVALID_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_INVALID_MAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_INVALID_MAIL');
					
                    return Redirect::to('delivery-manager-login')->withErrors(['email'=>$msg])->withInput();
				}
			}   
			
		}
		
		
		public function general_settings(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$DelMgrSessId  = Session::get('DelMgrSessId');
			$settings_details = DB::table('gr_delivery_manager')->where('dm_id', '=', $DelMgrSessId)->first();
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_GENERAL_SETTINGS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_GENERAL_SETTINGS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_GENERAL_SETTINGS');
			if($_POST)
			{
				
				$this->validate($request, 
				[
				'cus_rating_status' => 'Required',
				'cus_dataprotect_status' => 'Required',
				'cus_delivery_type' => 'Required',
				'del_failed_type' => 'Required',
				],[
				'cus_rating_status.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RATE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_RATE'),
				'cus_rating_status.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_DATA')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_DATA') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_DATA'),
				'cus_rating_status.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELIVERY_TYPE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELIVERY_TYPE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_DELIVERY_TYPE'),
				'del_failed_type.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMM_STATUS_SL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMM_STATUS_SL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_COMM_STATUS_SL'),
				]); 
				
				$profile_det = array(
				'dm_customer_rating'=>Input::get('cus_rating_status'),
				'dm_cust_data_protect'=>Input::get('cus_dataprotect_status'),
				'dm_delivery_type'=>Input::get('cus_delivery_type'),
				'dm_commission_status'=>Input::get('del_failed_type'),
				
				
				);			
				//DB::connection()->enableQueryLog();
				DB::table('gr_delivery_manager')->where('dm_id', '=', $DelMgrSessId)->update($profile_det);
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				
				$message = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE_SUCCESS');
				
				return redirect('delivery-manager-settings')->with('message',$message);
				
			}
			else
			{
				return view('DeliveryManager.generalSettings')->with('getvendor',$settings_details)->with('pagetitle',$page_title);
			}
			
		}
		public function order_notification(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_ORDER_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_ORDER_NOTIFICATION') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_ORDER_NOTIFICATION');
			return view('DeliveryManager.reports.order_notification')->with('pagetitle',$page_title);
		}
		
		public function order_notification_ajax(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$columns = array(   0 => 'read_status', 
			1 => 'order_id', 
			2 => 'message', 
			3 => 'updated_at',
			4 => 'id'
			);
			/*To get Total count */
			$totalData = DB::table('gr_general_notification')
			->select('id')
			->where('receiver_id','=',Session::get('DelMgrSessId'))
			->where('receiver_type','=','gr_delivery_manager')
			->count();
			$totalFiltered = $totalData; 
			/*EOF get Total count */
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');
			
			//if(empty($request->input('search.value')))
			$view_search = trim($request->view_search); 
			$orderId_search = trim($request->orderId_search); 
			$message_search = trim($request->message_search); 
			
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_general_notification')
			->select('id',
			'order_id',
			'message',
			'message_link',
			'updated_at',
			'read_status'
			)
			->where('receiver_id','=',Session::get('DelMgrSessId'))
			->where('receiver_type','=','gr_delivery_manager');
			if($orderId_search != '')
			{
				/*$q = $sql->whereRaw("order_id like '%".$orderId_search."%'");*/
				$q = $sql->whereRaw("order_id like ?", ['%'.$orderId_search.'%']); 
			}
			if($message_search != '')
			{
				/*$q = $sql->whereRaw("message like '%".$message_search."%'");*/ 
				$q = $sql->whereRaw("message like ?", ['%'.$message_search.'%']);
			}
			if($view_search != '')
			{
				$q = $sql->where('read_status','=',$view_search); 
			}
			$totalFiltered = $sql->count();
			//DB::connection()->enableQueryLog();
			$q = $sql->orderBy($order,$dir)->orderBy('read_status', 'ASC')->skip($start)->take($limit);
			$posts =  $q->get();
			/*$query = DB::getQueryLog();
				print_r($query);
			exit;*/
			
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				$view = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VIEW')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VIEW') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_VIEW');
				$read = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_READ_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_READ_NOTIFICATION') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_READ_NOTIFICATION');
				$unread = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_UNREAD_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_UNREAD_NOTIFICATION') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_UNREAD_NOTIFICATION');
				foreach ($posts as $post)
				{
					$nestedData['SNo'] 		= ++$snoCount;
					$nestedData['orderId'] 	= $post->order_id;
					$nestedData['message'] 	= $post->message;
					$nestedData['readstatus'] 	= ($post->read_status==0)?$unread:$read;
					$nestedData['date'] 	= date('m/d/Y H:i:s',strtotime($post->updated_at));
					$nestedData['view'] 	= '<a href="'.url('').'/'.$post->message_link.'" onclick="change_status(\''.$post->id.'\')" -target="_blank">'.$view.'</a>';
					$data[] = $nestedData;
					
				}
			}
			
			$json_data = array(
			"draw"            => intval($request->input('draw')),  
			"recordsTotal"    => intval($totalData),  
			"recordsFiltered" => intval($totalFiltered), 
			"data"            => $data   
			);
			echo json_encode($json_data); 
		}
		public function status_change_notification(Request $request){
			//print_r($request->all());
			$gotId = $request->gotId;
			//echo $gotId;
			return DB::table('gr_general_notification')->where('id','=',$gotId)->update(['read_status' => '1']);
		}
		
		
		/*Delivery Boy Map*/
		public function delivery_boy_map(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_DELIVERY_BOY_LOCATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_DELIVERY_BOY_LOCATION') : trans($this->DELMGR_OUR_LANGUAGE.'.DEL_DELIVERY_BOY_LOCATION');
			
			$del_boy_name = Input::get('del_boy_name');
			$del_boy_location = Input::get('del_boy_location');
			$us4_lat = Input::get('us4_lat');
			$us4_lon = Input::get('us4_lon');
			$us4_radius = Input::get('us4_radius');
			$del_boy_phone = Input::get('del_boy_phone');
			$del_boy_status = Input::get('del_boy_status');
			
			//$delivery_boy_details = DeliveryManager::delivery_boy_map_details($del_boy_name,$del_boy_location,$del_boy_phone,$del_boy_status);
			$delivery_boy_details = Admin::delivery_boy_map_details($del_boy_name,$us4_lat,$us4_lon,$us4_radius,$del_boy_phone,$del_boy_status,$del_boy_location);
			if(count($delivery_boy_details) > 0){
				if($del_boy_location == ''){
					
					$us4_lat=$delivery_boy_details[0]->deliver_latitude;
					$us4_lon=$delivery_boy_details[0]->deliver_longitude;
					
				}
			}
			//print_r($delivery_boy_details); exit;
			/*DELIVERY BOY LISTS*/
			$delboy_lists = DB::table('gr_delivery_member')->select('deliver_email',DB::Raw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) AS name"),'deliver_id')->where('deliver_status','=','1')->get();
			$delboy_array = array();
			if(count($delboy_lists) > 0 )
			{
				$delboy_array['']=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT');
				foreach($delboy_lists as $restar)
				{
					$delboy_array[$restar->name]=$restar->name.' - '.$restar->deliver_email;
				}
			}
			$delboy_list = $delboy_array;
			/* EOF Delivery BOY LISTS */
			return view('DeliveryManager.delivery_boy_map')->with(['pagetitle' => $page_title,'delivery_list'=>$delboy_list,'delivery_boy_details'=>$delivery_boy_details,'delivery_boy_status' => $del_boy_status,'del_boy_location'=>$del_boy_location,'us4_lat'=>$us4_lat,'us4_lon'=>$us4_lon,'us4_radius'=>$us4_radius,]);
			
		}
		
		public function refresh_delboy_notification(Request $request)
		{	
			$orderRejectByAgentCount = DB::table('gr_order_reject_history')->where('agent_id','!=','0')->where('read_status','=','0')->count();

			$newOrderRes = DB::table('gr_order')->select('gr_order.ord_id')->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')->where('gr_order.ord_payment_status','=','Success')->where('gr_order.ord_status','=','4')->where('gr_order.ord_task_status','=','0')->where('gr_order.ord_agent_acpt_status','=','0')->where('gr_order.ord_self_pickup','!=','1')->get();
			$newOrderCount = count($newOrderRes);

			$acceptedOrderRes = DB::table('gr_order')->select('gr_order.ord_id')->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')->where('gr_order.ord_payment_status','=','Success')->where('gr_order.ord_status','=','4')->where('gr_order.ord_task_status','=','1')->where('gr_order.ord_agent_acpt_status','=','1')->where('gr_order.ord_delmgr_id','=',Session::get('DelMgrSessId'))->where('gr_order.ord_agent_acpt_read_status','=','0')->where('gr_order.ord_self_pickup','!=','1')->get();
			$acceptedOrderCount = count($acceptedOrderRes);

			$ordernotifyCount = DB::table('gr_general_notification')->where('receiver_id','=',Session::get('DelMgrSessId'))->where('receiver_type','=','gr_delivery_manager')->where('read_status','=','0')->count();

			$totalCount = $orderRejectByAgentCount+$newOrderCount+$acceptedOrderCount+$ordernotifyCount;
			echo $totalCount.'`'.$orderRejectByAgentCount.'`'.$newOrderCount.'`'.$acceptedOrderCount.'`'.$ordernotifyCount;
			//echo '7'.'`'.'7'.'`'.'7';
			exit;
		}
		
		
	}
