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
								@if(empty($ord_status_chart)===false)
									@if($ord_status_chart->ord_pre_order_date === NULL)
									@else
										<span style="color:red;margin-left: 10px;font-weight: bold;">( {{(Lang::has(Session::get('mer_lang_file').'.MER_PRE_OR_DT')) ? trans(Session::get('mer_lang_file').'.MER_PRE_OR_DT') : trans($MER_OUR_LANGUAGE.'.MER_PRE_OR_DT')}} : {{date('m/d/Y H:i:s',strtotime($ord_status_chart->ord_pre_order_date))}} )</span>
									@endif
								@endif
								<a href="{{url('mer-admin-invoice-order/'.$ord_transaction_id)}}" target="_blank" class="pull-right btn btn-success">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_VIEW')}} {{(Lang::has(Session::get('mer_lang_file').'.ADMIN_INVOICE')) ? trans(Session::get('mer_lang_file').'.ADMIN_INVOICE') : trans($MER_OUR_LANGUAGE.'.ADMIN_INVOICE')}}</a>
							</div>
						
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								{{-- Display  message--}}
								@if(Session::has('message'))
							    <div class="alert alert-success alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							   		{{Session::get('message')}}    
							    </div>
								@endif
								<div id="track-steps">
								@php $cancelPlusReject = $status_qry->rejected+$status_qry->cancelled;@endphp
								@if($status_qry->rejected==$status_qry->totals)
									<ul id="ul_3">
										<li><div class="step  done" data-desc="New Order"><i class="icon-valid"></i></div></li>
										<li><div class="step  active" data-desc="Reject"><i class="icon-valid"></i></div></li>
									</ul>
								@elseif($status_qry->cancelled==$status_qry->totals)
									<ul id="ul_3">
										<li><div class="step  done" data-desc="New Order"><i class="icon-valid"></i></div></li>
										<li><div class="step  active" data-desc="Cancelled"><i class="icon-valid"></i></div></li>
									</ul>


								@elseif($status_qry->failed==$status_qry->totals)
									<ul id="ul_3">
										<li><div class="step  done" data-desc="New Order"><i class="icon-valid"></i></div></li>
										<li><div class="step  active" data-desc="Failed"><i class="icon-valid"></i></div></li>
									</ul>
								@elseif($cancelPlusReject!=$status_qry->totals && empty($ord_status_chart)===false)
									@php 
										$iconArray = array('1'=>'fa fa-shopping-basket','2'=>'fa fa-check','3'=>'fa fa-times','4'=>'fa fa-truck','5'=>'fa fa-truck','6'=>'fa fa-spinner','7'=>'fa fa-suitcase','8'=>'fa fa-male','9' => 'fa fa-times'); 
										if($ord_status_chart->ord_status=='8') { $iconArray['8']='icon-valid'; }
										$ordStatusArray = order_status_array('mer_lang_file',$MER_OUR_LANGUAGE);
									@endphp
									<ul id="ul_3">
									@for($i=1;$i<=8;$i++)
										<!--CHECKING WHEHTER IT IS SELF PICKUP-->
										@if($ord_status_chart->ord_self_pickup=='1')
											@if($i > 3 && $i<=7)
											@elseif($ord_status_chart->ord_status >= 4 && $i==3)
											@elseif($ord_status_chart->ord_status == 2 && $i==3)
											@elseif($ord_status_chart->ord_status == 3 && $i==2)
											@else
												@if($i==$ord_status_chart->ord_status) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
												<li><div class="step {{$active}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
											@endif
										@else	
											@if($ord_status_chart->ord_status >= 4 && $i==3)
											@elseif($ord_status_chart->ord_status == 2 && $i==3)
											@elseif($ord_status_chart->ord_status == 3 && $i==2)
											@else
												@if($i==$ord_status_chart->ord_status) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
												@php if($i<$ord_status_chart->ord_status) { $done='done';} else { $done=''; } @endphp
												<li><div class="step {{$active}} {{$done}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
											@endif
										@endif
									@endfor
									</ul>
									@if($ord_status_chart->ord_status==8)
										<script>
											$(document).ready(function(){
												$("#ul_3 li").removeClass('pulse');
												$("#ul_3 li div").removeClass('active').addClass('done');
											});
										</script>
										@endif
								@endif
								</div>
								<div class="" style="">
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
		                                	<tr>
												<th align="center">{{(Lang::has(Session::get('mer_lang_file').'.MER_SNO')) ? trans(Session::get('mer_lang_file').'.MER_SNO') : trans($MER_OUR_LANGUAGE.'.MER_SNO')}}</th>
			                                   <th>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ITEMRPDT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_ITEMRPDT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_ITEMRPDT_NAME')}}</th>
			                                    <th>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_QTY')}}
			                                    </th>
			                                    <th>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PRICE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PRICE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PRICE')}}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_TAX')) ? trans(Session::get('mer_lang_file').'.ADMIN_TAX') : trans($MER_OUR_LANGUAGE.'.ADMIN_TAX')}}</th>
			                                     <th>{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('mer_lang_file').'.ADMIN_SUBTOTAL') : trans($MER_OUR_LANGUAGE.'.ADMIN_SUBTOTAL')}}
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.MER_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_STATUS') }}</th>
			                                   
			                                </tr>
		                                </thead>
		                                <tbody>
		                                @if(count($Invoice_Order) > 0)
											@php $sno_count=0; @endphp
											@foreach($Invoice_Order as $Order_sub)
												@php $sno_count++; 
												$statusToUpdate = '';
												// $calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt; @endphp
		                                        <tr>
		                                           	<td>{{$sno_count}}
													</td>
		                                            <td>{{$Order_sub->pro_item_name}}</td>
		                                            <td>{{$Order_sub->ord_quantity}}</td>
		                                           	<td>{{number_format(($Order_sub->ord_grant_total - $Order_sub->ord_tax_amt),2) }} {{$Order_sub->ord_currency}}</td>
		                                           	<td>{{$Order_sub->ord_tax_amt}} {{$Order_sub->ord_currency}}</td>
		                                            <td>{{number_format($Order_sub->ord_grant_total,2)}} {{$Order_sub->ord_currency}}</td>
		                                            
		                                            <td>
		                                                @if($Order_sub->ord_cancel_status=='1' && $Order_sub->ord_status != 3) 
															<a href="javascript:;" data-toggle="modal" data-target="#cancelReasonModal_{{$Order_sub->ord_id}}" style="color:red;font-weight:bold;"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="left" title="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCEL_REASON')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCEL_REASON') : trans($MER_OUR_LANGUAGE.'.ADMIN_CANCEL_REASON')}}!">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_USER')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCELLED_BY_USER') : trans($MER_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_USER')}}!</i></a> 
															<!--VIEW CANCEL REASON -->
															<div id="cancelReasonModal_{{$Order_sub->ord_id}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<div class="modal-content">
																		<div class="modal-header">
																			<h4 class="modal-title">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CANCEL_REASON')) ? trans(Session::get('mer_lang_file').'.ADMIN_CANCEL_REASON') : trans($MER_OUR_LANGUAGE.'.ADMIN_CANCEL_REASON')}}</h4>
																		</div>
																		<div class="modal-body">
																			<p> {!!$Order_sub->ord_cancel_reason!!}</p>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div>
															</div>
															<!-- EOF VIEW CANCEL REASON -->
														@elseif($Order_sub->ord_cancel_status=='1' && $Order_sub->ord_status == 3) 
															<a href="javascript:;" data-toggle="modal" data-target="#rejectReasonModal_{{$Order_sub->ord_id}}" style="color:red;font-weight:bold;"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="left" title="{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}!">{{(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED')}}!</i></a> 
															<!--VIEW REJECTED REASON -->
															<div id="rejectReasonModal_{{$Order_sub->ord_id}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<div class="modal-content">
																		<div class="modal-header">
																			<h4 class="modal-title">{{(Lang::has(Session::get('mer_lang_file').'.MER_REASON_TO_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REASON_TO_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REASON_TO_REJECT')}}</h4>
																		</div>
																		<div class="modal-body">
																			<p> {!!$Order_sub->ord_cancel_reason!!}</p>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div>
															</div>
															<!-- EOF VIEW REJECTED REASON -->
															{{-- delviery failed --}}
														@elseif($Order_sub->ord_status== '9')
														<a href="javascript:;" data-toggle="modal" data-target="#failReasonModal_{{$Order_sub->ord_id}}" style="color:red;font-weight:bold;"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="left" title="{{(Lang::has(Session::get('mer_lang_file').'.MER_FAILED_REASON')) ? trans(Session::get('mer_lang_file').'.MER_FAILED_REASON') : trans($MER_OUR_LANGUAGE.'.MER_FAILED_REASON')}}!">{{(Lang::has(Session::get('mer_lang_file').'.MER_FAIL')) ? trans(Session::get('mer_lang_file').'.MER_FAIL') : trans($MER_OUR_LANGUAGE.'.MER_FAIL')}}!</i></a> 
															<!--VIEW FAIL REASON -->
															<div id="failReasonModal_{{$Order_sub->ord_id}}" class="modal fade" role="dialog">
																<div class="modal-dialog">
																	<div class="modal-content">
																		<div class="modal-header">
																			<h4 class="modal-title">{{(Lang::has(Session::get('mer_lang_file').'.MER_REASON_DEL_FAIL')) ? trans(Session::get('mer_lang_file').'.MER_REASON_DEL_FAIL') : trans($MER_OUR_LANGUAGE.'.MER_REASON_DEL_FAIL')}}</h4>
												 						</div>
																		<div class="modal-body">
																			<p> {!!$Order_sub->ord_failed_reason!!}</p>
																		</div>
																		<div class="modal-footer">
																			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div>
															</div>
															<!-- EOF VIEW REJECTED REASON -->
														@else
															<!-- IF IT IS NEW ORDER JUST ACCEPT / REJECT -->
															@if($Order_sub->ord_status=='1')
																@php 
																	$acceptBtn = (Lang::has(Session::get('mer_lang_file').'.MER_ACCEPT')) ? trans(Session::get('mer_lang_file').'.MER_ACCEPT') : trans($MER_OUR_LANGUAGE.'.MER_ACCEPT');
																	$rejectBtn = (Lang::has(Session::get('mer_lang_file').'.MER_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REJECT');
																@endphp
																<div id="btnDiv_{{$Order_sub->ord_id}}">
																{!! Form::button($acceptBtn,['class' => 'btn btn-xs btn-success' ,'onclick'=>"javascript:accept_order('$Order_sub->ord_id','2');"])!!}
																{!! Form::button($rejectBtn,['class' => 'btn btn-xs btn-danger' ,'onclick'=>"javascript:reject_order('$Order_sub->ord_id','3');"])!!}
																</div>
																<img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="width:23%;display:none" id="loader_{{$Order_sub->ord_id}}" />
															<!-- IF ORDER ACCEPTED,CHECK ORDER IS SELF PICK UP OR PREPARE TO DELIVER -->
															@elseif($Order_sub->ord_status=='2')
															<!-- IF ORDER IS SELF PICKUP -->
															@if($Order_sub->ord_self_pickup=='1')
																@php 
																	$changeStatusArray = array(''=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT'),'1'=>(Lang::has(Session::get('mer_lang_file').'.MER_ORDER_PLACED')) ? trans(Session::get('mer_lang_file').'.MER_ORDER_PLACED') : trans($MER_OUR_LANGUAGE.'.MER_ORDER_PLACED'),'8'=>(Lang::has(Session::get('mer_lang_file').'.MER_DELIVERED')) ? trans(Session::get('mer_lang_file').'.MER_DELIVERED') : trans($MER_OUR_LANGUAGE.'.MER_DELIVERED'));

																	$checkArray = array(1,3,8);
																	if(in_array($Order_sub->ord_status,$checkArray))
																	{	
																	$statusToUpdate = $Order_sub->ord_status;
																	}
																	else{
																	$statusToUpdate = '';
																	}
																@endphp
															@else
																@php
																	$checkArray = array(1,3,4);
																	if(in_array($Order_sub->ord_status,$checkArray))
																	{	
																		$statusToUpdate = $Order_sub->ord_status;
																	}
																	else {
																		$statusToUpdate = '';
																	}
																@endphp

																@php 
																	$changeStatusArray = array(''=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT'),'1'=>(Lang::has(Session::get('mer_lang_file').'.MER_ORDER_PLACED')) ? trans(Session::get('mer_lang_file').'.MER_ORDER_PLACED') : trans($MER_OUR_LANGUAGE.'.MER_ORDER_PLACED'),'3'=>(Lang::has(Session::get('mer_lang_file').'.MER_REJECTED')) ? trans(Session::get('mer_lang_file').'.MER_REJECTED') : trans($MER_OUR_LANGUAGE.'.MER_REJECTED'),'4'=>(Lang::has(Session::get('mer_lang_file').'.MER_PREPARE_DELIVER')) ? trans(Session::get('mer_lang_file').'.MER_PREPARE_DELIVER') : trans($MER_OUR_LANGUAGE.'.MER_PREPARE_DELIVER'));
																@endphp
															@endif
		<!-- {{ Form::select('change_status',$changeStatusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'change_status','onchange'=>'change_status(this.value,\''.$Order_sub->ord_id.'\')'] ) }} -->

		<!-- {{ Form::select('change_status',$changeStatusArray,$statusToUpdate,['class' => 'form-control' , 'style' => 'width:100%','id'=>'change_status','onchange'=>'change_status(this.value,\''.$Order_sub->ord_id.'\')'] ) }} -->

				<select class="form-control" style="width:100%" id="confuse_{{$Order_sub->ord_id}}" onchange="change_status(this.value,'{{$Order_sub->ord_id}}')" name="change_status">
						@if(count($changeStatusArray) > 0 )
							@foreach($changeStatusArray as $key=>$val)
								<option value="{{$key}}">{{$val}}</option>
							@endforeach
						@endif
				</select>								
				<!-- IF ORDER ACCEPTED THEN SHOW STATUS -->

				<script>document.getElementById('confuse_{{$Order_sub->ord_id}}').value='{{$statusToUpdate}}';</script>
			@elseif($Order_sub->ord_status >= '4')
				@php $order_status = order_status_array('mer_lang_file',$MER_OUR_LANGUAGE); @endphp
				<a href="javascript:;">{{$order_status[$Order_sub->ord_status]}}</a>
					@endif
			@endif	   
		        </td>
 </tr> 
											@endforeach
		                                @endif
		                                </tbody>
		                            </table>
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
<!-- Modal -->
	<div class="modal fade" id="orderRejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				
				<div class="modal-body" id="myBody">
					<input type="hidden" id="modal_order_id" value=''>
					{!! Form::textarea('modal_reject_rsn','',array('placeholder'=> (Lang::has(Session::get('mer_lang_file').'.MER_REASON_TO_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REASON_TO_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REASON_TO_REJECT'),'class'=>'form-control','id'=>'modal_reject_rsn')) !!}
					
				</div>
				<div class="modal-footer">
					@php 
						$rejectBtn = (Lang::has(Session::get('mer_lang_file').'.MER_REJECT')) ? trans(Session::get('mer_lang_file').'.MER_REJECT') : trans($MER_OUR_LANGUAGE.'.MER_REJECT');
						$closeBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_CLOSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_CLOSE');
					@endphp
					<img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="width:23%;display:none" id="loader_reject" />
					{!! Form::button($rejectBtn,['class' => 'btn btn-xs btn-danger' ,'onclick'=>"javascript:reject_order_submit();",'id'=>'rejectSubmitBtn'])!!}
					{!! Form::button($closeBtn,['class' => 'btn btn-xs btn-info' ,'data-dismiss'=>"modal"])!!}
				</div>
			</div>
		</div>
	</div>
<!-- /.panel-body -->
@section('script')
<script>
	$(document).ready(function () {
		$('#dataTables-example').dataTable({
			"bPaginate": false,
			"scrollX": true,
			"bLengthChange": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false 
		});
		$("#checkAll").click(function(){
			$('.myCheckboxes').not(this).prop('checked', this.checked);
		});
		
		$(".myCheckboxes").change(function(){
			$('#checkAll').prop('checked', false);
		});
		
		$('[data-toggle="tooltip"]').tooltip();
	});
	function accept_order(order_id,status)
	{
		$.ajax({
			type: 'post',
			data: {id:order_id,status:status},
			url: '{{url("merchant-change-status/")}}',
			beforeSend: function() {
				$('#btnDiv_'+order_id).hide();
				$('#loader_'+order_id).show();
			},
			success: function(responseText){
				window.location.reload();
			}		
		});	
	}
	function reject_order(order_id)
	{
		if(confirm('Are you want to reject?'))
		{
			$('#modal_order_id').val(order_id);
			$('#orderRejectModal').modal();
		}
	}
	function reject_order_submit()
	{
		var reason = $('#modal_reject_rsn').val();
		var orderId = $('#modal_order_id').val();
		if(reason!=''){
			$.ajax({
				type: 'post',
				data: {reason:reason,orderId:orderId},
				url: '{{url("merchant-reject-status")}}',
				beforeSend: function() {
					$('#btnDiv_'+orderId).hide();
					$('#loader_'+orderId).show();
					$('#loader_reject').show();
					$('#rejectSubmitBtn').hide();
				},
				success: function(responseText){ console.log(responseText);
					window.location.reload();
				}		
			});
		}
		else{
			alert("{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_REASON')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_REASON') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_REASON')}}");
			return false;
		}
	}
	function change_status(status,order_id)
	{
		if(status=='1' || status=='4' || status=='8')
		{
			@if(empty($ord_status_chart)===false)
				@if($ord_status_chart->ord_pre_order_date === NULL)
					accept_order(order_id,status);
				@else
					@php 
						$now = time();
						$your_date = strtotime($ord_status_chart->ord_pre_order_date);
						$datediff = $your_date-$now;
						$differenceDate = round($datediff / (60 * 60 * 24));
						$deliveredDate = date("m/d/Y H:i:s",strtotime($ord_status_chart->ord_pre_order_date));
					@endphp
					var diffDate = '{{$differenceDate}}';
					var deliveredDateIs = '{{$deliveredDate}}';
					if(diffDate > 0){
						if(confirm('This order deliver date is on '+deliveredDateIs+'. Are you sure want to continue? ')){
							accept_order(order_id,status);
						}else{
							$('#confuse_'+order_id).val('');
							return false;
						}
					}
					else{
						accept_order(order_id,status);
					}
				@endif
			@endif
			
		}
		else
		{
			reject_order(order_id);
		}
	}
	$('.step').each(function(index, el) {
		$(el).not('.active').addClass('done');
		$('.done').html('<i class="icon-valid"></i>');
		if($(this).is('.active')) {
			$(this).parent().addClass('pulse')
			return false;
		}
	});
</script>
@endsection
@stop