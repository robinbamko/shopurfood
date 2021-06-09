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
	
	use App\Admin;
	
	use Response;
	
	
	class CmsnTrackController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		/*MERCHANTS LIST */
		public function commision_list()
		{
			if(Session::has('admin_id') == 0)
			{
				return redirect('admin-login');
			}
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION_TRACKING')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION_TRACKING') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_COMMISSION_TRACKING');
			$commission_details = array();//Admin::get_commission_list();
			return view('Admin.reports.Manage_Commission')->with('pagetitle',$pagetitle)->with('commission_list',$commission_details);
		}
		public function ajax_commision_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array( 
				0 =>'mer_email', 
				1 =>'mer_email', 
				2 =>'mer_fname',
				3=> 'st_store_name',
				4=> 'or_mer_amt',
				5=> 'or_admin_amt',
				6=> 'or_coupon_amt',
				7=> 'or_cancel_amt',
				8=> 'or_cancel_amt',
				9=> 'or_cancel_amt',
				10=> 'or_cancel_amt',
				11=> 'or_cancel_amt'
				);
				/*To get Total count */
				
				$totalData = DB::table('gr_merchant_overallorder')
				->select('or_admin_amt',
				'or_coupon_amt',
				'or_cancel_amt',
				'or_mer_amt',
				'mer_fname',
				'mer_email',
				'gr_store.st_store_name as st_name',
				'or_mer_id',
				'gr_merchant.mer_currency_code'
				)
				->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id')
				->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
				->where('gr_merchant.mer_status','<>','2')
				->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$draw = $request->input('draw');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$merEmail_search = trim($request->merEmail_search); 
				$merName_search = trim($request->merName_search); 
				$storename_search = trim($request->storename_search); 
				if($merEmail_search=='' && $merName_search=='' && $storename_search=='')
				{    
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_merchant_overallorder')
					->select('or_admin_amt',
					'or_coupon_amt',
					'or_cancel_amt',
					'or_mer_amt',
					'mer_fname',
					'mer_lname',
					'mer_email',
					'gr_store.st_store_name as st_name',
					'or_mer_id',
					'gr_merchant.mer_currency_code',
					'gr_merchant.mer_paymaya_clientid',
					'gr_merchant.mer_paymaya_secretid',
					'gr_merchant.mer_paymaya_status',
					'gr_merchant.mer_bank_accno',
					'gr_merchant.mer_bank_name',
					'gr_merchant.mer_branch',
					'gr_merchant.mer_ifsc',
					'gr_merchant.mer_netbank_status'
					)
					->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id')
					->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
					->where('gr_merchant.mer_status','<>','2')
					->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					
					$sql = 	DB::table('gr_merchant_overallorder')
					->select('or_admin_amt',
					'or_coupon_amt',
					'or_cancel_amt',
					'or_mer_amt',
					'mer_fname',
					'mer_lname',
					'mer_email',
					'gr_store.st_store_name as st_name',
					'or_mer_id',
					'gr_merchant.mer_currency_code',
					'gr_merchant.mer_paymaya_clientid',
					'gr_merchant.mer_paymaya_secretid',
					'gr_merchant.mer_paymaya_status',
					'gr_merchant.mer_bank_accno',
					'gr_merchant.mer_bank_name',
					'gr_merchant.mer_branch',
					'gr_merchant.mer_ifsc',
					'gr_merchant.mer_netbank_status',
					'gr_merchant.mer_status'
					);
					if($merEmail_search != '')
					{
						$q = $sql->where('gr_merchant.mer_email','like','%'.$merEmail_search.'%'); 
					}
					if($merName_search != '')
					{
						$q = $sql->whereRaw("CONCAT(if(mer_fname is null,'',mer_fname),' ',if(mer_lname is null,'',mer_lname)) like '%".$merName_search."%'"); 
					}
					if($storename_search != '')
					{
						$q = $sql->where('gr_store.st_store_name','like','%'.$storename_search.'%'); 
					}
					
					$q = $sql->Join('gr_merchant','gr_merchant_overallorder.or_mer_id','=','gr_merchant.id');
					$q = $sql->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id');
					$q = $sql->where('gr_merchant.mer_status','<>','2');
					//DB::connection()->enableQueryLog();
					$totalFiltered = $q->count();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					
					
				}
				$viewText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($this->ADMIN_LANGUAGE.'.ADMIN_VIEW');
				$payOfflineText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_OFFLINE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_OFFLINE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_OFFLINE');
				$payRequestText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYREQ_FROM_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYREQ_FROM_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYREQ_FROM_MERCHANT');
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
						$paid_commission = get_paid_commission($post->or_mer_id); 
						//$balance = $post->or_mer_amt - $paid_commission - $post->or_cancel_amt - $post->or_admin_amt;
						$balance = $post->or_mer_amt - $paid_commission - $post->or_cancel_amt;
						$request = pay_request_notification($post->or_mer_id);
						
						if($request > 0)
						{
							$request_count = '<span class="badge bg-danger tooltip-demo" aria-hidden="true" title="'.$payRequestText.'">'.$request.'</span>';
						}
						else
						{
							$request_count='';
						}
						$pmtStatus = '';
						if(number_format($balance,2) > 0)
						{
							$paymayaForm='';
							if($post->mer_paymaya_status=='Publish')
							{
								$btn_text = 
								$paymayaForm='
								<form method="post" action="'.url('').'/paymaya-commission_payment" id="validate_form">
								<input name="_token" type="hidden" value="'.csrf_token().'">
								<input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.$balance.'">
								<input name="client_id" id="client_id" type="hidden" value="'.$post->mer_paymaya_clientid.'">
								<input name="secret_id" id="secret_id" type="hidden" value="'.$post->mer_paymaya_secretid.'">
								<input name="merchant_id" id="merchant_id" type="hidden" value="'.$post->or_mer_id.'">
								<button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-xs tooltip-demo" title='.__(Session::get('admin_lang_file').'.ADMIN_PAYTHRU').' '.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'">'.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'</button>
								</form>
								';
								
							}
							if($post->mer_netbank_status=='Publish')
							{	
								$bank_details = "'".$post->mer_bank_accno.'`'.$post->mer_bank_name.'`'.$post->mer_branch.'`'.$post->mer_ifsc."'";
								$paymayaForm .= '<a href="javascript:;"  class="btn btn-success btn-sm" onclick="payFun('.$post->or_mer_id.','.$balance.',\''.$post->mer_currency_code.'\','.$bank_details.')">'.$payOfflineText.'</a>';
							}
							if($post->mer_netbank_status=='Unpublish' && $post->mer_paymaya_status=='Unpublish')
							{
								$paymayaForm .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_DET_NOT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_DET_NOT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_DET_NOT');
							}
							$pmtStatus .= $paymayaForm;
						}
						else
						{
							$pmtStatus = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_BALANCE_TO_PAY')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_BALANCE_TO_PAY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_NO_BALANCE_TO_PAY');
						}		
						//CHECKING TRANSACTION IS DONE
						$check_transaction = DB::table('gr_merchant_commission')->where('commission_mer_id',$post->or_mer_id)->count();
						if($check_transaction > 0)
						{
							$view_transaxn = '<a href="'.url('').'/commission_view_transaction/'.$post->or_mer_id.'" class="btn btn-warning btn-sm">'.$viewText.'</a>';
						}
						else
						{
							$view_transaxn = '-';
						}
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['merEmail'] = $post->mer_email.' '.$request_count;
						$nestedData['merName'] = $post->mer_fname.' '.$post->mer_lname;
						$nestedData['storeName'] = $post->st_name;
						$nestedData['totMerAmt'] = number_format($post->or_mer_amt,2);
						$nestedData['adminComnAmt'] = number_format($post->or_admin_amt,2);
						$nestedData['walletAmt'] = number_format($post->or_coupon_amt,2);
						$nestedData['cancelAmt'] = number_format($post->or_cancel_amt,2);
						$nestedData['paidAmt'] = number_format($paid_commission,2);
						$nestedData['balAmt'] = number_format($balance,2);
						$nestedData['pmtStatus'] = $pmtStatus;
						$nestedData['action'] = $view_transaxn;
						$data[] = $nestedData;
					}
				}
				//print_r($request->input('length')); exit;
				$json_data = array(
				"draw"            => intval($draw),  
				"recordsTotal"    => intval($totalData),  
				"recordsFiltered" => intval($totalFiltered), 
				"data"            => $data   
				);
				
				echo json_encode($json_data); 
			}
		}
		///commission_view_transaction
		
		public function commission_view_transaction($vendor_id)
		{
			if(Session::has('admin_id') == 0) {
				return redirect('admin-login');
			}
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_COMM_TRANSACTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMM_TRANSACTION') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_COMM_TRANSACTION');
			//DB::enableQueryLog();
			$commission_list = DB::table('gr_merchant_commission')
			->select(
			'gr_merchant_commission.mer_commission_id',
			'gr_merchant_commission.commission_paid',
			'gr_merchant.mer_email',
			'gr_merchant_commission.commission_date',
			'gr_merchant_commission.commission_currency',
			'gr_merchant_commission.mer_transaction_id',
			'gr_merchant.mer_fname',
			'gr_merchant.mer_lname',
			'gr_merchant_commission.pay_type')
			->join('gr_merchant','gr_merchant.id','=','gr_merchant_commission.commission_mer_id')
			->where('gr_merchant_commission.commission_mer_id','=',$vendor_id)->orderby('gr_merchant_commission.commission_date','DESC')->get();
			//print_r($commission_list); exit;
			//print_r(DB::getQueryLog($commission_list));
			return view('Admin.reports.View_Commission_transaction')->with('pagetitle',$pagetitle)->with('commission_list',$commission_list);
		}
		
		
		/** send payment to merchant **/
		public function pay_to_merchant(Request $request)
		{
			$insertArr = ['commision_admin_id' 		=> '1',
			'commission_mer_id' 	=> $request->mer_id,
			'commission_paid'	 	=> $request->mer_balance,
			'commission_currency'	=> $request->mer_curr,
			'mer_commission_status'	=> '2',
			'pay_type'				=> 1,
			'mer_transaction_id' 	=> $request->trans_id,
			'commission_date'		=> date('Y-m-d H:i:s')];
			$insert = insertvalues('gr_merchant_commission',$insertArr);
			/**delete notification **/
			DB::table('gr_notification')->where(['no_status' => '1','no_mer_id' => $request->mer_id])->delete();
			Session::flash('message',"Payment Paid Successfully");
			return Redirect::to('admin-commission-tracking');
		}
	}	