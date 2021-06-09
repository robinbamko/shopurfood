<?php $current_route = Route::getCurrentRoute()->uri();  ?>
<div class="navbar-expand-lg" id="sidebar-wrapper">        
    {{-- <button>@lang(Session::get('front_lang_file').'.FRONT_ORDER_CATEGORY')</button> --}}
	<nav class="navbar d-lg-none d-md-none">
		@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebar-nav" aria-controls="sidebar-nav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>

		</button>
	</nav>
	
	
	<ul class="sidebar-nav nav nav-tabs collapse navbar-collapse" id="sidebar-nav">  
		
		
		
		<li class="<?php  if($current_route == "customer_profile") { echo 'active'; } ?>">	
			
			<a href="{{url('customer_profile')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-user-circle-o"></i><span>@lang(Session::get('front_lang_file').'.FRONT_CUSTOMER_PROFILE')</span></a>
		</li>
		<li class="<?php  if($current_route == "shipping_address") { echo 'active'; } ?>">
			
			<a href="{{url('shipping_address')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-shopping-cart"></i><span>@lang(Session::get('front_lang_file').'.FRONT_MY_SHIPPING_ADDRESS')</span></a>              
		</li>
		
		<li class="<?php  if($current_route == "user-wishlist") { echo 'active'; } ?>">
			
			<a href="{{url('user-wishlist')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-heart"></i><span>@lang(Session::get('front_lang_file').'.FRONT_WISHLIST')</span></a>
		</li>
		<li class="<?php  if($current_route == "my-orders") { echo 'active'; } ?>">
			
			<a href="{{url('my-orders')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-truck"></i><span>@lang(Session::get('front_lang_file').'.FRONT_MY_ORDERS')</span></a>
		</li>
		
		<li class="<?php  if($current_route == "user-change-password") { echo 'active'; } ?>">
			
			<a href="{{url('user-change-password')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-key"></i><span>@lang(Session::get('front_lang_file').'.FRONT_CHANGE_PASSWORD')</span></a>
		</li>
		<li class="<?php  if($current_route == "user-payment-settings") { echo 'active'; } ?>">
			
			<a href="{{url('user-payment-settings')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-money"></i><span>@lang(Session::get('front_lang_file').'.FRONT_PAYMENT_SETTINGS')</span></a>
		</li>
		<li class="<?php  if($current_route == "user-wallet") { echo 'active'; } ?>">
			
			<a href="{{url('user-wallet')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-google-wallet"></i><span>@lang(Session::get('front_lang_file').'.ADMIN_MY_WALLET')</span></a>
		</li>
		<li class="<?php  if($current_route == "user-review") { echo 'active'; } ?>">
			
			<a href="{{url('user-review')}}" id="mainCatSelId" class="maincatA "><i class="fa fa-comments-o"></i><span>@lang(Session::get('front_lang_file').'.ADMIN_MY_REVIEW')</span></a>
		</li>
	</ul>
</div>