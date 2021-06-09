<?php
	
	namespace App;
	
	use Illuminate\Database\Eloquent\Model;
	use DB;
	class Settings extends Model
	{
		//
		public static function insert_settings($insertArr)
		{
			return DB::table('gr_general_setting')->insert($insertArr);
		}
		
		public static function update_settings($siteid,$insertArr)
		{
			return DB::table('gr_general_setting')->where('gs_id',$siteid)->update($insertArr);
		}
		
		public static function get_settings_details()
		{
			return DB::table('gr_general_setting')->get();
		}
		
		public static function insert_smtp_settings($insertArr)
		{
			return DB::table('gr_general_setting')->insert($insertArr);
		}
		
		public static function update_smtp_settings($siteid,$insertArr)
		{
			return DB::table('gr_general_setting')->where('gs_id',$siteid)->update($insertArr);
		}
		
		public static function insert_logo_settings($insertArr)
		{
			return DB::table('gr_logo_settings')->insert($insertArr);
		}
		public static function update_logo_settings($logo_id,$insertArr)
		{
			return DB::table('gr_logo_settings')->where('id',$logo_id)->update($insertArr);
		}
		public static function get_logo_settings_details()
		{
			return DB::table('gr_logo_settings')->get();
		}
		
		public static function get_noimage_settings_details()
		{
			return DB::table('gr_no_images')->get();
		}
		
		public static function update_noimage_settings($noimageid,$insertArr)
		{
			return DB::table('gr_no_images')->where('id',$noimageid)->update($insertArr);
		}
		
		public static function insert_noimage_settings($insertArr)
		{
			return DB::table('gr_no_images')->insert($insertArr);
		}
		
		public static function get_payment_settings()
		{
			return DB::table('gr_payment_setting')->get();
		}
		
		public static function update_paynamics_details($ps_id,$insertArr)
		{
			return DB::table('gr_payment_setting')->where('ps_id',$ps_id)->update($insertArr);
		}
		
		public static function insert_paynamics_details($insertArr)
		{
			return DB::table('gr_payment_setting')->insert($insertArr);
		}
		
		public static function update_paymaya_details($ps_id,$insertArr)
		{
			return DB::table('gr_payment_setting')->where('ps_id',$ps_id)->update($insertArr);
		}
		
		public static function insert_paymaya_details($insertArr)
		{
			return DB::table('gr_payment_setting')->insert($insertArr);
		}
		
		public static function check_banner_count($banner_type)
		{
			return DB::table('gr_banner_image')->where('banner_type',$banner_type)->where('banner_status','!=','2')->count();
		}
		
		public static function get_tutorial_status()
		{
			return DB::table('gr_general_setting')->select('video_tutorial_status')->first();
			
		}
		
	}	