<?php 
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Admin;
	
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
	
	use App\Admin;
	
	use App\Settings;
	
	use Carbon\Carbon;
	
	class AdminController extends Controller
	{
		
		public function __construct()
		{	
			parent::__construct();
			$this->setAdminLanguage();
		}
		
		public function admin_dashboard()
		{
			
			if(Session::has('admin_id') == 1)
			{
				
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DASHBOARD')) ? trans(Session::get('admin_lang_file').'.ADMIN_DASHBOARD') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DASHBOARD');
				$customer_count 	= Admin::get_count('gr_customer','cus_status');
				$merchant_count 	= Admin::get_count('gr_merchant','mer_status');
				$agent_count 		= Admin::get_count('gr_agent','agent_status');
				$delivery_count 	= Admin::get_count('gr_delivery_member','deliver_status');
				$order_count 		= DB::table('gr_order')->count();
				$store_count 		= Admin::get_activestore_count();
				$restaurant_count	= Admin::get_activerest_count();//get_count1('gr_store','st_status',['st_type' => '1'])->count();
				//
				$Customer_normal	= Admin::get_count1('gr_customer','cus_status',['cus_login_type' => '1']);
				$Customer_admin		= Admin::get_count1('gr_customer','cus_status',['cus_login_type' => '2']);
				$Customer_facebook	= Admin::get_count1('gr_customer','cus_status',['cus_login_type' => '3']);
				$Customer_google	= Admin::get_count1('gr_customer','cus_status',['cus_login_type' => '4']);
				$delivered_count	= Admin::get_count2('gr_order',['ord_status' => '8']);
				$merchant_active	= Admin::get_count2('gr_merchant',['mer_status' => '1']);
				$merchant_deactive	= Admin::get_count2('gr_merchant',['mer_status' => '0']);
				$store_active	= Admin::get_count2('gr_store',['st_status' => '1','st_type' =>'2']);
				$store_deactive	= Admin::get_count2('gr_store',['st_status' => '0','st_type' =>'2']);
				
				
				$restaurant_active	= Admin::get_count2('gr_store',['st_status' => '1','st_type' =>'1']);
				$restaurant_deactive	= Admin::get_count2('gr_store',['st_status' => '0','st_type' =>'1']);
				$customers=DB::table('gr_customer')->select('cus_fname','cus_email','cus_phone1','cus_status')->where('cus_status','1')->orderby('cus_id','desc')->limit(5)->get();
				$merchants=DB::table('gr_merchant')->select('mer_fname','mer_email','mer_phone','mer_status')->where('mer_status','1')->orderby('id','desc')->limit(5)->get();
				
				$deliveryboy_active	= Admin::get_count2('gr_delivery_member',['deliver_status' =>'1']);
				$deliveryboy_deactive	= Admin::get_count2('gr_delivery_member',['deliver_status' =>'0']);
				
				
				$type = 2;
				$item_active = Admin::get_productRitem_count('1',$type);
				$item_deactive = Admin::get_productRitem_count('0',$type);
				$item_count = $item_active+$item_deactive;
				
				$recentcustomers=DB::table('gr_customer')->select('cus_fname','cus_email','cus_phone1','cus_status')->where('cus_status','!=','2')
                ->whereDate( 'cus_created_date', '>=', date('Y-m-d',strtotime("-1 days")))
                ->whereDate('cus_created_date','<=', Carbon::today())
                ->orderby('cus_id','desc')->limit(5)->get();
				
				$recentmerchants=DB::table('gr_merchant')->select('mer_fname','mer_email','mer_phone','mer_status')->where('mer_status','!=','2')
                ->whereDate( 'mer_created_date', '>=', date('Y-m-d',strtotime("-1 days")))
                ->whereDate('mer_created_date','<=', Carbon::today())
                ->orderby('id','desc')->limit(5)->get();
	
				return view('Admin.dashboard')->with(['pagetitle' => $page_title,'customer_count' => $customer_count,'merchant_count' => $merchant_count,'agent_count' => $agent_count,'delivery_count' => $delivery_count,'order_count' => $order_count,'store_count' => $store_count,'restaurant_count' => $restaurant_count,'delivered_count' => $delivered_count,'Customer_normal' => $Customer_normal,'Customer_admin'=>$Customer_admin,'Customer_facebook' => $Customer_facebook,'merchant_active' => $merchant_active,'merchant_deactive' => $merchant_deactive,'store_active' => $store_active,'store_deactive' => $store_deactive,'restaurant_active' => $restaurant_active,'restaurant_deactive' => $restaurant_deactive,'Customer_google' => $Customer_google,'customers' => $customers,'merchants' => $merchants,'recentcustomers' => $recentcustomers,'recentmerchants' => $recentmerchants,'item_count'=>$item_count,'deliveryboy_active'=>$deliveryboy_active,'deliveryboy_deactive' =>$deliveryboy_deactive]);
				}else{
				return Redirect::to('admin-login');
			}
		}
		
		/** admin login **/
		public function check_login(Request $request)
		{	
			$email_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL');
			$pass_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_PASS');
			
			$this->validate($request,['adm_email' => 'Required|email','adm_pass' => 'Required'],[ 'adm_email.email|required'    => $email_err_msg ,'adm_pass.required' => $pass_err_msg]);
			
			$email = mysql_escape_special_chars(Input::get('adm_email'));
			$pass = mysql_escape_special_chars(Input::get('adm_pass'));
			
			$LoginEmail = DB::table('gr_admin')->where('adm_email', '=', $email)->get()->count();
			$subAdminEmail = DB::table('gr_subadmin')->where('adm_email', '=', $email)->where('sub_status','=',1)->get()->count();

			//*** FOR ADMIN ***//
			$get_details = Admin::first();
			
			if($LoginEmail == 1){
				
				if(empty($get_details) === false)
				{
					Session::put('admin_name','Admin');
					if($email == $get_details->adm_email)
					{
						if(md5($pass) == ($get_details->adm_password))
						{
							Session::put('admin_id',$get_details->id);
							Session::put('admin_email',$get_details->adm_email);
							Session::put('admin_name',$get_details->adm_fname.' '.$get_details->adm_lname);
							Session::put('admin_type', 'admin');
							$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOG_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOG_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_LOG_SUCCESS');
							Session::flash('message',$msg);
							return Redirect::to('admin-dashboard');
						}
						else
						{	$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_PASS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVALID_PASS');
							
							return Redirect::to('admin-login')->withErrors(['pass'=> $msg])->withInput();
						}
					}
					else
					{	$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVALID_MAIL');
						return Redirect::to('admin-login')->withErrors(['email'=>$msg])->withInput();
					}
				}
				//*** END FOR ADMIN ***//
				} else if($subAdminEmail == 1){
				
				//*** FOR SUBADMIN ***//
				$subAdminEmail = get_count('gr_subadmin',['adm_email'=>$email,'sub_status'=>1]);
				$subAdminPassword = get_count('gr_subadmin',['adm_email'=>$email,'adm_password'=>md5($pass),'sub_status'=>1]);
				$get_subadmin_details = get_details('gr_subadmin',['adm_email'=>$email,'adm_password'=>md5($pass),'sub_status'=>1]);
				
				if($subAdminEmail == 1 && $subAdminPassword == 1)
				{
					
					$priv = unserialize($get_subadmin_details->sub_privileges); 
					Session::put('subadmin_email', $get_subadmin_details->adm_email);
					Session::put('admin_id', $get_subadmin_details->id); 
					Session::put('subadmin_name', $get_subadmin_details->adm_fname); 
					Session::put('admin_name', $get_subadmin_details->adm_fname); 
					Session::put('admin_type', 'sub_admin');
					if(!empty($priv)){
						Session::put('session_admin_privileges',$priv);
						} else {
						Session::put('session_admin_privileges',array());					
					}
					
					$datetime = Carbon::now();
					$data_edit = array( 
					'sub_last_login_date' 		=> $datetime,
					'sub_login_ip' 				=> $request->ip(),
					); 
					
					$insert_subadmin = updatevalues('gr_subadmin',$data_edit,['id'=>Session::get('admin_id')]);
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOG_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOG_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_LOG_SUCCESS');
					
					Session::flash('message',$msg);
					return Redirect::to('admin-dashboard');
					
				} 
				elseif($subAdminEmail== 0 && $subAdminPassword != 0)
				{
					$err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVALID_MAIL') ;
					return Redirect::to('admin-login')->withErrors(['email'=> $err_msg])->withInput();
					
				} 
				elseif($subAdminEmail != 0 && $subAdminPassword == 0) 
				{
					
					$err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_PASS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVALID_PASS') ;
					return Redirect::to('admin-login')->withErrors(['pass'=> $err_msg])->withInput();
				}
				
				//*** END FOR SUBADMIN ***//
			}
			else 
			{
				$err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVALID_MAIL');
				return Redirect::to('admin-login')->withErrors(['email'=> $err_msg])->withInput();
			}
		}
		
		public function forgot_password()
		{
			$get_details= Admin::first();
			if($get_details === false)
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ERROR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ERROR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ERROR_MAIL');
				return Redirect::to('admin-login')->withErrors(['email'=>$msg])->withInput();
			}
			else{
				return view('email.forget_password');
			}
		}
		
		public function admin_forgot_password(Request $request)
		{
			
			$email_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL');		
			$this->validate($request,['frgt_email' => 'Required|email'],[ 'frgt_email.email|required'    => $email_err_msg ]);
			
			$frg_email = Input::get('frgt_email');
			$frg_get_details = Admin::first();
			$pwd =  $frg_get_details->adm_email;
			$check = $frg_get_details->adm_fname;
			
			//print_r ($frg_email); 
			//print_r($pwd);
			
			if(empty($frg_get_details) === false)
			{
				if($frg_email == $pwd)
				{
					$rand_password = rand();
					$name = $check;
					
					/*MAIL FUNCTION */
					$send_mail_data = array(                      
					
					'name' => $name,                                
					'email' => Input::get('frgt_email'),
					'password' => $rand_password,
					);                        
					
					Mail::send('email.forgot_password_email', $send_mail_data, function($message)
					{
						$email  = Input::get('frgt_email');                                  
						$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_FORGOT_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_FORGOT_PASSWORD') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_FORGOT_PASSWORD');
						$message->to($email)->subject($subject);
					});
					
					$admin_fgt = Admin::first();
					$admin_id =  $frg_get_details->id;
					
					$send_forgot_data = array(                                                      
					'adm_email' => Input::get('frgt_email'),
					'adm_password' => md5($rand_password),
					);     
					
					$update = updatevalues('gr_admin',$send_forgot_data,['id' =>$admin_id]);
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CHK_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHK_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CHK_MAIL');
					Session::flash('message',$msg);
					
					return Redirect::to('admin-login')->withInput();
					
				}
				else
				{	
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVALID_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVALID_MAIL');
					return Redirect::to('forgot_password')->withErrors(['email'=>$msg])->withInput();
					
				}
			}	
			
		}
		
		
		public function admin_profile()
		{
			if(Session::has('admin_id') == 1)
			{	
				if(Session::has('session_admin_privileges') == 1){  
					$settings_details = Admin::get_subadmin_details();
					} else {
					$settings_details = Admin::get_admin_details();
				}
				
				$country_list = Admin::country_lists()->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'),'');
				
				return view('Admin.admin_profile',compact('country_list'))->with('settings_details',$settings_details);
				}else{
				return Redirect::to('admin-login');
			}
		}
		
		public function ajax_get_city()
		{
			$country_id = Input::get('country_id');
			$get_all_city = Admin::get_all_country_based_city($country_id);
			$html = '';
			if(count($get_all_city)>0)
			{
				$html .= '<select class="form-control admin_city" id="admin_city" name="admin_city">';
				foreach($get_all_city as $city_val)
				{
					$html .= '<option value='.$city_val->id.'>'.$city_val->ci_name_en.'</option>';
				}
				$html .='</select>';
				}else{
				$html .= '<select class="form-control admin_city" id="admin_city" name="admin_city">
				<option>--No Values--</option>
				</select>';
			}
			
			echo $html;
			
		}
		public function profile_submit(Request $request)
		{
			$admin_firstname_requried = (Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME_REQUIRED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_FIRST_NAME_REQUIRED');
			$admin_lastname_requried = (Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME_REQUIRED')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME_REQUIRED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_LAST_NAME_REQUIRED');
			
			$admin_valid_ph = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EN_VALID_PH');
			
			$validator = Validator::make($request->all(), [
            'adminfirstname' => 'required',
			
            'adminemail' => 'required|email',
            'admin_phone_one' => 'required|only_cnty_code',
            'admin_phone_two' => 'required',
            'admin_address' => 'required',
            'admin_country' => 'required',
			
			],[
        	'adminfirstname.required'		 => $admin_firstname_requried,
        	
        	'admin_phone_one.only_cnty_code' => $admin_valid_ph,
        	'admin_phone_one.required' 		=> $admin_valid_ph,
			]);
			
			if ($validator->fails()) {
				return redirect('admin-profile')
				->withErrors($validator)
				->withInput();
				}else{
				$adminid = Input::get('adminid');
				$adminfirstname = Input::get('adminfirstname');
				$adminlastname = Input::get('adminlastname');
				$adminemail = Input::get('adminemail');
				$admin_phone_one = Input::get('admin_phone_one');
				$admin_phone_two = Input::get('admin_phone_two');
				$admin_address = Input::get('admin_address');
				$admin_country = Input::get('admin_country');
				
				
				$insertArr = array(
				'adm_fname'=>$adminfirstname, 
				'adm_lname'=>$adminlastname,
				'adm_email' => $adminemail,
				'adm_phone1' =>	$admin_phone_one,
				'adm_phone2' => $admin_phone_two,
				'adm_address' => $admin_address,
				'adm_country'=> $admin_country
				);
				
				Session::put('admin_name',$adminfirstname.' '.$adminlastname);
				if($adminid != '')
				{
					if(Session::has('session_admin_privileges') == 1){ 
						/*SUB-ADMIN*/
						$update = Admin::subadmin_update_profile($adminid,$insertArr);
						} else {
						/*ADMIN*/
						$update = Admin::update_profile($adminid,$insertArr);
					}
					
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					}else{
					if(Session::has('session_admin_privileges') == 1){ 
						/*SUB-ADMIN*/
						$insert = Admin::subadmin_insert_profile($insertArr);
						} else {
						/*ADMIN*/
						$insert = Admin::insert_profile($insertArr);
					}
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				}
				
				
				/*SUB-ADMIN*/
				
				
				
				return Redirect::to('admin-profile')->withErrors(['success'=>$msg])->withInput();
			}
		}
		
		public function change_password()
		{
			if(Session::has('admin_id') == 1)
			{ 
				if(Session::has('session_admin_privileges') == 1){ 
					$admin_details = Admin::get_subadmin_det();
					} else {
					$admin_details = Admin::get_admin_det();
				}
				
				return view('Admin.change_password')->with('admin_details',$admin_details);
			}
			else{
				return redirect('admin-login');
			}
		}
		
		public function change_password_submit(Request $request)
		{
			if(Session::has('session_admin_privileges') == 1){ 
				$admin_details = Admin::get_subadmin_det();
				} else {
				$admin_details = Admin::get_admin_det();	
			}
			
			$adminid = Input::get('adminid');
			$current_password = Input::get('currentpassword');
			$new_password = Input::get('newpassword');
			$confirmpassword = Input::get('confirmpassword');
			
			$validator = Validator::make($request->all(), [
            'currentpassword' => 'required',
            'newpassword' => 'required|min:6',
            'confirmpassword' => 'required'
			]);
			if ($validator->fails()) {
				return redirect('admin-change-password')
				->withErrors($validator)
				->withInput();
			}elseif($admin_details->adm_password != md5($current_password))
			{
        		$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CURRENT_PASSWORD_NOT_MATCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURRENT_PASSWORD_NOT_MATCH') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CURRENT_PASSWORD_NOT_MATCH');
			}
			elseif($current_password == $new_password)
			{
        		$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_PASS_NOT_SAME_CURRENT_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_PASS_NOT_SAME_CURRENT_PASS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_NEW_PASS_NOT_SAME_CURRENT_PASS');
			}
			elseif($new_password != $confirmpassword)
			{
        		$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PASS_CONFIRMATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASS_CONFIRMATION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PASS_CONFIRMATION');;
			}
			else{
        		
				$insertArr = array(
				'adm_password' => md5($new_password)
				);
				
				if(Session::has('session_admin_privileges') == 1){ 
					$update = Admin::update_subadmin_password($adminid,$insertArr);
					}else {
					$update = Admin::update_password($adminid,$insertArr);
				}
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
			}
			return redirect('admin-change-password')->withErrors(['success'=>$msg])->withInput();
			
			
		}
		public function admin_logout()
		{
			
			//***** For subadmin ******//
			$datetime = Carbon::now();
			if(Session::has('session_admin_privileges') == 1){ 
				
				$update_data = updatevalues('gr_subadmin',['sub_last_logout_date'=>$datetime],['id'=>Session::get('admin_id')]);
			}
			//***** End For subadmin ******//
			Session::forget('admin_type');
			Auth::logout();
			Session::flush();
			Session::flash('message',"Logged out successfully");
			return redirect('admin-login');
		}
		
		/** add country **/
		public function add_country(Request $request)
		{ 
			if(Session::has('admin_id') == 1)
			{ 
				$name = 'co_name';
				$old_name = Input::get('co_name');
				$where = [$name => $old_name];
				$check = check_name_exists('gr_country','co_status',$where);
				if(count($check) > 0)
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CNTY_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_CNTY_EXISTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CNTY_EXISTS');
					
					return Redirect::to('manage-country')->withErrors(['errors' =>$msg]);
				}
				$entry['co_name'] = $old_name;
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{	
						$entry['co_name_'.$Lang->lang_code] = Input::get('co_name_'.$Lang->lang_code);
					}
				}
				$entry  = array_merge(array('co_code' => Input::get('co_code'),
				'co_curcode' => Input::get('cur_code'),
				'co_cursymbol' => Input::get('cur_sym'),
				'co_dialcode' => Input::get('tel_code'),
				'co_status' => 2,// index 2 for active(status 1)
				),$entry);
				//print_r($entry); exit;
				$insert = insertvalues('gr_country',$entry);
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-country');
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** manage country **/
		public function manage_country()
		{ 
			if(Session::has('admin_id') == 1)
			{
				$get_country_details = get_all_details('gr_country','co_status',10,'desc','id');
				//print_r($get_country_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_LOC');
				$id = ''; 
				return view('Admin.country.managecountry')->with('pagetitle',$page_title)->with('all_details',$get_country_details)->with('id',$id);
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** get all country list **/
		public function array_search_country()
		{
			$search_key = Input::get('searched_country');
			include('currency_list/currency_list.php');
		$i =1; ?>
		<ul id="country-list">
			<?php 
				foreach($currency_list as $key=>$val)
				{
					if($i<=5) {
						//if (stripos($key, $search_key,0) !== false) {
						if(stripos($key, $search_key) === 0){ 	
						?>
						<li onClick="selectCountry('<?php  echo $key; ?>');" >
							<?php  echo $key; ?>
						</li> 
						<?php
							
							$i++;
						} 
						
					}
					
				} ?> 
				</ul> <?php 
		}
		
		/** get selected country details **/
		public function add_searched_country(){
			$searched_country_name= Input::get('searched_country_name');
			include("currency_list/currency_list.php");
			foreach($currency_list as $key=>$val)
			{
				if($key==$searched_country_name){
					
					echo $val['Country_code'].'||';
					echo $val['Country_name'].'||';
					echo $val['currency_symbol'].'||';
					echo $val['currency_code'].'||';
					echo $val['dial_code'].'||';
				}
			}
			
			
			
		}
		
		/** block/unblock country **/
		public function change_status($id,$status)
		{	//echo $status; echo $id; exit;
			$update = ['co_status' => $status];
			$where = ['id' => $id];
			$a = updatevalues('gr_country',$update,$where);
			//echo $a; exit;
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-country');
			}
			if($status == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-country');
			}
			else   //block
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-country');
			}
		}
		
		/** make country default **/
		public function country_default()
		{//echo "1"; exit;
			$id =  Input::get('co_id');
			$update = ['default_counrty' => '1'];
			$where = ['id' => $id];
			//make selected country as default
			$a = updatevalues('gr_country',$update,$where);
			
			//make existing as not default
			$a = DB::table('gr_country')->where('id','!=',$id)->update(['default_counrty' => '0']);
			
			if($a == '1') 
			{
				echo "Success"; exit;
			}
			else   
			{
				echo "Failed"; exit;
			}
		}
		
		/** edit country **/
		public function edit_country($id)
		{
			$id = base64_decode($id);
			$where = ['id' => $id];
			$get_country_details = get_details('gr_country',$where);
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_LOC');
			$get_allcountry_details = get_all_details('gr_country','co_status',10,'desc','id');
			return view('Admin.country.managecountry')->with('pagetitle',$page_title)->with('all_details',$get_allcountry_details)->with('country_detail',$get_country_details)->with('id',$id);
		}
		
		/** update country **/
		public function update_country()
		{
			if(Session::has('admin_id') == 1)
			
			{	$id = Input::get('cnty_id');
				$name = 'co_name';
				$old_name = Input::get('co_name');
				$check =  DB::table('gr_country')->select($name,'id')->where('id','<>',$id)->where('co_status','!=','2')->where($name,'=',$old_name)->get();
				$entry = array();	
				if(count($check) > 0)
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CNTY_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_CNTY_EXISTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CNTY_EXISTS');
					
					return Redirect::to('manage-country')->withErrors(['errors' =>$msg]);
				}
				
				$entry['co_name'] = $old_name;
				if(count($this->get_Adminactive_language)>0 )
				{
					foreach($this->get_Adminactive_language as $Lang)
					{	
						
						$entry['co_name_'.$Lang->lang_code] = $old_name;		
					}
				}
				
				$entry  = array_merge(array('co_code' => Input::get('co_code'),
				'co_curcode' => Input::get('cur_code'),
				'co_cursymbol' => Input::get('cur_sym'),
				'co_dialcode' => Input::get('tel_code'),									
				),$entry);
				$update = updatevalues('gr_country',$entry,['id' =>Input::get('cnty_id')]);
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-country');
			}
			else
			{
				return Redirect::to('admin-login');
			}
			
		}
		
		/** change notification as read **/
		public function read_notification(Request $request)
		{
			$update = updatevalues('gr_notification',['read_status' => '1'],['no_status' =>1 ]);
			return Redirect::to('admin-commission-tracking');
		}
		
		public function order_notification(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{ 
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_NOTIFICATION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_NOTIFICATION');
				return view('Admin.reports.order_notification')->with('pagetitle',$page_title);
				}else{
				return Redirect::to('admin-login');
			}
		}
		
		public function order_notification_ajax(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array(   0 => 'id', 
				1 => 'read_status', 
				2 => 'order_id', 
				3 => 'message', 
				4 => 'updated_at',
				5 => 'id'
				);
				/*To get Total count */
				$totalData = DB::table('gr_general_notification')
				->select('id')
				->where('receiver_id','=',Session::get('admin_id'))
				->where('receiver_type','=','gr_admin')
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
				
				
				$sql = DB::table('gr_general_notification')
				->select('id',
				'order_id',
				'message',
				'message_link',
				'updated_at',
				'read_status'
				)
				->where('receiver_id','=',Session::get('admin_id'))
				->where('receiver_type','=','gr_admin');
				if($orderId_search != '')
				{
					$q = $sql->whereRaw("order_id like '%".$orderId_search."%'"); 
				}
				if($message_search != '')
				{
					$q = $sql->whereRaw("message like '%".$message_search."%'"); 
				}
				if($view_search != '')
				{
					$q = $sql->where('read_status','=',$view_search); 
				}
				$totalFiltered = $sql->count();
				
				$q = $sql->orderBy($order,$dir)->orderBy('read_status', 'ASC')->skip($start)->take($limit);
				$posts =  $q->get();
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$view = (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($this->ADMIN_LANGUAGE.'.ADMIN_VIEW');
					$read = (Lang::has(Session::get('admin_lang_file').'.ADMIN_READ_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_READ_NOTIFICATION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_READ_NOTIFICATION');
					$unread = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNREAD_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNREAD_NOTIFICATION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNREAD_NOTIFICATION');
					foreach ($posts as $post)
					{
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="'.$post->id.'">';
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
		}
		
		public function status_change_notification(Request $request){
			//print_r($request->all());
			$update = ['read_status' => Input::get('status')];
			$val = Input::get('val');
			$status = Input::get('status');
			//return count($val); exit;
			for($i=0; $i< count($val); $i++)
			{
				$where = ['id' => $val[$i]];
				$a = updatevalues('gr_general_notification',$update,$where);
			}
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS_CHANGED_SUXES')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS_CHANGED_SUXES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STATUS_CHANGED_SUXES');
			echo $msg;
		}
		
		
		
		
		public function manage_featured_store(Request $request){
			if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_FEATURED_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_FEATURED_STORE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_FEATURED_STORE');
				return view('Admin.reports.featured_store')->with('pagetitle',$page_title);
				}else{
				return Redirect::to('admin-login');
			}
		}
		
		public function ajax_featuredStore_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$columns = array(   0 => 'createdAt',
                1 => 'id',
                2 => 'st_store_name',
                3 => 'mer_fname',
                4 => 'from_date',
                5 => 'to_date',
                6 => 'total_price',
                7 => 'admin_approved_status',
                8 => 'id'
				);
				/*To get Total count */
				$totalData = DB::table('gr_featured_booking')
                ->select('id')
                ->count();
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$view_search = trim($request->view_search);
				$merchantName_search = trim($request->merchantName_search);
				$storeName_search = trim($request->storeName_search);
				
				//DB::connection()->enableQueryLog();
				$sql = DB::table('gr_featured_booking')
                ->select('gr_featured_booking.id',
				'gr_featured_booking.from_date',
				'gr_featured_booking.to_date',
				'gr_featured_booking.total_price',
				'gr_featured_booking.total_num_days',
				'gr_featured_booking.payment_method',
				'gr_featured_booking.transaction_id',
				'gr_featured_booking.paymaya_pmtId',
				'gr_featured_booking.paymaya_receiptnum',
				'gr_featured_booking.paymaya_paid_time',
				'gr_featured_booking.admin_approved_status',
				'gr_featured_booking.createdAt',
				'gr_merchant.mer_fname',
				'gr_merchant.mer_lname',
				'gr_merchant.mer_email',
				'gr_store.st_store_name'
                )
                ->leftJoin('gr_merchant','gr_merchant.id','=','gr_featured_booking.mer_id')
                ->leftJoin('gr_store','gr_store.id','=','gr_featured_booking.store_id');
				if($merchantName_search != '')
				{
					$q = $sql->whereRaw("CONCAT_WS(' ', if(gr_merchant.mer_fname is null,'',gr_merchant.mer_fname), if(gr_merchant.mer_lname is null,'',gr_merchant.mer_lname), if(gr_merchant.mer_email is null,'',gr_merchant.mer_email)) like '%".$merchantName_search."%'");
				}
				if($storeName_search != '')
				{
					$q = $sql->whereRaw("gr_store.st_store_name like '%".$storeName_search."%'");
				}
				if($view_search != '')
				{
					$q = $sql->where('gr_featured_booking.admin_approved_status','=',$view_search);
				}
				$totalFiltered = $sql->count();
				
				$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts =  $q->get();
				
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$view = (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($this->ADMIN_LANGUAGE.'.ADMIN_VIEW');
					$viewDet = (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_PAYMENT_DET')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_PAYMENT_DET') : trans($this->ADMIN_LANGUAGE.'.ADMIN_VIEW_PAYMENT_DET');
					$read = (Lang::has(Session::get('admin_lang_file').'.ADMIN_APPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_APPROVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_APPROVE');
					$unread = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DISAPPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISAPPROVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DISAPPROVE');
					$tnxnID = (Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRANSACTION_ID');
					$paid_at = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAID_DATE');
					$receipt_num = (Lang::has(Session::get('admin_lang_file').'.ADMIN_RECEIPT_NUM')) ? trans(Session::get('admin_lang_file').'.ADMIN_RECEIPT_NUM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RECEIPT_NUM');
					$pmt_id = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYMENT_ID');
					$click_to_disapprove = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DISAPPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DISAPPROVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DISAPPROVE');
					$click_to_approve = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_APPROVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_APPROVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_APPROVE');
					
					foreach ($posts as $post)
					{
						if($post->admin_approved_status == 1){
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',0);" id="statusLink_'.$post->id.'" class="btn btn-success tooltip-demo" title="'.$click_to_disapprove.'"><i class="lnr lnr-thumbs-up" aria-hidden="true" title="Click to Disapprove"></i>'.$read.'</a>';
							} else{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',1);" id="statusLink_'.$post->id.'" class="btn btn-danger btn-sm tooltip-demo" title="'.$click_to_approve.'"><i class="lnr lnr-thumbs-down" title="Click to Approve" ></i>'.$unread.'</a>';
						}
						if($post->payment_method=='offline'){
							$body = '
							<table class="table table-bordered table-striped table-hover">
							<tr>
							<td width="30%"><strong>'.$tnxnID.' :</strong>
							<td>'.$post->transaction_id.'</td>
							</tr>
							<tr>
							<td><strong>'.$paid_at.' :</strong>
							<td>'.date('m/d/Y H:i:s',strtotime($post->createdAt)).'</td>
							</tr>
							</table>
							';
							}elseif($post->payment_method=='paymaya'){
							$body = '
							<table class="table table-bordered table-striped table-hover">
							<tr>
							<td width="30%"><strong>'.$tnxnID.' :</strong>
							<td>'.$post->transaction_id.'</td>
							</tr>
							<tr>
							<td><strong>'.$paid_at.' :</strong>
							<td>'.date('m/d/Y H:i:s',strtotime($post->createdAt)).'</td>
							</tr>
							<tr>
							<td><strong>'.$receipt_num.' :</strong>
							<td>'.$post->paymaya_receiptnum.'</td>
							</tr>
							<tr>
							<td><strong>'.$pmt_id.' :</strong>
							<td>'.$post->paymaya_pmtId.'</td>
							</tr>
							
							</table>
							';
							}else{
							$body = '
							<table class="table table-bordered table-striped table-hover">
							<tr>
							<td width="30%"><strong>'.$tnxnID.' :</strong>
							<td>'.$post->transaction_id.'</td>
							</tr>
							<tr>
							<td><strong>'.$paid_at.' :</strong>
							<td>'.date('m/d/Y H:i:s',strtotime($post->createdAt)).'</td>
							</tr>
							</table>
							';
						}
						
						$modal = '
						<!-- Modal -->
						<div id="myModal_'.$post->id.'" class="modal fade" role="dialog">
						<div class="modal-dialog">
						
						<!-- Modal content-->
						<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">'.$viewDet.'</h4>
						</div>
						<div class="modal-body">
						'.$body.'
						</div>
						<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
						</div>
						
						</div>
						</div>
						';
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="'.$post->id.'">';
						$nestedData['SNo'] 		= ++$snoCount;
						$nestedData['storeName'] 	= $post->st_store_name;
						$nestedData['merName'] 	= $post->mer_fname.' '.$post->mer_lname.'<br>'.$post->mer_email;
						$nestedData['fromDate'] 	= date('m/d/Y',strtotime($post->from_date));
						$nestedData['toDate'] 	= date('m/d/Y',strtotime($post->to_date));
						$nestedData['paidAmount'] 	= number_format($post->total_price,2);
						$nestedData['approvestatus'] 	= $statusLink;
						$nestedData['view'] 	= '<a href="javascript:;" data-toggle="modal" data-target="#myModal_'.$post->id.'">'.$view.'</a>'.$modal;
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
		}
		
		
		
		//    public function featStore_approve_status(Request $request){
		//        $update = ['admin_approved_status' => Input::get('status'),'approvedAt'=>date('Y-m-d H:i:s')];
		//        $val = Input::get('val');
		//        $status = Input::get('status');
		//        //return count($val); exit;
		//        for($i=0; $i< count($val); $i++)
		//        {
		//            $where = ['id' => $val[$i]];
		//            $a = updatevalues('gr_featured_booking',$update,$where);
		//        }
		//        $msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS_CHANGED_SUXES')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS_CHANGED_SUXES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STATUS_CHANGED_SUXES');
		//        echo $msg;
		//    }
		
		public function featStore_approve_status(Request $request){
			$update = ['admin_approved_status' => Input::get('status'),'approvedAt'=>date('Y-m-d H:i:s')];
			$val = Input::get('val');
			$status = Input::get('status');
			//return count($val); exit;
			$featured_details = DB::table('gr_general_setting')->select('gs_featured_numstore','gs_featured_store')->first();
			if(empty($featured_details) === true)
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CANT_MK_FEATURED')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANT_MK_FEATURED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CANT_MK_FEATURED');
				echo $msg;
			}
			else
			{
				for($i=0; $i< count($val); $i++)
				{
					/* get already booked shop count in selected date */
					if(Input::get('status')==1){
						$check_count = Admin::check_approved_count($val[$i],$featured_details->gs_featured_numstore);
						//$check_count = Admin::check_approved_count($val[$i],4);
						if($check_count < 4)
						{
							$where = ['id' => $val[$i]];
							$a = updatevalues('gr_featured_booking',$update,$where);
						}
						else
						{
							//echo trans(Session::get('admin_lang_file').'.ADMIN_ON_CAN_MK_FEATURED');
							echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_ON_CAN_MK_FEATURED')) ? trans(Session::get('admin_lang_file').'.ADMIN_ON_CAN_MK_FEATURED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ON_CAN_MK_FEATURED');
							exit;
						}
					}
					else{
						$where = ['id' => $val[$i]];
						$a = updatevalues('gr_featured_booking',$update,$where);
					}
				}
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS_CHANGED_SUXES')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS_CHANGED_SUXES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STATUS_CHANGED_SUXES');
				echo $msg;
			}
		}
		
		/* --------manage failed orders -----*/
		public function manage_failed_orders(Request $request)
		{
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_FAIL_OR_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_FAIL_OR_AMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_FAIL_OR_AMT');
			$get_del_failed_orders = array();
			
			return view('Admin.reports.failed_orders')->with(['pagetitle' => $pagetitle,'failed_orders' => $get_del_failed_orders]);
		}
		
		public function failed_orders_ajax(Request $request)
		{
			$columns = array(
            0=>'ord_id',
            1=>'cus_email',
            2=> 'ord_transaction_id',
            3=> 'st_store_name',
            4=>'order_total',
            5=>'agent_fname',
            6=>'deliver_fname',
            7=>'ord_failed_reason',
            8=>'ord_failed_reason',
			);
			
			
			$sql 			= Admin::get_failed_orders();
			$totalData		= $sql->count();
			$totalFiltered 	= $totalData;
			$limit 			= $request->input('length');
			$start 			= $request->input('start');
			$order 			= $columns[$request->input('order.0.column')];
			$dir 			= $request->input('order.0.dir');
			$cusEmail_search = trim($request->cusEmail_search);
			$orderId_search = trim($request->orderId_search);
			$stName_search 	= trim($request->stName_search);
			$posts = Admin::get_failed_orders($cusEmail_search,$orderId_search,$stName_search,$order,$dir,$start,$limit);
			
			$data = array();
			if(count($posts) > 0)
			{
				foreach($posts as $post)
				{
					$haveToPayAmount = $post->order_total - $post->paid_amt;
					if(($haveToPayAmount) > 0 )
					{
						$paymayaForm='';
						if($post->cus_paymaya_status=='Publish')
						{
							$btn_text =
							$paymayaForm='
							<form method="post" action="'.url('').'/paymaya-failed-payment" id="validate_form">
							<input name="_token" type="hidden" value="'.csrf_token().'">
							<input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.$haveToPayAmount.'">
							<input name="client_id" id="client_id" type="hidden" value="'.$post->cus_paymaya_clientid.'">
							<input name="secret_id" id="secret_id" type="hidden" value="'.$post->cus_paymaya_secretid.'">
							<input name="customer_id" id="customer_id" type="hidden" value="'.$post->cus_id.'">
							<input name="trans_id" id="order_id" type="hidden" value="'.$post->ord_transaction_id.'">
							<input name="st_id" id="st_id" type="hidden" value="'.$post->ord_rest_id.'">
							<button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.__(Session::get('admin_lang_file').'.ADMIN_PAYTHRU').' '.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'">'.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'</button>
							</form>&nbsp;
							';
							
						}
						if($post->cus_netbank_status == "Publish")
						{
							$payOfflineText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_OFFLINE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_OFFLINE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_OFFLINE');
							$bank_details = "'".$post->cus_bank_accno.'`'.$post->cus_bank_name.'`'.$post->cus_branch.'`'.$post->cus_ifsc."'";
							$paymayaForm .='<a href="javascript:call_pay_fun(\''.$post->ord_transaction_id.'\','.$post->ord_rest_id.','.$haveToPayAmount.','.$bank_details.');" class="btn btn-success btn-sm" >'.$payOfflineText.'</a>';
						}
						if($post->cus_netbank_status == 'Unpublish' && $post->cus_paymaya_status == 'Unpublish')
						{
							$paymayaForm .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_DET_NOT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_DET_NOT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_DET_NOT');
						}
						//$payOnlineText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_ONLINE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_ONLINE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_ONLINE');
						
						$lastCol = $paymayaForm;
					}
					else
					{
						$lastCol = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_BALANCE_TO_PAY')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_BALANCE_TO_PAY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_NO_BALANCE_TO_PAY');
					}
					$nestedData['SNo'] 		= $start+1;
					$nestedData['custEmail']= $post->cus_email;
					$nestedData['orderId'] 	= $post->ord_transaction_id;
					$nestedData['stName'] 	= ucfirst($post->st_store_name);
					$nestedData['orderAmt'] = $post->ord_currency.' '.number_format($post->order_total,2);
					//$nestedData['agent']	= ucfirst($post->agent_fname).' '.$post->agent_lname;
					$nestedData['delBoy']	= ucfirst($post->deliver_fname).' '.$post->deliver_lname;
					$nestedData['reason'] 	= $post->ord_failed_reason;
					//$nestedData['action'] 	= $lastCol;
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
		
		/* refund amount to customer */
		public function pay_customer(Request $request)
		{
			/*print_r($_POST); exit;Array ( [_token] => cOZ8cOY5DMFbRjqFjga5JzHKmJxxsP3Sr3SM14bp [ord_cancel_paidamt] => 5.05 [ord_cancelpaid_transid] => test [ord_id] => 25*/
			$insertArr = ['ord_cancel_paidamt'			=> $request->ord_cancel_paidamt,
            'ord_cancelpaid_transid' 	=> $request->ord_cancelpaid_transid,
            'cancel_paid_date'			=>date('Y-m-d H:i:s'),
            'ord_cancel_paytype' 		=> $request->ord_cancel_paytype,
            'ord_cancel_payment_status'	=>'1'
			];
			
			DB::table('gr_order')->where(['ord_transaction_id'=>$request->trans_id,
            'ord_rest_id' => $request->st_id,
            'ord_status'=>'9'])
            ->update($insertArr);
			/* send mail to customer */
			$get_order_details = Admin::get_failed_or_details($request->trans_id,$request->st_id);
			if(count($get_order_details) > 0)
			{
				$send_mail_data = array('order_details'		=> $get_order_details,
                'transaction_id' 	=> $request->trans_id);
				$mail = $get_order_details[0]->order_ship_mail;
				Mail::send('email.failed_refund_mail', $send_mail_data, function($message) use($mail)
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_OR_REFUNDED');
					$message->to($mail)->subject($msg);
				});
			}
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_PAID_SUXUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_PAID_SUXUS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_PAID_SUXUS');
			Session::flash('message',$msg);
			return Redirect::to('manage_failed_orders');
		}
		
		/*Delivery Boy Map*/
		public function delivery_boy_map(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_BOY_LOCATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_BOY_LOCATION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELIVERY_BOY_LOCATION');
				
				$del_boy_name = Input::get('del_boy_name');
				$del_boy_location = Input::get('del_boy_location');
				$us4_lat = Input::get('us4_lat');
				$us4_lon = Input::get('us4_lon');
				$us4_radius = Input::get('us4_radius');
				$del_boy_phone = Input::get('del_boy_phone');
				$del_boy_status = Input::get('del_boy_status');
				
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
					$delboy_array['']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
					foreach($delboy_lists as $restar)
					{
						$delboy_array[$restar->name]=$restar->name.' - '.$restar->deliver_email;
					}
				}
				$delboy_list = $delboy_array;
				/* EOF Delivery BOY LISTS */
				return view('Admin.delivery_boy_map')->with(['pagetitle' => $pagetitle,'delivery_list'=>$delboy_list,'delivery_boy_details'=>$delivery_boy_details,'delivery_boy_status' => $del_boy_status,'del_boy_location'=>$del_boy_location,'us4_lat'=>$us4_lat,'us4_lon'=>$us4_lon,'us4_radius'=>$us4_radius,]);
			}
			else
			{
				return Redirect::to('admin-login');
			}
			
		}
		
		
	}
