<?php
	namespace App;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class Restaurant extends Model
	{
		use Notifiable;
		protected $table = 'gr_store';
		
		/** get all details **/
		public static function get_all_details($type)
		{
			return DB::table('gr_store')->select('gr_store.id','gr_merchant.mer_fname','gr_category.cate_name','st_store_name','st_status','st_logo','gr_store.added_by')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->where('gr_merchant.mer_status','!=','2')
			->where('gr_category.cate_status','!=','2')
			->where('gr_store.st_status','!=',2)
			->where('gr_store.st_type','=',$type)
			->orderBy('gr_store.id','desc')
			->paginate(10);
		}
		
		/** get specific details **/
		public static function get_details($id)
		{
			return DB::table('gr_store as st')->select('*')
			->where('st.st_status','!=',2)
			->where('st.id','=',$id)
			->first();
		}
	}	