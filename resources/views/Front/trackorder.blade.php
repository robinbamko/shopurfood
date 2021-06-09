@extends('Front.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style type="text/css">
	
	.order-restaurant button{background:transparent;}	
	.order-restaurant button a{color:#282828;}
	
	.order-box{
		color: #31708f;
		background-color: #d9edf7;
		border-color: #bce8f1;
		padding: 15px;
		margin-bottom: 20px;
		border: 1px solid transparent;
		border-top-color: transparent;
		border-right-color: transparent;
		border-bottom-color: transparent;
		border-left-color: transparent;
		border-radius: 4px;
		width: 100%;
	}
	
	.new-react-version {
		padding: 20px 20px;
		border: 1px solid #eee;
		border-radius: 20px;
		box-shadow: 0 2px 12px 0 rgba(0,0,0,0.1);
		text-align: center;
		font-size: 14px;
		line-height: 1.7;
	}
	
	.new-react-version .react-svg-logo {
		text-align: center;
		max-width: 60px;
		margin: 20px auto;
		margin-top: 0;
	}
	
	
	
	
</style>
<!-- MAIN -->
<div class="main-sec">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="">
						
						<div class="location panel">
							
							{{-- Display error message--}}
							
						</div>
						
						
						<div class="col-md-12 order-track">
							
							<div class="order-track-date">							
								<p><span>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_DATE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ORDER_DATE')}} :</span> @if($customer_details->ord_pre_order_date!=NULL) {{date('m/d/y H:i:s',strtotime($customer_details->ord_pre_order_date))}} @ELSE {{date('m/d/y H:i:s',strtotime($customer_details->ord_date))}} @endif</p>
								<p><span>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_ID') : trans($this->FRONT_LANGUAGE.'.ADMIN_ORDER_ID')}} :</span> {{$customer_details->ord_transaction_id}}</p>
							</div>
							<div class="order-track-sec1">
								<h4>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_CUSTOMER_DETAILS')}}</h4>
								<div class="order-track-sec1-inner">
									<p>{{$customer_details->ord_shipping_cus_name}}</p>
									<p>{{$customer_details->ord_shipping_mobile}}</p>
									<p>{{$customer_details->ord_shipping_mobile1}}</p>
									<p>{{$customer_details->order_ship_mail}}</p>
									<p>{{$customer_details->ord_shipping_address1}}</p>
									<p>{{$customer_details->ord_shipping_address}}</p>
									
								</div>
							</div>
							@if(count($storewise_details) > 0 )
							
							@foreach($storewise_details as $key=>$commonDet)
							
							@if(count($commonDet['general_detail']) > 0 )
							@php $chk_ord_status = $chk_cancel_status = array(); @endphp
							@php $popup_count = 1; @endphp 
							@foreach($commonDet['general_detail'] as $comDet)
							<div class="order-rest-div">
								<div class="order-track-sec2">
									<div class="order-restaurant">
										<h3>{{$key}} <br> {{$comDet['st_address']}}</h3>
										
										<div class="order-restaurant-title">									
											<div class="order-restaurant-logo">
												@php 
												$path = url('').'/public/images/noimage/'.$no_shop_logo; 
												@endphp
												
												@if($comDet['st_type'] == 1)
													@php $filename = public_path('images/restaurant/').$comDet['st_logo']; @endphp
													@if(file_exists($filename) && $comDet['st_logo'] != '')
														@php $path = url('').'/public/images/restaurant/'.$comDet[	'st_logo'];@endphp
													@endif
													@elseif($comDet['st_type'] == 2)
														@php $filename = public_path('images/store/').$comDet['st_logo']; @endphp
														@if(file_exists($filename) && $comDet['st_logo'] != '')
															@php $path = url('').'/public/images/store/'.$comDet['st_logo'];@endphp
														@endif
													@endif
												<img src="{{$path}}" alt="{{$key}}">	
											</div>
											<div class="order-restaurant-content">
												
												@foreach($commonDet['st_type'] as $typeval)  @endforeach
												@foreach($commonDet['st_id'] as $idval)  @endforeach
												@if($typeval == 1 && $comDet['reviewed'] == 'No' && $comDet['ord_status']=='8')
													<a  href="#" data-toggle="modal" data-target="#restaurant_review" onclick="fetch_id('','{{$comDet['mer_id']}}','{{ $idval }}');">@lang(Session::get('front_lang_file').'.FRONT_REST_REVIEW')</a>
												@elseif($typeval == 1 && $comDet['reviewed'] == 'Yes')	
												<!-- View Restaurant Review Popup -->
													<a  href="#" data-toggle="modal" data-target="#view_restaurant_review_{{$comDet['reviewCommentId']}}">@lang(Session::get('front_lang_file').'.FRONT_VI_REST_REVIEW')</a>
												@elseif(($typeval == 2 && $comDet['reviewed'] == 'No'))
													<button type="button"><a  href="#" data-toggle="modal" data-target="#store_review" onclick="fetch_id('','{{$comDet['mer_id']}}','{{ $idval }}');">@lang(Session::get('front_lang_file').'.FRONT_ST_REVIEW')</a></button>
												@elseif($typeval == 2 && $comDet['reviewed'] == 'Yes')
												<!-- View Store Review Popup -->
													<button type="button" class="view-rest-view"><a  href="#" data-toggle="modal" data-target="#view_store_review_{{$popup_count}}">@lang(Session::get('front_lang_file').'.FRONT_VI_ST_REV')</a></button>
												@endif
												<!-- ORDER REVIEW -->
												{{-- delivery manager disable write review for delviery person  --}}
												@if($comDet['cusRatingStatus'] == '0')
												@elseif($comDet['orderReviewed'] == 'No' && $comDet['ord_delivery_memid']!='' && $comDet['ord_self_pickup']=='0')
													<br><a  href="#" data-toggle="modal" data-target="#order_review" onclick="getOrderPopup('{{$comDet['ord_transaction_id']}}','{{$comDet['ord_agent_id']}}','{{ $comDet['ord_delivery_memid'] }}','{{ $idval }}');">@lang(Session::get('front_lang_file').'.FRONT_ORDER_REVIEW')</a>
												@elseif($comDet['orderReviewed'] == 'Yes' && $comDet['ord_delivery_memid']!='' && $comDet['ord_self_pickup']=='0')	
													<br><a  href="#" data-toggle="modal" data-target="#view_order_review_{{$comDet['orderCommentId']}}">@lang(Session::get('front_lang_file').'.FRONT_VIEW_ORDER_REVIEW')</a>
												<!-- View Restaurant Review Popup -->
											
												<div id="view_order_review_{{$comDet['orderCommentId']}}" class="modal fade view_restaurant_review" role="dialog">
													<div class="modal-dialog">
														
														<!-- Modal content-->
														<div class="modal-content">
															<div class="modal-header">													
																<button type="button" class="close" data-dismiss="modal">&times;</button>
																<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_YOUR_REVIEW')}}</h4>				 
															</div>
															<div class="modal-body">
																
																{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
																<div class="form-group"> 
																	<div class="row panel-heading">
																		<div style="width:100%; margin:0 0 20px;">
																			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																				<div class="form-group">
																					<span class="panel-title" >
																						<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}: </label>
																					</span>
																				</div>
																			</div>
																			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																				<span id="comments_span">{{ $comDet['orderComments'] }}</span>
																			</div>
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
																			<div class="ratings-new">
																				<div class="empty-stars"></div>
																				<div class="full-stars" style="width:{{ $comDet['orderRatings'] * 20 }}%"></div>
																			</div>
																		</div>
																	</div>
																</div>
																{!!Form::close()!!}
															</div>
															
														</div>
														
													</div>
												</div>
												<!-- End View Restaurant Review Popup -->
												@else
												<!--STAY WITHOUT VIEW / ADD BUTTON -->
												@endif
											</div>
										</div>
										
										<!-- Store/Restaurant Review Part -->
										@foreach($commonDet['st_type'] as $typeval)  @endforeach
										@foreach($commonDet['st_id'] as $idval)  @endforeach
										
										@if(($typeval == 1 && $comDet['reviewed'] == 'No'))
										<!-- <a  href="#" data-toggle="modal" data-target="#restaurant_review" onclick="fetch_id('','{{$comDet['mer_id']}}','{{ $idval }}');">Restaurant Review</a> -->
										@elseif($typeval == 1 && $comDet['reviewed'] == 'Yes')	
										<!-- View Restaurant Review Popup -->
										<!-- <button type="button" class="view-rest-view"><a  href="#" data-toggle="modal" data-target="#view_restaurant_review_{{$popup_count}}">View Restaurant Review</a></button> -->
										<div id="view_restaurant_review_{{$comDet['reviewCommentId']}}" class="modal fade view_restaurant_review" role="dialog">
											<div class="modal-dialog">
												
												<!-- Modal content-->
												<div class="modal-content">
													<div class="modal-header">													
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_YOUR_REVIEW')}}</h4>				 
													</div>
													<div class="modal-body">
														
														{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
														<div class="form-group"> 
															<div class="row panel-heading">
																<div style="width:100%; margin:0 0 20px;">
																	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																		<div class="form-group">
																			<span class="panel-title" >
																				<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}: </label>
																			</span>
																		</div>
																	</div>
																	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																		<span id="comments_span">{{ $comDet['reviewComments'] }}</span>
																	</div>
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
																	<div class="ratings-new">
																		<div class="empty-stars"></div>
																		<div class="full-stars" style="width:{{ $comDet['reviewRatings'] * 20 }}%"></div>
																	</div>
																</div>
															</div>
														</div>
														{!!Form::close()!!}
													</div>
													
												</div>
												
											</div>
										</div>
										<!-- End View Restaurant Review Popup -->
										@elseif(($typeval == 2 && $comDet['reviewed'] == 'No'))
										<!-- <button type="button"><a  href="#" data-toggle="modal" data-target="#store_review" onclick="fetch_id('','{{$comDet['mer_id']}}','{{ $idval }}');">Store Review</a></button> -->
										@elseif($typeval == 2 && $comDet['reviewed'] == 'Yes')
										<!-- View Store Review Popup -->
										<!-- <button type="button" class="view-rest-view"><a  href="#" data-toggle="modal" data-target="#view_store_review_{{$popup_count}}">View Store Review</a></button> -->
										<div id="view_store_review_{{$popup_count}}" class="modal fade view_restaurant_review" role="dialog">
											<div class="modal-dialog">
												
												<!-- Modal content-->
												<div class="modal-content">
													<div class="modal-header">													
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_YOUR_REVIEW')}}</h4>
													</div>
													<div class="modal-body">
														{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
														<div class="form-group"> 
															<div class="row panel-heading">
																<div style="width:100%; margin:0 0 20px;">
																	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																		<div class="form-group">
																			<span class="panel-title" >
																				<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}: </label>
																			</span>
																		</div>
																	</div>
																	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																		<span id="comments_span">{{ $comDet['reviewComments'] }}</span>
																	</div>
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
																	<div class="ratings-new">
																		<div class="empty-stars"></div>
																		<div class="full-stars" style="width:{{ $comDet['reviewRatings'] * 20 }}%"></div>
																	</div>
																</div>
															</div>
														</div>
														{!!Form::close()!!}
													</div>
													
												</div>
												
											</div>
										</div>													
										<!-- End View Store Review Popup -->
										@endif
										<!-- End Store/Restaurant Review Part -->
										
									</div>
									@if($AGENTMODULE==1)
									<div class="order-agent1">
										
										<h4>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_AGENT_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_AGENT_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_AGENT_DETAILS')}}</h4>
										@if($comDet['ord_task_status']==0)
										@if($comDet['ord_self_pickup']=='1')
										<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('front_lang_file').'.ADMIN_SELF_PICKUP') : trans($FRONT_LANGUAGE.'.ADMIN_SELF_PICKUP')}}</p>
										@else
										<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
										@endif
										{{--@else--}}
										@elseif($comDet['ord_task_status'] == 1 && $comDet['ord_agent_acpt_status'] == 1)
										<p>{{ucfirst($comDet['agent_fname']).' '.$comDet['agent_lname']}}</p>
										<p>{{$comDet['agent_phone1']}}</p>
										<p>{{$comDet['agent_email']}}</p>
										@else
										<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
										@endif
									</div>
									@endif
									<div class="order-agent2">
										<h4>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_DELBOY_DETAILS')) ? trans(Session::get('front_lang_file').'.ADMIN_DELBOY_DETAILS') : trans($this->FRONT_LANGUAGE.'.ADMIN_DELBOY_DETAILS')}}</h4>
										@if($comDet['ord_task_status']==0)
										@if($comDet['ord_self_pickup']=='1')
										<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('front_lang_file').'.ADMIN_SELF_PICKUP') : trans($FRONT_LANGUAGE.'.ADMIN_SELF_PICKUP')}}</p>
										@else
										<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
										@endif
										{{--@else--}}
										@elseif($comDet['ord_task_status'] == 1 && $comDet['ord_delboy_act_status'] == 1)
										<p>{{ucfirst($comDet['deliver_fname']).' '.$comDet['deliver_lname']}}</p>
										<p>{{$comDet['deliver_phone1']}}</p>
										<p>{{$comDet['deliver_email']}} </p>
										@else
										<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('front_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->FRONT_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
										@endif
									</div>
								</div>
								
								<div id="track-steps">
									<h4>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_ORDER_STATUS')}}</h4>
									
									@foreach($commonDet['delivery_detail'] as $comDet1)
									@foreach($comDet1 as $commDet)
									@php 
									array_push($chk_ord_status,$commDet->ord_status);	
									array_push($chk_cancel_status,$commDet->ord_cancel_status);
									@endphp
									@if($commDet->ord_cancel_status != 1)
									@php $ord_id  = $commDet->ord_id; 
									$show_ord_status = $commDet->ord_status;
									@endphp
									@endif
									@endforeach
									@endforeach
									
									<ul id="ul_{{$comDet['ord_id']}}">
										{{-- All items are cancelled --}}
										@if(!in_array('0',$chk_cancel_status))
										{{-- <li style="float:left;margin-left:20px;">
											<div class="step active " data-desc="{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED')}}" ><i class="fa fa-times"></i></div>
										</li> --}}
										<li>
											<div class="step done" data-desc="{{(Lang::has(Session::get('front_lang_file').'.MER_NEW_ORDER')) ? trans(Session::get('front_lang_file').'.MER_NEW_ORDER') : trans($this->FRONT_LANGUAGE.'.MER_NEW_ORDER')}}">
												<i class="icon-valid"></i>
											</div>
										</li>
										<li>
											<div class="step  active" data-desc="{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED')}}">
												<i class="icon-valid"></i>
											</div>
										</li>
										@elseif($show_ord_status == 9)
										<li style="float:left;margin-left:20px;">
											<div class="step tooltip-demo"  data-toggle="modal" data-target="#view_failed_reason" data-desc="{{(Lang::has(Session::get('front_lang_file').'.MER_FAILED')) ? trans(Session::get('front_lang_file').'.MER_FAILED') : trans($this->FRONT_LANGUAGE.'.MER_FAILED')}}" title="{{(Lang::has(Session::get('front_lang_file').'.FRONT_VIEW_FAIL_REASON')) ? trans(Session::get('front_lang_file').'.FRONT_VIEW_FAIL_REASON') : trans($FRONT_LANGUAGE.'.FRONT_VIEW_FAIL_REASON')}}!" ><i class="fa fa-times"></i></div>
										</li>
										@else
										@php $iconArray = array('1'=>'fa fa-shopping-basket','2'=>'fa fa-check','3'=>'fa fa-times','4'=>'fa fa-truck','5'=>'fa fa-truck','6'=>'fa fa-spinner','7'=>'fa fa-suitcase','8'=>'fa fa-male'); @endphp
										@if($show_ord_status==8) @php $iconArray['8']='icon-valid'; @endphp 
										@endif
										@php $ordStatusArray = order_status_array('front_lang_file',$FRONT_LANGUAGE);  
										@endphp
										@for($i=1;$i<=8;$i++)
										@if($comDet['ord_self_pickup']=='1')
										@if($i > 3 && $i<=7)
											@elseif($show_ord_status >= 4 && $i==3)
												@elseif($show_ord_status == 2 && $i==3)
												@elseif($show_ord_status == 3 && $i==2)
												@else
												@if($i==$show_ord_status) @php $active='active'; @endphp @else @php $active='';  @endphp  @endif
												
												<li><div class="step {{$active}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
												@endif
												@else
												@if($show_ord_status >= 4 && $i==3)
												@elseif($show_ord_status == 2 && $i==3)
												@elseif($show_ord_status == 3 && $i==2)
												@else
												
												@if($i==$show_ord_status) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
												@php if($i<$show_ord_status) { $done='done';} else { $done=''; } @endphp
												
												<li><div class="step {{$active}} {{$done}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
												@endif
												@endif
												@endfor	
												@endif
											</ul>
											{{-- order tracking status --}}
											@if($comDet['ord_status']==8)
											<script>
												$(document).ready(function(){
													$("#ul_{{$comDet['ord_id']}} li").removeClass('pulse');
													$("#ul_{{$comDet['ord_id']}} li div").removeClass('active').addClass('done');
												});
											</script>
											@endif
										</div>
										<!-- View failed reason start -->
										<div id="view_failed_reason" class="modal fade view_failed_reason" role="dialog">
											<div class="modal-dialog">
												
												<!-- Modal content-->
												<div class="modal-content">
													<div class="modal-header">													
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h5 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_FAILED_REASON')) ? trans(Session::get('front_lang_file').'.FRONT_FAILED_REASON') : trans($FRONT_LANGUAGE.'.FRONT_FAILED_REASON')}}</h5>				 
													</div>
													<div class="modal-body">
														
														{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
														<div class="form-group"> 
															<div class="row panel-heading">
																<div style="width:100%; margin:0 0 20px;">
																	
																	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																		<span id="comments_span">
																			@if($customer_details->ord_failed_reason!= '')
																			{{ $customer_details->ord_failed_reason}}
																			@else
																			@endif
																		</span>
																	</div>
																</div>
																<!-- </div>
																	
																<div class="row panel-heading"> -->
																<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
																	
																</div>
																
															</div>
														</div>
														{!!Form::close()!!}
													</div>
													
												</div>
												
											</div>
										</div>
										<!-- View failed reason ends -->
										@endforeach
										@endif
										
										@if(count($commonDet['delivery_detail']) > 0 )
										<div class="order-track-sec3">
											<table>
												
												@foreach($commonDet['delivery_detail'] as $comDet)
												@if(count($comDet) > 0 )
												@php $product_popup = $item_popup = 1;  @endphp
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
														@if($commDet->ord_cancel_status == '1' && $commDet->ord_status == '3')
														<a href="javascript:;" data-toggle="modal" data-target="#rejectReasonModal_{{$commDet->ord_id}}" style="color:red;"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('front_lang_file').'.MER_REJECTED_REASON') : trans($FRONT_LANGUAGE.'.MER_REJECTED_REASON')}}!"></i>
															@lang(Session::get('front_lang_file').'.FRONT_MER_REJECT')
														</a>
														{{-- reject reason popup --}}
														<div id="rejectReasonModal_{{$commDet->ord_id}}" class="modal fade" role="dialog">
															<div class="modal-dialog">
																<!-- Modal content-->
																<div class="modal-content">
																	<div class="modal-header">
																		<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.MER_REASON_TO_REJECT')) ? trans(Session::get('front_lang_file').'.MER_REASON_TO_REJECT') : trans($FRONT_LANGUAGE.'.MER_REASON_TO_REJECT')}}</h4>
																	</div>
																	<div class="modal-body">
																		
																		<p> {!!$commDet->ord_reject_reason!!}</p>
																	</div>
																	<div class="modal-footer">
																		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																	</div>
																</div>
															</div>
														</div>
														@elseif($commDet->ord_cancel_status == '1' && $commDet->ord_status != '3')
														<a href="javascript:;" data-toggle="modal" data-target="#cancelReasonModal_{{$commDet->ord_id}}" style="color:red;" >
															<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="{{(Lang::has(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_REASON_TO_CANCEL')}}!"></i>
															@lang(Session::get('front_lang_file').'.FRONT_CANCELLED_BY_USER')
														</a>
														{{-- cancel reason popup --}}
														<div id="cancelReasonModal_{{$commDet->ord_id}}" class="modal fade" role="dialog">
															<div class="modal-dialog">
																<!-- Modal content-->
																<div class="modal-content">
																	<div class="modal-header">
																		<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL')) ? trans(Session::get('front_lang_file').'.FRONT_REASON_TO_CANCEL') : trans($FRONT_LANGUAGE.'.FRONT_REASON_TO_CANCEL')}}</h4>
																	</div>
																	<div class="modal-body">
																		
																		<p> {!!$commDet->ord_cancel_reason!!}</p>
																	</div>
																	<div class="modal-footer">
																		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																	</div>
																</div>
															</div>
														</div>
														@else
														@if($commDet->ord_status == '1')
														@lang(Session::get('front_lang_file').'.MER_ORDER_PLACED')
														@elseif($commDet->ord_status == '2')
														@lang(Session::get('front_lang_file').'.MER_ACCEPT')
														@elseif($commDet->ord_status == '4')
														@lang(Session::get('front_lang_file').'.MER_PREPARE_DELIVER')
														@elseif($commDet->ord_status == '5')
														@lang(Session::get('front_lang_file').'.MER_DISPATCHED')
														@elseif($commDet->ord_status == '6')
														@lang(Session::get('front_lang_file').'.MER_STARTED')	
														@elseif($commDet->ord_status == '6')
														@lang(Session::get('front_lang_file').'.MER_ARRIVED')	
														@elseif($commDet->ord_status == '6')
														@lang(Session::get('front_lang_file').'.MER_DELIVERED')
														@endif
														@endif
													</td>
													<td><img src="{{$path}}"></td>
													<td>
														<p>{{$commDet->item_name}}</p>
														<p>{{$commDet->pro_item_code}}</p>
													</td>
													<td>{{$commDet->ord_grant_total .' '.$commDet->ord_currency}}</td>
													@php 
													
													$reviewDet = DB::table('gr_review')->select('customer_id','review_comments','review_rating')->where('customer_id','=',Session::get('customer_id'))->where('proitem_id','=',$commDet->ord_pro_id)->first();
													@endphp
													@if($commDet->ord_status == '8')
													<td>
														
														@if($commDet->ord_type == 'Product')
														@if(empty($reviewDet) === true)
														<button type="button"><a  href="#" data-toggle="modal" data-target="#product_review" onclick="fetch_id('{{ $commDet->ord_pro_id }}','{{$commDet->ord_merchant_id}}','{{$commDet->ord_rest_id}}')">Product Review</a></button>
														@else
														
														<!-- View Product Review Popup -->
														<button type="button"><a  href="#" data-toggle="modal" data-target="#view_product_review_{{$product_popup}}">View Product Review</a></button>
														<div id="view_product_review_{{$product_popup}}" class="modal view_restaurant_review  fade" role="dialog">
															<div class="modal-dialog">
																
																<!-- Modal content-->
																<div class="modal-content">
																	<div class="modal-header">													
																		<button type="button" class="close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_YOUR_REVIEW')}}</h4>
																	</div>
																	<div class="modal-body">
																		{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
																		<div class="form-group"> 
																			<div class="row panel-heading">
																				<div style="width:100%; margin:0 0 20px;">
																					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																						<div class="form-group">
																							<span class="panel-title" >
																								<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}: </label>
																							</span>
																						</div>
																					</div>
																					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																						<span id="comments_span">{{ $reviewDet->review_comments }}</span>
																					</div>
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
																					<div class="ratings-new">
																						<div class="empty-stars"></div>
																						<div class="full-stars" style="width:{{ $reviewDet->review_rating * 20 }}%"></div>
																					</div>
																				</div>
																			</div>
																		</div>
																		{!!Form::close()!!}
																	</div>
																	
																</div>
																
															</div>
														</div>													
														<!-- End View Product Review Popup -->
														
														
														
														@endif
														@else
														@if(empty($reviewDet) === true && $commDet->ord_status == '8')
														<a  href="#" data-toggle="modal" data-target="#item_review" onclick="fetch_id('{{ $commDet->ord_pro_id }}','{{$commDet->ord_merchant_id}}','{{$commDet->ord_rest_id}}')">@lang(Session::get('front_lang_file').'.FRONT_IT_REV')</a>
														@elseif(empty($reviewDet) === false && $commDet->ord_status == '8')
														<!-- View Item Review Popup -->
														<a  href="#" data-toggle="modal" data-target="#view_item_review_{{$item_popup}}">@lang(Session::get('front_lang_file').'.FRONT_VI_IT_REV')</a>
														<div id="view_item_review_{{$item_popup}}" class="modal view_restaurant_review fade" role="dialog">
															<div class="modal-dialog">
																
																<!-- Modal content-->
																<div class="modal-content">
																	<div class="modal-header">
																		{{-- <img src="../public/front/images/user-icon.png" width="50"> --}}
																		<button type="button" class="close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title">{{(Lang::has(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW')) ? trans(Session::get('front_lang_file').'.FRONT_YOUR_REVIEW') : trans($FRONT_LANGUAGE.'.FRONT_YOUR_REVIEW')}}</h4>
																	</div>
																	<div class="modal-body">
																		{!! Form::open(['method' => 'post','class' => 'form-auth-small']) !!}
																		<div class="form-group"> 
																			<div class="row panel-heading">
																				<div style="width:100%; margin:0 0 20px;">
																					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																						<div class="form-group">
																							<span class="panel-title" >
																								<label>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')) ? trans(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW') : trans($FRONT_LANGUAGE.'.ADMIN_COMMENT_REVIEW')}}: </label>
																							</span>
																						</div>
																					</div>
																					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
																						<span id="comments_span">{{ $reviewDet->review_comments }}</span>
																					</div>
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
																					<div class="ratings-new">
																						<div class="empty-stars"></div>
																						<div class="full-stars" style="width:{{ $reviewDet->review_rating * 20 }}%"></div>
																					</div>
																				</div>
																			</div>
																		</div>
																		{!!Form::close()!!}
																	</div>
																	
																</div>
																
															</div>
														</div>													
														<!-- End View Item Review Popup -->
														
														
														@endif
														@endif
													</td>
													@endif
												</tr>
												@php $product_popup++; $item_popup++; @endphp
												@endforeach
												@endif
												
												@endforeach
											</table>
										</div>
									</div>
									@endif
									@php $popup_count++; @endphp 
									@endforeach
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!--Restaurant Review Modal -->
		<div class="modal fade item_review" id="restaurant_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>@lang(Session::get('front_lang_file').'.FRONT_ADD_REV')</h4>
					</div>
					<div class="modal-body">
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'restaurant_review_submit','enctype'=>'multipart/form-data']) !!}
						<div class="form-group"> 
							<div class="store_res_area"></div>
							<label>@lang(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')</label>
							
							<textarea name="rev_comments" class="form-control" placeholder="" required="required" maxlength="200"></textarea>
							<input type="hidden" name="page_id" value="{{Request::segment(2)}}">
							<label>@lang(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')</label>
							
							<section class='rating-widget'>
								
								<!-- Rating Stars Box -->
								<div class='rating-stars text-center'>
									<ul id='stars'>
										<li class='star' title='Poor' data-value='1'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Fair' data-value='2'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Good' data-value='3'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Excellent' data-value='4'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='WOW!!!' data-value='5'>
											<i class='fa fa-star fa-fw'></i>
										</li>
									</ul>
								</div>
								
								<div class='success-box'>
									<div class='clearfix'></div>
									<i class="fa fa-hand-o-right"></i>
									<div class='text-message'></div>
									<div class="in_val"></div>
									<div class='clearfix'></div>
								</div>
								
							</section>
							
						</div>
						@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
						{!! Form::submit($saveBtn,['class' => ''])!!} 
						@php $closeBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('front_lang_file').'.ADMIN_CLOSE') : trans($FRONT_LANGUAGE.'.ADMIN_CLOSE'); @endphp
						<!--{!! Form::submit($closeBtn,['type'=>'button','class'=>"",'data-dismiss'=>"modal"])!!}-->
						{!! Form::close() !!}
						
					</div>
					
				</div>
			</div>
		</div>
		
		
		<!--Store Review Modal -->
		<div class="modal fade item_review" id="store_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>@lang(Session::get('front_lang_file').'.FRONT_ADD_REV')</h4>
						
					</div>
					<div class="modal-body">
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'store_review_submit','enctype'=>'multipart/form-data']) !!}
						<div class="form-group"> 
							<div class="store_res_area"></div>
							<label>@lang(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')</label>
							
							<textarea name="rev_comments" class="form-control" placeholder="" required="required" maxlength="200"></textarea>
							<input type="hidden" name="page_id" value="{{Request::segment(2)}}">
							<label>@lang(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')</label>
							
							<section class='rating-widget'>
								
								<!-- Rating Stars Box -->
								<div class='rating-stars'>
									<ul id='stars'>
										<li class='star' title='Poor' data-value='1'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Fair' data-value='2'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Good' data-value='3'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Excellent' data-value='4'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='WOW!!!' data-value='5'>
											<i class='fa fa-star fa-fw'></i>
										</li>
									</ul>
								</div>
								
								<div class='success-box'>
									<div class='clearfix'></div>
									<i class="fa fa-hand-o-right"></i>
									<div class='text-message'></div>
									<div class="in_val"></div>
									<div class='clearfix'></div>
								</div>
								
							</section>
							
						</div>
						@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
						{!! Form::submit($saveBtn,['class' => ''])!!}
						@php $closeBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('front_lang_file').'.ADMIN_CLOSE') : trans($FRONT_LANGUAGE.'.ADMIN_CLOSE'); @endphp
						<!--{!! Form::submit($closeBtn,['type'=>'button','class'=>"",'data-dismiss'=>"modal"])!!}-->
						{!! Form::close() !!}
						
					</div>
					
				</div>
			</div>
		</div>
		<!--Product Review Modal -->
		<div class="modal fade item_review" id="product_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>@lang(Session::get('front_lang_file').'.FRONT_ADD_REV')</h4>
						
					</div>
					<div class="modal-body">
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'product_review_submit','enctype'=>'multipart/form-data']) !!}
						<div class="form-group"> 
							<div class="store_res_area"></div>
							<label>@lang(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')</label>
							
							<!--<input type="textarea" name="rev_comments" class="form-control" placeholder="" required="required" maxlength="100">-->
							<textarea name="rev_comments" maxlength="200" required="required"></textarea>
							<input type="hidden" name="page_id" value="{{Request::segment(2)}}">
							<label>@lang(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')</label>
							
							<section class='rating-widget'>
								
								<!-- Rating Stars Box -->
								<div class='rating-stars'>
									<ul id='stars'>
										<li class='star' title='Poor' data-value='1'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Fair' data-value='2'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Good' data-value='3'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Excellent' data-value='4'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='WOW!!!' data-value='5'>
											<i class='fa fa-star fa-fw'></i>
										</li>
									</ul>
								</div>
								
								<div class='success-box'>
									<div class='clearfix'></div>
									<i class="fa fa-hand-o-right"></i>
									<div class='text-message'></div>
									<div class="in_val"></div>
									<div class='clearfix'></div>
								</div>
								
							</section>
							
						</div>
						@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
						{!! Form::submit($saveBtn,['class' => ''])!!}
						@php $closeBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('front_lang_file').'.ADMIN_CLOSE') : trans($FRONT_LANGUAGE.'.ADMIN_CLOSE'); @endphp
						<!--{!! Form::submit($closeBtn,['type'=>'button','class'=>"",'data-dismiss'=>"modal"])!!}-->
						{!! Form::close() !!}
						
					</div>
					
				</div>
			</div>
		</div>
		<!--  -->
		
		<!--Product Review Modal -->
		<div class="modal fade item_review" id="item_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>@lang(Session::get('front_lang_file').'.FRONT_ADD_REV')</h4>
					</div>
					<div class="modal-body">
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'item_review_submit','enctype'=>'multipart/form-data']) !!}
						<div class="form-group"> 
							<div class="store_res_area"></div>
							<label>@lang(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')</label>
							
							<!--<input type="text" name="rev_comments" class="form-control" placeholder="" maxlength="100" required="required">-->
							<textarea name="rev_comments"  maxlength="200" required="required"></textarea>
							<input type="hidden" name="page_id" value="{{Request::segment(2)}}">
							<label>@lang(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')</label>
							
							<section class='rating-widget'>
								
								<!-- Rating Stars Box -->
								<div class='rating-stars'>
									<ul id='stars'>
										<li class='star' title='Poor' data-value='1'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Fair' data-value='2'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Good' data-value='3'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Excellent' data-value='4'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='WOW!!!' data-value='5'>
											<i class='fa fa-star fa-fw'></i>
										</li>
									</ul>
								</div>
								
								<div class='success-box'>
									<div class='clearfix'></div>
									<!--<img alt='tick image'  src="../public/front/images/hand-pointer.png">-->
									<i class="fa fa-hand-o-right"></i>
									<div class='text-message'></div>
									<div class="in_val"></div>
									<div class='clearfix'></div>
								</div>
								
							</section>
							
						</div>
						@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
						{!! Form::submit($saveBtn,['class' => ''])!!}
						@php $closeBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('front_lang_file').'.ADMIN_CLOSE') : trans($FRONT_LANGUAGE.'.ADMIN_CLOSE'); @endphp
						<!--{!! Form::submit($closeBtn,['type'=>'button','class'=>"",'data-dismiss'=>"modal"])!!}-->
						{!! Form::close() !!}
						
					</div>
					
				</div>
			</div>
		</div>
		<!--  -->
		
		<!--ORDER REVIEW MODAL -->
		<div class="modal fade item_review" id="order_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>@lang(Session::get('front_lang_file').'.FRONT_ADD_REV')</h4>
					</div>
					<div class="modal-body">
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'order_review_submit','enctype'=>'multipart/form-data']) !!}
						<div class="form-group"> 
							<div class="store_res_area"></div>
							<label>@lang(Session::get('front_lang_file').'.ADMIN_COMMENT_REVIEW')</label>
							
							<textarea name="rev_comments" class="form-control" placeholder="" required="required" maxlength="200"></textarea>
							<input type="hidden" name="page_id" value="{{Request::segment(2)}}">
							<input type="hidden" name="order_id" id="orderReview_order_id" value="">
							<input type="hidden" name="delivery_id"  id="orderReview_delivery_id" value="">
							<input type="hidden" name="agent_id" id="orderReview_agent_id"value="">
							<input type="hidden" name="ord_rest_id" id="orderReview_rest_id"value="">
							<label>@lang(Session::get('front_lang_file').'.ADMIN_RATING_REVIEW')</label>
							
							<section class='rating-widget'>
								
								<!-- Rating Stars Box -->
								<div class='rating-stars text-center'>
									<ul id='stars'>
										<li class='star' title='Poor' data-value='1'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Fair' data-value='2'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Good' data-value='3'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='Excellent' data-value='4'>
											<i class='fa fa-star fa-fw'></i>
										</li>
										<li class='star' title='WOW!!!' data-value='5'>
											<i class='fa fa-star fa-fw'></i>
										</li>
									</ul>
								</div>
								
								<div class='success-box'>
									<div class='clearfix'></div>
									<i class="fa fa-hand-o-right"></i>
									<div class='text-message'></div>
									<div class="in_val"></div>
									<div class='clearfix'></div>
								</div>
								
							</section>
							
						</div>
						@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
						{!! Form::submit($saveBtn,['class' => ''])!!} 
						@php $closeBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_CLOSE')) ? trans(Session::get('front_lang_file').'.ADMIN_CLOSE') : trans($FRONT_LANGUAGE.'.ADMIN_CLOSE'); @endphp
						<!--{!! Form::submit($closeBtn,['type'=>'button','class'=>"",'data-dismiss'=>"modal"])!!}-->
						{!! Form::close() !!}
						
					</div>
					
				</div>
			</div>
		</div>
		<!-- EOF ORDER REVIEW-->
		
		
		@section('script')
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
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();   
				/* 1. Visualizing things on Hover - See next part for action on click */
				$('#stars li').on('mouseover', function(){
					var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
					
					// Now highlight all the stars that's not after the current hovered star
					$(this).parent().children('li.star').each(function(e){
						if (e < onStar) {
							$(this).addClass('hover');
						}
						else {
							$(this).removeClass('hover');
						}
					});
					
					}).on('mouseout', function(){
					$(this).parent().children('li.star').each(function(e){
						$(this).removeClass('hover');
					});
				});
				
				
				/* 2. Action to perform on click */
				$('#stars li').on('click', function(){
					var onStar = parseInt($(this).data('value'), 10); // The star currently selected
					var stars = $(this).parent().children('li.star');
					
					for (i = 0; i < stars.length; i++) {
						$(stars[i]).removeClass('selected');
					}
					
					for (i = 0; i < onStar; i++) {
						$(stars[i]).addClass('selected');
					}
					
					// JUST RESPONSE (Not needed)
					var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
					var msg = "";
					if (ratingValue > 1) {
						msg = "Thanks! You rated this " + ratingValue + " stars.";
					}
					else {
						msg = "We will improve ourselves. You rated this " + ratingValue + " stars.";
					}
					$('.success-box div.in_val').html("<input type='hidden' name='rat_val' value='"+ratingValue+"'>");
					responseMessage(msg);
					
				});
			});
			function responseMessage(msg) {
				$('.success-box').fadeIn(200);  
				$('.success-box div.text-message').html("<span>" + msg + "</span>");
			}
			
			
			function fetch_id(pro_id,mer_id,store_id)
			{	
				if(pro_id != '')
				{
					$('.store_res_area').html("<input name='product_id' type='hidden' value='"+pro_id+"'><input name='res_mer_id' type='hidden' value='"+mer_id+"'><input name='res_store_id' type='hidden' value='"+store_id+"'>");	
				}
				else
				{
					$('.store_res_area').html("<input name='res_store_id' type='hidden' value='"+store_id+"'><input name='res_mer_id' type='hidden' value='"+mer_id+"'>");
					
				}
			} 
			function getOrderPopup(ord_transaction_id,ord_agent_id,ord_delivery_memid,rest_id){
				$('#orderReview_order_id').val(ord_transaction_id);
				$('#orderReview_delivery_id').val(ord_delivery_memid);
				$('#orderReview_agent_id').val(ord_agent_id);
				$('#orderReview_rest_id').val(rest_id);
			}
		</script>
		@if(count($storewise_details) > 0 )
		@foreach($storewise_details as $key=>$commonDet)
		@if(count($commonDet['general_detail']) > 0 )
		@foreach($commonDet['general_detail'] as $comDet)
		@if(count($comDet) > 0 )
		@if($comDet['ord_status']==8)
		<script>
			$(document).ready(function(){
				$("#ul_{{$comDet['ord_id']}} li").removeClass('pulse');
				$("#ul_{{$comDet['ord_id']}} li div").removeClass('active').addClass('done');
			});
		</script>
		@endif
		@endif
		@endforeach
		@endif	
		@endforeach
		@endif
		@endsection
		@stop		
		