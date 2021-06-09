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
								
								
								<div>
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
		                                <thead>
		                                <tr>
		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO') }}</th>
		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_EMAIL') }}</th>
		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT_NAME') }}</th>

		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_TRANSACTION_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TRANSACTION_ID') }}</th>

		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_COMMISSION_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_COMMISSION_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAID_COMMISSION_AMOUNT') }}</th>


		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAID_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAID_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAID_DATE') }}
		                                        ({{ $default_currency }})
		                                    </th>
		                                    
		                                    <th>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAY_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAY_TYPE') }}
		                                        ({{ $default_currency }})
		                                    </th>
		                                </tr>
		                                </thead>
		                                <tbody>
		                                @if(count($commission_list)>0)
		                                    <?php $i = 1;//($commission_list->currentpage()-1)*$commission_list->perpage()+1;  ?>
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
						                                    {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('admin_lang_file').'.ADMIN_NETBANKING') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NETBANKING') }}
						                                  @elseif($v->pay_type==2)
															{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA') }}
														  @else
															  {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS') }}
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