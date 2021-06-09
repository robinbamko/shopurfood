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
	
	use Image;
	
	use File;
	
	class DeliverymanagerController extends Controller
	{
		
		public function __construct()
		{	
			parent::__construct();
			$this->setAdminLanguage();
			
		}
		
		/** add delivery manager  **/
		public function add_delivery_manager()
		{ 
			if(Session::has('admin_id') == 1)
			{         
                $page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DELIVERY_MNGR')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DELIVERY_MNGR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_DELIVERY_MNGR');
                $url = 'add-manager-submit';
				$array_name = array();
				foreach(DB::getSchemaBuilder()->getColumnListing('gr_delivery_manager') as $res)
				{
					$array_name[$res]='';
				}
				$object = (object) $array_name; // return all value as empty.
				return view('Admin.delivery_manager.add_delivery_manager')->with(['pagetitle'=>$page_title,'details' => $object,'url' => $url]);
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** add delivery manager **/
		
		public function add_manager_submit(Request $request)
		{ 
			if(Session::has('admin_id') == 1)
			{         
				$cus_name_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL');
				$cus_email_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTER_EMAIL');
				$cus_valid_email_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL');
				$cus_uniq_email_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL');
				$cus_phone_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTER_PHONE');
				$cus_pass_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_PASS_VAL');         
				$cus_img_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_VAL');   
                $validator = Validator::make($request->all(), [
				
				'name' => 'required',
				'dm_email' => 'required|email|unique:gr_delivery_manager',
				
				'phone' => 'required|only_cnty_code',
				
				'password' => 'required',
				
				'photo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_height = 500,max_width = 500'
				],[
				'name.required'=>$cus_name_req_err_msg,
				'dm_email.required'=>$cus_email_req_err_msg,
				'dm_email.email'=>$cus_valid_email_err_msg,
				'dm_email.unique'=>$cus_uniq_email_err_msg,
				'phone.required'=>$cus_phone_req_err_msg,
				'phone.only_cnty_code'=>$cus_phone_req_err_msg,
				'password.required'=>$cus_pass_req_err_msg,
				'photo.image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_height = 500,max_width = 500'=>$cus_img_req_err_msg,
				]);		
				if ($validator->fails()) { 
		            return redirect('add-delivery-manager')
					->withErrors($validator)
					->withInput();
				}  
		        else{
                    //echo "success"; exit;
		        	$cus_image = '';
		        	if($request->hasFile('photo'))
					{
						$cus_image = 'manager'.time().'.'.request()->photo->getClientOriginalExtension();
						$destinationPath = public_path('images/delivery_manager');
						$customer = Image::make(request()->photo->getRealPath())->resize(300, 300);
						$customer->save($destinationPath.'/'.$cus_image,80);
					}
					$insertArr = array(
					'dm_name'		 => Input::get('name'),
					'dm_email' 		 => Input::get('dm_email'),
					'dm_phone' 		 => Input::get('phone'),
					'dm_password'	 => md5(Input::get('password')),
					'dm_real_password'=>Input::get('password'),
					'dm_imge' 		 => $cus_image,
					);
					
					$insert = insertvalues('gr_delivery_manager',$insertArr);
					
					/*MAIL FUNCTION */
					$send_mail_data = array('name' => Input::get('name'),
											'password' => Input::get('password'),
											'email' => Input::get('dm_email'),
											'url'=>'delivery-manager-login',
											'andr_link' => 'javascript:;',
											'ios_link' => 'javascript:;'
											);
					Mail::send('email.username_password_email', $send_mail_data, function($message)
					{
						$email               = Input::get('dm_email');
						$name                = Input::get('name');
						$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
						$message->to($email, $name)->subject($subject);
					});
					/* EOF MAIL FUNCTION */ 
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					Session::flash('message',$msg);
					return Redirect::to('manage-delivery-manager');
				}            	
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** manage delivery manager **/
		public function manage_delivery_manager()
		{
			if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_DELIVERY_MNGR')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_DELIVERY_MNGR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MNGE_DELIVERY_MNGR');
				$get_details = array();//get_all_details('gr_delivery_manager','dm_status',10,'desc','dm_id');
				return view('Admin.delivery_manager.manage_delivery_manager')->with('pagetitle',$page_title)->with('all_details',$get_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function ajax_delivery_manager(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array( 
				0 =>'dm_id', 
				1 =>'dm_id', 
				2 =>'dm_name', 
				3=> 'dm_email',
				4=> 'dm_id',
				5=> 'dm_status',
				6=> 'dm_id'
				);
				/*To get Total count */
				$totalData = DB::table('gr_delivery_manager')
				->select('dm_id')
				->where('dm_status','<>','2')
				->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$name_search = trim($request->name_search); 
				$email_search = trim($request->email_search); 
				$publish_search = trim($request->publish_search); 
				if($name_search=='' && $email_search=='' && $publish_search=='')
				{    
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_delivery_manager')
					->select('dm_id',
					'dm_name',
					'dm_status',
					'dm_email'
					)
					->where('dm_status','<>','2')
					->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					
					$sql = DB::table('gr_delivery_manager')
					->select('dm_id',
					'dm_name',
					'dm_email',
					'dm_status'
					)
					->where('dm_status','<>','2');
					if($name_search != '')
					{
						$q = $sql->whereRaw("dm_name like '%".$name_search."%'"); 
					}
					if($email_search != '')
					{
						$q = $sql->whereRaw("dm_email like '%".$email_search."%'"); 
					}
					if($publish_search != '')
					{
						$q = $sql->where('dm_status',$publish_search); 
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
						if($post->dm_status == 1)
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->dm_id.'\',0);" id="statusLink_'.$post->dm_id.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
						}
						else
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->dm_id.'\',1);" id="statusLink_'.$post->dm_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'" ></i></a>';
						}
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="'.$post->dm_id.'">';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['name'] = $post->dm_name;
						$nestedData['email'] = $post->dm_email;
						$nestedData['Edit'] = '<a href="'.url('edit-manager').'/'.base64_encode($post->dm_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
						$nestedData['Status'] = $statusLink;
						$nestedData['delete'] = '<a href= "javascript:individual_change_status(\''.$post->dm_id.'\',2);" title="'.$click_to_delete.'" class="tooltip-demo" id="statusLink_'.$post->dm_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
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
		public function edit_delivery_manager($id)
		{
			$id = base64_decode($id);
			$where = ['dm_id' => $id];
			$url = 'update-manager';
			$get_details = get_details('gr_delivery_manager',$where);
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_DELIVERY_MNGR')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_DELIVERY_MNGR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EDIT_DELIVERY_MNGR');
			return view('Admin.delivery_manager.add_delivery_manager')->with('pagetitle',$page_title)->with('details',$get_details)->with('id',$id)->with('url',$url);
		}
		public function update_delivery_manager(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{         
				$cus_name_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL');
				$cus_valid_email_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTR_MAIL');
				$cus_phone_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTER_PHONE');
				$cus_phone_valid_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EN_VALID_PH');
				$cus_pass_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_PASS_VAL');         
				$cus_img_req_err_msg=(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_VAL');   
                $validator = Validator::make($request->all(), [
				
				'name' => 'required',
				
				'phone' => 'required|only_cnty_code',
				
				'dm_email' => 'Sometimes|email|unique:gr_delivery_manager,dm_email,'.Input::get('id').',dm_id',
				
				'photo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_height = 500,max_width = 500'
				],[
				'name.required'=>$cus_name_req_err_msg,
				'phone.required'=>$cus_phone_req_err_msg,
				'phone.only_cnty_code'=>$cus_phone_valid_err_msg,
				'dm_email.email'=>$cus_valid_email_err_msg,
				'password.required'=>$cus_pass_req_err_msg,
				'photo.image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_height = 500,max_width = 500'=>$cus_img_req_err_msg,
				]);     
				if ($validator->fails()) { 
                    return redirect('add-delivery-manager')
					->withErrors($validator)
					->withInput();
				}  
                else{  //print_r($request->all()); exit;
                    $cus_image = Input::get('old_photo');
                    $old_mail  = Input::get('old_email');
                    $password  = Input::get('password');
                    $old_password  = Input::get('old_password');
                    if($request->hasFile('photo'))
					{   
						$old_image = Input::get('old_photo');
						$image_path = public_path('images/delivery_manager/').$old_image;  // Value is not URL but directory file path
						if(File::exists($image_path)) 
						{
							$a =   File::delete($image_path);
							
						}
						
						$cus_image = 'manager'.time().'.'.request()->photo->getClientOriginalExtension();
						$destinationPath = public_path('images/delivery_manager');
						$customer = Image::make(request()->photo->getRealPath())->resize(300, 300);
						$customer->save($destinationPath.'/'.$cus_image,80);
					}
					$insertArr = array(
					'dm_name'        => Input::get('name'),
					'dm_phone'       => Input::get('phone'),
					'dm_email'       => Input::get('dm_email'),
					'dm_imge'        => $cus_image,
					'dm_password'	 => md5($password),
					'dm_real_password'=>$password,
					);
					
					$insert = updatevalues('gr_delivery_manager',$insertArr, ['dm_id' => Input::get('id')]);
					if(($old_mail != Input::get('dm_email')) || ($old_password != $password))
					{       
						
						/*MAIL FUNCTION */
						$send_mail_data = array('name' 		=> Input::get('name'),
												'password' 	=>  $password,
												'email' 	=> Input::get('dm_email'),
												'url'		=>'delivery-manager-login',
												'andr_link' => 'javascript:;',
												'ios_link' => 'javascript:;'
												);
						Mail::send('email.username_password_email', $send_mail_data, function($message)
						{
							$email               = Input::get('dm_email');
							$name                = Input::get('name');
							$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
							$message->to($email, $name)->subject($subject);
						});
						/* EOF MAIL FUNCTION */
						
					}
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					Session::flash('message',$msg);
					return Redirect::to('manage-delivery-manager');
				}               
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		/*public function manager_status($id,$status)
			{	//echo $status; echo $id; exit;
			$update = ['dm_status' => $status];
			$where = ['dm_id' => $id];
			$a = updatevalues('gr_delivery_manager',$update,$where);
			//echo $a; exit;
			$related_details = get_related_details('gr_delivery_manager',['dm_id' => $id],['dm_email','dm_imge','dm_name'],'individual');
			if(!empty($related_details))
			{
			$send_mail_data = array('name' => $related_details->dm_name,'status' => $status);
			$mail = $related_details->dm_email;
			Mail::send('email.deliveryManager_status_mail', $send_mail_data, function($message) use($mail)
			{
			$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DM_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_DM_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DM_STATUS_CH');
			$message->to($mail)->subject($subject);
			});
			if($status == 2)
			{
			if(File::exists(public_path('images/delivery_manager/').$related_details->dm_imge))
			{
			File::delete(public_path('images/delivery_manager/').$related_details->dm_imge);
			}
			}
			}
			if($status == 1) //Active
			{	
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('manage-delivery-manager');
			}
			if($status == 2) //Delete
			{	
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('manage-delivery-manager');
			}
			else   //block
			{	
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('manage-delivery-manager');
			}
			}
		*/
		/** multiple block/unblock  Customer**/
		public function multi_manager_block()
		{	
			$update = ['dm_status' => Input::get('status')];
			$val = Input::get('val');
			
			for($i=0; $i< count($val); $i++)
			{
				$where = ['dm_id' => $val[$i]];
				
				$a = updatevalues('gr_delivery_manager',$update,$where);
			}
			if(Input::get('status') == 1) //Active
			{	
				
				$msg = '1~';
				$msg .=(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				echo $msg;
				
			}
			elseif(Input::get('status') == 2) //Delete
			{	
				
				$msg = '1~';
				$msg .=(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				echo $msg;
				
			}
			elseif(Input::get('status') == 0)   //block
			{	
				
				$msg = '1~';
				$msg .=(Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				echo $msg;
			}
			else
			{
				$msg = '0~';
				$msg .=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SUMTHNG_WRONG');
				echo $msg;
			}
		}
	}			