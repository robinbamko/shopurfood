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
	
	use App\Product;
	use App\Settings;
	
	use Excel;
	
	use Response;
	use File;
	use Image;
	use ZipArchive;
	
	class ProductController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		/*MERCHANTS LIST */
		public function products_list($id='')
		{
			if(Session::has('admin_id') == 1)
			{   
				if($id!='')
				{
					DB::table('gr_product')->where('pro_type', $id)->update(['pro_read_status' => 1]);
					return Redirect::to('manage-product');
				}
				$type=1;
				$pdt_status	= Input::get('pdt_status');
				$get_items_details = array();//Product::get_all_details_search($type,$pdt_status);
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_PDT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_PDT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_PDT');
				return view('Admin.product.manage_products')->with('pagetitle',$page_title)->with('all_details',$get_items_details)->with('pdt_status',$pdt_status);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function ajax_products_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array( 
				0 =>'pro_id', 
				1 =>'st_store_name', 
				2 =>'pro_item_code',
				3=> 'pro_item_name',
				4=> 'pro_original_price',
				5=> 'pro_discount_price',
				6=> 'pro_images',
				7=> 'availablity',
				8=> 'pro_no_of_purchase',
				9=> 'added_by'
				);
				/*To get Total count */
				$type=1;
				$sql =  
				DB::table('gr_product')->select('gr_product.pro_id')
				->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
				->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
				->where('gr_merchant.mer_status','<>','2')
				->where('gr_store.st_status','<>','2')
				->where('gr_product.pro_status','<>','2')
				->where('gr_product.pro_type','=',$type);
				$totalData=$sql->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$pdtStore_search = trim($request->pdtStore_search); 
				$pdtCode_search = trim($request->pdtCode_search); 
				$pdtName_search = trim($request->pdtName_search); 
				$pdt_status = trim($request->pdt_status); 
				$addedBy_search = trim($request->addedBy_search); 
				if($pdtStore_search =='' && $pdtCode_search=='' && $pdtName_search=='' && $pdt_status=='' && $addedBy_search=='')
				{    
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_product')
					->select('gr_product.pro_id',
					'gr_product.pro_store_id',
					'gr_product.pro_status',
					'gr_store.st_store_name',
					'gr_product.pro_item_code',
					'gr_product.pro_item_name',
					'gr_product.pro_original_price',
					'gr_product.pro_discount_price',
					'gr_product.pro_images',
					'gr_product.added_by',
					'gr_merchant.mer_email',
					DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Avail','Sold') as availablity")
					)
					->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
					->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
					->where('gr_merchant.mer_status','<>','2')
					->where('gr_store.st_status','<>','2')
					->where('gr_product.pro_status','<>','2')
					->where('gr_product.pro_type','=',$type)
					->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_product')
					->select('gr_product.pro_id',
					'gr_product.pro_store_id',
					'gr_product.pro_status',
					'gr_store.st_store_name',
					'gr_product.pro_item_code',
					'gr_product.pro_item_name',
					'gr_product.pro_original_price',
					'gr_product.pro_discount_price',
					'gr_product.pro_images',
					'gr_product.added_by',
					'gr_merchant.mer_email',
					DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Avail','Sold') as availablity")
					);
					if($pdtStore_search != '')
					{
						$q = $sql->where('gr_store.st_store_name','like','%'.$pdtStore_search.'%'); 
					}
					if($pdtCode_search != '')
					{
						$q = $sql->where('gr_product.pro_item_code','like','%'.$pdtCode_search.'%'); 
					}
					if($pdtName_search != '')
					{
						$q = $sql->where('gr_product.pro_item_name','like','%'.$pdtName_search.'%'); 
					}
					if($pdt_status == '1')
					{ 
						$q = $sql->whereRaw('gr_product.pro_quantity > gr_product.pro_no_of_purchase');
					}
					if($pdt_status == '2')
					{ 
						$q = $sql->whereRaw('gr_product.pro_no_of_purchase >= gr_product.pro_quantity');
					}	
					if($addedBy_search == 'admin')
					{ 
						$q = $sql->where('gr_product.added_by','=','0');
					}
					if($addedBy_search == 'merchant')
					{ 
						$q = $sql->where('gr_product.added_by','>','0');
					}
					$q = $sql->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
					->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
					->where('gr_merchant.mer_status','<>','2')
					->where('gr_store.st_status','<>','2')
					->where('gr_product.pro_status','<>','2')
					->where('gr_product.pro_type','=',$type);
					$totalFiltered = $sql->count();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $sql->get();
					
					
					//echo $totalFiltered;
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
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
						$images = explode("/**/",$post->pro_images); 
						$filename = public_path('images/store/products/').$images[0];
						if(file_exists($filename))
						{
							$pdtImage = '<img class="item_image" id="myImg_'.$post->pro_id.'" src="'.url('public/images/store/products/'.$images[0]).'"  alt="'.$post->pro_item_name.'" style="width: 85px;height: 85px;cursor:pointer">';
						}
						else
						{
							$pdtImage = '<img class="item_image" id="myImg_'.$post->pro_id.'" src="'.url('public/images/noimage/'.$this->no_item).'"  alt="'.$post->pro_item_name.'" style="width: 85px;height: 85px;cursor:pointer">';
						}
						
						if($post->availablity == 'Avail')
						{
							$availabity = (Lang::has(Session::get('admin_lang_file').'.ADMIN_AVAILABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AVAILABLE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_AVAILABLE');
						}
						else
						{
							$availabity = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SOLD')) ? trans(Session::get('admin_lang_file').'.ADMIN_SOLD') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SOLD');
						}
						
						$edit_link = '';
						
						if ($allPrev == '1' || in_array('2', $Product)){
							$edit_link .= '<a href="'.url('edit-product').'/'.base64_encode($post->pro_id).'" class="label label-info tooltip-demo" title="Edit"><i class="fa fa-pencil " ></i> Edit</a><br>';
						}
						
						if($post->pro_status == 1)
						{
							
							$edit_link .= '<a href="javascript:individual_change_status(\''.$post->pro_id.'\',0);" id="statusLink_'.$post->pro_id.'" class="label label-success tooltip-demo" title="Click to Block"><i class="fa fa-check" ></i> Unblock</a><br>';
							
							// $edit_link .='<a href="#" class="label label-success tooltip-demo" onClick="change_pro_status('.$post->pro_id.','.'0'.',\'block\''.')" title="Click to Block"><i class="fa fa-check"  ></i> Unblock</a><br>';
						}
						else
						{
							
							$edit_link .= '<a href="javascript:individual_change_status(\''.$post->pro_id.'\',1);" id="statusLink_'.$post->pro_id.'" class="label label-warning  tooltip-demo" title="Click to Unblock"><i class="fa fa-ban"></i> Block</a><br>';
							
							// $edit_link .='<a href="#" class="label label-warning tooltip-demo" onClick="change_pro_status('.$post->pro_id.','.'1'.',\'unblock\''.')" title="Click to Unblock" ><i class="fa fa-ban " ></i> Block</a><br>';
						}
						
						
						
						if ($allPrev == '1' || in_array('3', $Product)){
							// $edit_link .='<a href="#" class="label label-danger tooltip-demo" onClick="change_pro_status('.$post->pro_id.','.'2'.',\'delete\''.')" title="Delete"><i class="fa fa-trash "></i> Delete</a>';
							$edit_link .= '<a href= "javascript:individual_change_status(\''.$post->pro_id.'\',2);" title="delete" class="label label-danger tooltip-demo" id="statusLink_'.$post->pro_id.'"><i class="fa fa-trash"></i> Delete</a>';
						}
						
						
						
						if($post->added_by == 0)
						{
							$addedBy = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedBy = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						}
						$nestedData['check'] = '<input type="checkbox" name ="chk[]" "value" = "'.$post->pro_id.'" />';
						$nestedData['storeName'] = $post->st_store_name.' - '.$post->mer_email;
						$nestedData['pdtCode'] = $post->pro_item_code;
						$nestedData['pdtName'] = $post->pro_item_name;
						$nestedData['price'] = $post->pro_original_price;
						$nestedData['discPrice'] = $post->pro_discount_price;
						$nestedData['itemImage'] = $pdtImage;
						$nestedData['stock'] = $availabity;
						$nestedData['action'] = $edit_link;
						$nestedData['addedBy'] = $addedBy;
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
		public function add_product()
		{
			if(Session::has('admin_id') == 1)
			{ 
				
				$category_list = DB::table('gr_proitem_maincategory')->where(['pro_mc_status' => '1','pro_mc_type'=>'2'])->orderby('pro_mc_name','asc')->pluck('pro_mc_name','pro_mc_id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'),'0')->toarray();
				$restaurants = Product::get_activestore_withmerch('2');
				$rest_array = array();
				if(count($restaurants) > 0 )
				{
					$rest_array[0]=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
					foreach($restaurants as $restar)
					{
						$rest_array[$restar->id]=$restar->st_store_name.' - '.$restar->mer_email;
					}
				}
				$restaurant_list = $rest_array;
				//$restaurant_list = DB::table('gr_store')->where(['st_status' => '1','st_type'=>'2'])->pluck('st_store_name','id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'),'0')->toarray();
				$choices_list = get_all_details('gr_choices','ch_status','','desc','ch_id','');
				//print_r($choices_list); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_PRODUCT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_PRODUCT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_PRODUCT');
				$action='save-product';
				$array_name = array();
				foreach(DB::getSchemaBuilder()->getColumnListing('gr_product') as $res)
				{
					$array_name[$res]='';
				}
				$object = (object) $array_name; // return all value as empty.
				
				return view('Admin.product.add_product')->with(['category_list' => $category_list,'subcategory_list'=>array(),'restaurant_list' => $restaurant_list,'choices_list'=>$choices_list,'pagetitle' => $page_title,'getstore'=>$object,'entered_spec'=>array(),'entered_choice'=>array(),'action'=>$action,'id'=>'']);
			}
			else
			{
				return Redirect::to('admin-login');
			}
			
		}
		
		public function save_product(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{ 
				/*print_r($_POST); exit;Array ( [_token] => MVd1YnORAW6G7MYyB4dUjwPZ6S1JdSkIfwOtxDTI [pro_id] => [pro_currency] => USD [pro_store_id] => 1 [pro_category_id] => 1 [pro_sub_cat_id] => 1 [pro_item_code] => 1 [pro_item_name] => 2 [pro_item_name_ar] => 3 [pro_per_product] => 4 [pro_per_product_ar] => 5 [pro_quantity] => 6 [pro_original_price] => 7 [pro_has_discount] => yes [pro_discount_price] => 8 [pro_desc] => 9 [pro_desc_ar] => 10 [pro_meta_keyword] => 11 [pro_meta_keyword_ar] => 12 [pro_meta_desc] => 13 [pro_meta_desc_ar] => 14 [pro_had_choice] => 1 [choices] => Array ( [0] => 6 [1] => 5 ) [pro_choice_price] => Array ( [6] => 15 [5] => 16 [4] => [3] => [2] => [1] => ) [pro_had_spec] => 1 [spec_title] => Array ( [0] => 17 [1] => 19 ) [spec_desc] => Array ( [0] => 18 [1] => 20 ) ) */
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_PRODUCT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_PRODUCT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_PRODUCT');
				$action='save-product';
				$array_name = array();
				foreach(DB::getSchemaBuilder()->getColumnListing('gr_product') as $res)
				{
					$array_name[$res]='';
				}
				$object = (object) $array_name; // return all value as empty.
				$this->validate($request, 
				[
				'pro_store_id'=>'Required|not_in:0',
				'pro_category_id'=>'Required|not_in:0',
				'pro_sub_cat_id'=>'Required|not_in:0',
				'pro_item_code'=>'Required',
				'pro_item_name'=>'Required',
				'pro_per_product'=>'Required',
				'pro_quantity'=>'Required|not_in:0',
				'pro_original_price'=>'Required',
				'pro_has_discount'=>'Required',
				'pro_desc'=>'Required',
				'pro_had_spec'=>'Required',
				'pro_had_tax'=>'Required',
				'item_img' => 'Required',
				'item_img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=800,min_height=800'
				],['pro_store_id.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_STORE'), 
				'pro_store_id.not_in'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_STORE'), 
				'pro_category_id.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE'),
				'pro_category_id.not_in'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE'),
				'pro_sub_cat_id.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE'),
				'pro_sub_cat_id.not_in'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE'),
				'pro_item_code.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_CODE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_CODE'),
				'pro_item_name.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_NAME'),
				'pro_per_product.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_CONTENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_CONTENT'),
				'pro_quantity.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_QUANTITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_QUANTITY') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_QUANTITY'),
				'pro_original_price.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ORIGINAL_PRICE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ORIGINAL_PRICE'),
				'pro_has_discount.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADDISC')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADDISC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADDISC'),
				'pro_desc.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DESC'),
				'pro_had_tax.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_TAX')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_TAX') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_TAX'),
				'pro_veg.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_VEG')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_VEG') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_VEG'),
				'pro_had_spec.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADINGREDIENTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADINGREDIENTS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADINGREDIENTS'),
				'item_img.*.mimes' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE'),
				'item_img.*.dimensions' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE')
				
				]); 
				/** check product name already exists **/
				$check = DB::table('gr_product')->where('pro_status','!=','2')
				->where(['pro_item_name' => Input::get('pro_item_name'),'pro_per_product' => Input::get('pro_per_product'),'pro_item_code' => Input::get('pro_item_code'),'pro_store_id' => Input::get('pro_store_id')])->get();
				if(count($check) > 0)
				{   
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PRODUCT_NAME_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRODUCT_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PRODUCT_NAME_EXISTS');
					
					return Redirect::to('add-product')->withErrors(['errors'=> $msg])->withInput();
					
				}
				
				if(Input::get('pro_has_discount')=='yes')
				{
					$this->validate($request, 
					[
					'pro_discount_price'=>'Required'
					],
					[
					'pro_discount_price.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DISCPRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DISCPRICE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DISCPRICE')
					
					]
					); 
				}
				//echo Input::get('choices'); exit;
				
				if(Input::get('pro_had_tax')=='Yes')
				{
					$this->validate($request, 
					[
					'pro_tax_name'=>'Required',
					'pro_tax_percent'=>'Required'
					],
					[ 
					'pro_tax_name|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_NAME') ,
					'pro_tax_percent|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_PERCENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_PERCENT')
					]
					); 
					
					$pro_tax_name = Input::get('pro_tax_name');
					$pro_tax_percent = Input::get('pro_tax_percent')
					}else{
					$pro_tax_name = '';
					$pro_tax_percent = '';
				}
				if(Input::get('pro_had_spec')=='1')
				{
					$this->validate($request, 
					[
					'spec_title.*'=>'Required',
					'spec_desc.*'=>'Required'
					],
					[ 
					'spec_title.*|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_SPECTITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_SPECTITLE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_SPECTITLE') ,
					'spec_desc.*|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DESC')
					]
					); 
				}
				$str_id = Input::get('pro_store_id');
				/*IMAGES */ 
				$pro_images_array = array();
				if($request->hasFile('item_img')) // add or update new banner images
				{
					$banner = Input::file('item_img');
					$count = count($banner);
					for($i = 0; $i< $count; $i++)
					{
						if(!isset($banner[$i]) && $banner[$i] == '' && $str_id != '') //In update,if banner image is empty then update with old banner values
						{
							$banner[$i] = $old_banner[$i];
						}
						$st_banner = 'item'.rand().'.'.$banner[$i]->getClientOriginalExtension();
						$destinationPath = public_path('images/store/products');
						$customer = Image::make($banner[$i]->getRealPath())->resize(800, 800);
						//$banner_file .= $st_banner."/**/";
						array_push($pro_images_array,$st_banner);
						$customer->save($destinationPath.'/'.$st_banner,80);
						
					}
				}
				elseif($request->hasFile('st_banner') == 0 && $banner == '' &&  $str_id != '')  //update old banner files
				{
					//$banner_file = '';
				}
				/* EOF IMAGES */
				/*SEO URL */ 
				$seourl = str_slug(Input::get('pro_item_name').'-'.Input::get('pro_per_product'));
				$generated_seourl = generate_seourl($seourl,'gr_product','','','pro_item_slug');
				/*EOF SEO URL */
				$profile_det = array(
				'pro_store_id'=>Input::get('pro_store_id'),
				'pro_category_id'=>Input::get('pro_category_id'),
				'pro_sub_cat_id'=>Input::get('pro_sub_cat_id'),
				'pro_item_code'=>Input::get('pro_item_code'),
				'pro_item_name'=>ucfirst(Input::get('pro_item_name')),
				'pro_item_slug' => $generated_seourl,
				'pro_per_product'=>Input::get('pro_per_product'),				
				'pro_quantity'=>Input::get('pro_quantity'),
				'pro_original_price'=>Input::get('pro_original_price'),
				'pro_has_discount'=>Input::get('pro_has_discount'),
				'pro_discount_price'=>Input::get('pro_discount_price'),
				'pro_desc'=>Input::get('pro_desc'),
				'pro_currency'=>Input::get('pro_currency'),
				'pro_type'=>1,
				'pro_meta_keyword'=>Input::get('pro_meta_keyword'),
				'pro_meta_desc'=>Input::get('pro_meta_desc'),
				'pro_had_spec'=>Input::get('pro_had_spec'),
				'pro_had_tax'=>Input::get('pro_had_tax'),
				'pro_tax_name'=>$pro_tax_name,
				'pro_tax_percent'=>$pro_tax_percent,
				'pro_veg'=>Input::get('pro_veg'),			
				'pro_images'=>implode("/**/",$pro_images_array),
				'pro_created_date' => date('Y-m-d H:i:s'),
				'pro_updated_date' => date('Y-m-d H:i:s')
				);			
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{	
						
						$profile_det['pro_item_name_'.$Lang->lang_code] = Input::get('pro_item_name_'.$Lang->lang_code);		
						$profile_det['pro_per_product_'.$Lang->lang_code] = Input::get('pro_per_product_'.$Lang->lang_code);		
						$profile_det['pro_desc_'.$Lang->lang_code] = Input::get('pro_desc_'.$Lang->lang_code);		
						$profile_det['pro_meta_keyword_'.$Lang->lang_code] = Input::get('pro_meta_keyword_'.$Lang->lang_code);		
						$profile_det['pro_meta_desc_'.$Lang->lang_code] = Input::get('pro_meta_desc_'.$Lang->lang_code);		
					}
				}
				//DB::connection()->enableQueryLog();
				$res=insertvalues('gr_product',$profile_det);
				$pro_id = DB::getPdo()->lastInsertId();
				//DB::table('gr_merchant')->save($profile_det);
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				
				if($res)
				{
					
					$spec_titles =  Input::get('spec_title');
					for($i=0;$i<count($spec_titles);$i++)
					{
						if(Input::get('spec_title')[$i]!='')
						{
							$spec_det = array(	'spec_title'=>Input::get('spec_title')[$i],
							'spec_desc'=>Input::get('spec_desc')[$i],
							'spec_pro_id'=>$pro_id
							); 
							$res=insertvalues('gr_product_spec',$spec_det);
						}
					}
					
					/* EOF MAIL FUNCTION */ 
					
					$message = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					return redirect('manage-product')->with('message',$message);
				}
				else
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SUMTHNG_WRONG');
					return Redirect::to('add-product')->withErrors(['errors'=> $msg])->withInput();
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** edit store category **/
		public function edit_product($id)
		{
			if(Session::has('admin_id') == 1)
			{ 
				$id = base64_decode($id);
				$where = ['pro_id' => $id];
				$get_items_details = get_details('gr_product',$where);
				$category_list = DB::table('gr_proitem_maincategory')->where(['pro_mc_status' => '1','pro_mc_type'=>'2'])->orderby('pro_mc_name','asc')->pluck('pro_mc_name','pro_mc_id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'),'0')->toarray();
				$subcategory_list = DB::table('gr_proitem_subcategory')->where(['pro_sc_status' => '1','pro_sc_type'=>'2','pro_main_id'=>$get_items_details->pro_category_id])->orderby('pro_sc_name','asc')->pluck('pro_sc_name','pro_sc_id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'),'0')->toarray();
				$restaurants = Product::get_activestore_withmerch('2');
				$rest_array = array();
				if(count($restaurants) > 0 )
				{
					$rest_array[0]=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
					foreach($restaurants as $restar)
					{
						$rest_array[$restar->id]=$restar->st_store_name.' - '.$restar->mer_email;
					}
				}
				$restaurant_list = $rest_array;
				//$restaurant_list = DB::table('gr_store')->where(['st_status' => '1','st_type'=>'2'])->pluck('st_store_name','id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'),'0')->toarray();
				$choices_list = get_all_details('gr_choices','ch_status','','desc','ch_id','');
				$entered_spec = DB::table('gr_product_spec')->where(['spec_pro_id' =>$id])->pluck('spec_title','spec_desc')->toarray();//Array ( [18] => 17 [20] => 19 )
				$entered_choice = DB::table('gr_product_choice')->where(['pc_pro_id' =>$id])->pluck('pc_choice_id','pc_price')->toarray();//Array ( [15.00] => 6 [16.00] => 5 )
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_PRODUCT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_PRODUCT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT_PRODUCT');
				$action='update-product';
				return view('Admin.product.add_product')->with(['category_list' => $category_list,'subcategory_list'=>$subcategory_list,'restaurant_list' => $restaurant_list,'choices_list'=>$choices_list,'pagetitle' => $page_title,'getstore'=>$get_items_details,'entered_spec'=>$entered_spec,'entered_choice'=>$entered_choice,'action'=>$action,'id'=>$id]);
				//return view('Admin.product.add_product')->with('pagetitle',$page_title)->with('getstore',$get_items_details)->with('id',$id)->with('action',$action);;
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function update_product(Request $request)
		{
			
			$id = Input::get('pro_id');
			
			if(Session::has('admin_id') == 1)
			{
				$this->validate($request, 
				[
				'pro_store_id'=>'Required|not_in:0',
				'pro_category_id'=>'Required|not_in:0',
				'pro_sub_cat_id'=>'Required|not_in:0',
				'pro_item_code'=>'Required',
				'pro_item_name'=>'Required',
				'pro_per_product'=>'Required',
				'pro_quantity'=>'Required|not_in:0',
				'pro_original_price'=>'Required',
				'pro_has_discount'=>'Required',
				'pro_desc'=>'Required',
				'pro_had_spec'=>'Required',
				'pro_had_tax'=>'Required',
				'item_img' => 'Sometimes',
				'item_img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=800,min_height=800'
				],['pro_store_id.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RESTAURANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RESTAURANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_RESTAURANT'), 
				'pro_store_id.not_in'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RESTAURANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RESTAURANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_RESTAURANT'), 
				'pro_category_id.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE'),
				'pro_category_id.not_in'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE'),
				'pro_sub_cat_id.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE'),
				'pro_sub_cat_id.not_in'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE'),
				'pro_item_code.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_CODE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_CODE'),
				'pro_item_name.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_NAME'),
				'pro_per_product.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_CONTENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PDT_CONTENT'),
				'pro_quantity.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_QUANTITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_QUANTITY') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_QUANTITY'),
				'pro_original_price.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ORIGINAL_PRICE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ORIGINAL_PRICE'),
				'pro_has_discount.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADDISC')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADDISC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADDISC'),
				
				'pro_desc.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DESC'),
				'pro_had_tax.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_TAX')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_TAX') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_TAX'),
				'pro_veg.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_VEG')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_VEG') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_VEG'),
				'pro_had_spec.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADINGREDIENTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADINGREDIENTS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADINGREDIENTS'),
				'item_img.*.mimes' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE'),
				'item_img.*.dimensions' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE')
				
				]); 
				/** check product name already exists **/
				$check = DB::table('gr_product')->where('pro_id','<>',$id)->where('pro_status','!=','2')
				->where(['pro_item_name' => Input::get('pro_item_name'),'pro_per_product' => Input::get('pro_per_product'),'pro_item_code' => Input::get('pro_item_code'),'pro_store_id' => Input::get('pro_store_id')])->get();
				if(count($check) > 0)
				{   
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PRODUCT_NAME_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRODUCT_NAME_EXISTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PRODUCT_NAME_EXISTS');
					
					return Redirect::to('edit-product/'.base64_encode($id))->withErrors(['pro_item_name' =>$msg])->withInput();
					
				}
				if(Input::get('pro_has_discount')=='yes')
				{
					$this->validate($request, 
					[
					'pro_discount_price'=>'Required'
					],
					[
					'pro_discount_price.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DISCPRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DISCPRICE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DISCPRICE')
					
					]
					); 
					
					$pro_tax_name = Input::get('pro_tax_name');
					$pro_tax_percent = Input::get('pro_tax_percent');
					}else{ 
					
					$pro_tax_name = '';
					$pro_tax_percent = '';
				}
				//echo Input::get('choices'); exit;
				
				if(Input::get('pro_had_tax')=='Yes')
				{
					$this->validate($request, 
					[
					'pro_tax_name'=>'Required',
					'pro_tax_percent'=>'Required'
					],
					[ 
					'pro_tax_name|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_NAME') ,
					'pro_tax_percent|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_PERCENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_PERCENT')
					]
					); 
				}
				if(Input::get('pro_had_spec')=='1')
				{
					$this->validate($request, 
					[
					'spec_title.*'=>'Required',
					'spec_desc.*'=>'Required'
					],
					[ 
					'spec_title.*|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_SPECTITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_SPECTITLE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_SPECTITLE') ,
					'spec_desc.*|required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DESC')
					]
					); 
				}
				/*IMAGES*/
				$pro_images_array = array();
				$old_banner = Input::get('old_images');
				$old = explode('/**/',$old_banner);
				$pro_images_array = array();
				if($request->hasFile('item_img')) // add or update new banner images
				{
					$banner = Input::file('item_img');
					$count = Input::get('fileCount');//count($banner);
					for($i = 0; $i<= $count; $i++)
					{
						if(array_key_exists($i,$banner)) //In update,if banner image is empty then update with old banner values
						{
							if(array_key_exists($i,$old))
							{
								$image_path = public_path('images/store/products/').$old[$i]; 
								if(File::exists($image_path))
								{
									$a =File::delete($image_path);
								}
							}
							$st_banner = 'item'.rand().'.'.$banner[$i]->getClientOriginalExtension();
							array_push($pro_images_array,$st_banner);
							$destinationPath = public_path('images/store/products');
							$customer = Image::make($banner[$i]->getRealPath())->resize(800, 800);
							$customer->save($destinationPath.'/'.$st_banner,80);
						}
						else
						{
							if(array_key_exists($i,$old))
							{
								array_push($pro_images_array,$old[$i]);
							}
						}
					}
					$banner_file = implode('/**/',$pro_images_array); 
				}
				elseif($request->hasFile('item_img') == 0 )  //update old banner files
				{
					$banner_file = $old_banner;
				}
				/* EOF IMAGES */
				/*SEO URL */
				//'pro_id' =>$id
				$seourl = str_slug(Input::get('pro_item_name').'-'.Input::get('pro_per_product'));
				$primary_id = $id;
				$generated_seourl = generate_seourl($seourl,'gr_product','pro_id',$primary_id,'pro_item_slug');
				echo $generated_seourl; exit;
				/* EOF SEO URL */
				$profile_det = array(
				'pro_store_id'		=> Input::get('pro_store_id'),
				'pro_category_id'	=> Input::get('pro_category_id'),
				'pro_sub_cat_id'	=> Input::get('pro_sub_cat_id'),
				'pro_item_code'		=> Input::get('pro_item_code'),
				'pro_item_name'		=> ucfirst(Input::get('pro_item_name')),
				'pro_item_slug'		=> $generated_seourl,
				'pro_per_product'	=> Input::get('pro_per_product'),
				'pro_quantity'		=> Input::get('pro_quantity'),
				'pro_original_price'=> Input::get('pro_original_price'),
				'pro_has_discount'	=> Input::get('pro_has_discount'),
				'pro_discount_price'=> Input::get('pro_discount_price'),
				'pro_desc'			=> Input::get('pro_desc'),
				'pro_currency'		=> Input::get('pro_currency'),
				'pro_type'			=> 1,
				'pro_meta_keyword'	=> Input::get('pro_meta_keyword'),
				'pro_meta_desc'		=> Input::get('pro_meta_desc'),
				'pro_had_spec'		=> Input::get('pro_had_spec'),
				'pro_had_tax'		=> Input::get('pro_had_tax'),
				'pro_tax_name'		=> $pro_tax_name,
                'pro_veg'			=> Input::get('pro_veg'),
				'pro_tax_percent'	=> $pro_tax_percent,
				'pro_images'		=> $banner_file,
				'pro_updated_date' 	=> date('Y-m-d H:i:s')
				);			
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{	
						
						$profile_det['pro_item_name_'.$Lang->lang_code] = Input::get('pro_item_name_'.$Lang->lang_code);		
						$profile_det['pro_per_product_'.$Lang->lang_code] = Input::get('pro_per_product_'.$Lang->lang_code);		
						$profile_det['pro_desc_'.$Lang->lang_code] = Input::get('pro_desc_'.$Lang->lang_code);		
						$profile_det['pro_meta_keyword_'.$Lang->lang_code] = Input::get('pro_meta_keyword_'.$Lang->lang_code);		
						$profile_det['pro_meta_desc_'.$Lang->lang_code] = Input::get('pro_meta_desc_'.$Lang->lang_code);		
					}
				}
				//DB::connection()->enableQueryLog();
				$update = updatevalues('gr_product',$profile_det,['pro_id' =>$id]);
				//DB::table('gr_merchant')->save($profile_det);
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				
				DB::table('gr_product_spec')->where('spec_pro_id', $id)->delete();
				$spec_titles = Input::get('spec_title');
				
				for($i=0;$i<count($spec_titles);$i++)
				{
					if(Input::get('spec_title')[$i]!='')
					{
						$spec_det = array(	'spec_title'=>Input::get('spec_title')[$i],
						'spec_desc'=>Input::get('spec_desc')[$i],
						'spec_pro_id'=>$id
						); 
						$res=insertvalues('gr_product_spec',$spec_det);
					}
				}
				
				/* EOF MAIL FUNCTION */ 
				
				$message = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				return redirect('manage-product')->with('message',$message);
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		/** update prodoct ends  **/
		
		
		/** block/unblock product **/
		public function change_status(Request $request)
		{
			$status = $request->status;
			$id 	= $request->id;
			$update = ['pro_status' => $status];
			$where = ['pro_id' => $id];
			$a = updatevalues('gr_product',$update,$where);
			
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
			}
			elseif($status == 2) //Delete
			{	
				$related_details = get_related_details('gr_product',['pro_id' => $id],['pro_images'],'individual');
				/* delete item images */
				if(!empty($related_details))
				{
					$delete_img = delete_product_images($related_details);
				}
				/* delete specifications */
				$delete_spec = DB::table('gr_product_spec')->where(['spec_pro_id' => $id])->delete();
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				
			}
			elseif($status == 0)  //block
			{	
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
			}
			return "success";
		}
		
		/** multiple block/unblock  product **/
		public function multi_changeStatus()
		{	
			if(Session::has('admin_id') == 1)
			{
				$update = ['pro_status' => Input::get('status')];
				$val = Input::get('val');
				
				//return count($val); exit;
				for($i=1; $i< count($val); $i++)
				{
					$where = ['pro_id' => $val[$i]];
					
					$a = updatevalues('gr_product',$update,$where);
					/* delete product images */
					if(Input::get('status') == 2)
					{
						$related_details = get_related_details('gr_product',['pro_id' => $val[$i]],['pro_images'],'individual');
						/* delete item images */
						if(!empty($related_details))
						{
							$delete_img = delete_product_images($related_details);
						}
						/* delete specifications */
						$delete_spec = DB::table('gr_product_spec')->where(['spec_pro_id' => $val[$i]])->delete();
					}
				}
				//echo Input::get('status'); exit;
				if(Input::get('status') == 1) //Active
				{	
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
					Session::flash('message',$msg);
					
				}
				if(Input::get('status') == 2) //Delete
				{	
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
					Session::flash('message',$msg);
					
				}
				elseif(Input::get('status') == 0)   //block
				{	
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
					Session::flash('message',$msg);
					
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** download product list **/
		public function download_products_list($type)
		{
			if(Session::has('admin_id') == 1)
			{
				$main_type=1;
				$data = Product::get_details($main_type);
				//print_r($get_items_details); exit;
				
				return Excel::create('Products list',function ($excel) use ($data)
				{
					$excel->sheet('Products_list', function ($sheet) use ($data)
					{
						$sheet->setFontFamily('Comic Sans MS');
                        +						$sheet->cell('A2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO'));   });
                        $sheet->cell('B2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ST_NAME'));});
                        $sheet->cell('C2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_CODE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PDT_CODE'));});
                        $sheet->cell('D2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PDT_NAME'));});
                        $sheet->cell('E2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PDT_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PDT_CONTENT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PDT_CONTENT'));});
                        $sheet->cell('F2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_MAINCAT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAINCAT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MAINCAT_NAME'));});
                        $sheet->cell('G2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBCAT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBCAT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SUBCAT_NAME'));});
                        $sheet->cell('H2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_QTY')) ? trans(Session::get('admin_lang_file').'.ADMIN_QTY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_QTY'));});
                        $sheet->cell('I2', function($cell) {
						$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORIGINAL_PRICE'));});
                        $sheet->cell('J2', function($cell) {
                            $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DISC_PRICE'));
						});
						
						
                        //$sheet->getStyle('F2')->getAlignment()->setWrapText(true);
						$j=3;
						foreach ($data as $key => $value) {
							
							$i= $key+2;
							$sheet->cell('A'.$j, $j-2); //print serial no
							$sheet->cell('B'.$j, $value->st_store_name); 
							$sheet->cell('C'.$j, $value->pro_item_code); 
							$sheet->cell('D'.$j, $value->pro_item_name); 
							$sheet->cell('E'.$j, $value->pro_per_product); 
							$sheet->cell('F'.$j, $value->pro_mc_name); 
							$sheet->cell('G'.$j, $value->pro_sc_name); 
							$sheet->cell('H'.$j, $value->pro_quantity); 
							$sheet->cell('I'.$j, $value->pro_currency.' '.$value->pro_original_price); 
							$sheet->cell('J'.$j, $value->pro_currency.' '.$value->pro_discount_price); 
							
							$j++;
						}
					});
				})->download($type);
			}
			else
			{
				return Redirect::to('admin-login');
			}
			
		}
		public function get_sub_category()
		{
			$data = DB::table('gr_proitem_subcategory')->select('pro_sc_name','pro_sc_id')->where('pro_main_id','=',Input::get('pro_main_id'))->where('pro_sc_type','=','2')->where('pro_sc_status','=',1)->orderby('pro_sc_name','asc')->get()->toarray();
			$option='';
			if(count($data) > 0 )
			{
				$option .='<option value="">Select Subcategory</option>';
				foreach($data as $dat)
				{
					$option .='<option value="'.$dat->pro_sc_id.'">'.$dat->pro_sc_name.'</option>';
				}
			}
			echo $option;
		}
		
		public function product_bulk_upload()
		{
			/*echo $srcfile=$_SERVER['DOCUMENT_ROOT']. "/edison_test/product-bulk-upload/pizza/pizza-800x800.jpg";;
				
				echo $dstfile=$_SERVER['DOCUMENT_ROOT']. "/edison_test/product-bulk-upload/abdul/pizza-800x800.jpg"; 
			copy($srcfile, $dstfile);*/
			
			if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PRODUCT_BULK_UPLOAD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRODUCT_BULK_UPLOAD') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PRODUCT_BULK_UPLOAD');
				$id = ''; 
				return view('Admin.product_bulk_upload')->with('pagetitle',$page_title)->with('id',$id);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function product_image_bulk_upload_submit(Request $request)
		{
			if ($_FILES['zip_file']['name'] != '')
			{
				$file_name = $_FILES['zip_file']['name'];
				$array = explode(".", $file_name);
				$name = $array[0];
				$ext = $array[1];
				if ($ext == 'zip')
				{
					$path = 'product-bulk-upload/' . $name;
					$location = $path . $file_name;
					if (file_exists($path))
					{
						$msg = "Already ZIP Found! try with different ZIP file! OR Delete that ZIP File";
						return Redirect::to('product_bulk_upload')->withErrors(['error'=>$msg,'filename'=>$name])->withInput();
					}
					else
					{
						if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $location))
						{
							$zip = new ZipArchive;
							if ($zip->open($location))
							{
								$zip->extractTo($path);
								$zip->close();
							}
							
							unlink($location);
						}
						
						$msg = "ZIP file uploaded and extracted successfully!";
						return Redirect::to('product_bulk_upload')->withErrors(['success'=>$msg])->withInput();
					}
				}
			}
			
			
		}
		public function delete_zip_folder($name)
		{
			
			// $name=Request::segment(2);
			$path = 'product-bulk-upload/' . $name;
			/* chown($path, 0777); */
			/* first delete all files in folder */
			if (!is_dir($path))
			{
				throw new InvalidArgumentException("$path must be a directory");
			}
			
			if (substr($path, strlen($path) - 1, 1) != '/')
			{
				$path.= '/';
			}
			
			$files = glob($path . '*', GLOB_MARK);
			foreach($files as $file)
			{
				if (is_dir($file))
				{
					self::deleteDir($file);
				}
				else
				{ 
					unlink($file);
				}
			}
			
			rmdir($path);
			Session::flash('zip_success_message', "Folder deleted successfully!");
			/* close */
			return Redirect('product_bulk_upload');
		}
		
		/* PRODUCT BULK UPLOAD FORM SUBMIT */
		public function product_bulk_upload_submit(Request $request)
		{ 
			set_time_limit(0);
			$errorArr = array();
			$successArr = array();
			$folder_array = array();
			$error = 0;
			$merchant_pto_title_dup = array();
			$mimes = array(
			'application/vnd.ms-excel',
			'text/plain',
			'text/csv',
			'text/tsv'
			);
			if (!in_array($_FILES['upload_file']['type'], $mimes))
			{
				$errorArr[].= 'File format is incorrect, Upload .csv format';
				Session::flash('message', $errorArr);
				return Redirect('product_bulk_upload');
			}
			else
			{
				
				// get file
				
				$upload = $request->file('upload_file');
				$filePath = $upload->getRealPath();
				
				// open and read
				
				$file = fopen($filePath, 'r');
				$header = fgetcsv($file);
				
				// dd($file);
				// exit();
				
				$escapedHeader = [];
				
				// validate
				
				foreach($header as $key => $value)
				{
					$lheader = strtolower($value);
					$escapedItem = preg_replace('/[^a-z,1-9]/', '', $lheader);
					array_push($escapedHeader, $escapedItem);
				}
				
				// looping through othe columns
				$j=0;
				while ($columns = fgetcsv($file))
				{
					/*Sathyaseelan Define size and color array */
					$size_id = array();
					$color_id = array();
					/* Sathyaseelan close array */
					// trim data
					
					foreach($columns as $key => & $value)
					{
						$value = $value;
					}
					
					$data = array_combine($escapedHeader, $columns);
					
					// print_r($data);
					// exit();
					// setting type
					
					foreach($data as $key => & $value)
					{
						
						$value = ($key == "storename" || $key=='meremail' || $key == "topcategory" || $key == "subcategory" || $key == "procode" || $key == "protitle" || $key == "procontent" || $key == "proqty" || $key == "proprice" || $key == "prodisprice" || $key == "taxname" || $key == "protax" || $key == "prodesc" || $key == "metakeyword" || $key == "metadesc" || $key == "proisspec" || $key == "specificationtitle" || $key == "specificationdesc" || $key == "image1" || $key == "image2" || $key == "image3" || $key == "image4" || $key == "image5" ) ? (string)$value : (float)$value;
						
						
					}
					
					
					// echo'<pre>';print_r($data);die;
					
					/*--------------------------------------------*/
					
					$pro_title = $data['protitle'];
					if ($pro_title == "")
					{
						$errorArr[].= 'Product Title Is Must.'.$pro_title.'';
						$error++;
						Session::flash('message', $errorArr);
					}
					else
					{
						// print_r($data); exit;
						/*--------------------------------------------*/
						
						$pro_title = $data['protitle'];
						if ($pro_title == "")
						{
							$errorArr[].= 'Product Title Is Must.'.$pro_title.'';
							$error++;
							Session::flash('message', $errorArr);
						}
						else
						{
							if($data['meremail'] !='')
							{
								//DB::connection()->enableQueryLog();
								$merchant_det = DB::table('gr_merchant')->select('id')->where('mer_email', '=',$data['meremail'])->where('mer_status', '!=', '2')->first();
								//$query = DB::getQueryLog();
								//print_r($query);
								//echo $merchant_det->id; exit;
								if (isset($merchant_det->id) && $merchant_det->id != '')
								{
									$merchant_id = $merchant_det->id;
								}
								else
								{
									$store_id = 0;
									$errorArr[].= 'Wrong Merchant Email in ' . $pro_title . '';
									$error++;
								}
							}
							else
							{
								$store_id = 0;
								$errorArr[].= 'Merchant email is required for ' . $pro_title . '';
								$error++;
							}
							$shop_name = DB::table('gr_store')->where('st_store_name', 'like', '%' . $data['storename'] . '%')->where('st_mer_id','=',$merchant_id)->where('st_status', '!=', 2)->first();
							$store_id = $shop_name->id;
							
							$already_product_exsist = DB::table('gr_product')->where('pro_status', '!=', 2)->where('pro_item_name', 'like', '' . $data['protitle'] . '')->where('pro_per_product', 'like', '' . $data['procontent'] . '')->where('pro_item_code', 'like', '' . $data['procode'] . '')->where('pro_store_id', '=',$store_id)->get();
							if (count($already_product_exsist) > 0)
							{
								$errorArr[].= '' . $pro_title . ' is Already exsist for same Store';
								Session::flash('message', $errorArr);
								$error++;
							}
							
							if ($data['storename'] != "")
							{
								
								// DB::connection()->enableQueryLog();
								$shop_name = DB::table('gr_store')->where('st_store_name', 'like', '%' . $data['storename'] . '%')->where('st_mer_id','=',$merchant_id)->where('st_status', '!=', 2)->first();
								
								if (isset($shop_name->id) && $shop_name->id != '')
								{
									$store_id = $shop_name->id;
								}
								else
								{
									$store_id = 0;
									$errorArr[].= 'Wrong Store Name in ' . $pro_title . '';
									
									// Session::flash(array('message','Wrong Store Name in '.$pro_title.''));
									
									$error++;
								}
							}
							else
							{
								$store_id = 0;
								$errorArr[].= 'Store name is required for ' . $pro_title . '';
								
								// Session::flash(array('message','Wrong Store Name in '.$pro_title.''));
								
								$error++;
							}
							
							/*--------------------------------------------*/
							if ($data['topcategory'] == "")
							{
								$pro_mc_id = 0;
								$errorArr[].= 'Topcategory is required for ' . $pro_title . '';
								
								// Session::flash(array('message','Wrong Store Name in '.$pro_title.''));
								
								$error++;
							}
							
							if ($data['topcategory'] != "")
							{
								$maincat_name = DB::table('gr_proitem_maincategory')->where('pro_mc_name', 'like', '' . $data['topcategory'] . '')->first();
								if (empty($maincat_name->pro_mc_id) === false)
								{
									$pro_mc_id = $maincat_name->pro_mc_id;
								}
								else
								{
									$pro_mc_id = 0;
									$errorArr[].= 'Wrong Top category in ' . $pro_title . '';
									
									// Session::flash(array('message','Wrong Top category in '.$pro_title.''));
									
									$error++;
								}
							}
							else
							{
								$pro_mc_id = 0;
								$errorArr[].= 'Top category is required for ' . $pro_title . '';
								
								// Session::flash(array('message','Wrong Top category in '.$pro_title.''));
								
								$error++;
							}
							
							/*--------------------------------------------*/
							if ($data['subcategory'] != '')
							{
								$sub_category_name = DB::table('gr_proitem_subcategory')->where('pro_sc_name', 'like', '%' . $data['subcategory'] . '%')->where('pro_main_id', $pro_mc_id)->first();
								if (empty($sub_category_name->pro_sc_id) === false)
								{
									$pro_sb_id = $sub_category_name->pro_sc_id;
								}
								else
								{
									$pro_sb_id = 0;
									$errorArr[].= 'Wrong Sub category ' . $pro_title . '';
									
									// Session::flash(array('message','Wrong Sub category '.$pro_title.''));
									
									$error++;
								}
							}
							else
							{
								$pro_sb_id = 0;
							}
							/*-------------------------------------------*/ 
							
							if($data['procontent'] == "")
							{
								$errorArr[].= 'Product Content is required for ' . $pro_title . '';
								$error++;
								}else{
								$pro_content = $data['procontent'];
							}
							
							/*--------------------------------------------*/
							/*--------------------------------------------*/
							
							$price_check = 0;
							$pro_price = $data['proprice'];
							if ($pro_price == "" || $pro_price == "0")
							{
								$errorArr[].= 'Price is must for ' . $pro_title . '';
								$error++;
								$price_check++;
							}
							elseif (!is_numeric($pro_price))
							{
								$errorArr[].= 'Price Should Be Number for ' . $pro_title . '';
								$error++;
								$price_check++;
							}
							
							$pro_disprice = $data['prodisprice'];
							if (!is_numeric($pro_disprice) && $pro_disprice!='')
							{
								$errorArr[].= 'Discount Price Should Be Number for ' . $pro_title . '';
								$error++;
								$price_check++;
							}
							
							if ($price_check == "0")
							{
								if ($pro_disprice > $pro_price)
								{
									$errorArr[].= 'Discount Price Should Less then Product Actual Price ' . $pro_title . '';
									$error++;
								}
							}
							
							$pro_desc = $data['prodesc'];
							if ($pro_desc == "")
							{
								$errorArr[].= 'Description is must for ' . $pro_title . '';
								$error++;
							}
							
							
							$pro_tax = $data['protax'];
							if ($pro_tax == "")
							{
								
								// $errorArr[] .= 'Tax is must for '.$pro_title.'';
								// $error++;
								
							}
							
							
							$pro_isspec = 2;
							if ($data['proisspec'] == 'Yes' || $data['proisspec'] == 'yes')
							{
								$pro_isspec = 1;
							}
							elseif ($data['proisspec'] == 'No' || $data['proisspec'] == 'no' || $data['proisspec'] == '')
							{
								$pro_isspec = 2;
							}
							
							
							
							if ($pro_isspec == 1 && $data['specificationtitle'] == '')
							{
								$errorArr[].= 'Specification is Must for ' . $pro_title . '';
								$error++;
							}
							
							if ($pro_isspec == 1 && $data['specificationdesc'] == '')
							{
								$errorArr[].= 'Specification Description is Must for ' . $pro_title . '';
								$error++;
							}
							elseif ($data['specificationtitle'] != '' && $data['specificationdesc'] != '')
							{
								if ($pro_isspec == 2)
								{
									$errorArr[].= 'Wrong Specification Status is choosen for ' . $pro_title . '';
									$error++;
								}
								else
								{
									
									$specificationArr = explode(',', $data['specificationtitle']);
									if ($data['specificationdesc'] != '')
									{
										$specification_textArr = explode('*', $data['specificationdesc']);
									}
									else
									{
										$specification_textArr = array();
									}
									
									if (count($specificationArr) != count($specification_textArr))
									{
										$errorArr[].= 'Check Specification Details. There is not correct for ' . $pro_title . '';
										$error++;
									}
									else
									{
										
										
										// if ($error == 0)
										// {
										//   $i = 0;
										//   foreach($specificationArr as $spec_val)
										//   {
										//     $specification_data = DB::table('nm_specification')->where('sp_name', 'like', '' . $spec_val . '')->first();
										//     if ($specification_data == '')
										//     {
										//       $errorArr[].= 'Wrong Specification, Check also Specification Group for ' . $spec_val . '';
										//       $error++;
										//     }
										//     else
										//     {
										//       $specification_group_id[] = $specification_data->sp_spg_id;
										//       $specification_id[] = $specification_data->sp_id;
										//     }
										
										//     $i++;
										//   }
										// }
									}
								}
							}
							
							
							
							
							if (($data['protax'] != ''))
							{
								if (!is_numeric($data['protax']))
								{
									$errorArr[].= 'Tax is must be an number for ' . $pro_title . '';
									$error++;
								}
								else
								{
									$pro_tax = $data['protax'];
								}
							}
							
							$pro_qty = $data['proqty'];
							if ($pro_qty == "" || $pro_qty == "0")
							{
								$errorArr[].= 'Product Quantity Is Must For ' . $pro_title . '';
								$error++;
							}
							elseif (!is_numeric($pro_qty))
							{
								$errorArr[].= 'Product quantity Should Be Number for ' . $pro_title . '';
								$error++;
							}
							
							$pro_mkeywords = $data['metakeyword'];
							$pro_mdesc = $data['metadesc'];
							
							if (isset($data['image1']) && $data['image1'] != '')
							{
								$video_image_url_1 = "product-bulk-upload/" . $data['image1'];
							}
							else
							{
								$video_image_url_1 = '';
								$errorArr[].= 'Image1 is Must for ' . $pro_title . '';
								$error++;
							}
							
							if (isset($data['image2']) && $data['image2'] != '')
							{
								$video_image_url_2 = "product-bulk-upload/" . $data['image2'];
							}
							else
							{
								$video_image_url_2 = '';
							}
							
							if (isset($data['videoimageurl3']) && $data['videoimageurl3'] != '')
							{
								$video_image_url_3 = "product-bulk-upload/" . $data['videoimageurl3'];
							}
							else
							{
								$video_image_url_3 = '';
							}
							
							if (isset($data['videoimageurl4']) && $data['videoimageurl4'] != '')
							{
								$video_image_url_4 = "product-bulk-upload/" . $data['videoimageurl4'];
							}
							else
							{
								$video_image_url_4 = '';
							}
							
							if (isset($data['videoimageurl5']) && $data['videoimageurl5'] != '')
							{
								$video_image_url_5 = "product-bulk-upload/" . $data['videoimageurl5'];
							}
							else
							{
								$video_image_url_5 = '';
							}
							
							/*---------------------------------------*/
							if ($video_image_url_1 != '')
							{
								if (strpos($video_image_url_1, '.jpg') == true || strpos($video_image_url_1, '.jpeg') == true || strpos($video_image_url_1, '.png') == true || $video_image_url_1 != '')
								{
									$url_Arr = explode('/', $video_image_url_1);
									$image_name = end($url_Arr);
									
									//echo basename($video_image_url_1); 
									//exit;
									
									if (@getimagesize($video_image_url_1))
									{
										$size = getimagesize($video_image_url_1);
									}
									else
									{
										$size = array();
										$error++;
										$errorArr[].= 'Image file does not exist. Give Correct image Path for ' . $data['protitle'] . '';
									}
									
									if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
									{
										$error++;
										$errorArr[].= 'Invalid Image Size. Need width800px , height 800px ' . $data['protitle'] . '';
									}
								}
								else
								{
									$error++;
									$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
								}
							}
							
							/*---------------------------------------*/
							if ($video_image_url_2 != '')
							{
								if (strpos($video_image_url_2, '.jpg') == true || strpos($video_image_url_2, '.jpeg') == true || strpos($video_image_url_2, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_2);
									$image_name = end($url_Arr);
									if (@getimagesize($video_image_url_2))
									{
										$size = getimagesize($video_image_url_2);
									}
									else
									{
										$size = array();
										$error++;
										$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
									}
									
									if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
									{
										$error++;
										$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
									}
								}
								else
								{
									$error++;
									$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
								}
							}
							
							/*-------------------------------------------------------------*/
							if ($video_image_url_3 != '')
							{
								if (strpos($video_image_url_3, '.jpg') == true || strpos($video_image_url_3, '.jpeg') == true || strpos($video_image_url_3, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_3);
									$image_name = end($url_Arr);
									if (@getimagesize($video_image_url_3))
									{
										$size = getimagesize($video_image_url_3);
									}
									else
									{
										$size = array();
										$error++;
										$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
									}
									
									if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
									{
										$error++;
										$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
									}
								}
								else
								{
									$error++;
									$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
								}
							}
							
							/*-------------------------------------------------------------*/
							if ($video_image_url_4 != '')
							{
								if (strpos($video_image_url_4, '.jpg') == true || strpos($video_image_url_4, '.jpeg') == true || strpos($video_image_url_4, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_4);
									$image_name = end($url_Arr);
									if (@getimagesize($video_image_url_4))
									{
										$size = getimagesize($video_image_url_4);
									}
									else
									{
										$size = array();
										$error++;
										$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
									}
									
									if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
									{
										$error++;
										$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
									}
								}
								else
								{
									$error++;
									$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
								}
							}
							
							/*-------------------------------------------------------------*/
							if ($video_image_url_5 != '')
							{
								if (strpos($video_image_url_5, '.jpg') == true || strpos($video_image_url_5, '.jpeg') == true || strpos($video_image_url_5, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_5);
									$image_name = end($url_Arr);
									if (@getimagesize($video_image_url_5))
									{
										$size = getimagesize($video_image_url_5);
									}
									else
									{
										$size = array();
										$error++;
										$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
									}
									
									if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
									{
										$error++;
										$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
									}
								}
								else
								{
									$error++;
									$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
								}
							}
							
							
						}
					}
					$j++;
					
					// print_r($merchant_pto_title_dup);die;
					
				}
				
				if ($error == 0)
				{
					
					// get file
					
					$upload = $request->file('upload_file');
					$filePath = $upload->getRealPath();
					
					// open and read
					
					$file = fopen($filePath, 'r');
					$header = fgetcsv($file);
					
					// dd($file);
					// exit();
					
					$escapedHeader = [];
					
					// validate
					
					foreach($header as $key => $value)
					{
						$lheader = strtolower($value);
						$escapedItem = preg_replace('/[^a-z,1-9]/', '', $lheader);
						array_push($escapedHeader, $escapedItem);
					}
					
					// looping through othe columns
					
					while ($columns = fgetcsv($file))
					{
						/*Sathyaseelan Define size and color array */
						$size_id = array();
						$color_id = array();
						/* Sathyaseelan close array */
						if ($columns[0] == "")
						{
							continue;
						}
						
						// trim data
						
						foreach($columns as $key => & $value)
						{
							$value = $value;
						}
						
						$data = array_combine($escapedHeader, $columns);
						foreach($data as $key => & $value)
						{
							$value = ($key == "storename" || $key == "meremail" || $key == "topcategory" || $key == "subcategory" || $key == "procode" || $key == "protitle" || $key == "procontent" || $key == "proqty" || $key == "proprice" || $key == "prodisprice" || $key == "taxname" || $key == "protax" || $key == "prodesc" || $key == "metakeyword" || $key == "metadesc" || $key == "proisspec" || $key == "specificationtitle" || $key == "specificationdesc" || $key == "image1" || $key == "image2" || $key == "image3" || $key == "image4" || $key == "image5") ? (string)$value : (float)$value;
						}
						
						/*--------------------------------------------*/
						$pro_title = $data['protitle'];
						$pro_code = $data['procode'];
						
						
						// DB::connection()->enableQueryLog();
						$merchant_det = DB::table('gr_merchant')->where('mer_email', '=',$data['meremail'])->where('mer_status', '!=', '2')->first();
						$merchant_id=$merchant_det->id;
						
						$shop_name = DB::table('gr_store')->where('st_store_name', 'like', '%' . $data['storename'] . '%')->where('st_mer_id','=',$merchant_id)->where('st_status', '!=', 2)->first();
						$store_id = $shop_name->id;
						$maincat_name = DB::table('gr_proitem_maincategory')->where('pro_mc_name', 'like', '' . $data['topcategory'] . '')->first();
						$pro_mc_id = $maincat_name->pro_mc_id;
						/*--------------------------------------------*/
						if ($data['subcategory'] != '')
						{
							$sub_category_name = DB::table('gr_proitem_subcategory')->where('pro_sc_name', 'like', '%' . $data['subcategory'] . '%')->where('pro_main_id', $pro_mc_id)->first();
							$pro_sb_id = $sub_category_name->pro_sc_id;
						}
						else
						{
							$pro_sb_id = 0;
						}
						
						/*--------------------------------------------*/
						
						$price_check = 0;
						$pro_price = $data['proprice'];
						if ($pro_price == "" || $pro_price == "0")
						{
							$errorArr[].= 'Price is must for ' . $pro_title . '';
							$error++;
							$price_check++;
						}
						elseif (!is_numeric($pro_price))
						{
							$errorArr[].= 'Price Should Be Number for ' . $pro_title . '';
							$error++;
							$price_check++;
						}
						
						$pro_desc = $data['prodesc'];
						if ($pro_desc == "")
						{
							$errorArr[].= 'Description is must for ' . $pro_title . '';
							$error++;
						}
						$pro_tax = $data['protax'];
						$pro_tax_name = $data['taxname'];
						if($pro_tax != ''){
							if (!is_numeric($pro_tax))
							{
								$errorArr[].= 'Product tax Should Be Number for ' . $pro_title . '';
								$error++;
							}
							if($pro_tax_name == ''){
								$errorArr[].= 'Enter Product tax name for ' . $pro_title . '';
								$error++;
							}
							$pro_had_tax = 'Yes';
							}else{
							$pro_had_tax = 'No';
						}
						
						
						$pro_disprice = $data['prodisprice'];
						if($pro_disprice != ''){
							
							if (!is_numeric($pro_disprice))
							{
								$errorArr[].= 'Discount Price Should Be Number for ' . $pro_title . '';
								$error++;
								$price_check++;
							}
							
							if ($price_check == "0")
							{
								if ($pro_disprice > $pro_price)
								{
									$errorArr[].= 'Discount Price Should Less then Product Actual Price ' . $pro_title . '';
									$error++;
								}
							}
							
							$pro_has_discount = 'yes';
							}else{
							$pro_has_discount = 'No';
						}
						$pro_isspec = 2;
						if ($data['proisspec'] == 'Yes' || $data['proisspec'] == 'yes')
						{
							$pro_isspec = 1;
						}
						elseif ($data['proisspec'] == 'No' || $data['proisspec'] == 'no' || $data['proisspec'] == '')
						{
							$pro_isspec = 2;
						}
						
						
						
						if ($pro_isspec == 1 && $data['specificationtitle'] == '')
						{
							$errorArr[].= 'Specification Title is Must for ' . $pro_title . '';
							$error++;
						}
						
						if ($pro_isspec == 1 && $data['specificationdesc'] == '')
						{
							$errorArr[].= 'Specification Text is Must for ' . $pro_title . '';
							$error++;
						}
						elseif ($data['specificationtitle'] != '' && $data['specificationdesc'] != '')
						{
							if ($pro_isspec == 2)
							{
								$errorArr[].= 'Wrong Specification Status is choosen for ' . $pro_title . '';
								$error++;
							}
							else
							{
								//$specgroupArr = explode(',', $data['specificationgroup']);
								$specificationArr = explode(',', $data['specificationtitle']);
								if ($data['specificationdesc'] != '')
								{
									$specification_textArr = explode('*', $data['specificationdesc']);
								}
								else
								{
									$specification_textArr = array();
								}
								
								if (count($specificationArr) != count($specification_textArr))
								{
									$errorArr[].= 'Check Specification Details. There is not correct for ' . $pro_title . '';
									$error++;
									}else{
									$error == 0;
								}
								
							}
						}
						
						$pro_qty = $data['proqty'];
						if ($pro_qty == "" || $pro_qty == "0")
						{
							$errorArr[].= 'Product Quantity Is Must For ' . $pro_title . '';
							$error++;
						}
						elseif (!is_numeric($pro_qty))
						{
							$errorArr[].= 'Product quantity Should Be Number for ' . $pro_title . '';
							$error++;
						}
						
						$pro_mkeywords = $data['metakeyword'];
						$pro_mdesc = $data['metadesc'];
						if (isset($data['image1']) && $data['image1'] != '')
						{
							$video_image_url_1 = "product-bulk-upload/" . $data['image1'];
						}
						else
						{
							$video_image_url_1 = '';
						}
						
						if (isset($data['image2']) && $data['image2'] != '')
						{
							$video_image_url_2 = "product-bulk-upload/" . $data['image2'];
						}
						else
						{
							$video_image_url_2 = '';
						}
						
						if (isset($data['image3']) && $data['image3'] != '')
						{
							$video_image_url_3 = "product-bulk-upload/" . $data['image3'];
						}
						else
						{
							$video_image_url_3 = '';
						}
						
						if (isset($data['image4']) && $data['image4'] != '')
						{
							$video_image_url_4 = "product-bulk-upload/" . $data['image4'];
						}
						else
						{
							$video_image_url_4 = '';
						}
						
						if (isset($data['image5']) && $data['image5'] != '')
						{
							$video_image_url_5 = "product-bulk-upload/" . $data['image5'];
						}
						else
						{
							$video_image_url_5 = '';
						}
						
						/*---------------------------------------*/
						if ($video_image_url_1 != '')
						{
							if (strpos($video_image_url_1, '.jpg') == true || strpos($video_image_url_1, '.jpeg') == true || strpos($video_image_url_1, '.png') == true || $video_image_url_1 != '')
							{
								$url_Arr = explode('/', $video_image_url_1);
								$image_name = end($url_Arr);
								
								// echo basename($video_image_url_1);
								
								if (@getimagesize($video_image_url_1))
								{
									$size = getimagesize($video_image_url_1);
								}
								else
								{
									$size = array();
									$error++;
									
									
									$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
								}
								
								if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
								{
									$error++;
									$errorArr[].= 'Invalid Image Size. Need width800px , height 800px ' . $data['protitle'] . '';
								}
							}
							else
							{
								$error++;
								$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
							}
						}
						
						/*---------------------------------------*/
						if ($video_image_url_2 != '')
						{
							if (strpos($video_image_url_2, '.jpg') == true || strpos($video_image_url_2, '.jpeg') == true || strpos($video_image_url_2, '.png') == true)
							{
								$url_Arr = explode('/', $video_image_url_2);
								$image_name = end($url_Arr);
								if (@getimagesize($video_image_url_2))
								{
									$size = getimagesize($video_image_url_2);
								}
								else
								{
									$size = array();
									$error++;
									$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
								}
								
								if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
								{
									$error++;
									$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
								}
							}
							else
							{
								$error++;
								$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
							}
						}
						
						/*-------------------------------------------------------------*/
						if ($video_image_url_3 != '')
						{
							if (strpos($video_image_url_3, '.jpg') == true || strpos($video_image_url_3, '.jpeg') == true || strpos($video_image_url_3, '.png') == true)
							{
								$url_Arr = explode('/', $video_image_url_3);
								$image_name = end($url_Arr);
								if (@getimagesize($video_image_url_3))
								{
									$size = getimagesize($video_image_url_3);
								}
								else
								{
									$size = array();
									$error++;
									$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
								}
								
								if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
								{
									$error++;
									$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
								}
							}
							else
							{
								$error++;
								$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
							}
						}
						
						/*-------------------------------------------------------------*/
						if ($video_image_url_4 != '')
						{
							if (strpos($video_image_url_4, '.jpg') == true || strpos($video_image_url_4, '.jpeg') == true || strpos($video_image_url_4, '.png') == true)
							{
								$url_Arr = explode('/', $video_image_url_4);
								$image_name = end($url_Arr);
								if (@getimagesize($video_image_url_4))
								{
									$size = getimagesize($video_image_url_4);
								}
								else
								{
									$size = array();
									$error++;
									$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
								}
								
								if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
								{
									$error++;
									$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
								}
							}
							else
							{
								$error++;
								$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
							}
						}
						
						/*-------------------------------------------------------------*/
						if ($video_image_url_5 != '')
						{
							if (strpos($video_image_url_5, '.jpg') == true || strpos($video_image_url_5, '.jpeg') == true || strpos($video_image_url_5, '.png') == true)
							{
								$url_Arr = explode('/', $video_image_url_5);
								$image_name = end($url_Arr);
								if (@getimagesize($video_image_url_5))
								{
									$size = getimagesize($video_image_url_5);
								}
								else
								{
									$size = array();
									$error++;
									$errorArr[].= 'Image file does not exsist. Give Correct image Path for ' . $data['protitle'] . '';
								}
								
								if (count($size) > 0 && $size[0] != '800' && $size[1] != '800')
								{
									$error++;
									$errorArr[].= 'Invalid Image Size. Need width 800px , height 800px ' . $data['protitle'] . '';
								}
							}
							else
							{
								$error++;
								$errorArr[].= 'Invalid File URL ' . $data['protitle'] . '';
							}
						}
						
						/* Sathyaseelan === policy-start */
						$allow_cancel = $allow_return = $allow_replace = $cancel_policy = $return_policy = $replace_policy = $cancel_days = $return_days = $replace_days = "";
						
						
						
						
						
						/* Sathyaseelan === Policy Close */
						if ($error > 0)
						{
							Session::flash('message', $errorArr);
							
							// return Redirect('mer_product_bulk_upload');
							
						}
						else
						{
							$insertArr = array(
							'pro_item_name' => $pro_title,
							'pro_item_code' => $pro_code,
							'pro_category_id' => $pro_mc_id,              
							'pro_sub_cat_id' => $pro_sb_id,
							'pro_original_price' => $pro_price,
							'pro_has_discount' => $pro_has_discount,
							'pro_discount_price' => $pro_disprice,
							'pro_desc' => $pro_desc,
							'pro_had_spec' => $pro_isspec,
							'pro_quantity' => $pro_qty,
							'pro_created_date' => date('Y-m-d') ,
							'pro_store_id' => $store_id,
							'pro_meta_keyword' => $pro_mkeywords,
							'pro_meta_desc' => $pro_mdesc,
							'pro_had_tax' => $pro_had_tax,
							'pro_tax_name' => $pro_tax_name,
							'pro_tax_percent' => $pro_tax,
							'pro_had_choice' => '2',
							'pro_type' => '1',
							'pro_status' => '1',
							'added_by' => '0',
							'pro_currency' => con_default_currency,
							'pro_per_product' => $pro_content
							);
							DB::table('gr_product')->insert($insertArr);
							$last_pro_id = DB::getPdo()->lastInsertId();
							/*-------------------------------------------------------------*/
							
							
							/*specification insert*/
							if (isset($specificationArr) && isset($specification_textArr) && count($specificationArr) > 0 && count($specification_textArr) > 0)
							{
								
								for($s=0; $s <= count($specificationArr); $s++)
								{
									if (isset($specification_textArr[$s]))
									{
										//$get_spg_id = DB::table('nm_specification')->where('sp_id', $sp_id)->first();
										$specification_insertArr = array(
										'spec_pro_id' => $last_pro_id,
										'spec_title' => $specificationArr[$s],
										'spec_desc' => $specification_textArr[$s]
										);
										DB::table('gr_product_spec')->insert($specification_insertArr);
									}
									
									
								}
							}
							
							
							$image_insert_arr = array();
							$folder_name = "";
							//echo $video_image_url_1.'/'.$video_image_url_2;
							/*---------------------------------------*/
							if ($video_image_url_1 != '')
							{
								if (strpos($video_image_url_1, '.jpg') == true || strpos($video_image_url_1, '.jpeg') == true || strpos($video_image_url_1, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_1);
									$folder_name = $url_Arr[1];
									if (!in_array($folder_name, $folder_array))
									{
										array_push($folder_array, $folder_name);
									}
									
									$image_name = end($url_Arr);
									$size = getimagesize($video_image_url_1);
									if ($size[0] == '800' && $size[1] == '800')
									{
										$current_time = time();
										
										//copy($video_image_url_1,"public/images/store/product/$image_name");
										
										if (strpos($video_image_url_1, '.jpg') == true)
										{
											$my_server_img = $video_image_url_1;
											
											$img = @imagecreatefromjpeg($my_server_img);
											
											$path = 'public/images/store/products/' . $current_time . $image_name;
											if ($img)
											{
												imagejpeg($img, $path);
											}
										}
										elseif (strpos($video_image_url_1, '.png') == true)
										{
											$my_server_img = $video_image_url_1;
											$img = @imagecreatefrompng($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											if ($img)
											{ 
												imagepng($img, $path);
											}
										}
										
										array_push($image_insert_arr, $current_time . $image_name . '/**/');
									}
								}
							}
							
							/*---------------------------------------*/
							if ($video_image_url_2 != '')
							{
								if (strpos($video_image_url_2, '.jpg') == true || strpos($video_image_url_2, '.jpeg') == true || strpos($video_image_url_2, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_2);
									$folder_name = $url_Arr[1];
									if (!in_array($folder_name, $folder_array))
									{
										array_push($folder_array, $folder_name);
									}
									
									$image_name = end($url_Arr);
									$size = getimagesize($video_image_url_2);
									if ($size[0] == '800' && $size[1] == '800')
									{
										$current_time = time();
										
										// copy($video_image_url_2,"public/assets/product/$image_name");
										
										if (strpos($video_image_url_2, '.jpg') == true)
										{
											$my_server_img = $video_image_url_2;
											$img = imagecreatefromjpeg($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagejpeg($img, $path);
										}
										elseif (strpos($video_image_url_2, '.png') == true)
										{
											$my_server_img = $video_image_url_2;
											$img = imagecreatefrompng($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagepng($img, $path);
										}
										
										array_push($image_insert_arr, $current_time . $image_name . '/**/');
									}
								}
							}
							
							/*---------------------------------------*/
							if ($video_image_url_3 != '')
							{
								if (strpos($video_image_url_3, '.jpg') == true || strpos($video_image_url_3, '.jpeg') == true || strpos($video_image_url_3, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_3);
									$folder_name = $url_Arr[1];
									if (!in_array($folder_name, $folder_array))
									{
										array_push($folder_array, $folder_name);
									}
									
									$image_name = end($url_Arr);
									$size = getimagesize($video_image_url_3);
									if ($size[0] == '800' && $size[1] == '800')
									{
										$current_time = time();
										
										// copy($video_image_url_3,"public/assets/product/$image_name");
										
										if (strpos($video_image_url_3, '.jpg') == true)
										{
											$my_server_img = $video_image_url_3;
											$img = imagecreatefromjpeg($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagejpeg($img, $path);
										}
										elseif (strpos($video_image_url_3, '.png') == true)
										{
											$my_server_img = $video_image_url_3;
											$img = imagecreatefrompng($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagepng($img, $path);
										}
										
										array_push($image_insert_arr, $current_time . $image_name . '/**/');
									}
								}
							}
							
							/*---------------------------------------*/
							if ($video_image_url_4 != '')
							{
								if (strpos($video_image_url_4, '.jpg') == true || strpos($video_image_url_4, '.jpeg') == true || strpos($video_image_url_4, '.png') == true)
								{
									$current_time = time();
									$url_Arr = explode('/', $video_image_url_4);
									$folder_name = $url_Arr[1];
									if (!in_array($folder_name, $folder_array))
									{
										array_push($folder_array, $folder_name);
									}
									
									$image_name = end($url_Arr);
									$size = getimagesize($video_image_url_4);
									if ($size[0] == '800' && $size[1] == '800')
									{
										
										// copy($video_image_url_4,"public/assets/product/$image_name");
										
										if (strpos($video_image_url_4, '.jpg') == true)
										{
											$my_server_img = $video_image_url_4;
											$img = imagecreatefromjpeg($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagejpeg($img, $path);
										}
										elseif (strpos($video_image_url_4, '.png') == true)
										{
											$my_server_img = $video_image_url_4;
											$img = imagecreatefrompng($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagepng($img, $path);
										}
										
										array_push($image_insert_arr, $current_time . $image_name . '/**/');
									}
								}
							}
							
							/*---------------------------------------*/
							if ($video_image_url_5 != '')
							{
								
								// strpos($video_image_url_1, 'youtube') == true
								
								if (strpos($video_image_url_5, '.jpg') == true || strpos($video_image_url_5, '.jpeg') == true || strpos($video_image_url_5, '.png') == true)
								{
									$url_Arr = explode('/', $video_image_url_5);
									$folder_name = $url_Arr[1];
									if (!in_array($folder_name, $folder_array))
									{
										array_push($folder_array, $folder_name);
									}
									
									$image_name = end($url_Arr);
									$size = getimagesize($video_image_url_5);
									if ($size[0] == '800' && $size[1] == '800')
									{
										$current_time = time();
										
										// copy($video_image_url_5,"public/assets/product/$image_name");
										
										if (strpos($video_image_url_5, '.jpg') == true)
										{
											$my_server_img = $video_image_url_5;
											$img = imagecreatefromjpeg($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagejpeg($img, $path);
										}
										elseif (strpos($video_image_url_5, '.png') == true)
										{
											$my_server_img = $video_image_url_5;
											$img = imagecreatefrompng($my_server_img);
											$path = 'public/images/store/products/' . $current_time . $image_name;
											imagepng($img, $path);
										}
										
										array_push($image_insert_arr, $current_time . $image_name . '/**/');
									}
								}
							}
							
							$imgs = "";
							if (!empty($image_insert_arr))
							{
								foreach($image_insert_arr as $images)
								{
									$imgs.= $images;
								}
							}
							
							$image_insert_arr = array(
							'pro_images' => $imgs
							);
							DB::table('gr_product')->where('pro_id', $last_pro_id)->update($image_insert_arr);
							/*-----------------------------------------------------*/
							$successArr[].= '' . $data['protitle'] . ' Product Uploaded!';
							Session::flash('success_message', $successArr);
						}
					}
				}
				else
				{
					Session::flash('message', $errorArr);
				}
				
				if (count($folder_array) > 0)
				{
					foreach($folder_array as $folder_name)
					{
						if ($folder_name != "")
						{
							$path = 'product-bulk-upload/' . $folder_name;
							/* chown($path, 0777); */
							/* first delete all files in folder */
							if (!is_dir($path))
							{
								throw new InvalidArgumentException("$path must be a directory");
							}
							
							if (substr($path, strlen($path) - 1, 1) != '/')
							{
								$path.= '/';
							}
							
							$files = glob($path . '*', GLOB_MARK);
							foreach($files as $file)
							{
								if (is_dir($file))
								{
									self::deleteDir($file);
								}
								else
								{
									unlink($file);
								}
							}
							
							/* close */
							if (rmdir($path))
							{
								/* then delete folder */
								Session::flash('zip_success_message', "Folder deleted successfully!");
							}
							else
							{
								Session::flash('zip_error_message', "Error in deleting folder!!");
							}
						}
					}
				}
				
				return Redirect('product_bulk_upload');
			}
		}
		/* END PRODUCT BULK UPLOAD FORM SUBMIT */  
	}	