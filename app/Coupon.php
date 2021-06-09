<?php
	
	namespace App;
	
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class Coupon extends Model
	{
		public static function user_lists()
		{
			return DB::table('gr_customer')->where('cus_status','=','1')->pluck('cus_email', 'cus_id');
		}
	}
