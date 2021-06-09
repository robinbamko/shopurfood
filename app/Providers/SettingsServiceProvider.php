<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Config;
class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

//        echo 'ddddddddddddd';
//        exit;
        $smtp_settings_array = DB::table('gr_general_setting')->get();
        $smtp_settings = DB::table('gr_general_setting')->first();
        $pmt_settings = DB::table('gr_payment_setting')->first();
        if(count($smtp_settings_array) > 0)
        {
            Config::set('mail.host',$smtp_settings->gs_smtp_host);
            Config::set('mail.port',$smtp_settings->gs_smtp_port);
            Config::set('mail.username',$smtp_settings->gs_smtp_email);
            Config::set('mail.password',$smtp_settings->gs_smtp_password);
            Config::set('mail.from.address',$smtp_settings->gs_smtp_email);

            /*Twilio Details*/ 
            Config::set('twilio.twilio.connections.twilio.sid',$smtp_settings->gs_twilio_sid);
            Config::set('twilio.twilio.connections.twilio.token',$smtp_settings->gs_twilio_token);
            Config::set('twilio.twilio.connections.twilio.from',$smtp_settings->gs_twilio_from);

            /*Facebook Details*/ 
            Config::set('services.facebook.client_id',$smtp_settings->facebook_app_id_web);
            Config::set('services.facebook.client_secret',$smtp_settings->facebook_secret_key);
            Config::set('services.facebook.redirect',$smtp_settings->gs_facebook_redirect_url);

            /*Google Details*/ 
            Config::set('services.google.client_id',$smtp_settings->google_client_id_web);
            Config::set('services.google.client_secret',$smtp_settings->google_secret_key);
            Config::set('services.google.redirect',$smtp_settings->google_redirect_url);
        }else{
            Config::set('mail.host','');
            Config::set('mail.port','');
            Config::set('mail.username','');
            Config::set('mail.password','');
            Config::set('mail.from.address','');

            /*Twilio Details*/ 
            Config::set('twilio.twilio.connections.twilio.sid','');
            Config::set('twilio.twilio.connections.twilio.token','');
            Config::set('twilio.twilio.connections.twilio.from','');

            /*Facebook Details*/ 
            Config::set('services.facebook.client_id','');
            Config::set('services.facebook.client_secret','');
            Config::set('services.facebook.redirect','');

             /*Google Details*/ 
            Config::set('services.google.client_id','');
            Config::set('services.google.client_secret','');
            Config::set('services.google.redirect','');
        }
        
        Config::set('env.PAYMAYA_PUBLIC_KEY',$pmt_settings->paymaya_client_id);
        Config::set('env.PAYMAYA_SECRET_KEY',$pmt_settings->paymaya_secret_id);
		if($pmt_settings->paymaya_mode==0) { $paymodepaymaya = 'SANDBOX'; } else { $paymodepaymaya = 'PRODUCTION'; } 
        Config::set('env.PAYMAYA_MODE',$paymodepaymaya);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
