<?php
	
	namespace App;
	
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
    use DateTime;
    use DateInterval;
    use DatePeriod;
	
	class Admin extends Model
	{
		use Notifiable;
		
		/**
			* The attributes that are mass assignable.
			*
			* @var array
		*/
		protected $table = 'gr_admin';
		
		public static function get_admin_details()
		{
			return DB::table('gr_admin')->get();
		}
		
		public static function get_subadmin_details()
		{
			return DB::table('gr_subadmin')->get();
		}
		
		public static function get_subadmin_det()
		{
			return DB::table('gr_subadmin')->first();
		}
		
		public static function get_admin_det()
		{
			return DB::table('gr_admin')->first();
		}
		
		public static function country_lists()
		{
			return DB::table('gr_country')->where('co_status','=','1')->pluck('co_name', 'id');
		}
		
		
		public static function get_all_country_based_city($country_id)
		{
			return DB::table('gr_city')->where('co_id','=',$country_id)->where('ci_status','=','1')->get();
		}
		
		public static function update_profile($adminid,$insertArr)
		{
			return DB::table('gr_admin')->where('id',$adminid)->update($insertArr);
		}
		
		public static function subadmin_update_profile($adminid,$insertArr)
		{
			return DB::table('gr_subadmin')->where('id',$adminid)->update($insertArr);
		}
		
		public static function insert_profile($insertArr)
		{
			return DB::table('gr_admin')->insert($insertArr);
		}
		
		public static function subadmin_insert_profile($insertArr)
		{
			return DB::table('gr_subadmin')->insert($insertArr);
		}
		
		public static function update_password($adminid,$insertArr)
		{
			return DB::table('gr_admin')->where('id',$adminid)->update($insertArr);
		}
		
		public static function update_subadmin_password($adminid,$insertArr)
		{
			return DB::table('gr_subadmin')->where('id',$adminid)->update($insertArr);
		}
		
		public static function get_categoryLists($cate_type)
		{
			return DB::table('gr_category')->where('cate_type',$cate_type)->where('cate_status','1')->get();
		}
		public static function get_categoryLists_count($table,$where,$wh_status)
		{
			
			//DB::connection()->enableQueryLog();
			return DB::table($table)->where($where)->where($wh_status,'<>','2')->count();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
        public static function get_count($table,$where_column,$select = '')
        {
            $q = array();
            $sql =  DB::table($table)->where($where_column ,'!=' ,'2');
            if($select != '')
            {
                $q  = $sql->addSelect($select);
			}
            $q = $sql->get()->count();
            return $q;
		}
		/** store ,restaurant count **/
        public static function get_count1($table,$where_column,$where,$select = '')
        {
            $q = array();
            $sql =  DB::table($table)->where($where_column ,'!=' ,'2');
            if($select != '')
            {
                $q  = $sql->addSelect($select);
			}
            $q = $sql->where($where)->get()->count();
            return $q;
		}
		
		/** delivered order count **/
        public static function get_count2($table,$where,$select = '')
        {
            $q = array();
            $sql = DB::table($table)->where($where);
            if($select != '')
            {
                $q  = $sql->addSelect($select);
			}
            $q = $sql->get()->count();
            return $q;
		}
        public static function get_activestore_count()
        {
            //DB::connection()->enableQueryLog();
            return DB::table('gr_store')->select('gr_store.id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_category.cate_status','!=','2')
			->where('gr_store.st_status','<>','2')
			->where('gr_store.st_type','=','2')
			->get()
			->count();
            $query = DB::getQueryLog();
            //print_r($query);
            //exit;
		}
		
        public static function get_activerest_count()
        {
            //DB::connection()->enableQueryLog();
            return DB::table('gr_store')->select('gr_store.id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_category.cate_status','!=','2')
			->where('gr_store.st_status','<>','2')
			->where('gr_store.st_type','=','1')
			->get()
			->count();
            $query = DB::getQueryLog();
            //print_r($query);
            //exit;
		}
        public static function get_activestore_count1($status)
        {
            //DB::connection()->enableQueryLog();
            return DB::table('gr_store')->select('gr_store.id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_category.cate_status','!=','2')
			->where('gr_store.st_status','<>','2')
			->where('gr_store.st_status','=',$status)
			->where('gr_store.st_type','=','2')
			->get()
			->count();
            $query = DB::getQueryLog();
            //print_r($query);
            //exit;
		}
		
        public static function get_activerest_count1($status)
        {
            //DB::connection()->enableQueryLog();
            return DB::table('gr_store')->select('gr_store.id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_category.cate_status','!=','2')
			->where('gr_store.st_status','=',$status)
			->where('gr_store.st_status','<>','2')
			->where('gr_store.st_type','=','1')
			->get()
			->count();
            $query = DB::getQueryLog();
            //print_r($query);
            //exit;
		}
		
		/** get commission details **/
		public static function get_commission_list()
		{	
			$name = (Session::get('admin_lang_code') == "en" ) ? 'st_store_name' : 'st_store_name_'.Session::get('admin_lang_code') ;
			return DB::table('c')->select('or_admin_amt','or_coupon_amt','or_cancel_amt','or_mer_amt','mer_fname','mer_email','gr_store.'.$name.' as st_name','or_mer_id','gr_merchant.mer_currency_code')
			->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id')
			->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_merchant.mer_status','<>','2')
			->paginate(10);
		}
		public static function get_cancelled_order($paid_status='')
		{
			//$name = (Session::get('admin_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('admin_lang_code');
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')
			->select(
			'gr_order.ord_id',
			'gr_customer.cus_email',
			'gr_order.ord_transaction_id',
			'gr_product.pro_item_name',
			'gr_order.ord_currency',
			DB::Raw('SUM(gr_order.ord_grant_total) AS ord_grant_total'),
			DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS ord_cancel_paidamt'),
			DB::Raw('SUM(gr_order.ord_cancel_amt) AS tot_cancel_withCommission'),
			DB::Raw('SUM(gr_order.ord_admin_amt) AS tot_admin_Commission'),
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_pay_type',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_merchant_id',
			//DB::Raw('gr_merchant_overallorder.or_cancel_amt AS tot_cancel_withCommission'),
			'gr_order.add_delfee_status',
			'gr_order.ord_status'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_merchant_overallorder','gr_merchant_overallorder.or_mer_id', '=', 'gr_order.ord_merchant_id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_cancel_status','=','1');
			if($paid_status == 'Paid') {
				$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)<=0');
			}
			elseif($paid_status == 'Unpaid') {
				$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)>0');
			}
			$sql->groupBy('gr_order.ord_transaction_id');
			$result = $sql->paginate(10);///->get();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			return $result;
		}
		
		
		public static function get_productRitem_count($status,$type)
		{
			$sql =  DB::table('gr_product')->select('gr_product.pro_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_store.st_status','<>','2')
			->where('gr_product.pro_status','=',$status)
			->where('gr_product.pro_type','=',$type);
            $totalData=$sql->count();
			
			return $totalData;
			
		}
		
		
        /* get failed orders */
        public static function get_failed_orders($email = '',$ord_id = '',$store = '',$order = '',$dir = '',$start = '',$limit ='' )
        {
            $q = array();
			
			//            $sql = DB::table('gr_order')->select('st_store_name','ord_transaction_id','gr_agent.agent_fname','gr_agent.agent_lname','gr_delivery_member.deliver_fname','gr_delivery_member.deliver_lname','ord_failed_reason','ord_delivery_fee',DB::Raw('SUM(gr_order.ord_grant_total) AS order_total'),'gr_customer.cus_email','gr_order.ord_currency','gr_customer.cus_paymaya_clientid','gr_customer.cus_paymaya_secretid','gr_customer.cus_id','gr_order.ord_id','gr_customer.cus_bank_accno','gr_customer.cus_bank_name','gr_customer.cus_branch','gr_customer.cus_ifsc','gr_customer.cus_paymaya_status','gr_customer.cus_netbank_status',DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS paid_amt'),'gr_order.ord_rest_id')
            $sql = DB::table('gr_order')->select('st_store_name','ord_transaction_id','gr_agent.agent_fname','gr_agent.agent_lname','gr_delivery_member.deliver_fname','gr_delivery_member.deliver_lname','ord_failed_reason','ord_delivery_fee',DB::Raw('SUM(gr_order.ord_grant_total) AS order_total'),'gr_customer.cus_email','gr_order.ord_currency','gr_customer.cus_paymaya_clientid','gr_customer.cus_paymaya_secretid','gr_customer.cus_id','gr_order.ord_id','gr_customer.cus_bank_accno','gr_customer.cus_bank_name','gr_customer.cus_branch','gr_customer.cus_ifsc','gr_customer.cus_paymaya_status','gr_customer.cus_netbank_status',DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS paid_amt'),'gr_order.ord_rest_id')
			
			->leftJoin('gr_agent','gr_order.ord_agent_id','=','gr_agent.agent_id')
			->leftJoin('gr_store','gr_order.ord_rest_id','=','gr_store.id')
			->leftJoin('gr_delivery_member','gr_order.ord_delivery_memid','gr_delivery_member.deliver_id')
			->leftJoin('gr_customer','gr_order.ord_cus_id','gr_customer.cus_id')
			->where('ord_status','=','9')
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id');
            if($email != '')
            {
                $q = $sql->where('gr_customer.cus_email','=',$email);
			}
            if($ord_id != '')
            {
                $q = $sql->where('gr_order.ord_transaction_id','=',$ord_id);
			}
            if($store != '')
            {
                $q = $sql->where('gr_store.st_store_name','=',$store);
			}
            if($order != '' && $dir != '' && $start != '' && $limit != '')
            {
                $q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
			}
            $q = $sql->get();
            return $q;
		}
		
        /* get already approved featured stores count */
        public static function check_approved_count($id,$count)
        {
            $get_date = DB::table('gr_featured_booking')->select('from_date','to_date','st_type')
			->leftJoin('gr_store','gr_store.id','=','gr_featured_booking.store_id')
			->where('gr_store.st_status','=','1')
			->where('gr_featured_booking.id','=',$id)->first();
            //print_r($get_date); exit();
            if(empty($get_date) === false)
            {
                $from = date('Y-m-d',strtotime($get_date->from_date));
                $to   = date('Y-m-d',strtotime($get_date->to_date));
                $BookedArr = array();
                $BookedDates = array();
                $bookingQry = DB::table('gr_featured_booking')->select('from_date','to_date')
				->leftJoin('gr_store','gr_store.id','=','gr_featured_booking.store_id')
				->where('gr_store.st_type','=',$get_date->st_type)
				->where('gr_featured_booking.id','!=',$id)
				->where('admin_approved_status','=','1')
				->Where(function ($query) use($from,$to) {
					$query->whereBetween('gr_featured_booking.from_date',[$from,$to])
					->orWhereBetween('gr_featured_booking.to_date',[$from,$to]);
				})
				->get();
                //print_r($bookingQry); exit;
                //print_r($bookingQry);
                if(count($bookingQry) > 0 )
                {
                    foreach($bookingQry as $bukQry){
                        $dateRanges = self::createDateRange($bukQry->from_date, $bukQry->to_date, $format = "Y-m-d");
                        //array_push($BookedDates,$dateRanges);
                        $BookedDates=array_merge($BookedDates,$dateRanges);
					}
                    //print_r($BookedDates);
					
                    $vals = array_count_values($BookedDates);
                    //print_r($vals);
                    //echo $gs_featured_numstore;
                    foreach($vals as $key=>$val)
                    {
                        /* date in which 12 restaurants are featured */
                        //if($val >= $count){
                        //echo $val;
                        $BookedArr['count'.$key] = $val;
                        //}
						
					}
                    //print_r($BookedArr); exit;
                    $key_max = array_search(max($BookedArr), $BookedArr);
                    //print_r($key_max); exit;
                    return $BookedArr[$key_max] ; exit;
				}
                else
                {
                    return 0; exit;
				}
			}
		}
        public static function get_failed_or_details($trans_id,$st_id)
        {
            return DB::table('gr_order')->select('gr_product.pro_item_name', DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),'ord_grant_total','ord_had_choices','ord_choices','ord_delivery_fee','add_delfee_status','ord_admin_amt','ord_cancel_paidamt','ord_transaction_id','ord_type','ord_currency','order_ship_mail','ord_status','gr_store.st_store_name')
			->leftjoin('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->leftjoin('gr_store','gr_order.ord_rest_id','=','gr_store.id')
			->where('gr_order.ord_transaction_id','=',$trans_id)
			->where('gr_order.ord_rest_id','=',$st_id)
			->where('gr_order.ord_status','=','9')
			->get();
		}
		
		
        public static function createDateRange($startDate, $endDate, $format = "Y-m-d")
        {
            $begin = new DateTime($startDate);
            $end = new DateTime($endDate);
			
            $interval = new DateInterval('P1D'); // 1 Day
            $dateRange = new DatePeriod($begin, $interval, $end);
			
            $range = [];
            foreach ($dateRange as $date) {
                $range[] = $date->format($format);
			}
            $range[] = $endDate;
            return $range;
		}
		
		public static function delivery_boy_map_details($del_boy_name,$us4_lat,$us4_lon,$us4_radius,$del_boy_phone,$del_boy_status='',$del_boy_location ='')
		{
			if($us4_radius=='') { $us4_radius = 1000; }
			DB::connection()->enableQueryLog();
			$sql = DB::table('gr_delivery_member')->select('deliver_fname','deliver_lname','deliver_latitude','deliver_longitude','deliver_location','deliver_phone1','deliver_phone2','deliver_avail_status')->where('deliver_status','=','1');
			//,DB::raw('(SELECT lat_lng_distance('.$us4_lat.','.$us4_lon.',deliver_latitude,deliver_longitude)) AS distance')
			if($del_boy_name != ''){
				//$sql = $sql->where('deliver_fname','LIKE','%'.$del_boy_name.'%')->orwhere('deliver_lname','LIKE','%'.$del_boy_name.'%');
				$sql = $sql->whereRaw("CONCAT(deliver_fname,' ',deliver_lname) like '%".$del_boy_name."%'");
			}
			
			if($del_boy_location!=''){
				//$sql = $sql->where('deliver_location','LIKE','%'.$del_boy_location.'%');
				$sql = $sql->whereRaw('(SELECT lat_lng_distance('.$us4_lat.','.$us4_lon.',deliver_latitude,deliver_longitude)) <= '.$us4_radius.'');
			}
			
			if($del_boy_phone != ''){
				$sql = $sql->where('deliver_phone1','LIKE','%'.$del_boy_phone.'%')->orwhere('deliver_phone2','=','%'.$del_boy_phone.'%');
			}
			
			if($del_boy_status != ''){
				$sql = $sql->where('deliver_avail_status','=',$del_boy_status);
			}
			
			$sql = $sql->get();
			return $sql;	
			// $query = DB::getQueryLog();
			// print_r($query);
			// exit; 
		}
		
        /* get active delivery boy list*/
        public static function get_delboy_active()
        {
            return DB::table('gr_delivery_member')->select('deliver_email',DB::Raw("CONCAT(deliver_fname,' ',deliver_lname) AS name"),'deliver_id')->where('deliver_status','<>','2')->get();
		}
	}
