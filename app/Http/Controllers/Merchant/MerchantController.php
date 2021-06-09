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
	
	class MerchantController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			$this->setLanguageLocaleMerchant();
		}
		
		public function order_notification(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{ 
				$page_title = (Lang::has(Session::get('mer_lang_file').'.MER_ORDER_NOTIFICATION')) ? trans(Session::get('mer_lang_file').'.MER_ORDER_NOTIFICATION') : trans($this->MER_OUR_LANGUAGE.'.MER_ORDER_NOTIFICATION');
				return view('sitemerchant.reports.order_notification')->with('pagetitle',$page_title);
				}else{
				return Redirect::to('merchant-login');
			}
		}
		
		public function order_notification_ajax(Request $request)
		{
			if(Session::has('merchantid') == 1)
			{	
				$columns = array(   0 => 'read_status', 
				1 => 'order_id', 
				2 => 'message', 
				3 => 'updated_at',
				4 => 'id'
				);
				/*To get Total count */
				$totalData = DB::table('gr_general_notification')
				->select('id')
				->where('receiver_id','=',Session::get('merchantid'))
				->where('receiver_type','=','gr_merchant')
				->count();
				$totalFiltered = $totalData; 
				/*EOF get Total count */
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = $columns[$request->input('order.0.column')];
				$dir = $request->input('order.0.dir');
				
				//if(empty($request->input('search.value')))
				$view_search = trim($request->view_search); 
				$orderId_search = trim($request->orderId_search); 
				$message_search = trim($request->message_search); 
				
				//DB::connection()->enableQueryLog();
				$sql = DB::table('gr_general_notification')
				->select('id',
				'order_id',
				'message',
				'message_link',
				'updated_at',
				'read_status'
				)
				->where('receiver_id','=',Session::get('merchantid'))
				->where('receiver_type','=','gr_merchant');
				if($orderId_search != '')
				{
					/*$q = $sql->whereRaw("order_id like '%".$orderId_search."%'"); */
					$q = $sql->whereRaw("order_id like ?", ['%'.$orderId_search.'%']); 
				}
				if($message_search != '')
				{
					/*$q = $sql->whereRaw("message like '%".$message_search."%'"); */
					$q = $sql->whereRaw("message like ?", ['%'.$message_search.'%']); 
				}
				if($view_search != '')
				{
					$q = $sql->where('read_status','=',$view_search); 
				}
				$totalFiltered = $sql->count();
				//DB::connection()->enableQueryLog();
				$q = $sql->orderBy($order,$dir)->orderBy('read_status', 'ASC')->skip($start)->take($limit);
				$posts =  $q->get();
				/*$query = DB::getQueryLog();
					print_r($query);
				exit;*/
				
				$data = array();
				if(!empty($posts))
				{
					$snoCount = $start;
					$view = (Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_VIEW');
					$read = (Lang::has(Session::get('mer_lang_file').'.MER_READ_NOTIFICATION')) ? trans(Session::get('mer_lang_file').'.MER_READ_NOTIFICATION') : trans($this->MER_OUR_LANGUAGE.'.MER_READ_NOTIFICATION');
					$unread = (Lang::has(Session::get('mer_lang_file').'.MER_UNREAD_NOTIFICATION')) ? trans(Session::get('mer_lang_file').'.MER_UNREAD_NOTIFICATION') : trans($this->MER_OUR_LANGUAGE.'.MER_UNREAD_NOTIFICATION');
					foreach ($posts as $post)
					{
						$nestedData['SNo'] 		= ++$snoCount;
						$nestedData['orderId'] 	= $post->order_id;
						$nestedData['message'] 	= $post->message;
						$nestedData['readstatus'] 	= ($post->read_status==0)?$unread:$read;
						$nestedData['date'] 	= date('m/d/Y H:i:s',strtotime($post->updated_at));
						$nestedData['view'] 	= '<a href="'.url('').'/'.$post->message_link.'" onclick="change_status(\''.$post->id.'\')" -target="_blank">'.$view.'</a>';
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
		
		public function status_change_notification(Request $request){
			//print_r($request->all());
			$gotId = $request->gotId;
			//echo $gotId;
			return DB::table('gr_general_notification')->where('id','=',$gotId)->update(['read_status' => '1']);
		}
	}							