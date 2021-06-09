@extends('Admin.layouts.default')
@section('PageTitle')
		@if(isset($pagetitle))
			{{$pagetitle}}
		@endif
	@stop
@section('content')
<!-- MAIN -->
<style>

.input-group-text {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
	padding: 8px 3px;
    margin-bottom: 0;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: .25rem;
}
.input-group-append {
    display: flex;
	margin-left: -1px;
}
.input-group>.input-group-append>.btn, .input-group>.input-group-append>.input-group-text, .input-group>.input-group-prepend:first-child>.btn:not(:first-child), .input-group>.input-group-prepend:first-child>.input-group-text:not(:first-child), .input-group>.input-group-prepend:not(:first-child)>.btn, .input-group>.input-group-prepend:not(:first-child)>.input-group-text{
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
input#mer_commission {
    float: left;
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
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
								<i class="fa fa-times-circle"></i>{{ Session::get('message') }}
							</div>
							@endif
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:20px">
								<div id="location_form" class="panel-body">
									{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => $action,'id'=>'profile_form','enctype'=>'multipart/form-data']) !!}
									<input type="hidden" name="gotId" id="gotId" value="{{ $id }}" />
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME')}}*:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_fname',$getvendor->mer_fname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_FIRST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FIRST_NAME'),'id' => 'mer_fname','required','maxlength' => '100']) !!}
											@if ($errors->has('mer_fname') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_fname') }}</p> 
											@endif
										</div>
									</div>
													
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME')}}*:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_lname',$getvendor->mer_lname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_NAME'),'id' => 'mer_lname','maxlength' => '100']) !!}
											@if ($errors->has('mer_lname') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_lname') }}</p> 
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL')}}*:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_email',$getvendor->mer_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),'id' => 'mer_email','maxlength' => '100']) !!}
											{!! Form::Hidden('old_email',$getvendor->mer_email) !!}
											@if ($errors->has('mer_email') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_email') }}</p> 
											@endif
										</div>
									</div>
									@if($action == "update-merchant")
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD')}}*:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_password',$getvendor->mer_decrypt_password,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD'),'id' => 'mer_password','maxlength' => '10']) !!}
											{!! Form::hidden('old_password',$getvendor->mer_decrypt_password) !!}
											@if ($errors->has('mer_password') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_password') }}</p> 
											@endif
										</div>
									</div>
									@endif
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO')}} * :</label>
										<div class="col-sm-6">
											{!! Form::text('mer_phone',$getvendor->mer_phone,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO'),'class'=>'form-control col-md-7 col-xs-12','maxlength'=>'15','autocomplete'=>'off','id'=>'mer_phone','onkeyup'=>'validate_phone(\'mer_phone\');')) !!}
											
											@if ($errors->has('mer_phone') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_phone') }}</p> 
											@endif
										</div>
									</div>
									@if($getvendor->mer_currency_code!='')
										@php $curcode=$getvendor->mer_currency_code; @endphp
									@else
										@php $curcode=$default_currency; @endphp
									@endif
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE')}}:</label>
										<div class="col-sm-2">
											{!! Form::text('mer_currency_code',$curcode,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CURR_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CURR_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CURR_CODE'),'id' => 'mer_currency_code','readonly']) !!}
											
											@if ($errors->has('mer_currency_code') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_currency_code') }}</p> 
											@endif
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BUSINESS_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BUSINESS_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BUSINESS_TYPE')}} * :</label>
										<div class="col-sm-6">
											@php $business_type = array(''=>'Select business type','1'=>'Store','2'=>'Restaurant'); @endphp
											@if($getvendor->mer_business_type == '')
											{{--{{ Form::select('mer_business_type',($business_type),$getvendor->mer_business_type,['class' => 'form-control' ] )}}--}}
												{{ Form::text('mer_business_type_name','Restaurant',['class' => 'form-control','readonly']) }}
												{{ Form::hidden('mer_business_type','2',['class' => 'form-control','readonly']) }}

											@else
												@php $type = ($getvendor->mer_business_type == '1') ? "Store" : "Restaurant"; @endphp
											{{ Form::text('mer_business_type',$type,['class' => 'form-control','readonly'])}}
											@endif
											{{--{{ Form::text('mer_business_type','Restaurant',['class' => 'form-control','readonly']) }}--}}
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMMISSION')}} In (%) *:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_commission',$getvendor->mer_commission,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_COMMISSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COMMISSION'),'id' => 'mer_commission','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength'=>'10']) !!}
											<input type="hidden" id="oldcomssion" value="{{$getvendor->mer_commission}}">
											<input id="commoncommission" name="commoncommission" type="checkbox" value="1" class="valid" aria-invalid="false" @if($getvendor->mer_comissionstatus==1) checked @endif>
											<label for="commoncommission">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_USE_COMMON_COMMISION')) ? trans(Session::get('admin_lang_file').'.ADMIN_USE_COMMON_COMMISION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_USE_COMMON_COMMISION')}}</label>
											
											@if ($errors->has('mer_commission') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_commission') }}</p> 
											@endif
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_POLICY')}}:</label>
										<div class="col-sm-6">
											{!! Form::textarea('mer_cancel_policy',$getvendor->mer_cancel_policy,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_POLICY'),'id' => 'mer_cancel_policy']) !!}
											@if ($errors->has('mer_cancel_policy') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_cancel_policy') }}</p> 
											@endif
										</div>
									</div>

									@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
											@php $cancel_policy = 'mer_cancel_policy_'.$lang->lang_code @endphp
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_POLICY')}}&nbsp; (In {{$lang->lang_name}}):</label>
												<div class="col-sm-6">
													{!!Form::textarea('mer_cancel_policy_'.$lang->lang_code.'',$getvendor->$cancel_policy,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_POLICY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_POLICY'),'class'=>'form-control')) !!}
												</div>
											</div>
										@endforeach
									@endif


									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Id_PROOF')) ? trans(Session::get('admin_lang_file').'.ADMIN_Id_PROOF') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Id_PROOF')}}<span class="impt">*</span>:</label>
										<div class="col-sm-6">
											{!! Form::file('idproof',array('class'=>'form-control','id' => 'idproof','accept'=>'image/*','onchange'=>'Upload(this.id,"300","300","500","500");')) !!}
											<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Id_PROOF_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_Id_PROOF_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Id_PROOF_IMG_VAL')}}</p>
											{!! Form::hidden('old_idproof',$getvendor->idproof) !!}

											@if($getvendor->idproof != '')
												{{ Form::image(url('public/images/merchant/'.$getvendor->idproof), 'alt text', array('class' => '','width'=>'100px','height'=>'100px','id' => 'idproof')) }}
											@endif
										</div>
									</div>
									<!--ORDER LIMIT-->
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RES_LICENCE')) ? trans(Session::get('admin_lang_file').'.ADMIN_RES_LICENCE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RES_LICENCE')}}<span class="impt">*</span>:</label>
										<div class="col-sm-6">
											{!! Form::file('license',array('class'=>'form-control','id' => 'license','accept'=>'image/*','onchange'=>'Upload(this.id,"300","300","500","500");')) !!}
											<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REST_LIC_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_REST_LIC_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REST_LIC_IMG_VAL')}}</p>
											{!! Form::hidden('old_license',$getvendor->license) !!}
											@if($getvendor->license != '')
												{{ Form::image(url('public/images/merchant/'.$getvendor->license), 'alt text', array('class' => '','width'=>'100px','height'=>'100px','id' => 'license')) }}
											@endif
										</div>
									</div>
									<input type="hidden" name="refund_status" id="refund_status" value="Yes" />
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCEL_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCEL_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CANCEL_STATUS')}}* :</label>
										<div class="col-sm-6">
											{{ Form::radio('cancel_status','Yes',(($getvendor->cancel_status == 'Yes') ? true : ''),['id' => 'enable5','required']) }}
											{{ Form::label('enable5',(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALLOW')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALLOW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALLOW')) }}
											{{ Form::radio('cancel_status','No',(($getvendor->cancel_status == 'No') ? true : '') ,['id' => 'disable5','required']) }}
											{{ Form::label('disable5',(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_ALLOW')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOT_ALLOW') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_ALLOW')) }}
											<div class="radioError"></div>
											@if ($errors->has('cancel_status') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('cancel_status') }}</p> 
											@endif
										</div>
									</div>
									<?php /* 
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_REFUND_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REFUND_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REFUND_STATUS')}}*:</label>
										<div class="col-sm-6">
											<div class="form-check form-check-inline">
												<label class="form-check-label">{!! Form::radio('refund_status', 'Yes',(($getvendor->refund_status == 'Yes') ? true : '')) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}} </label>
												<label class="form-check-label">{!! Form::radio('refund_status', 'No',(($getvendor->refund_status == 'No') ? true : '')) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}</label>
												<div class="radioError"></div>
												@if ($errors->has('refund_status') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('refund_status') }}</p> 
												@endif
											</div>
										</div>
									</div>
									*/ ?>
																	
								
									{{--<div class="row">
										<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMENT_SETTINGS')}}:</label>
										<div class="col-sm-6">
											<div class="form-check form-check-inline">
												<label class="form-check-label">{!! Form::checkbox('mer_payment_settings[]', 'paynamics',(in_array('paynamics',explode(",",$getvendor->mer_payment_settings))) ? true : false,['onchange'=>'callme()'])  !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS')}} </label>
												<label class="form-check-label">{!! Form::checkbox('mer_payment_settings[]', 'paynamics',(in_array('paynamics',explode(",",$getvendor->mer_payment_settings))) ? true : false,['onchange'=>'callme()'])  !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA')}} </label>
												<label class="form-check-label">{!! Form::checkbox('mer_payment_settings[]', 'paynamics',(in_array('paynamics',explode(",",$getvendor->mer_payment_settings))) ? true : false,['onchange'=>'callme()'])  !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('admin_lang_file').'.ADMIN_NETBANKING') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NETBANKING')}} </label>
											</div>
										</div>
									</div>--}}
									{{--PAYNAMICS --}}
										
									<div class="row">
										<fieldset>
											<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS')}}</legend>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paynamics_status', 'Publish',($getvendor->mer_paynamics_status=='Publish') ? true : false)  !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paynamics_status', 'Unpublish',(($getvendor->mer_paynamics_status=='Unpublish' || $getvendor->mer_paynamics_status == '')) ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
													</div>
													@if ($errors->has('mer_paynamics_status') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_status') }}</p> 
													@endif
												</div>
											</div>
											
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
											<input type="hidden" name="mer_paynamics_mode" id="mer_paynamics_mode" value="Live" />
											<?php /* 
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PMTMODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PMTMODE')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paynamics_mode', 'Live',($getvendor->mer_paynamics_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LIVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LIVE')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paynamics_mode', 'Sandbox',($getvendor->mer_paynamics_mode=='Live' || $getvendor->mer_paynamics_mode == '' ) ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('admin_lang_file').'.ADMIN_SANDBOX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SANDBOX')}}</label>
													</div>
													@if ($errors->has('mer_paynamics_mode') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_mode') }}</p> 
													@endif
												</div>
											</div> */ ?>
										</fieldset>
									</div>
									{{--PAYMAYA --}}
									<div class="row">
										<fieldset>
											<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA')}}</legend>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paymaya_status', 'Publish',($getvendor->mer_paymaya_status=='Publish') ? true : false,['id' => 'mer_paymaya_status']) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paymaya_status', 'Unpublish',($getvendor->mer_paymaya_status=='Unpublish' || $getvendor->mer_paymaya_status == '') ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
													</div>
													@if ($errors->has('mer_paymaya_status') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_status') }}</p> 
													@endif
												</div>
											</div>
											
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
														<label class="form-check-label">{!! Form::radio('mer_paymaya_mode', 'Sandbox',($getvendor->mer_paymaya_mode=='Sandbox' || $getvendor->mer_paymaya_mode == '') ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('admin_lang_file').'.ADMIN_SANDBOX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SANDBOX')}}</label>
													</div>
													@if ($errors->has('mer_paymaya_mode') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_mode') }}</p> 
													@endif
												</div>
											</div> */ ?>
										</fieldset>
									</div>
									{{--NET BANKING --}}
									<div class="row">
										<fieldset>
											<legend>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('admin_lang_file').'.ADMIN_NETBANKING') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NETBANKING')}}</legend>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_netbank_status', 'Publish',($getvendor->mer_netbank_status=='Publish') ? true : false) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_netbank_status', 'Unpublish',($getvendor->mer_netbank_status=='Unpublish' || $getvendor->mer_netbank_status == '') ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
													</div>
													@if ($errors->has('mer_netbank_status') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_netbank_status') }}</p> 
													@endif
												</div>
											</div>
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
													{!! Form::text('mer_location',$getvendor->mer_location,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOCATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOCATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOCATION'),'id' => 'mer_location','maxlength'=>'100']) !!}
													@if ($errors->has('mer_location') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_location') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('country',$getvendor->mer_country,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_COUNTRY')) ? trans(Session::get('admin_lang_file').'.ADMIN_COUNTRY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COUNTRY'),'id' => 'country']) !!}
													@if ($errors->has('country') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('country') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATE')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('mer_state',$getvendor->mer_state,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_STATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATE'),'id' => 'administrative_area_level_1']) !!}
													@if ($errors->has('mer_state') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_state') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CITY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CITY')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('mer_city',$getvendor->mer_city,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_CITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_CITY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CITY'),'id' => 'locality']) !!}
													@if ($errors->has('mer_city') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_city') }}</p> 
													@endif
												</div>
											</div>
										</fieldset>
									</div>
									
									
									<div class="form-group">
										<label class="col-sm-2"></label>
										<div class="col-sm-6">
												<!-- <div class="panel-heading col-md-offset-3">
 -->										@if($id!='')
											@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
										@else
											@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
										@endif
										@php $url = url('manage-merchant')@endphp
										{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
										{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'onclick'=>"javascript:window.location.href='$url'"])!!}
									<!-- </div>
 -->										</div>
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
<script>
	$('#category_name').bind('keyup blur',function(){ 
		var node = $(this);
	node.val(node.val().replace(/[^a-z]/g,'') ); }
	);

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
			mer_fname: "required",
			mer_lname: "required",
			mer_email: {
				required: true,
				email: true
			},
			mer_password: {
				required: {
					depends: function(element) {
						if($('input[name=gotId]').val()!=''){ return true; } else { return false; } 
					}
				}
			},
            mer_commission: {
                required: true,
                range: [0, 100]
            },
			mer_phone : { jsPhoneValidation : true  },
			// mer_commission: "required",

            // mer_commission: "required|integer",
			idproof: {
				required: {
					depends: function(element) {
						if($('input[name=old_idproof]').val()==''){ return true; } else { return false; } 
					}
				}
			},
			license: {
				required: {
					depends: function(element) {
						if($('input[name=old_license]').val()==''){ return true; } else { return false; } 
					}
				}
			},
			cancel_status: "required",
			mer_business_type: "required",
			/*mer_paynamics_status:"required",
			mer_paymaya_status:"required",
			mer_netbank_status:"required",*/
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
			mer_fname: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME')}}",
			mer_lname: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_LNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_LNAME')}}",
			mer_email: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
			mer_password: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_PASS')}}",
			mer_phone: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
			mer_commission: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_COMMISSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_COMMISSION')}}",
			mer_paymaya_clientid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')}}",
			mer_paymaya_secretid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET')}}",
			mer_paynamics_clientid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT')}}",
			mer_paynamics_secretid: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')}}",
			mer_bank_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BANK')}}",
			mer_branch: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BRANCH')}}",
			mer_bank_accno: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ACCNO')}}",
			mer_ifsc: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_IFSC')}}",
			cancel_status: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CANCEL_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CANCEL_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_CANCEL_STATUS')}}",
			idproof: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UPLOAD_IDPROOF')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPLOAD_IDPROOF') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPLOAD_IDPROOF')}}",
			license: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UPLOAD_RESTLICENCE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPLOAD_RESTLICENCE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPLOAD_RESTLICENCE')}}",
			mer_business_type: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SL_BU_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SL_BU_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SL_BU_TYPE')}}",
			/*mer_paynamics_status:"Enter Paynamics status",
			mer_paymaya_status:"Enter Paymaya status",
			mer_netbank_status:"Enter Netbanking status",*/

			
		},
		errorPlacement: function(error, element) 
		{
			if ( element.is(":radio") ) 
			{
				error.appendTo( $(element).next().next().next().next());
				//error.insertAfter($(element).parents('div').prev($('.pre_order_label')));
			}
			else 
			{ // This is the default behavior 
				error.insertAfter( element );
			}
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
            /** @type {!HTMLInputElement} */(document.getElementById('mer_location')),
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
			//console.log(addressType+' = '+val);
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
		var element = document.getElementById('mer_phone');
		if(element.value=='')
		{
			$('#mer_phone').val('{{$default_dial}}');
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


	$('#commoncommission:checkbox').bind('change', function(e) {

	   var oldcommissionvalue = $('#oldcomssion').val();

	   if ($(this).is(':checked')) {
			$('#mer_commission').val(<?php echo $commoncommission->common_commission;?>);
			$('#mer_commission').attr('readonly', true);
	   }
	   else {
			$('#mer_commission').attr('readonly', false);
			$('#mer_commission').val(oldcommissionvalue);
	   }
	})
		
	function Upload(files,widthParam,heightParam,maxwidthparam,maxheightparam)
	{
		//alert('s'+files+'\n'+widthParam+'\n'+heightParam+'\n'+maxwidthparam+'\n'+maxheightparam);
		var fileUpload = document.getElementById(files);
		//alert(fileUpload.name);
		var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.jpg|.png|.gif|.jpeg)$");
		if (regex.test(fileUpload.value.toLowerCase()))
		{
			if (typeof (fileUpload.files) != "undefined")
			{
				var reader = new FileReader();
				reader.readAsDataURL(fileUpload.files[0]);
				reader.onload = function (e)
				{
					var image = new Image();
					image.src = e.target.result;

					image.onload = function ()
					{
						var height = this.height;
						var width = this.width;
						//alert(height +'<'+ heightParam +'&&'+ height +'>'+ maxheightparam+')|| ('+width+' < '+widthParam +'&& '+width+' > '+maxwidthparam+')');
						if (height < heightParam || height > maxheightparam|| width < widthParam || width > maxwidthparam)
						{
							//document.getElementById("image_valid_error").style.display = "inline";
							//$("#image_valid_error").fadeOut(9000);
							alert('Please select image above '+widthParam+'X'+heightParam+' and below '+maxwidthparam+'X'+maxheightparam);
							$("#"+files).val('');
							$("#"+files).focus();
							return false;
						}
						return true;
					};
				}
			}
			else
			{
				alert("This browser does not support HTML5.");
				$("#image").val('');
				return false;
			}
		}
		/*else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}*/
	}
</script>
<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
   <script type="text/javascript">
    $("#mer_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
   </script>
@endsection
@stop