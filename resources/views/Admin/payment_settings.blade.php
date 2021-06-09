@extends('Admin.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')
<!-- MAIN -->
<div class="main">
	
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h3 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMENT_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMENT_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMENT_SETTINGS')}}</h3>
		<div class="container-fluid">
			
			
			<div class="row">
				<div class="container right-container">
					<div class="r-btn">
					</div>
					<div class="col-md-12">
						
						
						<!-- INPUTS -->
						<div class="panel">
							
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
							
							@if(count($payment_settings) > 0)
							@php
							foreach($payment_settings as $logo_set_val){ }
							@endphp
							
							<div class="panel-body">
								<div class="row well payment-form" style="margin:5px;">
								{{ Form::open(array('url' => 'admin-paynamics-payment-settings-submit','id'=>'paynamics_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal','onSubmit' => 'return check_payments_disabled("paynamics_status");')) }}
								{{ Form::hidden('ps_id',$logo_set_val->ps_id,array('class' => 'form-control')) }}
								<div class="form-group">
									{{ Form::label('paynamics_client_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_CLIENT_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_CLIENT_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_CLIENT_ID'),array('class'=>'control-label col-sm-3')) }} 
									<div class="col-sm-6">
										{{ Form::text('paynamics_client_id',$logo_set_val->paynamics_client_id,array('class' => 'form-control')) }} 
									</div>
								</div>
								
								<div class="form-group">
									{{ Form::label('paynamics_secret_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_SECRET_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_SECRET_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_SECRET_ID'),array('class'=>'control-label col-sm-3')) }} 
									<div class="col-sm-6">
										{{ Form::text('paynamics_secret_id',$logo_set_val->paynamics_secret_id,array('class' => 'form-control')) }} 
									</div>
								</div>
								
								<div class="form-group">
									{{ Form::label('paynamics_status', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_STATUS'),array('class'=>'control-label col-sm-3')) }} 
									<div class="col-sm-6">
										@if($logo_set_val->paynamics_status == 1)
											{{ Form::radio('paynamics_status','1',($logo_set_val->paynamics_status=='1') ? true : false) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}}
											{{ Form::radio('paynamics_status','0',($logo_set_val->paynamics_status=='0') ? true : false) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}
										@else
											{{ Form::radio('paynamics_status','1',($logo_set_val->paynamics_status=='1') ? true : false) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}}
											{{ Form::radio('paynamics_status','0',($logo_set_val->paynamics_status=='0') ? true : false) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}
										@endif
										<span id="err_paynamics_status" style="color:red"></span>
									</div>
									
								</div>
								<div class="form-group">
									{{ Form::label('paynamics_mode', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_MODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_MODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_MODE'),array('class'=>'control-label col-sm-3')) }} 
									<div class="col-sm-6">
										{{ Form::select('paynamics_mode',array(''=>'--Select--','0' => 'Sandbox','1'=>'Live'),$logo_set_val->paynamics_mode,array('class' => 'form-control')) }} 
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										<!-- <div class="panel-heading col-md-offset-3"> -->
									{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
								<!-- </div> -->
									</div>									
								</div>
								
								
								{{ Form::close() }}
								</div>
							</div>
							
							
							
							@else
							<div class="panel-body payment-form">
								{{ Form::open(array('url' => 'admin-paynamics-payment-settings-submit','id'=>'paynamics_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal','onSubmit' => 'return check_payments_disabled("paynamics_status");')) }}
								<div class="row well" style="margin:5px;">
								{{ Form::hidden('ps_id','',array('class' => 'form-control')) }}
								<div class="form-group">
									{{ Form::label('paynamics_client_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_CLIENT_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_CLIENT_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_CLIENT_ID'),array('class'=>'control-label col-sm-3')) }}
									<div class="col-sm-6">
										{{ Form::text('paynamics_client_id','',array('class' => 'form-control')) }}  
									</div>
								</div>
								
								<div class="form-group">
									{{ Form::label('paynamics_secret_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_SECRET_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_SECRET_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_SECRET_ID'),array('class'=>'control-label col-sm-3')) }}
									<div class="col-sm-6">
										{{ Form::text('paynamics_secret_id','',array('class' => 'form-control')) }} 
									</div>
								</div>
								
								<div class="form-group">
									{{ Form::label('paynamics_status', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_STATUS'),array('class'=>'control-label col-sm-3')) }}
									<div class="col-sm-6">
										{{ Form::radio('paynamics_status','1') }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}}
										{{ Form::radio('paynamics_status','0',true) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}
										<span id="err_paynamics_status" style="color:red"></span>
									</div>
								</div>
								
								<div class="form-group">
									{{ Form::label('paynamics_mode', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_MODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYNAMICS_MODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYNAMICS_MODE'),array('class'=>'control-label col-sm-3')) }}
									<div class="col-sm-6">
										{{ Form::select('paynamics_mode',array(''=>'--Select--','0' => 'Sandbox','1'=>'Live'),null,array('class' => 'form-control')) }} 
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
								</div>

								
								<!-- <div class="panel-heading col-md-offset-3">
									
								</div> -->
								</div>
								{{ Form::close() }}
								
							</div>
							
							
							@endif
							
							<!--  -->
							@if(count($payment_settings) > 0)
							@php
							foreach($payment_settings as $logo_set_val){ }
							@endphp
							<div class="panel-body payment-form">
								{{ Form::open(array('url' => 'admin-paymaya-payment-settings-submit','id'=>'paymaya_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal','onSubmit' => 'return check_payments_disabled("paymaya_status");')) }}
								
								{{ Form::hidden('ps_id',$logo_set_val->ps_id,array('class' => 'form-control')) }}
								<div class="row well" style="margin:5px;">
									<div class="form-group">
										{{ Form::label('paymaya_client_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_CLIENT_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_CLIENT_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_CLIENT_ID'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6">
											{{ Form::text('paymaya_client_id',$logo_set_val->paymaya_client_id,array('class' => 'form-control')) }} 
										</div>
									</div>

									<div class="form-group">
										{{ Form::label('paymaya_secret_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_SECRET_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_SECRET_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_SECRET_ID'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6">
											{{ Form::text('paymaya_secret_id',$logo_set_val->paymaya_secret_id,array('class' => 'form-control')) }} 
										</div>
									</div>
									
									<div class="form-group">
										{{ Form::label('paymaya_status', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_STATUS'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6 control-label text-ali-lef">
											@if($logo_set_val->paymaya_status == 1)
												{{ Form::radio('paymaya_status','1',true) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}}
												{{ Form::radio('paymaya_status','0') }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}
											@else
												{{ Form::radio('paymaya_status','1') }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}}
												{{ Form::radio('paymaya_status','0',true) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}
											@endif
											<span id="err_paymaya_status" style="color:red"></span>
										</div>
									</div>
									
									<div class="form-group">
										{{ Form::label('paymaya_mode', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_MODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_MODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_MODE'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6">
											{{ Form::select('paymaya_mode',array(''=>'--Select--','0' => 'Sandbox','1'=>'Live'),$logo_set_val->paymaya_mode,array('class' => 'form-control')) }}
										</div>
									</div>
									<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
								</div>
								<!-- 	<div class="panel-heading col-md-offset-3">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
 -->								
								{{ Form::close() }}
								</div>
							</div>
							
							
							
							@else
							<div class="panel-body payment-form">
								{{ Form::open(array('url' => 'admin-paymaya-payment-settings-submit','id'=>'paymaya_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal','onSubmit' => 'return check_payments_disabled("paymaya_status");')) }}
								<div class="row well" style="margin:5px;">
								{{ Form::hidden('ps_id','',array('class' => 'form-control')) }}
									<div class="form-group">
										{{ Form::label('paymaya_client_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_CLIENT_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_CLIENT_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_CLIENT_ID'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6">
											{{ Form::text('paymaya_client_id','',array('class' => 'form-control')) }} 
										</div>
									</div>
								
									<div class="form-group">
										{{ Form::label('paymaya_secret_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_SECRET_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_SECRET_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_SECRET_ID'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6">
											{{ Form::text('paymaya_secret_id','',array('class' => 'form-control')) }} 
										</div>
									</div>
									
									<div class="form-group">
										{{ Form::label('paymaya_status', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_STATUS'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6 control-label text-ali-lef">
											{{ Form::radio('paymaya_status','1') }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENABLE')}}
											{{ Form::radio('paymaya_status','0',true) }} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISABLE')}}
											<span id="err_paymaya_status" style="color:red"></span>
										</div>
									</div>
								
									<div class="form-group">
										{{ Form::label('paymaya_mode', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_MODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAYMAYA_MODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAYMAYA_MODE'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6">
											{{ Form::select('paymaya_mode',array(''=>'--Select--','0' => 'Sandbox','1'=>'Live'),null,array('class'=>'form-control')) }}
										</div>
									</div>
									<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
								</div>						
							
									<!-- <div class="panel-heading col-md-offset-3">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div -->>
								{{ Form::close() }}
								</div>
							</div>
							
							
							@endif
							<div class="panel-body payment-form">
								{{ Form::open(array('url' => 'admin-cod-payment-settings-submit','id'=>'cod_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal','onSubmit' => 'return check_payments_disabled("cod_status");')) }}
								@if(count($payment_settings) > 0)
								@php
								foreach($payment_settings as $logo_set_val){ }
								@endphp
								{{ Form::hidden('ps_id',$logo_set_val->ps_id,array('class' => 'form-control')) }}
								<div class="row well" style="margin:5px;">
								<div class="form-group">
										{{ Form::label('paymaya_mode', (Lang::has(Session::get('admin_lang_file').'.ADMIN_COD_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_COD_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COD_STATUS'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6 control-label text-ali-lef">
											{{ Form::radio('cod_status','1',($logo_set_val->cod_status == 1) ? 'checked' : '' ,['id' => 'enable','required']) }}
											{{ Form::label('enable','') }}
											{{ Form::radio('cod_status','0',($logo_set_val->cod_status == 0) ? 'checked' : '' ,['id' => 'disable','required']) }}
											{{ Form::label('disable','') }}
											<span id="err_cod_status" style="color:red"></span>
										</div>
									</div>
									<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
								</div>
									<!-- <div class="panel-heading col-md-offset-3">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div> -->
								</div>
								@else
								{{ Form::hidden('ps_id','',array('class' => 'form-control')) }}
								<div class="row well" style="margin:5px;">
								<div class="form-group">
										{{ Form::label('paymaya_mode', (Lang::has(Session::get('admin_lang_file').'.ADMIN_COD_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_COD_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_COD_STATUS'),array('class'=>'control-label col-sm-3')) }}
										<div class="col-sm-6 text-ali-lef">
											{{ Form::radio('cod_status','1','',['id' => 'enable','required']) }}
											{{ Form::label('enable','') }}
											{{ Form::radio('cod_status','0','' ,['id' => 'disable','required']) }}
											{{ Form::label('disable','') }}
											<span id="err_cod_status" style="color:red"></span>
										</div>
									</div>
									<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div>
								</div>
									<!-- <div class="panel-heading col-md-offset-3">
										{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									</div> -->
								</div>
								@endif
								{!! Form::close()!!}
							</div>
							
							<!-- NET BANKING -->
							@if(count($payment_settings) > 0)
							@php
							foreach($payment_settings as $logo_set_val){ }
							$netbank_status = $logo_set_val->netbank_status;
							$bank_name = $logo_set_val->bank_name;
							$branch = $logo_set_val->branch;
							$bank_accno = $logo_set_val->bank_accno;
							$ifsc = $logo_set_val->ifsc;
							$ps_id = $logo_set_val->ps_id;
							@endphp
							@else
								@php
									$netbank_status = '';
									$bank_name = '';
									$branch = '';
									$bank_accno = '';
									$ifsc = '';
									$ps_id = '';
								@endphp
							@endif
							<div class="panel-body payment-form">
								<div class="row well" style="margin:5px;">
									<h3>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('admin_lang_file').'.ADMIN_NETBANKING') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NETBANKING')}}</h3>
									{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => 'admin_netbanking_submit','id'=>'profile_form','enctype'=>'multipart/form-data']) !!}
										{{ Form::hidden('ps_id',$ps_id,array('class' => 'form-control')) }}
										<?php /* 
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}}:</label>
											<div class="col-sm-6">
												<div class="form-check form-check-inline">
													<label class="form-check-label">{!! Form::radio('netbank_status', 'Publish',($netbank_status=='Publish') ? true : false) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
													<label class="form-check-label">{!! Form::radio('netbank_status', 'Unpublish',($netbank_status=='Unpublish' || $netbank_status == '') ? true : false) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
												</div>
												@if ($errors->has('netbank_status') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('netbank_status') }}</p> 
												@endif
											</div>
										</div> */ ?>
										<input type="hidden" name="netbank_status" value="Publish" />
										<div class="form-group">
											<label class="control-label col-sm-3 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANKNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANKNAME')}}:</label>
											<div class="col-sm-6">
												{!! Form::text('bank_name',$bank_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANKNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANKNAME'),'id' => 'bank_name','maxlength' => 200]) !!}
												@if ($errors->has('bank_name') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('bank_name') }}</p> 
												@endif
											</div>
										</div>
											
										<div class="form-group">
											<label class="control-label col-sm-3 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BRANCH')}}:</label>
											<div class="col-sm-6">
												{!! Form::text('branch',$branch,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BRANCH'),'id' => 'branch','maxlength' => 200]) !!}
												@if ($errors->has('branch') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('branch') }}</p> 
												@endif
											</div>
										</div>
											
										<div class="form-group">
											<label class="control-label col-sm-3 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACCNO')}}:</label>
											<div class="col-sm-6">
												{!! Form::text('bank_accno',$bank_accno,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ACCNO'),'id' => 'bank_accno','maxlength' => 50]) !!}
												@if ($errors->has('bank_accno') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('bank_accno') }}</p> 
												@endif
											</div>
										</div>
											
										<div class="form-group">
											<label class="control-label col-sm-3 starclass" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IFSC')}}:</label>
											<div class="col-sm-6">
												{!! Form::text('ifsc',$ifsc,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IFSC'),'id' => 'ifsc','maxlength' => 50]) !!}
												@if ($errors->has('ifsc') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('ifsc') }}</p> 
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3"></label>
											<div class="col-sm-6">
												{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
											</div>
										</div>
									{!! Form::close() !!}
									
									
									
									
								</div>
							</div>
							<!-- EOF NET BANKING-->
						</div>
						<!-- END INPUTS -->

						
					</div>
				</div>
			</div>
			
			
		</div>
	</div>
	<!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->

@section('script')
<script type="text/javascript">
	$("#paynamics_settings").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			paynamics_client_id: "required",
			paynamics_secret_id: "required",
			paynamics_mode: "required",
			
		},
		messages: {
			paynamics_client_id: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT')}}",
			paynamics_secret_id: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')}}",
			paynamics_mode: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_MODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYNA_MODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYNA_MODE')}}",
			
		}
	});
	
	$("#paymaya_settings").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			paymaya_client_id: "required",
			paymaya_secret_id: "required",
			paymaya_mode: "required",
			
		},
		messages: {
			paymaya_client_id: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')}}",
			paymaya_secret_id: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET')}}",
			paymaya_mode: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_MODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PAYMA_MODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PAYMA_MODE')}}",
			
		}
	});
	$("#profile_form").validate({
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
		
			bank_name: {
				required: {
					depends: function(element) {
						if($('input[name=netbank_status]').val()=='Publish'){ return true; } else { return false; } 
					}
				}
			},
			branch: {
				required: {
					depends: function(element) {
						if($('input[name=netbank_status]').val()=='Publish'){ return true; } else { return false; } 
					}
				}
			},
			bank_accno: {
				required: {
					depends: function(element) {
						if($('input[name=netbank_status]').val()=='Publish'){ return true; } else { return false; } 
					}
				}
			},
			ifsc: {
				required: {
					depends: function(element) {
						if($('input[name=netbank_status]').val()=='Publish'){ return true; } else { return false; } 
					}
				}
			}

		},
		messages: {
			
			bank_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BANK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BANK')}}",
			branch: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_BRANCH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_BRANCH')}}",
			bank_accno: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ACCNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ACCNO')}}",
			ifsc: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_IFSC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_IFSC')}}",
			
			
		}
	});
	
	function check_payments_disabled(text)
	{
		var stripe_status = $("input[name='paynamics_status']:checked").val();
		var paypal_status = $("input[name='paymaya_status']:checked").val();
		var cod_status = $("input[name='cod_status']:checked").val();
		//alert("#err_"+text); return false;
		if(stripe_status == 0 && paypal_status == 0 && cod_status == 0)
		{
			$("#err_"+text).html("{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MIN_ONE_PAYMENT_ENABLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MIN_ONE_PAYMENT_ENABLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MIN_ONE_PAYMENT_ENABLE')}}");
			return false;
		}
		else
		{
			return true;
		}
		//return false;
	}
</script>
@endsection

@stop
