<?php 
	namespace App;
	use DB;
	use Illuminate\Database\Eloquent\Model as Eloquent;
	use Lang;
	use config;
	//DB::enableQueryLog();  
	
	class MobileModel extends Eloquent
	{        
		public static function get_lang_text($user_lang,$key,$default_value)
        {	
        	
         	$admin_lang = DB::table('gr_language')->where(['default_lang' => '1','status' => '1'])->pluck('lang_code');
			
         	$admin_lang = (empty($admin_lang)) ? 'en' : $admin_lang[0];
         	
         	if(Lang::has($user_lang.'_mob_lang.'.$key)!= '') 
         	{
         		return trans($user_lang.'_mob_lang.'.$key);
			}
         	elseif(Lang::has($admin_lang.'_mob_lang.'.$key)!= '')
         	{
         		return trans($admin_lang.'_mob_lang.'.$key);
			}
         	else
         	{
         		return $default_value;
			}
		}
		public static function get_field_byLang($user_lang,$admin_lang,$field){
			if($user_lang==$admin_lang)
			{
				return $field;
			}
			else
			{
				return $field.'_'.$user_lang;
			}
		}
		
		/* get all grocery lists */
		public static function all_grocery_list($user_lat,$user_long,$lang,$def_lang,$type,$page_no = '',$cate_id = '',$limit = '',$order_by = '')
		{
			$name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$cate_name = ($lang == $def_lang) ? 'cate_name' : 'cate_name_'.$lang;
			$desc = ($lang == $def_lang) ? 'st_desc' : 'st_desc_'.$lang;
			$q = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			if($type == 1)
			{
				$review_type = "restaurant";
			}
			else
			{
				$review_type = "store";
			}
			$sql = DB::table('gr_store')->select(DB::Raw('SUBSTRING_INDEX(st_banner,"/**/",1) as st_image'),$name .' as st_name','gr_store.id',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Available','Closed') as store_closed"),'st_rating','gr_store.'.$desc.' as desc','gr_store.st_logo',DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="'.$review_type.'") as avg_val'))
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_merchant.mer_status','=','1')
			->where('gr_category.cate_status','=','1')
			->where('gr_category.cate_type','=',$type)
			->where('gr_store.st_status','=','1')
			->where('gr_store.st_type','=',$type);
			if($cate_id != '')
			{
				$q = $sql->where(['gr_store.st_category' => $cate_id]);
			}
			if($user_lat != '' && $user_long != '')
			{ 
				$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			if($order_by != 'item_count')
			{
				$q = $sql->addSelect(DB::Raw("(SELECT count('gr_product.pro_id') FROM `gr_product` WHERE `pro_store_id`=gr_store.id) as pro_count"))->orderBy('pro_count','Desc');
			}
			if($limit == '')
			{
				$q = $sql->paginate(10,['*'],'pag',$page_no);
			}
			else
			{
				$q = $sql->limit($limit)->get();
			}
			
			
			return $q; 
		}
		
		/** all categories **/
		public static function get_all_categories($user_lat,$user_long,$lang,$def_lang,$type,$limit = '')
		{   
			$name = ($lang == $def_lang) ? 'cate_name' : 'cate_name_'.$lang;
			$sql = DB::table('gr_category')->select('cate_id',$name.' as category_name','cate_img')
			->leftJoin('gr_store','gr_store.st_category','=','gr_category.cate_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_store.st_status','=','1') 
			->where('gr_merchant.mer_status','=','1')
			->where('cate_status','=','1')
			->where('cate_type','=',$type)
			->where('gr_store.st_type','=',$type)
			->groupBy('gr_store.st_category')
			->havingRaw("count(gr_store.st_category) > 0")
			->orderBy(DB::raw('count(gr_store.st_category)'),'Desc');
			if($user_lat != '' && $user_long != '')
			{ 
				$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			if($limit != '')                              
			{
				$q = $sql->limit($limit)->get();
			}
			else
			{
				$q = $sql->get();
			}
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			return $q;
		}
		
		/*  category based shops */
		public static function category_based_grocery($user_lat,$user_long,$lang,$def_lang,$type,$cate_count,$shop_count)
		{	
			$return_array = array();
			/* get all grocery categories */
			$get_query = self::get_all_categories($user_lat,$user_long,$lang,$def_lang,$type,$cate_count); 
			if(count($get_query) > 0)
			{
				foreach($get_query as $query)
				{	/* get shops under specific category */
					$shop_array = self::all_grocery_list($user_lat,$user_long,$lang,$def_lang,$type,'',$query->cate_id,$shop_count,"item_count");
					if(count($shop_array) > 0)
					{
						foreach($shop_array as $array)
						{
							$return_array[$query->cate_id.'~`'.$query->category_name][] = $array;
						}
					}
				}
			}
			return $return_array;
		}
		
		/* check image exist or not */
		public static function get_image_store($img_name,$type = '')
		{
			$path = url('')."/public/images/noimage/".config::get('no_shop_banner');
		 	if($img_name != '')
		 	{
		 		$filename = public_path('images/store/banner/').$img_name;
			 	if(file_exists($filename))
			 	{
			 		$path =url('')."/public/images/store/banner/".$img_name;
				}
			}
			/* for getting logo */
			if($type=="logo")
			{
				$path = url('')."/public/images/noimage/".config::get('no_shop_logo');
			 	if($img_name != '')
			 	{
			 		$filename = public_path('images/store/').$img_name;
				 	if(file_exists($filename))
				 	{
				 		$path =url('')."/public/images/store/".$img_name;
					}
				}
			}
			return $path;
		}
		
		/* check image exist or not */
		public static function get_image_restaurant($img_name,$type = '')
		{ 
			$path = url('')."/public/images/noimage/".config::get('no_shop_banner');
		 	if($img_name != '')
		 	{
		 		$filename = public_path('images/restaurant/banner/').$img_name;
			 	if(file_exists($filename))
			 	{
			 		$path =url('')."/public/images/restaurant/banner/".$img_name;
				}
			}
			/* for getting logo */
			if($type=="logo")
			{
				$path = url('')."/public/images/noimage/".config::get('no_shop_logo');
			 	if($img_name != '')
			 	{
			 		$filename = public_path('images/restaurant/').$img_name;
				 	if(file_exists($filename))
				 	{ 
				 		$path =url('')."/public/images/restaurant/".$img_name;
					}
				}
			}
			return $path;
		}
		
		/* check product image exist or not */
		public static function get_image_product($img_name)
		{
			$path = url('')."/public/images/noimage/".config::get('no_product');
		 	if($img_name != '')
		 	{
		 		$filename = public_path('images/store/products/').$img_name;
			 	if(file_exists($filename))
			 	{
			 		$path =url('')."/public/images/store/products/".$img_name;
				}
			}
			return $path;
		}
		
		/* check item image exist or not */
		public static function get_image_item($img_name)
		{
			$path = url('')."/public/images/noimage/".config::get('no_item');
		 	if($img_name != '')
		 	{
		 		$filename = public_path('images/restaurant/items/').$img_name;
			 	if(file_exists($filename))
			 	{
			 		$path =url('')."/public/images/restaurant/items/".$img_name;
				}
			}
			return $path;
		}
		
		/* check categry image exist or not */
		public static function get_category_image($img_name)
		{
			$path = url('')."/public/images/noimage/".config::get('no_cate_img');
		 	if($img_name != '')
		 	{
		 		$filename = public_path('images/category/').$img_name;
			 	if(file_exists($filename))
			 	{
			 		$path =url('')."/public/images/category/".$img_name;
				}
			}
			return $path;
		}
		
		/* get customer profile */
		public static function get_cus_image($img_name)
		{
			$path = url('').'/public/images/noimage/user.png';
			if($img_name !='')
			{
				$filename = public_path('images/customer/').$img_name; 
				if(file_exists($filename))
				{
					$path = url('').'/public/images/customer/'.$img_name;
				}
			}
			return $path;
		}
		
		public static function get_grocery_banner_details()
		{
			return DB::table('gr_banner_image')->where('banner_type','=',3)->where('banner_status','=',1)->get();
		}
		public static function get_food_banner_details()
		{
			return DB::table('gr_banner_image')->where('banner_type','=',4)->where('banner_status','=',1)->get();
		}
		public static function get_logo_settings_details()
		{
			return DB::table('gr_logo_settings')->get();
		}
		
		/*GET CART DETAILS BY RESTAURANT WISE */
		public static function get_cart_restaurants($user_id,$cart_st_id='',$lang){
			$store_name = ($lang == 'en') ? 'st_store_name' : 'st_store_name_'.$lang;
			$current_time = date('H:i:s');
			$current_day=date('l');
			//DB::connection()->enableQueryLog();
			$group_sql = DB::table('gr_cart_save')
			->select('gr_store.'.$store_name.' as store_names',
			'gr_cart_save.cart_st_id',
			'gr_store.st_type',
			'gr_store.st_pre_order',
			'st_currency',
			DB::Raw('IFNULL(st_minimum_order, 0) as st_minimum_order'),
			DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Available','Closed') as store_closed"))
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
			->where('gr_cart_save.cart_cus_id','=',$user_id)
			->where('gr_store.st_status','=','1');
			if($cart_st_id!=''){
				$sql = $group_sql->where('cart_st_id','=',$car_st_id);
			}
			$sql = $group_sql->groupBy('gr_cart_save.cart_st_id')->get();
			return $sql;
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
		public static function get_cartdet_byrestId($cus_id,$cart_st_id,$lang){
			$store_name = ($lang == 'en') ? 'st_store_name' : 'st_store_name_'.$lang;
			$pdt_name = ($lang == 'en') ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$contains = ($lang == 'en') ? 'pro_per_product' : 'pro_per_product_'.$lang;
			$current_time = date('H:i:s');
			$current_day=date('l');
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_cart_save')
			->select(
			'gr_cart_save.cart_id',
			'gr_product.pro_id',
			'gr_product.pro_item_code',
			'gr_product.pro_original_price',
			'gr_product.pro_has_discount',
			'gr_product.pro_discount_price',
			'gr_cart_save.cart_type'
			,'gr_product.pro_had_choice',
			'gr_product.'.$pdt_name.' as item_name',
			'gr_product.'.$contains.' as contains_name',
			DB::Raw('SUBSTRING_INDEX(pro_images,"/**/",1) as pro_image'),
			'gr_cart_save.cart_quantity',
			'gr_cart_save.cart_currency',
			'gr_cart_save.cart_unit_amt',
			'gr_cart_save.cart_total_amt',
			'gr_cart_save.cart_had_choice',
			'gr_cart_save.cart_choices_id',
			DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),
			'gr_cart_save.cart_tax',
			'gr_product.pro_currency',
			'gr_product.pro_type',
			'gr_cart_save.cart_pre_order',
			'gr_cart_save.cart_pre_order',
			'gr_store.id as store_id_is',
			'gr_product.pro_store_id',
			'gr_merchant.id as mer_id',
			'gr_merchant.mer_commission',
			'gr_product.pro_no_of_purchase',
			'gr_merchant.refund_status',
			'cart_spl_req',
			'gr_country.co_curcode')         
			->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
			->where(['gr_cart_save.cart_cus_id' => $cus_id,'gr_country.co_status'=>'1','gr_cart_save.cart_st_id' => $cart_st_id,'gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
			->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
			->get();
			//$query = DB::getQueryLog();
			//print_r($query);
			//echo '<hr>';
			//exit;
			return $sql;
		}
		/* get cart details */
		public static function get_cart_details($user_id,$rest_id='',$lang,$def_lang)
		{
			$store_name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$pdt_name = ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$contains = ($lang == $def_lang) ? 'pro_per_product' : 'pro_per_product_'.$lang;
			$q   = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			$shippingDet = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id','=',$user_id)->first();
			if(!empty($shippingDet))
			{
				$user_lat = $shippingDet->sh_latitude;
				$user_long= $shippingDet->sh_longitude;
			}
			else
			{
				$user_lat = 0.0000;
				$user_long= 0.0000;
			}
			//GROUP BY
			$cart_array=array();
			$group_sql = DB::table('gr_cart_save')
			->select('gr_store.'.$store_name.' as store_names','gr_cart_save.cart_st_id','gr_store.st_type','gr_store.st_pre_order','st_currency',DB::Raw('IFNULL(st_minimum_order, 0) as st_minimum_order') ,DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Available','Closed') as store_closed"),'st_address','st_latitude','st_longitude','cart_spl_req')
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->where('gr_cart_save.cart_cus_id','=',$user_id)
			->where('gr_store.st_status','=','1');
			if($rest_id!='')
			{
				$sql = $group_sql->where('cart_st_id','=',$rest_id);
			}
			$sql = $group_sql->groupBy('gr_cart_save.cart_st_id')->get();
			//$query = DB::getQueryLog();
			//print_r($group_sql);
			//exit;
			if(count($sql) > 0 )
			{
				foreach($sql as $gsql)
				{
					if($gsql->st_pre_order=='1' && $gsql->st_type=='1' && $gsql->store_closed=='Closed')
					{
						$validate = 1;
					}
					/* store closed and pre order not enable*/
					elseif($gsql->st_pre_order !='1' && $gsql->st_type=='1' && $gsql->store_closed=='Closed')
					{
						$validate = 2;	
					}
					else {
						$validate = 0;
					}
					//echo $gsql->cart_st_id.'<bR>'; pro_original_price pro_discount_price pro_has_discount cart_tax
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_cart_save')
					->select('gr_cart_save.cart_id','gr_product.pro_id','gr_product.pro_item_code','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_cart_save.cart_type','gr_cart_save.cart_type','gr_product.pro_had_choice','gr_product.'.$pdt_name.' as item_name','gr_product.'.$contains.' as contains_name',DB::Raw('SUBSTRING_INDEX(pro_images,"/**/",1) as pro_image'),'gr_cart_save.cart_quantity','gr_cart_save.cart_currency','gr_cart_save.cart_unit_amt','gr_cart_save.cart_total_amt','gr_cart_save.cart_had_choice','gr_cart_save.cart_choices_id',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),'gr_cart_save.cart_tax','gr_product.pro_currency','gr_product.pro_type','gr_cart_save.cart_pre_order','gr_store.id as store_id_is','gr_product.pro_store_id','gr_merchant.id as mer_id','gr_merchant.mer_commission','gr_product.pro_no_of_purchase','gr_merchant.refund_status','cart_spl_req','gr_country.co_curcode','gr_merchant.cancel_status')         
					->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
					->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
					->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
					->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
					->where(['gr_cart_save.cart_cus_id' => $user_id,'gr_country.co_status'=>'1','gr_cart_save.cart_st_id' => $gsql->cart_st_id,'gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
					->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
					->get();
					$query = DB::getQueryLog();
					//print_r($query);
					//echo '<hr>';
					//exit;
					if(count($sql) > 0 )
					{
						foreach($sql as $sq)
						{
							//echo $sq->store_name.'<br>';
							$cart_array[$gsql->store_names.'~`'.$gsql->cart_st_id.'~`'.$gsql->st_type.'~`'.$gsql->st_currency.'~`'.$gsql->st_minimum_order.'~`'.$gsql->store_closed.'~`'.$validate.'~`'.$gsql->st_pre_order.'~`'.$gsql->st_address.'~`'.$gsql->st_latitude.'~`'.$gsql->st_longitude][] = $sq;
							//print_r($sq); echo '<hr>';
						}
					}
				}
			}
			//echo '<pre>'; print_r($cart_array);echo '</pre>'; 
			//exit;
			return $cart_array;  
		}
		public static function get_choice_name($ch_id,$lang,$def_lang)
		{   
			$name = ($lang == $def_lang) ? 'ch_name' : 'ch_name_'.$lang;
			return DB::table('gr_choices')->select('gr_choices.'.$name.' as ch_name')
			->where(['ch_id' => $ch_id,'gr_choices.ch_status' => '1'])
			->first();
		}
		/* get current day working time */
		public static function get_wk_time($id)
		{	
			$current_day=date('l');
			$return = DB::table('gr_res_working_hrs')->select('wk_closed','wk_start_time','wk_end_time')->where(['wk_res_id' => $id,'wk_date' => $current_day])->first();
			if(!empty($return))
			{
				if($return->wk_closed == 1)
				{
					return "Closed";
				}
				else
				{
					return $return->wk_start_time."-".$return->wk_end_time;
				}
			}
			else
			{
				return "Closed";
			}
		}
		public static function checkUserMailExist($agent_email)
		{	
			//DB::connection()->enableQueryLog();
			return DB::table('gr_agent')->where('agent_email','=',$agent_email)->where('agent_status','!=','2')->get();
			//print_r($test);
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
		public static function checkCustomerMailExist($cus_email){
			return DB::table('gr_customer')->where('cus_email','=',$cus_email)->where('cus_status','!=','2')->get();
		}
		/* get store and restaurant details */
		public static function get_shop_details($id,$lang,$def_lang,$type)
		{
			$name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$desc = ($lang == $def_lang) ? 'st_desc' : 'st_desc_'.$lang;
			$current_time = date('H:i:s');
			$current_day=date('l');
			return DB::table('gr_store')->select('gr_store.id',$name.' as st_name','st_minimum_order','st_pre_order','st_delivery_time','st_delivery_duration',$desc.' as st_desc','st_delivery_radius','st_logo','st_banner','st_rating','st_currency','st_address','gr_merchant.refund_status','gr_merchant.mer_cancel_policy',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Available','Closed') as store_closed"),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.proitem_id=gr_store.id  and gr_review.review_status="1") as avg_val'),'gr_merchant.cancel_status')         
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_store.id' => $id])
			->where(['gr_store.st_type' => $type])
			->where(['gr_store.st_status'=>'1'])
			->first();
		}
		
		/* get shop reviews */
		public static function get_shop_review($id,$type,$page_no = '1')
		{
			return DB::table('gr_review')->select('cus_fname','review_comments','review_rating','review_status','comment_id','gr_store.st_store_name','review_type','gr_merchant.mer_fname','cus_image')
            ->leftjoin('gr_store','gr_review.res_store_id', '=', 'gr_store.id')
            ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
            ->leftjoin('gr_merchant','gr_store.st_mer_id','gr_merchant.id')
            ->where(['review_status' =>'1'])
            ->where(['review_type' => $type])
            ->where(['res_store_id' => $id])
            ->paginate(10,['*'],'review_page',$page_no);
		}
		
		/** get store/restaurant categories **/
		public static function get_categories($id,$type,$lang,$def_lang)
		{   
			$return_array = array();
			$cate_name = ($lang == $def_lang) ? 'pro_mc_name' : 'pro_mc_name_'.$lang;
			$sc_name = ($lang == $def_lang) ? 'pro_sc_name' : 'pro_sc_name_'.$lang;
			$main_category =  DB::table('gr_proitem_maincategory')->select('pro_mc_id',$cate_name.' as mc_name')
			->Join('gr_product','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->where(['gr_product.pro_status' => '1',
			'gr_proitem_maincategory.pro_mc_status' => '1',
			'gr_product.pro_store_id' => $id,
			'gr_proitem_maincategory.pro_mc_type' => $type])
			->groupBy('gr_product.pro_category_id')
			->orderBy(DB::raw('count(gr_product.pro_category_id)'),'Desc')
			->get();
			if(count($main_category) > 0)
			{
				foreach($main_category as $main)
				{
					$sub_cate = DB::table('gr_proitem_subcategory')->select('gr_proitem_subcategory.pro_sc_id','gr_proitem_subcategory.'.$sc_name.' as sc_name','gr_proitem_subcategory.pro_main_id')
					->Join('gr_product','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
					->Join('gr_proitem_maincategory','gr_proitem_subcategory.pro_main_id','=','gr_proitem_maincategory.pro_mc_id')
					->where(['gr_product.pro_status' => '1',
					'gr_proitem_subcategory.pro_sc_status' => '1',
					'gr_product.pro_store_id' => $id,
					'gr_proitem_subcategory.pro_sc_type' => $type,
					'gr_proitem_maincategory.pro_mc_status' => '1',
					'gr_proitem_subcategory.pro_main_id' => $main->pro_mc_id
					])
					->groupBy('gr_product.pro_sub_cat_id')
					->get();
                    if(count($sub_cate) > 0)
                    {
                    	foreach($sub_cate as $sub)
                    	{
                    		$return_array[$main->pro_mc_id.'~~'.$main->mc_name][]=$sub;
						}
					}
				}
			}
			return $return_array;
		}
		
		public static function get_wishlistdetails($cus_id,$lang,$admin_default_lang,$page_no = '1')
		{
			//pro_images
			$item_name = ($lang == $admin_default_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			return DB::table('gr_wishlist')->select('gr_product.'.$item_name.' as pdtname',
			'gr_product.pro_quantity',
			'gr_product.pro_no_of_purchase',
			'gr_product.pro_discount_price',
			'gr_product.pro_id',
			'gr_product.pro_store_id',
			'gr_wishlist.ws_id',
			'gr_product.pro_original_price',
			'gr_product.pro_currency',
			DB::Raw('SUBSTRING_INDEX(pro_images,"/**/",1) as pro_image'),
			'gr_product.pro_type',
			'gr_product.pro_has_discount',
			DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Available','Sold') as availablity")
			)
			->Join('gr_product','gr_wishlist.ws_pro_id','=','gr_product.pro_id')
			->where('ws_cus_id',$cus_id)->where('gr_product.pro_status','=','1')
			->paginate(10,['*'],'wishlist_page',$page_no);
		}
		
		/* get category based product details */
		public static function get_items($st_id,$mc_id,$sc_id,$sortby = '',$type,$text = '',$page,$lang,$def_lang,$user_id = '',$item_type = '')
		{
			//echo "tetst"; exit;
			$name 			= ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$desc 			= ($lang == $def_lang) ? 'pro_desc' : 'pro_desc_'.$lang;
			$contains 		= ($lang == $def_lang) ? 'pro_per_product' : 'pro_per_product_'.$lang;
			$main_ca_name 	= ($lang == $def_lang) ? 'pro_mc_name' : 'pro_mc_name_'.$lang;
			$sb_ca_name 	= ($lang == $def_lang) ? 'pro_sc_name' : 'pro_sc_name_'.$lang;
			$st_name 		= ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$q   = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			//DB::connection()->enableQueryLog(); st_pre_order
			$sql = DB::table('gr_product')
			->select('gr_product.pro_id','gr_product.'.$name.' as item_name','pro_original_price','pro_has_discount','pro_discount_price','gr_product.'.$desc.' as desc','pro_currency','gr_product.pro_rating','gr_product.pro_no_of_purchase',DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as image'),'gr_product.'.$contains.' as contains','gr_product.pro_quantity','gr_product.pro_had_choice','gr_product.pro_had_tax','gr_product.pro_tax_name','gr_product.pro_tax_percent',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'gr_product.pro_store_id','gr_product.pro_per_product','gr_store.st_pre_order',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase as stock'),DB::Raw("IF((gr_product.pro_quantity-gr_product.pro_no_of_purchase)>0,'Available','Out Of Stock') as availablity"),'gr_proitem_maincategory.'.$main_ca_name.' as main_ca_name','gr_proitem_subcategory.'.$sb_ca_name.' as sub_ca_name','gr_store.'.$st_name.' as store_name',DB::Raw("IF((SELECT count(*) FROM `gr_wishlist` WHERE `ws_pro_id` =gr_product.pro_id AND `ws_cus_id` = '$user_id')>0,'Favourite','Not favourite') as wishlist"),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.proitem_id=gr_product.pro_id  and gr_review.review_status="1") as avg_val'),'gr_product.pro_veg')         
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where(['gr_product.pro_status' => '1','gr_proitem_maincategory.pro_mc_status' => '1','gr_proitem_subcategory.pro_sc_status' => '1','gr_store.st_status'=>'1','gr_product.pro_store_id' => $st_id,'gr_product.pro_type' => $type])
			->whereRaw(('gr_proitem_subcategory.pro_main_id=gr_product.pro_category_id'));
			if($mc_id != '')                          
			{
				$q = $sql->where(['gr_product.pro_category_id' => $mc_id]);
			}
			if($sc_id != '')
			{
				$q = $sql->where(['gr_product.pro_sub_cat_id' => $sc_id]);
			}
			if($text != '') 
			{
				$q = $sql->where('gr_product.'.$name,'LIKE','%'.$text.'%');
			}
			if($item_type != '')
			{
				$q = $sql->where(['gr_product.pro_veg' =>$item_type]);
			}
			if($sortby == 1 || $sortby == '') //newest
			{
				$q = $sql->orderBy('pro_id','desc');
			}
			if($sortby == 2) //title a-z
			{
				$q = $sql->orderBy('gr_product.'.$name,'asc');
			}
			if($sortby == 3) //title z-a
			{
				$q = $sql->orderBy('gr_product.'.$name,'desc');
			}
			if($sortby == 4) //original price low to high
			{
				$q = $sql->orderBy('gr_product.pro_original_price','asc');
			}
			if($sortby == 5) //original price high to low
			{
				$q = $sql->orderBy('gr_product.pro_original_price','desc');
			}
			
			$q = $sql->paginate(10,['*'],'item_list',$page);
			/*$query = DB::getQueryLog();
				print_r($query);
			exit;*/
			//print_r($q); exit;
			return $q;  
		}
		public static function get_my_review($wh_type = '',$cus_id,$lang,$admin_default_lang,$page_no = '1')
		{     
			$pro_name = ($lang == $admin_default_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$shop_name = ($lang == $admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			if($wh_type == "item" || $wh_type == "product")
			{
				//DB::connection()->enableQueryLog();
				return DB::table('gr_review')->select('proitem_id','res_store_id','merchant_id','review_comments','created_date','review_rating','gr_store.'.$shop_name.' as shop_name','gr_product.'.$pro_name.' as item_name',DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),'review_status')
				->leftJoin('gr_product','gr_review.proitem_id','=','gr_product.pro_id')
				->leftJoin('gr_merchant','gr_merchant.id','=','gr_review.merchant_id')
				->leftJoin('gr_store','gr_store.id','=','gr_review.res_store_id')
				->where(['gr_product.pro_status' => '1','gr_merchant.mer_status' => '1','gr_store.st_status' => '1','review_type' => $wh_type,'gr_review.customer_id' => $cus_id])
				->where('review_status','!=','2')
				->paginate(10,['*'],'wishlist_page',$page_no);
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
			}
			if($wh_type == "store" || $wh_type == "restaurant")
			{
				return DB::table('gr_review')->select('res_store_id','merchant_id','review_comments','created_date','review_rating','gr_store.'.$shop_name.' as shop_name','st_logo','review_status')
				->leftJoin('gr_merchant','gr_merchant.id','=','gr_review.merchant_id')
				->leftJoin('gr_store','gr_store.id','=','gr_review.res_store_id')
				->where(['gr_merchant.mer_status' => '1','gr_store.st_status' => '1','review_type' => $wh_type,'gr_review.customer_id' => $cus_id])
				->where('review_status','!=','2')
				->paginate(10,['*'],'wishlist_page',$page_no);
			}
		}
		public static function get_product_wishlist($product_id,$cus_id)    
		{        
			return DB::table('gr_wishlist')->join('gr_product','gr_product.pro_id','=','gr_wishlist.ws_pro_id')->where('ws_pro_id','=',$product_id)->where('ws_cus_id','=',$cus_id)->where('pro_status','=','1')->first();    
		}
		
		/* get choices list */
		public static function get_choices($pro_id,$lang,$def_lang)
		{
			$ch_name = ($lang == $def_lang) ? 'ch_name' : 'ch_name_'.$lang;
			$name 	= ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$q   = array();
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_product_choice')
			->select('gr_product_choice.pc_id','gr_product_choice.pc_choice_id','gr_choices.'.$ch_name.' as choice_name','gr_product_choice.pc_price','gr_product.pro_currency','gr_product.'.$name.' as pro_name')         
			->Join('gr_choices','gr_choices.ch_id','=','gr_product_choice.pc_choice_id')
			->Join('gr_product','gr_product.pro_id','=','gr_product_choice.pc_pro_id')
			->where(['gr_choices.ch_status' => '1'])
			->where(['gr_product.pro_type' => '2'])
			->where('gr_product_choice.pc_pro_id','=',$pro_id)->get();
			return $sql;  
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
		
		/* check id exist */
		public static function check_id_exist($id,$table,$where_column,$status_column,$select = '')
		{	//return $select; exit;
			return DB::table($table)->select($select)->where([$where_column=>$id,$status_column => '1'])->first();
		}
		/* get order details */
		public static function getordersdetails($cus_id,$order_num,$page_no = '1',$lang='',$def_lang = '')
		{	
			$store_name = 'st_store_name';
			if($lang!= '' && $def_lang != '' && ($lang != $def_lang))
			{
				$store_name = 'st_store_name_'.$lang;
			}
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id','gr_order.ord_date',DB::raw('SUM(gr_order.ord_grant_total) As revenue'),'gr_order.ord_transaction_id','gr_order.ord_currency','gr_order.ord_delivery_fee','ord_status','ord_wallet',DB::Raw('(SELECT GROUP_CONCAT(CONCAT(if('.$store_name.' is null,"",'.$store_name.'),"~",if(st.id is null,"",st.id),"~",if(st.st_address is null,"",st.st_address)) SEPARATOR "/**/") FROM gr_store as st WHERE `st`.`id` IN(SELECT nb.ord_rest_id FROM gr_order as nb WHERE nb.ord_transaction_id = `gr_order`.`ord_transaction_id` AND nb.ord_cus_id = "'.$cus_id.'") GROUP BY st.st_type) as store_name_list'),DB::Raw("( SELECT COUNT(ord_id) from gr_order as ord where ord.ord_status < 8 and ord.ord_cancel_status != 1 and ord.ord_transaction_id=`gr_order`.`ord_transaction_id`) as active_count"))
			->groupBy('gr_order.ord_transaction_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->where('gr_customer.cus_id',$cus_id);
			if ($order_num != '')
			{
				$sql->where('gr_order.ord_transaction_id', '=' , $order_num);
			}
			
			$result = $sql->paginate(10,['*'],'myorder_page',$page_no);
			
				/*$query = DB::getQueryLog();
				print_r($query);*/
				
			//exit;
			return $result;
		}
		
		/* get product details */
		public static function get_product_details($pro_id,$type,$lang,$def_lang,$user_id)
		{
			$name = ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$desc = ($lang == $def_lang) ? 'pro_desc' : 'pro_desc_'.$lang;
			$contains = ($lang == $def_lang) ? 'pro_per_product' : 'pro_per_product_'.$lang;
			$q   = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			//DB::connection()->enableQueryLog();
			return 
			DB::table('gr_product')
			->select('gr_product.pro_id','gr_product.'.$name.' as item_name','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_product.'.$desc.' as desc','gr_product.pro_currency','gr_product.pro_no_of_purchase','gr_product.pro_images','gr_product.pro_category_id','gr_product.pro_had_choice','gr_product.'.$contains.' as contains',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.proitem_id='.$pro_id.'  and gr_review.review_status="1") as avg_val'), DB::Raw('(select count(gr_review.comment_id) FROM gr_review where gr_review.proitem_id='.$pro_id.' and gr_review.review_status="1") as num_reviewers'),'gr_product.pro_store_id','gr_product.pro_quantity','gr_product.pro_had_tax','gr_product.pro_tax_name','gr_product.pro_tax_percent',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'gr_store.st_pre_order',DB::Raw("IF((SELECT count(*) FROM `gr_wishlist` WHERE `ws_pro_id`=gr_product.pro_id AND `ws_cus_id`='$user_id')>0,'Favourite','Not favourite') as wishlist"),DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Available','Out of stock') as availablity"),'gr_product.pro_veg')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where(['gr_product.pro_status' => '1','gr_product.pro_type' => $type,'gr_product.pro_id'=>$pro_id,'st_status' => '1'])->first();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
		
		/* get specification for product */
		public static function get_specification($item_id,$lang,$def_lang)
		{	
			$title 	= ($lang == $def_lang) ? 'spec_title' : 'spec_title_'.$lang;
			$desc 	= ($lang == $def_lang) ? 'spec_desc' : 'spec_desc_'.$lang;
			return DB::table('gr_product_spec')->select($title.' as spec_title',$desc.' as spec_desc')->where(['spec_pro_id' => $item_id])->get();
		}
		
		/* get product reviews */
		public static function get_product_review($id,$type,$page_no = '1')
		{
			return DB::table('gr_review')->select('cus_fname','review_comments','review_rating','review_status','comment_id','review_type','cus_image')
            ->leftjoin('gr_product','gr_review.proitem_id', '=', 'gr_product.pro_id')
            ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
            ->where(['review_status' =>'1'])
            ->where(['review_type' => $type])
            ->where(['proitem_id' => $id])
            ->paginate(10,['*'],'review_page',$page_no);
		}
		
		/* get related products */
		public static function get_relatedPdt_details($pro_id,$type,$lang,$def_lang,$user_id)
		{
			$store_id = DB::table('gr_product')->select('pro_store_id','pro_category_id')->where('pro_id','=',$pro_id)->first();
			$name = ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$contains = ($lang == $def_lang) ? 'pro_per_product' : 'pro_per_product_'.$lang;
			$desc = ($lang == $def_lang) ? 'pro_desc' : 'pro_desc_'.$lang;
			$q   = array();
			DB::connection()->enableQueryLog();
			
			$sql = DB::table('gr_proitem_maincategory')
			->select('gr_product.pro_id','gr_product.'.$name.' as item_name','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_product.pro_currency','gr_product.pro_images','gr_product.'.$contains.' as contains',DB::Raw('SUBSTRING_INDEX(pro_images,"/**/",1) as image'),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.proitem_id='.$pro_id.'  and gr_review.review_status="1") as avg_val'),DB::Raw("IF((SELECT count(*) FROM `gr_wishlist` WHERE `ws_pro_id`=gr_product.pro_id AND `ws_cus_id`='$user_id')>0,'Favourite','Not favourite') as wishlist"),'gr_product.pro_veg','gr_product.pro_store_id','gr_product.'.$desc.' as desc',DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Available','Out of stock') as availablity"))         
			->Join('gr_product','gr_product.pro_category_id','=','gr_proitem_maincategory.pro_mc_id')
			->where(['gr_product.pro_status' => '1','gr_product.pro_store_id' => $store_id->pro_store_id])
			->where(['gr_proitem_maincategory.pro_mc_status' => '1'])
			->where(['gr_proitem_maincategory.pro_mc_id' => $store_id->pro_category_id,'gr_product.pro_type' => $type])
			->where('gr_product.pro_id','!=',$pro_id);
			
			//$q = $sql->orderBy('gr_product.'.$name,'asc');
			$q = $sql->inRandomOrder();
			$q = $sql->limit(8)->get();
			return $q;      
		}
		
		/* get payment methods */
		public static function get_pay_method()
		{
			return DB::table('gr_payment_setting')->select('cod_status','paynamics_status','paymaya_status')->first();
		}
		
		public static function get_cancelled_customer_byOrderId($id)
		{
			//DB::connection()->enableQueryLog();
			$customer_det = DB::table('gr_order')->select(
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.ord_shipping_address1',
			'gr_order.order_ship_mail',
			'gr_order.ord_transaction_id',
			'gr_order.ord_cancel_reason'
			)
			->where('gr_order.ord_id','=',$id)
			->where('gr_order.ord_cancel_status','=','1')
			->groupBy('gr_order.ord_cus_id')
			->first();
			return $customer_det;
		}
		public static function get_cancelled_store_byOrderId($id)
		{
			$store_list = DB::table('gr_order')->select('gr_store.st_store_name','gr_store.id','gr_merchant.mer_email')
			->Join('gr_store','gr_store.id', '=', 'gr_order.ord_rest_id')
			->Join('gr_merchant','gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->where('gr_order.ord_id','=',$id)
			->where('gr_order.ord_cancel_status','=','1')
			->get();
			return $store_list;
		}
		public static function get_cancelled_order_byOrderId($id,$store_id,$lang,$def_lang)
		{
			$name = ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			//DB::connection()->enableQueryLog();
			$Invoice_Order = DB::table('gr_order')->select('gr_product.'.$name.' as productName',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_pay_type',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_had_choices',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_order.ord_grant_total',
			'gr_product.pro_images'
			,'gr_product.pro_type',
			'gr_customer.cus_fname',
			'gr_order.ord_cus_id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->leftJoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
			->where('gr_order.ord_id','=',$id)
			->where('gr_order.ord_rest_id','=',$store_id)
			->where('gr_order.ord_cancel_status','=','1')
			->get();
			
			return $Invoice_Order;
		}
		
		/* get customer shipping details */
		public static function get_customer_details($id)
		{
			$customer_det = DB::table('gr_order')->select(
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.ord_transaction_id',
			'gr_order.ord_cancel_reason'
			)
			->where('gr_order.ord_transaction_id','=',$id)
			->groupBy('gr_order.ord_cus_id')
			->first();
			return $customer_det;
		}		
		
		/* get order details based on ransaction id */
		public static function get_order_details($id,$lang,$def_lang)
		{
			$store_list = DB::table('gr_order')->select('gr_store.st_store_name','gr_store.id','gr_merchant.mer_email')
			->Join('gr_store','gr_store.id', '=', 'gr_order.ord_rest_id')
			->Join('gr_merchant','gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->where('gr_order.ord_transaction_id','=',$id)
			->groupBy('gr_order.ord_rest_id')
			->get();
			$Invoice_Order = array();		
			$name = ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			if(count($store_list) > 0)
			{
				foreach($store_list as $list)
				{
					//DB::connection()->enableQueryLog();
					$Invoice_Order[$list->st_store_name.'`'.$list->mer_email][] = DB::table('gr_order')->select('gr_product.'.$name.' as productName',
					'gr_order.ord_quantity',
					'gr_order.ord_unit_price',
					'gr_order.ord_sub_total',
					'gr_order.ord_tax_amt',
					'gr_order.ord_choices',
					'gr_order.ord_pay_type',
					'gr_order.ord_date',
					'gr_order.ord_pre_order_date',
					'gr_order.ord_had_choices',
					'gr_order.ord_delivery_fee',
					'gr_order.ord_currency',
					'gr_order.ord_grant_total',
					'gr_product.pro_images','gr_product.pro_type'
					)
					->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
					->where('gr_order.ord_transaction_id','=',$id)
					->where('gr_order.ord_rest_id','=',$list->id)
					->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//echo '<hr>';
				}
			}
			return $Invoice_Order;
		}
		
		/* get invoice detail */
		public static function get_invoice($order_id)
		{
			$sql = DB::table('gr_order')->select('ord_rest_id','gr_store.st_store_name','gr_store.st_address')->join('gr_store','gr_store.id', '=','gr_order.ord_rest_id')->where('ord_transaction_id','=',$order_id)->groupBy('gr_order.ord_rest_id')->get();
			$result = array();
			if(count($sql) > 0)
			{
				foreach($sql as $q)
				{
					$res =  DB::table('gr_order')->select('gr_order.ord_shipping_cus_name',
					'gr_order.ord_shipping_address',
					'gr_order.ord_shipping_address1',
					'gr_order.ord_shipping_mobile',
					'gr_order.ord_shipping_mobile1',
					'gr_order.order_ship_mail',
					'gr_store.st_store_name',
					'gr_product.pro_item_name',
					'gr_product.pro_per_product',
					'gr_product.pro_type',
					'gr_order.ord_quantity',
					'gr_order.ord_unit_price',
					'gr_order.ord_sub_total',
					'gr_order.ord_tax_amt',
					'gr_order.ord_choices',
					'gr_order.ord_pay_type',
					'gr_order.ord_date',
					'gr_order.ord_transaction_id',
					'gr_order.ord_pre_order_date',
					'gr_order.ord_had_choices',
					'gr_order.ord_delivery_fee',
					'gr_order.ord_currency',
					'gr_customer.cus_fname',
					'gr_customer.cus_lname',
					'gr_customer.cus_address',
					'gr_customer.cus_phone1',
					'gr_customer.cus_email',
					'gr_order.ord_wallet',
					'gr_order.ord_self_pickup',
					'gr_order.ord_spl_req',
					DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image')
					)
					->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
					->join('gr_store','gr_store.id', '=','gr_order.ord_rest_id')
					->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
					->where('gr_order.ord_transaction_id','=',$order_id)
					//->where('gr_order.ord_rest_id','=',$q->ord_rest_id)
					->get();
					if(count($res) > 0)
					{
						foreach($res as $arr)
						{
							$result[$q->st_store_name.'~`'.$q->st_address][] = $arr;
						}
					}
				}
			}
			return $result;
		}
		
		/* get order status timing */
		public static function get_or_status($cus_id,$trans_id,$store_id,$lang,$def_lang,$or_id = '')
		{
			$q = array();
			$st_name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$sql  = DB::table('gr_order')->select('ord_placed_on','ord_accepted_on','ord_rejected_on','ord_prepared_on','ord_dispatched_on','ord_started_on','ord_arrived_on','ord_delivered_on','ord_failed_on','ord_status','ord_cancel_status','ord_id','ord_otp','ord_pay_type','gr_store.st_latitude','gr_store.st_longitude','gr_store.'.$st_name.' as store_name','ord_estimated_arrival_hrs','ord_estimated_arrival_mins','ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_latitude','order_ship_longitude','ord_self_pickup','ord_failed_reason')
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->where(['ord_cus_id' =>$cus_id,'ord_rest_id'=>$store_id,'ord_transaction_id'=>$trans_id]);
			if($or_id != '')
			{
				$q = $sql->where('ord_id','=',$or_id)->first();
			}
			else
			{
				$q = $sql->get();
			}
			
			return $q;
		}
		
		/* get banner */
		public static function get_banner_img()
		{
			return DB::table('gr_banner_image')->select('mob_banner_img','banner_type','ios_banner_img')->where('banner_status','=','1')->get();
		}
		
		public static function get_icon($select)
		{
			return DB::table('gr_logo_settings')->select($select)->first();
		}
		
		public static function chk_minOr_preOr($cus_id,$lang,$def_lang)
		{	
			$now = date('H:i:s');
			$today = date('l');
			$name 	= ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			return DB::table('gr_cart_save')->select('st_minimum_order',DB::Raw("SUM(cart_total_amt) as store_total"),DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$today' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$now' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$now')>0,'Avail','Closed') as store_closed"),'gr_store.st_pre_order','gr_store.'.$name.' as st_name','gr_cart_save.cart_currency','cart_pre_order','gr_store.st_type')
			->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
			->where(['gr_cart_save.cart_cus_id' => $cus_id,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
			->groupBy('gr_cart_save.cart_st_id')
			->get();
		}
		
		/* featured shops */
		public static function get_feat_shops($user_lat,$user_long,$lang,$def_lang,$type,$limit = '')
		{  
			$name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$q = array();
			$current_date = date('Y-m-d');
			
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_store as a')->select('a.*','gr_category.cate_name',DB::raw('1 as active'),DB::Raw('(select AVG(gr_review.review_rating) from gr_review where gr_review.res_store_id=a.id  and gr_review.review_status="1") as review_rating'),'st_logo',$name .' as st_name','a.id');
			
            $sql ->join('gr_featured_booking as b', 'a.id', '=', 'b.store_id')
			->Join('gr_merchant','gr_merchant.id','=','a.st_mer_id')
			->Join('gr_category','gr_category.cate_id','=','a.st_category')
			->where('gr_merchant.mer_status','=','1')
			->where('gr_category.cate_status','=','1')
			->where('a.st_status','=',1)
			->where('b.from_date','<=',$current_date)->where('b.to_date','>=',$current_date)
			->where('admin_approved_status',1)
			->where('a.st_type','=',1)
			->where('a.st_status',1);
			if($user_lat != '' && $user_long != '')
			{		 
				$q=$sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',a.st_latitude,a.st_longitude)) <= a.st_delivery_radius');
			}
			
			$q=$sql->groupby('a.id');
			$q = $sql->orderby('b.total_price','desc')->limit($limit)->get();
			return $q;
			
			// $sql = DB::table('gr_featured_booking')->select('st_logo',$name .' as st_name','gr_store.id')
			// ->Join('gr_store','gr_store.id','=','gr_featured_booking.store_id')	
			// ->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			// ->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			// ->where('gr_featured_booking.from_date','<=',$current_date)
			// ->where('gr_featured_booking.to_date','>=',$current_date)
			// ->where('gr_featured_booking.admin_approved_status','=','1')
			// ->where('gr_merchant.mer_status','=', '1')
			// ->where('gr_category.cate_status','=', '1')
			// ->where('gr_category.cate_type', '=', $type)
			// ->where('gr_store.st_status','=','1')
			// ->where('gr_store.st_type', '=', $type)
			// ->orderby('gr_featured_booking.total_price','desc');
			// if($user_lat != '' && $user_long != '')
			// { 
			// $q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			// }
			// if($limit != '')
			// {
			// $q = $sql->limit($limit)->get();
			// }
			// else
			// {
			// $q = $sql->get();
			// }
			/*$query = DB::getQueryLog();
				print_r($query);
			exit;*/
			
			//return $q;
		}
		
		/* restaurants based on ratings */
		public static function get_rating_shops($user_lat,$user_long,$lang,$def_lang,$type,$limit = '',$exist_ids)
		{
			$name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$q = array();
			$current_date = date('Y-m-d');
			
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_store')->select('st_logo',$name .' as st_name','gr_store.id',DB::Raw('(select AVG(gr_review.review_rating) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="restaurant") as review_rating'))
			->leftJoin('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_merchant.mer_status','=', '1')
			->where('gr_category.cate_status','=', '1')
			//->where('gr_category.cate_type', '=', $type)
			->where('gr_store.st_status','=','1')
			->where('gr_store.st_type', '=', $type)
			;
			if(count($exist_ids) > 0)
			{
				$sql->whereNotIn('gr_store.id',$exist_ids);
			}
			if($user_lat != '' && $user_long != '')
			{ 
				$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			if($limit != '')
			{
				$q = $sql->orderby('review_rating', 'desc')->limit($limit)->get();
			}
			else
			{
				$q = $sql->orderby('review_rating', 'desc')->get();
			}
			/*$query = DB::getQueryLog();
				print_r($query);
			exit;*/
			
			return $q;
		}
		
		public static function wallet_details($id,$page)
		{
			return DB::table('gr_order')->select('ord_currency','ord_wallet','ord_date','ord_transaction_id','cus_wallet','used_wallet')
			->leftJoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
			->where('ord_cus_id','=',$id)
			->where('ord_wallet','!=','0')
			->groupBy('ord_transaction_id')
			->paginate(10,['*'],'review_page',$page);
		}
		
		public static function refered_user_details($id,$page)
		{
			return DB::table('gr_referal')->select('referre_email','re_offer_percent','re_offer_amt')
			->leftJoin('gr_customer','gr_customer.cus_id','=','gr_referal.referral_id')
			->where('referral_id','=',$id)
			->paginate(10,['*'],'review_page',$page);
		}

		public static function get_refund($id,$lang,$def_lang)
		{
			$st_name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
			$pro_name = ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			return DB::table('gr_order')->select('gr_store.'.$st_name.' as st_name','gr_product.'.$pro_name.' as pro_name','gr_order.ord_had_choices','gr_order.ord_choices','ord_grant_total','ord_admin_amt','ord_cancel_paidamt','ord_cancelpaid_transid','cancel_paid_date','ord_cancel_paytype','ord_cancel_status','ord_status','ord_pay_type','ord_currency','ord_cancel_payment_status','ord_pay_type')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$id)
			->Where(function ($query) {
				$query->where('gr_order.ord_cancel_status','=',1)
				->orWhere('gr_order.ord_status','=','9');
			})
			->get();
		}	
		
		public static function check_radius($ship_latitude,$ship_longitude,$st_id)
		{
			return DB::table('gr_store')->select('id')->whereRaw('(SELECT lat_lng_distance('.$ship_latitude.','.$ship_longitude.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius')
			->where('gr_store.id','=',$st_id)
			->first();
			
		}

		/* get Active item count */			
		public static function get_activeItem($ord_id,$st_id,$cus_id)	
		{
			return DB::table('gr_order')->select('ord_id')
										->where(['ord_cus_id' => $cus_id,'ord_transaction_id' => $ord_id,'ord_rest_id' => $st_id])
										->where('ord_cancel_status','!=',1)
										->where('ord_status','<','8')
										->get()->count();
		}

		/* get last added cart for item */
		public static function get_last_cart($cus_id,$item_id)
		{
			return DB::table('gr_cart_save')->select('cart_id')
					->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
					->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
					->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
					->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
					->where(['gr_cart_save.cart_cus_id' => $cus_id,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1','gr_cart_save.cart_item_id'=>$item_id])
					->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
					->orderBy('cart_updated_at','desc')
					->first();
		}
	}   
?>        