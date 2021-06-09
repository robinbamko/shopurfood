<?php
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Admin;
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Auth;
	use Validator;
	use Session;
	use Mail;
	use View;
	use Lang;
	use Redirect;
	use App\Reports;
	use App\Settings;
	use Excel;
	use Response;
	use File;
	use Image;
	
	class OrderMgmtController extends Controller
	{
		
		public function __construct(){
			parent::__construct();
			// set admin Panel language
			$this->setAdminLanguage();
		}
		
		public function deals_all_orders()
		{
			if(Session::has('admin_id') == 1)
			{
				$from_date	= Input::get('from_date');
				$to_date   	= Input::get('to_date');
				$ord_status = Input::get('ord_status');
				
				$order_id = Input::get('order_id');
				$orderdetails   = Reports::getall_dealreports($from_date, $to_date,$ord_status,$order_id);
				
				
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_MGMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_MGMT');
				return view('Admin.reports.manage_orders')->with('pagetitle',$page_title)->with('orderdetails',$orderdetails)->with('from_date',$from_date)->with('to_date',$to_date)->with('ord_status',$ord_status);
				} else {
				return Redirect::to('admin-login');
			}
		}
		public function order_tracking_design()
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_MGMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_MGMT');
			return view('Admin.reports.order-tracking')->with('pagetitle',$page_title);
		}
		public function InvoiceOrder($id)
		{
			$id=base64_decode($id);
			//DB::connection()->enableQueryLog();
			DB::table('gr_order')->where('ord_transaction_id', $id)->update(['ord_admin_viewed' => '1']);
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVOICE_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVOICE_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_INVOICE_DETAILS');
			$allchoices = array();
			
			$Invoice_Order = DB::table('gr_order')
			->select('gr_order.ord_id',
			'gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			'gr_store.st_store_name',
			'gr_store.st_address',
			'gr_product.pro_item_name',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_pay_type',
			'gr_order.ord_date',
			'gr_order.ord_transaction_id',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_had_choices',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_customer.cus_address',
			'gr_customer.cus_phone1',
			'gr_customer.cus_email',
			'gr_order.ord_wallet',
			'gr_order.ord_self_pickup',
			'gr_order.ord_spl_req'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$id)->get();
			
			/* Start-Sathyaseelan getting choices */
			if(count($Invoice_Order)>0)
			{
				foreach($Invoice_Order as $orders)
				{
					$choices = array();
					$splitted_choice=json_decode($orders->ord_choices,true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choices[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								$choices[$choices_name]=$choice['choice_price'];
							}
						}
					}
					$allchoices[$orders->ord_id] = $choices;
					
				}
			}
			
			
			$storewise_details   = Reports::track_reports($id);
			return view ('Admin.reports.InvoiceOrder')->with('Invoice_Order',$Invoice_Order)->with('choices',$allchoices)->with('pagetitle',$pagetitle)->with('storewise_details',$storewise_details);
		}
		public function TrackOrder($id)
		{
			$id=base64_decode($id);
			DB::table('gr_order')->where('ord_transaction_id', $id)->update(['ord_admin_viewed' => '1']);
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_TRACK_ORDER')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRACK_ORDER') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_TRACK_ORDER');
			$customer_details = DB::table('gr_order')->select('ord_transaction_id','ord_date','ord_pre_order_date','ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail')->where('ord_transaction_id',$id)->first();
			//print_r($customer_details); exit;
			$storewise_details   = Reports::track_reports($id);
			
			return view ('Admin.reports.order-tracking')->with('storewise_details',$storewise_details)->with('customer_details',$customer_details)->with('pagetitle',$pagetitle);
		}
		public function rejectStatus(Request $request)
		{
			/*echo '<pre>';
				print_r($request->all());
			exit;*/
			DB::table('gr_order')->where('ord_transaction_id', $request->orderId)->update(['ord_status' => '3','ord_reject_reason'=>$request->reason]);
			
		}
		public function changeStatus(Request $request)
		{
			DB::table('gr_order')->where('ord_transaction_id', $request->id)->update(['ord_status' => 3]);
		}
	}
