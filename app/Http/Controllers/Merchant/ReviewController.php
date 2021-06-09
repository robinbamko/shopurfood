<?php
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Merchant;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	
	use Validator;
	
	use Session;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	class ReviewController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
			
		}
		
		/** product / item review ***/
		public function manage_product_review()
		{
			if(Session::has('merchantid') == 1)
			{
				$get_details = DB::table('gr_review')->select('pro_item_name','cus_fname','cus_email','review_comments','review_rating','review_status','comment_id','gr_product.pro_item_name','gr_product.pro_item_code','gr_store.st_store_name','review_type')
                ->leftjoin('gr_product','gr_review.proitem_id', '=', 'gr_product.pro_id')
                ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
                ->leftjoin('gr_store','gr_product.pro_store_id','gr_store.id')
                ->leftjoin('gr_merchant','gr_merchant.id','=','gr_review.merchant_id')
                ->where('review_status','!=','2')
                ->where('review_type','=','product')
                ->where('gr_review.merchant_id','=',Session::get('merchantid'))
                ->paginate(10);
				
				//print_r($get_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_REVIEW_PRO')) ? trans(Session::get('mer_lang_file').'.ADMIN_REVIEW_PRO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REVIEW_PRO');
				//$id = '';
				return view('sitemerchant.review.manage_product_review')->with('pagetitle',$page_title)->with('all_details',$get_details);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** change review status **/
		public function pro_review_status($id,$status,$type)
		{
			if(Session::has('merchantid'))
			{
				$update = ['review_status' => $status];
				$where = ['comment_id' => base64_decode($id)];
				$a = updatevalues('gr_review',$update,$where);
				
				if($status == 1) //Active
				{
					
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_APPROVE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_APPROVE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_APPROVE_SUCCESS');
					Session::flash('message',$msg);
					if($type == "product")
					{
						return Redirect::to('mer-manage-product-review');
					}
					elseif($type == "item")
					{
						return Redirect::to('mer-manage-item-review');
					}
					elseif($type == "store")
					{
						return Redirect::to('mer-manage-store-review');
					}
					elseif($type == "restaurant")
					{
						return Redirect::to('mer-manage-restaurant-review');
					}
				}
				if($status == 2) //Delete
				{
					
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
					Session::flash('message',$msg);
					if($type == "product")
					{
						return Redirect::to('mer-manage-product-review');
					}
					elseif($type == "item")
					{
						return Redirect::to('mer-manage-item-review');
					}
					elseif($type == "store")
					{
						return Redirect::to('mer-manage-store-review');
					}
					elseif($type == "restaurant")
					{
						return Redirect::to('mer-manage-restaurant-review');
					}
				}
				else   //block
				{
					
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DISAPPROVE_SUCCESS');
					Session::flash('message',$msg);
					if($type == "product")
					{
						return Redirect::to('mer-manage-product-review');
					}
					elseif($type == "item")
					{
						return Redirect::to('mer-manage-item-review');
					}
					elseif($type == "store")
					{
						return Redirect::to('mer-manage-store-review');
					}
					elseif($type == "restaurant")
					{
						return Redirect::to('mer-manage-restaurant-review');
					}
				}
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** change review status **/
		public function multi_proreview_block()
		{
			$update = ['review_status' => Input::get('status')];
			$val = Input::get('val');
			
			//return count($val); exit;
			for($i=1; $i< count($val); $i++)
			{
				$where = ['comment_id' => $val[$i]];
				
				$a = updatevalues('gr_review',$update,$where);
			}
			//echo Input::get('status'); exit;
			if(Input::get('status') == 1) //Active
			{
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_APPROVE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_APPROVE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_APPROVE_SUCCESS');
				Session::flash('message',$msg);
				
			}
			if(Input::get('status') == 2) //Delete
			{
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				
			}
			elseif(Input::get('status') == 0)   //block
			{
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DISAPPROVE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DISAPPROVE_SUCCESS');
				Session::flash('message',$msg);
				
			}
		}
		
		/** view product review **/
		public function view_pro_review($id)
		{
			if(Session::has('merchantid'))
			{
				$select = "review_comments,review_rating";
				$details = get_details('gr_review',['comment_id' => base64_decode($id)],$select);
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW_REVIEW') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_VIEW_REVIEW');
				$id = base64_decode($id);
				return view('sitemerchant.review.view_review')->with('pagetitle',$page_title)->with(['detail'=>$details,'id' => $id]);
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** item review **/
		public function manage_item_review()
		{
			
			if(Session::has('merchantid') == 1)
			{
				$get_details = DB::table('gr_review')->select('pro_item_name','cus_fname','cus_email','review_comments','review_rating','review_status','comment_id','gr_product.pro_item_name','gr_product.pro_item_code','gr_store.st_store_name','review_type')
                ->leftjoin('gr_product','gr_review.proitem_id', '=', 'gr_product.pro_id')
                ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
                ->leftjoin('gr_store','gr_product.pro_store_id','gr_store.id')
                ->where('review_status','!=','2')
                ->where('review_type','=','item')
                ->where('gr_review.merchant_id','=',Session::get('merchantid'))
                ->paginate(10);
				
				//print_r($get_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_REVIEW_ITEM')) ? trans(Session::get('mer_lang_file').'.ADMIN_REVIEW_ITEM') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REVIEW_ITEM');
				//$id = '';
				return view('sitemerchant.review.manage_product_review')->with('pagetitle',$page_title)->with('all_details',$get_details);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
		
		/** store review ***/
		public function manage_store_review()
		{
			if(Session::has('merchantid') == 1)
			{
				$get_details = DB::table('gr_review')->select('cus_fname','cus_email','review_comments','review_rating','review_status','comment_id','gr_store.st_store_name','review_type','gr_merchant.mer_fname','gr_merchant.mer_lname')
                ->leftjoin('gr_store','gr_review.res_store_id', '=', 'gr_store.id')
                ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
                ->leftjoin('gr_merchant','gr_store.st_mer_id','gr_merchant.id')
                ->where('review_status','!=','2')
                ->where('review_type','=','store')
                ->where('gr_review.merchant_id','=',Session::get('merchantid'))
                ->paginate(10);
				
				//print_r($get_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_ST_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_ST_REVIEW') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_ST_REVIEW');
				//$id = '';
				return view('sitemerchant.review.manage_store_review')->with('pagetitle',$page_title)->with('all_details',$get_details);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		
		/** restaurant review **/
		public function manage_restaurant_review()
		{
			
			if(Session::has('merchantid') == 1)
			{
				$get_details = DB::table('gr_review')->select('cus_fname','cus_email','review_comments','review_rating','review_status','comment_id','gr_store.st_store_name','review_type','gr_merchant.mer_fname','gr_merchant.mer_lname')
                ->leftjoin('gr_store','gr_review.res_store_id', '=', 'gr_store.id')
                ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
                ->leftjoin('gr_merchant','gr_store.st_mer_id','gr_merchant.id')
                ->where('review_status','!=','2')
                ->where('review_type','=','restaurant')
                ->where('gr_review.merchant_id','=',Session::get('merchantid'))
                ->paginate(10);
				
				//print_r($get_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_RES_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_RES_REVIEW') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_RES_REVIEW');
				//$id = '';
				return view('sitemerchant.review.manage_restaurant_review')->with('pagetitle',$page_title)->with('all_details',$get_details);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
		/** restaurant review **/
		public function manage_order_review()
		{
			
			if(Session::has('merchantid') == 1)
			{
				$get_details = DB::table('gr_review')->select('cus_fname','review_comments','review_rating','review_status','comment_id','review_type','deliver_fname','agent_fname')
                ->leftjoin('gr_agent','gr_review.agent_id', '=', 'gr_agent.agent_id')
                ->leftjoin('gr_customer','gr_review.customer_id', '=', 'gr_customer.cus_id')
                ->leftjoin('gr_delivery_member','gr_review.delivery_id','gr_delivery_member.deliver_id')
                ->where('review_status','!=','2')
                ->where('review_type','=','order')
                ->where('gr_review.merchant_id','=',Session::get('merchantid'))
                ->paginate(10);
				
				//print_r($get_details); exit;
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_OR_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_OR_REVIEW') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_OR_REVIEW');
				//$id = '';
				return view('sitemerchant.review.manage_order_review')->with('pagetitle',$page_title)->with('all_details',$get_details);
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
	}	