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
				<!--DASHBOARD-->
				
				<li><a href="{{url('admin-dashboard')}}" <?php echo ($current_route == "admin-dashboard") ? 'class="active"' : ''; ?> ><i class="lnr lnr-home"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DASHBOARD')) ? trans(Session::get('admin_lang_file').'.ADMIN_DASHBOARD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DASHBOARD')}}</span></a></li>
				
				@php extract($privileges); @endphp
				
				<!--SETTINGS-->
				@if ($allPrev == '1')
				<li>
					<a href="#SettingsubPages" data-toggle="collapse" class="<?php  if($current_route == "admin-general-settings" || $current_route == "admin-smtp-settings" || $current_route == "admin-social-settings" || $current_route == "admin-logo-settings" || $current_route == "admin-noimage-settings" || $current_route == "admin-payment-settings" || $current_route == "admin-banner-settings" || $current_route == "manage-advertisement" || $current_route =="edit_banner/{id}") { echo 'active'; }else{ echo 'collapsed';  } ?>" ><i class="lnr lnr-cog"></i> <span>Settings</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="SettingsubPages" class="collapse <?php  if($current_route == "admin-general-settings" || $current_route == "admin-smtp-settings" || $current_route == "admin-social-settings" || $current_route == "admin-logo-settings" || $current_route == "admin-noimage-settings" || $current_route == "admin-payment-settings" || $current_route == "admin-banner-settings" || $current_route == "manage-advertisement" || $current_route =="edit_banner/{id}") { echo 'in'; } ?> ">
						<ul class="nav">
							<li><a href="{{url('admin-general-settings')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_GENERAL_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_GENERAL_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GENERAL_SETTINGS')}}</a></li>
							<li><a href="{{url('admin-smtp-settings')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SMTP_Settings')) ? trans(Session::get('admin_lang_file').'.ADMIN_SMTP_Settings') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SMTP_Settings')}}</a></li>
							<li><a href="{{url('admin-social-settings')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Social_Media_Settings')) ? trans(Session::get('admin_lang_file').'.ADMIN_Social_Media_Settings') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Social_Media_Settings')}}</a></li>
							
							<li>
								<a href="{{url('admin-logo-settings')}}" class="">
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_SETTINGS')}}
								</a>
							</li>
							<li>
								<a href="{{url('admin-noimage-settings')}}" class="">
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOIMAGE_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOIMAGE_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOIMAGE_SETTINGS')}}
								</a>
							</li>
							<li>
								<a href="{{url('admin-payment-settings')}}" class="">
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMENT_SETTINGS')}}
								</a>
							</li>
							<li>
								<a href="{{url('admin-banner-settings')}}" class="">
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_BANNER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_BANNER')}}
								</a>
							</li>
							<li>
								<a href="{{url('manage-advertisement')}}" class="">
									{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_ADVERTISE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_ADVERTISE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_ADVERTISE')}}
								</a>
							</li>
							
						</ul>
					</div>
				</li> 
				
				<li>
					<a href="#sublocation" data-toggle="collapse" class="<?php  if($current_route == "add-subadmin" || $current_route == "manage-subadmin") { echo 'active'; } else { echo 'collapsed'; } ?>" ><i class="fa fa-user-secret"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBADMIN')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="sublocation" class="collapse">
						<ul class="nav">
							<li><a href="{{url('add-subadmin')}}" class="active">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_SUBADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_SUBADMIN')}}</a></li>
							<li><a href="{{url('manage-subadmin')}}" class="collapsed">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_SUBADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_SUBADMIN')}}</a></li> 
							
						</ul>
					</div>
				</li> 
				@endif
				<!--END SETTINGS-->
				
					<?php /*				<li>
					<a href="#sublocation" data-toggle="collapse" class="<?php  if($current_route == "add-country" || $current_route=='edit_country/{id}' || $current_route == "manage-country" ) { echo 'active'; } ?>" ><i class="lnr lnr-earth"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_LOC')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="sublocation" class="collapse <?php if($current_route == "add-country" || $current_route == "manage-country" || $current_route=='edit_country/{id}' ) { echo "in"; } ?> ">
					<ul class="nav">
					{{-- <li><a href="{{url('add-country')}}" class="collapsed">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOC_ADD')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOC_ADD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOC_ADD')}}</a></li> --}}
					<li><a href="{{url('manage-country')}}" class="active">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_LOC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_LOC')}}</a></li>
					
					</ul>
					</div>
				</li>  */ ?> <!---->
				
				
				@if((isset($Category) && is_array($Category)) && in_array('0', $Category) || $allPrev == '1')
				<li>
					
					
					<a href="#subCategory" data-toggle="collapse"  class="<?php  if($current_route == "manage-restaurant-category" || $current_route == "manage-item-category" || $current_route == "manage-subitem" || $current_route == 'edit_store_category/{id}' || $current_route == 'edit_product_category/{id}'  || $current_route == 'manage-subproduct/{id}' || $current_route == 'edit_item_category/{id}' || $current_route == 'manage-subitem/{id}' || $current_route=='edit_sub_category/{id}/{main_id}' || $current_route=='edit_subitem_category/{id}/{main_id}') { echo 'active'; } else { echo 'collapsed'; } ?>" ><i class="fa fa-plus"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CATE_MANAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CATE_MANAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CATE_MANAGE')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="subCategory" class="collapse <?php  if( $current_route == "manage-restaurant-category" || $current_route == "manage-item-category" || $current_route == "manage-subitem" || $current_route == 'edit_store_category/{id}' || $current_route == 'edit_restaurant_category/{id}' || $current_route == 'edit_product_category/{id}'  || $current_route == 'manage-subproduct/{id}' || $current_route == 'edit_item_category/{id}' || $current_route == 'manage-subitem/{id}') { echo 'in'; } ?>">
						
						
						<ul class="nav">
							
							<li><a href="{{url('manage-restaurant-category')}}" class="<?php  if($current_route=='manage-restaurant-category' || $current_route == 'edit_restaurant_category/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_CATE') }} ({{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUISINE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUISINE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUISINE') }})</a></li>
							
							<li><a href="{{url('manage-item-category')}}" class="<?php  if($current_route=='manage-item-category' || $current_route == 'edit_item_category/{id}' || $current_route == 'manage-subitem/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_ITEM_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_ITEM_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_ITEM_CATE')}}</a></li>
							
						</ul>
					</div>
				</li>
				@endif
				
				
				@if((isset($Choices) && is_array($Choices)) && in_array('0', $Choices) || $allPrev == '1')
				<li><a href="{{url('manage-choices')}}" class="<?php if($current_route=='edit-choice/{id}') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-thumbs-o-up"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_CHOICES')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_CHOICES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_CHOICES')}}</span></a></li>
				@endif
				
				
				@if((isset($Merchant) && is_array($Merchant)) && in_array('0', $Merchant) || $allPrev == '1')
				<li>
					<a href="#merchantLi" data-toggle="collapse" class="<?php  if($current_route == "manage-merchant" || $current_route=='add-merchant' || $current_route=='edit-merchant/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="lnr lnr-user"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_MGMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_MGMT')}}</span> <i class="icon-submenu lnr lnr-chevron-left" ></i></a>
					<div id="merchantLi" class="collapse <?php  if($current_route == "manage-merchant" || $current_route=='add-merchant' || $current_route=='edit-merchant/{id}') { echo 'in'; }  ?>">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $Merchant))
							<li><a href="{{url('add-merchant')}}" class="<?php  if($current_route=='add-merchant' || $current_route=='edit-merchant/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MERCHANT')}}</a></li> 
							@endif
							<li><a href="{{url('manage-merchant')}}" class="<?php  if($current_route=='manage-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_MERCHANT')}}</a></li>
						</ul>
					</div>
				</li>
				@endif
				
				
				
				@if((isset($Restaurant) && is_array($Restaurant)) && in_array('0', $Restaurant) || $allPrev == '1')
				<li>
					<a href="#restarant" data-toggle="collapse" class="<?php  if($current_route == "manage-restaurant" || $current_route=='add-restaurant' || $current_route=='edit-restaurant/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-cutlery"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_MANAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_MANAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_MANAGE')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="restarant" class="collapse <?php  if($current_route == "manage-restaurant" || $current_route=='add-restaurant' || $current_route=='edit-restaurant/{id}') { echo 'in'; }  ?>">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $Restaurant))
							<li><a href="{{url('add-restaurant')}}" class="<?php  if($current_route=='add-merchant' || $current_route=='edit-restaurant/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_REST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_REST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_REST')}}</a></li> 
							@endif
							<li><a href="{{url('manage-restaurant')}}" class="<?php  if($current_route=='manage-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_REST')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_REST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_REST')}}</a></li>
						</ul>
					</div>
				</li>
				@endif
				
				
				
				
				
				<!--<li>
					<a href="#newsletter" data-toggle="collapse" class="<?php  if($current_route == "manage-newsletter" || $current_route=='add-newsletter') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-cutlery"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_NEWSLETTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_NEWSLETTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_NEWSLETTER')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="newsletter" class="collapse <?php  if($current_route == "manage-newsletter" || $current_route=='add-newsletter') { echo 'in'; }  ?>">
					<ul class="nav">
					<li><a href="{{url('add-newsletter')}}" class="<?php  if($current_route=='add-newsletter') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_NEWSLETTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_NEWSLETTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_NEWSLETTER')}}</a></li> 
					<li><a href="{{url('manage-newsletter')}}" class="<?php  if($current_route=='manage-newsletter') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_NEWSLETTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_NEWSLETTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_NEWSLETTER')}}</a></li>
					</ul>
					</div>
				</li>-->
				
				@if((isset($Customer) && is_array($Customer)) && in_array('0', $Customer) || $allPrev == '1')
				<li>
					<a href="#customerLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-customer" || $current_route=='add-customer' || $current_route=='edit-customer/{id}') { echo 'active'; } ?> "><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_CUST')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_CUST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_CUST')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="customerLi" class="collapse <?php if($current_route == "add-customer" || $current_route == "manage-customer" || $current_route=='edit-customer/{id}') { echo "in"; } ?> ">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $Customer))
							<li><a href="{{url('add-customer')}}" class="<?php  if($current_route=='add-customer' || $current_route=='edit-customer/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_CUSTOMER')}}</a></li> 
							@endif
							<li><a href="{{url('manage-customer')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_CUSTOMER')}}</a></li>
						</ul>
					</div>
				</li>
				@endif
				
				@if((isset($Item) && is_array($Item)) && in_array('0', $Item) || $allPrev == '1')
				<li>
					<a href="#item" data-toggle="collapse" class="<?php  if($current_route == "manage-item" || $current_route=='add-item' || $current_route=='item_bulk_upload' || $current_route=='edit-item/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-list-alt"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_MGMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_MGMT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="item" class="collapse <?php  if($current_route == "manage-item" || $current_route=='add-item' || $current_route=='edit-item/{id}' || $current_route=='item_bulk_upload') { echo 'in'; }  ?>">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $Item))
							<li><a href="{{url('add-item')}}" class="<?php  if($current_route=='add-item' || $current_route=='edit-item/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_ITEM')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_ITEM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_ITEM')}}</a></li> 
							@endif
							
							<li><a href="{{url('manage-item')}}" class="<?php  if($current_route=='manage-item') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_ITEM')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_ITEM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_ITEM')}}</a></li>
							
							@if($allPrev == '1' || in_array('1', $Item))
							<li>
								<a href="{{url('item_bulk_upload')}}" class="<?php  if($current_route=='item_bulk_upload') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_BULK_UPLOAD')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_BULK_UPLOAD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_BULK_UPLOAD')}}</a>
							</li>
							@endif
						</ul>
					</div>
				</li>
				@endif
				
				
				
				@if((isset($Delivery_Manager) && is_array($Delivery_Manager)) && in_array('0', $Delivery_Manager) || $allPrev == '1')
				<li>
					<a href="#manager" data-toggle="collapse" class="<?php  if($current_route == "add-delivery-manager" || $current_route=='manage-delivery-manager' || $current_route=='edit-manager/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-users"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGNT_DELIVERY_MNGR')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGNT_DELIVERY_MNGR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGNT_DELIVERY_MNGR')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="manager" class="collapse <?php  if($current_route == "add-delivery-manager" || $current_route=='manage-delivery-manager' || $current_route=='edit-manager/{id}') { echo 'in'; }  ?>">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $Delivery_Manager))
							<li><a href="{{url('add-delivery-manager')}}" class="<?php  if($current_route=='add-delivery-manager') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DELIVERY_MNGR')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DELIVERY_MNGR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DELIVERY_MNGR')}}</a></li> 
							@endif
							<li><a href="{{url('manage-delivery-manager')}}" class="<?php  if($current_route=='manage-delivery-manager') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_DELIVERY_MNGR')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_DELIVERY_MNGR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_DELIVERY_MNGR')}}</a></li>
						</ul>
					</div>
				</li>
				@endif
				
				<!-- AGENT AND DELIVERY BOY -->
				@if($AGENTMODULE==0)
					@if((isset($Delivery_Boy) && is_array($Delivery_Boy)) && in_array('0', $Delivery_Boy) || $allPrev == '1')
						<li> 
							<a href="#deliveryboyLi" data-toggle="collapse" class="<?php  if($current_route == "add-deliveryboy-admin1" || $current_route == "manage-deliveryboy-admin1/{id?}" || $current_route=='edit-deliveryboy-admin1/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_MANAGEMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_MANAGEMENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
							<div id="deliveryboyLi" class="collapse <?php if($current_route == "add-deliveryboy-admin1" || $current_route == "manage-deliveryboy-admin1/{id?}" || $current_route=='edit-deliveryboy-admin1/{id}') { echo "in"; } ?> ">
								<ul class="nav">
									@if($allPrev == '1' || in_array('1', $Delivery_Boy))
									<li><a href="{{url('add-deliveryboy-admin1')}}" class="<?php  if($current_route=='add-deliveryboy-admin1' || $current_route=='edit-deliveryboy-admin1/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DELBOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DELBOY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DELBOY')}}</a></li> 
									@endif
									<li><a href="{{url('manage-deliveryboy-admin1')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_DELBOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_DELBOY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_DELBOY')}}</a></li>
								</ul>
							</div>
						</li>
					@endif
				@else
					@if((isset($Agent) && is_array($Agent)) && in_array('0', $Agent) || $allPrev == '1')
						<li>
							<a href="#agentLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-agent-admin" || $current_route=='add-agent-admin' || $current_route=='edit-agent-admin/{id}') { echo 'active'; } ?> "><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_AGENT_MANAGEMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGENT_MANAGEMENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AGENT_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
							<div id="agentLi" class="collapse <?php if($current_route == "add-agent-admin" || $current_route == "manage-agent-admin" || $current_route=='edit-agent-admin/{id}') { echo "in"; } ?> ">
								<ul class="nav">
									@if($allPrev == '1' || in_array('1', $Agent))
									<li><a href="{{url('add-agent-admin')}}" class="<?php  if($current_route=='add-agent-admin' || $current_route=='edit-agent-admin/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_AGENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_AGENT')}}</a></li> 
									@endif
									<li><a href="{{url('manage-agent-admin')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_AGENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_AGENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_AGENT')}} {{$AGENTMODULE}} </a></li>
								</ul>
							</div>
						</li>
					@endif
						
					@if((isset($Delivery_Boy) && is_array($Delivery_Boy)) && in_array('0', $Delivery_Boy) || $allPrev == '1')
						<li>
							<a href="#deliveryboyLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-deliveryboy-admin" || $current_route=='add-deliveryboy-admin' || $current_route=='edit-deliveryboy-admin/{id}') { echo 'active'; } ?> "><i class="lnr lnr-users"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_MANAGEMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_MANAGEMENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
							<div id="deliveryboyLi" class="collapse <?php if($current_route == "add-deliveryboy-admin" || $current_route == "manage-deliveryboy-admin" || $current_route=='edit-deliveryboy-admin/{id}') { echo "in"; } ?> ">
								<ul class="nav">
									@if($allPrev == '1' || in_array('1', $Delivery_Boy))
									<li><a href="{{url('add-deliveryboy-admin')}}" class="<?php  if($current_route=='add-deliveryboy-admin' || $current_route=='edit-deliveryboy-admin/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DELBOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DELBOY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DELBOY')}}</a></li> 
									@endif
									<li><a href="{{url('manage-deliveryboy-admin')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_DELBOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_DELBOY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_DELBOY')}}</a></li>
								</ul>
							</div>
						</li>
					@endif
				@endif
				
				
				
				<li style="display: none;">
					<a href="#couponLi" data-toggle="collapse" class="collapsed" <?php  if($current_route == "manage-coupon" || $current_route=='add-coupon') { echo 'class="active"'; } ?> ><i class="lnr lnr-user"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_COUPON')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_COUPON') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_COUPON')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="couponLi" class="collapse ">
						<ul class="nav">
							<li><a href="{{url('add-coupon')}}" class="collapsed">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_COUPON')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_COUPON') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_COUPON')}}</a></li> 
							<li><a href="{{url('manage-coupon')}}" class="">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_COUPON')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_COUPON') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_COUPON')}}</a></li>
						</ul>
					</div>
				</li>
				
				
				{{--@if((isset($CMS) && is_array($CMS)) && in_array('0', $CMS) || $allPrev == '1')--}}
				
				@if((isset($CMS) && is_array($CMS)) && in_array('0', $CMS) || $allPrev == '1')
				<li>
					<a href="#cmsLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-cms" || $current_route=='add-cms' || $current_route=='edit-cms/{id}') { echo 'active'; } ?>" ><i class="lnr lnr-file-empty"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_CMS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_CMS')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					
					<div id="cmsLi" class="collapse <?php if($current_route == "add-cms" || $current_route == "manage-cms" || $current_route=='edit-cms/{id}') { echo "in"; } ?> ">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $CMS))
							<li><a href="{{url('add-cms')}}" class="<?php  if($current_route=='add-cms' || $current_route=='edit-cms/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CMS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_CMS')}}</a></li> 
							@endif
							<li><a href="{{url('manage-cms')}}" class="<?php  if($current_route=='manage-cms') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_CMS')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_CMS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_CMS')}}</a></li>
						</ul>
					</div>
				</li>
				@endif
				
				
				@if((isset($FAQ) && is_array($FAQ)) && in_array('0', $FAQ) || $allPrev == '1')
				<li>
					<a href="#faqLi" data-toggle="collapse" class="collapsed <?php  if($current_route == "manage-faq" || $current_route=='add-faq' || $current_route=='edit-faq/{id}') { echo 'active'; } ?>" ><i class="lnr lnr-file-empty"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_FAQ') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_FAQ')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					
					<div id="faqLi" class="collapse <?php if($current_route == "manage-faq" || $current_route == "add-faq" || $current_route=='edit-faq/{id}') { echo "in"; } ?> ">
						<ul class="nav">
							@if($allPrev == '1' || in_array('1', $FAQ))
							<li><a href="{{url('add-faq')}}" class="<?php  if($current_route=='add-faq' || $current_route=='edit-faq/{id}') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_FAQ') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_FAQ')}}</a></li> 
							@endif
							<li><a href="{{url('manage-faq')}}" class="<?php  if($current_route=='manage-faq') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_FAQ') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_FAQ')}}</a></li>
						</ul>
					</div>
				</li>
				@endif
				
				
				@if((isset($Review) && is_array($Review)) && in_array('0', $Review) || $allPrev == '1')
				<li>
					<a href="#review" data-toggle="collapse" class="<?php  if( $current_route=='manage-item-review' ||  $current_route=='manage-restaurant-review' || $current_route == "view_review/{id}") { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-comments"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REVIEW_MANAGEMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_REVIEW_MANAGEMENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REVIEW_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="review" class="collapse <?php  if( $current_route=='manage-item-review' || $current_route=='manage-restaurant-review' || $current_route=='manage-order-review' || $current_route == "view_review/{id}") { echo 'in'; }  ?>">
						<ul class="nav">
							
							<li><a href="{{url('manage-item-review')}}" class="<?php  if($current_route=='manage-item-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REVIEW_ITEM')) ? trans(Session::get('admin_lang_file').'.ADMIN_REVIEW_ITEM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REVIEW_ITEM')}}</a></li>
							
							<li><a href="{{url('manage-restaurant-review')}}" class="<?php  if($current_route=='manage-restaurant-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_RES_REVIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_RES_REVIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_RES_REVIEW')}}</a></li>
							<li><a href="{{url('manage-order-review')}}" class="<?php  if($current_route=='manage-order-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MNGE_OR_REVIEW')) ? trans(Session::get('admin_lang_file').'.ADMIN_MNGE_OR_REVIEW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MNGE_OR_REVIEW')}}</a></li>
							
						</ul>
					</div>
				</li>
				@endif
				
				
				@if((isset($Newsletter) && is_array($Newsletter)) && in_array('0', $Newsletter) || $allPrev == '1')
				<li>
					<a href="#newsletter" data-toggle="collapse" class="<?php  if($current_route == "manage-news-letter") { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-newspaper-o"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NEWSLETTER_MANAGEMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEWSLETTER_MANAGEMENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEWSLETTER_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="newsletter" class="collapse <?php  if($current_route == "manage-news-letter") { echo 'in'; }  ?>">
						<ul class="nav">
							<li><a href="{{url('manage-news-letter')}}" class="<?php  if($current_route=='manage-news-letter') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MGMT_NEWSLETTER_TEMPLATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MGMT_NEWSLETTER_TEMPLATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MGMT_NEWSLETTER_TEMPLATE')}}</a></li> 
							<li><a href="{{url('send-newsletter')}}" class="<?php  if($current_route=='send-newsletter') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEND_NEWSLETTER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEND_NEWSLETTER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEND_NEWSLETTER')}}</a></li> 
						</ul>
					</div>
				</li>
				@endif
				
				
				<!-- MANAGE ORDERS -->
				@if((isset($Order) && is_array($Order)) && in_array('0', $Order) || $allPrev == '1')
				<li><a href="{{url('manage-orders')}}" class="<?php  if($current_route == "manage-orders" || $current_route=='admin-invoice-order/{id}' || $current_route=="admin-track-order/{id}") { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_MGMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_MGMT')}}</span></a></li>
				@endif
				
				<!-- COMMISSION TRACKING-->
				@if((isset($Commission) && is_array($Commission)) && in_array('0', $Commission) || $allPrev == '1')
				<li><a href="{{url('admin-commission-tracking')}}" class="<?php  if($current_route == "admin-commission-tracking" || $current_route=='commission_view_transaction/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MER_COMM_TRACK')) ? trans(Session::get('admin_lang_file').'.ADMIN_MER_COMM_TRACK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MER_COMM_TRACK')}}</span></a></li>
				@endif
				@if((isset($Delivery_Commission) && is_array($Delivery_Commission)) && in_array('0', $Delivery_Commission) || $allPrev == '1')
				<li>
					<a href="{{url('admin-delivery-commission-tracking')}}" class="<?php  if($current_route=='admin-delivery-commission-tracking') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_COMM_TRACK')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_COMM_TRACK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEL_COMM_TRACK')}}</span>
					</a>
				</li>
				@endif
				
				@if((isset($Inventory) && is_array($Inventory)) && in_array('0', $Inventory) || $allPrev == '1')
				<li><a href="{{url('manage-inventory')}}" class=""><i class="fa fa-first-order" <?php  if($current_route=='manage-inventory') { echo 'active'; } else { echo ''; } ?>></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_INVENTOY_MGMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_INVENTOY_MGMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_INVENTOY_MGMT')}}</span></a></li>
				@endif
				
				@if((isset($Cancellation) && is_array($Cancellation)) && in_array('0', $Cancellation) || $allPrev == '1')
				<li><a href="{{url('manage-cancelled-order')}}" class="<?php  if($current_route=='manage-cancelled-order') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_PAYMENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_PAYMENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_PAYMENT')}}</span></a></li>
				@endif
				
				@if((isset($Refer_Friend) && is_array($Refer_Friend)) && in_array('0', $Refer_Friend) || $allPrev == '1')
				<li><a href="{{url('refer-friend-report')}}" class="<?php  if($current_route=='refer-friend-report') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REFERFRIEND_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_REFERFRIEND_REPORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REFERFRIEND_REPORT')}}</span></a></li>
				@endif
				
				@if((isset($Featured_Resturant) && is_array($Featured_Resturant)) && in_array('0', $Featured_Resturant) || $allPrev == '1')
				<li><a href="{{url('manage_featured_store')}}" class="<?php  if($current_route=='manage_featured_store') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_FEATURED_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_FEATURED_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_FEATURED_STORE')}}</span></a></li>
				@endif
				
				@if((isset($Failed_orders) && is_array($Failed_orders)) && in_array('0', $Failed_orders) || $allPrev == '1')
				<li><a href="{{url('manage_failed_orders')}}" class="<?php  if($current_route=='manage_failed_orders') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FAIL_OR_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_FAIL_OR_AMT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FAIL_OR_AMT')}}</span></a></li>
				@endif

				@if((isset($Delivery_Boy_Map) && is_array($Delivery_Boy_Map)) && in_array('0', $Delivery_Boy_Map) || $allPrev == '1')
				<li><a href="{{url('admin-delivery-boy-map')}}" class="<?php  if($current_route=='delivery_boy_map') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_BOY_MAP')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_BOY_MAP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELIVERY_BOY_MAP')}}</span></a></li>
				@endif
				<!-- STARTING REPORT SECTION -->
				@if((isset($Reports) && is_array($Reports)) && in_array('0', $Reports) || $allPrev == '1')
				<li>
					<a href="#reports" data-toggle="collapse" class="<?php  if($current_route == "manage-order-report" || $current_route == 'earning_report' || $current_route == 'merchant-transaction-report' || $current_route == 'delboy_earning_report' || $current_route=='delboy-transaction-report' || $current_route=='consolidate-report') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-bar-chart"></i><span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REPORTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REPORTS')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="reports" class="collapse <?php  if($current_route == "manage-order-report" || $current_route == 'earning_report' || $current_route == 'merchant-transaction-report' || $current_route == 'delboy_earning_report' || $current_route=='delboy-transaction-report' || $current_route=='consolidate-report') { echo 'in'; }  ?>">
						<ul class="nav">
							<li><a href="{{url('manage-order-report')}}" class="<?php  if($current_route=='manage-order-report') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_OR_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_OR_REPORTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OR_REPORTS')}}</a></li> 

							<li><a href="{{url('earning_report')}}" class="<?php  if($current_route=='earning_report') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EARN_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_EARN_REPORTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EARN_REPORTS')}}</a></li>
							
							<li><a href="{{url('merchant-transaction-report')}}" class="<?php  if($current_route=='merchant-transaction-report') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MER_TRANS_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MER_TRANS_REPORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MER_TRANS_REPORT')}}</a></li>
							
							<li><a href="{{url('delboy_earning_report')}}" class="<?php  if($current_route=='delboy_earning_report') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_EARN_REPORTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_EARN_REPORTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEL_EARN_REPORTS')}}</a></li>
							
							<li><a href="{{url('delboy-transaction-report')}}" class="<?php  if($current_route=='delboy-transaction-report') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_TRANS_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_TRANS_REPORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_TRANS_REPORT')}}</a></li>
							
							<li><a href="{{url('consolidate-report')}}" class="<?php  if($current_route=='consolidate-report') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CONSOLIDATE_REPORT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CONSOLIDATE_REPORT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CONSOLIDATE_REPORT')}}</a></li>

						</ul>
					</div>
				</li>
				@endif
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<!-- EOF REPORT SECTION -->

			</ul>
		</nav>
	</div>
</div>