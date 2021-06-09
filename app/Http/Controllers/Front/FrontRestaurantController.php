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
	//use Request;
	
	class FrontRestaurantController extends Controller
	{
		//static $LANGUAGE;
		
		public function __construct(Request $request)
		{
			parent::__construct();
			//$this->no_item = $this->no_item;
			//echo static $LANGUAGE = \Request::get('FR_LANG'); exit;
			/*echo "tst";
			echo \Request::get('FR_LANG'); exit;*/
			// echo $request->get('FR_LANG');
			// $this->FRONT_LANGUAGE =  $request->get('FR_LANG');//\Request::get('FR_LANG'); 
			//echo $this->FRONT_LANGUAGE; exit;
			
		}
		
		public function get_lang()
		{
			$LANGUAGE =  \Request::get('FR_LANG'); 
			return $LANGUAGE;
		}
		
		/** restaurant details **/
		public function restaurant_detail(Request $request)
		{
			//print_r($request->name); exit;
			//$id = base64_decode($request->id);
			$slug = mysql_escape_special_chars($request->name);
			$id=DB::table('gr_store')->where('store_slug','=',$slug)->first()->id;
			$name = $request->name;
			$shop_status = Home::check_shop_status($id,'1','');
			//echo '<pre>'; print_r($shop_status); exit;
			if(count($shop_status)=='0'){
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REST_NOTAVAIL')) ? trans(Session::get('front_lang_file').'.FRONT_REST_NOTAVAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_REST_NOTAVAIL');
				return view('Front.general_error')->with(['error_msg' => $msg ]);
				}else{
				/** for loading ajax starts**/
				$mc_id = '';
				$sc_id = '';
				
				if ($request->has('mc_id'))
				$mc_id = $request->mc_id;
				
				if ($request->has('sc_id'))
				$sc_id = $request->sc_id;
				
				$st_id 	= $request->st_id;
				$sortby = $request->sortby;
				$text 	= $request->text;
				$page_count_max = 0;
				$html 	= '';
				$url 	= url('');
				$pro_veg = $request->get('pro_veg');
				$top_disc = $request->get('top_disc');
				$get_item_details = Home::get_items($id, $mc_id, $sc_id, $sortby, 2, $text, $pro_veg,$top_disc);   //type - 2 for items
				//print_r($get_item_details); exit;
				$noiteams_found = (Lang::has(Session::get('front_lang_file').'.FRONT_NO_ITEM_FOUND')) ? trans(Session::get('front_lang_file').'.FRONT_NO_ITEM_FOUND') : trans($this->FRONT_LANGUAGE.'.FRONT_NO_ITEM_FOUND');
				$count = $get_item_details->total();
				if ($count == 0) {
					$html .= '<div class="infoMsg"><img src="' . $url . '/public/front/images/empty-cart.png" alt="No item found"><br> '.$noiteams_found.'</div>';
				}
				$item_count = 0;
				if($request->ajax())
				{
					if(count($get_item_details) > 0)
					{
						$i=($get_item_details->currentpage()-1)*$get_item_details->perpage()+1;
						$item_count = count($get_item_details);
						$page_count_max = $get_item_details->lastPage();
						$get_delevery  = Home::get_delevery_time($id);
						if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en'){
							$duration = $get_delevery->st_delivery_duration;
							}else{
							if($get_delevery->st_delivery_duration == 'hours'){
								$duration = (Lang::has(Session::get('front_lang_file').'.FRONT_HOURS')) ? trans(Session::get('front_lang_file').'.FRONT_HOURS') : trans($this->FRONT_LANGUAGE.'.FRONT_HOURS');
								}elseif($get_delevery->st_delivery_duration == 'minutes'){
								$duration = (Lang::has(Session::get('front_lang_file').'.FRONT_MINUTES')) ? trans(Session::get('front_lang_file').'.FRONT_MINUTES') : trans($this->FRONT_LANGUAGE.'.FRONT_MINUTES');
							}
						}
						
						foreach($get_item_details as $detils)
						{
							if($detils->pro_veg==1)
							$veg=(Lang::has(Session::get('front_lang_file').'.FRONT_PRO_VEG')) ? trans(Session::get('front_lang_file').'.FRONT_PRO_VEG') : trans($this->FRONT_LANGUAGE.'.FRONT_PRO_VEG');
							else
							$veg=(Lang::has(Session::get('front_lang_file').'.FRONT_PRO_NONVEG')) ? trans(Session::get('front_lang_file').'.FRONT_PRO_NONVEG') : trans($this->FRONT_LANGUAGE.'.FRONT_PRO_NONVEG');
							
							$popup_price 	= ($detils->pro_has_discount == 'yes') ? $detils->pro_discount_price : $detils->pro_original_price;
							$discount_price = ($detils->pro_has_discount == 'yes') ? $detils->pro_original_price : '';
							$available_text = (Lang::has(Session::get('front_lang_file').'.FRONT_AVAILABLE')) ? trans(Session::get('front_lang_file').'.FRONT_AVAILABLE') : trans($this->FRONT_LANGUAGE.'.FRONT_AVAILABLE');
							$instock_text 	= (Lang::has(Session::get('front_lang_file').'.FRONT_INSTOCK')) ? trans(Session::get('front_lang_file').'.FRONT_INSTOCK') : trans($this->FRONT_LANGUAGE.'.FRONT_INSTOCK');
							$ordernow_text 	= (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_NOW')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_NOW') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_NOW');
							$moreDetail_text= (Lang::has(Session::get('front_lang_file').'.FRONT_MORE_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_MORE_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_MORE_DETAILS');
							
							$preOr_text = (Lang::has(Session::get('front_lang_file').'.FRONT_PRE_OR_AVAIL')) ? trans(Session::get('front_lang_file').'.FRONT_PRE_OR_AVAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_PRE_OR_AVAIL');
							$veg_txt  	= (Lang::has(Session::get('front_lang_file').'.FRONT_PRO_VEG')) ? trans(Session::get('front_lang_file').'.FRONT_PRO_VEG') : trans($this->FRONT_LANGUAGE.'.FRONT_PRO_VEG');
							$Non_veg_txt= (Lang::has(Session::get('front_lang_file').'.FRONT_PRO_NONVEG')) ? trans(Session::get('front_lang_file').'.FRONT_PRO_NONVEG') : trans($this->FRONT_LANGUAGE.'.FRONT_PRO_NONVEG');
							$no_Ratings = (Lang::has(Session::get('front_lang_file').'.FRONT_NO_RATINGS')) ? trans(Session::get('front_lang_file').'.FRONT_NO_RATINGS') : trans($this->FRONT_LANGUAGE.'.FRONT_NO_RATINGS');
							
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
									$html .='<div class="col-md-12 item-list-item overlay">';
								}
								else
								{
									continue 1;
								}
							}
							elseif($detils->pro_quantity > $detils->pro_no_of_purchase)
							{
								$html .='<div class="col-md-12 item-list-item">';
							}
							
							
							$html .='<div class="item-list-item1">';
							$img 	= explode('/**/',$detils->pro_images);
							$path 	= $url.'/public/images/noimage/'.$this->no_item;
							$prodInWishlist = DB::table('gr_wishlist')->where('ws_pro_id','=',$detils->pro_id)->where('ws_cus_id','=',Session::get('customer_id'))->first();
							if(count($img) > 0)
							{
								$filename = public_path('images/restaurant/items/').$img[0];
								if($img[0] != '' && file_exists($filename))
								{
									$path = $url.'/public/images/restaurant/items/'.$img[0];
								}
								
							}
							
							$html .='<div class="item-list-item1-img">';
							$html .='<a href="'.url('').'/'.$detils->store_slug.'/item-details/'.$detils->pro_item_slug.'" id="list_item_image_'.$detils->pro_id.'"><img src="'.$path.'" alt="'.$detils->item_name.'" ></a>';
							if(INVENTORY_STATUS == '1' && (($detils->pro_quantity <= $detils->pro_no_of_purchase)))
							{
								$html.='<div class="out-of-stock"><img src="'.$url.'/public/front/images/out-of-stock2.png"></div>';
							}
							$html.='</div>
							<div class="item-list-item1-des">
							
							<a href="'.url('').'/'.$detils->store_slug.'/item-details/'.$detils->pro_item_slug.'" >
							<h3><span>'.ucfirst(str_limit(strtoupper($detils->item_name),100)).'</span></span>('.$detils->contains.')</h3>						
							
							<p>'.ucfirst(str_limit(strip_tags($detils->desc),100)).'</p>
							<ul>
							';
							
							if($detils->avg_val != '')
							{
								
								if($detils->avg_val == '5'){
									
									$html .='<li style="left:40%;"class="star-des"><img src="'.$url.'/public/front/images/star1.png"> <span>'.intval($detils->avg_val).'</span> </li>';
								}elseif(filter_var($detils->avg_val, FILTER_VALIDATE_INT) && $detils->avg_val != '5'){
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"> <span>'.intval($detils->avg_val).'.0</span> </li>';
								}else{
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"> <span>'.intval($detils->avg_val).'</span> </li>';
								}
							}
							else {
								
								$html.= '<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"> <span><i title="'.$no_Ratings.'" class="fa fa-star-o" aria-hidden="true"></i></span> </li>';
							}
							$html.='
							';
							if($detils->pro_veg == '1')
							{
								
								$html .='<li><img src="'.$url.'/public/front/images/veg-icon.png">
								'.$veg_txt.'</li>';
								
							}
							else {
								
								$html.= '<li><img src="'.$url.'/public/front/images/non-veg-icon.png">
								'.$Non_veg_txt.'</li>';
							}
							
							$html.='
							
							
							<li><img src="'.$url.'/public/front/images/minutes-icon.png"> '.$get_delevery->st_delivery_time.' '.$duration.' </li>
							</ul></a>
							</div>
							<div class="item-list-item1-content">
							
							
							';
							if(Session::has('customer_id')  == 1){
								if(empty($prodInWishlist)===true){
									$html.='<a href="javascript:;" id="wishId_'.$detils->pro_id.'" onclick="addtowishlist_ajax('.$detils->pro_id.',2,\'add\',\'0\')"><i class="fa fa-heart-o"></i></a>';
									}else{
									$html.='<a href="javascript:;" id="wishId_'.$detils->pro_id.'" onclick="addtowishlist_ajax('.$detils->pro_id.',2,\'remove\',\'0\')"><i class="fa fa-heart"></i></a>';
								}
								}else{
								$html.='<div><a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-heart-o"></i></a></div>';
							}
							
							
							$html.='
							<div><h4>'.$detils->pro_currency.'&nbsp;';
							if($detils->pro_has_discount == 'yes')
							{
								$html .=$detils->pro_discount_price.'<span>'.$detils->pro_currency.'&nbsp;'.$detils->pro_original_price.'</span>';
							}
							else {
								$html.= $detils->pro_original_price;
							}
							$ordernow_text = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_NOW')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_NOW') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_NOW');
							if(($detils->pro_quantity <= $detils->pro_no_of_purchase))
							{
								$ordernow_text = (Lang::has(Session::get('front_lang_file').'.FRONT_OUT_OF_STK')) ? trans(Session::get('front_lang_file').'.FRONT_OUT_OF_STK') : trans($this->FRONT_LANGUAGE.'.FRONT_OUT_OF_STK');
							}
							$html.='</h4></div>';
							if(($detils->pro_quantity <= $detils->pro_no_of_purchase))
							{
								$ordernow_text = (Lang::has(Session::get('front_lang_file').'.FRONT_OUT_OF_STK')) ? trans(Session::get('front_lang_file').'.FRONT_OUT_OF_STK') : trans($this->FRONT_LANGUAGE.'.FRONT_OUT_OF_STK');
								$html.='<div><a href="#" data-toggle="modal" data-target="#quickview-modal'.$detils->pro_id.'"><button class="btn btn-danger"><i class="fa fa-shopping-cart"></i> '.$ordernow_text.'</button></a></div></div>';
							}
							else
							{
								$ordernow_text = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_NOW')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_NOW') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_NOW');
								$html.='<div><a href="#" data-toggle="modal" data-target="#quickview-modal'.$detils->pro_id.'"><button><i class="fa fa-shopping-cart"></i> '.$ordernow_text.'</button></a></div></div>';
							}
							
							
							$html.='<div class="quick-view">
							<div class="wishlist-item">';
							if(Session::has('customer_id')  == 1){
								if(empty($prodInWishlist)===true){
									$html.='<a href="javascript:;" id="wishId_'.$detils->pro_id.'" onclick="addtowishlist_ajax('.$detils->pro_id.',2,\'add\',\'0\')"><i class="fa fa-heart-o"></i></a>';
								}else{
									$html.='<a href="javascript:;" id="wishId_'.$detils->pro_id.'" onclick="addtowishlist_ajax('.$detils->pro_id.',2,\'remove\',\'0\')"><i class="fa fa-heart"></i></a>';
								}
							}else{
								$html.='<a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-heart-o"></i></a>';
							}
							$html.='</div>
							<div class="quick-search"><a href="#" data-toggle="modal" data-target="#quickview-modal'.$detils->pro_id.'"><i class="fa fa-search"></i></a>
							</div>
							</div>
							</div>
							</div>';
							
							/* Quick view modal */
							if($detils->pro_veg==1)
							$veg="Veg";
							else
							$veg="Non-veg";
							
							$html.='	<div id="quickview-modal'.$detils->pro_id.'" class="modal fade quickview-modal-rest" role="dialog">							
							
							<div class="modal-dialog">							
							
							<!-- Modal content-->
							<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="clear_data('.$i.')">&times;</button></div>
							<div class="modal-body">
							<div class="row">
							<div class="col-md-12 quickview-content">	
							
							<form method="POST" action="'.url('').'/add_cart_item" accept-charset="UTF-8">
							<input name="_token" type="hidden" value="'.csrf_token().'">
							
							<div class="row">
							<div class="col-md-4 col-sm-4 quickview-img" id="item_quick_image_'.$i.'">
							
							<img src="'.$path.'" alt="">	
							</div>
							<div class="col-md-8 col-sm-8 quickview-text">
							<div class="product-name">
							<h3><span>'.ucfirst($detils->item_name).'</span><span>('.$detils->contains.')</span></h3>
							
							<p>'.str_limit(strip_tags($detils->desc),100).'</p>
							<a href="'.url('').'/'.$detils->store_slug.'/item-details/'.$detils->pro_item_slug.'">'.$moreDetail_text.'</a>
							</div>
							<div class="price-box">
							<h4>'.$detils->pro_currency.' '.$popup_price.'
							<span>'.$discount_price.'</span>
							</h4>
							<div id="input_div">    
							<button id="moins'.$i.'" onclick="minus('.$i.')" type="button">-</button>
							<input id="count'.$i.'" maxlength="5" class="changeQty quantity" name="qty" type="text" value="1" onkeypress="return isNumberKey(event)" onchange="checkQuantity(this.value,'.$i.')" >
							<button id="plus'.$i.'" onclick="plus('.$i.','.($detils->pro_quantity - $detils->pro_no_of_purchase).')" type="button">+</button>
							</div>
							</div>';
							if($detils->store_closed =="Closed" && $detils->st_pre_order==1)
							{
								$html .='<div>
								<img src="'.url('').'/public/front/images/shop_closed.png" style="width:100px;height:100px;float:left">											<p style="color:brown;float:right;padding:40px;padding-right:0px;">'.$preOr_text.'
								</p>
								</div>
								';
							}
							//    $html .='	<div class="ratings">
							// <div class="rating">';
							//    if($detils->pro_rating != '')
							//    {
							//        for($i=1;$i<=$detils->pro_rating;$i++)
							//        {
							//            $html .='<i class="fa fa-star checked">';
							//        }
							//    }
							//    $html .='</div>';
							$html .='<div class="ratings">	<p class="">'.$available_text.' :<span>  '.($detils->pro_quantity - $detils->pro_no_of_purchase).'&nbsp;'.$instock_text.' </span></p></div>';
							if($detils->pro_had_choice == '1')
							{
								$html .='<div class="multi-select">';
								$get_choice = get_choices($detils->pro_id);
								if(count($get_choice) > 0)
								{
									foreach($get_choice as $choice)
									{
										if($choice->pc_price==''){ $choice_pc_price='0.00'; } else{ $choice_pc_price=$choice->pc_price; }
										$html .='<label class="multi-checkbox">'.$choice->ch_name.' <span>'.$detils->pro_currency.'&nbsp;'.$choice_pc_price.'</span><input type="checkbox" name="choice_list'.$i.'[]" value="'.$choice->ch_id.'"><span class="checkmark"></span></label>';
									}
								}
								$html .='</div>';
							}
							$html .='<div class="add-minus-cart">
							<!--<div id="input_div">    
							<button id="moins'.$i.'" onclick="minus('.$i.')" type="button">-</button>
							<input id="count'.$i.'" maxlength="5" class="quantity" name="qty" type="text" value="1">
							<button id="plus'.$i.'" onclick="plus('.$i.','.($detils->pro_quantity - $detils->pro_no_of_purchase).')" type="button">+</button>
							</div>-->
							<input name="pro_price" type="hidden" value="'.$pro_priceVar.'" id="pro_price_hid'.$i.'">
							<input name="st_id" type="hidden" value="'.$detils->pro_store_id.'" id="store_id'.$i.'">
							<input name="item_id" type="hidden" value="'.$detils->pro_id.'" id="product_id'.$i.'">
							<input name="currency" type="hidden" value="'.$detils->pro_currency.'" id="currency_hid'.$i.'">
							<input name="tax" type="hidden" value="'.$detils->pro_tax_percent.'" id="tax_hid'.$i.'">
							<input id="max_qty'.$i.'" name="max" type="hidden" value="'.($detils->pro_quantity - $detils->pro_no_of_purchase).'">
							<div class="order-now-btn">
							
							';
							if($detils->store_closed =="Closed" && $detils->st_pre_order==0)
							{
								$html .='<span style="color:#7f1900">';
								$html .=(Lang::has(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED')) ? trans(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED') : trans($this->FRONT_LANGUAGE.'.FRONT_SORRY_WEARE_CLOSED');
								$html .='</span>';
								// @lang(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED');
							}
							else
							{
								if(Session::has('customer_id') == 1)
								{
									// $html .='<img src="'.url('').'/public/images/spinning-loading-bar.gif" style="max-width:61px;display:none" id="loader_'.$i.'" /><input type="button" value="'.$ordernow_text.'" onclick="addToCart(\''.$i.'\');" id="orderNowBtn'.$i.'"/>';
									
									$html .='<img src="'.url('').'/public/images/spinning-loading-bar.gif" style="max-width:61px;display:none" id="loader_'.$i.'" /><input type="button" value="'.$ordernow_text.'" onclick="addToCart(\''.$i.'\');" id="orderNowBtn'.$i.'"/>';
									//$html .='<input type="submit" value="'.$ordernow_text.'" />';
								}
								else
								{
									$html .='<a href="javascript:myLoginFun(\'quickview-modal'.$detils->pro_id.'\');" ><input type="button" value="'.$ordernow_text.'" /></a>';
								}
							}
							$html .='</div>
							
							</div>
							</div>
							<div style="width: 100%;margin: 0px 15px;">
							<div id="err'.$i.'" style="font-size: 16px;font-weight:bold;text-align: center;"></div>
							</div>
							<div class="col-md-12">	
							
							<div class="order-now-btn">';
							if($detils->store_closed =="Closed" && $detils->st_pre_order==0)
							{
								$html .='<span style="color:#7f1900">';
								$html .=(Lang::has(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED')) ? trans(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED') : trans($this->FRONT_LANGUAGE.'.FRONT_SORRY_WEARE_CLOSED');
								$html .='</span>';
								// @lang(Session::get('front_lang_file').'.FRONT_SORRY_WEARE_CLOSED');
							}
							else
							{
								if(Session::has('customer_id') == 1)
								{
									$html .='<img src="'.url('').'/public/images/spinning-loading-bar.gif" style="max-width:61px;display:none" id="loader_'.$i.'" /><input type="button" value="'.$ordernow_text.'" onclick="addToCart(\''.$i.'\');" id="orderNowBtnn'.$i.'"/>';
									//$html .='<input type="submit" value="'.$ordernow_text.'" />';
								}
								else
								{
									$html .='<a href="javascript:myLoginFun(\'quickview-modal'.$detils->pro_id.'\');" ><input type="button" value="'.$ordernow_text.'" /></a>';
								}
							}
							$html .='</div>					
							<div class="order-now-btn continue-btn">
							
							<!--<a href="#"><input type="submit" value="CONTINUE"></a>-->
							</div>
							<div class="short-description">
							<p>
							'.ucfirst(str_limit(strip_tags($detils->desc),250)).'
							</p>									
							<a href="'.url('').'/'.$detils->store_slug.'/item-details/'.$detils->pro_item_slug.'">'.$moreDetail_text.'</a>
							</div>
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
					
					return $html.'~`'.$page_count_max.'~`'.$item_count;
				}
				//print_r($get_item_details); exit;
				
				/** for loading ajax ends **/
				$get_details 		  = Home::get_shop_details($id);
				//echo '<pre>'; print_r($get_details); exit;
				$get_category_details = Home::get_categories($id,1);
				$get_wk_hrs 		  = get_working_hours($id);
				$get_reviews 		  = Home::get_shop_review($id,'restaurant');
				return view('Front.restaurant_detail')->with(['name' => $name,'get_category_details' => $get_category_details,'shop_details' => $get_details,'get_wk_hrs' => $get_wk_hrs,'all_reviews' => $get_reviews]);
			}
		}
		
		public function get_pro_count(Request $request){
			$mc_id 	= input::get('mc_id');
			$st_id 	= input::get('st_id');
			$sc_id 	= input::get('sc_id');
			$text 	= input::get('text');
			$pro_veg= input::get('pro_veg');
			$name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			$general_set = DB::table('gr_general_setting')->select('gs_show_inventory')->first();
			if(empty($general_set)===false){
				$inventory_status =$general_set->gs_show_inventory;
				}else{
				$inventory_status ='0';
			}
			if($sc_id == '' || $sc_id == 0)
			{
				//DB::connection()->enableQueryLog();
				$sql = DB::table('gr_product')->where('pro_category_id','=',$mc_id)->where('pro_store_id','=',$st_id);
				if($inventory_status == '0')
				{
					$sql->whereRaw('gr_product.pro_quantity > gr_product.pro_no_of_purchase');
				}
				if ($text != '') 
				{
					$sql->where('gr_product.'.$name,'LIKE','%'.$text.'%');
				}
				if($pro_veg != ''){
					$sql->whereIn('gr_product.pro_veg',$pro_veg);
				}
				$get_count=$sql->count();
				//$query = DB::getQueryLog();
				//print_r($query);
				
			}
			else
			{
				$sql = DB::table('gr_product')->where('pro_category_id','=',$mc_id)->where('pro_store_id','=',$st_id)->where('pro_sub_cat_id','=',$sc_id);
				if($inventory_status == '0')
				{
					$sql->whereRaw('gr_product.pro_quantity > gr_product.pro_no_of_purchase');
				}
				if ($text != '') 
				{
					$sql->where('gr_product.'.$name,'LIKE','%'.$text.'%');
				}
				if($pro_veg != ''){
					$sql->whereIn('gr_product.pro_veg',$pro_veg);
				}
				$get_count= $sql->count();
			}
			
			if($get_count > 0){
				echo $get_count;
				}else{
				echo 0;
			}
			
		}
		public function getItemName(Request $request)
		{
			$requestsR=$request->all();
			$ca_id=$requestsR['ca_id'];
			$st_id=$requestsR['st_id'];
			$sub_ca_id=$requestsR['sub_ca_id'];
			$query=$requestsR['query'];
			$get_item_details = Home::get_items_autocomplete($st_id,$ca_id,$sub_ca_id,$query,'2');
			//print_r($get_item_details); exit;
			$skillData = array();
			if(count($get_item_details) > 0){
				foreach($get_item_details as $row){
					//$path = url('').'/public/images/noimage/'.$this->no_item;
					//$filename = public_path('images/restaurant/items/').$row->pdt_image;
					//if($row->pdt_image != '' && file_exists($filename))
					//{
					//$path = url('').'/public/images/restaurant/items/'.$row->pdt_image;
					//}
					$path='test';
					$data = array($row->item_name,$path);
					array_push($skillData, $data);
				}
			}
			//print_r($skillData); exit;
			echo json_encode($skillData);
			//Array ( [ca_id] => 2 [st_id] => 20 [sub_ca_id0] => [query] => t )
		}
		//item_details2
		public function item_details2(Request $request)
		{	
			//echo $request->rest_name.'/'.$request->item_name; exit; 
			$item_slug = mysql_escape_special_chars($request->item_slug);
			$pro_id = DB::table('gr_product')->select('pro_id')->where('pro_item_slug','=',$item_slug)->first()->pro_id;
			//$pro_id=base64_decode($request->id);
			
			$get_product_details = Home::get_product_details($pro_id,'2');
			if(empty($get_product_details)===true){
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_PDT_NOTAVAIL')) ? trans(Session::get('front_lang_file').'.FRONT_PDT_NOTAVAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_PDT_NOTAVAIL');
				return view('Front.general_error')->with(['error_msg' => $msg ]);
				
				}else{
				$get_specifi_details = DB::table('gr_product_spec')->select('spec_title','spec_desc')->where('spec_pro_id','=',$pro_id)->get();
				$get_review_details  = Home::get_product_reviews($pro_id,'item');
				$related_pdtDetails  = Home::get_relatedPdt_details($pro_id,$get_product_details->pro_category_id,'2');
				if($get_product_details->pro_had_choice=='1')
				{
					$get_choice_details  = Home::get_choice_details($pro_id);
				}
				else
				{
					$get_choice_details = array();
				}
				// print_r($get_product_details); exit;
				return view('Front.item_details')->with(['get_product_details' => $get_product_details,'get_specifi_details' => $get_specifi_details,'get_review_details' => $get_review_details,'related_pdtDetails'=>$related_pdtDetails,'get_choice_details'=>$get_choice_details]);
			}
		}
		public function item_details(Request $request)
		{
			$pro_id=base64_decode($request->id);
			$pro_id = (int)$pro_id;
			$get_product_details = Home::get_product_details($pro_id,'2');
			if(empty($get_product_details)===true){
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_PDT_NOTAVAIL')) ? trans(Session::get('front_lang_file').'.FRONT_PDT_NOTAVAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_PDT_NOTAVAIL');
				return view('Front.general_error')->with(['error_msg' => $msg ]);
				
				}else{
				$get_specifi_details = DB::table('gr_product_spec')->select('spec_title','spec_desc')->where('spec_pro_id','=',$pro_id)->get();
				$get_review_details  = Home::get_product_reviews($pro_id,'item');
				$related_pdtDetails  = Home::get_relatedPdt_details($pro_id,$get_product_details->pro_category_id,'2');
				if($get_product_details->pro_had_choice=='1')
				{
					$get_choice_details  = Home::get_choice_details($pro_id);
				}
				else
				{
					$get_choice_details = array();
				}
				// print_r($get_product_details); exit;
				return view('Front.item_details')->with(['get_product_details' => $get_product_details,'get_specifi_details' => $get_specifi_details,'get_review_details' => $get_review_details,'related_pdtDetails'=>$related_pdtDetails,'get_choice_details'=>$get_choice_details]);
			}
		}
		
		/**  add to cart **/
		//    public function add_cart_item(Request $request)
		//    {
		//
		//
		//        $cus_id 	= Session::get('customer_id');
		//        $item_id 	= $request->item_id;
		//        $qty 		= $request->qty;
		//        $st_id 		= $request->st_id;
		//        $pro_currency = $request->currency;
		//        $tax		 = $request->tax;
		//        $had_ch 	= 'No';
		//        $ch_list 	= $request->input('choice_list');
		//        $pro_price 	= $request->pro_price;
		//        $max_qty 	= $request->max_qty;
		//        $cartKount = cart_count($cus_id);
		//        $ch_price = 0;
		//        $ch_array = array();
		//
		//
		//        if($ch_list != '')
		//        {
		//            $had_ch 	= 'Yes';
		//            foreach($ch_list as $ch)
		//            {
		//                $get_price = DB::table('gr_product_choice')->select('pc_price')->where(['pc_choice_id' => $ch,'pc_pro_id' => $item_id])->first();
		//                if(empty($get_price) === false)
		//                {
		//                    $ch_array[] = array("choice_id" => $ch,"choice_price" => $get_price->pc_price);
		//                    $ch_price +=$get_price->pc_price;
		//                }
		//            }
		//        }
		//
		//        /** check quantity **/
		//        $available = check_qty($item_id);
		//
		//
		//        $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EXCEED_LIMIT');
		//        if(empty($available) === false)
		//        {
		//            if($available->stock < $qty)
		//            {
		//
		//                echo '0`'.$msg.'`'.$cartKount;
		//                exit;
		//                //Session::flash('val_errors',"Quantity Exceeds Stock Limit");
		//                //return Redirect::back();
		//            }
		//        }
		//        else
		//        {
		//            echo '0`'.$msg.'`'.$cartKount;
		//                exit;
		//        }
		//
		//        $check_cart = DB::table('gr_cart_save')->selectRaw('SUM(cart_quantity) as added_qty')->where(['cart_st_id' => $st_id,'cart_item_id' => $item_id,'cart_cus_id' => $cus_id])->pluck('added_qty');
		//        //print_r($check_cart); echo $check_cart; exit;
		//        $total_count = $qty+$check_cart[0];
		//        if($total_count > $max_qty)
		//        {
		//            $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EXCEED_LIMIT');
		//            $msg .='. ';
		//            $msg .=(Lang::has(Session::get('front_lang_file').'.FRONT_HAVE_CART')) ? trans(Session::get('front_lang_file').'.FRONT_HAVE_CART') : trans($this->FRONT_LANGUAGE.'.FRONT_HAVE_CART');
		//            $msg .= ' '.$check_cart[0];
		//            echo '0`'.$msg.'`'.$cartKount;
		//            exit;
		//        }
		//        /** check item with choice already added **/
		//        $check 	  = check_cart($cus_id,$st_id,$item_id,json_encode($ch_array),$pro_currency);
		//        if(empty($check) === false)   //update cart
		//        {
		//            $quantity = $check->cart_quantity + $qty;
		//            $tax = ((($pro_price * $quantity) * $tax)/100);
		//            $insert_arr = [	'cart_quantity' 	=> $quantity,
		//                'cart_unit_amt'		=> $pro_price,
		//                'cart_currency'		=> $pro_currency,
		//                'cart_tax'			=> $tax,
		//                'cart_total_amt' 	=> ((($pro_price + $ch_price) * $quantity) + $tax),
		//                'cart_updated_at' 	=> date('Y-m-d H:i:s')];
		//
		//            $insert = updatevalues('gr_cart_save',$insert_arr,['cart_cus_id' => $cus_id,'cart_id' => $check->cart_id,'cart_st_id' => $st_id]);
		//            $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CART_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CART_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.FRONT_CART_UPDATE_SUCCESS');
		//            $cartKount = cart_count($cus_id);
		//            echo '1`'.$msg.'`'.$cartKount;
		//            //Session::flash('success',$msg);
		//        }
		//        else 	// add new cart
		//        {
		//            $tax = ((($pro_price * $qty) * $tax)/100);
		//            $insert_arr = [	'cart_cus_id' 		=> $cus_id,
		//                'cart_st_id' 		=> $st_id,
		//                'cart_item_id'		=> $item_id,
		//                'cart_had_choice'	=> $had_ch,
		//                'cart_quantity' 	=> $qty,
		//                'cart_unit_amt'		=> $pro_price,
		//                'cart_tax'			=> $tax,
		//                'cart_total_amt' 	=> ((($pro_price + $ch_price) * $qty) + $tax),
		//                'cart_currency'		=> $pro_currency,
		//                'cart_type' 		=> 2, //item cart
		//                'cart_choices_id' 	=> json_encode($ch_array),
		//                'cart_updated_at' 	=> date('Y-m-d H:i:s')];
		//            $insert = insertvalues('gr_cart_save',$insert_arr);
		//            $msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CART_ADD_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CART_ADD_SUCCESS'): trans($this->FRONT_LANGUAGE.'.FRONT_CART_ADD_SUCCESS');
		//            $cartKount = cart_count($cus_id);
		//            echo '1`'.$msg.'`'.$cartKount;
		//            //Session::flash('success',$msg);
		//        }
		//        //return Redirect::back();
		//    }
		
		
		
		public function add_cart_item(Request $request)
		{
			
			$cus_id 	= (int)Session::get('customer_id');
			$item_id 	= (int)$request->item_id;
			$qty 		= (int)$request->qty;
			$st_id 		= (int)$request->st_id;
			$pro_currency = mysql_escape_special_chars($request->currency);
			$tax		 = $request->tax;
			$had_ch 	= 'No';
			$ch_list 	= mysql_escape_special_chars($request->input('choice_list'));
			$pro_price 	= mysql_escape_special_chars($request->pro_price);
			$max_qty 	= (int)$request->max_qty;
			$cartKount = cart_count($cus_id);
			$ch_price = 0;
			$ch_array = array();
			//print_r($item_id);
			//exit;
			
			if($ch_list != '')
			{
				$had_ch 	= 'Yes';
				foreach($ch_list as $ch)
				{
					$get_price = DB::table('gr_product_choice')->select('pc_price')->where(['pc_choice_id' => $ch,'pc_pro_id' => $item_id])->first();
					if(empty($get_price) === false)
					{
						$ch_array[] = array("choice_id" => $ch,"choice_price" => $get_price->pc_price);
						$ch_price +=$get_price->pc_price;
					}
				}
			}
			/*CHECK SHIPPING ADDRESS*/
			$get_shipping_address = DB::table('gr_shipping')->select('sh_latitude','sh_longitude')->where('sh_cus_id',Session::get('customer_id'))->first();
			if(empty($get_shipping_address)===true){
				$shipErrMsg = (Lang::has(Session::get('front_lang_file').'.PLS_ENTER_SHIPPING_ADDRESS')) ? trans(Session::get('front_lang_file').'.PLS_ENTER_SHIPPING_ADDRESS') : trans($this->FRONT_LANGUAGE.'.PLS_ENTER_SHIPPING_ADDRESS');
				$clickMsg = (Lang::has(Session::get('front_lang_file').'.FRONT_CLICK_HERE')) ? trans(Session::get('front_lang_file').'.FRONT_CLICK_HERE') : trans($this->FRONT_LANGUAGE.'.FRONT_CLICK_HERE');
				$cartAmt = cart_amount($cus_id);
				echo '2`'.$shipErrMsg.'&nbsp;&nbsp;<a href="'.url('shipping_address').'" class="btn btn-success" style="background:#28a745">'.$clickMsg.'</a>`'.$cartKount.'`'.$cartAmt;
				exit;
			}else{
				$user_lat = (double)$get_shipping_address->sh_latitude;
				$user_long= (double)$get_shipping_address->sh_longitude;
				$q = array();
				$current_time = date('H:i:s');
				$current_day=date('l');
				$sql = DB::table('gr_store')->select('id','st_delivery_radius',DB::Raw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',st_latitude,st_longitude))'))
				->where(['id'=>$st_id]);
				
				if($user_lat != '' && $user_long != '')
				{
					$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',st_latitude,st_longitude)) <= st_delivery_radius');
				}
				$q = $sql->first();
				//echo '<pre>';print_r($q);
				//exit;
				if(empty($q)===true){
					$shipErrMsg = (Lang::has(Session::get('front_lang_file').'.DELIVERY_NOTAVAIL_TOUR_SHIPLOC')) ? trans(Session::get('front_lang_file').'.DELIVERY_NOTAVAIL_TOUR_SHIPLOC') : trans($this->FRONT_LANGUAGE.'.DELIVERY_NOTAVAIL_TOUR_SHIPLOC');
					$cartAmt = cart_amount($cus_id);
					echo '3`'.$shipErrMsg.'`'.$cartKount.'`'.$cartAmt;
					exit;
				}
			}
			
			/** check quantity **/
			
			$available = check_qty($item_id);
			if(empty($available) === false)
			{
				if($available->stock < $qty)
				{
					$cartAmt = cart_amount($cus_id);
					echo '0`Quantity Exceeds Stock Limit`'.$cartKount.'`'.$cartAmt;
					exit;
					//Session::flash('val_errors',"Quantity Exceeds Stock Limit");
					//return Redirect::back();
				}
			}
			else
			{
				$cartAmt = cart_amount($cus_id);
				echo '0`Quantity Exceeds Stock Limit`'.$cartKount.'`'.$cartAmt;
				exit;
			}
			
			$check_cart = DB::table('gr_cart_save')->selectRaw('SUM(cart_quantity) as added_qty')->where(['cart_st_id' => $st_id,'cart_item_id' => $item_id,'cart_cus_id' => $cus_id])->pluck('added_qty');
			//        print_r($max_qty); echo $check_cart; exit;
			$total_count = $qty+$check_cart[0];
			if($total_count > $max_qty)
			{
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')) ? trans(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT') : trans($this->FRONT_LANGUAGE.'.FRONT_EXCEED_LIMIT');
				$msg .='. ';
				$msg .=(Lang::has(Session::get('front_lang_file').'.FRONT_HAVE_CART')) ? trans(Session::get('front_lang_file').'.FRONT_HAVE_CART') : trans($this->FRONT_LANGUAGE.'.FRONT_HAVE_CART');
				$msg .= ' '.$check_cart[0];
				$cartAmt = cart_amount($cus_id);
				echo '0`'.$msg.'`'.$cartKount.'`'.$cartAmt;
				exit;
			}
			/** check item with choice already added **/
			$check 	  = check_cart($cus_id,$st_id,$item_id,json_encode($ch_array),$pro_currency);
			if(empty($check) === false)   //update cart
			{
				$quantity = $check->cart_quantity + $qty;
				$tax = ((($pro_price * $quantity) * $tax)/100);
				$insert_arr = [	'cart_quantity' 	=> $quantity,
                'cart_unit_amt'		=> $pro_price,
                'cart_currency'		=> $pro_currency,
                'cart_tax'			=> $tax,
                'cart_total_amt' 	=> ((($pro_price + $ch_price) * $quantity) + $tax),
                'cart_updated_at' 	=> date('Y-m-d H:i:s')];
				
				$insert = updatevalues('gr_cart_save',$insert_arr,['cart_cus_id' => $cus_id,'cart_id' => $check->cart_id,'cart_st_id' => $st_id]);
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CART_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CART_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.FRONT_CART_UPDATE_SUCCESS');
				$cartKount = cart_count($cus_id);
				$cartAmt = cart_amount($cus_id);
				echo '1`'.$msg.'`'.$cartKount.'`'.$cartAmt;
				//Session::flash('success',$msg);
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
                'cart_tax'			=> $tax,
                'cart_total_amt' 	=> ((($pro_price + $ch_price) * $qty) + $tax),
                'cart_currency'		=> $pro_currency,
                'cart_type' 		=> 2, //item cart
                'cart_choices_id' 	=> json_encode($ch_array),
                'cart_updated_at' 	=> date('Y-m-d H:i:s')];
				$insert = insertvalues('gr_cart_save',$insert_arr);
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CART_ADD_SUCCESS')) ? trans(Session::get('front_lang_file').'.FRONT_CART_ADD_SUCCESS'): trans($this->FRONT_LANGUAGE.'.FRONT_CART_ADD_SUCCESS');
				$cartKount = cart_count($cus_id);
				$cartAmt = cart_amount($cus_id);
				echo '1`'.$msg.'`'.$cartKount.'`'.$cartAmt;
				//Session::flash('success',$msg);
			}
			//return Redirect::back();
		}
		public function clear_session(){
			Session::forget('customer_login');
			Session::forget('customer_id');
			Session::forget('customer_details');
			Session::forget('customer_mail');
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_BLOCKED')) ? trans(Session::get('front_lang_file').'.FRONT_BLOCKED') : trans($this->FRONT_LANGUAGE.'.FRONT_BLOCKED');
			Session::flash('val_errors',$msg);
			return  Redirect::to('/');
		}
	}		