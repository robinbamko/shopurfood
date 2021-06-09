@extends('DeliveryManager.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')		

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
							@if (Session::has('message')) 
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
							@endif
							@if ($errors->any())
							    <div class="alert alert-warning alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							        <ul>
							            @foreach ($errors->all() as $error)
							                <li>{{ $error }}</li>
							            @endforeach
							        </ul>
							    </div>
							@endif
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:20px">
								<div class="row-fluid">
									<div class="alert alert-success">
										<strong>Selected Order(s)</strong>
										<div>
										@php $ord_transaction_array = explode(",",$ord_transaction_id); @endphp
										@if(count($ord_transaction_array) > 0 )
											@foreach($ord_transaction_array as $txnId)
												@php $explodeTransId = explode("`",$txnId) @endphp
												<a href="{{url('delivery-invoice-order/'.base64_encode($explodeTransId[0]).'/'.base64_encode($explodeTransId[1]))}}" target="_blank">{{$explodeTransId[0]}}&nbsp;{{($explodeTransId[3])}}</a><br>
											@endforeach
										@endif
										</div>
									</div>
									<div id="loading-image" style="display:none">
										<button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
									</div>
									
									<div class="top-button top-btn-full" style="position:relative;margin-bottom: 15px;">
										{!! Form::button((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ASSIGN') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ASSIGN'),['id' => 'block_value1','class' => 'block_value btn btn-success'])!!}
									</div>
									
									<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
										<thead>
											<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td><input type="text" id="delboyName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
												<td><input type="text" id="delboyEmail_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
												<td><input type="text" id="delboyPhone_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('DelMgr_lang_file').'.DEL_TXT_SERCH') }}"/></td>
												<td>&nbsp;</td>
												<td><input type="hidden" id="publish_search" value="" />
													<?php /*
													<select id="publish_search" class="form-control" style="width:100%">
														<option value="">@lang(Session::get('DelMgr_lang_file').'.DEL_ALL')</option>
														<option value="1">@lang(Session::get('DelMgr_lang_file').'.DEL_PUBLISH')</option>
														<option value="0">@lang(Session::get('DelMgr_lang_file').'.DEL_UNPUBLISH')</option>
													</select> */ ?>
												</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<th  style="text-align:center">&nbsp;</th>
												<th class="sorting_no_need">
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SNO')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_NAME')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_EMAIL')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_PHONE')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FARE_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FARE_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FARE_DETAILS')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SUXES_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SUXES_ORDER') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SUXES_ORDER')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FAILED_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FAILED_ORDER') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FAILED_ORDER')}}
												</th>
												<th>
													{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PENDING_ORDER')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PENDING_ORDER') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PENDING_ORDER')}}
												</th>
											</tr>
										</thead>
										<tbody>
										
										</tbody>
										<tfoot>
										</tfoot>
									</table>

								</div>
							</div>
							
							{{-- Add/Edit page ends--}}
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>
@section('script')
<script>
var table='';
	$(document).ready(function() {
		table = $('#dataTables-example').DataTable({
					"processing": true,
					"serverSide": true,
					"bLengthChange": true,
					"bAutoWidth": false, 
					"searching": false,
					
					"ajax":{
						"url": "{{ url('reassign_delboy_ajax') }}",
						"dataType": "json",
						"type": "POST",
						"beforeSend":function(){ $('#check_all').prop('checked',false); },
						"data":{ _token: "{{csrf_token()}}",'delboyName_search': function(){return $("#delboyName_search").val(); },delboyEmail_search:function(){return $("#delboyEmail_search").val(); },delboyPhone_search:function(){return $("#delboyPhone_search").val(); },publish_search:function(){return $("#publish_search").val(); },ord_transaction_id:'{{$ord_transaction_id}}'}
					},
					"columnDefs": [ {
						"targets": [0,1],
						"orderable": false
					} ],
					"order": [ 1, 'desc' ],
					"columns": [
								{ "data": "checkBox",sWidth: '5%' },
								{ "data": "SNo", sWidth: '9%' },
								{ "data": "delboyName", sWidth: '18%' },
								{ "data": "delboyEmail", sWidth: '17%' },
								{ "data": "delboyPhone", sWidth: '12%' },
								{ "data": "Edit", sWidth: '12%' },
								{ "data": "suxes_order", sWidth: '9%'},
								{ "data": "failed_order", sWidth: '9%'},
								{ "data": "pending_order", sWidth: '9%'}
								],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'right'})
					}
				});
		 $('#delboyName_search, #delboyEmail_search, #delboyPhone_search').keyup( function() {
			table.draw();
			//table.search( this.value ).draw();
		});
		$('#block_value1').click(function(){
			var radioValue = $("input[name='chk']:checked").val();
			var ord_transaction_id = '{{$ord_transaction_id}}';
            if(radioValue){
                $.ajax({
					type:'get',
					url :"{{url('reassign_order_to_delboy')}}",
					beforeSend: function() {
						$("#loading-image").show();
					},
					data:{'delboy_id':radioValue,'ord_transaction_id':ord_transaction_id},
					success:function(response){
						$("#loading-image").hide();
						//location.reload();
						window.location.href='{{url("rejected-order-delboy-dmgr")}}';
					}
				}); 
            }
		});
	});
</script>

	@endsection
@stop