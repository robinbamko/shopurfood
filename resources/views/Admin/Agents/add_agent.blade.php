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
								{{-- Add/Edit page starts--}}
								<div class="box-body spaced" style="padding:20px">
									<div id="location_form" class="panel-body">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => $action,'id'=>'profile_form','enctype'=>'multipart/form-data']) !!}
										<input type="hidden" name="gotId" value="{{ $id }}" />
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('agent_fname',$getvendor->agent_fname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME'),'id' => 'agent_fname','required','maxlength' => '100']) !!}
												@if ($errors->has('agent_fname') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_fname') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME')}}:</label>
											<div class="col-sm-6">
												{!! Form::text('agent_lname',$getvendor->agent_lname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME'),'id' => 'agent_lname','maxlength' => '100']) !!}
												@if ($errors->has('agent_lname') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_lname') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('agent_email',$getvendor->agent_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),'id' => 'agent_email','maxlength' => '100']) !!}
												{!! Form::Hidden('old_email',$getvendor->agent_email) !!}
												@if ($errors->has('agent_email') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_email') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('agent_phone1',$getvendor->agent_phone1,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO'),'class'=>'form-control col-md-7 col-xs-12','maxlength'=>'15','autocomplete'=>'off','id'=>'agent_phone1','onkeyup'=>'validate_phone(\'agent_phone1\');')) !!}

												@if ($errors->has('agent_phone1') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_phone1') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALTERNATIVE_NUMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALTERNATIVE_NUMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALTERNATIVE_NUMBER')}}:</label>
											<div class="col-sm-6">
												{!! Form::text('agent_phone2',$getvendor->agent_phone2,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALTERNATIVE_NUMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALTERNATIVE_NUMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALTERNATIVE_NUMBER'),'class'=>'form-control col-md-7 col-xs-12','maxlength'=>'15','autocomplete'=>'off','id'=>'agent_phone2','onkeyup'=>'validate_phone(\'agent_phone2\');')) !!}

												@if ($errors->has('agent_phone2') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_phone2') }}</p>
												@endif
											</div>
										</div>
										@if($getvendor->agent_currency_code!='')
											@php $curcode=$getvendor->agent_currency_code; @endphp
										@else
											@php $curcode=$default_currency; @endphp
										@endif
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE')}}:</label>
											<div class="col-sm-2">
												{!! Form::text('agent_currency_code',$curcode,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE'),'id' => 'agent_currency_code','readonly']) !!}

												@if ($errors->has('agent_currency_code') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_currency_code') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESPONSE_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESPONSE_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESPONSE_TIME')}} <span class="impt">*</span>:</label>
											<div class="col-sm-2">
												<div class="input-group clockpicker pull-center" data-placement="left" data-align="top" data-autoclose="true">
													{!! Form::text('agent_response_time',date('H:i',strtotime($getvendor->agent_response_time)),['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESPONSE_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESPONSE_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESPONSE_TIME'),'id' => 'agent_base_fare','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10',"data-placement"=>"right","data-align"=>"top","data-autoclose"=>"true"]) !!}
													<span class="input-group-addon">
													<span class="glyphicon glyphicon-time"></span>
												</span>
													@if ($errors->has('agent_response_time') )
														<p class="error-block" style="color:red;">{{ $errors->first('agent_response_time') }}</p>
													@endif
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BASE_FARE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BASE_FARE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BASE_FARE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-2">
												{!! Form::text('agent_base_fare',$getvendor->agent_base_fare,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_BASE_FARE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BASE_FARE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BASE_FARE'),'id' => 'agent_base_fare','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}

												@if ($errors->has('agent_base_fare') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_base_fare') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FARE_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_FARE_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												<div class="form-check form-check-inline">
													<label class="form-check-label">{!! Form::radio('agent_fare_type', 'per_km',($getvendor->agent_fare_type=='per_km') ? true : true)  !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PER_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_PER_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PER_KM')}} </label>
													<label class="form-check-label">{!! Form::radio('agent_fare_type', 'per_min',($getvendor->agent_fare_type=='per_min') ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PER_MIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_PER_MIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PER_MIN')}}</label>
												</div>
												@if ($errors->has('agent_fare_type') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_fare_type') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group" id="per_km_div" style="@if($getvendor->agent_fare_type!='per_km' && $getvendor->agent_fare_type!='') display:none @endif">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PER_KM_CHARGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PER_KM_CHARGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PER_KM_CHARGE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-2">
												{!! Form::text('agent_perkm_charge',$getvendor->agent_perkm_charge,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PER_KM_CHARGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PER_KM_CHARGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PER_KM_CHARGE'),'id' => 'agent_perkm_charge','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}

												@if ($errors->has('agent_perkm_charge') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_perkm_charge') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group" id="per_min_div" style="@if($getvendor->agent_fare_type!='per_min') display:none @endif">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PER_MIN_CHARGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PER_MIN_CHARGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PER_MIN_CHARGE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-2">
												{!! Form::text('agent_permin_charge',$getvendor->agent_permin_charge,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PER_MIN_CHARGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PER_MIN_CHARGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PER_MIN_CHARGE'),'id' => 'agent_permin_charge','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}

												@if ($errors->has('agent_permin_charge') )
													<p class="error-block" style="color:red;">{{ $errors->first('agent_permin_charge') }}</p>
												@endif
											</div>
										</div>


										{{--PAYNAMICS --}}

										<div class="row">
											<fieldset>
												<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS')}}</legend>
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:</label>
													<div class="col-sm-6">
														<div class="form-check form-check-inline">
															<label class="form-check-label">{!! Form::radio('mer_paynamics_status', 'Publish',($getvendor->mer_paynamics_status=='Publish') ? true : false)  !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
															<label class="form-check-label">{!! Form::radio('mer_paynamics_status', 'Unpublish',($getvendor->mer_paynamics_status=='Unpublish') ? true : true) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
														</div>
														@if ($errors->has('mer_paynamics_status') )
															<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_status') }}</p>
														@endif
													</div>
												</div>
												<div id="paynamicsDiv" style="@if($getvendor->mer_paynamics_status!='Publish') display:none @endif">
													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLIENTID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CLIENTID')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_paynamics_clientid',$getvendor->mer_paynamics_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLIENTID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CLIENTID'),'id' => 'mer_paynamics_clientid','maxlength'=>'100']) !!}
															@if ($errors->has('mer_paynamics_clientid') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_clientid') }}</p>
															@endif
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('admin_lang_file').'.ADMIN_SECRETID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SECRETID')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_paynamics_secretid',$getvendor->mer_paynamics_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('admin_lang_file').'.ADMIN_SECRETID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SECRETID'),'id' => 'mer_paynamics_secretid','maxlength'=>'100']) !!}
															@if ($errors->has('mer_paynamics_secretid') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_secretid') }}</p>
															@endif
														</div>
													</div>
													<input type="hidden" name="mer_paynamics_mode" id="mer_paynamics_mode"  value="Live" />
                                                    <?php /*
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PMTMODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PMTMODE')}}:</label>
													<div class="col-sm-6">
														<div class="form-check form-check-inline">
															<label class="form-check-label">{!! Form::radio('mer_paynamics_mode', 'Live',($getvendor->mer_paynamics_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LIVE')}} </label>
															<label class="form-check-label">{!! Form::radio('mer_paynamics_mode', 'Sandbox',($getvendor->mer_paynamics_mode=='Live') ? true : true) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('admin_lang_file').'.ADMIN_SANDBOX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SANDBOX')}}</label>
														</div>
														@if ($errors->has('mer_paynamics_mode') )
														<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_mode') }}</p>
														@endif
													</div>
												</div>*/ ?>
												</div>
											</fieldset>
										</div>
										{{--PAYMAYA --}}
										<div class="row">
											<fieldset>
												<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA')}}</legend>
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:</label>
													<div class="col-sm-6">
														<div class="form-check form-check-inline">
															<label class="form-check-label">{!! Form::radio('mer_paymaya_status', 'Publish',($getvendor->mer_paymaya_status=='Publish') ? true : false,['id' => 'mer_paymaya_status']) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
															<label class="form-check-label">{!! Form::radio('mer_paymaya_status', 'Unpublish',($getvendor->mer_paymaya_status=='Unpublish') ? true : true) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
														</div>
														@if ($errors->has('mer_paymaya_status') )
															<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_status') }}</p>
														@endif
													</div>
												</div>
												<div id="paymayaDiv" style="@if($getvendor->mer_paymaya_status!='Publish') display:none @endif">
													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLIENTID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CLIENTID')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_paymaya_clientid',$getvendor->mer_paymaya_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('admin_lang_file').'.ADMIN_CLIENTID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CLIENTID'),'id' => 'mer_paymaya_clientid','maxlength'=>'100']) !!}
															@if ($errors->has('mer_paymaya_clientid') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_clientid') }}</p>
															@endif
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('admin_lang_file').'.ADMIN_SECRETID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SECRETID')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_paymaya_secretid',$getvendor->mer_paymaya_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('admin_lang_file').'.ADMIN_SECRETID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SECRETID'),'id' => 'mer_paymaya_secretid','maxlength'=>'100']) !!}
															@if ($errors->has('mer_paymaya_secretid') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_secretid') }}</p>
															@endif
														</div>
													</div>
													<input type="hidden" name="mer_paymaya_mode" id="mer_paymaya_mode"  value="Live" />
                                                    <?php /*
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PMTMODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PMTMODE')}}:</label>
													<div class="col-sm-6">
														<div class="form-check form-check-inline">
															<label class="form-check-label">{!! Form::radio('mer_paymaya_mode', 'Live',($getvendor->mer_paymaya_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LIVE')}} </label>
															<label class="form-check-label">{!! Form::radio('mer_paymaya_mode', 'Sandbox',($getvendor->mer_paymaya_mode=='Sandbox') ? true : true) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('admin_lang_file').'.ADMIN_SANDBOX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SANDBOX')}}</label>
														</div>
														@if ($errors->has('mer_paymaya_mode') )
														<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_mode') }}</p>
														@endif
													</div>
												</div> */ ?>
												</div>
											</fieldset>
										</div>
										{{--NET BANKING --}}
										<div class="row">
											<fieldset>
												<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('admin_lang_file').'.ADMIN_NETBANKING') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NETBANKING')}}</legend>
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:</label>
													<div class="col-sm-6">
														<div class="form-check form-check-inline">
															<label class="form-check-label">{!! Form::radio('mer_netbank_status', 'Publish',($getvendor->mer_netbank_status=='Publish') ? true : false) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
															<label class="form-check-label">{!! Form::radio('mer_netbank_status', 'Unpublish',($getvendor->mer_netbank_status=='Unpublish') ? true : true) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
														</div>
														@if ($errors->has('mer_netbank_status') )
															<p class="error-block" style="color:red;">{{ $errors->first('mer_netbank_status') }}</p>
														@endif
													</div>
												</div>
												<div id="netBankDiv" style="@if($getvendor->mer_netbank_status!='Publish') display:none @endif">
													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANKNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANKNAME')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_bank_name',$getvendor->mer_bank_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANKNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANKNAME'),'id' => 'mer_bank_name','maxlength'=>'100']) !!}
															@if ($errors->has('mer_bank_name') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_bank_name') }}</p>
															@endif
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BRANCH')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_branch',$getvendor->mer_branch,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BRANCH'),'id' => 'mer_branch','maxlength'=>'100']) !!}
															@if ($errors->has('mer_branch') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_branch') }}</p>
															@endif
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.MER_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACCNO')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_bank_accno',$getvendor->mer_bank_accno,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACCNO'),'id' => 'mer_bank_accno','maxlength'=>'100']) !!}
															@if ($errors->has('mer_bank_accno') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_bank_accno') }}</p>
															@endif
														</div>
													</div>

													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IFSC')}}:</label>
														<div class="col-sm-6">
															{!! Form::text('mer_ifsc',$getvendor->mer_ifsc,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IFSC'),'id' => 'mer_ifsc','maxlength'=>'100']) !!}
															@if ($errors->has('mer_ifsc') )
																<p class="error-block" style="color:red;">{{ $errors->first('mer_ifsc') }}</p>
															@endif
														</div>
													</div>
												</div>
											</fieldset>
										</div>

										{{--LOCATION --}}
										<div class="row">
											<fieldset>
												<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDRESS')}}</legend>

												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOCATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOCATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOCATION')}}:</label>
													<div class="col-sm-6">
														<input type="hidden" name="street_number" id="street_number" />
														<input type="hidden" name="route" id="route" />
														<input type="hidden" name="postal_code" id="postal_code" />
														{!! Form::text('agent_location',$getvendor->agent_location,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOCATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOCATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOCATION'),'id' => 'agent_location','maxlength'=>'100']) !!}
														@if ($errors->has('agent_location') )
															<p class="error-block" style="color:red;">{{ $errors->first('agent_location') }}</p>
														@endif
													</div>
												</div>

												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('country',$getvendor->agent_country,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY'),'id' => 'country']) !!}
														@if ($errors->has('country') )
															<p class="error-block" style="color:red;">{{ $errors->first('country') }}</p>
														@endif
													</div>
												</div>

												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATE')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('agent_state',$getvendor->agent_state,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATE'),'id' => 'administrative_area_level_1']) !!}
														@if ($errors->has('agent_state') )
															<p class="error-block" style="color:red;">{{ $errors->first('agent_state') }}</p>
														@endif
													</div>
												</div>

												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CITY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CITY')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('agent_city',$getvendor->agent_city,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CITY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CITY'),'id' => 'locality']) !!}
														@if ($errors->has('agent_city') )
															<p class="error-block" style="color:red;">{{ $errors->first('agent_city') }}</p>
														@endif
													</div>
												</div>
											</fieldset>
										</div>
										<div class="row">
										<div class="form-group">
											<label class="col-sm-2"></label>
											<div class="col-sm-6">
												<!-- <div class="panel-heading  col-md-offset-2"> -->
											@if($id!='')
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
											@else
												@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
											@endif
											@php $url = url('manage-agent-admin')@endphp
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'onclick'=>"javascript:window.location.href='$url'"])!!}
											<!-- </div> -->
											</div>
										</div>
									</div>

										{!! Form::close() !!}
									</div>
								</div>
								{{-- Add page ends--}}

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
	<script src="https://maps.googleapis.com/maps/api/js?key={{$MAP_KEY}}&libraries=places&callback=initAutocomplete"   async defer></script>
	<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
	<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/public/css/bootstrap-clockpicker.min.css">
	<script type="text/javascript" src="<?php echo URL::to('/'); ?>/public/js/bootstrap-clockpicker.min.js"></script>
	<script type="text/javascript">
        $('.clockpicker').clockpicker()
            .find('input').change(function(){
            console.log(this.value);
        });

	</script>
	<script>
        $(document).ready(function(){
            $("input[name='agent_fare_type']").click(function(){
                if($(this).val()=='per_km')
                {
                    $('#per_km_div').show();
                    $('#per_min_div').hide();
                }
                else
                {
                    $('#per_km_div').hide();
                    $('#per_min_div').show();

                }
            });
            $("input[name='mer_paynamics_status']").click(function(){
                if($(this).val()=='Publish')
                {
                    $('#paynamicsDiv').show();
                }
                else
                {
                    $('#paynamicsDiv').hide();
                }
            });
            $("input[name='mer_paymaya_status']").click(function(){
                if($(this).val()=='Publish')
                {
                    $('#paymayaDiv').show();
                }
                else
                {
                    $('#paymayaDiv').hide();
                }
            });
            $("input[name='mer_netbank_status']").click(function(){
                if($(this).val()=='Publish')
                {
                    $('#netBankDiv').show();
                }
                else
                {
                    $('#netBankDiv').hide();
                }
            });
        });
        $.validator.addMethod("jsPhoneValidation", function(value, element) {
            var defaultDial = '{{Config::get('config_default_dial')}}';
            return value.substr(0, (defaultDial.trim().length)) != value.trim()
        }, "No space please and don't leave it empty");

        $("#profile_form").validate({
            //onkeyup: true,
            onfocusout: function (element) {
                this.element(element);
            },
            rules: {
                agent_fname: "required",
                agent_email: {
                    required: true,
                    email: true
                },
                //agent_password: "required",
                agent_phone1 : { jsPhoneValidation : true  },
                //agent_phone1: "required",
                agent_response_time: "required",
                agent_base_fare: "required",
                agent_fare_type: "required",
                agent_perkm_charge: {
                    required: {
                        depends: function(element) {
                            if($('input[name=agent_fare_type]:checked').val()=='per_km'){ return true; } else { return false; }
                        }
                    }
                },
                agent_permin_charge: {
                    required: {
                        depends: function(element) {
                            if($('input[name=agent_fare_type]:checked').val()=='per_min'){ return true; } else { return false; }
                        }
                    }
                },
                mer_paymaya_status: "required",
                mer_paymaya_clientid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_paymaya_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_paymaya_clientid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_paymaya_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_paymaya_secretid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_paymaya_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_paynamics_status: "required",
                mer_paynamics_clientid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_paynamics_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_paynamics_secretid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_paynamics_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_bank_name: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_branch: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_bank_accno: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                mer_ifsc: {
                    required: {
                        depends: function(element) {
                            if($('input[name=mer_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
            },
            messages: {
                agent_fname: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME')}}",
                /*agent_lname: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_LNAME')}}",*/
                agent_email: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
                /*agent_password: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_PASS')}}",*/
                agent_phone1: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
                agent_response_time: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_RESPONSE_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_RESPONSE_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_RESPONSE_TIME')}}",
                agent_base_fare: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BASE_FARE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BASE_FARE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BASE_FARE')}}",
                agent_fare_type: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_FARE_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_FARE_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_FARE_TYPE')}}",
                agent_perkm_charge: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_KM')}}",
                agent_permin_charge: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_MIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FARE_MIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FARE_MIN')}}",
                mer_paymaya_status:"{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_PAYMAYA_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_PAYMAYA_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_PAYMAYA_STATUS')}}",
                mer_paymaya_clientid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')}}",
                mer_paymaya_secretid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET')}}",
                mer_paynamics_status: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_PAYNAMICS_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_PAYNAMICS_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_PAYNAMICS_STATUS')}}",
                mer_paynamics_clientid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT')}}",
                mer_paynamics_secretid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')}}",
                mer_bank_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BANK')}}",
                mer_branch: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BRANCH')}}",
                mer_bank_accno: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ACCNO')}}",
                mer_ifsc: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_IFSC')}}",
            }
        });

        var placeSearch, autocomplete;
        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'long_name',
            country: 'long_name',
            postal_code: 'short_name'
        };

        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('agent_location')),
                {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                    console.log(addressType+' = '+val);
                }
            }
        }

        // Bias the autocomplete object to the user's geographical location,
        // as supplied by the browser's 'navigator.geolocation' object.
        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }

        $(document).ready(function(){
            var element = document.getElementById('agent_phone1');
            if(element.value=='')
            {
                $('#agent_phone1').val('{{$default_dial}}');
            }
            var element2 = document.getElementById('agent_phone2');
            if(element2.value=='')
            {
                $('#agent_phone2').val('{{$default_dial}}');
            }
        });

        // Get the modal
	</script>
	<script type="text/javascript">
        function onlyNumbersWithDot(e) {
            var charCode;
            if (e.keyCode > 0) {
                charCode = e.which || e.keyCode;
            }
            else if (typeof (e.charCode) != "undefined") {
                charCode = e.which || e.keyCode;
            }
            if (charCode == 46)
                return true
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
        function onlyNumbers(evt) {
            var e = event || evt; // for trans-browser compatibility
            var charCode = e.which || e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

	</script>
	<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
	<script type="text/javascript">
        $("#agent_phone1").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
        $("#agent_phone2").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

	</script>
@endsection
@stop