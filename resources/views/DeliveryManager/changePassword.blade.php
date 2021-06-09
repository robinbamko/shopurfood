@extends('DeliveryManager.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
	<style>
		@media only screen and (max-width:767px)
		{
			.box-body{ padding:5px!important; }
		}
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
										<i class="fa fa-check-circle"></i>{{ Session::get('message') }}
									</div>
								@endif
								{{-- Add/Edit page starts--}}
								<div class="box-body spaced" style="padding:20px">

									<div class="">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => 'delivery-manager-change-password','id'=>'profile_form']) !!}
                                        <?php //print_r($getvendor); exit; ?>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_OLD_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_OLD_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_OLD_PASS')}}:</label>
											<div class="col-sm-6">
												{!! Form::input('password','old_pwd','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_OLD_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_OLD_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_OLD_PASS'),'id' => 'old_pwd','maxlength' => 15]) !!}
												@if ($errors->has('old_pwd') )
													<p class="error-block" style="color:red;">{{ $errors->first('old_pwd') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NEW_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NEW_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NEW_PASS')}}:</label>
											<div class="col-sm-6">
												{!! Form::input('password','new_pwd','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NEW_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NEW_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NEW_PASS'),'id' => 'new_pwd','maxlength' => 15]) !!}
												@if ($errors->has('new_pwd') )
													<p class="error-block" style="color:red;">{{ $errors->first('new_pwd') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CONFIRM_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CONFIRM_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CONFIRM_PASS')}}:</label>
											<div class="col-sm-6">
												{!! Form::input('password','conf_pwd','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CONFIRM_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CONFIRM_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CONFIRM_PASS'),'id' => 'conf_pwd','maxlength' => 15]) !!}
												@if ($errors->has('conf_pwd') )
													<p class="error-block" style="color:red;">{{ $errors->first('conf_pwd') }}</p>
												@endif
											</div>
										</div>


										<div class="form-group">
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
												@php $saveBtn=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE') @endphp
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											</div>

										</div>
										{!! Form::close() !!}
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
	<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
	<script>
        $("#profile_form").validate({
            rules: {
                old_pwd: "required",
                new_pwd: "required",
                conf_pwd: {
                    required: true,
                    equalTo: "#new_pwd"
                }
            },
            messages: {
                old_pwd: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_OLDPWD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_OLDPWD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_OLDPWD')}}",
                new_pwd: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NEWPWD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NEWPWD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_NEWPWD')}}",
                conf_pwd: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_CONPWD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_CONPWD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_CONPWD')}}"
            }
});
</script>
@endsection
@stop