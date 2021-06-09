@extends('sitemerchant.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
<style>
.order-restaurant {
    width: 33%;
    border: 1px solid #ddd;
    float: left;
    padding: 19px 10px;
    background: #fff;
    margin: 5px;
}
.order-restaurant-logo {
    float: left;
    width: 40%;
}
.order-restaurant h3 {
    float: right;
    margin: 62px 0;
    width: 60%;
}
.order-agent {
    width: 32%;
    float: right;
    border: 1px solid #ddd;
    padding: 10px;
    background: #fff;
    margin: 5px;
}
</style>
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-12">
						
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
							</div>
							{{-- Display error message--}}
							
						</div>
						
						@if(count($storewise_details) > 0 )
							<div class="col-md-12 order-track">
								@foreach($storewise_details as $key=>$st_details)
									@if(count($st_details['customer_detail']) > 0 )
										@foreach($st_details['customer_detail'] as $cust_detail)
											<div class="order-track-sec1">
												<h4>Customer Details</h4>
												<p>{{$cust_detail['ord_shipping_cus_name']}}</p>
												
												<p>{{$cust_detail['ord_shipping_mobile']}}</p>
												<p>{{$cust_detail['ord_shipping_mobile1']}}</p>
												<p>{{$cust_detail['order_ship_mail']}}</p>
												<p>{{$cust_detail['ord_shipping_address1']}}</p>
												<p>{{$cust_detail['ord_shipping_address']}}</p>
												
												<p>Order ID </p>
												<p>{{$cust_detail['ord_transaction_id']}}
											</div>
										@endforeach
									@endif
									<div class="order-track-sec2">
										<div class="order-restaurant">
											<div class="order-restaurant-logo">
												@php 
													$path = url('').'/public/images/noimage/'.$no_shop_logo; 
													$filename = public_path('images/store/').$cust_detail['st_logo']; 
												@endphp
												@if(file_exists($filename) && $cust_detail['st_logo'] != '')
													@php $path = url('').'/public/images/store/'.$cust_detail['st_logo'];@endphp
												@endif
												<img src="{{$path}}" alt="{{$key}}">			

											</div>
											<h3>{{$key}}</h3>
										</div>
										@foreach($st_details['delivery_detail'] as $st_detail)
											@foreach($st_detail as $stdet)
												<div class="order-agent">
													<h4>Agent Details</h4>
													<p>{{$stdet->agent_fname.' '.$stdet->agent_lname}}</p>
													<p>{{$stdet->agent_phone1}}</p>
													<p>{{$stdet->agent_email}}</p>
												</div>
												<div class="order-agent">
													<h4>Delivery boy Details</h4>
													<p>{{$stdet->deliver_fname.' '.$stdet->deliver_lname}}</p>
													<p>{{$stdet->deliver_phone1}}</p>
													<p>{{$stdet->deliver_email}}</p>
												</div>
											@foreach
										@foreach
									</div>
								
								<div id="track-steps">
									<h4>Order Status</h4>
									<ul>
										<li><div class="step" data-desc="Order placed"><i class="fa fa-shopping-basket"></i></div></li>
										<li><div class="step" data-desc="Prepare to deliver"><i class="fa fa-truck"></i></div></li>
										<li><div class="step active" data-desc="Dispatched"><i class="fa fa-truck"></i></div></li>
										<li><div class="step" data-desc="Started"><i class="fa fa-spinner"></i></div></li>
										<li><div class="step" data-desc="Arrived"><i class="fa fa-suitcase"></i></div></li>
										<li><div class="step" data-desc="Delivered"><i class="fa fa-male"></i></div></li>						
									</ul>
								</div>
								
								
								
								<div class="order-track-sec3">
									<table>
										<tr>
											<td><img src="http://192.168.0.65/edison_grocery_v1/public/images/restaurant/restaurant1223775606.jpg"></td>
											<td>
												<p>Chinese</p>
												<p>PFDJF - 1233</p>
											</td>
											<td>RS 450.00</td>
										</tr>
										<tr>
											<td><img src="http://192.168.0.65/edison_grocery_v1/public/images/restaurant/restaurant1070242171.gif"></td>
											<td>
												<p>Chinese</p>
												<p>PFDJF - 1233</p>
											</td>
											<td>RS 450.00</td>
										</tr>
										<tr>
											<td><img src="http://192.168.0.65/edison_grocery_v1/public/images/restaurant/restaurant1223775606.jpg"></td>
											<td>
												<p>Chinese</p>
												<p>PFDJF - 1233</p>
											</td>
											<td>RS 450.00</td>
										</tr>
									</table>
								</div>
								
								
							</div>
						@else
							<div class="col-md-12 order-track">
								{{(Lang::has(Session::get('mer_lang_file').'.MER_NO_DETAILS')) ? trans(Session::get('mer_lang_file').'.MER_NO_DETAILS') : trans($MER_OUR_LANGUAGE.'.MER_NO_DETAILS')}}
							</div>
						@endif
					</div>
					
					
				</div>
			</div>
		</div>
		
	</div>
	<!-- END MAIN CONTENT -->
</div>




<script>
	$('.step').each(function(index, el) {
		$(el).not('.active').addClass('done');
		$('.done').html('<i class="icon-valid"></i>');
		if($(this).is('.active')) {
			$(this).parent().addClass('pulse')
			return false;
		}
	});
</script>




@stop