<?php
	
	namespace App\Http\Controllers;
	namespace App\Http\Controllers\Front;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use Socialite;
	use Exception;
	use DB;
	use Session;
	use Redirect;
	use GuzzleHttp\Client;
	use Lang;
	class FacebookController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function redirectToFacebook()
		{
			//return $this->socialite->driver($provider)->redirect();
			return Socialite::driver('facebook')->redirect();
		}
		
		
		/**
			* Create a new controller instance.
			*
			* @return void
		*/
		public function handleFacebookCallback()
		{
			try {
				$user = Socialite::driver('facebook')->user();
				
				$create['name'] = $user->getName();
				$create['email'] = $user->getEmail();
				$create['facebook_id'] = $user->getId();
				$create['access_token']=$user->token;
				Session::put('access_token',$user->token);
				$check_already_exsist = DB::table('gr_customer')->where('cus_email','=',$create['email'])->where('cus_status','!=','2')->first();
				if(empty($check_already_exsist) === false)
				{
					
					
					$update = ['cus_facebook_id' => $create['facebook_id']];
					$where = ['cus_id' => $check_already_exsist->cus_id];
					updatevalues('gr_customer',$update,$where);
					Session::put('customer_login',1);
					Session::put('customer_id',$check_already_exsist->cus_id);
					Session::put('customer_mail',$check_already_exsist->cus_email);
					$msg = 'LoggedIn Successfully!';
					Session::flash('success',$msg);
					/* save customer shipping address */
					if(Session::has('shipping_session') == 1)
					{
						$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',$check_already_exsist->cus_id)->first();
						if(empty($shipAddDet)===false){
							$update = updatevalues('gr_shipping',Session::get('shipping_session'),['sh_cus_id'=>$check_already_exsist->cus_id]);
						}
						else
						{
							$shipArray = Session::get('shipping_session');
							$shipArray['sh_cus_id']=$check_already_exsist->cus_id;
							$insert = insertvalues('gr_shipping',$shipArray);
						}
					}
				}
				else
				{
					$insertArr = ['cus_fname' => $create['name'],
					'cus_email' => $create['email'], 
					'cus_facebook_id' => $create['facebook_id'], 
					'cus_status' => '1', 
					'cus_login_type'=>'3',
					'cus_created_date'=>date('Y-m-d')];
					$insert = insertvalues('gr_customer',$insertArr);
					$lastinsertid = DB::getPdo()->lastInsertId();
					Session::put('customer_login',1);
					Session::put('customer_id',$lastinsertid);
					Session::put('customer_mail',$create['email']);
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REG_SUXUS')) ? trans(Session::get('front_lang_file').'.FRONT_REG_SUXUS') : trans($this->FRONT_LANGUAGE.'.FRONT_REG_SUXUS');
					Session::flash('success',$msg);
					/* save customer shipping address */
					
					if(Session::has('shipping_session') == 1)
					{
						$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',$lastinsertid)->first();
						if(empty($shipAddDet)===false)
						{
							$update = updatevalues('gr_shipping',Session::get('shipping_session'),['sh_cus_id'=>$lastinsertid]);
						}
						else
						{
							$shipArray = Session::get('shipping_session');
							$shipArray['sh_cus_id']=$lastinsertid;
							$insert = insertvalues('gr_shipping',$shipArray);
						}
					}
				}
				return Redirect::to('/');
			} catch (Exception $e) {
				return redirect('auth/facebook');
			
			}
		}
		public function logout()
		{
			$user = Socialite::driver('facebook')->user();
			print_r($user); exit;
		}
	}
