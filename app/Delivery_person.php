<?php
	
    namespace App;
	
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Tymon\JWTAuth\Contracts\JWTSubject;
    use  Illuminate\Database\Eloquent\Model;
    use DB;
    class Delivery_person extends Authenticatable implements JWTSubject
    {
        use Notifiable;
        protected $table = 'gr_delivery_member';
        protected $primaryKey = 'deliver_id';
        /**
			* The attributes that are mass assignable. 
			*
			* @var array
		*/
        protected $fillable = [ 'deliver_fname', 'deliver_lname', 'deliver_password', 'deliver_phone1', 'deliver_email', 'deliver_phone2','deliver_profile_image','deliver_licence','deliver_address_proof','deliver_response_time','deliver_andr_fcm_id','deliver_ios_fcm_id','deliver_status'];
		
        /** 
            * The attributes that should be hidden for arrays.
			*
			* @var array
		*/
        protected $hidden = [
		'deliver_password', 'remember_token',
        ];
        public function getAuthPassword()
        {
            return $this->deliver_password;
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
            return 'deliver_id';
		}
		
        public static function get_order_details($lang,$admin_default_lang,$agent_id,$deliver_id,$status,$page_no,$from = '',$to =  '',$search_id = '',$where = '',$orderby_column = '',$agent_module = '')
        {
            $st_store_name = ($lang == $admin_default_lang) ? 'st_store_name' : 'st_store_name_'.$lang;
            $q = array();
			
            DB::connection()->enableQueryLog();
			
            $sql =   DB::table('gr_order')
			->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_transaction_id',
			'gr_order.ord_cus_id',
			'gr_order.ord_pay_type',
			'gr_order.ord_status',
			'gr_order.ord_delmgr_id',
			DB::raw('SUM(gr_order.ord_grant_total) As order_amount'),
			'gr_order.ord_currency',
			'gr_store.id as storeId',
			'gr_store.'.$st_store_name.' as storeName',
			'gr_store.st_address',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			'gr_store.st_type',
			'gr_store.st_logo',
			'gr_store.st_delivery_time',
			'gr_store.st_delivery_duration',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_delivered_on',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname'
			)
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
			->where('ord_task_status','=','1')
			->where('ord_delivery_memid','=',$deliver_id)
			->where('ord_delboy_act_status','=',$status)  
			->where('ord_status','>=','4')
			->where('ord_self_pickup','!=','1')
			->where('ord_cancel_status','=','0')
			->groupBy('ord_rest_id','ord_transaction_id');
            if($agent_module == 1)
            {
                $q = $sql->where('ord_agent_id','=',$agent_id)->where('ord_agent_acpt_status','=','1');
			}
            if($where != '')
            {
				$q = $sql->whereIn('ord_status',$where);
			}
            if($from != '' && $to != '')
            {   //echo $from; echo $to; exit;
				$q = $sql->whereBetween('ord_date', array($from, $to));
			}
            if($search_id != '')
            {   //echo $search_id; exit;
                $q = $sql->where('ord_transaction_id','LIKE','%'.$search_id.'%');
			}
            if($orderby_column != '')
            {
                $q = $sql->orderBy($orderby_column,'desc');
			}
			
            $q = $sql->paginate(10,['*'],'order_management',$page_no);
            return $q;
			//$query = DB::getQueryLog();
			//print_r($query);exit;
		}
		
        /* get orders count */
        public static function get_orders_count($agent_id,$deliver_id,$where = '',$status = '',$wh_array = '',$agent_module='')
        {   
            $q = '';
            //DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('ord_id')
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->leftJoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
			->where('ord_task_status','=','1')
			->where('ord_delivery_memid','=',$deliver_id)
			->where('ord_status','>=','4')
			->where('ord_cancel_status','!=','1')
			->where('ord_self_pickup','!=','1')
			->groupBy('ord_rest_id','ord_transaction_id');
            if($agent_module == 1)
            {
                $q = $sql->where('ord_agent_id','=',$agent_id)->where('ord_agent_acpt_status','=','1');
				
			}
            if($status != '')
            {
                $q = $sql->where('ord_status','=',$status);
			}
            if($wh_array != '')
            {
                $q = $sql->whereIn('ord_status',$wh_array);
			}
            if($where != '')
            {
                $q = $sql->where('ord_delboy_act_status','=',$where);
			}
            $q = $sql->where('ord_delboy_act_status','!=',2)->where('ord_status','!=','9')->get();
            //$query = DB::getQueryLog();
            //print_r($query);
            //exit;
            return count($q);   
		}
		
        public static function get_otp_status($order_id,$deliver_id,$store_id)
        {
            return DB::table('gr_order')->select('ord_otp',
			DB::Raw('SUM(Case When gr_order.ord_pay_type="COD" Then gr_order.ord_grant_total Else 0 End) AS order_amount'),
			//DB::raw('SUM(gr_order.ord_grant_total) As order_amount'),
			'mer_email',
			'ord_delmgr_id',
			'ord_merchant_id',
			'ord_currency',
			'ord_pay_type'
			)
			->leftJoin('gr_merchant','gr_order.ord_merchant_id','=','gr_merchant.id')
			->where(['ord_transaction_id' => $order_id,'ord_rest_id' => $store_id,'ord_delivery_memid' => $deliver_id,'ord_cancel_status' => '0'])
			->first();
		}
		
        public static function get_ord_details($order_trans_id,$store_id,$deliver_id,$lang)
        {
            $name   = ($lang == 'en') ? 'pro_item_name' : 'pro_item_name_'.$lang;
            $st_name = ($lang == 'en') ? 'st_store_name' : 'st_store_name_'.$lang;
            return DB::table('gr_order')->select('gr_product.'.$name.' as productName',
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
			'gr_product.pro_images','gr_product.pro_type',
			'gr_merchant.mer_email',
			'gr_store.'.$st_name.' as store_name',
			'gr_store.st_latitude',
			'gr_store.st_longitude'
			)
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->join('gr_merchant','gr_order.ord_merchant_id','=','gr_merchant.id')
			->join('gr_store','gr_order.ord_rest_id','=','gr_store.id')
			->where(['gr_order.ord_transaction_id'=>$order_trans_id,'gr_order.ord_rest_id' => $store_id,'ord_delivery_memid' => $deliver_id,'ord_cancel_status' => '0'])
			->get();
		}
		
        /* get customer details */
        public static function get_customer_details($order_id)
        {
            return DB::table('gr_order')->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.ord_transaction_id',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cus_id',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude'
			)
			->where('gr_order.ord_transaction_id','=',$order_id)
			->groupBy('gr_order.ord_cus_id')
			->first();
		}
		
        /* get invoice */
        public static function get_invoice($order_id,$store_id,$deliver_id,$lang)
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
			'gr_order.order_ship_mail',
			'gr_store.'.$st_name.' as st_store_name',
			'gr_product.'.$name.' as pro_item_name',
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
			'gr_order.ord_status',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_customer.cus_address',
			'gr_customer.cus_phone1',
			'gr_customer.cus_email',
			'gr_order.ord_wallet',
			'gr_order.ord_self_pickup',
			'gr_order.ord_spl_req',
			DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),
			/*DB::raw('(SELECT SUM(ord_grant_total) FROM gr_order WHERE ord_transaction_id="'.$order_id.'" AND ord_delivery_memid="'.$deliver_id.'") As order_amount')*/DB::raw('(SELECT SUM(ord_grant_total) FROM gr_order WHERE ord_transaction_id=? AND ord_delivery_memid=?) As order_amount', [$order_id,$deliver_id]),
			'gr_delivery_manager.dm_cust_data_protect',
			'gr_store.st_address'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->leftJoin('gr_delivery_manager','gr_delivery_manager.dm_id','=','gr_order.ord_delmgr_id')
			->where('gr_order.ord_transaction_id','=',$order_id)
			->where('gr_order.ord_delivery_memid','=',$deliver_id)
			->get();
            
		}
		
        /* get basic details */
        public static function get_basic_details($table,$where,$select)
        {
            return DB::table($table)->select($select)->where($where)->first();
		}
		
        /* calculate delivery fee and wallet for individual store */
        public static function get_receivable_amount($order_id,$store_or_amt)
        {
            $totalOrderAmount = DB::table('gr_order')->select(DB::raw('SUM(gr_order.ord_grant_total) As total_order_amount'),
			'gr_order.ord_wallet',
			'gr_order.ord_delivery_fee')
			->where('ord_self_pickup','!=','1')
			->where('ord_cancel_status','=','0')
			->where('ord_transaction_id','=',$order_id)
			->groupBy('ord_transaction_id')
			->first();
			if(empty($totalOrderAmount)===true){
				//$orderAmount_cal = $orderWallet_cal = $walletFee = $delFee = $orderDeliveryFee_cal = 0;
				//return 0.'~'.0;
				return '0~0';
			}
			else{   
				$orderAmount_cal = $totalOrderAmount->total_order_amount;
				$orderWallet_cal = $totalOrderAmount->ord_wallet;
				$orderDeliveryFee_cal = $totalOrderAmount->ord_delivery_fee;
				if($orderDeliveryFee_cal > 0 ) 
				{ 
					$delFee = $store_or_amt * ($orderDeliveryFee_cal/$orderAmount_cal); 
				}
				else 
				{ 
					$delFee= 0; 
				}
				if($orderWallet_cal > 0 ) 
				{ 
					$walletFee = $store_or_amt * ($orderWallet_cal/$orderAmount_cal); 
				} 
				else 
				{ 
					$walletFee = 0; 
				} 
				return $delFee.'~'.$walletFee;
			}
		}
		
        /* earning details */
        public static function get_earning_details($id,$from_date = '',$to_date='')
        {
            $q = array();
            $sql =DB::table('gr_delivery_person_earnings')->select('de_updated_at','de_ord_currency','de_ord_status','de_total_amount','de_transaction_id','de_order_total')
			->where(['de_deliver_id' => $id]);
            if($from_date != '')
            {
                $q = $sql->whereDate('de_updated_at','>=',$from_date);
			}
            if($to_date != '')
            {
                $q = $sql->whereDate('de_updated_at','<=',$to_date);   
			}
            $q = $sql->get();
            return $q;
		}
		
        /* get related details */
        public static function get_related_details($order_id,$store_id)
        {
            return DB::table('gr_order')->select('ord_merchant_id','ord_delmgr_id','ord_cus_id','cus_andr_fcm_id','cus_ios_fcm_id')
			->leftJoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
			->where(['ord_transaction_id' => $order_id,'ord_rest_id' => $store_id])
			->first();
		}
	}	