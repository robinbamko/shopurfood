<style type="text/css">
	.sidebar .nav i {
    margin-right: 5px;
    
	}
</style>

<?php $current_route = Route::getCurrentRoute()->uri();  ?>
<div id="sidebar-nav" class="sidebar">
	<div class="sidebar-scroll">
		<nav>
			<ul class="nav">
				<li><a href="{{url('delivery-manager-dashboard')}}" <?php echo ($current_route == "delivery-manager-dashboard") ? 'class="active"' : ''; ?> ><i class="lnr lnr-home"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DASHBOARD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DASHBOARD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DASHBOARD')}}</span></a></li>
				<li><a href="{{url('delivery-manager-settings')}}" <?php echo ($current_route == "delivery-manager-settings") ? 'class="active"' : ''; ?> ><i class="lnr lnr-cog"></i><span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_GENERAL_SETTINGS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_GENERAL_SETTINGS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_GENERAL_SETTINGS')}}</span></a></li>
				<!-- IF AGENT DISABLED -->
				@if($AGENTMODULE==0)
					<li>
						<a href="#deliveryboyLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-deliveryboy1" || $current_route=='add-deliveryboy1' || $current_route=='edit-deliveryboy1/{id}') { echo 'active'; } ?> "><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_MANAGEMENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_MANAGEMENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="deliveryboyLi" class="collapse <?php if($current_route == "add-deliveryboy1" || $current_route == "manage-deliveryboy1" || $current_route=='edit-deliveryboy1/{id}') { echo "in"; } ?> ">
							<ul class="nav">
								<li><a href="{{url('add-deliveryboy1')}}" class="<?php  if($current_route=='add-deliveryboy1' || $current_route=='edit-deliveryboy1/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ADD_DELBOY')}}</a></li> 
								<li><a href="{{url('manage-deliveryboy1')}}" class="">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_DELBOY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MANAGE_DELBOY')}}</a></li>
							</ul>
						</div>
					</li>
				@else
					<li>
						<a href="#customerLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-agent" || $current_route=='add-agent' || $current_route=='edit-agent/{id}') { echo 'active'; } ?> "><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_MANAGEMENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_MANAGEMENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGENT_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="customerLi" class="collapse <?php if($current_route == "add-agent" || $current_route == "manage-agent" || $current_route=='edit-agent/{id}') { echo "in"; } ?> ">
							<ul class="nav">
								<li><a href="{{url('add-agent')}}" class="<?php  if($current_route=='add-agent' || $current_route=='edit-agent/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADD_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADD_AGENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ADD_AGENT')}}</a></li> 
								<li><a href="{{url('manage-agent')}}" class="">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_AGENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MANAGE_AGENT')}}</a></li>
							</ul>
						</div>
					</li>
					<li>
						<a href="#deliveryboyLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-deliveryboy" || $current_route=='add-deliveryboy' || $current_route=='edit-deliveryboy/{id}') { echo 'active'; } ?> "><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_MANAGEMENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_MANAGEMENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="deliveryboyLi" class="collapse <?php if($current_route == "add-deliveryboy" || $current_route == "manage-deliveryboy" || $current_route=='edit-deliveryboy/{id}') { echo "in"; } ?> ">
							<ul class="nav">
								<li><a href="{{url('add-deliveryboy')}}" class="<?php  if($current_route=='add-deliveryboy' || $current_route=='edit-deliveryboy/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADD_DELBOY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ADD_DELBOY')}}</a></li> 
								<li><a href="{{url('manage-deliveryboy')}}" class="">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_DELBOY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MANAGE_DELBOY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MANAGE_DELBOY')}}</a></li>
							</ul>
						</div>
					</li>
				@endif
		
				<!-- MANAGE ORDERS  || $current_route=='delivery-invoice-order/{id}' || $current_route=="delivery-track-order/{id}"-->
				<li><a href="{{url('new-delivery-manage-orders')}}" class="<?php  if($current_route == "new-delivery-manage-orders") { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_MGMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NEWORDER_MGMT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NEWORDER_MGMT')}}</span></a></li>
				
				@if($AGENTMODULE==0)
					<li><a href="{{url('rejected-order-delboy-dmgr')}}" class="<?php  if($current_route == "rejected-order-delboy-dmgr" || $current_route=='reassign-delivery-boy') { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_DELBOY')) ? ucwords(trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_DELBOY')) : ucwords(trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_REJECTED_BY_DELBOY'))}}</span></a></li>
				
					<li><a href="{{url('delivery-manage-orders1')}}" class="<?php  if($current_route == "delivery-manage-orders1") { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_MGMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_MGMT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_MGMT')}}</span></a></li>
				@else
					<li><a href="{{url('rejected-order-agent-dmgr')}}" class="<?php  if($current_route == "rejected-order-agent-dmgr" || $current_route=='reassign-delivery-boy') { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_AGENT')) ? ucwords(trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_REJECTED_BY_AGENT')) : ucwords(trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_REJECTED_BY_AGENT'))}}</span></a></li>
				
					<li><a href="{{url('delivery-manage-orders')}}" class="<?php  if($current_route == "delivery-manage-orders") { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_MGMT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_MGMT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_MGMT')}}</span></a></li>
				@endif
				
				<!-- COMMISSION TRACKING-->
				@if($AGENTMODULE==0)
					<li><a href="{{url('deliveryboy-commission-tracking')}}" class="<?php  if($current_route == "deliveryboy-commission-tracking" || $current_route=='delmgr_view_transaction1/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_COMMISSION_TRACKING')}}</span></a></li>
				@else
					<li><a href="{{url('delivery-commission-tracking')}}" class="<?php  if($current_route == "delivery-commission-tracking" || $current_route=='delmgr_view_transaction/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMMISSION_TRACKING') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_COMMISSION_TRACKING')}}</span></a></li>
				@endif
				
				<li><a href="{{url('delivery-boy-map')}}" class="<?php  if($current_route=='delivery_boy_map') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELIVERY_BOY_MAP')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELIVERY_BOY_MAP') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELIVERY_BOY_MAP')}}</span></a></li>
				
			</ul>
		</nav>
	</div>
</div>