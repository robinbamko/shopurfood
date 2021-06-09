<?php
	
	namespace App;
	
	use Illuminate\Database\Eloquent\Model;
	use DB;
	use Session;
	
	class Customer extends Model
	{
		public static function login_check($email,$password)
		{
			//echo md5($password); exit;
			$details =  DB::table('gr_customer')->select('cus_email')->where(['cus_status' => '1','cus_email' => $email])->first();
			
			if(!empty($details))
			{
				$get_pwd = DB::table('gr_customer')->select('cus_password')->where(['cus_email' =>$details->cus_email,'cus_status' => '1'])->first();
				if(!empty($get_pwd))    /* login success */
				{
					if($get_pwd->cus_password == md5($password))
					{
						return 1;
					}
					else    /* Invalid password */
					{
						return -1;
					}
				}
				
			}
			else    /* Invalid mail */
			{
				return 0; 
			}
		}
		
		public static function login_ph_check($email,$password)
		{
			//echo md5($password); exit;
			$details =  DB::table('gr_customer')->select('cus_phone1')->where(['cus_status' => '1','cus_phone1' => $email])->first();
			
			if(!empty($details))
			{
				$get_pwd = DB::table('gr_customer')->select('cus_password')->where(['cus_phone1' =>$details->cus_phone1])->first();
				if(!empty($get_pwd))    /* login success */
				{
					if($get_pwd->cus_password == md5($password))
					{
						return 1;
					}
					else    /* Invalid password */
					{
						return -1;
					}
				}
				
			}
			else    /* Invalid phone number */
			{
				return -4; 
			}
		}
		
		
		public static function get_customer_det($customer_id)
		{
            return DB::table('gr_customer')->where('cus_id','=',$customer_id)->first();
		}
		
		public static function get_cus_order_details($customer_id)
		{
			return DB::table('gr_order')->where('ord_cus_id','=',$customer_id)->get();
		}
		
		public static function getall_customer_order($customer_id,$order_number)
		{ 
			//$from = date("Y-m-d", strtotime($from_date));
			//$to   = date("Y-m-d", strtotime($to_date));
			//SELECT (CASE WHEN (select count(*) from gr_order where ord_transaction_id = 'COD-1592373903') = (select count(*) from gr_order where ord_transaction_id = 'COD-1592373903' AND ord_status > 3) THEN 'Yes' ELSE 'No' END) AS 'all_cancel' FROM gr_order where ord_transaction_id = 'COD-1592373903' group by ord_transaction_id
			///DB::connection()->enableQueryLog();
			$sql = DB::table('gr_order')->select('gr_order.ord_id','gr_customer.cus_fname','gr_customer.cus_lname','gr_order.ord_date','gr_order.ord_agent_id',DB::raw('SUM(gr_order.ord_grant_total) As revenue'),'gr_order.ord_wallet','gr_order.ord_payment_status','gr_order.ord_transaction_id','gr_order.ord_merchant_id','gr_order.ord_task_status','gr_order.ord_delivery_fee','gr_order.ord_currency','gr_order.ord_status','gr_order.ord_delivery_memid','gr_order.ord_cancel_reason','gr_order.ord_self_pickup','gr_order.ord_delmgr_id')
			->groupBy('gr_order.ord_transaction_id')
			->orderBy('ord_date', 'desc')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->where('gr_customer.cus_id',$customer_id);
			
			if ($order_number != '')
			{
				$sql->where('gr_order.ord_transaction_id', 'like' , '%'.$order_number.'%');
			}
			/* if($to_date != '') {
				$sql->whereDate('gr_order.ord_date', '<=' , $to);
			} */
			
			$result = $sql->paginate(10);
			/*
				$query = DB::getQueryLog();
				print_r($query);
				
			exit;*/
			return $result;
		}
		
		public static function track_reportss($id)
		{
			
			$store_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			
			$results = array();
			$store_det = DB::table('gr_order')->select('gr_order.ord_id',$store_name .' as st_store_name')
			->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->where('gr_order.ord_transaction_id', '=' , $id)
			->groupBy('gr_order.ord_rest_id')
			->orderBy('ord_id', 'desc')->get();
			if(count($store_det) > 0 )
			{
				foreach($store_det as $stdet)
				{
					$detail_qry = DB::table('gr_order')->select('gr_order.ord_id',
					'gr_order.ord_transaction_id',
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
					'gr_delivery_member.deliver_phone1'
					)
					->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->orderBy('ord_id', 'desc')
					->where('gr_order.ord_id', '=' , $stdet->ord_id)->get();
					$results[$stdet->st_store_name][]=$detail_qry;
				}
			}
			//print_r($results);
			//exit;
			return $results;
			
		}
		
		public static function track_reports($id)
		{
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			
			$store_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			
			$results = array();
			//GET GENERAL DATA
			$store_det = DB::table('gr_order')->select( 'gr_order.ord_id',
			'gr_order.ord_rest_id',
            $store_name .' as st_store_name',
			'gr_store.st_address',
			'gr_store.st_logo',
			'gr_order.ord_transaction_id',
			'gr_order.ord_date',
			'gr_order.ord_agent_id',
			'gr_order.ord_merchant_id',
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
			'gr_delivery_manager.dm_customer_rating',
			'gr_order.ord_task_status',
			'gr_order.ord_agent_acpt_status',
			'gr_order.ord_delboy_act_status',
			'gr_order.ord_pro_id',
			'gr_order.ord_cancel_status',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cancel_date',
			'gr_order.ord_self_pickup',
			'gr_store.st_type'
			)
			->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
			->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
			->leftjoin('gr_delivery_manager', 'gr_order.ord_delmgr_id', '=', 'gr_delivery_manager.dm_id')
			->where('gr_order.ord_transaction_id', '=' , $id)
			->where('gr_order.ord_payment_status', '=' , 'Success')
			->groupBy('gr_order.ord_rest_id')
			->orderBy('ord_id', 'desc')->get();
			
			if(count($store_det) > 0 )
			{
				foreach($store_det as $stdet)
				{
					//DB::connection()->enableQueryLog();
					$reviewDet = DB::table('gr_review')->select('customer_id','review_comments','review_rating','comment_id')->where('customer_id','=',Session::get('customer_id'))->where('res_store_id','=',$stdet->ord_rest_id)->whereRaw("(review_type='store' OR review_type='restaurant')")->first();
					//$query = DB::getQueryLog();
					//echo '<pre>';print_r($query);
					
					
					if(empty($reviewDet)===false)
					{
						$reviewed='Yes';
						$reviewComments = $reviewDet->review_comments;
						$reviewRatings = $reviewDet->review_rating;
						$reviewCommentId = $reviewDet->comment_id;
					}
					else
					{
						$reviewed='No';
						$reviewComments = '';
						$reviewRatings = '';
						$reviewCommentId = '';
					}
					
					
					
					
					$proitemDet = DB::table('gr_review')->select('customer_id','review_comments','review_rating','comment_id')->where('customer_id','=',Session::get('customer_id'))->where('proitem_id','=',$stdet->ord_pro_id)->whereRaw("(review_type='item' OR review_type='product')")->first();
					
					if(empty($proitemDet)===false)
					{
						$proitemreviewed='Yes';
						$proitemreviewComments = $proitemDet->review_comments;
						$proitemreviewRatings = $proitemDet->review_rating;
						
					}
					else
					{
						$proitemreviewed='No';
						$proitemreviewComments = '';
						$proitemreviewRatings = '';
						
					}
					
					/*ORDER REVIEW*/
					$orderReview = DB::table('gr_review')->select('customer_id','review_comments','review_rating','comment_id')->where('customer_id','=',Session::get('customer_id'))->where('agent_id','=',$stdet->ord_agent_id)->where('delivery_id','=',$stdet->ord_delivery_memid)->where('res_store_id','=',$stdet->ord_rest_id)->where('order_id','=',$id)->whereRaw("(review_type='order')")->first();
					
					if(empty($orderReview)===false)
					{
						$orderReviewed='Yes';
						$orderComments = $orderReview->review_comments;
						$orderRatings = $orderReview->review_rating;
						$orderCommentId = $orderReview->comment_id;
					}
					else
					{
						$orderReviewed='No';
						$orderComments = '';
						$orderRatings = '';
						$orderCommentId ='';
					}
					
					$results[$stdet->st_store_name]['general_detail'][]=array('st_logo'=>$stdet->st_logo,
					'st_type'=>$stdet->st_type,
					'st_address'=>$stdet->st_address,
					'mer_id'=>$stdet->ord_merchant_id,
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
					'reviewed'=>$reviewed,
					'reviewComments'=>$reviewComments,
					'reviewRatings'=>$reviewRatings,
					'orderReviewed'=>$orderReviewed,
					'reviewCommentId'=>$reviewCommentId,
					'cusRatingStatus'=>$stdet->dm_customer_rating,
					'orderComments'=>$orderComments,
					'orderRatings'=>$orderRatings,
					'orderCommentId'=>$orderCommentId,
					'ord_cancel_status'=>$stdet->ord_cancel_status,
					'ord_cancel_reason'=>$stdet->ord_cancel_reason,
					'ord_cancel_date'=>$stdet->ord_cancel_date,
					'ord_id'=>$stdet->ord_id,
					'ord_self_pickup'=>$stdet->ord_self_pickup
					
					);
					
					/*Store Type */
					$res_store_type_qry = DB::table('gr_order')
					->select(
					'gr_store.st_type'
					)
					->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
					->orderBy('ord_id', 'desc')
					->where('gr_order.ord_id', '=' , $stdet->ord_id)->first();
					$results[$stdet->st_store_name]['st_type']=$res_store_type_qry;
					
					/*Store ID*/
					$res_store_id_qry = DB::table('gr_order')
					->select(
					'gr_store.id'
					)
					->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
					->orderBy('ord_id', 'desc')
					->where('gr_order.ord_id', '=' , $stdet->ord_id)->first();
					$results[$stdet->st_store_name]['st_id']=$res_store_id_qry; 
					
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
					'gr_order.ord_id',
					'gr_order.ord_cancel_status',
					'gr_order.ord_cancel_reason',
					'gr_order.ord_reject_reason',
					'gr_order.ord_cancel_date',
					'gr_order.ord_status',
					'gr_order.ord_merchant_id',
					'gr_order.ord_rest_id',
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
				//exit;
			}
			//print_r($results);
			//exit;
			return $results;
			
		}
		
		public static function track_reports2($id)
		{
			$results = array();
			$customer_id = Session::get('customer_id');
			$store_det = DB::table('gr_order')->select('gr_order.ord_id','gr_store.st_store_name','gr_order.ord_transaction_id','gr_store.st_type')
			->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
			->leftjoin('gr_customer', 'gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->where('gr_order.ord_payment_status', '=' , 'Success')
			->where('gr_order.ord_transaction_id', '=' , $id)
			->where('gr_customer.cus_id','=',$customer_id)
			->groupBy('gr_order.ord_rest_id')
			->orderBy('ord_id', 'desc')->get();
			
			if(count($store_det) > 0 )
			{
				
				$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
				foreach($store_det as $stdet)
				{
					$detail_qry = DB::table('gr_order')
					->select('gr_order.ord_id',
					'gr_order.ord_transaction_id',
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
					'gr_store.st_type'
					)
					->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
					->orderBy('ord_id', 'desc')
					->where('gr_order.ord_id', '=' , $stdet->ord_id)->get();
					$results[$stdet->st_store_name]['delivery_detail'][]=$detail_qry;
					
					/*Store Type */
					$res_store_type_qry = DB::table('gr_order')
					->select(
					'gr_store.st_type'
					)
					->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
					->orderBy('ord_id', 'desc')
					->where('gr_order.ord_id', '=' , $stdet->ord_id)->first();
					$results[$stdet->st_store_name]['st_type']=$res_store_type_qry;
					
					/*Store ID*/
					$res_store_id_qry = DB::table('gr_order')
					->select(
					'gr_store.id'
					)
					->leftjoin('gr_agent', 'gr_order.ord_agent_id', '=', 'gr_agent.agent_id')
					->leftjoin('gr_delivery_member', 'gr_order.ord_delivery_memid', '=', 'gr_delivery_member.deliver_id')
					->leftjoin('gr_store', 'gr_order.ord_rest_id', '=', 'gr_store.id')
					->orderBy('ord_id', 'desc')
					->where('gr_order.ord_id', '=' , $stdet->ord_id)->first();
					$results[$stdet->st_store_name]['st_id']=$res_store_id_qry; 
					
					/*ITEM DETAILS */
					$itemDetail_qry = DB::table('gr_order')
					->select('gr_order.ord_id',
					'gr_order.ord_pro_id',
					'gr_order.ord_quantity',
					'gr_order.ord_currency',
					'gr_order.ord_grant_total',
					'gr_order.ord_delivery_fee',
					'gr_product.pro_item_code',
					'gr_product.'.$name.' as item_name'
					)
					->leftjoin('gr_product', 'gr_product.pro_id', '=', 'gr_order.ord_pro_id')
					->where('gr_product.pro_status', '=' , '1')
					->where('gr_order.ord_transaction_id', '=' , $stdet->ord_transaction_id)->get();
					$results[$stdet->st_store_name]['itemDet'][]=$itemDetail_qry;
					/* EOF ITEM DETAILS */ 
				}
			}
			//print_r($results);
			//exit;
			return $results;
			
		}
		public static function restaurant_review($customer_id,$idval)
		{
			return DB::table('gr_review')->where('customer_id','=',$customer_id)->where('review_type','=','restaurant')->where('res_store_id','=',$idval)->first();
		}
		public static function store_review($customer_id,$idval)
		{
			return DB::table('gr_review')->where('customer_id','=',$customer_id)->where('review_type','=','store')->where('res_store_id','=',$idval)->first();
		}
		
		/** get review **/
		public static function get_my_review($wh_type = '')
		{     
			$pro_name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$shop_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			if($wh_type == "item" || $wh_type == "product")
			{
				return DB::table('gr_review')->select('proitem_id','res_store_id','merchant_id','review_comments','created_date','review_rating','gr_store.'.$shop_name.' as shop_name','gr_store.store_slug','gr_product.'.$pro_name.' as item_name',DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),'review_status','gr_store.store_slug','gr_product.pro_item_slug')
				->leftJoin('gr_product','gr_review.proitem_id','=','gr_product.pro_id')
				->leftJoin('gr_merchant','gr_merchant.id','=','gr_review.merchant_id')
				->leftJoin('gr_store','gr_store.id','=','gr_review.res_store_id')
				->where(['gr_product.pro_status' => '1','gr_merchant.mer_status' => '1','gr_store.st_status' => '1','review_type' => $wh_type,'gr_review.customer_id' => Session::get('customer_id')])
				->where('review_status','!=','2')
				->paginate(10);
			}
			if($wh_type == "store" || $wh_type == "restaurant")
			{
				return DB::table('gr_review')->select('res_store_id','merchant_id','review_comments','created_date','review_rating','gr_store.'.$shop_name.' as shop_name','gr_store.store_slug','st_logo','review_status')
				->leftJoin('gr_merchant','gr_merchant.id','=','gr_review.merchant_id')
				->leftJoin('gr_store','gr_store.id','=','gr_review.res_store_id')
				->where(['gr_merchant.mer_status' => '1','gr_store.st_status' => '1','review_type' => $wh_type,'gr_review.customer_id' => Session::get('customer_id')])
				->where('review_status','!=','2')
				->paginate(10);
			}
			if($wh_type == "order")
			{
				return DB::table('gr_review')->select('delivery_id','review_comments','created_date','review_rating','review_status',DB::Raw("CONCAT(if(deliver_fname is null,'',deliver_fname),' ',if(deliver_lname is null,'',deliver_lname)) as deliver_name"))
				->leftJoin('gr_delivery_member','gr_delivery_member.deliver_id','=','gr_review.delivery_id')
				->where(['gr_delivery_member.deliver_status' => '1','review_type' => $wh_type,'gr_review.customer_id' => Session::get('customer_id')])
				->where('review_status','!=','2')
				->paginate(10);
			}
		}
		
		/* get order summary details */
		public static function get_or_summary_details($ord_id,$cus_id)
		{
			$item_name = (Session::get('front_lang_code') == 'en')  ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$st_name    = (Session::get('front_lang_code') == 'en')  ?'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			return DB::table('gr_order')->select('ord_pro_id','ord_rest_id','ord_choices','ord_had_choices','ord_spl_req','ord_quantity','ord_currency','ord_unit_price','ord_sub_total','ord_tax_amt','add_delfee_status','ord_grant_total','ord_type','ord_delivery_fee','ord_wallet','gr_product.'.$item_name.' as it_name','gr_store.'.$st_name.' as st_name', DB::Raw('SUBSTRING_INDEX(gr_product.pro_images,"/**/",1) as pro_image'),'ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail','cus_fname','cus_lname','cus_address','cus_phone1','cus_email','ord_transaction_id','ord_pay_type','ord_pre_order_date','ord_self_pickup','ord_id')
			->leftJoin('gr_product','gr_product.pro_id','=','gr_order.ord_pro_id')
			->leftJoin('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->leftjoin('gr_customer','gr_customer.cus_id','=','gr_order.ord_cus_id')
			->where(['ord_transaction_id' => $ord_id,'ord_cus_id' => $cus_id])
			->get();
		}
	}
