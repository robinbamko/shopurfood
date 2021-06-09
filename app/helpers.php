<?php 
	function get_all_details($table,$where_status,$paginate = '',$order_by = '',$order_column = '',$where = '',$join='',$join_column1 = '',$join_column2 = '')
    {	
        $q =array();
		//DB::connection()->enableQueryLog();
        $sql = DB::table($table)->orderBy($order_column,$order_by);
        
        if($join != '')
        {
			$q = $sql->join($join,$join_column1,'=',$join_column2);
		}
        if($where != '')
        {
            $q = $sql->where($where);
		}
        
        if($where_status != '')
        {
			$q = $sql->where($where_status,'!=','2');
		}
		
        if($paginate != '')
        {
			$q = $sql->paginate($paginate);
		}
        else
        {
			
        	$q = $sql->get();
		}
		//$query = DB::getQueryLog();
		//print_r($query);
		//exit;
        
		return $q;
        
	}
	
	
    /** get specific id details **/
	function get_details($table,$where,$select = '')
    {
		if($select == '')
        {
			return DB::table($table)->where($where)->first();
		}
        else
        {
            return DB::table($table)->select(DB::raw($select))->where($where)->first();   
		}
	}
	
	/** insert values **/
    function insertvalues($table,$values)
    {
        return	 DB::table($table)->insert($values);
    	
	}
	
    /** update values **/
    function updatevalues($table,$values,$where)
    {
    	return DB::table($table)->where($where)->update($values);
    	
	}
	
	
	/* ------- Get count -----------*/
	function get_count($table,$where,$wheretatus = '',$val =''){
		$Qs = array();
		$sql = DB::table($table)->where($where);
		if($wheretatus != ''){
			$Qs = $sql->where($wheretatus,'=',$val)->count();
		}
		
		if($where != ''){
			$Qs = $sql->count();
		}
		return $Qs;
	}
	
	/**check name exist while add **/
	
    function check_name_exists($table,$where_status = '',$where='')
    {
    	$sql =  DB::table($table)->where($where_status,'!=','2');
        if($where != '')
        {
			$sql->where($where);
		}
        $q = $sql->get();
        return $q;
	}
	
    /** get specific details using pluck function **/
    function get_details_pluck($table,$where,$pluck = '')
    {
		return  DB::table($table)->where($where)->pluck('mer_fname','id');
	}
	
	
    /** get working hours **/
	function get_working_hours($id,$currentday = '')
    {
        return DB::table('gr_res_working_hrs')->select('wk_date','wk_closed','wk_start_time','wk_end_time')
		->where('wk_res_id','=',$id)
		->get()->toArray();
	}
	
	function get_category_shops($id,$type)
	{
        //date_default_timezone_set("Asia/Calcutta");
        $name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
        $cate_name = (Session::get('front_lang_code') == 'en') ? 'cate_name' : 'cate_name_'.Session::get('front_lang_code');
        $desc = (Session::get('front_lang_code') == 'en') ? 'st_desc' : 'st_desc_'.Session::get('front_lang_code');
        $current_time = date('H:i:s');
        $current_day=date('l');
        $user_lat = Session::get('search_latitude');
        $user_long= Session::get('search_longitude');
		
		$sql = DB::table('gr_store')->select('st_banner',$name .' as st_name','gr_category.'.$cate_name.' as category_name',$desc.' as st_desc','st_logo','gr_category.cate_id','gr_store.id',DB::Raw("IF((SELECT count(*) FROM `gr_res_working_hrs` WHERE `wk_res_id`=gr_store.id AND `wk_date`='$current_day' AND STR_TO_DATE(`wk_start_time`, '%l:%i %p') <= '$current_time' AND STR_TO_DATE(`wk_end_time`, '%l:%i %p') >= '$current_time')>0,'Avail','Closed') as store_closed"))
		->Join('gr_category','gr_category.cate_id','=','gr_store.st_category')
		->Join('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
		->where(['gr_merchant.mer_status' => '1'])
		->where(['gr_category.cate_status' => '1'])
		->where(['gr_category.cate_type' => $type])
		->where(['gr_store.st_category' => $id])
		->where(['gr_store.st_status'=>'1'])
		->where(['gr_store.st_type' => $type]);
        if($user_lat != '' && $user_long != '')
        { 
            /*$q = $sql->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
            $q = $sql->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
		}
        $q = $sql->limit(9)->get();
        return $q;
        //$query = DB::getQueryLog();
        //print_r($query);
        //exit;
		
	}
	
    function get_sub_categories($mc_id,$id,$type)
    {   
        $cate_name = (Session::get('front_lang_code') == 'en') ? 'pro_sc_name' : 'pro_sc_name_'.Session::get('front_lang_code');
        return DB::table('gr_proitem_subcategory')->select('gr_proitem_subcategory.pro_sc_id','gr_proitem_subcategory.'.$cate_name.' as sc_name','gr_proitem_subcategory.pro_main_id')
		->Join('gr_product','gr_proitem_subcategory.pro_sc_id','=','gr_product.pro_sub_cat_id')
		->Join('gr_proitem_maincategory','gr_proitem_subcategory.pro_main_id','=','gr_proitem_maincategory.pro_mc_id')
		->where(['gr_product.pro_status' => '1',
		'gr_proitem_subcategory.pro_sc_status' => '1',
		'gr_product.pro_store_id' => $id,
		'gr_proitem_subcategory.pro_sc_type' => $type,
		'gr_proitem_maincategory.pro_mc_status' => '1',
		'gr_proitem_subcategory.pro_main_id' => $mc_id
		])
		->groupBy('gr_product.pro_sub_cat_id')
		->get();
	}
	function vehicle_option()
	{
		$DELMGR_OUR_LANGUAGE = \Request::get('DELMGR_OUR_LANGUAGE');
		$vehicleArray =array(''=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT'),
		'Truck'=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TRUCK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TRUCK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TRUCK'),
		'Two Wheeler'=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TWO_WHEELER')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TWO_WHEELER') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TWO_WHEELER'),
		'Both'=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BOTH')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BOTH') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BOTH'));
		return $vehicleArray;
	}
	
	
    /** check user exists for forget password **/
    function check_user($where = '')
    {
        return DB::table('gr_customer')->where($where)->count();
	}
	
    /** get customer details **/
    function get_user($where = '')
    {
        return DB::table('gr_customer')->select('cus_id','cus_email','cus_phone1','cus_fname','cus_wallet')->where($where)->first();
	}
	
    /** generate random password ***/
    function rand_password()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
		return  substr( str_shuffle( $chars ), 0, 8 );
	}
	
    /** get choices **/
    function get_choices($id)
    {   $name = (Session::get('front_lang_code') == 'en') ? 'ch_name' : 'ch_name_'.Session::get('front_lang_code');
        return DB::table('gr_product_choice')->select('gr_choices.'.$name.' as ch_name','pc_price','gr_choices.ch_id')
		->Join('gr_choices','gr_choices.ch_id','=','gr_product_choice.pc_choice_id')
		->Join('gr_product','gr_product.pro_id','=','gr_product_choice.pc_pro_id')
		->where(['pc_pro_id' => $id,'gr_choices.ch_status' => '1','gr_product.pro_status' => '1'])
		->get();
	}
	function get_choice_name($ch_id)
    {   $name = (Session::get('front_lang_code') == 'en') ? 'ch_name' : 'ch_name_'.Session::get('front_lang_code');
        return DB::table('gr_choices')->select('gr_choices.'.$name.' as ch_name')
		->where(['ch_id' => $ch_id,'gr_choices.ch_status' => '1'])
		->first();
	}
	function get_choice_name_even_its_blocked($ch_id)
    {   $name = (Session::get('front_lang_code') == 'en') ? 'ch_name' : 'ch_name_'.Session::get('front_lang_code');
        return DB::table('gr_choices')->select('gr_choices.'.$name.' as ch_name')
		->where(['ch_id' => $ch_id])//,'gr_choices.ch_status' => '1'
		->first();
	}
    /** check already exist cart **/
	
    function check_cart($cus_id,$st_id,$item_id,$ch_list ='',$curr = '')
    {
		//$selected_choices = json_encode($ch_list);
		return $query = DB::table('gr_cart_save')->select('cart_id','cart_quantity','cart_choices_id')
		->where(['cart_cus_id' => $cus_id,
		'cart_item_id' => $item_id,
		'cart_choices_id'=>$ch_list,
		'cart_st_id'    =>$st_id,
		//'cart_currency' => $curr
		])->first();
		
	}
	
	function remove_cart_choice($choice_id){
		$result = DB::table('gr_cart_save')->select('cart_id','cart_quantity','cart_total_amt','cart_choices_id')->where('cart_had_choice','=','Yes')->get();
		if(count($result) > 0 ){
			$ch_array= array();
			
			foreach($result as $res){
				$subtracted_price = 0;
				$json_choice = $res->cart_choices_id;
				$decoded_json = json_decode($json_choice,true);
				//print_r($decoded_json);
				if(count($decoded_json) > 0 ){
					foreach($decoded_json as $decod){
						//echo $decod['choice_id'].'/'.$decod['choice_price'].'<br>';
						if($decod['choice_id']==$choice_id){
							$subtracted_price += $res->cart_quantity*$decod['choice_price'];
						} else { 
							$ch_array[] = array("choice_id" => $decod['choice_id'],"choice_price" => $decod['choice_price']);
							
						}
					}
				}
				$insert = updatevalues('gr_cart_save',array('cart_choices_id'=>json_encode($ch_array),'cart_total_amt'=>($res->cart_total_amt-$subtracted_price)),['cart_id' => $res->cart_id]);
			}
		}
	}
    function update_cart_price($id=''){
		$now = date('Y-m-d H:i'); //reorder
		if($id != '')
		{
			$cart_cus_id = (int)$id;
		}
		else
		{
			$cart_cus_id = (int)Session::get('customer_id');
		}
		$group_sql = DB::table('gr_cart_save')->select('cart_item_id')->where('cart_cus_id',$cart_cus_id)->groupBy('cart_item_id')->get();
		if(count($group_sql) > 0 ){
			foreach($group_sql as $gs){
				$detils = DB::table('gr_product')->select('pro_original_price','pro_has_discount','pro_discount_price','pro_had_tax','pro_tax_percent','pro_had_choice')->where('pro_id',$gs->cart_item_id)->first();
				if($detils->pro_has_discount == 'yes')
				{
					$pro_priceVar = $detils->pro_discount_price;
				}else{
					$pro_priceVar = $detils->pro_original_price;
				}
				if($detils->pro_had_tax=='Yes'){
					$single_tax_amount = ($pro_priceVar*$detils->pro_tax_percent)/100;
				}else{
					$single_tax_amount = 0;
				}
				$individual_sql = DB::table('gr_cart_save')->select('cart_id','cart_quantity','cart_choices_id')->where('cart_cus_id',Session::get('customer_id'))->where('cart_item_id',$gs->cart_item_id)->get();
				if(count($individual_sql) > 0 ){
					foreach($individual_sql as  $insql){
						$grand_choice_price=0;
						$cart_had_choice = 0;
						$ch_array = array();
						$choiceArray = json_decode($insql->cart_choices_id);
						if(count($choiceArray) > 0 ){
							foreach($choiceArray as $choiceElement){
								$choice_id = $choiceElement->choice_id;
								$choice_checking = DB::table('gr_choices')->select('ch_name')->where('ch_status','1')->where('ch_id',$choice_id)->first();
								if(empty($choice_checking)===false){
									$choice_price_det = DB::table('gr_product_choice')->select('pc_price')->where('pc_choice_id',$choice_id)->where('pc_pro_id',$gs->cart_item_id)->first();
									if(empty($choice_price_det)===false){
										$cart_had_choice++;
										$get_product_price = $choice_price_det->pc_price;
										$ch_array[] = array('choice_id' => $choice_id,'choice_price' => $get_product_price);
										$grand_choice_price += $get_product_price;
									}
								}
							}
						}
						$cart_unit_amt = $pro_priceVar;
						$tax_amount = $insql->cart_quantity*$single_tax_amount;
						$cart_total_amt = ((($pro_priceVar + $grand_choice_price) * $insql->cart_quantity) + $tax_amount);
						if($cart_had_choice > 0 ) { $cart_had_choice='Yes'; } else { $cart_had_choice='No'; }  
						DB::table('gr_cart_save')->where('cart_id', $insql->cart_id)->update(['cart_unit_amt'=>$cart_unit_amt,'cart_choices_id' => json_encode($ch_array),'cart_tax'=>$tax_amount,'cart_had_choice'=>$cart_had_choice,'cart_total_amt' => $cart_total_amt]);
					}
				}
			}
		}
	}
    /** check quantity **/
    function check_qty($item_id)
    {
        return DB::table('gr_product')->select(DB::Raw('gr_product.pro_quantity-gr_product.pro_no_of_purchase AS stock'))->where(['pro_id'  => $item_id,'pro_status' => '1'])->first();
	}
	
    /** get payment method details **/
    function get_payment()
    {
        return DB::table('gr_payment_setting')->select('paynamics_status','paymaya_status','cod_status')->first();
	}
	
    /** delete cart **/
    function deletecart($id)
    {
        return DB::table('gr_cart_save')->where(['cart_id' => $id])->delete();
	}
	
    /** update quantity **/
    function update_quantity($qty,$id)
    {
        return DB::table('gr_product')->where(['pro_id' => $id])->update(['pro_no_of_purchase' => $qty]);
	}
	
    function cart_count($id,$st_id = '')
    {
        //return DB::table('gr_cart_save')->where(['cart_cus_id' => $id])->count();
        $q = 0;
		$sql =  DB::table('gr_cart_save')->select('`gr_cart_save`.`cart_id`')
		->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
		->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
		->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
		->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
		->where(['gr_cart_save.cart_cus_id' => $id,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
		->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity');
        if($st_id != '')
        {
            $q = $sql->where(['gr_cart_save.cart_st_id' => $st_id]);
		}
		$q = $sql->count();
        return $q;
		
	}
	function order_status_array($lang_file,$ourlang)
	{
		$ordarray = array(
		''=>(Lang::has(Session::get($lang_file).'.ADMIN_SELECT')) ? trans(Session::get($lang_file).'.ADMIN_SELECT') : trans($ourlang.'.ADMIN_SELECT'),
		'1'=>(Lang::has(Session::get($lang_file).'.MER_NEW_ORDER')) ? trans(Session::get($lang_file).'.MER_NEW_ORDER') : trans($ourlang.'.MER_NEW_ORDER'),
		'2'=>(Lang::has(Session::get($lang_file).'.MER_ACCEPTED')) ? trans(Session::get($lang_file).'.MER_ACCEPTED') : trans($ourlang.'.MER_ACCEPTED'),
		'3'=>(Lang::has(Session::get($lang_file).'.MER_REJECTED')) ? trans(Session::get($lang_file).'.MER_REJECTED') : trans($ourlang.'.MER_REJECTED'),
		'4'=>(Lang::has(Session::get($lang_file).'.MER_PREPARE_DELIVER')) ? trans(Session::get($lang_file).'.MER_PREPARE_DELIVER') : trans($ourlang.'.MER_PREPARE_DELIVER'),
		'5'=>(Lang::has(Session::get($lang_file).'.MER_DISPATCHED')) ? trans(Session::get($lang_file).'.MER_DISPATCHED') : trans($ourlang.'.MER_DISPATCHED'),
		'6'=>(Lang::has(Session::get($lang_file).'.MER_STARTED')) ? trans(Session::get($lang_file).'.MER_STARTED') : trans($ourlang.'.MER_STARTED'),
		'7'=>(Lang::has(Session::get($lang_file).'.MER_ARRIVED')) ? trans(Session::get($lang_file).'.MER_ARRIVED') : trans($ourlang.'.MER_ARRIVED'),				
		'8'=>(Lang::has(Session::get($lang_file).'.MER_DELIVERED')) ? trans(Session::get($lang_file).'.MER_DELIVERED') : trans($ourlang.'.MER_DELIVERED'),
		'9'=>(Lang::has(Session::get($lang_file).'.MER_FAILED')) ? trans(Session::get($lang_file).'.MER_FAILED') : trans($ourlang.'.MER_FAILED'));
		return $ordarray;
	}
	
    function get_general_settings()
    {
        return DB::table('gr_general_setting')->select('gs_refer_friend','gs_offer_percentage')->first();
	}
	function get_cancellation_policy($mer_id)
	{
		return DB::table('gr_merchant')->select('mer_cancel_policy')->where('id','=',$mer_id)->first();
	}
	function get_refund_status($mer_id)
	{
		return DB::table('gr_merchant')->select('refund_status')->where('id','=',$mer_id)->first();
	}
	function get_mer_cancel_status($mer_id)
	{
		return DB::table('gr_merchant')->select('cancel_status')->where('id','=',$mer_id)->first();
	}
    /** get paid commission details **/
    function get_paid_commission($id)
    {
        $sql =  DB::table('gr_merchant_commission')->select(DB::Raw('SUM(gr_merchant_commission.commission_paid) as paid_commission'),'commission_currency')
		->where(['commission_mer_id' => $id,'mer_commission_status' => '2'])
		->get();
        if(count($sql) > 0)
        {
            foreach($sql as $q)
            {
                return $q->paid_commission;
			}
		}
        else
        {
            return 0;
		}
	}
	
    /** get pay request notification from merchant **/
    function pay_request_notification($id)
    {
		return  DB::table('gr_notification')->where(['no_status' => '1','no_mer_id' => $id])->count();
	}
    function get_total_order_count($ord_transaction_id)
	{
		return  DB::table('gr_order')->where(['ord_transaction_id' => $ord_transaction_id])->count();
	}
	function get_total_cancelled_count($ord_transaction_id)
	{
		return  DB::table('gr_order')->where(['ord_transaction_id' => $ord_transaction_id,'ord_cancel_status'=>1])->count();
	}
	function get_total_cannotcancelled_count($ord_transaction_id)
	{
		return  DB::table('gr_order')->where('ord_transaction_id',$ord_transaction_id)->where('ord_status','>','1')->count();
	}
	function get_commision($ord_merchant_id)
	{
		return  DB::table('gr_merchant')->select('mer_commission')->where(['id' => $ord_merchant_id])->first()->mer_commission;
	}
	function getCustomerRateStatus($del_mgr_id)
	{
		return  DB::table('gr_delivery_manager')->select('dm_customer_rating')->where(['dm_id' => $del_mgr_id])->first();
	}
	function get_status_count_byTransId($ord_transaction_id,$merchantid)
	{
		return DB::table('gr_order')->select('ord_id',DB::raw("sum(case when ord_status = '3' AND ord_cancel_status='1' then 1 else 0 end) as rejected, sum(case when ord_status != '3' AND ord_cancel_status='1' then 1 else 0 end) as cancelled,sum(case when ord_status = '1' AND ord_cancel_status !='1' then 1 else 0 end) as unassigned,sum(case when ord_status > '3' AND ord_cancel_status !='1' then 1 else 0 end) as assigned, count(*) as totals"))->where('ord_transaction_id',$ord_transaction_id)->where('ord_merchant_id',$merchantid)->groupBy('ord_transaction_id')->first();
	}
	
	
	function get_status_count_byTransIdOnly($ord_transaction_id)
	{
		return DB::table('gr_order')->select('ord_id',DB::raw("sum(case when ord_status = '3' AND ord_cancel_status='1' then 1 else 0 end) as rejected, sum(case when ord_status != '3' AND ord_cancel_status='1' then 1 else 0 end) as cancelled,sum(case when ord_status = '1' AND ord_cancel_status !='1' then 1 else 0 end) as unassigned,sum(case when ord_status > '3' AND ord_cancel_status !='1' then 1 else 0 end) as assigned, count(*) as totals,sum(case when ord_status = '9' then 1 else 0 end) as failed"))->where('ord_transaction_id',$ord_transaction_id)->groupBy('ord_transaction_id')->first();
	}
	function get_active_ordStatus_byTransId($ord_transaction_id,$merchantid)
	{
		return DB::table('gr_order')->select('ord_status','ord_self_pickup')->where('ord_status','!=','3')->where('ord_cancel_status','!=','1')->where('ord_transaction_id',$ord_transaction_id)->where('ord_merchant_id',$merchantid)->first();
	}
	
	function convertCurrency_old($from_Currency, $to_Currency, $amount)
	{	
		$amount = urlencode($amount);
		$from_Currency = urlencode($from_Currency);
		$to_Currency = urlencode($to_Currency);	
		$html = file_get_contents("https://www.xe.com/currencyconverter/convert/?Amount=".$amount."&From=".$from_Currency."&To=".$to_Currency);
		echo $html; exit;
		$dom = new \DOMDocument;
		$internalErrors = libxml_use_internal_errors(true);
		
		$dom->loadHTML($html);
		foreach ($dom->getElementsByTagName('span') as $node) {
			if ($node->hasAttribute('class') && strstr($node->getAttribute('class'), 'uccResultAmount')){
				$convertedAmt=explode(".",$dom->saveHtml($node));
				$repClass=str_replace('<span class="uccResultAmount">','',$convertedAmt[0]); 
				$twoGt=str_split($convertedAmt[1],2);
				return $repClass.".".$twoGt[0];
			}	 
		}
		libxml_use_internal_errors($internalErrors);
	}
	
    function convertCurrency($from, $to, $amount)    
    {
        ini_set("allow_url_fopen", 1);
		
		//https://free.currencyconverterapi.com/api/v6/convert?q=USD_PHP&compact=ultra&apiKey=c704d0d634d384c60ae6
		$apiKey = DB::table('gr_general_setting')->select('gs_currency_api')->first();
        $url = @file_get_contents('http://free.currencyconverterapi.com/api/v6/convert?q=' . $from . '_' . $to . '&compact=ultra&apiKey='.$apiKey->gs_currency_api);
		if($url === FALSE) { 
			return $amount;
		}else{
			$json = json_decode($url, true);
			$rate = implode(" ",$json);
			$total = $rate * $amount;
			$rounded = number_format(round($total),2, '.', '');
			return $rounded;
		}
      
	}
    function convertCurrency_withoutRound($from, $to, $amount)    
    {
        ini_set("allow_url_fopen", 1);
		$apiKey = DB::table('gr_general_setting')->select('gs_currency_api')->first();
        $url = @file_get_contents('https://free.currencyconverterapi.com/api/v6/convert?q=' . $from . '_' . $to . '&compact=ultra&apiKey='.$apiKey->gs_currency_api);
		if($url === FALSE) { 
			return $amount;
		}else{
			$json = json_decode($url, true);
			$rate = implode(" ",$json);
			$total = $rate * $amount;
			$rounded = number_format(round($total),2, '.', '');
			return $total;
		}
	}
    
	
    function get_related_details($table,$where,$select,$type = '')
    {
        if($type == '')
        {
            return DB::table($table)->select($select)->where($where)->get();
		}
        else
        {
            return DB::table($table)->select($select)->where($where)->first();   
		}
	}
	
    function get_merchant_details($st_id)
    {
        return DB::table('gr_store')->select('st_mer_id','mer_email','mer_fname')
		->leftJoin('gr_merchant','gr_merchant.id','=','gr_store.st_mer_id')
		->where(['gr_store.id' => $st_id])
		->first();
	}
	
    function delete_store_images($related_details)
    {
        $images = explode('/**/',$related_details->st_banner);
        for($i=0; $i<count($images); $i++)
        {   
			
            if(File::exists(public_path('images/store/banner/').$images[$i]))
            {
                File::delete(public_path('images/store/banner/').$images[$i]);
			}
		}
        if(File::exists(public_path('images/store/').$related_details->st_logo))
        {
            File::delete(public_path('images/store/').$related_details->st_logo);
		}
        return "1";
	}
	
    function delete_restaurant_images($related_details)
    {
        $images = explode('/**/',$related_details->st_banner);
        for($i=0; $i<count($images); $i++)
        {   
			
            if(File::exists(public_path('images/restaurant/banner/').$images[$i]))
            {
                File::delete(public_path('images/restaurant/banner/').$images[$i]);
			}
		}
        if(File::exists(public_path('images/restaurant/').$related_details->st_logo))
        {
            File::delete(public_path('images/restaurant/').$related_details->st_logo);
		}
        return "1";
	}
	
    function delete_product_images($related_details)
    {
        $images = explode('/**/',$related_details->pro_images);
        for($i=0; $i<count($images); $i++)
		{   
			if(File::exists(public_path('images/store/products/').$images[$i]))
			{
				File::delete(public_path('images/store/products/').$images[$i]);
			}
		}
		return "1";
	}
	
    function delete_item_images($related_details)
    {
		$images = explode('/**/',$related_details->pro_images);
        for($i=0; $i<count($images); $i++)
		{
			if(File::exists(public_path('images/restaurant/items/').$images[$i]))
			{
				File::delete(public_path('images/restaurant/items/').$images[$i]);
			}
		}
        return "1";
	}
	function getStoreUsedThisCategory($cat_id,$cat_type)
	{
		return DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`st_store_name`,'</li>'))  AS usedStore,COUNT(*) AS usedCount FROM `gr_store` WHERE `st_category` ='".$cat_id."' AND `st_type`='".$cat_type."' AND st_status <> '2' ");
	}
	
	//    function getStoreUsedThisCategory($cat_id,$cat_type,$action)
	//    {
	//        if($action=='block') { $remain_qry = " AND st_status = '1'"; } elseif($action=='delete'){ $remain_qry = " AND st_status != '2'"; } else { $remain_qry=''; }
	//        return DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`st_store_name`,'</li>'))  AS usedStore,COUNT(*) AS usedCount FROM `gr_store` WHERE `st_category` ='".$cat_id."' AND `st_type`='".$cat_type."' ".$remain_qry);
	//        //echo "SELECT GROUP_CONCAT(CONCAT('<li>',`st_store_name`,'</li>'))  AS usedStore,COUNT(*) AS usedCount FROM `gr_store` WHERE `st_category` ='".$cat_id."' AND `st_type`='".$cat_type."' ".$remain_qry;
	//        //exit;
	//    }
	
	
	function getProductsUsedThisCategory($cat_id,$cat_type)
	{
		return DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`pro_item_name`,'</li>')) AS usedProduct,COUNT(*) AS usedCount FROM `gr_product` WHERE `pro_category_id` = '".$cat_id."' AND pro_type='".$cat_type."' and pro_status <> '2'");
	}
	//	function getProductsUsedThisCategory($cat_id,$cat_type,$action)
	//    {
	//        if($action=='block') { $remain_qry = " AND pro_status = '1'"; } elseif($action=='delete'){ $remain_qry = " AND pro_status != '2'"; } else { $remain_qry=''; }
	//        return DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`pro_item_name`,'</li>')) AS usedProduct,COUNT(*) AS usedCount FROM `gr_product` WHERE `pro_category_id` = '".$cat_id."' AND pro_type='".$cat_type."' ".$remain_qry);
	//    }
	
	function getProductsUsedThisSubcategory($subCatId,$cat_type,$action)
    {
        if($action=='block') { $remain_qry = " AND pro_status = '1'"; } elseif($action=='delete'){ $remain_qry = " AND pro_status != '2'"; } else { $remain_qry=''; }
        return DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`pro_item_name`,'</li>')) AS usedProduct,COUNT(*) AS usedCount FROM `gr_product` WHERE `pro_sub_cat_id` = '".$subCatId."' AND pro_type='".$cat_type."' ".$remain_qry);
	}
	
	// function getStoresUsedThisMerchant($mer_id,$st_type)
	// {
	// 	return DB::select("SELECT GROUP_CONCAT(`st_store_name`) AS usedStore,COUNT(*) AS usedCount FROM `gr_store` WHERE `st_mer_id` = '".$mer_id."' AND st_type='".$st_type."' and st_status = '1'");
	// }
	
	
    function getStoresUsedThisMerchant($mer_id,$st_type)
    {
        return DB::select("SELECT GROUP_CONCAT(`st_store_name`) AS usedStore,COUNT(*) AS usedCount FROM `gr_store` WHERE `st_mer_id` = '".$mer_id."' AND st_type='".$st_type."' and st_status <> '2'");
	}
	
	
	//	function getProductsUsedThisStore($store_id,$pro_type,$action)
	//    {
	//        if($action=='block') { $remain_qry = " AND pro_status = '1'"; } elseif($action=='delete'){ $remain_qry = " AND pro_status != '2'"; } else { $remain_qry=''; }
	//        $sql = DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`pro_item_name`,'</li>')) AS usedProduct,COUNT(*) AS usedCount FROM `gr_product` WHERE `pro_store_id` = '".$store_id."' AND pro_type='".$pro_type."' ".$remain_qry);
	//        return $sql;
	//    }
	
	function getProductsUsedThisStore($store_id,$pro_type)
	{
		return DB::select("SELECT GROUP_CONCAT(CONCAT('<li>',`pro_item_name`,'</li>')) AS usedProduct,COUNT(*) AS usedCount FROM `gr_product` WHERE `pro_store_id` = '".$store_id."' AND pro_type='".$pro_type."' and pro_status <> '2'");
	}
	function getCartItemsUsedThisStore($store_id,$pro_type){
        return DB::select("SELECT COUNT(*) AS usedCount FROM `gr_cart_save` WHERE `cart_st_id` = '".$store_id."' AND cart_type='".$pro_type."' ");
	}
    
	function get_main_cate($name,$type)
    {
        return DB::table('gr_proitem_maincategory')->select('pro_mc_id')->where(['pro_mc_name' => $name,'pro_mc_type' => $type])->first();
	}
	
    function change_status($table,$where,$val){
		return DB::table($table)->where($where)->update($val);
		
	}
	
	
    
    function cart_amount($cusid,$st_id = '',$item_id = ''){
		//return DB::table('gr_cart_save')->where('cart_cus_id','=',$cusid)->sum('cart_total_amt');
        $sql =  DB::table('gr_cart_save')->select('cart_cus_id')
		->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
		->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
		->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
		->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
		->where(['gr_cart_save.cart_cus_id' => $cusid,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
		->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity');
        if($st_id != '')
        {
            $q = $sql->where(['gr_cart_save.cart_st_id' => $st_id]);
		}
		if($item_id != '')
        {
            $q = $sql->where(['gr_cart_save.cart_item_id' => $item_id]);
		}
        $q = $sql->sum('cart_total_amt');
        return $q;
	}
	
	function cart_qty($cusid,$st_id = '',$item_id = ''){
		//return DB::table('gr_cart_save')->where('cart_cus_id','=',$cusid)->sum('cart_total_amt');
        $sql =  DB::table('gr_cart_save')->select('cart_cus_id')
		->Join('gr_product','gr_product.pro_id','=','gr_cart_save.cart_item_id')
		->Join('gr_store','gr_store.id','=','gr_cart_save.cart_st_id')
		->Join('gr_merchant','gr_store.st_mer_id','=','gr_merchant.id')
		->Join('gr_country','gr_country.co_cursymbol','=','gr_cart_save.cart_currency')
		->where(['gr_cart_save.cart_cus_id' => $cusid,'gr_country.co_status'=>'1','gr_product.pro_status'=>'1','gr_store.st_status'=>'1','gr_merchant.mer_status'=>'1'])
		->whereRaw('gr_product.pro_no_of_purchase < gr_product.pro_quantity');
        if($st_id != '')
        {
            $q = $sql->where(['gr_cart_save.cart_st_id' => $st_id]);
		}
		if($item_id != '')
        {
            $q = $sql->where(['gr_cart_save.cart_item_id' => $item_id]);
		}
        $q = $sql->count();
        return $q;
	}
	
    function search_restaurantdet(){
		
		// return DB::table('gr_store')->select(DB::raw('CONCAT(st_store_name, " ", id) AS cities'))->pluck('cities')->first();
		
        $name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
		
		return DB::table('gr_store')->select(DB::raw('group_concat("\"",'.$name.',"\"") as cities'))->pluck('cities')->first();
		// return DB::table('gr_store')->select('st_store_name')->get();
		
		
	}
    function search_restaurantdet_sample($user_lat='',$user_long=''){
		// $res=DB::table('gr_store')->select(DB::raw('group_concat(st_store_name) as cities'))->pluck('cities');
		
		// $res=DB::table('gr_store')->select(DB::raw("CONCAT('',st_store_name,'_',id) as cities"))->pluck('cities');
		
		//      $res=DB::table('gr_store')->select(DB::raw("CONCAT('',st_store_name,'') as cities"))
		//          ->pluck('cities');
        $name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');
		
        $user_lat = Session::get('search_latitude');
        $user_long= Session::get('search_longitude');
		
        $res=DB::table('gr_store')->select(DB::raw("CONCAT('','.$name.','') as cities"));
		if($user_lat!='' && $user_long!='')
		{
			/*$res->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
			$res->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
		}
        $res = $res->pluck('cities');
		
		
		return json_encode($res);
		
	}
	
	function search_restaurantdet_sampleone($user_lat='',$user_long=''){
		
		$user_lat = Session::get('search_latitude');
		$user_long= Session::get('search_longitude');
		
		$res=DB::table('gr_store')->select('id','st_store_name');
		if($user_lat!='' && $user_long!='')
		{
			/*$res->whereRaw('(SELECT lat_lng_distance('.$user_lat.','.$user_long.',gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius');*/
			$res->whereRaw('(SELECT lat_lng_distance(?,?,gr_store.st_latitude,gr_store.st_longitude)) <= gr_store.st_delivery_radius', [$user_lat,$user_long]);
		}
		$res = $res->get();
		return json_encode($res);
		
	}
	
    function get_choice_name_defaultLang($ch_id)
    {   $name = 'ch_name';
        return DB::table('gr_choices')->select('gr_choices.'.$name.' as ch_name')
		->where(['ch_id' => $ch_id,'gr_choices.ch_status' => '1'])
		->first();
	}
	
    function push_notification($sender_id,$receiver_id,$sender_type,$receiver_type,$message,$order_id,$message_link){
        $insertArray = array('sender_id'    => $sender_id, 
		'receiver_id'  => $receiver_id, 
		'sender_type'  => $sender_type, 
		'receiver_type'=> $receiver_type, 
		'message'      => $message, 
		'order_id'     => $order_id, 
		'message_link' => $message_link, 
		'read_status'  => '0', 
		'updated_at'   => date('Y-m-d H:i;s') 
		);
        DB::table('gr_general_notification')->insert($insertArray);
        $id = DB::getPdo()->lastInsertId();
        return $id;
	}
	
	function get_advertisement_details()
	{
		return DB::table('gr_advertisement')->get();
	}
	
	function get_admin_details(){
		return DB::table('gr_admin')->first();
	}
	function get_merchant_commission($mer_id){
        return DB::table('gr_merchant')->select('mer_commission')->where('id',$mer_id)->first();
		
	}
	
	function calculate_distance($user_lat,$user_long,$st_lat,$st_long)
	{
		/*$results = DB::select(DB::Raw('SELECT lat_lng_distance('.$user_lat.','.$user_long.','.$st_lat.','.$st_long.') as distance'));*/
		$results = DB::select(DB::Raw('SELECT lat_lng_distance(?,?,?,?) as distance', [$user_lat,$user_long,$st_lat,$st_long]));
		return $results;
	}
	function get_min_price($store_id){
		$result = DB::table('gr_product')->select('pro_original_price','pro_discount_price')->where('pro_store_id','=',$store_id)->where('pro_status','1')->get();
		$min_price_array = array();
		if(count($result) > 0 ){
			foreach($result as $res){
				if($res->pro_discount_price === NULL) { 
					array_push($min_price_array,$res->pro_original_price);
				}
				else{
					array_push($min_price_array,$res->pro_discount_price);
				}
			}
			return min($min_price_array);
		}else{
			return 0;
		}
	}
	function get_max_price($store_id){
		$result = DB::table('gr_product')->select('pro_original_price','pro_discount_price')->where('pro_store_id','=',$store_id)->where('pro_status','1')->get();
		$min_price_array = array();
		if(count($result) > 0 ){
			foreach($result as $res){
				if($res->pro_discount_price === NULL) { 
					array_push($min_price_array,$res->pro_original_price);
				}
				else{
					array_push($min_price_array,$res->pro_discount_price);
				}
			}
			return max($min_price_array);
		}else{
			return 0;
		}
	}

	/** send notification to mobile starts **/
		
        
		// function makes curl request to firebase servers
		
		function sendPushNotification($fields,$key) 
		{ 
			
			$data = json_encode($fields);
			//FCM API end-point
			$url = 'https://fcm.googleapis.com/fcm/send';
			//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
			$server_key = $key;
			//header with content_type api key
			$headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$server_key
			);
			//CURL request to route notification to FCM connection server (provided by Google)
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$result = curl_exec($ch);
			
			if ($result === FALSE) {
				die('Oops! FCM Send Error: ' . curl_error($ch));
			}
			curl_close($ch);
			
			return $result;
			
		}
		
		function generate_seourl($seo_url,$table_name,$primary_id_column='',$primary_id='',$seo_column_name){
			$seourl = $seo_url;
			$checkSeo = DB::table($table_name)->select($seo_column_name)->where($seo_column_name,'=',$seourl);
			if($primary_id!=''){
				$checkSeo->where($primary_id_column,'!=',$primary_id);
			}
			$q = $checkSeo->get();

			$seo_count = 1;
			while (count($q) > 0){
				$seourl = $seo_url.$seo_count;
				$seo_count++;
				$checkSeo = DB::table($table_name)->select($seo_column_name)->where($seo_column_name,'=',$seourl);
				if($primary_id!=''){
					$checkSeo->where($primary_id_column,'!=',$primary_id);
				}
				$q = $checkSeo->get();
			}
			return $seourl;
		}
		/** send notification  ends**/

		/* calculate travel duration and distance */
		function GetDrivingDistance($lat1, $lat2, $long1, $long2)
		{
		    //$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL&key=AIzaSyDI3KfTjweOu_rjMSgzZpV3kq_GCxwPLvI";
		    $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$lat1.",".$long1."&destination=".$lat2.",".$long2."&mode=driving&key=AIzaSyBg5e4lx9fS1voiwnPjJ8YkjISFt7-sbfU";
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		    $response = curl_exec($ch);
		    curl_close($ch);
		    $response_a = json_decode($response, true);
		    //print_r($response_a); exit;
		    $dist = $response_a['routes'][0]['legs'][0]['distance']['value'];
		    $time = $response_a['routes'][0]['legs'][0]['duration']['value'];

		    return array('distance' => $dist, 'time' => $time);
		}

		/* remove array element */
		function removeElementWithValue($array, $key, $value){
			 foreach($array as $subKey => $subArray){
				   if($subArray[$key] == $value){
					   unset($array[$subKey]);
				  }
			 }
			 return $array;
		}


		//mysql_real_escape_string
		function mysql_escape_special_chars($inp)
	    { 
	        if(is_array($inp)) return array_map(__METHOD__, $inp);

	        if(!empty($inp) && is_string($inp)) { 
	        	$inp = strip_tags($inp);
	            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
	        } 

	        return $inp; 
	    }
	    //mysql_real_escape_string
		function mysql_escape_special_chars_with_tags($inp)
	    { 
	        if(is_array($inp)) return array_map(__METHOD__, $inp);

	        if(!empty($inp) && is_string($inp)) { 
	            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
	        } 

	        return $inp; 
	    }