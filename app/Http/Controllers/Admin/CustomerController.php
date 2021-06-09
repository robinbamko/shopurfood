<?php
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rule;
	use Validator;
	use File;
	use Session;
	
	use Mail;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use App\Admin;
	
	use App\Settings;
	
	use Image;
	
	use Excel;
	
	class CustomerController extends Controller
	{
		//
		
		public function __construct()
		{
			parent::__construct();
			$this->setAdminLanguage();
		}
		
		public function random_password( $length = 8 )
		{
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			//$chars = "0123456789";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		
		public function manage_customer($id='')
		{
			if(Session::has('admin_id') == 1)
			{
				if($id!='')
				{
					DB::table('gr_customer')->update(['cus_read_status' => 1]);
					return Redirect::to('manage-customer');
				}
				$where = [];
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_CUSTOMER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_CUSTOMER');
				$get_customer_details = array();//get_all_details('gr_customer','cus_status',10,'desc','cus_id');
				return view('Admin.manage_customer')->with('pagetitle',$page_title)->with('all_details',$get_customer_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function ajax_customer_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$columns = array(
                0 =>'cus_fname',
                1 =>'cus_id',
                2 =>'cus_fname',
                3=> 'cus_email',
                4=> 'cus_login_type',
                5=> 'cus_id',
                6=> 'cus_status',
                7=> 'cus_id'
				);
				/*To get Total count */
				$totalData = DB::table('gr_customer')
                ->select('cus_id')
                ->where('cus_status','<>','2')
                ->count();
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$cusName_search = trim($request->cusName_search);
				$cusEmail_search = trim($request->cusEmail_search);
				$addedBy_search = trim($request->addedBy_search);
				$status_search = trim($request->status_search);
				if($cusName_search=='' && $cusEmail_search=='' && $addedBy_search=='' && $status_search=='')
				{
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_customer')->select('cus_id',
                    'cus_fname',
                    'cus_email',
                    'cus_login_type',
                    'cus_status'
					)
                    ->where('cus_status','<>','2')
                    ->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					
					$sql = DB::table('gr_customer')
                    ->select('cus_id',
					'cus_fname',
					'cus_email',
					'cus_login_type',
					'cus_status'
                    )
                    ->where('cus_status','<>','2');
					if($cusName_search != '')
					{
						$q = $sql->whereRaw("cus_fname like '%".$cusName_search."%'");
					}
					if($cusEmail_search != '')
					{
						$q = $sql->whereRaw("cus_email like '%".$cusEmail_search."%'");
					}
					if($addedBy_search != '')
					{
						$q = $sql->where("cus_login_type",$addedBy_search);
					}
					if($status_search != '')
					{
						$q = $sql->where('cus_status',$status_search);
					}
					$totalFiltered = $q->count();
					//DB::connection()->enableQueryLog();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					
					
				}
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$click_to_block = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
					$click_to_unblock = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
					$click_to_edit = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
					$click_to_delete = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
					foreach ($posts as $post)
					{
						if($post->cus_status == 1)
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->cus_id.'\',0);" id="statusLink_'.$post->cus_id.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
						}
						else
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->cus_id.'\',1);" id="statusLink_'.$post->cus_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'"></i></a>';
						}
						if($post->cus_login_type == 1){
							$loginType = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NORMAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_NORMAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_NORMAL');
							} elseif($post->cus_login_type == 2){
							$loginType = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
							} elseif($post->cus_login_type == 3) {
							$loginType = (Lang::has(Session::get('admin_lang_file').'.ADMIN_FACEBOOK')) ? trans(Session::get('admin_lang_file').'.ADMIN_FACEBOOK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_FACEBOOK');
							} elseif($post->cus_login_type == 4)  {
							$loginType = (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_GOOGLE');
						}
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="'.$post->cus_id.'">';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['cusName'] = $post->cus_fname;
						$nestedData['cusEmail'] = $post->cus_email;
						$nestedData['addedBy'] = $loginType;
						$nestedData['Edit'] = '<a href="'.url('edit-customer').'/'.base64_encode($post->cus_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
						$nestedData['Status'] = $statusLink;
						$nestedData['delete'] = '<a href= "javascript:individual_change_status(\''.$post->cus_id.'\',2);" title="'.$click_to_delete.'" class="tooltip-demo" id="statusLink_'.$post->cus_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
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
		public function add_customer()
		{
    		if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CUSTOMER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_CUSTOMER');
				$id = ''; 
				$get_customer_details = get_all_details('gr_customer','cus_status',10,'desc','cus_id');
				return view('Admin.add_customer')->with('pagetitle',$page_title)->with('all_details',$get_customer_details)->with('id',$id);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function edit_customer($id)
		{
			$id = base64_decode($id);
			$where = ['cus_id' => $id];
			$get_customer_details = get_details('gr_customer',$where);
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_CUSTOMER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EDIT_CUSTOMER');
			$get_allcustomer_details = get_all_details('gr_customer','cus_status',10,'desc','cus_id');
			return view('Admin.add_customer')->with('pagetitle',$page_title)->with('all_details',$get_allcustomer_details)->with('customer_detail',$get_customer_details)->with('id',$id);
		}
		
		public function add_update_customer(Request $request)
		{  
			if(Session::has('admin_id') == 1)
			{ 
				$cus_name_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL');
				$cus_email_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTER_EMAIL');
				$cus_valid_email_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL');
				$cuspass_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_PASS');
				$cus_unique_email_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
				$cus_phone_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTER_PHONE');
				$cus_phone_valid_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EN_VALID_PH');
				//$cus_altphone_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ALTPHONE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ALTPHONE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_ALTPHONE_VAL');
				$cus_addr_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_ADDR_VAL');
				$cus_pass_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_PASS_VAL');
				$cus_img_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_VAL');
				$cus_img_dimen_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_dimen_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_dimen_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_dimen_VAL');
				$cus_id = Input::get('cus_id');
				
				if($cus_id != '')    //update
				{
					$validator = Validator::make($request->all(), [
					
					'cus_name' => 'required',
					
					//'cus_email' => 'required|email|unique:gr_customer,cus_email,'.$cus_id.',cus_id',
                    'cus_pass'  => 'required',
					//'cus_phone' => 'required|only_cnty_code',
					'cus_email'  => [
						'required', 
						Rule::unique('gr_customer')->where(function ($query) use ($request) {
						return $query->where('gr_customer.cus_id', '<>', Input::get('cus_id'))->where('gr_customer.cus_status','<>','2');
						}),
						],
					'cus_phone'  => [
						'required', 
						'only_cnty_code',
						Rule::unique('gr_customer','cus_phone1')->where(function ($query) use ($request) {
						return $query->where('gr_customer.cus_id', '<>', Input::get('cus_id'))->where('gr_customer.cus_status','<>','2');
						}),
					],
					
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
					'cus_pass.required'=>$cuspass_err_msg,
					'cus_phone.required'=>$cus_phone_req_err_msg,
					'cus_phone.only_cnty_code'=>$cus_phone_req_err_msg,
					//'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
					'cus_address.required'=>$cus_addr_req_err_msg,
					'cus_pass.required'=>$cus_pass_req_err_msg,
					'cus_image.required'=>$cus_img_req_err_msg,
					'cus_image.dimensions'=>$cus_img_dimen_err_msg,
                    ]);
				}
				else    //add
				{
					$validator = Validator::make($request->all(), [
					
					'cus_name' => 'required',
					'cus_email'  => [
						'required', 
						Rule::unique('gr_customer')->where(function ($query) use ($request) {
						return $query->where('gr_customer.cus_status','<>','2');
						}),
						],
					'cus_phone'  => [
						'required', 
						'only_cnty_code',
						Rule::unique('gr_customer','cus_phone1')->where(function ($query) use ($request) {
						return $query->where('gr_customer.cus_status','<>','2');
						}),
					],
					
					//'cus_email' => 'required|email|unique:gr_customer',
                    //'cus_pass'  => 'required',
					//'cus_phone' => 'required',
					
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
					// 'cus_pass.unique'=>$cuspass_err_msg,
					'cus_phone.required'=>$cus_phone_req_err_msg,
					// 'cus_alt_phone.required'=>$cus_altphone_req_err_msg,
					'cus_address.required'=>$cus_addr_req_err_msg,
					// 'cus_pass.required'=>$cus_pass_req_err_msg,
					'cus_image.required'=>$cus_img_req_err_msg,
					'cus_image.dimensions'=>$cus_img_dimen_err_msg,
                    ]);
				}
				if ($validator->fails()) {
		            return redirect('add-customer')
					->withErrors($validator)
					->withInput();
					}else{
					
					//echo 'inside'; exit;
					$cus_name = Input::get('cus_name');
					$cus_email = Input::get('cus_email');	
					$cus_phone = Input::get('cus_phone');
					$cus_alt_phone = Input::get('cus_alt_phone');
					$cus_address = Input::get('cus_address');
					$cus_lat = Input::get('cus_lat');
					$cus_long = Input::get('cus_long');
					$cus_pass = $this->random_password(6);                        
					$login_type = '2';
					if($cus_id != '')
					{
						$login_type = $request->login_type;
						$cus_pass   = Input::get('cus_pass');
					}
						if($request->hasFile('cus_image')) 
						{
						$cus_image = 'customer'.time().'.'.request()->cus_image->getClientOriginalExtension();
						$destinationPath = public_path('images/customer');
						$customer = Image::make(request()->cus_image->getRealPath())->resize(300, 300);
						$customer->save($destinationPath.'/'.$cus_image,80);
						
						$insertArr = array(
						'cus_fname' => $cus_name,
						'cus_email' => $cus_email,
						'cus_phone1' => $cus_phone,
						'cus_phone2' => $cus_alt_phone,
						'cus_address' => $cus_address,
						'cus_latitude' => $cus_lat,
						'cus_longitude' => $cus_long,
						'cus_password' => md5($cus_pass),
						'cus_decrypt_password' => $cus_pass,
						'cus_image' => $cus_image,
						'cus_login_type' => $login_type,
						'cus_status' => '1',
						'cus_created_date' => date('Y-m-d')
						
						);  
                        }
                        else
                        {
						$insertArr = array(
						'cus_fname' => $cus_name,
						'cus_email' => $cus_email,
						'cus_phone1' => $cus_phone,
						'cus_phone2' => $cus_alt_phone,
						'cus_address' => $cus_address,
						'cus_latitude' => $cus_lat,
						'cus_longitude' => $cus_long,
						'cus_password' => md5($cus_pass),
						'cus_decrypt_password' => $cus_pass,
						'cus_login_type' => $login_type,
						'cus_status' => '1',
						'cus_created_date' => date('Y-m-d')
						
						);
					}
					
					$old_pass = Input::get('old_password');
					$new_pass = Input::get('cus_pass');
					
					if($cus_id != '')
					{   
						$old_mail = Input::get('old_email');
						if(($old_mail != $cus_email) || ($old_pass != $new_pass))
						{
							$send_mail_data = array('name' => Input::get('cus_name'),
													'password' => $cus_pass,
													'email' => Input::get('cus_email'),
													'url'=>'',
													'andr_link' => Session::get('CUS_ANDR_LINK'),
													'ios_link' => Session::get('CUS_IOS_LINK')
													);
							Mail::send('email.username_password_email', $send_mail_data, function($message)
							{
								$email               = Input::get('cus_email');
								$name                = Input::get('cus_name');
								$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
								$message->to($email, $name)->subject($subject);
							});
						}
						$update = updatevalues('gr_customer',$insertArr,['cus_id' =>$cus_id]);
						$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					}
					else
					{
						//MAIL FUNCTION
						$send_mail_data = array('name' => Input::get('cus_name'),
												'password' => $cus_pass,
												'email' => Input::get('cus_email'),
												'url'=>'',
												'andr_link' => Session::get('CUS_ANDR_LINK'),
												'ios_link' => Session::get('CUS_IOS_LINK')
												);
						Mail::send('email.username_password_email', $send_mail_data, function($message)
						{
							$email               = Input::get('cus_email');
							$name                = Input::get('cus_name');
							$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
							$message->to($email, $name)->subject($subject);
						});
						/* EOF MAIL FUNCTION */
						$insert = insertvalues('gr_customer',$insertArr);
						$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					}
					return Redirect::to('manage-customer')->withErrors(['success'=>$msg])->withInput();
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function change_customer_status($id,$status)
		{	//echo $status; echo $id; exit;
			$update = ['cus_status' => $status];
			$where = ['cus_id' => $id];
			$a = updatevalues('gr_customer',$update,$where);
			/* send mail to customer */
			$related_details = get_related_details('gr_customer',['cus_id' => $id],['cus_email','cus_image','cus_fname'],'individual');
			if(!empty($related_details))
			{
				$send_mail_data = array('name' => $related_details->cus_fname,'status' => $status);
				$mail = $related_details->cus_email;
				Mail::send('email.customer_status_mail', $send_mail_data, function($message) use($mail)
		        {
		            $subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUS_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUS_STATUS_CH') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUS_STATUS_CH');
		            $message->to($mail)->subject($subject);
				});
		        /* delete customer image */
		        if($status == 2)
		        {
		        	if(File::exists(public_path('images/customer/').$related_details->cus_image))
		            {
		                File::delete(public_path('images/customer/').$related_details->cus_image);
					}
				}
			}
	        /* send mail ends */
			//echo $a; exit;
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				
			}
			elseif($status == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				
			}
			elseif($status == 0)   //block
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				
			}
			return Redirect::to('manage-customer');
		}
		
		/** multiple block/unblock  Customer**/
		/** multiple block/unblock  Customer**/
		public function multi_customer_block()
		{
			$update = ['cus_status' => Input::get('status')];
			$val = Input::get('val');
			$status = Input::get('status');
			//return count($val); exit;
			for($i=0; $i< count($val); $i++)
			{
				$where = ['cus_id' => $val[$i]];
				
				$a = updatevalues('gr_customer',$update,$where);
				/* send mail to customer */
				$related_details = get_related_details('gr_customer',['cus_id' => $val[$i]],['cus_email','cus_image','cus_fname'],'individual');
				if(!empty($related_details))
				{
					/*$send_mail_data = array('name' => $related_details->cus_fname,'status' => $status);
						$mail = $related_details->cus_email;
						Mail::send('email.customer_status_mail', $send_mail_data, function($message) use($mail)
						{
						$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUS_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUS_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUS_STATUS_CH');
						$message->to($mail)->subject($subject);
					});*/
					/* delete customer image */
					if($status == 2)
					{
						if(File::exists(public_path('images/customer/').$related_details->cus_image))
						{
							File::delete(public_path('images/customer/').$related_details->cus_image);
						}
					}
				}
				/* send mail ends */
			}
			//echo Input::get('status'); exit;
			if(Input::get('status') == 1) //Active
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
			if(Input::get('status') == 2) //Delete
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
			elseif(Input::get('status') == 0)   //block
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
		}
		public function export_customer($type)
		{
			$data = DB::table('gr_customer')->select('*')->where('cus_status','=','1')->get()->toarray();
			
			return Excel::create('Customer lists',function ($excel) use ($data)
    		{
    			$excel->sheet('customer_list', function ($sheet) use ($data)
    			{   
    				$sheet->row(1, ['Col 1', 'Col 2', 'Col 3']); // etc etc
					$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });	 
                    $sheet->cell('A1', function($cell) {$cell->setValue('S.No');  });   
                    $sheet->cell('B1', function($cell) {$cell->setValue('Customer Name');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Customer Email');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Customer Address');}); 
                    $sheet->cell('E1', function($cell) {$cell->setValue('Mobile Number');}); 
                    $sheet->cell('F1', function($cell) {$cell->setValue('Alternate Mobile Number');});
					$sheet->cell('G1', function($cell) {$cell->setValue('Created By');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Created At');});   
    				$sheet->setFontFamily('Comic Sans MS');
    				
					//$sheet->row(1, function($row) { $row->setBackground('#000000'); });
					$j=2;
					$k=1;
					foreach ($data as $key => $value) {
						$addedBy = "Normal";
						if($value->cus_login_type == 2)
						{
							$addedBy = "Admin";
						}
						elseif($value->cus_login_type == 3)
						{
							$addedBy = "Facebook";
						}
						elseif($value->cus_login_type == 4)
						{
							$addedBy = "Google";
						}
						$i= $key+2;
						$sheet->cell('A'.$j, $k); //print serial no
						$sheet->cell('B'.$j, $value->cus_fname);
						$sheet->cell('C'.$j, $value->cus_email); 
						$sheet->cell('D'.$j, $value->cus_address); 
						$sheet->cell('E'.$j, $value->cus_phone1);
						$sheet->cell('F'.$j, $value->cus_phone2); 
						$sheet->cell('G'.$j, $addedBy); 
						$sheet->cell('H'.$j, $value->cus_created_date); 
						$j++;
						$k++;
					}
				});
			})->download($type);
			
		}
		
	}
