<style type="text/css">
	.sidebar .nav i {
    margin-right: 5px;
    
}
</style>
<?php $current_route = Route::getCurrentRoute()->uri();?>
<!-- LEFT SIDEBAR -->
<div id="sidebar-nav" class="sidebar">
	<div class="sidebar-scroll">
		<nav>
			<ul class="nav">
				
				<?php
				$check_business_type = DB::table('gr_merchant')->select('mer_business_type')->where('id','=',Session::get('merchantid'))->first()->mer_business_type;

                $check_featured_enable= DB::table('gr_general_setting')->select('gs_featured_store')->first();
                if(empty($check_featured_enable)===true){
                    $featured_enabled = 0;
                }else{
                    $featured_enabled = $check_featured_enable->gs_featured_store;
                }
				
				//echo $check_business_type;
				$check_store = DB::table('gr_store')->where('st_mer_id','=',Session::get('merchantid'))->count();
				?>
				@if($check_store > 0 )
					
					@if(Session::get('mer_business_type')=='1')
					{{--Store--}}
					<li><a href="{{url('merchant_dashboard')}}" <?php echo ($current_route == "merchant_dashboard") ? 'class="active"' : ''; ?> ><i class="lnr lnr-home"></i> <span>Dashboard</span></a></li>
					<li>
						<a href="#subCategory" data-toggle="collapse" <?php  if($current_route == "mer_manage-product-category" || $current_route == "mer_edit_product_category/{id}" || $current_route == "mer_manage-subproduct/{id}" || $current_route == "mer_edit_sub_category/{id}/{id}") { echo 'class="active"'; }else{ echo 'collapsed'; } ?> ><i class="lnr lnr-earth"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_CATE_MANAGE')) ? trans(Session::get('mer_lang_file').'.MER_CATE_MANAGE') : trans($MER_OUR_LANGUAGE.'.MER_CATE_MANAGE')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="subCategory" class="collapse <?php if($current_route == "mer_manage-product-category" || $current_route == "mer_edit_product_category/{id}" || $current_route == "mer_manage-subproduct/{id}" || $current_route == "mer_edit_sub_category/{id}/{id}") { echo 'in'; } ?>">
							<ul class="nav">
								
								<li><a href="{{url('mer_manage-product-category')}}" class="">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_PRO_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_PRO_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_PRO_CATE')}}</a></li>
							</ul>
						</div>
					</li>
					<li>
						<a href="{{url('mer-manage-store')}}"  class="<?php  if($current_route=='mer-manage-store') { echo 'active'; }  ?>"  ><i class="lnr lnr-store"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE_MGNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE_MGNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_STORE_MGNT')}}</span></a>
						
					</li>
					
					
					
					<li>
						<a href="#product" data-toggle="collapse" class="<?php  if($current_route == "mer-manage-product" || $current_route=='mer-add-product' || $current_route=='mer_product_bulk_upload' || $current_route=="mer-edit-product/{id}") { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-list-alt"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PDT_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_PDT_MGMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_PDT_MGMT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="product" class="collapse <?php  if($current_route == "mer-manage-product" || $current_route=='mer-add-product' || $current_route=='mer_product_bulk_upload' || $current_route=="mer-edit-product/{id}") { echo 'in'; }  ?>">
							<ul class="nav">
								<li><a href="{{url('mer-add-product')}}" class="<?php  if($current_route=='mer-add-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_PROD')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_PROD') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_PROD')}}</a></li> 
								<li><a href="{{url('mer-manage-product')}}" class="<?php  if($current_route=='mer-manage-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MANAGE_PDT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MANAGE_PDT') : trans($MER_OUR_LANGUAGE.'.ADMIN_MANAGE_PDT')}}</a></li>
								<li>
									<a href="{{url('mer_product_bulk_upload')}}" class="<?php  if($current_route=='mer_product_bulk_upload') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PRODUCT_BULK_UPLOAD')) ? trans(Session::get('mer_lang_file').'.ADMIN_PRODUCT_BULK_UPLOAD') : trans($MER_OUR_LANGUAGE.'.ADMIN_PRODUCT_BULK_UPLOAD')}}</a>
								</li>
							</ul>
						</div>
					</li>
					
					<li>
						<a href="#review" data-toggle="collapse" class="<?php  if($current_route == "mer-manage-product-review" || $current_route=='mer-manage-store-review' || $current_route=='mer-manage-restaurant-review' || $current_route=='mer_view_review/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-comments"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_REVIEW_MANAGEMENT')) ? trans(Session::get('mer_lang_file').'.ADMIN_REVIEW_MANAGEMENT') : trans($MER_OUR_LANGUAGE.'.ADMIN_REVIEW_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="review" class="collapse <?php  if($current_route == "mer-manage-product-review" || $current_route=='mer-manage-store-review' || $current_route=='mer-manage-restaurant-review' || $current_route=='mer-manage-order-review' || $current_route=='mer_view_review/{id}') { echo 'in'; }  ?>">
							<ul class="nav">
								<li><a href="{{url('mer-manage-product-review')}}" class="<?php  if($current_route=='mer-manage-product-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_REVIEW_PRO')) ? trans(Session::get('mer_lang_file').'.ADMIN_REVIEW_PRO') : trans($MER_OUR_LANGUAGE.'.ADMIN_REVIEW_PRO')}}</a></li> 
								<li><a href="{{url('mer-manage-store-review')}}" class="<?php  if($current_route=='mer-manage-store-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_ST_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_ST_REVIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_ST_REVIEW')}}</a></li> 
								<li><a href="{{url('mer-manage-order-review')}}" class="<?php  if($current_route=='mer-manage-order-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_OR_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_OR_REVIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_OR_REVIEW')}}</a></li>
								
							</ul>
						</div>
					</li>
					<!-- MANAGE ORDERS -->
					<li><a href="{{url('mer-manage-orders')}}" class="<?php  if($current_route == "mer-manage-orders" || $current_route =="mer-admin-invoice-order/{id}" || $current_route == "mer-admin-track-order/{id}") { echo 'active'; } else { echo 'collapsed'; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_MGMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_MGMT')}}</span></a></li>
					<!-- COMMISSION TRACKING-->
					<li><a href="{{url('merchant-commission-tracking')}}" <?php  if($current_route == "merchant-commission-tracking" || $current_route =="mer_commission_view_transaction/{id}") { echo 'active'; } else { echo 'collapsed'; } ?>><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_COMMISSION_TRACKING')) ? trans(Session::get('mer_lang_file').'.ADMIN_COMMISSION_TRACKING') : trans($MER_OUR_LANGUAGE.'.ADMIN_COMMISSION_TRACKING')}}</span></a></li>
					<li><a href="{{url('mer-manage-inventory')}}" class=""><i class="fa fa-first-order" <?php  if($current_route=='mer-manage-inventory') { echo 'active'; } else { echo ''; } ?>></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_INVENTOY_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVENTOY_MGMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_INVENTOY_MGMT')}}</span></a></li>
					<li><a href="{{url('mer-manage-cancelled-order')}}" class="<?php  if($current_route=='mer-manage-cancelled-order') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_CANCEL_PAYMENT')) ? trans(Session::get('mer_lang_file').'.MER_CANCEL_PAYMENT') : trans($MER_OUR_LANGUAGE.'.MER_CANCEL_PAYMENT')}}</span></a></li>
					@else
					{{--Restaurant--}}
					<li><a href="{{url('merchant_dashboard')}}" <?php echo ($current_route == "merchant_dashboard") ? 'class="active"' : ''; ?> ><i class="lnr lnr-home"></i> <span>Dashboard</span></a></li>
					<li>
						<a href="#subCategory" data-toggle="collapse" <?php  if($current_route == "mer_manage-item-category" || $current_route == "mer_edit_item_category/{id}") { echo 'class="active"'; }else{ echo 'collapsed'; } ?> ><i class="lnr lnr-earth"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_CATE_MANAGE')) ? trans(Session::get('mer_lang_file').'.MER_CATE_MANAGE') : trans($MER_OUR_LANGUAGE.'.MER_CATE_MANAGE')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="subCategory" class="collapse <?php if($current_route == "mer_manage-item-category" || $current_route == "mer_edit_item_category/{id}") { echo 'in'; } ?>">
							<ul class="nav">
								<li><a href="{{url('mer_manage-item-category')}}" class="">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_ITEM_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_ITEM_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_ITEM_CATE')}}</a></li>
								
							</ul>
						</div>
					</li>
					
					
					<li><a href="{{url('mer-manage-choices')}}" <?php  if($current_route == "mer-manage-choices" || $current_route == "mer-edit-choice/{id}") { echo 'class="active"'; }else{ echo 'collapsed'; } ?> ><i class="fa fa-thumbs-o-up"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_CHOICES')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_CHOICES') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_CHOICES')}}</span></a></li>
					
					<li>
						<a href="{{url('mer-manage-restaurant')}}"  class="<?php  if($current_route=='mer-manage-restaurant') { echo 'active'; }  ?>"  ><i class="fa fa-cutlery"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_REST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_REST') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_REST')}}</span></a>
						
					</li>
				
					<li>
						
						<a href="#item" data-toggle="collapse" class="<?php  if($current_route == "mer-manage-item" || $current_route=='mer-add-item' || $current_route=='mer_item_bulk_upload' || $current_route=='mer-edit-item/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-list-alt"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ITEM_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ITEM_MGMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ITEM_MGMT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="item" class="collapse <?php  if($current_route == "mer-manage-item" || $current_route=='mer-add-item' || $current_route=='mer_item_bulk_upload' || $current_route=='mer-edit-item/{id}') { echo 'in'; }  ?>">
							<ul class="nav">
								<li><a href="{{url('mer-add-item')}}" class="<?php  if($current_route=='mer-add-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD_ITEM')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD_ITEM') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD_ITEM')}}</a></li> 
								<li><a href="{{url('mer-manage-item')}}" class="<?php  if($current_route=='mer-manage-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MANAGE_ITEM')) ? trans(Session::get('mer_lang_file').'.ADMIN_MANAGE_ITEM') : trans($MER_OUR_LANGUAGE.'.ADMIN_MANAGE_ITEM')}}</a></li>
								
								<li>
									<a href="{{url('mer_item_bulk_upload')}}" class="<?php  if($current_route=='mer-manage-merchant') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ITEM_BULK_UPLOAD')) ? trans(Session::get('mer_lang_file').'.ADMIN_ITEM_BULK_UPLOAD') : trans($MER_OUR_LANGUAGE.'.ADMIN_ITEM_BULK_UPLOAD')}}</a>
								</li>
								
							</ul>
						</div>
					</li>
					
					<li>
						<a href="#review" data-toggle="collapse" class="<?php  if($current_route == "mer-manage-product-review" || $current_route=='mer-manage-item-review' || $current_route=='mer-manage-store-review' || $current_route=='mer-manage-restaurant-review' || $current_route == 'mer_view_review/{id}') { echo 'active'; } else { echo 'collapsed'; } ?>"  ><i class="fa fa-comments"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_REVIEW_MANAGEMENT')) ? trans(Session::get('mer_lang_file').'.ADMIN_REVIEW_MANAGEMENT') : trans($MER_OUR_LANGUAGE.'.ADMIN_REVIEW_MANAGEMENT')}}</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
						<div id="review" class="collapse <?php  if($current_route == "mer-manage-product-review" || $current_route=='mer-manage-item-review' || $current_route=='mer-manage-store-review' || $current_route=='mer-manage-restaurant-review' || $current_route=='mer-manage-order-review' || $current_route == 'mer_view_review/{id}') { echo 'in'; }  ?>">
							<ul class="nav">
							
								<li><a href="{{url('mer-manage-item-review')}}" class="<?php  if($current_route=='mer-manage-item-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_REVIEW_ITEM')) ? trans(Session::get('mer_lang_file').'.ADMIN_REVIEW_ITEM') : trans($MER_OUR_LANGUAGE.'.ADMIN_REVIEW_ITEM')}}</a></li>
								<li><a href="{{url('mer-manage-restaurant-review')}}" class="<?php  if($current_route=='mer-manage-restaurant-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_RES_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_RES_REVIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_RES_REVIEW')}}</a></li>
								<li><a href="{{url('mer-manage-order-review')}}" class="<?php  if($current_route=='mer-manage-order-review') { echo 'active'; } else { echo ''; } ?>">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_OR_REVIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_OR_REVIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_OR_REVIEW')}}</a></li>
								
							</ul>
						</div>
					</li>
					<!-- MANAGE ORDERS -->
					<li><a href="{{url('mer-manage-orders')}}" <?php  if($current_route == "mer-manage-orders" || $current_route =="mer-admin-invoice-order/{id}" || $current_route == "mer-admin-track-order/{id}") { echo 'active'; } else { echo 'collapsed'; } ?> ><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_MGMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_MGMT')}}</span></a></li>
					<!-- COMMISSION TRACKING-->
					<li><a href="{{url('merchant-commission-tracking')}}" <?php  if($current_route == "merchant-commission-tracking" || $current_route =="mer_commission_view_transaction/{id}") { echo 'active'; } else { echo 'collapsed'; } ?> ><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_COMMISSION_TRACKING')) ? trans(Session::get('mer_lang_file').'.ADMIN_COMMISSION_TRACKING') : trans($MER_OUR_LANGUAGE.'.ADMIN_COMMISSION_TRACKING')}}</span></a></li>
					<li><a href="{{url('mer-manage-inventory')}}" class=""><i class="fa fa-first-order" <?php  if($current_route=='mer-manage-inventory') { echo 'active'; } else { echo ''; } ?>></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_INVENTOY_MGMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVENTOY_MGMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_INVENTOY_MGMT')}}</span></a></li>
					<li><a href="{{url('mer-manage-cancelled-order')}}" class="<?php  if($current_route=='mer-manage-cancelled-order') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_CANCEL_PAYMENT')) ? trans(Session::get('mer_lang_file').'.MER_CANCEL_PAYMENT') : trans($MER_OUR_LANGUAGE.'.MER_CANCEL_PAYMENT')}}</span></a></li>
				
					@endif
				@else
					@if($check_business_type=='1')
						<li>
						<a href="{{url('mer-manage-store')}}"  class="<?php  if($current_route=='mer-manage-store') { echo 'active'; }  ?>"  ><i class="lnr lnr-store"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_STORE_MGNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_STORE_MGNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_STORE_MGNT')}}</span></a></li>
					@else
										
					<li>
						<a href="{{url('mer-manage-restaurant')}}"  class="<?php  if($current_route=='mer-manage-restaurant') { echo 'active'; }  ?>"  ><i class="fa fa-cutlery"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MNGE_REST')) ? trans(Session::get('mer_lang_file').'.ADMIN_MNGE_REST') : trans($MER_OUR_LANGUAGE.'.ADMIN_MNGE_REST')}}</span></a></li>
					@endif
				@endif

                    <?php if($featured_enabled==1) { ?>
					<li><a href="{{url('make-featured')}}" class="<?php  if($current_route=='make-featured') { echo 'active'; } else { echo ''; } ?>"><i class="fa fa-first-order"></i> <span>{{(Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_REST')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_REST') : trans($MER_OUR_LANGUAGE.'.MER_FEATURED_REST')}}</span></a></li>
                    <?php } ?>



			</ul>
		</nav>
	</div>
</div>
<!-- END LEFT SIDEBAR -->