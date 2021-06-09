<?php

namespace App\Http\Middleware;

use Closure;
use Session ;
use Config;
use View;
use App;

class SubadminPrivilege
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
		
			Config::set('subadmin_privilege',['Category','Choices','Merchant','Restaurant','Customer','Item','Delivery_Manager','Delivery_Boy','CMS','FAQ','Review','Order','Commission','Inventory','Cancellation','Refer_Friend','Newsletter','Featured_Resturant','Failed_orders','Delivery_Boy_Map','Reports','Delivery_Commission']);
	
			$allPrev = '0';
			
			if ($request->session()->exists('admin_email')){ 
				$allPrev = '1';
			}

			View::share ('allPrev', $allPrev);
			Config::set('allPrev', $allPrev);
			
			$privileges[] = array();
			
			if(Session::has('session_admin_privileges') == 1){ 
				$privileges = Session::get('session_admin_privileges');
			} 
			
			View::share ('privileges', $privileges); 
			Config::set('privileges', $privileges);
			
			return $next($request);
    }
}



