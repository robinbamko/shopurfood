<?php
	
    namespace App;
	
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Tymon\JWTAuth\Contracts\JWTSubject;
    use  Illuminate\Database\Eloquent\Model;
    use DB;
    class Agent extends Authenticatable implements JWTSubject
    {
        use Notifiable;
        protected $table = 'gr_agent';
        protected $primaryKey = 'agent_id';
        /**
			* The attributes that are mass assignable.
			*
			* @var array
		*/
        protected $fillable = [ 'agent_fname', 'agent_lname', 'agent_password', 'agent_phone1', 'agent_status', 'mer_paynamics_status', 'mer_paymaya_status', 'mer_netbank_status', 'agent_created_at','agent_email'];
		
        /**
            * The attributes that should be hidden for arrays.
			*
			* @var array
		*/
        protected $hidden = [
		'agent_password', 'remember_token',
        ];
        public function getAuthPassword()
        {
            return $this->agent_password;
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
            return 'agent_id';
		}
        /* get orders count */
        public static function get_orders_count($agent_id,$where = '',$status = '',$wh_array = '')
        {   
            DB::connection()->enableQueryLog();
            $q = '';
			$sql = DB::table('gr_order')->select('ord_id')
			->where('ord_task_status','=','1')
			->where('ord_agent_id','=',$agent_id)
			->where('ord_self_pickup','!=','1')
			->groupBy('ord_rest_id','ord_transaction_id');
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
                $q = $sql->where('ord_agent_acpt_status','=',$where);
			}
            $q = $sql->get();
			//$query =  DB::getQueryLog();
			//return $query;
            return count($q);   
		}
		
        /*  get individual store order amount */
        public static function individual_st_amt($order_id,$store_amount)
        {
            $totalOrderAmount = DB::table('gr_order')->select(DB::raw('SUM(gr_order.ord_grant_total) As total_order_amount'),'gr_order.ord_wallet','gr_order.ord_delivery_fee')
			->where('ord_self_pickup','!=','1')
			->where('ord_cancel_status','=','0')
			->where('ord_transaction_id','=',$order_id)
			->groupBy('ord_transaction_id')
			->first();
            if(empty($totalOrderAmount)===true)
            {
                return '0~0';
			}
            else
            {
                $orderAmount_cal = $totalOrderAmount->total_order_amount;
                $orderWallet_cal = $totalOrderAmount->ord_wallet;
                $orderDeliveryFee_cal = $totalOrderAmount->ord_delivery_fee;
                if($orderDeliveryFee_cal > 0 ) 
                {
                    $delFee = $store_amount * ($orderDeliveryFee_cal/$orderAmount_cal); 
				} 
                else 
                { 
                    $delFee= 0; 
				}
                if($orderWallet_cal > 0 ) 
                { 
                    $walletFee = $store_amount * ($orderWallet_cal/$orderAmount_cal); 
				} 
                else 
                { 
                    $walletFee = 0; 
				} 
                return $delFee.'~'.$walletFee;
			}
		}
		
        /*  get delivery manager and store details */
        public static function get_dm_details($orderId,$storeId,$lang)
        {
            $st_name = ($lang == 'en') ? 'st_store_name' : 'st_store_name_'.$lang;
            return DB::table('gr_order')->select('ord_delmgr_id','gr_store.'.$st_name.' as s_name')->leftjoin('gr_store','gr_order.ord_rest_id','=','gr_store.id')->where('ord_rest_id','=',$storeId)->where('ord_transaction_id','=',$orderId)->first();
		}
		
        /* earning report */
        public static function earning_report($agent_id,$from_date = '',$to_date = '')
        {
            $sql = DB::table('gr_agent_earnings')->select('ae_updated_at','ae_ord_currency','ae_ord_status','ae_total_amount','ae_transaction_id','ae_order_total')->where('ae_agent_id','=',$agent_id);
            if($from_date!='')
            {
                /*$q = $sql->whereRaw('DATE(ae_updated_at) >= "'.$from_date.'"');*/
                $q = $sql->whereRaw('DATE(ae_updated_at) >= ?', [$from_date]);
			}
            if($to_date!='')
            {
                /*$q = $sql->whereRaw('DATE(ae_updated_at) <= "'.$to_date.'"');*/
                $q = $sql->whereRaw('DATE(ae_updated_at) <= ?', [$to_date]);
			}
            $q = $sql->get();
            return $q;
		}
	}	