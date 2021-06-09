@extends('DeliveryManager.layouts.default')
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
								<p><span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_DATE')}} :</span> @if($customer_details->ord_pre_order_date!=NULL) {{date('m/d/y H:i:s',strtotime($customer_details->ord_pre_order_date))}} @ELSE {{date('m/d/y H:i:s',strtotime($customer_details->ord_date))}} @endif</p>
								<p><span>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_ID') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_ID')}} :</span> {{$customer_details->ord_transaction_id}}</p>
							</div>
							<div class="order-track-sec1">
								<h4>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_DETAILS')}}</h4>
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
														@php $path = url('').'/public/images/noimage/'.$no_shop_logo; @endphp
														@if($comDet['st_type'] == '1')
															@php
																$filename = public_path('images/restaurant/').$comDet['st_logo']; 
															@endphp
															@if(file_exists($filename) && $comDet['st_logo'] != '')
																@php $path = url('').'/public/images/restaurant/'.$comDet['st_logo'];@endphp
															@endif
														@elseif($comDet['st_type'] == '2')
															@php
																$filename = public_path('images/store/').$comDet['st_logo']; 
															@endphp
															@if(file_exists($filename) && $comDet['st_logo'] != '')
																@php $path = url('').'/public/images/store/'.$comDet['st_logo'];@endphp
															@endif
														@endif
														<img src="{{$path}}" alt="{{$key}}">	
													</div>
													<h3>{{$key}} <br> {{$comDet['st_address']}}</h3>
												</div>
												@if($AGENTMODULE==1)
												<div class="order-agent1">
													<h4>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGENT_DETAILS')}}</h4>
													@if($comDet['ord_task_status']==0)
														<p>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NOT_YET_ASSIGNED')}}</p>
													@elseif($comDet['ord_task_status'] == 1 && $comDet['ord_agent_acpt_status'] == 1)
														<p>{{ucfirst($comDet['agent_fname']).' '.$comDet['agent_lname']}}</p>
														<p>{{$comDet['agent_phone1']}}</p>
														<p>{{$comDet['agent_email']}}</p>
													@else
														<p>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NOT_YET_ASSIGNED')}}</p>
													@endif
												</div>
												@endif
												<div class="order-agent2">
													<h4>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_DETAILS')}}</h4>
													@if($comDet['ord_task_status']==0)
														<p>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NOT_YET_ASSIGNED')}}</p>
													@elseif($comDet['ord_task_status'] == 1 && $comDet['ord_delboy_act_status'] == 1)
													<p>{{ucfirst($comDet['deliver_fname']).' '.$comDet['deliver_lname']}}</p>
													<p>{{$comDet['deliver_phone1']}}</p>
													<p>{{$comDet['deliver_email']}}</p>
													@else
													<p>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NOT_YET_ASSIGNED') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NOT_YET_ASSIGNED')}}</p>
													@endif
												</div>
											</div>
											

											<div id="track-steps">
												<h4>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_STATUS')}}</h4>
												
												<!-- Order flow diagram -->
												@if(count($commonDet['delivery_detail'])>0)
													@foreach($commonDet['delivery_detail'] as $detailVal)
													@php 
														$chk_ord_status = $chk_cancel_status = array(); 
													@endphp
													
													
													@foreach($detailVal as $detVal)
													@php 
														 array_push($chk_ord_status,$detVal->ord_status);	
														 array_push($chk_cancel_status,$detVal->ord_cancel_status);
													@endphp
													<ul id="ul_{{$detVal->ord_id}}">
													@if($detVal->ord_cancel_status == 1 && $detVal->ord_status != 3 && !in_array('0',$chk_cancel_status))
														<li style="float:left"><div class="step active" data-desc="{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_CANCELLED_BY_USER')) ? trans(Session::get('DelMgr_lang_file').'.DEL_CANCELLED_BY_USER') : trans($DELMGR_OUR_LANGUAGE.'.DEL_CANCELLED_BY_USER')}}" ><i class="fa fa-times"></i></div></li>
														@break
													@elseif($detVal->ord_status == 9)
													<li style="float:left" data-target="#failReasonModal_{{$detVal->ord_id}}" data-toggle="modal">
														<a href="javascript:;" data-toggle="tooltip" title="{{(Lang::has(Session::get('DelMgr_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('DelMgr_lang_file').'.MER_REJECTED_REASON') : trans($DELMGR_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}" >
														<div class="step active" data-desc="{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_DELIVERY_FAILED')) ? trans(Session::get('DelMgr_lang_file').'.DEL_DELIVERY_FAILED') : trans($DELMGR_OUR_LANGUAGE.'.DEL_DELIVERY_FAILED')}}" ><i class="fa fa-times"></i>
														</div>
														</a>
													</li>
													{{-- view failed reason --}}
														<div id="failReasonModal_{{$detVal->ord_id}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<div class="modal-content">
																		<div class="modal-header">
																			<h4 class="modal-title">{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_REASON_FAIL')) ? trans(Session::get('DelMgr_lang_file').'.DEL_REASON_FAIL') : trans($DELMGR_OUR_LANGUAGE.'.DEL_REASON_FAIL')}}</h4>
																		</div>
																		<div class="modal-body">
																			<p> {!!$detVal->ord_failed_reason!!}</p>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div>
															</div>
													@else
														@php $iconArray = array('1'=>'fa fa-shopping-basket','2'=>'fa fa-check','3'=>'fa fa-times','4'=>'fa fa-truck','5'=>'fa fa-truck','6'=>'fa fa-spinner','7'=>'fa fa-suitcase','8'=>'fa fa-male'); @endphp
														@if($detVal->ord_status==8) @php $iconArray['8']='icon-valid'; @endphp @endif
														@php $ordStatusArray = order_status_array('DelMgr_lang_file',$DELMGR_OUR_LANGUAGE);  @endphp
														@for($i=1;$i<=8;$i++)
															
																@if($comDet['ord_status'] >= 4 && $i==3)
																@elseif($comDet['ord_status'] == 2 && $i==3)
																@elseif($comDet['ord_status'] == 3 && $i==2)
																@else
																	@if($i==$detVal->ord_status) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
																	@php if($i<$detVal->ord_status) { $done='done';} else { $done=''; } @endphp
																	<li><div class="step {{$active}} {{$done}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
																@endif
															
														@endfor		
													@endif
												</ul>
								
													@if (in_array('0',$chk_cancel_status) || $detVal->ord_status == '3')
												        @break
												    @elseif($loop->last && !in_array('0',$chk_cancel_status))
												    	@break
												    @endif
												@endforeach
												@php
													$chk_cancel_status = array();
													$chk_ord_status = array();
												@endphp
											 @endforeach
											@endif
												<!-- End Order flow diagram -->
																	@if($comDet['ord_status']==8)
														<script>
															$(document).ready(function(){
																$("#ul_{{$detVal->ord_id}} li").removeClass('pulse');
																$("#ul_{{$detVal->ord_id}} li div").removeClass('active').addClass('done');
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
																<td><img src="{{$path}}"></td>
																<td>
																	<p>{{$commDet->item_name}}</p>
																	<p>{{$commDet->itemCodeType}} : {{$commDet->pro_item_code}}</p>
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

	$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
	});
</script>




@stop