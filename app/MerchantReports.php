<?php
	namespace App;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class MerchantReports extends Model
	{
		use Notifiable;
		protected $table = 'gr_product';
		public static function getall_dealreports($from_date, $to_date,$ord_status)
		{ 
			/*select `gr_order`.`ord_id`, `gr_customer`.`cus_fname`, `gr_customer`.`cus_lname`, `gr_order`.`ord_date`, SUM(gr_order.ord_grant_total) As revenue, `gr_order`.`ord_payment_status`, `gr_order`.`ord_transaction_id`, `gr_order`.`ord_task_status`, `gr_order`.`ord_delivery_fee`, `gr_order`.`ord_currency`, `gr_order`.`ord_self_pickup`,gr_order.ord_status,gr_order.ord_merchant_id from `gr_order` left join `gr_customer` on `gr_order`.`ord_cus_id` = `gr_customer`.`cus_id` where `gr_order`.`ord_merchant_id` = '4' and `gr_order`.`ord_status` = '1' and `gr_order`.`ord_payment_status` = 'Success' group by `gr_order`.`ord_transaction_id` order by `ord_date` desc limit 10 offset 0*/
			$from = date("Y-m-d", strtotime($from_date));
			$to   = date("Y-m-d", strtotime($to_date));
			
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id','gr_customer.cus_fname','gr_customer.cus_lname','gr_order.ord_date',DB::raw('SUM(gr_order.ord_grant_total) As revenue'),'gr_order.ord_payment_status','gr_order.ord_transaction_id','gr_order.ord_task_status','gr_order.ord_delivery_fee','gr_order.ord_currency','gr_order.ord_self_pickup','gr_order.ord_status','gr_order.ord_reject_reason','gr_order.ord_cancel_status','gr_order.ord_cancel_reason','gr_order.ord_merchant_viewed','gr_order.ord_pre_order_date')
			->groupBy('gr_order.ord_transaction_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id');
			$sql->where('gr_order.ord_merchant_id','=',Session::get('merchantid'));
			if ($from_date != '')
			{
				$sql->whereDate('gr_order.ord_date', '>=' , $from);
			}
			if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to);
			}
			if($ord_status != '') {
				if($ord_status <= 8)
				{
					$sql->where('gr_order.ord_status', '=' , $ord_status);
				}
				else
				{
					$sql->where('gr_order.ord_cancel_status', '=' , '1')->where('gr_order.ord_status', '!=' , '3');
				}
			}
			$sql->where('gr_order.ord_payment_status','=','Success');
			$result = $sql->paginate(10);
			/*
				$query = DB::getQueryLog();
				print_r($query);
				
			exit;*/
			return $result;
		}
		public static function track_reports($id)
		{
			$name = (Session::get('mer_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('mer_lang_code');
			$results = array();
			$store_det = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_rest_id',
			'gr_store.st_store_name',
			'gr_store.st_logo',
			'gr_order.ord_transaction_id',
			'gr_order.ord_date',
			'gr_order.ord_agent_id',
			'gr_order.ord_delivery_memid',
			'gr_order.ord_status',
			'gr_agent.agent_fname',
			'gr_agent.agent_lname',
			'gr_agent.agent_email',
			'gr_agent.agent_phone1',
			'gr_delivery_member.deliver_fname',
			'gr_delivery_member.deliver_lname',
			'gr_delivery_member.deliver_email',
			'gr_delivery_member.deliver_phone1',
			'gr_order.ord_task_status',
			'gr_order.ord_cancel_status',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_self_pickup'
			)
			->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
			->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
			->where('gr_order.ord_transaction_id', '=' , $id)
			->where('gr_order.ord_payment_status', '=' , 'Success')
			->where('gr_order.ord_merchant_id','=',Session::get('merchantid'))
			->groupBy('gr_order.ord_rest_id')
			->orderBy('ord_id', 'desc')->get();
			if(count($store_det) > 0 )
			{
				foreach($store_det as $stdet)
				{
					$results[$stdet->st_store_name]['general_detail'][]=array('st_logo'=>$stdet->st_logo,
					'ord_transaction_id'=>$stdet->ord_transaction_id,
					'ord_agent_id'=>$stdet->ord_agent_id,
					'ord_delivery_memid'=>$stdet->ord_delivery_memid,
					'ord_status'=>$stdet->ord_status,
					'agent_fname'=>$stdet->agent_fname,
					'agent_lname'=>$stdet->agent_lname,
					'agent_email'=>$stdet->agent_email,
					'agent_phone1'=>$stdet->agent_phone1,
					'deliver_fname'=>$stdet->deliver_fname,
					'deliver_lname'=>$stdet->deliver_lname,
					'deliver_email'=>$stdet->deliver_email,
					'deliver_phone1'=>$stdet->deliver_phone1,
					'ord_task_status'=>$stdet->ord_task_status,
					'ord_cancel_status'=>$stdet->ord_cancel_status,
					'ord_cancel_reason'=>$stdet->ord_cancel_reason,
					'ord_cancel_date'=>$stdet->ord_cancel_date,
					'ord_id'=>$stdet->ord_id,
					'ord_self_pickup'=>$stdet->ord_self_pickup
					);
					
					
					/*ITEM DETAILS */
					$itemDetail_qry = DB::table('gr_order')
					->select('gr_order.ord_id',
					'gr_order.ord_pro_id',
					'gr_order.ord_quantity',
					'gr_order.ord_currency',
					'gr_order.ord_grant_total',
					'gr_order.ord_delivery_fee',
					'gr_product.pro_item_code',
					'gr_product.'.$name.' as item_name',
					'gr_product.pro_images',
					'gr_order.ord_choices',
					'gr_order.ord_type',
					'gr_order.ord_cancel_status',
					'gr_order.ord_cancel_reason',
					'gr_order.ord_cancel_date',
					'gr_order.ord_status'
					)
					->leftjoin('gr_product', 'gr_product.pro_id', '=', 'gr_order.ord_pro_id')
					->where('gr_product.pro_status', '=' , '1')
					->where('gr_order.ord_merchant_id','=',Session::get('merchantid'))
					->where('gr_order.ord_transaction_id', '=' , $stdet->ord_transaction_id)
					->where('gr_order.ord_rest_id', '=' , $stdet->ord_rest_id)->get();
					$results[$stdet->st_store_name]['delivery_detail'][]=$itemDetail_qry;
					/* EOF ITEM DETAILS */ 
				}
			}
			//print_r($results);
			//exit;
			return $results;
			
		}
		public static function get_inventory_details()
		{
			return DB::table('gr_product')->select('gr_product.pro_item_code',
			'gr_product.pro_item_name',
			'gr_store.st_store_name',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_product.pro_quantity',
			'gr_product.pro_no_of_purchase',
			'gr_product.pro_id'
			)
			->leftjoin('gr_store', 'gr_product.pro_store_id', '=', 'gr_store.id')
			->leftjoin('gr_merchant', 'gr_store.st_mer_id', '=', 'gr_merchant.id')
			->where('gr_merchant.id','=',Session::get('merchantid'))
			->orderBy('pro_id', 'desc')->paginate(10);
		}
		
		/** get commission details **/
		public static function get_commission_list()
		{	
			//echo Session::get('mer_lang_code'); exit;
			$name = (Session::get('mer_lang_code') == "en" ) ? 'st_store_name' : 'st_store_name_'.Session::get('mer_lang_code') ;
			return DB::table('gr_merchant_overallorder')->select('or_admin_amt','or_coupon_amt','or_cancel_amt','or_mer_amt','mer_fname','mer_email','gr_store.'.$name.' as st_name','or_mer_id')			
			->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id')
			->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
			->where('gr_merchant.mer_status','<>','2')
			->where('or_mer_id','=',Session::get('merchantid'))
			->first();
		}
		public static function get_cancelled_order($paid_status='')
		{
			//$name = (Session::get('admin_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('admin_lang_code');
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')
			->select(
			'gr_order.ord_id',
			'gr_customer.cus_email',
			'gr_order.ord_transaction_id',
			'gr_product.pro_item_name',
			'gr_order.ord_currency',
			DB::Raw('SUM(gr_order.ord_grant_total) AS ord_grant_total'),
			DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS ord_cancel_paidamt'),
			DB::Raw('SUM(gr_order.ord_cancel_amt) AS tot_cancel_withCommission'),
			DB::Raw('SUM(gr_order.ord_admin_amt) AS tot_admin_Commission'),
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_pay_type',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_merchant_id',
			//DB::Raw('gr_merchant_overallorder.or_cancel_amt AS tot_cancel_withCommission'),
			'gr_order.add_delfee_status',
			'gr_order.ord_status'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->join('gr_merchant_overallorder','gr_merchant_overallorder.or_mer_id', '=', 'gr_order.ord_merchant_id')
			->where('gr_order.ord_cancel_status','=','1')
			->where('gr_order.ord_merchant_id','=',Session::get('merchantid'));
			if($paid_status == 'Paid') {
				$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)<=0');
			}
			elseif($paid_status == 'Unpaid') {
				$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)>0');
			}
			$sql->groupBy('gr_order.ord_transaction_id');
			$result = $sql->paginate(10);///->get();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			return $result;
		}
		
		/** get rejected item details **/
		public static function get_rejected_details($id,$trans_id)
		{	
			$st_name = (Session::get('mer_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('mer_lang_code');
			$item_name = (Session::get('mer_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('mer_lang_code');
			return DB::table('gr_order')->select('ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail','ord_pro_id','ord_rest_id','ord_had_choices','ord_choices','ord_grant_total','ord_reject_reason','gr_store.'.$st_name.' as shop_name','gr_product.'.$item_name.' as item_name','gr_product.pro_images','ord_currency','gr_store.st_type','ord_self_pickup')
			->leftJoin('gr_store','gr_order.ord_rest_id','=','gr_store.id')
			->leftJoin('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where(['ord_merchant_id' => $id,'ord_transaction_id' => $trans_id,'ord_status' => '3'])
			->get();
		}
		public static function get_rejected_details_byOrderId($merchant_id,$ord_id)
		{	
			$st_name = (Session::get('mer_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('mer_lang_code');
			$item_name = (Session::get('mer_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('mer_lang_code');
			return DB::table('gr_order')->select('ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail','ord_pro_id','ord_rest_id','ord_had_choices','ord_choices','ord_grant_total','ord_reject_reason','gr_store.'.$st_name.' as shop_name','gr_product.'.$item_name.' as item_name','gr_product.pro_images','ord_currency','gr_store.st_type','ord_self_pickup')
			->leftJoin('gr_store','gr_order.ord_rest_id','=','gr_store.id')
			->leftJoin('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where(['ord_merchant_id' => $merchant_id,'ord_id' => $ord_id,'ord_status' => '3'])
			->get();
		}
		
		/* get merchant payment details */
		public static function get_payment_details($merchant_id)
		{
			return DB::table('gr_merchant')->select('mer_paynamics_status','mer_paymaya_status','mer_netbank_status')->where('id','=',$merchant_id)->first();
		}
	}					