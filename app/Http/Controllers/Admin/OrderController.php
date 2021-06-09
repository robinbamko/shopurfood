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
	
	class OrderController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->setAdminLanguage();
		}
		public function manage_order()
		{
    		if(Session::has('admin_id') == 1)
			{
				
				$where = [];
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_ORDER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_ORDER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_ORDER');
				$get_cms_details = get_all_details('gr_order','',10,'desc','order_id');
				return view('Admin.manage_order')->with('pagetitle',$page_title)->with('all_details',$get_cms_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
	}
