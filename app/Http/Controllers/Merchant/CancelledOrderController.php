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
	use Mail;
	use View;
	use Lang;
	use Redirect;
	use App\MerchantReports;
	use App\Settings;
	use Excel;
	use Response;
	use File;
	use Image;
	
	
	class CancelledOrderController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
		}
		/*MERCHANTS LIST */
		public function cancelled_order_list()
		{
			if(Session::has('merchantid') == 0)
			{
				return redirect('merchant-login');
			}
			$ord_status	= Input::get('ord_status');
			//$from_date	= Input::get('from_date');
			$pagetitle = (Lang::has(Session::get('mer_lang_file').'.MER_CANCEL_PAYMENT')) ? trans(Session::get('mer_lang_file').'.MER_CANCEL_PAYMENT') : trans($this->MER_OUR_LANGUAGE.'.MER_CANCEL_PAYMENT');
			$commission_details = array();//MerchantReports::get_cancelled_order($ord_status);//NEED TO START HERE.
			return view('sitemerchant.reports.cancelled_order')->with('pagetitle',$pagetitle)->with('commission_details',$commission_details)->with('ord_status',$ord_status);
		}
		public function cancelled_orders_ajax(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{	
				$columns = array( 
				0=>'ord_id',
				1=>'cus_email',
				2=> 'ord_transaction_id',
				3=>'tot_cancel_withCommission',
				4=>'tot_admin_Commission',
				5=>'tot_cancel_withCommission',
				6=>'ord_cancel_paidamt',
				7=>'ord_cancel_paidamt',
				);
				$cusEmail_search = trim($request->cusEmail_search); 
				$orderId_search = trim($request->orderId_search); 
				$ordStatus_search = trim($request->ordStatus_search); 
				/*To get Total count */
				//DB::connection()->enableQueryLog();
				$q=array();
				$sql = DB::table('gr_order')
				->select('gr_order.ord_id',
				DB::Raw('SUM(gr_order.ord_grant_total) AS ord_grant_total'),
				DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS ord_cancel_paidamt'),
				DB::Raw('SUM(gr_order.ord_cancel_amt) AS tot_cancel_withCommission'),
				DB::Raw('SUM(gr_order.ord_admin_amt) AS tot_admin_Commission'))
				->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
				->join('gr_merchant_overallorder','gr_merchant_overallorder.or_mer_id', '=', 'gr_order.ord_merchant_id')
				->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
				->where('gr_order.ord_merchant_id','=',Session::get('merchantid'))
				->where('gr_order.ord_cancel_status','=','1');
				/*if($cusEmail_search!='')
					{
					$q=$sql->where('gr_customer.cus_email','like','%'.$cusEmail_search.'%');
					}
					if($orderId_search!='')
					{
					$q=$sql->where('gr_order.ord_transaction_id','like','%'.$orderId_search.'%');
					}
				*/
				$q=$sql->groupBy('gr_order.ord_transaction_id');
				$result = $q->get();
				$totalData  = count($result);
				/*$query = DB::getQueryLog();
					print_r($query);
				exit;*/
				//$totalData = 
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				
				//$posts = DB::table('gr_category')->where('cate_type','2')->where('cate_status','!=','2')->orderBy($order,$dir)->skip($start)->take($limit)->get();
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
				->join('gr_merchant_overallorder','gr_merchant_overallorder.or_mer_id', '=', 'gr_order.ord_merchant_id')
				->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
				->where('gr_order.ord_merchant_id','=',Session::get('merchantid'))
				->where('gr_order.ord_cancel_status','=','1');
				if($cusEmail_search!='')
				{
					$sql->where('gr_customer.cus_email','like','%'.$cusEmail_search.'%');
				}
				if($orderId_search!='')
				{
					$q=$sql->where('gr_order.ord_transaction_id','like','%'.$orderId_search.'%');
				}
				/*if($ordStatus_search == 'Paid') {
					$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)<=0');
					}
					elseif($ordStatus_search == 'Unpaid') {
					$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)>0');
				}*/
				$sql->groupBy('gr_order.ord_transaction_id');
				$totalFiltered = $sql->count();
				$sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts = $sql->get();
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
						
						
						$get_total_order_count = get_total_order_count($post->ord_transaction_id);
						$get_total_cancelled_count = get_total_cancelled_count($post->ord_transaction_id);
						
						if($get_total_cancelled_count==$get_total_order_count)
						{
							$cancellation_amt1 = $post->tot_cancel_withCommission+$post->ord_delivery_fee;
							} else { 
							$cancellation_amt1 = $post->tot_cancel_withCommission;
						}
						//$cancellation_amt1 = $post->tot_cancel_withCommission;
						$commission_amount = number_format($post->tot_admin_Commission,2); 
						if($post->ord_pay_type=='COD') 
						{
							$payableAmt = ' -- ';
							$paidAmt = ' -- ';
							$haveToPayAmount=' -- ';
							$lastCol = (Lang::has(Session::get('mer_lang_file').'.MER_COD_ORDER')) ? trans(Session::get('mer_lang_file').'.MER_COD_ORDER') : trans($this->MER_OUR_LANGUAGE.'.MER_COD_ORDER');
							} else {
							$payable_amount = $cancellation_amt1; 
							$payableAmt = number_format($payable_amount,2).' '.$post->ord_currency; 
							$paidAmt = number_format(($post->ord_cancel_paidamt),2).' '.$post->ord_currency;
							
							$haveToPayAmount = $payable_amount-$post->ord_cancel_paidamt;
							if(($haveToPayAmount) > 0 ) 
							{
								
								$lastCol = $haveToPayAmount;
							}
							else
							{
								$lastCol = (Lang::has(Session::get('mer_lang_file').'.ADMIN_NO_BALANCE_TO_PAY')) ? trans(Session::get('mer_lang_file').'.ADMIN_NO_BALANCE_TO_PAY') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_NO_BALANCE_TO_PAY');
							}
						}
						
						
						$viewText=(Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_VIEW');
						$orderDetText = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ORDER_DETAILS');
						
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['custEmail'] = $post->cus_email;
						$nestedData['orderId'] = '<a href="'.url('').'/mer-order-details/'.base64_encode($post->ord_transaction_id).'" target="_blank" data-toggle="tooltip" data-placement="right" title="'.$viewText.' '.$orderDetText.'" class="tooltip-demo">'. $post->ord_transaction_id.'</a>';
						$nestedData['cancelAmt'] = number_format($post->tot_cancel_withCommission,2).' '.$post->ord_currency;
						$nestedData['commission'] = $commission_amount.' '.$post->ord_currency;//
						$nestedData['payableAmt'] = $payableAmt;
						$nestedData['paidAmt'] = $paidAmt;//$added_by;
						$nestedData['balanceAmt'] = $lastCol;//$added_by;
						$data[] = $nestedData;
						
					}
				}
				
				$json_data = array(
				"draw"            => intval($request->input('draw')),  
				"recordsTotal"    => intval($totalData),  
				"recordsFiltered" => intval($totalFiltered), 
				"data"            => $data   
				);
				
				echo json_encode($json_data); 
			}
			else
			{
				return redirect('merchant-login');
			}
		}
		///commission_view_transaction
		
		
		/** send payment to merchant **/
		public function pay_to_customer(Request $request)
		{
			/*print_r($_POST); exit;Array ( [_token] => cOZ8cOY5DMFbRjqFjga5JzHKmJxxsP3Sr3SM14bp [ord_cancel_paidamt] => 5.05 [ord_cancelpaid_transid] => test [ord_id] => 25*/
			$insertArr = ['ord_cancel_paidamt'=> $request->ord_cancel_paidamt,'ord_cancelpaid_transid' 	=> $request->ord_cancelpaid_transid,'cancel_paid_date'=>date('Y-m-d H:i:s')];
			DB::table('gr_order')->where('ord_id',$request->ord_id)->update($insertArr);
            $msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAY_PAID_SUXUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAY_PAID_SUXUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PAY_PAID_SUXUS');
            Session::flash('message',$msg);
            return Redirect::to('manage-cancelled-order');
		}
	}		