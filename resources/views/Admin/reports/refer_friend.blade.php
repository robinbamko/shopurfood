@extends('Admin.layouts.default')
@section('PageTitle')
	{{$pagetitle}}
@endsection
@section('content')

	<style>
		table.dataTable { margin-top:20px!important; }
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
								{{-- Display error message--}}

								@if ($errors->has('errors'))
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										{{ $errors->first('errors') }}
									</div>
								@endif
								@if ($errors->has('upload_file'))
									<p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p>
								@endif
								@if (Session::has('message'))
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										<i class="fa fa-check-circle"></i>{{ Session::get('message') }}
									</div>
								@endif


								{{--Manage page starts--}}
								<div class="panel-body" id="location_table" >
									<div id="loading-image" style="display:none">
										<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
									</div>

									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
										<tr>
											<td>&nbsp;</td>
											<td><input type="text" id="referrerMail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td><input type="text" id="referralMail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th  style="text-align:center" class="sorting_no_need">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REFERRER_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_REFERRER_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REFERRER_MAIL')}}
											</th>

											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REFERRAL_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_REFERRAL_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REFERRAL_MAIL')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_OFFER_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_OFFER_PERCENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_OFFER_PERCENT')}}
											</th>
											<th style="text-align:center">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_WALLET_AMOUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WALLET_AMOUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WALLET_AMOUNT')}} <i class="fa fa-info-circle tooltip-demo" title="Wallet amount will be added, only if the referral is purchased" data-placement="left"></i>
											</th>
										</tr>
										</thead>
										<tbody>

										</tbody>
										<tfoot>
										</tfoot>
									</table>
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
        var table='';
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).ready(function () {
            table = $('#dataTables-example').DataTable({
                "processing": true,
				"responsive": true,
                "serverSide": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching": false,

                "ajax":{
                    "url": "{{ route('refer_friend_ajax') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}",'referrerMail_search': function(){return $("#referrerMail_search").val(); },referralMail_search:function(){return $("#referralMail_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": 0,
                    "orderable": false
                } ],
                "columns": [
                    { "data": "SNo",sWidth: '8%' },
                    { "data": "referrerEmail", sWidth: '30%' },
                    { "data": "referralEmail", sWidth: '30%' },
                    { "data": "offerPercent", sWidth: '12%' },
                    { "data": "offerAmt", sWidth: '20%' }
                ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'left'})
                }
            });
            $('#referrerMail_search,#referralMail_search').keyup( function() {
                table.draw();
                //table.search( this.value ).draw();
            });

        });



	</script>

@endsection
@stop