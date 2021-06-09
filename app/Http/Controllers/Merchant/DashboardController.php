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
	
	use Image;
	
	use App\Merchant;
	
    use Carbon\Carbon;
	
    use File;
	
	class DashboardController extends Controller
	{
		
		/*
			|--------------------------------------------------------------------------
			| Default Home Controller
			|--------------------------------------------------------------------------
			|
			| You may wish to use controllers instead of, or in addition to, Closure
			| based routes. That's great! Here is an example controller method to
			| get you started. To route to this controller, just add the route:
			|
			|	Route::get('/', 'HomeController@showWelcome');
			|
		*/
		public function __construct(){
			parent::__construct();
			$this->setLanguageLocaleMerchant();
		}
		
		
		public function merchant_dashboard()
		{	
			if (Session::has('merchantid')) {
				$merid  = Session::get('merchantid'); 
				$type = Session::get('mer_business_type'); //1 - product,2-item
				$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_DASHBOARD')) ? trans(Session::get('mer_lang_file').'.MER_DASHBOARD') : trans($this->MER_OUR_LANGUAGE.'.MER_DASHBOARD');
				$item_count = $item_active = $item_deactive = 0;
				$store_id = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first(); 
				if(empty($store_id) === false)
				{
					//				$item_count 		= Merchant::get_count1('gr_product','pro_status',['pro_type' => $type,'pro_store_id'=>$store_id->id],'pro_id')->count();
					//					$item_active	= Merchant::get_count2('gr_product',['pro_status' => '1','pro_type' => $type,'pro_store_id'=>$store_id->id],'pro_id');
					//					$item_deactive	= Merchant::get_count2('gr_product',['pro_status' => '0','pro_type' => $type,'pro_store_id'=>$store_id->id],'pro_id');
					
					
                    $item_count 		= Merchant::get_count1('gr_product','pro_status',['pro_type' => $type,'pro_store_id'=>$store_id->id],'pro_id')->count();
                    $item_active	= Merchant::get_count2('gr_product',['pro_status' => '1','pro_type' => $type,'pro_store_id'=>$store_id->id],'pro_id');
                    $item_deactive	= Merchant::get_count2('gr_product',['pro_status' => '0','pro_type' => $type,'pro_store_id'=>$store_id->id],'pro_id');
					
					
				}
				
				
                $order_count 		= DB::table('gr_order')->select('ord_id')->where('ord_merchant_id','=',$merid)->groupBy('ord_transaction_id')->get()->count();
                $delivered_count	= Merchant::get_count2('gr_order',['ord_status' => '8','ord_merchant_id' => $merid,'ord_cancel_status' => '0'],'ord_id','ord_transaction_id');
				
				
                //Add by karthik on 28122018
                $lastorders=DB::table('gr_order')->where('ord_merchant_id','=',$merid)->orderby('ord_id','desc')->limit(5)->get();
				
				
                $recentorders=DB::table('gr_order')->where('ord_merchant_id','=',$merid)
				->whereDate( 'ord_date', '>=', date('Y-m-d',strtotime("-1 days")))
				->whereDate('ord_date','<=', Carbon::today())
				->orderby('ord_id','desc')->limit(5)->get();
				
				
				
				return view('sitemerchant.merchant_dashboard')->with(['pagetitle' => $page_title,'item_count' => $item_count,'item_active' => $item_active,'delivery_count' => $delivered_count,'order_count' => $order_count,'item_deactive' => $item_deactive,'lastorders'=>$lastorders,'recentorders'=>$recentorders]);
				
			} 
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		public function merchant_profile(Request $request)
		{
			if (Session::has('merchantid')) {
				$merid  = Session::get('merchantid');
				//$this->setLanguageLocaleMerchant(); 
				//starts here
				$pagetitle = (Lang::has(Session::get('mer_lang_file').'.MER_EDIT_PROFILE')) ? trans(Session::get('mer_lang_file').'.MER_EDIT_PROFILE') : trans($this->MER_OUR_LANGUAGE.'.MER_EDIT_PROFILE');
				$getvendors = DB::table('gr_merchant')->where('id', '=', $merid)->first();
				if($_POST) { 
					/*print_r($_POST); exit;Array ( [_token] => h2XC1URxU6T7mdXKqucXPkHr72ClmTsD8HgxZVzy [mer_fname] => Nagoor [mer_lname] => Meeran [mer_email] => nagoor@pofitec.com [mer_phone] => 8220807756 [mer_cancel_policy] => test [refund_status] => Yes [mer_commission] => 0.00 [mer_mininmum_order] => 0 [profile_photo] => [mer_paynamics_status] => No [mer_paynamics_clientid] => [mer_paynamics_secretid] => [mer_paynamics_mode] => Yes [mer_paymaya_status] => No [mer_paymaya_clientid] => [mer_paymaya_secretid] => [mer_paymaya_mode] => Yes [mer_netbank_status] => No [mer_bank_name] => [mer_branch] => [mer_bank_accno] => [mer_ifsc] => [street_number] => [route] => [postal_code] => [mer_location] => 1 [country] => 3 [mer_state] => s [mer_city] => 2 ) */ 
					$this->validate($request, 
					[
					'mer_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
					'mer_lname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
					'mer_email'=>'Required|Email',
					'mer_phone'=>'Required',
					'mer_cancel_policy'=>'Required',
					'mer_location'=>'Required',
					'country'=>'Required',
					'mer_state'=>'Required',
					'mer_city'=>'Required',
					'idproof'	=>'sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
					'license'	=>'sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
					],['mer_fname.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_FNAME')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_FNAME') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_FNAME'), 
					'mer_lname.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_LNAME')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_LNAME') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_LNAME'),
					'mer_email.email'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_EMAIL') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_EMAIL'),
					'mer_phone.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PHONE')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PHONE') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_PHONE'),
					'mer_cancel_policy.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CANCELPOLICY')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CANCELPOLICY') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_CANCELPOLICY'),
					'mer_location.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_LOCATION')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_LOCATION') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_LOCATION'),
					'country.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_COUNTRY')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_COUNTRY') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_COUNTRY'),
					'mer_state.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_STATE')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_STATE') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_STATE'),
					'mer_city.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CITY')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CITY') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_CITY'),					
					'idproof.dimensions'    => (Lang::has(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INVALID_DIMENSION_ID'),					
					'license.dimensions'    => (Lang::has(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_LICENSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_LICENSE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INVALID_DIMENSION_LICENSE'),					
					]); 
					//echo 'payanamics status';exit;// .$_POST['mer_paynamics_status']; exit;
					$old_id = Input::get('old_idproof');
					$old_license = Input::get('old_license');
					if($old_id =='')
					{
						$this->validate($request,
						['idproof'	=>'Required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_height=300,max_width=300'
						],
						['idproof.required' => (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPLOAD_ID_PROOF')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPLOAD_ID_PROOF') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPLOAD_ID_PROOF'),
						'idproof.dimensions'    => (Lang::has(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INVALID_DIMENSION_ID')
						]);
						
					}
					if($old_license == '')
					{
						$this->validate($request,
						['license'	=>'Required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300,max_height=300,max_width=300'
						],
						['license.required' => (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPLOAD_LICENSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPLOAD_LICENSE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPLOAD_LICENSE'),
						'license.dimensions'    => (Lang::has(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVALID_DIMENSION_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INVALID_DIMENSION_ID')
						]);
					}
					if($request->hasFile('profile_photo')!='')
					{
						$this->validate($request, ['profile_photo'=>'sometimes|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,min_height=300'],[ 'profile_photo.sometimes'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PHOTO')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PHOTO') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_PHOTO') ]);
					}
					if($_POST['mer_paynamics_status']=='Publish')
					{
						$this->validate($request, 
						[
						'mer_paynamics_clientid'=>'Required',
						'mer_paynamics_secretid'=>'Required'
						],
						[
						'mer_paynamics_clientid.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_CLIENT')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_CLIENT') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_PAYNA_CLIENT') , 
						'mer_paynamics_secretid.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_SECRET')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_SECRET') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_PAYNA_SECRET')
						]
						); 
					}
					if($_POST['mer_paymaya_status']=='Publish')
					{
						$this->validate($request, 
						[
						'mer_paymaya_clientid'=>'Required',
						'mer_paymaya_secretid'=>'Required'
						],
						[ 
						'mer_paymaya_secretid.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_SECRET')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_SECRET') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_PAYMA_SECRET'),
						'mer_paymaya_clientid.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_CLIENT')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_CLIENT') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_PAYMA_CLIENT')
						]
						); 
					}
					if($_POST['mer_netbank_status']=='Publish')
					{
						$this->validate($request, 
						[
						'mer_bank_name'=>'Required',
						'mer_branch'=>'Required',
						'mer_bank_accno'=>'Required',
						'mer_ifsc'=>'Required'
						],
						[ 
						'mer_bank_name.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_BANK')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_BANK') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_BANK') ,
						'mer_branch.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_BRANCH')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_BRANCH') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_BRANCH'),
						'mer_bank_accno.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_ACCNO')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_ACCNO') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_ACCNO'),
						'mer_ifsc.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_IFSC')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_IFSC') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_IFSC') 
						]
						); 
					}
					/* paypal or net banking details mandatory even stripe in publish status  */
					if($_POST['mer_paymaya_status']=='Unpublish' && $_POST['mer_netbank_status']=='Unpublish')
					{
						$msg = (Lang::has(Session::get('mer_lang_file').'.MER_FILL_PAY_NET')) ? trans(Session::get('mer_lang_file').'.MER_FILL_PAY_NET') : trans($this->MER_OUR_LANGUAGE.'.MER_FILL_PAY_NET');
						return Redirect::to('merchant_profile')->withErrors(['errors'=> $msg])->withInput();
					}
					if($request->hasFile('profile_photo')!='')
					{ 
						$avatar = $request->file('profile_photo'); 
						$filename = 'vendor_'.$merid. '.' . $avatar->getClientOriginalExtension();
						Image::make($avatar)->resize(300, 300)->save(public_path('/images/vendor_photos/' .$filename ));
						//$VendorUpdateDetailss = array('profile_photo'=>$filename,);
						//DB::table('tb_vendor')->where('id', '=', $id)->update($VendorUpdateDetailss);
					}
					else
					{
						$filename=$_POST['old_profile_foto'];
					}
					
					$idproof_image = Input::get('old_idproof');
		            if($request->hasFile('idproof'))
		            {
		                $old_image = Input::get('old_idproof');
		                $image_path = public_path('images/merchant/').$old_image;  // Value is not URL but directory file path
		                if(File::exists($image_path))
		                {
		                    $a =   File::delete($image_path);
							
						}
		                $idproof_image = 'Id_Proof_'.rand().'.'.request()->idproof->getClientOriginalExtension();
		                $destinationPath = public_path('images/merchant');
		                $Idproof = Image::make(request()->idproof->getRealPath())->resize(300, 300);
		                $Idproof->save($destinationPath.'/'.$idproof_image,80);
		                //$rcbook_image
					}
		            $license_image = Input::get('old_license');
		            if($request->hasFile('license'))
		            {
		                $old_image = Input::get('old_license');
		                $image_path = public_path('images/merchant/').$old_image;  // Value is not URL but directory file path
		                if(File::exists($image_path))
		                {
		                    $a =   File::delete($image_path);
							
						}
		                $license_image = 'Licence_'.rand().'.'.request()->license->getClientOriginalExtension();
		                $destinationPath = public_path('images/merchant');
		                $License = Image::make(request()->license->getRealPath())->resize(300, 300);
		                $License->save($destinationPath.'/'.$license_image,80);
					}
					$profile_det = array(
					'mer_fname'				=>Input::get('mer_fname'),
					'mer_lname'				=>Input::get('mer_lname'),
					'mer_email'				=>Input::get('mer_email'),
					'mer_phone'				=>Input::get('mer_phone'),
					'mer_cancel_policy'		=>Input::get('mer_cancel_policy'),
					'refund_status'			=>Input::get('refund_status'),
					'cancel_status'			=>Input::get('cancel_status'),
					'mer_commission'		=>Input::get('mer_commission'),
					'mer_profile_img'		=>$filename,
					'mer_paynamics_status'	=>Input::get('mer_paynamics_status'),
					'mer_paynamics_clientid'=>Input::get('mer_paynamics_clientid'),
					'mer_paynamics_secretid'=>Input::get('mer_paynamics_secretid'),
					'mer_paymaya_status'	=>Input::get('mer_paymaya_status'),
					'mer_paymaya_clientid'	=>Input::get('mer_paymaya_clientid'),
					'mer_paymaya_secretid'	=>Input::get('mer_paymaya_secretid'),
					'mer_netbank_status'	=>Input::get('mer_netbank_status'),
					'mer_bank_name'			=>Input::get('mer_bank_name'),
					'mer_branch'			=>Input::get('mer_branch'),
					'mer_bank_accno'		=>Input::get('mer_bank_accno'),
					'mer_ifsc'				=>Input::get('mer_ifsc'),
					'mer_location'			=>Input::get('mer_location'),
					'mer_country'			=>Input::get('country'),
					'mer_state'				=>Input::get('mer_state'),
					'mer_city'				=>Input::get('mer_city'),
					'idproof' 				=>   $idproof_image,
            		'license' 				=>   $license_image,
					);		
					//print_r($profile_det); exit;	
					//DB::connection()->enableQueryLog();
					DB::table('gr_merchant')->where('id', '=', $merid)->update($profile_det);
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
					
					$message = (Lang::has(Session::get('mer_lang_file').'.MER_PROFILE_UPDATED')) ? trans(Session::get('mer_lang_file').'.MER_PROFILE_UPDATED') : trans($this->MER_OUR_LANGUAGE.'.MER_PROFILE_UPDATED');
					return redirect('merchant_profile')->with('message',$message);
				}
				else
				{
					return view('sitemerchant.merchant_editProfile')->with('pagetitle',$pagetitle)->with('getvendor',$getvendors);
				}
				
				
				} else {
				return Redirect::to('merchant-login');
			}
		}
		public function change_password(Request $request)
		{
			if (Session::has('merchantid')) {
				$merid  = Session::get('merchantid');
				//$this->setLanguageLocaleMerchant(); 
				//starts here
				$pagetitle = (Lang::has(Session::get('mer_lang_file').'.MER_CHANGE_PASS')) ? trans(Session::get('mer_lang_file').'.MER_CHANGE_PASS') : trans($this->MER_OUR_LANGUAGE.'.MER_CHANGE_PASS');
				//$getvendors = DB::table('gr_merchant')->where('id', '=', $merid)->first();
				if($_POST) { 
					$this->validate($request, 
					[
					'old_pwd'=>'Required',
					'new_pwd'=>'Required',
					'conf_pwd'=>'Required|same:new_pwd'
					],['old_pwd.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_OLDPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_OLDPWD') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_OLDPWD'), 
					'new_pwd.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_NEWPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_NEWPWD') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_NEWPWD'),
					'conf_pwd.required'    => (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CONPWD')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CONPWD') : trans($this->MER_OUR_LANGUAGE.'.MER_ENTER_CONPWD')				
					]); 
					$old_pwd      = Input::get('old_pwd');
					$new_pwd      = Input::get('new_pwd');
					$conf_pwd	  = Input::get('conf_pwd');
					//DB::connection()->enableQueryLog();
					$oldpwdcheck = Merchant::check_oldpwd($merid, $old_pwd);
					//$query = DB::getQueryLog();
					//print_r($query);
					//print_r($oldpwdcheck); echo count($oldpwdcheck); exit;
					if (count($oldpwdcheck) > 0) {
						Merchant::update_newpwd($merid, $conf_pwd);
						
                        $message = (Lang::has(Session::get('mer_lang_file').'.MER_PASSWORD_CHANGED_SUCCESSFULLY')) ? trans(Session::get('mer_lang_file').'.MER_PASSWORD_CHANGED_SUCCESSFULLY') : trans($this->MER_OUR_LANGUAGE.'.MER_PASSWORD_CHANGED_SUCCESSFULLY');
						
                        return Redirect::to('merchant_change_password')->with('message', $message);
					}
					else
					{
						$message = (Lang::has(Session::get('mer_lang_file').'.MER_OLD_PASSWORD_DONOT_MATCH')) ? trans(Session::get('mer_lang_file').'.MER_OLD_PASSWORD_DONOT_MATCH') : trans($this->MER_OUR_LANGUAGE.'.MER_OLD_PASSWORD_DONOT_MATCH');
						return Redirect::to('merchant_change_password')->withErrors(['password_error'=>$message])->withInput();
					}
				}
				else
				{
					return view('sitemerchant.merchant_changePassword')->with('pagetitle',$pagetitle);
				}
				
				} else {
				return Redirect::to('merchant-login');
			}
		}

		public function refresh_mer_notification(Request $request)
		{	
			$store_id = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
			if(empty($store_id->id) === false){
				$storeidIs = $store_id->id;
			}else{
				$storeidIs = 0;
			}
			

			$item_notifyCount = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','2')->where('pro_status','!=','2')->where('gr_product.pro_store_id','=',$storeidIs)->count();

			

			$ordernotifyCount = DB::table('gr_general_notification')->where('receiver_id','=',Session::get('merchantid'))->where('receiver_type','=','gr_merchant')->where('read_status','=','0')->count();


			$total_notifyCount = $item_notifyCount+$ordernotifyCount;
			echo $total_notifyCount.'`'.$item_notifyCount.'`'.$ordernotifyCount;
			//echo '7'.'`'.'7'.'`'.'7';
			exit;
		}
	}
