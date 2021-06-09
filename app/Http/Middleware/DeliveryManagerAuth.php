<?php
	
	namespace App\Http\Middleware;
	
	use Closure;
	
	use DB , Session;
	
	use view , Redirect;
	
	use App\Settings;
	
	class DeliveryManagerAuth
	{
		/**
			* Handle an incoming request.
			*
			* @param  \Illuminate\Http\Request  $request
			* @param  \Closure  $next
			* @return mixed
		*/
		public $attributes;
	//	protected $config_delmgr_our_language;
		public function handle($request, Closure $next)
		{   
			//echo 'there';exit;
			$request->attributes->add(['DELMGR_OUR_LANGUAGE' => 'delmgr_en_lang']);
			//config(['config_delmgr_our_language'=>'delmgr_en_lang']);
			$this->DELMGR_OUR_LANGUAGE ="delmgr_en_lang";
			//GET ALL ADMIN ADDED LANGUAGES
            $this->get_Adminactive_language = DB::table('gr_language')->where('default_lang','=','0')->where('status','=','1')->get();
            $DelMgr_Default_Language = DB::table('gr_language')->where('status',1)->where('default_lang',1)->get();
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

                Session::put('DelMgr_lang_file','delmgr_'.Session::get('DelMgr_lang_code').'_lang');

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
			
            
            $DelMgr_lang_code = Session::get('DelMgr_lang_code');
            app()->setLocale($DelMgr_lang_code);
            $default = DB::table('gr_country')->select('co_curcode','co_dialcode','co_code')->where(['default_counrty' => '1','co_status' => '1'])->first();
            if(empty($default) === false)
            {
                View::share('default_country_code',$default->co_code);
                View::share('default_currency',$default->co_curcode);
                View::share('default_dial',$default->co_dialcode);
                Session::put('del_default_curr_sym',$default->co_curcode);
			}
            else
            {
                View::share('default_country_code',"US");
                View::share('default_currency',"USD");
                View::share('default_dial',"+1");
                Session::put('del_default_curr_sym','USD');
			}
			
            //Sitename
			
            $this->general_setting = DB::table('gr_general_setting')->get();
            if(count($this->general_setting) > 0){
				
                foreach($this->general_setting as $s){
                    View::share("SITENAME",$s->gs_sitename);
                    View::share("FOOTERNAME",$s->footer_text);
					View::share("AGENTMODULE",$s->gs_agent_module);
                    if(Session('lang_code')=='en'){
						
                        View::share("SITENAME",$s->gs_sitename);
                        View::share("FOOTERNAME",$s->footer_text);
						
						
					}
				}
				}else{
                View::share("SITENAME","Grocery");
                View::share("FOOTERNAME","Test");
				View::share("AGENTMODULE","0");
			}
			
			// check login authentication 
			if (Session::has('DelMgrSessId'))
			{ 
				//  return Redirect::to('delivery-manager-dashboard');
			} 
			else 
			{ 
				return Redirect::to('delivery-manager-login');
			} 
			return $next($request);
		}
	}
