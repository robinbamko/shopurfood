@extends('sitemerchant.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
<style>
	#location_table .dataTables_length label{margin:0;}
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
						
							<div class="panel-body" id="location_table" >
								<div id="loading-image" style="display:none">
									<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
								</div>
								
								
								<div style="margin: 7px 23px;">
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
		                                <tr>
		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_SNO')) ? trans(Session::get('mer_lang_file').'.ADMIN_SNO') : trans($MER_OUR_LANGUAGE.'.ADMIN_SNO') }}</th>
		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT_EMAIL')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT_EMAIL') : trans($MER_OUR_LANGUAGE.'.ADMIN_MERCHANT_EMAIL') }}</th>
		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME') }}</th>

		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('mer_lang_file').'.ADMIN_TRANSACTION_ID') : trans($MER_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID') }}</th>

		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAID_COMMISSION_AMOUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAID_COMMISSION_AMOUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_PAID_COMMISSION_AMOUNT') }}</th>


		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAID_DATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAID_DATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PAID_DATE') }}
		                                        ({{ $default_currency }})
		                                    </th>
		                                    
		                                    <th>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_PAY_TYPE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PAY_TYPE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PAY_TYPE') }}
		                                        ({{ $default_currency }})
		                                    </th>
		                                </tr>
		                                </thead>
		                                <tbody>
		                                @if(count($commission_list)>0)
		                                    <?php $i = 1; ?>
		                                    @foreach($commission_list as $v)
		                                        <tr>
		                                            <td>{{$i}}</td>
		                                            <td>{{ucfirst($v->mer_email)}}</td>
		                                            <td>{{ucfirst($v->mer_fname.' '.$v->mer_lname)}}</td>
		                                            <td>{{ucfirst($v->mer_transaction_id)}}</td>
		                                            <td>{{number_format($v->commission_paid,2)}} </td>
		                                            <td>{{$v->commission_date}}</td>
		                                            <td>
						                                  @if($v->pay_type==1)
						                                    Online-Card
						                                  @elseif($v->pay_type==2)
						                                    Paypal
														  @else
															  Stripe
						                                  @endif
						                            </td>
						                        </tr> 
		                                        <?php $i++; ?>
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