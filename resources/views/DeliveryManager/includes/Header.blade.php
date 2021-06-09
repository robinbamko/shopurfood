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
		$filename = public_path('images/logo/').$logo_set_val->admin_logo;
		@endphp
		@if(file_exists($filename))
		<a href="#"><img src="{{url('public/images/logo/'.$logo_set_val->admin_logo)}}" alt="Logo" class="img-responsive logo"></a>
		@else
		<a href="#"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo" width="50px"></a>
		@endif
		@else
		<a href="#"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo" width="50px"></a>
		@endif
		
	</div>
	@php
		$orderRejectByAgentCount = DB::table('gr_order_reject_history')->where('agent_id','!=','0')->where('read_status','=','0')->count();

		$newOrderRes = DB::table('gr_order')->select('gr_order.ord_id')->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')->where('gr_order.ord_payment_status','=','Success')->where('gr_order.ord_status','=','4')->where('gr_order.ord_task_status','=','0')->where('gr_order.ord_agent_acpt_status','=','0')->where('gr_order.ord_self_pickup','!=','1')->get();
		$newOrderCount = count($newOrderRes);

		$acceptedOrderRes = DB::table('gr_order')->select('gr_order.ord_id')->groupBy('gr_order.ord_transaction_id','gr_order.ord_rest_id')->where('gr_order.ord_payment_status','=','Success')->where('gr_order.ord_status','=','4')->where('gr_order.ord_task_status','=','1')->where('gr_order.ord_agent_acpt_status','=','1')->where('gr_order.ord_delmgr_id','=',Session::get('DelMgrSessId'))->where('gr_order.ord_agent_acpt_read_status','=','0')->where('gr_order.ord_self_pickup','!=','1')->get();
		$acceptedOrderCount = count($acceptedOrderRes);

		$ordernotifyCount = DB::table('gr_general_notification')->where('receiver_id','=',Session::get('DelMgrSessId'))->where('receiver_type','=','gr_delivery_manager')->where('read_status','=','0')->count();

		$totalCount = $orderRejectByAgentCount+$newOrderCount+$acceptedOrderCount+$ordernotifyCount;
	@endphp

		
		
	<div class="container-fluid admin-menu">
		<div class="navbar-btn">
			<button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-menu"></i></button>
		</div> 
		<div id="navbar-menu" >
			<ul class="nav navbar-nav navbar-right">
				@if($totalCount > 0 )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown">
						<i class="lnr lnr-alarm"></i>
						<span class="badge bg-danger" id="grant_total_notify">{{$totalCount}}</span>
					</a>
					<ul class="dropdown-menu notifications">

							<li><a href="{{url('rejected-order-delboy-dmgr')}}" class="notification-item"><span class="dot bg-warning"></span><span id="reject_count">{{$orderRejectByAgentCount}}</span> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_DELBOY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_REJECTED_BY_DELBOY')}}</a></li>

							<li><a href="{{url('new-delivery-manage-orders')}}" class="notification-item"><span class="dot bg-success"></span><span id="new_or_count">{{$newOrderCount}} </span> {{(Lang::has(Session::get('DelMgr_lang_file').'.MER_NEW_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.MER_NEW_ORDER') : trans($DELMGR_OUR_LANGUAGE.'.MER_NEW_ORDER')}}</a></li>
							
							<li><a href="{{url('delivery-manage-orders')}}" class="notification-item"><span class="dot bg-danger"></span><span id="accept_or_count">{{$acceptedOrderCount}}</span> {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AXPT_BY_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_AXPT_BY_DELBOY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_AXPT_BY_DELBOY')}}</a></li>
							<li><a href="{{url('dmgr-order-notification')}}" class="notification-item"><span class="dot bg-primary"></span><span id="or_count">{{$ordernotifyCount}}</span> {{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_ORDER_NOTIFICATION')) ? trans(Session::get('DelMgr_lang_file').'.DEL_ORDER_NOTIFICATION') : trans($DELMGR_OUR_LANGUAGE.'.DEL_ORDER_NOTIFICATION')}}</a></li>
					
						<!--<li><a href="#" class="notification-item"><span class="dot bg-danger"></span>You have 9 unfinished tasks</a></li>
						<li><a href="#" class="notification-item"><span class="dot bg-success"></span>Monthly report is available</a></li>
						<li><a href="#" class="notification-item"><span class="dot bg-warning"></span>Weekly meeting in 1 hour</a></li>
						<li><a href="#" class="notification-item"><span class="dot bg-success"></span>Your request has been approved</a></li>
						<li><a href="#" class="more">See all notifications</a></li>-->
					</ul>
				</li>
				@endif
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="{{url('')}}/public/admin/assets/img/user.png" class="img-circle" alt="Avatar"> <span>{{Session::get('dm_name')}}</span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
					<ul class="dropdown-menu">
						<li><a href="{{ url('delivery-managerprofile') }}"><i class="lnr lnr-user"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MYPROFILE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MYPROFILE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MYPROFILE')}}</span></a></li>
						
						<li><a href="{{ url('delivery-manager-change-password') }}"><i class="lnr lnr-cog"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CHANGE_PASSWORD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CHANGE_PASSWORD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CHANGE_PASSWORD')}}</span></a></li>
						<li><a href="{{url('delivery-manager-logout')}}"><i class="lnr lnr-exit"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOGOUT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOGOUT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOGOUT')}}</span></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<div class="notify_alert alert-info  text-center animated fadeIn" style="display:none">
        <button type="button" class="close" data-hide="notify_alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>    
        	{!! trans(Session::get('DelMgr_lang_file').'.DEL_NOTI_INFO')!!}   
                  
	</div>
</nav>
