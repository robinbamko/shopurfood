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
	
	use Carbon\Carbon;
	
	use Redirect;
	
	use App\Admin;
	
	use App\Settings;
	
	use Image;
	
	class SubadminController extends Controller
	{
		
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		
		
		public function add_subadmin(){
			
			$s_id = '';
			$PageTitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_SUBADMIN') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_ADD_SUBADMIN');
			
			return view('Admin.subadmin.add_edit_subadmin')->with('pagetitle',$PageTitle)->with('s_id',$s_id);
			
		}
		
		public function manage_subadmin()
		{
			$PageTitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_SUBADMIN') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_MANAGE_SUBADMIN');
			
			return view('Admin.subadmin.manage_subadmin')->with('pagetitle',$PageTitle);
		}
		
		
		//**** SUBADMIN EMAIL EXISTS CHECK ****//
		public function ajax_checksubadmin(){
			
			$subadmin_email_id = Input::get('subadmin_email_id');
			$check_email_exists = get_details('gr_subadmin',['adm_email' => $subadmin_email_id],'adm_email');
			$check_email_exists_admin =get_details('gr_admin',['adm_email' => $subadmin_email_id],'adm_email');
			
			if(!empty($check_email_exists) > 0 || !empty($check_email_exists_admin) > 0) { 	
			echo "2"; } else { echo "1"; }
		}
		
		
		public function subadmin_list(Request $request)
		{
			$columns = array(
			
			0 =>'id',
			1 =>'id',
			2 =>'adm_fname',
			3 =>'adm_email',
			4 =>'adm_mobile',
			5 =>'sub_last_login_date',
			6 =>'sub_last_logout_date',
			7 =>'sub_login_ip',
			8 =>'id',
			9 =>'sub_status',
			10 =>'sub_status'
			);
			$total_record = DB::table('gr_subadmin')->select('id','adm_fname','adm_email','sub_last_login_date','sub_last_logout_date','sub_login_ip','sub_status')->orderBy('id','desc')->where('sub_status','!=',2)->get();
			$totalData = $total_record->count();     
			$totalFiltered = count($total_record); 
			
			//*** Here are the parameters sent from client for paging ***//
			
			$start = $request->input ( 'start' );     // Skip first start records
			$length = $request->input ( 'length' );   //  Get length record from start
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');
			
			//*** Search Function***//
			
			$search_name_email = trim($request->subadmin_search_name_email);  		
			
			
			if($search_name_email == ''){   
				$record = DB::table('gr_subadmin')->where('sub_status','!=','2')->orderBy($order,$dir)->skip($start)->take($length)->get();
			} 
			else 
			{  
				$Q = array();
				$sql = DB::table('gr_subadmin')->where('sub_status','!=','2');
				
				if($search_name_email != '' ){
					$Q =  $sql->where('adm_fname','LIKE','%'.$search_name_email.'%')->orwhere('adm_email','LIKE','%'.$search_name_email.'%');
				}	
				
				
				$Q = $sql->orderBy($order,$dir)->skip($start)->take($length);			 
				$totalFiltered = $Q->count();
				$record = $Q->get();				
			}
			//*** END Search Function ***//
			
			$data = array ();
			$snoCount = $start;
		    foreach ( $record as $details ) {
				
				
		    	if($details->sub_status==1)
				{			
					$block='<a href="'.url("admin-subadmin-status").'/'.$details->id.'/0'.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
				}
				else
				{
					$block='<a href="'.url("admin-subadmin-status").'/'.$details->id.'/1'.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock"></i></a>';
				}
				
				
				//***delete***//
				$deletelink = '<a href="'.url("admin-subadmin-status").'/'.$details->id.'/2'.'" title="delete" class="tooltip-demo"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
				
				$editlink = '<a href="'.url("edit-subadmin").'/'.base64_encode($details->id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" ></i></a>';
				
				/*----------Delete --------*/
				
				/*----------Last Login Date--------*/
				
				if($details->sub_last_login_date != ''){
					$last_login_date = $details->sub_last_login_date;
					} else {
					$last_login_date = '--';
				}
				/*----------Last Logout --------*/
				
				if($details->sub_last_logout_date != ''){
					$last_logout_date = $details->sub_last_logout_date;
					} else {
					$last_logout_date = '--';
				}
				
				/*----------Last IP --------*/
				
				if($details->sub_login_ip != ''){
					$last_login_ip = $details->sub_login_ip;
					} else {
					$last_login_ip = '--';
				}
				
				
		    	$nestedData = array ();
				$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" id="check_id" value="'.$details->id.'">';
				$nestedData['SNo'] = ++$snoCount;
				$nestedData['sub_name'] = ucfirst($details->adm_fname);
				$nestedData['sub_email'] = $details->adm_email;
				$nestedData['sub_mobile'] = $details->adm_phone1;
				$nestedData['sub_login'] = $last_login_date;
				$nestedData['sub_logout'] = $last_logout_date;
				$nestedData['sub_login_ip'] = $last_login_ip;
				$nestedData['edit'] = $editlink;
				$nestedData['status'] = $block;
				$nestedData['delete'] = $deletelink;
				
				$data [] = $nestedData;
			}
			/*
				* This below structure is required by Datatables
			*/ 
			
			$tableContent = array (
			"draw" => intval ( $request->input ( 'draw' ) ), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal" => intval ( $totalData ), // total number of records
			"recordsFiltered" => intval ( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data" => $data
			);
			
			return $tableContent;
		}
		
		/* SUB ADMIN STATUS CHANGE FOR BLOCK, UNBLOCK AND DELETE */
		public function admin_subadmin_status($id,$status)
		{
			$update = ['sub_status' => $status];
			$where = ['id' => $id];
			$up = updatevalues('gr_subadmin',$update,$where);
			
			if($status == 1)//Active
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message' ,$msg);
			}
			else if($status == 0)//Block
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message' ,$msg);
			}
			else if($status == 2)//delete
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message' ,$msg);
			}
			Session::flash('admin_message',$msg);
			return Redirect::to('manage-subadmin')->withInput();
		}
		
		public function change_multi_subadmin_status()
		{
			$ids = Input::get('val');
			$status = Input::get('status');
			for($i=0;$i<count($ids);$i++)
			{
				$id = $ids[$i];
				$update = updatevalues('gr_subadmin',['sub_status' => $status],['id'=>$id]);
			}
			
			if(Input::get('status') == 1)//Active
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message' ,$msg);
			}
			else if(Input::get('status') == 0)//Block
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message' ,$msg);
			}
			else if(Input::get('status') == 2)//delete
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message' ,$msg);
			}
	        Session::flash('admin_message',$msg);
			
		}
		
		
		
		public function edit_subadmin($id){
			
			$s_id = base64_decode($id);
			$subadmin_details = get_details('gr_subadmin',['id' => $s_id]);
			$PageTitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_SUBADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EDIT_SUBADMIN');
			
			return view('Admin.subadmin.add_edit_subadmin')->with('pagetitle',$PageTitle)->with('s_id',$s_id)->with('subadmin_details',$subadmin_details);
			
		}
		
		//**** SUBADMIN ADD FORM SUBMIT ****//
		public function submit_subadmin(Request $request){
			
			$subadmin_email_id = mysql_escape_special_chars(Input::get('subadmin_email_id'));
			$subadmin_name = mysql_escape_special_chars(Input::get('subadmin_name'));
			$subadmin_phone = mysql_escape_special_chars(Input::get('subadmin_phone'));
			$subadmin_password = mysql_escape_special_chars(Input::get('subadmin_password'));
			$subadmin_privilege = mysql_escape_special_chars(Input::get('subadmin_privilege'));
			
			
			//******Check Validator*******//
			$email_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL');
			
			$name_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_NAME');
			
			$phone_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PHONE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_PHONE');
			
			$pass_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_PASS');
			
			$prev_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOOSE_PRIVILEGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOOSE_PRIVILEGE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CHOOSE_PRIVILEGE');
			
			$pass_match_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD_MUST_MATCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD_MUST_MATCH') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PASSWORD_MUST_MATCH');
			
			$pass_min_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD_MUST_6')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD_MUST_6') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PASSWORD_MUST_6');
			
			
			$validator = Validator::make($request->all(), [
		    'subadmin_name' 					 => 'required',
            'subadmin_email_id'					 => 'required|email',
            'subadmin_phone' 					 => 'required',
            'subadmin_password' 				 => 'required|min:6|required_with:password_confirmation|same:password_confirmation',
			'password_confirmation' 			 => 'required'],[
            //'subadmin_privilege' 		 		 => 'required',
			'subadmin_email_id.required|email'	 => $email_err_msg,
			'subadmin_name.required'			 => $name_err_msg,
			'subadmin_phone.required'			 => $phone_err_msg,
			'subadmin_password.required'		 => $pass_err_msg,
			'password_confirmation.required'	 => $pass_err_msg,
			//'subadmin_privilege.required'		 => $prev_err_msg,
			'subadmin_password.min'		 		 => $pass_min_err_msg,
			'subadmin_password.required_with'	 => $pass_match_err_msg,
			'subadmin_password.same'			 => $pass_match_err_msg,
			]);
			
			if ($validator->fails()) {
				return redirect('add-subadmin')->withErrors($validator) ->withInput();                 
			}
			
			//******Check Email Exists*******//
			$check_email_exists = get_details('gr_subadmin',['adm_email' => $subadmin_email_id]);
			
			if(!empty($check_email_exists )) {
				
				$error_email_exists = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL_ALREADY_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL_ALREADY_EXISTS') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_EMAIL_ALREADY_EXISTS');
				
				return redirect('add-subadmin')->withErrors($error_email_exists)->withInput();      
			}
			
			//******Insert Subadmin*******//
			
			//$excludeArr = array('Category','Choices','Merchant','Restaurant','Store','Customer','Item','Product','Delivery_Manager','Agent','Delivery_Boy','CMS','FAQ','Review','Order','Commission','Inventory','Cancellation','Refer_Friend');
			$excludeArr = Config('subadmin_privilege');
			$privArr = array();
			
			foreach (Input::get() as $key => $val) 
			{
				if (in_array($key, $excludeArr)) 
				{
					$privArr[$key] = $val;
				}
			}
			
			$datetime_str = Carbon::now();
			$datetime =  $datetime_str->toDateTimeString(); 
			
			$data = array( 
			
			'adm_fname' 		=> $subadmin_name,
			'adm_email' 		=> $subadmin_email_id,
			'adm_phone1' 		=> $subadmin_phone,
			'adm_password' 		=> md5($subadmin_password),
			'adm_decrypt_password' 	=> $subadmin_password,
			'sub_status' 		=> 1,
			'sub_privileges' 	=> serialize($privArr),
			'created_date' 		=> $datetime,
			'modified_date' 	=> $datetime,
			
			);
			
			$insert_subadmin = insertValues('gr_subadmin',$data);
			
			if($insert_subadmin){
				/* MAIL FUNCTION*/
				
				$send_mail_data = array(
				'name'=>$subadmin_name,
				'email'=>$subadmin_email_id,
				'password'=>$subadmin_password,
				); 
				Mail::send('email.subadmin_register_email',$send_mail_data,function($message) use($send_mail_data) {
					$email = $send_mail_data['email'];
					$name = $send_mail_data['name'];
					$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email,$name)->subject($subject);
					
				});
			}
			
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');		
			
			Session::flash('admin_message',$msg);
			return redirect('manage-subadmin');
			
			
		}
		
		//**** SUBADMIN EDIT FORM SUBMIT ****//
		public function edit_submit_subadmin(Request $request){
			
			$subadmin_email_id = Input::get('subadmin_email_id');
			$old_email = Input::get('old_email');
			$subadmin_name = Input::get('subadmin_name');
			$subadmin_phone = Input::get('subadmin_phone');
			$subadmin_password = Input::get('subadmin_password');
			$old_password = Input::get('old_password');
			$subadmin_privilege = Input::get('subadmin_privilege');
			$subadmin_edit_id = Input::get('subadmin_edit_id');
			
			
			//******Check Validator*******//
			
			$name_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_NAME');
			
			$phone_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PHONE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_PHONE');
			
			$prev_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOOSE_PRIVILEGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOOSE_PRIVILEGE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CHOOSE_PRIVILEGE');
			
			
			$validator = Validator::make($request->all(), [
            'subadmin_name' 					 => 'required',
            'subadmin_phone' 					 => 'required'
			],[
            //'subadmin_privilege' 		 		 => 'required',
			'subadmin_name.required'			 => $name_err_msg,
			'subadmin_phone.required'			 => $phone_err_msg,
			//'subadmin_privilege.required'		 => $prev_err_msg,
			]);
			
			if ($validator->fails()) {
				return redirect('edit-subadmin/'.$subadmin_edit_id)->withErrors($validator) ->withInput();                 
			}
			
			
		    //$excludeArr = array('Category','Choices','Merchant','Restaurant','Store','Customer','Item','Product','Delivery_Manager','Agent','Delivery_Boy','CMS','FAQ','Review','Order','Commission','Inventory','Cancellation','Refer_Friend');
			$excludeArr = Config('subadmin_privilege');
			$privArr = array();
			
			foreach (Input::get() as $key => $val) 
			{
				if (in_array($key, $excludeArr)) 
				{
					$privArr[$key] = $val;
				}
				
			}
			
			$datetime = Carbon::now();
			
			$data_edit = array( 
			'adm_fname' 		=> $subadmin_name,
			'adm_phone1' 		=> $subadmin_phone,
			'sub_privileges' 	=> serialize($privArr),
			'modified_date' 	=> $datetime,
			'adm_password' 		=> md5($subadmin_password),
			'adm_decrypt_password' 	=> $subadmin_password,
			);
			
			if(($old_email != $subadmin_email_id) || ($old_password != $subadmin_password))
			{
				$send_mail_data = array(
				'name'=>$subadmin_name,
				'email'=>$subadmin_email_id,
				'password'=>$subadmin_password,
				); 
				Mail::send('email.subadmin_register_email',$send_mail_data,function($message) use($send_mail_data) {
					$email = $send_mail_data['email'];
					$name = $send_mail_data['name'];
					$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->L_ADMIN_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email,$name)->subject($subject);
					
				});
			}
			
			$insert_subadmin = updatevalues('gr_subadmin',$data_edit,['id'=>$subadmin_edit_id]);
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');		
			
			Session::flash('admin_message',$msg);
			return redirect('manage-subadmin');
			
		}
		
	}
?>