@extends('DeliveryManager.layouts.default')
@section('PageTitle')
		@if(isset($pagetitle))
			{{$pagetitle}}
		@endif
	@stop
@section('content')
<style type="text/css">
	.fieldsetrow{margin-left: 0px; margin-right: 0px;}

	@media only screen and (min-device-width : 768px) and (max-device-width : 1023px) {

		#availDivId input.form-control{width: 47% !important;}
	}
</style>
<!-- MAIN -->
<script src="<?php echo URL::to('/'); ?>/public/js/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/public/css/jquery.timepicker.css" />
<script src="<?php echo URL::to('/'); ?>/public/js/moment.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/public/js/datepair.js"></script>
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
										{!! Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AGENT_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AGENT_NAME'),['class' => 'control-label col-sm-2 require']) !!} 
										<div class="col-sm-6">
											@if(count($agents_list) > 0)
												{{ Form::select('deliver_agent_id',($agents_list),$getvendor->deliver_agent_id,['class' => 'form-control' , 'style' => 'width:100%'] ) }}
											@else
												{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NO_DETAILS') }}
											@endif 
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FIRST_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FIRST_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FIRST_NAME')}} <span class="impt">*</span>:</label>
										<div class="col-sm-6">
											{!! Form::text('deliver_fname',$getvendor->deliver_fname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FIRST_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FIRST_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FIRST_NAME'),'id' => 'deliver_fname','required','maxlength' => '100']) !!}
											@if ($errors->has('deliver_fname') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_fname') }}</p> 
											@endif
										</div>
									</div>
													
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LAST_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LAST_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LAST_NAME')}}:</label>
										<div class="col-sm-6">
											{!! Form::text('deliver_lname',$getvendor->deliver_lname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LAST_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LAST_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LAST_NAME'),'id' => 'deliver_lname','maxlength' => '100']) !!}
											@if ($errors->has('deliver_lname') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_lname') }}</p> 
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL')}} <span class="impt">*</span>:</label>
										<div class="col-sm-6">
											@if($getvendor->deliver_email == '')
												{!! Form::text('deliver_email',$getvendor->deliver_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL'),'id' => 'deliver_email','maxlength' => '100']) !!}
											@else
												{!! Form::text('deliver_email',$getvendor->deliver_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL'),'id' => 'deliver_email','maxlength' => '100', 'readonly']) !!}
											@endif
											{!! Form::Hidden('old_email',$getvendor->deliver_email) !!}
											@if ($errors->has('deliver_email') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_email') }}</p> 
											@endif
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MOBILENO')}} <span class="impt">*</span>:</label>
										<div class="col-sm-6">
											{!! Form::text('deliver_phone1',$getvendor->deliver_phone1,array('required','placeholder'=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MOBILENO'),'class'=>'form-control col-md-7 col-xs-12','maxlength'=>'15','autocomplete'=>'off','id'=>'deliver_phone1','onkeyup'=>'validate_phone(\'deliver_phone1\');')) !!}
											
											@if ($errors->has('deliver_phone1') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_phone1') }}</p> 
											@endif
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ALTER_MOBILE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ALTER_MOBILE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ALTER_MOBILE')}}:</label>
										<div class="col-sm-6">
											{!! Form::text('deliver_phone2',$getvendor->deliver_phone2,array('required','placeholder'=>(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ALTER_MOBILE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ALTER_MOBILE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ALTER_MOBILE'),'class'=>'form-control col-md-7 col-xs-12','maxlength'=>'15','autocomplete'=>'off','id'=>'deliver_phone2','onkeyup'=>'validate_phone(\'deliver_phone2\');')) !!}
											
											@if ($errors->has('deliver_phone2') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_phone2') }}</p> 
											@endif
										</div>
									</div>
									@if($getvendor->deliver_currency_code!='')
										@php $curcode=$getvendor->deliver_currency_code; @endphp
									@else
										@php $curcode=$default_currency; @endphp
									@endif
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CURR_CODE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CURR_CODE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CURR_CODE')}}:</label>
										<div class="col-sm-2">
											{!! Form::text('deliver_currency_code',$curcode,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CURR_CODE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CURR_CODE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CURR_CODE'),'id' => 'deliver_currency_code','readonly']) !!}
											
											@if ($errors->has('deliver_currency_code') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_currency_code') }}</p> 
											@endif
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_RESPONSE_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_RESPONSE_TIME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_RESPONSE_TIME')}} <span class="impt">*</span>:</label>
										<div class="col-sm-2">
											<div class="input-group clockpicker pull-center" data-placement="left" data-align="top" data-autoclose="true">
												{!! Form::text('deliver_response_time',date('H:i',strtotime($getvendor->deliver_response_time)),['class'=>'form-control','placeholder' => 'HH:MM','id' => 'agent_base_fare','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10',"data-placement"=>"right","data-align"=>"top","data-autoclose"=>"true"]) !!}
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-time"></span>
												</span>
												@if ($errors->has('deliver_response_time') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('deliver_response_time') }}</p> 
												@endif
											</div>
										</div>
										<div class="col-sm-3 icon-help no-left-pad">
											<a href="#" id="help-name" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_RES_TIME_HELP_TEXT')) ? trans(Session::get('DelMgr_lang_file').'.DEL_RES_TIME_HELP_TEXT') : trans($DELMGR_OUR_LANGUAGE.'.DEL_RES_TIME_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BASE_FARE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BASE_FARE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BASE_FARE')}} <span class="impt">*</span>:</label>
										<div class="col-sm-2">
											{!! Form::text('deliver_base_fare',$getvendor->deliver_base_fare,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_BASE_FARE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_BASE_FARE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_BASE_FARE'),'id' => 'deliver_base_fare','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}
											
											@if ($errors->has('deliver_base_fare') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_base_fare') }}</p> 
											@endif
											<span class="help-block">{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_SET_ZERO')) ? trans(Session::get('DelMgr_lang_file').'.DEL_SET_ZERO') : trans($DELMGR_OUR_LANGUAGE.'.DEL_SET_ZERO')}}</span>
										</div>
										<div class="col-sm-3 icon-help no-left-pad">
											<a href="#" id="help-name" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_BASE_FARE_HELP_TEXT')) ? trans(Session::get('DelMgr_lang_file').'.DEL_BASE_FARE_HELP_TEXT') : trans($DELMGR_OUR_LANGUAGE.'.DEL_BASE_FARE_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FARE_TYPE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FARE_TYPE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PUBLISH_STATUS')}} <span class="impt">*</span>:</label>
										<div class="col-sm-6">
											<div class="form-check form-check-inline">
												<label class="form-check-label">{!! Form::radio('deliver_fare_type', 'per_km',($getvendor->deliver_fare_type=='per_km') ? true : true)  !!} {{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM')}} </label>
												<label class="form-check-label">{!! Form::radio('deliver_fare_type', 'per_min',($getvendor->deliver_fare_type=='per_min') ? true : false) !!}{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN')}}</label>
												<span><i class="fa fa-info-circle tooltip-demo" title="{{(Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_FARE_INFO')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_FARE_INFO') : trans($DELMGR_OUR_LANGUAGE.'.ADMIN_FARE_INFO')}}"></i> </span>
											</div>
											@if ($errors->has('deliver_fare_type') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_fare_type') }}</p> 
											@endif
										</div>
									</div>
									<div class="form-group" id="per_km_div" style="@if($getvendor->deliver_fare_type!='per_km' && $getvendor->deliver_fare_type!='') display:none @endif">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM_CHARGE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM_CHARGE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM_CHARGE')}} <span class="impt">*</span>:</label>
										<div class="col-sm-2">
											{!! Form::text('deliver_perkm_charge',$getvendor->deliver_perkm_charge,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM_CHARGE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_KM_CHARGE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PER_KM_CHARGE'),'id' => 'deliver_perkm_charge','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}
											
											@if ($errors->has('deliver_perkm_charge') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_perkm_charge') }}</p> 
											@endif
											<span class="help-block">{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_SET_ZERO')) ? trans(Session::get('DelMgr_lang_file').'.DEL_SET_ZERO') : trans($DELMGR_OUR_LANGUAGE.'.DEL_SET_ZERO')}}</span>
										</div>

									</div>
									<div class="form-group" id="per_min_div" style="@if($getvendor->deliver_fare_type!='per_min') display:none @endif">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN_CHARGE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN_CHARGE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN_CHARGE')}} <span class="impt">*</span>:</label>
										<div class="col-sm-2">
											{!! Form::text('deliver_permin_charge',$getvendor->deliver_permin_charge,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN_CHARGE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PER_MIN_CHARGE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PER_MIN_CHARGE'),'id' => 'deliver_permin_charge','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}
											
											@if ($errors->has('deliver_permin_charge') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_permin_charge') }}</p> 
											@endif
											<span class="help-block">{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_SET_ZERO')) ? trans(Session::get('DelMgr_lang_file').'.DEL_SET_ZERO') : trans($DELMGR_OUR_LANGUAGE.'.DEL_SET_ZERO')}}</span>
										</div>
									</div>
									
									<!--VEHICLE OPTION-->
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_VEHICLE_OPTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_VEHICLE_OPTION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_VEHICLE_OPTION')}} <span class="impt">*</span>:</label>
										<div class="col-sm-2">
											@php $vehicles_list = vehicle_option(); @endphp
											@if(count($vehicles_list) > 0)
												{{ Form::select('deliver_vehicle_details',($vehicles_list),$getvendor->deliver_vehicle_details,['class' => 'form-control' , 'style' => 'width:100%'] ) }}
											@else
												{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_NO_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_NO_DETAILS') }}
											@endif 
											@if ($errors->has('deliver_vehicle_details') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_vehicle_details') }}</p> 
											@endif
										</div>
									</div>
									<!--ORDER LIMIT-->
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_LIMIT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_LIMIT')}} <span class="impt">*</span>:</label>
										<div class="col-sm-2">
											{!! Form::text('deliver_order_limit',$getvendor->deliver_order_limit,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ORDER_LIMIT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ORDER_LIMIT'),'id' => 'deliver_order_limit','onkeypress'=>"return onlyNumbers(event);",'maxlength'=>'10']) !!}
											
											@if ($errors->has('deliver_order_limit') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('deliver_order_limit') }}</p> 
											@endif
										</div>
										<div class="col-sm-3 icon-help no-left-pad">
											<a href="#" id="help-name" data-placement="right" data-toggle="tooltip" title="" data-original-title="{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_OR_LIM_HELP_TEXT')) ? trans(Session::get('DelMgr_lang_file').'.DEL_OR_LIM_HELP_TEXT') : trans($DELMGR_OUR_LANGUAGE.'.DEL_OR_LIM_HELP_TEXT')}}"><span class="glyphicon glyphicon-question-sign"></span></a>
										</div>
									</div>
									{{--  uploaded document --}}
									@if($id != '')
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_LICENCE')) ? trans(Session::get('DelMgr_lang_file').'.DEL_LICENCE') : trans($DELMGR_OUR_LANGUAGE.'.DEL_LICENCE')}}:</label>
										<div class="col-sm-2">
											@if($getvendor->deliver_licence != '')
											 @php $filename = public_path('images/delivery_person/').$getvendor->deliver_licence; @endphp
											 @if(file_exists($filename))
													{{-- Form::image(url('public/images/delivery_person/'.$getvendor->deliver_licence), 'alt text', array('class' => '','width'=>'50px','height'=>'50px')) --}}
													<a href="{{url('public/images/delivery_person/'.$getvendor->deliver_licence)}}" download>{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_DWLD')) ? trans(Session::get('DelMgr_lang_file').'.DEL_DWLD') : trans($DELMGR_OUR_LANGUAGE.'.DEL_DWLD')}}</a>
											 @endif
											@else
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_NOT_YET_UP')) ? trans(Session::get('DelMgr_lang_file').'.DEL_NOT_YET_UP') : trans($DELMGR_OUR_LANGUAGE.'.DEL_NOT_YET_UP')}}
											@endif
											
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_ADDR_PROOF')) ? trans(Session::get('DelMgr_lang_file').'.DEL_ADDR_PROOF') : trans($DELMGR_OUR_LANGUAGE.'.DEL_ADDR_PROOF')}}:</label>
										<div class="col-sm-2">
											@if($getvendor->deliver_address_proof != '')
											 @php $filename = public_path('images/delivery_person/').$getvendor->deliver_address_proof; @endphp
											 @if(file_exists($filename))
													{{-- Form::image(url('public/images/delivery_person/'.$getvendor->deliver_address_proof), 'alt text', array('class' => '','width'=>'50px','height'=>'50px')) --}}
													<a href="{{url('public/images/delivery_person/'.$getvendor->deliver_address_proof)}}" download>{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_DWLD')) ? trans(Session::get('DelMgr_lang_file').'.DEL_DWLD') : trans($DELMGR_OUR_LANGUAGE.'.DEL_DWLD')}}</a>
											 @endif
											@else
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_NOT_YET_UP')) ? trans(Session::get('DelMgr_lang_file').'.DEL_NOT_YET_UP') : trans($DELMGR_OUR_LANGUAGE.'.DEL_NOT_YET_UP')}}
											@endif
											
										</div>
									</div>
									@endif
									{{--LOCATION --}}
									<div class="row fieldsetrow">
										<fieldset>
											<legend>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ADDRESS')}}</legend>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOCATION')}}:</label>
												<div class="col-sm-6">
													<input type="hidden" name="street_number" id="street_number" />
													<input type="hidden" name="route" id="route" />
													<input type="hidden" name="postal_code" id="postal_code" />
													{!! Form::text('deliver_location',$getvendor->deliver_location,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOCATION'),'id' => 'deliver_location','maxlength'=>'100']) !!}
													@if ($errors->has('deliver_location') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('deliver_location') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COUNTRY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COUNTRY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_COUNTRY')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('country',$getvendor->deliver_country,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COUNTRY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COUNTRY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_COUNTRY'),'id' => 'country']) !!}
													@if ($errors->has('country') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('country') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_STATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_STATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_STATE')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('deliver_state',$getvendor->deliver_state,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_STATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_STATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_STATE'),'id' => 'administrative_area_level_1']) !!}
													@if ($errors->has('deliver_state') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('deliver_state') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CITY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CITY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CITY')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('deliver_city',$getvendor->deliver_city,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CITY')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CITY') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CITY'),'id' => 'locality']) !!}
													@if ($errors->has('deliver_city') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('deliver_city') }}</p> 
													@endif
												</div>
											</div>
										</fieldset>
									</div>
									<div class="form-group">
										<label class="col-sm-2"></label>
										<div class="col-sm-6">
											<!-- <div class="panel-heading text-center"> -->
										@if($id!='')
											@php $saveBtn = (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE') @endphp
										@else
											@php $saveBtn=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SAVE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SAVE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SAVE') @endphp
										@endif
										@php $url = url('manage-deliveryboy')@endphp
										{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
										{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'onclick'=>"javascript:window.location.href='$url'"])!!}
									<!-- </div> -->
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
		$("input[name='deliver_fare_type']").click(function(){
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
		
		$('#availDivId .time').timepicker({
											'showDuration': true,
											'timeFormat': 'g:ia'
										});
										
		var timeOnlyExampleEl = document.getElementById('availDivId');
		var timeOnlyDatepair = new Datepair(timeOnlyExampleEl);
		
		$.validator.addMethod("valueNotEquals", function(value, element, arg){
		  return arg !== value;
		 }, "Value must not equal arg.");
	});
	$.validator.addMethod("jsPhoneValidation", function(value, element) { 
		var defaultDial = '{{$default_dial}}';
		return value.substr(0, (defaultDial.trim().length)) != value.trim()
	}, "No space please and don't leave it empty");
	$("#profile_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			deliver_agent_id: { valueNotEquals: "0" },
			deliver_fname : "required",
			deliver_email: {
				required: true,
				email: true
			},
			//agent_password: "required",
			//deliver_phone1: "required",
			deliver_phone1 : { jsPhoneValidation : true  },
			deliver_response_time: "required",
			deliver_base_fare: "required",
			deliver_fare_type: "required",
			deliver_perkm_charge: {
				required: {
					depends: function(element) {
						if($('input[name=deliver_fare_type]:checked').val()=='per_km'){ return true; } else { return false; } 
					}
				}
			},
			deliver_permin_charge: {
				required: {
					depends: function(element) {
						if($('input[name=deliver_fare_type]:checked').val()=='per_min'){ return true; } else { return false; } 
					}
				}
			},
			deliver_vehicle_details:"required",
			deliver_order_limit:"required",
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
			deliver_agent_id : "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SEL_AGENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SEL_AGENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SEL_AGENT')}}",
			deliver_fname : "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FNAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FNAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FNAME')}}",
			/*deliver_lname: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_LNAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_LNAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_LNAME')}}",*/
			deliver_email: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_EMAIL')}}",
			/*agent_password: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTR_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTR_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTR_PASS')}}",*/
			deliver_phone1: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PHONE')}}",
			deliver_response_time: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RESPONSE_TIME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_RESPONSE_TIME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_RESPONSE_TIME')}}",
			deliver_base_fare: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BASE_FARE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BASE_FARE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_BASE_FARE')}}",
			deliver_fare_type: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SEL_FARE_TYPE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SEL_FARE_TYPE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SEL_FARE_TYPE')}}",
			deliver_perkm_charge: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_KM')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_KM') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FARE_KM')}}",
			deliver_permin_charge: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_MIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_FARE_MIN') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_FARE_MIN')}}",
			deliver_vehicle_details:"{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELVEHICLE_OPTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELVEHICLE_OPTION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELVEHICLE_OPTION')}}",
			deliver_order_limit:"{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTERORDER_LIMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTERORDER_LIMIT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTERORDER_LIMIT')}}",		
			mer_paymaya_status:"{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SEL_PAYMAYA_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SEL_PAYMAYA_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SEL_PAYMAYA_STATUS')}}",
			mer_paymaya_clientid: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYMA_CLIENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYMA_CLIENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PAYMA_CLIENT')}}",
			mer_paymaya_secretid: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYMA_SECRET')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYMA_SECRET') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PAYMA_SECRET')}}",
			mer_paynamics_status: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SEL_PAYNAMICS_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SEL_PAYNAMICS_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SEL_PAYNAMICS_STATUS')}}",
			mer_paynamics_clientid: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYNA_CLIENT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYNA_CLIENT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PAYNA_CLIENT')}}",
			mer_paynamics_secretid: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYNA_SECRET')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PAYNA_SECRET') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PAYNA_SECRET')}}",
			mer_bank_name: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BANK')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BANK') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_BANK')}}",
			mer_branch: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BRANCH')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_BRANCH') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_BRANCH')}}",
			mer_bank_accno: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_ACCNO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_ACCNO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_ACCNO')}}",
			mer_ifsc: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_IFSC')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_IFSC') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_IFSC')}}",
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
            /** @type {!HTMLInputElement} */(document.getElementById('deliver_location')),
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
		var element = document.getElementById('deliver_phone1');
		if(element.value=='')
		{
			$('#deliver_phone1').val('{{$default_dial}}');
		}
		var element2 = document.getElementById('deliver_phone2');
		if(element2.value=='')
		{
			$('#deliver_phone2').val('{{$default_dial}}');
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
    $("#deliver_phone1").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
    $("#deliver_phone2").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

   </script>
   <script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	});
	</script>
@endsection
@stop




