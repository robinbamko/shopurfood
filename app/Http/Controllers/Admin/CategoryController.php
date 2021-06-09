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
	
	use Excel;
	
	use Response;
	
	use File;
	use Image;
	//use App\User;
	use Freshbitsweb\Laratables\Laratables;
	
	use Illuminate\Support\Str;
	
	
	class CategoryController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
			
		}
		
		/** manage store category **/
		public function store_category()
		{
			if (Session::has('admin_id') == 1) {
				$where = ['cate_type' => 2]; //2 for store
				$get_category_details = array();//get_all_details('gr_category','cate_status',10,'desc','cate_id',$where);
				//print_r($get_country_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_STORE_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_STORE_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_STORE_CATE');
				//$id = '';
				return view('Admin.category.manage_store_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details);
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function store_category_list(Request $request)
		{
			//print_r($query);
			if (Session::has('admin_id') == 1) {
				$columns = array(
                0 => 'cate_id',
                1 => 'cate_id',
                2 => 'cate_name',
                3 => 'cate_id',
                4 => 'cate_status',
                5 => 'cate_id',
                6 => 'cate_added_by'
				);
				/*To get Total count */
				$totalData = Admin::get_categoryLists_count('gr_category', ['cate_type' => '2'], 'cate_status');
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$search = trim($request->catName_search);
				$addedBySearch = trim($request->addedBy_search);
				$catStatus_search = trim($request->catStatus_search);
				if ($search == '' && $addedBySearch == '' && $catStatus_search == '') {
					$posts = DB::table('gr_category')->where('cate_type', '2')->where('cate_status', '!=', '2')->orderBy($order, $dir)->skip($start)->take($limit)->get();
					} else {
					$sql = DB::table('gr_category')->where('cate_type', '2')->where('cate_status', '!=', '2');
					if ($addedBySearch != '') {
						$q = $sql->where('cate_added_by', $addedBySearch);
					}
					if ($search != '') {
						$q = $sql->where('cate_name', 'LIKE', '%' . $search . '%');
					}
					if ($catStatus_search != '') {
						$q = $sql->where('cate_status', $catStatus_search);
					}
					//$q = $sql->orWhere('cate_name', 'LIKE','%'.$search.'%');
					//DB::connection()->enableQueryLog();
					$totalFiltered = $q->count();
					$q = $sql->orderBy($order, $dir)->skip($start)->take($limit);
					$posts = $q->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
					
				}
				
				/** FOR SUBADMIN PRIVILAGES **/
				$allPrev = Config('allPrev');
				$privileges = Config('privileges');
				extract($privileges);
				/** END FOR SUBADMIN PRIVILAGES **/
				
				
				$data = array();
				if (!empty($posts)) {
					$snoCount = $start;
					foreach ($posts as $post) {
						//                    $getUsedStore = getStoreUsedThisCategory($post->cate_id, 2);
						$getUsedStore = getStoreUsedThisCategory($post->cate_id,1,'delete');
						$usedStore = $getUsedStore[0]->usedStore;
						$usedCount = $getUsedStore[0]->usedCount;
						$toolTipTitle = 'Sorry! Can\'t Block';
						if ($usedCount > 1) {
							$toolTipText = $usedCount . ' stores are used this category. Please block those stores before you block this category';
							$deleteToolTipText = $usedCount . ' stores are used this category. Please delete those stores before you delete this category';
							} else {
							$toolTipText = $usedCount . 'store is used this category.if you Block this category  stores and their products also will be blocked';
							$deleteToolTipText = $usedCount . 'store is used this category. Please Delete that store before you Delete this category';
							
						}
						//$deleteLink = '<a href="javascript:;" data-toggle="popover" title="Popover Header" data-content="Some content inside the popover" data-trigger="hover" >Toggle popover</a>';
						if ($post->cate_status == 1) {
							if ($usedCount > 0) {
								//$blockRunblock='<a href="javascript:;"><i class="fa fa-check" data-toggle="popover" data-html="true" title="'.$toolTipTitle.'" data-content="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
								$blockRunblock = '<a href="javascript:change_all_statusblk(\'' . $post->cate_id . '\');" disabled="disabled" id="statusblock"><i class="fa fa-check tooltip-demo" data-html="true" title="<div>' . $toolTipText . '</div>"></i></a>';
								} else {
								// $blockRunblock='<a href="'.url("store_cate_status").'/'.$post->cate_id.'/0'.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
								
								$blockRunblock = '<a href="javascript:individual_change_status(\'' . $post->cate_id . '\',0);" id="statusLink_' . $post->cate_id . '"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
								
							}
							} else {
							
							if ($usedCount > 0) {
								
								$toolTipText = $usedCount . 'store is used this category.if you UnBlock this category  stores and their products also will be UnBlocked';
								
								$blockRunblock = '<a href="javascript:change_all_unblock(\'' . $post->cate_id . '\');" id="statusLink_' . $post->cate_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="' . $toolTipText . '" ></i></a>';
								} else {
								$blockRunblock = '<a href="javascript:individual_change_status(\'' . $post->cate_id . '\',1);" id="statusLink_' . $post->cate_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
								
								// $blockRunblock='<a href="'.url("store_cate_status").'/'.$post->cate_id.'/1'.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock"></i></a>';
								
							}
							
							
						}
						
						if ($allPrev == '1' || in_array('2', $Category)) {
							$editLink = '<a href="' . url("edit_store_category") . '/' . base64_encode($post->cate_id) . '"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
							} else {
							$editLink = '--';
						}
						
						
						if ($allPrev == '1' || in_array('3', $Category)) {
							if ($usedCount > 0) {
								//$deleteLink='<a href="javascript:;"><i class="fa fa-trash" data-toggle="popover" title="Sorry! Can\'t Delete" data-content="'.$deleteToolTipText.'" data-trigger="hover"  disabled="disabled"></i></a>';
								$deleteLink = '<a href="javascript:;" disabled="disabled"><i class="fa fa-trash tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Delete</h4><div>' . $deleteToolTipText . '</div>"></i></a>';
								$chkName = '';
								$chkDisabled = 'disabled="disabled"';
								} else {
								$deleteLink = '<a href="' . url("store_cate_status") . '/' . $post->cate_id . '/2' . '"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>';
								// $deleteLink = '<a href= "javascript:individual_change_status(\''.$post->cate_id.'\',2);" title="delete" class="tooltip-demo" id="statusLink_'.$post->cate_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
								$chkName = 'chk[]';
								$chkDisabled = '';
							}
							} else {
							
							$deleteLink = '--';
							$chkName = '';
							$chkDisabled = '';
						}
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="' . $chkName . '" value="' . $post->cate_id . '" ' . $chkDisabled . '>';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['CategoryName'] = $post->cate_name;
						$nestedData['Edit'] = $editLink;
						$nestedData['Status'] = $blockRunblock;
						$nestedData['Delete'] = $deleteLink;
						$nestedData['AddedBy'] = $post->cate_added_by;//$added_by;
						
						$data[] = $nestedData;
						
					}
				}
				
				$json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
				);
				
				echo json_encode($json_data);
			}
		}
		
		
		/** add store category **/
		public function add_store_category(Request $request)
		{ //print_r($request->all()); exit;
			if (Session::has('admin_id') == 1) {
				$err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_CATE_NAME');
				$this->validate($request, ['cate_name' => 'Required',
                'cate_image' => 'Required'],
                ['cate_name.required' => $err_msg,
				'cate_image.Sometimes' => (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SEL_IMAGE')]);
				
				$name = 'cate_name';
				$old_name = Input::get('cate_name');
				
				$where = [$name => $old_name, 'cate_type' => '2'];  //2 for store
				$check = check_name_exists('gr_category', 'cate_status', $where);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-store-category')->withErrors(['errors' => $msg])->with('id', '');
					
				}
				$entry['cate_name'] = ucfirst($old_name);
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['cate_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				if (request()->cate_image != '') {
					$image_name = 'store_cate_' . rand() . '.' . request()->cate_image->getClientOriginalExtension();
					$image = Image::make(request()->cate_image->getRealPath())->resize(300, 150);
					$a = $image->save(public_path('images/category') . '/' . $image_name, 80);
					//echo $a; exit;
					$entry['cate_img'] = $image_name;
				}
				$entry = array_merge(array('cate_type' => 2), $entry); //2 for store
				$insert = insertvalues('gr_category', $entry);
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-store-category');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** edit store category **/
		public function edit_store_category($id)
		{
			$id = base64_decode($id);
			$where = ['cate_id' => $id];
			$get_cate_details = get_details('gr_category', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EDIT_ST_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EDIT_ST_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EDIT_ST_CATE');
			$where = ['cate_type' => 2]; //2 for store
			$get_allcate_details = get_all_details('gr_category', 'cate_status', 10, 'desc', 'cate_id', $where);
			return view('Admin.category.manage_store_category')->with('pagetitle', $page_title)->with('all_details', $get_allcate_details)->with('cate_detail', $get_cate_details)->with('id', $id);
		}
		
		/** update country **/
		public function update_store_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_CATE_NAME');
				$this->validate($request, ['cate_name' => 'Required', 'cate_image' => 'Sometimes'],
                ['cate_name.required' => $err_msg,
				'cate_image.Sometimes' => (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SEL_IMAGE')]);
				
				$name = 'cate_name';
				$old_name = Input::get('cate_name');
				$id = Input::get('category_id');
				$check = DB::table('gr_category')->select($name, 'cate_id')->where('cate_id', '<>', $id)->where('cate_type', '=', '2')->where('cate_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['cate_name'] = ucfirst($old_name);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-store-category')->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['cate_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				//echo request()->cate_image; exit;
				if (request()->cate_image != '') {//echo "test"; exit;
					/** delete old image **/
					$image_path2 = public_path('images/category/') . Input::get('old_img');  // Value is not URL but directory file path
					if (File::exists($image_path2)) {
						$a = File::delete($image_path2);
					}
					$image_name = 'store_cate_' . rand() . '.' . request()->cate_image->getClientOriginalExtension();
					$image = Image::make(request()->cate_image->getRealPath())->resize(300, 150);
					$a = $image->save(public_path('images/category') . '/' . $image_name, 80);
					$entry['cate_img'] = $image_name;
					} else {
					$ca_img = Input::get('old_img');
					$entry['cate_img'] = $ca_img;
				}
				//print_r($entry); exit;
				$update = updatevalues('gr_category', $entry, ['cate_id' => Input::get('category_id')]);
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-store-category');
				} else {
				return Redirect::to('admin-login');
			}
			
		}
		
		/** block/unblock category **/
		public function store_cate_status($id, $status)
		{
			$update = ['cate_status' => $status];
			$where = ['cate_id' => $id];
			$a = updatevalues('gr_category', $update, $where);
			
			if ($status == 1) //Active
			{
				/** update store status**/
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UNBL_ST_PRO')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UNBL_ST_PRO') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UNBL_ST_PRO');
				Session::flash('message', $msg);
				return Redirect::to('manage-store-category');
			}
			if ($status == 2) //Delete
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELETE_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-store-category');
			} else   //block
			{
				/** update store status**/
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BL_ST_PRO')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BL_ST_PRO') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BL_ST_PRO');
				Session::flash('message', $msg);
				return Redirect::to('manage-store-category');
			}
		}
		
		/** multiple block/unblock  categoory**/
		public function multi_store_block()
		{
			$update = ['cate_status' => Input::get('status')];
			$val = Input::get('val');
			$status = Input::get('status');
			//return count($val); exit;
			for ($i = 0; $i < count($val); $i++) {
				$where = ['cate_id' => $val[$i]];				
				$a = updatevalues('gr_category', $update, $where);
				
				DB::select("UPDATE gr_product AS st1, gr_store AS st2 SET st1.pro_status = '".$status."' WHERE st1.pro_store_id = st2.id and st2.st_category = '".$val[$i]."'");//blocking store products
				DB::table('gr_store')->where('st_category','=',$val[$i])->update(['st_status'=>$status]);																		// blocking store
				DB::select("DELETE FROM gr_cart_save WHERE cart_st_id IN (SELECT * FROM (SELECT gr_store.id FROM gr_store WHERE gr_store.st_category = '".$val[$i]."' ) AS p)");//DELETE FROM CART_SAVE
			}
			//echo Input::get('status'); exit;
			if (Input::get('status') == 1) //Active
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UNBL_ST_PRO')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UNBL_ST_PRO') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UNBL_ST_PRO');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
			if (Input::get('status') == 2) //Delete
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DL_ST_PRO')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DL_ST_PRO') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DL_ST_PRO');
				//Session::flash('message',$msg);
				echo $msg;
				
			} elseif (Input::get('status') == 0)   //block
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BL_ST_PRO')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BL_ST_PRO') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BL_ST_PRO');
				///Session::flash('message',$msg);
				echo $msg;
			}
		}
		
		/** download store category **/
		public function download_store_category($type, $sample = null)
		{
			if ($sample == 'sample_file') {
				$data = DB::table('gr_category')->select('cate_name as StoreCategoryName', 'cate_status', 'cate_added_by')->where('cate_status', '!=', '2')->where(['cate_type' => '2', 'cate_added_by' => '0'])->limit(4)->get()->toarray();
				} else {
				$data = DB::table('gr_category')->select('cate_name as StoreCategoryName', 'cate_status', 'cate_added_by')->where('cate_status', '!=', '2')->where('cate_type', '=', '2')->get()->toarray();
			}
			
			return Excel::create('Store Category lists', function ($excel) use ($data) {
				$excel->sheet('category_list', function ($sheet) use ($data) {
					$sheet->cell('A1', function ($cell) {
						$cell->setValue('S.No');
					});
					$sheet->cell('B1', function ($cell) {
						$cell->setValue('Category Name');
					});
					$sheet->cell('C1', function ($cell) {
						$cell->setValue('Status');
					});
					$sheet->cell('D1', function ($cell) {
						$cell->setValue('Added By');
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function ($row) {
						$row->setBackground('#CCCCCC');
					});
					foreach ($data as $key => $value) {
						$i = $key + 2;
						if ($value->cate_status == '0') {
							$status = 'Inactive';
							} else {
							$status = 'Active';
						}
						
						if($value->cate_added_by=='0')
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						}
						
						$sheet->cell('A' . $i, $i - 1); //print serial no
						$sheet->cell('B' . $i, $value->StoreCategoryName);
						$sheet->cell('C' . $i, $status);
						$sheet->cell('D'.$i, $addedByRes);
					}
				});
			})->download($type);
			
		}
		
		/** import category file **/
		public function import_store_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$this->validate($request, array('upload_file' => 'required'));
				
				$mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
				if (!in_array($_FILES['upload_file']['type'], $mimes)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('manage-store-category')->withErrors(['upload_file' => $msg]);
				}
				
				$upload = $request->upload_file;
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function ($reader) {
				})->get();
				$headerRow = $data1->first()->keys()->toArray();
				//print_r($headerRow); exit;
				$errorArray = $data = array();
				/* check header  */
				$existing_header = ['s.no', 'category_name', 'status', 'added_by'];
				$diff_arr = array_diff($headerRow, $existing_header);
				if (!empty($diff_arr)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INVALID_COLUMN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INVALID_COLUMN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INVALID_COLUMN');
					Session::flash('import_err', str_replace('implodData', implode(',', $diff_arr), $msg));//"Invalid column name(s) : ".implode(',',$diff_arr).".Please upload valid data");
					Session::flash('popup', "open");
					return Redirect::to('manage-store-category');
					} else {
					$data = $data1->toArray();
				}
				if (!empty($data)) {
					
					foreach ($data as $key => $value) {
						if ($value['category_name'] != '') {
							$where = ['cate_name' => $value['category_name'], 'cate_type' => '2'];
							$check = check_name_exists('gr_category', 'cate_status', $where);
							if (count($check) > 0) {
								$msg = $value['category_name'] . ' - ';
								$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray, $msg);
								} else {
								$insert = array(
                                'cate_name' => ucfirst($value['category_name']),
                                'cate_type' => 2, // 2 - for store
                                'cate_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'cate_added_by' => 0 //($value['added_by'] == 'Admin') ? '0' : '1'
								);
								//print_r($insert); echo '<hr>';
								$insertData = DB::table('gr_category')->insert($insert);
							}
						}
					}
					
					if (count($errorArray) > 0) {
						return Redirect::to('manage-store-category')->withErrors(['err_errors' => $errorArray]);
						} else {
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_IMPORT_SUCCESS');
						Session::flash('message', $msg);
						return Redirect::to('manage-store-category');
					}
					} else {
					return Redirect::to('manage-store-category')->with('message', 'Fill the data');
				}
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** restaurant category starts **/
		/** manage store category **/
		public function manage_restaurant_category()
		{
			if (Session::has('admin_id') == 1) {
				$where = ['cate_type' => 1]; //1 for restaurant
				$get_category_details = array();//get_all_details('gr_category','cate_status',10,'desc','cate_id',$where);
				//print_r($get_country_details); exit;
				$page_title_r = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_REST_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_REST_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_REST_CATE');
				$page_title_c = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CUISINE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CUISINE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CUISINE');
				$page_title = $page_title_r . '(' . $page_title_c . ')';
				return view('Admin.category.manage_restaurant_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details);
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/* get restaurant category list ajax */
		public function restaurant_category_list(Request $request)
		{
			//print_r($query);
			if (Session::has('admin_id') == 1) {
				$columns = array(
                0 => 'cate_id',
                1 => 'cate_id',
                2 => 'cate_name',
                3 => 'cate_id',
                4 => 'cate_status',
                5 => 'cate_id',
                6 => 'cate_added_by'
				);
				/*To get Total count */
				$totalData = Admin::get_categoryLists_count('gr_category', ['cate_type' => '1'], 'cate_status');
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				$search = trim($request->catName_search);
				$addedBySearch = trim($request->addedBy_search);
				$catStatus_search = trim($request->catStatus_search);
				if ($search == '' && $addedBySearch == '' && $catStatus_search == '') {
					$posts = DB::table('gr_category')->where('cate_type', '1')->where('cate_status', '!=', '2')->orderBy($order, $dir)->skip($start)->take($limit)->get();
					} else {
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_category')->where('cate_type', '1')->where('cate_status', '!=', '2');
					if ($addedBySearch != '') {
						$q = $sql->where('cate_added_by', $addedBySearch);
					}
					if ($search != '') {
						$q = $sql->where('cate_name', 'LIKE', '%' . $search . '%');
					}
					if ($catStatus_search != '') {
						$q = $sql->where('cate_status', $catStatus_search);
					}
					$totalFiltered = $sql->count();
					$q = $sql->orderBy($order, $dir)->skip($start)->take($limit);
					$posts = $q->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
					
				}
				
				
				/** FOR SUBADMIN PRIVILAGES **/
				$allPrev = Config('allPrev');
				$privileges = Config('privileges');
				extract($privileges);
				/** END FOR SUBADMIN PRIVILAGES **/
				
				
				$data = array();
				if (!empty($posts)) {
					$snoCount = $start;
					foreach ($posts as $post) {
						$getUsedStore = getStoreUsedThisCategory($post->cate_id, 1);
						//                    $getUsedStore = getStoreUsedThisCategory($post->cate_id,1,'block');
						$usedStore = $getUsedStore[0]->usedStore;
						$usedCount = $getUsedStore[0]->usedCount;
						if ($usedCount > 1) {
							$toolTipText = $usedCount . ' restaurants are used this category. Please block those restaurants before you block this category';
							$deleteToolTipText = $usedCount . ' restaurants are used this category. Please delete those restaurants before you delete this category';
							} else {
							$toolTipText = $usedCount . ' restaurant is used this category.if you Block this category, the restaurants and their items will be Blocked! ';
							$deleteToolTipText = $usedCount . ' restaurant is used this category. Please delete that restaurant before you delete this category';
						}
						
						if ($post->cate_status == 1) {
							if ($usedCount > 0) {
								//$blockRunblock='<a href="javascript:;"><i class="fa fa-check" data-toggle="popover" data-html="true" title="Sorry! Can\'t Block" data-content="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
								$blockRunblock = '<a href="javascript:change_all_statusblk(\'' . $post->cate_id . '\');" disabled="disabled"><i class="fa fa-check tooltip-demo" data-html="true" title="<div>' . $toolTipText . '</div>"></i></a>';
								} else {
								// $blockRunblock='<a href="'.url("restaurant_cate_status").'/'.$post->cate_id.'/0'.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
								
								$blockRunblock = '<a href="javascript:individual_change_status(\'' . $post->cate_id . '\',0);" id="statusLink_' . $post->cate_id . '"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
								
							}
							} else {
							
							if ($usedCount > 0) {
								
								$toolTipText = $usedCount . ' restaurant is used this category.if you UnBlock this category, the restaurants and their items will be UnBlocked! ';
								
								$blockRunblock = '<a href="javascript:change_all_unblock(\'' . $post->cate_id . '\');" id="statusLink_' . $post->cate_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="' . $toolTipText . '" ></i></a>';
								} else {
								
								// $blockRunblock='<a href="'.url("restaurant_cate_status").'/'.$post->cate_id.'/1'.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock"></i></a>';
								$blockRunblock = '<a href="javascript:individual_change_status(\'' . $post->cate_id . '\',1);" id="statusLink_' . $post->cate_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
							}
						}
						
						if ($allPrev == '1' || in_array('2', $Category)) {
							$editLink = '<a href="' . url("edit_restaurant_category") . '/' . base64_encode($post->cate_id) . '"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
							} else {
							$editLink = '--';
						}
						
						
						if ($allPrev == '1' || in_array('3', $Category)) {
							if ($usedCount > 0) {
								
								$deleteLink = '<a href="javascript:;" disabled="disabled"><i class="fa fa-trash tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Delete</h4><div>' . $deleteToolTipText . '</div>"></i></a>';
								$chkName = '';
								$chkDisabled = 'disabled="disabled"';
								} else {
								// $deleteLink='<a href="'.url("restaurant_cate_status").'/'.$post->cate_id.'/2'.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>';
								$deleteLink = '<a href= "javascript:individual_change_status(\'' . $post->cate_id . '\',2);" title="delete" class="tooltip-demo" id="statusLink_' . $post->cate_id . '"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
								$chkName = 'chk[]';
								$chkDisabled = '';
							}
							} else {
							
							$deleteLink = '--';
							$chkName = '';
							$chkDisabled = '';
						}
						if ($post->cate_added_by == 0) {
							$added_by = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN');
							} else {
							$added_by = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MERCHANT');
						}
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="' . $chkName . '" value="' . $post->cate_id . '" ' . $chkDisabled . '>';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['CategoryName'] = $post->cate_name;
						$nestedData['Edit'] = $editLink;
						$nestedData['Status'] = $blockRunblock;
						$nestedData['Delete'] = $deleteLink;
						$nestedData['AddedBy'] = $post->cate_added_by;;
						$data[] = $nestedData;
						
					}
				}
				
				$json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
				);
				
				echo json_encode($json_data);
			}
		}
		
		/** add restaurant category **/
		public function add_restaurant_category(Request $request)
		{
			
			
			if (Session::has('admin_id') == 1) {
				$err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_CATE_NAME');
				$err_img_req = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SEL_IMAGE');
				$this->validate($request, ['cate_name' => 'Required',
				//                'cate_image' => 'Required',
                'cate_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=330,max_width=330,min_height=450,max_height=450',
                'cate_icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=75,max_width=75,min_height=75,max_height=75',
				],
                ['cate_name.required' => $err_msg,
				'cate_image.required' => $err_img_req,
				'cate_icon.required' => $err_img_req
                ]);
				
				$name = 'cate_name';
				$old_name = Input::get('cate_name');
				$where = [$name => $old_name, 'cate_type' => '1'];
				$check = check_name_exists('gr_category', 'cate_status', $where);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-restaurant-category')->withErrors(['errors' => $msg]);
					
				}
				$entry['cate_name'] = ucfirst($old_name);
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$validatoradv = Validator::make($request->all(), [
                        'cate_name_'.$Lang->lang_code => 'required',
						]);
						if($validatoradv->fails()){
							return redirect('manage-restaurant-category')->withErrors($validatoradv)->withInput();
							}else{
							$entry['cate_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
						}
						
						
					}
				}
				if (request()->cate_image != '') {
					$image_name = 'restaurant_cate_' . rand() . '.' . request()->cate_image->getClientOriginalExtension();
					$image = Image::make(request()->cate_image->getRealPath())->resize(300, 150);
					$a = $image->save(public_path('images/category') . '/' . $image_name, 80);
					//echo $a; exit;
					$entry['cate_img'] = $image_name;
					}
					//
					if (request()->cate_icon != '') {
					$cate_name = 'cat_icon_' . rand() . '.' . request()->cate_icon->getClientOriginalExtension();
					$image = Image::make(request()->cate_icon->getRealPath())->resize(75, 75);
					$a = $image->save(public_path('images/category') . '/' . $cate_name, 80);
					
					$entry['cate_icon'] = $cate_name;
					}
				
				
				$entry = array_merge(array('cate_type' => 1), $entry); //1 for restaurant
				$insert = insertvalues('gr_category', $entry);
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-restaurant-category');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** edit restaurant category **/
		public function edit_restaurant_category($id)
		{
			$id = base64_decode($id);
			
			$where = ['cate_id' => $id];
			$get_cate_details = get_details('gr_category', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EDIT_RES_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EDIT_RES_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EDIT_RES_CATE');
			$where = ['cate_type' => 1]; //1 for restaurant
			$get_allcate_details = get_all_details('gr_category', 'cate_status', 10, 'desc', 'cate_id', $where);
			return view('Admin.category.manage_restaurant_category')->with('pagetitle', $page_title)->with('all_details', $get_allcate_details)->with('cate_detail', $get_cate_details)->with('id', $id);
		}
		
		/** update country **/
		public function update_restaurant_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_CATE_NAME');
				$err_img_req = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SEL_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SEL_IMAGE');
				$this->validate($request, ['cate_name' => 'Required',
                'cate_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=330,max_width=330,min_height=450,max_height=450',
                'cate_icon' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=75,max_width=75,min_height=75,max_height=75',
				], 
				['cate_name.required' => $err_msg,
				'cate_image.Sometimes' => $err_img_req,
                'cate_icon.Sometimes' => $err_img_req
				]);
				$name = 'cate_name';
				$old_name = Input::get('cate_name');
				$id = Input::get('category_id');
				$check = DB::table('gr_category')->select($name, 'cate_id')->where('cate_id', '<>', $id)->where('cate_type', '=', '1')->where('cate_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['cate_name'] = ucfirst($old_name);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-restaurant-category')->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						
						$validatoradv = Validator::make($request->all(), [
                        'cate_name_' . $Lang->lang_code => 'required',
						]);
						if ($validatoradv->fails()) {
							return redirect('manage-restaurant-category')->withErrors($validatoradv)->withInput();
							} else {
							$entry['cate_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
						}
						
						
					}
				}
				if (request()->cate_image != '') {//echo "test"; exit;
					$image_path2 = public_path('images/category/') . Input::get('old_img');  // Value is not URL but directory file path
					if (File::exists($image_path2)) {
						$a = File::delete($image_path2);
					}
					$image_name = 'restaurant_cate_' . rand() . '.' . request()->cate_image->getClientOriginalExtension();
					$image = Image::make(request()->cate_image->getRealPath())->resize(330, 450);
					$a = $image->save(public_path('images/category') . '/' . $image_name, 80);
					$entry['cate_img'] = $image_name;
				} else {
					$ca_img = Input::get('old_img');
					$entry['cate_img'] = $ca_img;
				}
					
				if (request()->cate_icon != '') {//echo "test"; exit;
					$icon_path2 = public_path('images/category/') . Input::get('old_icon');  // Value is not URL but directory file path
					if (File::exists($icon_path2)) {
						$a = File::delete($icon_path2);
					}
					$icon_name = 'cate_icon' . rand() . '.' . request()->cate_icon->getClientOriginalExtension();
					$image = Image::make(request()->cate_icon->getRealPath())->resize(75, 75);
					$a = $image->save(public_path('images/category') . '/' . $icon_name, 80);
					$entry['cate_icon'] = $icon_name;
					} else {
					$ca_icon = Input::get('old_icon');
					$entry['cate_icon'] = $ca_icon;
				}
				$update = updatevalues('gr_category', $entry, ['cate_id' => Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-restaurant-category');
			} else {
				return Redirect::to('admin-login');
			}
			
		}
		
		/** block/unblock category **/
		/*public function restaurant_cate_status($id,$status)
			{
			$update = ['cate_status' => $status];
			$where = ['cate_id' => $id];
			$a = updatevalues('gr_category',$update,$where);
			
			if($status == 1) //Active
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
			Session::flash('message',$msg);
			
			}
			if($status == 2) //Delete
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
			Session::flash('message',$msg);
			
			}
			else   //block
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
			Session::flash('message',$msg);
			
			}
			return Redirect::to('manage-restaurant-category');
		}*/
		
		/** multiple block/unblock  category**/
		public function multi_restaurant_block()
		{
			$update = ['cate_status' => Input::get('status')];
			$val = Input::get('val');
			$status = Input::get('status');
			//return count($val); exit;
			for ($i = 0; $i < count($val); $i++) {
				$where = ['cate_id' => $val[$i]];				
				$a = updatevalues('gr_category', $update, $where);
				
				DB::select("UPDATE gr_product AS st1, gr_store AS st2 SET st1.pro_status = '".$status."' WHERE st1.pro_store_id = st2.id and st2.st_category = '".$val[$i]."'");//blocking store products
				DB::table('gr_store')->where('st_category','=',$val[$i])->update(['st_status'=>$status]);																		// blocking store
				DB::select("DELETE FROM gr_cart_save WHERE cart_st_id IN (SELECT * FROM (SELECT gr_store.id FROM gr_store WHERE gr_store.st_category = '".$val[$i]."' ) AS p)");//DELETE FROM CART_SAVE
			}
			//echo Input::get('status'); exit;
			if (Input::get('status') == 1) //Active
			{
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UNBLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
			} elseif (Input::get('status') == 2) //Delete
			{
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELETE_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
			} elseif (Input::get('status') == 0)   //block
			{
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
			}
		}
		
		/** download restaurant category **/
		public function download_restaurant_category($type, $sample = null)
		{
			if ($sample == 'sample_file') {
				$data = DB::table('gr_category')->select('cate_name as RestaurantCategoryName', 'cate_status', 'cate_added_by')->where('cate_status', '!=', '2')->where(['cate_type' => '1', 'cate_added_by' => '0'])->limit(4)->get()->toarray();
				} else {
				$data = DB::table('gr_category')->select('cate_name as RestaurantCategoryName', 'cate_status', 'cate_added_by')->where('cate_status', '!=', '2')->where('cate_type', '=', '1')->get()->toarray();
			}
			
			//  print_r($data); exit;
			return Excel::create('Restaurant Category lists', function ($excel) use ($data) {
				$excel->sheet('category_list', function ($sheet) use ($data) {
					$sheet->cell('A1', function ($cell) {
						$cell->setValue('S.No');
					});
					$sheet->cell('B1', function ($cell) {
						$cell->setValue('Category Name');
					});
					$sheet->cell('C1', function ($cell) {
						$cell->setValue('Status');
					});
					$sheet->cell('D1', function ($cell) {
						$cell->setValue('Added By');
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function ($row) {
						$row->setBackground('#CCCCCC');
					});
					foreach ($data as $key => $value) {
						$i = $key + 2;
						if ($value->cate_status == '0') {
							$status = 'Inactive';
							} else {
							$status = 'Active';
						}
						
						
						if($value->cate_added_by=='0')
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						}
						
						$sheet->cell('A' . $i, $i - 1); //print serial no
						$sheet->cell('B' . $i, $value->RestaurantCategoryName);
						$sheet->cell('C' . $i, $status);
						
						$sheet->cell('D'.$i, $addedByRes);
					}
				});
			})->download($type);
			
		}
		
		/** import category file **/
		public function import_restaurant_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$this->validate($request, array('upload_file' => 'required'));
				
				$mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
				if (!in_array($_FILES['upload_file']['type'], $mimes)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('manage-restaurant-category')->withErrors(['upload_file' => $msg]);
				}
				
				$upload = $request->upload_file;
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function ($reader) {
				})->get();
				$headerRow = $data1->first()->keys()->toArray();
				//print_r($headerRow); exit;
				$errorArray = $data = array();
				/* check header  */
				$existing_header = ['s.no', 'category_name', 'status', 'added_by'];
				$diff_arr = array_diff($headerRow, $existing_header);
				if (!empty($diff_arr)) {
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INVALID_COLUMN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INVALID_COLUMN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INVALID_COLUMN');
					Session::flash('import_err', str_replace('implodData', implode(',', $diff_arr), $msg));
					//Session::flash('import_err',"Invalid column name(s) : ".implode(',',$diff_arr).".Please upload valid data");
					Session::flash('popup', "open");
					return Redirect::back();
					} else {
					$data = $data1->toArray();
				}
				if (!empty($data)) {
					
					foreach ($data as $key => $value) {
						if ($value['category_name'] != '') {
							$where = ['cate_name' => $value['category_name'], 'cate_type' => '1'];
							$check = check_name_exists('gr_category', 'cate_status', $where);
							if (count($check) > 0) {
								$msg = $value['category_name'] . ' - ';
								$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray, $msg);
								} else {
								$insert = array(
                                'cate_name' => ucfirst($value['category_name']),
                                'cate_type' => 1, // 1 - for restaurant
                                'cate_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'cate_added_by' => 0 //($value['added_by'] == 'Admin') ? '0' : '1'
								);
								//print_r($insert); echo '<hr>';
								$insertData = DB::table('gr_category')->insert($insert);
							}
						}
					}
					//print_r($insert); exit;
					
					if (count($errorArray) > 0) {
						return Redirect::to('manage-restaurant-category')->withErrors(['err_errors' => $errorArray]);
						} else {
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_IMPORT_SUCCESS');
						Session::flash('message', $msg);
						return Redirect::to('manage-restaurant-category');
					}
				}
				return Redirect::to('manage-restaurant-category')->with('message', 'Fill the data');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** product main category **/
		
		public function manage_product_category()
		{
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MNGE_PRO_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MNGE_PRO_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MNGE_PRO_CATE');
			
			return view('Admin.category.manage_product_category')->with('pagetitle', $page_title);
		}
		
		/* get product category list ajax */
		public function product_category_list_ajax(Request $request)
		{
			
			$columns = array(
            0 => 'pro_mc_id',
            1 => 'pro_mc_id',
            2 => 'pro_mc_name',
            3 => 'pro_mc_id',
            4 => 'pro_mc_id',
            5 => 'pro_mc_status',
            6 => 'pro_mc_id',
            7 => 'pro_added_by'
			);
			/*To get Total count */
			$totalData = Admin::get_categoryLists_count('gr_proitem_maincategory', ['pro_mc_type' => '2'], 'pro_mc_status');
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
			
			if ($search == '' && $addedBySearch == '' && $catStatus_search == '') {
				$posts = DB::table('gr_proitem_maincategory')->where('pro_mc_type', '2')->where('pro_mc_status', '!=', '2')->orderBy($order, $dir)->skip($start)->take($limit)->get();
				} else {
				$q = array();
				//DB::connection()->enableQueryLog();
				$sql = DB::table('gr_proitem_maincategory')->where('pro_mc_type', '2')->where('pro_mc_status', '!=', '2');
				
				if ($search != '') {
					$q = $sql->whereRaw("pro_mc_name LIKE '%" . $search . "%'");
				}
				if ($addedBySearch != '') {
					$q = $sql->where('pro_added_by', '=', $addedBySearch);
				}
				if ($catStatus_search != '') {
					$q = $sql->where('pro_mc_status', '=', $catStatus_search);
				}
				//$posts =  $q->get();
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				$totalFiltered = $q->count();
				$posts = $q->orderBy($order, $dir)->skip($start)->take($limit)->get();
			}
			
			/** FOR SUBADMIN PRIVILAGES **/
			$allPrev = Config('allPrev');
			$privileges = Config('privileges');
			extract($privileges);
			/** END FOR SUBADMIN PRIVILAGES **/
			
			$data = array();
			if (!empty($posts)) {
				$snoCount = $start;
				foreach ($posts as $post) {
					//                $getUsedProduct = getProductsUsedThisCategory($post->pro_mc_id, 1);
					$getUsedProduct = getProductsUsedThisCategory($post->pro_mc_id,1,'block');
					$usedProduct = $getUsedProduct[0]->usedProduct;
					$usedCount = $getUsedProduct[0]->usedCount;
					if ($usedCount > 1) {
						$toolTipText = $usedCount . ' products are used in this category.if Block this category the products also will be Blocked!';
						$deleteToolTipText = $usedCount . ' products are used this category. Please delete those products before you delete this category';
						} else {
						$toolTipText = $usedCount . ' product is used this category.if you block this category the product also will be Blocked!';
						$deleteToolTipText = $usedCount . ' product is used this category. Please delete that product before you delete this category';
					}
					if ($post->pro_mc_status == 1) {
						if ($usedCount > 0) {
							//$blockRunblock='<a href="javascript:;"><i class="fa fa-check" data-toggle="popover" data-html="true" title="Sorry! Can\'t Block" data-content="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
							$blockRunblock = '<a href="javascript:change_all_statusblk(' . $post->pro_mc_id . ');" disabled="disabled"><i class="fa fa-check tooltip-demo" data-html="true" title="' . $toolTipText . '"></i></a>';
							} else {
							// $blockRunblock='<a href="'.url("pro_cate_status").'/'.base64_encode($post->pro_mc_id).'/0/product'.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
							
							$blockRunblock = '<a href="javascript:individual_change_status(\'' . $post->pro_mc_id . '\',0);" id="statusLink_' . $post->pro_mc_id . '"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
						}
						} else {
						
						if ($usedCount > 0) {
							$toolTipTextUb = $usedCount . ' products are used in this category.if you UnBlock this category the products and subcategory also will be UnBlocked!';
							
							$blockRunblock = '<a href="javascript:change_all_unblock(\'' . $post->pro_mc_id . '\');" id="statusLink_' . $post->pro_mc_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="' . $toolTipTextUb . '" ></i></a>';
							
							} else {
							
							
							// $blockRunblock='<a href="'.url('pro_cate_status').'/'.base64_encode($post->pro_mc_id).'/1/product'.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock"></i></a>';
							
							$blockRunblock = '<a href="javascript:individual_change_status(\'' . $post->pro_mc_id . '\',1);" id="statusLink_' . $post->pro_mc_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
							
						}
						
						
					}
					if ($post->pro_added_by == 0) {
						$added_by = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN');
						} else {
						$added_by = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MERCHANT');
					}
					
					if ($allPrev == '1' || in_array('2', $Category)) {
						$editLink = '<a href="' . url('edit_product_category') . '/' . base64_encode($post->pro_mc_id) . '"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
						} else {
						$editLink = '--';
					}
					
					if ($allPrev == '1' || in_array('3', $Category)) {
						if ($usedCount > 0) {
							//$deleteLink='<a href="javascript:;"><i class="fa fa-trash" data-toggle="popover" title="Sorry! Can\'t Delete" data-content="'.$deleteToolTipText.'" data-trigger="hover"  disabled="disabled"></i></a>';
							$deleteLink = '<a href="javascript:;" disabled="disabled"><i class="fa fa-trash tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Delete</h4><div>' . $deleteToolTipText . '</div>"></i></a>';
							$chkName = '';
							$chkDisabled = 'disabled="disabled"';
							} else {
							// $deleteLink='<a href="'.url("pro_cate_status").'/'.base64_encode($post->pro_mc_id).'/2/product'.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>';
							
							$deleteLink = '<a href= "javascript:individual_change_status(\'' . $post->pro_mc_id . '\',2);" title="delete" class="tooltip-demo" id="statusLink_' . $post->pro_mc_id . '"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
							$chkName = 'chk[]';
							$chkDisabled = '';
						}
						} else {
						$deleteLink = '--';
						$chkName = '';
						$chkDisabled = '';
					}
					
					$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="' . $chkName . '" ' . $chkDisabled . ' value="' . $post->pro_mc_id . '">';
					$nestedData['SNo'] = ++$snoCount;
					$nestedData['CategoryName'] = $post->pro_mc_name;
					$nestedData['ManageSubCategory'] = '<a href="' . url('manage-subproduct') . '/' . base64_encode($post->pro_mc_id) . '"><button type="button" class="btn btn-light">' . ((Lang::has(Session::get('admin_lang_file') . '.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MNGE_SUBPRO_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MNGE_SUBPRO_CATE')) . '</button></a>';
					$nestedData['Edit'] = $editLink;
					$nestedData['Status'] = $blockRunblock;
					$nestedData['Delete'] = $deleteLink;
					$nestedData['AddedBy'] = $added_by;
					$data[] = $nestedData;
					
				}
			}
			
			$json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
			);
			
			echo json_encode($json_data);
			
		}
		
		/** add product category **/
		public function add_product_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_CATE_NAME');
				$this->validate($request, ['cate_name' => 'Required'], ['cate_name.required' => $err_msg]);
				
				$name = 'pro_mc_name';
				$old_name = Input::get('cate_name');
				$where = [$name => $old_name, 'pro_mc_type' => '2'];
				$check = check_name_exists('gr_proitem_maincategory', 'pro_mc_status', $where);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-product-category')->withErrors(['errors' => $msg]);
					
				}
				$entry['pro_mc_name'] = ucfirst($old_name);
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['pro_mc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				$entry = array_merge(array('pro_mc_type' => 2, 'pro_mc_status' => 1), $entry); //2 for product
				$insert = insertvalues('gr_proitem_maincategory', $entry);
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-product-category');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** edit restaurant category **/
		public function edit_product_category($id)
		{
			$id = base64_decode($id);
			$where = ['pro_mc_id' => $id];
			$get_cate_details = get_details('gr_proitem_maincategory', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EDIT_PRO_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EDIT_PRO_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EDIT_PRO_CATE');
			$where = ['pro_mc_type' => 2]; //2 for product
			$get_category_details = get_all_details('gr_proitem_maincategory', 'pro_mc_status', 10, 'desc', 'pro_mc_id', $where);
			return view('Admin.category.manage_product_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details)->with('cate_detail', $get_cate_details)->with('id', $id);
		}
		
		/** update country **/
		public function update_product_category()
		{
			if (Session::has('admin_id') == 1) {
				$name = 'pro_mc_name';
				$old_name = Input::get('cate_name');
				$id = Input::get('category_id');
				$check = DB::table('gr_proitem_maincategory')->select($name, 'pro_mc_id')->where('pro_mc_id', '<>', $id)->where('pro_mc_type', '=', '2')->where('pro_mc_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['pro_mc_name'] = ucfirst($old_name);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-product-category')->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['pro_mc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				$update = updatevalues('gr_proitem_maincategory', $entry, ['pro_mc_id' => Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-product-category');
				} else {
				return Redirect::to('admin-login');
			}
			
		}
		
		/** block/unblock category **/
		/*public function pro_cate_status($id,$status,$type)
			{
			$update = ['pro_mc_status' => $status];
			$where = ['pro_mc_id' => base64_decode($id)];
			$a = updatevalues('gr_proitem_maincategory',$update,$where);
			
			if($status == 1) //Active
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
			Session::flash('message',$msg);
			}
			if($status == 2) //Delete
			{
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
			Session::flash('message',$msg);
			}
			else   //block
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
			Session::flash('message',$msg);
			}
			return Redirect::back();
		}*/
		
		/** multiple block/unblock  category**/
		public function multi_pro_block()
		{
			$update = ['pro_mc_status' => Input::get('status')];
			$val = Input::get('val');
			$status = Input::get('status');
			$type = Input::get('type');
			//return count($val); exit;
			for ($i = 0; $i < count($val); $i++) {
				$where = ['pro_mc_id' => $val[$i]];				
				$a = updatevalues('gr_proitem_maincategory', $update, $where);
				
				DB::table('gr_product')->where('pro_category_id','=',$val[$i])->update(['pro_status'=>$status]);
				DB::table('gr_proitem_subcategory')->where('pro_main_id','=',$val[$i])->update(['pro_sc_status'=>$status]);
			}
			//echo Input::get('status'); exit;
			if (Input::get('status') == 1) //Active
			{
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UNBLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
			} elseif (Input::get('status') == 2) //Delete
			{
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELETE_SUCCESS');
				echo $msg;
			} elseif (Input::get('status') == 0)   //block
			{
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BLOCK_SUCCESS');
				echo $msg;
			}
		}
		
		
		/** import category file **/
		public function import_product_category(Request $request)
		{
			//echo 'inside'; exit;
			if (Session::has('admin_id') == 1) {
				
				$this->validate($request, array('upload_file' => 'required'));
				
				$mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
				if (!in_array($_FILES['upload_file']['type'], $mimes)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('manage-product-category')->withErrors(['upload_file' => $msg]);
				}
				
				$upload = $request->upload_file;
				// $filePath = $upload->getRealPath();
				$type = $request->cate_type;
				
				//echo $type;exit;
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function ($reader) {
				})->get();
				$headerRow = $data1->first()->keys()->toArray();
				//print_r($headerRow); exit;
				$errorArray = $data = array();
				/* check header  */
				$existing_header = ['s.no', 'category_name', 'status', 'added_by'];
				$diff_arr = array_diff($headerRow, $existing_header);
				if (!empty($diff_arr)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INVALID_COLUMN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INVALID_COLUMN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INVALID_COLUMN');
					Session::flash('import_err', str_replace('implodData', implode(',', $diff_arr), $msg));
					//Session::flash('import_err',"Invalid column name(s) : ".implode(',',$diff_arr).".Please upload valid data");
					Session::flash('popup', "open");
					return Redirect::back();
					} else {
					$data = $data1->toArray();
				}
				if (!empty($data)) {
					
					foreach ($data as $key => $value) {//print_r($value);
						if ($value['category_name'] != '') {
							$where = ['pro_mc_name' => $value['category_name'], 'pro_mc_type' => '2'];
							$check = check_name_exists('gr_proitem_maincategory', 'pro_mc_status', $where);
							if (count($check) > 0) {
								$msg = $value['category_name'] . ' - ';
								$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray, $msg);
								} else {
								$insert = array(
                                'pro_mc_name' => ucfirst($value['category_name']),
                                'pro_mc_type' => 2, // 2 for product
                                'pro_mc_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'pro_added_by' => '0'
								);
								
								
								//print_r($insert); echo '<hr>';
								$insertData = DB::table('gr_proitem_maincategory')->insert($insert);
							}
						}
					}
					//print_r($insert); exit;
					if (count($errorArray) > 0) {
						return Redirect::to('manage-product-category')->withErrors(['err_errors' => $errorArray]);
						} else {
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_IMPORT_SUCCESS');
						Session::flash('message', $msg);
						return Redirect::to('manage-product-category');
					}
					
				}
				return Redirect::to('manage-product-category')->with('message', 'Fill the data');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function import_item_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				
				$this->validate($request, array(
                'upload_file' => 'required'
				));
				
				$mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
				if (!in_array($_FILES['upload_file']['type'], $mimes)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('manage-item-category')->withErrors(['upload_file' => $msg]);
				}
				
				$upload = $request->upload_file;
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function ($reader) {
				})->get();
				$headerRow = $data1->first()->keys()->toArray();
				//print_r($headerRow); exit;
				$errorArray = $data = array();
				/* check header  */
				$existing_header = ['s.no', 'category_name', 'status', 'added_by'];
				$diff_arr = array_diff($headerRow, $existing_header);
				if (!empty($diff_arr)) {
					Session::flash('import_err', "Invalid column name(s) : " . implode(',', $diff_arr) . ".Please upload valid data");
					Session::flash('popup', "open");
					return Redirect::back();
					} else {
					$data = $data1->toArray();
				}
				if (!empty($data)) {
					
					foreach ($data as $key => $value) {
						if ($value['category_name'] != '') {
							$where = ['pro_mc_name' => $value['category_name'], 'pro_mc_type' => '1'];
							$check = check_name_exists('gr_proitem_maincategory', 'pro_mc_status', $where);
							
							if (count($check) > 0) {
								$msg = $value['category_name'] . ' - ';
								$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray, $msg);
								} else {
								$insert = array(
                                'pro_mc_name' => ucfirst($value['category_name']),
                                'pro_mc_type' => 1, // 1 for item
                                'pro_mc_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'pro_added_by' => '0'
								);
								
								
								//print_r($insert); echo '<hr>';
								$insertData = DB::table('gr_proitem_maincategory')->insert($insert);
							}
						}
					}
					//print_r($insert); exit;
					if (count($errorArray) > 0) {
						return Redirect::to('manage-item-category')->withErrors(['err_errors' => $errorArray]);
						} else {
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_IMPORT_SUCCESS');
						Session::flash('message', $msg);
						return Redirect::to('manage-item-category');
					}
					
				}
				return Redirect::to('manage-item-category')->with('message', 'Fill the data');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** download restaurant category **/
		public function download_product_category($type, $sample = null)
		{
			if ($sample == 'sample_file') {
				$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName', 'pro_mc_status', 'pro_added_by')->where(['pro_mc_type' => '2', 'pro_added_by' => '0'])->where('pro_mc_status', '!=', '2')->limit(4)->get()->toarray();
				} else {
				$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName', 'pro_mc_status', 'pro_added_by')->where('pro_mc_type', '=', '2')->where('pro_mc_status', '!=', '2')->get()->toarray();
			}
			// print_r($data); exit;
			return Excel::create('Product Category lists', function ($excel) use ($data) {
				$excel->sheet('category_list', function ($sheet) use ($data) {
					$sheet->cell('A1', function ($cell) {
						$cell->setValue('S.No');
					});
					$sheet->cell('B1', function ($cell) {
						$cell->setValue('Category Name');
					});
					$sheet->cell('C1', function ($cell) {
						$cell->setValue('Status');
					});
					$sheet->cell('D1', function ($cell) {
						$cell->setValue('Added By');
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function ($row) {
						$row->setBackground('#CCCCCC');
					});
					foreach ($data as $key => $value) {
						
						if($value->pro_mc_status=='0')
						{
							$status = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_INACTIVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INACTIVE');
						}
						else
						{
							$status = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACTIVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ACTIVE');
						}
						
						if($value->pro_added_by=='0')
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						}
						$i = $key + 2;
						$sheet->cell('A' . $i, $i - 1); //print serial no
						$sheet->cell('B' . $i, $value->CategoryName);
						$sheet->cell('C'.$i, $status);
						$sheet->cell('D'.$i, $addedByRes);
					}
				});
			})->download($type);
			
		}
		
		public function download_item_category($type, $sample = null)
		{
			if ($sample == 'sample_file') {
				$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName', 'pro_mc_status', 'pro_added_by')->where(['pro_mc_type' => '1', 'pro_added_by' => '0'])->where('pro_mc_status', '!=', '2')->limit(4)->get()->toarray();
				} else {
				$data = DB::table('gr_proitem_maincategory')->select('pro_mc_name as CategoryName', 'pro_mc_status', 'pro_added_by')->where('pro_mc_type', '=', '1')->where('pro_mc_status', '!=', '2')->get()->toarray();
			}
			
			// print_r($data); exit;
			return Excel::create('Item Category lists', function ($excel) use ($data) {
				$excel->sheet('category_list', function ($sheet) use ($data) {
					$sheet->cell('A1', function ($cell) {
						$cell->setValue('S.No');
					});
					$sheet->cell('B1', function ($cell) {
						$cell->setValue('Category Name');
					});
					$sheet->cell('C1', function ($cell) {
						$cell->setValue('Status');
					});
					$sheet->cell('D1', function ($cell) {
						$cell->setValue('Added By');
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function ($row) {
						$row->setBackground('#CCCCCC');
					});
					foreach ($data as $key => $value) {
						
						
						if($value->pro_mc_status=='0')
						{
							$status = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_INACTIVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INACTIVE');
						}
						else
						{
							$status = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACTIVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ACTIVE');
						}
						
						if($value->pro_added_by=='0')
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						}
						$i = $key + 2;
						$sheet->cell('A' . $i, $i - 1); //print serial no
						$sheet->cell('B' . $i, $value->CategoryName);
						$sheet->cell('C'.$i, $status);
						$sheet->cell('D'.$i, $addedByRes);
					}
				});
			})->download($type);
			
		}
		
		/** manage product sub category **/
		public function manage_subproduct(Request $request, $id)
		{
			if (Session::has('admin_id') == 1) {
				$main_id = base64_decode($id);
				
				/* get category list and ajax search starts */
				if ($request->ajax()) {
					$columns = array(
                    0 => 'pro_sc_id',
                    1 => 'pro_sc_id',
                    2 => 'pro_sc_name',
                    3 => 'pro_sc_id',
                    4 => 'pro_sc_status',
                    5 => 'pro_sc_id',
                    6 => 'pro_added_by'
					);
					/* get total count */
					$totalData = Admin::get_categoryLists_count('gr_proitem_subcategory', ['pro_sc_type' => '2', 'pro_main_id' => $main_id], 'pro_sc_status');
					
					$totalFiltered = $totalData;
					/* eof get total count */
					$limit = $request->input('length');
					$start = $request->input('start');
					$order = $columns[$request->input('order.0.column')];
					$dir = $request->input('order.0.dir');
					$search = trim($request->catName_search);
					$addedBySearch = trim($request->addedBy_search);
					$catStatus_search = trim($request->catStatus_search);
					if ($search == '' && $addedBySearch == '' && $catStatus_search == '') {
						$posts = DB::table('gr_proitem_subcategory')
                        ->where('pro_sc_status', '<>', '2')
                        ->where(['pro_main_id' => $main_id, 'pro_sc_type' => '2'])
                        ->orderBy($order, $dir)->skip($start)->take($limit)
                        ->get();
						} else {
						$q = array();
						$sql = DB::table('gr_proitem_subcategory')->where('pro_sc_status', '<>', '2')->where(['pro_main_id' => $main_id, 'pro_sc_type' => '2']);
						if ($search != '') {
							$q = $sql->where('pro_sc_name', 'LIKE', '%' . $search . '%');
						}
						if ($catStatus_search != '') {
							$q = $sql->where(['pro_sc_status' => $catStatus_search]);
						}
						if ($addedBySearch != '') {
							$q = $sql->where(['pro_added_by' => $addedBySearch]);
						}
						$totalFiltered = $q->count();
						$q = $sql->orderBy($order, $dir)->skip($start)->take($limit);
						$posts = $q->get();
						
					}
					$data = array();
					if (!empty($posts)) {
						$snoCount = $start;
						foreach ($posts as $post) {
							$getUsedProduct = getProductsUsedThisSubcategory($post->pro_sc_id, 1);
							$usedProduct = $getUsedProduct[0]->usedProduct;
							$usedCount = $getUsedProduct[0]->usedCount;
							if ($usedCount > 1) {
								$toolTipText = $usedCount . ' products are used this subcategory.if you block this subcategory those products also will be Blocked! ';
								$deleteToolTipText = $usedCount . ' products are used this subcategory. Please delete those products before you delete this subcategory';
								} else {
								$toolTipText = $usedCount . ' product is used this subcategory.if you block this subcategory the product also will be Blocked!';
								$deleteToolTipText = $usedCount . ' product is used this subcategory. Please delete that product before you delete this subcategory';
							}
							if ($post->pro_sc_status == 1) {
								if ($usedCount > 0) {
									//$blockRunblock='<a href="javascript:;"><i class="fa fa-check" data-toggle="popover" data-html="true" title="Sorry! Can\'t Block" data-content="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
									$blockRunblock = '<a href="javascript:change_all_statusblk(\'' . $post->pro_sc_id . '\');" disabled="disabled"><i class="fa fa-check tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Block</h4><div>' . $toolTipText . '</div>"></i></a>';
									} else {
									$blockRunblock = '<a href="' . url("sub_cate_status") . '/' . base64_encode($post->pro_sc_id) . '/0/product' . '"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
									
									// $blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_sc_id.'\',0);" id="statusLink_'.$post->pro_sc_id.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
								}
								
								} else {
								
								
								if ($usedCount > 0) {
									
									$toolTipTextUb = $usedCount . ' products are used this subcategory.if you Unblock this subcategory those products also will be UnBlocked! ';
									
									$blockRunblock = '<a href="javascript:change_all_unblock(\'' . $post->pro_sc_id . '\');" id="statusLink_' . $post->pro_sc_id . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="' . $toolTipTextUb . '" ></i></a>';
									
									} else {
									
									$blockRunblock = '<a href="' . url("sub_cate_status") . '/' . base64_encode($post->pro_sc_id) . '/1/product' . '"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock"></i></a>';
									
									// $blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_sc_id.'\',1);" id="statusLink_'.$post->pro_sc_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
								}
								
							}
							if ($usedCount > 0) {
								//$deleteLink='<a href="javascript:;"><i class="fa fa-trash" data-toggle="popover" title="Sorry! Can\'t Delete" data-content="'.$deleteToolTipText.'" data-trigger="hover"  disabled="disabled"></i></a>';
								$deleteLink = '<a href="javascript:;" disabled="disabled"><i class="fa fa-trash tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Delete</h4><div>' . $deleteToolTipText . '</div>"></i></a>';
								$chkName = '';
								$chkDisabled = 'disabled="disabled"';
								} else {
								$deleteLink = '<a href="' . url("sub_cate_status") . '/' . base64_encode($post->pro_sc_id) . '/2/product' . '"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>';
								// $deleteLink = '<a href= "javascript:individual_change_status(\''.$post->pro_sc_id.'\',2);" title="delete" class="tooltip-demo" id="statusLink_'.$post->pro_sc_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
								$chkName = 'chk[]';
								$chkDisabled = '';
							}
							if ($post->pro_added_by == 0) {
								$added_by = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN');
								} else {
								$added_by = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MERCHANT');
							}
							
							$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="' . $chkName . '" ' . $chkDisabled . ' value="' . $post->pro_sc_id . '">';
							$nestedData['SNo'] = ++$snoCount;
							$nestedData['CategoryName'] = $post->pro_sc_name;
							$nestedData['Edit'] = '<a href="' . url("edit_sub_category") . '/' . base64_encode($post->pro_sc_id) . '/' . base64_encode($main_id) . '"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
							$nestedData['Status'] = $blockRunblock;
							$nestedData['Delete'] = $deleteLink;
							$nestedData['AddedBy'] = $added_by;//$added_by;
							$data[] = $nestedData;
						}
					}
					$json_data = array(
                    "draw" => intval($request->input('draw')),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
					);
					return $json_data;
				}
				/* get category lists and ajax search ends */
				
				$categoryName = DB::table('gr_proitem_maincategory')->select('pro_mc_name')->where('pro_mc_id', '=', $main_id)->first()->pro_mc_name;
				//echo $categoryName; exit;
				$where = ['pro_sc_type' => 2, 'pro_main_id' => $main_id]; //2 for product
				
				$get_category_details = get_all_details('gr_proitem_subcategory', 'pro_sc_status', 10, 'desc', 'pro_sc_id', $where, 'gr_proitem_maincategory', 'pro_mc_id', 'pro_main_id');
				//print_r($get_category_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SUB_CATE_LIST')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SUB_CATE_LIST') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SUB_CATE_LIST');
				$page_title = $categoryName . '\'s ' . $page_title;
				
				//echo $page_title; exit;
				return view('Admin.category.manage_sub_category')->with('pagetitle', $page_title)->with('main_id', $main_id);
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** edit sub product  category **/
		public function edit_sub_category($id, $main_id)
		{
			$id = base64_decode($id);
			$main_id = base64_decode($main_id);
			//echo $id.'/'.$main_id; exit;
			$where = ['pro_sc_id' => $id];
			$get_cate_details = get_details('gr_proitem_subcategory', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EDIT_SUB_PRO_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EDIT_SUB_PRO_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EDIT_SUB_PRO_CATE');
			$where = ['pro_sc_type' => 2, 'pro_main_id' => $main_id]; //2 for product
			$get_category_details = get_all_details('gr_proitem_subcategory', 'pro_sc_status', 10, 'desc', 'pro_sc_id', $where, 'gr_proitem_maincategory', 'pro_mc_id', 'pro_main_id');
			return view('Admin.category.manage_sub_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details)->with('cate_detail', $get_cate_details)->with('id', $id)->with('main_id', $main_id);
		}
		
		/** update country **/
		public function update_sub_category()
		{
			if (Session::has('admin_id') == 1) {
				$name = 'pro_sc_name';
				$old_name = Input::get('cate_name');
				$id = Input::get('category_id');
				$main_id = Input::get('main_id');
				$cate_type = Input::get('cate_type');
				$check = DB::table('gr_proitem_subcategory')->select($name, 'pro_sc_id')->Join('gr_proitem_maincategory', 'gr_proitem_maincategory.pro_mc_id', '=', 'gr_proitem_subcategory.pro_main_id')->where('gr_proitem_maincategory.pro_mc_name', '=', $old_name)->where('pro_sc_id', '<>', $id)->where('pro_main_id', '=', $main_id)->where('pro_sc_type', '=', $cate_type)->where('pro_sc_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['pro_sc_name'] = $old_name;
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-subproduct/' . base64_encode($id))->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['pro_sc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				$update = updatevalues('gr_proitem_subcategory', $entry, ['pro_sc_id' => Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				Session::flash('message', $msg);
				//return Redirect::back();
				return Redirect::to('manage-subproduct/' . base64_encode($main_id));
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** block/unblock category **/
		/*public function sub_cate_status($id,$status,$type)
			{
			$update = ['pro_sc_status' => $status];
			$where = ['pro_sc_id' => base64_decode($id)];
			$a = updatevalues('gr_proitem_subcategory',$update,$where);
			
			if($status == 1) //Active
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
			Session::flash('message',$msg);
			}
			if($status == 2) //Delete
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
			Session::flash('message',$msg);
			}
			else   //block
			{
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
			Session::flash('message',$msg);
			}
			return Redirect::back();
		}*/
		
		
		/** multiple block/unblock  category**/
		public function multi_sub_block()
		{
			$update = ['pro_sc_status' => Input::get('status')];
			$val = Input::get('val');
			$status = Input::get('status');
			$type = Input::get('type');
			//return count($val); exit;
			for ($i = 0; $i < count($val); $i++) {
				$where = ['pro_sc_id' => $val[$i]];
				$a = updatevalues('gr_proitem_subcategory', $update, $where);
				DB::table('gr_product')->where('pro_sub_cat_id','=',$val[$i])->update(['pro_status'=>$status]);
			}	
			//echo Input::get('status'); exit;
			if (Input::get('status') == 1) //Active
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UNBLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			} elseif (Input::get('status') == 2) //Delete
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELETE_SUCCESS');
				echo $msg;
			} elseif (Input::get('status') == 0)   //block
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BLOCK_SUCCESS');
				echo $msg;
			}
		}
		
		/** add sub product category **/
		public function add_sub_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$name = 'pro_sc_name';
				$old_name = Input::get('cate_name');
				$main_id = Input::get('main_id');
				$cate_type = Input::get('cate_type');
				$check = DB::table('gr_proitem_subcategory')->where('pro_main_id', '=', $main_id)->where('pro_sc_type', '=', $cate_type)->where('pro_sc_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['pro_sc_name'] = $old_name;
				
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::back()->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['pro_sc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				$entry = array_merge(array('pro_main_id' => $main_id,
				'pro_sc_type' => $cate_type,
				'pro_sc_status' => 1)
                , $entry);
				//print_r($entry); exit;
				$update = insertvalues('gr_proitem_subcategory', $entry);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::back();
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** import category file **/
		public function import_sub_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$this->validate($request, array(
                'upload_file' => 'required'
				));
				
				$mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
				if (!in_array($_FILES['upload_file']['type'], $mimes)) {
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FORMAT_INCORRECT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('manage-product-category')->withErrors(['upload_file' => $msg]);
				}
				
				$upload = $request->upload_file;
				$main = $request->main_id;
				$type = $request->cate_type;   // 2 -product,1 item
				//echo $upload.'/'.$main.'/'.$type; exit;
				// echo $type;exit;
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath();
				$data1 = Excel::load($path, function ($reader) {
				})->get();
				$headerRow = $data1->first()->keys()->toArray();
				//print_r($headerRow); exit;
				$errorArray = $data = array();
				/* check header  */
				$existing_header = ['s.no', 'sub_category_name', 'main_category_name', 'status', 'added_by'];
				$diff_arr = array_diff($headerRow, $existing_header);
				if (!empty($diff_arr)) {
					Session::flash('import_err', "Invalid column name(s) : " . implode(',', $diff_arr) . ".Please upload valid data");
					Session::flash('popup', "open");
					return Redirect::back();
					} else {
					$data = $data1->toArray();
				}
				if (!empty($data)) {
					
					foreach ($data as $key => $value) {
						if ($value['sub_category_name'] != '') {
							$exist_cate_id = get_main_cate($value['main_category_name'], $type);
							if (empty($exist_cate_id) === true) {
								$msg = $value['main_category_name'] . ' - ';
								$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_NT_EXIST')) ? trans(Session::get('admin_lang_file') . '.ADMIN_NT_EXIST') : trans($this->ADMIN_LANGUAGE . '.ADMIN_NT_EXIST');
								array_push($errorArray, $msg);
								} else {
								$main = $exist_cate_id->pro_mc_id;
							}
							
							$where = ['pro_sc_name' => $value['sub_category_name'], 'pro_sc_type' => $type, 'pro_main_id' => $main];
							$check = check_name_exists('gr_proitem_subcategory', 'pro_sc_status', $where);
							
							// echo $exist_cate_id;
							if (count($check) > 0 || (empty($exist_cate_id) === true)) {
								$msg = $value['sub_category_name'] . ' - ';
								$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
								array_push($errorArray, $msg);
								} else {
								$insert = array(
                                'pro_sc_name' => ucfirst($value['sub_category_name']),
                                'pro_sc_type' => $type, // 1 - for restaurant
                                'pro_sc_status' => ($value['status'] == 'Active') ? '1' : '0',
                                'pro_added_by' => '0',
                                'pro_main_id' => $main
								);
								
								//print_r($insert); echo '<hr>';
								$insertData = DB::table('gr_proitem_subcategory')->insert($insert);
							}
						}
					}
					//exit;
					if (count($errorArray) > 0) {
						return Redirect::back()->withErrors(['err_errors' => $errorArray]);
						} else {
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_IMPORT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_IMPORT_SUCCESS');
						Session::flash('message', $msg);
						return Redirect::back();
					}
					/*if(!empty($insert)){
						
						
						if ($insertData) {
						$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMPORT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_IMPORT_SUCCESS');
						Session::flash('message',$msg);
						return Redirect::back();
						
						}else {
						
						$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_IMPORT_NOT_SUCCESSS')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMPORT_NOT_SUCCESSS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_IMPORT_NOT_SUCCESSS');
						Session::flash('message',$msg);
						return Redirect::back();
						}
					}*/
				}
				return Redirect::back()->with('message', 'Fill the data');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** download restaurant category **/
		public function download_sub_category($type, $title, $main, $sample = '')
		{
			if ($sample == 'sample_file') {
				$data = DB::table('gr_proitem_subcategory')
                ->select('pro_sc_name as CategoryName', 'pro_mc_name as MainCat', 'gr_proitem_subcategory.pro_sc_status', 'gr_proitem_subcategory.pro_added_by')
                ->join('gr_proitem_maincategory', 'gr_proitem_maincategory.pro_mc_id', '=', 'gr_proitem_subcategory.pro_main_id')
                ->where(['pro_sc_type' => $title, 'gr_proitem_subcategory.pro_added_by' => '0', 'pro_main_id' => base64_decode($main)])->where('pro_sc_status', '!=', '2')->limit(4)->get()->toarray();
				} else {
				$data = DB::table('gr_proitem_subcategory')
                ->select('pro_sc_name as CategoryName', 'pro_mc_name as MainCat', 'gr_proitem_subcategory.pro_sc_status', 'gr_proitem_subcategory.pro_added_by')
                ->join('gr_proitem_maincategory', 'gr_proitem_maincategory.pro_mc_id', '=', 'gr_proitem_subcategory.pro_main_id')
                ->where('pro_sc_type', '=', $title)->where('pro_sc_status', '!=', '2')->where('pro_main_id', '=', base64_decode($main))->get()->toarray();
			}
			
			//print_r($data); exit;
			return Excel::create('Product Sub Category lists', function ($excel) use ($data) {
				$excel->sheet('category_list', function ($sheet) use ($data) {
					$sheet->cell('A1', function ($cell) {
						$cell->setValue('S.No');
					});
					$sheet->cell('B1', function ($cell) {
						$cell->setValue('Main Category Name');
					});
					$sheet->cell('C1', function ($cell) {
						$cell->setValue('Sub Category Name');
					});
					$sheet->cell('D1', function ($cell) {
						$cell->setValue('Status');
					});
					$sheet->cell('E1', function ($cell) {
						$cell->setValue('Added By');
					});
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function ($row) {
						$row->setBackground('#CCCCCC');
					});
					foreach ($data as $key => $value) {
						
						
						if($value->pro_sc_status=='0')
						{
							$status = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_INACTIVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INACTIVE');
						}
						else
						{
							$status = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ACTIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACTIVE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ACTIVE');
						}
						
						if($value->pro_added_by=='0') { $addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN'); } else { $addedByRes = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT'); }
						$i = $key + 2;
						$sheet->cell('A' . $i, $i - 1); //print serial no
						$sheet->cell('B' . $i, $value->MainCat);
						$sheet->cell('C' . $i, $value->CategoryName);
						$sheet->cell('D'.$i, $status);
						$sheet->cell('E'.$i, $addedByRes);
					}
				});
			})->download($type);
			
		}
		
		/** Item main category **/
		
		public function manage_item_category()
		{
			if (Session::has('admin_id') == 1) {
				$where = ['pro_mc_type' => 1]; //2 for product , 1 for main category
				$get_category_details = get_all_details('gr_proitem_maincategory', 'pro_mc_status', 10, 'desc', 'pro_mc_id', $where);
				//print_r($get_country_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MNGE_ITEM_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MNGE_ITEM_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MNGE_ITEM_CATE');
				
				return view('Admin.category.manage_item_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details);
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function item_category_list_ajax(Request $request)
		{
			
			$columns = array(
            0 => 'pro_mc_id',
            1 => 'pro_mc_id',
            2 => 'pro_mc_name',
            3 => 'pro_mc_id',
            4 => 'pro_mc_id',
            5 => 'pro_mc_status',
            6 => 'pro_mc_id',
            7 => 'pro_added_by'
			);
			/*To get Total count */
			$totalData = Admin::get_categoryLists_count('gr_proitem_maincategory',['pro_mc_type' => '1'],'pro_mc_status');
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
			
			/** FOR SUBADMIN PRIVILAGES **/
			$allPrev = Config('allPrev');
			$privileges = Config('privileges');
			extract($privileges);
			/** END FOR SUBADMIN PRIVILAGES **/
			
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				foreach ($posts as $post)
				{
					$getUsedProduct = getProductsUsedThisCategory($post->pro_mc_id,2);
					$usedProduct = $getUsedProduct[0]->usedProduct;
					$usedCount = $getUsedProduct[0]->usedCount;
					if($usedCount > 1)
					{
						$toolTipText = $usedCount.' items are used this category.if you block this category those items and subcategory also will be Blocked!';
						$deleteToolTipText = $usedCount.' items are used this category. Please delete those items before you delete this category';
					}
					else
					{
						$toolTipText = $usedCount.' item is used this category.if you block this category the item and subcategory also will be Blocked!';
						$deleteToolTipText = $usedCount.' item is used this category. Please delete that item before you delete this category';
					}
					if($post->pro_mc_status==1)
					{
						if($usedCount > 0)
						{
							//$blockRunblock='<a href="javascript:;"><i class="fa fa-check" data-toggle="popover" data-html="true" title="Sorry! Can\'t Block" data-content="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
							$blockRunblock = '<a href="javascript:change_all_statusblk(\''.$post->pro_mc_id.'\');" disabled="disabled"><i class="fa fa-check tooltip-demo" data-html="true" title="'.$toolTipText.'"></i></a>';
						}
						else
						{
							// $blockRunblock='<a href="'.url("pro_cate_status").'/'.base64_encode($post->pro_mc_id).'/0/item'.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
							$blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_mc_id.'\',0);" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
						}
					}
					else
					{
						
						
						if($usedCount > 0)
						{
							
							$toolTipTextUb = $usedCount.' items are used this category.if you unblock this category those items and subcategory also will be UnBlocked!';
							
							$blockRunblock = '<a href="javascript:change_all_unblock(\''.$post->pro_mc_id.'\');" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$toolTipTextUb.'" ></i></a>';
							
							}else{
							
							// $blockRunblock='<a href="'.url('pro_cate_status').'/'.base64_encode($post->pro_mc_id).'/1/item'.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to nblock"></i></a>';
							
							$blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_mc_id.'\',1);" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
						}
						
						
					}
					if($post->pro_added_by == 0)
					{
						$added_by = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
					}
					else
					{
						$added_by = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
					}
					
					if ($allPrev == '1' || in_array('2', $Category)){
						$editLink = '<a href="'.url('edit_item_category').'/'.base64_encode($post->pro_mc_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
						} else {
						$editLink = '--';
					}
					
					if ($allPrev == '1' || in_array('3', $Category)){
						if($usedCount > 0)
						{
							//$deleteLink='<a href="javascript:;"><i class="fa fa-trash" data-toggle="popover" title="Sorry! Can\'t Delete" data-content="'.$deleteToolTipText.'" data-trigger="hover"  disabled="disabled"></i></a>';
							$deleteLink = '<a href="javascript:;" disabled="disabled"><i class="fa fa-trash tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Delete</h4><div>'.$deleteToolTipText.'</div>"></i></a>';
							$chkName = '';
							$chkDisabled = 'disabled="disabled"';
						}
						else
						{
							// $deleteLink='<a href="'.url("pro_cate_status").'/'.base64_encode($post->pro_mc_id).'/2/item'.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>';
							
							$deleteLink = '<a href= "javascript:individual_change_status(\''.$post->pro_mc_id.'\',2);" title="delete" class="tooltip-demo" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
							$chkName = 'chk[]';
							$chkDisabled='';
						}
						} else {
						$deleteLink = '--';
						$chkName = '';
						$chkDisabled='';
					}
					
					
					$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass"  name="'.$chkName.'" '.$chkDisabled.' value="'.$post->pro_mc_id.'">';
					$nestedData['SNo'] = ++$snoCount;
					$nestedData['CategoryName'] = $post->pro_mc_name;
					$nestedData['ManageSubCategory'] = '<a href="'.url('manage-subitem').'/'.base64_encode($post->pro_mc_id).'"><button type="button" class="btn btn-light">'.((Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_SUBPRO_CATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MNGE_SUBPRO_CATE')).'</button></a>';
					$nestedData['Edit'] = $editLink;
					$nestedData['Status'] = $blockRunblock;
					$nestedData['Delete'] = $deleteLink;
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
		
		//    public function item_category_list_ajax(Request $request)
		//    {
		//
		//        $columns = array(
		//            0 => 'pro_mc_id',
		//            1 => 'pro_mc_id',
		//            2 => 'pro_mc_name',
		//            3 => 'pro_mc_id',
		//            4 => 'pro_mc_id',
		//            5 => 'pro_mc_status',
		//            6 => 'pro_mc_id',
		//            7 => 'pro_added_by'
		//        );
		//        /*To get Total count */
		//        $totalData = Admin::get_categoryLists_count('gr_proitem_maincategory',['pro_mc_type' => '1'],'pro_mc_status');
		//        $totalFiltered = $totalData;
		//        //echo $totalData; exit;
		//        /*EOF get Total count */
		//        $limit = $request->input('length');
		//        $start = $request->input('start');
		//        $order = $columns[$request->input('order.0.column')];
		//        $dir = $request->input('order.0.dir');
		//        $search = trim($request->catName_search);
		//        $addedBySearch = trim($request->addedBy_search);
		//        $catStatus_search = trim($request->catStatus_search);
		//
		//        if($search == '' && $addedBySearch =='' && $catStatus_search == '')
		//        {
		//            $posts = DB::table('gr_proitem_maincategory')->where('pro_mc_type','1')->where('pro_mc_status','!=','2')->orderBy($order,$dir)->skip($start)->take($limit)->get();
		//        }
		//        else {
		//            $q = array();
		//            //DB::connection()->enableQueryLog();
		//            $sql = DB::table('gr_proitem_maincategory')->where('pro_mc_type','1')->where('pro_mc_status','!=','2');
		//
		//            if($search != '')
		//            {
		//                $q = $sql->where('pro_mc_name','LIKE','%'.$search.'%');
		//            }
		//            if($addedBySearch == '0')
		//            {
		//                $q= $sql->where('pro_added_by','=',$addedBySearch);
		//            }
		//            if($addedBySearch == '1')
		//            {
		//                $q= $sql->where('pro_added_by','>','0');
		//            }
		//
		//            if($catStatus_search != '')
		//            {
		//                $q = $sql->where('pro_mc_status','=',$catStatus_search);
		//            }
		//
		//            //$query = DB::getQueryLog();
		//            //print_r($query);
		//            //exit;
		//            $totalFiltered = $q->count();
		//            $posts =  $q->orderBy($order,$dir)->skip($start)->take($limit)->get();
		//        }
		//
		//        /** FOR SUBADMIN PRIVILAGES **/
		//        $allPrev = Config('allPrev');
		//        $privileges = Config('privileges');
		//        extract($privileges);
		//        /** END FOR SUBADMIN PRIVILAGES **/
		//
		//        $data = array();
		//        if(!empty($posts))
		//        {
		//            $snoCount = $start;
		//            foreach ($posts as $post)
		//            {
		//                $getUsedProduct = getProductsUsedThisCategory($post->pro_mc_id,2,'delete');
		//                $usedProduct = $getUsedProduct[0]->usedProduct;
		//                $usedCount = $getUsedProduct[0]->usedCount;
		//                if($usedCount > 1)
		//                {
		//                    $toolTipText = $usedCount.' items are used this category.if you block this category those items and subcategory also will be Blocked!';
		//                    $deleteToolTipText = $usedCount.' items are used this category. Please delete those items before you delete this category';
		//                }
		//                else
		//                {
		//                    $toolTipText = $usedCount.' item is used this category.if you block this category the item and subcategory also will be Blocked!';
		//                    $deleteToolTipText = $usedCount.' item is used this category. Please delete that item before you delete this category';
		//                }
		//                if($post->pro_mc_status==1)
		//                {
		//                    if($usedCount > 0)
		//                    {
		//                        //$blockRunblock='<a href="javascript:;"><i class="fa fa-check" data-toggle="popover" data-html="true" title="Sorry! Can\'t Block" data-content="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
		//                        $blockRunblock = '<a href="javascript:change_all_statusblk(\''.$post->pro_mc_id.'\');" disabled="disabled"><i class="fa fa-check tooltip-demo" data-html="true" title="'.$toolTipText.'"></i></a>';
		//                    }
		//                    else
		//                    {
		//                        // $blockRunblock='<a href="'.url("pro_cate_status").'/'.base64_encode($post->pro_mc_id).'/0/item'.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
		//                        $blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_mc_id.'\',0);" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
		//                    }
		//                }
		//                else
		//                {
		//
		//
		//                    if($usedCount > 0)
		//                    {
		//
		//                        $toolTipTextUb = $usedCount.' items are used this category.if you unblock this category those items and subcategory also will be UnBlocked!';
		//
		//                        $blockRunblock = '<a href="javascript:change_all_unblock(\''.$post->pro_mc_id.'\');" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$toolTipTextUb.'" ></i></a>';
		//
		//                    }else{
		//
		//                        // $blockRunblock='<a href="'.url('pro_cate_status').'/'.base64_encode($post->pro_mc_id).'/1/item'.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to nblock"></i></a>';
		//
		//                        $blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_mc_id.'\',1);" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
		//                    }
		//
		//
		//                }
		//                if($post->pro_added_by == 0)
		//                {
		//                    $added_by = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
		//                }
		//                else
		//                {
		//                    $added_by = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
		//                }
		//
		//                if ($allPrev == '1' || in_array('2', $Category)){
		//                    $editLink = '<a href="'.url('edit_item_category').'/'.base64_encode($post->pro_mc_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
		//                } else {
		//                    $editLink = '--';
		//                }
		//
		//                if ($allPrev == '1' || in_array('3', $Category)){
		//                    if($usedCount > 0)
		//                    {
		//                        //$deleteLink='<a href="javascript:;"><i class="fa fa-trash" data-toggle="popover" title="Sorry! Can\'t Delete" data-content="'.$deleteToolTipText.'" data-trigger="hover"  disabled="disabled"></i></a>';
		//                        $deleteLink = '<a href="javascript:;" disabled="disabled"><i class="fa fa-trash tooltip-demo" data-html="true" title="<h4>Sorry! Can\'t Delete</h4><div>'.$deleteToolTipText.'</div>"></i></a>';
		//                        $chkName = '';
		//                        $chkDisabled = 'disabled="disabled"';
		//                    }
		//                    else
		//                    {
		//                        // $deleteLink='<a href="'.url("pro_cate_status").'/'.base64_encode($post->pro_mc_id).'/2/item'.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" title="Delete"></i></a>';
		//
		//                        $deleteLink = '<a href= "javascript:individual_change_status(\''.$post->pro_mc_id.'\',2);" title="delete" class="tooltip-demo" id="statusLink_'.$post->pro_mc_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
		//                        $chkName = 'chk[]';
		//                        $chkDisabled='';
		//                    }
		//                } else {
		//                    $deleteLink = '--';
		//                    $chkName = '';
		//                    $chkDisabled='';
		//                }
		//
		//
		//                $nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass"  name="'.$chkName.'" '.$chkDisabled.' value="'.$post->pro_mc_id.'">';
		//                $nestedData['SNo'] = ++$snoCount;
		//                $nestedData['CategoryName'] = $post->pro_mc_name;
		//                $nestedData['ManageSubCategory'] = '<a href="'.url('manage-subitem').'/'.base64_encode($post->pro_mc_id).'"><button type="button" class="btn btn-light">'.((Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_SUBPRO_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_SUBPRO_CATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MNGE_SUBPRO_CATE')).'</button></a>';
		//                $nestedData['Edit'] = $editLink;
		//                $nestedData['Status'] = $blockRunblock;
		//                $nestedData['Delete'] = $deleteLink;
		//                $nestedData['AddedBy'] = $added_by;
		//                $data[] = $nestedData;
		//
		//            }
		//        }
		//
		//        $json_data = array(
		//            "draw"            => intval($request->input('draw')),
		//            "recordsTotal"    => intval($totalData),
		//            "recordsFiltered" => intval($totalFiltered),
		//            "data"            => $data
		//        );
		//
		//        echo json_encode($json_data);
		//
		//    }
		
		/** add item category **/
		public function add_item_category(Request $request)
		{
			
			
			if (Session::has('admin_id') == 1) {
				
				
				$err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_CATE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_CATE_NAME');
				$this->validate($request, ['cate_name' => 'Required',
                // 'cate_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=65,max_width=80,min_height=65,max_height=80',
				], ['cate_name.required' => $err_msg]);
				
				$name = 'pro_mc_name';
				$old_name = Input::get('cate_name');
				$where = [$name => $old_name, 'pro_mc_type' => '1'];
				$check = check_name_exists('gr_proitem_maincategory', 'pro_mc_status', $where);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-item-category')->withErrors(['errors' => $msg]);
					
				}
				$entry['pro_mc_name'] = ucfirst($old_name);
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$validatoradv = Validator::make($request->all(), [
                        'cate_name_'.$Lang->lang_code => 'required'
						]);
						if($validatoradv->fails()){
							return redirect('manage-item-category')->withErrors($validatoradv)->withInput();
							}else{
							$entry['pro_mc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
						}
						
						
					}
				}
				
				$destinationPath = public_path('images/category/restaurant');
				
				
				// if(request()->cate_image != ''){
				//     /* deleting the existing image*/
				//     $imagePath = public_path('images/category/restaurant').input::get('cate_image');
				//     if(File::exists($imagePath)){
				//         $delete = File::delete($imagePath);
				//     }
				//     $cate_img = 'res_'.rand().'.'.request()->cate_image->getClientOriginalExtension();
				
				//     $category_img = Image::make(request()->cate_image->getRealPath())->resize(75,75);
				//     $category_img->save($destinationPath.'/'.$cate_img,80);
				
				//  }else{
				//     $cate_img = input::get('cate_image');
				//  }
				
				// $entry['pro_mc_image'] = $cate_img;
				$entry = array_merge(array('pro_mc_type' => 1, 'pro_mc_status' => 1), $entry); //2 for item
				$insert = insertvalues('gr_proitem_maincategory', $entry);
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-item-category');
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** edit restaurant category **/
		public function edit_item_category($id)
		{
			$id = base64_decode($id);
			$where = ['pro_mc_id' => $id];
			$get_cate_details = get_details('gr_proitem_maincategory', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EDIT_ITEM_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EDIT_ITEM_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EDIT_ITEM_CATE');
			
			$where = ['pro_mc_type' => 1]; //1 for item
			$get_category_details = get_all_details('gr_proitem_maincategory', 'pro_mc_status', 10, 'desc', 'pro_mc_id', $where);
			return view('Admin.category.manage_item_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details)->with('cate_detail', $get_cate_details)->with('id', $id);
		}
		
		/** update country **/
		public function update_item_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$name = 'pro_mc_name';
				$old_name = Input::get('cate_name');
				$id = Input::get('category_id');
				$check = DB::table('gr_proitem_maincategory')->select($name, 'pro_mc_id')->where('pro_mc_id', '<>', $id)->where('pro_mc_type', '=', '1')->where('pro_mc_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['pro_mc_name'] = ucfirst($old_name);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-item-category')->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$validatorcat = Validator::make($request->all(), [
                        'cate_name_'.$Lang->lang_code => 'required',
						
						]);
						if($validatorcat->fails()){
							return redirect('manage-item-category')->withErrors($validatorcat)->withInput();
						}
						else{
							$entry['pro_mc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
						}
						
					}
				}
				$update = updatevalues('gr_proitem_maincategory', $entry, ['pro_mc_id' => Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('manage-item-category');
				} else {
				return Redirect::to('admin-login');
			}
			
		}
		
		/** manage item sub category **/
		public function manage_subitem(Request $request, $id)
		{
			if (Session::has('admin_id') == 1) {
				$main_id = base64_decode($id);
				/* get category list and ajax search starts */
				if ($request->ajax()) {
					$columns = array(
                    0 => 'pro_sc_id',
                    1 => 'pro_sc_id',
                    2 => 'pro_sc_name',
                    3 => 'pro_sc_id',
                    4 => 'pro_sc_status',
                    5 => 'pro_sc_id',
                    6 => 'pro_added_by'
					);
					/* get total count */
					$totalData = Admin::get_categoryLists_count('gr_proitem_subcategory', ['pro_sc_type' => '1', 'pro_main_id' => $main_id], 'pro_sc_status');
					
					
					$totalFiltered = $totalData;
					/* eof get total count */
					$limit = $request->input('length');
					$start = $request->input('start');
					$order = $columns[$request->input('order.0.column')];
					$dir = $request->input('order.0.dir');
					$search = trim($request->catName_search);
					$addedBySearch = trim($request->addedBy_search);
					$catStatus_search = trim($request->catStatus_search);
					if ($search == '' && $addedBySearch == '' && $catStatus_search == '') {
						$posts = DB::table('gr_proitem_subcategory')
                        ->where('pro_sc_status', '<>', '2')
                        ->where(['pro_main_id' => $main_id, 'pro_sc_type' => '1'])
                        ->orderBy($order, $dir)->skip($start)->take($limit)
                        ->get();
						} else {
						$q = array();
						$sql = DB::table('gr_proitem_subcategory')->where('pro_sc_status', '<>', '2')
                        ->where(['pro_main_id' => $main_id, 'pro_sc_type' => '1']);
						if ($search != '') {
							$q = $sql->where('pro_sc_name', 'LIKE', '%' . $search . '%');
						}
						if ($catStatus_search != '') {
							$q = $sql->where(['pro_sc_status' => $catStatus_search]);
						}
						if ($addedBySearch != '') {
							$q = $sql->where(['pro_added_by' => $addedBySearch]);
						}
						$totalFiltered = $q->count();
						$q = $sql->orderBy($order, $dir)->skip($start)->take($limit);
						$posts = $q->get();
						
					}
					
					$data = array();
					if (!empty($posts)) {
						$snoCount = $start;
						$phrase  = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBCATEGORY_BLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBCATEGORY_BLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SUBCATEGORY_BLOCK_ERROR');
						$blockPhrase = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STORE_UNBLOCK_ERROR');
						$storeType=1;
						$store_type=(Lang::has(Session::get('admin_lang_file').'.ADMIN_RTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_RTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RTS'); 
						$pro_type = 2; 
						$product_type = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ITS');
						$click_to_block = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
						$click_to_unblock = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
						$click_to_edit = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
						$click_to_delete = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
						foreach ($posts as $post) {
							$getCartProduct = DB::select("SELECT COUNT(*) AS cart_count FROM gr_cart_save WHERE cart_item_id IN (SELECT gr_product.pro_id FROM gr_product WHERE pro_sub_cat_id = '".$post->pro_sc_id."')");
							$cartCount = $getCartProduct[0]->cart_count;
							$getUsedProduct = getProductsUsedThisSubcategory($post->pro_sc_id,2,'block');
							$usedProduct = $getUsedProduct[0]->usedProduct;
							$usedCount = $getUsedProduct[0]->usedCount;
							$totalBlockCount = $usedCount+$cartCount;
							if($totalBlockCount > 0 ){
								$search_str = array(":num_products", ":product_type", ":num_items", ":action_text");
								$replace_str= array($usedCount, $product_type, $cartCount, 'block');
								$new_string = str_replace($search_str, $replace_str, $phrase);
								$toolTipText = $new_string;//$usedCount.' products are added in this store. Please block those products before you block this store';
								$onclickfun = 'onclick="return confirm(\''.$toolTipText.'\');"';
								}else {
								$onclickfun = '';
							}
							if($post->pro_sc_status==1)
							{
								$blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_sc_id.'\',0);" id="statusLink_'.$post->pro_sc_id.'" '.$onclickfun.'><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
							}
							else
							{
								$blockRunblock = '<a href="javascript:individual_change_status(\''.$post->pro_sc_id.'\',1);" id="statusLink_'.$post->pro_sc_id.'" onclick="return confirm(\''.str_replace(":product_type",$product_type,$blockPhrase).'\');"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'" ></i></a>';
							}
							if($post->pro_added_by == 0)
							{
								$added_by = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
							}
							else
							{	
								$added_by = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
							}
							
							$getUsedProduct = getProductsUsedThisSubcategory($post->pro_sc_id,2,'delete');
							$usedProduct = $getUsedProduct[0]->usedProduct;
							$usedCount = $getUsedProduct[0]->usedCount;
							$totalDeleteCount = $usedCount+$cartCount;
							
							if($totalDeleteCount > 0 ){
								$search_str = array(":num_products", ":product_type", ":num_items", ":action_text");
								$replace_str= array($usedCount, $product_type, $cartCount, 'delete');
								$new_string = str_replace($search_str, $replace_str, $phrase);
								$deleteToolTipText = $new_string;//$usedCount.' products are added in this store. Please delete those products before you delete this store';
								$onclickfun = 'onclick="return confirm(\''.$deleteToolTipText.'\');"';
								}else {
								$onclickfun = '';
							}
							
							$deleteLink = '<a href= "javascript:individual_change_status(\''.$post->pro_sc_id.'\',2);" title="'.$click_to_delete.'" class="tooltip-demo" id="statusLink_'.$post->pro_sc_id.'" '.$onclickfun.'><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
							$chkName = 'chk[]';
							$chkDisabled='';
							
							$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="'.$chkName.'" '.$chkDisabled.' value="'.$post->pro_sc_id.'">';
							$nestedData['SNo'] = ++$snoCount;
							$nestedData['CategoryName'] = $post->pro_sc_name;
							$nestedData['Edit'] = '<a href="'.url("edit_subitem_category").'/'.base64_encode($post->pro_sc_id).'/'.base64_encode($main_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
							$nestedData['Status'] = $blockRunblock;
							$nestedData['Delete'] = $deleteLink;
							$nestedData['AddedBy'] = $added_by;//$added_by;
							$data[] = $nestedData;
						}
					}
					
					// print_r($data);
					// exit;
					$json_data = array(
                    "draw" => intval($request->input('draw')),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $data
					);
					
					return $json_data;
					
				}
				
				/* get category lists and ajax search ends */
				$categoryName = DB::table('gr_proitem_maincategory')->select('pro_mc_name')->where('pro_mc_id', '=', $main_id)->first()->pro_mc_name;
				$where = ['pro_sc_type' => 1, 'pro_main_id' => $main_id]; //1 for item
				
				$get_category_details = get_all_details('gr_proitem_subcategory', 'pro_sc_status', 10, 'desc', 'pro_sc_id', $where, 'gr_proitem_maincategory', 'pro_mc_id', 'pro_main_id');
				//print_r($get_country_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SUB_CATE_LIST')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SUB_CATE_LIST') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SUB_CATE_LIST');
				$page_title = $categoryName . '\'s ' . $page_title;
				return view('Admin.category.manage_subitem_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details)->with('main_id', $main_id);
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** edit sub item  category **/
		public function edit_subtiem_category($id, $main_id)
		{
			$id = base64_decode($id);
			$main_id = base64_decode($main_id);
			$where = ['pro_sc_id' => $id];
			$get_cate_details = get_details('gr_proitem_subcategory', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EDIT_SUB_ITEM_CATE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EDIT_SUB_ITEM_CATE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EDIT_SUB_ITEM_CATE');
			$where = ['pro_sc_type' => 1, 'pro_main_id' => $main_id]; //1 for item
			$get_category_details = get_all_details('gr_proitem_subcategory', 'pro_sc_status', 10, 'desc', 'pro_sc_id', $where, 'gr_proitem_maincategory', 'pro_mc_id', 'pro_main_id');
			return view('Admin.category.manage_subitem_category')->with('pagetitle', $page_title)->with('all_details', $get_category_details)->with('cate_detail', $get_cate_details)->with('id', $id)->with('main_id', $main_id);
		}
		
		/** update country **/
		public function update_subitem_category()
		{
			if (Session::has('admin_id') == 1) {
				$name = 'pro_sc_name';
				$old_name = Input::get('cate_name');
				$id = Input::get('category_id');
				$main_id = Input::get('main_id');
				$cate_type = Input::get('cate_type');
				$check = DB::table('gr_proitem_subcategory')->select($name, 'pro_sc_id')->Join('gr_proitem_maincategory', 'gr_proitem_maincategory.pro_mc_id', '=', 'gr_proitem_subcategory.pro_main_id')->where('gr_proitem_maincategory.pro_mc_name', '=', $old_name)->where('pro_sc_id', '<>', $id)->where('pro_main_id', '=', $main_id)->where('pro_sc_type', '=', $cate_type)->where('pro_sc_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['pro_sc_name'] = ucfirst($old_name);
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::to('manage-subitem/' . base64_encode($id))->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['pro_sc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				$update = updatevalues('gr_proitem_subcategory', $entry, ['pro_sc_id' => Input::get('category_id')]);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				Session::flash('message', $msg);
				//return Redirect::back();
				return Redirect::to('manage-subitem/' . base64_encode($main_id));
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		/** add sub product category **/
		public function add_subitem_category(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				$name = 'pro_sc_name';
				$old_name = Input::get('cate_name');
				$main_id = Input::get('main_id');
				$cate_type = Input::get('cate_type');
				$check = DB::table('gr_proitem_subcategory')->where('pro_main_id', '=', $main_id)->where('pro_sc_type', '=', $cate_type)->where('pro_sc_status', '!=', '2')->where($name, '=', $old_name)->get();
				$entry['pro_sc_name'] = ucfirst($old_name);
				
				if (count($check) > 0) {
					$msg = Input::get('cate_name') . ' - ';
					$msg .= (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CATE_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CATE_NAME_EXISTS');
					
					return Redirect::back()->withErrors(['errors' => $msg]);
					
				}
				if (count($this->get_Adminactive_language) > 0) {
					foreach ($this->get_Adminactive_language as $Lang) {
						
						$entry['pro_sc_name_' . $Lang->lang_code] = Input::get('cate_name_' . $Lang->lang_code);
					}
				}
				$entry = array_merge(array('pro_main_id' => $main_id,
				'pro_sc_type' => $cate_type,
				'pro_sc_status' => 1)
                , $entry);
				//print_r($entry); exit;
				$update = insertvalues('gr_proitem_subcategory', $entry);
				// print_r($entry); exit;
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::back();
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		
		/*change all status start*/
		public function ajax_change_all_status(Request $request)
		{
			$cate_id = input::get('cat_id');
			$status = input::get('status');
			$update = ['cate_status' => $status];
			$where = ['cate_id' => $cate_id];
			$cate_status = updatevalues('gr_category', $update, $where);
			$whr = ['st_category' => $cate_id, 'st_type' => 2];
			if ($cate_status) {
				$upd_str = ['st_status' => $status];
				$store_status = updatevalues('gr_store', $upd_str, $whr);
			}
			
			$get_store_id = DB::table('gr_store')->where($whr)->get()->toArray();
			if (count($get_store_id) > 0) {
				foreach ($get_store_id as $det) {
					$store_id = $det->id;
					if ($store_id) {
						$product_sts = updatevalues('gr_product', ['pro_status' => $status], ['pro_store_id' => $store_id]);
					}
				}
			}
			
		}
		/*change all status end*/
		
		/*----------change status of all restaturent and item of category start ----------- */
		public function ajax_change_restaurant_status(Request $request)
		{
			
			$cate_id = $request->get('cat_id');
			$status = $request->get('status');
			$update = ['cate_status' => $status];
			$where = ['cate_id' => $cate_id];
			$cate_status = updatevalues('gr_category', $update, $where);
			if ($cate_status) {
				$whr = ['st_category' => $cate_id, 'st_type' => 1];
				if ($cate_status) {
					$upd_str = ['st_status' => $status];
					$store_status = updatevalues('gr_store', $upd_str, $whr);
				}
				
				$get_store_id = DB::table('gr_store')->where($whr)->get()->toArray();
				
				
				if (count($get_store_id) > 0) {
					foreach ($get_store_id as $det) {
						$store_id = $det->id;
						if ($store_id) {
							$product_sts = updatevalues('gr_product', ['pro_status' => $status], ['pro_store_id' => $store_id, 'pro_type' => 2]);
						}
					}
				}
				
			}
		}
		
		/*change status of all restaturent and item of category end */
		
		/*--change product category status start ----------*/
		public function ajax_change_productCat_status(Request $request)
		{
			
			$mc_id = input::get('main_cat_id');
			$status = input::get('status');
			
			$update = ['pro_mc_status' => $status];
			$where = ['pro_mc_id' => $mc_id, 'pro_mc_type' => 2];
			$mainCat_status = updatevalues('gr_proitem_maincategory', $update, $where);
			
			if ($mainCat_status) {
				$upd_sub = ['pro_sc_status' => $status];
				$whr_sub = ['pro_main_id' => $mc_id, 'pro_sc_type' => 2];
				$subCat_status = updatevalues('gr_proitem_subcategory', $upd_sub, $whr_sub);
				
				$upd_pro = ['pro_status' => $status];
				$whr_pro = ['pro_category_id' => $mc_id, 'pro_type' => 1];
				$product_status = updatevalues('gr_product', $upd_pro, $whr_pro);
				
				if ($product_status) {
					echo '1';
					} else {
					echo '0';
				}
				
			}
			
			
		}
		/*--change product category status end ----------*/
		
		/*----change item category status start ------*/
		
		public function ajax_change_itemCat_status(Request $request)
		{
			
			$mc_id = input::get('main_cat_id');
			$status = input::get('status');
			
			$update = ['pro_mc_status' => $status];
			$where = ['pro_mc_id' => $mc_id, 'pro_mc_type' => 1];
			$mainCat_status = updatevalues('gr_proitem_maincategory', $update, $where);
			
			if ($mainCat_status) {
				
				$upd_sub = ['pro_sc_status' => $status];
				$whr_sub = ['pro_main_id' => $mc_id, 'pro_sc_type' => 1];
				$subCat_status = updatevalues('gr_proitem_subcategory', $upd_sub, $whr_sub);
				
				$upd_pro = ['pro_status' => $status];
				$whr_pro = ['pro_category_id' => $mc_id, 'pro_type' => 2];
				$product_status = updatevalues('gr_product', $upd_pro, $whr_pro);
				
				if ($product_status) {
					echo '1';
					} else {
					echo '0';
				}
				
			}
			
		}
		/*----change item category status start ------*/
		
		/*----change sub category status  start--------*/
		
		public function ajax_change_subcat_status(Request $request)
		{
			$sub_id = input::get('sub_cat_id');
			$status = input::get('status');
			
			$update = ['pro_sc_status' => $status];
			$where = ['pro_sc_id' => $sub_id];
			$subCat_status = updatevalues('gr_proitem_subcategory', $update, $where);
			if ($subCat_status) {
				$upd_pro = ['pro_status' => $status];
				$whr_pro = ['pro_sub_cat_id' => $sub_id];
				$product_status = updatevalues('gr_product', $upd_pro, $whr_pro);
			}
			
			
		}
		/*----change sub category status  end--------*/
		
		
	}					