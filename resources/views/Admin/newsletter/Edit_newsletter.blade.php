@extends('Admin.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
	<!-- MAIN -->
	<style type="text/css">

		.input-group {
			width: 110px;
			margin-bottom: 10px;
		}


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
								<div class="panel-heading p__title">
									{{$pagetitle}}

								</div>
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
								
								{!! Form::open(['method' => 'any','url'=>'send_newsletter','id'=>'demo-form2','class'=>'form-horizontal form-label-left','data-parsley-validate']) !!}
								{{-- SEND NEWSLETTER STARTS --}}
								<div class="box-body spaced" style="padding:20px">
									<div id="location_form" class="panel-body">
										<div class="row-fluid well">
										<div class="form-group">
											<label class="control-label col-sm-2" for="subadmin_name">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL_TO')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL_TO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL_TO')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												
												{{ Form::radio('email_to',1,false, ['required','class'=>'field','onclick' => 'check_user_type(0)']) }}
												{{ Form::label('All Users',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALL_USER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALL_USER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALL_USER')) }}
												{{ Form::radio('email_to',1,false, ['required','class'=>'field','onclick' => 'check_user_type(1)']) }}
												{{ Form::label('Particular User',(Lang::has(Session::get('admin_lang_file').'.ADMIN_PARTICULAR_USER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PARTICULAR_USER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PARTICULAR_USER')) }}
												{{ Form::radio('email_to',1,false, ['required','class'=>'field','onclick' => 'check_user_type(2)']) }}
												{{ Form::label('Subscriber',(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBSCRIBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBSCRIBER')) }}
								
												@if ($errors->has('email_to') )
													<p class="error-block" style="color:red;">{{ $errors->first('email_to') }}</p>
												@endif
											</div>
										</div>
										
										<div class="form-group" id="users_list" style="display:none;">
											<label class="control-label col-sm-2"></label>
											<div class="col-sm-6">
												@if(count($customer_details)>0)
													<select name="user_id[]" id="multi_user_select" class="select2_multiple form-control" multiple ="multiple" >
														@foreach($customer_details as $cus)
															<option value="{{ $cus->cus_id }}">{{$cus->cus_fname}} - {{$cus->cus_email}}</option>
														@endforeach
													</select>
												@else 
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_USER_NOT_FOUND')) ? trans(Session::get('admin_lang_file').'.ADMIN_USER_NOT_FOUND') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_USER_NOT_FOUND')}}
												@endif
											</div>
										</div>
										
										<div class="form-group" id="subscriber_list" style="display:none;">
											<label class="control-label col-sm-2"></label>
											<div class="col-sm-6">
												@if(count($subscriber_details)>0)
													<select name="subscriber_id[]" id="multi_user_select" class="select2_multiple form-control" multiple ="multiple" >
														@foreach($subscriber_details as $cus)
															<option value="{{ $cus->cus_id }}">{{$cus->cus_fname}} - {{$cus->cus_email}}</option>
														@endforeach
													</select>
												@else 
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_SUBSCRIBER_AVAILABEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_SUBSCRIBER_AVAILABEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_SUBSCRIBER_AVAILABEL')}}
												@endif
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="newsletter_subject">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBJECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBJECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBJECT')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('newsletter_subject','',['autocomplete'=>'nope','class'=>'form-control','maxlength'=>'50','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBJECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBJECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBJECT'),'id' => 'newsletter_subject','required','maxlength' => '100']) !!}
												@if ($errors->has('newsletter_subject') )
													<p class="error-block" style="color:red;">{{ $errors->first('newsletter_subject') }}</p>
												@endif
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="newsletter_message">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MESSAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MESSAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MESSAGE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::textarea('newsletter_message',null,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MESSAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MESSAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MESSAGE'),'class'=>'form-control col-md-7 col-xs-12')) !!}
												@if ($errors->has('newsletter_message') )
													<p class="error-block" style="color:red;">{{ $errors->first('newsletter_message') }}</p>
												@endif
											</div>
										</div>
										
										
									<div class="form-group">
											<label class="control-label col-sm-2"></label>
										
										<div class="col-sm-6">
											{{ Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_SUBMIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SUBMIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SUBMIT'),array('class'=>'btn btn-success')) }}
											{{ Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_RESET')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESET'),array('class'=>'btn btn-success')) }}
										</div>
									</div>
												
										{!! Form::close() !!}
										</div>
									</div>
								</div>
								{{-- SEND NEWSLETTER END --}}

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

@endsection
@stop