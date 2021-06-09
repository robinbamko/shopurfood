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
	use App\Merchant;
	use Excel;
	use Response;
	use File;
	use DateTime;
	use DateInterval;
	use DatePeriod;
	use Aceraven777\PayMaya\PayMayaSDK;
	use Aceraven777\PayMaya\API\Checkout;
	use Aceraven777\PayMaya\Model\Checkout\Item;
	use App\Libraries\PayMaya\User as PayMayaUser;
	use Aceraven777\PayMaya\Model\Checkout\ItemAmount;
	use Aceraven777\PayMaya\Model\Checkout\ItemAmountDetails;
	use Aceraven777\PayMaya\Model\Checkout\Address;
	class FeaturedController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			$this->setLanguageLocaleMerchant();
		}
		
		//		public function make_featured(Request $request)
		//		{
		//			if(Session::has('merchantid') == 1)
		//			{
		//				if(Session::get('mer_business_type')=='1'){
		//					$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_STORE')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_STORE') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURED_STORE');
		//					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
		//				}else{
		//					$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_REST')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_REST') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURED_REST');
		//					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
		//				}
		//				$featured_numstore_qry=DB::table('gr_general_setting')->select('gs_featured_numstore')->first();
		//				if(empty($featured_numstore_qry) === true){
		//					$BookedArr = array();
		//				}else{
		//					$BookedArr = array();
		//					$gs_featured_numstore = $featured_numstore_qry->gs_featured_numstore;
		//					if($gs_featured_numstore > 0 ){
		//						$BookedDates = array();
		//						$bookingQry = DB::table('gr_featured_booking')->select('from_date','to_date')->where('to_date','>=',date('Y-m-d'))->where('admin_approved_status','=','1')->get();
		//						if(count($bookingQry) > 0 ){
		//							foreach($bookingQry as $bukQry){
		//								$dateRanges = $this->createDateRange($bukQry->from_date, $bukQry->to_date, $format = "Y-m-d");
		//								//array_push($BookedDates,$dateRanges);
		//								$BookedDates=array_merge($BookedDates,$dateRanges);
		//							}
		//
		//							$vals = array_count_values($BookedDates);
		//							foreach($vals as $key=>$val){
		//								if($val >= $gs_featured_numstore){
		//									//echo $key .' =>' .$val.'<br>';
		//									array_push($BookedArr,date('m/d/Y',strtotime($key)));
		//								}
		//							}
		//						}else{
		//							$BookedArr = array();
		//						}
		//
		//					}else{
		//						$BookedArr = array();
		//					}
		//				}
		//				//print_r($BookedArr);Array ( [0] => 2019-01-13 [1] => 2019-01-14 [2] => 2019-01-15 [3] => 2019-01-16 [4] => 2019-01-17 [5] => 2019-01-18 [6] => 2019-01-19 [7] => 2019-01-20 )
		//				//exit;
		//				$featured_details = DB::table('gr_general_setting')->select('gs_featured_store','gs_featured_price','gs_featured_numstore')->first();
		//				if(empty($featured_details)===true){
		//					$featured_store = 0;
		//					$featured_price = 0;
		//					$featured_numstore = 0;
		//				}else{
		//					$featured_store = $featured_details->gs_featured_store;
		//					$featured_price = $featured_details->gs_featured_price;
		//					$featured_numstore = $featured_details->gs_featured_numstore;
		//				}
		//
		//				$payment_details = DB::table('gr_payment_setting')->first();
		//
		//
		//				$booked_Dates = "'" . implode ( "', '", array_unique($BookedArr) ) . "'";// implode( "','", array_unique($BookedArr) );
		//				//echo $booked_Dates;
		//				//exit;
		//				return view('sitemerchant.reports.make_featured')->with(['pagetitle'=>$page_title,'featured_store'=>$featured_store,'featured_price'=>$featured_price,'featured_numstore'=>$featured_numstore,'payment_details'=>$payment_details,'booked_Dates'=>$booked_Dates,'business_type'=>$business_type]);
		//			}else{
		//				return Redirect::to('merchant-login');
		//			}
		//		}
		
        public function make_featured(Request $request)
        {
            if(Session::has('merchantid') == 1)
            {
                if(Session::get('mer_business_type')=='1'){
                    $page_title = (Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_STORE')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_STORE') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURED_STORE');
                    $business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
					}else{
                    $page_title = (Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_REST')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_REST') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURED_REST');
                    $business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
				}
                $featured_numstore_qry=DB::table('gr_general_setting')->select('gs_featured_numstore')->first();
                if(empty($featured_numstore_qry) === true)
                {
                    $BookedArr = array();
				}
                else
                {
                    $BookedArr = array();
                    $gs_featured_numstore = $featured_numstore_qry->gs_featured_numstore;
                    if($gs_featured_numstore > 0 ){
                        $BookedDates = array();
                        $bookingQry = DB::table('gr_featured_booking')->select('from_date','to_date')->where('to_date','>=',date('Y-m-d'))->where('admin_approved_status','=','1')->get();
                        //print_r($bookingQry);
                        if(count($bookingQry) > 0 )
                        {
                            foreach($bookingQry as $bukQry){
                                $dateRanges = $this->createDateRange($bukQry->from_date, $bukQry->to_date, $format = "Y-m-d");
                                //array_push($BookedDates,$dateRanges);
                                $BookedDates=array_merge($BookedDates,$dateRanges);
							}
                            //print_r($BookedDates);
							
                            $vals = array_count_values($BookedDates);
                            //print_r($vals);
                            //echo $gs_featured_numstore;
                            foreach($vals as $key=>$val){
                                /* date in which 12 restaurants are featured */
                                if($val >= $gs_featured_numstore){
                                    //echo $key .' =>' .$val.'<br>';
                                    array_push($BookedArr,date('m/d/Y',strtotime($key)));
								}
								
							}
						}
                        else
                        {
                            $BookedArr = array();
						}
                        /* merge dates in which merchant already booked */
                        $bookingMer = DB::table('gr_featured_booking')->select('from_date','to_date')->where('to_date','>=',date('Y-m-d'))->where('mer_id','=',Session::get('merchantid'))->get();
						
                        if(count($bookingMer) > 0 )
                        {
                            $DatesBooked = array();
                            foreach($bookingMer as $bukQry){
                                $dateRanges = $this->createDateRange($bukQry->from_date, $bukQry->to_date, $format = "Y-m-d");
                                //array_push($BookedDates,$dateRanges);
                                $DatesBooked=array_merge($DatesBooked,$dateRanges);
							}
                            //print_r($BookedDates);
							
                            $vals = array_count_values($DatesBooked);
                            //print_r($vals);
                            //echo $gs_featured_numstore;
                            foreach($vals as $key=>$val)
                            {
                                array_push($BookedArr,date('m/d/Y',strtotime($key)));
							}
						}
                        /* merge dates in which merchant already booked ends */
						
					}
                    else
                    {
                        $BookedArr = array();
					}
				}
                //print_r($BookedArr);
                //exit;
                //print_r($BookedArr);Array ( [0] => 2019-01-13 [1] => 2019-01-14 [2] => 2019-01-15 [3] => 2019-01-16 [4] => 2019-01-17 [5] => 2019-01-18 [6] => 2019-01-19 [7] => 2019-01-20 )
                //exit;
                $featured_details = DB::table('gr_general_setting')->select('gs_featured_store','gs_featured_price','gs_featured_numstore')->first();
                if(empty($featured_details)===true){
                    $featured_store = 0;
                    $featured_price = 0;
                    $featured_numstore = 0;
					}else{
                    $featured_store = $featured_details->gs_featured_store;
                    $featured_price = $featured_details->gs_featured_price;
                    $featured_numstore = $featured_details->gs_featured_numstore;
				}
				
                $payment_details = DB::table('gr_payment_setting')->first();
				
				
                $booked_Dates = "'" . implode ( "', '", array_unique($BookedArr) ) . "'";// implode( "','", array_unique($BookedArr) );
                //echo $booked_Dates;
				
                return view('sitemerchant.reports.make_featured')->with(['pagetitle'=>$page_title,'featured_store'=>$featured_store,'featured_price'=>$featured_price,'featured_numstore'=>$featured_numstore,'payment_details'=>$payment_details,'booked_Dates'=>$booked_Dates,'business_type'=>$business_type]);
				}else{
                return Redirect::to('merchant-login');
			}
		}
		public function createDateRange($startDate, $endDate, $format = "Y-m-d")
		{
			$begin = new DateTime($startDate);
			$end = new DateTime($endDate);
			
			$interval = new DateInterval('P1D'); // 1 Day
			$dateRange = new DatePeriod($begin, $interval, $end);
			
			$range = [];
			foreach ($dateRange as $date) {
				$range[] = $date->format($format);
			}
			$range[] = $endDate;
			return $range;
		}
		public function featured_offline_checkout(Request $request){
			//print_r($request->all());
			//exit;/*Array ( [_token] => Bg0ODNJaogczZ2gWdxdxTyIciqjC18dPeUe2Qvp7 [from_date] => 01/10/2019 [to_date] => 01/12/2019 [total_price] => 20 [feat_num_days] => 2 [payment_method] => offline [paynamics_client_id] => gfh [paynamics_secret_id] => fghgfh [paymaya_client_id] => pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz [paymaya_secret_id] => sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd [transaction_id] => test [btn] => )  */
			$validator = Validator::make($request->all(), [
			'from_date' 	=> 'required',
			'to_date' 		=> 'required',
			'payment_method'=> 'required',
			'transaction_id'=> 'required'
        	]);
			if ($validator->fails()) {
	            return redirect('make-featured')->withErrors($validator)->withInput();
				}else{
				$storeDet = DB::table('gr_store')->select('st_store_name','id')->where('st_mer_id','=',Session::get('merchantid'))->where('st_status','=','1')->first();
				if(empty($storeDet)===true){
					$store_name = '';
					$store_id = '0';
					}else{
					$store_name = '('.$storeDet->st_store_name.')';
					$store_id = $storeDet->id;
				}
				
	        	$insertArr = array(
				'mer_id'=> Session::get('merchantid'),
				'store_id' 			=> $store_id,
				'from_date' 		=> date('Y-m-d',strtotime(Input::get('from_date'))),
				'to_date' 			=> date('Y-m-d',strtotime(Input::get('to_date'))),
				'total_price' 		=> Input::get('total_price'),
				'total_num_days'	=> Input::get('feat_num_days'),
				'payment_method'	=> Input::get('payment_method'),
				'transaction_id' 	=> Input::get('transaction_id'),
				'createdAt' 		=> date('Y-m-d H:i:s')
	        	);
				DB::table('gr_featured_booking')->insert($insertArr);
	        	
				
				/* ---------------- SEND MAIL TO ADMIN ----------------*/
				
				if(Session::get('mer_business_type')=='1'){
					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
					}else{
					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
				}
				//The merchant (:merchant_name) requested to make :business_type as featured
				$got_message = (Lang::has(Session::get('mer_lang_file').'.MER_REQUEST_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_REQUEST_FEATURED') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUEST_FEATURED');
				$searchReplaceArray = array(':merchant_name' => ucfirst(Session::get('mer_name')),':business_type' => $business_type, '(:store_name)'=> $store_name);
				$heading = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
				
				$payment_method = (Lang::has(Session::get('mer_lang_file').'.MER_PMTMODE')) ? trans(Session::get('mer_lang_file').'.MER_PMTMODE') : trans($this->MER_OUR_LANGUAGE.'.MER_PMTMODE');
				$transaxn_id	= (Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID');
				$send_mail_data = array('heading'=>$heading,
				'name' => Session::get('mer_name'),
				'email' => Session::get('mer_email'),
				'featured_date' => date('m/d/Y',strtotime(Input::get('from_date'))).' to '.date('m/d/Y',strtotime(Input::get('to_date'))),
				'payment_detail'=>'<tr>
				<td><strong>'.$payment_method.' :</strong></td>
				<td>'.Input::get('payment_method').'</td>
				</tr>
				<tr>
				<td><strong>'.$transaxn_id.' :</strong></td>
				<td>'.Input::get('transaction_id').'</td>
				</tr>'
				);
				Mail::send('email.featured_store', $send_mail_data, function($message) use($business_type)
				{
					$email               = 'nagoor@mailinator.com';//Session::get('mer_email');
					$name                = Session::get('mer_name');
					$subject = (Lang::has(Session::get('mer_lang_file').'.MER_REQUESTING_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_REQUESTING_FEATURED') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUESTING_FEATURED');
					$subject = str_replace(':business_type',$business_type,$subject);
					$message->to($email, $name)->subject($subject);
				});
				/* ---------------- EOF MAIL FUNCTION  ----------------*/
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.MER_FEATURES_SUXES')) ? trans(Session::get('mer_lang_file').'.MER_FEATURES_SUXES') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURES_SUXES');;
	        	return Redirect::to('make-featured')->withErrors(['success'=>$msg])->withInput();
				
				
			}
		}
		public function featured_paynamics_checkout(Request $request){
			//print_r($request->all());
			//exit;/*Array ( [_token] => Bg0ODNJaogczZ2gWdxdxTyIciqjC18dPeUe2Qvp7 [from_date] => 01/10/2019 [to_date] => 01/12/2019 [total_price] => 20 [feat_num_days] => 2 [payment_method] => offline [paynamics_client_id] => gfh [paynamics_secret_id] => fghgfh [paymaya_client_id] => pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz [paymaya_secret_id] => sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd [transaction_id] => test [btn] => )  */
			$validator = Validator::make($request->all(), [
			'from_date' 	=> 'required',
			'to_date' 		=> 'required',
			'payment_method'=> 'required',
			//'transaction_id'=> 'required'
        	]);
			if ($validator->fails()) {
	            return redirect('make-featured')->withErrors($validator)->withInput();
				}else{
				$storeDet = DB::table('gr_store')->select('st_store_name','id')->where('st_mer_id','=',Session::get('merchantid'))->where('st_status','=','1')->first();
				if(empty($storeDet)===true){
					$store_name = '';
					$store_id = '0';
					}else{
					$store_name = '('.$storeDet->st_store_name.')';
					$store_id = $storeDet->id;
				}
				
	        	$insertArr = array(
				'mer_id'=> Session::get('merchantid'),
				'store_id' 			=> $store_id,
				'from_date' 		=> date('Y-m-d',strtotime(Input::get('from_date'))),
				'to_date' 			=> date('Y-m-d',strtotime(Input::get('to_date'))),
				'total_price' 		=> Input::get('total_price'),
				'total_num_days' 	=> Input::get('feat_num_days'),
				'payment_method'	=> Input::get('payment_method'),
				'transaction_id' 	=> 'PAYNAMICS-'.mt_rand(),
				'createdAt' 		=> date('Y-m-d H:i:s')
	        	);
				DB::table('gr_featured_booking')->insert($insertArr);
	        	
				
				/* ---------------- SEND MAIL TO ADMIN ----------------*/
				
				if(Session::get('mer_business_type')=='1'){
					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
					}else{
					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
				}
				//The merchant (:merchant_name) requested to make :business_type as featured
				$got_message = (Lang::has(Session::get('mer_lang_file').'.MER_REQUEST_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_REQUEST_FEATURED') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUEST_FEATURED');
				$searchReplaceArray = array(':merchant_name' => ucfirst(Session::get('mer_name')),':business_type' => $business_type, '(:store_name)'=> $store_name);
				$heading = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
				
				$payment_method = (Lang::has(Session::get('mer_lang_file').'.MER_PMTMODE')) ? trans(Session::get('mer_lang_file').'.MER_PMTMODE') : trans($this->MER_OUR_LANGUAGE.'.MER_PMTMODE');
				$transaxn_id	= (Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID');
				$send_mail_data = array('heading'=>$heading,
				'name' => Session::get('mer_name'),
				'email' => Session::get('mer_email'),
				'featured_date' => date('m/d/Y',strtotime(Input::get('from_date'))).' to '.date('m/d/Y',strtotime(Input::get('to_date'))),
				'payment_detail'=>'<tr>
				<td><strong>'.$payment_method.' :</strong></td>
				<td>'.Input::get('payment_method').'</td>
				</tr>
				'
				);
				Mail::send('email.featured_store', $send_mail_data, function($message) use($business_type)
				{
					$email               = Session::get('mer_email');
					$name                = Session::get('mer_name');
					$subject = (Lang::has(Session::get('mer_lang_file').'.MER_REQUESTING_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_REQUESTING_FEATURED') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUESTING_FEATURED');
					$subject = str_replace(':business_type',$business_type,$subject);
					$message->to($email, $name)->subject($subject);
				});
				/* ---------------- EOF MAIL FUNCTION  ----------------*/
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.MER_FEATURES_SUXES')) ? trans(Session::get('mer_lang_file').'.MER_FEATURES_SUXES') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURES_SUXES');;
	        	return Redirect::to('make-featured')->withErrors(['success'=>$msg])->withInput();
				
				
			}
		}
		public function featured_paymaya_checkout(Request $request){
			//print_r($request->all());
			//exit;
			/*Array ( [_token] => CPfSDEGRcdTq2rY7Dmg6Cxg24EVIYhnfBtmRp5OD [from_date] => 01/21/2019 [to_date] => 01/23/2019 [total_price] => 20 [feat_num_days] => 2 [paynamics_client_id] => gfh [paynamics_secret_id] => fghgfh [paymaya_client_id] => pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz [paymaya_secret_id] => sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd [payment_method] => paymaya [transaction_id] => [btn] => )*/
			Session::forget('sess_checkoutId');
			Session::forget('sess_mer_clientId');
			Session::forget('sess_mer_secretId');
			
			Session::put('sess_mer_clientId',$request->paymaya_client_id);
			Session::put('sess_mer_secretId',$request->paymaya_secret_id);
			
			PayMayaSDK::getInstance()->initCheckout($request->paymaya_client_id,$request->paymaya_secret_id,Config::get('env.PAYMAYA_MODE'));
			$merchant_id=Session::get('merchantid');
			if(Session::get('mer_business_type')=='1'){
				$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
				}else{
				$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
			}
			
			$merchant_det = DB::table('gr_general_setting')->first();
			if(empty($merchant_det)===false)
			{
				$sample_item_name = 'Featured '.$business_type.' Payment';
				$sample_total_price = $request->total_price;
				
				$converted_amount = convertCurrency(Session::get('default_currency_code'),'PHP',$request->total_price);
				$sample_user_phone = $merchant_det->gs_phone;
				$sample_user_email = $merchant_det->gs_email;
				// Item
				$itemAmountDetails = new ItemAmountDetails();
				$itemAmountDetails->tax = "0.00";
				$itemAmountDetails->subtotal = $converted_amount;
				
				$itemAmount = new ItemAmount();
				$itemAmount->currency = "PHP";
				$itemAmount->value = $converted_amount;
				$itemAmount->details = $itemAmountDetails;
				
				$item = new Item();
				$item->name = $sample_item_name;
				$item->amount = $itemAmount;
				$item->quantity = '1';
				$item->totalAmount = $itemAmount;
				
				// Checkout
				$itemCheckout = new Checkout();
				$user = new PayMayaUser();
				$user->firstName = $merchant_det->gs_sitename;
				$user->middleName=$request->from_date.'-'.$request->to_date.'-'.$request->total_price.'-'.$request->feat_num_days.'-'.Session::get('merchantid');
				//$user->lastName = $merchant_det->mer_lname;
				$user->contact->phone = $sample_user_phone;
				$user->contact->email = $sample_user_email;
				
				/*$address = new Address();
					$address->line1 = $merchant_det->mer_location;
				$user->shippingAddress = $address;*/
				
				
				$sample_reference_number = 'feat-'.rand();
				$itemCheckout->buyer = $user->buyerInfo();
				
				$itemCheckout->items = array($item);
				$itemCheckout->totalAmount = $itemAmount;
				$itemCheckout->requestReferenceNumber = $sample_reference_number;
				$itemCheckout->redirectUrl = array("success" => url('paymaya_featured_success'),
				"failure" => url('paymaya_featured_failure'),
				"cancel" => url('paymaya_featured_failure'));
				
				//echo "<pre>"; print_r($itemCheckout); exit;
				if ($itemCheckout->execute() === false) {
					$error = $itemCheckout::getError();
					//print_r($error); exit;
					//Session::flash('message',$error['message']);
					return redirect()->back()->withErrors(['errors' => $error['message']]);
				}
				
				if ($itemCheckout->retrieve() === false) {
					$error = $itemCheckout::getError();
					//print_r($error); exit;
					//Session::flash('message',$error['message']);
					return redirect()->back()->withErrors(['errors' => $error['message']]);
				}
				
				Session::put('sess_checkoutId',$itemCheckout->id);
				return redirect()->to($itemCheckout->url);
			}
			else
			{
				//Session::flash('message','No merchant found');
				return Redirect::to('/featured_paymaya_checkout')->withErrors(['errors' => 'No merchant found']);
			}
		}
		/* Checkout success */
		public function checkout_success(Request $request)
		{	
			PayMayaSDK::getInstance()->initCheckout(Session::get('sess_mer_clientId'),Session::get('sess_mer_secretId'),Config::get('env.PAYMAYA_MODE'));
			//PayMayaSDK::getInstance()->initCheckout(env('PAYMAYA_PUBLIC_KEY'),env('PAYMAYA_SECRET_KEY'),Config::get('env.PAYMAYA_MODE'));
			$transaction_id = Session::get('sess_checkoutId');
			if (!$transaction_id) {
				//Session::flash('message','Transaction Id Missing');
				return Redirect::to('/featured_paymaya_checkout')->withErrors(['message' => 'Transaction Id Missing']);
			}
			$itemCheckout = new Checkout();
			$itemCheckout->id = $transaction_id;
			$checkout = $itemCheckout->retrieve();
			//echo '<pre>'; print_r($checkout); exit;
			if($checkout['paymentStatus'] == "PAYMENT_SUCCESS")
			{
				$merchant_midName = explode('-',$checkout['buyer']['middleName']);
				//$user->middleName=$request->from_date.'-'.$request->to_date.'-'.$request->total_price.'-'.$request->feat_num_days.'-'.Session::get('merchantid');
				$from_date 		= $merchant_midName[0];
				$to_date 		= $merchant_midName[1];
				$total_price	= $merchant_midName[2];
				$feat_num_days 	= $merchant_midName[3];
				$merchant_id 	= $merchant_midName[4];
				$storeDet = DB::table('gr_store')->select('st_store_name','id')->where('st_mer_id','=',$merchant_id)->where('st_status','=','1')->first();
				if(empty($storeDet)===true){
					$store_name = '';
					$store_id = '0';
					}else{
					$store_name = '('.$storeDet->st_store_name.')';
					$store_id = $storeDet->id;
				}
				$insertArr = [	'mer_id' 			=> $merchant_id,
				'store_id'			=> $store_id,
				'from_date' 		=> date('Y-m-d',strtotime($from_date)),
				'to_date' 			=> date('Y-m-d',strtotime($to_date)),
				'total_price' 		=> $total_price,
				'total_num_days' 	=> $feat_num_days,
				'payment_method'	=> 'paymaya',
				'commission_status'	=> '2',
				'transaction_id' 	=> $checkout['requestReferenceNumber'],
				'paymaya_pmtId'		=> $checkout['transactionReferenceNumber'],
				'paymaya_receiptnum'=> $checkout['receiptNumber'],
				'paymaya_paid_time'	=> $checkout['paymentDetails']['paymentAt'],
				'paymaya_last4'		=> $checkout['paymentDetails']['last4'],
				'paymaya_cardtype'	=> $checkout['paymentDetails']['cardType'],
				'paymaya_maskedcard'=> $checkout['paymentDetails']['maskedCardNumber'],
				'createdAt'			=> date('Y-m-d H:i:s')
				];
				$insert = insertvalues('gr_merchant_commission',$insertArr);
				/**delete notification **/
				/* ---------------- SEND MAIL TO ADMIN ----------------*/
				
				if(Session::get('mer_business_type')=='1'){
					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
					}else{
					$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
				}
				//The merchant (:merchant_name) requested to make :business_type as featured
				$got_message = (Lang::has(Session::get('mer_lang_file').'.MER_REQUEST_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_REQUEST_FEATURED') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUEST_FEATURED');
				$searchReplaceArray = array(':merchant_name' => ucfirst(Session::get('mer_name')),':business_type' => $business_type, '(:store_name)'=> $store_name);
				$heading = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
				
				$payment_method = (Lang::has(Session::get('mer_lang_file').'.MER_PMTMODE')) ? trans(Session::get('mer_lang_file').'.MER_PMTMODE') : trans($this->MER_OUR_LANGUAGE.'.MER_PMTMODE');
				$transaxn_id	= (Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID');
				$transaxnRef_Num	= (Lang::has(Session::get('mer_lang_file').'.MER_TRAXN_REFNUM')) ? trans(Session::get('mer_lang_file').'.MER_TRAXN_REFNUM') : trans($this->MER_OUR_LANGUAGE.'.MER_TRAXN_REFNUM');
				$receipt_Num	= (Lang::has(Session::get('mer_lang_file').'.MER_RECEIPT_NUM')) ? trans(Session::get('mer_lang_file').'.MER_RECEIPT_NUM') : trans($this->MER_OUR_LANGUAGE.'.MER_RECEIPT_NUM');
				$paid_on	= (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAYMENT_PAID_ON')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAYMENT_PAID_ON') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PAYMENT_PAID_ON');
				$send_mail_data = array('heading'=>$heading,
				'name' => Session::get('mer_name'),
				'email' => Session::get('mer_email'),
				'featured_date' => date('m/d/Y',strtotime($from_date)).' to '.date('m/d/Y',strtotime($to_date)),
				'payment_detail'=>'<tr>
				<td><strong>'.$payment_method.' :</strong></td>
				<td>paymaya</td>
				</tr>
				<tr>
				<td><strong>'.$transaxn_id.' :</strong></td>
				<td>'.$checkout['requestReferenceNumber'].'</td>
				</tr>
				<tr>
				<td><strong>'.$transaxnRef_Num.' :</strong></td>
				<td>'.$checkout['transactionReferenceNumber'].'</td>
				</tr>
				<tr>
				<td><strong>'.$receipt_Num.' :</strong></td>
				<td>'.$checkout['receiptNumber'].'</td>
				</tr>
				<tr>
				<td><strong>'.$paid_on.' :</strong></td>
				<td>'.$checkout['paymentDetails']['paymentAt'].'</td>
				</tr>
				'
				);
				Mail::send('email.featured_store', $send_mail_data, function($message) use($business_type)
				{
					$email               = Session::get('mer_email');
					$name                = Session::get('mer_name');
					$subject = (Lang::has(Session::get('mer_lang_file').'.MER_REQUESTING_FEATURED')) ? trans(Session::get('mer_lang_file').'.MER_REQUESTING_FEATURED') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUESTING_FEATURED');
					$subject = str_replace(':business_type',$business_type,$subject);
					$message->to($email, $name)->subject($subject);
				});
				/* ---------------- EOF MAIL FUNCTION  ----------------*/
				
				//DB::table('gr_notification')->where(['no_status' => '1','no_mer_id' => $merchant_id])->delete();
				$text = (Lang::has(Session::get('mer_lang_file').'.ADMIN_COMM_PAID_SUXUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_COMM_PAID_SUXUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_COMM_PAID_SUXUS');
				Session::flash('message',$text);
				return Redirect::to('/featured_paymaya_checkout');
			}
			else
			{
				//Session::flash('message','Payment Failure');
				$text1 = (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAY_FAIL')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAY_FAIL') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PAY_FAIL');
				return Redirect::to('paymaya_featured_failure')->withErrors(['errors' => $text1]);
			}
		}
		public function checkout_failure(Request $request)
		{
			//print_r($request->all());
			//PayMayaSDK::getInstance()->initCheckout('pk-6y2WX6WhWxfQOg8ezKIUuiJxa7gC4sDvOipn9NFXlwz','sk-BoTm71oqA1jdCd6bwLwxK3QsVPo9ZOcr1dpYfyAPUUd',(\App::environment('production') ? 'SANDBOX' : 'SANDBOX'));
			
			PayMayaSDK::getInstance()->initCheckout(Session::get('sess_mer_clientId'),Session::get('sess_mer_secretId'),Config::get('env.PAYMAYA_MODE'));
			$transaction_id = Session::get('sess_checkoutId');
			if (!$transaction_id) {
				$text1 = (Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANS_ID_MISS')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANS_ID_MISS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_TRANS_ID_MISS');
				return Redirect::to('featured_paymaya_checkout')->withErrors(['errors' => $text1]);
			}
			$itemCheckout = new Checkout();
			$itemCheckout->id = $transaction_id;
			//$itemCheckout->execute();
			$checkout = $itemCheckout->retrieve();
			//Session::flash('message','Payment Failure \n Error Code: '.$checkout['paymentStatus']);
			$text1 = (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAY_ERR_CODE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAY_ERR_CODE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_PAY_ERR_CODE');
			return Redirect::to('/featured_paymaya_checkout')->withErrors(['errors' => $text1.' '.$checkout['paymentStatus']]);
			/*$itemCheckout = new Checkout();
				
				$itemCheckout->id = $transaction_id;
				$itemCheckout->retrieve();
				
				$error = $itemCheckout::getError();
				echo "retrive failure<pre>"; print_r($error); exit;
				if ($itemCheckout->retrieve() === false)
				{
				$error = $itemCheckout::getError();
				echo "retrive failure<pre>"; print_r($error); exit;
				//return redirect()->back()->withErrors(['message' => $error['message']]);
			}*/
		}
	}							