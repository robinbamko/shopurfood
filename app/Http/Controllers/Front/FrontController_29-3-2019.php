<?php 
	
	namespace App\Http\Controllers;
	
	namespace App\Http\Controllers\Front;
	
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
	
	use App\Home;
	
	use App\Customer;
	
	use Config;
	
	use Swap;
	use Artisan;
	use Twilio;
	class FrontController extends Controller
	{
		public function __construct()
		{	
			parent::__construct();
			
		}
		
		public function testcron(){
			echo date_default_timezone_get();
			//Artisan::call("AbandonedCart:send_mail", []);
		}
		
		public function index(Request $request)
		{
			$latitude = Session::get('search_latitude');
			$langitude = Session::get('search_longitude');
            //Here get shop list where activate 1;
            $Getstores=array();
            if(Session::has('search_latitude') && Session::has('search_longitude')) {
                $latitude = session::get('search_latitude');
                $longitude = session::get('search_longitude');
				
                $sql="SELECT a.*,(6371 * acos (cos ( radians($latitude) )* cos( radians(a.st_latitude ) )* cos(radians(a.st_longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians(a.st_latitude ) ))) AS distance,b.cate_name,MIN(CASE WHEN gr_product.pro_discount_price IS NULL THEN gr_product.pro_original_price ELSE gr_product.pro_discount_price  END)  as minimumprice,MAX(CASE WHEN gr_product.pro_discount_price IS NOT NULL THEN gr_product.pro_discount_price ELSE gr_product.pro_original_price  END)  as maximumprice FROM gr_store a join gr_category b on a.st_category=b.cate_id  left join gr_product on a.id=gr_product.pro_store_id  where a.st_status=1 HAVING distance <=a.st_delivery_radius ORDER BY distance desc limit 10";
                $Getstores=DB::select($sql);
			}
			
			
			//Add by karthik on 24012019 Feautred stores based on Paid dates
			
            $name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
		
			$featuredrestarants = Home::get_feature_restaurant();
			$featured_ids = array();
			if(count($featuredrestarants) > 0 ){
				foreach($featuredrestarants as $featRes){
					array_push($featured_ids,$featRes->id);
				}
			}
			//print_r($featured_ids); exit;
			$featured_limit = 4-count($featuredrestarants);
			
			if($featured_limit > 0 ){
				if (is_object($featuredrestarants)) {
                    $featuredrestarants = json_decode(json_encode($featuredrestarants));
				}
				$user_lat = Session::get('search_latitude');
				$user_long= Session::get('search_longitude');
				$featuredrestarants_ratings = DB::table('gr_store')->select('gr_store.*', 'gr_category.*',DB::raw('0 as active'),DB::Raw('(select AVG(gr_review.review_rating) from gr_review where gr_review.res_store_id=gr_store.id  and gr_review.review_status="1" and gr_review.review_type="restaurant") as review_rating'))
				->Join('gr_merchant', 'gr_merchant.id', '=', 'gr_store.st_mer_id')
				->leftJoin('gr_category', 'gr_category.cate_id', '=', 'gr_store.st_category')
				->where('gr_merchant.mer_status', '=', '1')
				->where('gr_category.cate_status', '=', '1')                      
				->where('gr_store.st_type', '=', 1)
				->where('gr_store.st_status', '=', 1);
				if(count($featured_ids) > 0 ){
					$featuredrestarants_ratings->whereNotIn('gr_store.id',$featured_ids);
				}
				if($user_lat != '' && $user_long != '')
				{	
					$featuredrestarants_ratings->whereRaw('(SELECT lat_lng_distance(' . $latitude . ',' . $langitude . ',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');
				}
				$featuredrestarants_ratings = $featuredrestarants_ratings->orderby('review_rating', 'desc')->limit($featured_limit)->get();
				if (is_object($featuredrestarants_ratings)) {
					$featuredrestarants_ratings = json_decode(json_encode($featuredrestarants_ratings));
					}
				$featuredrestarants = array_merge($featuredrestarants, $featuredrestarants_ratings);
			} 
			
            $pre_footer = Home::get_pre_footerDetails();
            $advertise_details = get_advertisement_details();
			$grocery_banner_details = array(); //Home::get_grocery_banner_details();
			$food_banner_details 	= array();//Home::get_food_banner_details();			
			$banner_imgs = DB::table('gr_banner_image')->where('banner_type','=',2)->where('banner_status','=',1)->get();
			
			
			$category_det = Home::get_category_details();
			
			$get_near_restaurant = array();
			if(count($category_det) > 0){
				foreach($category_det as $det){
					$cate_id = $det->cate_id;
					$get_near_restaurant[] = Home::near_by_restaurant($latitude,$langitude,$cate_id);
					//echo '<pre>'; print_r(Home::near_by_restaurant($latitude,$langitude,$cate_id));
				}
			}
			/*echo '<pre>'; print_r($grocery_banner_details);
				echo '<hr><<pre>'; print_r($food_banner_details);
				echo '<hr><<pre>'; print_r($Getstores);
				echo '<hr><<pre>'; print_r($featuredrestarants);
				echo '<hr><<pre>'; print_r($pre_footer);
				echo '<hr><<pre>'; print_r($banner_imgs);
				echo '<hr><<pre>'; print_r($category_det);
				echo '<hr><<pre>'; print_r($get_near_restaurant);
				echo '<hr><<pre>'; print_r($advertise_details);
			exit;*/
			return view('Front.index')->with('grocery_banner_details',$grocery_banner_details)->with('food_banner_details',$food_banner_details)->with('storelist',$Getstores)->with('featuredstores',$featuredrestarants)->with('pre_footer',$pre_footer)->with('banner_imgs',$banner_imgs)->with('category_list',$category_det)->with('near_restaurant',$get_near_restaurant)->with('advertise_details',$advertise_details);
		}
		
		/** restaurant listing **/
		public function restaurant_listings(Request $request)
		{	
			$get_banner 		 = Home::get_banner('2');
			$all_restaurants	 = Home::get_all_shops('1','12'); // 1 for restaurant
			
            $feat_restaurants 	 = Home::get_feat_shops('1','12');
			//print_r($all_restaurants); exit;
			//echo strtotime('2:00am');
			//exit;
			$all_categories		 = Home::get_all_categories(1,5); // 1 for restaurant category
			if ($request->ajax()) {
				return view('Front.restaurant_listings_ajax')->with(['all_categories' => $all_categories]);
			}
			
            return view('Front.restaurant_listings')->with(['all_restaurants'=>$all_restaurants,'feat_restaurants'=>$feat_restaurants,'all_categories' => $all_categories,'banner_details' => $get_banner]);
			
		}
		
		public function all_categories(Request $request){
			$id = $request->id; //used for specific category
			$top_disc_filter = $request->top_disc_filter;
			$del_time_filter = $request->del_time_filter;
			$rate_filter = $request->rate_filter;
			$cuisines = $request->cuisines;
			$text = $request->text;
			//echo $id.'/'.$top_disc_filter.'/'.$del_time_filter.'/'.$rate_filter.'/'.$text.'<pre>';  print_r($cuisines);
			$restaurant_lists    = array();
			$html = '';
            $duration = '';
			$page_count_max = 0;
			$item_count = 0;
			$url=url('');
			$all_categories = Home::get_all_categories(1); // 1 for restaurant category
            $no_Ratings = (Lang::has(Session::get('front_lang_file').'.FRONT_NO_RATINGS')) ? trans(Session::get('front_lang_file').'.FRONT_NO_RATINGS') : trans($this->FRONT_LANGUAGE.'.FRONT_NO_RATINGS');
			/*if(isset($id))  //restaurant list for particular category
			{
				$restaurant_lists	 = Home::get_category_shops_pofi(base64_decode($id),'1',24, $top_disc_filter, $del_time_filter, $rate_filter, $text, $cuisines);
			}else{
				$search_loaction = Session::get('search_location');
				$latitude = Session::get('search_latitude');
				$langitude = Session::get('search_longitude');
				$restaurant_lists = Home::get_category_shops_byCategory_pofi('1',24,$latitude,$langitude, $top_disc_filter, $del_time_filter, $rate_filter, $text, $cuisines); 
			}*/ 
			$search_loaction = Session::get('search_location');
			$latitude = Session::get('search_latitude');
			$langitude = Session::get('search_longitude');
			$restaurant_lists = Home::get_category_shops_byCategory_pofi('1',24,$latitude,$langitude, $top_disc_filter, $del_time_filter, $rate_filter, $text, $cuisines); 
			if(count($restaurant_lists) > 0 )
			{
				$i=($restaurant_lists->currentpage()-1)*$restaurant_lists->perpage()+1;
				$item_count = count($restaurant_lists);
				$page_count_max += $restaurant_lists->lastPage();
				foreach($restaurant_lists as $restaurant)
				{
					if(empty($restaurant) === false)
					{
						/*$sql="SELECT AVG(pro_discount_price) as discount,avg(pro_original_price) as price FROM `gr_product` WHERE pro_store_id=".$restaurant->id ." and pro_has_discount='Yes'";
						$productdiscount=DB::select($sql);
						$productdiscount=$productdiscount[0];
						$avgdiscountpercentage =0;
						
						if($productdiscount->price > 0 && $productdiscount->discount > 0) {
							$avagdiscount = $productdiscount->discount / $productdiscount->price;
							$avgdiscountpercentage = $avagdiscount * 100;
							$avgdiscountpercentage = round($avgdiscountpercentage);
						}*/
						$avgdiscountpercentage = round($restaurant->off);
						$filename = public_path('images/restaurant/').$restaurant->st_logo;
						if(file_exists($filename) && $restaurant->st_logo!= '')
						{
							$imageUrl=$url.'/public/images/restaurant/'.$restaurant->st_logo;
						}
						else
						{
							$imageUrl=$url.'/public/images/noimage/'.$this->no_shop_logo;
						}
						
						/*if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en'){
							$duration = $restaurant->st_delivery_duration;
						}else {
							if ($restaurant->st_delivery_duration == 'hours') {
								$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_HOURS')) ? trans(Session::get('front_lang_file') . '.FRONT_HOURS') : trans($this->FRONT_LANGUAGE . '.FRONT_HOURS');
							} elseif ($restaurant->st_delivery_duration == 'minutes') {
								$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_MINUTES')) ? trans(Session::get('front_lang_file') . '.FRONT_MINUTES') : trans($this->FRONT_LANGUAGE . '.FRONT_MINUTES');
							}
						}*/
						$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_MINS')) ? trans(Session::get('front_lang_file') . '.FRONT_MINS') : trans($this->FRONT_LANGUAGE . '.FRONT_MINS');
						if(Session::get('front_lang_code') != 'en'){
							$re_url = $restaurant->st_store_name;
						}else{
							$re_url = $restaurant->st_name;
						}
						
						$html .='
						<div class="catgImgDiv">	
						<a href="'.url('restaurant').'/'.$restaurant->store_slug.'" >
						<div class="catgImg" style="background: #ccc url('.$imageUrl.');"></div>
						<div class="ovrLay"><p>'.$restaurant->st_rating.' ' .$restaurant->st_delivery_time .' '.$restaurant->st_delivery_duration. ' '.  $avgdiscountpercentage .' % </p></div>
						<h6 class="title">'.ucfirst($restaurant->st_name).'</h6>
						</a>';
						if($restaurant->store_closed == "Closed")
						{
							$html .='<div class="closed-div">
							<p>'.__(Session::get('front_lang_file').'.FRONT_CLOSED').'</p>
							</div>';
						}
						$html .='</div>';
						
						

						$html .='<div class="col-md-6">
								<div class="all-rest-cate">
								<div class="all-rest-name">
								<h4>'.$restaurant->st_name.'</h4>
								<p>'.$restaurant->category_name.'</p>							
								<a href="'.url('restaurant').'/'.$restaurant->store_slug.'" ><img src="'.$imageUrl.'"></a>';
						/*$food_type = explode(',',$restaurant->st_food_type);
						if(in_array(1,$food_type))
						{
							$html .='<img src="'.$url.'/public/front/images/veg-icon.png" style="float:right">';
						}
						if(in_array(2,$food_type))
						{
							$html .='<img src="'.$url.'/public/front/images/non-veg-icon.png" style="float:right">';
						}*/		
						$html .='</div>
								<div class="all-rest-off">
								<ul>'; 

						if($restaurant->avg_val != ''){

							if($restaurant->avg_val == '5'){
								$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span id="five">'.intval($restaurant->avg_val).'</span>
								</li>';
								
								}elseif(filter_var($restaurant->avg_val, FILTER_VALIDATE_INT) && ($restaurant->avg_val != '5')){	
								$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span>'.intval($restaurant->avg_val).'.0</span>
								</li>';
								}else{
								$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span>'.intval($restaurant->avg_val).'</span>
								</li>';
							}
							
							}else{
							$html .= '<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span style="left:39%"><i title="'.$no_Ratings.'"class="fa fa-star-o" aria-hidden="true"></i></span>
							</li>';
						}
						$html .='							
						<li>
						<img src="'.$url.'/public/front/images/offer.png">
						'; if($avgdiscountpercentage != 0){
							$html .= '<p>'.  $avgdiscountpercentage .'% '.__(Session::get('front_lang_file').'.FRONT_OFF').'</p>';
							}else{
							$html .='<p>'.__(Session::get('front_lang_file').'.FRONT_NO_OFF').'</p>';
						}
						$html .='
						
						</li>
						<li>
						<img src="'.$url.'/public/front/images/bike-icon1.png">
						<p>' .$restaurant->delDurationInMin .' '.$duration. '</p>
						</li>
						</ul>
						</div>
						</div>
						</div>';	
					}
				}
			}
			if ($request->ajax()) 
			{
	            return $html.'~`'.$page_count_max.'~`'.$item_count;
			}
			return view('Front.all_categories')->with(['all_categories' => $all_categories,'page_max' => $page_count_max]);	
		}
		/** all categories for restaurant **/
		public function all_categories_SuganyaT(Request $request)
		{			
			$id = $request->id; //used for specific category
			$restaurant_lists    = array();
			$html = '';
            $duration = '';
			$page_count_max = 0;
			$url=url('');
			$all_categories		 = Home::get_all_categories(1); // 1 for restaurant category
            $no_Ratings = (Lang::has(Session::get('front_lang_file').'.FRONT_NO_RATINGS')) ? trans(Session::get('front_lang_file').'.FRONT_NO_RATINGS') : trans($this->FRONT_LANGUAGE.'.FRONT_NO_RATINGS');
			
			if(isset($id))  //restaurant list for particular category
			{
				$restaurant_lists	 = Home::get_category_shops(base64_decode($id),'1',24);
				if(count($restaurant_lists) > 0 )
				{
					$page_count_max = $restaurant_lists->lastPage();
					foreach($restaurant_lists as $restaurant)
					{
						
						if(empty($restaurant) === false)
						{
							$sql="SELECT AVG(pro_discount_price) as discount,avg(pro_original_price) as price FROM `gr_product` WHERE pro_store_id=".$restaurant->id ." and pro_has_discount='Yes'";
							$productdiscount=DB::select($sql);
							$productdiscount=$productdiscount[0];
							$avgdiscountpercentage =0;
							
							if($productdiscount->price>0 && $productdiscount->discount>0) {
								$avagdiscount = $productdiscount->discount / $productdiscount->price;
								$avgdiscountpercentage = $avagdiscount * 100;
								$avgdiscountpercentage = round($avgdiscountpercentage);
							}
							
							$filename = public_path('images/restaurant/').$restaurant->st_logo;
							if(file_exists($filename) && $restaurant->st_logo!= '')
							{
								$imageUrl=$url.'/public/images/restaurant/'.$restaurant->st_logo;
							}
							else
							{
								$imageUrl=$url.'/public/images/noimage/'.$this->no_shop_logo;
							}
							
							if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en'){
								$duration = $restaurant->st_delivery_duration;
								}else {
								
								if ($restaurant->st_delivery_duration == 'hours') {
									$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_HOURS')) ? trans(Session::get('front_lang_file') . '.FRONT_HOURS') : trans($this->FRONT_LANGUAGE . '.FRONT_HOURS');
									
									} elseif ($restaurant->st_delivery_duration == 'minutes') {
									$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_MINUTES')) ? trans(Session::get('front_lang_file') . '.FRONT_MINUTES') : trans($this->FRONT_LANGUAGE . '.FRONT_MINUTES');
									
								}
							}
							
							if(Session::get('front_lang_code') != 'en'){
							    $re_url = $restaurant->st_store_name;
								}else{
                                $re_url = $restaurant->st_name;
							}
							
							
							
							$html .='
							<div class="catgImgDiv">	
							<a href="'.url('restaurant').'/'.$restaurant->store_slug.'" >
							<div class="catgImg" style="background: #ccc url('.$imageUrl.');"></div>
							<div class="ovrLay"><p>'.$restaurant->st_rating.' ' .$restaurant->st_delivery_time .' '.$duration. ' '.  $avgdiscountpercentage .' % </p></div>
							<h6 class="title">'.ucfirst($restaurant->st_name).'</h6>
							</a>';
							if($restaurant->store_closed == "Closed")
							{
								$html .='<div class="closed-div">
								<p>'.__(Session::get('front_lang_file').'.FRONT_CLOSED').'</p>
								</div>';
							}
							$html .='</div>';
							
							
							$html .='
							<div class="col-md-6">
							<div class="all-rest-cate">
							<div class="all-rest-name">
							<h4>'.$restaurant->st_name.'</h4>
							<p>'.$restaurant->category_name.'</p>
							<a href="'.url('restaurant').'/'.$restaurant->store_slug.'" >
							<img src="'.$imageUrl.'"></a>
							</div>
							<div class="all-rest-off">
							<ul>
							'; if($restaurant->avg_val != ''){
								if($restaurant->avg_val == '5'){
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span id="five">'.intval($restaurant->avg_val).'</span>
									</li>';
									
									}elseif(filter_var($restaurant->avg_val, FILTER_VALIDATE_INT) && ($restaurant->avg_val != '5')){	
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span>'.intval($restaurant->avg_val).'.0</span>
									</li>';
									}else{
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span>'.intval($restaurant->avg_val).'</span>
									</li>';
								}
								
								}else{
								$html .= '<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span style="left:39%"><i title="'.$no_Ratings.'"class="fa fa-star-o" aria-hidden="true"></i></span>
								</li>';
							}
							$html .='							
							<li>
							<img src="'.$url.'/public/front/images/offer.png">
							'; if($avgdiscountpercentage != 0){
								$html .= '<p>'.  $avgdiscountpercentage .'% '.__(Session::get('front_lang_file').'.FRONT_OFF').'</p>';
								}else{
								$html .='<p>'.__(Session::get('front_lang_file').'.FRONT_NO_OFF').'</p>';
							}
							$html .='
							
							</li>
							<li>
							<img src="'.$url.'/public/front/images/bike-icon1.png">
							<p>' .$restaurant->st_delivery_time .' '.$duration. '</p>
							</li>
							</ul>
							</div>
							</div>
							</div>';
							
							
						}
					}
					//$html .='</div>';
				}
				// else
				// 	{
				// 	$html .= '<center><p>No Restaurants Found</p></center>';
				// }
			}
			else   // restaurant list for all category
			{
				
				$search_loaction = Session::get('search_location');
				$latitude = Session::get('search_latitude');
				$langitude = Session::get('search_longitude');
				
				$restaurant_lists	 = Home::get_category_shops_byCategory('1',24,$latitude,$langitude); 
				//print_r($restaurant_lists); exit;		
				
				if(count($restaurant_lists) > 0 )
				{
					$page_count_max += $restaurant_lists->lastPage();
					foreach($restaurant_lists as $restaurant)
					{
						if(empty($restaurant) === false)
						{
							$sql="SELECT AVG(pro_discount_price) as discount,avg(pro_original_price) as price FROM `gr_product` WHERE pro_store_id=".$restaurant->id ." and pro_has_discount='Yes'";
							$productdiscount=DB::select($sql);
							$productdiscount=$productdiscount[0];
							$avgdiscountpercentage =0;
							
							if($productdiscount->price>0 && $productdiscount->discount>0) {
								$avagdiscount = $productdiscount->discount / $productdiscount->price;
								$avgdiscountpercentage = $avagdiscount * 100;
								$avgdiscountpercentage = round($avgdiscountpercentage);
							}
							
							
							$filename = public_path('images/restaurant/').$restaurant->st_logo;
							if(file_exists($filename) && $restaurant->st_logo!= '')
							{
								$imageUrl=$url.'/public/images/restaurant/'.$restaurant->st_logo;
							}
							else
							{
								$imageUrl=$url.'/public/images/noimage/'.$this->no_shop_logo;
							}
							
							
							
							
							
							if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en'){
								$duration = $restaurant->st_delivery_duration;
								}else {
								
								if ($restaurant->st_delivery_duration == 'hours') {
									$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_HOURS')) ? trans(Session::get('front_lang_file') . '.FRONT_HOURS') : trans($this->FRONT_LANGUAGE . '.FRONT_HOURS');
									
									} elseif ($restaurant->st_delivery_duration == 'minutes') {
									$duration = (Lang::has(Session::get('front_lang_file') . '.FRONT_MINUTES')) ? trans(Session::get('front_lang_file') . '.FRONT_MINUTES') : trans($this->FRONT_LANGUAGE . '.FRONT_MINUTES');
									
								}
							}
							
							
							if(Session::get('front_lang_code') != 'en'){
								$re_url = $restaurant->st_store_name;
								}else{
								$re_url = $restaurant->st_name;
							}
							
							
							$html .='
							<div class="catgImgDiv">	
							<a href="'.url('restaurant').'/'.$restaurant->store_slug.'" >
							<div class="catgImg" style="background: #ccc url('.$imageUrl.');"></div>
							<div class="ovrLay"><p>'.$restaurant->st_rating.' ' .$restaurant->st_delivery_time .' '.$restaurant->st_delivery_duration. ' '.  $avgdiscountpercentage .' % </p></div>
							<h6 class="title">'.ucfirst($restaurant->st_name).'</h6>
							</a>';
							if($restaurant->store_closed == "Closed")
							{
								$html .='<div class="closed-div">
								<p>'.__(Session::get('front_lang_file').'.FRONT_CLOSED').'</p>
								</div>';
							}
							$html .='</div>';
							
							
							$html .='
							<div class="col-md-6">
							<div class="all-rest-cate">
							<div class="all-rest-name">
							<h4>'.$restaurant->st_name.'</h4>
							<p>'.$restaurant->category_name.'</p>							
							<a href="'.url('restaurant').'/'.$restaurant->store_slug.'" ><img src="'.$imageUrl.'"></a>
							</div>
							<div class="all-rest-off">
							<ul>
							'; if($restaurant->avg_val != ''){
								if($restaurant->avg_val == '5'){
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span id="five">'.intval($restaurant->avg_val).'</span>
									</li>';
									
									}elseif(filter_var($restaurant->avg_val, FILTER_VALIDATE_INT) && ($restaurant->avg_val != '5')){	
								 	$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span>'.intval($restaurant->avg_val).'.0</span>
									</li>';
									}else{
									$html .='<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span>'.intval($restaurant->avg_val).'</span>
									</li>';
								}
								
								}else{
								$html .= '<li class="star-des"><img src="'.$url.'/public/front/images/star1.png"><span style="left:39%"><i title="'.$no_Ratings.'"class="fa fa-star-o" aria-hidden="true"></i></span>
								</li>';
							}
							$html .='							
							<li>
							<img src="'.$url.'/public/front/images/offer.png">
							'; if($avgdiscountpercentage != 0){
								$html .= '<p>'.  $avgdiscountpercentage .'% '.__(Session::get('front_lang_file').'.FRONT_OFF').'</p>';
								}else{
								$html .='<p>'.__(Session::get('front_lang_file').'.FRONT_NO_OFF').'</p>';
							}
							$html .='
							
							</li>
							<li>
							<img src="'.$url.'/public/front/images/bike-icon1.png">
							<p>' .$restaurant->st_delivery_time .' '.$duration. '</p>
							</li>
							</ul>
							</div>
							</div>
							</div>';	
							
							
							
							
						}
					}
					
				}
				// else{
				// 	$html .= '<center><p>No Restaurants Found</p></center>';
				// }
				//}
				//$html .='</div>';
				//}
			}
			if ($request->ajax()) 
			{
	            return $html;
			}
			return view('Front.all_categories')->with(['all_categories' => $all_categories,'page_max' => $page_count_max]);
		}
		
		/** grocery listings **/
		public function grocery_listings(Request $request)
		{	
			$get_banner 		= Home::get_banner('1');	//1 for store
			$all_stores	 		= Home::get_all_shops('2','12'); // 2 for store
			$all_categories		= Home::get_all_categories(2,5); // 2 for store category
            $feat_restaurants 	= Home::get_feat_shops('2','12');
			//print_r($all_stores); exit;
			if ($request->ajax()) {
				return view('Front.grocery_listings_ajax')->with(['all_categories' => $all_categories]);
			}
			//			return view('Front.grocery_listings')->with(['all_stores'=>$all_stores,'all_categories' => $all_categories,'banner_details' => $get_banner]);
			
            return view('Front.grocery_listings')->with(['all_stores'=>$all_stores,'feat_restaurants'=>$feat_restaurants,'all_categories' => $all_categories,'banner_details' => $get_banner]);
			
		}
		
		/** grocery categories **/
		public function all_grocery_categories(Request $request)
		{	
			$id = $request->id; //used for specific category
			$store_lists    = array();
			$html = ''; $page_count_max = 0;
			$url=url('');
			$all_categories		 = Home::get_all_categories(2); // 2 for store category 
			//print_r($all_categories); exit;
			if(isset($id))  //store list for particular category
			{
				$store_lists	 = Home::get_category_shops(base64_decode($id),'2',24); 
				if(count($store_lists) > 0 )
				{	
					
					$page_count_max = $store_lists->lastPage();
					foreach($store_lists as $store)
					{	
						if(empty($store) === false)
						{
							$filename = public_path('images/store/').$store->st_logo;
							if(file_exists($filename) && $store->st_logo!= '')
							{
								$imageUrl=$url.'/public/images/store/'.$store->st_logo;
							}
							else
							{
								$imageUrl=$url.'/public/images/noimage/'.$this->no_shop_logo;
							}
							$html .='
							<div class="catgImgDiv">	
							<a href="'.url('store').'/'.$store->store_slug.'" alt="here">
							<div class="catgImg" style="background: #ccc url('.$imageUrl.');"></div>
							<div class="ovrLay"></div>						
							<h6 class="title">'.ucfirst($store->st_name).'</h6>
							</a>					
							</div>';
							
						}
					}
					
				}
				
			}
			else   // store list for all category
			{
				
				$store_lists	 = Home::get_category_shops_byCategory('2',24); 
				if(count($store_lists) > 0 )
				{
					//$html='';
					$page_count_max += $store_lists->lastPage();
					foreach($store_lists as $store)
					{	
						if(empty($store) === false)
						{
							$filename = public_path('images/store/').$store->st_logo;
							if(file_exists($filename) && $store->st_logo!= '')
							{
								$imageUrl=$url.'/public/images/store/'.$store->st_logo;
							}
							else
							{
								$imageUrl=$url.'/public/images/noimage/'.$this->no_shop_logo;
							}
							$html .='
							<div class="catgImgDiv">	
							<a href="'.url('store').'/'.$store->store_slug.'" alt="store">
							<div class="catgImg" style="background: #ccc url('.$imageUrl.');"></div>
							<div class="ovrLay"></div>						
							<h6 class="title">'.ucfirst($store->st_name).'</h6>
							</a>					
							</div>';
							
						}
					}
					
				}
				
			}
			if ($request->ajax()) 
			{
	            return $html;
			}
			
			return view('Front.all_grocery_categories')->with(['all_categories' => $all_categories,'page_max' => $page_count_max]);
		}
		
		public function session_location(Request $request)
		{
			//echo $request->location;
			if(Session::has('search_location') == 1)
			{
				if(Session::get('search_latitude')==$request->latitude && Session::get('search_longitude')==$request->longitude )
				{
					echo '0';
				}
				else
				{
					$cartKount = DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->count();
					if($cartKount > 0 )
					{
						echo '2';
					}
					else
					{
						echo '1';
						DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
						Session::put('search_location',$request->location);
						Session::put('search_latitude',$request->latitude);
						Session::put('search_longitude',$request->longitude);
						if(Session::has('customer_id') == 1)
						{
							$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',Session::get('customer_id'))->first();
							$shipArray = array(	'sh_cus_id'		=>	Session::get('customer_id'),
												'sh_location' 	=> $request->location,
												'sh_latitude'	=> $request->latitude,
												'sh_longitude'	=> $request->longitude,
											);
							if(empty($shipAddDet)===false){
								$update = updatevalues('gr_shipping',$shipArray,['sh_cus_id'=>Session::get('customer_id')]);
							}else{
								$insert = insertvalues('gr_shipping',$shipArray);
							}
						}
						else{
							$shipArray = array(	'sh_location' 	=> $request->location,
												'sh_latitude'	=> $request->latitude,
												'sh_longitude'	=> $request->longitude,
											);
							Session::put('shipping_session',$shipArray);
						}
					}
				}
			}
			else
			{
				DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
				echo '1';
				Session::put('search_location',$request->location);
				Session::put('search_latitude',$request->latitude);
				Session::put('search_longitude',$request->longitude);
				if(Session::has('customer_id') == 1)
				{
					$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',Session::get('customer_id'))->first();
					$shipArray = array(	'sh_cus_id'		=>	Session::get('customer_id'),
										'sh_location' 	=> $request->location,
										'sh_latitude'	=> $request->latitude,
										'sh_longitude'	=> $request->longitude,
									);
					if(empty($shipAddDet)===false){
						$update = updatevalues('gr_shipping',$shipArray,['sh_cus_id'=>Session::get('customer_id')]);
					}else{
						$insert = insertvalues('gr_shipping',$shipArray);
					}
				}
				else{
					$shipArray = array(	'sh_location' 	=> $request->location,
										'sh_latitude'	=> $request->latitude,
										'sh_longitude'	=> $request->longitude,
									);
					Session::put('shipping_session',$shipArray);
				}
			}
		}
		// public function session_location_clearcart(Request $request)
		// {
		// 	//echo $request->location;
		// 	if(Session::has('search_location') == 1)
		// 	{
		// 		if(Session::get('search_latitude')==$request->latitude && Session::get('search_longitude')==$request->longitude )
		// 		{
		// 			echo '0';
		// 		}
		// 		else
		// 		{
		// 			DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
		// 			Session::put('search_location',$request->location);
		// 			Session::put('search_latitude',$request->latitude);
		// 			Session::put('search_longitude',$request->longitude);
		// 		}
		// 	}
		// 	else
		// 	{
		// 		DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
		// 		echo '1';
		// 		Session::put('search_location',$request->location);
		// 		Session::put('search_latitude',$request->latitude);
		// 		Session::put('search_longitude',$request->longitude);
		// 	}
		// }
		
		public function session_location_clearcart(Request $request)
		{
			//echo $request->location;
			if(Session::has('search_location') == 1)
			{
				if(Session::get('search_latitude')==$request->latitude && Session::get('search_longitude')==$request->longitude )
				{
					echo '0';
				}
				else
				{
					DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
					Session::put('search_location',$request->location);
					Session::put('search_latitude',$request->latitude);
					Session::put('search_longitude',$request->longitude);
					if(Session::has('customer_id') == 1)
					{
						$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',Session::get('customer_id'))->first();
						$shipArray = array(	'sh_cus_id'		=>	Session::get('customer_id'),
											'sh_location' 	=> $request->location,
											'sh_latitude'	=> $request->latitude,
											'sh_longitude'	=> $request->longitude,
										);
						if(empty($shipAddDet)===false){
							$update = updatevalues('gr_shipping',$shipArray,['sh_cus_id'=>Session::get('customer_id')]);
						}else{
							$insert = insertvalues('gr_shipping',$shipArray);
						}
					}
					else{
						$shipArray = array(	'sh_location' 	=> $request->location,
											'sh_latitude'	=> $request->latitude,
											'sh_longitude'	=> $request->longitude,
										);
						Session::put('shipping_session',$shipArray);
					}
				}
			}
			else
			{
				DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
				echo '1';
				Session::put('search_location',$request->location);
				Session::put('search_latitude',$request->latitude);
				Session::put('search_longitude',$request->longitude);
				
				if(Session::has('customer_id') == 1)
				{
					$shipAddDet=DB::table('gr_shipping')->select('id')->where('sh_cus_id',Session::get('customer_id'))->first();
					$shipArray = array(	'sh_cus_id'		=>	Session::get('customer_id'),
										'sh_location' 	=> $request->location,
										'sh_latitude'	=> $request->latitude,
										'sh_longitude'	=> $request->longitude,
									);
					if(empty($shipAddDet)===false){
						$update = updatevalues('gr_shipping',$shipArray,['sh_cus_id'=>Session::get('customer_id')]);
					}else{
						$insert = insertvalues('gr_shipping',$shipArray);
					}
				}
				else{
					$shipArray = array(	'sh_location' 	=> $request->location,
										'sh_latitude'	=> $request->latitude,
										'sh_longitude'	=> $request->longitude,
									);
					Session::put('shipping_session',$shipArray);
				}
			}
		}
		
		public function clearcart(Request $request)
		{
			DB::table('gr_cart_save')->where('cart_cus_id',Session::get('customer_id'))->delete();
		}
		// public function addtowish()
		// {
		
		// 	$data = Input::except(array('_token'));
		
		// 	$pro_id              = Input::get('pro_id'); 
		// 	$pro_type 			 = Input::get('pro_type');
		// 	$cus_id              = Session::get('customer_id');
		
		
		// 	$entry  = array(
		//           'ws_pro_id' => $pro_id,
		//           'ws_cus_id' => $cus_id,
		//           'ws_type' => $pro_type,
		//           'ws_date' => date('Y-m-d')
		// 	);
		
		// 	$check = Home::check_wishlist($pro_id,$cus_id);
		
		// 	if($check==0){
		// 		$wish       = Home::insert_wish($entry);
		// 		echo 0;
		// 		$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_ADDED_PRODUCT_WISHLIST')) ? trans(Session::get('front_lang_file').'.FRONT_ADDED_PRODUCT_WISHLIST') : trans($this->FRONT_LANGUAGE.'.FRONT_ADDED_PRODUCT_WISHLIST');
		// 		Session::flash('success',$msg);
		// 		}elseif($check!=0){
		// 		echo 1;
		// 	}
		
		// }
		
		
		
		public function addtowish()
		{
			
			$data = Input::except(array('_token'));
			$pro_id              = Input::get('pro_id'); 
			$pro_type 			 = Input::get('pro_type');
			$pro_type_text = ($pro_type=='1')?'Product':'Item';
			$cus_id              = Session::get('customer_id');
			
			$entry  = array('ws_pro_id' => $pro_id,
			'ws_cus_id' => $cus_id,
			'ws_type' => $pro_type,
			'ws_date' => date('Y-m-d')
			);
			$check = Home::check_wishlist($pro_id,$cus_id);
			if($check==0){
				$wish       = Home::insert_wish($entry);
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_ADDED_PRODUCT_WISHLIST')) ? trans(Session::get('front_lang_file').'.FRONT_ADDED_PRODUCT_WISHLIST') : trans($this->FRONT_LANGUAGE.'.FRONT_ADDED_PRODUCT_WISHLIST');
				$msg = str_replace(":product_type",$pro_type_text,$msg);
				echo '0`'.$msg;
				//Session::flash('success',$msg);
				}elseif($check!=0){
				//echo 1;
				DB::table('gr_wishlist')->where('ws_pro_id','=',$pro_id)->where('ws_cus_id','=',$cus_id)->where('ws_type','=',$pro_type)->delete();
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REMOVED_FROM_WISHLIST')) ? trans(Session::get('front_lang_file').'.FRONT_REMOVED_FROM_WISHLIST') : trans($this->FRONT_LANGUAGE.'.FRONT_REMOVED_FROM_WISHLIST');
				$msg = str_replace(":product_type",$pro_type_text,$msg);
				echo '1`'.$msg;
			}
			
		}
		
		public function remove_wish_product($id){  // getting wishlist_id to remove wishlist from table
			
			$remove = Home::remove_wish_product(base64_decode($id));
			
			$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REMOVED_FROM_WISHLIST')) ? trans(Session::get('front_lang_file').'.FRONT_REMOVED_FROM_WISHLIST') : trans($this->FRONT_LANGUAGE.'.FRONT_REMOVED_FROM_WISHLIST');
			$msg = str_replace(":product_type","",$msg);
			
			Session::flash('success',$msg);
			return Redirect::back();
		}
		
		public function user_wishlist()
		{
			if(Session::has('customer_id') == 1)
			{
				$cus_id = Session::get('customer_id');
				$sel = Home::get_wishlist_details($cus_id);
				return view('Front.user_wishlist',['wishlist_detail'=>$sel]);
			}
			else{
				return Redirect::to('/');
			}
			
		}
		public function user_change_password()
		{
			return view('Front.user_change_password');
		}
		public function user_change_password_submit(Request $request)
		{
			$customer_id = Session::get('customer_id');
			$customer_details = Customer::get_customer_det($customer_id);
			
			$current_password = Input::get('currentpassword');
			$new_password = Input::get('newpassword');
			$confirmpassword = Input::get('confirmpassword');
			// $this->validate($request, 
			// [
			// 'currentpassword'=>'Required',
			// 'newpassword'=>'Required|min:6',
			// 'confirmpassword'=>'Required']);
			
			
			if($this->general_setting->gs_password_protect == '1')
			{
				$this->validate($request, 
				[
				'currentpassword'=>'Required',
				'newpassword'=>'Required|min:6|regex:/(^[A-Za-z0-9!@$%^&*() ]+$)+/',
				'confirmpassword'=>'Required']);
			}
			else
			{ 
				$this->validate($request, 
				[
				'currentpassword'=>'Required',
				'newpassword'=>'Required|min:6',
				'confirmpassword'=>'Required']);
			}
			
			if($customer_details->cus_password != md5($current_password))
			{
				// echo 'hi'; exit();
        		$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_CURRENT_PASSWORD_NOT_MATCH')) ? trans(Session::get('front_lang_file').'.ADMIN_CURRENT_PASSWORD_NOT_MATCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_CURRENT_PASSWORD_NOT_MATCH');
        		Session::flash('val_errors',$msg);
			}
			elseif($current_password == $new_password)
			{
				// echo 'hello'; exit();
        		$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_NEW_PASS_NOT_SAME_CURRENT_PASS')) ? trans(Session::get('front_lang_file').'.ADMIN_NEW_PASS_NOT_SAME_CURRENT_PASS') : trans($this->FRONT_LANGUAGE.'.ADMIN_NEW_PASS_NOT_SAME_CURRENT_PASS');
        		Session::flash('val_errors',$msg);
				//echo session()->has('errors').'<br>';
				//print_r(session()->all()['errors']);
				//exit;
			}
			elseif($new_password != $confirmpassword)
			{
				// echo 'hellodddd'; exit();
        		$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_PASS_CONFIRMATION')) ? trans(Session::get('front_lang_file').'.ADMIN_PASS_CONFIRMATION') : trans($this->FRONT_LANGUAGE.'.ADMIN_PASS_CONFIRMATION');
        		Session::flash('val_errors',$msg);
			}
			else{
        		
				$insertArr = array(
				'cus_password' => md5($new_password),
				'cus_decrypt_password' => $new_password
				);
				$update = updatevalues('gr_customer',$insertArr,['cus_id'=>$customer_id]);
				$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				Session::flash('success',$msg);
			}
			//echo session()->has('errors'); exit;
			return redirect('user-change-password');
			
			
			
		}
		
		public function my_orders()
		{
			$customer_id = Session::get('customer_id');
			$order_number = Input::get('order_number');
			$order_details = Customer::getall_customer_order($customer_id,$order_number);
			
			return view('Front.my_orders',['orderdetails'=>$order_details]);
		}
		
		public function InvoiceOrder($id)
		{
			$id=base64_decode($id);
			$pagetitle = (Lang::has(Session::get('front_lang_file').'.ADMIN_INVOICE_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_INVOICE_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_INVOICE_DETAILS');
			$choices=array();
			
			
            $store_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			
            $pdt_name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			
			$Invoice_Order = DB::table('gr_order')
			->select('gr_order.ord_shipping_cus_name',
			'gr_order.ord_shipping_address',
			'gr_order.ord_shipping_address1',
			'gr_order.ord_shipping_mobile',
			$store_name .' as st_store_name',
			$pdt_name .' as pro_item_name',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_pay_type',
			'gr_order.ord_date',
			'gr_order.ord_transaction_id',
			'gr_order.ord_pre_order_date',
			'gr_order.ord_had_choices',
			'gr_order.ord_delivery_fee',
			'gr_order.ord_currency',
			'gr_customer.cus_fname',
			'gr_customer.cus_lname',
			'gr_customer.cus_address',
			'gr_customer.cus_phone1',
			'gr_customer.cus_email',
			'gr_order.ord_wallet',
			'gr_order.ord_self_pickup',
			'gr_order.ord_spl_req',
			'gr_order.ord_id',
			'gr_order.order_ship_mail'
			)
			->join('gr_customer','gr_order.ord_cus_id', '=', 'gr_customer.cus_id')
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$id)->get();
			
			
			/* Start-Sathyaseelan getting choices */
			if(count($Invoice_Order)>0)
			{
				foreach($Invoice_Order as $orders)
				{
					$splitted_choice=json_decode($orders->ord_choices,true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choices[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								
                                $choices[$choices_name.'`'.$orders->ord_id]=$choice['choice_price'];
								
							}
						}
					}
					
				}
			}
			//TRACK ORDER DETAILS
			$storewise_details   = Customer::track_reports($id);
			
			return view ('Front.order_invoice')->with('Invoice_Order',$Invoice_Order)->with('choices',$choices)->with('pagetitle',$pagetitle)->with('storewise_details',$storewise_details);
		}
		public function OrderDetailToCancel($id)
		{
			$ord_transaction_id = base64_decode($id);
			$pagetitle = (Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_ORDER_DETAILS');
			$pagetitle .= ' - '.$ord_transaction_id;
			
			$choices=array();
			
			
            $store_name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
			
            $pdt_name = (Session::get('front_lang_code') == 'en') ? 'pro_item_name' : 'pro_item_name_'.Session::get('front_lang_code');
			
			$Invoice_Order = DB::table('gr_order')
			->select('gr_order.ord_id',
			'gr_order.ord_merchant_id',
			$store_name .' as st_store_name',
			$pdt_name .' as pro_item_name',
			'gr_order.ord_refund_status',
			'gr_order.ord_mer_cancel_status',
			'gr_order.ord_quantity',
			'gr_order.ord_unit_price',
			'gr_order.ord_sub_total',
			'gr_order.ord_tax_amt',
			'gr_order.ord_choices',
			'gr_order.ord_status',
			'gr_order.ord_pay_type',
			'gr_order.ord_had_choices',
			'gr_order.ord_reject_reason',
			'gr_order.ord_currency',
			'gr_order.ord_cancel_status',
			'gr_order.ord_cancel_reason',
			'gr_order.ord_cancel_date'
			)
			->join('gr_store','gr_order.ord_rest_id', '=', 'gr_store.id')
			->join('gr_product','gr_order.ord_pro_id','=','gr_product.pro_id')
			->where('gr_order.ord_transaction_id','=',$ord_transaction_id)
			//->where('gr_order.ord_status','<','2')
			->get();
			
			
			/* Start-Sathyaseelan getting choices */
			if(count($Invoice_Order)>0)
			{
				foreach($Invoice_Order as $orders)
				{
					$splitted_choice=json_decode($orders->ord_choices,true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choices[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								
                                $choices[$choices_name.'`'.$orders->ord_id]=$choice['choice_price'];
								
							}
						}
					}
					
				}
			}
			//echo '<pre>';print_r($choices);exit;
			return view ('Front.order_details')->with('Invoice_Order',$Invoice_Order)->with('choices',$choices)->with('pagetitle',$pagetitle)->with('transid',$ord_transaction_id);
		}
		//		public function cancelOrder(Request $request)
		//		{
		//			//echo $request->reason.'/'.$request->orderId;test/24,25
		//			$order_ids = explode(',',$request->orderId);
		//			$customerDetArray = array();
		//			$storeDetArray = array();
		//			if(count($order_ids) > 0 )
		//			{
		//				foreach($order_ids as $order_id)
		//				{
		//					$ord_grand = DB::table('gr_order')->select('ord_grant_total','ord_merchant_id','ord_pay_type','ord_transaction_id','ord_admin_amt','ord_refund_status','ord_quantity','ord_pro_id')
		//													->where('ord_id','=',$order_id)->first();
		//					if(empty($ord_grand)===false)
		//					{
		//
		//							$ord_transaction_id=$ord_grand->ord_transaction_id;
		//							//echo $ord_transaction_id.'<br>';
		//							$get_total_order_count = get_total_order_count($ord_transaction_id);
		//							$get_total_cancelled_count = get_total_cancelled_count($ord_transaction_id)+1;
		//							if($get_total_cancelled_count==$get_total_order_count)
		//							{
		//								$add_delfee_status=1;
		//							}
		//							else
		//							{
		//								$add_delfee_status=0;
		//							}
		//							/** update cancel status **/
		//							/** while user cancel the item, admin detect commission from user's item grant total amount **/
		//							/* if refund status is no, no need to add cancellation amount */
		//							if($ord_grand->ord_refund_status == 'Yes')
		//							{
		//								DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_cancel_amt' => ($ord_grand->ord_grant_total- $ord_grand->ord_admin_amt) ]);
		//								/** update merchant amount **/
		//								DB::table('gr_merchant_overallorder')->where('or_mer_id', $ord_grand->ord_merchant_id)->update(['or_cancel_amt' => DB::raw('or_cancel_amt+'.($ord_grand->ord_grant_total-$ord_grand->ord_admin_amt))]);
		//								/*--- update product quantity ---*/
		//								updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
		//							}
		//							else
		//							{
		//								DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s')]);
		//								/*--- update product quantity ---*/
		//								updatevalues('gr_product',['pro_quantity' => DB::raw('pro_quantity+'.$ord_grand->ord_quantity)],['pro_id' => $ord_grand->ord_pro_id]);
		//							}
		//					}
		//
		//
		//					//GET CUSTOMER DETAILS
		//					$customerDet = Home::get_cancelled_customer_byOrderId($order_id);
		//					if(empty($customerDet) === false)
		//					{
		//						$customerDetArray[$customerDet->ord_transaction_id]=$customerDet;
		//					}
		//					//GET PRODUCT DETAILS
		//					$storeDet = Home::get_cancelled_store_byOrderId($order_id);
		//					if(count($storeDet)> 0 )
		//					{
		//						foreach($storeDet as $stDet)
		//						{
		//							$storeDetArray[$stDet->st_store_name.'`'.$stDet->mer_email.'`'.$stDet->ord_merchant_id][]=Home::get_cancelled_order_byOrderId($order_id,$stDet->id);
		//						}
		//					}
		//
		//
		//				}
		//				//echo '<pre>'; print_r($storeDetArray);
		//				//echo '<pre>'; print_r($customerDetArray);
		//				//exit;
		//
		//				foreach($customerDetArray as $key=>$customerDet)
		//				{
		//					$transaction_id = $key;
		//				}
		//				if(empty($customerDet) === false)
		//				{
		//					/** ------------------ MAIL FUNCTION  ----------------------------- **/
		//					//1) MAIL TO CUSTOMER
		//					$customerMail =  $customerDet->order_ship_mail;
		//					$send_mail_data = array('order_details'	=> $storeDetArray,'transaction_id'=>$transaction_id);
		//					Mail::send('email.order_cancel_mail_customer', $send_mail_data, function($message) use($customerMail)
		//					{
		//						$message->to($customerMail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
		//					});
		//					//2) MAIL TO ADMIN
		//					$adminMail = Config::get('admin_mail');
		//					$send_mail_data = array('order_details'	=> $storeDetArray,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id);
		//					Mail::send('email.order_cancel_mail_admin', $send_mail_data, function($message) use($adminMail)
		//					{
		//						$message->to($adminMail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
		//					});
		//
		//					/* ---------- SEND NOTIFICATION TO ADMIN ----------------*/
		//					//echo '<pre>'; print_r($customerDetArray);
		//					//exit;
		//					if(count($customerDetArray) > 0 ){
		//						foreach($customerDetArray as $custDet){
		//							$shipping_name = $custDet->ord_shipping_cus_name;
		//							$cus_id = $custDet->ord_cus_id;
		//						}
		//					}
		//					$pdtnameArray = array();
		//					foreach($storeDetArray as $key=>$itemsDet){
		//						$explodeRest = explode('`',$key);
		//						if(count($itemsDet) > 0 ){
		//							foreach($itemsDet  as $itemDet) {
		//								if(count($itemDet) > 0) {
		//									foreach($itemDet as $itDet) {
		//										array_push($pdtnameArray,ucfirst($itDet->productName));
		//									}
		//								}
		//							}
		//						}
		//					}
		//					$got_message = (Lang::has(Session::get('front_lang_file').'.ADMIN_CANCEL_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.ADMIN_CANCEL_NOTIFICATION') : trans($this->FRONT_LANGUAGE.'.ADMIN_CANCEL_NOTIFICATION') ;
		//					$message = str_replace(':transaction_id', $transaction_id, $got_message);
		//					$message = str_replace(':user_name', ucfirst($shipping_name), $message);
		//					$message = str_replace(':item_nmae', implode(",",$pdtnameArray), $message);
		//					$admin_det = get_admin_details();
		//					$admin_id  = $admin_det->id;
		//
		//					$message_link = 'admin-track-order/'.base64_encode($transaction_id);
		//					push_notification($cus_id,$admin_id,'gr_customer','gr_admin',$message,$transaction_id,$message_link);
		//
		//
		//					//3) MAIL TO MERCHANT
		//					if(count($storeDetArray) > 0 )
		//					{
		//						foreach($storeDetArray as $key=>$itemsDet)
		//						{
		//							$explodeRes = explode('`',$key);
		//							$merchantEmail = $explodeRes[1];
		//							$merchantId = $explodeRes[2];
		//							$send_mail_data = array('order_details'	=> $itemsDet,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id,'storename'=>$explodeRes[0]);
		//							Mail::send('email.order_cancel_mail_merchant', $send_mail_data, function($message) use($merchantEmail)
		//							{
		//								$message->to($merchantEmail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
		//							});
		//
		//							/* ---------- SEND NOTIFICATION TO MERCHANT ----------------*/
		//							$pdtnameArray = array();
		//							if(count($itemsDet) > 0 ){
		//								foreach($itemsDet  as $itemDet) {
		//									if(count($itemDet) > 0) {
		//										foreach($itemDet as $itDet) {
		//											array_push($pdtnameArray,ucfirst($itDet->productName));
		//										}
		//									}
		//								}
		//							}
		//							$got_message = (Lang::has(Session::get('front_lang_file').'.ADMIN_CANCEL_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.ADMIN_CANCEL_NOTIFICATION') : trans($this->FRONT_LANGUAGE.'.ADMIN_CANCEL_NOTIFICATION') ;
		//							$message = str_replace(':transaction_id', $transaction_id, $got_message);
		//							$message = str_replace(':user_name', ucfirst($shipping_name), $message);
		//							$message = str_replace(':item_nmae', implode(",",$pdtnameArray), $message);
		//							$message_link = 'mer-order-details/'.base64_encode($transaction_id);
		//							push_notification($cus_id,$merchantId,'gr_customer','gr_merchant',$message,$transaction_id,$message_link);
		//						}
		//
		//					}
		//					/** --------------- EOF MAIL FUNCTION **/
		//				}
		//
		//			}
		//			Session::flash('message',(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_SUXS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_SUXS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCEL_SUXS'));
		//		}
		
		
        public function cancelOrder(Request $request)
        {
            //echo $request->reason.'/'.$request->orderId;test/24,25
            $order_ids = explode(',',$request->orderId);
            $customerDetArray = array();
            $storeDetArray = array();
            //print_r($order_ids); exit;
            if(count($order_ids) > 0 )
            {
                foreach($order_ids as $order_id)
                {
                    $ord_grand = DB::table('gr_order')->select('ord_grant_total','ord_delivery_fee','ord_merchant_id','ord_pay_type','ord_transaction_id','ord_admin_amt','gr_merchant.refund_status')
					->leftJoin('gr_merchant','gr_order.ord_merchant_id','=','gr_merchant.id')
					->where('ord_id','=',$order_id)->first();
                    if(empty($ord_grand)===false)
                    {
						DB::table('gr_order')->where('ord_id', $order_id)->update(['ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s')]);
                        $ord_transaction_id=$ord_grand->ord_transaction_id;
                        //echo $ord_transaction_id.'<br>';
                        $get_total_order_count = get_total_order_count($ord_transaction_id);
                        $get_total_cancelled_count = get_total_cancelled_count($ord_transaction_id);
                        if($get_total_cancelled_count==$get_total_order_count)
                        {
                            $add_delfee_status=1;
							$cancel_amt = $ord_grand->ord_grant_total + $ord_grand->ord_delivery_fee;
							$mer_amount = $ord_grand->ord_grant_total-$ord_grand->ord_admin_amt+$ord_grand->ord_delivery_fee;
						}
                        else
                        {
                            $add_delfee_status=0;
							$cancel_amt = $ord_grand->ord_grant_total;
							$mer_amount = $ord_grand->ord_grant_total-$ord_grand->ord_admin_amt;
						}
                        /** update cancel status **/ 
                        /** while user cancel the item, admin detect commission from user's item grant total amount **/
                        /* If order is cod, then admin commission for the cancelled item will be 0.00 */
                        /* if refund status is no, no need to add cancellation amount */
                        $adm_comm_order = $ord_grand->ord_admin_amt;  //update in gr_order table
                        $adm_comm_Overallorder = 0.00;  //update in gr_emrchant_overallordere table
                        if($ord_grand->ord_pay_type == 'COD')
                        {
                        	$adm_comm_order = 0.00;
                        	$adm_comm_Overallorder = $ord_grand->ord_admin_amt;
						}
                        if($ord_grand->refund_status == 'Yes')
                        {
                            DB::table('gr_order')->where('ord_id', $order_id)
							->update([	'add_delfee_status'=>$add_delfee_status,
							'ord_cancel_status' => '1',
							'ord_cancel_reason'=>$request->reason,
							'ord_cancel_date'=>date('Y-m-d H:i:s'),
							'ord_cancel_amt' => ($ord_grand->ord_grant_total- $ord_grand->ord_admin_amt),
							'ord_admin_amt' => $adm_comm_order 
							]);
                            /** update merchant amount **/
                            DB::table('gr_merchant_overallorder')->where('or_mer_id', $ord_grand->ord_merchant_id)
							->update([	'or_cancel_amt' => DB::raw('or_cancel_amt+'.($cancel_amt-$ord_grand->ord_admin_amt)), //175
							'or_mer_amt' => DB::raw('or_mer_amt+'.($mer_amount)),      //175
							'or_admin_amt' => DB::raw('or_admin_amt-'.($adm_comm_Overallorder))//0
							]);
						}
                        else
                        {   
                            DB::table('gr_order')->where('ord_id', $order_id)->update(['add_delfee_status'=>$add_delfee_status,'ord_cancel_status' => '1','ord_cancel_reason'=>$request->reason,'ord_cancel_date'=>date('Y-m-d H:i:s'),'ord_admin_amt' => $adm_comm_order]);
						}
					}
					
					
                    //GET CUSTOMER DETAILS
                    $customerDet = Home::get_cancelled_customer_byOrderId($order_id);
                    if(empty($customerDet) === false)
                    {
                        $customerDetArray[$customerDet->ord_transaction_id]=$customerDet;
					}
                    //GET PRODUCT DETAILS
                    $storeDet = Home::get_cancelled_store_byOrderId($order_id);
                    if(count($storeDet)> 0 )
                    {
                        foreach($storeDet as $stDet)
                        {
                            $storeDetArray[$stDet->st_store_name.'`'.$stDet->mer_email.'`'.$stDet->ord_merchant_id][]=Home::get_cancelled_order_byOrderId($order_id,$stDet->id);
						}
					}
					
					
				}
				//print_r($storeDetArray); exit;
                foreach($customerDetArray as $key=>$customerDet)
                {
                    $transaction_id = $key;
				}
                if(empty($customerDet) === false)
                {
                    /** ------------------ MAIL FUNCTION  ----------------------------- **/
                    //1) MAIL TO CUSTOMER
                    $customerMail =  $customerDet->order_ship_mail;
                    $send_mail_data = array('order_details'	=> $storeDetArray,'transaction_id'=>$transaction_id);
                    Mail::send('email.order_cancel_mail_customer', $send_mail_data, function($message) use($customerMail)
                    {
                        $message->to($customerMail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
					});
                    //2) MAIL TO ADMIN
                    $adminMail = Config::get('admin_mail');
                    $send_mail_data = array('order_details'	=> $storeDetArray,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id);
                    Mail::send('email.order_cancel_mail_admin', $send_mail_data, function($message) use($adminMail)
                    {
                        $message->to($adminMail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
					});
                    //3) MAIL TO MERCHANT
                    if(count($storeDetArray) > 0 )
                    {	
                    	$message_link = 'mer-order-details/'.base64_encode($transaction_id);
                        foreach($storeDetArray as $key=>$itemsDet)
                        {	
                            $explodeRes = explode('`',$key);
                            $merchantEmail = $explodeRes[1];
                            $merchantId = $explodeRes[2];
                            /* send notification to mobile */
                            if(count($itemsDet) > 0 )
                            {
                            foreach($itemsDet as $det)
                            {
                            	foreach($det as $items)
                            	{
		                            $got_message = (Lang::has(Session::get('front_lang_file').'.FRONT_CANCELL_NOTIFI')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELL_NOTIFI') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELL_NOTIFI') ;
									$searchReplaceArray = array(':user' => ucfirst($items->cus_fname),':item' => $items->productName,':order_id' => $transaction_id);
									$result = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message); 
									/* web notification */
		                            $noti_id= push_notification($items->ord_cus_id,$merchantId,'gr_customer','gr_merchant',$result,$transaction_id,$message_link);
									$mer_details = get_details('gr_merchant',['id'=>$merchantId],('mer_andr_fcm_id,mer_ios_fcm_id'));
									if(empty($mer_details) === false)
									{
										
										if($mer_details->mer_andr_fcm_id !='')
										{
											$json_data = ["to" => $mer_details->mer_andr_fcm_id,
														  "data"		=> ['transaction_id'	=> $transaction_id,
																			'type'				=> 'Cancelled order',
																			'notification_id'	=> $noti_id,
																			"body" => $result,
																			"title" => "Order Cancelled Notification"]
															];
											$notify = sendPushNotification($json_data,ANDR_FIREBASE_API_KEY_MER);
										}
										if($mer_details->mer_ios_fcm_id !='')
										{
											$json_data = ["to" => $mer_details->mer_ios_fcm_id,
															"data"		=> ['transaction_id'	=> $transaction_id,
																			'type'				=> 'Cancelled order',
																			'notification_id'	=> $noti_id,
																			"body" 				=> $result,
																			"title" => "Order Cancelled Notification"]
															];
											$notify = sendPushNotification($json_data,IOS_FIREBASE_API_KEY_MER);
										}
										
									}
								}
							}
							}
							/* send notification to mobile ends */
                            $send_mail_data = array('order_details'	=> $itemsDet,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id,'storename'=>$explodeRes[0]);
                            Mail::send('email.order_cancel_mail_merchant', $send_mail_data, function($message) use($merchantEmail)
                            {
                                $message->to($merchantEmail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
							});
						}
						
					}
                    /** --------------- EOF MAIL FUNCTION **/
				}
				
			}
            Session::flash('message',(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL_SUXS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL_SUXS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCEL_SUXS'));
		}
		
		
		public function TrackOrder($id)
		{
			$id=base64_decode($id);
			$pagetitle = (Lang::has(Session::get('front_lang_file').'.ADMIN_TRACK_ORDER')) ? trans(Session::get('front_lang_file').'.ADMIN_TRACK_ORDER') : trans($this->FRONT_LANGUAGE.'.ADMIN_TRACK_ORDER');
			
			$customer_details = DB::table('gr_order')->select('ord_transaction_id','ord_date','ord_pre_order_date','ord_shipping_cus_name','ord_shipping_address','ord_shipping_address1','ord_shipping_mobile','ord_shipping_mobile1','order_ship_mail','ord_failed_reason')->where('ord_transaction_id',$id)->first();
			
			$storewise_details   = Customer::track_reports($id);
			$customer_id 		 =  Session::get('customer_id'); 
			$store_review = '';
			$restaurant_review = '';
			//echo '<pre>'; print_r($storewise_details); exit;
			return view ('Front.trackorder')->with('storewise_details',$storewise_details)->with('pagetitle',$pagetitle)->with('restaurant_review',$restaurant_review)->with('store_review',$store_review)->with('customer_details',$customer_details);
		}
		
		
		
		public function TrackOrder1($id)
		{
			$id=base64_decode($id);
			$pagetitle = (Lang::has(Session::get('front_lang_file').'.ADMIN_TRACK_ORDER')) ? trans(Session::get('front_lang_file').'.ADMIN_TRACK_ORDER') : trans($this->FRONT_LANGUAGE.'.ADMIN_TRACK_ORDER');
			$storewise_details   = Customer::track_reports($id);
			$customer_id 		 =  Session::get('customer_id'); 
			$restaurant_review 	 = Customer::restaurant_review($customer_id);
			$store_review 		 = Customer::store_review($customer_id);
			/*//print_r($storewise_details);
				foreach($storewise_details as $key=>$st_details)
				{
				echo $key.'<hr>';
				foreach($st_details['delivery_detail'] as $st_detail)
				{
				print_r($st_detail); echo '<hr>';
				//echo $st_detail->ord_id.'<bR>';
				}
				foreach($st_details['itemDet'] as $st_detail2)
				{
				print_r($st_detail2); echo '<hr>';
				//echo $st_detail->ord_id.'<bR>';
				}
				}
			exit;*/
			return view ('Front.trackorder')->with('storewise_details',$storewise_details)->with('pagetitle',$pagetitle)->with('restaurant_review',$restaurant_review)->with('store_review',$store_review);
			
		}
		public function payment_settings(Request $request)
		{
			$customer_id  = Session::get('customer_id');
			//starts here
			$pagetitle = (Lang::has(Session::get('front_lang_file').'.ADMIN_PAYMENT_SETTINGS')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYMENT_SETTINGS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PAYMENT_SETTINGS');
			$getCustomer = DB::table('gr_customer')->where('cus_id', '=', $customer_id)->first();
			if($_POST) { 
				/*print_r($_POST); exit;Array ( [_token] => 69yWhaKG346HnBrY4yOxCbZUCAE8ym8DAxf87Kwo [cus_paynamics_status] => Publish [mer_paynamics_clientid] => clientid [mer_paynamics_secretid] => Secret ID [cus_paynamics_mode] => Sandbox [cus_paymaya_status] => Unpublish [cus_paymaya_clientid] => [cus_paymaya_secretid] => [cus_paymaya_mode] => Sandbox [cus_netbank_status] => Unpublish [cus_bank_name] => [cus_branch] => [cus_bank_accno] => [cus_ifsc] => )*/
				/* paypal or net banking details mandatory even stripe in publish status  */
				if(Input::get('cus_paymaya_status')=='Unpublish' && Input::get('cus_netbank_status')=='Unpublish')
				{
					$msg = (Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_ATLEAST')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_ATLEAST') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_ATLEAST');
					// return Redirect::to('user-payment-settings')->withErrors(['errors'=> $msg])->withInput();
					Session::flash('val_errors',$msg);
					return Redirect::to('user-payment-settings')->withInput();
				}
				
				if(Input::get('cus_paynamics_status')=='Publish')
				{
					$this->validate($request, 
					[
					'cus_paynamics_clientid'=>'Required',
					'cus_paynamics_secretid'=>'Required'
					],
					[
					'cus_paynamics_clientid.required'    => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT') , 
					'cus_paynamics_secretid.required'    => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')
					]
					); 
				}
				if(Input::get('cus_paymaya_status')=='Publish')
				{
					$this->validate($request, 
					[
					'cus_paymaya_clientid'=>'Required',
					'cus_paymaya_secretid'=>'Required'
					],
					[ 
					'cus_paymaya_secretid.required'    => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET'),
					'cus_paymaya_clientid.required'    => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')
					]
					); 
				}
				if(Input::get('cus_netbank_status')=='Publish')
				{
					$this->validate($request, 
					[
					'cus_bank_name'	=> 'Required',
					'cus_branch'	=> 'Required',
					'cus_bank_accno'=> 'Required',
					'cus_ifsc'		=> 'Required'
					],
					[ 
					'cus_bank_name.required' => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_BANK') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_BANK') ,
					'cus_branch.required'    => (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_BRANCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_BRANCH'),
					'cus_bank_accno.required'=> (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_ACCNO') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_ACCNO'),
					'cus_ifsc.required'    	=> (Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_IFSC') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_IFSC') 
					]
					); 
				}
				
				$profile_det = array(
				'cus_paynamics_status'	=> Input::get('cus_paynamics_status'),
				'cus_paynamics_clientid'=> Input::get('cus_paynamics_clientid'),
				'cus_paynamics_secretid'=> Input::get('cus_paynamics_secretid'),
				'cus_paynamics_mode'	=> Input::get('cus_paynamics_mode'),
				'cus_paymaya_status'	=> Input::get('cus_paymaya_status'),
				'cus_paymaya_clientid'	=> Input::get('cus_paymaya_clientid'),
				'cus_paymaya_secretid'	=> Input::get('cus_paymaya_secretid'),
				'cus_paymaya_mode'		=> Input::get('cus_paymaya_mode'),
				'cus_netbank_status'	=> Input::get('cus_netbank_status'),
				'cus_bank_name'			=> Input::get('cus_bank_name'),
				'cus_branch'			=> Input::get('cus_branch'),
				'cus_bank_accno'		=> Input::get('cus_bank_accno'),
				'cus_ifsc'				=> Input::get('cus_ifsc'),
				'cus_updated_date' 		=> date('Y-m-d H:i:s')
				);			
				//DB::connection()->enableQueryLog();
				DB::table('gr_customer')->where('cus_id', '=', Session::get('customer_id'))->update($profile_det);
				//$query = DB::getQueryLog();
				//print_r($query);
				//exit;
				
				$message = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE_SUCCESS') : trans($this->FRONT_LANGUAGE.'.ADMIN_UPDATE_SUCCESS');
				// return redirect('user-payment-settings')->with('message',$message);
				
				Session::flash('success',$message);
				return redirect('user-payment-settings');
			}
			else
			{
				//print_r($getCustomer); exit;
				return view('Front.payment_settings')->with('pagetitle',$pagetitle)->with('getvendor',$getCustomer);
			}
		}
		/* Refer friends */
		public function refer_friend(Request $request)
		{
			$refer_mail = $request->mail;
			$check = DB::table('gr_customer')->where('cus_email','=',$refer_mail)->where('cus_status','=','1')->count();
			/** check email in referel table **/
			$check_refer = DB::table('gr_referal')->where(['referre_email' => $refer_mail])->count();
			if(($check_refer == 0 ) && ($check == 0))
			{	
				/*	MAIL FUNCTION */
				$send_mail_data = array('name' => '','refer_mail'=>$refer_mail);
				Mail::send('email.referrel_mail', $send_mail_data, function($message) use($refer_mail)
				{
					$message->to($refer_mail)->subject("Referal mail");
				});
				/* EOF MAIL FUNCTION */ 
				
				/** add data in referal table **/
				$arr = ['referral_id' 		=> Session::get('customer_id'),
				'referre_email' 	=> $refer_mail,
				're_offer_percent' 	=> $request->offer,
				're_purchased'		=> '0'
				];
				$insert = insertvalues('gr_referal',$arr);
				$msg	= (Lang::has(Session::get('front_lang_file').'.FRONT_MAIL_SEND_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.FRONT_MAIL_SEND_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.FRONT_MAIL_SEND_SUCCESSFULLY');
				Session::flash('success',$msg);
				return Redirect::to('/');
			}
			else
			{
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_REF_EMAIL_ALREADY_EXISTS')) ? trans(Session::get('front_lang_file').'.FRONT_REF_EMAIL_ALREADY_EXISTS') : trans($this->FRONT_LANGUAGE.'.FRONT_REF_EMAIL_ALREADY_EXISTS');
				Session::flash('val_errors',$msg);
				return Redirect::to('/');
			}
		}
		public function TestOrder(Request $request)
		{		
			//$order_id = '163,164';
			//$order_id = '160,161';
			$orderIdArray = explode(',',$order_id);
			if(count($orderIdArray) > 0 )
			{
				$customerDetArray = array();
				$storeDetArray = array();
				foreach($orderIdArray as $orderId)
				{
					//GET CUSTOMER DETAILS
					$customerDet = Home::get_cancelled_customer_byOrderId($orderId);
					if(empty($customerDet) === false)
					{
						$customerDetArray[$customerDet->ord_transaction_id]=$customerDet;
					}
					//GET PRODUCT DETAILS
					$storeDet = Home::get_cancelled_store_byOrderId($orderId);
					if(count($storeDet)> 0 )
					{
						foreach($storeDet as $stDet)
						{
							$storeDetArray[$stDet->st_store_name.'`'.$stDet->mer_email][]=Home::get_cancelled_order_byOrderId($orderId,$stDet->id);
						}
					}
					
				}
				//echo array_keys($customerDetArray[0]);
				foreach($customerDetArray as $key=>$customerDet)
				{
					$transaction_id = $key;
				}
				//echo $transaction_id.'<hr>'; 
				//echo '<hr><pre>'; print_r($customerDetArray);
				//echo '<hr><pre>'; print_r($storeDetArray);
				//exit;
				if(empty($customerDet) === false)
				{
					$customerMail =  $customerDet->order_ship_mail;
					//1) MAIL TO CUSTOMER
					/*$send_mail_data = array('order_details'	=> $storeDetArray,'transaction_id'=>$transaction_id);
						Mail::send('email.order_cancel_mail_customer', $send_mail_data, function($message) use($customerMail)
						{	
						print_r($message);
						//$message->to($customerMail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
					});*/
					//2) MAIL TO ADMIN
					/*$adminMail = Config::get('admin_mail');
						echo '<br>Admin Mail'.$adminMail;
						$send_mail_data = array('order_details'	=> $storeDetArray,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id);
						Mail::send('email.order_cancel_mail_admin', $send_mail_data, function($message) use($adminMail)
						{	
						print_r($message);
						//$message->to($adminMail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
					});*/
					//3) MAIL TO MERCHANT
					if(count($storeDetArray) > 0 )
					{
						foreach($storeDetArray as $key=>$itemsDet)
						{
							$explodeRes = explode('`',$key);
							$merchantEmail = $explodeRes[1];
							$send_mail_data = array('order_details'	=> $itemsDet,'customerDet'=>$customerDetArray,'transaction_id'=>$transaction_id,'storename'=>$explodeRes[0]);
							Mail::send('email.order_cancel_mail_merchant', $send_mail_data, function($message) use($merchantEmail)
							{	
								print_r($message);
								//$message->to($merchantEmail)->subject((Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED_ORDER_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED_ORDER_DETAILS'));
							});
						}
						
					}
				}
			}
		}
		
		/** faq details **/
		public function faq()
		{
			$get_details = Home::get_faq();
			
			$pagetitle = (Lang::has(Session::get('front_lang_file').'.FRONT_FAQ')) ? trans(Session::get('front_lang_file').'.FRONT_FAQ') : trans($this->FRONT_LANGUAGE.'.FRONT_FAQ');
			return view('Front.view_faq')->with(['pagetitle' => $pagetitle,'faq_details' => $get_details]);
		}
		
		/* send verification mail */
		public function send_verification_mail(Request $request)
		{
			$code = mt_rand(100000, 999999);
			$mail =  $request->mail;
			$send_mail_data = ['code' => $code];
			/*$send = Mail::send('email.verification_mail', $send_mail_data, function($message) use($mail)
				{	
            	$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_CN_MAIL')) ? trans(Session::get('front_lang_file').'.FRONT_CN_MAIL') : trans($this->FRONT_LANGUAGE.'.FRONT_CN_MAIL');
            	$message->to($mail)->subject($msg);
			});*/
			
            /*if($send)
			{*/
			echo "success".'`'.$code;
			/* }
				else
				{
            	echo "fail".$code;
			}*/
		}

		public function send_verification_msg(Request $request)
		{	
			$prof_cus_phone = Input::get('phone');
			$cus_id = Session::get('customer_id');
			$otp = mt_rand(100000, 999999);
				try{
					Twilio::message($prof_cus_phone, $otp);
					$jsonArr = array('msg'=>'New','otp'=>$otp);
					echo "success".'`'.$otp;
				}
				catch (\Exception $e)
				{       
					
					$jsonArr = array('msg'=>$e->getMessage());
					echo "fail".'`'.$otp;
				}
				
			
		}
		
		/* save verify status */
		public function save_verify_status()
		{
			$update = updatevalues('gr_customer',['mail_verify' => '1'],['cus_id'=>Session::get('customer_id')]);
			
			echo "success";
		}
		
		public function refreshCaptcha()
	    {
	        return response()->json(['captcha'=> captcha_img()]);
		}
		
	    public function Reorder($id)
        {
			//$reorders=DB::table('gr_order')->select('*')->where('ord_transaction_id',base64_decode($id))->get(); 
			$current_time = date('H:i:s');
            $current_day=date('l');
			$reorders = $sql = DB::table('gr_order')->select('gr_order.*','gr_product.pro_had_choice','gr_product.pro_item_name','gr_product.pro_no_of_purchase','gr_product.pro_quantity','gr_product.pro_has_discount','gr_product.pro_original_price','gr_product.pro_discount_price','gr_product.pro_had_tax','gr_product.pro_tax_percent','gr_product.pro_currency','gr_store.st_pre_order',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Available','Closed') as store_closed"))
			->Join('gr_product','gr_product.pro_id','=','gr_order.ord_pro_id')
			->Join('gr_store','gr_store.id','=','gr_order.ord_rest_id')
			->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
			->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
			->Join('gr_proitem_maincategory','gr_proitem_maincategory.pro_mc_id','=','gr_product.pro_category_id')
			->Join('gr_proitem_subcategory','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
			->where(
			['gr_order.ord_transaction_id' => base64_decode($id),
			'gr_product.pro_status'=>'1',
			'gr_store.st_status'=>'1',
			'gr_merchant.mer_status'=>'1',
			'gr_category.cate_status' => '1',
			'gr_merchant.mer_status'=>'1',
			'gr_proitem_maincategory.pro_mc_status'=>'1',
			'gr_proitem_subcategory.pro_sc_status'=>'1'
			])
			->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity')
			->get();

            $selectreordercustomerid=DB::table('gr_order')->select('ord_cus_id')->where('ord_transaction_id',base64_decode($id))->first(); //Here we can use session customerid
			//remove previous items from cart after get customer conformation
            $removeitems=DB::table('gr_cart_save')->where('cart_cus_id',$selectreordercustomerid->ord_cus_id)->delete();
			//Here the value insert to add_cart
			$added_count=$not_added_count=$store_closed_count = 0;
			$error_msg = '';
			if(count($reorders) > 0){
				foreach ($reorders as $reorder) {
					if($reorder->st_pre_order==0 && $reorder->store_closed=='Closed')
					{
						$store_closed_count++;
					}
					if($reorder->st_pre_order==1 || $reorder->store_closed=='Available')
					{
						$ch_array = array();
						$grand_choice_price = 0;
						//Here check available quantity of product						/*$productqty=DB::table('gr_product')->select('pro_item_name','pro_no_of_purchase','pro_quantity','pro_has_discount','pro_discount_time','pro_discount_from','pro_discount_to','pro_original_price','pro_discount_price','pro_had_tax','pro_tax_percent','pro_currency','pro_had_choice')->where('pro_id',$reorder->ord_pro_id)->first();*/
						$availableqty=$reorder->pro_quantity-$reorder->pro_no_of_purchase;
						
						if($availableqty >= 1){
							
							/* CHOICE MANAGEMENT */
							if($reorder->pro_had_choice=='1'){
								$choices = $reorder->ord_choices;
								$choiceArray = json_decode($choices);
								if(count($choiceArray) > 0 ){
									foreach($choiceArray as $choiceElement){
										$choice_id = $choiceElement->choice_id;
										$choice_price = $choiceElement->choice_price;
										$choice_checking = DB::table('gr_choices')->select('ch_name')->where('ch_status','1')->where('ch_id',$choice_id)->first();
										if(empty($choice_checking)===false){
											$choice_price_det = DB::table('gr_product_choice')->select('pc_price')->where('pc_choice_id',$choice_id)->where('pc_pro_id',$reorder->ord_pro_id)->first();
											if(empty($choice_price_det)===false){
												$get_product_price = $choice_price_det->pc_price;
												$ch_array[] = array('choice_id' => $choice_id,'choice_price' => $get_product_price);
												$grand_choice_price += $get_product_price;
											}
										}
									}
								}
							}
							/* EOF CHOICE */
							if($availableqty >= $reorder->ord_quantity){
								$ord_quantity=$reorder->ord_quantity;
							}else{
								$ord_quantity=$availableqty;
							}
							
							if($reorder->pro_had_choice=='1'){ $hadChoice = 'Yes'; } else { $hadChoice = 'No'; $grand_choice_price = 0;}
							$now = date('Y-m-d H:i');
							if($reorder->pro_has_discount=='yes'){											
								$unit_price = $reorder->pro_discount_price;
							}else{
								$unit_price = $reorder->pro_original_price;
							}
							if($reorder->pro_had_tax=='Yes'){
								$single_tax_amount = ($unit_price*$reorder->pro_tax_percent)/100;
								$tax_amount = $ord_quantity*$single_tax_amount;
							}else{
								$tax_amount = 0;
							}
							//((($pro_price + $ch_price) * $qty) + $tax),
							if($reorder->ord_type=='Item') 		{ $cartype=2; }
							if($reorder->ord_type=='Product') 	{ $cartype=1; }
							$insert_arr = [	'cart_cus_id' 		=> $reorder->ord_cus_id,
							'cart_st_id' 		=> $reorder->ord_rest_id,
							'cart_item_id' 		=> $reorder->ord_pro_id,
							'cart_had_choice' 	=> $hadChoice,
							'cart_quantity' 	=> $ord_quantity,
							'cart_unit_amt' 	=> $unit_price,
							'cart_tax' 			=> $tax_amount,
							'cart_total_amt' 	=> ((($unit_price + $grand_choice_price) * $ord_quantity) + $tax_amount),//$reorder->ord_grant_total,
							'cart_currency' 	=> $reorder->pro_currency,
							'cart_type' 		=> $cartype,
							'cart_choices_id' 	=> json_encode($ch_array),
							'cart_updated_at' 	=> date('Y-m-d H:i:s')];
							$insert = insertvalues('gr_cart_save', $insert_arr);
							$added_count++;
						} else {
							$not_added_count++;
							$phrase = (Lang::has(Session::get('front_lang_file').'.FRONT_ITEM_OUTOFSTOCK')) ? trans(Session::get('front_lang_file').'.FRONT_ITEM_OUTOFSTOCK') : trans($this->FRONT_LANGUAGE.'.FRONT_ITEM_OUTOFSTOCK');
							$replaced_str = str_replace(':pdt_name',$reorder->pro_item_name,$phrase);
							$error_msg .=$replaced_str.'\\n';
						}
					}
				}
			}
            if (count($reorders)==$not_added_count) {
				$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_ALLITEM_OUTOFSTOCK')) ? trans(Session::get('front_lang_file').'.FRONT_ALLITEM_OUTOFSTOCK') : trans($this->FRONT_LANGUAGE.'.FRONT_ALLITEM_OUTOFSTOCK').'\\n';
				echo $added_count.'`'.$msg;
			} else {
				echo '-1';
				/*if($added_count==count($reorders)){
					echo '-1`';
				}
				else{
					if($store_closed_count > 0){
						$msg = (Lang::has(Session::get('front_lang_file').'.FRONT_STORE_CLOSED_PREORDERD')) ? trans(Session::get('front_lang_file').'.FRONT_STORE_CLOSED_PREORDERD') : trans($this->FRONT_LANGUAGE.'.FRONT_STORE_CLOSED_PREORDERD').'\\n';
						echo '1`'.$msg;
					}else{
						$phrase = (Lang::has(Session::get('front_lang_file').'.FRONT_REORDER_ERROR_MSG')) ? trans(Session::get('front_lang_file').'.FRONT_REORDER_ERROR_MSG') : trans($this->FRONT_LANGUAGE.'.FRONT_REORDER_ERROR_MSG');
						if($added_count > 1){
							$search_str = array(":added_count", ":verb", ":noun");
							$replace_str= array($added_count, "Items", "are");
							$new_string = str_replace($search_str, $replace_str, $phrase);
							$msg = $new_string.' \\n'.$error_msg;
							}else{
							$search_str = array(":added_count", ":verb", ":noun");
							$replace_str= array($added_count, "Item", "is");
							$new_string = str_replace($search_str, $replace_str, $phrase);
							$msg = $new_string.' \\n'.$error_msg;
						}
						echo $added_count.'`'.$msg;
					}
				}*/
			}
			
			//echo "test\\r\\ntest";
		}
		
		
		/* Insert News Letter Subscription */
		public function insert_newsletter_subscription()
		{
			$Email_Id = Input::get('Email_Id');
			$news_details = DB::table('gr_newsletter_subscription')->where([['news_status','!=',2],['news_email_id','=',$Email_Id]])->get();
			if(count($news_details)==0){
				$Details = array
				(
				'news_email_id' => $Email_Id,
				'news_status'=>1, // Unblock
				);
				$Newsletter_add = DB::table('gr_newsletter_subscription')->insert($Details);
				echo 1;
				exit;
			} 
			echo -1;
			exit;
		}
		
		/*search restaurant start*/
		
		public function search_restaurant(Request $request){
			
            $no_restaurant = (Lang::has(Session::get('front_lang_file').'.FRONT_NO_RESTAURANT')) ? trans(Session::get('front_lang_file').'.FRONT_NO_RESTAURANT') : trans($this->FRONT_LANGUAGE.'.FRONT_NO_RESTAURANT');
			$html = '';
			$htmlselc = '';
			$st_name = trim($request->get('search_res'));
			
			if($st_name != ''){
				
				$get_restaurant = Home::search_restaurantdet($st_name);
				if(count($get_restaurant) > 0){
					
					foreach($get_restaurant as $rest){
						
                        $st_name_en =$rest->st_store_name;
						
                        if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en'){
							
                            $st_name =$rest->st_store_name;
							}else{
                            $add_tit_tbl = 'st_store_name_'.Session::get('front_lang_code');
                            $st_name = $rest->$add_tit_tbl;
						}
						
						$htmlselc .='<a href="'.url('restaurant').'/'.$rest->store_slug.'"><li>'.$st_name.'</li></a>';
						
					}
                    $html ='<ul>'.$htmlselc.'</ul>';
                    return $html;
					}else{
					
                    return $html = '<ul><li>'.$no_restaurant.'</li></ul>';
				}
				
				
				}else{
				return $html = '<ul>'.$no_restaurant.'</ul>';
			}
			
			
			
			
		}
		
		
		public function add_searched_restaurant(){
			
			$rest = search_restaurantdet();
			$restaurant = "[".$rest."]";
			return $restaurant;
		}
		
		public function restaurant_redirect(Request $request){			
			$name = $request->get('name');
			
			$get_res_id = DB::table('gr_store')->select('id','st_store_name','store_slug')->where('st_store_name','=',$name)->first();
			
			if($get_res_id != ''){
				echo $get_res_id->store_slug;
				}else{
				echo 0;
			}
		}
		
		
		/*search restaurant end*/
		
		
		public function order_notification(Request $request)
		{
			$page_title = (Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_NOTIFICATION') : trans($this->FRONT_LANGUAGE.'.FRONT_ORDER_NOTIFICATION');
			return view('Front.order_notification')->with('pagetitle',$page_title);
			
		}
		
		public function order_notification_ajax(Request $request)
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
			->where('receiver_id','=',Session::get('customer_id'))
			->where('receiver_type','=','gr_customer')
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
			->where('receiver_id','=',Session::get('customer_id'))
			->where('receiver_type','=','gr_customer');
			if($orderId_search != '')
			{
				$q = $sql->whereRaw("order_id like '%".$orderId_search."%'"); 
			}
			if($message_search != '')
			{
				$q = $sql->whereRaw("message like '%".$message_search."%'"); 
			}
			if($view_search != '')
			{
				$q = $sql->where('read_status','=',$view_search); 
			}
			$totalFiltered = $sql->count();
			
			$q = $sql->orderBy($order,$dir)->orderBy('read_status', 'ASC')->skip($start)->take($limit);
			$posts =  $q->get();
			
			
			$data = array();
			if(!empty($posts))
			{
				$snoCount = $start;
				$view = (Lang::has(Session::get('front_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_VIEW') : trans($this->FRONT_LANGUAGE.'.ADMIN_VIEW');
				$read = (Lang::has(Session::get('front_lang_file').'.FRONT_READ_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.FRONT_READ_NOTIFICATION') : trans($this->FRONT_LANGUAGE.'.FRONT_READ_NOTIFICATION');
				$unread = (Lang::has(Session::get('front_lang_file').'.FRONT_UNREAD_NOTIFICATION')) ? trans(Session::get('front_lang_file').'.FRONT_UNREAD_NOTIFICATION') : trans($this->FRONT_LANGUAGE.'.FRONT_UNREAD_NOTIFICATION');
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
		
		/*  order summary */
		// public function order_summary(Request $request,$id)
		// {
		// 	$transaction_id = base64_decode($id);
		// 	echo "test"; exit;
		// }
		
		/*  order summary */
		public function order_summary(Request $request,$id)
		{
			$transaction_id = base64_decode($id);
			$cus_id 		= Session::get('customer_id');
			$page_title = (Lang::has(Session::get('front_lang_file').'.FRONT_OR_SUMMARY')) ? trans(Session::get('front_lang_file').'.FRONT_OR_SUMMARY') : trans($this->FRONT_LANGUAGE.'.FRONT_OR_SUMMARY');
			/*  get order details */
			$get_or_details = Customer::get_or_summary_details($transaction_id,$cus_id);
			$choices = array();
			if(count($get_or_details)>0)
			{
				foreach($get_or_details as $orders)
				{
					$splitted_choice=json_decode($orders->ord_choices,true);
					if(!empty($splitted_choice))
					{
						foreach($splitted_choice as $choice)
						{
							if(!isset($choices[$choice['choice_id']]))
							{
								$choices_name=DB::table("gr_choices")->where("ch_id","=",$choice)->first()->ch_name;
								$choices[$choices_name.'`'.$orders->ord_id]=$choice['choice_price'];
							}
						}
					}
					
				}
			}
			return view('Front.order_summary')->with(['or_details' => $get_or_details,'pagetitle' => $page_title,'choices'	=> $choices]);
		}
		
        /* refund details */
        public function refund_details($id)
        {
            $ord_id = base64_decode($id);
            $details = Home::get_refund($ord_id);
            $title = (Lang::has(Session::get('front_lang_file').'.FRONT_REF_DETAILS')) ? trans(Session::get('front_lang_file').'.FRONT_REF_DETAILS') : trans($this->FRONT_LANGUAGE.'.FRONT_REF_DETAILS');
            $pagetitle = $title.' - '.$ord_id;
            return view('Front.view_refund')->with(['cancel_details' => $details,'pagetitle' => $pagetitle]);
			
		}
		public function subscription_submit(Request $request)
		{
			$email = Input::get('email');
			$validator = Validator::make($request->all(), ['email' => ['required','email',
			Rule::unique('gr_newsletter_subscription','news_email_id')->where(function ($query)  {
				return $query->where('news_email_id','!=','');
			})
			]]);
			if ($validator->fails()) {
				echo $validator->messages()->first(); exit;
				}else{
				$check_email = DB::table('gr_newsletter_subscription')->where('news_email_id', '=', $email)->get();
				if (count($check_email) > 0) {
					echo "0";
					exit;
				}
				else {
					$email = Input::get('email');
					Mail::send('email.subscription_mail', array('email' => Input::get('email')) ,function ($message){
						$session_message = (Lang::has(Session::get('front_lang_file').'.EMAIL_HAS_BEEN_SUBSCRIPTION_SUCCESSFULLY')) ? trans(Session::get('front_lang_file').'.EMAIL_HAS_BEEN_SUBSCRIPTION_SUCCESSFULLY') : trans($this->FRONT_LANGUAGE.'.EMAIL_HAS_BEEN_SUBSCRIPTION_SUCCESSFULLY');
						//$session_message='Your email has been subscribed succesfully!';
						$message->to(Input::get('email'))->subject($session_message);
					});
					$Details = array('news_email_id' => Input::get('email'),'news_status'=>1);
					$Newsletter_add = DB::table('gr_newsletter_subscription')->insert($Details);
				}
				echo "1";
				exit;
			}
			
		}
		
	}			