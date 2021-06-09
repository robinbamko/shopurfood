<?php
	
	namespace App\Http\Controllers;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Support\Facades\Input;
	use DB;
	use Session;
	use Redirect;
	use View;
	use App\Settings;
	use Config;
	class Controller extends BaseController
	{
		use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
		protected  $no_item;
		
		public function __construct()
		{	
			/** set no images **/
			$get_images = DB::table('gr_no_images')->select('banner_image','restaurant_store_image','restaurant_item_image','product_image','logo_image','landing_image','shop_logo_image','category_image')->first();
			if(empty($get_images) === false)
			{ 
				$no_logo = $get_images->logo_image;
				$no_banner = $get_images->banner_image;
				$this->no_reStoreListbanner = $get_images->banner_image;//restaurant/store list page banner
				$this->no_reStoreDetailbanner = $get_images->restaurant_store_image;//restaurant/store detail page banner
				$this->no_shop_logo = $get_images->shop_logo_image;
				$this->no_item = $get_images->restaurant_item_image;
				$no_product = $get_images->product_image;
				$no_landing_image = $get_images->landing_image;
				
				View::share('no_logo', $no_logo);
				View::share('no_banner', $no_banner);
				View::share('no_list_banner', $this->no_reStoreListbanner);
				View::share('no_reStoreDetailbanner', $this->no_reStoreDetailbanner);
				View::share('no_shop_logo', $this->no_shop_logo);
				View::share('no_item', $this->no_item);
				
				View::share('no_landing_image',$no_landing_image);
				Config(['no_item' => $this->no_item]);
				Config::set('no_shop_banner', $this->no_reStoreDetailbanner);
				Config::set('no_product', $no_product);
				Config::set('no_shop_logo',$this->no_shop_logo);
			}
			else
			{
				
				View::share('no_logo','default_logo.png');
				View::share('no_list_banner', 'default_store_listing_banner.png');
				View::share('no_reStoreDetailbanner', 'default_store_detailpage_banner.png');
				View::share('no_shop_logo', 'default_image_logo.jpg');
				View::share('no_item','default_image_item.png');
				View::share('no_product', 'default_image_item.png');
				View::share('no_landing_image','default_image_landing.jpg');
				Config(['no_item' => 'default_image_item.png']);
				View::share('no_banner', 'default_store_detailpage_banner.png');
				Config::set('no_shop_banner', 'default_store_detailpage_banner.png');
				Config::get('no_shop_logo','default_image_logo.jpg');
			}
			
			$this->general_setting = DB::table('gr_general_setting')->first();
			if(empty($this->general_setting) === false)
			{
				/*define("TWILIO_STATUS",$this->general_setting->otp_verification_status);
				View::share("TWILIO_STATUS", $this->general_setting->otp_verification_status);*/
				View::share("MAP_KEY", $this->general_setting->google_map_key_web);
				View::share("CUS_ANDR_LINK", $this->general_setting->playstore_link);
				View::share("CUS_IOS_LINK", $this->general_setting->itunes_link);
				View::share("MER_ANDR_LINK", $this->general_setting->playstore_link_merchant);
				View::share("MER_IOS_LINK", $this->general_setting->itunes_link_merchant);
				View::share("DEL_ANDR_LINK", $this->general_setting->playstore_link_deliver);
				View::share("DEL_IOS_LINK", $this->general_setting->itunes_link_deliver);
				View::share("ANALYTICS_CODE", $this->general_setting->analytics_code);
				Session::put("CUS_ANDR_LINK", $this->general_setting->playstore_link);
				Session::put("CUS_IOS_LINK", $this->general_setting->itunes_link);
				Session::put("MER_ANDR_LINK", $this->general_setting->playstore_link_merchant);
				Session::put("MER_IOS_LINK", $this->general_setting->itunes_link_merchant);
				Session::put("DEL_ANDR_LINK", $this->general_setting->playstore_link_deliver);
				Session::put("DEL_IOS_LINK", $this->general_setting->itunes_link_deliver);
				Session::put("SITENAME",$this->general_setting->gs_sitename);
				
			}
			else
			{
				/*define("TWILIO_STATUS",0);
				View::share("TWILIO_STATUS", 0);*/
				View::share("MAP_KEY", "AIzaSyCsDoY1OPjAqu1PlQhH3UljYsfw-81bLkI");
				View::share("CUS_ANDR_LINK", "javascript:;");
				View::share("CUS_IOS_LINK", "javascript:;");
				View::share("MER_ANDR_LINK","javascript:;");
				View::share("MER_IOS_LINK","javascript:;");
				View::share("DEL_ANDR_LINK","javascript:;");
				View::share("DEL_IOS_LINK", "javascript:;");
				View::share("ANALYTICS_CODE", "");
				Session::put("CUS_ANDR_LINK", "javascript:;");
				Session::put("CUS_IOS_LINK", "javascript:;");
				Session::put("MER_ANDR_LINK","javascript:;");
				Session::put("MER_IOS_LINK","javascript:;");
				Session::put("DEL_ANDR_LINK","javascript:;");
				Session::put("DEL_IOS_LINK", "javascript:;");
				Session::put("SITENAME", "Shopurfood");
			}
			
			$Admindefault_language = DB::table('gr_language')->select('lang_code')->where('status','=','1')->where('default_lang','=','1')->first();
			if(empty($Admindefault_language) === false)
            {
                Config::set('adminMob_default_lang',$Admindefault_language->lang_code);
			}
			else
			{
                Config::set('adminMob_default_lang','en');
			}
			
			/* for android mobile images */
			Config::set('mob_splash_width','1440');
			Config::set('mob_splash_height','2560');
			Config::set('mob_gr_icon_wi','316');
			Config::set('mob_gr_icon_he','316');
			Config::set('listing_banner_wi','512');
			Config::set('listing_banner_he','464');
			Config::set('mob_logo_wid','512');
			Config::set('mob_logo_hei','512');
			/* for ios mobile images */
			Config::set('ios_splash_width','414');
			Config::set('ios_splash_height','736');
			Config::set('ios_gr_icon_wi','316');
			Config::set('ios_gr_icon_he','316');
			Config::set('ios_listing_banner_wi','512');
			Config::set('ios_listing_banner_he','464');
			Config::set('ios_login_logo_wi','80');
			Config::set('ios_login_logo_he','80');
			Config::set('ios_register_logo_wi','160');
			Config::set('ios_register_logo_he','160');
			Config::set('ios_forget_logo_wi','240');
			Config::set('ios_forget_logo_he','240');
			
			/* firebase key for android and ios */
			define('ANDR_FIREBASE_API_KEY_CUS', 'AAAAXh28N2o:APA91bFYS4dM9yRYV9jn4quaYV4GRz86d__FbVmDhVMPIElfl47xs3rUrNEn6OcMx1F2iMRJ4ieWEZihTxnW2fUjluAHa2XdudbuvZTBIgW-6qVF8_55vRiiVwuaIJ0obkTeYrHF91EL');
			define('IOS_FIREBASE_API_KEY_CUS', 'AAAARTY8Nrw:APA91bFGcHazli7nfn6e0if5uwF2vqbqm_HMTP7FRwH_7g7CbbECS-oJBGBdIB03TuteM88Kx2LWe-pxgtdbSKF7ECKzwS4OD0YNcQqHTDOlYzvlYr5YZASRNifl7pA6z2rgCOBLlbrA');
			define('ANDR_FIREBASE_API_KEY_DEL', 'AAAAxj_H3M8:APA91bHgi5n6sLw4TY1SV1t9nk_Dk5ESFVppeRawacisTa3AnHx9ucSY0fUIZZ47pfgQANFUzVK-3_KI_sbNfcO4Ys_-yoCuyJhw6RcaDgKoJfaiGybFr1ivD5RomBR7eqP3RRVxIG1N');
			define('IOS_FIREBASE_API_KEY_DEL', 'AAAAtbx_WHw:APA91bGp_g-JFtufKbsfVRyKa-dX6j_LIfxf3anEmTlP1_f0cyKyE1d74QLDRt1vCSJ63SwJobzkExpIX1Ya2uZUMYVt7wrBv67_rbv7ZiH2p74UFDSqxAAGDxiGGHOIRy5tkbPfuHrX');
			define('ANDR_FIREBASE_API_KEY_MER', 'AAAA-DdmUXY:APA91bGdU2FHIf6Pkn5ivbOutSAoLrv-aG9YxYQI-MZcvX4KmLSzKVEiuLnxWpr2zgjfDvAwlmwB-baOkS_N1lq1oli13KRWoCCYkPnjcOE0eaJiioUujYOskuFNU9mRrrzXffy5Vvfb');
			define('IOS_FIREBASE_API_KEY_MER', 'AAAA-DdmUXY:APA91bGdU2FHIf6Pkn5ivbOutSAoLrv-aG9YxYQI-MZcvX4KmLSzKVEiuLnxWpr2zgjfDvAwlmwB-baOkS_N1lq1oli13KRWoCCYkPnjcOE0eaJiioUujYOskuFNU9mRrrzXffy5Vvfb');
		}
		
		//declare admin language
    	public function setAdminLanguage()
    	{
    		$this->ADMIN_LANGUAGE = "admin_en_lang";
    		$this->get_Adminactive_language = DB::table('gr_language')->where('default_lang','=','0')->where('status','=','1')->get();
			//print_r($get_Adminactive_language); exit;						
			$get_Admindefault_language = DB::table('gr_language')->where('status','=','1')->where('default_lang','=','1')->first();
			
			/*NOTIFICATION */
			$cus_notifyCount = DB::table('gr_customer')->where('cus_read_status','=','0')->where('cus_status','!=','2')->count();
			$mer_notifyCount = DB::table('gr_merchant')->where('mer_read_status','=','0')->where('mer_status','!=','2')->count();
			$del_notifyCount = DB::table('gr_delivery_member')->where('deliver_read_status','=','0')->where('deliver_status','!=','2')->count();
			$pdt_notifyCount = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','1')->where('pro_status','!=','2')->count();
			$item_notifyCount = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','2')->where('pro_status','!=','2')->count();
			$pay_request_notifyCount = DB::table('gr_notification')->where(['no_status' => '1','read_status' => '0'])->count();
			//DB::connection()->enableQueryLog();
			$ordernotifyCount = count(DB::table('gr_order')->where('ord_admin_viewed','=','0')->groupBy('ord_transaction_id')->get());
			//$query = DB::getQueryLog();
			//print_r($query); exit;
			$total_notifyCount = $cus_notifyCount+$mer_notifyCount+$pdt_notifyCount+$item_notifyCount + $pay_request_notifyCount+$ordernotifyCount+$del_notifyCount; 
			//echo $del_notifyCount; exit;
			View::share("cus_notification",$cus_notifyCount);
			View::share("mer_notification",$mer_notifyCount);
			View::share("del_notification",$del_notifyCount);
			View::share("pdt_notification",$pdt_notifyCount);
			View::share("item_notification",$item_notifyCount);
			View::share("tot_notification",$total_notifyCount);
			View::share("pay_notification",$pay_request_notifyCount);
			View::share("order_notification",$ordernotifyCount);
			
			/* EOF NOTIFICATION */
			if(Session::has('admin_lang_code') != 1) //admin language not set
			{
				if(empty($get_Admindefault_language) === false) //default language is there
				{
					Session::put('admin_lang_code',$get_Admindefault_language->lang_code);
					Session::put('admin_lang_file','admin_'.$get_Admindefault_language->lang_code.'_lang');
				}
				else  //default language is not there.set english as default
				{
					Session::put('admin_lang_code','en');
					Session::put('admin_lang_file','admin_en_lang');
				}
			}
			else  // admin language is set
			{
				$admin_selected_lang_code = Session::get('admin_lang_code');
				Session::put('admin_lang_file','admin_'.Session::get('admin_lang_code').'_lang');
			}
			
			$this->general_setting = DB::table('gr_general_setting')->get();
			if(count($this->general_setting) > 0){
				
				foreach($this->general_setting as $s){
					
					View::share("SITENAME",$s->gs_sitename);
					View::share("FOOTERNAME",$s->footer_text);
					if(Session('lang_code')=='en'){
						View::share("SITENAME",$s->gs_sitename);
						View::share("FOOTERNAME",$s->footer_text);
					}
					View::share("AGENTMODULE",$s->gs_agent_module);
					$this->agent_module=$s->gs_agent_module;
				}
				}else{
				View::share("SITENAME","ePickMeUp");
				View::share("FOOTERNAME","ePickMeUp");
				View::share("AGENTMODULE","0");
				$this->agent_module=0;
			}
			
			View::share ('Admin_Active_Language', $this->get_Adminactive_language);
			View::share('ADMIN_OUR_LANGUAGE',$this->ADMIN_LANGUAGE);
			$logo_settings_details = Settings::get_logo_settings_details();
			View::share('logo_settings_details',$logo_settings_details);
			$footer_text=Settings::get_settings_details();
			View::share('footer_text',$footer_text);
			
			if(empty($get_Admindefault_language->lang_name) === false)
			{
				View::share('default_lang',$get_Admindefault_language->lang_name);
				}else{
				View::share('default_lang','English');
			}
			//set selected language code dynamically
			app()->setLocale(Session::get('admin_lang_code'));
			/** share admin details  **/
			// $default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code','co_cursymbol')->where(['default_counrty' => '1','co_status' => '1'])->first();
			
			$default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code','co_cursymbol','co_name')->where(['default_counrty' => '1','co_status' => '1'])->first();
			
			if(empty($default) === false)
			{
				View::share('default_country_code',$default->co_code);
				View::share('default_currency',$default->co_cursymbol);
				View::share('default_dial',$default->co_dialcode);
				View::share('default_country',ucfirst($default->co_name));
				Config::set('config_default_dial',$default->co_dialcode);
				define("con_default_currency",$default->co_cursymbol);
				Session::put('default_currency_code', $default->co_curcode);
				Session::put('default_currency_symbol', $default->co_cursymbol);
			}
			else
			{
				View::share('default_country_code',"US");
				View::share('default_currency',"$");
				View::share('default_dial',"+1");
				Config::set('config_default_dial','+1');
				View::share('default_country',"United States of America");
				define("con_default_currency","$");
				Session::put('default_currency_code','USD');
				Session::put('default_currency_symbol','$');
			}
		}
		
		public function setLanguageLocaleMerchant()
		{
			//if(Session::get('mer_has_shop')==0){ return Redirect::to('merchant-restaurant'); } 
			/*Get merchant Language*/ 
			$this->MER_OUR_LANGUAGE ="merchant_en_lang";
			$this->get_Adminactive_language = DB::table('gr_language')->where('default_lang','=','0')->where('status','=','1')->get();
			//$Mer_Active_Language = DB::table('gr_language')->where('status',1)->get();
			$Mer_Default_Language = DB::table('gr_language')->where('status','1')->where('default_lang','1')->get();
			$get_Admindefault_language = DB::table('gr_language')->where('status','=','1')->where('default_lang','=','1')->first();
			//View::share ('Mer_Active_Language', $Mer_Active_Language);
			//print_r($Mer_Default_Language); exit;
			
			if(Session::has('mer_lang_code') != 1)
			{
				
				if(count($Mer_Default_Language)>0) 
				{
					foreach($Mer_Default_Language as $Lang)
					{
						Session::put('mer_lang_code',$Lang->lang_code);
						Session::put('mer_lang_file','merchant_'.$Lang->lang_code.'_lang');
						
					}
					$mer_selected_lang_code='';
				}	
				else
				{
					Session::put('mer_lang_code','en');
					Session::put('mer_lang_file','merchant_en_lang');
					$mer_selected_lang_code='';
				}
			}
			else
			{
				$mer_selected_lang_code = Session::get('mer_lang_code');
				Session::put('mer_lang_file','merchant_'.Session::get('mer_lang_code').'_lang');
			}	
			View::share ('Mer_Active_Language', $this->get_Adminactive_language);	
			View::share ('MER_OUR_LANGUAGE', $this->MER_OUR_LANGUAGE);	
			$logo_settings_details = Settings::get_logo_settings_details();
			//print_r($logo_settings_details); exit;
			View::share('logo_settings_details',$logo_settings_details);
			View::share ('mer_selected_lang_code', $mer_selected_lang_code);
			if(empty($get_Admindefault_language->lang_name) === false)
			{
				View::share('default_lang',$get_Admindefault_language->lang_name);
				}else{
				View::share('default_lang','English');
			}
			
			/*set the local language is dynamically (confiq/app.php --> 'locale' => 'en')*/
			$mer_lang_code = Session::get('mer_lang_code'); 
			app()->setLocale($mer_lang_code);
			$default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code','co_cursymbol')->where(['default_counrty' => '1','co_status' => '1'])->first();
			if(empty($default) === false)
			{
				View::share('default_country_code',$default->co_code);
				View::share('default_currency',$default->co_cursymbol);
				View::share('default_dial',$default->co_dialcode);
				define('con_default_currency',$default->co_cursymbol);
				Config::set('config_default_dial',$default->co_dialcode);
				Session::put('default_currency_code', $default->co_curcode);
				Session::put('default_currency_symbol', $default->co_cursymbol);
			}
			else
			{
				View::share('default_country_code',"US");
				View::share('default_currency',"$");
				View::share('default_dial',"+1");
				define('con_default_currency',"$");
				Config::set('config_default_dial',"+1");
				Session::put('default_currency_code','USD');
				Session::put('default_currency_symbol','$');
			}
			
			//Sitename 	
			
			$this->general_setting = DB::table('gr_general_setting')->get();
			if(count($this->general_setting) > 0){
				
				foreach($this->general_setting as $s){
					View::share("SITENAME",$s->gs_sitename);
					View::share("FOOTERNAME",$s->footer_text);	 
					View::share("PW_PROTECT", $s->gs_password_protect);
					$this->admin_mail = $s->gs_email;
					if(Session('lang_code')=='en'){
						View::share("SITENAME",$s->gs_sitename);
						View::share("FOOTERNAME",$s->footer_text);
						View::share("FOOTERNAME",$s->footer_text);	 
					}
					View::share("AGENTMODULE",$s->gs_agent_module);
					if($mer_lang_code== '' || $mer_lang_code== 'en'){
						if($s->footer_text != '') {
							View::share("SITEFOOTERTEXT", $s->footer_text);
						}
						}else{
						$footer_txt_tbl = 'footer_text_'.$mer_lang_code;
						if($s->$footer_txt_tbl != '') {
							View::share("SITEFOOTERTEXT", $s->$footer_txt_tbl);
							}else{
							View::share("SITEFOOTERTEXT", $s->footer_text);
						}
					}
				}		 	  
				}else{
				View::share("AGENTMODULE","0");
				View::share("SITENAME","ePickMeUp");
				View::share("FOOTERNAME","ePickMeUp");  
				View::share("PW_PROTECT", 0);
				$this->admin_mail = "admin@gmail.com";
			}
			
			
		}
		
        public function setLanguageLocalDeliveryManager()
        {
			
            $this->DELMGR_OUR_LANGUAGE ="delmgr_en_lang";
            //GET ALL ADMIN ADDED LANGUAGES
            $this->get_Adminactive_language = DB::table('gr_language')->where('default_lang','=','0')->where('status','=','1')->get();
            $DelMgr_Default_Language = DB::table('gr_language')->where('status','=',1)->where('default_lang','=',1)->get();
            $get_Admindefault_language = DB::table('gr_language')->where('status','=','1')->where('default_lang','=','1')->first();
            //View::share ('Mer_Active_Language', $Mer_Active_Language);
			
            if(Session::has('DelMgr_lang_code') != 1)
            {
				
                if(count($DelMgr_Default_Language)>0)
                {
                    foreach($DelMgr_Default_Language as $Lang)
                    {
                        Session::put('DelMgr_lang_code',$Lang->lang_code);
                        Session::put('DelMgr_lang_file','delmgr_'.$Lang->lang_code.'_lang');
						
					}
                    $DelMgr_selected_lang_code='';
				}
                else
                {
                    Session::put('DelMgr_lang_code','en');
                    Session::put('DelMgr_lang_file','delmgr_en_lang');
                    $DelMgr_selected_lang_code='';
				}
			}
            else
            {
                $DelMgr_selected_lang_code = Session::get('DelMgr_lang_code');
                Session::put('DelMgr_lang_file','delmgr_'.Session::get('delmgr_lang_code').'_lang');
			}
            View::share ('DelMgr_Active_Language', $this->get_Adminactive_language);
            View::share ('DELMGR_OUR_LANGUAGE', $this->DELMGR_OUR_LANGUAGE);
            $logo_settings_details = Settings::get_logo_settings_details();
            //print_r($logo_settings_details); exit;
            View::share('logo_settings_details',$logo_settings_details);
            View::share ('DelMgr_selected_lang_code', $DelMgr_selected_lang_code);
            if(empty($get_Admindefault_language->lang_name) === false)
            {
                View::share('default_lang',$get_Admindefault_language->lang_name);
				}else{
                View::share('default_lang','English');
			}
			
            /*set the local language is dynamically (confiq/app.php --> 'locale' => 'en')*/
            $DelMgr_lang_code = Session::get('DelMgr_lang_code');
            app()->setLocale($DelMgr_lang_code);
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
			
            //Sitename
			
            $this->general_setting = DB::table('gr_general_setting')->get();
            if(count($this->general_setting) > 0){
				
                foreach($this->general_setting as $s){
                    View::share("SITENAME",$s->gs_sitename);
                    View::share("FOOTERNAME",$s->footer_text);
					
                    if(Session('lang_code')=='en'){
						
                        View::share("SITENAME",$s->gs_sitename);
                        View::share("FOOTERNAME",$s->footer_text);
						
						
					}
				}
				}else{
				
                View::share("SITENAME","ePickMeUp");
                View::share("FOOTERNAME","ePickMeUp");
			}
		}
		/** set language for front **/
		//		public function setLanguageFront()
		//			{
		//
		//                $this->get_active_language = DB::table('gr_language')->where('status','=','1')->get();
		//        //print_r($get_Adminactive_language); exit;
		//        $get_default_language = DB::table('gr_language')->where('status','=','1')->where('default_lang','=','1')->first();
		//        $this->FRONT_LANGUAGE = "front_en_lang";
		//        if(Session::has('front_lang_code') != 1) // language not set
		//        {
		//            if(empty($get_default_language) === false) //default language is there
		//            {
		//                Session::put('front_lang_code',$get_default_language->lang_code);
		//                Session::put('front_lang_file','front_'.$get_default_language->lang_code.'_lang');
		//                $this->FRONT_LANGUAGE = 'front_'.$get_default_language->lang_code.'_lang';
		//                $front_selected_lang_code = $get_default_language->lang_code;
		//            }
		//            else  //default language is not there.set english as default
		//            {
		//                Session::put('front_lang_code','en');
		//                Session::put('front_lang_file','front_en_lang');
		//                $this->FRONT_LANGUAGE = "front_en_lang";
		//                $front_selected_lang_code = "en";
		//            }
		//        }
		//        else  //  language is set
		//        {
		//            $front_selected_lang_code = Session::get('front_lang_code');
		//            Session::put('front_lang_file','front_'.Session::get('front_lang_code').'_lang');
		//            $this->FRONT_LANGUAGE  = 'front_'.$front_selected_lang_code.'_lang';
		//        }
		////
		////            $logo_settings_details = Settings::get_logo_settings_details();
		////            View::share('logo_settings_details',$logo_settings_details);
		//        View::share('FRONT_LANGUAGE',$this->FRONT_LANGUAGE);
		//        View::share ('selected_lang_code', $front_selected_lang_code);
		//        View::share('Active_language',$this->get_active_language);
		//        app()->setLocale(Session::get('front_lang_code'));
		//
		//
		//		}
		
        public function change_language(){
			
            $lang = Input::get('lang_code');
            Session::put('front_lang_code',$lang);
            Session::put('front_lang_file',$lang.'_lang');
			
            if(Session::get('front_lang_code') == $lang){
                echo 'success';
				}else{
                echo ' Fail';
			}
			
			
		}
		
	}
	
