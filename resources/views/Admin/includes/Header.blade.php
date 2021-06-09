	@php extract($privileges); @endphp
	@php	
		$cus_notification = 0;
		$mer_notification = 0;
		$pdt_notification = 0;
		$item_notification = 0;
		$pay_notification = 0;
		$order_notification = 0;
		$del_notification = 0;
	@endphp
	
		@if((isset($Customer) && is_array($Customer)) && in_array('0', $Customer) || $allPrev == '1')
		@php $cus_notification = DB::table('gr_customer')->where('cus_read_status','=','0')->where('cus_status','!=','2')->count(); @endphp
		@endif
		
		@if((isset($Customer) && is_array($Customer)) && in_array('0', $Customer) || $allPrev == '1')
		@php $mer_notification = DB::table('gr_merchant')->where('mer_read_status','=','0')->where('mer_status','!=','2')->count(); @endphp
		@endif
		
		@if((isset($Delivery_Boy) && is_array($Delivery_Boy)) && in_array('0', $Delivery_Boy) || $allPrev == '1')
		@php $del_notification = DB::table('gr_delivery_member')->where('deliver_read_status','=','0')->where('deliver_status','!=','2')->count(); @endphp
		@endif

		@if((isset($Product) && is_array($Product)) && in_array('0', $Product) || $allPrev == '1')
		@php $pdt_notification = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','1')->where('pro_status','!=','2')->count(); @endphp
		@endif
		
		@if((isset($Item) && is_array($Item)) && in_array('0', $Item) || $allPrev == '1')
		@php $item_notification = DB::table('gr_product')->where('pro_read_status','=','0')->where('pro_type','=','2')->where('pro_status','!=','2')->count(); @endphp
		@endif
		
		@if((isset($Commission) && is_array($Commission)) && in_array('0', $Commission) || $allPrev == '1')
		@php $pay_notification = DB::table('gr_notification')->where(['no_status' => '1','read_status' => '0'])->count(); @endphp
		@endif
		
		
		@if((isset($Order) && is_array($Order)) && in_array('0', $Order) || $allPrev == '1')

		@php 
		
			$order_notification = DB::table('gr_general_notification')->where('receiver_id','=',Session::get('admin_id'))->where('receiver_type','=','gr_admin')->where('read_status','=','0')->count();
		@endphp
		@endif

		@php $tot_notification = $cus_notification+$mer_notification+$pdt_notification+$item_notification + $pay_notification+$order_notification+$del_notification;  @endphp
	<style type="text/css">
	.qustnDiv {position: fixed;width: 40px;height: 40px;background: #373756;z-index: 99;right: 0;top: 90px;display: block;padding: 7px 0;text-align: center;color: #fff;font-size: 21px;cursor: pointer;}	
	div.vidImg {position: fixed;background: #fff;width: 50%;z-index: 9999;left: 30%;box-shadow: 0px 2px 4px #000000;padding: 50px;transition: 0.5s;display: none;top: 20%;}
	.vidImg .flex {display: flex;flex-direction: row;justify-content: center;flex-wrap: wrap;}
	.vidImg a {display: flex;flex-direction: column;align-items: center;padding: 40px 50px;background: #ededed;margin: 5px;min-width: 220px;min-height: 150px;justify-content: center;color: #373756;font-size: 24px;text-transform: uppercase;font-weight: 500;flex: 1;}
	.vidImg a:hover {background: #ff5215;color: #fff;}
	.vidImg a .fa {font-size: 44px;margin-bottom: 20px;}
	.fa-times-circle-o {position: absolute;top: 0px;right: 0px;font-size: 21px;opacity: 0.5;color: #373756;background: #fff;cursor: pointer;}
	</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".qustnClick").click(function(){		
		$("#video").fadeToggle();
	});
});
</script>
@if($tutorial_status == '1' && in_array($path_url, $allowed_urls) && ($video_status == 1 || $doc_status == 1))	
<div class="qustnDiv qustnClick">
	<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</div>
@endif
<div class="vidImg" id="video">
	<p><?php echo  ucfirst($heading).' Tutorial'; ?></p>
	<i class="fa fa-times-circle-o qustnClick" aria-hidden="true"></i>
	<div class="flex">
		<?php if($video_status == '1'){	$video_url = url('/public/tutorials/'.$path_url.'/'.$video_name); ?>
		<a onclick="window.open('<?php echo $video_url; ?>', '_blank', 'location=yes,height=600,width=1300,scrollbars=no,status=yes');" >
			<span><i class="fa fa-file-video-o" aria-hidden="true"></i></span>
			<span>{{(Lang::has(Session::get('admin_lang_file').'.VIDEO')) ? trans(Session::get('admin_lang_file').'.VIDEO') : trans($ADMIN_OUR_LANGUAGE.'.VIDEO')}}</span>	
		</a>
		<?php } ?>

		<?php if($doc_status == '1'){  ?>	
		<a href="{{url('/public/tutorials/'.$path_url.'/'.$doc_name)}}" target="_blank">
			<span><i class="fa fa-file-text" aria-hidden="true"></i></span>
			<span>{{(Lang::has(Session::get('admin_lang_file').'.DOCUMENT')) ? trans(Session::get('admin_lang_file').'.DOCUMENT') : trans($ADMIN_OUR_LANGUAGE.'.DOCUMENT')}}</span>		
		</a>
		<?php } ?>
	</div>	
</div>
<nav class="navbar navbar-default navbar-fixed-top">
			<div class="brand">
				@if(count($logo_settings_details) > 0)
					@php
						foreach($logo_settings_details as $logo_set_val){ }
						$filename = public_path('images/logo/').$logo_set_val->admin_logo;
					@endphp
					@if(file_exists($filename))
					<a href="{{url('')}}" target="new"><img src="{{url('public/images/logo/'.$logo_set_val->admin_logo)}}" alt="Logo" class="img-responsive logo"></a>
					@else
					<a href="{{url('')}}" target="new"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo" width="50px"></a>
					@endif
				@else
					<a href="{{url('')}}" target="new"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo" width="50px"></a>
				@endif
				
			</div>
			
			<div class="container-fluid admin-menu">
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
				
				<div id="navbar-menu" >
					<ul class="nav navbar-nav navbar-right">
						@if($tot_notification > 0 )
						<li class="dropdown">
							<a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown">
								<i class="lnr lnr-alarm"></i> <span class="badge bg-danger">{{$tot_notification}}</span>
							</a>
							<ul class="dropdown-menu notifications">
							@if((isset($Customer) && is_array($Customer)) && in_array('0', $Customer) || $allPrev == '1')
								<li><a href="{{url('manage-customer/1')}}" class="notification-item"><span class="dot bg-warning"></span>{{$cus_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_CUSTOMER')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_CUSTOMER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_CUSTOMER') }}</a></li>
							@endif
							@if((isset($Merchant) && is_array($Merchant)) && in_array('0', $Merchant) || $allPrev == '1')
								<li><a href="{{url('manage-merchant/1')}}" class="notification-item"><span class="dot bg-danger"></span>{{$mer_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_MERCHANT') }}</a></li>
							@endif	
							@if((isset($Delivery_Boy) && is_array($Delivery_Boy)) && in_array('0', $Delivery_Boy) || $allPrev == '1')
								<li><a href="{{url('manage-deliveryboy-admin1/1')}}" class="notification-item"><span class="dot bg-danger"></span>{{$del_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_DEL_BOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_DEL_BOY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_DEL_BOY') }}</a></li>
							@endif
							{{--@if((isset($Product) && is_array($Product)) && in_array('0', $Product) || $allPrev == '1')--}}
								{{--<li><a href="{{url('manage-product/1')}}" class="notification-item"><span class="dot bg-success"></span>{{$pdt_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_PDT')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_PDT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_PDT') }}</a></li>--}}
							{{--@endif--}}
							@if((isset($Item) && is_array($Item)) && in_array('0', $Item) || $allPrev == '1')
								<li><a href="{{url('manage-item/2')}}" class="notification-item"><span class="dot bg-primary"></span>{{$item_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_ITEM')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_ITEM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_ITEM') }}</a></li>
							@endif	
								
							@if((isset($Commission) && is_array($Commission)) && in_array('0', $Commission) || $allPrev == '1')							
								<li><a href="{{url('read-notification/1')}}" class="notification-item"><span class="dot bg-primary"></span>{{$pay_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NEW_PAY_REQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_NEW_PAY_REQ') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NEW_PAY_REQ') }}</a></li>
							@endif
							@if((isset($Order) && is_array($Order)) && in_array('0', $Order) || $allPrev == '1')							



					<li><a href="{{url('admin-order-notification')}}" class="notification-item"><span class="dot bg-warning"></span>{{$order_notification}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_NOTIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_NOTIFICATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_NOTIFICATION') }}</a></li>

							@endif
							<!--<li><a href="#" class="notification-item"><span class="dot bg-success"></span>Your request has been approved</a></li>
								<li><a href="#" class="more">See all notifications</a></li>-->
							</ul>
						</li>
						@endif
						<!-- <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="lnr lnr-question-circle"></i> <span>Help</span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
							<ul class="dropdown-menu">
								<li><a href="#">Basic Use</a></li>
								<li><a href="#">Working With Data</a></li>
								<li><a href="#">Security</a></li>
								<li><a href="#">Troubleshooting</a></li>
							</ul>
						</li> -->
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="{{url('')}}/public/admin/assets/img/user.png" class="img-circle" alt="{{Session::get('admin_name')}} "> <span>{{Session::get('admin_name')}} </span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
							<ul class="dropdown-menu">
								<li><a href="{{ url('admin-profile') }}"><i class="lnr lnr-user"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MYPROFILE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MYPROFILE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MYPROFILE')}}</span></a></li>
								
								<li><a href="{{ url('admin-change-password') }}"><i class="lnr lnr-cog"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CHANGE_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHANGE_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CHANGE_PASSWORD')}}</span></a></li>
								<li><a href="{{url('admin-logout')}}"><i class="lnr lnr-exit"></i> <span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGOUT')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGOUT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGOUT')}}</span></a></li>
							</ul>
							</li>
						<!-- <li>
							<a class="update-pro" href="https://www.themeineed.com/downloads/klorofil-pro-bootstrap-admin-dashboard-template/?utm_source=klorofil&utm_medium=template&utm_campaign=KlorofilPro" title="Upgrade to Pro" target="_blank"><i class="fa fa-rocket"></i> <span>UPGRADE TO PRO</span></a>
						</li> -->
					</ul>
				</div>
			</div>
		</nav>