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
	
	use App\Restaurant;
	
	use Excel;
	
	use Response;
	
	use File;
	
	use Image;
	
	class MerRestaurantController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
			
		}
		
		/** manage restaurant **/
		public function manage_restaurant()
		{
			if(Session::has('merchantid') == 1)
			{
				
				if(Session::get('mer_has_shop') == 0) //merchant not has shop
				{
					$category_list = DB::table('gr_category')->where(['cate_status' => '1','cate_type'=>'1'])->orderBy('cate_name','asc')
                    ->pluck('cate_name','cate_id')
                    ->prepend((Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SELECT'), '0')
                    ->toarray();
					$url  ='mer-add-restaurant';
					
					$array_name = array();
					foreach(DB::getSchemaBuilder()->getColumnListing('gr_store') as $res)
					{
						$array_name[$res]='';
					}
					$object = (object) $array_name; // return all value as empty.
					$get_details = DB::table('gr_merchant')->select('idproof','license')->where('id','=',Session::get('merchantid'))->first();
					$mer_com = get_merchant_commission(Session::get('merchantid'));
					$mer_comm_per = $mer_com->mer_commission;
					if($mer_comm_per!=''){
						$mer_commission = $mer_comm_per.'%';
						}else{
						$mer_commission = '0%';
					}
					//make working hours array as empty
					$object1 = array();
					$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_REST')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_REST') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADD_REST');
					return view('sitemerchant.restaurant.add_restaurant')->with(['category_list' => $category_list,'pagetitle' => $pagetitle,'getrestaurant'=>$object,'url'=>$url,'wk_hours'=>$object1,'mer_commission' => $mer_commission,'details' => $get_details]);
				}
				else //merchant has shop
				{
					
					$category_list  = array();
					$url 	= 'mer-update-restaurant';
					$store = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
					Session::put('shop_id',$store->id);
					$object 	= Restaurant::get_details($store->id);
					$get_hours 	= get_working_hours(Session::get('shop_id'));
					$mer_id = $object->st_mer_id;
					$mer_com = get_merchant_commission($mer_id);
					$mer_comm_per = $mer_com->mer_commission;
					if($mer_comm_per!=''){
						$mer_commission = $mer_comm_per.'%';
						}else{
						$mer_commission = '0%';
					}
					
					$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_REST_EDIT')) ? trans(Session::get('mer_lang_file').'.ADMIN_REST_EDIT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REST_EDIT');
					return view('sitemerchant.restaurant.add_restaurant')->with(['category_list' => $category_list,'pagetitle' => $page_title,'getrestaurant'=>$object,'url'=>$url,'wk_hours'=>$get_hours])->with('mer_commission',$mer_commission);
				}
				
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		public function add_restaurant(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{
				//print_r($request->all()); exit;
				$banner_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_BANNER');
				$logo_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_LOGO');
				$proof_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE');
				$res_id = Input::get('rs_id');
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
				if($res_id == '')
				{
					$validator = Validator::make($request->all(), [
					
                    $input_name => 'required',
					
                    'rs_name' => 'required',
					
                    'rs_desc' =>'required',
					
                    'del_radius' =>'required',
					
                    'del_time' =>'required',
					
                    'rs_addr' => 'required',
					
                    'rs_lat' => 'required',
					
                    'rs_long' => 'required',
					
                    'rs_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
                    'rs_banner' => 'required',
                    'rs_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500',
                    'id_proof' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
                    'license' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500'
					
					
					],
                    [ 	'rs_banner.*.required'     => $banner_err,
					'rs_banner.*.image'        => $banner_err,
					'rs_banner.*.mimes'        => $banner_err,
					'rs_banner.*.dimensions'   => $banner_err,
					'rs_logo.required'      => $logo_err,
					'rs_logo.dimensions'    => $logo_err,
					'id_proof.mimes'        => $proof_err,
					'id_proof.dimensions'   => $proof_err,
					'license.mimes'         => $proof_err,
					'license.dimensions'    => $proof_err
                    ]);
				}
				else
				{
					$validator = Validator::make($request->all(), [
					
                    'rs_name' => 'required',
					
                    'rs_desc' =>'required',
					
                    'del_radius' =>'required',
					
                    'del_time' =>'required',
					
                    'rs_addr' => 'required',
					
                    'rs_lat' => 'required',
					
                    'rs_long' => 'required',
					
                    'rs_logo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
                    'rs_banner' => 'Sometimes',
                    'rs_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500',
                    'id_proof' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
                    'license' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500'
					
					],
                    [ 'rs_banner.Sometimes'   => $banner_err,
					'rs_banner.|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500'   => $banner_err,
					'rs_logo.Sometimes' => $logo_err,
					'rs_logo.|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500' => $logo_err,
					'id_proof.mimes'        => $proof_err,
					'id_proof.dimensions'   => $proof_err,
					'license.mimes'         => $proof_err,
					'license.dimensions'    => $proof_err
                    ]);
				}
				if ($validator->fails()) {
					return redirect('mer-manage-restaurant')->withErrors($validator)
                    ->withInput();
					}else{
					if($res_id == ''){
						/*SEO URL */ 
						$seourl = str_slug(Input::get('rs_name'));
						$generated_seourl = generate_seourl($seourl,'gr_store','','','store_slug');
						/*EOF SEO URL */
					}else{
						$seourl = str_slug(Input::get('rs_name'));
						$primary_id = $res_id;
						$generated_seourl = generate_seourl($seourl,'gr_store','id',$primary_id,'store_slug');
					}
					$cate_id = $cate_name;
					//update category in table
					if(!is_numeric($cate_name))
					{	$name = 'cate_name';
						$old_name = Input::get('cate_name');
						$check = DB::table('gr_category')->select('cate_id')->where([$name => $old_name,'cate_type' => '1'])->where('cate_status','!=' ,'2')->first();
						//print_r($check); exit;
						if(empty($check) === true)
						{
							insertvalues('gr_category',['cate_name' => Input::get('cate_name'),'cate_type' => 1,'cate_added_by' => 1]);
							$cate_id = DB::getPdo()->lastInsertId();
						}
						else
						{
							$cate_id = $check->cate_id;
						}
						
					}
					
					$add = new Restaurant;
					$add->st_mer_id 			= Session::get('merchantid');
					$add->st_store_name 		= ucfirst(mysql_escape_special_chars(Input::get('rs_name')));
					$add->store_slug			= $generated_seourl;
					$add->st_category 			= $cate_id;
					$add->st_currency			= Input::get('curr_code');
					$add->st_minimum_order		= mysql_escape_special_chars(Input::get('min_order'));
					$add->st_delivery_radius	= mysql_escape_special_chars(Input::get('del_radius'));
					$add->st_delivery_time		= mysql_escape_special_chars(Input::get('del_time'));
					$add->st_delivery_duration	= mysql_escape_special_chars(Input::get('deli_duration'));
					$add->st_pre_order			= Input::get('pre_order');
					$add->st_desc				= mysql_escape_special_chars(Input::get('rs_desc'));
					$add->st_address			= mysql_escape_special_chars(Input::get('rs_addr'));
					$add->st_latitude			= Input::get('rs_lat');
					$add->st_longitude			= Input::get('rs_long');
					$add->st_type				= 1; // restaurant
					//$add->st_status				= 0; // block
					$add->added_by				= Session::get('merchantid'); // block
					$add->save();
					$insert_id 	= $add->id;
					$mer_id 	= Session::get('merchantid');
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
							
							$insertArr['st_store_name_'.$Lang->lang_code] = Input::get('rs_name_'.$Lang->lang_code);
							$insertArr['st_desc_'.$Lang->lang_code] = Input::get('rs_desc_'.$Lang->lang_code);
						}
					}
					
					$insert = updatevalues('gr_store',$insertArr,['id' => $insert_id]);
					
					//update working hours
					$wk_array = array();
					for($i=1;$i<=7;$i++)
					{
						$wk_array['wk_res_id'] = $insert_id;
						$wk_array['wk_date'] = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'')) ? trans(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DAY'.$i.'');
						$wk_array['wk_closed']=(Input::get('closed'.$i)!=null)?Input::get('closed'.$i):'0';
						$wk_array['wk_start_time']  = (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['old_start_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['wk_end_time']    = (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$wk_array['old_end_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$update = insertvalues('gr_res_working_hrs',$wk_array);
					}
					
					$idproof_image = '';
					if($request->hasFile('id_proof'))
					{
						
						$idproof_image = 'Id_Proof_'.rand().'.'.request()->id_proof->getClientOriginalExtension();
						$destinationPath = public_path('images/merchant');
						$Idproof = Image::make(request()->id_proof->getRealPath())->resize(300, 300);
						$Idproof->save($destinationPath.'/'.$idproof_image,80);
						//$rcbook_image
					}
					$license_image = '';
					if($request->hasFile('license'))
					{
						
						$license_image = 'Licence_'.rand().'.'.request()->license->getClientOriginalExtension();
						$destinationPath = public_path('images/merchant');
						$License = Image::make(request()->license->getRealPath())->resize(300, 300);
						$License->save($destinationPath.'/'.$license_image,80);
					}
					//UPDATE STORE CATEGORY 
					DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count+1 where cate_id = '".$cate_id."'");
					//EOF UPDATE STORE CATEGORY 
					
					//update merchant has shop
					$update_mercahnt = updatevalues('gr_merchant',['has_shop' => 1,'idproof' => $idproof_image,'license' => $license_image],['id' => $mer_id]);
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					$request->session()->forget('has_shop');
					Session::put('shop_id',$insert_id);
					Session::put('mer_has_shop',1);
					Session::flash('message',$msg);
					return Redirect::to('mer-manage-restaurant');
				}
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** Remove banner **/
		public function remove_restaurant_banner()
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
				return Redirect::to('merchant-login');
			}
		}
		
		/** update restaurant **/
		public function update_restaurant(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{
				//print_r($request->all()); exit;
				$banner_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_BANNER') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_BANNER');
				$logo_err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_VALID_LOGO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_LOGO');
				$validator = Validator::make($request->all(), [
				
                'rs_name' => 'required',
				
                'rs_desc' =>'required',
				
                'del_radius' =>'required',
				
                'del_time' =>'required',
				
                'rs_addr' => 'required',
				
                'rs_lat' => 'required',
				
                'rs_long' => 'required',
				
                'rs_logo' => 'Sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
                'rs_banner' => 'Sometimes',
                'rs_banner.*' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1366,min_height=300,max_width=1500,max_height=500'
				
				],[ 'rs_banner.*.required'   => $banner_err,
				'rs_banner.*.image'   => $banner_err,
				'rs_banner.*.mimes'   => $banner_err,
				'rs_banner.*.dimensions'   => $banner_err,
				'rs_logo.Sometimes' => $logo_err,
				'rs_logo.image|mimes|dimensions' => $logo_err]
				);
				if ($validator->fails()) {
					return redirect('mer-manage-restaurant')->withErrors($validator)
                    ->withInput();
					}else{
					$add = Restaurant::find(Input::get('rs_id'));
					$add->st_store_name 		= ucfirst(mysql_escape_special_chars(Input::get('rs_name')));
					$add->st_currency			= Input::get('curr_code');
					$add->st_minimum_order		= mysql_escape_special_chars(Input::get('min_order'));
					$add->st_delivery_radius	= mysql_escape_special_chars(Input::get('del_radius'));
					$add->st_delivery_time		= mysql_escape_special_chars(Input::get('del_time'));
					$add->st_delivery_duration	= mysql_escape_special_chars(Input::get('deli_duration'));
					$add->st_pre_order			= Input::get('pre_order');
					$add->st_desc				= mysql_escape_special_chars(Input::get('rs_desc'));
					$add->st_address			= mysql_escape_special_chars(Input::get('rs_addr'));
					$add->st_latitude			= Input::get('rs_lat');
					$add->st_longitude			= Input::get('rs_long');
					$add->st_type				= 1; // restaurant
					$add->added_by				= Session::get('merchantid'); // block
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
					/** update logo **/ echo $count;
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
						for($i = 0; $i< $count; $i++)
						{
							if(array_key_exists($i,$banner)) //In update,if banner image is empty then update with old banner values
							{
								$old_image = $banner[$i];
								$image_path = public_path('images/restaurant/banner/').$old_image;  // Value is not URL but directory file path
								if(File::exists($image_path))
								{
									$a =   File::delete($image_path);
									
								}
								
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
								return redirect('mer-manage-restaurant')->withErrors($validatoradv)->withInput();
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
						$wk_date = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'')) ? trans(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DAY'.$i.'');
						$wk_array['wk_closed']=(Input::get('closed'.$i)!=null)?Input::get('closed'.$i):'0';
						$wk_array['wk_start_time']  = (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['old_start_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_start_'.$i);
						$wk_array['wk_end_time']    = (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$wk_array['old_end_time']	= (Input::get('closed'.$i)!=null)?'12:00am':Input::get('wk_end_'.$i);
						$update = updatevalues('gr_res_working_hrs',$wk_array,['wk_res_id' => $insert_id,'wk_date' => $wk_date]);
					}
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
					
					Session::flash('message',$msg);
					return Redirect::to('mer-manage-restaurant');
				}
			}
			else
			{
				return Redirect::to('merchant-login');
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
				$err = (Lang::has(Session::get('mer_lang_file').'.ADMIN_REST_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_REST_NAME_EXISTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_NAME_EXISTS');
			}else{
				$err = '';
			}
			echo $err;
		}
	}	