<?php
	namespace App;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class DeliveryManager extends Model
	{
		use Notifiable;
		protected $table = 'gr_delivery_manager';
		public static function check_oldpwd($mer_id, $oldpwd)
		{
			return DB::table('gr_delivery_manager')->where('dm_id', '=', $mer_id)->where('dm_password', '=', md5($oldpwd))->get();
			
		}
		
		public static function update_newpwd($mer_id, $confirmpwd)
		{
			return DB::table('gr_delivery_manager')->where('dm_id', '=', $mer_id)->update(array('dm_password' => md5($confirmpwd),'dm_real_password'=>$confirmpwd ));
			
		}
		
		/** store ,restaurant count **/
		public static function get_count1($table,$where_column,$where)
		{
			return DB::table($table)->where($where_column ,'!=' ,'2')->where($where)->get();
		}
		
		/** delivered order count **/
		public static function get_count2($table,$where)
		{
			return DB::table($table)->where($where)->get()->count();
		}
		public static function get_commission_list()
		{   
			$name = (Session::get('mer_lang_code') == "en" ) ? 'st_store_name' : 'st_store_name_'.Session::get('admin_lang_code') ;
			return DB::table('gr_merchant_overallorder')->select('or_admin_amt','or_coupon_amt','or_cancel_amt','or_mer_amt','mer_fname','mer_email','gr_store.'.$name.' as st_name','or_mer_id')           
			->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id')
			->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_merchant.mer_status','<>','2')
			->where('or_mer_id','=',Session::get('merchantid'))
			->first();
		}
		
		public static function delivery_boy_map_details($del_boy_name,$us4_lat,$us4_lon,$us4_radius,$del_boy_phone,$del_boy_status)
		{
			if($us4_radius=='') { $us4_radius = 1000; }
			DB::connection()->enableQueryLog();
			$sql = DB::table('gr_delivery_member')->select('deliver_fname','deliver_lname','deliver_latitude','deliver_longitude','deliver_location','deliver_phone1','deliver_phone2','deliver_avail_status')->where('deliver_status','=','1');
			if($del_boy_name != ''){
				/*$sql = $sql->whereRaw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) like '%".$del_boy_name."%'");*/
				$sql = $sql->whereRaw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) like ?", ['%'.$del_boy_name.'%']);
			}
			
			if($us4_lat != '' && $us4_lon!=''){
				/*$sql = $sql->whereRaw('(SELECT lat_lng_distance('.$us4_lat.','.$us4_lon.',deliver_latitude,deliver_longitude)) <= '.$us4_radius.'');*/
				$sql = $sql->whereRaw('(SELECT lat_lng_distance(?,?,deliver_latitude,deliver_longitude)) <= ?', [$us4_lat,$us4_lon,$us4_radius]);
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
		
		
	}
