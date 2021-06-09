<?php 
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Merchant;
	
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
	
	use App\Merchant;
	
	use Excel;
	
	use Response;
	use File;
	
	class ChoiceController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get Merchant language
			$this->setLanguageLocaleMerchant();
			
		}
		
		/** manage store category **/
		public function choice_management(Request $request)
		{ 
			if(Session::has('merchantid') == 1)
			{         
				//$get_category_details = get_all_details('gr_choices','ch_status',10,'desc','ch_id','');
				if ($request->ajax()) {
					$columns = array(
					0 => 'ch_id',
					1 => 'ch_name',
					2 => 'ch_id',
					3 => 'ch_status',
					4 => 'ch_added_by'
					);
					$totalData = get_all_details('gr_choices','ch_status','','desc','ch_id','');
					
					$totalData = count($totalData);
					$limit = $request->input('length');
					$start = $request->input('start');
					$order = $columns[$request->input('order.0.column')];
					$dir = $request->input('order.0.dir');
					
					$choiceNamesearch = trim($request->choiceNamesearch);
					$addedBySearch = trim($request->addedBy_search);
					$status_search = trim($request->status_search);
					
					$q = array();
					$sql = DB::table('gr_choices')->where('ch_status', '<>', '2');
					if ($choiceNamesearch != '') {
						$q = $sql->where('ch_name', 'LIKE', '%' . $choiceNamesearch . '%');
					}
					
					if ($addedBySearch != '') {
						if($addedBySearch=='0'){
							$q = $sql->where('ch_added_by','=',$addedBySearch);
							}else{
							$q = $sql->where('ch_added_by','!=','0');
						}
					}
					if ($status_search != '') {
						$q = $sql->where('ch_status','=',$status_search);
					}
					$totalFiltered = $sql->count();
					$q = $sql->orderBy($order, $dir)->skip($start)->take($limit);
					$posts = $q->get();
					
					$data = array();
					if (!empty($posts)) {
						$snoCount = $start;
						foreach ($posts as $post) {
							if($post->ch_status == 1)
							{  
								$toolTipText = (Lang::has(Session::get('mer_lang_file') . '.MER_PUBLISH')) ? trans(Session::get('mer_lang_file') . '.MER_PUBLISH') : trans($this->MER_OUR_LANGUAGE . '.MER_PUBLISH');
								$blockRunblock = '<a href="javascript:javascript;" id="statusLink_'.$post->ch_id.'" class="label label-success tooltip-demo" title="'.$toolTipText.'"><i class="fa fa-check"></i></a><br>';
							}
							else
							{
								$toolTipText = (Lang::has(Session::get('mer_lang_file') . '.MER_UNPUBLISH')) ? trans(Session::get('mer_lang_file') . '.MER_UNPUBLISH') : trans($this->MER_OUR_LANGUAGE . '.MER_UNPUBLISH');
								$blockRunblock = '<a href="javascript:javascript;" id="statusLink_'.$post->ch_id.'" class="label label-warning tooltip-demo" title="'.$toolTipText.'"><i class="fa fa-ban"></i></a><br>';
							}
							if($post->ch_added_by == Session::get('merchantid')) {
								$edit_tooltip = (Lang::has(Session::get('mer_lang_file') . '.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('mer_lang_file') . '.ADMIN_CLICK_TO_EDIT') : trans($this->MER_OUR_LANGUAGE . '.ADMIN_CLICK_TO_EDIT');
								$edit_link = "<a href='".url("mer-edit-choice").'/'.base64_encode($post->ch_id)."' class='label label-primary'><i class='fa fa-pencil tooltip-demo' aria-hidden='true' title='".$edit_tooltip."'></i></a>";
								}else{
								$edit_link = '';
							}
							if ($post->ch_added_by == 0) {
								$added_by = (Lang::has(Session::get('mer_lang_file') . '.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file') . '.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE . '.ADMIN_ADMIN');
								} else {
								$added_by = (Lang::has(Session::get('mer_lang_file') . '.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file') . '.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE . '.ADMIN_MERCHANT');
							}
							
							$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="chk[]" value="' . $post->ch_id . '">';
							$nestedData['SNo'] = ++$snoCount;
							$nestedData['choiceName'] = $post->ch_name;
							$nestedData['Edit'] = $edit_link;
							$nestedData['Status'] = $blockRunblock;
							$nestedData['AddedBy'] = $added_by;//$added_by;
							$data[] = $nestedData;
						}
					}
					$json_data = array(
					"draw" => intval($request->input('draw')),
					"recordsTotal" => intval($totalData),
					"recordsFiltered" => intval($totalFiltered),
					"data" => $data
					);
					return $json_data;
				}
				$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_CHOICES')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_CHOICES') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_CHOICES');
				//$id = ''; 
				$sid='';
				return view('sitemerchant.choices.manage_choice')->with('pagetitle',$page_title)->with('sid','');
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		/** add store category **/
		public function add_choice(Request $request)
		{ 
			if(Session::has('merchantid') == 1)
			{	
				$name = 'ch_name';
				$old_name = mysql_escape_special_chars(Input::get('ch_name'));
				$this->validate($request,['ch_name' => 'required' ], [ 'ch_name.required'    => (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CHOICE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CHOICE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ENTR_CHOICE_NAME') ] );
				$where = [$name => $old_name];	    						
				$check = check_name_exists('gr_choices','ch_status',$where);
				if(count($check) > 0)
				{	
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME_EXISTS');
					return Redirect::to('mer-manage-choices')->withErrors(['errors' =>$msg])->with('sid','Yes');
				}
				$entry['ch_name'] = ucfirst($old_name);
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						
                        $validatoradv = Validator::make($request->all(), [
						'ch_name_'.$Lang->lang_code => 'required'
                        ]);
                        if($validatoradv->fails()){
                            return redirect('mer-manage-choices')->withErrors($validatoradv)->withInput();
							}else {
                            $entry['ch_name_'.$Lang->lang_code] = Input::get('ch_name_'.$Lang->lang_code);
						}
						
					}
				}
				$entry = array_merge(array('ch_status' => 2,'ch_added_by'=>Session::get('merchantid')),$entry); //2 for store
				$insert = insertvalues('gr_choices',$entry);
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer-manage-choices');
				
			}
			else
			{
				return Redirect::to('merchant-login');
			}
		}
		
		/** edit store category **/
		public function edit_choice($id)
		{	
			$id = base64_decode($id);
			$where = ['ch_id' => $id];
			$get_cate_details = get_details('gr_choices',$where);
			$page_title = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_CHOICES')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_CHOICES') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MNGE_CHOICES');
			$where = ['ch_status' => 2]; //1 for store
			$get_allcate_details = get_all_details('gr_choices','ch_status',10,'desc','ch_id',$where);
			return view('sitemerchant.choices.manage_choice')->with('pagetitle',$page_title)->with('all_details',$get_allcate_details)->with('cate_detail',$get_cate_details)->with('id',$id)->with('sid',"Yes");
		}
		
		/** update country **/
		public function update_choice(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{	
				$name = 'ch_name';
				$old_name = mysql_escape_special_chars(Input::get('ch_name'));
				$this->validate($request,['ch_name' => 'required' ], [ 'ch_name.required'    => (Lang::has(Session::get('mer_lang_file').'.ADMIN_ENTR_CHOICE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ENTR_CHOICE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ENTR_CHOICE_NAME') ] );
				$id = Input::get('ch_id');
				$check = DB::table('gr_choices')->select($name,'ch_id')->where('ch_id','<>',$id)->where('ch_status','!=','2')->where($name,'=',$old_name)->get();
				$entry['ch_name'] = ucfirst($old_name);
				if(count($check) > 0)
				{	
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME_EXISTS');
					return Redirect::to('mer-manage-choices')->withErrors(['errors' =>$msg])->with('sid',"Yes");
				}
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
                        $validatoradv = Validator::make($request->all(), [
						'ch_name_'.$Lang->lang_code => 'required'
                        ]);
                        if($validatoradv->fails()){
                            return redirect('mer-manage-choices')->withErrors($validatoradv)->withInput();
							}else {
                            $entry['ch_name_'.$Lang->lang_code] = Input::get('ch_name_'.$Lang->lang_code);
						}
						
					}
				}
				$entry['ch_added_by']=Session::get('merchantid');
				$update = updatevalues('gr_choices',$entry,['ch_id' =>Input::get('ch_id')]);
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer-manage-choices');
			}
			else
			{
				return Redirect::to('merchant-login');
			}
			
		}
		
		/** block/unblock category **/
		public function choice_status($id,$status)
		{	
			$update = ['ch_status' => $status];
			$where = ['ch_id' => $id];
			$a = updatevalues('gr_choices',$update,$where);
			
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer-manage-choices');
			}
			if($status == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer-manage-choices');
			}
			else   //block
			{	
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('mer-manage-choices');
			}
		}
		
		/** multiple block/unblock  categoory**/
		public function multi_choice_block()
		{	
			$update = ['ch_status' => Input::get('status')];
			$val = Input::get('val');
			
			//return count($val); exit;
			for($i=0; $i< count($val); $i++)
			{
				$where = ['ch_id' => $val[$i]];
				
				$a = updatevalues('gr_choices',$update,$where);
			}
			//echo Input::get('status'); exit;
			if(Input::get('status') == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				
			}
			if(Input::get('status') == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				
			}
			elseif(Input::get('status') == 0)   //block
			{	
				
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				
			}
		}
		
		/** download store category **/
		public function download_choices($type,$sample = '')
		{
			if($sample == 'sample_file')
			{
				$data = DB::table('gr_choices')->select('ch_name as StoreCategoryName','ch_status','ch_added_by')->where('ch_status','=','1')->limit(4)->get()->toarray();
			}
			else
			{
				$data = DB::table('gr_choices')->select('ch_name as StoreCategoryName','ch_status','ch_added_by')->where('ch_status','=','1')->get()->toarray();
			}
			return Excel::create('Choices lists',function ($excel) use ($data)
    		{
    			$excel->sheet('choice_list', function ($sheet) use ($data)
    			{    
                    $sheet->cell('A1', function($cell) {
					$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SNO'));   });   
                    $sheet->cell('B1', function($cell) {
                    	$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME'));
					});   
                    $sheet->cell('C1', function($cell) {
                    	$cell->setValue((Lang::has(Session::get('mer_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_STATUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STATUS'));
					});   
                    $sheet->cell('D1', function($cell) {
                    	$cell->setValue((Lang::has(Session::get('mer_lang_file').'.MER_ADDED_BY')) ? trans(Session::get('mer_lang_file').'.MER_ADDED_BY') : trans($this->MER_OUR_LANGUAGE.'.MER_ADDED_BY'));
					});   
					$sheet->setFontFamily('Comic Sans MS');
					$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });
					//$j=3;
					foreach ($data as $key => $value) {
						$i= $key+2;
						$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ACT');
						$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_MERCHANT');
						if($value->ch_status==0)
						{
							$status = (Lang::has(Session::get('mer_lang_file').'.ADMIN_INACT')) ? trans(Session::get('mer_lang_file').'.ADMIN_INACT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_INACT');
						}
						
						if($value->ch_added_by==0)
						{
							$added_by = (Lang::has(Session::get('mer_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADMIN') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ADMIN');
						}
						$sheet->cell('A'.$i, $i-1); //print serial no
						$sheet->cell('B'.$i, $value->StoreCategoryName); 
						$sheet->cell('C'.$i, $status); 
						$sheet->cell('D'.$i,$added_by); 
						$i++;
					}
				});
			})->download($type);
			
		}
		
		/** import category file **/
		public function import_choices(Request $request){
			if(Session::has('merchantid') == 1){
				$this->validate($request, array('upload_file' => 'required'));
				
				$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
				if(!in_array($_FILES['upload_file']['type'],$mimes))
				{
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_FORMAT_INCORRECT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_FORMAT_INCORRECT');
					return Redirect::to('mer-manage-choices')->withErrors(['upload_file'=> $msg]);
				} 
				
				$upload = $request->upload_file;  
				// $filePath = $upload->getRealPath();
				$path = $upload->getRealPath(); 
				$data1 = Excel::load($path, function($reader) {
				})->get();
				$headerRow = $data1->first()->keys()->toArray();
				//print_r($headerRow); exit;
				$errorArray = $data = array();
				/* check header  */
				$existing_header = ['s.no','choice_name','status','added_by'];
				$diff_arr = array_diff($headerRow,$existing_header);
				if(!empty($diff_arr))
				{
					$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPLODE_ERR') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPLODE_ERR');
					Session::flash('import_err',str_replace(':columns',implode(',',$diff_arr),$msg));
					Session::flash('popup',"open");
					return Redirect::back();
				}
				else
				{
					$data = $data1->toArray();
				}
				if(!empty($data))
				{
					
					foreach ($data as $key => $value) {
						$name = 'ch_name';
						$old_name = $value['choice_name'];
						$where = [$name => $old_name]; 
						$check = check_name_exists('gr_choices','ch_status',$where);
						//  print_r($check);echo $old_name;echo $name; print_r($where);exit; 
						if(count($check) > 0)
						{   
							$msg = $value['choice_name'].' - ';
							$msg .= (Lang::has(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME_EXISTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CHOICE_NAME_EXISTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CHOICE_NAME_EXISTS');
							array_push($errorArray,$msg);
							
						}
						else
						{
							$insData = DB::table('gr_choices')->insert(['ch_name'=>$value['choice_name'],
							'ch_status'=> ($value['status'] == 'Active') ? '1' : '0',
							'ch_added_by' => ($value['added_by'] == 'Admin') ? '0' : Session::get('merchantid')]);
						}
						
					}
					if(count($errorArray) > 0 )
					{
						return Redirect::to('mer-manage-choices')->withErrors(['err_errors' =>$errorArray]);
					}
					else
					{
						$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_IMPORT_SUCCESS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_IMPORT_SUCCESS');
						Session::flash('message',$msg);
						return Redirect::to('mer-manage-choices');
					}
				}
				$msg = (Lang::has(Session::get('mer_lang_file').'.ADMIN_FILL_DATA')) ? trans(Session::get('mer_lang_file').'.ADMIN_FILL_DATA') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_FILL_DATA');
				return Redirect::to('mer-manage-choices')->with('message',$msg);
				
			}
			else
			{
				return Redirect::to('merchant-login') ;
			}
		}
		
		
	}		