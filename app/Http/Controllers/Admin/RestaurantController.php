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
	
	use App\Restaurant;
	use App\Store;
	
	use Excel;
	
	use Response;
	
	use File;
	
	use Image;
    use Mail;
	
	
	class RestaurantController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
			
		}
		
		/** add restaurant **/
		public function add_restaurant()
		{
						
			if(Session::has('admin_id') == 1)
			{ 
				$category_list = DB::table('gr_category')->where(['cate_status' => '1','cate_type'=>'1'])->orderBy('cate_name','asc')->pluck('cate_name','cate_id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'), '0')->toarray();
				$merchant_list = DB::table('gr_merchant')->select('id',DB::raw("concat(if(mer_fname is null,'',mer_fname), ' - ', if(mer_email is null,'',mer_email)) as full_name"))->where(['mer_status' => '1','has_shop'=>'0','mer_business_type' => '2'])->pluck('full_name','id')->prepend((Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT'), '0')->toarray();
				$url='add-restaurant-submit';
				$array_name = array();
				foreach(DB::getSchemaBuilder()->getColumnListing('gr_store') as $res)
				{
					$array_name[$res]='';
				}
				$object = (object) $array_name; // return all value as empty.
				//make working hours array as empty
				$object1 = array();
				
				$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_REST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_REST') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_REST');
				return view('Admin.restaurant.add_restaurant')->with(['category_list' => $category_list,'merchant_list' => $merchant_list,'pagetitle' => $pagetitle,'getrestaurant'=>$object,'url'=>$url,'wk_hours'=>$object1]);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function add_restaurant_submit(Request $request)
		{
		
			if(Session::has('admin_id') == 1)
			{ 	
				
				$banner_err = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_BANNER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_BANNER');
				$logo_err = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_LOGO')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_LOGO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_LOGO');
				$validator = Validator::make($request->all(), [
				
				'mer_fname' => 'required',
				
				'cate_name' => 'required',
				
				'rs_name' => 'required',
				
				'rs_desc' =>'required',
				
				'del_radius' =>'required',
				
				'del_time' =>'required',
				
				'rs_addr' => 'required',
				
				'rs_lat' => 'required',
				
				'rs_long' => 'required',
				'min_order' => 'required',
				
				'rs_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
				'rs_banner' => 'required',
 				'rs_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500'
	            
	        	],[ 'rs_banner.*.required'   => $banner_err,
				'rs_banner.*.image'   => $banner_err,
				'rs_banner.*.mimes'   => $banner_err,
				'rs_banner.*.dimensions'   => $banner_err,
				'rs_logo.required' => $logo_err,
				'rs_logo.|image|mimes|dimensions' => $logo_err]);
				if ($validator->fails()) {
		            return redirect('add-restaurant')->withErrors($validator)
					->withInput();
				}else{
					/*SEO URL */ 
					$seourl = str_slug(Input::get('rs_name'));
					$generated_seourl = generate_seourl($seourl,'gr_store','','','store_slug');
					/*EOF SEO URL */
		        	$add = new Restaurant;
           			$add->st_mer_id 			= Input::get('mer_fname');
           			$add->st_store_name 		= ucfirst(Input::get('rs_name'));
           			$add->store_slug 			= $generated_seourl;
           			$add->st_category 			= Input::get('cate_name');
           			$add->st_minimum_order		= Input::get('min_order');
           			$add->st_currency			= Input::get('curr_code');
           			$add->st_delivery_radius	= Input::get('del_radius');
           			$add->st_delivery_time		= Input::get('del_time');
           			$add->st_delivery_duration	= Input::get('deli_duration');
           			$add->st_pre_order			= Input::get('pre_order');
           			$add->st_desc				= Input::get('rs_desc');
           			$add->st_address			= Input::get('rs_addr');
           			$add->st_latitude			= Input::get('rs_lat');
           			$add->st_longitude			= Input::get('rs_long');
           			$add->st_type				= 1; // restaurant
           			$add->save();
           			$insert_id = $add->id;
			        $mer_id 	= Input::get('mer_fname');
					$st_logo = '';
					$banner_file = '';
					$logo = '';
					$banner = Input::file('rs_banner');
					
					/** update logo **/
					if($request->hasFile('rs_logo'))  //add or update new logo
					{
		        		$st_logo = 'restaurant'.rand().'.'.request()->rs_logo->getClientOriginalExtension();
			        	$destinationPath = public_path('images/restaurant');
			        	$customer = Image::make(request()->rs_logo->getRealPath())->resize(300, 300);
			        	$customer->save($destinationPath.'/'.$st_logo,80);
			        	$insertArr['st_logo'] = $st_logo;
					}
					
					/** update banner **/
					if($request->hasFile('rs_banner')) // add or update new banner images
					{
			        	$count = count($banner);
			        	for($i = 0; $i< $count; $i++)
			        	{
							$st_banner = 'restaurant'.rand().'.'.$banner[$i]->getClientOriginalExtension();
							$destinationPath = public_path('images/restaurant/banner');
							$customer = Image::make($banner[$i]->getRealPath())->resize(1366, 300);
							$banner_file .= $st_banner."/**/";
							$customer->save($destinationPath.'/'.$st_banner,80);
						}
			        	$insertArr['st_banner'] = $banner_file;
					}
					
					if(count($this->get_Adminactive_language) > 0)
					{
						foreach($this->get_Adminactive_language as $Lang)
						{
							
                            $validatoradv = Validator::make($request->all(), [
							'restaurant_'.$Lang->lang_code => 'required',
							'rs_desc_'.$Lang->lang_code => 'required'
                            ]);
                            if($validatoradv->fails()){
                                return redirect('add-restaurant')->withErrors($validatoradv)->withInput();
								}else {
                                $insertArr['st_store_name_'.$Lang->lang_code] = Input::get('restaurant_'.$Lang->lang_code);
                                $insertArr['st_desc_'.$Lang->lang_code] = Input::get('rs_desc_'.$Lang->lang_code);
							}
							
							
						}
					}
					
					$insert = updatevalues('gr_store',$insertArr,['id' => $insert_id]);
					
					//update working hours			        		
					$wk_array = array();
					for($i=1;$i<=7;$i++)
					{	
						$wk_array['wk_res_id'] = $insert_id;
						$wk_array['wk_date'] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DAY'.$i.'')) ? trans(Session::get('admin_lang_file').'.ADMIN_DAY'.$i.'') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DAY'.$i.'');
						$wk_array['wk_closed']=(Input::get('closed'.$i)!=null)?Input::get('closed'.$i):'0';
						$wk_array['wk_start_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['old_start_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['wk_end_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$wk_array['old_end_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$update = insertvalues('gr_res_working_hrs',$wk_array);
					}
					
					//UPDATE STORE CATEGORY 
					DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count+1 where cate_id = '".Input::get('cate_name')."'");
					//EOF UPDATE STORE CATEGORY 
				
					//update merchant has shop
					$update_mercahnt = updatevalues('gr_merchant',['has_shop' => 1],['id' => $mer_id]);
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					
					Session::flash('message',$msg);
					return Redirect::to('manage-restaurant');
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** manage store **/
		public function restaurant_management()
		{
    		if(Session::has('admin_id') == 1)
			{
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_REST')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_REST') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MNGE_REST');
				$type = 1; // 1 for restaurant
				$get_all_details = array();//Restaurant::get_all_details($type); 
				return view('Admin.restaurant.manage_restaurant')->with('pagetitle',$page_title)->with('all_details',$get_all_details);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function ajax_restaurant_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$columns = array(
				0 =>'id',
				1 =>'id',
				2 =>'st_store_name',
				3=> 'mer_fname',
				4=> 'cate_name',
				5=> 'added_by',
				6=> 'id',
				7=> 'st_status',
				8=> 'id'
				);
				$type = 1;
				/*To get Total count */
				$totalData = Store::get_all_details($type)->count();
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$storeName_search = trim($request->storeName_search);
				$merName_search = trim($request->merName_search);
				$cat_search = trim($request->cat_search);
				$addedBy_search = trim($request->addedBy_search);
				$status_search = trim($request->status_search);
				if($storeName_search=='' && $merName_search=='' && $cat_search=='' && $addedBy_search=='' && $status_search=='')
				{
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_store')
					->select('gr_store.id','gr_merchant.mer_fname','gr_merchant.mer_lname','gr_merchant.mer_email','gr_category.cate_name','st_store_name','st_status','st_logo','gr_store.added_by','gr_store.st_type','gr_store.st_mer_id')
					->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
					->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
					->where('gr_merchant.mer_status','!=','2')
					->where('gr_category.cate_status','!=','2')
					->where('gr_store.st_status','!=',2)
					->where('gr_store.st_type','=',$type)
					->orderBy($order,$dir)->skip($start)->take($limit)->get();
					
				}
				else {
					
					$sql =	DB::table('gr_store')
					->select('gr_store.id','gr_merchant.mer_fname','gr_merchant.mer_lname','gr_merchant.mer_email','gr_category.cate_name','st_store_name','st_status','st_logo','gr_store.added_by','gr_store.st_type','gr_store.st_mer_id')
					->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
					->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
					->where('gr_merchant.mer_status','!=','2')
					->where('gr_category.cate_status','!=','2')
					->where('gr_store.st_status','!=',2)
					->where('gr_store.st_type','=',$type);
					if($storeName_search != '')
					{
						$q = $sql->whereRaw("gr_store.st_store_name like '%".$storeName_search."%'");
					}
					if($merName_search != '')
					{
						$q = $sql->whereRaw("Concat(if(gr_merchant.mer_fname is null,'',gr_merchant.mer_fname),' ',if(gr_merchant.mer_lname is null,'',gr_merchant.mer_lname)) like '%".$merName_search."%'");
					}
					if($cat_search != '')
					{
						$q = $sql->whereRaw("gr_category.cate_name like '%".$cat_search."%'");
					}
					if($addedBy_search == 'admin')
					{
						$q = $sql->where("gr_store.added_by",'0');
					}
					if($addedBy_search == 'merchant')
					{
						$q = $sql->where("gr_store.added_by",'>','0');
					}
					if($status_search != '')
					{
						$q = $sql->where('gr_store.st_status',$status_search);
					}
					$totalFiltered = $q->count();
					//DB::connection()->enableQueryLog();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					
					
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
					$phrase  = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE_BLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE_BLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STORE_BLOCK_ERROR');
					$blockPhrase = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STORE_UNBLOCK_ERROR');
					$click_to_block = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
					$click_to_unblock = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
					$click_to_edit = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
					$click_to_delete = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
					foreach ($posts as $post)
					{
						if($post->st_type==1) 
						{
							$store_type=(Lang::has(Session::get('admin_lang_file').'.ADMIN_RTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_RTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RTS'); 
							$pro_type = 2; 
							$product_type = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ITS'); 
						}
						else 
						{ 
							$store_type=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ST');  
							$pro_type=1; 
							$product_type=(Lang::has(Session::get('admin_lang_file').'.ADMIN_PROS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PROS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PROS');
						}
						$getCartProduct = getCartItemsUsedThisStore($post->id,$pro_type);
						$cartCount = $getCartProduct[0]->usedCount;
						
						$getUsedProduct = getProductsUsedThisStore($post->id,$pro_type,'block');
						$usedProduct = $getUsedProduct[0]->usedProduct;
						$usedCount = $getUsedProduct[0]->usedCount;
						
						$totalBlockCount = $usedCount+$cartCount;
						
						if($totalBlockCount > 0 ){
							$search_str = array(":store_type", ":num_products", ":product_type", ":num_items", ":action_text");
							$replace_str= array($store_type, $usedCount, $product_type, $cartCount, 'block');
							$new_string = str_replace($search_str, $replace_str, $phrase);
							$toolTipText = $new_string;//$usedCount.' products are added in this store. Please block those products before you block this store';
							$onclickfun = 'onclick="return confirm(\''.$toolTipText.'\');"';
							}else {
							$onclickfun = '';
						}
						
						
						if($post->st_status == 1)
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',0);" id="statusLink_'.$post->id.'" '.$onclickfun.'><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
						}
						else
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',1);" id="statusLink_'.$post->id.'" onclick="return confirm(\''.str_replace(":product_type",$product_type,$blockPhrase).'\');"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'" ></i></a>';
						}
						
						
						if($post->added_by == 0){
							$addedbyText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
							} else{ 
							$addedbyText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						} 
						
						$getUsedProduct = getProductsUsedThisStore($post->id,$pro_type,'delete');
						$usedCount = $getUsedProduct[0]->usedCount;
						$totalDeleteCount = $usedCount+$cartCount;
						
						if($totalDeleteCount > 0 ){
							$search_str = array(":store_type", ":num_products", ":product_type", ":num_items", ":action_text");
							$replace_str= array($store_type, $usedCount, $product_type, $cartCount, 'delete');
							$new_string = str_replace($search_str, $replace_str, $phrase);
							$deleteToolTipText = $new_string;//$usedCount.' products are added in this store. Please delete those products before you delete this store';
							$onclickfun = 'onclick="return confirm(\''.$deleteToolTipText.'\');"';
							}else {
							$onclickfun = '';
						}
						
						$deleteLink = '<a href= "javascript:individual_change_status(\''.$post->id.'\',2);" title="'.$click_to_delete.'" class="tooltip-demo" id="statusLink_'.$post->id.'" '.$onclickfun.'><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
						$chkName = 'chk[]';
						$chkDisabled='';
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass"  name="'.$chkName.'" '.$chkDisabled.' value="'.$post->id.'" >';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['storeName'] = $post->st_store_name;
						$nestedData['merName'] = $post->mer_fname.' '.$post->mer_lname.'<br>(<span style="color:#021a31;">'.$post->mer_email.')</span>';
						$nestedData['category'] = $post->cate_name;
						$nestedData['addedBy'] = $addedbyText;
						$nestedData['Edit'] = '<a href="'.url('edit-restaurant').'/'.base64_encode($post->id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
						$nestedData['Status'] = $statusLink;
						$nestedData['delete'] = $deleteLink;
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
		
		/** edit store **/
		public function edit_restaurant($id)
		{
			if(Session::has('admin_id') == 1)
			{ 
				
				$merchant_list = $category_list = array();
				$url = 'update-restaurant';
				$object = Restaurant::get_details(base64_decode($id)); 
				$get_hours = get_working_hours(base64_decode($id));
				$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_EDIT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REST_EDIT');
				return view('Admin.restaurant.add_restaurant')->with(['category_list' => $category_list,'merchant_list' => $merchant_list,'pagetitle' => $pagetitle,'getrestaurant'=>$object,'url' => $url,'wk_hours' => $get_hours]);
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** Remove banner **/
		public function remove_restaurant_banner()
		{
			if(Session::has('admin_id') == 1)
			{
				$file = Input::get('file'); //file to remove
				$store_id = Input::get('id');
				$old_banner = Input::get('old_ban');
				$banner = explode('/**/',$old_banner,-1);
				$value = '';
				for($i=0;$i<count($banner); $i++)
				{
					if($banner[$i] != $file) //add filename except selected banner
					{	
						$value .= $banner[$i].'/**/';
					}
					elseif($banner[$i] == $file)
					{
						$image_path = public_path('images/restaurant/banner/').$banner[$i];  // Value is not URL but directory file path
						//echo $image_path; exit;
						if(File::exists($image_path)) {
							$a =   File::delete($image_path);
							//echo $a; exit;
						}
					}
				}
				
				$update = updatevalues('gr_store',['st_banner'=>$value],['id' => $store_id]);
				return $value; exit;
			}
			else
			{
				return Redirect::to('admin-login');	
			}
		}
		
		/** update restaurant **/
		public function update_restaurant(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{ 	
				//print_r(Input::file('rs_banner')); exit;
				$banner_err = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_BANNER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_BANNER');
				$logo_err = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_LOGO')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_LOGO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_LOGO');
				$validator = Validator::make($request->all(), [
				
				'rs_name' => 'required',
				
				'rs_desc' =>'required',
				
				'del_radius' =>'required',
				
				'del_time' =>'required',
				
				'rs_addr' => 'required',
				
				'rs_lat' => 'required',
				
				'rs_long' => 'required',
				'min_order' => 'required',
				
				'rs_logo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
				'rs_banner' => 'Sometimes',
 				'rs_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500'
	            
	        	],[ 'rs_banner.*.Sometimes'   => $banner_err,
				'rs_banner.*.image'   => $banner_err,
				'rs_banner.*.mimes'   => $banner_err,
				'rs_banner.*.dimensions'   => $banner_err,
				'rs_logo.Sometimes' => $logo_err,
				'rs_logo.|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500' => $logo_err]);
				if ($validator->fails()) {
		            return redirect('edit-restaurant/'.base64_encode(Input::get('rs_id')))->withErrors($validator)
					->withInput();
				}else{ 
					$seourl = str_slug(Input::get('rs_name'));
					$primary_id = Input::get('rs_id');
					$generated_seourl = generate_seourl($seourl,'gr_store','id',$primary_id,'store_slug');
		        	$add = Restaurant::find(Input::get('rs_id'));
           			$add->st_store_name 		= ucfirst(Input::get('rs_name'));
					$add->store_slug			= $generated_seourl;
           			$add->st_minimum_order		= Input::get('min_order');
           			$add->st_currency			= Input::get('curr_code');
           			$add->st_delivery_radius	= Input::get('del_radius');
           			$add->st_delivery_time		= Input::get('del_time');
           			$add->st_delivery_duration	= Input::get('deli_duration');
           			$add->st_pre_order			= Input::get('pre_order');
           			$add->st_desc				= Input::get('rs_desc');
           			$add->st_address			= Input::get('rs_addr');
           			$add->st_latitude			= Input::get('rs_lat');
           			$add->st_longitude			= Input::get('rs_long');
           			$add->st_type				= 1; // restaurant
           			$add->save();
					$st_logo = '';
					$st_banner = '';
					$banner_file = '';
					$logo = '';
					$banner = Input::file('rs_banner');
					$insert_id = Input::get('rs_id');
					$old_banner = Input::get('old_banner');
					$old = explode('/**/',$old_banner,-1);
					
					$count = Input::get('count');
					/** update logo **/ 
					if($request->hasFile('rs_logo'))  //add or update new logo
					{
						/** delete old images **/
						$old_image = Input::get('old_logo');
						$image_path = public_path('images/restaurant/').$old_image;  // Value is not URL but directory file path
						if(File::exists($image_path)) 
						{
							$a =   File::delete($image_path);
							
						}
						/** delete old images ends **/
						$st_logo = 'restaurant'.rand().'.'.request()->rs_logo->getClientOriginalExtension();
						$destinationPath = public_path('images/restaurant');
						$customer = Image::make(request()->rs_logo->getRealPath())->resize(300, 300);
						$customer->save($destinationPath.'/'.$st_logo,80);
						$insertArr['st_logo'] = $st_logo;
					}
					/** update banner **/
					if($request->hasFile('rs_banner')) // add or update new banner images
					{ 
						for($i = 0; $i< $count; $i++)
						{
				        	if(array_key_exists($i,$banner)) //In update,if banner image is empty then update with old banner values
				        	{
				        		/** delete old images **/
			        			$old_image = $banner[$i];
	                            $image_path = public_path('images/restaurant/banner/').$old_image;  // Value is not URL but directory file path
	                            if(File::exists($image_path)) 
	                            {
									$a =   File::delete($image_path);
									
								}
                            	/** delete old images ends **/
					        	$st_banner = 'restaurant'.rand().'.'.$banner[$i]->getClientOriginalExtension();
					        	$destinationPath = public_path('images/restaurant/banner');
					        	$customer = Image::make($banner[$i]->getRealPath())->resize(1366, 300);
					        	$customer->save($destinationPath.'/'.$st_banner,80);
							}
				        	else
				        	{ 
				        		$st_banner = $old[$i];
							}
				        	
				        	$banner_file .= $st_banner."/**/";
						}
					}
					elseif($request->hasFile('rs_banner') == 0)  //update old banner files
					{ 
						$banner_file = $old_banner;
					}
					
					$insertArr['st_banner'] = $banner_file;
					
					
					if(count($this->get_Adminactive_language) > 0)
					{
						foreach($this->get_Adminactive_language as $Lang)
						{
                            $validatoradv = Validator::make($request->all(), [
							'restaurant_'.$Lang->lang_code => 'required',
							'rs_desc_'.$Lang->lang_code => 'required'
                            ]);
                            if($validatoradv->fails()){
                                return redirect('add-restaurant')->withErrors($validatoradv)->withInput();
								}else {
                                $insertArr['st_store_name_'.$Lang->lang_code] = Input::get('restaurant_'.$Lang->lang_code);
                                $insertArr['st_desc_'.$Lang->lang_code] = Input::get('rs_desc_'.$Lang->lang_code);
							}
							
						}
					}
					//print_r($insertArr); exit;
					$insert = updatevalues('gr_store',$insertArr,['id' => $insert_id]);
					
					//update working hours			        		
					$wk_array = array();
					for($i=1;$i<=7;$i++)
					{	
						$wk_date = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'')) ? trans(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DAY'.$i.'');
						$wk_array['wk_closed']=(Input::get('closed'.$i)!=null)?Input::get('closed'.$i):'0';
						$wk_array['wk_start_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['old_start_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['wk_end_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$wk_array['old_end_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						
						//$wk_array['wk_start_time']	= Input::get('wk_start_'.$i);
						//$wk_array['wk_end_time']	= Input::get('wk_end_'.$i);
						$update = updatevalues('gr_res_working_hrs',$wk_array,['wk_res_id' => $insert_id,'wk_date' => $wk_date]);
						//echo $update; exit;
					}
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					
					Session::flash('message',$msg);
					return Redirect::to('manage-restaurant');
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** block/unblock category **/
		public function restaurant_status(Request $request)
		{	
			$status = $request->status;
			$id 	= $request->id;
			$mer_id 	= $request->mer_id;
			$update = ['st_status' => $status];
			$where = ['id' => $id];
			$a = updatevalues('gr_store',$update,$where);
			/* send mail to merchant */
			$related_details = get_related_details('gr_merchant',['id' => $mer_id],['mer_email','mer_fname'],'individual');
			if(!empty($related_details))
			{
				$send_mail_data = array('name' => $related_details->mer_fname,'status' => $status);
				$mail = $related_details->mer_email;
				Mail::send('email.store_status_mail', $send_mail_data, function($message) use($mail)
				{
					$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ST_STATUS_CH');
					$message->to($mail)->subject($subject);
				});
			}
			/* send mail to merchant ends */
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
			}
			if($status == 2) //Delete
			{	
				/* delete store related images  */
				$related_details = get_related_details('gr_store',['id' => $id],['st_logo','st_banner'],'individual');
				if(!empty($related_details))
				{
					$delete_img = delete_restaurant_images($related_details);
				}
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
			}
			else   //block
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
			}
			return "success";
			
		}
		
        public function multi_restaurant_block()
        {
			
            $update = ['st_status' => Input::get('status')];
            $val = Input::get('val');
            $status = Input::get('status');
            //return count($val); exit;
            for($i=0; $i< count($val); $i++)
            {
				if($status==0){
					DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count-1 where cate_id = (SELECT st_category FROM gr_store WHERE id = '".$val[$i]."')");
				}elseif($status==2){
					$get_old_status  = DB::table('gr_store')->select('gr_category.cate_status')
										->join('gr_category','gr_category.cate_id','=','gr_store.st_category') 
										->where('gr_store.id','=',$val[$i])    
										->first();
					if($get_old_status->cate_status!='0'){
						DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count-1 where cate_id = (SELECT st_category FROM gr_store WHERE id = '".$val[$i]."')");
					}
				}else{			
					
					DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count+1 where cate_id = (SELECT st_category FROM gr_store WHERE id = '".$val[$i]."')");
				}
				
                $where = ['id' => $val[$i]];
                $a = updatevalues('gr_store',$update,$where);
				
                /* delete store related images  */
                if($status == 2)
                {
                    DB::select("DELETE FROM gr_product_spec WHERE spec_pro_id IN (SELECT * FROM (SELECT pro_id FROM gr_product WHERE pro_store_id = '".$val[$i]."'  ) AS p)");
                    DB::select("DELETE FROM gr_product_choice WHERE pc_pro_id IN (SELECT * FROM (SELECT pro_id FROM gr_product WHERE pro_store_id = '".$val[$i]."'  ) AS p)");
                    $related_details = get_related_details('gr_store',['id' => $val[$i]],['st_logo','st_banner'],'individual');
                    if(!empty($related_details))
                    {
                        $delete_img = delete_store_images($related_details);
					}
				}
				
                /*--------------- CHANGE STATUS --------------------*/
                DB::table('gr_product')->where('pro_store_id','=',$val[$i])->update(['pro_status'=>$status]);
                DB::table('gr_cart_save')->where('cart_st_id','=',$val[$i])->delete();
                /* send mail to merchant */
                $related_details = get_merchant_details($val[$i]);
				if(!empty($related_details))
				{
					$send_mail_data = array('name' => $related_details->mer_fname,'status' => $status);
					$mail = $related_details->mer_email;
					Mail::send('email.store_status_mail', $send_mail_data, function($message) use($mail)
					{
						$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ST_STATUS_CH');
						$message->to($mail)->subject($subject);
					});
				}
                /* send mail to merchant ends */
			}
            //echo Input::get('status'); exit;
            if(Input::get('status') == 1) //Active
            {
				
                $msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
                echo $msg;
				
			}
            if(Input::get('status') == 2) //Delete
            {
				
                $msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
                echo $msg;
				
			}
            elseif(Input::get('status') == 0)   //block
            {
				
                $msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
                echo $msg;
				
			}
		}
		
		public function ajax_change_res_status(Request $request){
			$res_id = $request->get('res_id');
			$status = $request->get('status');
			
			$update = ['st_status' => $status];
			$where = ['id' => $res_id,'st_type'=>1];
			$cate_status = updatevalues('gr_store',$update,$where);
			
			if($cate_status){
				$product_sts = updatevalues('gr_product',['pro_status'=>$status],['pro_store_id'=>$res_id,'pro_type'=>2]);
			}
			
			
		}
		
		public function check_restName_exists(Request $request){
			/*print_r($request->all()); exit;*/
			/*Array ( [hidRestId] => [column] => rs_name [column_value] => test )*/
			$checkName = DB::table('gr_store')->select('id')->where($request->column,'=',$request->column_value);
			if($request->hidRestId!=''){
				$checkName->where($id,'!=',$request->hidRestId);
			}
			$q = $checkName->get();
			if(count($q) > 0 ){
				$err = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_NAME_EXISTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_NAME_EXISTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_NAME_EXISTS');
			}else{
				$err = '';
			}
			echo $err;
		}
		
		
		
	}		