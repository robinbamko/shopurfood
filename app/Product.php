<?php
	namespace App;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class Product extends Model
	{
		use Notifiable;
		protected $table = 'gr_product';
		
		/** get all details **/
		public static function get_all_details($type)
		{
			return 
			DB::table('gr_product')->select('gr_product.pro_id','gr_product.pro_store_id','gr_product.pro_status','gr_store.st_store_name','gr_product.pro_item_name','gr_product.pro_original_price','gr_product.pro_discount_price','gr_product.pro_images','gr_product.added_by','gr_merchant.mer_email')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_store.st_status','<>','2')
			->where('gr_product.pro_status','<>','2')
			->where('gr_product.pro_type','=',$type)
			->orderBy('gr_product.pro_id','desc')
			->paginate(10);
			
		}
		
		public static function get_all_details_search($type,$pdt_status)
		{
			$sql =  
			DB::table('gr_product')->select('gr_product.pro_id','gr_product.pro_store_id','gr_product.pro_status','gr_store.st_store_name','gr_product.pro_item_code','gr_product.pro_item_name','gr_product.pro_original_price','gr_product.pro_discount_price','gr_product.pro_images','gr_product.added_by','gr_merchant.mer_email',DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Avail','Sold') as availablity"))
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_store.st_status','<>','2')
			->where('gr_product.pro_status','<>','2')
			->where('gr_product.pro_type','=',$type);
			if($pdt_status == '1')
			{ 
				$q = $sql->whereRaw('gr_product.pro_quantity > gr_product.pro_no_of_purchase');
			}
			if($pdt_status == '2')
			{ 
				$q = $sql->whereRaw('gr_product.pro_no_of_purchase >= gr_product.pro_quantity');
			}	
			$q=$sql->orderBy('gr_product.pro_id','desc');
			$q=$sql->paginate(10);
			return $q;
			
		}
		
		public static function get_merchant_all_details($type,$pdt_status)
		{
			$store_id = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
			$storeidIs = $store_id->id;
			//DB::connection()->enableQueryLog();
			$sql =   DB::table('gr_product')->select('gr_product.pro_id','gr_product.pro_store_id','gr_product.pro_status','gr_store.st_store_name','gr_product.pro_item_code','gr_product.pro_item_name','gr_product.pro_original_price','gr_product.pro_discount_price','gr_product.pro_images','gr_product.added_by',DB::Raw(" IF(((gr_product.pro_quantity- gr_product.pro_no_of_purchase) > 0),'Avail','Sold') as availablity"))
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where('gr_store.st_status','<>','2')
			->where('gr_product.pro_status','<>','2')
			->where('gr_product.pro_type','=',$type)
			->where('gr_product.pro_store_id','=',$storeidIs);
			if($pdt_status == '1')
			{ 
				$q = $sql->whereRaw('gr_product.pro_quantity > gr_product.pro_no_of_purchase');
			}
			if($pdt_status == '2')
			{ 
				$q = $sql->whereRaw('gr_product.pro_no_of_purchase >= gr_product.pro_quantity');
			}	
			$q=$sql->orderBy('gr_product.pro_id','desc');
			$q=$sql->paginate(10);
			return $q;
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
		/** get specific details **/
		public static function get_details($type)
		{
			return DB::table('gr_product')->select(
			'gr_product.pro_id',
			'gr_product.pro_item_code',
			'gr_product.pro_item_code',
			'gr_product.pro_item_name',
			'gr_product.pro_per_product',
			'gr_product.pro_quantity',
			'gr_product.pro_currency',
			'gr_product.pro_original_price',
			'gr_product.pro_discount_price',
			'gr_proitem_maincategory.pro_mc_name',
			'gr_proitem_subcategory.pro_sc_name',
			'gr_store.st_store_name'
			)
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where('gr_product.pro_status','!=','2')
			->where('gr_product.pro_type','=',$type)
			->get();
		}
		
		public static function get_details_byMerchant($type)
		{
			$store_id = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
			$storeidIs = $store_id->id;
			return DB::table('gr_product')->select(
			'gr_product.pro_id',
			'gr_product.pro_item_code',
			'gr_product.pro_item_code',
			'gr_product.pro_item_name',
			'gr_product.pro_per_product',
			'gr_product.pro_quantity',
			'gr_product.pro_currency',
			'gr_product.pro_original_price',
			'gr_product.pro_discount_price',
			'gr_proitem_maincategory.pro_mc_name',
			'gr_proitem_subcategory.pro_sc_name',
			'gr_store.st_store_name'
			)
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->Join('gr_store','gr_store.id','=','gr_product.pro_store_id')
			->where('gr_product.pro_status','!=','2')
			->where('gr_product.pro_type','=',$type)
			->where('gr_product.pro_store_id','=',$storeidIs)
			->get();
		}
		public static function get_activestore_withmerch($type)
		{
			//DB::connection()->enableQueryLog();
			return DB::table('gr_store')->select('gr_store.st_store_name','gr_merchant.mer_email','gr_store.id')
			->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
			->where('gr_store.st_status','<>','2')
			->where('gr_merchant.mer_status','<>','2')
			->where('gr_store.st_type','=',$type)
			->get();
			$query = DB::getQueryLog();
			//print_r($query);
			//exit;
		}
	}	