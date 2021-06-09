<?php

namespace App\Http\Middleware;

use Closure;
use DB , Session ;
use App;
use View;
use App\Settings;
use Config;

class FrontLanguage
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


        $this->get_active_language = DB::table('gr_language')->where('status','=','1')->get();
        //print_r($get_Adminactive_language); exit;
        $get_default_language = DB::table('gr_language')->where('status','=','1')->where('default_lang','=','1')->first();
        $this->FRONT_LANGUAGE = "front_en_lang";
        if(Session::has('front_lang_code') != 1) // language not set
        {
            if(empty($get_default_language) === false) //default language is there
            {
                Session::put('front_lang_code',$get_default_language->lang_code);
                Session::put('front_lang_file','front_'.$get_default_language->lang_code.'_lang');
                $this->FRONT_LANGUAGE = 'front_'.$get_default_language->lang_code.'_lang';
                $front_selected_lang_code = $get_default_language->lang_code;
            }
            else  //default language is not there.set english as default
            {
                Session::put('front_lang_code','en');
                Session::put('front_lang_file','front_en_lang');
                $this->FRONT_LANGUAGE = "front_en_lang";
                $front_selected_lang_code = "en";
            }
        }
        else  //  language is set
        {
            $front_selected_lang_code = Session::get('front_lang_code');
            Session::put('front_lang_file','front_'.Session::get('front_lang_code').'_lang');
            $this->FRONT_LANGUAGE  = 'front_'.$front_selected_lang_code.'_lang';
        }
         if(Session::get('front_lang_code') == 'ar')
         {
          $direction = 'rtl';  
         }
         else
         {
          $direction = 'ltr';
         }
         
         View::share('dir',$direction);
         $request->attributes->add(['FR_LANG' => $this->FRONT_LANGUAGE]);
         /*echo "test";
         echo \Request::get('FR_LANG'); exit;*/
        $logo_settings_details = Settings::get_logo_settings_details();
        View::share('logo_settings_details',$logo_settings_details);
        View::share('FRONT_LANGUAGE',$this->FRONT_LANGUAGE);
        View::share ('selected_lang_code', $front_selected_lang_code);
        View::share('Active_language',$this->get_active_language);
        app()->setLocale(Session::get('front_lang_code'));

		//print_r(app()->getLocale(Session::get('front_lang_file')));
		//exit;
        $this->general_setting = DB::table('gr_general_setting')->first();

        $this->cms_setting = DB::table('gr_cms')->where('page_status','=','1')->where('page_status','=', '1')->get();
        $cms_help= DB::table('gr_cms')->where('slug','=','help')->first();
        if(empty($cms_help)===false)
        {
            $this->help = $cms_help;
        }
        else
        {

            $insertArr = array('page_title_en' => 'Help','description_en' => 'Help Description','page_status' => '1','page_name' => 'Help','slug' => 'help');
            DB::table('gr_cms')->insert($insertArr);

            $cms_help = DB::table('gr_cms')->where('slug','=','help')->where('page_status','=', '1')->first();
            $this->help = $cms_help;

        }
        $cms_aboutus= DB::table('gr_cms')->where('slug','=','about-us')->where('page_status','=', '1')->first();
        if(empty($cms_aboutus)===false)
        {

        }
        else
        {
            $insertArr = array('page_title_en' => 'About Us','description_en' => 'About Us Description','page_status' => '1','page_name' => 'About Us','slug' => 'about-us');
            DB::table('gr_cms')->insert($insertArr);
        }
        $cms_terms= DB::table('gr_cms')->where('slug','=','terms')->where('page_status','=', '1')->first();
        if(empty($cms_terms)===false)
        {
            $this->terms = $cms_terms;
        }
        else
        {
            $insertArr = array('page_title_en' => 'Terms and Conditions','description_en' => 'Terms of use Description','page_status' => '1','page_name' => 'Terms and Conditions','slug' => 'terms');
            DB::table('gr_cms')->insert($insertArr);
            $gt_cms_terms= DB::table('gr_cms')->where('slug','=','terms')->where('page_status','=', '1')->first();
            $this->terms = $gt_cms_terms;

        }
        $cms_privacy= DB::table('gr_cms')->where('slug','=','privacy-policy')->where('page_status','=', '1')->first();
        if(empty($cms_privacy)===false)
        {
            $this->policy = $cms_privacy;
        }
        else
        {
            $insertArr = array('page_title_en' => 'Privacy and Policy','description_en' => 'Privacy and Policy Description','page_status' => '1','page_name' => 'Privacy and Policy','slug' => 'privacy-policy',);
            DB::table('gr_cms')->insert($insertArr);
            $cms_privacy= DB::table('gr_cms')->where('slug','=','privacy-policy')->where('page_status','=', '1')->first();
            $this->policy = $cms_privacy;

        }


        View::share("help_cms",$this->help);
        View::share("policy",$this->policy);
        View::share("terms",$this->terms);
        if(empty($this->general_setting) === false)
        {
            $sitename = (Session::get('front_lang_code') == 'en') ? 'gs_sitename' : 'gs_sitename_'.Session::get('front_lang_code');

            View::share("SITENAME",$this->general_setting->$sitename);
            View::share("SITE_BANNER_TEXT",$this->general_setting->gs_banner_text);
            View::share("FOOTERNAME",$this->general_setting->footer_text);
           
            if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en') {

                View::share("FOOTERDES", $this->general_setting->gs_footerdesc);
            }else{
                $footer_des_tbl = 'gs_footerdesc_'.session::get('front_lang_code');
                if($footer_des_tbl != '') {
                    View::share("FOOTERDES", $this->general_setting->$footer_des_tbl);
                }
            }
            View::share("HIPPO_STATUS",$this->general_setting->gs_hippo_chat_status);
            View::share("HIPPO_SECRET_KET",$this->general_setting->gs_hippo_secret_key);
            View::share("SITEPHONE", $this->general_setting->gs_phone);
            View::share("SITEEMAIL", $this->general_setting->gs_email);

            if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en'){
                if($this->general_setting->footer_text != '') {
                    View::share("SITEFOOTERTEXT", $this->general_setting->footer_text);
                }
            }else{
                $footer_txt_tbl = 'footer_text_'.session::get('front_lang_code');
                if($this->general_setting->$footer_txt_tbl != '') {
                    View::share("SITEFOOTERTEXT", $this->general_setting->$footer_txt_tbl);
                }else{
					View::share("SITEFOOTERTEXT", $this->general_setting->footer_text);
				}
            }
            View::share("SITEFACEBOOK", $this->general_setting->facebook_page_url);
            View::share("SITETWITTER", $this->general_setting->twitter_page_url);
            View::share("SITELINKEDIN", $this->general_setting->linkedin_page_url);
            View::share("SITEGOOGLE", $this->general_setting->google_page_url);
            View::share("SITEGOOLGEPLAY", $this->general_setting->gs_playstore_url);
            View::share("SITEAPPLEPLAYSTORE", $this->general_setting->gs_apple_appstore_url);
            View::share("MAP_KEY", $this->general_setting->google_map_key_web);
            View::share("MAIL_VERIFY_STATUS", $this->general_setting->mail_verification_status);
            Config::set('admin_mail',$this->general_setting->gs_email);
            define("TWILIO_STATUS",$this->general_setting->otp_verification_status);
            define("INVENTORY_STATUS",$this->general_setting->gs_show_inventory);
            View::share("TWILIO_STATUS", $this->general_setting->otp_verification_status);
            View::share("SELF_PICKUP_STATUS", $this->general_setting->self_pickup_status);
            View::share("PW_PROTECT", $this->general_setting->gs_password_protect);
            View::share("SHOW_CAPTCHA", $this->general_setting->gs_show_captcha);
            Config::set("COMMON_COMMI", $this->general_setting->common_commission);

            View::share("LOGINIMAGE", $this->general_setting->gs_login_image);
            if($this->general_setting->gs_delivery_fee_status == 1)
            {
                View::share('delivery_fee',$this->general_setting->gs_delivery_fee);
                View::share('delivery_fee_curr',$this->general_setting->gs_currency_code);
                Config::set('delivery_fee',$this->general_setting->gs_delivery_fee);
                Config::set('delivery_fee_type',$this->general_setting->gs_del_fee_type);
                Config::set('delivery_km_fee',$this->general_setting->gs_km_fee);
            }
            else
            {
                View::share('delivery_fee',0);
                View::share('delivery_fee_curr','USD');
                Config::set('delivery_fee',0);
                Config::set('delivery_fee_type','common_fee');
                Config::set('delivery_km_fee',0);
            }
			View::share("AGENTMODULE",$this->general_setting->gs_agent_module);
        }
        else
        {
			View::share("AGENTMODULE","0");
            View::share("SITENAME","ePickMeUp");
            View::share("SITE_BANNER_TEXT","Meets All Your Needs");
            View::share("FOOTERNAME","ePickMeUp");
            View::share("HIPPO_STATUS",0);
            View::share("MAP_KEY","AIzaSyCsDoY1OPjAqu1PlQhH3UljYsfw-81bLkI");
            Config::set('admin_mail',"admin@gmail.com");
            define("TWILIO_STATUS",0);
            define("INVENTORY_STATUS",0);
            View::share("TWILIO_STATUS", 0);
            View::share('delivery_fee',0);
            View::share('delivery_fee_curr','USD');
            Config::set('delivery_fee',0);
            Config::set('delivery_fee_type','common_fee');
            Config::set('delivery_km_fee',0);
            View::share("SITEPHONE", '');
            View::share("SITEEMAIL", '');
            View::share("SITEFOOTERTEXT",'');
            View::share("MAIL_VERIFY_STATUS",0);
            View::share("SITEFACEBOOK", '');
            View::share("SITETWITTER", '');
            View::share("SITELINKEDIN",'');
            View::share("SITEGOOGLE",'');
            View::share("SITEGOOLGEPLAY",'');
            View::share("SITEAPPLEPLAYSTORE",'');
            View::share("SELF_PICKUP_STATUS", 0);
            View::share("PW_PROTECT", 0);
            View::share("SHOW_CAPTCHA", 0);
            View::share("LOGINIMAGE", '');
            View::share("FOOTERNAME",'');
            View::share("FOOTERDES",'');
            Config::set("COMMON_COMMI",1);
        }
		
        //echo 'config'.Config::get('suspend_status'); exit;
        View::share("cms_details",$this->cms_setting);
        $default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code','co_cursymbol')->where(['default_counrty' => '1','co_status' => '1'])->first();
        if(empty($default) === false)
        {
            View::share('default_country_code',$default->co_code);
            View::share('default_currency',$default->co_cursymbol);
            View::share('default_dial',$default->co_dialcode);
            Config::set('config_default_dial',$default->co_dialcode);
			Session::put('default_currency_code', $default->co_curcode);
			Session::put('default_currency_symbol', $default->co_cursymbol);
        }
        else
        {
            View::share('default_country_code',"US");
            View::share('default_currency',"$");
            View::share('default_dial',"+1");
            Config::set('config_default_dial',"+1");
			Session::put('default_currency_code','USD');
			Session::put('default_currency_symbol','$');
        }
		
		
		/*START HERE */
		$ip= '106.51.49.53';
		//$ip = request()->ip();
        $data = \Location::get($ip);
		//print_r($data); exit;
        if(!empty($data))
        {
            if($data->latitude != '' && $data->longitude != '')
            {
                View::share('ip_latitude',$data->latitude);
                View::share('ip_longitude',$data->longitude);
				View::share('ip_location',$data->cityName.', '.$data->regionName.', '.$data->zipCode);
            }
            
        }
        /* only for live demo */
        /*if(Session::has('search_location')== '')
        {
            Session::put('search_location','Los Angeles, CA, USA');
            Session::put('search_latitude','34.0522342');
            Session::put('search_longitude','-118.2436849');
            $shipArray = array(	'sh_location' 	=> 'Los Angeles, CA, USA',
								'sh_latitude'	=> '34.0522342',
								'sh_longitude'	=> '-118.2436849',
											);
			Session::put('shipping_session',$shipArray);
        }*/
		/* END HERE */
        /*CHECKING distance kilometer AVAILABLE*/
        $checking_procedure = DB::select('SELECT ROUTINE_DEFINITION FROM information_schema.ROUTINES WHERE SPECIFIC_NAME= "lat_lng_distance"' );
        //DB::statement("SELECT ROUTINE_DEFINITION FROM information_schema.ROUTINES WHERE SPECIFIC_NAME='lat_lng_distance1fdgsdgdsfg'")->get();
        /* old formula - 15_3_19 
        BEGIN RETURN 6371 * 2 * ASIN(SQRT( POWER(SIN((lat1 - abs(lat2)) * pi()/180 / 2), 2) + COS(lat1 * pi()/180 ) * COS(abs(lat2) * pi()/180) * POWER(SIN((lng1 - lng2) * pi()/180 / 2), 2) ));END
        */
        if(count($checking_procedure) <= 0 )
        {
            DB::unprepared('CREATE FUNCTION `lat_lng_distance` (lat1 FLOAT, lng1 FLOAT, lat2 FLOAT, lng2 FLOAT) RETURNS FLOAT DETERMINISTIC BEGIN RETURN 6371 * 2 * ASIN(SQRT( POWER(SIN((lat1 - (lat2)) * pi()/180 / 2), 2) + COS(lat1 * pi()/180 ) * COS((lat2) * pi()/180) * POWER(SIN((lng1 - lng2) * pi()/180 / 2), 2) )); END');
            //DROP FUNCTION IF EXISTS hello
        }
        /* EOF CHECKING */

        /* get location from ip */
        /*$ip = $_SERVER['REMOTE_ADDR'];*/
        if (App::environment('local'))
        { // The environment is local
            //echo "local";
        }
        if (App::environment(['staging']))
        { // The environment is either local OR staging...
            // echo "staging";
        }
	
		
		//View::share('ip_latitude','11.016010');
        //View::share('ip_longitude','76.970310');
        return $next($request);
    }
}
