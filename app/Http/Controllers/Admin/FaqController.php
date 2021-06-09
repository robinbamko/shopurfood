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
	
	use Excel;
	
	
	class FaqController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->setAdminLanguage();
		}
		
		public function manage_faq()
		{
			if(Session::has('admin_id') == 1)
			{
				
				$where 		= [];
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_FAQ') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_FAQ');
				$get_cms_details = get_all_details('gr_faq','faq_status',10,'desc','id');
				return view('Admin.manage_faq')->with('pagetitle',$page_title)->with('all_details',$get_cms_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function add_faq()
		{
			if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_FAQ') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_FAQ');
				$id = ''; 
				$get_faq_details = get_all_details('gr_faq','faq_status',10,'desc','id');
				return view('Admin.add_edit_faq')->with('pagetitle',$page_title)->with('all_details',$get_faq_details)->with('id',$id);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function edit_faq($id)
		{
			$id 	= base64_decode($id);
			$where 	= ['id' => $id];
			$get_faq_details= get_details('gr_faq',$where);
			$page_title 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_FAQ') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EDIT_FAQ');
			$get_allfaq_details = get_all_details('gr_faq','faq_status',10,'desc','id');
			return view('Admin.add_edit_faq')->with('pagetitle',$page_title)->with('all_details',$get_allfaq_details)->with('faq_detail',$get_faq_details)->with('id',$id);
		}
		public function add_update_faq(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{ 
				$faq_name_en_err_msg		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FAQ') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PLEASE_ENTER_FAQ');
				$faq_name_en_unique_err_msg	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_UNIQUE_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_UNIQUE_FAQ') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PLEASE_ENTER_UNIQUE_FAQ');
				$faq_ans_en_err_msg			= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ANSWER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ANSWER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PLEASE_ENTER_ANSWER');
				$faq_id = Input::get('faq_id');
				if($faq_id != '')
				{
					$validator = Validator::make($request->all(), ['faq_name_en'=> 'required|unique:gr_faq,faq_name_en,'.$faq_id.'',
					'faq_ans_en'	=> 'required',
					],[
					'faq_name_en.required'	=> $faq_name_en_err_msg,
					'faq_name_en.unique'	=> $faq_name_en_unique_err_msg,
					'faq_ans_en.required'	=> $faq_ans_en_err_msg,
					]
					);
				}
				else{
					$validator = Validator::make($request->all(),  ['faq_name_en' 	=> 'required|unique:gr_faq',
					'faq_ans_en'	=> 'required'
					],
					[
					'faq_name_en.required'	=> $faq_name_en_err_msg,
					'faq_name_en.unique'	=> $faq_name_en_unique_err_msg,
					'faq_ans_en.required'	=> $faq_ans_en_err_msg
					]
					);
				}
				
				if ($validator->fails()) {
		            return redirect('add-faq')->withErrors($validator)->withInput();
					}else{
					$faq_name_en= Input::get('faq_name_en');
					$faq_ans_en	= Input::get('faq_ans_en');	
					
					$insertArr = array(	'faq_name_en' 	=> $faq_name_en,
					'faq_ans_en'	=> $faq_ans_en,
					'faq_status' 	=> 1
					);
					
                    if(count($this->get_Adminactive_language) > 0)
                    {
                        foreach($this->get_Adminactive_language as $Lang)
                        {
                            $validatorfaq = Validator::make($request->all(), [
							'faq_name_'.$Lang->lang_code => 'required',
							'faq_ans_'.$Lang->lang_code => 'required'
                            ]);
                            if($validatorfaq->fails()){
                                return redirect('add-faq')->withErrors($validatorfaq)->withInput();
								}else {
                                $insertArr['faq_name_' . $Lang->lang_code] = Input::get('faq_name_' . $Lang->lang_code);
                                $insertArr['faq_ans_' . $Lang->lang_code] = Input::get('faq_ans_' . $Lang->lang_code);
							}
						}
					}
					
					
					if($faq_id != '')
					{
						$update = updatevalues('gr_faq',$insertArr,['id' =>$faq_id]);
						$msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					}
					else
					{
						$insert = insertvalues('gr_faq',$insertArr);
						$msg 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					}
					return Redirect::to('manage-faq')->withErrors(['success'=>$msg])->withInput();
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function change_faq_status($id,$status)
		{	
			$update = ['faq_status' => $status];
			$where 	= ['id' => $id];
			$a = updatevalues('gr_faq',$update,$where);
			if($status == 1) //Active
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-faq')->withErrors(['success'=>$msg])->withInput();
			}
			if($status == 2) //Delete
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-faq')->withErrors(['success'=>$msg])->withInput();
			}
			else   //block
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-faq')->withErrors(['success'=>$msg])->withInput();
			}
		}
	}
