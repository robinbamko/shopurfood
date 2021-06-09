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
	
	use Excel;
	
	use Response;
	use File;
	
	class MerchantController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		/*MERCHANTS LIST */
		public function merchants_list($id='')
		{
			if(Session::has('admin_id') == 1)
			{
				if($id!='')
				{
					DB::table('gr_merchant')->update(['mer_read_status' => 1]);
					return Redirect::to('manage-merchant');
				}    
				$get_merchants_details = array();//get_all_details('gr_merchant','mer_status',10,'desc','id','');
				//print_r($get_merchants_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MANAGE_MERCHANT');
				return view('Admin.merchant.manage_merchants')->with('pagetitle',$page_title)->with('all_details',$get_merchants_details);
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		
		public function ajax_merchants_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{	
				$columns = array( 
				0 => 'id', 
				1 => 'id', 
				2 => 'mer_fname', 
				3 => 'mer_business_type',
				4 => 'st_store_name',
				5 => 'mer_email',
				6 => 'id',
				7 => 'mer_status',
				8 => 'id',
				9 => 'addedby'
				);
				/*To get Total count */
				$totalData = DB::table('gr_merchant')
				->select('id')
				->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
				->where('gr_merchant.mer_status','<>','2')
				->where('gr_merchant.mer_business_type','=','2')
				//->where('gr_store.st_type','=','1')
				->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit 	= $request->input('length');
				$start 	= $request->input('start');
				$order 	= $columns[$request->input('order.0.column')];
				$dir	= $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$merName_search = trim($request->merName_search); 
				$busType_search = trim($request->busType_search); 
				$email_search 	= trim($request->email_search); 
				$status_search 	= trim($request->status_search); 
				$addedBy_search = trim($request->addedBy_search); 
				$restName_search = trim($request->restName_search); 
				//&& $busType_search==''
				if($merName_search==''  && $email_search=='' && $status_search=='' && $addedBy_search=='' && $restName_search=='')
				{    
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_merchant')->select('gr_merchant.id',
					'gr_merchant.mer_fname',
					'gr_merchant.mer_lname',
					'gr_merchant.mer_business_type',
					'gr_merchant.mer_email',
					'gr_merchant.mer_status',
					'gr_merchant.mer_commission',
					'gr_merchant.mer_newly_register',
					'gr_merchant.addedby',
					'gr_store.st_store_name',
					'gr_store.id as storeId'
					)
					->leftJoin('gr_store','gr_store.st_mer_id','=','gr_merchant.id')
					->where('gr_merchant.mer_status','<>','2')
					->where('gr_merchant.mer_business_type','=','2')
					//->where('gr_store.st_type','=','1')// AND gr_store.st_type=1
					->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_merchant')->select('gr_merchant.id',
					'gr_merchant.mer_fname',
					'gr_merchant.mer_lname',
					'gr_merchant.mer_business_type',
					'gr_merchant.mer_email',
					'gr_merchant.mer_status',
					'gr_merchant.mer_commission',
					'gr_merchant.addedby',
					'gr_merchant.mer_newly_register',
					'gr_store.st_store_name',
					'gr_store.id as storeId'
					)
					->leftJoin('gr_store','gr_store.st_mer_id','=','gr_merchant.id')
					->where('gr_merchant.mer_business_type','=','2')
					->where('gr_merchant.mer_status','<>','2');
					if($merName_search != '')
					{
						$q = $sql->whereRaw("CONCAT(if(gr_merchant.mer_fname is null,'',gr_merchant.mer_fname),' ',if(gr_merchant.mer_lname is null,'',gr_merchant.mer_lname)) like '%".$merName_search."%'"); 
					}
					/*if($busType_search != '')
					{
						$q = $sql->where("gr_merchant.mer_business_type",$busType_search); 
					}*/
					if($email_search != '')
					{
						$q = $sql->whereRaw("gr_merchant.mer_email like '%".$email_search."%'"); 
					}
					if($status_search != '')
					{
						$q = $sql->where('gr_merchant.mer_status',$status_search); 
					}
					if($addedBy_search != '')
					{
						$q = $sql->where('gr_merchant.addedby',$addedBy_search); 
					}
					if($restName_search != ''){
						$q = $sql->where('gr_store.st_store_name','LIKE','%'.$restName_search.'%'); 
					}
					$totalFiltered = $q->count();
					//DB::connection()->enableQueryLog();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					/*$query = DB::getQueryLog();
						print_r($query);
					exit;*/
					
				}
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$phrase  		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_BLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_BLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT_BLOCK_ERROR');
					$blockPhrase 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STORE_UNBLOCK_ERROR');
					$click_to_block = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
					$click_to_unblock = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
					$click_to_edit = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
					$click_to_delete = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
					$fill_details = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CH_COMM')) ? trans(Session::get('admin_lang_file').'.ADMIN_CH_COMM') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CH_COMM');
					foreach ($posts as $post)
					{
						
						if($post->mer_business_type==1) { 
							$storeType 		= 2; 
							$store_type		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ST');  
							$pro_type		= 1; 
							$product_type	=(Lang::has(Session::get('admin_lang_file').'.ADMIN_PROS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PROS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PROS');
							} else { 
							$storeType		= 1;
							$store_type		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_RTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_RTS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RTS'); 
							$pro_type 		= 2; 
							$product_type 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ITS');
						}
						
						$getUsedProduct = getStoresUsedThisMerchant($post->id,$storeType);
						$usedStore 		= $getUsedProduct[0]->usedStore;
						$usedCount 		= $getUsedProduct[0]->usedCount;
						
						$getCartProduct = getCartItemsUsedThisStore($post->storeId,$pro_type);
						$cartCount 		= $getCartProduct[0]->usedCount;
						
						$getUsedProduct = getProductsUsedThisStore($post->storeId,$pro_type,'block');
						$usedProduct 	= $getUsedProduct[0]->usedProduct;
						$usedCount 		= $getUsedProduct[0]->usedCount;
						$totalBlockCount= $usedCount+$cartCount;
						
						if($totalBlockCount > 0 ){
							$search_str = array(":num_products", ":product_type", ":num_items", ":action_text");
							$replace_str= array($usedCount, $product_type, $cartCount, 'block');
							$new_string = str_replace($search_str, $replace_str, $phrase);
							$toolTipText= $new_string;//$usedCount.' products are added in this store. Please block those products before you block this store';
							$onclickfun = 'onclick="return confirm(\''.$toolTipText.'\');"';
							}else {
							$onclickfun = '';
						}
						
						$new_tag = '';
						if($post->mer_newly_register == '1')
						{
							$new_tag = '<span class="badge bg-danger">New</span>';
						}

						if($post->mer_business_type == 1)
						{
							$businessTypeText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STORE');
						}
						else
						{
							$businessTypeText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_RESTAURANT');
						}
						if($post->mer_status == 1)
						{
							$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',0);" id="statusLink_'.$post->id.'" '.$onclickfun.'><i class="fa fa-check tooltip-demo" aria-hidden="true" title="'.$click_to_block.'"></i></a>';
							
						}
						else
						{
							if($new_tag != '')
							{	
								$search_str = array(":comm", ":name");
								$replace_str= array($post->mer_commission,ucfirst($post->mer_fname));
								$new_string = str_replace($search_str, $replace_str, $fill_details);

								$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',1);" id="statusLink_'.$post->id.'" onclick="return confirm(\''.str_replace(":product_type",$product_type,$blockPhrase).'\');"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$new_string.'" ></i></a>';
							}
							else
							{
								$statusLink = '<a href="javascript:individual_change_status(\''.$post->id.'\',1);" id="statusLink_'.$post->id.'" onclick="return confirm(\''.str_replace(":product_type",$product_type,$blockPhrase).'\');"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$click_to_unblock.'" ></i></a>';	
							}
							
						}
						if($post->addedby == '0')
						{
							$addedByText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedByText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT');
						}

						$getUsedProduct 	= getProductsUsedThisStore($post->storeId,$pro_type,'delete');
						$usedCount 			= $getUsedProduct[0]->usedCount;
						$totalDeleteCount 	= $usedCount+$cartCount;
						
						if($totalDeleteCount > 0 ){
							$search_str 		= array(":num_products", ":product_type", ":num_items", ":action_text");
							$replace_str		= array($usedCount, $product_type, $cartCount, 'delete');
							$new_string 		= str_replace($search_str, $replace_str, $phrase);
							$deleteToolTipText 	= $new_string;//$usedCount.' products are added in this store. Please delete those products before you delete this store';
							$onclickfun 		= 'onclick="return confirm(\''.$deleteToolTipText.'\');"';
							}else {
							$onclickfun 		= '';
						}
						
						//usedStore
						$deleteLink = '<a href= "javascript:individual_change_status(\''.$post->id.'\',2);" title="'.$click_to_delete.'" class="tooltip-demo" id="statusLink_'.$post->id.'" '.$onclickfun.'><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
						$chkName = 'chk[]';
						$chkDisabled='';
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="'.$chkName.'" value="'.$post->id.'" '.$chkDisabled.'>';
						$nestedData['SNo'] 		= ++$snoCount;
						$nestedData['merName'] 	= ucfirst($post->mer_fname).' '.ucfirst($post->mer_lname).' '.$new_tag;
						$nestedData['busType'] 	= $businessTypeText;
						$nestedData['storeName']= $usedStore;
						$nestedData['merEmail'] = $post->mer_email;
						$nestedData['Edit'] 	= '<a href="'.url('edit-merchant').'/'.base64_encode($post->id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="'.$click_to_edit.'"></i></a>';
						$nestedData['Status'] 	= $statusLink;
						$nestedData['delete'] 	= $deleteLink;
						$nestedData['addedBy'] 	= $addedByText;
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
		
		public function ajax_merchants_list_sathya(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$columns = array(
                0 =>'id',
                1 =>'id',
                2 =>'mer_fname',
                3=> 'mer_business_type',
                4=> 'st_store_name',
                5=> 'mer_email',
                6=> 'id',
                7=> 'mer_status',
                8=> 'id',
                9=> 'addedby'
				);
				/*To get Total count */
				$totalData = DB::table('gr_merchant')
                ->select('id')
                ->where('mer_status','<>','2')
                ->count();
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$merName_search = trim($request->merName_search);
				$busType_search = trim($request->busType_search);
				$email_search = trim($request->email_search);
				$status_search = trim($request->status_search);
				$addedBy_search = trim($request->addedBy_search);
				if($merName_search=='' && $busType_search=='' && $email_search=='' && $status_search=='' && $addedBy_search=='')
				{
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_merchant')->select('gr_merchant.id',
                    'mer_fname',
                    'mer_lname',
                    'mer_business_type',
                    'mer_email',
                    'mer_status',
                    'addedby',
                    'st_store_name'
					)
                    ->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
                    ->where('mer_status','<>','2')
                    ->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					//DB::connection()->enableQueryLog();
					$sql = DB::table('gr_merchant')
                    ->select('gr_merchant.id',
					'mer_fname',
					'mer_lname',
					'mer_business_type',
					'mer_email',
					'mer_status',
					'addedby',
					'st_store_name'
                    )
                    ->leftJoin('gr_store','gr_merchant.id','=','gr_store.st_mer_id')
                    ->where('mer_status','<>','2');
					if($merName_search != '')
					{
						$q = $sql->whereRaw("CONCAT(if(mer_fname is null,'',mer_fname),' ',if(mer_lname is null,'',mer_lname)) like '%".$merName_search."%'");
					}
					if($busType_search != '')
					{
						$q = $sql->where("mer_business_type",$busType_search);
					}
					if($email_search != '')
					{
						$q = $sql->whereRaw("mer_email like '%".$email_search."%'");
					}
					if($status_search != '')
					{
						$q = $sql->where('mer_status',$status_search);
					}
					if($addedBy_search != '')
					{
						$q = $sql->where('addedby',$addedBy_search);
					}
					$totalFiltered = $q->count();
					//DB::connection()->enableQueryLog();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					/*$query = DB::getQueryLog();
						print_r($query);
					exit;*/
					
				}
				
				/** FOR SUBADMIN PRIVILAGES **/
				$allPrev = Config('allPrev');
				$privileges = Config('privileges');
				extract($privileges);
				/** END FOR SUBADMIN PRIVILAGES **/
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$phrase  		= (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_BLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_BLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_MERCHANT_BLOCK_ERROR');
					$blockPhrase 	= (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE_UNBLOCK_ERROR') : trans($this->ADMIN_LANGUAGE.'.ADMIN_STORE_UNBLOCK_ERROR');
					$click_to_block = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_BLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_BLOCK');
					$click_to_unblock = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_UNBLOCK') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_UNBLOCK');
					$click_to_edit = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_EDIT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_EDIT');
					$click_to_delete = (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLICK_TO_DELETE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_CLICK_TO_DELETE');
					foreach ($posts as $post)
					{
						if($post->mer_business_type==1) { $storeType = 2; } else { $storeType=1;}
						$getUsedProduct = getStoresUsedThisMerchant($post->id,$storeType);
						$usedStore = $getUsedProduct[0]->usedStore;
						$usedCount = $getUsedProduct[0]->usedCount;
						$toolTipText = $usedCount.' store is added by this merchant.if you block this merchant the store also will be Blockd';
						$deleteToolTipText = $usedCount.' store is added by this merchant. Please delete that store before you delete this merchant';
						if($post->mer_business_type == 1)
						{
							$businessTypeText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STORE');
						}
						else
						{
							$businessTypeText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTAURANT');
						}
						if($post->mer_status == 1)
						{
							if($usedCount > 0)
							{
								$statusLink='<a href="javascript:change_all_statusblk('.$post->id.');"><i class="fa fa-check tooltip-demo" data-toggle="popover" data-html="true" title="'.$toolTipText.'" data-trigger="hover" disabled="disabled"></i></a>';
								
								// $statusLink = '<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Sorry! Can\'t Block" ></i>';
							}
							else
							{
								$statusLink = '<a href="'.url('merchant_status').'/'.$post->id.'/0"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>';
								
								
							}
							
						}
						else
						{
							
							if($usedCount > 0)
							{
								
								$toolTipTextUb = $usedCount.' store is added by this merchant.if you Unblock this merchant the store also will be UnBlockd';
								
								$statusLink = '<a href="javascript:change_all_unblock('.$post->id.');"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="'.$toolTipTextUb.'" ></i></a>';
								
								}else{
								$statusLink = '<a href="'.url('merchant_status').'/'.$post->id.'/1"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i></a>';
							}
							
						}
						if($post->addedby == '0')
						{
							$addedByText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
						}
						else
						{
							$addedByText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT');
						}
						
						if ($allPrev == '1' || in_array('2', $Merchant)){
							$editLink='<a href="'.url('edit-merchant').'/'.base64_encode($post->id).'"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a>';
							} else {
							$editLink='--';
						}
						
						if ($allPrev == '1' || in_array('3', $Merchant)){
							if($usedCount > 0)
							{
								$deleteLink='<a href="javascript:;"><i class="fa fa-trash" data-toggle="popover" title="Sorry! Can\'t Delete" data-content="'.$deleteToolTipText.'" data-trigger="hover"  disabled="disabled"></i></a>';
								$chkName = '';
								$chkDisabled = 'disabled="disabled"';
							}
							else
							{
								$deleteLink='<a href= "'.url('merchant_status').'/'.$post->id.'/2" title="delete" class="tooltip-demo"><i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i></a>';
								$chkName = 'chk[]';
								$chkDisabled='';
							}
							} else {
							$deleteLink= '--';
							$chkName = '';
							$chkDisabled='';
						}
						
						$nestedData['checkBox'] = '<input type="checkbox" class="checkboxclass" name="'.$chkName.'" value="'.$post->id.'" '.$chkDisabled.'>';
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['merName'] = $post->mer_fname.' '.$post->mer_lname;
						$nestedData['busType'] = $businessTypeText;
						$nestedData['storeName'] = $usedStore;
						$nestedData['merEmail'] = $post->mer_email;
						$nestedData['Edit'] = $editLink;
						$nestedData['Status'] = $statusLink;
						$nestedData['delete'] = $deleteLink;
						$nestedData['addedBy'] = $addedByText;
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
		public function add_merchant()
		{
			if(Session::has('admin_id') == 1)
			{ 
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MERCHANT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_MERCHANT');
				$action='save-merchant';
				$array_name = array();
				$commissionsettings=DB::table('gr_general_setting')->select('common_commission')->first();
				foreach(DB::getSchemaBuilder()->getColumnListing('gr_merchant') as $res)
				{
					$array_name[$res]='';
				}
				$object = (object) $array_name; // return all value as empty.
				return view('Admin.merchant.add_merchant')->with('pagetitle',$page_title)->with('getvendor',$object)->with('id','')->with('action',$action)->with('commoncommission',$commissionsettings);
			}
			else
			{
				return Redirect::to('admin-login');
			}
			
		}
		public function random_password( $length = 8 ) {
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			//$chars = "0123456789";
			$password = substr( str_shuffle( $chars ), 0, $length );
			return $password;
		}
		public function save_merchant(Request $request)
		{		
			
			if(Session::has('admin_id') == 1)
			{
				
				
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MERCHANT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MERCHANT');
				$action='save_merchant';
				$array_name = array();
				foreach(DB::getSchemaBuilder()->getColumnListing('gr_merchant') as $res)
				{
					$array_name[$res]='';
				}
				$object = (object) $array_name; // return all value as empty.
				//return view('Admin.merchant.add_merchant')->with('pagetitle',$page_title)->with('getvendor',$object)->with('id','')->with('action',$action);
				$this->validate($request, 
				[
				'mer_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
				'mer_lname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
				'idproof' 			=> 'Required',
				'idproof.*' 		=> 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,max_width=500,min_height=300,max_height=500',
				'license' 			=> 'Required',
				'license.*' 		=> 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=300,max_width=500,min_height=300,max_height=500',
				'refund_status'=>'Required',
				'cancel_status'=>'Required',
				'mer_business_type'=>'Required',
				'mer_email'  => [
				'required', 
				Rule::unique('gr_merchant')->where(function ($query) use ($request) {
					return $query->where('gr_merchant.mer_status','<>','2');
				}),
				],
				'mer_phone'  => [
				'only_cnty_code', 
				Rule::unique('gr_merchant')->where(function ($query) use ($request) {
					return $query->where('gr_merchant.mer_status','<>','2');
				}),
				],
				//'mer_email'=>'Required|Email|unique:gr_merchant,mer_email',
				//'mer_phone'=>'Required|Numeric|unique:gr_merchant,mer_phone',
				'mer_commission'=>'Required|Numeric',
				],['mer_fname.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME'), 
				'mer_lname.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_LNAME'),
				'mer_email.email'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL'),
				'cancel_status.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CANCEL_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CANCEL_STATUS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_CANCEL_STATUS'),
				'mer_email.unique'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL_UNIQUE_VAL'),
				'mer_commission.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_COMMISSION'),
				'mer_phone.only_cnty_code'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EN_VALID_PH'),
				'mer_business_type.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SL_BU_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SL_BU_TYPE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SL_BU_TYPE'),
				'idproof.required' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPLOAD_IDPROOF')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPLOAD_IDPROOF') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPLOAD_IDPROOF'),
				'idproof.*.mimes' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE'),
				'idproof.*.dimensions' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE'),
				'license.required' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPLOAD_RESTLICENCE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPLOAD_RESTLICENCE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPLOAD_RESTLICENCE'),
				'license.*.mimes' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE'),
				'license.*.dimensions' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_VALID_IMAGE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_VALID_IMAGE')
				
				]);
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
				$idproof_image="";
				if($request->hasFile('idproof'))
				{
					$idproof_image = 'Id_Proof_'.rand().'.'.request()->idproof->getClientOriginalExtension();
					$destinationPath = public_path('images/merchant');
					$Idproof = Image::make(request()->idproof->getRealPath())->resize(300, 300);
					$Idproof->save($destinationPath.'/'.$idproof_image,80);
					//$rcbook_image
				}
				
				$license_image = '';
				if($request->hasFile('license'))
				{
					$license_image = 'Licence_'.rand().'.'.request()->license->getClientOriginalExtension();
					$destinationPath = public_path('images/merchant');
					$License = Image::make(request()->license->getRealPath())->resize(300, 300);
					$License->save($destinationPath.'/'.$license_image,80);
				}
				
				$passwordIs = $this->random_password(6);
				$profile_det = array(
									'mer_fname'=>Input::get('mer_fname'),
									'mer_lname'=>Input::get('mer_lname'),
									'mer_email'=>Input::get('mer_email'),
									'mer_password'=>md5($passwordIs),
									'mer_decrypt_password'=>$passwordIs,
									'mer_phone'=>Input::get('mer_phone'),
									'mer_cancel_policy'=>Input::get('mer_cancel_policy'),
									'refund_status'=>Input::get('refund_status'),
									'cancel_status'=>Input::get('cancel_status'),
									'mer_currency_code'=>Input::get('mer_currency_code'),
									'mer_commission'=>Input::get('mer_commission'),
									'mer_paynamics_status'=>Input::get('mer_paynamics_status'),
									'mer_paynamics_clientid'=>Input::get('mer_paynamics_clientid'),
									'mer_paynamics_secretid'=>Input::get('mer_paynamics_secretid'),
									'mer_paymaya_status'=>Input::get('mer_paymaya_status'),
									'mer_paymaya_clientid'=>Input::get('mer_paymaya_clientid'),
									'mer_paymaya_secretid'=>Input::get('mer_paymaya_secretid'),
									'mer_netbank_status'=>Input::get('mer_netbank_status'),
									'mer_bank_name'=>Input::get('mer_bank_name'),
									'mer_branch'=>Input::get('mer_branch'),
									'mer_bank_accno'=>Input::get('mer_bank_accno'),
									'mer_ifsc'=>Input::get('mer_ifsc'),
									'mer_location'=>Input::get('mer_location'),
									'mer_country'=>Input::get('country'),
									'mer_state'=>Input::get('mer_state'),
									'mer_city'=>Input::get('mer_city'),
									'idproof' =>   $idproof_image,
									'license' =>   $license_image,
									'mer_business_type'=>Input::get('mer_business_type'),
									'mer_created_date' => date('Y-m-d H:i:s'),
									'mer_updated_date' => date('Y-m-d H:i:s')
									);
				
				
				if(count($this->get_Adminactive_language) > 0)
				{
					foreach($this->get_Adminactive_language as $Lang)
					{
						$profile_det['mer_cancel_policy_' . $Lang->lang_code] = Input::get('mer_cancel_policy_' . $Lang->lang_code);
						
					}
				}
				
				
				$res=insertvalues('gr_merchant',$profile_det);
				
				//print_r($res); exit;
				
				if($res)
				{
					//----MAIL FUNCTION
					$send_mail_data = array('name' => Input::get('mer_fname').' '.Input::get('mer_lname'),
					'password' => $passwordIs,
					'email' => Input::get('mer_email'),
					'commission' => Input::get('mer_commission')
					);
					Mail::send('email.merchant_register_email', $send_mail_data, function($message)
					{
						$email               = Input::get('mer_email');
						$name                = Input::get('mer_fname').' '.Input::get('mer_lname');
						$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
						$message->to($email, $name)->subject($subject);
					});
					//				 EOF MAIL FUNCTION
					
					$message = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_INSERT_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_INSERT_SUCCESS');
					
					
					
					
					if(Input::get('mer_business_type') == 1){
						echo "<script>
						if(confirm('Do you want to Add Store')){
						window.location.href = 'add-store';					
						}else{
						window.location.href = 'manage-merchant';	
						}
						</script>";
						
						}else if(Input::get('mer_business_type') == 2){
						echo "<script>
						if(confirm('Do you want to Add Restaurant')){
						window.location.href = 'add-restaurant';					
						}else{
						window.location.href = 'manage-merchant';	
						}
						</script>";
					}
					// return redirect('manage-merchant')->with('message',$message);
				}
				else
				{
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUMTHNG_WRONG') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_SUMTHNG_WRONG');
					return Redirect::to('add-merchant')->withErrors(['errors'=> $msg])->withInput();
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		
		/** edit store category **/
		public function edit_merchant($id)
		{
			if(Session::has('admin_id') == 1)
			{
				$commissionsettings=DB::table('gr_general_setting')->select('common_commission')->first();
				$id = base64_decode($id);
				$where = ['id' => $id];
				$get_merchants_details = get_details('gr_merchant',$where);
				//print_r($get_merchants_details); exit;
				$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_MERCHANT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT_MERCHANT');
				$action='update-merchant';
				return view('Admin.merchant.add_merchant')->with('pagetitle',$page_title)->with('getvendor',$get_merchants_details)->with('id',$id)->with('action',$action)->with('commoncommission',$commissionsettings);
				
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function update_merchant(Request $request)
		{
			
			$id = Input::get('gotId');
			$where = ['id' => $id];
			$get_merchants_details = get_details('gr_merchant',$where);
			//print_r($get_merchants_details); exit;
			$page_title = (Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT_MERCHANT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT_MERCHANT');
			$action='update-merchant';
			if(Session::has('admin_id') == 1)
			{
				
				$this->validate($request, 
				[
				'mer_fname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
				'mer_lname'=>'Required|regex:/(^[A-Za-z0-9 ]+$)+/',
				'mer_email'  => [
				'required', 
				Rule::unique('gr_merchant')->where(function ($query) use ($request) {
					return $query->where('gr_merchant.id', '<>', Input::get('gotId'))->where('gr_merchant.mer_status','<>','2');
				}),
				],
				'mer_password' => 'required',
				'mer_phone'  => [
				'only_cnty_code', 
				Rule::unique('gr_merchant')->where(function ($query) use ($request) {
					return $query->where('gr_merchant.id', '<>', Input::get('gotId'))->where('gr_merchant.mer_status','<>','2');
				}),
				],
				'cancel_status'=>'required',
				//'mer_email' => 'Required|Email|unique:gr_merchant,mer_email,'.$id.',
				//'mer_phone'=>'Required|Numeric|unique:gr_merchant,mer_phone,'.$id,
				'mer_commission'=>'Required|Numeric'		
				],['mer_fname.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME'), 
				'mer_lname.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_LNAME'),
				'mer_email.email'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL'),
				'mer_password.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_PASS'),
				'mer_commission.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_COMMISSION'),
				'mer_phone.only_cnty_code'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH')) ? trans(Session::get('admin_lang_file').'.ADMIN_EN_VALID_PH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_EN_VALID_PH'),
                'idproof.required'    => 'Please Choose Idproof image || image size 500*500',
                'license.required'    => 'Please Choose license image || image size 500*500',
				'cancel_status.required'    => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CANCEL_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CANCEL_STATUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_SEL_CANCEL_STATUS'), 
				]); 
				
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
				
				$idproof_image = Input::get('old_idproof');
				if($request->hasFile('idproof'))
				{
					$old_image = Input::get('old_idproof');
					$image_path = public_path('images/merchant/').$old_image;  // Value is not URL but directory file path
					if(File::exists($image_path))
					{
						$a =   File::delete($image_path);
						
					}
					$idproof_image = 'Id_Proof_'.rand().'.'.request()->idproof->getClientOriginalExtension();
					$destinationPath = public_path('images/merchant');
					$Idproof = Image::make(request()->idproof->getRealPath())->resize(300, 300);
					$Idproof->save($destinationPath.'/'.$idproof_image,80);
					//$rcbook_image
				}
				$license_image = Input::get('old_license');
				if($request->hasFile('license'))
				{
					$old_image = Input::get('old_license');
					$image_path = public_path('images/merchant/').$old_image;  // Value is not URL but directory file path
					if(File::exists($image_path))
					{
						$a =   File::delete($image_path);
						
					}
					$license_image = 'Licence_'.rand().'.'.request()->license->getClientOriginalExtension();
					$destinationPath = public_path('images/merchant');
					$License = Image::make(request()->license->getRealPath())->resize(300, 300);
					$License->save($destinationPath.'/'.$license_image,80);
				}
				if($request->has('commoncommission')){
					$mer_comissionstatus=1;
				}
				else{
					$mer_comissionstatus=0;
				}
				$old_commission = Input::get('oldcomssion');
				
				$profile_det = array(
									'mer_fname'				=>Input::get('mer_fname'),
									'mer_lname'				=>Input::get('mer_lname'),
									'mer_email'				=>Input::get('mer_email'),
									'mer_phone'				=>Input::get('mer_phone'),
									'mer_cancel_policy'		=>Input::get('mer_cancel_policy'),
									'refund_status'			=>Input::get('refund_status'),
									'cancel_status'			=>Input::get('cancel_status'),
									'mer_currency_code'		=>Input::get('mer_currency_code'),
									'mer_commission'		=>Input::get('mer_commission'),
									'mer_paynamics_status'	=>Input::get('mer_paynamics_status'),
									'mer_paynamics_clientid'=>Input::get('mer_paynamics_clientid'),
									'mer_paynamics_secretid'=>Input::get('mer_paynamics_secretid'),
									'mer_paymaya_status'	=>Input::get('mer_paymaya_status'),
									'mer_paymaya_clientid'	=>Input::get('mer_paymaya_clientid'),
									'mer_paymaya_secretid'	=>Input::get('mer_paymaya_secretid'),
									'mer_netbank_status'	=>Input::get('mer_netbank_status'),
									'mer_bank_name'			=>Input::get('mer_bank_name'),
									'mer_branch'			=>Input::get('mer_branch'),
									'mer_bank_accno'		=>Input::get('mer_bank_accno'),
									'mer_ifsc'				=>Input::get('mer_ifsc'),
									'mer_location'			=>Input::get('mer_location'),
									'mer_country'			=>Input::get('country'),
									'mer_state'				=>Input::get('mer_state'),
									'mer_city'				=>Input::get('mer_city'),
									'mer_comissionstatus'	=>$mer_comissionstatus,
									'mer_updated_date' 		=> date('Y-m-d H:i:s')
									);
				
				if($idproof_image!=""){
					$idarray1=array('idproof' =>   $idproof_image);
					$profile_det=array_merge($profile_det,$idarray1);
				}
				if($license_image!=""){
					$idarray2=array('license' =>   $license_image);
					$profile_det=array_merge($profile_det,$idarray2);
				}
				
				
				
				$old_pass = Input::get('old_password');
				$new_pass = Input::get('mer_password');
				if((Input::get('old_email')!=Input::get('mer_email')) || ($old_pass != $new_pass))
				{	
					$profile_det=array_merge($profile_det,['mer_password' => md5($new_pass),'mer_decrypt_password' => $new_pass]);
					/*MAIL FUNCTION */
					$send_mail_data = array('name' => Input::get('mer_fname').' '.Input::get('mer_lname'),
											'password' => $new_pass,
											'email' => Input::get('mer_email'),
											'url'=>'merchant-login',
											'andr_link' => Session::get('MER_ANDR_LINK'),
											'ios_link' => Session::get('MER_IOS_LINK')
											);
					Mail::send('email.username_password_email', $send_mail_data, function($message)
					{
						$email               = Input::get('mer_email');
						$name                = Input::get('mer_fname').' '.Input::get('mer_lname');
						$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_REG_DETAILS');
						$message->to($email, $name)->subject($subject);
					});
					/* EOF MAIL FUNCTION */ 
				}			
				elseif($old_commission != Input::get('mer_commission')) 
				{
					$send_mail_data = array('name' => Input::get('mer_fname'),'commission' => Input::get('mer_commission'));
						$mail 			= Input::get('mer_email');
						Mail::send('email.merchant_commission_mail', $send_mail_data, function($message) use($mail)
						{
							$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_COMM_DETAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMM_DETAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_COMM_DETAIL');
							$message->to($mail)->subject($subject);
						});
				}
				//print_r($update_det); exit;
				$update = updatevalues('gr_merchant',$profile_det,['id' =>$id]);
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-merchant');
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		/** update country **/
		
		
		/** block/unblock category **/
		public function change_status($id,$status)
		{	
			
			$update = ['mer_status' => $status];
			$where = ['id' => $id];
			$a = updatevalues('gr_merchant',$update,$where);
			/* send mail to merchant */
			$related_details = get_related_details('gr_merchant',['id' => $id],['mer_email','mer_fname','mer_profile_img'],'individual');
			if(!empty($related_details))
			{
				$send_mail_data = array('name' => $related_details->mer_fname,'status' => $status);
				$mail = $related_details->mer_email;
				Mail::send('email.merchant_status_mail', $send_mail_data, function($message) use($mail)
		        {
		            $subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MER_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_MER_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_MER_STATUS_CH');
		            $message->to($mail)->subject($subject);
				});
		        /* delete merchant image */
		        if($status == 2)
		        {
		        	if(File::exists(public_path('images/vendor_photos/').$related_details->mer_profile_img))
		            {
		                File::delete(public_path('images/vendor_photos/').$related_details->mer_profile_img);
					}
				}
			}
	        /* send mail ends */
			if($status == 1) //Active
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-merchant');
			}
			if($status == 2) //Delete
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-merchant');
			}
			else   //block
			{	
				
				$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
				Session::flash('message',$msg);
				return Redirect::to('manage-merchant');
			}
		}
		
		/** multiple block/unblock  categoory**/
		public function multi_changeStatus_sathya()
		{	
			if(Session::has('admin_id') == 1)
			{
				$update = ['mer_status' => Input::get('status')];
				$val = Input::get('val');
				$status = Input::get('status');
				//return count($val); exit;
				for($i=0; $i< count($val); $i++)
				{
					$where = ['id' => $val[$i]];
					
					$a = updatevalues('gr_merchant',$update,$where);
					/* send mail to merchant */
					$related_details = get_related_details('gr_merchant',['id' => $val[$i]],['mer_email','mer_fname','mer_profile_img'],'individual');
					if(!empty($related_details))
					{
						$send_mail_data = array('name' => $related_details->mer_fname,'status' => $status);
						$mail = $related_details->mer_email;
						Mail::send('email.merchant_status_mail', $send_mail_data, function($message) use($mail)
						{
							$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MER_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_MER_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_MER_STATUS_CH');
							$message->to($mail)->subject($subject);
						});
						/* delete merchant image */
						if($status == 2)
						{
							if(File::exists(public_path('images/vendor_photos/').$related_details->mer_profile_img))
							{
								File::delete(public_path('images/vendor_photos/').$related_details->mer_profile_img);
							}
						}
					}
					/* send mail ends */
				}
				//echo Input::get('status'); exit;
				if(Input::get('status') == 1) //Active
				{	
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK_SUCCESS');
					Session::flash('message',$msg);
					
				}
				if(Input::get('status') == 2) //Delete
				{	
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE_SUCCESS');
					Session::flash('message',$msg);
					
				}
				elseif(Input::get('status') == 0)   //block
				{	
					
					$msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK_SUCCESS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK_SUCCESS');
					Session::flash('message',$msg);
					
				}
			}
			else
			{
				return Redirect::to('admin-login');
			}
		}
		public function multi_changeStatus()
		{	
			if(Session::has('admin_id') == 1)
			{	
				$val 	= Input::get('val');
				$status = Input::get('status');					
				$update_arr['mer_status'] = Input::get('status');
				if($status == 1)
				{
					$update_arr['mer_newly_register'] = '0';
				}
				
				//return count($val); exit;
				for($i=0; $i< count($val); $i++)
				{
					$where = ['id' => $val[$i]];
					
					$a = updatevalues('gr_merchant',$update_arr,$where);
					/* send mail to merchant */
					$related_details = get_related_details('gr_merchant',['id' => $val[$i]],['mer_email','mer_fname','mer_profile_img'],'individual');
					if(!empty($related_details))
					{

						$send_mail_data = array('name' => $related_details->mer_fname,'status' => $status);
						$mail 			= $related_details->mer_email;
						Mail::send('email.merchant_status_mail', $send_mail_data, function($message) use($mail)
						{
							$subject = (Lang::has(Session::get('admin_lang_file').'.ADMIN_MER_STATUS_CH')) ? trans(Session::get('admin_lang_file').'.ADMIN_MER_STATUS_CH') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_MER_STATUS_CH');
							$message->to($mail)->subject($subject);
						});
						/* delete merchant image */
						if($status == 2)
						{
							DB::select("DELETE FROM gr_product_spec WHERE spec_pro_id IN (SELECT * FROM (SELECT gr_product.pro_id FROM gr_product JOIN gr_store ON gr_product.pro_store_id = gr_store.id WHERE gr_store.st_mer_id = '".$val[$i]."'  ) AS p)");
							DB::select("DELETE FROM gr_product_choice WHERE pc_pro_id IN (SELECT * FROM (SELECT gr_product.pro_id FROM gr_product JOIN gr_store ON gr_product.pro_store_id = gr_store.id WHERE gr_store.st_mer_id = '".$val[$i]."'  ) AS p)");
							if(File::exists(public_path('images/vendor_photos/').$related_details->mer_profile_img))
							{
								File::delete(public_path('images/vendor_photos/').$related_details->mer_profile_img);
							}
						}
						/*--------------- CHANGE STATUS --------------------*/
						DB::table('gr_store')->where('st_mer_id','=',$val[$i])->update(['st_status'=>$status]);
						DB::select("UPDATE gr_product AS st1, gr_store AS st2 SET st1.pro_status = '".$status."' WHERE st1.pro_store_id = st2.id and st2.st_mer_id = '".$val[$i]."'");
						DB::select("DELETE FROM gr_cart_save WHERE cart_st_id =  (SELECT id FROM gr_store WHERE st_mer_id = '".$val[$i]."') ");
					}
					/* send mail ends */
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
			else
			{
				return Redirect::to('admin-login');
			}
		}
		/** download store category **/
		public function download_merchants_list($type)
		{
			if(Session::has('admin_id') == 1)
			{
				$data = DB::table('gr_merchant')->select('mer_fname','mer_lname','mer_email','mer_phone','mer_location','mer_business_type')->where('mer_status','=','1')->get()->toarray();
				
				return Excel::create('Merchants list',function ($excel) use ($data)
				{
					$excel->sheet('merchants_list', function ($sheet) use ($data)
					{
						$sheet->setFontFamily('Comic Sans MS');
						//$sheet->row(2, function($row) { $row->setBackground('#CCCCCC'); });
						
						$sheet->cell('A2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO'));   });
						
						$sheet->cell('B2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_FIRST_NAME'));});
						
						$sheet->cell('C2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($this->ADMIN_LANGUAGE.'.ADMIN_LAST_NAME'));});
						
						$sheet->cell('D2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($this->ADMIN_LANGUAGE.'.ADMIN_EMAIL'));});
						
						$sheet->cell('E2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_PHONE'));});
						
						$sheet->cell('F2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDRESS') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADDRESS'));});
						
						$sheet->cell('G2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_BUSINESS_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BUSINESS_TYPE') : trans($this->ADMIN_LANGUAGE.'.ADMIN_BUSINESS_TYPE'));});
						
						$sheet->getStyle('F2')->getAlignment()->setWrapText(true);
						$j=3;
						foreach ($data as $key => $value) {
							$i= $key+2;
							$sheet->cell('A'.$j, $j-2); //print serial no
							$sheet->cell('B'.$j, $value->mer_fname); 
							$sheet->cell('C'.$j, $value->mer_lname); 
							$sheet->cell('D'.$j, $value->mer_email); 
							$sheet->cell('E'.$j, $value->mer_phone); 
							$sheet->cell('F'.$j, $value->mer_location); 
							$sheet->cell('G'.$j, ($value->mer_business_type==1)?'Store':'Restaurant'); 
							$j++;
						}
					});
				})->download($type);
			}
			else{
				return Redirect::to('admin-login');
			}
			
		}
		
		/* ---------Change merchant status---------*/
		public function ajax_change_mer_all_status(Request $request){
			$mercant_id = $request->get('mer_id');
			$status = $request->get('status');    	
    		$update = ['mer_status' => $status];
			$where = ['id' => $mercant_id];
			$merchant_status = updatevalues('gr_merchant',$update,$where);
			if($merchant_status){
				$upd_store = ['st_status' => $status];
				$whr_store = ['st_mer_id' => $mercant_id];
				$merchant_status = updatevalues('gr_store',$upd_store,$whr_store);
				$get_store_id = DB::table('gr_store')->where($whr_store)->get()->toArray();
				
				if(count($get_store_id) >0 ){
					foreach($get_store_id as $det){
						$store_id = $det->id;					
						if($store_id){
							$product_sts = updatevalues('gr_product',['pro_status'=>$status],['pro_store_id'=>$store_id]);
						}					
					}
				}
				
				echo '1';
				
				}else{
				echo '0';
			}
			
		}
	}	