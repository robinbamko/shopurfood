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
	
	use App\Admin;
	
	use Image;
	
	use Excel;
	
	class AgentController extends Controller
	{
		//
		
		public function __construct()
		{
			parent::__construct();
			$this->setAdminLanguage();
			if(Session::has('admin_id') == 1)
			{
			}
			else
			{
				return Redirect::to('admin-login');
			}
			//$this->setLanguageLocalDeliveryManager();
			//if (Session::has('DelMgrSessId')) { } else { return Redirect::to('delivery-manager-login'); } 
		}
		
		
        public function manage_agent($id='')
        {
			
            if($id!='')
            {
                DB::table('gr_agent')->update(['agent_read_status' => 1]);
                return Redirect::to('manage-agent-admin');
			}
            $where = [];
            $page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_AGENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_AGENT');
            $get_agent_details = array();//get_all_details('gr_agent','agent_status',10,'desc','agent_id');
            return view('Admin.Agents.manage_agent')->with('pagetitle',$page_title)->with('all_details',$get_agent_details);
		}
		public function agent_list_ajax(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array( 
				0 =>'agent_id', 
				1 =>'agent_id', 
				2 =>'agent_fname', 
				3=> 'agent_email',
				4=> 'agent_phone1',
				5=> 'agent_id',
				6=> 'agent_status',
				7=> 'agent_id'
				);
				/*To get Total count */
				$totalData = DB::table('gr_agent')
				->select('agent_id')
				->where('agent_status','<>','2')
				->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$agentName_search = trim($request->agentName_search); 
				$agentEmail_search = trim($request->agentEmail_search); 
				$agentPhone_search = trim($request->agentPhone_search); 
				$publish_search = trim($request->publish_search); 
				if($agentName_search=='' && $agentEmail_search=='' && $agentPhone_search=='' && $publish_search=='')
				{    
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_agent')->select('agent_id',
					'agent_fname',
					'agent_lname',
					'agent_email',
					'agent_phone1',
					'agent_status'
					)
					->where('agent_status','<>','2')
					->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					
					$sql = DB::table('gr_agent')
					->select('agent_id',
					'agent_fname',
					'agent_lname',
					'agent_email',
					'agent_phone1',
					'agent_status'
					)
					->where('agent_status','<>','2');
					if($agentName_search != '')
					{
						$q = $sql->whereRaw("agent_fname like '%".$agentName_search."%'"); 
					}
					if($agentEmail_search != '')
					{
						$q = $sql->whereRaw("agent_email like '%".$agentEmail_search."%'"); 
					}
					if($agentPhone_search != '')
					{
						$q = $sql->whereRaw("agent_phone1 like '%".$agentPhone_search."%'"); 
					}
					if($publish_search != '')
					{
						$q = $sql->where('agent_status',$publish_search); 
					}
					$totalFiltered = $q->count();
					//DB::connection()->enableQueryLog();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					
					
				}
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$click_to_block = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
					$click_to_unblock = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
					$click_to_edit = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
					$click_to_delete = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
					foreach ($posts as $post)
					{
						if($post->agent_status == 1)
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->agent_id.'\',0);" id="statusLink_'.$post->agent_id.'"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
						}
						else
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->agent_id.'\',1);" id="statusLink_'.$post->agent_id.'"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'" ></i></a>';
						}
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="'.$post->agent_id.'">';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['delboyName'] = $post->agent_fname.' '.$post->agent_lname;
						$nestedData['delboyEmail'] = $post->agent_email;
						$nestedData['delboyPhone'] = $post->agent_phone1;
						$nestedData['Edit'] = '<a href="'.url('edit-agent-admin').'/'.base64_encode($post->agent_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
						$nestedData['Status'] = $statusLink;
						$nestedData['delete'] = '<a href= "javascript:individual_change_status(\''.$post->agent_id.'\',2);" title="'.$click_to_delete.'" class="tooltip-demo" id="statusLink_'.$post->agent_id.'"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
						$data[] = $nestedData;
						
					}
				}
				
				$json_data = array(
				"draw"            => intval($request->input('draw')),  
				"recordsTotal"    => intval($totalData),  
				"recordsFiltered" => intval($totalFiltered), 
				"data"            => $data   
				);
				
				echo json_encode($json_data); 
			}
		}
		public function add_agent()
		{
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_AGENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_AGENT');
			$action='save-agent-admin';
			$array_name = array();
			foreach(DB::getSchemaBuilder()->getColumnListing('gr_agent') as $res)
			{
				$array_name[$res]='';
			}
			$object = (object) $array_name; // return all value as empty.
			return view('Admin.Agents.add_agent')->with('pagetitle',$page_title)->with('getvendor',$object)->with('id','')->with('action',$action);
		}
		public function random_password( $length = 8 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		public function save_agent(Request $request)
		{
			
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_AGENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_AGENT');
			$action='save-agent-admin';
			$array_name = array();
			foreach(DB::getSchemaBuilder()->getColumnListing('gr_agent') as $res)
			{
				$array_name[$res]='';
			}
			$object = (object) $array_name; // return all value as empty.
			$this->validate($request, 
			[
			'agent_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
			'agent_email'  => [
			'required', 
			Rule::unique('gr_agent')->where(function ($query) use ($request) {
				return $query->where('gr_agent.agent_status','<>','2');
			}),
			],
			//'agent_password'=>'Required',
			'agent_phone1'  => [
			'only_cnty_code', 
			Rule::unique('gr_agent')->where(function ($query) use ($request) {
				return $query->where('gr_agent.agent_status','<>','2');
			}),
			],
			'agent_response_time'=>'Required',
			'agent_base_fare'=>'Required|Numeric'
			],['agent_fname.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME'),
			'agent_email.email'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL'),
			'agent_email.unique'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL'),
			'agent_phone1.only_cnty_code'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EN_VALID_PH'),
			'agent_phone1.unique'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE_UNIQUE_VAL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PHONE_UNIQUE_VAL'),
			'agent_response_time.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_RESPONSE_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_RESPONSE_TIME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_RESPONSE_TIME'),
			'agent_base_fare.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BASE_FARE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BASE_FARE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BASE_FARE')							
			]); 
			if(Input::get('agent_fare_type')=='per_km')
			{
				$this->validate($request, 
				[
				'agent_perkm_charge'=>'Required',
				],
				[
				'agent_perkm_charge.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_KM')
				]
				); 
			}
			if(Input::get('agent_fare_type')=='per_min')
			{
				$this->validate($request, 
				[
				'agent_permin_charge'=>'Required',
				],
				[
				'agent_permin_charge.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_MIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_MIN') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_MIN')
				]
				); 
			}
			if(Input::get('mer_paynamics_status')=='Publish')
			{
				$this->validate($request, 
				[
				'mer_paynamics_clientid'=>'Required',
				'mer_paynamics_secretid'=>'Required'
				],
				[
				'mer_paynamics_clientid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT') , 
				'mer_paynamics_secretid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')
				]
				); 
			}
			if(Input::get('mer_paymaya_status')=='Publish')
			{
				$this->validate($request, 
				[
				'mer_paymaya_clientid'=>'Required',
				'mer_paymaya_secretid'=>'Required'
				],
				[ 
				'mer_paymaya_secretid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET'),
				'mer_paymaya_clientid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')
				]
				); 
			}
			if(Input::get('mer_netbank_status')=='Publish')
			{
				$this->validate($request, 
				[
				'mer_bank_name'=>'Required',
				'mer_branch'=>'Required',
				'mer_bank_accno'=>'Required',
				'mer_ifsc'=>'Required'
				],
				[ 
				'mer_bank_name.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BANK') ,
				'mer_branch.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BRANCH'),
				'mer_bank_accno.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ACCNO'),
				'mer_ifsc.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_IFSC') 
				]
				); 
			}
			
			$passwordIs = $this->random_password(6);
			$profile_det = array(
			'agent_fname'=>Input::get('agent_fname'),
			'agent_lname'=>Input::get('agent_lname'),
			'agent_email'=>Input::get('agent_email'),
			'agent_password'=>md5($passwordIs),
			'agent_phone1'=>Input::get('agent_phone1'),
			'agent_phone2'=>Input::get('agent_phone2'),
			'agent_state'=>Input::get('agent_state'),
			'agent_location'=>Input::get('agent_location'),
			'agent_city'=>Input::get('agent_city'),
			'agent_country'=>Input::get('country'),
			'agent_avail_status'=>'1',
			'agent_response_time'=>date('H:i:s',strtotime(Input::get('agent_response_time'))),
			'agent_status'=>'1',
			'agent_currency_code'=>Input::get('agent_currency_code'),
			'agent_base_fare'=>Input::get('agent_base_fare'),
			'agent_fare_type'=>Input::get('agent_fare_type'),
			'agent_perkm_charge'=>Input::get('agent_perkm_charge'),
			'agent_permin_charge'=>Input::get('agent_permin_charge'),
			'mer_paynamics_status'=>Input::get('mer_paynamics_status'),
			'mer_paynamics_clientid'=>Input::get('mer_paynamics_clientid'),
			'mer_paynamics_secretid'=>Input::get('mer_paynamics_secretid'),
			'mer_paynamics_mode'=>Input::get('mer_paynamics_mode'),
			'mer_paymaya_status'=>Input::get('mer_paymaya_status'),
			'mer_paymaya_clientid'=>Input::get('mer_paymaya_clientid'),
			'mer_paymaya_secretid'=>Input::get('mer_paymaya_secretid'),
			'mer_paymaya_mode'=>Input::get('mer_paymaya_mode'),
			'mer_netbank_status'=>Input::get('mer_netbank_status'),
			'mer_bank_name'=>Input::get('mer_bank_name'),
			'mer_branch'=>Input::get('mer_branch'),
			'mer_bank_accno'=>Input::get('mer_bank_accno'),
			'mer_ifsc'=>Input::get('mer_ifsc'),
			'agent_created_at' => date('Y-m-d H:i:s'),
			'agent_updated_at' => date('Y-m-d H:i:s')
			);			
			//DB::connection()->enableQueryLog();
			$res=insertvalues('gr_agent',$profile_det);
			//DB::table('gr_merchant')->save($profile_det);
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			if($res)
			{
				//----MAIL FUNCTION 
				$send_mail_data = array('name' => Input::get('agent_fname').' '.Input::get('agent_lname'),
				'password' => $passwordIs,
				'email' => Input::get('agent_email'),
				);
				Mail::send('email.admin_agent_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('agent_email');
					$name                = Input::get('agent_fname').' '.Input::get('agent_lname');
					$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
				// EOF MAIL FUNCTION *
				
				$message = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				return redirect('manage-agent-admin')->with('message',$message);
			}
			else
			{
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SUMTHNG_WRONG');
				return Redirect::to('add-agent-admin')->withErrors(['errors'=> $msg])->withInput();
			}
			
		}
		public function edit_agent($id)
		{
			
			$id = base64_decode($id);
			$where = ['agent_id' => $id];
			$get_merchants_details = get_details('gr_agent',$where);
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_AGENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT_AGENT');
			$action='update-agent-admin';
			return view('Admin.Agents.add_agent')->with('pagetitle',$page_title)->with('getvendor',$get_merchants_details)->with('id',$id)->with('action',$action);;
		}
		
		public function update_agent(Request $request)
		{
			
			$id = Input::get('gotId');
			$where = ['agent_id' => $id];
			$get_merchants_details = get_details('gr_agent',$where);
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_AGENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT_AGENT');
			$action='update-agent-admin';
			$this->validate($request, 
			[
			'agent_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
			'agent_email'  => [
			'required', 
			Rule::unique('gr_agent')->where(function ($query) use ($request) {
				return $query->where('gr_agent.agent_id', '<>', Input::get('gotId'))->where('gr_agent.agent_status','<>','2');
			}),
			],
			//'agent_password'=>'Required',
			'agent_phone1'  => [
			'only_cnty_code', 
			Rule::unique('gr_agent')->where(function ($query) use ($request) {
				return $query->where('gr_agent.agent_id', '<>', Input::get('gotId'))->where('gr_agent.agent_status','<>','2');
			}),
			],
			'agent_response_time'=>'Required',
			'agent_base_fare'=>'Required|Numeric',
			],['agent_fname.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME'),
			'agent_email.email'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL'),
			'agent_email.unique'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL'),
			//'agent_password.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_PASS'),
			'agent_phone1.only_cnty_code'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EN_VALID_PH'),
			'agent_phone1.unique'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE_UNIQUE_VAL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PHONE_UNIQUE_VAL'),
			'agent_response_time.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_RESPONSE_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_RESPONSE_TIME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_RESPONSE_TIME'),
			'agent_base_fare.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BASE_FARE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BASE_FARE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BASE_FARE'),
			
			]); 
			if(Input::get('agent_fare_type')=='per_km')
			{
				$this->validate($request, 
				[
				'agent_perkm_charge'=>'Required',
				],
				[
				'agent_perkm_charge.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_KM')
				]
				); 
			}
			if(Input::get('agent_fare_type')=='per_min')
			{
				$this->validate($request, 
				[
				'agent_permin_charge'=>'Required',
				],
				[
				'agent_permin_charge.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_MIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_MIN') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_MIN')
				]
				); 
			}
			if(Input::get('mer_paynamics_status')=='Publish')
			{
				$this->validate($request, 
				[
				'mer_paynamics_clientid'=>'Required',
				'mer_paynamics_secretid'=>'Required'
				],
				[
				'mer_paynamics_clientid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT') , 
				'mer_paynamics_secretid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')
				]
				); 
			}
			if(Input::get('mer_paymaya_status')=='Publish')
			{
				$this->validate($request, 
				[
				'mer_paymaya_clientid'=>'Required',
				'mer_paymaya_secretid'=>'Required'
				],
				[ 
				'mer_paymaya_secretid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET'),
				'mer_paymaya_clientid.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')
				]
				); 
			}
			if(Input::get('mer_netbank_status')=='Publish')
			{
				$this->validate($request, 
				[
				'mer_bank_name'=>'Required',
				'mer_branch'=>'Required',
				'mer_bank_accno'=>'Required',
				'mer_ifsc'=>'Required'
				],
				[ 
				'mer_bank_name.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BANK') ,
				'mer_branch.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BRANCH'),
				'mer_bank_accno.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ACCNO'),
				'mer_ifsc.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_IFSC') 
				]
				); 
			}
			
			
			$profile_det = array(
			'agent_fname'=>Input::get('agent_fname'),
			'agent_lname'=>Input::get('agent_lname'),
			'agent_email'=>Input::get('agent_email'),
			'agent_phone1'=>Input::get('agent_phone1'),
			'agent_phone2'=>Input::get('agent_phone2'),
			'agent_state'=>Input::get('agent_state'),
			'agent_location'=>Input::get('agent_location'),
			'agent_city'=>Input::get('agent_city'),
			'agent_country'=>Input::get('country'),
			'agent_response_time'=>date('H:i:s',strtotime(Input::get('agent_response_time'))),
			'agent_currency_code'=>Input::get('agent_currency_code'),
			'agent_base_fare'=>Input::get('agent_base_fare'),
			'agent_fare_type'=>Input::get('agent_fare_type'),
			'agent_perkm_charge'=>Input::get('agent_perkm_charge'),
			'agent_permin_charge'=>Input::get('agent_permin_charge'),
			'mer_paynamics_status'=>Input::get('mer_paynamics_status'),
			'mer_paynamics_clientid'=>Input::get('mer_paynamics_clientid'),
			'mer_paynamics_secretid'=>Input::get('mer_paynamics_secretid'),
			'mer_paynamics_mode'=>Input::get('mer_paynamics_mode'),
			'mer_paymaya_status'=>Input::get('mer_paymaya_status'),
			'mer_paymaya_clientid'=>Input::get('mer_paymaya_clientid'),
			'mer_paymaya_secretid'=>Input::get('mer_paymaya_secretid'),
			'mer_paymaya_mode'=>Input::get('mer_paymaya_mode'),
			'mer_netbank_status'=>Input::get('mer_netbank_status'),
			'mer_bank_name'=>Input::get('mer_bank_name'),
			'mer_branch'=>Input::get('mer_branch'),
			'mer_bank_accno'=>Input::get('mer_bank_accno'),
			'mer_ifsc'=>Input::get('mer_ifsc'),
			'agent_updated_at' => date('Y-m-d H:i:s')
			);			
			if(Input::get('old_email')!=Input::get('agent_email'))
			{
				$passwordIs = $this->random_password(6);
				$a2=array("agent_password"=>md5($passwordIs));
				$update_det=array_merge($profile_det,$a2);
				
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => Input::get('agent_fname').' '.Input::get('agent_lname'),
				'password' => $passwordIs,
				'email' => Input::get('agent_email'),
				);
				Mail::send('email.admin_agent_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('agent_email');
					$name                = Input::get('agent_fname').' '.Input::get('agent_lname');
					$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
			}
			else
			{
				$update_det = $profile_det;
			}
			//print_r($update_det); echo  '<hr>'.$id.'/'.Input::get('gotId'); exit;
			$update = updatevalues('gr_agent',$update_det,['agent_id' =>$id]);
			
			$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('manage-agent-admin');
		}
		
		/*public function change_agent_status($id,$status)
			{	
			
			//echo $status; echo $id; exit;
			$update = ['agent_status' => $status];
			$where = ['agent_id' => $id];
			$a = updatevalues('gr_agent',$update,$where);
			//echo $a; exit;
			if($status == 1) //Active
			{	
    		
    		$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
    		Session::flash('message',$msg);
    		return Redirect::to('manage-agent-admin')->withErrors(['success'=>$msg])->withInput();
			}
			if($status == 2) //Delete
			{	
    		
    		$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
    		Session::flash('message',$msg);
    		return Redirect::to('manage-agent-admin')->withErrors(['success'=>$msg])->withInput();
			}
			else   //block
			{	
    		
    		$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
    		Session::flash('message',$msg);
    		return Redirect::to('manage-agent-admin')->withErrors(['success'=>$msg])->withInput();
			}
			}
		*/
		/** multiple block/unblock  Customer**/
		public function multi_agent_block()
		{
			
			$update = ['agent_status' => Input::get('status')];
			$val = Input::get('val');
			
			//return count($val); exit;
			for($i=0; $i< count($val); $i++)
			{
				$where = ['agent_id' => $val[$i]];
				$a = updatevalues('gr_agent',$update,$where);
			}
			//echo Input::get('status'); exit;
			if(Input::get('status') == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
			if(Input::get('status') == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
			elseif(Input::get('status') == 0)   //block
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				//Session::flash('message',$msg);
				echo $msg;
				
			}
		}
		
		public function export_agent($type)
		{
			
			$data = DB::table('gr_agent')->select('*')->where('agent_status','=','1')->get()->toarray();
			
			return Excel::create('Agent lists',function ($excel) use ($data)
    		{
    			$excel->sheet('Agent_list', function ($sheet) use ($data)
    			{   
    				$sheet->row(1, ['Col 1', 'Col 2', 'Col 3']); // etc etc
					$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });	 
                    $sheet->cell('A1', function($cell) {$cell->setValue('S.No');  });   
                    $sheet->cell('B1', function($cell) {$cell->setValue('Agent Name');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Agent Email');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Agent Address');}); 
                    $sheet->cell('E1', function($cell) {$cell->setValue('Mobile Number');}); 
                    $sheet->cell('F1', function($cell) {$cell->setValue('Alternate Mobile Number');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Response Time');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Base Fare');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Per Km');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Per Minute');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Created At');});   
    				$sheet->setFontFamily('Comic Sans MS');
    				
					//$sheet->row(1, function($row) { $row->setBackground('#000000'); });
					$j=2;
					$k=1;
					foreach ($data as $key => $value) {
						$i= $key+2;
						$sheet->cell('A'.$j, $k); //print serial no
						$sheet->cell('B'.$j, $value->agent_fname.' '.$value->agent_lname);
						$sheet->cell('C'.$j, $value->agent_email); 
						$sheet->cell('D'.$j, $value->agent_location); 
						$sheet->cell('E'.$j, $value->agent_phone1);
						$sheet->cell('F'.$j, $value->agent_phone2); 
						$sheet->cell('G'.$j, $value->agent_response_time); 
						$sheet->cell('H'.$j, $value->agent_currency_code.' '.$value->agent_base_fare); 
						$sheet->cell('I'.$j, $value->agent_currency_code.' '.$value->agent_perkm_charge); 
						$sheet->cell('J'.$j, $value->agent_currency_code.' '.$value->agent_permin_charge); 
						$sheet->cell('K'.$j, $value->agent_created_at); 
						$j++;
						$k++;
					}
				});
			})->download($type);
			
		}
		
	}
