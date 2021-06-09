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
	
	use App\Coupon;	
	
	use Image;
	
	use Excel;
	
	class CouponController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->setAdminLanguage();
		}
		
		public function manage_coupon()
		{
			if(Session::has('admin_id') == 1)
			{
				
				$where = [];
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_COUPON')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_COUPON') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_COUPON');
				$get_coupon_details = get_all_details('gr_coupon','coupon_status',10,'desc','id');
				return view('Admin.manage_coupon')->with('pagetitle',$page_title)->with('all_details',$get_coupon_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function add_coupon()
		{
			if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_COUPON')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_COUPON') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_COUPON');
				$id = ''; 
				$get_cms_details = get_all_details('gr_coupon','coupon_status',10,'desc','id');
				$user_list = Coupon::user_lists();
				return view('Admin.add_edit_coupon',compact('user_list'))->with('pagetitle',$page_title)->with('all_details',$get_cms_details)->with('id',$id);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function edit_coupon($id)
		{
			$id 	= base64_decode($id);
			$where	= ['id' => $id];
			$get_coupon_details  = get_details('gr_coupon',$where);
			$page_title			 = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_CMS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EDIT_CMS');
			$get_allcoupon_details = get_all_details('gr_coupon','coupon_status',10,'desc','id');
			$user_list = Coupon::user_lists();
			return view('Admin.add_edit_coupon',compact('user_list'))->with('pagetitle',$page_title)->with('all_details',$get_allcoupon_details)->with('coupon_detail',$get_coupon_details)->with('id',$id);
		}
		public function add_update_coupon(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{ 
				$coupon_name_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_NAME_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_NAME_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_NAME_VAL');
				$coupon_code_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_CODE_VAL');
				$coupon_code_unique_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CODE_UNIQUE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_CODE_UNIQUE_VAL');
				$coupon_percentage_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_PERCENTAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_PERCENTAGE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_PERCENTAGE_VAL');
				$coupon_sdate_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_SDATE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_SDATE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_SDATE_VAL');
				$coupon_edate_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_EDATE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_EDATE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_EDATE_VAL');
				$coupon_cust_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_CUSTOMER_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_CUSTOMER_VAL');
				$coupon_desc_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUPON_DESC_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUPON_DESC_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUPON_DESC_VAL');
				$coupon_id = Input::get('coupon_id');
				
				if($coupon_id != '')
				{
					$validator = Validator::make($request->all(), [ 'coupon_name' 		=> 'required',
					'coupon_code' 		=> 'required|unique:gr_coupon,coupon_code,'.$coupon_id.'',
					'coupon_percent' 	=> 'required',
					'coupon_start_date' => 'required',
					'coupon_end_date' 	=> 'required',
					'coupon_customer' 	=> 'required',
					'coupon_desc_en' 	=> 'required',
					],
					[
					'coupon_name.required' 		=> $coupon_name_err_msg,
					'coupon_code.required'		=> $coupon_code_err_msg,
					'coupon_code.unique'		=> $coupon_code_unique_err_msg,
					'coupon_percent.required'	=> $coupon_percentage_err_msg,
					'coupon_start_date.required'=> $coupon_sdate_err_msg,
					'coupon_end_date.required'	=> $coupon_edate_err_msg,
					'coupon_customer.required'	=> $coupon_cust_err_msg,
					'coupon_desc_en.required'	=> $coupon_desc_err_msg,
					]
					);
				}
				else{
					$validator = Validator::make($request->all(), ['coupon_name' 		=> 'required',
					'coupon_code' 		=> 'required|unique:gr_coupon',
					'coupon_percent' 	=> 'required',
					'coupon_start_date' => 'required',
					'coupon_end_date' 	=> 'required',
					'coupon_customer' 	=> 'required',
					'coupon_desc_en' 	=> 'required',
					],[
					'coupon_name.required' 		=> $coupon_name_err_msg,
					'coupon_code.required'		=> $coupon_code_err_msg,
					'coupon_code.unique'		=> $coupon_code_unique_err_msg,
					'coupon_percent.required'	=> $coupon_percentage_err_msg,
					'coupon_start_date.required'=> $coupon_sdate_err_msg,
					'coupon_end_date.required'	=> $coupon_edate_err_msg,
					'coupon_customer.required'	=> $coupon_cust_err_msg,
					'coupon_desc_en.required'	=> $coupon_desc_err_msg,
					]
					);
				}
				
				if ($validator->fails()) {
		            return redirect('add-coupon')->withErrors($validator)->withInput();
					}else{
					$coupon_name 		= Input::get('coupon_name');
					$coupon_code 		= Input::get('coupon_code');	
					$coupon_percent 	= Input::get('coupon_percent');
					$coupon_start_date 	= Input::get('coupon_start_date');
					$coupon_end_date 	= Input::get('coupon_end_date');
					$coupon_customer_arr= Input::get('coupon_customer');
					$coupon_customer 	= implode(',', $coupon_customer_arr);
					$coupon_desc_en 	= Input::get('coupon_desc_en');
					
					$insertArr = array(
					'coupon_name' 		=> $coupon_name,
					'coupon_code' 		=> $coupon_code,
					'coupon_percent' 	=> $coupon_percent,
					'coupon_start_date' => $coupon_start_date,
					'coupon_end_date' 	=> $coupon_end_date,
					'coupon_customer' 	=> $coupon_customer,
					'coupon_desc_en' 	=> $coupon_desc_en,
					'coupon_status' 	=> '1',
					);
					if($coupon_id != '')
					{
						$update = updatevalues('gr_coupon',$insertArr,['id' =>$coupon_id]);
						$msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					}
					else
					{
						$insert = insertvalues('gr_coupon',$insertArr);
						$msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					}
					return Redirect::to('manage-coupon')->withErrors(['success'=>$msg])->withInput();
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function change_coupon_status($id,$status)
		{	
			$update = ['coupon_status' => $status];
			$where 	= ['id' => $id];
			$a = updatevalues('gr_coupon',$update,$where);
			if($status == 1) //Active
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-coupon')->withErrors(['success'=>$msg])->withInput();
			}
			if($status == 2) //Delete
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-coupon')->withErrors(['success'=>$msg])->withInput();
			}
			else   //block
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-coupon')->withErrors(['success'=>$msg])->withInput();
			}
		}
	}
