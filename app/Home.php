<?php
	namespace App;	
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class Home extends Model
	{
		public static function get_grocery_banner_details()
		{
			return DB::table('gr_banner_image')->where('banner_type','=',3)->where('banner_status','=',1)->get();
		}
		public static function get_food_banner_details()
		{
			return DB::table('gr_banner_image')->where('banner_type','=',4)->where('banner_status','=',1)->get();
		}
		
		
		public static function banner_images(){
			return DB::table('gr_banner_image')
			->where('banner_type','=',4)
			
			->where('banner_status','=',1)->get();
			
		}
		
		public static function check_shop_status($id,$type,$limit = '')
		{  
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$q = array();
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');	
			
			$sql = DB::table('gr_store')->select('st_logo',$name .' as st_name','gr_store.id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_category.cate_type' => $type])
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_store.st_type' => $type])
			->where(['gr_store.id' => $id]);
			if($user_lat != '' && $user_long != '')
			{ 
				/*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
				$q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
			if($limit != '')
			{
				$q = $sql->limit($limit)->get();
			}
			else
			{
				$q = $sql->get();
			}
			
			return $q;
		}
		
		public static function get_all_shops($type,$limit = '')
		{  
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$q = array();
			
			/*select `st_logo`, `st_store_name` as `st_name`, `gr_store`.`id`,IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='Monday' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '02:30:00' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '02:30:00')>0,'Avail','Closed') as store_closed from `gr_store` inner join `gr_category` on `gr_category`.`cate_id` = `gr_store`.`st_category` inner join `gr_merchant` on `gr_merchant`.`id` = `gr_store`.`st_mer_id` where (`gr_merchant`.`mer_status` = '1') and (`gr_category`.`cate_status` = '1') and (`gr_category`.`cate_type` = '1') and (`gr_store`.`st_status` = '1') and (`gr_store`.`st_type` = '1') and (SELECT lat_lng_distance(11.0168445,76.95583209999995,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius limit 12*/
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');	
			
			$sql = DB::table('gr_store')->select('st_logo',$name .' as st_name','gr_store.id','gr_store.store_slug')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_category.cate_type' => $type])
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_store.st_type' => $type]);
			if($user_lat != '' && $user_long != '')
			{ 
				/*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
				$q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
			if($limit != '')
			{
				$q = $sql->limit($limit)->get();
			}
			else
			{
				$q = $sql->get();
			}
			
			return $q;
		}
		
		
        public static function get_feat_shops($type,$limit = '')
        {
            $name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
            $q = array();
            $current_date = date('Y-m-d');
            $user_lat = (double)Session::get('search_latitude');
            $user_long= (double)Session::get('search_longitude');
			
            $sql = DB::table('gr_featured_booking')->select('st_logo',$name .' as st_name','gr_store.id','gr_store.store_slug')
			->Join('gr_store','gr_store.id','=','gr_featured_booking.store_id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_featured_booking.from_date','<=',$current_date)
			->where('gr_featured_booking.to_date','>=',$current_date)
			->where('gr_featured_booking.admin_approved_status','=','1')
			->where('gr_merchant.mer_status','=', '1')
			->where('gr_category.cate_status','=', '1')
			->where('gr_category.cate_type', '=', $type)
			->where('gr_store.st_status','=','1')
			->where('gr_store.st_type', '=', $type);
            if($user_lat != '' && $user_long != '')
            {
                /*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
                $q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
            if($limit != '')
            {
                $q = $sql->limit($limit)->get();
			}
            else
            {
                $q = $sql->get();
			}
			
			
            return $q;
		}
		
		/** all categories **/
		public static function get_all_categories($type,$limit = '')
		{   
			$name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
			
			$sql = DB::table('gr_category')->select('cate_id',$name.' as category_name')
			->leftJoin('gr_store','gr_store.st_category','=','gr_category.cate_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_store.st_status','=','1') 
			->where('gr_merchant.mer_status','=','1')
			->where('cate_status','=','1')
			->where('cate_type','=',$type)
			->groupBy('gr_store.st_category')
			->havingRaw("count(gr_store.st_category) > 0")
			->orderBy(DB::raw('count(gr_store.st_category)'),'Desc');
			if($user_lat != '' && $user_long != '')
			{ 
				/*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
				$q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
			if($limit != '')                              
			{
				$q = $sql->paginate($limit);
			}
			else
			{
				$q = $sql->get();
			}
			
			return $q;
		}
		public static function get_category_shops_byCategory_pofi($type,$paginate = '',$user_lat='',$user_long='',$top_disc_filter='', $del_time_filter='', $rate_filter='', $text='', $cuisines)
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$cate_name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'st_desc' : 'st_desc_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
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
			$sql = DB::table('gr_store')->select('st_banner',$name .' as st_name','gr_category.'.$cate_name.' as category_name',$desc.' as st_desc','st_logo','gr_category.cate_id','gr_store.id',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'st_rating','st_delivery_duration','st_delivery_time',DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="'.$review_type.'") as avg_val'),DB::Raw("IF(`st_delivery_duration` = 'hours', CAST((st_delivery_time*60) AS SIGNED),CAST(st_delivery_time AS SIGNED) ) as delDurationInMin"),DB::Raw('(SELECT IFNULL(((AVG(pro_discount_price)/AVG(pro_original_price))*100),0) from gr_product where gr_product.pro_store_id=gr_store.id  and gr_product.pro_has_discount="Yes" and gr_product.pro_status="1") as off'),'gr_store.store_slug')
			
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_category.cate_type' => $type])
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_store.st_type' => $type]);
			if($cuisines != ''){
				$q = $sql->whereIn('gr_store.st_category',$cuisines);
			}
			
            if(Session::get('front_lang_code') != 'en'){
                $q =  $sql->addselect('gr_store.st_store_name');
			}
			if($user_lat != '' && $user_long != '')
			{
				/*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
				$q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
			if($text!=''){
				$q = $sql->where($name,'LIKE',''.$text.'%');
			}
			if($del_time_filter=='1'){
				$q = $sql->orderBy('delDurationInMin','asc');
			}
			if($top_disc_filter=='1'){
				$q = $sql->orderBy('off','desc');
			}
			if($rate_filter=='1'){
				$q = $sql->orderBy('avg_val','desc');
			}
			if($paginate != '')
			{
				$q = $sql->paginate($paginate);
			}
			else
			{
				$q = $sql->limit(24)->get();
			}
			return $q; 
		}
		
		/** restaurants based on category **/
		public static function get_category_shops_pofi($id,$type,$paginate = '',$top_disc_filter='', $del_time_filter='', $rate_filter='', $text='', $cuisines)
		{
			
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$cate_name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'st_desc' : 'st_desc_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
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
			
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_store')->select('st_banner','st_rating','st_delivery_time','st_delivery_duration',$name .' as st_name','gr_category.'.$cate_name.' as category_name',$desc.' as st_desc','st_logo','gr_category.cate_id','gr_store.id',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="'.$review_type.'") as avg_val'),DB::Raw("IF(`st_delivery_duration` = 'hours', CAST((st_delivery_time*60) AS SIGNED),CAST(st_delivery_time AS SIGNED) ) as delDurationInMin"),DB::Raw('(SELECT IFNULL(((AVG(pro_discount_price)/AVG(pro_original_price))*100),0) from gr_product where gr_product.pro_store_id=gr_store.id  and gr_product.pro_has_discount="Yes" and gr_product.pro_status="1") as off'),'gr_store.store_slug')
			//,DB::Raw("((AVG(pro_discount_price)/AVG(pro_original_price))*100) AS off")
			//->leftJoin('gr_product','gr_product.pro_store_id','=','gr_store.id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1']);
			if($cuisines != ''){
				$q = $sql->whereIn('gr_store.st_category',$cuisines);
			}else{
				$q = $sql->where(['gr_store.st_category' => $id]);
			}
			
			$q = $sql->where(['gr_store.st_status'=>'1'])
			//->where(['gr_product.pro_has_discount'=>'Yes'])
			->where(['gr_store.st_type' => $type]);
			
            if(Session::get('front_lang_code') != 'en'){
                $q =  $sql->addselect('gr_store.st_store_name');
			}
			if($user_lat != '' && $user_long != '')
			{ 
				$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			if($text!=''){
				$q = $sql->where($name,'LIKE',''.$text.'%');
			}
			if($del_time_filter=='1'){
				$q = $sql->orderBy('delDurationInMin','asc');
			}
			if($top_disc_filter=='1'){
				$q = $sql->orderBy('off','desc');
			}
			if($rate_filter=='1'){
				$q = $sql->orderBy('avg_val','desc');
			}
			if($paginate != '')
			{
				$q = $sql->paginate($paginate);
			}
			else
			{
				$q = $sql->limit(24)->get();
			}
			// $query = DB::getQueryLog();
			// print_r($query);
			// exit;
			return $q; 
		}
		
		/** restaurants based on category **/
		public static function get_category_shops($id,$type,$paginate = '')
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$cate_name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'st_desc' : 'st_desc_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
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
			$sql = DB::table('gr_store')->select('st_banner','st_rating','st_delivery_time','st_delivery_duration',$name .' as st_name','gr_category.'.$cate_name.' as category_name',$desc.' as st_desc','st_logo','gr_category.cate_id','gr_store.id',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="'.$review_type.'") as avg_val'))
			
			
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_store.st_category' => $id])
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_store.st_type' => $type]);
			
            if(Session::get('front_lang_code') != 'en'){
                $q =  $sql->addselect('gr_store.st_store_name');
			}
			if($user_lat != '' && $user_long != '')
			{ 
				/*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
				$q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
			if($paginate != '')
			{
				$q = $sql->paginate($paginate);
			}
			else
			{
				$q = $sql->limit(24)->get();
			}
			
			return $q; 
		}
		public static function get_category_shops_byCategory($type,$paginate = '',$user_lat='',$user_long='')
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$cate_name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'st_desc' : 'st_desc_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
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
			$sql = DB::table('gr_store')->select('st_banner',$name .' as st_name','gr_category.'.$cate_name.' as category_name',$desc.' as st_desc','st_logo','gr_category.cate_id','gr_store.id',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'st_rating','st_delivery_duration','st_delivery_time',DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="'.$review_type.'") as avg_val'))
			
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_category.cate_type' => $type])
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_store.st_type' => $type]);
			
            if(Session::get('front_lang_code') != 'en'){
                $q =  $sql->addselect('gr_store.st_store_name');
			}
			if($user_lat != '' && $user_long != '')
			{
				/*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
				$q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
			}
			if($paginate != '')
			{
				$q = $sql->paginate($paginate);
			}
			else
			{
				$q = $sql->limit(24)->get();
			}
			return $q; 
		}
		/** get banner **/
		public static function get_banner($type)
		{
			return DB::table('gr_banner_image')->select('image_title','image_text','banner_image')
			->where(['banner_type' => $type,'banner_status' => '1'])
			->get();
		}
		
		/** get store/restaurant categories **/
		public static function get_categories($id,$type)
		{   
			$cate_name = (Session::get('front_lang_code') == 'en') ? 'pro_mc_name' : 'pro_mc_name_'.Session::get('front_lang_code');
			return DB::table('gr_proitem_maincategory')->select('pro_mc_id',$cate_name.' as mc_name')
			->Join('gr_product','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->where(['gr_product.pro_status' => '1',
			'gr_proitem_maincategory.pro_mc_status' => '1',
			'gr_product.pro_store_id' => $id,
			'gr_proitem_maincategory.pro_mc_type' => $type])
			->groupBy('gr_product.pro_category_id')
			->orderBy(DB::raw('count(gr_product.pro_category_id)'),'Desc')
			->get();
		}
		
		
		
		/** get store/resturant  banner details **/
		public static function get_shop_details($id)
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'st_desc' : 'st_desc_'.Session::get('front_lang_code');
			$current_time = date('H:i:s');
			
            $cate_name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			
            $cancel = (Session::get('front_lang_code') == 'en') ? 'mer_cancel_policy' : 'mer_cancel_policy_'.Session::get('front_lang_code');
			
			$current_day=date('l');
			return DB::table('gr_store')->select('gr_store.id',$name.' as st_name','st_minimum_order','st_pre_order','st_delivery_time','st_delivery_duration',$desc.' as st_desc','st_delivery_radius','st_logo','st_banner','st_rating','st_currency','st_address',$cate_name.' as cate_name','cate_id','gr_merchant.refund_status','gr_merchant.cancel_status',$cancel.' as mer_cancel_policy',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.res_store_id='.$id.'  and gr_review.review_status="1" and (gr_review.review_type="restaurant" or gr_review.review_type="store")) as avg_val'))
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->where(['gr_merchant.mer_status' => '1'])
			->where(['gr_category.cate_status' => '1'])
			->where(['gr_store.id' => $id])
			->where(['gr_store.st_status'=>'1'])
			->first();
		}
		/* get delevery time */
		public static function get_delevery_time($id){
			
			return DB::table('gr_store')->select('st_delivery_time','st_delivery_duration')->where('id','=',$id)->first();
		}
		
		
		/** item details **/
		public static function get_items($st_id,$mc_id='',$sc_id='',$sortby = '',$type,$text = '',$pro_veg ='',$top_disc='')
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'pro_desc' : 'pro_desc_'.Session::get('front_lang_code');
			$contains = (Session::get('front_lang_code') == 'en') ? 'pro_per_product' : 'pro_per_product_'.Session::get('front_lang_code');
			
            $taxname = (Session::get('front_lang_code') == 'en') ? 'pro_tax_name' : 'pro_tax_name_'.Session::get('front_lang_code');
			$q   = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			if($type == 2)
			{
				$review_type = "item";
			}
			else
			{
				$review_type = "product";
			}
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_product')
			->select('gr_product.pro_id','gr_product.'.$name.' as item_name','pro_original_price','pro_has_discount','pro_discount_price','gr_product.'.$desc.' as desc','pro_currency','gr_product.pro_rating','gr_product.pro_no_of_purchase','gr_product.pro_images','gr_product.'.$contains.' as contains','gr_product.pro_quantity','gr_product.pro_had_choice','gr_product.pro_had_tax',$taxname .' as pro_tax_name','gr_product.pro_tax_percent',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'gr_product.pro_store_id','gr_product.pro_per_product','gr_product.pro_rating','gr_product.pro_veg','gr_store.st_pre_order',DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.proitem_id=gr_product.pro_id  and gr_review.review_status="1" and gr_review.review_type="'.$review_type.'") as avg_val'),'gr_store.store_slug','gr_product.pro_item_slug');
			if($top_disc=='1'){
				$sql->addSelect(DB::Raw("IF(`pro_has_discount` = 'yes', ((`pro_original_price` - `pro_discount_price`)/`pro_original_price`)*100,'0' ) as percentage"));
			}
			
			$sql->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where(['gr_product.pro_status' => '1'])
			->where(['gr_proitem_maincategory.pro_mc_status' => '1'])
			->where(['gr_proitem_subcategory.pro_sc_status' => '1'])
			->whereRaw(('gr_proitem_subcategory.pro_main_id=gr_product.pro_category_id'))
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_product.pro_store_id' => $st_id,'gr_product.pro_type' => $type]);
			if($mc_id != '')                          
			{
				$q = $sql->where(['gr_product.pro_category_id' => $mc_id]);
			}
			if($sc_id != '')
			{
				$q = $sql->where(['gr_product.pro_sub_cat_id' => $sc_id]);
			}
			if ($text != '') 
			{
				$q = $sql->where('gr_product.'.$name,'LIKE','%'.$text.'%');
			}
			if($pro_veg != ''){
				//echo 'inside';
				//$q = $sql->where(['gr_product.pro_veg' =>$pro_veg]);
				 $q = $sql->whereIn('gr_product.pro_veg',$pro_veg);
			}
			
			if($sortby == 'new' || $sortby == '') //newest
			{
				$q = $sql->orderBy('pro_id','desc');
			}
			if($sortby == 'a_z') //title a-z
			{
				$q = $sql->orderBy('gr_product.'.$name,'asc');
			}
			if($sortby == 'z_a') //title z-a
			{
				$q = $sql->orderBy('gr_product.'.$name,'desc');
			}
			if($sortby == 'low_high') //original price low to high
			{
				$q = $sql->orderBy('gr_product.pro_original_price','asc');
			}
			if($sortby == 'high_low') //original price high to low
			{
				$q = $sql->orderBy('gr_product.pro_original_price','desc');
			}
			if($top_disc=='1'){
				$q = $sql->orderBy('percentage','desc');
			}
			$q = $sql->paginate(12);
			/*$query = DB::getQueryLog();
			print_r($query);
			exit;*/
			return $q;                        
		}
		
		
		public static function get_items_autocomplete($st_id,$mc_id,$sc_id,$query,$type)
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'pro_desc' : 'pro_desc_'.Session::get('front_lang_code');
			$contains = (Session::get('front_lang_code') == 'en') ? 'pro_per_product' : 'pro_per_product_'.Session::get('front_lang_code');
			$q   = array();
			
			$sql = DB::table('gr_product')
			->select('gr_product.'.$name.' as item_name',DB::Raw('SUBSTRING_INDEX(`pro_images`, "/**/", 1) as pdt_image'))         
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where(['gr_product.pro_status' => '1'])
			->where(['gr_proitem_maincategory.pro_mc_status' => '1'])
			->where(['gr_proitem_subcategory.pro_sc_status' => '1'])
			->whereRaw(('gr_proitem_subcategory.pro_main_id=gr_product.pro_category_id'))
			->where(['gr_store.st_status'=>'1'])
			->where(['gr_product.pro_store_id' => $st_id,'gr_product.pro_type' => $type]);
			if($mc_id != '')                          
			{
				$q = $sql->where(['gr_product.pro_category_id' => $mc_id]);
			}
			if($sc_id != '')
			{
				$q = $sql->where(['gr_product.pro_sub_cat_id' => $sc_id]);
			}
			if ($query != '') {
				$q = $sql->where('gr_product.'.$name,'like','%'.$query.'%');
			}
			$q = $sql->get();
			
			return $q;                        
		}
		public static function get_product_details($pro_id,$type)
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$desc = (Session::get('front_lang_code') == 'en') ? 'pro_desc' : 'pro_desc_'.Session::get('front_lang_code');
			$contains = (Session::get('front_lang_code') == 'en') ? 'pro_per_product' : 'pro_per_product_'.Session::get('front_lang_code');
			
            $taxname = (Session::get('front_lang_code') == 'en') ? 'pro_tax_name' : 'pro_tax_name_'.Session::get('front_lang_code');
			$q   = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			
			return 
			DB::table('gr_product')
			->select('gr_product.pro_id','gr_product.'.$name.' as item_name','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_product.'.$desc.' as desc','gr_product.pro_currency','gr_product.pro_rating','gr_product.pro_no_of_purchase','gr_product.pro_images','gr_product.pro_category_id','gr_product.pro_had_choice','gr_product.'.$contains.' as contains',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),DB::Raw('(select IFNULL(AVG(gr_review.review_rating),0) from gr_review where gr_review.proitem_id='.$pro_id.'  and gr_review.review_status="1") as avg_val'), DB::Raw('(select count(gr_review.comment_id) FROM gr_review where gr_review.proitem_id='.$pro_id.' and gr_review.review_status="1") as num_reviewers'),'gr_product.pro_store_id','gr_product.pro_quantity','gr_product.pro_had_tax',$taxname.' as pro_tax_name','gr_product.pro_tax_percent',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'gr_store.st_pre_order')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->where(['gr_product.pro_status' => '1','gr_product.pro_type' => $type,'gr_product.pro_id'=>$pro_id,'gr_merchant.mer_status'=>'1','gr_category.cate_status'=>'1','gr_store.st_status'=>'1','gr_proitem_maincategory.pro_mc_status'=>'1','gr_proitem_subcategory.pro_sc_status'=>'1'])->first();
			
		}
		public static function get_product_reviews($pro_id,$type)
		{
			$q   = array();
			
			return DB::table('gr_review')
			->select('gr_review.review_comments','gr_review.review_rating','gr_review.created_date','gr_customer.cus_fname','gr_customer.cus_lname','gr_customer.cus_email','gr_customer.cus_image')         
			->Join('gr_customer','gr_customer.cus_id','=','gr_review.customer_id')
			->where(['gr_review.proitem_id' => $pro_id,'gr_review.review_type'=>$type,'gr_review.review_status'=>'1'])
			->where(['gr_customer.cus_status' => '1'])
			->get();
		}
		
		
		public static function get_shop_review($id,$type)
		{
			return DB::table('gr_review')->select('cus_fname','review_comments','review_rating','review_status','comment_id','gr_store.st_store_name','review_type','gr_merchant.mer_fname','cus_image')
            ->leftjoin('gr_store','gr_review.res_store_id', '=', 'gr_store.id')
            ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
            ->leftjoin('gr_merchant','gr_store.st_mer_id','gr_merchant.id')
            ->where(['review_status' =>'1'])
            ->where(['review_type' => $type])
            ->where(['res_store_id' => $id])
            ->paginate(10);
		}
		public static function get_relatedPdt_details($pro_id,$pro_category_id,$type)
		{
			$store_id = DB::table('gr_product')->select('pro_store_id')->where('pro_id','=',$pro_id)->first();
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$contains = (Session::get('front_lang_code') == 'en') ? 'pro_per_product' : 'pro_per_product_'.Session::get('front_lang_code');
			$q   = array();
			DB::connection()->enableQueryLog();
			
			$sql = DB::table('gr_proitem_maincategory')
			->select('gr_product.pro_id','gr_product.'.$name.' as item_name','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_product.pro_currency','gr_product.pro_images','gr_product.'.$contains.' as contains','gr_store.store_slug','gr_product.pro_item_slug')         
			->Join('gr_product','gr_product.pro_category_id','=','gr_proitem_maincategory.pro_mc_id')
			->leftjoin('gr_store','gr_store.id', '=', 'gr_product.pro_store_id')
			->where(['gr_product.pro_status' => '1','gr_product.pro_store_id' => $store_id->pro_store_id])
			->where(['gr_proitem_maincategory.pro_mc_status' => '1'])
			->where(['gr_proitem_maincategory.pro_mc_id' => $pro_category_id,'gr_product.pro_type' => $type])
			->where('gr_product.pro_id','!=',$pro_id);
			
			
			$q = $sql->inRandomOrder();
			$q = $sql->limit(10)->get();
			return $q;      
		}
		public static function get_choice_details($pro_id)
		{
			$ch_name = (Session::get('front_lang_code') == 'en') ? 'ch_name' : 'ch_name_'.Session::get('front_lang_code');
			
			
			
			$q   = array();
			DB::connection()->enableQueryLog();
			
			$sql = DB::table('gr_product_choice')
			->select('gr_product_choice.pc_id','gr_product_choice.pc_choice_id','gr_choices.'.$ch_name.' as choice_name','gr_product_choice.pc_price','gr_product.pro_currency')         
			->Join('gr_choices','gr_choices.ch_id','=','gr_product_choice.pc_choice_id')
			->Join('gr_product','gr_product.pro_id','=','gr_product_choice.pc_pro_id')
			->where(['gr_choices.ch_status' => '1'])
			->where(['gr_product.pro_type' => '2'])
			->where('gr_product_choice.pc_pro_id','=',$pro_id)->get();
			return $sql;  
			
		}
		
		public static function check_wishlist($pro_id,$cus_id){
			return DB::table('gr_wishlist')->where('ws_pro_id','=',$pro_id)->where('ws_cus_id','=',$cus_id)->count();
		}
		
		public static function insert_wish($entry)
		{
			$insertcheck = DB::table('gr_wishlist')->insert($entry);
			
		}
		
		public static function remove_wish_product($id){ // to remove product from wishlist table ( id = wishlist_id )
			return DB::table('gr_wishlist')->where('ws_id','=',$id)->delete();
		}
		public static function get_products_incart($cart_type='')
		{
			$user_id=(int)Session::get('customer_id');
			$store_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$pdt_name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$contains = (Session::get('front_lang_code') == 'en') ? 'pro_per_product' : 'pro_per_product_'.Session::get('front_lang_code');
			$q   = array();
			$current_time = date('H:i:s');
			$current_day=date('l');
			$shippingDet = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id','=',(int)Session::get('customer_id'))->first();
			if(empty($shippingDet)===false){
				$user_lat = $shippingDet->sh_latitude;
				$user_long= $shippingDet->sh_longitude;
			}else{
				$user_lat = '0.000';
				$user_long= '0.000';
			}
			//GROUP BY
			$cart_array=array();
			// DB::connection()->enableQueryLog();
			$group_sql = DB::table('gr_cart_save')
			->select('gr_store.'.$store_name.' as store_names','gr_cart_save.cart_st_id','gr_store.st_type','st_currency',DB::Raw('IFNULL(st_minimum_order, 0) as st_minimum_order') ,DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"),'gr_store.st_pre_order','st_latitude','st_longitude',DB::Raw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) AS distance'),'gr_store.st_delivery_radius')
				/**/
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->where(['gr_cart_save.cart_cus_id'=>$user_id])
			->where(['gr_store.st_status'=>'1'])
			->groupBy('gr_cart_save.cart_st_id')->get();
			// $query = DB::getQueryLog();
		// print_r($query);
		// exit;
			if(count($group_sql) > 0 )
			{
				foreach($group_sql as $gsql)
				{
					//echo $gsql->cart_st_id.'<bR>'; pro_original_price pro_discount_price pro_has_discount cart_tax
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_cart_save')
					->select('gr_cart_save.cart_id','gr_product.pro_id','gr_product.pro_item_code','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_cart_save.cart_type','gr_cart_save.cart_type','gr_product.pro_had_choice','gr_product.'.$pdt_name.' as item_name','gr_product.'.$contains.' as contains_name','gr_product.pro_images','gr_cart_save.cart_quantity','gr_cart_save.cart_currency','gr_cart_save.cart_unit_amt','gr_cart_save.cart_total_amt','gr_cart_save.cart_had_choice','gr_cart_save.cart_choices_id',DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'),'gr_cart_save.cart_tax','gr_product.pro_currency','gr_product.pro_type','gr_cart_save.cart_pre_order','gr_cart_save.cart_pre_order','gr_store.id as store_id_is','gr_product.pro_store_id','gr_merchant.id as mer_id','gr_merchant.mer_commission','gr_product.pro_no_of_purchase','gr_merchant.refund_status','gr_merchant.cancel_status','cart_spl_req','gr_country.co_curcode','gr_store.store_slug','gr_product.pro_item_slug')         
					->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
					->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
					->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
					->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
					->where(['gr_cart_save.cart_cus_id' => $user_id,'gr_country.co_status'=>'1','gr_cart_save.cart_st_id' => $gsql->cart_st_id,'gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
					->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
					->get();
					$query = DB::getQueryLog();
					
					if(count($sql) > 0 )
					{
						foreach($sql as $sq)
						{
							//echo $sq->store_name.'<br>';
							
							$cart_array[$gsql->store_names.'~`'.$gsql->cart_st_id.'~`'.$gsql->st_type.'~`'.$gsql->st_currency.'~`'.$gsql->st_minimum_order.'~`'.$gsql->store_closed.'~`'.$gsql->st_pre_order.'~`'.$gsql->st_latitude.'~`'.$gsql->st_longitude.'~`'.$gsql->distance.'~`'.$gsql->st_delivery_radius][] = $sq;
							//print_r($sq); echo '<hr>';
						}
					}
				}
			} 
			//echo '<pre>'; print_r($cart_array);echo '</pre>'; 
			//exit;
			return $cart_array;  
		}
		public static function get_products_incart_byTransId($cart_type='')
		{
			
		}
		public static function get_wishlist_details($cus_id)
		{
			$cus_id = (int)$cus_id;
            $name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			/*
				$sql = DB::table('gr_store')->select('st_logo',$name .' as st_name','gr_store.id')
				->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
				->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
				->where(['gr_merchant.mer_status' => '1'])
				->where(['gr_category.cate_status' => '1'])
				->where(['gr_category.cate_type' => $type])
				->where(['gr_store.st_status'=>'1'])
				->where(['gr_store.st_type' => $type]);
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
			*/
			return DB::table('gr_wishlist')->select($name .' as pro_item_name','gr_product.pro_quantity','gr_product.pro_no_of_purchase','gr_product.pro_id','gr_wishlist.ws_type','gr_wishlist.ws_id','gr_product.pro_original_price','gr_product.pro_has_discount','gr_product.pro_discount_price','gr_product.pro_currency','gr_store.store_slug','gr_product.pro_item_slug')
			->Join('gr_product','gr_wishlist.ws_pro_id','=','gr_product.pro_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->where('ws_cus_id',$cus_id)
			->where('gr_product.pro_status','=','1')
			/**/
			->where('gr_merchant.mer_status','=','1')
			->where('gr_category.cate_status','=','1')
			->where('gr_store.st_status','=','1')
			->where('gr_proitem_maincategory.pro_mc_status','=','1')
			->where('gr_proitem_subcategory.pro_sc_status','=','1')
			//->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
			/**/
			->get();
		}
		
		/** referel status **/
		public static function refer_status($mail,$type = '')
		{	
			$q  =array();
			$sql =  DB::table('gr_referal')->select('referral_id','re_offer_percent','re_purchased')
			->Join('gr_customer','gr_referal.referral_id','=','gr_customer.cus_referedBy')
			->where('re_purchased','=','0')
			->where('referre_email','=',$mail);
			if($type == "mobile")
			{
				$q = $sql->where('re_code_used','=','1');
			}
			$q = $sql->first();
			return $q;
		}
		
		/** get merchant overall order details **/
		public static function merchant_orderDetails($id)
		{
			return DB::table('gr_merchant_overallorder')->select('or_total_order','or_admin_amt','or_coupon_amt','or_mer_amt')			
			->where(['or_mer_id' => $id])->first();
		}
		
		/** get refered details**/
        public static function get_refered_details($id,$start = '',$limit='',$order='')
        {
            $q = array();
            $sql = DB::table('gr_referal')->select('referre_email','re_offer_percent','re_offer_amt','gr_customer.cus_wallet','gr_customer.used_wallet')
			->where('referral_id','=', $id)
			->Join('gr_customer','gr_customer.cus_id','=','gr_referal.referral_id');
            if($start != '' && $limit != '')
            {
                $q = $sql->orderBy($order)->skip($start)->take($limit)->get();
			}
            else
            {
                $q = $sql->get();
			}
            return $q;
		}
		
		/** order details  **/
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
		
		
		public static function get_order_details($id)
		{
            $store_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$store_list = DB::table('gr_order')->select($store_name . ' as st_store_name','gr_store.id','gr_merchant.mer_email')
			->Join('gr_store','gr_store.id', '=', 'gr_order.ord_rest_id')
			->Join('gr_merchant','gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->where('gr_order.ord_transaction_id','=',$id)
			->groupBy('gr_order.ord_rest_id')
			->get();
			$Invoice_Order = array();		
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			if(count($store_list) > 0)
			{
				foreach($store_list as $list)
				{
					
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
					
				}
			}
			return $Invoice_Order;
		}
		
		
		public static function get_cancelled_customer_byOrderId($id)
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
			->where('gr_order.ord_id','=',$id)
			->where('gr_order.ord_cancel_status','=','1')
			->groupBy('gr_order.ord_cus_id')
			->first();
			return $customer_det;
		}
		public static function get_cancelled_store_byOrderId($id)
		{
			
			$store_list = DB::table('gr_order')->select('gr_store.st_store_name','gr_store.id','gr_order.ord_merchant_id','gr_merchant.mer_email')
			
			->Join('gr_store','gr_store.id', '=', 'gr_order.ord_rest_id')
			->Join('gr_merchant','gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->where('gr_order.ord_id','=',$id)
			->where('gr_order.ord_cancel_status','=','1')
			->get();
			return $store_list;
		}
		public static function get_cancelled_order_byOrderId($id,$store_id)
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			
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
		
		public static function get_faq()
		{
			return DB::table('gr_faq')->select('faq_name_'.Session::get('front_lang_code').' as que','faq_ans_'.Session::get('front_lang_code').' as ans')->where(['faq_status' => '1'])->paginate(10);
		}
		
		/* calculate store/restaurant added product amount */
        public static function get_shop_total_amt($st_id)
        {
            $amt = DB::table('gr_cart_save')->select(DB::Raw('SUM(cart_total_amt) as total_amt'))->where(['cart_st_id' => $st_id])
			->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
			->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->where(['gr_cart_save.cart_st_id' => $st_id,'gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1','gr_cart_save.cart_cus_id' => Session::get('customer_id')])
			->whereRaw('gr_product.pro_quantity>gr_product.pro_no_of_purchase')
			->first();
            if(!empty($amt))
            {
                return $amt->total_amt;
			}
            else
            {
                return 0;
			}
		}
		
		public static function get_pre_footerDetails(){
			//        return DB::table('gr_general_setting')->select('playstore_link',
			//            'itunes_link','prefooter_text','prefooter_desc','app_sec_image'
			//            )->get();
            $Active_language = DB::table('gr_language')->where('status','=','1')->get();
			$q = array();
			
			$sql = DB::table('gr_general_setting');
            $sql ->select('playstore_link',
			'itunes_link','prefooter_text','prefooter_desc','app_sec_image');
            if(count($Active_language) > 0 ){
                foreach($Active_language as $default){
                    $lang = $default->lang_code;
                    if($lang != 'en') {
                        $sql->addselect('prefooter_desc_' . $lang,'prefooter_text_'.$lang);
					}
				}
			}
			
			$q = $sql->get();
            return $q;
		}
		
		
		
        public static function get_advertise_Details(){
            return DB::table('gr_general_setting')->select('gs_advertisement_title','gs_advertisement_desc','gs_advertisement_image')->get();
		}
		
		public static function get_category_details($user_lat='',$user_long=''){
			$q = array();
			// ,DB::Raw("(SELECT count('gr_store.id') FROM `gr_store` WHERE `st_type`=1) as res_count")
			// $sql = DB::table('gr_category')->select('gr_category.cate_name','gr_category.cate_img','gr_category.cate_id','gr_store.id','gr_store.st_store_name','gr_category.cate_icon') 
			/*,DB::Raw("(SELECT count('gr_store.id') FROM `gr_store` WHERE `st_category`=gr_category.cate_id AND `st_type`=1) as res_count"),(DB::Raw("(SELECT count('gr_product.pro_id') FROM `gr_product` WHERE `pro_store_id`=gr_store.id AND `st_type`=1) as pro_count"))*/
			DB::connection()->enableQueryLog();
			$name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
			$sql = DB::table('gr_category')->select('gr_category.cate_name','gr_category.'.$name.' as '.$name,'gr_category.cate_img','gr_category.cate_id','gr_store.id','gr_store.st_store_name','gr_category.cate_icon',(DB::Raw("COUNT(gr_product.pro_id) as pro_count")))
			->leftJoin('gr_store','gr_store.st_category','=','gr_category.cate_id') 
			->leftJoin('gr_product','gr_product.pro_store_id','=','gr_store.id')
			->where('gr_store.st_type','=',1)    
			->where('gr_store.st_status','=',1)    
			->where('gr_category.cate_type','=','1')
			->where('gr_category.cate_status','=','1') //AND gr_product.`pro_type`='2' AND gr_product.pro_status='1'
			->where('gr_product.pro_type','=','2') 
			->where('gr_product.pro_status','=','1');
			$user_lat = Session::get('search_latitude');
			$user_long= Session::get('search_longitude');
			if($user_lat != '' && $user_long != '')
			{ 
				$q=$sql->whereRaw('(SELECT lat_lng_distance('.(double)$user_lat.','.(double)$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			
			$q = $sql->groupBy('gr_store.st_category')
			//->orderby('cate_store_count','desc')
			->orderby('pro_count','desc')
			->limit(5)
			->get();
			// $query = DB::getQueryLog();
		    // print_r($query);
			return $q;
		}
		
		public static function near_by_restaurant($lat,$long,$catId){
			
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
			$q = array();
			
			$q = DB::table('gr_store')->select('gr_store.*','gr_category.cate_name')
			->addSelect(DB::Raw("(SELECT count('gr_product.pro_id') FROM `gr_product` WHERE `pro_store_id`=gr_store.id) as pro_count"),DB::Raw('(select AVG(gr_review.review_rating) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type ="restaurant") as review_rating'));
			if($user_lat != '' && $user_long != '')
			{ 
				$q=$q->whereRaw('(SELECT lat_lng_distance('.(double)$lat.','.(double)$long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			
			$q = $q->leftJoin('gr_product','gr_product.pro_id','=','gr_store.id')
			->leftJoin('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->where('gr_store.st_category','=',$catId)
			->where('gr_store.st_type','=',1)
			->where('gr_store.st_status','=',1)		
			->orderby('pro_count','desc')		
			->limit(2)
			->get();
			return $q;
			//echo '<pre>'; print_r($q); exit;
		}
		
		public static function search_restaurantdet($stname =''){
			// return DB::table('gr_store')->get();
			$stname = mysql_escape_special_chars($stname);
			$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
			$q = array();
			
			$sql = DB::table('gr_store')->select('gr_store.st_store_name','gr_store.id','gr_store.store_slug');
			if(Session::get('front_lang_code') != 'en'){
				$sql->addselect($name);
			}
			$sql->leftJoin('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id');
			$sql->leftJoin('gr_category','gr_category.cate_id','=','gr_store.st_category');
			if($user_lat != '' && $user_long != '')
			{ 
				$q=$sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
			}
			$sql->where('gr_merchant.mer_status','=','1');
			$sql->where('gr_category.cate_status','=','1');
			$sql->where('gr_store.st_status','=','1');
			if($stname != ''){
				$sql->where('gr_store.'.$name,'like','%'.$stname.'%');
			}
			
    		$q = $sql->get();
    		return $q;
			
		}
		
		
        public static function get_refund($id)
        {
            $st_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
            $pro_name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
            return DB::table('gr_order')->select('gr_store.'.$st_name.' as st_name','gr_product.'.$pro_name.' as pro_name','gr_order.ord_had_choices','gr_order.ord_choices','ord_grant_total','ord_admin_amt','ord_cancel_paidamt','ord_cancelpaid_transid','cancel_paid_date','ord_cancel_paytype','ord_cancel_status','ord_status','ord_pay_type','ord_currency','ord_cancel_payment_status','ord_pay_type')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$id)
			->where(function ($query) {
				$query->where('gr_order.ord_cancel_status','=',1);
				//->orWhere('gr_order.ord_status','=','9');
			})
			->get();
		}
		
        public static function wallet_details($id,$start,$limit,$order,$dir)
        {
            return DB::table('gr_order')->select('ord_currency','ord_wallet','ord_date','ord_transaction_id')
			->where('ord_cus_id','=',$id)
			->where('ord_wallet','!=','0')
			->orderBy($order,$dir)
			->groupBy('ord_transaction_id')
			->skip($start)->take($limit)->get();
		}
        public static function get_feature_restaurant(){
			
            $q = array();
			$user_lat = (double)Session::get('search_latitude');
			$user_long= (double)Session::get('search_longitude');
			//DB::connection()->enableQueryLog();
            $sql = DB::table('gr_store as a')->select('a.*','gr_category.cate_name',DB::raw('1 as active'),DB::Raw('(select AVG(gr_review.review_rating) from gr_review where gr_review.res_store_id=a.id  and gr_review.review_status="1") as review_rating'));
			
            $sql ->join('gr_featured_booking as b', 'a.id', '=', 'b.store_id')
			->Join('gr_merchant','gr_merchant.id','=','a.st_mer_id')
			->Join('gr_category','gr_category.cate_id','=','a.st_category')
			->where('gr_merchant.mer_status','=','1')
			->where('gr_category.cate_status','=','1')
			->where('a.st_status','=',1)
			->where('b.from_date','<=',date('Y-m-d'))->where('b.to_date','>=',date('Y-m-d'))
			->where('admin_approved_status',1)
			->where('a.st_type','=',1)
			->where('a.st_status',1);
			if($user_lat != '' && $user_long != '')
			{		 
				$q=$sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',a.st_latitude,a.st_longitude)) <= a.st_delivery_radius');
			}
			
			$q=$sql->groupby('a.id');
			$q = $sql->orderby('b.total_price','desc')->limit(4)->get();
			return $q;
			// $query = DB::getQueryLog();
			// print_r($query);
			// exit;
		}
		
		
		
	}
