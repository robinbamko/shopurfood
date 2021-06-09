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
	use App\Item;
	use App\Reports;
	use Carbon\Carbon;
	use Excel;
	class ReportController extends Controller
	{
		
		public function __construct()
		{	
			parent::__construct();
			$this->setAdminLanguage();
			
		}
		public function order_report(){
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REPORTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_OR_REPORTS');
			$restaurants = Item::get_activestore_withmerch('1');
			$rest_array = array();
			if(count($restaurants) > 0 )
			{
				//$rest_array[0]='-- All Restaurant --';
				foreach($restaurants as $restar)
				{
					$rest_array[$restar->id]=$restar->st_store_name.' - '.$restar->mer_email;
				}
			}
			$restaurant_list = $rest_array;
			$delBoyArray = array();
			$delBoyList = DB::table('gr_delivery_member')->select('deliver_id','deliver_fname','deliver_lname','deliver_email')->where('deliver_status','<>','2')->get();
			if(count($delBoyList) > 0 )
			{
				//$rest_array[0]='-- All Restaurant --';
				foreach($delBoyList as $delBoy)
				{
					$delBoyArray[$delBoy->deliver_id]=$delBoy->deliver_fname.' '.$delBoy->deliver_lname.' - '.$delBoy->deliver_email;
				}
			}
			return view('Admin.consolidate_reports.order_report')->with(['pagetitle'=>$page_title,'restaurant_list'=>$restaurant_list,'delBoyList'=>$delBoyArray]);
		}
		
		public function download_order_report(Request $request){
			/*print_r($request->all());
			exit;Array ( [_token] => MasWKp1AUna0jFghUuyO37rB4WmqTiiWHnl8e2Fx [pro_store_id] => Array ( [0] => 6 ) [from_date] => [to_date] => [payment_method] => Array ( [0] => COD [1] => PAYPAL ) [ord_status] => Array ( [0] => 2 [1] => 3 ) )*/
			//echo '<pre>'; print_r($get_order_details);
			$type='xls';
			if(Session::has('admin_id') == 1)
			{
				$store_id = $request->pro_store_id;
				$from_date = $request->from_date;
				$to_date = $request->to_date;
				$payment_method = $request->payment_method;
				$ord_status = $request->ord_status;
				$deliver_id = $request->deliver_id;
				$data = Reports::order_report_xl($store_id,$from_date,$to_date,$payment_method,$ord_status,$deliver_id);
				if(count($data) > 0 ){
					
					return Excel::create('Order Report',function ($excel) use ($data)
					{
						$excel->sheet('Order Report', function ($sheet) use ($data)
						{
							$order_status_array = order_status_array('admin_lang_file',$this->ADMIN_LANGUAGE);
							$sheet->setFontFamily('Verdana');
							$sheet->mergeCells('A1:AB1');
							$sheet->cell('A1:AB1', function($cell) { $cell->setFontSize(12); $cell->setFontWeight('bold'); });
							$sheet->cell('A1', function($cell) {$cell->setValue('Order Report'); }); 
							$sheet->row(1, function($row) { $row->setBackground('#5cb85cc2'); });
							
							$sheet->cell('A2:AB2', function($cell) { $cell->setFontSize(11); $cell->setFontWeight('bold'); });
							$sheet->row(2, function($row) { $row->setBackground('#CCCCCC'); });
							
							$sheet->setFreeze('A3');
							$sheet->setFontSize('10');
							$sheet->setAutoSize(true); 
							/*HEADINGS */
							$sheet->cell('A2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO'));   });
							$sheet->cell('B2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CUSTOMER_NAME'));});
							
							$sheet->cell('C2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRANSACTION_ID'));});
							
							$sheet->cell('D2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_DATE'));});
							
							$sheet->cell('E2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PRE_ORDER_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRE_ORDER_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PRE_ORDER_DATE'));});
							
							$sheet->cell('F2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ITEM_CODE'));});
							
							$sheet->cell('G2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ITEM_NAME'));});
							
							$sheet->cell('H2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ITEM_QTY'));});
							
							$sheet->cell('I2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUNTRY_SYM'));});
							
							$sheet->cell('J2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOICES')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOICES') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CHOICES'));});
							
							$sheet->cell('K2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOICE_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOICE_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CHOICE_AMOUNT'));});
							
							$sheet->cell('L2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SPECIAL_REQUEST')) ? trans(Session::get('admin_lang_file').'.ADMIN_SPECIAL_REQUEST') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SPECIAL_REQUEST'));});
							
							$sheet->cell('M2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRICE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PRICE'));});
							
							$sheet->cell('N2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBTOTAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SUBTOTAL'));});
							
							$sheet->cell('O2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX_PERCENT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TAX_PERCENT'));});
							
							$sheet->cell('P2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TAX'));});
							
							$sheet->cell('Q2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_WALLET_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WALLET_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_WALLET_AMOUNT'));});
							
							$sheet->cell('R2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELIVERY_FEE'));});
							
							$sheet->cell('S2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TOTAL'));});
							
							$sheet->cell('T2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PMTMODE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PMTMODE'));});
							
							$sheet->cell('U2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STATUS'));});
							
							$sheet->cell('V2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COMMISSION'));});
							
							$sheet->cell('W2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELI_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELI_NAME'));});
							
							$sheet->cell('X2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_STATUS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CANCEL_STATUS'));});
							
							$sheet->cell('Y2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_PAYMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_PAYMENT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CANCEL_PAYMENT'));});
							
							$sheet->cell('Z2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CANCEL_DATE'));});
							
							$sheet->cell('AA2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_AXPTSTATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_AXPTSTATUS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELBOY_AXPTSTATUS'));});
							
							$sheet->cell('AB2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_AXPTD_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AXPTD_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_AXPTD_DATE'));});
							//$sheet->setColumnFormat(array('B' => '0','K' => '0.00','F' => '@','F' => 'yyyy-mm-dd'));
							$sheet->setColumnFormat(array('K' => '0.00','M' => '0.00','N' => '0.00','P' => '0.00','Q' => '0.00','R' => '0.00','S' => '0.00','V' => '0.00','Y' => '0.00'));
							$sheet->setWidth(array('K' => '30','M' => '30','N' => '30','P' => '30','Q' => '30','R' => '30','S' => '30','V' => '30','Y' => '30'));
							$j=3;
							$k=1;
							$grand_choice_amt = 0;
							$grand_price_amt = 0;
							$grand_subtot_amt = 0;
							$grand_tax_amt = 0;
							$grand_wallet_amt = 0;
							$grand_grandTot_amt = 0;
							$grand_commision_amt = 0;
							$grand_cancel_amt = 0;
							foreach($data as $key=>$gotRes){
								if(count($gotRes) > 0 ){
									$restNameText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RESTAURANT_NAME');
									$split_key = explode('`',$key);
									$sheet->mergeCells('A'.$j.':F'.$j);
									$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#b32927');});
									$sheet->cell('A'.$j, $restNameText.' : '.$split_key[0]);
									
									$merchantNameText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT_NAME');
									$sheet->mergeCells('G'.$j.':L'.$j);
									$sheet->cell('G'.$j.':L'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#b32927');});
									$sheet->cell('G'.$j, $merchantNameText.' :'.$split_key[1].' '.$split_key[2].' ( '.$split_key[3].' )');
									$j++;
									
									$glob_choice_amt = 0;
									$glob_price_amt = 0;
									$glob_subtot_amt = 0;
									$glob_tax_amt = 0;
									$glob_wallet_amt = 0;
									$glob_grandTot_amt = 0;
									$glob_commision_amt = 0;
									$glob_cancel_amt = 0;
									foreach($gotRes as $value){
										if($value->ord_pre_order_date === NULL) { $preOrder=''; } else{ $preOrder=date('m/d/Y H:i:s',strtotime($value->ord_pre_order_date)); }
										$choices = array();
										if($value->ord_had_choices=="Yes"){
											$splitted_choice=json_decode($value->ord_choices,true);
											if(!empty($splitted_choice))
											{
												foreach($splitted_choice as $choice)
												{
													if(!isset($choices[$choice['choice_id']]))
													{
														$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
														//$choices[$choices_name]=$choice['choice_price'];
														array_push($choices,$choices_name);
													}
												}
											}
										}
										if($value->ord_status > 0 && $value->ord_status <= 9){
											$ordStatus = $order_status_array[$value->ord_status];
										}
										if($value->ord_cancel_status=='1'){
											if($value->ord_status==3){
												$cancelStatus = '';
												}else{
												$ordStatus .=' ( Cancelled )';
												$cancelStatus = 'Cancelled';
											}
											} else{
											$cancelStatus = '-';
										}
										if($value->ord_cancel_date === NULL) { $cancelDate=''; } else{ $cancelDate=date('m/d/Y H:i:s',strtotime($value->ord_cancel_date)); }
										if($value->ord_delboy_accept_on === NULL) { $delBoyAxptDat=''; } else{ $delBoyAxptDat=date('m/d/Y H:i:s',strtotime($value->ord_delboy_accept_on)); }
										if($value->ord_delboy_act_status == '0') { $delBoyAxpt='-'; } elseif($value->ord_delboy_act_status == '1') { $delBoyAxpt='Accepted'; } else { $delBoyAxpt= 'Rejected'; }
										
										/*VALUES */
										$sheet->cell('A'.$j, $k++); //print serial no
										$sheet->cell('B'.$j, $value->cus_fname.' '.$value->cus_lname); 
										$sheet->cell('C'.$j, $value->ord_transaction_id); 
										$sheet->cell('D'.$j, date('m/d/Y H:i:s',strtotime($value->ord_date))); 
										$sheet->cell('E'.$j, $preOrder); 
										$sheet->cell('F'.$j, $value->pro_item_code); 
										$sheet->cell('G'.$j, $value->pro_item_name); 
										$sheet->cell('H'.$j, $value->ord_quantity); 
										$sheet->cell('I'.$j, $value->ord_currency); 
										$sheet->cell('J'.$j, implode(",",$choices)); 
										$sheet->cell('K'.$j, $value->ord_choice_amount); 
										$sheet->cell('L'.$j, $value->ord_spl_req); 
										$sheet->cell('M'.$j, $value->ord_unit_price); 
										$sheet->cell('N'.$j, $value->ord_sub_total); 
										$sheet->cell('O'.$j, $value->ord_tax_percent); 
										$sheet->cell('P'.$j, $value->ord_tax_amt); 
										$sheet->cell('Q'.$j, $value->ord_wallet); 
										$sheet->cell('R'.$j, $value->ord_delivery_fee); 
										$sheet->cell('S'.$j, $value->ord_grant_total); 
										$sheet->cell('T'.$j, $value->ord_pay_type); 
										$sheet->cell('U'.$j, $ordStatus); 
										$sheet->cell('V'.$j, $value->ord_admin_amt); 
										if($value->deliver_fname=='' && $value->deliver_email==''){
											$sheet->cell('W'.$j, '-'); 
											}else{
											$sheet->cell('W'.$j, $value->deliver_fname.' '.$value->deliver_lname.' ('.$value->deliver_email.')'); 
										}
										$sheet->cell('X'.$j, $cancelStatus); 
										$sheet->cell('Y'.$j, $value->ord_cancel_amt); 
										$sheet->cell('Z'.$j, $cancelDate); 
										$sheet->cell('AA'.$j, $delBoyAxpt); 
										$sheet->cell('AB'.$j, $delBoyAxptDat); 
										$glob_choice_amt += $value->ord_choice_amount;
										$glob_price_amt += $value->ord_unit_price;
										$glob_subtot_amt += $value->ord_sub_total;
										$glob_tax_amt += $value->ord_tax_amt;
										$glob_wallet_amt += $value->ord_wallet;
										$glob_grandTot_amt += $value->ord_grant_total;
										$glob_commision_amt += $value->ord_admin_amt;
										$glob_cancel_amt += $value->ord_cancel_amt;
										
										$grand_choice_amt += $value->ord_choice_amount;
										$grand_price_amt += $value->ord_unit_price;
										$grand_subtot_amt += $value->ord_sub_total;
										$grand_tax_amt += $value->ord_tax_amt;
										$grand_wallet_amt += $value->ord_wallet;
										$grand_grandTot_amt += $value->ord_grant_total;
										$grand_commision_amt += $value->ord_admin_amt;
										$grand_cancel_amt += $value->ord_cancel_amt;
										
										$j++;
									}
									/*sub total*/
									//$sheet->row($j, function($row) { $row->setBackground('##ffccb3'); });
									$sheet->mergeCells('A'.$j.':J'.$j);
									$sheet->cell('A'.$j.':AB'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#ff5500'); });
									$sheet->cell('A'.$j, 'SUBTOTAL:'); 
									$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
									$sheet->cell('K'.$j, $glob_choice_amt); 
									$sheet->cell('M'.$j, $glob_price_amt); 
									$sheet->cell('N'.$j, $glob_subtot_amt); 
									$sheet->cell('P'.$j, $glob_tax_amt); 
									$sheet->cell('Q'.$j, $glob_wallet_amt); 
									$sheet->cell('S'.$j, $glob_grandTot_amt); 
									$sheet->cell('V'.$j, $glob_commision_amt); 
									$sheet->cell('Y'.$j, $glob_cancel_amt); 
									$j++;
								}
								
							}
							$j++;
							$sheet->mergeCells('A'.$j.':J'.$j);
							$sheet->cell('A'.$j.':AB'.$j, function($cell) { $cell->setFontSize(12); $cell->setFontWeight('bold');$cell->setFontColor('#ff5500');  });
							//$sheet->row($j, function($row) { $row->setBackground('##ffccb3'); });
							$sheet->cell('A'.$j, 'GRAND TOTAL:'); 
							$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
							$sheet->cell('K'.$j, $grand_choice_amt); 
							$sheet->cell('M'.$j, $grand_price_amt); 
							$sheet->cell('N'.$j, $grand_subtot_amt); 
							$sheet->cell('P'.$j, $grand_tax_amt); 
							$sheet->cell('Q'.$j, $grand_wallet_amt); 
							$sheet->cell('S'.$j, $grand_grandTot_amt); 
							$sheet->cell('V'.$j, $grand_commision_amt); 
							$sheet->cell('Y'.$j, $grand_cancel_amt); 
						});
					})->download($type);
					} else {
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NO_RECORDS');
					return Redirect::to('manage-order-report')->withErrors(['errors'=> $msg])->withInput();
				}
			}
			else{
				return Redirect::to('admin-login');
			}
		}
		
		
		/* admin earning report */
		public function earning_report(Request $request)
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EARN_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_EARN_REPORTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EARN_REPORTS');
			$restaurants = Item::get_activestore_withmerch('1');
			$rest_array = array();
			if(count($restaurants) > 0 )
			{
				$rest_array[0]=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
				foreach($restaurants as $restar)
				{
					$rest_array[$restar->id]=$restar->st_store_name.' - '.$restar->mer_email;
				}
			}
			$restaurant_list = $rest_array;
			return view('Admin.consolidate_reports.earning_report')->with(['pagetitle'=>$page_title,'restaurant_list'=>$restaurant_list]);
		}
		
		/*  admin earning report*/
		public function download_earning_rpt(Request $request)
		{
			$store_id 	= $request->pro_store_id;
			$from_date 	= $request->from_date;
			$to_date 	= $request->to_date;
			$payment_method = $request->payment_method;
			$get_reports = Reports::download_earning_report($store_id,$from_date,$to_date,$payment_method);
			$type='xls';
			//echo '<pre>';
			//print_r($get_reports); exit;
			if(count($get_reports) > 0)
			{
				return Excel::create('Earning Report',function ($excel) use ($get_reports)
				{
					$excel->sheet('Earning Report', function ($sheet) use ($get_reports)
					{	
						//$sheet->setAutoSize(true);
						//$sheet->setSize('A1:J1',5);
						$sheet->setFontFamily('Verdana');
						$sheet->mergeCells('A1:J1');
						$sheet->cell('A1:J1', function($cell) { 
							$cell->setFontSize(12); $cell->setFontWeight('bold'); 
						});
						$sheet->cell('A1', function($cell) {
							$cell->setValue('Earning Report');
						});
						$sheet->row(1, function($row) { 
							$row->setBackground('#5cb85cc2'); 
						});
						$sheet->cell('A2:J2', function($cell) { 
							$cell->setFontSize(11); 
							$cell->setFontWeight('bold'); 
						});
						$sheet->setFreeze('A3');
						$sheet->setFontSize('10');
						$sheet->row(2, function($row) { 
							$row->setBackground('#CCCCCC'); 
							
						});
						$sheet->setBorder('A2:J2', 'thin');
						
						$sheet->cell('A2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO'));
							
						});
						$sheet->cell('B2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRANSACTION_ID'));
						});
						$sheet->cell('C2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_DATE'));
							
						});
						$sheet->cell('D2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_TYPE'));
							
						});
						$sheet->cell('E2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUNTRY_SYM'));
							
						});
						$sheet->cell('F2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_AMOUNT'));
							$cell->setBorder('solid','solid','solid','solid');
						});
						$sheet->cell('G2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COMMISSION'));
							
						});
						$sheet->cell('H2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_CANCEL_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_CANCEL_AMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TOTAL_CANCEL_AMT'));
							
						});
						$sheet->cell('I2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_WALLET_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_WALLET_AMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TOTAL_WALLET_AMT'));
							
						});
						$sheet->cell('J2', function($cell) {
							$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELIVERY_FEE'));
							
						});
						$j=3;
						$k=1;
						$glob_store_name = $grand_store_name = '';
						$glob_st_id 	= '';
						$sub_order_amt 	= 0;
						$sub_comm_amt 	= 0;
						$sub_cancel_amt = 0;
						$sub_wallet_amt = 0;
						$sub_delfee_amt = 0;
						$grant_order_amt = 0;
						$grant_comm_amt	  = 0;
						$grant_cancel_amt = 0;
						$grant_wallet_amt = 0;
						$grant_delfee_amt = 0;
						foreach($get_reports as $key => $value)
						{
							//$i = $key+2;
							$det = explode('``',$key);
							if($glob_st_id!=$det[0])
							{
								$sheet->mergeCells('A'.$j.':F'.$j);
								$sheet->cell('A'.$j.':F'.$j, function($cell) { 
									$cell->setFontSize(10); 
									$cell->setFontWeight('bold'); 
									$cell->setFontColor('#b32927');
								});
								$sheet->cell('A'.$j, 'Restaurant : '.$det[1]);
								
								$sheet->mergeCells('G'.$j.':L'.$j);
								$sheet->cell('G'.$j.':L'.$j, function($cell) { 
									$cell->setFontSize(10); 
									$cell->setFontWeight('bold');
									$cell->setFontColor('#b32927');
								});
								$sheet->cell('G'.$j, 'Merchant Name :'.$det[3].' ( '.$det[2].' )');
								$glob_st_id = $key;
								$j++;
								//$k=1;
							}
							foreach($value as $result)
							{
								$sheet->cell('A'.$j, $k++); //print serial no
								$sheet->cell('B'.$j,$result->ord_transaction_id);
								$sheet->cell('C'.$j,$result->ord_date);
								$sheet->cell('D'.$j,$result->ord_pay_type);
								$sheet->cell('E'.$j,$result->ord_currency);
								$sheet->cell('F'.$j,$result->total_order_amount);
								$sheet->cell('G'.$j,$result->total_admin_amount);
								$sheet->cell('H'.$j,$result->total_cancel_amt);
								$sheet->cell('I'.$j,$result->ord_wallet);
								$sheet->cell('J'.$j,$result->ord_delivery_fee);
								$sub_order_amt += $result->total_order_amount;
								$sub_comm_amt	+= $result->total_admin_amount;
								$sub_cancel_amt += $result->total_cancel_amt;
								$sub_wallet_amt += $result->ord_wallet;
								$sub_delfee_amt += $result->ord_delivery_fee;
								$j++;
							}
							if($glob_st_id==$key)
							{
								$sheet->mergeCells('A'.$j.':E'.$j);
								$sheet->cell('A'.$j.':J'.$j, function($cell) 
								{
									$cell->setFontSize(10); 
									$cell->setFontWeight('bold'); 
									$cell->setFontColor('#de8910');
								});
								$sheet->cell('A'.$j, 'SUBTOTAL:'); 
								$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
								$sheet->cell('F'.$j, $sub_order_amt); 
								$sheet->cell('G'.$j, $sub_comm_amt); 
								$sheet->cell('H'.$j, $sub_cancel_amt); 
								$sheet->cell('I'.$j, $sub_wallet_amt); 
								$sheet->cell('J'.$j, $sub_delfee_amt); 
								$grant_order_amt += $sub_order_amt;
								$grant_comm_amt	  += $sub_comm_amt;
								$grant_cancel_amt += $sub_cancel_amt;
								$grant_wallet_amt += $sub_wallet_amt;
								$grant_delfee_amt += $sub_delfee_amt;
								$sub_order_amt 	= 0;
								$sub_comm_amt 	= 0;
								$sub_cancel_amt = 0;
								$sub_wallet_amt = 0;
								$sub_delfee_amt = 0;
								$j++;
								//$glob_store_name = $value->st_store_name;
								$k=1;
							}
						}
						$j++;
						$sheet->mergeCells('A'.$j.':E'.$j);
						$sheet->cell('A'.$j.':J'.$j, function($cell) 
						{
							$cell->setFontSize(10); 
							$cell->setFontWeight('bold'); 
							$cell->setFontColor('#de8910');
						});
						$sheet->cell('A'.$j, 'GRANTTOTAL:'); 
						$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
						$sheet->cell('F'.$j, $grant_order_amt); 
						$sheet->cell('G'.$j, $grant_comm_amt); 
						$sheet->cell('H'.$j, $grant_cancel_amt); 
						$sheet->cell('I'.$j, $grant_wallet_amt); 
						$sheet->cell('J'.$j, $grant_delfee_amt);
					});
				})->download($type);
			}
			else 
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NO_RECORDS');
				return Redirect::to('manage-order-report')->withErrors(['errors'=> $msg])->withInput();
			}
		}
		
		/* merchant transaction report */
		public function merchant_transaction_report(Request $request)
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MER_TRANS_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MER_TRANS_REPORT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MER_TRANS_REPORT');
			$restaurants = Item::get_activestore_withmerch('1');
			$delboy_lists = Admin::get_delboy_active();
			$rest_array = array();
			if(count($restaurants) > 0 )
			{
				$rest_array[0]=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
				foreach($restaurants as $restar)
				{
					$rest_array[$restar->mer_id]=$restar->st_store_name.' - '.$restar->mer_email;
				}
			}
			$restaurant_list = $rest_array;
			
			return view('Admin.consolidate_reports.mer_transaction_report')->with(['pagetitle'=>$page_title,'restaurant_list'=>$restaurant_list]);
		}
		
		/* download merchant transaction */
		public function download_mer_transaction(Request $request)
		{
			
			$mer_id 	= $request->pro_store_id;
			$from_date 	= $request->from_date;
			$to_date 	= $request->to_date;
			//$payment_method = $request->payment_method;
			$get_reports = Reports::get_mer_trans($mer_id,$from_date,$to_date);
			$type='xls';
			/*if(count($get_reports) > 0){
				foreach($get_reports as $key=>$get_report){
				
				if(count($get_report) > 0 ){
				echo $key;
				foreach($get_report as $value){
				
				}
				}
				}
			}*/
			//echo '<pre>';
			//print_r($get_reports); exit;
			if(count($get_reports) > 0){
				return Excel::create('Commission Transaction Report',function ($excel) use ($get_reports){
					$excel->sheet('Commission Transaction Report', function ($sheet) use ($get_reports){
						$sheet->setFontFamily('Verdana');
						$sheet->mergeCells('A1:F1');
						$sheet->cell('A1:F1', function($cell) { $cell->setFontSize(12); $cell->setFontWeight('bold');  });
						$sheet->cell('A1', function($cell) { $cell->setValue('Commission Transaction History Report'); });
						$sheet->row(1, function($row) { $row->setBackground('#5cb85cc2'); });
						
						$sheet->cell('A2:F2', function($cell) { $cell->setFontSize(11); $cell->setFontWeight('bold'); });
						$sheet->row(2, function($row) { $row->setBackground('#CCCCCC'); });
						$sheet->setBorder('A2:F2', 'thin');
						
						$sheet->setFreeze('A3');
						$sheet->setFontSize('10');
						$sheet->setAutoSize(true);
						
						$sheet->setColumnFormat(array('C' => '0.00'));
						$sheet->setWidth(array('C' => '30'));
						
						$j=3;
						$k=1;
						$sheet->cell('A2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO')); });
						
						$sheet->cell('B2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUNTRY_SYM')); });
						
						$sheet->cell('C2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAID_AMOUNT')); });
						
						$sheet->cell('D2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_PAID_ON')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_PAID_ON') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYMENT_PAID_ON')); });
						
						$sheet->cell('E2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRANSACTION_ID')); });
						
						$sheet->cell('F2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_TYPE')); });
						$grand_order_amt = 0;
						foreach($get_reports as $key=>$get_report){
							if(count($get_report) > 0 ){
								$det = explode('``',$key);
								$restNameText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RESTAURANT_NAME');
								$sheet->mergeCells('A'.$j.':F'.$j);
								$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#b32927');});
								$sheet->cell('A'.$j, $restNameText.' : '.$det[1]);
								$j++;
								$sheet->mergeCells('A'.$j.':F'.$j);
								$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10);  $cell->setFontWeight('bold'); $cell->setFontColor('#b32927');}); 
								$merchantNameText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT_NAME');
								$sheet->cell('A'.$j, $merchantNameText.' :'.$det[3].' ( '.$det[2].' )');
								$sub_order_amt 	= 0;
								$j++;
								foreach($get_report as $value){
									$paid_on = ($value->commission_date!='')? date('m/d/Y H:i:s',strtotime($value->commission_date)) :'';
									$paid_amt = ($value->commission_paid!='')?$value->commission_paid :'0.00';
									$transaction_id = ($value->mer_transaction_id!='')?$value->mer_transaction_id :'-';
									if($value->pay_type=='1'){ $payType = 'Net Banking'; } elseif($value->pay_type=='2'){ $payType='Paypal'; } elseif($value->pay_type=='3') { $payType='Stripe'; } else {  $payType='-';}
									
									$sheet->cell('A'.$j, $k++); //print serial no
									$sheet->cell('B'.$j,$value->commission_currency);
									$sheet->cell('C'.$j,$paid_amt);
									$sheet->cell('D'.$j,$paid_on);
									$sheet->cell('E'.$j,$transaction_id);
									$sheet->cell('F'.$j,$payType);
									$sub_order_amt 	+= $paid_amt;
									$j++;
								}
								$sheet->mergeCells('A'.$j.':B'.$j);
								$sheet->cell('A'.$j.':C'.$j, function($cell){ $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#ff5500');});
								$sheet->cell('A'.$j, 'SUBTOTAL:'); 
								$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
								$sheet->cell('C'.$j, $sub_order_amt); 
								$grand_order_amt += $sub_order_amt;
								$j++;
							}
						}
						
						$sheet->mergeCells('A'.$j.':B'.$j);
						$sheet->cell('A'.$j.':C'.$j, function($cell){$cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#ff5500');});
						$sheet->cell('A'.$j, 'GRAND TOTAL:'); 
						$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
						$sheet->cell('C'.$j, $grand_order_amt); 
					});
				})->download($type);
			}
			else 
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NO_RECORDS');
				return Redirect::to('merchant-transaction-report')->withErrors(['errors'=> $msg])->withInput();
			}
			
		}
		
		/*DELIVERY BOY Transaction Report */
		public function delboy_transaction_report(Request $request)
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_TRANS_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_TRANS_REPORT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELBOY_TRANS_REPORT');
			$delboy_lists = Admin::get_delboy_active();
			//echo '<pre>'; print_r($delboy_lists);exit;
			$delboy_array = array();
			if(count($delboy_lists) > 0 )
			{
				//$rest_array[0]=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
				foreach($delboy_lists as $restar)
				{
					$delboy_array[$restar->deliver_id]=$restar->name.' - '.$restar->deliver_email;
				}
			}
			$delboy_list = $delboy_array;
			
			return view('Admin.consolidate_reports.delboy_transaction_report')->with(['pagetitle'=>$page_title,'delboy_list'=>$delboy_list]);
		}
		
		/* download merchant transaction */
		public function download_delboy_transaction(Request $request)
		{
			
			$deliver_id 	= $request->deliver_id;
			$from_date 	= $request->from_date;
			$to_date 	= $request->to_date;
			//$payment_method = $request->payment_method;
			$get_reports = Reports::get_delboy_trans($deliver_id,$from_date,$to_date);
			$type='xls';
			
			if(count($get_reports) > 0){
				return Excel::create('Delivery boy-Commission Report',function ($excel) use ($get_reports){
					$excel->sheet('Delivery boy-Commission Report', function ($sheet) use ($get_reports){
						$sheet->setFontFamily('Verdana');
						$sheet->mergeCells('A1:G1');
						$sheet->cell('A1:G1', function($cell) { $cell->setFontSize(12); $cell->setFontWeight('bold');  });
						$sheet->cell('A1', function($cell) { $cell->setValue('Delivery boy-Commission Report'); });
						$sheet->row(1, function($row) { $row->setBackground('#5cb85cc2'); });
						
						$sheet->cell('A2:G2', function($cell) { $cell->setFontSize(11); $cell->setFontWeight('bold'); });
						$sheet->row(2, function($row) { $row->setBackground('#CCCCCC'); });
						$sheet->setBorder('A2:G2', 'thin');
						
						$sheet->setFreeze('A3');
						$sheet->setFontSize('10');
						$sheet->setAutoSize(true);
						
						$sheet->setColumnFormat(array('C' => '0.00'));
						$sheet->setWidth(array('C' => '30'));
						
						$j=3;
						$k=1;
						$sheet->cell('A2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO')); });
						
						$sheet->cell('B2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUNTRY_SYM')); });
						
						$sheet->cell('C2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAID_AMOUNT')); });
						
						$sheet->cell('D2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_RECEIVED_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_RECEIVED_AMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RECEIVED_AMT')); });
						
						$sheet->cell('E2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_PAID_ON')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_PAID_ON') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAYMENT_PAID_ON')); });
						
						$sheet->cell('F2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRANSACTION_ID')); });
						
						$sheet->cell('G2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PAY_TYPE')); });
						$grand_order_amt = $grand_rcvd_amt = 0;
						foreach($get_reports as $key=>$get_report){
							if(count($get_report) > 0 ){
								$det = explode('``',$key);
								$sheet->mergeCells('A'.$j.':F'.$j);
								$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10);  $cell->setFontWeight('bold'); $cell->setFontColor('#b32927');}); 
								$merchantNameText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELBOY');
								$sheet->cell('A'.$j, $merchantNameText.' :'.$det[1].' ( '.$det[2].' )');
								$sub_order_amt 	= $sub_rcvd_amt = 0;
								
								$j++;
								foreach($get_report as $value){
									$paid_on = ($value->commission_date!='')? date('m/d/Y H:i:s',strtotime($value->commission_date)) :'';
									$rcved_amt = ($value->amount_received!='')?$value->amount_received :'0.00';
									$paid_amt = ($value->commission_paid!='')?$value->commission_paid :'0.00';
									$transaction_id = ($value->transaction_id!='')?$value->transaction_id :'-';
									if($value->pay_type=='0'){ $payType = 'Net Banking'; } elseif($value->pay_type=='1'){ $payType='Paypal'; } elseif($value->pay_type=='2') { $payType='Stripe'; } else {  $payType='-';}
									
									$sheet->cell('A'.$j, $k++); //print serial no
									$sheet->cell('B'.$j,$value->commission_currency);
									$sheet->cell('C'.$j,$paid_amt);
									$sheet->cell('D'.$j,$rcved_amt);
									$sheet->cell('E'.$j,$paid_on);
									$sheet->cell('F'.$j,$transaction_id);
									$sheet->cell('G'.$j,$payType);
									$sub_order_amt 	+= $paid_amt;
									$sub_rcvd_amt 	+= $rcved_amt;
									$j++;
								}
								$sheet->mergeCells('A'.$j.':B'.$j);
								$sheet->cell('A'.$j.':D'.$j, function($cell){ $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#ff5500');});
								$sheet->cell('A'.$j, 'SUBTOTAL:'); 
								$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
								$sheet->cell('C'.$j, $sub_order_amt); 
								$sheet->cell('D'.$j, $sub_rcvd_amt); 
								$grand_order_amt += $sub_order_amt;
								$grand_rcvd_amt += $sub_rcvd_amt;
								$j++;
							}
						}
						
						$sheet->mergeCells('A'.$j.':B'.$j);
						$sheet->cell('A'.$j.':D'.$j, function($cell){$cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#ff5500');});
						$sheet->cell('A'.$j, 'GRAND TOTAL:'); 
						$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
						$sheet->cell('C'.$j, $grand_order_amt); 
						$sheet->cell('D'.$j, $grand_rcvd_amt); 
					});
				})->download($type);
			}
			else 
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NO_RECORDS');
				return Redirect::to('delboy-transaction-report')->withErrors(['errors'=> $msg])->withInput();
			}
			
		}
		
		/*  delivery person earning report */
		public function delboy_earning_report(Request $request)
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_EARN_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_EARN_REPORTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DEL_EARN_REPORTS');
			$delboy_lists = Admin::get_delboy_active();
			$delboy_array = array();
			if(count($delboy_lists) > 0 )
			{
				//$rest_array[0]=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
				foreach($delboy_lists as $restar)
				{
					$delboy_array[$restar->deliver_id]=$restar->name.' - '.$restar->deliver_email;
				}
			}
			$delboy_list = $delboy_array;
			return view('Admin.consolidate_reports.delboy_earning_report')->with(['pagetitle'=>$page_title,'delivery_list' => $delboy_list]);
		}
		
		/*  delviery person earning report*/
		public function download_delboy_earning_rpt(Request $request)
		{
			$deliver_id = $request->deliver_id;
			$from_date 	= $request->from_date;
			$to_date 	= $request->to_date;
			$get_reports = Reports::delboy_earning_report($deliver_id,$from_date,$to_date);
			$type='xls';
			//echo '<pre>';
			//print_r($get_reports); exit;
			/*if(count($get_reports) > 0)
				{
				foreach($get_reports as $key => $value){
				echo $key.'<br>';
				foreach($value as $val){
				echo '<pre>'; print_r($val); 
				}
				}
				}
			exit;*/
			
			if(count($get_reports) > 0)
			{
				return Excel::create('Delivery boy-Earning Report',function ($excel) use ($get_reports)
				{
					$excel->sheet('Delivery boy-Earning Report', function ($sheet) use ($get_reports)
					{	
						//$sheet->setAutoSize(true);
						//$sheet->setSize('A1:J1',5);
						$sheet->setFontFamily('Verdana');
						$sheet->mergeCells('A1:F1');
						$sheet->cell('A1:F1', function($cell) { $cell->setFontSize(12); $cell->setFontWeight('bold');  });
						$sheet->cell('A1', function($cell) { $cell->setValue('Delivery boy-Earning Report'); });
						$sheet->row(1, function($row) {  $row->setBackground('#5cb85cc2');  });
						$sheet->cell('A2:F2', function($cell) {  $cell->setFontSize(11);  $cell->setFontWeight('bold');  });
						$sheet->row(2, function($row) {  $row->setBackground('#CCCCCC'); });
						
						$sheet->setFreeze('A3');
						$sheet->setFontSize('10');
						
						$sheet->setBorder('A2:F2', 'thin');
						$sheet->setColumnFormat(array('E' => '0.00','F' => '0.00'));
						$sheet->setWidth(array('E' => '30','F' => '30'));
						
						$sheet->cell('A2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO')); });
						
						$sheet->cell('B2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_TRANSACTION_ID')); });
						
						$sheet->cell('C2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DATE')); });
						
						$sheet->cell('D2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY_SYM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COUNTRY_SYM')); });
						
						$sheet->cell('E2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_AMOUNT')); });
						
						$sheet->cell('F2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COMMISSION')); });
						
						
						$j=3;
						$k=1;
						
						$grand_order_amt 	= 0;
						$grand_comm_amt 	= 0;
						$merchantNameText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELBOY');
						foreach($get_reports as $key => $value)
						{
							$sub_order_amt 	= 0;
							$sub_comm_amt 	= 0;
							//$i = $key+2;
							$det = explode('`',$key);
							$sheet->mergeCells('A'.$j.':F'.$j);
							$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#b32927');});
							$sheet->cell('A'.$j, $merchantNameText.' :'.$det[1].' ( '.$det[2].' )');
							$j++;
							foreach($value as $result)
							{
								$ord_date = date('m/d/Y H:i:s',strtotime($result->de_updated_at));
								$sheet->cell('A'.$j, $k++); //print serial no
								$sheet->cell('B'.$j,$result->de_transaction_id);
								$sheet->cell('C'.$j,$ord_date);
								$sheet->cell('D'.$j,$result->de_ord_currency);
								$sheet->cell('E'.$j,$result->de_order_total);
								$sheet->cell('F'.$j,$result->de_total_amount);
								$sub_order_amt += $result->de_order_total;
								$sub_comm_amt	+= $result->de_total_amount;
								$j++;
							}
							$sheet->mergeCells('A'.$j.':D'.$j);
							$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#de8910'); });
							$sheet->cell('A'.$j, 'SUBTOTAL:'); 
							$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
							$sheet->cell('E'.$j, $sub_order_amt); 
							$sheet->cell('F'.$j, $sub_comm_amt); 
							$grand_order_amt += $sub_order_amt;
							$grand_comm_amt	  += $sub_comm_amt;
							$j++;
						}
						$j++;
						$sheet->mergeCells('A'.$j.':D'.$j);
						$sheet->cell('A'.$j.':F'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#de8910'); });
						
						$sheet->cell('A'.$j, 'GRANTTOTAL:'); 
						$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
						$sheet->cell('E'.$j, $grand_order_amt); 
						$sheet->cell('F'.$j, $grand_comm_amt); 
					});
				})->download($type);
			}
			else 
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NO_RECORDS');
				return Redirect::to('delboy_earning_report')->withErrors(['errors'=> $msg])->withInput();
			}
		}
		/*  CONSOLIDATE REPORT */
		public function consolidate_report(Request $request)
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CONSOLIDATE_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CONSOLIDATE_REPORT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CONSOLIDATE_REPORT');
			return view('Admin.consolidate_reports.consolidate_report')->with(['pagetitle'=>$page_title]);
		}
		
		public function download_consolidate_rpt(Request $request)
		{
			$from_date 	= $request->from_date;
			$to_date 	= $request->to_date;
			$get_reports = Reports::consolidate_report($from_date,$to_date);
			//echo '<pre>';print_r($get_reports);
			//exit;
			$type='xls';
			if(count($get_reports) > 0 ){
				return Excel::create('Consolidate Report',function ($excel) use ($get_reports)
				{
					$excel->sheet('Consolidate Report', function ($sheet) use ($get_reports)
					{	
						$sheet->setFontFamily('Verdana');
						$sheet->mergeCells('A1:J1');
						$sheet->cell('A1:J1', function($cell) { $cell->setFontSize(12); $cell->setFontWeight('bold');  });
						$sheet->cell('A1', function($cell) { $cell->setValue('Consolidate Report'); });
						$sheet->row(1, function($row) {  $row->setBackground('#5cb85cc2');  });
						
						$sheet->cell('A2:J2', function($cell) {  $cell->setFontSize(11);  $cell->setFontWeight('bold');  });
						$sheet->row(2, function($row) {  $row->setBackground('#CCCCCC'); });
						$sheet->setBorder('A2:J2', 'thin');
						
						$sheet->setFreeze('A3');
						$sheet->setFontSize('10');
						$sheet->setColumnFormat(array('D' => '0.00','E' => '0.00','F' => '0.00','G' => '0.00','H' => '0.00','I' => '0.00'));
						$sheet->setWidth(array('D' => '30','E' => '30','F' => '30','G' => '30','H' => '30','I' => '30'));
						$sheet->setAutoSize(true); 	
						$sheet->cell('A2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO')); });
						$sheet->cell('B2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_ID')); });
						$sheet->cell('C2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_DATE')); });
						$sheet->cell('D2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ORDER_AMOUNT')); });
						$sheet->cell('E2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCELLATION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCELLATION_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CANCELLATION_AMOUNT')); });
						$sheet->cell('F2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_UESD_WALLET')) ? trans(Session::get('admin_lang_file').'.ADMIN_UESD_WALLET') : trans($this->ADMIN_LANGUAGE.'.ADMIN_UESD_WALLET')); });
						$sheet->cell('G2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($this->ADMIN_LANGUAGE.'.ADMIN_COMMISSION')); });
						$sheet->cell('H2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_FEE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_DELIVERY_FEE')); });
						$sheet->cell('I2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_AMOUNT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT_AMOUNT')); });
						$sheet->cell('J2', function($cell) { $cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PROFIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PROFIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PROFIT')); });
						$grand_order_amt = $grand_wallet = $grand_cancel_amt = $grand_comm_amt = $grand_merchant_amt = $grand_delFee_amt = $grand_profit = 0;
						$j=3;
						$k=1;
						foreach($get_reports as $result)
						{
							$ord_date = date('m/d/Y H:i:s',strtotime($result->ord_date));
							/*$get_total_order_count = get_total_order_count($result->ord_transaction_id);
								$get_total_cancelled_count = get_total_cancelled_count($result->ord_transaction_id);
								if($get_total_cancelled_count==$get_total_order_count)
								{
								$delFee = '0.00'; $delFee1=0;
								} else {
								if($result->ord_self_pickup=='1'){
								$delFee = '0.00'; $delFee1=0;
								}else{
								$delFee = $result->AverageDelFee; $delFee1 = $result->AverageDelFee;
								}
							}*/
							if($result->ord_self_pickup=='1'){
								$delFee = '0.00'; $delFee1=0;
								}else{
								$delFee = $result->AverageDelFee; $delFee1 = $result->AverageDelFee;
							}
							/*STYLE*/
							$sheet->getStyle('J'.$j)->applyFromArray(
							array(
							
							'font' => array(
							'name' => 'Verdana',
							'size' => 10,
							'bold'=>true,
							'color' => array('rgb' => 'DE5A00'),
							),
							)
							);
							
							/* EOF STYLE */
							
							
							$total_order_amount = $delFee1+$result->total_order_amount;
							$total_cancel_amount = $result->total_cancel_amount;
							$merchant_amount = $total_order_amount - $total_cancel_amount - ($result->total_admin_commission+$delFee);
							if($merchant_amount==0) { $merDisplay='0.00'; } else { $merDisplay=$merchant_amount; } 
							$net_profit = $total_order_amount - $total_cancel_amount - $result->total_wallet_amount - $merDisplay;
							
							$sheet->cell('A'.$j, $k++); //print serial no
							$sheet->cell('B'.$j,$result->ord_transaction_id);
							$sheet->cell('C'.$j,$ord_date);
							$sheet->cell('D'.$j,$total_order_amount);
							$sheet->cell('E'.$j,$total_cancel_amount);
							$sheet->cell('F'.$j,$result->total_wallet_amount);
							$sheet->cell('G'.$j,$result->total_admin_commission);
							$sheet->cell('H'.$j,$delFee);
							$sheet->cell('I'.$j,$merDisplay);
							$sheet->cell('J'.$j,$net_profit);
							$grand_order_amt += $total_order_amount;
							$grand_cancel_amt += $total_cancel_amount;
							$grand_wallet += $result->total_wallet_amount;
							$grand_comm_amt	+= $result->total_admin_commission;
							$grand_delFee_amt	+= $delFee;
							$grand_merchant_amt	+= $merchant_amount;
							$grand_profit	+= $net_profit;
							$j++;
						}
						$sheet->mergeCells('A'.$j.':C'.$j);
						$sheet->cell('A'.$j.':J'.$j, function($cell) { $cell->setFontSize(10); $cell->setFontWeight('bold'); $cell->setFontColor('#DE5A00'); });
						$sheet->cell('A'.$j, 'GRAND TOTAL:'); 
						$sheet->getStyle('A'.$j)->getAlignment()->applyFromArray(array('horizontal' => 'right'));
						if($grand_merchant_amt==0) { $grandMerDisplay='0.00'; } else { $grandMerDisplay=$grand_merchant_amt; } 
						if($grand_profit==0) { $grandNetProfitDisplay='0.00'; } else { $grandNetProfitDisplay=$grand_profit; }
						
						$sheet->cell('D'.$j, $grand_order_amt); 
						$sheet->cell('E'.$j, $grand_cancel_amt);
						$sheet->cell('F'.$j, $grand_wallet); 
						$sheet->cell('G'.$j, $grand_comm_amt); 
						$sheet->cell('H'.$j, $grand_delFee_amt); 
						$sheet->cell('I'.$j, $grandMerDisplay); 
						$sheet->cell('J'.$j, $grandNetProfitDisplay); 
						$j++;
					});
				})->download($type);	
				
				}else{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_RECORDS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NO_RECORDS');
				return Redirect::to('consolidate-report')->withErrors(['errors'=> $msg])->withInput();
			}
		} 
	}
