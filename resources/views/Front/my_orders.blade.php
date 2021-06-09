@extends('Front.layouts.default')
@section('content')
<!--<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">-->
<link rel="stylesheet" href="{{url('')}}/public/front/css/fxss-rate.css">
<style type="text/css">
	
	/************************/
	.rating {
    float:left;
	}
	
	/* :not(:checked) is a filter, so that browsers that don’t support :checked don’t 
	follow these rules. Every browser that supports :checked also supports :not(), so
	it doesn’t make the test unnecessarily selective */
	.rating:not(:checked) > input {
    position:absolute;
    top:-9999px;
    clip:rect(0,0,0,0);
	}
	
	.rating:not(:checked) > label {
    float:right;
    width:1em;
    padding:0 .1em;
    overflow:hidden;
    white-space:nowrap;
    cursor:pointer;
    font-size:200%;
    line-height:1.2;
    color:#ddd;
    text-shadow:1px 1px #bbb, 2px 2px #666, .1em .1em .2em rgba(0,0,0,.5);
	}
	
	.rating:not(:checked) > label:before {
    content: '★ ';
	}
	
	.rating > input:checked ~ label {
    color: #f70;
    text-shadow:1px 1px #c60, 2px 2px #940, .1em .1em .2em rgba(0,0,0,.5);
	}
	
	.rating:not(:checked) > label:hover,
	.rating:not(:checked) > label:hover ~ label {
    color: gold;
    text-shadow:1px 1px goldenrod, 2px 2px #B57340, .1em .1em .2em rgba(0,0,0,.5);
	}
	
	.rating > input:checked + label:hover,
	.rating > input:checked + label:hover ~ label,
	.rating > input:checked ~ label:hover,
	.rating > input:checked ~ label:hover ~ label,
	.rating > label:hover ~ input:checked ~ label {
    color: #ea0;
    text-shadow:1px 1px goldenrod, 2px 2px #B57340, .1em .1em .2em rgba(0,0,0,.5);
	}
	
	.rating > label:active {
    position:relative;
    top:2px;
    left:2px;
	}
	/**********************/
	
	.ratings-new {
    position: relative;
    vertical-align: middle;
    display: inline-block;
    color: #b1b1b1;
    overflow: hidden;
	}
	.full-stars {
    position: absolute;
    left: 0;
    top: 0;
    white-space: nowrap;
    overflow: hidden;
    color: #fde16d;
	}
	.empty-stars:before, .full-stars:before {
    content:"\2605\2605\2605\2605\2605";
    font-size: 14pt;
	}
	.empty-stars:before {
    -webkit-text-stroke: 1px #848484;
	}
	.full-stars:before {
    -webkit-text-stroke: 1px orange;
	}
	/* Webkit-text-stroke is not supported on firefox or IE */
	
	/* Firefox */
	@-moz-document url-prefix() {
    .full-stars {
	color: #ECBE24;
    }
	}
	label{
		font-size: 15px;
		font-weight: 800;
	}
	
</style> 


		<div class="profile-sidebar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 profile-sidebar-sec">
						<!-- Sidebar -->
						@include('Front.includes.profile_sidebar')
						<!-- Sidebar -->
					</div>
				</div>
			</div>
		</div>


<div class="section9-inner">
	<div class="container userContainer">
		<div class="row"> 
			<!--<div class="col-lg-12">
						<h5 class="sidebar-head">             
								@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')              
						</h5>
			</div>	-->
			<div class="userContainer-bg row">
			
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div"> 
				<div class="row panel-heading">
					<div class="my-order-form col-12">
                        {!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => url('my-orders'),'id'=>'validate_form']) !!}
                        <div class="form-group row">
							

							<label for="orderType" class="col-lg-2 col-md-3 col-xs-2 col-sm-3 col-form-label">@lang(Session::get('front_lang_file').'.FRONT_ORDER_NUMBER') :</label>

							{!! Form::text('order_number','',['class'=>'form-control col-lg-5 col-md-3 col-xs-2 col-sm-3 border-0 p-0','id' => 'cus_name','required','maxlength'=>50]) !!}
							@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.FRONT_SEARCH')) ? trans(Session::get('front_lang_file').'.FRONT_SEARCH') : trans($FRONT_LANGUAGE.'.FRONT_SEARCH') 
							
							@endphp
							
							{!! Form::submit($saveBtn,['class' => 'btn btn-success col-lg-2 ord-search'])!!}
							
							<a href="{{url('my-orders')}}" class="col-lg-2 ord-reset"><button type="button" name="reset" class="btn btn-block btn-info"> {{(Lang::has(Session::get('front_lang_file').'.FRONT_RESET')) ? trans(Session::get('front_lang_file').'.FRONT_RESET') : trans($FRONT_LANGUAGE.'.FRONT_RESET')}} </button></a>
						</div>
                        {!! Form::close() !!}
					</div>
					<div class="my-order-table col-12">
                        <div class="table-responsive form-group">
							<table class="table table-hover ">
								<thead>
									<tr>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SNO')) ? trans(Session::get('front_lang_file').'.ADMIN_SNO') : trans($FRONT_LANGUAGE.'.ADMIN_SNO')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_NUMBER')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_NUMBER') : trans($FRONT_LANGUAGE.'.FRONT_ORDER_NUMBER')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_DATE') : trans($FRONT_LANGUAGE.'.ADMIN_ORDER_DATE')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_TOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_TOTAL')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.FRONT_ACTION')) ? trans(Session::get('front_lang_file').'.FRONT_ACTION') : trans($FRONT_LANGUAGE.'.FRONT_ACTION')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($orderdetails) > 0)
										@php $i = ($orderdetails->currentpage()-1)*$orderdetails->perpage()+1; @endphp
										@foreach($orderdetails as $details)
											<?php

											if($details->ord_self_pickup==0){
                                                $total=($details->revenue+$details->ord_delivery_fee)-$details->ord_wallet;
											}
											else{
                                                $total=($details->revenue-$details->ord_wallet);
											}
											?>

											<tr>
												<td>{{$i}}</td>
												<td>{{ $details->ord_transaction_id }}</td>
												<td>{{ date('m/d/Y',strtotime($details->ord_date)) }} </td>

												<td width="15%">{{$details->ord_currency.' '. number_format(($total),2) }}</td>
												<td>
													<a href="{{url('order-invoice/'.base64_encode($details->ord_transaction_id))}}" target="_blank">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_VIEW_INVOICE')) ? trans(Session::get('front_lang_file').'.ADMIN_VIEW_INVOICE') : trans($FRONT_LANGUAGE.'.ADMIN_VIEW_INVOICE')}}</a>
													@php
														$total_order_count = get_total_order_count($details->ord_transaction_id);
														$get_total_cannotcancelled_count = get_total_cannotcancelled_count($details->ord_transaction_id);
														$get_statuses=get_status_count_byTransIdOnly($details->ord_transaction_id);
													@endphp
													@if(empty($get_statuses)==false)
														@php

														$getTotalCancelled = $get_statuses->rejected+$get_statuses->cancelled;
														$totalFailed = $get_statuses->failed;

														//echo $get_statuses->totals.'/'.$get_statuses->cancelled.'/'.$get_statuses->rejected;
														@endphp
														@if($get_statuses->totals == $get_statuses->cancelled)
															<a href="{{url('order-details/'.base64_encode($details->ord_transaction_id))}}">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($FRONT_LANGUAGE.'.FRONT_CANCELLED')}}</a>
														@elseif($get_statuses->totals == $get_statuses->rejected)
															<a href="{{url('order-details/'.base64_encode($details->ord_transaction_id))}}">{{(Lang::has(Session::get('front_lang_file').'.MER_REJECTED')) ? trans(Session::get('front_lang_file').'.MER_REJECTED') : trans($FRONT_LANGUAGE.'.MER_REJECTED')}}</a>
														@elseif($get_statuses->totals == $getTotalCancelled)	
															<a href="{{url('order-details/'.base64_encode($details->ord_transaction_id))}}">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($FRONT_LANGUAGE.'.FRONT_CANCELLED')}}/{{(Lang::has(Session::get('front_lang_file').'.MER_REJECTED')) ? trans(Session::get('front_lang_file').'.MER_REJECTED') : trans($FRONT_LANGUAGE.'.MER_REJECTED')}}</a>
														@elseif($get_statuses->totals == $totalFailed)
															<a href="{{url('track-order/'.base64_encode($details->ord_transaction_id))}}" style="color: #dc3545;border-bottom: 1px solid #dc3545" target="new">{{(Lang::has(Session::get('front_lang_file').'.FRONT_FAIL')) ? trans(Session::get('front_lang_file').'.FRONT_FAIL') : trans($FRONT_LANGUAGE.'.FRONT_FAIL')}}</a>

														@else
															<a href="{{url('order-details/'.base64_encode($details->ord_transaction_id))}}">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL')}}</a>
														@endif
													@else
														<a href="{{url('order-details/'.base64_encode($details->ord_transaction_id))}}">{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($FRONT_LANGUAGE.'.FRONT_CANCELLED')}} / {{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_CANCEL')}}</a>
													@endif
													
													{{--@endif--}}
													<a href="{{url('track-order/'.base64_encode($details->ord_transaction_id))}}" target="_blank" class="table-track-link">{{(Lang::has(Session::get('front_lang_file').'.FRONT_TRACK_ORDER')) ? trans(Session::get('front_lang_file').'.FRONT_TRACK_ORDER') : trans($FRONT_LANGUAGE.'.FRONT_TRACK_ORDER')}}</a>
													<?php /* 
													<a onclick="return confirm('<?php echo(Lang::has(Session::get('front_lang_file').'.FRONT_REORDER_CONFIRM')) ? trans(Session::get('front_lang_file').'.FRONT_REORDER_CONFIRM') : trans($FRONT_LANGUAGE.'.FRONT_REORDER_CONFIRM'); ?>')" href="{{url('reorder/'.base64_encode($details->ord_transaction_id))}}">@lang(Session::get('front_lang_file').'.FRONT_REORDER')</a> */ ?>
													<a href="javascript:reorderFun('<?php echo base64_encode($details->ord_transaction_id);?>','<?php echo $details->ord_transaction_id;?>');">
														<span id="reOrderText_<?php echo $details->ord_transaction_id;?>">@lang(Session::get('front_lang_file').'.FRONT_REORDER')</span>
														<span id="loading-image_<?php echo $details->ord_transaction_id;?>" style="display:none">
															<i class="fa fa-spinner fa-spin"></i> Loading...
														</span>
													</a> 
													
													<?php /* 
													@if($details->ord_status == '8')
														@php $review_details = DB::table('gr_review')->where('order_id','=',$details->ord_id)->first(); @endphp
														@if(empty($review_details) === true)
															@if($details->ord_self_pickup==1)
															@else 
																{{--CHECK ONLY DELIVERY MANAGER ENABLE/DISABLE THE CUSTOEMR RATING --}}
																@php 
																	$del_mgr_id = $details->ord_delmgr_id; 
																	$customer_rating_status = getCustomerRateStatus($del_mgr_id);
																@endphp
																@if(empty($customer_rating_status)===false)
																	@if($customer_rating_status->dm_customer_rating==1)
																		<a href="javascript:call_my_orderReview('{{$details->ord_id}}','{{$details->ord_agent_id}}','{{$details->ord_delivery_memid}}');" >{{(Lang::has(Session::get('front_lang_file').'.FRONT_ORDER_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_ORDER_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_ORDER_REVIEW')}}</a>
																	@else
																	@endif
																@endif
															@endif
														@elseif(empty($review_details) === false)
															<a href="javascript:call_viewmy_orderReview('{{$review_details->review_comments}}','{{$review_details->review_rating}}');" >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_VIEW_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_VIEW_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_VIEW_REVIEW')}}</a>
															
														@endif
													@endif */ ?>

													{{-- show if item is cancelled --}}
													@if(empty($get_statuses)==false)
														@php $getTotalCancelled = $get_statuses->rejected+$get_statuses->cancelled; @endphp
													@if($getTotalCancelled > 0)
															<a href="{{url('view-refund/'.base64_encode($details->ord_transaction_id))}}" >@lang(Session::get('front_lang_file').'.FRONT_VI_REFUND')</a>
														@endif
													@endif
												</td>
											</tr>
											
											
											@php $i++; @endphp
										@endforeach
										@else
											<tr>
												<td></td>
												<td></td>
												<td>{{(Lang::has(Session::get('front_lang_file').'.NO_DATA_FOUND')) ? trans(Session::get('front_lang_file').'.NO_DATA_FOUND') : trans($FRONT_LANGUAGE.'.NO_DATA_FOUND')}}</td>
												<td></td>
												<td></td>
											</tr>
										@endif
									
								</tbody>
							</table>
						</div>
						
                        <div class="form-group">
							@if(count($orderdetails) > 0)
                            {{ $orderdetails->render() }}
							@endif
							<!-- <div class="paginationDiv">
								<nav aria-label="Page navigation example">
								<ul class="pagination">
                                <li class="page-item">
								<a class="page-link" href="#" aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
								<span class="sr-only">Previous</span>
								</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
								<a class="page-link" href="#" aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
								<span class="sr-only">Next</span>
								</a>
                                </li>
								</ul>
								</nav>
							</div> -->
						</div>
					</div>
				</div>
			</div> 
			</div>
		</div>
	</div>
</div>          

<!--Order Review Modal -->
<div class="modal fade" id="order_review_moal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'order_review_submit','enctype'=>'multipart/form-data','id'=>'myForm']) !!}
				<div class="form-group"> 
					
					<input name='order_id' type='hidden' id="order_id" value=''>
					<input type="hidden" name="agent_id" id="agent_id" value="">
					<input type="hidden" name="delivery_id" id="delivery_id" value="">					    
				<label>@lang(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')</label>
				<input type="text" name="rev_comments" class="form-control" placeholder="">
				<label>@lang(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')</label> <br> 					
				<div class="rateBox"></div> 
					<div class="rat_val_area"></div>                   
					<br>
					
				</div>
				@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
				{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
				<button type="button" class="btn btn-default" data-dismiss="modal">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('front_lang_file').'.ADMIN_CLOSE') : trans($FRONT_LANGUAGE.'.ADMIN_CLOSE')}}</button>
				{!! Form::close() !!}
				
			</div>
			<div class="modal-footer">
				
			</div>
		</div>
	</div>
</div>

<button type="button" class="btn btn-info btn-lg" id="myBtn" data-toggle="modal" data-target="#order_review_moal" style="display: none;"></button>

<!-- VIEW ORDER REVIEW-->

<div class="modal fade" id="order_MYreview_moal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				 <h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_YOUR_REVIEW')}}</h4>
			</div>
			<div class="modal-body">
				{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
					<div class="form-group"> 
						<div class="row panel-heading">
							<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
								<div class="form-group">
									<span class="panel-title" >
										<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}: </label>
									</span>
								</div>
							</div>
							<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
								<span id="comments_span"></span>
							</div>
							<!-- </div>

							<div class="row panel-heading"> -->
							<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
								<div class="form-group">
									<span class="panel-title">
										<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_RATING_REVIEW')}}:</label>
									</span>
								</div>
							</div>
							<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
								<span id="rating_span"></span>
							</div>
						</div>
					</div>
				{!!Form::close()!!}
			</div>
			<div class="modal-footer">
				
			</div>
		</div>
	</div>
</div>

<button type="button" class="btn btn-info btn-lg" id="myReviewBtn" data-toggle="modal" data-target="#order_MYreview_moal" style="display: none;"></button>




@section('script')
<!--  -->
<script>
	function call_my_orderReview(ord_id,agent_id,delivery_id)
	{
		$('#order_id').val(ord_id);
		$('#agent_id').val(agent_id);
		$('#delivery_id').val(delivery_id);
		$('#myBtn').trigger('click');
	}
	function call_viewmy_orderReview(comments,rating)
	{
		$('#comments_span').html(comments);
		var stars = '';
		for($i=1;$i<=rating;$i++)
		{
			stars +='<i class="fa fa-star" style="color: orange;"></i>';
		}
		$('#rating_span').html(stars);
		$('#myReviewBtn').trigger('click');
	}
</script>
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>-->
<script src="{{url('')}}/public/front/js/fxss-rate-iconfont.js"></script>
<script src="{{url('')}}/public/front/js/fxss-rate.js"></script>
<script>
	$(".rateBox").rate({
		length: 5,
		value: 0,
		readonly: false,
		size: '25px',
		selectClass: 'fxss_rate_select',
		incompleteClass: 'fxss_rate_no_all_select',
		customClass: 'custom_class',
		callback: function(object){
			var rat_val = object['index']+1;
			$('.rat_val_area').html('<input type="hidden" name="rat_val" value="'+rat_val+'">');
		}
	});
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	});	
	function reorderFun(transaction_id,trans_id){
		if(confirm('<?php echo(Lang::has(Session::get('front_lang_file').'.FRONT_REORDER_CONFIRM')) ? trans(Session::get('front_lang_file').'.FRONT_REORDER_CONFIRM') : trans($FRONT_LANGUAGE.'.FRONT_REORDER_CONFIRM'); ?>')){
			/*AJAX FUNCTION*/
			$.ajax({

                type:'get',
                url :"<?php echo url("reorder"); ?>/"+transaction_id,
				async:false,
				datatype: "html",
                beforeSend: function() {
                    $("#loading-image_"+trans_id).show();
                    $("#reOrderText_"+trans_id).hide();
                },
                data:{},
                success:function(response){
					var splitResponse = response.split('`');
					if(splitResponse[0]=='-1'){
						window.location.href='<?php echo url('cart');?>';
					}
					else{
						alert(splitResponse[1].replace(/\\n/g,"\n"));
						if(splitResponse[0] > 0){
							window.location.href='<?php echo url('cart');?>';
						}else{
							$("#loading-image_"+trans_id).hide();
							$("#reOrderText_"+trans_id).show();
						}
					}
                }
            });
			/* EOF AJAX FUNCTION */
		}else{
			return false;
		}
	}
</script>
@endsection
@stop

