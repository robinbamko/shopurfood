@extends('Admin.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
<style>
	.datepicker-dropdown{z-index: 1030 !important;}
</style>

	<div class="main">
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<h1 class="page-header">{{$pagetitle}}</h1>
			<div class="container-fluid add-country">
				<div class="row">
					<div class="container right-container">
						<div class="r-btn">
						</div>
						<div class="col-md-12">
							<div class="location panel">
								{{-- Display error message--}}
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
								@if (Session::has('message'))
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
										{{ Session::get('message') }}
									</div>
								@endif
								
								{!! Form::open(['method' => 'any','url'=>'download_delboy_earning_rpt','id'=>'demo-form2','class'=>'form-horizontal form-label-left','data-parsley-validate']) !!}
								{{-- SEND NEWSLETTER STARTS --}}
								<div class="box-body spaced" style="padding:10px">
									<div id="location_form" class="panel-body">
										<div class="">
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SL_DEL_BOY')) ? trans(Session::get('admin_lang_file').'.ADMIN_SL_DEL_BOY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SL_DEL_BOY')}} :</label>

												<div class="col-sm-4">
													@if(count($delivery_list) > 0)

														{{ Form::select('deliver_id[]',$delivery_list,'',['class' => 'form-control select2' , 'style' => 'width:100%','id' => 'deliver_id','multiple'=>'multiple'] ) }}

													@else
														{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DELIVERY_BOY_FOUND')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DELIVERY_BOY_FOUND') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DELIVERY_BOY_FOUND') }}
													@endif
													@if ($errors->has('deliver_id') )
														<p class="error-block" style="color:red;">{{ $errors->first('deliver_id') }}</p>
													@endif
												</div>
												
											</div>
											<div class="form-group">
												<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.MER_FROM_DATE')) ? trans(Session::get('admin_lang_file').'.MER_FROM_DATE') : trans($ADMIN_OUR_LANGUAGE.'.MER_FROM_DATE')}}</label>
												<div class="col-md-4">
													{!! Form::text('from_date','',['class'=>'form-control','placeholder' => 'MM/DD/YYYY','id' => 'startDatePicker','required'=>"required" ,'readonly'=>"readonly"]) !!}
												</div>
												<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.MER_TO_DATE')) ? trans(Session::get('admin_lang_file').'.MER_TO_DATE') : trans($ADMIN_OUR_LANGUAGE.'.MER_TO_DATE')}}</label>
												<div class="col-md-4">
													{!! Form::text('to_date','',['class'=>'form-control','placeholder' => 'MM/DD/YYYY','id' => 'endDatePicker','required'=>"required" ,'readonly'=>"readonly"]) !!}
												</div>
											</div> 
											
											<div class="form-group">
												<label class="control-label col-sm-2"></label>
												<div class="col-sm-6">
													{{ Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWLD')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWLD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWLD'),array('class'=>'btn btn-success')) }}
													{{ Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_RESET')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESET'),array('class'=>'btn btn-info','onclick'=>'javascript:window.location.href="'.url('delboy_earning_report').'";')) }}
												</div>
											</div>
												
										{!! Form::close() !!}
										</div>
									</div>
								</div>
								
							</div>
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
<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap-datepicker.css">
<script src="{{url('')}}/public/admin/assets/scripts/bootstrap-datepicker.js"></script>

<script type="text/javascript">
// check user type ,all user or particular user
$(function(){	
	$("#startDatePicker").datepicker({
		todayBtn:  1,
		autoclose: true,
	}).on('changeDate', function (selected) {
		var minDate = new Date(selected.date.valueOf());
		$('#endDatePicker').datepicker('setStartDate', minDate);
	});

	$("#endDatePicker").datepicker().on('changeDate', function (selected) {
		var maxDate = new Date(selected.date.valueOf());
		$('#startDatePicker').datepicker('setEndDate', maxDate);
	});
});	
function getResult(gotRes){
	/*var store_id = $("input[name='pro_store_id[]']:checked").map(function () { return $(this).val(); }).get();
	var from_date= $('#startDatePicker').val();
	var store_id = $("input[name='pro_store_id[]']:checked").map(function () { return $(this).val(); }).get();*/
	$('#demo-form2').attr('action','download_order_rpt').submit();
}
</script>
@endsection
@stop