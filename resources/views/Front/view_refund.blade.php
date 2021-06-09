@extends('Front.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
<style type="text/css">
	
</style>
<div class="main-sec">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		
		<div class="container-fluid add-country">
			<div class="row">
				<div class="container right-container">
					<div class="col-md-12 invoice-section">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
							</div>
							{{-- Display error message--}}
							
							
							
							{{--Manage page starts--}}
							<div class="panel-body" id="location_table" >
								
								
								<div>
									
									<!-- Table row -->
									<div class="row">
										<div class="col-lg-12 col-md-12 col-xs-12 table invoice-table">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ITEMRPDT_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_ITEMRPDT_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_ITEMRPDT_NAME')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.FRONT_IT_AMT')) ? trans(Session::get('front_lang_file').'.FRONT_IT_AMT') : trans($FRONT_LANGUAGE.'.FRONT_IT_AMT')}}</th>
														<th >{{(Lang::has(Session::get('front_lang_file').'.FRONT_TRANS_FEE')) ? trans(Session::get('front_lang_file').'.FRONT_TRANS_FEE') : trans($FRONT_LANGUAGE.'.FRONT_TRANS_FEE')}}</th>
														<th >{{(Lang::has(Session::get('front_lang_file').'.FRONT_CAN_TP')) ? trans(Session::get('front_lang_file').'.FRONT_CAN_TP') : trans($FRONT_LANGUAGE.'.FRONT_CAN_TP')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.FRONT_REF_AMT')) ? trans(Session::get('front_lang_file').'.FRONT_REF_AMT') : trans($FRONT_LANGUAGE.'.FRONT_REF_AMT')}}&nbsp;<i class="fa fa-info-circle tooltip-demo" title="{{__(Session::get('front_lang_file').'.FRONT_CANCEL_TITLE')}}"></i></th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('front_lang_file').'.ADMIN_TRANSACTION_ID') : trans($FRONT_LANGUAGE.'.ADMIN_TRANSACTION_ID')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.FRONT_REF_DT')) ? trans(Session::get('front_lang_file').'.FRONT_REF_DT') : trans($FRONT_LANGUAGE.'.FRONT_REF_DT')}}</th>
														<th>{{(Lang::has(Session::get('front_lang_file').'.FRONT_REF_STATUS')) ? trans(Session::get('front_lang_file').'.FRONT_REF_STATUS') : trans($FRONT_LANGUAGE.'.FRONT_REF_STATUS')}}</th>

													</tr>
												</thead>
												<tbody class="checkoutDetail">
													@foreach($cancel_details as $details)
													<tr>
													<td>
													<span style="color: #7f1900">@lang(Session::get('front_lang_file').'.FRONT_IN') {{ucfirst($details->st_name)}}</span><br>
													{{ucfirst($details->pro_name)}}
													@php $choices = ''; @endphp
													@if($details->ord_had_choices == 'Yes')
						
													@php $ch_array = json_decode($details->ord_choices,true); @endphp 
													@if(count($ch_array) > 0)
														@php 
														$choices .= (Lang::has(Session::get('front_lang_file').'.ADMIN_INCLUDES')) ? trans(Session::get('front_lang_file').'.ADMIN_INCLUDES') : trans($this->ADMIN_LANGUAGE.'.FRONT_INCLUDES');
														$choices .= ' : ';
														@endphp
														@foreach($ch_array as $arr)
																	@if(session::get('front_lang_code') == '' || session::get('front_lang_code') == 'en')
													@php $ch_name ='ch_name'  @endphp
																	@else
																		@php $ch_name ='ch_name_'.Session::get('front_lang_code');  @endphp
																	@endif
															@php

                                                               $choices .=DB::table("gr_choices")->where("ch_id","=",$arr['choice_id'])->first()->$ch_name;
															@endphp
															@if ($arr === end($ch_array) && count($ch_array) > 1) 
															
														    @php    $choices .= ','; @endphp
														    @endif
														@endforeach
													@endif
													@endif
													<br>
													{{$choices}}
													</td>
													<td>{{$details->ord_currency.' '.$details->ord_grant_total}}</td>
													<td>
														@if($details->ord_status == '3' || $details->ord_status == '9')
														{{'-'}}
														@else
														{{$details->ord_currency.' '.$details->ord_admin_amt}}
														@endif
													</td>
													<td>
														@if($details->ord_cancel_status == '1' && $details->ord_status == '3')
															@lang(Session::get('front_lang_file').'.FRONT_MER_REJE')
														@elseif($details->ord_cancel_status == '1' && $details->ord_status == '1')
															@lang(Session::get('front_lang_file').'.FRONT_USER_CANCEL')
														@elseif($details->ord_status == '9')
															@lang(Session::get('front_lang_file').'.FRONT_DEL_FAIL')
														@endif
													</td>
													<td>
														@if($details->ord_pay_type =="COD")
															@lang(Session::get('front_lang_file').'.FRONT_NO_BAL_TO_RCV')
														@else
														{{($details->ord_cancel_paidamt != '') ? $details->ord_currency.' '.$details->ord_cancel_paidamt : '-'}}
														@endif
													</td>
													<td>{{($details->ord_cancelpaid_transid != '') ? $details->ord_cancelpaid_transid : '-'}}</td>
													<td>{{($details->cancel_paid_date != '') ? $details->cancel_paid_date : '-'}}</td>
													<td>
														@if($details->ord_pay_type =="COD")
															{{ "-"}} 
														@elseif($details->ord_cancel_payment_status != '')
															@if($details->ord_cancel_payment_status == '1')
															 @lang(Session::get('front_lang_file').'.FRONT_SUXUS')
															@elseif($details->ord_cancel_payment_status == '2')
																@lang(Session::get('front_lang_file').'.FRONT_FAIL')
															@endif
														@else
															@lang(Session::get('front_lang_file').'.FRONT_PENDING')
														@endif
													</td>
												</tr>
													@endforeach
													
													
													
												</tbody>
											</table>
										</div>
										<!-- /.col -->
										
									</div>
									<!-- /.row -->
									
									
								</div>
							</div>
							{{--Manage page ends--}}
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>
	
@endsection
@section('script')
<script>
$(document).ready(function(){
	$('.tooltip-demo').tooltip({placement:'bottom'})
});

</script>
@endsection


