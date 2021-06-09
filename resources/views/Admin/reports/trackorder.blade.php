@extends('Admin.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
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
						
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div style="margin: 7px 23px;">
									@php
									$order_status = array('1'=>'Order placed','2'=>'Prepare to deliver','3'=>'Dispatched','4'=>'Started','5'=>'Arrived','6'=>'Delivered');
									$myVal='';
									@endphp
									@if(count($storewise_details) > 0 )
										@php
										$restname = (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_RESTSTORE_NAME');
										$orderId = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID');
										$orderStatus = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_STATUS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_STATUS'); 
										$agent_name = (Lang::has(Session::get('admin_lang_file').'.ADMIN_AGENT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGENT_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_AGENT_NAME');
										$agent_phone = (Lang::has(Session::get('admin_lang_file').'.ADMIN_AGENT_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGENT_PHONE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_AGENT_PHONE');
										$agent_email = (Lang::has(Session::get('admin_lang_file').'.ADMIN_AGENT_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGENT_EMAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_AGENT_EMAIL');
										$deli_name=(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELI_NAME') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELI_NAME');
										$deli_phone=(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELI_PHONE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELI_PHONE');
										$deli_email=(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELI_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELI_EMAIL') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELI_EMAIL');
										@endphp
										@foreach($storewise_details as $key=>$st_details)
										
											
											<div class="row">
												<div class="alert alert-info">
												<h3>{{ $restname }} : {{ $key }}</h3> 
												<br>
												@if(count($st_details) > 0)
													@foreach($st_details['delivery_detail'] as $st_detail)
														@foreach($st_detail as $stdet)
															<table class="table table-bordered" style="background-color:#f9f9f9">
																<tr>
																	<td><strong>{{ $orderId}}</strong></td>
																	<td>{{$stdet->ord_transaction_id}}</td>
																</tr>
																<tr style="background-color: #ffb613;color: #000;">
																	<td><strong>{{$orderStatus}}</strong></td>
																	<td>{{$order_status[$stdet->ord_status]}}</td>
																</tr>
																<tr>
																	<td><strong>{{$agent_name}}</strong></td>
																	<td>{{$stdet->agent_fname.' '.$stdet->agent_lname}}</td>
																</tr>
																<tr>
																	<td><strong>{{$agent_phone}}</strong></td>
																	<td>{{$stdet->agent_phone1}}</td>
																</tr>
																<tr>
																	<td><strong>{{$agent_email}}</strong></td>
																	<td>{{$stdet->agent_email}}</td>
																</tr>
																<tr>
																	<td><strong>{{$deli_name}}</strong></td>
																	<td>{{$stdet->deliver_fname.' '.$stdet->deliver_lname}}</td>
																</tr>
																<tr>
																	<td><strong>{{$deli_phone}}</strong></td>
																	<td>{{$stdet->deliver_phone1}}</td>
																</tr>
																<tr>
																	<td><strong>{{$deli_email}}</strong></td>
																	<td>{{$stdet->deliver_email}}</td>
																</tr>
															</table>
														@endforeach
														<br>
														<table class="table table-bordered" style="background-color:#f9f9f9">
														@foreach($st_details['itemDet'] as $itemDetails)
															@foreach($itemDetails as $itemDetail)
																<tr>
																	<td><strong>Product Name</strong></td>
																	<td>{{$itemDetail->pro_item_code.' '.$itemDetail->item_name}}</td>
																</tr>
															@endforeach
														@endforeach
														</table>
													@endforeach
												@endif
											  </div>
											</div>
											
										@endforeach
									@endif
								</div>
								<!--table-->
							</div>
							{{--Manage page ends--}}
						</div>

					</div>
				</div>
			</div>
		</div>
		
	</div>
	<!-- END MAIN CONTENT -->
</div>


@endsection
