<?php

namespace App\Http\Middleware;

use Closure;
use Session ;
use DB;

use Redirect;

class merchantSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->exists('merchantid')) {
            // user value cannot be found in session
            Session::flash('message',"Please Login to Continue");
            return redirect('merchant-login');
        }else{
			/*GET LANGUAGE CODE */ 
			$get_default_language = DB::table('gr_language')->where('status','=','1')->where('default_lang','=','1')->first();
			$default_lang = "merchant_en_lang";
			if(empty($get_default_language) === false) //default language is there
			{
				$default_lang = 'merchant_'.$get_default_language->lang_code.'_lang';
			}
			/*EOF GET LANGUAGE CODE */
				
			$check_merchant_status = DB::table('gr_merchant')->where('id','=',Session::get('merchantid'))->first();
			if(empty($check_merchant_status)===false){
				if($check_merchant_status->mer_status==0){
					$errormsg = trans($default_lang.'.MERCHANT_BLOCKED');
					Session::flash('message',$errormsg);
					Session::forget('merchantid');
					Session::forget('mer_email');
					Session::forget('mer_name');
					Session::forget('mer_has_shop');
					//Session::flush();
					return Redirect::to('merchant-login');
					//return Redirect::to('merchant-logout');
				}
				elseif($check_merchant_status->mer_status==2){
					$errormsg = trans($default_lang.'.MERCHANT_DELETED');
					Session::flash('message',$errormsg);
					Session::forget('merchantid');
					Session::forget('mer_email');
					Session::forget('mer_name');
					Session::forget('mer_has_shop');
					//Session::flush();
					return Redirect::to('merchant-login');
				}
			}
			$check_store = DB::table('gr_store')->where('st_mer_id','=',Session::get('merchantid'))->first();
			if(empty($check_store)===false){
				
				//CHECKING INACTIVE
				if($check_store->st_status==0){
					$errormsg = trans($default_lang.'.MER_STORE_BLOCKED');
					Session::flash('message',$errormsg);
					Session::forget('merchantid');
					Session::forget('mer_email');
					Session::forget('mer_name');
					Session::forget('mer_has_shop');
					//Session::flush();
					return Redirect::to('merchant-login');
					//return Redirect::to('merchant-logout');
				}
				elseif($check_store->st_status==2){
					$errormsg = trans($default_lang.'.MER_STORE_DELETED');
					Session::flash('message',$errormsg);
					Session::forget('merchantid');
					Session::forget('mer_email');
					Session::forget('mer_name');
					Session::forget('mer_has_shop');
					//Session::flush();
					return Redirect::to('merchant-login');
				}
			}
		}

        return $next($request);
    }
}
