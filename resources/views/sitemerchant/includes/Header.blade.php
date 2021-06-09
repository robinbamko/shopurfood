@php

$store_id = DB::table('gr_store')->select('id')->where('st_mer_id','=',Session::get('merchantid'))->first();
if(empty($store_id->id) === false){
	$storeidIs = $store_id->id;
}else{
	$storeidIs = 0;
}


$bussiness_type = DB::table('gr_merchant')->select('mer_business_type')->where('id','=',Session::get('merchantid'))->first();
if(empty($bussiness_type->mer_business_type) === false){
	$bussiness_types = $bussiness_type->mer_business_type;
}else{
	$bussiness_types = 0;
}

$pdt_notifyCount = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','1')->where('pro_status','!=','2')->where('gr_product.pro_store_id','=',$storeidIs)->count();

$item_notifyCount = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','2')->where('pro_status','!=','2')->where('gr_product.pro_store_id','=',$storeidIs)->count();

//$ordernotifyCount = count(DB::table('gr_order')->where('ord_merchant_viewed','=','0')->where('ord_merchant_id','=',Session::get('merchantid'))->groupBy('ord_transaction_id')->get());


$ordernotifyCount = DB::table('gr_general_notification')->where('receiver_id','=',Session::get('merchantid'))->where('receiver_type','=','gr_merchant')->where('read_status','=','0')->count();


$total_notifyCount = $pdt_notifyCount+$item_notifyCount+$ordernotifyCount;

$featured_qry = DB::select("SELECT COUNT(*) AS featCount,DATEDIFF(to_date, CURDATE()) AS expiry FROM `gr_featured_booking` WHERE DATEDIFF(to_date, CURDATE()) BETWEEN 0 AND 1 AND mer_id='".Session::get('merchantid')."' AND admin_approved_status='1' ");  

if($featured_qry[0]->featCount==0){
	$feat_heading = '';
}else{
	if($featured_qry[0]->expiry=='0'){ $expiryDays='Today'; } else { $expiryDays=$featured_qry[0]->expiry.' day'; } 
	if(Session::get('mer_business_type')=='1'){
		$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_STORE');
	}else{
		$business_type = (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_RESTS');
	}
	$got_message = (Lang::has(Session::get('mer_lang_file').'.MER_FEAT_OPTION_EXPIRY')) ? trans(Session::get('mer_lang_file').'.MER_FEAT_OPTION_EXPIRY') : trans($this->MER_OUR_LANGUAGE.'.MER_FEAT_OPTION_EXPIRY');
	$searchReplaceArray = array(':business_type' => $business_type, ':num_days'=>$expiryDays );
	$feat_heading = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$got_message);
}
@endphp
<style>
.notify_alert {
    position: fixed;
    width: 100%;
    z-index: 999;
    border-radius: 0;
    top: 0px;
    background: #373756;
    border: 0px;
    color: #fff;
    height: 60px;
    padding: 18px;
    box-shadow: 0px 1px 4px 2px #373756;
}
</style>
<nav class="navbar navbar-default navbar-fixed-top">
			<div class="brand">
				@if(count($logo_settings_details) > 0)
					@php
						foreach($logo_settings_details as $logo_set_val){ }
					@endphp
					<a href="#"><img src="{{url('public/images/logo/'.$logo_set_val->admin_logo)}}" alt="Klorofil Logo" class="img-responsive logo"></a>
				@else
					<a href="#"><img src="{{url('')}}/public/admin/assets/img/logo-dark.png" alt="Klorofil Logo" class="img-responsive logo"></a>
				@endif
			</div>
			
			<div class="container-fluid mrchnt-menu">
				<div class="navbar-btn">
					<button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-menu"></i></button>
				</div> 
				<!-- <form class="navbar-form navbar-left">
					<div class="input-group">
						<input type="text" value="" class="form-control" placeholder="Search dashboard...">
						<span class="input-group-btn"><button type="button" class="btn btn-primary">Go</button></span>
					</div>
				</form> -->
				<!-- <div class="navbar-btn navbar-btn-right">
					<a class="btn btn-success update-pro" href="https://www.themeineed.com/downloads/klorofil-pro-bootstrap-admin-dashboard-template/?utm_source=klorofil&utm_medium=template&utm_campaign=KlorofilPro" title="Upgrade to Pro" target="_blank"><i class="fa fa-rocket"></i> <span>UPGRADE TO PRO</span></a>
				</div> -->
				<div id="navbar-menu">
					<ul class="nav navbar-nav navbar-right">
						@if($total_notifyCount > 0 )
						<li class="dropdown">
							<a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown">
								<i class="lnr lnr-alarm"></i> <span class="badge bg-danger" id="grant_tot_notify">{{$total_notifyCount}}</span>
							</a>
							<ul class="dropdown-menu notifications">
								
								@if($bussiness_types == '1')
									<li><a href="{{url('mer-manage-product/1')}}" class="notification-item"><span class="dot bg-success"></span>{{$pdt_notifyCount}} {{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_NEW_PDT')) ? trans(Session::get('mer_lang_file').'.ADMIN_NEW_PDT') : trans($MER_OUR_LANGUAGE.'.ADMIN_NEW_PDT') }}</a></li>
								@else
									<li><a href="{{url('mer-manage-item/2')}}" class="notification-item"><span class="dot bg-primary" ></span><span id="item_notify">{{$item_notifyCount}}</span> {{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_NEW_ITEM')) ? trans(Session::get('mer_lang_file').'.ADMIN_NEW_ITEM') : trans($MER_OUR_LANGUAGE.'.ADMIN_NEW_ITEM') }}</a></li>
								@endif
								<li><a href="{{url('notification-manager')}}" class="notification-item"><span class="dot bg-warning" ></span><span id="ord_notify">{{$ordernotifyCount}} </span> {{ (Lang::has(Session::get('mer_lang_file').'.MER_ORDER_NOTIFICATION')) ? trans(Session::get('mer_lang_file').'.MER_ORDER_NOTIFICATION') : trans($MER_OUR_LANGUAGE.'.MER_ORDER_NOTIFICATION') }}</a></li>
								<!--<li><a href="#" class="notification-item"><span class="dot bg-success"></span>Your request has been approved</a></li>
								<li><a href="#" class="more">See all notifications</a></li>-->
							</ul>
						</li>
						@endif
						@if($feat_heading!='')
							<li><span class="badge bg-danger">{{$feat_heading}}</span></li>
						@endif
						@if(Session::has('merchantid'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="{{url('')}}/public/admin/assets/img/user.png" class="img-circle" alt="Avatar"> <span>{{ Session::get('mer_name') }}</span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
							<ul class="dropdown-menu">
								<li><a href="{{url('merchant_profile')}}"><i class="lnr lnr-user"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_MY_PROFILE')) ? trans(Session::get('mer_lang_file').'.MER_MY_PROFILE') : trans($MER_OUR_LANGUAGE.'.MER_MY_PROFILE')}}</span></a></li>
								<li><a href="{{url('merchant_change_password')}}"><i class="lnr lnr-envelope"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_CHANGE_PASS')) ? trans(Session::get('mer_lang_file').'.MER_CHANGE_PASS') : trans($MER_OUR_LANGUAGE.'.MER_CHANGE_PASS')}}</span></a></li>
								<!--<li><a href="#"><i class="lnr lnr-cog"></i> <span>Settings</span></a></li>-->
								<li><a href="{{url('merchant-logout')}}"><i class="lnr lnr-exit"></i> <span>Logout</span></a></li>
							</ul>
						</li>
						@endif
						<!-- <li>
							<a class="update-pro" href="https://www.themeineed.com/downloads/klorofil-pro-bootstrap-admin-dashboard-template/?utm_source=klorofil&utm_medium=template&utm_campaign=KlorofilPro" title="Upgrade to Pro" target="_blank"><i class="fa fa-rocket"></i> <span>UPGRADE TO PRO</span></a>
						</li> -->
					</ul>
				</div>
			</div>
			<div class="notify_alert alert-info  text-center animated fadeIn" style="display:none">
		        <button type="button" class="close" data-hide="notify_alert" aria-label="Close">
		            <span aria-hidden="true">&times;</span>
		        </button>    
		        	{!! trans(Session::get('mer_lang_file').'.MER_NOTI_INFO')!!}   
		                  
			</div>
		</nav>