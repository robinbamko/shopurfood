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
	
	use App\Item;
	use App\Reports;
	
	use Excel;
	
	use Response;
	use File;
	use Image;
	
	class InventoryController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
			//get admin language
			$this->setAdminLanguage();
		}
		/*MERCHANTS LIST */
		public function inventory_list()
		{
			if(Session::has('admin_id') == 0)
			{
				return redirect('admin-login');
			}
			$pagetitle = (Lang::has(Session::get('admin_lang_file').'.ADMIN_INVENTOY_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVENTOY_MGMT') : trans($this->ADMIN_LANGUAGE.'.ADMIN_INVENTOY_MGMT');
			$inventory_list = array();//Reports::get_inventory_details();
			return view('Admin.reports.Manage_Inventory')->with('pagetitle',$pagetitle)->with('inventory_list',$inventory_list);
		}
		public function ajax_inventory_list(Request $request)
		{
			if(Session::has('admin_id') == 1)
			{
				$columns = array(
                0 =>'pro_item_code',
                1 =>'pro_item_code',
                2 =>'pro_item_name',
                3=> 'st_store_name',
                4=> 'mer_fname',
                5=> 'pro_quantity',
                6=> 'pro_no_of_purchase',
                7=> 'pro_no_of_purchase'
				);
				/*To get Total count */
				$totalData = DB::table('gr_product')
                ->select('gr_product.pro_item_code',
				'gr_product.pro_item_name',
				'gr_store.st_store_name',
				'gr_merchant.mer_fname',
				'gr_merchant.mer_lname',
				'gr_product.pro_quantity',
				'gr_product.pro_no_of_purchase',
				'gr_product.pro_id'
                )
                ->leftjoin('gr_store', 'gr_product.pro_store_id', '=', 'gr_store.id')
                ->leftjoin('gr_merchant', 'gr_store.st_mer_id', '=', 'gr_merchant.id')
                ->orderBy('pro_id', 'desc')->count();
				$totalFiltered = $totalData;
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$pdtCode_search = trim($request->pdtCode_search);
				$pdtName_search = trim($request->pdtName_search);
				$storename_search = trim($request->storename_search);
				$merName_search = trim($request->merName_search);
				if($pdtCode_search=='' && $pdtName_search=='' && $storename_search=='' && $merName_search=='')
				{
					//DB::connection()->enableQueryLog();
					$posts = DB::table('gr_product')->select('gr_product.pro_item_code',
                    'gr_product.pro_item_name',
                    'gr_store.st_store_name',
                    'gr_merchant.mer_fname',
                    'gr_merchant.mer_lname',
                    'gr_product.pro_quantity',
                    'gr_product.pro_no_of_purchase',
                    'gr_product.pro_id'
					)
                    ->leftjoin('gr_store', 'gr_product.pro_store_id', '=', 'gr_store.id')
                    ->leftjoin('gr_merchant', 'gr_store.st_mer_id', '=', 'gr_merchant.id')
                    ->orderBy($order,$dir)->skip($start)->take($limit)->get();
					//$query = DB::getQueryLog();
					//print_r($query);
					//exit;
				}
				else {
					
					$sql = DB::table('gr_product')->select('gr_product.pro_item_code',
                    'gr_product.pro_item_name',
                    'gr_store.st_store_name',
                    'gr_merchant.mer_fname',
                    'gr_merchant.mer_lname',
                    'gr_product.pro_quantity',
                    'gr_product.pro_no_of_purchase',
                    'gr_product.pro_id'
					);
					if($pdtCode_search != '')
					{
						$q = $sql->whereRaw("gr_product.pro_item_code like '%".$pdtCode_search."%'");
					}
					if($pdtName_search != '')
					{
						$q = $sql->whereRaw("gr_product.pro_item_name like '%".$pdtName_search."%'");
					}
					if($storename_search != '')
					{
						$q = $sql->whereRaw("gr_store.st_store_name like '%".$storename_search."%'");
					}
					if($merName_search != '')
					{
						$q = $sql->whereRaw("CONCAT(if(gr_merchant.mer_fname is null,'',gr_merchant.mer_fname),' ',if(gr_merchant.mer_lname is null,'',gr_merchant.mer_lname)) like '%".$merName_search."%'");
					}
					$q = $sql->leftjoin('gr_store', 'gr_product.pro_store_id', '=', 'gr_store.id');
					$q = $sql->leftjoin('gr_merchant', 'gr_store.st_mer_id', '=', 'gr_merchant.id');
					//$q = $sql->orWhere('cate_name', 'LIKE','%'.$search.'%');
					//DB::connection()->enableQueryLog();
					$totalFiltered = $q->count();
					$q = $sql->orderBy($order,$dir)->skip($start)->take($limit);
					$posts =  $q->get();
					
					
				}
				$addQtyText = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_QTY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_QTY') : trans($this->ADMIN_LANGUAGE.'.ADMIN_ADD_QTY');
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					foreach ($posts as $post)
					{
						$nestedData['SNo'] = ++$snoCount;
						$nestedData['pdtCode'] = $post->pro_item_code;
						$nestedData['pdtName'] = $post->pro_item_name;
						$nestedData['storeName'] = $post->st_store_name;
						$nestedData['merName'] = $post->mer_fname.' '.$post->mer_lname;
						$nestedData['qty'] = '<span id="quantity_'.$post->pro_id.'">'.$post->pro_quantity.'</span>';
						$nestedData['soldQty'] = $post->pro_no_of_purchase;
						$nestedData['action'] = '<a href="javascript:show_pop('.$post->pro_id.');" >'.$addQtyText.'</a>';
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
		public function updat_quantity()
		{
			//echo Input::get('updatable_qty').'/'.Input::get('selectedPrdt_id');
			$update = updatevalues('gr_product',array('pro_quantity'=>Input::get('updatable_qty')),['pro_id' =>Input::get('selectedPrdt_id')]);
			echo $update;
		}
		
		/** download store category **/
		public function download_inventory_list($type)
		{
			if(Session::has('admin_id') == 1)
			{
				/*$main_type=2;*/
				$data = Reports::get_inventory_details();
				//print_r($get_items_details); exit;
				
				return Excel::create('Inventory list',function ($excel) use ($data)
				{
					$excel->sheet('Inventory_list', function ($sheet) use ($data)
					{
						$sheet->setFontFamily('Comic Sans MS');
						$sheet->cell('A2', function($cell) {$cell->setValue((Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($this->ADMIN_LANGUAGE.'.ADMIN_SNO'));   });
						$sheet->cell('B2', function($cell) {$cell->setValue('Item Code');});
						$sheet->cell('C2', function($cell) {$cell->setValue('Item Name');});
						$sheet->cell('D2', function($cell) {$cell->setValue('Restaurant Name');});
						$sheet->cell('E2', function($cell) {$cell->setValue('Merchant Name');});
						$sheet->cell('F2', function($cell) {$cell->setValue('Quantity');});
						$sheet->cell('G2', function($cell) {$cell->setValue('Sold Quantity');});
						
						//$sheet->getStyle('F2')->getAlignment()->setWrapText(true);
						$j=3;
						foreach ($data as $key => $value) {
							
							$i= $key+2;
							$sheet->cell('A'.$j, $j-2); //print serial no
							$sheet->cell('B'.$j, $value->pro_item_code);
							$sheet->cell('C'.$j, $value->pro_item_name);
							$sheet->cell('D'.$j, $value->st_store_name);
							$sheet->cell('E'.$j, $value->mer_fname.' '.$value->mer_lname);
							$sheet->cell('F'.$j, $value->pro_quantity);
							$sheet->cell('G'.$j, $value->pro_no_of_purchase);
							
							$j++;
						}
					});
				})->download($type);
			}
			else
			{
				return Redirect::to('admin-login');
			}
			
		}
		
	}	