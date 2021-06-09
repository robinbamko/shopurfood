<?php
	
    namespace App;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Tymon\JWTAuth\Contracts\JWTSubject;
    use  Illuminate\Database\Eloquent\Model;
    use DB;
    class User extends Authenticatable implements JWTSubject
    {
        use Notifiable;
        protected $table = 'gr_customer';
        protected $primaryKey = 'cus_id';
        /**
			* The attributes that are mass assignable.
			*
			* @var array
		*/
        protected $fillable = [ 'cus_fname', 'cus_email', 'cus_password', 'cus_phone1', 'cus_login_type', 'cus_status', 'cus_paynamics_status', 'cus_paymaya_status', 'cus_netbank_status', 'cus_created_date','cus_referedBy','cus_ios_fcm_id','cus_andr_fcm_id','cus_decrypt_password'];
		
        /**
            * The attributes that should be hidden for arrays.
			*
			* @var array
		*/
        protected $hidden = [
		'cus_password', 'remember_token',
        ];
        public function getAuthPassword()
        {
            return $this->cus_password;
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
            return 'cus_id';
		}
		
        /* get delivery boy details */
        public static function get_delboy_details($order_id,$store_id)
        {
            return DB::table('gr_order')->select(DB::Raw("(SELECT CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname))) AS del_name"),'deliver_phone1','deliver_profile_image','deliver_latitude','deliver_longitude','ord_delivery_memid')
			->Join('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_order.ord_delivery_memid')
			->where(['ord_transaction_id' => $order_id,'ord_rest_id'=>$store_id,'ord_delboy_act_status' => '1'])
			->first();
		}
		
        /* get customer rating status */
        public static function get_rating_status($order_id)
        {
            return DB::table('gr_order')->select('dm_customer_rating','ord_status')->leftjoin('gr_delivery_manager','gr_delivery_manager.dm_id','=','gr_order.ord_delmgr_id')->where('ord_id','=',$order_id)->first();
		}
	}	