@extends('DeliveryManager.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style>
	#location_table .dataTables_length label{margin:0;}
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
								<a href="{{url('/delivery-commission-tracking')}}" class="btn btn-success pull-right">{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BACK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BACK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BACK') }}</a>
							</div>
						
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div style="">
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
		                                <tr>
		                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO') }}</th>
		                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_TRANSACTION_ID')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_TRANSACTION_ID') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_TRANSACTION_ID') }}</th>
		                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAY_TYPE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAY_TYPE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAY_TYPE') }}</th>
											<th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAID_COMMISSION_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAID_COMMISSION_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAID_COMMISSION_AMOUNT') }}
		                                        ({{ $default_currency }})
		                                    </th>
											<th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_RECEIVED_ORDER_AMOUNT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_RECEIVED_ORDER_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_RECEIVED_ORDER_AMOUNT') }}
		                                        ({{ $default_currency }})
		                                    </th>
		                                    <th>{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAID_DATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAID_DATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAID_DATE') }}</th>
		                                </tr>
		                                </thead>
		                                <tbody>
		                                @if(count($commission_list)>0)
		                                    @php $i = ($commission_list->currentpage()-1)*$commission_list->perpage()+1;  @endphp
		                                    @foreach($commission_list as $v)
		                                        <tr>
		                                            <td>{{$i}}</td>
		                                            <td>{{ucfirst($v->transaction_id)}}</td>
		                                            <td>
														@php if($v->pay_type=='0') { 
															echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NETBANKING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NETBANKING') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NETBANKING');
														}elseif($v->pay_type=='1') {
															echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYMAYA') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAYMAYA');
														}elseif($v->pay_type=='2') {
															echo (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PAYNAMICS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PAYNAMICS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PAYNAMICS');
														}
														@endphp
													</td>
													<td>{{number_format($v->commission_paid,2)}} </td>
													<td>{{number_format($v->amount_received,2)}} </td>
		                                            <td>@if($v->commission_date!='0000-00-00 00:00:00') {{date('m/d/Y H:i:s',strtotime($v->commission_date))}} @endif</td>
		                                            
						                        </tr> 
		                                        @php $i++; @endphp
		                                    @endforeach
		                                @endif
		                                </tbody>
		                            </table>
								</div>
								<!--table-->
							</div>
							
							{{--Manage page ends--}}
						</div>
						@if(count($commission_list) > 0)
							{!! $commission_list->render() !!}
						@endif
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
		    "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		"bAutoWidth": false });
	});
</script>
@endsection
@stop