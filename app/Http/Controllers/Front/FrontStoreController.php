<?php 
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Front;
	
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
	
	use App\Home;
	
	class FrontStoreController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			//$this->no_item = $this->no_item;
			
		}
		
		/** restaurant details **/
		public function store_detail(Request $request)
		{
			$id   = base64_decode($request->id);
			$name = $request->name;
			/** for loading ajax starts**/
			$mc_id = $request->mc_id;
			$sc_id = $request->sc_id;
			$st_id = $request->st_id;
			$sortby = $request->sortby;
			$text=$request->text;
			$html = '';
			$url = url('');
			$get_item_details = Home::get_items($id,$mc_id,$sc_id,$sortby,1,$text);
			$page_count_max = 0;
			//print_r($get_item_details); exit;
			if($request->ajax())
			{
				if(count($get_item_details) > 0)
				{	
					$page_count_max = $get_item_details->lastPage();
					$i=($get_item_details->currentpage()-1)*$get_item_details->perpage()+1;
					
					foreach($get_item_details as $detils)
					{	
						$popup_price = ($detils->pro_has_discount == 'yes') ? $detils->pro_discount_price : $detils->pro_original_price;
						$discount_price = ($detils->pro_has_discount == 'yes') ? $detils->pro_original_price : '';
						$available_text = (Lang::has(Session::get('front_lang_file').'.FRONT_AVAILABLE')) ? trans(Session::get('front_lang_file').'.FRONT_AVAILABLE') : trans($this->FRONT_LANGUAGE.'.FRONT_AVAILABLE');
						$instock_text = (Lang::has(Session::get('front_lang_file').'.FRONT_INSTOCK')) ? trans(Session::get('front_lang_file').'.FRONT_INSTOCK') : trans($this->FRONT_LANGUAGE.'.FRONT_INSTOCK');
						$ordernow_text = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_NOW')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_NOW') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_NOW');
						$moreDetail_text = (Lang::has(Session::get('front_lang_file').'.FRONT_MORE_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_MORE_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_MORE_DETAILS');
						
						if($detils->pro_has_discount == 'yes')
						{
							$pro_priceVar = $detils->pro_discount_price;
						}
						else
						{
							$pro_priceVar = $detils->pro_original_price;
						}
						
						if((($detils->pro_quantity <= $detils->pro_no_of_purchase)))
						{
							if(INVENTORY_STATUS == '1')
							{
								$html .='<div class="col-md-3 item-list-item overlay">';
							}
							else
							{
								continue 1;
							}
						}
						elseif($detils->pro_quantity > $detils->pro_no_of_purchase)
						{
							$html .='<div class="col-md-3 col-sm-4 col-xs-6 item-list-item">';
						}
						$html .='<div class="item-list-item1">';
						$img = explode('/**/',$detils->pro_images);
						$path = $url.'/public/images/noimage/'.$this->no_item;
						$prodInWishlist = DB::table('gr_wishlist')->where('ws_pro_id','=',$detils->pro_id)->where('ws_cus_id','=',Session::get('customer_id'))->first();
						if(count($img) > 0)
						{	
							$filename = public_path('images/store/products/').$img[0];
							if($img[0] != '' && file_exists($filename))
							{
								$path = $url.'/public/images/store/products/'.$img[0];
							}
							
						}
						$html .='<div class="item-list-item1-img">';
						$html .='<a href="'.url('').'/product-details/'.base64_encode($detils->pro_id).'"><img src="'.$path.'" alt="'.$detils->item_name.'"></a>';
						if(INVENTORY_STATUS == '1' && (($detils->pro_quantity <= $detils->pro_no_of_purchase)))
						{
							$html.='<div class="out-of-stock"><img src="'.$url.'/public/front/images/out-of-stock2.png"></div>';
						}
						$html .='</div>
						<div class="item-list-item1-content">					
						<h4>'.$detils->pro_currency.'&nbsp;';
						if($detils->pro_has_discount == 'yes') 
						{ 
							$html .=$detils->pro_discount_price.' <span>'.$detils->pro_original_price.'</span>';
						}
						else {
							$html.= $detils->pro_original_price;
						}  
						$html.='</h4>
						<p><a href="'.url('').'/product-details/'.base64_encode($detils->pro_id).'" data-toggle="tooltip" title="'.$detils->item_name.'!">'.ucfirst(str_limit($detils->item_name,20)).'</a>';
						$html .= '</p>
						<p>'.ucfirst(str_limit(strip_tags($detils->contains),20)).'';
						$html.='</p>';
						
						$html.='
						<a href="#" data-toggle="modal" data-target="#quickview-modal'.$detils->pro_id.'"><button><i class="fa fa-shopping-cart"></i> Order Now</button></a></div>
						<div class="quick-view">
						<div class="wishlist-item wishlist-item-store">';
						if(Session::has('customer_id')  == 1){
							if(empty($prodInWishlist)===true){
								// $html.='<a href="#" onclick="addtowish(\''.$detils->pro_id.'\',\'1\')"><i class="fa fa-heart-o"></i></a>';
								
								$html.='<a href="javascript:;" id="wishId_'.$detils->pro_id.'" onclick="addtowishlist_ajax('.$detils->pro_id.',1,\'add\',\'0\')"><i class="fa fa-heart-o"></i></a>';
								}else{
								// $html.='<a href="'.url('remove_wish_product').'/'.base64_encode($prodInWishlist->ws_id).'"><i class="fa fa-heart" aria-hidden="true"></i></a>';
								
								$html.='<a href="javascript:;" id="wishId_'.$detils->pro_id.'" onclick="addtowishlist_ajax('.$detils->pro_id.',1,\'remove\',\'0\')"><i class="fa fa-heart"></i></a>';
							}
							}else{
							
							$html.='<a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-heart-o"></i></a>';
						}
						
						
						$html.='</div>
						<div class="quick-search quick-search-store"><a href="#" data-toggle="modal" data-target="#quickview-modal'.$detils->pro_id.'"><i class="fa fa-search"></i></a>
						</div>
						</div>
						</div>
						
						</div>';
						/* Quick view modal */					
						
						$html.='	<div id="quickview-modal'.$detils->pro_id.'" class="modal fade quickview-modal-rest quickview-modal-store" role="dialog">
						
						
						<div class="modal-dialog">
						<button type="button" class="close" data-dismiss="modal" onclick="clear_data('.$i.')">&times;</button>
						<!-- Modal content-->
						<div class="modal-content">
						
						<div class="modal-body">
						<div class="row">
						<div class="col-md-12 quickview-content">	
						
						<form method="POST" action="'.url('add_cart_product').'" accept-charset="UTF-8" onsubmit="return chk_qty('.$i.')">
						<input name="_token" type="hidden" value="'.csrf_token().'">
						
						<div class="row">
						<div class="col-md-5 quickview-img">
						<img src="'.$path.'" alt="">	
						</div>
						<div class="col-md-7 quickview-text">
						<div class="product-name">
						<h3>'.ucfirst($detils->item_name).'</h3>
						<p>'.$detils->pro_per_product.'</p>
						</div>
						<div class="price-box">
						<h4>'.$detils->pro_currency.' '.$popup_price.'
						<span>'.$discount_price.'</span>
						</h4>
						</div>
						<div class="ratings">
						<div class="rating">';
						if($detils->pro_rating != '') 
						{
							for($i=1;$i<=$detils->pro_rating;$i++)
							{
								$html .='<i class="fa fa-star checked">';
							}
						}
						$html .='
						</div>
						<p class="">  '.$available_text.' : 
						<span>  '.($detils->pro_quantity - $detils->pro_no_of_purchase).'&nbsp;'.$instock_text.' </span></p>
						</div>
						<div class="add-minus-cart">
						
						<div id="input_div">    
						<button id="moins'.$i.'" onclick="minus('.$i.')" type="button">-</button>
						<input id="count'.$i.'" maxlength="5" class="quantity" name="qty" type="text" value="1">
						<button id="plus'.$i.'" onclick="plus('.$i.','.($detils->pro_quantity - $detils->pro_no_of_purchase).')" type="button">+</button>
						</div>
						<input name="pro_price" type="hidden" value="'.$pro_priceVar.'" id="pro_price_hid'.$i.'">
						<input name="st_id" type="hidden" value="'.$detils->pro_store_id.'" id="store_id'.$i.'">
						<input name="item_id" type="hidden" value="'.$detils->pro_id.'" id="product_id'.$i.'">
						<input name="currency" type="hidden" value="'.$detils->pro_currency.'" id="currency_hid'.$i.'">
						<input name="tax" type="hidden" value="'.$detils->pro_tax_percent.'" id="tax_hid'.$i.'">
						<input id="max_qty'.$i.'" name="max" type="hidden" value="'.($detils->pro_quantity - $detils->pro_no_of_purchase).'">
						<div class="order-now-btn">';
						if(Session::has('customer_id') == 1)
						{
							$html .='<img src="'.url('').'/public/images/spinning-loading-bar.gif" style="max-width:61px;display:none" id="loader_'.$i.'" /><input type="button" value="'.$ordernow_text.'" onclick="addToCart(\''.$i.'\');" id="orderNowBtn'.$i.'"/>';
						}
						else
						{
							///$html .='<a href="#" data-toggle="modal" data-target="#myModal"><input type="button" value="'.$ordernow_text.'" /></a>';
							$html .='<a href="javascript:myLoginFun(\'quickview-modal'.$detils->pro_id.'\');" ><input type="button" value="'.$ordernow_text.'" /></a>';
						}
						$html .='
						</div>
						<div id="err'.$i.'" style="float:left;width:100%;font-size: 13px;"></div>
						</div>
						</div>
						
						<div class="col-md-12 short-description">																	  
						<p>
						'.ucfirst(str_limit(strip_tags($detils->desc),250)).'
						</p>									
						<a href="'.url('').'/product-details/'.base64_encode($detils->pro_id).'">'.$moreDetail_text.'</a>
						</div>
						
						</div>
						
						</form>
						
						</div>
						</div>
						</div>
						
						</div>
						
						</div>
						</div>
						</div>	
						
						';
						/*<!-- EOF QUICK VIEW MODEL -->*/
						$i++;
					}	
				}
				$html .='
				<script>
				function myLoginFun(myModalId)
				{
				$("#"+myModalId).modal("hide");
				$("#myModal").modal("show");
				
				}
				</script>
				';
				return $html.'~`'.$page_count_max;
			}
			/** for loading ajax ends **/
			$get_details 		  = Home::get_shop_details($id);
			$get_category_details = Home::get_categories($id,2);
			
			$get_reviews 		  = Home::get_shop_review($id,'store');
			
			return view('Front.store_detail')->with(['name' => $name,'get_category_details' => $get_category_details,'shop_details' => $get_details,'all_reviews' => $get_reviews]);
		}
		/*AUTO COMPLETE */
		public function getItemName(Request $request)
		{
			$requestsR=$request->all();
			$ca_id=$requestsR['ca_id'];
			$st_id=$requestsR['st_id'];
			$sub_ca_id=$requestsR['sub_ca_id'];
			$query=$requestsR['query'];
			$get_item_details = Home::get_items_autocomplete($st_id,$ca_id,$sub_ca_id,$query,'1');
			$item_name_array=array();
			foreach($get_item_details as $itemnames)
			{
				array_push($item_name_array,$itemnames->item_name);
			}
			echo json_encode($item_name_array);
			//Array ( [ca_id] => 2 [st_id] => 20 [sub_ca_id0] => [query] => t )
		}
		/* EOF AUTO COMPLETE */
		public function product_details(Request $request)
		{
			$pro_id=base64_decode($request->id);
			$get_product_details = Home::get_product_details($pro_id,'1');
			$get_specifi_details = DB::table('gr_product_spec')->select('spec_title','spec_desc')->where('spec_pro_id','=',$pro_id)->get();
			$get_review_details  = Home::get_product_reviews($pro_id,'product');
			$related_pdtDetails  = Home::get_relatedPdt_details($pro_id,$get_product_details->pro_category_id,'1');
			//print_r($related_pdtDetails); exit;
			return view('Front.product_details')->with(['get_product_details' => $get_product_details,'get_specifi_details' => $get_specifi_details,'get_review_details' => $get_review_details,'related_pdtDetails'=>$related_pdtDetails]);
		}
		
		/**  add to cart **/
		public function add_cart_product(Request $request)
		{	
			/*print_r($request->all()); exit; Array ( [pro_price] => 4.20 [item_id] => 1 [qty] => 1 [st_id] => 1 [tax] => [pro_currency] => $ )*/
			
			$cus_id 	= Session::get('customer_id');
			$item_id 	= $request->item_id;
			$qty 		= $request->qty;
			$st_id 		= $request->st_id;
			$tax		= $request->tax;
			$pro_currency 	= $request->currency;
			$had_ch 		= 'No';
			$pro_price 		= $request->pro_price; 
			$max_qty 	= $request->max_qty; 
			$cartKount = cart_count($cus_id);
			$ch_array = array();
			/** check quantity **/
			$available = check_qty($item_id);
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EXCEED_LIMIT');
			if(empty($available) === false)
			{
				if($available->stock < $qty)
				{	
					//Session::flash('val_errors',"Quantity Exceeds Stock Limit");
					//return Redirect::back();
					echo '0`'.$msg.'`'.$cartKount;
					exit;
				}
			}
			else
			{
				echo '0`'.$msg.'`'.$cartKount;
				exit;
			}
			
			
			$check_cart = DB::table('gr_cart_save')->selectRaw('SUM(cart_quantity) as added_qty')->where(['cart_st_id' => $st_id,'cart_item_id' => $item_id,'cart_cus_id' => $cus_id])->pluck('added_qty');
			$total_count = $qty+$check_cart[0];
			if($total_count > $max_qty)
			{
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EXCEED_LIMIT');
				$msg .='. ';
				$msg .=(Lang::has(Session::get('front_lang_file').'.FRONT_HAVE_CART')) ? trans(Session::get('front_lang_file').'.FRONT_HAVE_CART') : trans($this->FRONT_LANGUAGE.'.FRONT_HAVE_CART');
				$msg .= ' '.$check_cart[0];
				echo '0`'.$msg.'`'.$cartKount;
				exit;
			}
			
			/** check item with choice already added **/
			//echo $cus_id.','.$st_id.','.$item_id.','.$ch_array.','.$pro_currency;
			//exit;
			$check 	  = check_cart($cus_id,$st_id,$item_id,json_encode($ch_array),$pro_currency);
			
			if(empty($check) === false)   //update cart
			{	
				$quantity = $check->cart_quantity + $qty;
				$tax = ((($pro_price * $quantity) * $tax)/100);
				$insert_arr = [	'cart_quantity' 	=> $quantity,
				'cart_unit_amt'		=> $pro_price,
				'cart_currency'		=> $pro_currency,
				'cart_tax'			=> $tax,
				'cart_total_amt' 	=> (($pro_price * $quantity) + $tax),
				'cart_updated_at'	 => date('Y-m-d H:i:s')];
				
				$insert = updatevalues('gr_cart_save',$insert_arr,['cart_cus_id' => $cus_id,'cart_id' => $check->cart_id,'cart_st_id' => $st_id]);
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CART_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CART_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.FRONT_CART_UPDATE_SUCCESS');
				$cartKount = cart_count($cus_id);
				//Session::flash('success',$msg);
				echo '1`'.$msg.'`'.$cartKount;
			}
			else 	// add new cart
			{	
				$tax = ((($pro_price * $qty) * $tax)/100);
				$insert_arr = [	'cart_cus_id' 		=> $cus_id,
				'cart_st_id' 		=> $st_id,
				'cart_item_id'		=> $item_id,
				'cart_had_choice'	=> $had_ch,
				'cart_quantity' 	=> $qty,
				'cart_unit_amt'		=> $pro_price,
				'cart_total_amt' 	=> (($pro_price * $qty) + $tax),
				'cart_currency'		=> $pro_currency,
				'cart_tax'			=> $tax,
				'cart_type' 		=> 1,	//product cart 
				'cart_choices_id' 	=> json_encode($ch_array),
				'cart_updated_at' 	=> date('Y-m-d H:i:s')];
				
				$insert = insertvalues('gr_cart_save',$insert_arr);
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CART_ADD_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CART_ADD_SUCCESS') : trans($this->FRONT_LANGUAGE.'.FRONT_CART_ADD_SUCCESS');
				//Session::flash('success',$msg);
				$cartKount = cart_count($cus_id);
				echo '1`'.$msg.'`'.$cartKount;
			}
			//return Redirect::back();
		}
		
		
	}		