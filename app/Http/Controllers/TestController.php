<?php 
	
	namespace App\Http\Controllers;
	
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;

	class TestController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			set_time_limit(0);
		}
		public function delete_duplicate(){
			echo time(); exit;
			//$result = DB::statement("SELECT `mer_email`, count(id) as cnt FROM gr_merchant GROUP BY mer_email HAVING cnt > 1");
			$result = DB::table('gr_merchant')
			->select('id', DB::raw('COUNT(id) as products_count'))
			->groupBy('mer_email')
			->having('products_count', '>' , 1)
			->get();
			//echo '<pre>';
			//print_r($result);
			if(count($result) > 0 ){
				foreach($result as $res){
					$mer_id = $res->id;
					$store_qry = DB::table('gr_store')->select('id')->where('st_mer_id',$mer_id)->get();
					if(count($store_qry) > 0 ){
						foreach($store_qry as $stqry){
							DB::connection()->enableQueryLog();
							$del_pdt = DB::table('gr_product')->where('pro_store_id',$stqry->id)->delete();
							$query = DB::getQueryLog();
							echo '<pre>'; print_r($query);
							echo '<hr>';
							DB::connection()->enableQueryLog();
							
							$del_store = DB::table('gr_store')->where('id',$stqry->id)->delete();
							$query = DB::getQueryLog();
							echo '<pre>'; print_r($query);
							echo '<hr>';
						}
					}
					DB::connection()->enableQueryLog();
					$del_store = DB::table('gr_merchant')->where('id',$mer_id)->delete();
					$query = DB::getQueryLog();
					echo '<pre>'; print_r($query);
					echo '<hr>';
				}
			}
		}
		public function add_store_merchant(Request $request)
		{
			/*$a = 25;
			$one = 1; $two = 2; $three = 3; $four = 4; $five = 5; 
			echo ceil($a/$one).'/'.ceil($a/$two).'/'.ceil($a/$three).'/'.ceil($a/$four).'/'.ceil($a/$five);
			exit;*/
			$kount = $request->count;
			$cate_id = $this->category_store_rest(2);
			echo 'Category = '.$cate_id;
			$pdt_count = 1;//($kount)*($kount);
			for($i=1;$i<=$kount;$i++)
			{
				
				DB::statement("INSERT INTO `gr_merchant` (`addedby`, `mer_fname`, `mer_lname`, `mer_email`, `mer_password`, `mer_phone`, `mer_cancel_policy`, `refund_status`, `mer_currency_code`, `mer_paynamics_clientid`, `mer_paynamics_secretid`, `mer_paynamics_mode`, `mer_paynamics_status`, `mer_paymaya_clientid`, `mer_paymaya_secretid`, `mer_paymaya_mode`, `mer_paymaya_status`, `mer_netbank_status`, `mer_bank_accno`, `mer_bank_name`, `mer_branch`, `mer_ifsc`, `mer_commission`, `mer_location`, `mer_city`, `mer_state`, `mer_country`, `mer_latitude`, `mer_longitude`, `mer_serving_radius`, `mer_mininmum_order`, `mer_profile_img`, `mer_status`, `mer_created_date`, `mer_updated_date`, `has_shop`, `mer_business_type`, `mer_total_commission_amount`, `mer_balance_commission_amount`, `mer_admin_paid_amount`, `mer_read_status`) VALUES ('0', 'storeMerchant".$cate_id.'_'.time().$i."', 'lName".$cate_id."', 'storemerchant".$cate_id.'_'.time().$i."@pofitec.com', 'e10adc3949ba59abbe56e057f20f883e', '+1951951951".$cate_id."', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.', 'Yes', '$', '123456', '1564', 'Sandbox', 'Publish', '123456', '3652', 'Sandbox', 'Publish', 'Unpublish', '31809629906', 'SBi', '5689', '458961', '15.00', 'Coimbatore, Tamil Nadu, India', 'Coimbatore', 'Tamil Nadu', 'India', NULL, NULL, '10', '100', NULL, '1', '".date('Y-m-d')."', '".date('Y-m-d')."', '1', '1', '0.00', '0.00', '0.00', '0')");
				$st_mer_id = DB::getPdo()->lastInsertId();
				echo '<br>Merchant  = '.$st_mer_id; 
				$pro_store_id = $this->store_insert('2',$st_mer_id,$cate_id);
				echo '<br>Store  = '.$pro_store_id; 
				for($k=1;$k<=$pdt_count;$k++)
				{
					$pro_category_id = $this->mainCat_insert('2',$cate_id,$k);
					echo '<br>Main Cat  = '.$pro_category_id; 
					$pro_sub_cat_id = $this->subCat_insert($pro_category_id,'2',$cate_id,$k);
					echo '<br>Sub Cat  = '.$pro_sub_cat_id; 
					for($jj=1;$jj<=18;$jj++)
					{
						$item_id =$this->Item_insert($pro_store_id,$pro_category_id,$pro_sub_cat_id,'1',$jj);
						echo '<br>item_id  = '.$item_id; 
					}
				}
			}
			echo "success"; exit;
		}

		public function add_merchant_restaurant(Request $request)
		{
			$kount = $request->count;
			$cate_id = $this->category_store_rest(1);
			echo 'Category = '.$cate_id;
			$pdt_count = 1;//($kount)*($kount);
			DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count+1 where cate_id = '".$cate_id."'");
			for($i=1;$i<=$kount;$i++)
			{
			
				DB::statement("INSERT INTO `gr_merchant` (`addedby`, `mer_fname`, `mer_lname`, `mer_email`, `mer_password`, `mer_phone`, `mer_cancel_policy`, `refund_status`, `mer_currency_code`, `mer_paynamics_clientid`, `mer_paynamics_secretid`, `mer_paynamics_mode`, `mer_paynamics_status`, `mer_paymaya_clientid`, `mer_paymaya_secretid`, `mer_paymaya_mode`, `mer_paymaya_status`, `mer_netbank_status`, `mer_bank_accno`, `mer_bank_name`, `mer_branch`, `mer_ifsc`, `mer_commission`, `mer_location`, `mer_city`, `mer_state`, `mer_country`, `mer_latitude`, `mer_longitude`, `mer_serving_radius`, `mer_mininmum_order`, `mer_profile_img`, `mer_status`, `mer_created_date`, `mer_updated_date`, `has_shop`, `mer_business_type`, `mer_total_commission_amount`, `mer_balance_commission_amount`, `mer_admin_paid_amount`, `mer_read_status`) VALUES ('0', 'RestMerchant".$cate_id."', 'lName".$cate_id."', 'Restmerchant".$cate_id.'_'.time().'_'.$i."@pofitec.com', 'e10adc3949ba59abbe56e057f20f883e', '+1159159159".$cate_id."', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.', 'Yes', '$', '123456', '1564', 'Sandbox', 'Publish', '123456', '3652', 'Sandbox', 'Publish', 'Unpublish', '31809629906', 'SBi', '5689', '458961', '15.00', 'Coimbatore, Tamil Nadu, India', 'Coimbatore', 'Tamil Nadu', 'India', NULL, NULL, NULL, NULL, NULL, '1', '".date('Y-m-d')."', '".date('Y-m-d')."', '1', '2', '0.00', '0.00', '0.00', '0')");
				$st_mer_id = DB::getPdo()->lastInsertId();
				echo '<br>Merchant  = '.$st_mer_id; 
				$pro_store_id = $this->store_insert('1',$st_mer_id,$cate_id);
				echo '<br>Store  = '.$pro_store_id; 
				$this->Working_hours_insert($pro_store_id);
				for($k=1;$k<=$pdt_count;$k++)
				{
					$pro_category_id = $this->mainCat_insert('1',$cate_id,$k);
					echo '<br>Main Cat  = '.$pro_category_id; 
					$pro_sub_cat_id = $this->subCat_insert($pro_category_id,'1',$cate_id,$k);
					echo '<br>Sub Cat  = '.$pro_sub_cat_id; 
					for($jj=1;$jj<=3;$jj++)
					{
						$item_id = $this->Item_insert($pro_store_id,$pro_category_id,$pro_sub_cat_id,'2',$jj);
						
						echo '<br>item_id  = '.$item_id; 
					}
				}
				
				echo '<hr>';
			}
			//echo "success"; exit;
		} 
		public function add_merchant_restaurant1(Request $request)
		{
			$kount = $request->count;
			$cate_id = $this->category_store_rest(1);
			echo 'Category = '.$cate_id;
			$pdt_count = 1;//($kount)*($kount);
			DB::statement("UPDATE gr_category SET cate_store_count = cate_store_count+1 where cate_id = '".$cate_id."'");
			for($i=1;$i<=$kount;$i++)
			{
			
				DB::statement("INSERT INTO `gr_merchant` (`addedby`, `mer_fname`, `mer_lname`, `mer_email`, `mer_password`, `mer_phone`, `mer_cancel_policy`, `refund_status`, `mer_currency_code`, `mer_paynamics_clientid`, `mer_paynamics_secretid`, `mer_paynamics_mode`, `mer_paynamics_status`, `mer_paymaya_clientid`, `mer_paymaya_secretid`, `mer_paymaya_mode`, `mer_paymaya_status`, `mer_netbank_status`, `mer_bank_accno`, `mer_bank_name`, `mer_branch`, `mer_ifsc`, `mer_commission`, `mer_location`, `mer_city`, `mer_state`, `mer_country`, `mer_latitude`, `mer_longitude`, `mer_serving_radius`, `mer_mininmum_order`, `mer_profile_img`, `mer_status`, `mer_created_date`, `mer_updated_date`, `has_shop`, `mer_business_type`, `mer_total_commission_amount`, `mer_balance_commission_amount`, `mer_admin_paid_amount`, `mer_read_status`) VALUES ('0', 'RestMerchant".$cate_id."', 'lName".$cate_id."', 'Restmerchant".$cate_id.'_'.time().'_'.$i."@pofitec.com', 'e10adc3949ba59abbe56e057f20f883e', '+1159159159".$cate_id."', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.', 'Yes', '$', '123456', '1564', 'Sandbox', 'Publish', '123456', '3652', 'Sandbox', 'Publish', 'Unpublish', '31809629906', 'SBi', '5689', '458961', '15.00', 'Coimbatore, Tamil Nadu, India', 'Coimbatore', 'Tamil Nadu', 'India', NULL, NULL, NULL, NULL, NULL, '1', '".date('Y-m-d')."', '".date('Y-m-d')."', '1', '2', '0.00', '0.00', '0.00', '0')");
				$st_mer_id = DB::getPdo()->lastInsertId();
				echo '<br>Merchant  = '.$st_mer_id; 
				$pro_store_id = $this->store_insert('1',$st_mer_id,$cate_id);
				echo '<br>Store  = '.$pro_store_id; 
				$this->Working_hours_insert($pro_store_id);
				for($k=1;$k<=$pdt_count;$k++)
				{
					$pro_category_id = $this->mainCat_insert('1',$cate_id,$k);
					echo '<br>Main Cat  = '.$pro_category_id; 
					$pro_sub_cat_id = $this->subCat_insert($pro_category_id,'1',$cate_id,$k);
					echo '<br>Sub Cat  = '.$pro_sub_cat_id; 
					for($jj=1;$jj<=1000;$jj++)
					{
						$item_id = $this->Item_insert($pro_store_id,$pro_category_id,$pro_sub_cat_id,'2',$jj);
						
						echo '<br>item_id  = '.$item_id; 
					}
				}
				
				echo '<hr>';
			}
			//echo "success"; exit;
		} 
		public function category_store_rest($cate_type)
		{
			DB::statement("INSERT INTO `gr_category` (`cate_name`, `cate_name_ar`, `cate_type`, `cate_status`, `cate_added_by`) VALUES ('cat_".date('YmdHis')."', 'arCat_".date('YmdHis')."', '".$cate_type."', '1', '0')");
			$last_pro_id = DB::getPdo()->lastInsertId();
			return $last_pro_id;
		}
		public function store_insert($st_type,$st_mer_id,$cate_id)
		{
			if($st_type==2) 
			{ 
				$store_name = 'Store_'.$cate_id.'_'.$st_mer_id.'_'.time(); 
				$store_logo = 'store_logo.jpg';
				$store_banner = 'store_banner.jpg/**/';
			} 
			else 
			{ 
				$store_name = 'Restaurant_'.$cate_id.'_'.$st_mer_id.'_'.time(); 
				$store_logo = 'restaurant_logo.jpg';
				$store_banner = 'restaurant_banner.jpg/**/';
			} 
			DB::statement("INSERT INTO `gr_store` (`st_type`, `st_mer_id`, `st_store_name`, `st_store_name_ar`, `st_category`, `st_location`, `st_city`, `st_country`, `st_currency`, `st_minimum_order`, `st_pre_order`, `st_delivery_time`, `st_delivery_duration`, `st_desc`, `st_desc_ar`, `st_address`, `st_latitude`, `st_longitude`, `st_delivery_radius`, `st_logo`, `st_banner`, `st_status`, `st_rating`, `added_by`, `created_at`, `updated_at`) VALUES('".$st_type."', '".$st_mer_id."', '".$store_name."', 'ar".$store_name."', '".$cate_id."', NULL, NULL, NULL, '$', 100, 1, '5', 'hours', 'About content will be display here', 'desc ar', 'Coimbatore, Tamil Nadu 641012, India', '11.0168445', '76.95583209999995', '5', '".$store_logo."', '".$store_banner."', 1, NULL, 0, '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')");
			$pro_store_id = DB::getPdo()->lastInsertId();
			return $pro_store_id;
		}
		public function mainCat_insert($type,$cate_id,$k)
		{
			if($type==2) { $cate_name = 'storeCate_'.$cate_id.'_'.$k.'_'.time(); } else { $cate_name = 'RestCate_'.$cate_id.'_'.$k.'_'.time();  } 
			DB::statement("INSERT INTO `gr_proitem_maincategory` (`pro_mc_name`, `pro_mc_name_ar`, `pro_mc_type`, `pro_mc_status`, `pro_added_by`, `pro_mc_created`) VALUES('".$cate_name."', 'ar_".$cate_name."', '".$type."', 1, 0, '".date('Y-m-d H:i:s')."')");
			$pro_category_id = DB::getPdo()->lastInsertId();
			return $pro_category_id;
		}
		public function subCat_insert($pro_main_id,$type,$cate_id,$k)
		{
			if($type==2) { $cate_name = 'storeSubcate_'.$cate_id.'_'.$k.'_'.time(); } else { $cate_name = 'RestSubcate_'.$cate_id.'_'.$k.'_'.time();  } 
			DB::statement("INSERT INTO `gr_proitem_subcategory` (`pro_sc_name`, `pro_sc_name_ar`, `pro_main_id`, `pro_sc_type`, `pro_sc_status`, `pro_added_by`, `pro_sc_created`) VALUES ('".$cate_name."', 'ar_".$cate_name."', '".$pro_main_id."', '".$type."', 1, 0, '".date('Y-m-d H:i:s')."');");
			$pro_sub_cat_id = DB::getPdo()->lastInsertId();
			return $pro_sub_cat_id;
		}
		public function Item_insert($pro_store_id,$pro_category_id,$pro_sub_cat_id,$type,$jj)
		{
			if($type==1) 
			{ 
				$pdt_name = 'Product_'.$pro_category_id.'_'.$pro_sub_cat_id.'_'.$jj.'_'.time(); 
				$pdt_image = 'product'.$jj.'.jpg/**/';
			} 
			else 
			{ 
				$pdt_name = 'Item_'.$pro_category_id.'_'.$pro_sub_cat_id.'_'.$jj.'_'.time();  
				$pdt_image = 'item.jpg/**/';
			} 
			DB::statement("INSERT INTO `gr_product` (`pro_store_id`, `pro_item_code`, `pro_item_name`, `pro_item_name_ar`, `pro_category_id`, `pro_sub_cat_id`, `pro_original_price`, `pro_per_product`, `pro_per_product_ar`, `pro_has_discount`, `pro_discount_price`, `pro_had_tax`, `pro_tax_name`, `pro_tax_percent`, `pro_desc`, `pro_desc_ar`, `pro_currency`, `pro_type`, `pro_delivery_fee`, `pro_shopper_fee`, `pro_hit_count`, `pro_quantity`, `pro_had_choice`, `pro_had_spec`, `pro_no_of_purchase`, `pro_cancel_option`, `pro_cancel_duration`, `pro_rating`, `pro_meta_keyword`, `pro_meta_keyword_ar`, `pro_meta_desc`, `pro_meta_desc_ar`, `pro_catelogue`, `pro_status`, `pro_update_inventory`, `pro_show_inventory`, `pro_created_date`, `pro_updated_date`, `peo_accept_addon`, `pro_images`, `added_by`, `pro_read_status`) VALUES ('".$pro_store_id."', '".date('YmdHis')."', '".$pdt_name."', 'Arp_".$pdt_name."', '".$pro_category_id."', '".$pro_sub_cat_id."', '10.00', '".$pro_store_id.'_'.$pro_category_id.'_'.$pro_sub_cat_id.'_'.$type.'_'.$jj."', 'ar_".$pro_store_id.'_'.$pro_category_id.'_'.$pro_sub_cat_id.'_'.$type.'_'.$jj."', 'yes', '8.00', 'Yes', 'GST', 2, 'Description".$pro_store_id.'_'.$pro_category_id.'_'.$pro_sub_cat_id.'_'.$type.'_'.$jj."', 'Ar Description".$pro_store_id.'_'.$pro_category_id.'_'.$pro_sub_cat_id.'_'.$type."', '$', '".$type."', '5.00', NULL, NULL, 750, '2', '2', 0, NULL, NULL, NULL, 'Fresh Fruits , Apples & Pears,  Product - 300249', NULL, 'Fresh Fruits , Apples & Pears,  Product - 300249', NULL, NULL, '1', NULL, NULL, '".date('Y-m-d H:i:s')."', NULL, NULL, '".$pdt_image."', 3, 0)");
			$item_id = DB::getPdo()->lastInsertId();
			return $item_id;
		}
		public function Working_hours_insert($res_id)
		{
			DB::statement("INSERT INTO `gr_res_working_hrs` (`wk_res_id`, `wk_date`, `wk_start_time`, `wk_end_time`) VALUES 
						('".$res_id."', 'Sunday', '9:00am', '11:30am'),
						('".$res_id."', 'Monday', '9:00am', '11:30pm'),
						('".$res_id."', 'Tuesday', '9:00am', '11:30pm'),
						('".$res_id."', 'Wednesday', '9:00am', '11:30pm'),
						('".$res_id."', 'Thursday', '9:00am', '11:30pm'),
						('".$res_id."', 'Friday', '9:00am', '11:30pm'),
						('".$res_id."', 'Saturday', '9:00am', '11:30pm')");
		}
	}