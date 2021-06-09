<?php
	namespace App;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class Reports extends Model
	{
		use Notifiable;
		protected $table = 'gr_product';
		public static function getall_dealreports($from_date, $to_date,$ord_status,$order_id='')
		{ 
			$from = date("Y-m-d", strtotime($from_date));
			$to   = date("Y-m-d", strtotime($to_date));
			
			//DB::connection()->enableQueryLog(); 
			$sql = DB::table('gr_order')->select('gr_order.ord_id','gr_customer.cus_fname','gr_customer.cus_lname','gr_order.ord_date',DB::raw('SUM(gr_order.ord_grant_total) As revenue'),'gr_order.ord_payment_status','gr_order.ord_transaction_id','gr_order.ord_task_status','gr_order.ord_delivery_fee','gr_order.ord_currency','gr_order.ord_self_pickup','gr_order.ord_status','gr_order.ord_reject_reason','gr_order.ord_admin_viewed','gr_order.ord_pre_order_date')
			->groupBy('gr_order.ord_transaction_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id');
			if($order_id != ''){
				$sql->where('gr_order.ord_transaction_id', '=' , $order_id);
			}
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
			$name = (Session::get('admin_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('admin_lang_code');
			$results = array();
			//GET GENERAL DATA
			$store_det = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_rest_id',
			'gr_store.st_store_name',
			'gr_store.st_logo',
			'gr_store.st_address',
			'gr_order.ord_transaction_id',
			'gr_order.ord_date',
			'gr_order.ord_agent_id',
			'gr_order.ord_delivery_memid',
			'gr_order.ord_status',
			'gr_order.ord_agent_acpt_status',
			'gr_order.ord_delboy_act_status',
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
			'gr_order.ord_self_pickup',
			'gr_store.st_type',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_merchant.mer_email',
			'gr_merchant.mer_phone'
			)
			->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->leftjoin('gr_merchant', 'gr_merchant.id', '=', 'gr_store.st_mer_id')
			->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
			->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
			->where('gr_order.ord_transaction_id', '=' , $id)
			->where('gr_order.ord_payment_status', '=' , 'Success')
			->groupBy('gr_order.ord_rest_id')
			->orderBy('ord_id', 'desc')->get();
			
			if(count($store_det) > 0 )
			{
				foreach($store_det as $stdet)
				{
					$results[$stdet->st_store_name]['general_detail'][]=array('st_logo'=>$stdet->st_logo,
					'st_type'=>$stdet->st_type,
					'st_address'=>$stdet->st_address,
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
					'ord_agent_acpt_status'=>$stdet->ord_agent_acpt_status,
					'ord_delboy_act_status'=>$stdet->ord_delboy_act_status,
					'ord_cancel_status'=>$stdet->ord_cancel_status,
					'ord_cancel_reason'=>$stdet->ord_cancel_reason,
					'ord_cancel_date'=>$stdet->ord_cancel_date,
					'ord_id'=>$stdet->ord_id,
					'ord_self_pickup'=>$stdet->ord_self_pickup,
					'mer_fname'=>$stdet->mer_fname,
					'mer_lname'=>$stdet->mer_lname,
					'mer_email'=>$stdet->mer_email,
					'mer_phone'=>$stdet->mer_phone
					);
					
					$detail_qry = DB::table('gr_order')->select(/*'gr_order.ord_agent_id',
						'gr_order.ord_delivery_memid',
						'gr_order.ord_status',
						'gr_agent.agent_fname',
						'gr_agent.agent_lname',
						'gr_agent.agent_email',
						'gr_agent.agent_phone1',
						'gr_delivery_member.deliver_fname',
						'gr_delivery_member.deliver_lname',
						'gr_delivery_member.deliver_email',
					'gr_delivery_member.deliver_phone1',*/
					'gr_order.ord_id',
					'gr_order.ord_pro_id',
					'gr_order.ord_quantity',
					'gr_order.ord_currency',
					'gr_order.ord_grant_total',
					'gr_order.ord_delivery_fee',
					'gr_order.ord_failed_reason',
					'gr_product.pro_item_code',
					'gr_product.'.$name.' as item_name',
					'gr_product.pro_images',
					'gr_order.ord_choices',
					'gr_order.ord_type',
					'gr_order.ord_cancel_status',
					'gr_order.ord_cancel_reason',
					'gr_order.ord_cancel_date',
					'gr_order.ord_status',
					'gr_order.ord_self_pickup',
					DB::raw("case when gr_product.pro_type = '1' then 'Product Code' else 'Item Code' end as itemCodeType")
					)
					//->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					//->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_product', 'gr_product.pro_id', '=', 'gr_order.ord_pro_id')
					->orderBy('ord_id', 'desc')
					//->where('gr_product.pro_status', '=' , '1')
					->where('gr_order.ord_transaction_id', '=' ,$stdet->ord_transaction_id)
					->where('gr_order.ord_rest_id', '=' , $stdet->ord_rest_id)->get();
					$results[$stdet->st_store_name]['delivery_detail'][]=$detail_qry;
					/* EOF ITEM DETAILS */ 
				}
			}
			//print_r($results);
			//exit;
			return $results;
			
		}
		
		public static function delivery_track_reports($id,$mer_id)
		{
			$results = array();
			$store_det = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_rest_id',
			'gr_store.st_store_name',
			'gr_store.st_address',
			'gr_store.st_logo',
			'gr_store.st_type',
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
			'gr_order.ord_agent_acpt_status',
			'gr_order.ord_delboy_act_status',
			'gr_order.ord_cancel_status',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_self_pickup'
			)
			->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
			->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
			->where('gr_order.ord_payment_status', '=' , 'Success')
			->where('gr_order.ord_transaction_id', '=' , $id)
			->where('gr_order.ord_merchant_id', '=' , $mer_id)
			->where('gr_order.ord_cancel_status', '!=' , '1')
			->groupBy('gr_order.ord_rest_id')
			->orderBy('ord_id', 'desc')->get();
			
			if(count($store_det) > 0 )
			{
				
				$name = (Session::get('DelMgr_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('DelMgr_lang_code');
				foreach($store_det as $stdet)
				{
					$results[$stdet->st_store_name]['general_detail'][]=array('st_logo'=>$stdet->st_logo,
					'st_type'=>$stdet->st_type,
					'st_address'=>$stdet->st_address,
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
					'ord_agent_acpt_status'=>$stdet->ord_agent_acpt_status,
					'ord_delboy_act_status'=>$stdet->ord_delboy_act_status,
					'ord_cancel_status'=>$stdet->ord_cancel_status,
					'ord_cancel_reason'=>$stdet->ord_cancel_reason,
					'ord_cancel_date'=>$stdet->ord_cancel_date,
					'ord_id'=>$stdet->ord_id,
					'ord_self_pickup'=>$stdet->ord_self_pickup,
					'ord_merchant_id'=>$mer_id
					);
					
					/*ITEM DETAILS */
					
					$detail_qry = DB::table('gr_order')->select(
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
					'gr_order.ord_failed_reason',
					'gr_order.ord_cancel_date',
					'gr_order.ord_status',
					'gr_order.ord_id',
					DB::raw("case when gr_product.pro_type = '1' then 'Product Code' else 'Item Code' end as itemCodeType")
					)
					//->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					//->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_product', 'gr_product.pro_id', '=', 'gr_order.ord_pro_id')
					->orderBy('ord_id', 'desc')
					->where('gr_product.pro_status', '=' , '1')
					->where('gr_order.ord_transaction_id', '=' ,$stdet->ord_transaction_id)
					->where('gr_order.ord_merchant_id', '=' , $mer_id)
					->where('gr_order.ord_cancel_status', '!=' , '1')
					->where('gr_order.ord_rest_id', '=' , $stdet->ord_rest_id)->get();
					$results[$stdet->st_store_name]['delivery_detail'][]=$detail_qry;
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
			->orderBy('pro_id', 'desc')->get();
		}
		public static function newgetall_dealreports($from_date, $to_date)
		{ 
			$from = date("Y-m-d", strtotime($from_date));
			$to   = date("Y-m-d", strtotime($to_date));
			
			///DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_merchant_id',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			DB::raw('SUM(gr_order.ord_grant_total) As revenue'),
			'gr_order.ord_payment_status',
			'gr_order.ord_transaction_id',
			'gr_order.ord_task_status',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_order.ord_rest_id',
			'gr_store.st_store_name',
			'gr_store.st_address',
			'gr_store.st_latitude',
			'gr_store.st_longitude'
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->leftjoin('gr_store', 'gr_store.id', '=', 'gr_order.ord_rest_id')
			->where('gr_order.ord_payment_status','=','Success')
			->where('gr_order.ord_status','=','4')
			->where('gr_order.ord_task_status','=','0')
			->where('gr_order.ord_agent_acpt_status','=','0')
			->where('gr_order.ord_self_pickup','!=','1');
			if ($from_date != '')
			{
				$sql->whereDate('gr_order.ord_date', '>=' , $from);
			}
			if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to);
			}
			
			$result = $sql->paginate(10);
			
			return $result;
		}
		
		public static function newgetall_unassignedOrders()
		{ 
			//DB::connection()->enableQueryLog();
			/*----------------- GET NOT ASSIGNED REPORTS ---------------------------*/
			$newOrders = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_transaction_id',
			'gr_order.ord_rest_id',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			DB::raw('\'newOrder\' As assigned_status')
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_id', 'asc')
			->leftjoin('gr_store', 'gr_store.id', '=', 'gr_order.ord_rest_id')
			->where('gr_order.ord_payment_status','=','Success')
			->where('gr_order.ord_status','=','4')
			->where('gr_order.ord_task_status','=','0')
			->where('gr_order.ord_agent_acpt_status','=','0')
			->where('gr_order.ord_self_pickup','!=','1')
			->get();
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			
			/*--------------------- GET DELIVERY BOY REJECTED REPORT ------------------*/ 
			$rejectedOrders = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_transaction_id',
			'gr_order.ord_rest_id',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			DB::raw('\'rejectedOrder\' As assigned_status')
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_id', 'asc')
			->leftjoin('gr_store', 'gr_store.id', '=', 'gr_order.ord_rest_id')
			->where('gr_order.ord_payment_status','=','Success')
			->where('gr_order.ord_status','=','4')
			->where('gr_order.ord_task_status','=','1')
			->where('gr_order.ord_agent_acpt_status','=','1')
			->where('gr_order.ord_delboy_act_status','=','2')
			->where('gr_order.ord_self_pickup','!=','1')
			->get();
			
			$merged = $newOrders->merge($rejectedOrders);
			
			$result = $merged->all();
			return $result;
		}
		public static function rejected_order_by_agent(){
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_merchant_id',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_agent_id',
			DB::raw('SUM(gr_order.ord_grant_total) As revenue'),
			'gr_order.ord_payment_status',
			'gr_order.ord_transaction_id',
			'gr_order.ord_task_status',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_store.st_store_name',
			'gr_store.st_address',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			'gr_agent.agent_fname',
			'gr_agent.agent_lname',
			'gr_agent.agent_phone1',
			'gr_agent.agent_phone2',
			'gr_agent.agent_email',
			'gr_order.ord_agent_rjct_reason',
			'gr_order.ord_agent_rejected_at'
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->leftjoin('gr_store', 'gr_store.id', '=', 'gr_order.ord_rest_id')
			->leftjoin('gr_agent', 'gr_agent.agent_id', '=', 'gr_order.ord_agent_id')
			->where('gr_order.ord_payment_status','=','Success')
			->where('gr_order.ord_status','=','4')
			->where('gr_order.ord_agent_acpt_status','=','2')
			->where('gr_order.ord_self_pickup','!=','1');
			
			$result = $sql->paginate(10);
			/*$query = DB::getQueryLog();
                print_r($query);
			exit;*/
			return $result;
		}
		
		/*REJECTED ORDER BY DELIVERY BOY */
		public static function rejected_order_by_delboy(){
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_order.ord_merchant_id',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_order.ord_shipping_mobile1',
			'gr_order.order_ship_mail',
			'gr_order.order_ship_latitude',
			'gr_order.order_ship_longitude',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_delivery_memid',
			DB::raw('SUM(gr_order.ord_grant_total) As revenue'),
			'gr_order.ord_payment_status',
			'gr_order.ord_transaction_id',
			'gr_order.ord_task_status',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_store.st_store_name',
			'gr_store.st_address',
			'gr_store.st_latitude',
			'gr_store.st_longitude',
			'gr_delivery_member.deliver_fname',
			'gr_delivery_member.deliver_lname',
			'gr_delivery_member.deliver_phone1',
			'gr_delivery_member.deliver_phone2',
			'gr_delivery_member.deliver_email',
			'gr_order.ord_delboy_rjct_reason',
			'gr_order.ord_delboy_rjct_time'
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->leftjoin('gr_store', 'gr_store.id', '=', 'gr_order.ord_rest_id')
			->leftjoin('gr_delivery_member', 'gr_delivery_member.deliver_id', '=', 'gr_order.ord_delivery_memid')
			->where('gr_order.ord_payment_status','=','Success')
			->where('gr_order.ord_status','=','4')
			->where('gr_order.ord_delboy_act_status','=','2')
			->where('gr_order.ord_self_pickup','!=','1');
			
			$result = $sql->paginate(10);
			/*$query = DB::getQueryLog();
                print_r($query);
			exit;*/
			return $result;
		} 
		
		public static function getassigned_reports($from_date, $to_date)
		{ 
			$from = date("Y-m-d", strtotime($from_date));
			$to   = date("Y-m-d", strtotime($to_date));
			
			///DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			DB::raw('SUM(gr_order.ord_grant_total) As revenue'),
			'gr_order.ord_payment_status',
			'gr_order.ord_transaction_id',
			'gr_order.ord_task_status',
			'gr_order.ord_merchant_id',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_agent.agent_fname',
			'gr_agent.agent_lname',
			'gr_agent.agent_email'
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->leftjoin('gr_merchant', 'gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->leftjoin('gr_agent', 'gr_agent.agent_id', '=', 'gr_order.ord_agent_id')
			->where('ord_delmgr_id','=',Session::get('DelMgrSessId'))
			->where('gr_order.ord_status','>=','4')
			->where('gr_order.ord_agent_acpt_status','!=','2')
			->where('gr_order.ord_task_status','=','1');
			
			if ($from_date != '')
			{
				$sql->whereDate('gr_order.ord_date', '>=' , $from);
			}
			if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to);
			}
			
			$result = $sql->paginate(10);
			
			return $result;
		}
		
		public static function getassigned_reports1($from_date, $to_date)
		{ 
			$from = date("Y-m-d", strtotime($from_date));
			$to   = date("Y-m-d", strtotime($to_date));
			
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_order.ord_date',
			'gr_order.ord_pre_order_date',
			DB::raw('SUM(gr_order.ord_grant_total) As revenue'),
			'gr_order.ord_payment_status',
			'gr_order.ord_transaction_id',
			'gr_order.ord_task_status',
			'gr_order.ord_merchant_id',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_delivery_member.deliver_fname',
			'gr_delivery_member.deliver_lname',
			'gr_delivery_member.deliver_email'
			)
			->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
			->orderBy('ord_taskassigned_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->leftjoin('gr_merchant', 'gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->leftjoin('gr_delivery_member', 'gr_delivery_member.deliver_id', '=', 'gr_order.ord_delivery_memid')
			->where('ord_delmgr_id','=',Session::get('DelMgrSessId'))
			->where('gr_order.ord_status','>=','4')
			->whereRaw("NOT(`ord_delboy_act_status` <=> '2')")//gr_order.ord_delboy_act_status','!=','2')
			->where('gr_order.ord_task_status','=','1');
			
			if ($from_date != '')
			{
				$sql->whereDate('gr_order.ord_date', '>=' , $from);
			}
			if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to);
			}
			
			$result = $sql->paginate(10);
			/*$query = DB::getQueryLog();
			print_r($query); exit;      */
			return $result;
		}
		
		public static function get_cancel_or_details($ord_id)
		{
			
			return DB::table('gr_order')->select('gr_product.pro_item_name', DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),'ord_grant_total','ord_had_choices','ord_choices','ord_delivery_fee','add_delfee_status','ord_admin_amt','ord_cancel_paidamt','ord_transaction_id','ord_type','ord_currency','order_ship_mail','ord_status')
			->leftjoin('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_id','=',$ord_id)
			->first();
		}
		public static function order_report_xl($store_id,$from_date,$to_date,$payment_method,$ord_status,$deliver_id){
			/*echo '<pre>'; print_r($store_id);
				echo '<br>'.$from_date;
				echo '<br>'.$to_date;
				echo '<pre>'; print_r($payment_method);
			echo '<pre>'; print_r($ord_status);*/
			//exit;
			//DB::connection()->enableQueryLog();
			/*GROUP QUERY */ 
			$group_sql = DB::table('gr_order')->select( 'gr_order.ord_rest_id',
			'gr_store.st_store_name',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_merchant.mer_email'
			);
			if($store_id != '')
			{
				$group_sql->whereIn('gr_order.ord_rest_id',$store_id);
			}
			if($deliver_id != '')
			{
				$group_sql->whereIn('gr_order.ord_delivery_memid',$deliver_id);
			}
			if($from_date != '')
			{
				$group_sql->whereDate('gr_order.ord_date', '>=' , date('Y-m-d',strtotime($from_date)));
			}
			if($to_date != '') {
				$group_sql->whereDate('gr_order.ord_date', '<=' , date('Y-m-d',strtotime($to_date)));
			}
			if($payment_method != '')
			{
				$group_sql->whereIn('gr_order.ord_pay_type',$payment_method);
			}
			if($ord_status!=''){
				if(in_array('10',$ord_status)){
					if (($key = array_search('10', $ord_status)) !== false) { unset($ord_status[$key]); }
					if(count($ord_status) > 0){
						$group_sql->whereIn('gr_order.ord_status',$ord_status);
					}
					$group_sql->where('gr_order.ord_cancel_status','1');
					}else{
					$group_sql->whereIn('gr_order.ord_status',$ord_status);
				}
				
			}
			$group_sql->leftjoin('gr_merchant', 'gr_merchant.id', '=', 'gr_order.ord_merchant_id')
			->leftjoin('gr_store', 'gr_store.id', '=', 'gr_order.ord_rest_id');
			$group_sql->groupBy('ord_rest_id');
			$group_result = $group_sql->get();
			//echo '<pre>';print_r($group_result);
			//exit;
			$resultArray = array();
			if(count($group_result) >  0){
				foreach($group_result as $gresult){
					
					$sql = DB::table('gr_order')->select('gr_order.ord_id',
					'gr_customer.cus_fname',
					'gr_customer.cus_lname',
					'gr_delivery_member.deliver_fname',
					'gr_delivery_member.deliver_lname',
					'gr_delivery_member.deliver_email',
					'gr_product.pro_item_code',
					'gr_product.pro_item_name',
					'gr_order.ord_had_choices',
					'gr_order.ord_choices',
					'gr_order.ord_choice_amount',
					'gr_order.ord_spl_req',
					'gr_order.ord_quantity',
					'gr_order.ord_currency',
					'gr_order.ord_unit_price',
					'gr_order.ord_sub_total',
					'gr_order.ord_tax_percent',
					'gr_order.ord_tax_amt',
					'gr_order.ord_has_coupon',
					'gr_order.ord_coupon_amt',
					'gr_order.ord_wallet',
					'gr_order.ord_delivery_fee',
					'gr_order.ord_grant_total',
					'gr_order.ord_refund_status',
					'gr_order.ord_pay_type',
					'gr_order.ord_transaction_id',
					'gr_order.ord_status',
					'gr_order.ord_date',
					'gr_order.ord_pre_order_date',
					'gr_order.ord_admin_amt',
					'gr_order.ord_delmgr_id',
					'gr_order.ord_delivery_memid',
					'gr_order.ord_cancel_status',
					'gr_order.ord_cancel_amt',
					'gr_order.ord_cancel_reason',
					'gr_order.ord_cancel_date',
					'gr_order.ord_delboy_act_status',
					'gr_order.ord_delboy_accept_on',
					'gr_order.ord_delboy_rjct_reason'
					);
					//->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')
					$sql->orderBy('ord_rest_id','ASC');
					$sql->orderBy('ord_pay_type','ASC');
					$sql->orderBy('ord_status','ASC');
					$sql->orderBy('ord_date','ASC');
					
					$sql->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
					->leftjoin('gr_delivery_member', 'gr_delivery_member.deliver_id', '=', 'gr_order.ord_delivery_memid')
					->leftjoin('gr_product', 'gr_product.pro_id', '=', 'gr_order.ord_pro_id');
					
					/*if($store_id != '')
						{
						$sql->whereIn('gr_order.ord_rest_id',$store_id);
					}*/ 
					$sql->where('gr_order.ord_rest_id',$gresult->ord_rest_id);
					if($deliver_id != '')
					{
						$sql->whereIn('gr_order.ord_delivery_memid',$deliver_id);
					}
					if($from_date != '')
					{
						$sql->whereDate('gr_order.ord_date', '>=' , date('Y-m-d',strtotime($from_date)));
					}
					if($to_date != '') {
						$sql->whereDate('gr_order.ord_date', '<=' , date('Y-m-d',strtotime($to_date)));
					}
					if($payment_method != '')
					{
						$sql->whereIn('gr_order.ord_pay_type',$payment_method);
					}
					if($ord_status!=''){
						if(in_array('10',$ord_status)){
							if (($key = array_search('10', $ord_status)) !== false) { unset($ord_status[$key]); }
							if(count($ord_status) > 0){
								$sql->whereIn('gr_order.ord_status',$ord_status);
							}
							$sql->where('gr_order.ord_cancel_status','1');
							}else{
							$sql->whereIn('gr_order.ord_status',$ord_status);
						}
						
					}
					$result = $sql->get();
					$resultArray[$gresult->st_store_name.'`'.$gresult->mer_fname.'`'.$gresult->mer_lname.'`'.$gresult->mer_email] = $result;
				}
			}
			//$query = DB::getQueryLog();
			//print_r($query); exit;     
			//echo '<pre>'; print_r($resultArray); exit;
			return $resultArray;
		}
		
		
		/* earning reports */
		public static function download_earning_report($st_id,$from_date = '',$to_date = '',$payment_method = '')
		{
			$return_array = $q = array();
			$gsql = DB::table('gr_store')->select('gr_store.id','gr_merchant.mer_email',DB::Raw("CONCAT(if(gr_merchant.mer_fname is null,'',gr_merchant.mer_fname),' ',if(gr_merchant.mer_lname is null,'',gr_merchant.mer_lname) ) as mer_name"),'gr_store.st_store_name');
			if($st_id!=''){
				$gsql->whereIn('gr_store.id',$st_id);
			}
			$gsql->leftjoin('gr_order','gr_order.ord_rest_id','=','gr_store.id')
			->leftjoin('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id');
			$gsql=$gsql->groupBy('ord_rest_id')->get();
			//return $gsql; exit;
			if(count($gsql) > 0)
			{
				foreach($gsql as $data)
				{
					$sql = DB::table('gr_order')->select('ord_transaction_id',
					'ord_date',
					'ord_status',
					'ord_cancel_status',
					'ord_pay_type',
					'ord_currency',
					'ord_rest_id',
					DB::Raw('SUM(ord_grant_total) AS total_order_amount'),
					DB::Raw('SUM(ord_admin_amt) AS total_admin_amount'),
					'add_delfee_status',
					'ord_delivery_fee',
					'ord_wallet',
					DB::Raw('SUM(ord_cancel_amt) AS total_cancel_amt'))
					->leftjoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
					->leftjoin('gr_merchant','gr_merchant.id','=','gr_order.ord_merchant_id')
					->where('ord_rest_id','=',$data->id);
					
					
					if($from_date != '')
					{
						$q = $sql->whereDate('ord_date','>=',date('Y-m-d',strtotime($from_date)));
					}
					if($to_date != '')
					{
						$q = $sql->whereDate('ord_date','<=',date('Y-m-d',strtotime($to_date)));   
					}
					if($payment_method != '')
					{
						$sql->whereIn('gr_order.ord_pay_type',$payment_method);
					}
					$q = $sql->groupBy('ord_transaction_id')->get();
					if(count($q) > 0)
					{
						foreach($q as $result)
						{
							$return_array[$data->id.'``'.$data->st_store_name.'``'.$data->mer_email.'``'.$data->mer_name][] = $result;
						}
					}
				}
				
				
			}
			return $return_array;
		}
		
		/* merchant transation report */
		public static function get_mer_trans($mer_id,$from_date,$to_date)
		{
			$return_array = $q = array();
			$gsql = DB::table('gr_merchant')->select('gr_store.id','gr_merchant.mer_email',
			DB::Raw("CONCAT(if(gr_merchant.mer_fname is null,'',gr_merchant.mer_fname),' ',if(gr_merchant.mer_lname is null,'',gr_merchant.mer_lname)) as mer_name"),'gr_merchant.id as mer_id',
			'gr_store.st_store_name')
			->leftjoin('gr_merchant_commission','gr_merchant_commission.commission_mer_id','=','gr_merchant.id')
			->leftjoin('gr_store','gr_store.st_mer_id','=','gr_merchant.id');
			if($mer_id!=''){
				$gsql->whereIn('gr_merchant.id',$mer_id);
			}
			$result = $gsql->groupBy('commission_mer_id')->get();
			//print_r($result); exit;
			//return $gsql; exit;
			if(count($result) > 0)
			{
				foreach($result as $data)
				{
					//echo '<pre>';print_r($data);
					$sql = DB::table('gr_merchant_commission')
					->select('commission_paid',
					'commission_currency',
					'mer_commission_status',
					'mer_transaction_id',
					'pay_type',
					'commission_date')
					->leftjoin('gr_merchant','gr_merchant.id','=','gr_merchant_commission.commission_mer_id')
					->where('commission_mer_id','=',$data->mer_id);
					if($from_date != '')
					{
						$sql->whereDate('commission_date','>=',date('Y-m-d',strtotime($from_date)));
					}
					if($to_date != '')
					{
						$sql->whereDate('commission_date','<=',date('Y-m-d',strtotime($to_date)));   
					}
					//$q = $sql->orderBy(DB::Raw('count(commission_mer_id)'),'Desc')->get();
					$q = $sql->get();
					if(count($q) > 0)
					{
						foreach($q as $comRes)
						{
							$return_array[$data->mer_id.'``'.$data->st_store_name.'``'.$data->mer_email.'``'.$data->mer_name][] = $comRes;
						}
					}
				}
			}
			return $return_array;
		}
		/*DELIVERY PERSON TRANSACTION HISOTRY*/
		public static function get_delboy_trans($deliver_id,$from_date,$to_date)
		{
			$return_array = $q = array();
			$gsql = DB::table('gr_delivery_member')->select('gr_delivery_member.deliver_email',
			DB::Raw("CONCAT(if(gr_delivery_member.deliver_fname is null,'',gr_delivery_member.deliver_fname),' ',if(gr_delivery_member.deliver_lname is null,'',gr_delivery_member.deliver_lname)) as delboy_name"),'gr_delivery_member.deliver_id')
			->leftjoin('gr_delboy_commission','gr_delboy_commission.delboy_id','=','gr_delivery_member.deliver_id');
			if($deliver_id!=''){
				$gsql->whereIn('gr_delivery_member.deliver_id',$deliver_id);
			}
			$result = $gsql->groupBy('delboy_id')->get();
			//print_r($result); exit;
			//return $gsql; exit;
			if(count($result) > 0)
			{
				foreach($result as $data)
				{
					//echo '<pre>';print_r($data);
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_delboy_commission')
					->select('commission_paid',
					'amount_received',
					'commission_currency',
					'commission_status',
					'transaction_id',
					'pay_type',
					'commission_date')
					->leftjoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_delboy_commission.delboy_id')
					->where('delboy_id','=',$data->deliver_id);
					if($from_date != '')
					{
						$sql->whereDate('commission_date','>=',date('Y-m-d',strtotime($from_date)));
					}
					if($to_date != '')
					{
						$sql->whereDate('commission_date','<=',date('Y-m-d',strtotime($to_date)));   
					}
					//$q = $sql->orderBy(DB::Raw('count(commission_mer_id)'),'Desc')->get();
					$q = $sql->get();
					//$query = DB::getQueryLog();
					//echo '<pre>'; print_r($query);
					if(count($q) > 0)
					{
						foreach($q as $comRes)
						{
							$return_array[$data->deliver_id.'``'.$data->delboy_name.'``'.$data->deliver_email][] = $comRes;
						}
					}
				}
			}
			//exit;
			return $return_array;
		}
		
		/*  delivery person earning report */
		public static function delboy_earning_report1($deliver_id,$from_date = '',$to_date='')
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
		public static function delboy_earning_report($deliver_id,$from_date = '',$to_date = '')
		{
			$return_array = $q = array();
			$gsql = DB::table('gr_delivery_member')->select('deliver_email',DB::Raw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) AS name"),'deliver_id');
			if($deliver_id!=''){
				$gsql->whereIn('gr_delivery_member.deliver_id',$deliver_id);
			}
			$gsql->leftjoin('gr_delivery_person_earnings','gr_delivery_person_earnings.de_deliver_id','=','gr_delivery_member.deliver_id');
			$gsql=$gsql->groupBy('deliver_id')->get();
			//return $gsql; exit;
			if(count($gsql) > 0)
			{
				foreach($gsql as $data)
				{   
					$base_charge = 0;
					$sql =DB::table('gr_delivery_person_earnings')->select('de_updated_at','de_ord_currency','de_ord_status','de_total_amount','de_transaction_id','de_order_total','de_pay_type')
                    ->where(['de_deliver_id' => $data->deliver_id]);
					if($from_date != '')
					{
						$sql->whereDate('de_updated_at','>=',$from_date);
					}
					if($to_date != '')
					{
						$sql->whereDate('de_updated_at','<=',$to_date);   
					}
					$result = $sql->get();
					if(count($result) > 0 ){
						$return_array[$data->deliver_id.'`'.$data->name.'`'.$data->deliver_email] = $result;
					}
				}
			}
			return $return_array;
		}
		public static function consolidate_report($from_date = '',$to_date = ''){
			//DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select(	'ord_transaction_id',
			'ord_date',
			'ord_self_pickup',
			DB::raw('SUM(gr_order.ord_grant_total) As total_order_amount'),
			DB::raw('SUM(gr_order.ord_cancel_amt) As total_cancel_amount'),
			//DB::raw('AVG(ord_delivery_fee) AS AverageDelFee'),
			'ord_delivery_fee  AS AverageDelFee',
			DB::raw('SUM(gr_order.ord_admin_amt) As total_admin_commission'),
			DB::raw('AVG(gr_order.ord_wallet) As total_wallet_amount')
			);
			if($from_date != '')
			{
				$sql->whereDate('ord_date','>=',date('Y-m-d',strtotime($from_date)));
			}
			if($to_date != '')
			{
				$sql->whereDate('ord_date','<=',date('Y-m-d',strtotime($to_date)));   
			}
			$sql->groupBy('ord_transaction_id')->orderBy('ord_id','DESC');
			
			$result = $sql->get();
			$query = DB::getQueryLog();
			//print_r($query);
			//exit; 
			return $result;		
		}
		
	}		