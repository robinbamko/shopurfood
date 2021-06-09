<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use DB;
use Config;
use Request;
//use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //echo \Request::segment(2); exit;
        /*  D:\xampp\htdocs\projects\edison_grocery_v1\vendor\laravel\framework\src\Illuminate\Auth\EloquentUserProvider.php need to change password in the above file also */
       if(\Request::segment(2)=='agent')
        {
            Config::set('jwt.user', 'App\Agent'); 
            Config::set('auth.providers.users.model', \App\Agent::class);
            Config::set('jwt.userpassword','agent_password');
            Config::set('jwt.usertable','gr_agent');
            Config::set('auth.model', App\Agent::class);
        }
        elseif(\Request::segment(2)=='delivery')
        {
            Config::set('jwt.user', 'App\Delivery_person'); 
            Config::set('auth.providers.users.model', \App\Delivery_person::class);
            Config::set('jwt.userpassword','deliver_password');
            Config::set('jwt.usertable','gr_delivery_member');
            Config::set('auth.model', App\Agent::class);
        }
        elseif(\Request::segment(2)=='merchant')
        {
            Config::set('jwt.user', 'App\Merchant'); 
            Config::set('auth.providers.users.model', \App\Merchant::class);
            Config::set('jwt.userpassword','mer_password');
            Config::set('jwt.usertable','gr_merchant');
            Config::set('auth.model', App\Merchant::class);
        }
        else{
            Config::set('jwt.user', 'App\User'); 
            Config::set('auth.providers.users.model', \App\User::class);
            Config::set('jwt.userpassword','cus_password');
            Config::set('jwt.usertable','gr_customer');
            Config::set('auth.model', App\User::class);
        }
            
        $general_setting = DB::table('gr_general_setting')->select('suspend_status','max_attempt','suspend_time','suspend_duration')->first();
        if(!empty($general_setting))
        {   
            Config::set('suspend_status',$general_setting->suspend_status);
            Config::set('max_attempt',$general_setting->max_attempt);
            Config::set('suspend_duration',($general_setting->suspend_duration=='minutes')?$general_setting->suspend_time:($general_setting->suspend_time*60));
        }
        else
        {
            Config::set('suspend_status',0);
            Config::set('max_attempt',3);
            Config::set('suspend_duration',10);
        }
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
