<?php
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	
	use Validator;
	
	use Session;
	
	use Mail;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use App\Admin;
	
	use App\Settings;
	
	use Image;
	
	use Twilio;
	
	use File;
	
	use Config;
	
	class SettingsController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->setAdminLanguage();
		}
		
		public function general_settings()
		{
			
			
			if (Session::has('admin_id') == 1) {
				$settings_details = DB::table('gr_general_setting')->first();
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_GENERAL_SETTINGS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_GENERAL_SETTINGS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_GENERAL_SETTINGS');
				$details = array();
				if(empty($settings_details) === true)
				{
					$array_name = array();
					foreach(DB::getSchemaBuilder()->getColumnListing('gr_general_setting') as $res)
					{
						$array_name[$res]='';
					}
					$details = (object) $array_name; // return all value as empty.
				}
				else
				{
					$details = $settings_details;
				}
				return view('Admin.general_settings')->with('details', $details)->with('pagetitle', $page_title);
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function general_settings_submit(Request $request)
		{
			$sitename_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SITE_NAME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SITE_NAME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SITE_NAME');
			$sitebanner_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EN_SITE_BANNER_TEXT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EN_SITE_BANNER_TEXT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EN_SITE_BANNER_TEXT');
			$email_err_required_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SITE_EMAIL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SITE_EMAIL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SITE_EMAIL');
			$ph_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_PHONE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_PHONE');
			$validemail_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_MAIL');
			$desc_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SITE_DESCRIPTION')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SITE_DESCRIPTION') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SITE_DESCRIPTION');
			$metatitle_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_METATITLE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_METATITLE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_METATITLE');
			$metakeywords_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_METAKEYWORDS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_METAKEYWORDS') : trans($this->ADMIN_LANGUAGE . 'ADMIN_PLEASE_ENTER_METAKEYWORDS');
			$metadesc_err_mag = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_METADESC')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_METADESC') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_METADESC');
			$footerdes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FOOTERDES')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FOOTERDES') : trans($this->ADMIN_LANGUAGE . ',ADMIN_PLEASE_ENTER_FOOTERDES');
			$prefooterdes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_PRE_FOOTERDES')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_PRE_FOOTERDES') : trans($this->ADMIN_LANGUAGE . ',ADMIN_PLEASE_ENTER_PRE_FOOTERDES');
			$footertext_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FOOTERTEXT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FOOTERTEXT') : trans($this->ADMIN_LANGUAGE . ',ADMIN_PLEASE_ENTER_FOOTERTEXT');
			$prefootertext_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_PRE_FOOTERTEXT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_PRE_FOOTERTEXT') : trans($this->ADMIN_LANGUAGE . ',ADMIN_PLEASE_ENTER_PRE_FOOTERTEXT');
			$itunes_url_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_ITUNES_URL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_ITUNES_URL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_ITUNES_URL');
			$playstore_url_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_PLAYSTORE_URL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_PLAYSTORE_URL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_PLAYSTORE_URL');
			$deliver_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELI_FEE_STATUS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELI_FEE_STATUS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELI_FEE_STATUS');
			$deliver_fee_type_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SELECT_DELIFEE_TYPE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SELECT_DELIFEE_TYPE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SELECT_DELIFEE_TYPE');
			$gs_delivery_fee_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_DELIFEE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_DELIFEE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_DELIFEE');
			$km_range_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELBOY_KMRANGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELBOY_KMRANGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELBOY_KMRANGE');
			
			$mail_status_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CART_STATUS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CART_STATUS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CART_STATUS');
			$mail_dura_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MAIL_TIME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MAIL_TIME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MAIL_TIME');
			$refer_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SELECT_REFER_FRND')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SELECT_REFER_FRND') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SELECT_REFER_FRND');
			$offer_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_OFFER_PERCENT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_OFFER_PERCENT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_OFFER_PERCENT');
			$gs_twilio_sid_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_TWILIO_ID')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_TWILIO_ID') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_TWILIO_ID');
			$gs_twilio_token_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_TWILIO_TOKEN')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_TWILIO_TOKEN') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_TWILIO_TOKEN');
			$gs_twilio_from_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_TWILIO_FROM')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_TWILIO_FROM') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_TWILIO_FROM');
			
			/*  $gs_hippo_secret_key_err = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_HIPPO_SECRET_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_HIPPO_SECRET_KEY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ENTER_HIPPO_SECRET_KEY');*/
			$gs_pw_protect_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SL_PW_ST')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SL_PW_ST') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SL_PW_ST');
			$gs_captcha_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SL_CAPTCHA_ST')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SL_CAPTCHA_ST') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SL_CAPTCHA_ST');
			$status_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_LOGIN_ATTEMPT_SL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_LOGIN_ATTEMPT_SL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_LOGIN_ATTEMPT_SL');
			$attempt_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_LOGIN_ATTEMPT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_LOGIN_ATTEMPT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_LOGIN_ATTEMPT');
			$sus_time_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_EN_LOGIN_SUSPEND_TIME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_EN_LOGIN_SUSPEND_TIME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_EN_LOGIN_SUSPEND_TIME');
			$feat_price_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_FEATURED_PRICE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_FEATURED_PRICE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_FEATURED_PRICE');
			$feat_store_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SELECT_FEATURED')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SELECT_FEATURED') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SELECT_FEATURED');
			$num_store_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTER_NO_OF_FEATURED_STORE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTER_NO_OF_FEATURED_STORE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTER_NO_OF_FEATURED_STORE');
			$login_img_size = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_LOGIN_IMAGE_ERR')) ? trans(Session::get('admin_lang_file') . '.ADMIN_LOGIN_IMAGE_ERR') : trans($this->ADMIN_LANGUAGE . '.ADMIN_LOGIN_IMAGE_ERR');
			$login_img_format = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_LOGIN_IMAGE_FORMAT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_LOGIN_IMAGE_FORMAT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_LOGIN_IMAGE_FORMAT');
			$featured_img_size = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FEATURE_IMAGE_ERR')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FEATURE_IMAGE_ERR') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FEATURE_IMAGE_ERR');
			$featured_img_format = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FEATURE_IMAGE_FORMAT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FEATURE_IMAGE_FORMAT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FEATURE_IMAGE_FORMAT');
			$gs_currency_api_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_API_KEY_CURRENCY_ERR')) ? trans(Session::get('admin_lang_file') . '.ADMIN_API_KEY_CURRENCY_ERR') : trans($this->ADMIN_LANGUAGE . '.ADMIN_API_KEY_CURRENCY_ERR');
			
			
			$validator = Validator::make($request->all(), [
            'sitename'              => 'required',
            'email'                 => 'required|email',
            'phone'                 => 'required',
            'description'           => 'required',
            'metatitle'             => 'required',
            'metakeywords'          => 'required',
            'metadescription'       => 'required',
            'footertext'            => 'required',
            'footerdescription'     => 'required',
            'app_sec_image'         => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=380,max_width=400,min_height=640,max_height=670',
            'login_image'           => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=465,max_width=475,min_height=335,max_height=345',
            'feature_image'         => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=660,max_width=660,min_height=380,max_height=380',
            'itunes_url'            => 'required',
            'playstore_url'         => 'required',
            'otp_status'            => 'required',
            'gs_twilio_sid'         => 'required_if:otp_status,1',
            'gs_twilio_token'       => 'required_if:otp_status,1',
            'gs_twilio_from'        => 'required_if:otp_status,1',
            'mail_status'           => 'required',
            'self_status'           => 'required',
            'gs_delivery_fee_status' => 'required',
            'del_fee_type'          => 'required_if:gs_delivery_fee_status,1',
            'gs_common_fee'         => 'required_if:del_fee_type,common_fee',
            'gs_km_fee'             => 'required_if:del_fee_type,km_fee',
            //'gs_hippo_chat_status' => 'required',
            'gs_show_inventory'     => 'required',
            'gs_abandoned_mail'     => 'required',
            'gs_mail_after'         => 'required_if:gs_abandoned_mail,1',
            'gs_refer_friend'       => 'required',
            'gs_offer_percentage'   => 'required_if:gs_refer_friend,1',
            'gs_password_protect'   => 'required',
            'gs_show_captcha'       => 'required',
            'suspend_status'        => 'required',
            'max_attempt'           => 'required_if:suspend_status,1',
            'suspend_time'          => 'required_if:suspend_status,1',
            'prefootertext'         => 'required',
            'prefooterdesc'         => 'required',
            'gs_featured_store'     => 'required',
            'gs_featured_price'     => 'required',
            'gs_featured_numstore'  => 'required',
			'gs_currency_api'		=> 'required_if:hid_currency_code,1',
			], [
            'sitename.required'             => $sitename_err_msg,
            'email.required'                => $email_err_required_msg,
            'email.email'                   => $validemail_err_msg,
            'phone.required'                => $ph_req_err_msg,
            'description.required'          => $desc_err_msg,
            'metatitle.required'            => $metatitle_err_msg,
            'metakeywords.required'         => $metakeywords_err_msg,
            'metadescription.required'      => $metadesc_err_mag,
            'footertext.required'           => $footertext_err_msg,
            'footerdescription.required'    => $footerdes_err_msg,
            'itunes_url.required'           => $itunes_url_err_msg,
            'playstore_url.required'        => $playstore_url_err_msg,
            'gs_delivery_fee_status.required' => $deliver_err,
            'del_fee_type.required_if'      => $deliver_fee_type_err,
            'gs_common_fee.required_if'     => $gs_delivery_fee_err,
            'gs_km_fee.required_if'         => $gs_delivery_fee_err,
            //'gs_hippo_chat_status.required' => $hippo_err,
            'gs_abandoned_mail.required'    => $mail_status_err,
            'gs_refer_friend.required'      => $refer_err,
            'gs_password_protect.required'  => $gs_pw_protect_err,
            'gs_show_captcha.required'      => $gs_captcha_err,
            'prefootertext.required'        => $prefootertext_err_msg,
            'prefooterdesc.required'        => $prefooterdes_err_msg,
            'gs_featured_store.required'    => $feat_store_err,
            'gs_twilio_sid.required'        => $gs_twilio_sid_err,
            'gs_twilio_token.required'      => $gs_twilio_token_err,
            'gs_twilio_from.required'       => $gs_twilio_from_err,
            'gs_mail_after.required'        => $mail_dura_err,
            'gs_offer_percentage.required'  => $offer_err,
            'gs_featured_price.required'    => $feat_price_err,
            'gs_featured_numstore.required' => $num_store_err,
            'suspend_time.required'         => $sus_time_err,
            'max_attempt.required'          => $attempt_err,
            'login_image.image'             => $login_img_format,
            'login_image.mimes'             => $login_img_size,
            'login_image.max'               => $login_img_size,
            'login_image.dimensions'        => $login_img_size,
            'feature_image.image'           => $featured_img_format,
            'feature_image.mimes'           => $featured_img_size,
            'feature_image.max'             => $featured_img_size,
            'feature_image.dimensions'      => $featured_img_size,
            'gs_currency_api.required_if'      => $gs_currency_api_err,
			
            // 'app_sec_image.required' => $front_logo_err_msg,
            // 'app_sec_image.sometimes' => $front_logo_err_msg,
            // 'app_sec_image.image'=>$front_logo_img_err_msg,
            // 'app_sec_image.mimes'=> $front_logo_mimes_err_msg,
            // 'app_sec_image.max' => $front_logo_max_err_msg,
            // 'app_sec_image.dimensions'=>$front_logo_dimen_err_msg,
			
			]);
			
			
			if($validator->fails()) 
			{
				return redirect('admin-general-settings')->withErrors($validator)->withInput();
			}
			else {
				
				$destinationPath = public_path('front/frontImages');
				
				if (request()->app_sec_image != '') {
					/* deleting the existing image*/
					$imagePath = public_path('front/frontImages/') . input::get('preapp_sec_image');
					if (File::exists($imagePath)) {
						$delete = File::delete($imagePath);
					}
					$app_img = 'app_' . rand() . '.' . request()->app_sec_image->getClientOriginalExtension();
					
					$appSec_img = Image::make(request()->app_sec_image->getRealPath())->resize(390, 650);
					$appSec_img->save($destinationPath . '/' . $app_img, 80);
					
					} else {
					$app_img = input::get('preapp_sec_image');
				}
				
				/* ---------LOGIN PAGE IMAGE START-----------*/
				
				
				if (request()->login_image != '') {
					/* deleting the existing image*/
					
					
					$loginimgPath = public_path('front/frontImages/') . input::get('pre_login_image');
					
					if (File::exists($loginimgPath)) {
						$delete = File::delete($loginimgPath);
					}
					
					$login_img = 'login_' . rand() . '.' . request()->login_image->getClientOriginalExtension();
					
					$loginPage_img = Image::make(request()->login_image->getRealPath())->resize(471, 341);
					$loginPage_img->save($destinationPath . '/' . $login_img, 80);
					
					} else {
					$login_img = input::get('pre_login_image');
				}
				/* ---------LOGIN PAGE IMAGE END-----------*/
				
				
				
				/* ---------FEATURED RESTAURANT IMAGE START-----------*/
				
				
				if (request()->feature_image != '') {
					/* deleting the existing image*/
					
					
					$featuredimgPath = public_path('front/frontImages/') . input::get('pre_feature_image');
					
					if (File::exists($featuredimgPath)) {
						$delete = File::delete($featuredimgPath);
					}
					
					$featured_img = 'featured_' . rand() . '.' . request()->feature_image->getClientOriginalExtension();
					
					$featuredRes_img = Image::make(request()->feature_image->getRealPath())->resize(660, 380);
					$featuredRes_img->save($destinationPath . '/' . $featured_img, 80);
					
					} else {
					$featured_img = input::get('pre_feature_image');
				}
				/* ---------FEATURED RESTAURANT IMAGE END-----------*/
				
				
				$siteid = mysql_escape_special_chars(Input::get('siteid'));
				$sitename = mysql_escape_special_chars(Input::get('sitename'));
				$email = mysql_escape_special_chars(Input::get('email'));
				$phone = mysql_escape_special_chars(Input::get('phone'));
				$description = mysql_escape_special_chars(Input::get('description'));
				$metatitle = mysql_escape_special_chars(Input::get('metatitle'));
				$metakeywords = mysql_escape_special_chars(Input::get('metakeywords'));
				$metadescription = mysql_escape_special_chars(Input::get('metadescription'));
				$footertext = mysql_escape_special_chars(Input::get('footertext'));
				$itunes_url = mysql_escape_special_chars(Input::get('itunes_url'));
				$playstore_url = mysql_escape_special_chars(Input::get('playstore_url'));
				$otp_status = mysql_escape_special_chars(Input::get('otp_status'));
				$inventory_status = mysql_escape_special_chars(Input::get('gs_show_inventory'));
				$abandoned_mail = mysql_escape_special_chars(Input::get('gs_abandoned_mail'));
				$mail_time = mysql_escape_special_chars(Input::get('gs_mail_after'));
				$mail_time_dur = mysql_escape_special_chars(Input::get('mail_duration'));
				$footer_desc = mysql_escape_special_chars(Input::get('footerdescription'));
				$pre_footer_text = mysql_escape_special_chars(Input::get('prefootertext'));
				$pre_footer_desc = mysql_escape_special_chars(Input::get('prefooterdesc'));
				$app_sec_image = $app_img;
				$login_image = $login_img;
				$featured_resImag = $featured_img;
				/*if($otp_status == 1)
					{
					$admin_details = DB::table('gr_admin')->first();
					if(empty($admin_details) === false)
					{
					$admin_phone = $admin_details->adm_phone1;
					}else{
					$admin_phone = '+919092398789';
					}
					
					try{
					Twilio::message($admin_phone, 'OTP Enabled!');
					}
					catch (\Exception $e)
					{
					
					// if($e->getCode() == 21211)
					// {
					
					
					
					// $message = $e->getMessage();
					
					// session()->flash('message', $message);
					
					// return redirect()->back();
					// }
					
					
					return Redirect::to('admin-general-settings')->withErrors(['success'=>$e->getMessage()])->withInput();
					
					throw $exception;
					}
					
				} */
				
				$general_det = array(
				
                'gs_sitename'           => $sitename,
                'gs_email'              => $email,
                'gs_phone'              => $phone,
                'gs_sitedescription'    => $description,
                'gs_metatitle'          => $metatitle,
                'gs_metakeywords'       => $metakeywords,
                'gs_metadesc'           => $metadescription,
                'gs_footerdesc'         => $footer_desc,
                'footer_text'           => $footertext,
                'gs_apple_appstore_url' => $itunes_url,
                'gs_playstore_url'      => $playstore_url,
                'otp_verification_status' => $otp_status,
                'mail_verification_status' => mysql_escape_special_chars(Input::get('mail_status')),
                'self_pickup_status'    => mysql_escape_special_chars(Input::get('self_status')),
                'gs_delivery_fee_status'=> mysql_escape_special_chars(Input::get('gs_delivery_fee_status')),
                'gs_del_fee_type'       => mysql_escape_special_chars(Input::get('del_fee_type')),
                'gs_km_fee'             => mysql_escape_special_chars(Input::get('gs_km_fee')),
                'gs_delivery_fee'       => mysql_escape_special_chars(Input::get('gs_common_fee')),
                'gs_currency_code'      => mysql_escape_special_chars(Input::get('deli_curr')),
                /* 'gs_hippo_chat_status' => Input::get('gs_hippo_chat_status'),
				'gs_hippo_secret_key' => Input::get('gs_hippo_secret_key'),*/
                'gs_twilio_sid'         => mysql_escape_special_chars(Input::get('gs_twilio_sid')),
                'gs_twilio_token'       => mysql_escape_special_chars(Input::get('gs_twilio_token')),
                'gs_twilio_from'        => mysql_escape_special_chars(Input::get('gs_twilio_from')),
                'gs_show_inventory'     => mysql_escape_special_chars(Input::get('gs_show_inventory')),
                'gs_abandoned_mail'     => $abandoned_mail,
                'gs_mail_after'         => $mail_time,
                'gs_mail_duration'      => $mail_time_dur,
                'gs_refer_friend'       => mysql_escape_special_chars(Input::get('gs_refer_friend')),
                'gs_offer_percentage'   => mysql_escape_special_chars(Input::get('gs_offer_percentage')),
                'gs_banner_text'        => mysql_escape_special_chars(Input::get('site_banner_text')),
                'gs_password_protect'   => mysql_escape_special_chars(Input::get('gs_password_protect')),
                'gs_show_captcha'       => mysql_escape_special_chars(Input::get('gs_show_captcha')),
                'suspend_status'        => mysql_escape_special_chars(Input::get('suspend_status')),
                'suspend_time'          => mysql_escape_special_chars(Input::get('suspend_time')),
                'max_attempt'           => mysql_escape_special_chars(Input::get('max_attempt')),
                'suspend_duration'      => mysql_escape_special_chars(Input::get('suspend_duration')),
                'common_commission'     => mysql_escape_special_chars(Input::get('commissionpercentage')),
                'prefooter_text'        => $pre_footer_text,
                'prefooter_desc'        => $pre_footer_desc,
                'gs_delivery_kmrange'   => mysql_escape_special_chars(Input::get('gs_delivery_kmrange')),
                'app_sec_image'         => $app_img,
                'gs_login_image'        => $login_image,
				
                'gs_featured_store'     => mysql_escape_special_chars(Input::get('gs_featured_store')),
                'gs_featured_price'     => mysql_escape_special_chars(Input::get('gs_featured_price')),
                'gs_featured_numstore'  => mysql_escape_special_chars(Input::get('gs_featured_numstore')),
                'gs_feature_res_image'  => $featured_resImag,
                'gs_currency_api' 		=> mysql_escape_special_chars(Input::get('gs_currency_api')),
				
				);
				
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						$validatorlang = Validator::make($request->all(), [
                        'sitename_'.$Lang->lang_code => 'required',
                        'description_'.$Lang->lang_code => 'required',
                        'metatitle_'.$Lang->lang_code => 'required',
                        'metakeywords_'.$Lang->lang_code => 'required',
                        'metadescription_'.$Lang->lang_code => 'required',
                        'footertext_'.$Lang->lang_code => 'required',
                        'footerdescription_'.$Lang->lang_code => 'required',
                        'prefootertext_'.$Lang->lang_code => 'required',
                        'prefooterdesc_'.$Lang->lang_code => 'required'
						
						]);
						if($validatorlang->fails()){
							return redirect('admin-general-settings')->withErrors($validatorlang)->withInput();
							}else {
							$general_det['gs_sitename_' . $Lang->lang_code] = Input::get('sitename_' . $Lang->lang_code);
							$general_det['gs_sitedescription_' . $Lang->lang_code] = Input::get('description_' . $Lang->lang_code);
							$general_det['gs_metatitle_' . $Lang->lang_code] = Input::get('metatitle_' . $Lang->lang_code);
							$general_det['gs_metakeywords_' . $Lang->lang_code] = Input::get('metakeywords_' . $Lang->lang_code);
							$general_det['gs_metadesc_' . $Lang->lang_code] = Input::get('metadescription_' . $Lang->lang_code);
							$general_det['footer_text_' . $Lang->lang_code] = Input::get('footertext_' . $Lang->lang_code);
							$general_det['gs_footerdesc_' . $Lang->lang_code] = Input::get('footerdescription_' . $Lang->lang_code);
							$general_det['prefooter_text_' . $Lang->lang_code] = Input::get('prefootertext_' . $Lang->lang_code);
							$general_det['prefooter_desc_' . $Lang->lang_code] = Input::get('prefooterdesc_' . $Lang->lang_code);
						}
					}
				}
				
				if ($siteid != '') {
					$update = Settings::update_settings($siteid, $general_det);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
					
					} else {
					$insert = Settings::insert_settings($general_det);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				
				return Redirect::to('admin-general-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		public function smtp_settings()
		{
			if (Session::has('admin_id') == 1) {
				$settings_details = Settings::get_settings_details();
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_SMTP_Settings')) ? trans(Session::get('admin_lang_file') . '.ADMIN_SMTP_Settings') : trans($this->ADMIN_LANGUAGE . '.ADMIN_SMTP_Settings');
				return view('Admin.smtp_settings')->with('settings_details', $settings_details)->with('pagetitle', $page_title);
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function smtp_settings_submit(Request $request)
		{
			$smtp_host_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_HOST')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_HOST') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SMTP_HOST');
			$smtp_port_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_PORT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_PORT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SMTP_PORT');
			$smtp_email_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_EMAIL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_EMAIL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SMTP_EMAIL');
			$smtp_email_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ENTR_MAIL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ENTR_MAIL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ENTR_MAIL');
			$smtp_password_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_PASSWORD')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_SMTP_PASSWORD') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_SMTP_PASSWORD');
			
			$validator = Validator::make($request->all(), [
            'smtp_host' => 'required',
            'smtp_port' => 'required',
            'smtp_email' => 'required|email',
            'smtp_password' => 'required',
			
			], [
            'smtp_host.required' => $smtp_host_err_msg,
            'smtp_port.required' => $smtp_port_err_msg,
            'smtp_email.required' => $smtp_email_err_msg,
            'smtp_email.email' => $smtp_email_req_err_msg,
            'smtp_password.required' => $smtp_password_err_msg
			]);
			if ($validator->fails()) {
				return redirect('admin-smtp-settings')
                ->withErrors($validator)
                ->withInput();
				} else {
				$siteid = mysql_escape_special_chars(Input::get('siteid'));
				$smtp_host = mysql_escape_special_chars(Input::get('smtp_host'));
				$smtp_port = mysql_escape_special_chars(Input::get('smtp_port'));
				$smtp_email = mysql_escape_special_chars(Input::get('smtp_email'));
				$smtp_password = mysql_escape_special_chars(Input::get('smtp_password'));
				
				$insertArr = array(
				
                'gs_smtp_host' => $smtp_host,
                'gs_smtp_port' => $smtp_port,
                'gs_smtp_email' => $smtp_email,
                'gs_smtp_password' => $smtp_password
				
				
				);
				if ($siteid != '') {
					$update = Settings::update_smtp_settings($siteid, $insertArr);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
					
					} else {
					$insert = Settings::insert_smtp_settings($insertArr);
					
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				
				return Redirect::to('admin-smtp-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		public function social_settings()
		{
			if (Session::has('admin_id') == 1) 
			{
				$settings_details = DB::table('gr_general_setting')->first();
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_Social_Media_Settings')) ? trans(Session::get('admin_lang_file') . '.ADMIN_Social_Media_Settings') : trans($this->ADMIN_LANGUAGE . '.ADMIN_Social_Media_Settings');
				$details = array();
				if(empty($settings_details) === true)
				{
					$array_name = array();
					foreach(DB::getSchemaBuilder()->getColumnListing('gr_general_setting') as $res)
					{
						$array_name[$res]='';
					}
					$details = (object) $array_name; // return all value as empty.
				}
				else
				{
					$details = $settings_details;
				}
				
				return view('Admin.social_settings')->with('settings_details', $details)->with('pagetitle', $page_title);
			} 
			else 
			{
				return Redirect::to('admin-login');
			}
		}
		
		public function social_settings_submit(Request $request)
		{
			
			$fb_app_id_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FB_APP_ID')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FB_APP_ID') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_FB_APP_ID');
			$fb_app_secret_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FB_APP_SECRET')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FB_APP_SECRET') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_FB_APP_SECRET');
			$fb_app_link_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FB_LINK')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_FB_LINK') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_FB_LINK');
			$fb_redirect_url_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_REDIRECT_URL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_ENTER_REDIRECT_URL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_ENTER_REDIRECT_URL');
			
			$validator = Validator::make($request->all(), [
            'facebook_app_id' => 'required',
            'facebook_app_secret' => 'required',
            'facebook_app_link' => 'required',
            'gs_facebook_redirect_url' => 'required',
            'google_client_id_web' => 'required',
            'google_secret_key' => 'required',
            'google_redirect_url' => 'required',
            'google_page_url' => 'required',
            'google_map_key_web' => 'required',
            'twitter_page_url' => 'required',
            'linkedin_page_url' => 'required',
            'analytics_code' => 'required'
			
			
			], [
            'facebook_app_id.required' => $fb_app_id_err_msg,
            'facebook_app_secret.required' => $fb_app_secret_err_msg,
            'facebook_app_link.required' => $fb_app_link_err_msg,
            'gs_facebook_redirect_url.required' => $fb_redirect_url_err
			
			]);
			if ($validator->fails()) {
				return redirect('admin-social-settings')
                ->withErrors($validator)
                ->withInput();
				} else {
				$siteid = mysql_escape_special_chars(Input::get('siteid'));
				$facebook_app_id = mysql_escape_special_chars(Input::get('facebook_app_id'));
				$facebook_app_secret = mysql_escape_special_chars(Input::get('facebook_app_secret'));
				$facebook_app_link = mysql_escape_special_chars(Input::get('facebook_app_link'));
				$gs_facebook_redirect_url = mysql_escape_special_chars(Input::get('gs_facebook_redirect_url'));
				$google_client_id_web = mysql_escape_special_chars(Input::get('google_client_id_web'));
				
				$google_secret_key = mysql_escape_special_chars(Input::get('google_secret_key'));
				$google_redirect_url = mysql_escape_special_chars(Input::get('google_redirect_url'));
				$google_page_url = mysql_escape_special_chars(Input::get('google_page_url'));
				$google_map_key_web = mysql_escape_special_chars(Input::get('google_map_key_web'));
				$twitter_page_url = mysql_escape_special_chars(Input::get('twitter_page_url'));
				$linkedin_page_url = mysql_escape_special_chars(Input::get('linkedin_page_url'));
				$analytics_code = mysql_escape_special_chars_with_tags(Input::get('analytics_code'));
				$andr_link_cus = mysql_escape_special_chars(Input::get('play_store_link'));
				$ios_link_cus = mysql_escape_special_chars(Input::get('itunes_link'));
				$andr_link_mer = mysql_escape_special_chars(Input::get('play_store_link_mer'));
				$ios_link_mer = mysql_escape_special_chars(Input::get('itunes_link_mer'));
				$andr_link_del = mysql_escape_special_chars(Input::get('play_store_link_del'));
				$ios_link_del = mysql_escape_special_chars(Input::get('itunes_link_del'));

				$insertArr = array(
                'facebook_app_id_web' => $facebook_app_id,
               
                'facebook_secret_key' => $facebook_app_secret,
                'facebook_page_url' => $facebook_app_link,
                'google_client_id_web' => $google_client_id_web,
                
                'google_secret_key' => $google_secret_key,
                'google_redirect_url' => $google_redirect_url,
                'google_page_url' => $google_page_url,
                'google_map_key_web' => $google_map_key_web,
               'twitter_page_url' => $twitter_page_url,
                'linkedin_page_url' => $linkedin_page_url,
                'analytics_code' => $analytics_code,
                'gs_facebook_redirect_url' => $gs_facebook_redirect_url,
                'playstore_link' => $andr_link_cus,
                'itunes_link' => $ios_link_cus,
                'playstore_link_merchant' => $andr_link_mer,
                'itunes_link_merchant' => $ios_link_mer,
                'playstore_link_deliver' => $andr_link_del,
                'itunes_link_deliver' => $ios_link_del,
				
				);
				if ($siteid != '') {
					$update = Settings::update_settings($siteid, $insertArr);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
					
					} else {
					$insert = Settings::insert_settings($insertArr);
					
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				
				return Redirect::to('admin-social-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		public function logo_settings()
		{
			if (Session::has('admin_id') == 1) 
			{
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_LOGO_SETTINGS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_LOGO_SETTINGS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_LOGO_SETTINGS');
				$image_details = DB::table('gr_logo_settings')->select('id')->first();
				$details = array();
				if(empty($image_details) === true)
				{
					$array_name = array();
					foreach(DB::getSchemaBuilder()->getColumnListing('gr_logo_settings') as $res)
					{
						$array_name[$res]='';
					}
					$details = (object) $array_name; // return all value as empty.
				}
				else
				{
					$details = DB::table('gr_logo_settings')->first();
				}
				//print_r($details); exit;
				return view('Admin.logo_settings')->with(['pagetitle' =>  $page_title,'get_details' => $details]);
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function logo_settings_submit(Request $request)
		{ //echo request()->front_logo; exit;
			$front_logo_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_FRONT_LOGO_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_FRONT_LOGO_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_FRONT_LOGO_VAL');
			$front_logo_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_FRONT_IMG_VAL');
			$front_logo_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_FRONT_MIMES_VAL');
			$front_logo_max_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FRONT_MAX_SIZE_VAL');
			$front_logo_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FRONT_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FRONT_DIMEN_VAL');
			
			$admin_logo_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_LOGO_VAL');
			$admin_logo_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_IMG_LOGO_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_IMG_LOGO_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_IMG_LOGO_VAL');
			$admin_logo_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_MIMES_VAL');
			$admin_logo_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_MAX_SIZE_VAL');
			$admin_logo_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_DIMEN_VAL');
			
			$favicon_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FAV_VAL');
			$favicon_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_IMG_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FAV_IMG_VAL');
			$favicon_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FAV_MIMES_VAL');
			$favicon_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FAV_MAX_SIZE_VAL');
			$favicon_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_FAV_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . 'ADMIN_ADMIN_FAV_DIMEN_VAL');
			
			$spla_dimen_err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($this->ADMIN_LANGUAGE.'ADMIN_VALID_DIMENSION');
			
			$validator = Validator::make($request->all(), [
            'front_logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=140,max_width=200,min_height=50,max_height=50',
			
            'admin_logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=140,max_width=200,min_height=50,max_height=50',
			
            'favicon' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=50,max_width=80,min_height=50,max_height=80',
			// 'res_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=540,max_width=850,min_height=300,max_height=450',
            //'store_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=540,max_width=850,min_height=300,max_height=450',
            'splash_screen'     =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('mob_splash_width').',height='.Config::get('mob_splash_height').'',
            'mob_logo'     =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('mob_logo_wid').',height='.Config::get('mob_logo_hei').'',
            'grocery_icon'      =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('mob_gr_icon_wi').',height='.Config::get('mob_gr_icon_he').'',
            'res_icon'          =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('mob_gr_icon_wi').',height='.Config::get('mob_gr_icon_he').'',
            'splash_img_agent'  =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('mob_splash_width').',height='.Config::get('mob_splash_height').'',
            'splash_img_delivery'=>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('mob_splash_width').',height='.Config::get('mob_splash_height').'',
            'ios_login_logo'=>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_login_logo_wi').',height='.Config::get('ios_login_logo_he').'',
            'ios_signup_logo'=>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_register_logo_wi').',height='.Config::get('ios_register_logo_he').'',
            'ios_frpw_logo'=>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_forget_logo_wi').',height='.Config::get('ios_forget_logo_he').'',
            'ios_splash_screen' =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_splash_width').',height='.Config::get('ios_splash_height').'',
            'ios_grocery_icon'  =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_gr_icon_wi').',height='.Config::get('ios_gr_icon_wi').'',
            'ios_res_icon'      =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_gr_icon_wi').',height='.Config::get('ios_gr_icon_he').'',
            'ios_splash_agent'  =>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_splash_width').',height='.Config::get('ios_splash_height').'',
            'ios_splash_delivey'=>'sometimes|image|mimes:jpeg,png,jpg,svg|dimensions:width='.Config::get('ios_splash_width').',height='.Config::get('ios_splash_height').'',
			
			], [
            'front_logo.required' => $front_logo_err_msg,
            'front_logo.sometimes' => $front_logo_err_msg,
            'front_logo.image' => $front_logo_img_err_msg,
            'front_logo.mimes' => $front_logo_mimes_err_msg,
            'front_logo.max' => $front_logo_max_err_msg,
            'front_logo.dimensions' => $front_logo_dimen_err_msg,
			
            'admin_logo.required' => $admin_logo_req_err_msg,
            'admin_logo.image' => $admin_logo_img_err_msg,
            'admin_logo.mimes' => $admin_logo_mimes_err_msg,
            'admin_logo.max' => $admin_logo_max_size_err_msg,
            'admin_logo.dimensions' => $admin_logo_dimen_err_msg,
			
            'favicon.required' => $favicon_req_err_msg,
            'favicon.image' => $favicon_img_err_msg,
            'favicon.mimes' => $favicon_mimes_err_msg,
            'favicon.max' => $favicon_max_size_err_msg,
            'favicon.dimensions' => $favicon_dimen_err_msg,
			
            'splash_screen.sometimes'       => $spla_dimen_err_msg,
            'splash_screen.dimensions'      => $spla_dimen_err_msg,
            'splash_img_vendor.sometimes'    => $spla_dimen_err_msg,
            'splash_img_vendor.dimensions'   => $spla_dimen_err_msg,
            'splash_img_delivery.sometimes' => $spla_dimen_err_msg,
            'splash_img_delivery.dimensions'=> $spla_dimen_err_msg,
            'ios_splash_screen.sometimes'   => $spla_dimen_err_msg,
            'ios_splash_screen.dimensions'  => $spla_dimen_err_msg,
            'ios_splash_vendor.sometimes'    => $spla_dimen_err_msg,
            'ios_splash_vendor.dimensions'   => $spla_dimen_err_msg,
            'ios_splash_delivey.sometimes'  => $spla_dimen_err_msg,
            'ios_splash_delivey.dimensions' => $spla_dimen_err_msg,
            'mob_logo.sometimes'            => $spla_dimen_err_msg,
            'mob_logo.dimensions'           => $spla_dimen_err_msg,
            'ios_login_logo.sometimes'      => $spla_dimen_err_msg,
            'ios_login_logo.dimensions'     => $spla_dimen_err_msg,
            'ios_signup_logo.sometimes'     => $spla_dimen_err_msg,
            'ios_signup_logo.dimensions'    => $spla_dimen_err_msg,
            'ios_frpw_logo.sometimes'       => $spla_dimen_err_msg,
            'ios_frpw_logo.dimensions'      => $spla_dimen_err_msg,
			]);
			if ($validator->fails()) {
				return redirect('admin-logo-settings')
                ->withErrors($validator)
                ->withInput();
				} else {
				//print_r(request()->res_image);
				//exit;
				$destinationPath = public_path('images/logo');
				$logo_id = Input::get('logoid');
				
				
				if (request()->front_logo != '') {
					/** delete old image **/
					$image_path = public_path('images/logo/') . Input::get('pre_front_logo');  // Value is not URL but directory file path
					if (File::exists($image_path)) {
						$a = File::delete($image_path);
					}
					//$front_logo = 'front'.rand().'.'.request()->front_logo->getClientOriginalExtension();
					$front_logo = 'front_' . rand() . '.' . request()->front_logo->getClientOriginalExtension();
					
					$front_img = Image::make(request()->front_logo->getRealPath())->resize(140, 50);
					
					$front_img->save($destinationPath . '/' . $front_logo, 80);
					
					} else {
					$front_logo = Input::get('pre_front_logo');
					
				}
				
				
				if (request()->admin_logo != '') {
					/** delete old image **/
					$image_path2 = public_path('images/logo/') . Input::get('pre_admin_logo');  // Value is not URL but directory file path
					if (File::exists($image_path2)) {
						$a = File::delete($image_path2);
					}
					$admin_logo = 'admin_' . rand() . '.' . request()->admin_logo->getClientOriginalExtension();
					$admin_img = Image::make(request()->admin_logo->getRealPath())->resize(140, 50);
					$admin_img->save($destinationPath . '/' . $admin_logo, 80);
					} else {
					$admin_logo = Input::get('pre_admin_logo');
				}
				
				
				if (request()->favicon != '') {
					/** delete old image **/
					$image_path3 = public_path('images/logo/') . Input::get('pre_favicon');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$favicon = 'favicon_' . rand() . '.' . request()->favicon->getClientOriginalExtension();
					$favicon_img = Image::make(request()->favicon->getRealPath())->resize(50, 50);
					$favicon_img->save($destinationPath . '/' . $favicon, 80);
					} else {
					$favicon = Input::get('pre_favicon');
				}
				
				if (request()->store_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/logo/') . Input::get('pre_st_img');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$st_img_name = 'store_' . rand() . '.' . request()->store_image->getClientOriginalExtension();
					$st_img = Image::make(request()->store_image->getRealPath())->resize(540, 300);
					$st_img->save($destinationPath . '/' . $st_img_name, 80);
					} else {
					$st_img_name = Input::get('pre_st_img');
				}
				//exit;
				if (request()->res_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/logo/') . Input::get('pre_res_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$re_img_name = 'restaurant_' . rand() . '.' . request()->res_image->getClientOriginalExtension();
					$re_img = Image::make(request()->res_image->getRealPath())->resize(540, 300);
					$re_img->save($destinationPath . '/' . $re_img_name, 80);
					} else {
					$re_img_name = Input::get('pre_res_image');
				}
				
				if (request()->mob_logo != '') {
					/** delete old image **/
					$image_path3 = public_path('images/logo/') . Input::get('pre_mob_logo');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$logo_img_name = 'andr_logo' . rand() . '.' . request()->mob_logo->getClientOriginalExtension();
					$st_img = Image::make(request()->mob_logo->getRealPath())->resize(512,512);
					$st_img->save($destinationPath . '/' . $logo_img_name, 80);
					} else {
					$logo_img_name = Input::get('pre_mob_logo');
				}
				
				if(request()->splash_screen != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_splash_img');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $sp_img_name = 'splash_'.rand().'.'.request()->splash_screen->getClientOriginalExtension();
                    $re_img = Image::make(request()->splash_screen->getRealPath());
                    $re_img->save($destinationPath.'/'.$sp_img_name,80);
				}
                else
                {
                    $sp_img_name = Input::get('pre_splash_img'); 
				}
				
                
				
                if(request()->splash_img_vendor != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_splash_img_vendor');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $agent_splash = 'splash_vendor_'.rand().'.'.request()->splash_img_vendor->getClientOriginalExtension();
                    $re_img = Image::make(request()->splash_img_vendor->getRealPath());
                    $re_img->save($destinationPath.'/'.$agent_splash,80);
				}
                else
                {
                    $agent_splash = Input::get('pre_splash_img_vendor'); 
				}
				
                if(request()->splash_img_delivery != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_splash_img_delivery');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $delivery_splash = 'splash_delivery_'.rand().'.'.request()->splash_img_delivery->getClientOriginalExtension();
                    $re_img = Image::make(request()->splash_img_delivery->getRealPath());
                    $re_img->save($destinationPath.'/'.$delivery_splash,80);
				}
                else
                {
                    $delivery_splash = Input::get('pre_splash_img_delivery'); 
				}
				
                if(request()->ios_login_logo != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_ios_login_logo');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $ios_login = 'ios_login_logo'.rand().'.'.request()->ios_login_logo->getClientOriginalExtension();
                    $re_img = Image::make(request()->ios_login_logo->getRealPath());
                    $re_img->save($destinationPath.'/'.$ios_login,80);
				}
                else
                {
                    $ios_login = Input::get('pre_ios_login_logo'); 
				}
                if(request()->ios_signup_logo != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_ios_reg_sc_logo');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $ios_signup = 'ios_signup_logo'.rand().'.'.request()->ios_login_logo->getClientOriginalExtension();
                    $re_img = Image::make(request()->ios_signup_logo->getRealPath());
                    $re_img->save($destinationPath.'/'.$ios_signup,80);
				}
                else
                {
                    $ios_signup = Input::get('pre_ios_reg_sc_logo'); 
				}
                if(request()->ios_frpw_logo != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_ios_fp_pw_logo');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $ios_frpw = 'ios_frpw_logo'.rand().'.'.request()->ios_login_logo->getClientOriginalExtension();
                    $re_img = Image::make(request()->ios_frpw_logo->getRealPath());
                    $re_img->save($destinationPath.'/'.$ios_frpw,80);
				}
                else
                {
                    $ios_frpw = Input::get('pre_ios_fp_pw_logo'); 
				}
                if(request()->ios_splash_screen != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('pre_splash_img_ios');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $ios_splash = 'ios_splash_cus'.rand().'.'.request()->ios_splash_screen->getClientOriginalExtension();
                    $re_img = Image::make(request()->ios_splash_screen->getRealPath());
                    $re_img->save($destinationPath.'/'.$ios_splash,80);
				}
                else
                {
                    $ios_splash = Input::get('pre_splash_img_ios'); 
				}
				
				
                if(request()->ios_splash_vendor != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('vendor_splash_img_ios');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $ios_agent_spalsh = 'ios_splash_vendor'.rand().'.'.request()->ios_splash_vendor->getClientOriginalExtension();
                    $re_img = Image::make(request()->ios_splash_vendor->getRealPath());
                    $re_img->save($destinationPath.'/'.$ios_agent_spalsh,80);
				}
                else
                {
                    $ios_agent_spalsh = Input::get('vendor_splash_img_ios'); 
				}
				
                if(request()->ios_splash_delivey != '')
                {
                    /** delete old image **/
                    $image_path3 = public_path('images/logo/').Input::get('deli_splash_img_ios');  // Value is not URL but directory file path
                    if(File::exists($image_path3)) 
                    {
                        $a =   File::delete($image_path3);                   
					}
                    $ios_delivery_splash = 'ios_splash_delivery_'.rand().'.'.request()->ios_splash_delivey->getClientOriginalExtension();
                    $re_img = Image::make(request()->ios_splash_delivey->getRealPath());
                    $re_img->save($destinationPath.'/'.$ios_delivery_splash,80);
				}
                else
                {
                    $ios_delivery_splash = Input::get('deli_splash_img_ios'); 
				}
				
				$insertArr = array(
                'front_logo' => $front_logo,
                'admin_logo' => $admin_logo,
                'favicon' => $favicon,
                'restaurant_image' => $re_img_name,
                'store_image' => $st_img_name,
                'andr_splash_img_cus'       => $sp_img_name,
                'andr_splash_img_vendor'     => $agent_splash,
                'andr_splash_img_delivery'  => $delivery_splash,
                'ios_splash_img_cus'        => $ios_splash,
                'ios_splash_img_vendor'      => $ios_agent_spalsh,
                'ios_splash_img_delivery'   => $ios_delivery_splash,
                'andr_logo'                 => $logo_img_name,
                'ios_login_sc_logo'         => $ios_login,
                'ios_register_sc_logo'      => $ios_signup,
                'ios_forget_pw_logo'        => $ios_frpw,
				);
				
				if ($logo_id != '') {
					
					// DB::connection()->enableQueryLog();
					$update = Settings::update_logo_settings($logo_id, $insertArr);
					
					// print_r($query);
					//print_r($insertArr);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
					} else {
					
					$insert = Settings::insert_logo_settings($insertArr);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				
				return Redirect::to('admin-logo-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		public function noimage_settings()
		{
			if (Session::has('admin_id') == 1) {
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_NOIMAGE_SETTINGS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_NOIMAGE_SETTINGS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_NOIMAGE_SETTINGS');
				
				$noimage_settings = Settings::get_noimage_settings_details();
				return view('Admin.noimage_settings')->with('noimage_settings', $noimage_settings)->with('pagetitle', $page_title);
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function noimage_settings_submit(Request $request)
		{
			$bimg_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_NOBANNER_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_NOBANNER_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_SELECT_NOBANNER_IMAGE');
			$bimg_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_NOBANNER_IMG_VAL');
			$bimg_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_NOBANNER_MIMES_VAL');
			$bimg_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_NOBANNER_MAX_SIZE_VAL');
			$bimg_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_NOBANNER_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_NOBANNER_DIMEN_VAL');
			
			$rsimg_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_RES_STR_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_RES_STR_BANNER_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_SELECT_RES_STR_BANNER_IMAGE');
			$rsimg_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_STR_IMG_VAL');
			$rsimg_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_STR_MIMES_VAL');
			$rsimg_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_STR_MAX_SIZE_VAL');
			$rsimg_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_STR_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_STR_DIMEN_VAL');
			
			$res_itemimg_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_REST_ITEM_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_REST_ITEM_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_SELECT_REST_ITEM_IMAGE');
			$res_itemimg_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_ITEM_IMG_VAL');
			$res_itemimg_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_ITEM_MIMES_VAL');
			$res_itemimg_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_ITEM_MAX_SIZE_VAL');
			$res_itemimg_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_REST_ITEM_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_REST_ITEM_DIMEN_VAL');
			
			$str_itemimg_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_STR_PROD_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_STR_PROD_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_SELECT_STR_PROD_IMAGE');
			$str_itemimg_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_STR_PROD_IMG_VAL');
			$str_itemimg_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_STR_PROD_MIMES_VAL');
			$str_itemimg_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_STR_PROD_MAX_SIZE_VAL');
			$str_itemimg_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_STR_PROD_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_STR_PROD_DIMEN_VAL');
			
			$land_img_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_LAND_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_LAND_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_SELECT_LAND_IMAGE');
			$land_img_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LAND_IMG_IMG_VAL');
			$land_img_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LAND_IMG_MIMES_VAL');
			$land_img_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LAND_IMG_MAX_SIZE_VAL');
			$land_img_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LAND_IMG_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LAND_IMG_DIMEN_VAL');
			
			$logo_img_req_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PLEASE_SELECT_LOGO_IMAGE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PLEASE_SELECT_LOGO_IMAGE');
			$logo_img_img_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LOGO_IMG_VAL');
			$logo_img_mimes_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_MIMES_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_MIMES_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LOGO_IMG_MIMES_VAL');
			$logo_img_max_size_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_MAX_SIZE_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_MAX_SIZE_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LOGO_IMG_MAX_SIZE_VAL');
			$logo_img_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_DIMEN_VAL')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADMIN_LOGO_IMG_DIMEN_VAL') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADMIN_LOGO_IMG_DIMEN_VAL');
			
			$cate_dimen_err_msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file') . '.ADMIN_VALID_DIMENSION') : trans($this->ADMIN_LANGUAGE . 'ADMIN_VALID_DIMENSION');
			
			$noimageid = Input::get('noimageid');
			
			$validator = Validator::make($request->all(), [
            'banner_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=1366,min_height=300', 'max_width=1500,max_height=500',
			
            'res_store_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=1366,min_height=300', 'max_width=1500,max_height=500',
			
			
            'res_item_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=800,min_height=800',
			
            'store_item_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=800,min_height=800',
			
            'landing_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=560,min_height=294',
            'logo_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions::min_width=140,min_height=50,max_width=200,max_height=50',
            'sh_logo_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,min_height=300,max_width=500,max_height=500',
            'cate_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=300,max_width=500,min_height=150,max_height=300'
			], [
            'banner_image.sometimes' => $bimg_req_err_msg,
            'banner_image.image' => $bimg_img_err_msg,
            'banner_image.mimes' => $bimg_mimes_err_msg,
            'banner_image.max' => $bimg_max_size_err_msg,
            'banner_image.dimensions' => $bimg_dimen_err_msg,
			
            'res_store_image.sometimes' => $rsimg_req_err_msg,
            'res_store_image.image' => $rsimg_img_err_msg,
            'res_store_image.mimes' => $rsimg_mimes_err_msg,
            'res_store_image.max' => $rsimg_max_size_err_msg,
            'res_store_image.dimensions' => $rsimg_dimen_err_msg,
			
            'res_item_image.sometimes' => $res_itemimg_req_err_msg,
            'res_item_image.image' => $res_itemimg_img_err_msg,
            'res_item_image.mimes' => $res_itemimg_mimes_err_msg,
            'res_item_image.max' => $res_itemimg_max_size_err_msg,
            'res_item_image.dimensions' => $res_itemimg_dimen_err_msg,
			
            'store_item_image.sometimes' => $str_itemimg_req_err_msg,
            'store_item_image.image' => $str_itemimg_img_err_msg,
            'store_item_image.mimes' => $str_itemimg_mimes_err_msg,
            'store_item_image.max' => $str_itemimg_max_size_err_msg,
            'store_item_image.dimensions' => $str_itemimg_dimen_err_msg,
			
            'landing_image.sometimes' => $land_img_req_err_msg,
            'landing_image.image' => $land_img_img_err_msg,
            'landing_image.mimes' => $land_img_mimes_err_msg,
            'landing_image.max' => $land_img_max_size_err_msg,
            'landing_image.dimensions' => $land_img_dimen_err_msg,
			
            'logo_image.sometimes' => $logo_img_req_err_msg,
            'logo_image.image' => $logo_img_img_err_msg,
            'logo_image.mimes' => $logo_img_mimes_err_msg,
            'logo_image.max' => $logo_img_max_size_err_msg,
            'logo_image.dimensions' => $logo_img_dimen_err_msg,
            'cate_image.dimensions' => $cate_dimen_err_msg,
			]);
			
			if ($validator->fails()) {
				return redirect('admin-noimage-settings')
                ->withErrors($validator)
                ->withInput();
				} else {
				
				$destinationPath = public_path('images/noimage');
				
				
				if (request()->banner_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_banner_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$banner_image = 'banner' . time() . '.' . request()->banner_image->getClientOriginalExtension();
					$banner = Image::make(request()->banner_image->getRealPath())->resize(1366,500);
					$banner->save($destinationPath . '/' . $banner_image, 80);
					} else {
					$banner_image = Input::get('old_banner_image');
				}
				
				
				if (request()->res_store_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_res_store_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$res_store_image = 'res_store' . time() . '.' . request()->res_store_image->getClientOriginalExtension();
					$res_store = Image::make(request()->res_store_image->getRealPath())->resize(1366, 300);
					$res_store->save($destinationPath . '/' . $res_store_image, 80);
					} else {
					$res_store_image = Input::get('old_res_store_image');
				}
				
				if (request()->res_item_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_res_item_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$res_item_image = 'res_item' . time() . '.' . request()->res_item_image->getClientOriginalExtension();
					$res_item = Image::make(request()->res_item_image->getRealPath())->resize(800, 800);
					$res_item->save($destinationPath . '/' . $res_item_image, 80);
					} else {
					$res_item_image = Input::get('old_res_item_image');
				}
				
				if (request()->store_item_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_store_item_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$store_item_image = 'store_item' . time() . '.' . request()->store_item_image->getClientOriginalExtension();
					$store_item = Image::make(request()->store_item_image->getRealPath())->resize(800, 800);
					$store_item->save($destinationPath . '/' . $store_item_image, 80);
					} else {
					$store_item_image = Input::get('old_store_item_image');
				}
				
				if (request()->landing_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_landing_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$landing_image = 'landing' . time() . '.' . request()->landing_image->getClientOriginalExtension();
					$landing = Image::make(request()->landing_image->getRealPath())->resize(560, 294);
					$landing->save($destinationPath . '/' . $landing_image, 80);
					} else {
					$landing_image = Input::get('old_landing_image');
				}
				
				if (request()->logo_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_logo_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$logo_image = 'logo' . time() . '.' . request()->logo_image->getClientOriginalExtension();
					$logo = Image::make(request()->logo_image->getRealPath())->resize(140, 50);
					$logo->save($destinationPath . '/' . $logo_image, 80);
					} else {
					$logo_image = Input::get('old_logo_image');
				}
				
				if (request()->sh_logo_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_shlogo_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$shop_logo_image = 'shop_logo' . time() . '.' . request()->sh_logo_image->getClientOriginalExtension();
					$logo = Image::make(request()->sh_logo_image->getRealPath())->resize(300, 300);
					$logo->save($destinationPath . '/' . $shop_logo_image, 80);
					} else {
					$shop_logo_image = Input::get('old_shlogo_image');
				}
				
				/* NO IMAGE FOR CATEGORY */
				if (request()->cate_image != '') {
					/** delete old image **/
					$image_path3 = public_path('images/noimage/') . Input::get('old_cate_image');  // Value is not URL but directory file path
					if (File::exists($image_path3)) {
						$a = File::delete($image_path3);
					}
					$ca_img_name = 'category_' . rand() . '.' . request()->cate_image->getClientOriginalExtension();
					$re_img = Image::make(request()->cate_image->getRealPath())->resize(300, 150);
					$re_img->save($destinationPath . '/' . $ca_img_name, 80);
					} else {
					$ca_img_name = Input::get('old_cate_image');
				}
				
				
				$insertArr = array(
                'banner_image' => $banner_image,
                'restaurant_store_image' => $res_store_image,
                'restaurant_item_image' => $res_item_image,
                'product_image' => $store_item_image,
                'landing_image' => $landing_image,
                'logo_image' => $logo_image,
                'shop_logo_image' => $shop_logo_image,
                'category_image' => $ca_img_name,
				);
				
				
				if ($noimageid != '') {
					$update = Settings::update_noimage_settings($noimageid, $insertArr);
					$msg = 'Update Successfully!';
					} else {
					$insert = Settings::insert_noimage_settings($insertArr);
					
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				
				return Redirect::to('admin-noimage-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		public function payment_settings()
		{
			if (Session::has('admin_id') == 1) {
				$payment_settings = Settings::get_payment_settings();
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_PAYMENT_SETTINGS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_PAYMENT_SETTINGS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_PAYMENT_SETTINGS');
				
				return view('Admin.payment_settings')->with('payment_settings', $payment_settings)->with('pagetitle', $page_title);
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function paynamics_payment_settings_submit(Request $request)
		{
			$validator = Validator::make($request->all(), [
            'paynamics_client_id' => 'required',
			
            'paynamics_secret_id' => 'required',
			
            'paynamics_status' => 'required',
			
            'paynamics_mode' => 'required'
			
			]);
			if ($validator->fails()) 
			{
				return redirect('admin-payment-settings')
                ->withErrors($validator)
                ->withInput();
				}
				else
				{
				$ps_id = mysql_escape_special_chars(Input::get('ps_id'));
				$paynamics_client_id = mysql_escape_special_chars(Input::get('paynamics_client_id'));
				$paynamics_secret_id = mysql_escape_special_chars(Input::get('paynamics_secret_id'));
				$paynamics_status = mysql_escape_special_chars(Input::get('paynamics_status'));
				$paynamics_mode = mysql_escape_special_chars(Input::get('paynamics_mode'));

				$insertArr = array(
                'paynamics_client_id' => $paynamics_client_id,
                'paynamics_secret_id' => $paynamics_secret_id,
                'paynamics_status' => $paynamics_status,
                'paynamics_mode' => $paynamics_mode
				);
				
				/* check payment status */
				$get_status = get_payment();
				if(empty($get_status) === false)
				{
					if($get_status->paymaya_status == 0 && $get_status->cod_status == 0 && $paynamics_status == 0)
					{	
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MIN_ONE_PAYMENT_ENABLE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MIN_ONE_PAYMENT_ENABLE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MIN_ONE_PAYMENT_ENABLE');
						return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
					}
				}
				if ($ps_id != '') 
				{
					$insert = Settings::update_paynamics_details($ps_id, $insertArr);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
					
					
				}
				else 
				{
					$update = Settings::insert_paynamics_details($insertArr);
					//                $msg = 'Added Paynamics Successfully!';
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				
				
				return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
				
				
			}
		}
		
		public function paymaya_payment_settings_submit(Request $request)
		{
			$validator = Validator::make($request->all(), [
            'paymaya_client_id' => 'required',
			
            'paymaya_secret_id' => 'required',
			
            'paymaya_status' => 'required',
			
            'paymaya_mode' => 'required'
			
			]);
			if ($validator->fails()) 
			{
				return redirect('admin-payment-settings')
                ->withErrors($validator)
                ->withInput();
			} 
			else 
			{
				$ps_id = Input::get('ps_id');
				$paymaya_client_id = Input::get('paymaya_client_id');
				$paymaya_secret_id = Input::get('paymaya_secret_id');
				$paymaya_status = Input::get('paymaya_status');
				$paymaya_mode = Input::get('paymaya_mode');
				
				$insertArr = array(
                'paymaya_client_id' => $paymaya_client_id,
                'paymaya_secret_id' => $paymaya_secret_id,
                'paymaya_status' => $paymaya_status,
                'paymaya_mode' => $paymaya_mode
				);
				/* check payment status */
				$get_status = get_payment();
				if(empty($get_status) === false)
				{
					if($get_status->paynamics_status == 0 && $get_status->cod_status == 0 && $paymaya_status == 0)
					{	
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MIN_ONE_PAYMENT_ENABLE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MIN_ONE_PAYMENT_ENABLE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MIN_ONE_PAYMENT_ENABLE');
						return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
					}
				}
				if ($ps_id != '') 
				{
					$insert = Settings::update_paymaya_details($ps_id, $insertArr);
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				} 
				else 
				{
					$update = Settings::insert_paymaya_details($insertArr);
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
				}
				
				return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		/** cod status **/
		public function cod_payment_settings_submit(Request $request)
		{
			$validator = Validator::make($request->all(), [
            'cod_status' => 'required',
			]);
			if ($validator->fails())
			{
				return redirect('admin-payment-settings')
                ->withErrors($validator)
                ->withInput();
			} 
			else 
			{
				$ps_id = Input::get('ps_id');
				$cod_status = Input::get('cod_status');
				/* check payment status */
				$get_status = get_payment();
				if(empty($get_status) === false)
				{
					if($get_status->paynamics_status == 0 && $get_status->paymaya_status == 0 && $cod_status == 0)
					{	
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MIN_ONE_PAYMENT_ENABLE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MIN_ONE_PAYMENT_ENABLE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MIN_ONE_PAYMENT_ENABLE');
						return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
					}
				}
				
				if ($ps_id != '') 
				{
					$insert = Settings::update_paymaya_details($ps_id, ['cod_status' => $cod_status]);
					$msg = 'Updated Cod Status Successfully!';
					} else {
					$update = Settings::insert_paymaya_details(['cod_status' => $cod_status]);
					$msg = 'Added Cod Status Successfully!';
				}
				
				return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
			}
		}
		
		public function banner_settings()
		{
			if (Session::has('admin_id') == 1) {
				$get_country_details = get_all_details('gr_banner_image', 'banner_status', 10, 'desc', 'id');
				$landing_store_banner_count = DB::table('gr_banner_image')->where('banner_type', '3')->where('banner_status', '<>', '2')->count();
				$landing_resta_banner_count = DB::table('gr_banner_image')->where('banner_type', '4')->where('banner_status', '<>', '2')->count();
				
				//print_r($get_country_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MANAGE_BANNER')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MANAGE_BANNER') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MANAGE_BANNER');
				$id = '';
				return view('Admin.manage_banner')->with('pagetitle', $page_title)->with('all_details', $get_country_details)->with('id', $id)->with('landing_store_banner_count', $landing_store_banner_count)->with('landing_resta_banner_count', $landing_resta_banner_count);
				
				
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function edit_banner($id)
		{
			$id = base64_decode($id);
			$where = ['id' => $id];
			$landing_store_banner_count = DB::table('gr_banner_image')->where('banner_type', '3')->where('banner_status', '<>', '2')->count();
			$landing_resta_banner_count = DB::table('gr_banner_image')->where('banner_type', '4')->where('banner_status', '<>', '2')->count();
			
			$get_country_details = get_details('gr_banner_image', $where);
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MANAGE_BANNER')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MANAGE_BANNER') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MANAGE_BANNER');
			$get_allcountry_details = get_all_details('gr_banner_image', 'banner_status', 10, 'desc', 'id');
			return view('Admin.manage_banner')->with('pagetitle', $page_title)->with('all_details', $get_allcountry_details)->with('image_detail', $get_country_details)->with('id', $id)->with('landing_store_banner_count', $landing_store_banner_count)->with('landing_resta_banner_count', $landing_resta_banner_count);
		}
		
		public function add_update_banner(Request $request)
		{
			if (Session::has('admin_id') == 1) {
				if (Input::get('banner_id') != '') {
					$banner_image_req = 'Sometimes';
					} else {
					$banner_image_req = 'required';
				}
				$validator = Validator::make($request->all(), [
				//                'image_title' => 'required',
				
                'image_text' => 'required',
				
                'banner_image' => $banner_image_req . '|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=1366,min_height=500,max_width=1500,max_height=500',
				
                'banner_type' => 'required'
				
				]);
				if ($validator->fails()) {
					return redirect('admin-banner-settings/?addnow=1')
                    ->withErrors($validator)
                    ->withInput();
					} else {
					$banner_id = Input::get('banner_id');
					//                $image_title = Input::get('image_title');
					$image_text = mysql_escape_special_chars(Input::get('image_text'));
					$banner_type = mysql_escape_special_chars(Input::get('banner_type'));
					if ($request->hasFile('banner_image')) // add or update new banner images
					{
						$banner_image = 'banner' . time() . '.' . request()->banner_image->getClientOriginalExtension();
						$destinationPath = public_path('images/banner');
						$banner = Image::make(request()->banner_image->getRealPath())->resize(1366, 500);
						$banner->save($destinationPath . '/' . $banner_image, 80);
						} else {
						$banner_image = Input::get('oldBanner');
					}
					$check_banner_count = Settings::check_banner_count($banner_type);
					
					
					if ($check_banner_count >= 6 && $banner_id == '') // && $banner_id == ''
					{
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BANNER_LIMIT_EXCEED')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BANNER_LIMIT_EXCEED') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BANNER_LIMIT_EXCEED');
						return Redirect::to('admin-banner-settings')->withErrors(['message' => $msg]);
						} elseif ($check_banner_count > 6 && $banner_id != '') {
						$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BANNER_LIMIT_EXCEED')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BANNER_LIMIT_EXCEED') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BANNER_LIMIT_EXCEED');
						return Redirect::to('admin-banner-settings')->withErrors(['message' => $msg]);
						} else {
						
						$insertArr = array(
						//                        'image_title' => $image_title,
                        'image_text' => $image_text,
                        'banner_image' => $banner_image,
                        'banner_type' => $banner_type,
                        'banner_status' => 1,
                        'created_at' => date('Y-m-d')
						);
						
						if ($banner_id != '') {
							$update = updatevalues('gr_banner_image', $insertArr, ['id' => $banner_id]);
							$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
							} else {
							$insert = insertvalues('gr_banner_image', $insertArr);
							$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
						}
						
					}
					return Redirect::to('admin-banner-settings')->withErrors(['success' => $msg]);
				}
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function change_banner_status($id, $status)
		{    //echo $status; echo $id; exit;
			$update = ['banner_status' => $status];
			$where = ['id' => $id];
			$a = updatevalues('gr_banner_image', $update, $where);
			//echo $a; exit;
			if ($status == 1) //Active
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('admin-banner-settings');
			}
			if ($status == 2) //Delete
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_DELETE_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('admin-banner-settings');
			} else   //block
			{
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_BLOCK_SUCCESS');
				Session::flash('message', $msg);
				return Redirect::to('admin-banner-settings');
			}
		}
		
		/** manage abandoned cart **/
		public function manage_cart()
		{
			if (Session::has('admin_id') == 1) {
				$array_name = array();
				$get_details = DB::table('gr_general_setting')->select('gs_abandoned_mail', 'gs_mail_after', 'gs_mail_duration', 'gs_id')->first();
				if (empty($get_details) === true) {
					foreach (DB::getSchemaBuilder()->getColumnListing('gr_general_setting') as $res) {
						$array_name[$res] = '';
					}
					$get_details = (object)$array_name; // return all value as empty.
				}
				
				$pagetitle = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MNGE_ABAN_CART')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MNGE_ABAN_CART') : trans($this->ADMIN_OUR_LANGUAGE . '.ADMIN_MNGE_ABAN_CART');
				return view('Admin.cart_settings')->with(['pagetitle' => $pagetitle, 'get_details' => $get_details]);
				} else {
				return Redirect::to('admin-login');
			}
		}
		
		public function update_cart_setting(Request $request)
		{ //print_r($request->all()); exit;
			$mail_status_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_CART_STATUS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_CART_STATUS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_CART_STATUS');
			$mail_dura_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MAIL_TIME')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MAIL_TIME') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MAIL_TIME');
			
			$validator = Validator::make($request->all(), ['gs_abandoned_mail' => 'required',
            'gs_mail_after' => 'required',], ['gs_abandoned_mail.required' => $mail_status_err,
            'gs_mail_after.required' => $mail_dura_err]);
			if ($validator->fails()) {
				return redirect('abandoned-cart-setting')
                ->withErrors($validator)
                ->withInput();
				} else {
				$abandoned_mail = Input::get('gs_abandoned_mail');
				$mail_time = Input::get('gs_mail_after');
				$mail_time_dur = Input::get('mail_duration');
				$siteid = Input::get('siteid');
				
				$insertArr = array('gs_abandoned_mail' => $abandoned_mail,
                'gs_mail_after' => $mail_time,
                'gs_mail_duration' => $mail_time_dur
				);
				
				$update = Settings::update_settings($siteid, $insertArr);
				
				$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
				
				return Redirect::to('abandoned-cart-setting')->withErrors(['success' => $msg]);
			}
		}
		
		
		public function admin_netbanking_submit(Request $request)
		{
			$validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'branch' => 'required',
            'bank_accno' => 'required',
            'ifsc' => 'required'
			]);
			if ($validator->fails()) {
				return redirect('admin-payment-settings')
                ->withErrors($validator)
                ->withInput();
				} else {
				$ps_id = Input::get('ps_id');
				$netbank_status = Input::get('netbank_status');
				$bank_accno = Input::get('bank_accno');
				$bank_name = Input::get('bank_name');
				$branch = Input::get('branch');
				$ifsc = Input::get('ifsc');
				
				$insertArr = array(
                'netbank_status' => $netbank_status,
                'bank_accno' => $bank_accno,
                'bank_name' => $bank_name,
                'branch' => $branch,
                'ifsc' => $ifsc
				);
				
				if ($ps_id != '') {
					$insert = Settings::update_paynamics_details($ps_id, $insertArr);
					$msg = 'Updated Netbanking details Successfully!';
					} else {
					$update = Settings::insert_paynamics_details($insertArr);
					$msg = 'Added Netbanking details Successfully!';
				}
				
				return Redirect::to('admin-payment-settings')->withErrors(['success' => $msg])->withInput();
				
				
			}
		}
		
		public function manage_advertisement()
		{
			$page_title = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_MANAGE_ADVERTISE')) ? trans(Session::get('admin_lang_file') . '.ADMIN_MANAGE_ADVERTISE') : trans($this->ADMIN_LANGUAGE . '.ADMIN_MANAGE_ADVERTISE');
			
			$get_advertisemnt_det = get_advertisement_details();
			
			
			return view('Admin.manage_advertisement')->with('pagetitle', $page_title)->with('adv_details',$get_advertisemnt_det);
			
		}
		
		public function advertisement_submit(Request $request)
		{
			$add_img_size = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADD_IMAGE_ERR')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADD_IMAGE_ERR') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADD_IMAGE_ERR');
			
			$add_img_format = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADD_IMAGE_FORMAT')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADD_IMAGE_FORMAT') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADD_IMAGE_FORMAT');
			
			$ad_title_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_TITLE_REC')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_TITLE_REC') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADVERTISE_TITLE_REC');
			
			$ad_desc_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_DESC_REC')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_DESC_REC') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADVERTISE_DESC_REC');
			
			/* --------ADD IMAGE VALIDATION START- ------*/
			
			
			if (request()->advertisement_image != '') {
				$validatoradd = Validator::make($request->all(), [
                'advertisement_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=1360,max_width=1370,min_height=390,max_height=400',
				], [
                'advertisement_image.image' => $add_img_format,
                'advertisement_image.mimes' => $add_img_size,
                'advertisement_image.max' => $add_img_size,
                'advertisement_image.dimensions' => $add_img_size,
				]);
				if ($validatoradd->fails()) {
					return redirect('manage-advertisement')->withErrors($validatoradd)->withInput();
				}
				
			}
			/* -------- ADD IMAGE VALIDATION END ------*/
			
			
			$validator = Validator::make($request->all(),['advertisement_title' => 'required','advertisement_desc' => 'required'],
			['advertisement_title.required' => $ad_title_err,'advertisement_desc.required' => $ad_desc_err]);
			if ($validator->fails()) {
				return redirect('manage-advertisement')
                ->withErrors($validator)
                ->withInput();
				} else {
				
				/* ---------ADD IMAGE START-----------*/
				if (request()->advertisement_image != '') {
					$destinationPath = public_path('front/frontImages');
					/* deleting the existing image*/
					$addimgPath = public_path('front/frontImages/') . input::get('pre_adv_image');
					if (File::exists($addimgPath)) {
						$delete = File::delete($addimgPath);
					}
					
					$add_img = 'add_' . rand() . '.' . request()->advertisement_image->getClientOriginalExtension();
					$landAdd_img = Image::make(request()->advertisement_image->getRealPath())->resize(1366, 399);
					$landAdd_img->save($destinationPath . '/' . $add_img, 80);
					} else {
					$add_img = input::get('pre_adv_image');
				}
				/* ---------ADD IMAGE END-----------*/
				
				$ad_title = mysql_escape_special_chars(Input::get('advertisement_title'));
				$ad_desc =mysql_escape_special_chars(Input::get('advertisement_desc'));
				$ad_link = mysql_escape_special_chars(Input::get('advertisement_link'));
				$ad_id = mysql_escape_special_chars(Input::get('adv_id'));
				
				$insertAdv = array(
                'ad_title' => $ad_title,
                'ad_link' => $ad_link,
                'ad_desc' => $ad_desc,
                'ad_image' => $add_img
				);
				
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						$ad_title_other_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_TITLE_OTHER_REQ')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_TITLE_OTHER_REQ') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADVERTISE_TITLE_OTHER_REQ');
						$ad_title_other_err = str_replace(':Lang',$Lang->lang_name,$ad_title_other_err);
						
						$ad_desc_other_err = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_DESC_OTHER_REQ')) ? trans(Session::get('admin_lang_file') . '.ADMIN_ADVERTISE_DESC_OTHER_REQ') : trans($this->ADMIN_LANGUAGE . '.ADMIN_ADVERTISE_DESC_OTHER_REQ');
						$ad_desc_other_err = str_replace(':Lang',$Lang->lang_name,$ad_desc_other_err);
						
						$validatoradv = Validator::make($request->all(),['advertisement_title_'.$Lang->lang_code => 'required','advertisement_desc_'.$Lang->lang_code => 'required'],
						['advertisement_title_'.$Lang->lang_code.'.required' => $ad_title_other_err,'advertisement_desc_'.$Lang->lang_code.'.required' => $ad_desc_other_err]);
						/*								
							$validatoradv = Validator::make($request->all(), [
							'advertisement_title_'.$Lang->lang_code => 'required',
							'advertisement_desc_'.$Lang->lang_code => 'required'
						]);*/
						if($validatoradv->fails()){
							return redirect('manage-advertisement')->withErrors($validatoradv)->withInput();
							}else {
							$insertAdv['ad_title_' . $Lang->lang_code] = Input::get('advertisement_title_' . $Lang->lang_code);
							$insertAdv['ad_desc_' . $Lang->lang_code] = Input::get('advertisement_desc_' . $Lang->lang_code);
						}
					}
				}
				
				
				
				if ($ad_id != '') {
					$update = updatevalues('gr_advertisement', $insertAdv, ['ad_id' => $ad_id]);
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_UPDATE_SUCCESS');
					
					} else {
					$insert = insertvalues('gr_advertisement', $insertAdv);
					
					$msg = (Lang::has(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file') . '.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_LANGUAGE . '.ADMIN_INSERT_SUCCESS');
					
				}
				return Redirect::to('manage-advertisement')->withErrors(['success' => $msg]);
				
				
			}
			
			
		}

		
				
	}		