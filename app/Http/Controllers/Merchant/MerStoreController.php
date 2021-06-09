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
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use App\Store;
	
	use Excel;
	
	use Response;
	
	use File;
	
	use Image;
	
	class MerStoreController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
			
		}
		
		
		/** manage restaurant **/
		public function manage_store()
		{
			if(Session::has('merchantid') == 1)
			{ 
				if(Session::get('mer_has_shop') == 0) //merchant not have shop
				{
					$category_list = DB::table('gr_category')->where(['cate_status' => '1','cate_type'=>'2'])->pluck('cate_name','cate_id')->prepend((Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SELECT'), '0')->toarray();
					$url = 'mer-add-store';
					$array_name = array();
					foreach(DB::getSchemaBuilder()->getColumnListing('gr_store') as $res)
					{
						$array_name[$res]='';
					}
					$object = (object) $array_name; // return all value as empty.
					$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADD_STORE');
					return view('sitemerchant.store.add_store')->with(['category_list' => $category_list,'pagetitle' => $pagetitle,'getstore'=>$object,'url'=>$url]);
				}
				else
				{
					$category_list = array();
					$url = 'mer-update-store';
					$store = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
					
					Session::put('shop_id',$store->id);
					$object = Store::get_details($store->id); 
					$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_EDIT_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_EDIT_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_EDIT_STORE');
					return view('sitemerchant.store.add_store')->with(['category_list' => $category_list,'pagetitle' => $pagetitle,'getstore'=>$object,'url' => $url]);
				}
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		public function add_store(Request $request)
		{	//print_r($request->all()); exit;
			if(Session::has('merchantid') == 1)
			{ 	
				$select = $request->select;
				$cate_name = $input_name = '';
				if($select == "list")
				{
					$cate_name = Input::get('cate_name1');
					$input_name = 'cate_name1';
				}
				elseif($select == "text")
				{
					$cate_name = Input::get('cate_name');
					$input_name = 'cate_name';
				}
				$banner_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_BANNER');
				$logo_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_LOGO');
				
				$validator = Validator::make($request->all(), [
				
				$input_name => 'required',
				
				'st_name' => 'required',
				
				'st_desc' =>'required',
				'del_time' =>'required',
				
				'del_radius' =>'required',
				
				'st_addr' => 'required',
				
				'st_lat' => 'required',
				
				'st_long' => 'required',
				
				'st_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
				'st_banner' => 'required',
 				'st_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500'
	            
	        	],
	        	[ 
				'st_banner.*.required'   => $banner_err,
				'st_banner.*.image'   => $banner_err,
				'st_banner.*.mimes'   => $banner_err,
				'st_banner.*.dimensions'   => $banner_err,
				'st_logo.required' => $logo_err,
				'st_logo.|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500' => $logo_err,
				]);
				if ($validator->fails()) {
		            return redirect('mer-manage-store')
					->withErrors($validator)
					->withInput();
					}else{
					$cate_id = $cate_name;	
					//update category in table
					if(!is_numeric($cate_name))
					{	$name = 'cate_name';
						$old_name = Input::get('cate_name');
						$check = DB::table('gr_category')->select('cate_id')->where([$name => $old_name,'cate_type' => '2'])->where('cate_status','!=' ,'2')->first();
						//print_r($check); exit;
						if(empty($check) === true)
						{
		        			insertvalues('gr_category',['cate_name' => Input::get('cate_name'),'cate_type' => 2,'cate_added_by' => 1]);
			        		$cate_id = DB::getPdo()->lastInsertId();
						}
						else
						{
							$cate_id = $check->cate_id;
						}
						
					}
					$mer_id = Session::get('merchantid');
					
					$str_name = mysql_escape_special_chars(Input::get('st_name'));
					$currency = Input::get('curr_code');
					$min_order = mysql_escape_special_chars(Input::get('min_order_amt'));
					$str_desc = mysql_escape_special_chars(Input::get('st_desc'));
					$str_deli_radius = mysql_escape_special_chars(Input::get('del_radius'));
					$str_deli_time = mysql_escape_special_chars(Input::get('del_time'));
					$str_deli_dur = mysql_escape_special_chars(Input::get('deli_duration'));
					$str_addr = mysql_escape_special_chars(Input::get('st_addr'));
					$str_lat = Input::get('st_lat');
					$str_long = Input::get('st_long');
					$st_logo = '';
					$banner_file = '';
					$logo = '';
					$banner = Input::file('st_banner');
					
					/** update logo **/
					if($request->hasFile('st_logo'))  //add or update new logo
					{
		        		$st_logo = 'store'.rand().'.'.request()->st_logo->getClientOriginalExtension();
			        	$destinationPath = public_path('images/store');
			        	$customer = Image::make(request()->st_logo->getRealPath())->resize(300, 300);
			        	$customer->save($destinationPath.'/'.$st_logo,80);
					}
					
					/** update banner **/
					if($request->hasFile('st_banner')) // add or update new banner images
					{
			        	$count = count($banner);
			        	for($i = 0; $i< $count; $i++)
			        	{
							$st_banner = 'store'.rand().'.'.$banner[$i]->getClientOriginalExtension();
							$destinationPath = public_path('images/store/banner');
							$customer = Image::make($banner[$i]->getRealPath())->resize(1366, 300);
							$banner_file .= $st_banner."/**/";
							$customer->save($destinationPath.'/'.$st_banner,80);
						}
					}
					$insertArr = array(
					'st_mer_id' => $mer_id,
					'st_store_name' => ucfirst($str_name),
					'st_category' => $cate_id,
					'st_currency' => $currency,
					'st_minimum_order' => $min_order,
					'st_delivery_radius' => $str_deli_radius,
					
					'st_delivery_time' => $str_deli_time,
					'st_delivery_duration' => $str_deli_dur,
					'st_desc' => $str_desc,
					'st_address' => $str_addr,
					'st_latitude' => $str_lat,
					'st_longitude' => $str_long,
					'st_logo' => $st_logo,
					'st_banner' => $banner_file,
					'st_type'=> 2,
					'st_status'=>0,
					'added_by' => Session::get('merchantid')
					
					);
					//print_r($insertArr); exit;
					if(count($this->get_Adminactive_language) > 0)
					{
						foreach($this->get_Adminactive_language as $Lang)
						{	
							
							$insertArr['st_store_name_'.$Lang->lang_code] = Input::get('st_name_'.$Lang->lang_code);		
							$insertArr['st_desc_'.$Lang->lang_code] = Input::get('st_desc_'.$Lang->lang_code);		
						}
					}
					
					insertvalues('gr_store',$insertArr);
					$insert = DB::getPdo()->lastInsertId();
					//echo $insert;  exit;
					//update merchant has shop
					$update_mercahnt = updatevalues('gr_merchant',['has_shop' => 1],['id' => $mer_id]);
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					$request->session()->forget('has_shop');
					Session::put('shop_id',$insert);
					Session::put('mer_has_shop',1);
					Session::flash('message',$msg);
					return Redirect::to('mer-manage-store');
				}
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		
		/** Remove banner **/
		public function remove_store_banner()
		{
			if(Session::has('merchantid') == 1)
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
						$image_path = public_path('images/store/banner/').$banner[$i];  // Value is not URL but directory file path
						//echo $image_path; exit;
						if(File::exists($image_path)) {
							$a =   File::delete($image_path);
							//echo $a; exit;
						}
					}
				}
				
				$update = updatevalues('gr_store',['st_banner'=>$value],['id' => $store_id]);
				//echo $value; exit;
				return $value; exit;
			}
			else
			{
				return Redirect::to('merchant-login');	
			}
		}
		
		/** edit submit **/
		public function update_store(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{ 	
				
				$banner_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_BANNER');
				$logo_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_VALID_LOGO');
				$validator = Validator::make($request->all(), [
				
				'st_name' => 'required',
				
				'st_desc' =>'required',
				
				'del_radius' =>'required',
				
				'del_time' =>'required',
				
				'st_addr' => 'required',
				
				'st_lat' => 'required',
				
				'st_long' => 'required',
				
				'st_logo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
				'st_banner' => 'Sometimes',
 				'st_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500'
	            
	        	],[ 
				'st_banner.*.required'   => $banner_err,
				'st_banner.*.image'   => $banner_err,
				'st_banner.*.mimes'   => $banner_err,
				'st_banner.*.dimensions'   => $banner_err,
				'st_logo.required' => $logo_err,
				'st_logo.|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500' => $logo_err]);
				if ($validator->fails()) {
		            return Redirect::back()->withErrors($validator)->withInput();
					}else{
					
					$str_id = Input::get('st_id');
					$str_name = mysql_escape_special_chars(Input::get('st_name'));
					$currency = Input::get('curr_code');
					$min_order = mysql_escape_special_chars(Input::get('min_order_amt'));
					$str_desc = mysql_escape_special_chars(Input::get('st_desc'));
					$str_deli_radius = mysql_escape_special_chars(Input::get('del_radius'));
					$str_deli_time = mysql_escape_special_chars(Input::get('del_time'));
					$str_deli_dur = mysql_escape_special_chars(Input::get('deli_duration'));
					
					$str_addr = mysql_escape_special_chars(Input::get('st_addr'));
					$str_lat = Input::get('st_lat');
					$str_long = Input::get('st_long');
					$st_logo = '';
					$banner_file = '';
					$logo = '';
					$banner = Input::file('st_banner');
					$count = Input::get('count');
					$old_banner = Input::get('old_banner');
					$old = explode('/**/',$old_banner,-1);
					/** update logo **/
					if($request->hasFile('st_logo'))  //add or update new logo
					{
						//delete old logo
						$image_path = public_path('images/store/').Input::get('old_logo'); 
						if(File::exists($image_path))
						{
							$a =File::delete($image_path);
						}
		        		$st_logo = 'store'.rand().'.'.request()->st_logo->getClientOriginalExtension();
			        	$destinationPath = public_path('images/store');
			        	$customer = Image::make(request()->st_logo->getRealPath())->resize(300, 300);
			        	$customer->save($destinationPath.'/'.$st_logo,80);
					}
					else  //update old logo
					{
						$st_logo = Input::get('old_logo');
					}
					/** update banner **/
					if($request->hasFile('st_banner')) // add or update new banner images
					{ 
						for($i = 0; $i< $count; $i++)
						{
				        	if(array_key_exists($i,$banner)) //In update,if banner image is empty then update with old banner values
				        	{
					        	$st_banner = 'store'.rand().'.'.$banner[$i]->getClientOriginalExtension();
					        	$destinationPath = public_path('images/store/banner');
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
					elseif($request->hasFile('st_banner') == 0)  //update old banner files
					{ 
						$banner_file = $old_banner;
					}
					//echo $banner_file; exit;
					$insertArr = array(
					'st_store_name' => ucfirst($str_name),
					'st_currency' => $currency,
					'st_minimum_order' => $min_order,
					'st_delivery_radius' => $str_deli_radius,
					'st_delivery_time' => $str_deli_time,
					'st_delivery_duration' => $str_deli_dur,
					
					'st_desc' => $str_desc,
					'st_address' => $str_addr,
					'st_latitude' => $str_lat,
					'st_longitude' => $str_long,
					'st_logo' => $st_logo,
					'st_banner' => $banner_file,
					'added_by' => Session::get('merchantid')
					);
					//print_r($insertArr); exit;
					if(count($this->get_Adminactive_language) > 0)
					{
						foreach($this->get_Adminactive_language as $Lang)
						{	
							
							$insertArr['st_store_name_'.$Lang->lang_code] = Input::get('st_name_'.$Lang->lang_code);		
							$insertArr['st_desc_'.$Lang->lang_code] = Input::get('st_desc_'.$Lang->lang_code);		
						}
					}
					
					$update = updatevalues('gr_store',$insertArr,['id' =>$str_id,'st_type' => 2]);
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					Session::flash('message',$msg);
					return Redirect::to('mer-manage-store');
				}
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		
	}	