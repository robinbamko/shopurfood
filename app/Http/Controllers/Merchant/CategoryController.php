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
	
	use Excel;
	
	use Response;
	use File;
	
	class CategoryController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
			
		}
		
		
		/** product main category **/
		
		public function manage_product_category()
		{
			if(Session::has('merchantid') == 1)
			{
				$where = ['pro_mc_type' => 2]; //1 for store
				$get_category_details = array();//get_all_details('gr_proitem_maincategory','pro_mc_status',10,'desc','pro_mc_id',$where);
				
				$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_MNGE_PRO_CATE')) ? trans(Session::get('mer_lang_file').'.MER_MNGE_PRO_CATE') : trans($this->MER_OUR_LANGUAGE.'.MER_MNGE_PRO_CATE');
				
				return view('sitemerchant.category.manage_product_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details);
				exit;
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		public function ajax_product_category(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{
				$columns = array(
                0 =>'pro_mc_id',
                1 => 'pro_mc_name',
                2=> 'pro_mc_status',
                3=> 'pro_mc_type',
                4=> 'pro_added_by'
				);
				/*To get Total count */
				$totalData = DB::table('gr_proitem_maincategory')->select('pro_mc_id')->where('pro_mc_status','<>','2')->count();
				$totalFiltered = $totalData;
				//echo $totalData; exit;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				$search = trim($request->catName_search);
				$addedBySearch = trim($request->addedBy_search);
				if($search == '' && $addedBySearch =='')
				{
					$posts = DB::table('gr_proitem_maincategory')->where('pro_mc_type','2')->where('pro_mc_status','!=','2')->orderBy($order,$dir)->skip($start)->take($limit)->get();
				}
				else {
					$q = array();
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_proitem_maincategory')->where('pro_mc_type','2')->where('pro_mc_status','!=','2');
					
					if($search != '')
					{
						/*$q = $sql->whereRaw("pro_mc_name LIKE '%".$search."%'");*/
						$q = $sql->whereRaw("pro_mc_name LIKE ?", ['%'.$search.'%']);
					}
					if($addedBySearch == '0')
					{
						$q= $sql->where('pro_added_by','=',$addedBySearch);
					}
					if($addedBySearch == '1')
					{
						$q= $sql->where('pro_added_by','>','0');
					}
					
					//$posts =  $q->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
					$totalFiltered = $q->count();
					$posts = $q->orderBy($order,$dir)->skip($start)->take($limit)->get();
				}
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					
					$click_to_edit = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
					
					foreach ($posts as $post)
					{
						
						if($post->pro_added_by == 0)
						{
							$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
						}
						$manageSubText = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_SUBPRO_CATE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_SUBPRO_CATE');
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['CategoryName'] = $post->pro_mc_name;
						$nestedData['ManageSubCategory'] = '<a href="'.url('mer_manage-subproduct').'/'.base64_encode($post->pro_mc_id).'"><button type="button" class="btn btn-light">'.$manageSubText.'</button></a>';
						if($post->pro_added_by==Session::get('merchantid'))
						{
							$nestedData['Edit'] = '<a href="'.url('mer_edit_product_category').'/'.base64_encode($post->pro_mc_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
						}
						else
						{
							$nestedData['Edit'] = '';
						}
						$nestedData['AddedBy'] = $added_by;
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
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		/** add product category **/
		public function add_product_category(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{
				$err_msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME');
				$this->validate($request,['cate_name' => 'Required'],[ 'cate_name.required'    => $err_msg]);
				
				$name = 'pro_mc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$where = [$name => $old_name,'pro_mc_type' => '2'];
				$check = check_name_exists('gr_proitem_maincategory','pro_mc_status',$where);
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('mer_manage-product-category')->withErrors(['errors' =>$msg]);
					
				}
				$entry['pro_mc_name'] = ucfirst($old_name);
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_mc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry = array_merge(array('pro_mc_type' => 2,'pro_mc_status' => 1,'pro_added_by'=>Session::get('merchantid')),$entry); //2 for product
				$insert = insertvalues('gr_proitem_maincategory',$entry);
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer_manage-product-category');
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		public function edit_product_category($id)
		{
			if(Session::has('merchantid') == 1)
			{
				$id = base64_decode($id);
				$where = ['pro_mc_id' => $id];
				$get_cate_details = get_details('gr_proitem_maincategory',$where);
				$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_EDIT_PRO_CATE')) ? trans(Session::get('mer_lang_file').'.MER_EDIT_PRO_CATE') : trans($this->MER_OUR_LANGUAGE.'.MER_EDIT_PRO_CATE');
				$where = ['pro_mc_type' => 2]; //2 for product
				$get_category_details = get_all_details('gr_proitem_maincategory','pro_mc_status',10,'desc','pro_mc_id',$where);
				return view('sitemerchant.category.manage_product_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details)->with('cate_detail',$get_cate_details)->with('id',$id);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		/** update country **/
		public function update_product_category()
		{
			if(Session::has('merchantid') == 1)
			{
				$name = 'pro_mc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$id = Input::get('category_id');
				$check = DB::table('gr_proitem_maincategory')->select($name,'pro_mc_id')->where('pro_mc_id','<>',$id)->where('pro_mc_type' ,'=','2')->where('pro_mc_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['pro_mc_name'] = ucfirst($old_name);
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('mer_manage-product-category')->withErrors(['errors' =>$msg]);
					
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_mc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry['pro_added_by']=Session::get('merchantid');
				$update = updatevalues('gr_proitem_maincategory',$entry,['pro_mc_id' =>Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer_manage-product-category');
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
		/** manage product sub category **/
		public function manage_subproduct(Request $request,$id)
		{
			if(Session::has('merchantid') == 1)
			{
				$main_id = base64_decode($id);
				if($request->ajax())
				{
					$columns = array(
                    0 =>'pro_sc_id',
                    1 =>'pro_sc_name',
                    2=> 'pro_main_id',
                    3=> 'pro_sc_status',
                    4=> 'pro_added_by'
					);
					/* get total count */
					$totalData = Merchant::get_categoryLists_count('gr_proitem_subcategory',['pro_sc_type' => '2','pro_main_id' => $main_id],'pro_sc_status');
					
					$totalFiltered = $totalData;
					/* eof get total count */
					$limit = $request->input('length');
					$start = $request->input('start');
					$order = $columns[$request->input('order.0.column')];
					$dir = $request->input('order.0.dir');
					$search = trim($request->catName_search);
					$addedBySearch = trim($request->addedBy_search);
					$catStatus_search = trim($request->catStatus_search);
					if($search == '' && $addedBySearch == '' && $catStatus_search == '')
					{
						$posts = DB::table('gr_proitem_subcategory')
                        ->where('pro_sc_status','<>','2')
                        ->where(['pro_main_id' => $main_id,'pro_sc_type' => '2'])
                        ->orderBy($order,$dir)->skip($start)->take($limit)
                        ->get();
					}
					else
					{
						$q = array();
						$sql = DB::table('gr_proitem_subcategory')->where('pro_sc_status','<>','2')
                        ->where(['pro_main_id' => $main_id,'pro_sc_type' => '2']);
						if($search != '')
						{
							$q = $sql->where('pro_sc_name','LIKE','%'.$search.'%');
						}
						if($catStatus_search != '')
						{
							$q = $sql->where(['pro_sc_status' => $catStatus_search]);
						}
						if($addedBySearch != '')
						{
							$q = $sql->where(['pro_added_by' => $addedBySearch]);
						}
						$totalFiltered = $q->count();
						$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
						$posts = $q->get();
						
					}
					$data = array();
					if(!empty($posts))
					{
						$snoCount = $start;
						$click_to_edit = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
						
						foreach($posts as $post)
						{
							
							if($post->pro_added_by == 0)
							{
								$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
							}
							else
							{
								$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
							}
							
							$nestedData['SNo'] = ++$snoCount;
							$nestedData['CategoryName'] = $post->pro_sc_name;
							if($post->pro_added_by == Session::get('merchantid'))
							{
								$nestedData['Edit'] = '<a href="'.url("mer_edit_sub_category").'/'.base64_encode($post->pro_sc_id).'/'.base64_encode($main_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
							}
							else
							{
								$nestedData['Edit'] = '';
							}
							$nestedData['AddedBy'] = $added_by;//$added_by;
							$data[] = $nestedData;
						}
					}
					$json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $data
					);
					return $json_data;
				}
				$categoryName = DB::table('gr_proitem_maincategory')->select('pro_mc_name')->where('pro_mc_id','=',$main_id)->first()->pro_mc_name;
				//$where = ['pro_sc_type' => 2,'pro_main_id' =>$main_id]; //2 for product
				$get_category_details = array();//get_all_details('gr_proitem_subcategory','pro_sc_status',10,'desc','pro_sc_id',$where,'gr_proitem_maincategory','pro_mc_id','pro_main_id');
				//print_r($get_country_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_SUB_CATE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_SUB_CATE_LIST') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SUB_CATE_LIST');
				$page_title =  $categoryName.'\'s '.$page_title;
				return view('sitemerchant.category.manage_sub_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details)->with('main_id',$main_id);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		
		
		/** add sub product category **/
		public function add_sub_category(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{
				$name = 'pro_sc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$main_id = Input::get('main_id');
				$cate_type = mysql_escape_special_chars(Input::get('cate_type'));
				$check = DB::table('gr_proitem_subcategory')->where('pro_main_id','=',$main_id)->where('pro_sc_type' ,'=',$cate_type)->where('pro_sc_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['pro_sc_name'] = ucfirst($old_name);
				
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::back()->withErrors(['errors' =>$msg]);
					
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_sc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				
				$entry = array_merge(array('pro_main_id' => $main_id,'pro_sc_type' => $cate_type,'pro_sc_status' => 1,'pro_added_by'=>Session::get('merchantid')),$entry);
				//print_r($entry); exit;
				$update = insertvalues('gr_proitem_subcategory',$entry);
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::back();
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		/** edit restaurant category **/
		/** edit restaurant category **/
		public function edit_sub_category($id,$main_id)
		{
			$id = base64_decode($id);
			$main_id = base64_decode($main_id);
			$where = ['pro_sc_id' => $id];
			$get_cate_details = get_details('gr_proitem_subcategory',$where);
			$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_EDIT_SUB_CATE')) ? trans(Session::get('mer_lang_file').'.MER_EDIT_SUB_CATE') : trans($this->MER_OUR_LANGUAGE.'.MER_EDIT_SUB_CATE');
			$where = ['pro_sc_type' => 2,'pro_main_id' =>$main_id]; //2 for product
			$get_category_details = get_all_details('gr_proitem_subcategory','pro_sc_status',10,'desc','pro_sc_id',$where,'gr_proitem_maincategory','pro_mc_id','pro_main_id');
			return view('sitemerchant.category.manage_sub_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details)->with('cate_detail',$get_cate_details)->with('id',$id)->with('main_id',$main_id);
		}
		/** update country **/
		public function update_sub_category()
		{
			if(Session::has('merchantid') == 1)
			
			{
				$name = 'pro_sc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$id = Input::get('category_id');
				$main_id = Input::get('main_id');
				$cate_type = mysql_escape_special_chars(Input::get('cate_type'));
				$check = DB::table('gr_proitem_subcategory')->select($name,'pro_sc_id')->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_proitem_subcategory.pro_main_id')->where('gr_proitem_maincategory.pro_mc_name','=',$old_name)->where('pro_sc_id','<>',$id)->where('pro_main_id','=',$main_id)->where('pro_sc_type' ,'=',$cate_type)->where('pro_sc_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['pro_sc_name'] = ucfirst($old_name);
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('mer_manage-subproduct/'.base64_encode($id))->withErrors(['errors' =>$msg]);
					
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_sc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry['pro_added_by']=Session::get('merchantid');
				$update = updatevalues('gr_proitem_subcategory',$entry,['pro_sc_id' =>Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				//return Redirect::back();
				return Redirect::to('mer_manage-subproduct/'.base64_encode($main_id));
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		
		
		
		/** download product category **/
		public function download_product_category($type,$sample = '')
		{
			
			if(Session::has('merchantid') == 1)
			{
				if($sample == 'sample_file')
				{
					$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName','pro_mc_status','pro_added_by')->where('pro_mc_type','=', '2')->where('pro_mc_status','!=','2')->limit(4)->get()->toarray();
				}
				else
				{
					$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName','pro_mc_status','pro_added_by')->where('pro_mc_type','=', '2')->where('pro_mc_status','!=','2')->get()->toarray();
				}
				
				// print_r($data); exit;
				return Excel::create('Product Category lists',function ($excel) use ($data)
				{
					$excel->sheet('category_list', function ($sheet) use ($data)
					{
						$sheet->cell('A1', function($cell) {
							
							$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SNO'));
						});
						$sheet->cell('B1', function($cell) {
							$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME'));
						});
						$sheet->cell('C1', function($cell) {
							$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_STATUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STATUS'));
						});
						$sheet->cell('D1', function($cell) {
							$cell->setValue((Lang::has(Session::get('mer_lang_file').'.MER_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.MER_ADDED_BY') : trans($this->MER_OUR_LANGUAGE.'.MER_ADDED_BY'));
						});
						
						$sheet->setFontFamily('Comic Sans MS');
						$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });
						
						foreach ($data as $key => $value) {
							$i= $key+2;
							$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ACT');
							$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
							if($value->pro_mc_status==0)
							{
								$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_INACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INACT');
							}
							
							if($value->pro_added_by==0)
							{
								$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
							}
							$sheet->cell('A'.$i, $i-1); //print serial no
							$sheet->cell('B'.$i, $value->CategoryName);
							$sheet->cell('C'.$i,$status);
							$sheet->cell('D'.$i, $added_by);
						}
					});
				})->download($type);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
		
		public function download_item_category($type,$sample = null)
		{
			if($sample == 'sample_file')
			{
				$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName','pro_mc_status','pro_added_by')->where('pro_mc_type','=', '1')->where('pro_mc_status','!=','2')->limit(4)->get()->toarray();
			}
			else
			{
				$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName','pro_mc_status','pro_added_by')->where('pro_mc_type','=', '1')->where('pro_mc_status','!=','2')->get()->toarray();
			}
			
			// print_r($data); exit;
			return Excel::create('Item Category lists',function ($excel) use ($data)
			{
				$excel->sheet('category_list', function ($sheet) use ($data)
				{
					$sheet->cell('A1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SNO'));
					});
					$sheet->cell('B1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME'));
					});
					$sheet->cell('C1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_STATUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STATUS'));
					});
					$sheet->cell('D1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.MER_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.MER_ADDED_BY') : trans($this->MER_OUR_LANGUAGE.'.MER_ADDED_BY'));
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });
					foreach ($data as $key => $value) {
						$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ACT');
						$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
						if($value->pro_mc_status==0)
						{
							$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_INACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INACT');
						}
						
						if($value->pro_added_by==0)
						{
							$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
						}
						$i= $key+2;
						$sheet->cell('A'.$i, $i-1); //print serial no
						$sheet->cell('B'.$i, $value->CategoryName);
						$sheet->cell('C'.$i,$status);
						$sheet->cell('D'.$i, $added_by);
					}
				});
			})->download($type);
			
		}
		
		
		/** download restaurant category **/
		public function download_sub_category($type,$title,$main,$sample = null)
		{
			if($sample == 'sample_file')
			{
				$data = DB::table('gr_proitem_subcategory')
                ->select('pro_sc_name as CategoryName','pro_mc_name as MainCat','gr_proitem_subcategory.pro_sc_status','gr_proitem_subcategory.pro_added_by')
                ->join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_proitem_subcategory.pro_main_id')
                ->where('pro_sc_type','=',$title)->where('pro_sc_status','!=','2')->where('pro_main_id','=',base64_decode($main))->limit(4)->get()->toarray();
			}
			else
			{
				$data = DB::table('gr_proitem_subcategory')
                ->select('pro_sc_name as CategoryName','pro_mc_name as MainCat','gr_proitem_subcategory.pro_sc_status','gr_proitem_subcategory.pro_added_by')
                ->join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_proitem_subcategory.pro_main_id')
                ->where('pro_sc_type','=',$title)->where('pro_sc_status','!=','2')->where('pro_main_id','=',base64_decode($main))->get()->toarray();
			}
			
			//print_r($data); exit;
			return Excel::create('Product Sub Category lists',function ($excel) use ($data)
			{
				$excel->sheet('category_list', function ($sheet) use ($data)
				{
					$sheet->cell('A1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SNO'));
					});
					$sheet->cell('B1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_MAIN_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_MAIN_CATE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MAIN_CATE_NAME'));
					});
					$sheet->cell('C1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_SUB_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_SUB_CATE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SUB_CATE_NAME'));
					});
					$sheet->cell('D1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_STATUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STATUS'));
					});
					$sheet->cell('E1', function($cell) {
						$cell->setValue((Lang::has(Session::get('mer_lang_file').'.MER_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.MER_ADDED_BY') : trans($this->MER_OUR_LANGUAGE.'.MER_ADDED_BY'));
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });
					foreach ($data as $key => $value) {
						$i= $key+2;
						$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ACT');
						$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
						if($value->pro_sc_status==0)
						{
							$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_INACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INACT');
						}
						
						if($value->pro_added_by==0)
						{
							$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
						}
						$sheet->cell('A'.$i, $i-1); //print serial no
						$sheet->cell('B'.$i, $value->MainCat);
						$sheet->cell('C'.$i, $value->CategoryName);
						$sheet->cell('D'.$i, $status);
						$sheet->cell('E'.$i, $added_by);
					}
				});
			})->download($type);
		}
		
		
		/** Item main category **/
		
		public function manage_item_category()
		{
			if(Session::has('merchantid') == 1)
			{
				$where = ['pro_mc_type' => 1]; //2 for product , 1 for main category
				$get_category_details = array();//get_all_details('gr_proitem_maincategory','pro_mc_status',10,'desc','pro_mc_id',$where);
				//print_r($get_category_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_ITEM_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_ITEM_CATE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_ITEM_CATE');
				
				return view('sitemerchant.category.manage_item_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		/* get product category list ajax */
		public function item_category_list_ajax(Request $request)
		{
			
			$columns = array(
            0 =>'pro_mc_id',
            1 => 'pro_mc_name',
            2=> 'pro_mc_status',
            3=> 'pro_mc_type',
            4=> 'pro_added_by'
			);
			/*To get Total count */
			$totalData = Merchant::get_categoryLists_count('gr_proitem_maincategory',['pro_mc_type' => '1'],'pro_mc_status');
			$totalFiltered = $totalData;
			//echo $totalData; exit;
			/*EOF get Total count */
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');
			$search = trim($request->catName_search);
			$addedBySearch = trim($request->addedBy_search);
			$catStatus_search = trim($request->catStatus_search);
			
			if($search == '' && $addedBySearch =='' && $catStatus_search == '')
			{
				$posts = DB::table('gr_proitem_maincategory')->where('pro_mc_type','1')->where('pro_mc_status','!=','2')->orderBy($order,$dir)->skip($start)->take($limit)->get();
			}
			else {
				$q = array();
				//DB::connection()->enableQueryLog();
				$sql = DB::table('gr_proitem_maincategory')->where('pro_mc_type','1')->where('pro_mc_status','!=','2');
				
				if($search != '')
				{
					$q = $sql->where('pro_mc_name','LIKE','%'.$search.'%');
				}
				if($addedBySearch == '0')
				{
					$q= $sql->where('pro_added_by','=',$addedBySearch);
				}
				if($addedBySearch == '1')
				{
					$q= $sql->where('pro_added_by','>','0');
				}
				
				if($catStatus_search != '')
				{
					$q = $sql->where('pro_mc_status','=',$catStatus_search);
				}
				
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				$totalFiltered = $q->count();
				$posts =  $q->orderBy($order,$dir)->skip($start)->take($limit)->get();
			}
			
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				foreach ($posts as $post)
				{
					
					if($post->pro_added_by == 0)
					{
						$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
					}
					else
					{
						$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
					}
					$subCateText = ((Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_SUBPRO_CATE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_SUBPRO_CATE'));
					$nestedData['SNo'] = ++$snoCount;
					$nestedData['CategoryName'] = $post->pro_mc_name;
					$nestedData['ManageSubCategory'] = '<a href="'.url('mer_manage-subitem').'/'.base64_encode($post->pro_mc_id).'"><button type="button" class="btn btn-light">'.$subCateText.'</button></a>';
					if($post->pro_added_by==Session::get('merchantid'))
					{
						
						$text = (Lang::has(Session::get('mer_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_EDIT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_EDIT');
						
						$nestedData['Edit'] = '<a href="'.url('mer_edit_item_category').'/'.base64_encode($post->pro_mc_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title=""></i></a>';
					}
					else
					{
						
						$nestedData['Edit'] = '<i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Cannot Edit" ></i>';
					}
					$nestedData['AddedBy'] = $added_by;
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
		/** add item category **/
		public function add_item_category(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{
				
				$err_msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME');
				$this->validate($request,['cate_name' => 'Required'],[ 'cate_name.required'    => $err_msg]);
				
				$name = 'pro_mc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$where = [$name => $old_name,'pro_mc_type' => '1'];
				$check = check_name_exists('gr_proitem_maincategory','pro_mc_status',$where);
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					return Redirect::to('mer_manage-item-category')->withErrors(['errors' =>$msg]);
					
				}
				$entry['pro_mc_name'] = ucfirst($old_name);
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_mc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry = array_merge(array('pro_mc_type' => 1,'pro_mc_status' => 1,'pro_added_by'=>Session::get('merchantid')),$entry); //2 for item
				$insert = insertvalues('gr_proitem_maincategory',$entry);
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer_manage-item-category');
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		public function edit_item_category($id)
		{
			$id = base64_decode($id);
			$where = ['pro_mc_id' => $id];
			$get_cate_details = get_details('gr_proitem_maincategory',$where);
			$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_EDIT_IT_CATE')) ? trans(Session::get('mer_lang_file').'.MER_EDIT_IT_CATE') : trans($this->MER_OUR_LANGUAGE.'.MER_EDIT_IT_CATE');
			$where = ['pro_mc_type' => 1]; //1 for item
			$get_category_details = get_all_details('gr_proitem_maincategory','pro_mc_status',10,'desc','pro_mc_id',$where);
			return view('sitemerchant.category.manage_item_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details)->with('cate_detail',$get_cate_details)->with('id',$id);
		}
		public function update_item_category()
		{
			if(Session::has('merchantid') == 1)
			{
				$name = 'pro_mc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$id = Input::get('category_id');
				$check = DB::table('gr_proitem_maincategory')->select($name,'pro_mc_id')->where('pro_mc_id','<>',$id)->where('pro_mc_type' ,'=','1')->where('pro_mc_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['pro_mc_name'] = $old_name;
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-item-category')->withErrors(['errors' =>$msg]);
					
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_mc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry['pro_added_by']=Session::get('merchantid');
				$update = updatevalues('gr_proitem_maincategory',$entry,['pro_mc_id' =>Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer_manage-item-category');
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
		/*SUB ITEM SECTION */
		public function manage_subitem(Request $request,$id)
		{
			if(Session::has('merchantid') == 1)
			{
				$main_id = base64_decode($id);
				//$where = ['pro_sc_type' => 1,'pro_main_id' =>$main_id]; //1 for item
				
				$get_category_details = array();//get_all_details('gr_proitem_subcategory','pro_sc_status',10,'desc','pro_sc_id',$where,'gr_proitem_maincategory','pro_mc_id','pro_main_id');
				//print_r($get_country_details); exit;
				if($request->ajax())
				{
					$columns = array(
                    0 =>'pro_sc_id',
                    1 =>'pro_sc_name',
                    2=> 'pro_main_id',
                    3=> 'pro_sc_status',
                    4=> 'pro_added_by'
					);
					/* get total count */
					$totalData = Merchant::get_categoryLists_count('gr_proitem_subcategory',['pro_sc_type' => '1','pro_main_id' => $main_id],'pro_sc_status');
					
					$totalFiltered = $totalData;
					/* eof get total count */
					$limit = $request->input('length');
					$start = $request->input('start');
					$order = $columns[$request->input('order.0.column')];
					$dir = $request->input('order.0.dir');
					$search = trim($request->catName_search);
					$addedBySearch = trim($request->addedBy_search);
					$catStatus_search = trim($request->catStatus_search);
					if($search == '' && $addedBySearch == '' && $catStatus_search == '')
					{
						$posts = DB::table('gr_proitem_subcategory')
                        ->where('pro_sc_status','<>','2')
                        ->where(['pro_main_id' => $main_id,'pro_sc_type' => '1'])
                        ->orderBy($order,$dir)->skip($start)->take($limit)
                        ->get();
					}
					else
					{
						$q = array();
						$sql = DB::table('gr_proitem_subcategory')->where('pro_sc_status','<>','2')
                        ->where(['pro_main_id' => $main_id,'pro_sc_type' => '1']);
						if($search != '')
						{
							$q = $sql->where('pro_sc_name','LIKE','%'.$search.'%');
						}
						if($catStatus_search != '')
						{
							$q = $sql->where(['pro_sc_status' => $catStatus_search]);
						}
						if($addedBySearch != '')
						{
							$q = $sql->where(['pro_added_by' => $addedBySearch]);
						}
						$totalFiltered = $q->count();
						$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
						$posts = $q->get();
						
					}
					$data = array();
					if(!empty($posts))
					{
						$snoCount = $start;
						
						$click_to_edit = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
						
						foreach($posts as $post)
						{
							
							if($post->pro_added_by == 0)
							{
								$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
							}
							else
							{
								$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
							}
							
							$nestedData['SNo'] = ++$snoCount;
							$nestedData['CategoryName'] = $post->pro_sc_name;
							if($post->pro_added_by==Session::get('merchantid'))
							{
								$nestedData['Edit'] = '<a href="'.url("mer_edit_subitem_category").'/'.base64_encode($post->pro_sc_id).'/'.base64_encode($main_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
							}
							else
							{
								$nestedData['Edit'] = '';
							}
							$nestedData['AddedBy'] = $added_by;//$added_by;
							$data[] = $nestedData;
						}
					}
					$json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $data
					);
					return $json_data;
				}
				$categoryName = DB::table('gr_proitem_maincategory')->select('pro_mc_name')->where('pro_mc_id','=',$main_id)->first()->pro_mc_name;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_SUB_CATE_LIST')) ? trans(Session::get('mer_lang_file').'.ADMIN_SUB_CATE_LIST') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SUB_CATE_LIST');
				$page_title =  $categoryName.'\'s '.$page_title;
				return view('sitemerchant.category.manage_subitem_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details)->with('main_id',$main_id);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** edit restaurant category **/
		public function edit_subtiem_category($id,$main_id)
		{
			$id = base64_decode($id);
			$main_id = base64_decode($main_id);
			$where = ['pro_sc_id' => $id];
			$get_cate_details = get_details('gr_proitem_subcategory',$where);
			$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_EDIT_SUB_CATE')) ? trans(Session::get('mer_lang_file').'.MER_EDIT_SUB_CATE') : trans($this->MER_OUR_LANGUAGE.'.MER_EDIT_SUB_CATE');
			$where = ['pro_sc_type' => 1,'pro_main_id' =>$main_id]; //1 for item
			$get_category_details = get_all_details('gr_proitem_subcategory','pro_sc_status',10,'desc','pro_sc_id',$where,'gr_proitem_maincategory','pro_mc_id','pro_main_id');
			return view('sitemerchant.category.manage_subitem_category')->with('pagetitle',$page_title)->with('all_details',$get_category_details)->with('cate_detail',$get_cate_details)->with('id',$id)->with('main_id',$main_id);
		}
		/** update country **/
		public function update_subitem_category()
		{
			if(Session::has('merchantid') == 1)
			
			{
				$name = 'pro_sc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$id = Input::get('category_id');
				$main_id = Input::get('main_id');
				$cate_type = mysql_escape_special_chars(Input::get('cate_type'));
				$check = DB::table('gr_proitem_subcategory')->select($name,'pro_sc_id')->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_proitem_subcategory.pro_main_id')->where('gr_proitem_maincategory.pro_mc_name','=',$old_name)->where('pro_sc_id','<>',$id)->where('pro_main_id','=',$main_id)->where('pro_sc_type' ,'=',$cate_type)->where('pro_sc_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['pro_sc_name'] = ucfirst($old_name);
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('mer_manage-subitem/'.base64_encode($id))->withErrors(['errors' =>$msg]);
					
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_sc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry['pro_added_by']=Session::get('merchantid');
				$update = updatevalues('gr_proitem_subcategory',$entry,['pro_sc_id' =>Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				//return Redirect::back();
				return Redirect::to('mer_manage-subitem/'.base64_encode($main_id));
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** add sub product category **/
		public function add_subitem_category(Request $request)
		{
			if(Session::has('merchantid') == 1)
			
			{
				$name = 'pro_sc_name';
				$old_name = mysql_escape_special_chars(Input::get('cate_name'));
				$main_id = Input::get('main_id');
				$cate_type = mysql_escape_special_chars(Input::get('cate_type'));
				$check = DB::table('gr_proitem_subcategory')->where('pro_main_id','=',$main_id)->where('pro_sc_type' ,'=',$cate_type)->where('pro_sc_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['pro_sc_name'] = ucfirst($old_name);
				
				if(count($check) > 0)
				{
					$msg = Input::get('cate_name').' - ';
					$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::back()->withErrors(['errors' =>$msg]);
					
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
						$entry['pro_sc_name_'.$Lang->lang_code] = Input::get('cate_name_'.$Lang->lang_code);
					}
				}
				$entry = array_merge(array('pro_main_id' => $main_id,'pro_sc_type' => $cate_type, 'pro_sc_status' => 1,'pro_added_by'=>Session::get('merchantid')),$entry);
				//print_r($entry); exit;
				$update = insertvalues('gr_proitem_subcategory',$entry);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::back();
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		/* EOF SUB ITEM SECTION */
		
		
		/** import category file **/
		public function import_product_category(Request $request){
			//echo 'inside'; exit;
			if(Session::has('merchantid') == 1){
				
				$this->validate($request, array('upload_file'      => 'required'));
				
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				if(!in_array($_FILES['upload_file']['type'],$mimes))
				{
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('mer_manage-product-category')->withErrors(['upload_file'=> $msg]);
				}
				
				$upload = $request->upload_file;
				// $filePath = $upload->getRealPath();
				$type = $request->cate_type;
				
				//echo $type;exit;
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function($reader) {
				})->get();
                $headerRow = $data1->first()->keys()->toArray();
                //print_r($headerRow); exit;
                $errorArray = $data = array();
                /* check header  */
                $existing_header = ['s.no','category_name','status','added_by'];
                $diff_arr = array_diff($headerRow,$existing_header);
                if(!empty($diff_arr))
                {
					
                    $msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPLODE_ERR');
					
                    Session::flash('import_err',str_replace(':columns',implode(',',$diff_arr),$msg));
                    Session::flash('popup',"open");
                    return Redirect::back();
				}
                else
                {
                    $data = $data1->toArray();
				}
				if(!empty($data)){
					
					foreach ($data as $key => $value) {
						if($value['category_name']!='' )
						{
							/** chack name already exists **/
							
							$where = ['pro_mc_name' => $value['category_name'],'pro_mc_type' => '2'];
							$check = check_name_exists('gr_proitem_maincategory','pro_mc_status',$where);
							//  print_r($check);exit;
							
							if(count($check) > 0)
							{
								$msg = $value['category_name'].' - ';
								$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray,$msg);
								// return Redirect::to('manage-product-category')->withErrors(['errors' =>$msg]);
								
							}
							else
							{
								$insert = [
                                'pro_mc_name' => ucfirst($value['category_name']),
                                'pro_mc_type' => 2, // 2 for product
                                'pro_mc_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'pro_added_by'	=> Session::get('merchantid')
								];
								$insertData = DB::table('gr_proitem_maincategory')->insert($insert);
							}
						}
						
					}
					//print_r($insert); exit;
					if(count($errorArray) > 0 )
					{
						return Redirect::to('mer_manage-product-category')->withErrors(['err_errors' =>$errorArray]);
					}
					else
					{
						$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPORT_SUCCESS');
						Session::flash('message',$msg);
						return Redirect::to('mer_manage-product-category');
					}
					
				}
				return Redirect::to('mer_manage-product-category')->with('message','Fill the data');
				
			}
			else
			{
				return Redirect::to('merchant-login') ;
			}
		}
		public function import_item_category(Request $request){
			if(Session::has('merchantid') == 1){
				
				$this->validate($request, array(
                'upload_file'      => 'required'
				));
				
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				if(!in_array($_FILES['upload_file']['type'],$mimes))
				{
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('mer_manage-item-category')->withErrors(['upload_file'=> $msg]);
				}
				
				$upload = $request->upload_file;
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function($reader) {
				})->get();
                $headerRow = $data1->first()->keys()->toArray();
                //print_r($headerRow); exit;
                $errorArray = $data = array();
                /* check header  */
                $existing_header = ['s.no','category_name','status','added_by'];
                $diff_arr = array_diff($headerRow,$existing_header);
                if(!empty($diff_arr))
                {
					
                    $msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPLODE_ERR');
                    Session::flash('import_err',str_replace(':columns',implode(',',$diff_arr),$msg));
                    Session::flash('popup',"open");
                    return Redirect::back();
				}
                else
                {
                    $data = $data1->toArray();
				}
				if(!empty($data)){
					
					foreach ($data as $key => $value) {
						if($value['category_name']!='' )
						{
							/** chack name already exists **/
							$where = ['pro_mc_name' => $value['category_name'],'pro_mc_type' => '1'];
							$check = check_name_exists('gr_proitem_maincategory','pro_mc_status',$where);
							
							$check= check_name_exists('gr_proitem_maincategory','pro_mc_status',$where);
							
							//  print_r($check);exit;
							
							if(count($check) > 0)
							{
								$msg = $value['category_name'].' - ';
								$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray,$msg);
								//return Redirect::to('manage-item-category')->withErrors(['errors' =>$msg]);
								
							}
							else
							{
								$insert = array(
                                'pro_mc_name' => ucfirst($value['category_name']),
                                'pro_mc_type' => 1, // 1 for item
                                'pro_mc_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'pro_added_by'	=> Session::get('merchantid')
								);
								$insertData = DB::table('gr_proitem_maincategory')->insert($insert);
							}
						}
						
					}
					//print_r($insert); exit;
					if(count($errorArray) > 0 )
					{
						return Redirect::to('mer_manage-item-category')->withErrors(['err_errors' =>$errorArray]);
					}
					else
					{
						$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPORT_SUCCESS');
						Session::flash('message',$msg);
						return Redirect::to('mer_manage-item-category');
					}
					
				}
				return Redirect::to('mer_manage-item-category')->with('message','Fill the data');
				
			}
			else
			{
				return Redirect::to('merchant-login') ;
			}
		}
		
		
		/** import category file **/
		public function import_sub_category(Request $request){
			if(Session::has('merchantid') == 1){
				$this->validate($request, array(
                'upload_file'      => 'required'
				));
				
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				if(!in_array($_FILES['upload_file']['type'],$mimes))
				{
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_FORMAT_INCORRECT');
					return Redirect::back()->withErrors(['upload_file'=> $msg]);
				}
				
				$upload = $request->upload_file;
				$main = $request->main_id;
				$type = $request->cate_type;   // 2 -product,1 item
				//echo $upload.'/'.$main.'/'.$type; exit;
				// echo $type;exit;
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function($reader) {
				})->get();
                $headerRow = $data1->first()->keys()->toArray();
                //print_r($headerRow); exit;
                $errorArray = $data = array();
                /* check header  */
                $existing_header = ['s.no','main_category_name','sub_category_name','status','added_by'];
                $diff_arr = array_diff($headerRow,$existing_header);
                if(!empty($diff_arr))
                {
					
					
                    $msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPLODE_ERR');
                    Session::flash('import_err',str_replace(':columns',implode(',',$diff_arr),$msg));
					
                    Session::flash('popup',"open");
                    return Redirect::back();
				}
                else
                {
                    $data = $data1->toArray();
				}
				if(!empty($data)){
					
					foreach ($data as $key => $value) {
						if($value['sub_category_name']!='' )
						{
							/** chack name already exists **/
							$exist_cate_id = get_main_cate($value['main_category_name'],$type);
							if(empty($exist_cate_id) === true)
							{
								$msg = $value['main_category_name'].' - ';
								$msg .= (Lang::has(Session::get('mer_lang_file').'.MER_NT_EXIST')) ? trans(Session::get('mer_lang_file').'.MER_NT_EXIST') : trans($this->MER_OUR_LANGUAGE.'.MER_NT_EXIST');
								array_push($errorArray,$msg);
							}
							else
							{
								$main = $exist_cate_id->pro_mc_id;
							}
							$where = ['pro_sc_name' => $value['sub_category_name'],'pro_sc_type' => $type,'pro_main_id'=>$main];
							$check = check_name_exists('gr_proitem_subcategory','pro_sc_status',$where);
							// print_r($where);exit;
							if(count($check) > 0 || (empty($exist_cate_id) === true))
							{
								$msg = $value['sub_category_name'].' - ';
								$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CATE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray,$msg);
								//return Redirect::back()->withErrors(['errors' =>$msg]);
								
							}
							else{
								$insert = [
                                'pro_sc_name' => ucfirst($value['sub_category_name']),
                                'pro_sc_type' => $type, // 1 - for restaurant
                                'pro_sc_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'pro_main_id' => $main,
                                'pro_added_by' => Session::get('merchantid')
								];
								$insertData = DB::table('gr_proitem_subcategory')->insert($insert);
							}
						}
						
					}
					//print_r($insert); exit;
					if(count($errorArray) > 0 )
					{
						return Redirect::back()->withErrors(['err_errors' =>$errorArray]);
					}
					else
					{
						$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPORT_SUCCESS');
						Session::flash('message',$msg);
						return Redirect::back();
					}
					/*if(!empty($insert)){
						
						
						if ($insertData) {
                        $msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPORT_SUCCESS');
                        Session::flash('message',$msg);
                        return Redirect::back();
						
                        }else {
						
                        $msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_NOT_SUCCESSS')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_NOT_SUCCESSS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPORT_NOT_SUCCESSS');
                        Session::flash('message',$msg);
                        return Redirect::back();
						}
					}*/
				}
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_FILL_DATA')) ? trans(Session::get('mer_lang_file').'.ADMIN_FILL_DATA') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_FILL_DATA');
				
				return Redirect::back()->with('message',$msg);
				
				
			}
			else
			{
				return Redirect::to('merchant-login') ;
			}
		}
	}	