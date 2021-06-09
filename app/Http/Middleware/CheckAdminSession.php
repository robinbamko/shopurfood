<?php

namespace App\Http\Middleware;

use Closure;
use Session ;

class CheckAdminSession
{

    public function handle($request, Closure $next)
    {	
		
        if (!$request->session()->exists('admin_id')) {
            // user value cannot be found in session
            Session::flash('message',"Please Login to Continue");
            return redirect('admin-login');
        }
		else{
			if(Session::has('admin_type') == 1){ 
				if(Session::get('admin_type')=='sub_admin'){
					$get_subadmin_details = get_details('gr_subadmin',['id'=>Session::get('admin_id')]);
					if(empty($get_subadmin_details)===false){
						$priv = unserialize($get_subadmin_details->sub_privileges); 
						if(!empty($priv)){
							Session::put('session_admin_privileges',$priv);
							} else {
							Session::put('session_admin_privileges',array());					
						}
					}
				}
			}
		}
        return $next($request);
    }

}
