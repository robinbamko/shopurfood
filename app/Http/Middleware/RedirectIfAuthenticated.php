<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use User;
use App\Customer;
use Session;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $email = Input::get('mail');
        $password = Input::get('pwd');
       
        $cus_check = Customer::login_check($email,$password);
        if($cus_check == 0) /* invalid email */
        {
            return 0;
        }
        elseif($cus_check == -1)    /* invalid password */
        {
            return -1;
        }
        elseif($cus_check == 1) /* login success */
        {
            Session::put('customer_login',1);
            Session::put('customer_name',$cus_check->cus_fname);
            Session::put('customer_id',$cus_check->cus_id);
            Session::put('customer_mail',$cus_check->cus_email);
             if(!session()->has('from'))
            {
                Session::put('from',url()->previous());
            }
            return 1;
        }
        

        return $next($request);
    }
}
