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
	
	
	class ReferFriendReportController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		/*MERCHANTS LIST */
		public function refer_friend_list(Request $request)
		{
			if(Session::has('admin_id') == 0)
			{
				return redirect('admin-login');
			}
			if($request->ajax())
			{
				$this->cancelled_orders_ajax($request);
				exit;
			}
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REFERFRIEND_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_REFERFRIEND_REPORT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REFERFRIEND_REPORT');
			return view('Admin.reports.refer_friend')->with('pagetitle',$pagetitle);
		}
		
		///commission_view_transaction
		
		public function cancelled_orders_ajax($request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array( 
				0=>'re_id',
				1=>'cus_email',
				2=> 'referre_email',
				3=>'re_offer_percent',
				4=>'re_offer_amt'
				);
				
				/*To get Total count */
				$q=array();
				$totalData = DB::table('gr_referal')->select('gr_referal.re_id')->join('gr_customer','gr_customer.cus_id', '=', 'gr_referal.re_id')->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				$referrerMail_search = $request->referrerMail_search;
				$referralMail_search = $request->referralMail_search;
				
				$sql = DB::table('gr_referal')->select('gr_referal.re_id','gr_customer.cus_email','gr_referal.referre_email','gr_referal.re_offer_percent','gr_referal.re_offer_amt')->join('gr_customer','gr_customer.cus_id', '=', 'gr_referal.referral_id');
				if($referrerMail_search!='')
				{
					$sql->whereRaw("gr_customer.cus_email like '%".$referrerMail_search."%'");
				}
				if($referralMail_search!='')
				{
					$sql->whereRaw("gr_referal.referre_email like '%".$referralMail_search."%'");
				}
				$totalFiltered = $sql->count();
				$posts = $sql->skip($start)->take($limit)->get();
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['referrerEmail'] = $post->cus_email;
						$nestedData['referralEmail'] = $post->referre_email;
						$nestedData['offerPercent'] = $post->re_offer_percent.' %';
						$nestedData['offerAmt'] = number_format($post->re_offer_amt,2);
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
		
	}		