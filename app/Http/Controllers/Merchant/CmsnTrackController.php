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
	
	use Response;
	
	class CmsnTrackController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setLanguageLocaleMerchant();
		}
		/*MERCHANTS LIST */
		public function commision_list()
		{
			if(Session::has('merchantid') == 0)
			{
				return redirect('merchant-login');
			}
			$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_COMMISSION_TRACKING')) ? trans(Session::get('mer_lang_file').'.ADMIN_COMMISSION_TRACKING') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_COMMISSION_TRACKING');
			DB::enableQueryLog();
			$merchantid = Session::get('merchantid');
			$commission_details = MerchantReports::get_commission_list();
			$payment_details 	= MerchantReports::get_payment_details($merchantid); 
			return view('sitemerchant.reports.Manage_Commission')->with('pagetitle',$pagetitle)->with('commission_list',$commission_details)->with('payment_details',$payment_details);
		}
		
		///commission_view_transaction
		
		public function commission_view_transaction($vendor_id)
		{
			if(Session::has('merchantid') == 0) {
				return redirect('merchant-login');
			}
			$pagetitle = (Lang::has(Session::get('mer_lang_file').'.ADMIN_COMM_TRANSACTION')) ? trans(Session::get('mer_lang_file').'.ADMIN_COMM_TRANSACTION') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_COMM_TRANSACTION');
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
			return view('sitemerchant.reports.View_Commission_transaction')->with('pagetitle',$pagetitle)->with('commission_list',$commission_list);
		}
		
		/** send pay request to admin **/
		public function pay_request($amount)
		{
			/** update notification count **/
			$update = DB::table('gr_notification')->insert(['no_mer_id' => Session::get('merchantid'),
			'no_status' => '1' ]);
			/** Send  Mail  **/
    		$merchant_details = DB::table('gr_merchant')->where('id','=',Session::get('merchantid'))->first();
    		$send_mail_data = array(
			'name' => $merchant_details->mer_fname.' '.$merchant_details->mer_lname,
			'amount' => $amount,
			);
			Mail::send('email.pay_request_to_admin', $send_mail_data, function($message)
			{
				$merchant_details = DB::table('gr_merchant')->where('id','=',Session::get('merchantid'))->first();
				
				$adminemail          = $this->admin_mail;
				$merchantname        = $merchant_details->mer_fname.' '.$merchant_details->mer_lname;
				$subject = (Lang::has(Session::get('mer_lang_file').'.MER_PAY_REQUEST')) ? trans(Session::get('mer_lang_file').'.MER_PAY_REQUEST') : trans($this->MER_OUR_LANGUAGE.'.MER_PAY_REQUEST');
				$message->to($adminemail, $merchantname)->subject($subject);
			});
			/** Send mail ends **/
			
			$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_REQ_SENT')) ? trans(Session::get('mer_lang_file').'.ADMIN_REQ_SENT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_REQ_SENT');
			Session::flash('message',$msg);
			
			
			return Redirect::back();
		}
	}	