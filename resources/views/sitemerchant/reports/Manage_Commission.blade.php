@extends('sitemerchant.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style>
	table.dataTable
	{
		margin-top: 20px !important;
	}
#location_table .dataTables_length label{ margin:0; }
</style>

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
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> {{ (Lang::has(Session::get('mer_lang_file').'.MER_LOADING')) ? trans(Session::get('mer_lang_file').'.MER_LOADING') : trans($MER_OUR_LANGUAGE.'.MER_LOADING') }}</button>
								</div>
								{{-- Display  message--}}
								@if(Session::has('message'))
							    <div class="alert alert-success alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							   		{{Session::get('message')}}    
							    </div>
								@endif
								
								<div class="table-responsive" style="margin-top:10px;">
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
		                                	<tr>
			                                   <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTSTORE_NAME') }}</th>
			                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOT_ORDER_AMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOT_ORDER_AMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOT_ORDER_AMT') }}
			                                        ({{ $default_currency }})<span style="font-size: 11px;"> ({{(Lang::has(Session::get('mer_lang_file').'.ADMIN_TOT_ORDER_AMT_EX_COMM')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOT_ORDER_AMT_EX_COMM') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOT_ORDER_AMT_EX_COMM')}})</span>
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOTAL_COMMISSION_AMOUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOTAL_COMMISSION_AMOUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_COMMISSION_AMOUNT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOTAL_CANCEL_AMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOTAL_CANCEL_AMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_CANCEL_AMT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                     <th>{{ (Lang::has(Session::get('mer_lang_file').'.MER_RECEIVED_AMOUNT_FROM_ADMIN')) ? trans(Session::get('mer_lang_file').'.MER_RECEIVED_AMOUNT_FROM_ADMIN') : trans($MER_OUR_LANGUAGE.'.MER_RECEIVED_AMOUNT_FROM_ADMIN') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_BALANCE_COMMISSION_AMOUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_BALANCE_COMMISSION_AMOUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_BALANCE_COMMISSION_AMOUNT') }}
			                                        ({{ $default_currency }})
			                                    </th>
			                                    
			                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW_TRANSACTION')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW_TRANSACTION') : trans($MER_OUR_LANGUAGE.'.ADMIN_VIEW_TRANSACTION') }}</th>
			                                    <th>@lang(Session::get('mer_lang_file').'.ADMIN_PAY_REQ')
			                                    </th>
			                                </tr>
		                                </thead>
		                                <tbody> 
		                                	@php $payment_status = 'publish'; @endphp

											@if(empty($payment_details) === false)
												@if($payment_details->mer_paymaya_status == 'Unpublish' && $payment_details->mer_netbank_status == 'Unpublish')
													@php $payment_status = 'unpublish'; @endphp
												@endif
											@endif
											@if(empty($commission_list)=== false)

		                                     	@php 
		                                    		$paid_commission = get_paid_commission($commission_list->or_mer_id); 
		                                    		/* $balance = $commission_list->or_mer_amt - $paid_commission - $commission_list->or_admin_amt - $commission_list->or_cancel_amt; */
		                                    		$balance = $commission_list->or_mer_amt - $paid_commission  - $commission_list->or_cancel_amt;
		                                    	@endphp
		                                        <tr>
		                                           	<td>{{ucfirst($commission_list->st_name)}}</td>
		                                            <td>{{number_format($commission_list->or_mer_amt,2)}} </td>
		                                            <td>{{number_format($commission_list->or_admin_amt,2)}}</td>
		                                           	<td>{{number_format($commission_list->or_cancel_amt,2)}}</td>
		                                           	<td>{{number_format($paid_commission,2)}}</td>
		                                            <td>{{number_format($balance,2)}}</td>
		                                            
		                                            <td>
		                                                <a href="{{ url('mer_commission_view_transaction')."/".$commission_list->or_mer_id}}" class="btn btn-warning btn-sm">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_VIEW')) ? trans(Session::get('mer_lang_file').'.ADMIN_VIEW') : trans($MER_OUR_LANGUAGE.'.ADMIN_VIEW')}}</a>
		                                            </td>
		                                            <td>
		                                            	@if(number_format($balance,2) > 0)
		                                            		@if($payment_status == 'unpublish')
		                                            			<span style="color:red">
				                                            		{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_FILL_PAY_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_FILL_PAY_DETAILS') : trans($MER_OUR_LANGUAGE.'.ADMIN_FILL_PAY_DETAILS')}}
				                                            	</span>
				                                            	<a href="{{url('merchant_profile')}}" target="new">
				                                            	<button class="btn btn-success">
				                                            		
				                                            		@lang(Session::get('mer_lang_file').'.ADMIN_CLICK')
				                                            	</button>
				                                            	</a>

			                                            	@else
				                                            	<a href="{{url('send_pay_request').'/'.number_format($balance,2)}}">
				                                            	<button class="btn btn-success">
				                                            		@lang(Session::get('mer_lang_file').'.ADMIN_SEND_PAY_REQ')
				                                            	</button>
				                                            	</a>
				                                            @endif
		                                            	@else
		                                            		Paid
		                                            	@endif
		                                            </td>
						                        </tr> 
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

@section('script')
<script>
	$(document).ready(function () {
		
		$('#dataTables-example').dataTable({
		    "bPaginate": false,
			"responsive": true,
		    //"scrollX": true,
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		"bAutoWidth": false });
	});
</script>
@endsection
@stop