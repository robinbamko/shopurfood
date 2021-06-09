<?php

namespace App\Http\Middleware;

use Closure;
use Session ;
use DB;
use Lang;
use Redirect;
class CheckUserSession
{

    public function handle($request, Closure $next)
    {	
		
        if (!$request->session()->exists('customer_id')) {
            // user value cannot be found in session
            Session::flash('val_errors',"Please Login to continue");
			return redirect('/');
        }else{
			$checkBlock = DB::table('gr_customer')->where('cus_id','=',Session::get('customer_id'))->first();
			if(empty($checkBlock)===false){
				$status = $checkBlock->cus_status;
				if($status==0){
					Session::forget('customer_login');
					Session::forget('customer_id');
					Session::forget('customer_details');
					Session::forget('customer_mail');
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_BLOCKED')) ? trans(Session::get('front_lang_file').'.FRONT_BLOCKED') : trans($this->FRONT_LANGUAGE.'.FRONT_BLOCKED');
					Session::flash('val_errors',$msg);
					if($request->ajax()){
						header('HTTP/1.1 500 Internal Server Booboo');
						header('Content-Type: application/json; charset=UTF-8');
						$result=array();
						$result['message'] = 'errors';
						die(json_encode($result));
					}else{
						return  Redirect::to('/');
					}
				}elseif($status==2){
					Session::forget('customer_login');
					Session::forget('customer_id');
					Session::forget('customer_details');
					Session::forget('customer_mail');
					$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_DELETED')) ? trans(Session::get('front_lang_file').'.FRONT_DELETED') : trans($this->FRONT_LANGUAGE.'.FRONT_DELETED');
					Session::flash('val_errors',$msg);
					return Redirect::to('/');
				}
			}else{
				Session::forget('customer_login');
				Session::forget('customer_id');
				Session::forget('customer_details');
				Session::forget('customer_mail');
				Session::flash('val_errors',"You are not available!");
				return redirect('/');
			}
		}

        return $next($request);
    }

}
