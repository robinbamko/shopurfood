<?php
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	
	use Illuminate\Http\Request;
	
	use Illuminate\Support\Facades\DB;
	
	use Illuminate\Support\Facades\Input;
	
	use Illuminate\Support\Facades\Auth;
	
	use Illuminate\Validation\Rule;
	
	use Validator;
	
	use Session;
	
	use Mail;
	
	use View;
	
	use Lang;
	
	use Redirect;
	
	use Image;
	
	use App\Admin;
	
	use App\Settings;
	
	use Response;
	
	use File;
	
	
	class NewsletterController extends Controller
	{
		
		
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		
		
		/* MANAGE NEWSLETTER */
		public function manage_news_template()
		{
			if(Session::has('admin_id') == 0)
			{
				return redirect('admin-login');
			}
			
			$subscriber_details = DB::table('gr_newsletter_subscription')->orderBy('id','DESC')->paginate(10);
			
			$pagetitle =  (Lang::has(Session::get('admin_lang_file').'.ADMIN_MGMT_NEWSLETTER_TEMPLATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MGMT_NEWSLETTER_TEMPLATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_MGMT_NEWSLETTER_TEMPLATE');
			return view('Admin.newsletter.Manage_news_letter')->with('pagetitle',$pagetitle)->with('subscriber_details',$subscriber_details);
		}
		
		
		
		/*SEND NEWSLETTER PAGE*/
		
		Public function send_newsletter(Request $request){
			
			if(Session::has('admin_id')==1){
				
				$customer_details = DB::table('gr_customer')->select('cus_id','cus_email','cus_fname')->where('cus_status','1')->get();
				$subscriber_details = DB::table('gr_newsletter_subscription')->select('news_email_id','id')->where('news_status','1')->get();
				
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEND_NEWSLETTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEND_NEWSLETTER') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SEND_NEWSLETTER');
				return view('Admin.newsletter.send_newsletter')->with('pagetitle',$page_title)->with('customer_details',$customer_details)->with('subscriber_details',$subscriber_details);
				
			}
			else
			{
				return redirect('admin-login');
			}
			
			
		}
		
		/*SEND NEWSLETTER*/
		Public function send_newsletter_submit(Request $request){
			
			if(Session::has('admin_id')==1){
				
				if($_POST){
					$this->validate($request, 
					[ 	'email_to'		=>'Required',
					'user_id'		=>'required_if:email_to,1',
					'subscriber_id'	=>'required_if:email_to,2',
					'newsletter_subject'		=>'Required',
					'newsletter_message'	=>'Required'
					],
					[
					'email_to.required'    => (Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_SELECTMAILTO')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_SELECTMAILTO') : trans($this->MER_OUR_LANGUAGE.'.MER_NEWSLE_SELECTMAILTO'), 
					'user_id.required_if'    => (Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_CUSTOMER_MAIL')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_CUSTOMER_MAIL') : trans($this->MER_OUR_LANGUAGE.'.MER_NEWSLE_CUSTOMER_MAIL'), 
					'subscriber_id.required_if'    => (Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_SUBSCRIBER_MAIL')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_SUBSCRIBER_MAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_SUBSCRIBER_MAIL'),
					'newsletter_subject.required'    => (Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_SUBJECT')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_SUBJECT') : trans($this->ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_ENTER_SUBJECT'),
					'newsletter_message.required'    => (Lang::has(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_MESSAGE')) ? trans(Session::get('admin_lang_file').'.MER_NEWSLE_ENTER_MESSAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.MER_NEWSLE_ENTER_MESSAGE')
					]);
					
					if (!empty(Input::get('user_id')))
					{
						$userid = Input::get('user_id');
						/* Particular User */
						$user_details = DB::table('gr_customer')->select('cus_email')->whereIn('cus_id', $userid)->get();
					}
					else if(!empty(Input::get('subscriber_id')))
					{
						$userid = Input::get('subscriber_id');
						/* Subscribed User */
						$user_details = DB::table('gr_newsletter_subscription')->select('news_email_id As cus_email')->whereIn('id', $userid)->get();
					}
					else {
						/* All users */
						$user_details = DB::table('gr_customer')->select('cus_email')->where('cus_status','=','1')->get();
					}
					
					
					if(count($user_details)>0){
						/* Mail Functionality starts */
						$newsletter_subject =Input::get('newsletter_subject');
						$newsletter_template = Input::get('newsletter_message');
						/*$this->mail->queue('email.news_letter', ['news_template'=>$newsletter_template], function($message) use ($newsletter_template, $newsletter_subject, $from, $fromName, $to)
							{
							$message->to($to, $toUserName)
							->from($from, $fromName)
							->subject($subject);
							});
						*/
						Mail::Send('email.news_letter',['news_template'=>$newsletter_template], function($message) use ($newsletter_template,$user_details,$newsletter_subject){
							$message->setBody($newsletter_template, 'text/html');
							foreach($user_details as $u) {
								$message->to($u->cus_email)->subject($newsletter_subject .'- Daily Newsletter');
							}
							
						});
						
						/* Mail Functionality Ends */
						$mesage = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SENT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SENT_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SENT_SUCCESS') ;
						
						return redirect('send-newsletter')->with('message',$mesage);
						} else {
						
						$mesage = (Lang::has(Session::get('admin_lang_file').'.ADMIN_USER_NOT_FOUND')) ? trans(Session::get('admin_lang_file').'.ADMIN_USER_NOT_FOUND') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_USER_NOT_FOUND') ;
						return redirect('send-newsletter')->with('message',$mesage);
						
					}
					return redirect('send-newsletter');
				}
				
				} else {
				return redirect('admin-login');
			}
			
		}
		
		/* NEWSLETTER SUBSCRIBER STATUS */
		public function edit_newsletter_subscriber_status($id, $status)
		{
			if(Session::has('admin_id')==1){
				
				$return = DB::table('gr_newsletter_subscription')->where('id', '=', $id)->update(array('news_status' => $status));
				
				if ($status == 0) {
					
					if(Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')!= '')
					{
						$session_message = trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS');
					}
					else
					{
						$session_message =  trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
					}
					return Redirect::to('manage-news-letter')->with('success', $session_message);
					
					} else if ($status == 1) {
					
					if(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')!= '')
					{
						$session_message = trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS');
					}
					else
					{
						$session_message =  trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
					}
					return Redirect::to('manage-news-letter')->with('success', $session_message);
				}
			}
			else
			{
				return Redirect::to('siteadmin');
			}
		}
		
		/* NEWSLETTER SUBSCRIBER DELETE */
		public function delete_newsletter_subscriber($id)
		{
			if(Session::has('admin_id')==1){
				
				DB::table('gr_newsletter_subscription')->where('id', '=', $id)->delete();
				
				if(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')!= '')
				{
					$session_message = trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS');
				}
				else
				{
					$session_message =  trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				}
				return Redirect::to('manage-news-letter')->with('success', $session_message);
				
				} else {
				return Redirect::to('siteadmin');
			}
		}
		
		
		
	}
