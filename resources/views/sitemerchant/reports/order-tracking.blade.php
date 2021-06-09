@extends('sitemerchant.layouts.default')
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
							{{-- Display error message--}}
							
						</div>
						
						
						<div class="col-md-12 order-track">
							
							<div class="order-track-date">							
								<p><span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_DATE') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ORDER_DATE')}} :</span> @if($customer_details->ord_pre_order_date!=NULL) {{date('m/d/y H:i:s',strtotime($customer_details->ord_pre_order_date))}} @ELSE {{date('m/d/y H:i:s',strtotime($customer_details->ord_date))}} @endif</p>
								<p><span>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_ID') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}} :</span> {{$customer_details->ord_transaction_id}}</p>
							</div>
							<div class="order-track-sec1">
								<h4>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CUSTOMER_DETAILS')}}</h4>
								<p>{{$customer_details->ord_shipping_cus_name}}</p>
								<p>{{$customer_details->ord_shipping_mobile}}</p>
								<p>{{$customer_details->ord_shipping_mobile1}}</p>
								<p>{{$customer_details->order_ship_mail}}</p>
								<p>{{$customer_details->ord_shipping_address1}}</p>
								<p>{{$customer_details->ord_shipping_address}}</p>
								
							</div>
							@if(count($storewise_details) > 0 )
								@foreach($storewise_details as $key=>$commonDet)
									
									@if(count($commonDet['general_detail']) > 0 )
										@foreach($commonDet['general_detail'] as $comDet)
										<div class="order-rest-div">
											<div class="order-track-sec2">
												<div class="order-restaurant">
													<div class="order-restaurant-logo">
														@php $path = url('').'/public/images/noimage/'.$no_shop_logo; 
															$filename = public_path('images/restaurant/').$comDet['st_logo']; 
														@endphp
														@if(file_exists($filename) && $comDet['st_logo'] != '')
															@php $path = url('').'/public/images/restaurant/'.$comDet['st_logo'];@endphp
														@endif
														<img src="{{$path}}" alt="{{$key}}">	
													</div>
													<h3>{{$key}}</h3>
												</div>
												@if($AGENTMODULE==1)
												<div class="order-agent1">
													<h4>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_AGENT_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_AGENT_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_AGENT_DETAILS')}}</h4>
													@if($comDet['ord_task_status']==0)
														@if($comDet['ord_self_pickup']=='1')
															{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELF_PICKUP') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELF_PICKUP')}}
														@else
															<p>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('mer_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
														@endif
													@else
														<p>{{$comDet['agent_fname'].' '.$comDet['agent_lname']}}</p>
														<p>{{$comDet['agent_phone1']}}</p>
														<p>{{$comDet['agent_email']}}</p>
													@endif
												</div>
												@endif
												<div class="order-agent2">
													<h4>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DELBOY_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELBOY_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_DELBOY_DETAILS')}}</h4>
													@if($comDet['ord_task_status']==0)
														@if($comDet['ord_self_pickup']=='1')
															{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELF_PICKUP') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELF_PICKUP')}}
														@else
															<p>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('mer_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
														@endif
													@else
														<p>{{$comDet['deliver_fname'].' '.$comDet['deliver_lname']}}</p>
														<p>{{$comDet['deliver_phone1']}}</p>
														<p>{{$comDet['deliver_email']}} </p>
													@endif
												</div>
											</div>
											
											<div id="track-steps">
												<h4>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_STATUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_STATUS') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_ORDER_STATUS')}}</h4>
												<ul id="ul_{{$comDet['ord_id']}}">
													@if(count($commonDet['delivery_detail'])==1 && $comDet['ord_cancel_status']==1 && $comDet['ord_status']!=3 )
														<li style="float:left" data-target="#payCustModal_{{$comDet['ord_id']}}" data-toggle="modal"><div class="step active" data-desc="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_USER')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_USER') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_USER')}}" data-toggle="tooltip" title="{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}"><i class="fa fa-times"></i></div></li>
													@else
														@php $iconArray = array('1'=>'fa fa-shopping-basket','2'=>'fa fa-check','3'=>'fa fa-times','4'=>'fa fa-truck','5'=>'fa fa-truck','6'=>'fa fa-spinner','7'=>'fa fa-suitcase','8'=>'fa fa-male'); @endphp
														@if($comDet['ord_status']==8) @php $iconArray['8']='icon-valid'; @endphp @endif
														@php $ordStatusArray = order_status_array('mer_lang_file',$MER_OUR_LANGUAGE);  @endphp
														@for($i=1;$i<=8;$i++)
															@if($comDet['ord_self_pickup']=='1')
																@if($i > 3 && $i<=7)
																@elseif($comDet['ord_status'] >= 4 && $i==3)
																@elseif($comDet['ord_status'] == 2 && $i==3)
																@elseif($comDet['ord_status'] == 3 && $i==2)
																@else
																	@if($i==$comDet['ord_status']) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
																	<li><div class="step {{$active}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
																@endif
															@else	
																@if($comDet['ord_status'] >= 4 && $i==3)
																@elseif($comDet['ord_status'] == 2 && $i==3)
																@elseif($comDet['ord_status'] == 3 && $i==2)
																@else
																	@if($i==$comDet['ord_status']) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
																	<li><div class="step {{$active}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
																@endif
															@endif
														@endfor		
													@endif
												</ul>
												@if($comDet['ord_status']==8)
												<script>
													$(document).ready(function(){
														$('#ul_{{$comDet['ord_id']}} li').removeClass('pulse');
														$('#ul_{{$comDet['ord_id']}} li div').removeClass('active').addClass('done');
													});
												</script>
												@endif
											</div>
										@endforeach
									@endif
							
									@if(count($commonDet['delivery_detail']) > 0 )
										<div class="order-track-sec3">
											<table>
												@foreach($commonDet['delivery_detail'] as $comDet)
													@if(count($comDet) > 0 )
														@foreach($comDet as $commDet)
															@php $pro_images = explode('/**/',$commDet->pro_images);@endphp
															@if($commDet->pro_images!='')
																@if($commDet->ord_type=='Item')
																	@php $path = url('').'/public/images/noimage/'.$no_item ;@endphp 
																	@php $filename = public_path('images/restaurant/items/').$pro_images[0]; @endphp
																	@if($pro_images[0] != '' && file_exists($filename))
																		@php $path = url('').'/public/images/restaurant/items/'.$pro_images[0]; @endphp 
																	@else
																		@php $path = url('').'/public/images/noimage/'.$no_item; @endphp 
																	@endif
																@else
																	@php $filename = public_path('images/store/products/').$pro_images[0]; @endphp
																	@if($pro_images[0] != '' && file_exists($filename))
																		@php $path = url('').'/public/images/store/products/'.$pro_images[0]; @endphp 
																	@else
																		@php $path = url('').'/public/images/noimage/'.$no_item; @endphp 
																	@endif
																@endif
															@endif
															<tr>
																<td>
																	@if($commDet->ord_cancel_status=='1')
																		<a href="javascript:;" data-target="#payCustModal_{{$commDet->ord_id}}" data-toggle="modal"><span data-toggle="tooltip" title="{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}" >
																			@if($commDet->ord_status=='3')
																				{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_MERCHANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_MERCHANT') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_MERCHANT')}}
																			@else
																				{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_USER')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_USER') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_USER')}}
																			@endif
																			</span></a>
																	<!--MODAL -->
																	<div id="payCustModal_{{$commDet->ord_id}}" class="modal payModal" role="dialog" style="color:#000;" data-backdrop="static" data-keyboard="false">
																		<div class="modal-dialog">
																			<!-- Modal content-->
																			<div class="modal-content">
																				<div class="modal-header">
																					<button type="button" class="close" data-dismiss="modal">&times;</button>
																					<h4 class="modal-title">{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}</h4>
																				</div>
																				<div class="modal-body">
																				{{$commDet->ord_cancel_reason}}
																				</div>
																				<div class="modal-footer">
																					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																				</div>
																			</div>
																			
																		</div>
																	</div>
																	<!-- END OF MODAL-->
																	@else
																		&nbsp;
																	@endif
																</td>
																<td><img src="{{$path}}"></td>
																<td>
																	<p>{{$commDet->item_name}}</p>
																	<p>{{$commDet->pro_item_code}}</p>
																</td>
																<td>{{$commDet->ord_grant_total .' '.$commDet->ord_currency}}</td>
															</tr>
														@endforeach
													@endif
												@endforeach
											</table>
										</div>
									</div>
									@endif
								@endforeach
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
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