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
	
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use App\Admin;
	
	use App\Settings;
	
	
	class CmsController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			$this->setAdminLanguage();
		}
		
		public function manage_cms()
		{
			if(Session::has('admin_id') == 1)
			{
				$where 			= [];
				$page_title 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_CMS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_CMS');
				$get_cms_details= get_all_details('gr_cms','page_status',10,'desc','id','','','');
				
				return view('Admin.manage_cms')->with('pagetitle',$page_title)->with('all_details',$get_cms_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function add_cms()
		{
			if(Session::has('admin_id') == 1)
			{
				$page_title 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CMS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_CMS'); $id = ''; 
				$get_cms_details= get_all_details('gr_cms','page_status',10,'desc','id');
				return view('Admin.add_edit_cms')->with('pagetitle',$page_title)->with('all_details',$get_cms_details)->with('id',$id);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function edit_cms($id)
		{
			$id 	= base64_decode($id);
			$where 	= ['id' => $id];
			$get_cms_details= get_details('gr_cms',$where);
			$page_title 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_CMS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EDIT_CMS');
			$get_allcms_details = get_all_details('gr_cms','page_status',10,'desc','id');
			return view('Admin.add_edit_cms')->with('pagetitle',$page_title)->with('all_details',$get_allcms_details)->with('cms_detail',$get_cms_details)->with('id',$id);
		}
		public function add_update_cms(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{ 
				$page_title_err_msg 		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAGE_TITLE_VAL');
				$page_title_unique_err_msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE_UNIQUE_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAGE_TITLE_UNIQUE_VAL');
				$page_desc_err_msg 			= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION_VAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAGE_DESCRIPTION_VAL');
				$page_id = Input::get('page_id');
				if($page_id != '')
				{
					$validator = Validator::make($request->all(), [ 'page_title_en' => 'required|unique:gr_cms,page_title_en,'.$page_id.'',
					'description' 	=> 'required'
					],[
					'page_title_en.required'=> $page_title_err_msg,
					'page_title_en.unique' 	=> $page_title_unique_err_msg,
					'description.required' 	=> $page_desc_err_msg,
					]
					);
				}
				else{
					$validator = Validator::make($request->all(), [	'page_title_en' => 'required|unique:gr_cms',
					'description' => 'required'
					],[
					'page_title_en.required'=> $page_title_err_msg,
					'page_title_en.unique' 	=> $page_title_unique_err_msg,
					'description.required' 	=> $page_desc_err_msg,
					]
					);
				}
				
				if ($validator->fails()) {
		            return redirect('add-cms')->withErrors($validator)->withInput();
					}else{
					$page_title  = Input::get('page_title_en');
					$description = Input::get('description');	
					
					$insertArr = array( 'page_title_en'	=> $page_title,
					'description_en'=> $description,
					'page_status' 	=> 1,
					'page_name' 	=> $page_title
					);
					
                    if(count($this->get_Adminactive_language) > 0)
                    {
                        foreach($this->get_Adminactive_language as $Lang)
                        {
                            $validatorcms = Validator::make($request->all(), [
							'page_title_'.$Lang->lang_code => 'required',
							'description_'.$Lang->lang_code => 'required'
                            ]);
                            if($validatorcms->fails()){
                                return redirect('add-cms')->withErrors($validatorcms)->withInput();
								}else {
                                $insertArr['page_title_' . $Lang->lang_code] = Input::get('page_title_' . $Lang->lang_code);
                                $insertArr['description_' . $Lang->lang_code] = Input::get('description_' . $Lang->lang_code);
							}
						}
					}
					if($page_id != '')
					{
						$update = updatevalues('gr_cms',$insertArr,['id' =>$page_id]);
						$msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					}
					else
					{
						$insert = insertvalues('gr_cms',$insertArr);
						$msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					}
					return Redirect::to('manage-cms')->withErrors(['success'=>$msg])->withInput();
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function change_cms_status($id,$status)
		{	
			$update= ['page_status' => $status];
			$where = ['id' => $id];
			$a = updatevalues('gr_cms',$update,$where);
			if($status == 1) //Active
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-cms')->withErrors(['success'=>$msg])->withInput();
			}
			if($status == 2) //Delete
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-cms')->withErrors(['success'=>$msg])->withInput();
			}
			else   //block
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-cms')->withErrors(['success'=>$msg])->withInput();
			}
		}
	}
