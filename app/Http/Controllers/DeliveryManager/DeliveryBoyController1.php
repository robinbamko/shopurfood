<?php
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\DeliveryManager;
	
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
	
	class DeliveryBoyController1 extends Controller
	{
		//
		
		public function __construct(Request $request)
		{
			parent::__construct();
		}
		
		
		public function manage_deliveryboy($id='')
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			if($id!='')
			{
				DB::table('gr_delivery_member')->update(['deliver_read_status' => 1]);
				return Redirect::to('manage-deliveryboy');
			}
			$where = [];
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_DELBOY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_MANAGE_DELBOY');
			$get_agent_details = array();//get_all_details('gr_delivery_member','deliver_status',10,'desc','deliver_id');
			return view('DeliveryManager.DeliveryPerson1.manage_deliveryboy')->with('pagetitle',$page_title)->with('all_details',$get_agent_details);
		}
		public function deliveryboy_list_ajax(Request $request)
		{
			//if(Session::has('admin_id') == 1)
			//{	
			$columns = array( 
			0 =>'deliver_id', 
			1 =>'deliver_id', 
			2 =>'deliver_fname', 
			3=> 'deliver_email',
			4=> 'deliver_phone1',
			5=> 'deliver_id',
			6=> 'deliver_status',
			7=> 'deliver_id'
			);
			/*To get Total count */
			$totalData = DB::table('gr_delivery_member')
			->select('deliver_id')
			->where('deliver_status','<>','2')
			->count();
			$totalFiltered = $totalData; 
			/*EOF get Total count */
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');
			
			//if(empty($request->input('search.value')))
			$delboyName_search = trim($request->delboyName_search); 
			$delboyEmail_search = trim($request->delboyEmail_search); 
			$delboyPhone_search = trim($request->delboyPhone_search); 
			$publish_search = trim($request->publish_search); 
			if($delboyName_search=='' && $delboyEmail_search=='' && $delboyPhone_search=='' && $publish_search=='')
			{    
				//DB::connection()->enableQueryLog();
				$posts = DB::table('gr_delivery_member')->select('gr_delivery_member.deliver_id',
				'gr_delivery_member.deliver_fname',
				'gr_delivery_member.deliver_lname',
				'gr_delivery_member.deliver_email',
				'gr_delivery_member.deliver_phone1',
				'gr_delivery_member.deliver_status'
				)
				->where('gr_delivery_member.deliver_status','<>','2')
				->orderBy($order,$dir)->skip($start)->take($limit)->get();
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
			}
			else {
				
				$sql = DB::table('gr_delivery_member')->select('gr_delivery_member.deliver_id',
				'gr_delivery_member.deliver_fname',
				'gr_delivery_member.deliver_lname',
				'gr_delivery_member.deliver_email',
				'gr_delivery_member.deliver_phone1',
				'gr_delivery_member.deliver_status'
				)
				->where('gr_delivery_member.deliver_status','<>','2');
				if($delboyName_search != '')
				{
					$q = $sql->whereRaw("CONCAT(if(gr_delivery_member.deliver_fname is null,'',gr_delivery_member.deliver_fname),' ',if(gr_delivery_member.deliver_lname is null,'',gr_delivery_member.deliver_lname)) like '%".$delboyName_search."%'"); 
				}
				if($delboyEmail_search != '')
				{
					$q = $sql->whereRaw("gr_delivery_member.deliver_email like '%".$delboyEmail_search."%'"); 
				}
				if($delboyPhone_search != '')
				{
					$q = $sql->whereRaw("gr_delivery_member.deliver_phone1 like '%".$delboyPhone_search."%'"); 
				}
				if($publish_search != '')
				{
					$q = $sql->where('gr_delivery_member.deliver_status',$publish_search); 
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
				$click_to_block = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
				$click_to_unblock = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
				$click_to_edit = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
				$click_to_delete = (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->DELMGR_OUR_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
				foreach ($posts as $post)
				{
					if($post->deliver_status == 1)
					{
						$statusLink = '<a href="'.url('deliveryboy_status1').'/'.$post->deliver_id.'/0"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
					}
					else
					{
						$statusLink = '<a href="'.url('deliveryboy_status1').'/'.$post->deliver_id.'/1"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'" ></i></a>';
					}
					
					$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="'.$post->deliver_id.'">';
					$nestedData['SNo'] = ++$snoCount;
					$nestedData['delboyName'] = $post->deliver_fname.' '.$post->deliver_lname;
					$nestedData['delboyEmail'] = $post->deliver_email;
					$nestedData['delboyPhone'] = $post->deliver_phone1;
					$nestedData['Edit'] = '<a href="'.url('edit-deliveryboy1').'/'.base64_encode($post->deliver_id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
					$nestedData['Status'] = $statusLink;
					$nestedData['delete'] = '<a href= "'.url('deliveryboy_status1').'/'.$post->deliver_id.'/2" title="'.$click_to_delete.'" class="tooltip-demo"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
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
			//}
		}
		public function add_deliveryboy()
		{
			
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ADD_DELBOY');
			$action='save-deliveryboy1';
			$array_name = array();
			foreach(DB::getSchemaBuilder()->getColumnListing('gr_delivery_member') as $res)
			{
				$array_name[$res]='';
			}
			$object = (object) $array_name; // return all value as empty.
			return view('DeliveryManager.DeliveryPerson1.add_deliveryboy')->with('pagetitle',$page_title)->with('getvendor',$object)->with('id','')->with('action',$action);
		}
		public function random_password( $length = 8 ) {
			//$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$chars = "0123456789";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		public function save_deliveryboy(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ADD_DELBOY');
			$action='save-deliveryboy1';
			$array_name = array();
			foreach(DB::getSchemaBuilder()->getColumnListing('gr_delivery_member') as $res)
			{
				$array_name[$res]='';
			}
			$object = (object) $array_name; // return all value as empty.
			//return view('Admin.merchant.add_merchant')->with('pagetitle',$page_title)->with('getvendor',$object)->with('id','')->with('action',$action);
			$this->validate($request, 
			[
			'deliver_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
			'deliver_email'  => [
			'required', 
			Rule::unique('gr_delivery_member')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			//'agent_password'=>'Required',
			'deliver_phone1'  => [
			'required', 
			Rule::unique('gr_delivery_member')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			'deliver_response_time'=>'Required',
			'deliver_base_fare'=>'Required|Numeric',
			'deliver_vehicle_details'=>'Required',
			'deliver_order_limit'=>'Required',
			],[
			'deliver_fname.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FNAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FNAME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FNAME'),
			'deliver_email.email'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_EMAIL'),
			'deliver_email.unique'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_EMAIL_UNIQUE_VAL'),
			//'agent_password.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTR_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTR_PASS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTR_PASS'),
			'deliver_phone1.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PHONE'),
			'deliver_phone1.unique'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_PHONE_UNIQUE_VAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_PHONE_UNIQUE_VAL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_PHONE_UNIQUE_VAL'),
			'deliver_response_time.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RESPONSE_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RESPONSE_TIME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_RESPONSE_TIME'),
			'deliver_base_fare.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BASE_FARE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BASE_FARE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_BASE_FARE'),
			'deliver_vehicle_details.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELVEHICLE_OPTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELVEHICLE_OPTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_SELVEHICLE_OPTION'),
			'deliver_order_limit.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTERORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTERORDER_LIMIT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTERORDER_LIMIT')			
			]); 
			if(Input::get('deliver_fare_type')=='per_km')
			{
				$this->validate($request, 
				[
				'deliver_perkm_charge'=>'Required',
				],
				[
				'deliver_perkm_charge.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FARE_KM')
				]
				); 
			}
			if(Input::get('deliver_fare_type')=='per_min')
			{
				$this->validate($request, 
				[
				'deliver_permin_charge'=>'Required',
				],
				[
				'deliver_permin_charge.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FARE_MIN')
				]
				); 
			}
			
			$passwordIs = $this->random_password(6);
			
			$profile_det = array(
			'deliver_agent_id'=>Input::get('deliver_agent_id'),
			'deliver_fname'=>Input::get('deliver_fname'),
			'deliver_lname'=>Input::get('deliver_lname'),
			'deliver_email'=>Input::get('deliver_email'),
			'deliver_password'=>md5($passwordIs),
			'deliver_decrypt_password'=>$passwordIs,
			'deliver_phone1'=>Input::get('deliver_phone1'),
			'deliver_phone2'=>Input::get('deliver_phone2'),
			'deliver_state'=>Input::get('deliver_state'),
			'deliver_location'=>Input::get('deliver_location'),
			'deliver_city'=>Input::get('deliver_city'),
			'deliver_country'=>Input::get('country'),
			'deliver_avail_status'=>'1',
			'deliver_response_time'=>date('H:i:s',strtotime(Input::get('deliver_response_time'))),
			'deliver_status'=>'1',
			'deliver_currency_code'=>Input::get('deliver_currency_code'),
			'deliver_base_fare'=>Input::get('deliver_base_fare'),
			'deliver_fare_type'=>Input::get('deliver_fare_type'),
			'deliver_perkm_charge'=>Input::get('deliver_perkm_charge'),
			'deliver_permin_charge'=>Input::get('deliver_permin_charge'),
			'deliver_vehicle_details'=>Input::get('deliver_vehicle_details'),
			'deliver_order_limit'=>Input::get('deliver_order_limit'),
			'deliver_read_status'=>'0',
			'deliver_created_at' => date('Y-m-d H:i:s'),
			'deliver_updated_at' => date('Y-m-d H:i:s'),
			'deliver_latitude'=>Input::get('deliver_latitude'),
			'deliver_longitude'=>Input::get('deliver_longitude')
			);			
			//DB::connection()->enableQueryLog();
			$res=insertvalues('gr_delivery_member',$profile_det);
			//DB::table('gr_merchant')->save($profile_det);
			//$query = DB::getQueryLog();
			//print_r($query);
			//exit;
			if($res)
			{
				//----MAIL FUNCTION 
				$send_mail_data = array('name' => Input::get('deliver_fname').' '.Input::get('deliver_lname'),
				'password' => $passwordIs,
				'email' => Input::get('deliver_email'),
				);
				Mail::send('email.delboy_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('deliver_email');
					$name                = Input::get('deliver_fname').' '.Input::get('deliver_lname');
					$subject = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_REG_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_REG_DETAILS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
				// EOF MAIL FUNCTION *
				
				$message = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_INSERT_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_INSERT_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_INSERT_SUCCESS');
				return redirect('manage-deliveryboy1')->with('message',$message);
			}
			else
			{
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SUMTHNG_WRONG')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SUMTHNG_WRONG') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_SUMTHNG_WRONG');
				return Redirect::to('add-deliveryboy1')->withErrors(['errors'=> $msg])->withInput();
			}
			
		}
		public function edit_deliveryboy($id)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$id = base64_decode($id);
			$where = ['deliver_id' => $id];
			$get_merchants_details = get_details('gr_delivery_member',$where);
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EDIT_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EDIT_DELBOY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_EDIT_DELBOY');
			$action='update-deliveryboy1';
			return view('DeliveryManager.DeliveryPerson1.add_deliveryboy')->with('pagetitle',$page_title)->with('getvendor',$get_merchants_details)->with('id',$id)->with('action',$action);
		}
		
		public function update_deliveryboy(Request $request)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$id = Input::get('gotId');
			$where = ['deliver_id' => $id];
			$get_merchants_details = get_details('gr_delivery_member',$where);
			$page_title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EDIT_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EDIT_DELBOY') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_EDIT_DELBOY');
			$action='update-deliveryboy1';
			$this->validate($request, 
			[
			'deliver_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
			'deliver_email'  => [
			'required', 
			Rule::unique('gr_delivery_member')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_id', '<>', Input::get('gotId'))->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			'deliver_password'=>'Required',
			'deliver_phone1'  => [
			'required', 
			Rule::unique('gr_delivery_member')->where(function ($query) use ($request) {
				return $query->where('gr_delivery_member.deliver_id', '<>', Input::get('gotId'))->where('gr_delivery_member.deliver_status','<>','2');
			}),
			],
			'deliver_response_time'=>'Required',
			'deliver_base_fare'=>'Required|Numeric',
			'deliver_vehicle_details'=>'Required',
			'deliver_order_limit'=>'Required',
			],[
			'deliver_fname.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FNAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FNAME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FNAME'),
			'deliver_email.email'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_EMAIL'),
			'deliver_email.unique'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_EMAIL_UNIQUE_VAL'),
			'deliver_password.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTR_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTR_PASS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTR_PASS'),
			'deliver_phone1.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PHONE'),
			'deliver_phone1.unique'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_PHONE_UNIQUE_VAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_PHONE_UNIQUE_VAL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_PHONE_UNIQUE_VAL'),
			'deliver_response_time.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RESPONSE_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RESPONSE_TIME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_RESPONSE_TIME'),
			'deliver_base_fare.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BASE_FARE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BASE_FARE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_BASE_FARE'),
			'deliver_vehicle_details.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELVEHICLE_OPTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELVEHICLE_OPTION') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_SELVEHICLE_OPTION'),
			'deliver_order_limit.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTERORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTERORDER_LIMIT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTERORDER_LIMIT')
			
			]); 
			if(Input::get('deliver_fare_type')=='per_km')
			{
				$this->validate($request, 
				[
				'deliver_perkm_charge'=>'Required',
				],
				[
				'deliver_perkm_charge.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FARE_KM')
				]
				); 
			}
			if(Input::get('deliver_fare_type')=='per_min')
			{
				$this->validate($request, 
				[
				'deliver_permin_charge'=>'Required',
				],
				[
				'deliver_permin_charge.required'    => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FARE_MIN')
				]
				); 
			}
			$passwordIs = Input::get('deliver_password');
			$old_password = Input::get('old_password');
			
			$lat = Input::get('deliver_latitude');
			$long = Input::get('deliver_longitude');
			$old_lat = Input::get('old_latitude');
			$old_long = Input::get('old_longitude');
			$profile_det = array(
			'deliver_agent_id'=>Input::get('deliver_agent_id'),
			'deliver_fname'=>Input::get('deliver_fname'),
			'deliver_lname'=>Input::get('deliver_lname'),
			'deliver_email'=>Input::get('deliver_email'),
			'deliver_password'=>md5($passwordIs),
			'deliver_decrypt_password'=>$passwordIs,
			'deliver_phone1'=>Input::get('deliver_phone1'),
			'deliver_phone2'=>Input::get('deliver_phone2'),
			'deliver_state'=>Input::get('deliver_state'),
			'deliver_location'=>Input::get('deliver_location'),
			'deliver_city'=>Input::get('deliver_city'),
			'deliver_country'=>Input::get('country'),
			'deliver_response_time'=>date('H:i:s',strtotime(Input::get('deliver_response_time'))),
			'deliver_currency_code'=>Input::get('deliver_currency_code'),
			'deliver_base_fare'=>Input::get('deliver_base_fare'),
			'deliver_fare_type'=>Input::get('deliver_fare_type'),
			'deliver_perkm_charge'=>Input::get('deliver_perkm_charge'),
			'deliver_permin_charge'=>Input::get('deliver_permin_charge'),
			'deliver_vehicle_details'=>Input::get('deliver_vehicle_details'),
			'deliver_order_limit'=>Input::get('deliver_order_limit'),
			'deliver_updated_at' => date('Y-m-d H:i:s'),
			'deliver_latitude'=> ($lat !='') ? $lat : $old_lat,
			'deliver_longitude'=>($long != '') ? $long : $old_long
			);			
			if((Input::get('old_email')!=Input::get('deliver_email')) || ($old_password != $passwordIs))
			{
				
				$a2=array("deliver_password"=>md5($passwordIs),'deliver_decrypt_password' => $passwordIs);
				$update_det=array_merge($profile_det,$a2);
				
				/*MAIL FUNCTION */
				$send_mail_data = array('name' => Input::get('deliver_fname').' '.Input::get('deliver_lname'),
				'password' => $passwordIs,
				'email' => Input::get('deliver_email'),
				);
				Mail::send('email.delboy_register_email', $send_mail_data, function($message)
				{
					$email               = Input::get('deliver_email');
					$name                = Input::get('deliver_fname').' '.Input::get('deliver_lname');
					$subject = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_REG_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_REG_DETAILS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_REG_DETAILS');
					$message->to($email, $name)->subject($subject);
				});
			}
			else
			{
				$update_det = $profile_det;
			}
			//print_r($update_det); echo  '<hr>'.$id.'/'.Input::get('gotId'); exit;
			$update = updatevalues('gr_delivery_member',$update_det,['deliver_id' =>$id]);
			
			$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE_SUCCESS');
			Session::flash('message',$msg);
			return Redirect::to('manage-deliveryboy1');
		}
		
		public function change_deliveryboy_status($id,$status)
		{	
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			//echo $status; echo $id; exit;
			$update = ['deliver_status' => $status];
			$where = ['deliver_id' => $id];
			$a = updatevalues('gr_delivery_member',$update,$where);
			//echo $a; exit;
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-deliveryboy1')->withErrors(['success'=>$msg])->withInput();
			}
			if($status == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELETE_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELETE_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-deliveryboy1')->withErrors(['success'=>$msg])->withInput();
			}
			else   //block
			{	
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-deliveryboy1')->withErrors(['success'=>$msg])->withInput();
			}
		}
		
		/** multiple block/unblock  Customer**/
		public function multi_deliveryboy_block()
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$update = ['deliver_status' => Input::get('status')];
			$val = Input::get('val');
			
			//return count($val); exit;
			for($i=0; $i< count($val); $i++)
			{
				$where = ['deliver_id' => $val[$i]];
				$a = updatevalues('gr_delivery_member',$update,$where);
			}
			//echo Input::get('status'); exit;
			if(Input::get('status') == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UNBLOCK_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				
			}
			if(Input::get('status') == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELETE_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELETE_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_DELETE_SUCCESS');
				Session::flash('message',$msg);
				
			}
			elseif(Input::get('status') == 0)   //block
			{	
				
				$msg = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK_SUCCESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BLOCK_SUCCESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				
			}
		}
		
		public function export_deliveryboy($type)
		{
			$this->DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
			$data = DB::table('gr_delivery_member')->select('*')->where('deliver_status','=','1')->get()->toarray();
			
			return Excel::create('Delivery boy lists',function ($excel) use ($data)
    		{
    			$excel->sheet('Deliveryboy_list', function ($sheet) use ($data)
    			{   
    				$sheet->row(1, ['Col 1', 'Col 2', 'Col 3']); // etc etc
					$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });	 
                    $sheet->cell('A1', function($cell) {
					$cell->setValue((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_SNO'));  });   
                    $sheet->cell('B1', function($cell) {
                    	$cell->setValue((Lang::has(Session::get('DelMgr_lang_file').'.CUS_REG_NAME')) ? trans(Session::get('DelMgr_lang_file').'.CUS_REG_NAME') : trans($this->DELMGR_OUR_LANGUAGE.'.CUS_REG_NAME'));
					});
                    $sheet->cell('C1', function($cell) {
                    	$cell->setValue((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL'));
					});
                    $sheet->cell('D1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ADDRESS');
						$cell->setValue($title);
					}); 
                    $sheet->cell('E1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_MOBILENO'); 
						$cell->setValue($title);
					}); 
                    $sheet->cell('F1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ALTER_MOBILE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ALTER_MOBILE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_ALTER_MOBILE');
						$cell->setValue($title);
					});
                    $sheet->cell('G1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_RESPONSE_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_RESPONSE_TIME') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_RESPONSE_TIME');
						$cell->setValue($title);
					});
                    $sheet->cell('H1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BASE_FARE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BASE_FARE') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_BASE_FARE');
						$cell->setValue($title);
					});
                    $sheet->cell('I1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM');
						$cell->setValue($title);
					});
                    $sheet->cell('J1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN');
						$cell->setValue($title);
					});
                    $sheet->cell('K1', function($cell) {
						$title = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CREATED_AT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CREATED_AT') : trans($this->DELMGR_OUR_LANGUAGE.'.DELMGR_CREATED_AT');
						$cell->setValue($title);
					});  
    				$sheet->setFontFamily('Comic Sans MS');
    				
					//$sheet->row(1, function($row) { $row->setBackground('#000000'); });
					$j=2;
					$k=1;
					foreach ($data as $key => $value) {
						$i= $key+2;
						$sheet->cell('A'.$j, $k); //print serial no
						$sheet->cell('B'.$j, $value->deliver_fname.' '.$value->deliver_lname);
						$sheet->cell('C'.$j, $value->deliver_email); 
						$sheet->cell('D'.$j, $value->deliver_location); 
						$sheet->cell('E'.$j, $value->deliver_phone1);
						$sheet->cell('F'.$j, $value->deliver_phone2); 
						$sheet->cell('G'.$j, $value->deliver_response_time); 
						$sheet->cell('H'.$j, $value->deliver_currency_code.' '.$value->deliver_base_fare); 
						$sheet->cell('I'.$j, $value->deliver_currency_code.' '.$value->deliver_perkm_charge); 
						$sheet->cell('J'.$j, $value->deliver_currency_code.' '.$value->deliver_permin_charge); 
						$sheet->cell('K'.$j, $value->deliver_created_at); 
						$j++;
						$k++;
					}
				});
			})->download($type);
			
		}
		
	}
