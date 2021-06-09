<?php
	namespace App;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use Tymon\JWTAuth\Contracts\JWTSubject;
	use DB;
	use Session;
	
	class Merchant extends Authenticatable implements JWTSubject
	{
		use Notifiable;
		protected $table = 'gr_merchant';
		protected $primaryKey = 'id';
		
		protected $fillable = [ 'mer_fname', 'mer_lname', 'mer_email', 'mer_password', 'mer_decrypt_password', 'mer_phone', 'mer_paynamics_status', 'mer_paymaya_status', 'mer_netbank_status', 'mer_created_date','mer_status','mer_andr_fcm_id','mer_ios_fcm_id'];
		
		protected $hidden = ['mer_password', 'remember_token',];
		
        public function getAuthPassword()
        {
            return $this->mer_password;
		}
		
        public function getJWTIdentifier()
        {
            return $this->getKey();
		}
        public function getJWTCustomClaims()
        {
            return [];
		}
        public function getAuthIdentifierName(){
            return 'id';
		}
		
		public static function check_oldpwd($mer_id, $oldpwd)
		{
			return DB::table('gr_merchant')->where('id', '=', $mer_id)->where('mer_password', '=', md5($oldpwd))->get();
			
		}
		
		public static function update_newpwd($mer_id, $confirmpwd)
		{
			return DB::table('gr_merchant')->where('id', '=', $mer_id)->update(array('mer_password' => md5($confirmpwd),'mer_decrypt_password' => $confirmpwd));
			
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
			$q = $sql->where($where)->get();
			return $q;
		}
		
		/** delivered order count **/
		public static function get_count2($table,$where,$select = '',$groupBy='')
		{
			$q = array();
			$sql =  DB::table($table)->where($where);
			if($select != '')
			{
				$q  = $sql->addSelect($select);
			}
			if($groupBy != '')
			{
				$q  = $sql->groupBy($groupBy);
			}
			$q = $sql->get()->count();
			return $q;
			
		}
		public static function get_categoryLists_count($table,$where,$wh_status)
		{
			
			//DB::connection()->enableQueryLog();
			return DB::table($table)->where($where)->where($wh_status,'<>','2')->count();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
		
		public static function get_order_details($mer_id,$page,$from_date,$to_date,$search,$where,$orderby,$cancel_status='')
		{	//print_r($where); exit;
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id','gr_customer.cus_fname','gr_customer.cus_lname','gr_customer.cus_image','gr_customer.cus_address','gr_order.ord_date',DB::raw('SUM(gr_order.ord_grant_total) As revenue'),'gr_order.ord_payment_status','gr_order.ord_transaction_id','gr_order.ord_currency','gr_order.ord_self_pickup','gr_order.ord_status','gr_order.ord_reject_reason','gr_order.ord_cancel_status','gr_order.ord_cancel_reason','gr_order.ord_merchant_viewed','gr_order.ord_pre_order_date','gr_order.ord_pay_type','ord_delivered_on','gr_order.ord_shipping_address',DB::Raw('(SELECT GROUP_CONCAT(SUBSTRING_INDEX(gr_product.pro_images,"/**/",1)) FROM gr_product WHERE `pro_id` IN(SELECT nb.ord_pro_id FROM gr_order as nb WHERE nb.ord_transaction_id = `gr_order`.`ord_transaction_id` AND nb.ord_merchant_id = "'.$mer_id.'") GROUP BY pro_type) as item_image'),'gr_order.ord_shipping_cus_name',DB::Raw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) as deliver_name"),'gr_delivery_member.deliver_phone1','ord_delivery_memid','ord_accepted_on','deliver_profile_image','ord_delboy_act_status')
			->groupBy('gr_order.ord_transaction_id')
			->leftJoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
			->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_order.ord_delivery_memid');

			$sql->where('gr_order.ord_merchant_id','=',$mer_id);
			if ($from_date != '')
			{
				$sql->whereDate('gr_order.ord_date', '>=' , $from_date);
			}
			if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to_date);
			}
			if($where != '') 
			{
				$sql->whereIn('gr_order.ord_status',$where);
			}
			if($search != '')
			{
				$sql->where('ord_transaction_id','LIKE','%'.$search.'%');
			}
			if($cancel_status != '')
			{
				$sql->whereIn('gr_order.ord_cancel_status',$cancel_status);
			}
			if($orderby != '')
			{
				$sql->orderBy($orderby, 'desc');
			}
			else
			{
				$sql->orderBy('ord_date', 'desc');
			}
			$sql->where('gr_order.ord_payment_status','=','Success');
			/*$sql->whereExists(function ($query) {
                $query->select(DB::raw("SELECT * FROM some_table WHERE some_col = :somevariable"), array('somevariable' => $someVariable,))
                      ->from('orders')
                      ->whereRaw('orders.user_id = users.id');
            }) */
			$result = $sql->paginate(10,['*'],'order_management',$page);
			/*$query = DB::getQueryLog();
			print_r($query);
			exit;*/
			//print_r($result); exit;
			return $result;
		}
		
		/* get invoice */
        public static function get_invoice($order_id,$store_id,$lang)
        {
            $name   = ($lang == 'en') ? 'pro_item_name' : 'pro_item_name_'.$lang;
            $st_name = ($lang == 'en') ? 'st_store_name' : 'st_store_name_'.$lang;
			//DB::connection()->enableQueryLog();
            return  DB::table('gr_order')
			->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_store.'.$st_name.' as st_store_name',
			'gr_product.'.$name.' as pro_item_name',
			'gr_product.pro_type',
			'gr_order.ord_id',
			'gr_order.ord_pro_id',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_pay_type',
			'gr_order.ord_date',
			'gr_order.ord_cancel_status',
			'gr_order.ord_transaction_id',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_had_choices',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_status',
			'gr_order.ord_currency',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_customer.cus_address',
			'gr_customer.cus_phone1',
			'gr_customer.cus_email',
			'gr_order.ord_wallet',
			'gr_order.ord_self_pickup',
			'gr_order.ord_spl_req',
			'gr_order.order_ship_mail',
			DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),
			DB::raw('(SELECT SUM(ord_grant_total) FROM gr_order WHERE ord_transaction_id="'.$order_id.'" AND ord_rest_id="'.$store_id.'") As order_amount')
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$order_id)
			->where('gr_order.ord_rest_id','=',$store_id)
			->get();
            
		}
		
        /* get commission */
        public static function get_commisssion($mer_id)
        {
            return DB::table('gr_merchant_overallorder')->select('or_admin_amt','or_coupon_amt','or_cancel_amt','or_mer_amt',DB::Raw('SUM(commission_paid) as paid_commission'),'or_total_order','or_reject_amt')           
            ->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id')
            ->leftJoin('gr_merchant_commission','gr_merchant_commission.commission_mer_id','=','gr_merchant_overallorder.or_mer_id')
            ->where('gr_merchant.mer_status','<>','2')
            ->where('or_mer_id','=',$mer_id)
            ->first();
		}
        public static function store_details($mer_id,$lang = '',$def_lang = '')
        {   
            $current_time = date('H:i:s');
            $current_day=date('l');
            $st_name = 'st_store_name';
            if($lang != '' && $def_lang != '')
            {
            	$st_name = ($lang == $def_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
            }
           
			return DB::table('gr_store')->select(DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Available','Closed') as store_closed"),'gr_store.id','old_end_time','old_start_time','gr_store.'.$st_name.' as store_name')
			->leftjoin('gr_res_working_hrs','gr_res_working_hrs.wk_res_id','=','gr_store.id')
			->where('wk_date','=',$current_day)
			->where('st_mer_id','=',$mer_id)->first();
		}

		public static function get_stock_details($mer_id,$lang,$def_lang,$page,$search = '')
		{
			$name 			= ($lang == $def_lang) ? 'pro_item_name' : 'pro_item_name_'.$lang;
			$totalData = DB::table('gr_product')
				->select('gr_product.pro_item_code',
				'gr_product.'.$name.' as pro_name',
				'gr_product.pro_quantity',
				'gr_product.pro_no_of_purchase',
				'gr_product.pro_id',
				DB::Raw('SUBSTRING_INDEX(pro_images,"/**/",1) as pro_image'),
				'pro_status',
				'pro_store_id'
				)
				->leftjoin('gr_store', 'gr_product.pro_store_id', '=', 'gr_store.id')
				->leftjoin('gr_merchant', 'gr_store.st_mer_id', '=', 'gr_merchant.id')
				->where('gr_merchant.id','=',$mer_id)
				//->orderBy('pro_id', 'desc')
				->orderBy(DB::raw('case when pro_status= "1" then 1 when pro_status= "0" then 2 when pro_status= "2" then 3 end'));
				//->orderBy('pro_id', 'desc');
			if($search != '')
			{
				$totalData->where('gr_product.'.$name,'LIKE','%'.$search.'%');
			}
			$q = $totalData->paginate(10,['*'],'pag',$page);
			return $q;
		}

		/* get panding items count */
		public static function pending_item_count($mer_id,$trans_id)
		{
			return DB::table('gr_order')->select(DB::Raw('(select COUNT(ord_id) from gr_order where gr_order.ord_status=1 and gr_order.ord_merchant_id="'.$mer_id.'" and gr_order.ord_transaction_id="'.$trans_id.'") as pending_items'))->first()->pending_items;
		}
	}   
