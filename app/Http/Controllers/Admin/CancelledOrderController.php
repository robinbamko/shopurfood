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
	
	use App\Reports;
	
	use Response;
	
	
	class CancelledOrderController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		/*MERCHANTS LIST */
		public function cancelled_order_list()
		{
			if(Session::has('admin_id') == 0)
			{
				return redirect('admin-login');
			}
			$ord_status	= Input::get('ord_status');
			//$from_date	= Input::get('from_date');
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_PAYMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_PAYMENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_PAYMENT');
			$commission_details = array();//Admin::get_cancelled_order($ord_status);//NEED TO START HERE.
			//print_r($commission_details); exit;
			return view('Admin.reports.cancelled_order')->with('pagetitle',$pagetitle)->with('commission_details',$commission_details)->with('ord_status',$ord_status);
		}
		
		///commission_view_transaction
		
		public function cancelled_orders_ajax(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$columns = array(
                0=>'ord_id',
                1=>'cus_email',
                2=> 'ord_transaction_id',
                3=> 'pro_item_name',
                4=>'tot_cancel_withCommission',
                5=>'tot_admin_Commission',
                6=>'tot_cancel_withCommission',
                7=>'ord_cancel_paidamt',
                8=>'ord_cancel_paidamt',
				);
				$cusEmail_search = trim($request->cusEmail_search);
				$orderId_search = trim($request->orderId_search);
				$pdtName_search = trim($request->pdtName_search);
				$ordStatus_search = trim($request->ordStatus_search);
				/*To get Total count */
				//DB::connection()->enableQueryLog();
				$q=array();
				$sql = DB::table('gr_order')
                ->select('gr_order.ord_id',
				DB::Raw('SUM(gr_order.ord_grant_total) AS ord_grant_total'),
				DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS ord_cancel_paidamt'),
				DB::Raw('SUM(gr_order.ord_cancel_amt) AS tot_cancel_withCommission'),
				DB::Raw('SUM(gr_order.ord_admin_amt) AS tot_admin_Commission'),'gr_product.pro_item_name','gr_order.ord_choices','gr_order.ord_had_choices','gr_order.add_delfee_status'
                )
                ->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
                ->join('gr_merchant_overallorder','gr_merchant_overallorder.or_mer_id', '=', 'gr_order.ord_merchant_id')
                ->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
                ->where('gr_order.ord_cancel_status','=','1');
				
				//$q=$sql->groupBy('gr_order.ord_transaction_id');
				$q=$sql;
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
				'gr_order.ord_grant_total AS ord_grant_total',
				'gr_order.ord_cancel_paidamt AS ord_cancel_paidamt',
				'gr_order.ord_cancel_amt AS tot_cancel_withCommission',
				'gr_order.ord_admin_amt AS tot_admin_Commission',
				// DB::Raw('SUM(gr_order.ord_grant_total) AS ord_grant_total'),
				// DB::Raw('SUM(gr_order.ord_cancel_paidamt) AS ord_cancel_paidamt'),
				// DB::Raw('SUM(gr_order.ord_cancel_amt) AS tot_cancel_withCommission'),
				// DB::Raw('SUM(gr_order.ord_admin_amt) AS tot_admin_Commission'),
				'gr_order.ord_cancel_reason',
				'gr_order.ord_cancel_date',
				'gr_order.ord_pay_type',
				'gr_order.ord_delivery_fee',
				'gr_order.ord_merchant_id',
				//DB::Raw('gr_merchant_overallorder.or_cancel_amt AS tot_cancel_withCommission'),
				'gr_order.add_delfee_status',
				'gr_order.ord_status',
				'gr_customer.cus_id',
				'gr_customer.cus_paymaya_clientid',
				'gr_customer.cus_paymaya_secretid',
				'gr_customer.cus_paymaya_status',
				'gr_customer.cus_netbank_status',
				'gr_customer.cus_bank_accno',
				'gr_customer.cus_bank_name',
				'gr_customer.cus_branch',
				'gr_customer.cus_ifsc',
				'gr_product.pro_item_name',
				'gr_order.ord_choices',
				'gr_order.ord_had_choices',
				'gr_order.add_delfee_status'
                )
                ->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
                ->join('gr_merchant_overallorder','gr_merchant_overallorder.or_mer_id', '=', 'gr_order.ord_merchant_id')
                ->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
                ->where('gr_order.ord_cancel_status','=','1');
				if($cusEmail_search!='')
				{
					$q = $sql->where('gr_customer.cus_email','like','%'.$cusEmail_search.'%');
				}
				if($orderId_search!='')
				{
					$q=$sql->where('gr_order.ord_transaction_id','like','%'.$orderId_search.'%');
				}
				if($pdtName_search!='')
				{
					$q=$sql->where('gr_product.pro_item_name','like','%'.$pdtName_search.'%');
				} 
				/*if($ordStatus_search == 'Paid') {
					$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)<=0');
					}
					elseif($ordStatus_search == 'Unpaid') {
					$sql->whereRaw('(gr_order.ord_grant_total-gr_order.ord_cancel_paidamt)>0');
				}*/
				//$sql->groupBy('gr_order.ord_transaction_id');
				$totalFiltered = $sql->count();
				$sql->orderBy($order,$dir)->skip($start)->take($limit);
				$posts = $sql->get();
				//SATHYA
				/*$q = $sql->orderby('gr_order.ord_cancel_date','desc')->get();
					$posts = $q;
					$totalFiltered = $q->count();
					$sql->orderBy($order,$dir)->skip($start)->take($limit);
				*/
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
						$get_total_order_count = get_total_order_count($post->ord_transaction_id);
						$get_total_cancelled_count = get_total_cancelled_count($post->ord_transaction_id);
						
						if(($get_total_cancelled_count==$get_total_order_count) && ($post->add_delfee_status == '1'))
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
							$lastCol = (Lang::has(Session::get('admin_lang_file').'.ADMIN_COD_ORDER')) ? trans(Session::get('admin_lang_file').'.ADMIN_COD_ORDER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COD_ORDER');
							} else {
							$payable_amount = $cancellation_amt1;
							$payableAmt = number_format($payable_amount,2).' '.$post->ord_currency;
							$paidAmt = number_format(($post->ord_cancel_paidamt),2).' '.$post->ord_currency;
							
							$haveToPayAmount = $payable_amount-$post->ord_cancel_paidamt;
							if(($haveToPayAmount) > 0 )
							{
								$paymayaForm='';
								if($post->cus_paymaya_status=='Publish')
								{
									/*$paymayaForm='
									<!--form method="post" action="'.url('').'/paymaya-cancel-payment" id="validate_form"-->
									<form method="post" action="'.url('').'/paypal-email-cancel-payment" id="validate_form">
									<input name="_token" type="hidden" value="'.csrf_token().'">
									<input name="amt_to_pay" id="amt_to_pay" type="hidden" value="'.$haveToPayAmount.'">
									<input name="client_id" id="client_id" type="hidden" value="'.$post->cus_paymaya_clientid.'">
									<input name="secret_id" id="secret_id" type="hidden" value="'.$post->cus_paymaya_secretid.'">
									<input name="customer_id" id="customer_id" type="hidden" value="'.$post->cus_id.'">
									<input name="order_id" id="order_id" type="hidden" value="'.$post->ord_id.'">
									<button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.__(Session::get('admin_lang_file').'.ADMIN_PAYTHRU').' '.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'">'.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'</button>
									</form>&nbsp;
									';*/

									if(Session::get('default_currency_code')=='USD')
									{
										$converted_amount = $haveToPayAmount;
									} else {
										$converted_amount = convertCurrency(Session::get('default_currency_code'),'USD',$haveToPayAmount);
									}
									$paymayaForm='
									<form action="'.url('').'/paypal-details-refund" method="post" name="frmTransaction" id="frmTransaction">
									<input name="_token" type="hidden" value="'.csrf_token().'">
									<input type="hidden" name="business" value="'.$post->cus_paymaya_clientid.'">
								   <input type="hidden" name="ord_id" value="'.$post->ord_id.'">
								    <input type="hidden" name="client_id" value="'.$post->cus_paymaya_clientid.'">
								   <input type="hidden" name="amount" value="'.$converted_amount.'">   
								  
								   <button type="submit" id = "checkout_btn" data-placement="left" class="btn btn-success btn-sm tooltip-demo" title="'.__(Session::get('admin_lang_file').'.ADMIN_PAYTHRU').' '.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'" >'.__(Session::get('admin_lang_file').'.ADMIN_PAYMAYA').'</button>
									</form>&nbsp;
									';
									
								}
								if($post->cus_netbank_status == "Publish")
								{
									$payOfflineText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_OFFLINE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_OFFLINE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_OFFLINE');
									$bank_details = "'".$post->cus_bank_accno.'`'.$post->cus_bank_name.'`'.$post->cus_branch.'`'.$post->cus_ifsc."'";
									$paymayaForm .='<a href="javascript:call_pay_fun('.$post->ord_id.','.$haveToPayAmount.','.$bank_details.');" class="btn btn-success btn-sm" >'.$payOfflineText.'</a>';
								}
								if($post->cus_netbank_status == 'Unpublish' && $post->cus_paymaya_status == 'Unpublish')
								{
									$walletText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TO_WALLET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TO_WALLET') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_TO_WALLET');
									$result = str_replace(':amt',Session::get('default_currency_symbol'). $haveToPayAmount,$walletText);
									$paymayaForm .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_USER_WALLET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_USER_WALLET') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_USER_WALLET');
									$paymayaForm .='<br><a href="javascript:call_wallet_fun('.$post->ord_id.','.$post->cus_id.','.$haveToPayAmount.',\''.$post->ord_transaction_id.'\');" class="btn btn-success btn-sm" >'.$result.'</a>';
								}
								//$payOnlineText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_ONLINE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_ONLINE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_ONLINE');
								
								$lastCol = $paymayaForm;
							}
							else
							{
								$lastCol = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_BALANCE_TO_PAY')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_BALANCE_TO_PAY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_NO_BALANCE_TO_PAY');
							}
						}
						$choices = array();
						$choices_text  = '';
						/* get choices */
						if($post->ord_had_choices == 'Yes')
						{
							$ch_array = json_decode($post->ord_choices,true);
							if(count($ch_array) > 0)
							{
								$includes_text = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INCLUDES')) ? trans(Session::get('admin_lang_file').'.ADMIN_INCLUDES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INCLUDES');
								//$choices .= ' : ';
								foreach($ch_array as $arr)
								{
									$choices_name=DB::table("gr_choices")->where("ch_id","=",$arr['choice_id'])->first()->ch_name;
									array_push($choices,$choices_name);
								}
								$choices_text = '<strong>'.$includes_text.'</strong><br>'.implode(',',$choices);
							}
						}
						
						$viewText=(Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW') : trans($this->ADMIN_LANGUAGE.'.ADMIN_VIEW');
						$orderDetText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_DETAILS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_DETAILS');
						
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['custEmail'] = $post->cus_email;
						$nestedData['orderId'] = '<a href="'.url('').'/admin-track-order/'.base64_encode($post->ord_transaction_id).'" target="_blank" data-toggle="tooltip" data-placement="right" title="'.$viewText.' '.$orderDetText.'" class="tooltip-demo">'. $post->ord_transaction_id.'</a>';
						$nestedData['pdtName'] 	= ucfirst($post->pro_item_name).'<br>'.$choices_text;
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
		}
		/** send payment to merchant **/
		public function pay_to_customer(Request $request)
		{
			/*print_r($_POST); exit;Array ( [_token] => cOZ8cOY5DMFbRjqFjga5JzHKmJxxsP3Sr3SM14bp [ord_cancel_paidamt] => 5.05 [ord_cancelpaid_transid] => test [ord_id] => 25*/
			$insertArr = ['ord_cancel_paidamt'			=> $request->ord_cancel_paidamt,
            'ord_cancelpaid_transid' 	=> $request->ord_cancelpaid_transid,
            'cancel_paid_date'			=>date('Y-m-d H:i:s'),
            'ord_cancel_paytype' 		=> $request->ord_cancel_paytype,
            'ord_cancel_payment_status'	=>'1'
			];
			DB::table('gr_order')->where('ord_id',$request->ord_id)->update($insertArr);
			/* send mail to customer */
			$get_order_details = Reports::get_cancel_or_details($request->ord_id);
			if(!empty($get_order_details))
			{
				$send_mail_data = array('order_details'		=> $get_order_details,
                'transaction_id' =>$get_order_details->ord_transaction_id );
				$mail = $get_order_details->order_ship_mail;
				Mail::send('email.refund_mail', $send_mail_data, function($message) use($mail)
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_OR_REFUNDED');
					$message->to($mail)->subject($msg);
				});
			}
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_PAID_SUXUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_PAID_SUXUS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_PAID_SUXUS');
			Session::flash('message',$msg);
			return Redirect::to('manage-cancelled-order');
		}

		/* add cancellation amount to user wallet */
		public function refund_to_wallet(Request $request)
		{
			/*print_r($_POST); exit;Array ( [_token] => cOZ8cOY5DMFbRjqFjga5JzHKmJxxsP3Sr3SM14bp [ord_cancel_paidamt] => 5.05 [ord_cancelpaid_transid] => test [ord_id] => 25*/
			$user_id = $request->cus_id;
			$amount = $request->amount;
			/* add to wallet */
			$updat = updatevalues('gr_customer',['cus_wallet' => DB::Raw('cus_wallet+'.$amount)],['cus_id' => $user_id]);
			/* update in cancellation payment */
			$insertArr = ['ord_cancel_paidamt'			=> $request->amount,
			            'ord_cancelpaid_transid' 	=> "Wallet-".rand(),
			            'cancel_paid_date'			=>date('Y-m-d H:i:s'),
			            'ord_cancel_paytype' 		=> "Wallet",
			            'ord_cancel_payment_status'	=>'1'
						];
			DB::table('gr_order')->where('ord_id',$request->ord_id)->update($insertArr);
			/* update in refereal table */
			$msg = Session::get('SITENAME').": Refund Amount (Transaction Id : ".$request->trans_id.")";
			$insertArr = ['referral_id'			=> $user_id,
			            'referre_email' 		=> $msg,
			            're_offer_amt'			=> $amount,
			            're_currency' 			=> Session::get('default_currency_symbol'),
			            ];
			DB::table('gr_referal')->insert($insertArr);
			/* send mail to customer */
			$get_order_details = Reports::get_cancel_or_details($request->ord_id);
			if(!empty($get_order_details))
			{
				$send_mail_data = array('order_details'		=> $get_order_details,
                'transaction_id' =>$get_order_details->ord_transaction_id );
				$mail = $get_order_details->order_ship_mail;
				Mail::send('email.refund_mail', $send_mail_data, function($message) use($mail)
				{
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REFUNDED') : trans($this->ADMIN_LANGUAGE.'.ADMIN_OR_REFUNDED');
					$message->to($mail)->subject($msg);
				});
			}
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_PAID_SUXUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_PAID_SUXUS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_PAID_SUXUS');
			//Session::flash('message',$msg);
			echo $msg; exit;
			//return Redirect::to('manage-cancelled-order');
		}

	}	