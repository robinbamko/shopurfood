@extends('DeliveryManager.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')		
<style type="text/css">
	.form-horizontal .control-label{padding-top: 0px;}
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
							<div class="panel-heading p__title">
								{{$pagetitle}}
							</div>
							{{-- Display error message--}}
							@if ($errors->has('errors')) 
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								<i class="fa fa-check-circle"></i>{{ $errors->first('errors') }}
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
							<div class="box-body spaced" style="padding:20px;">
								
								<div class="">


									{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => 'delivery-manager-settings','id'=>'profile_form','enctype'=>'multipart/form-data']) !!}
									
									<?php //print_r($getvendor); exit; ?>										
									<div class="form-group">
										{{ Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_RATING')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_RATING') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_RATING'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">

										{{ Form::radio('cus_rating_status','1',($getvendor->dm_customer_rating==1)?true:false,['id' => 'enable1','required']) }}
										{{ Form::label('enable1',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENABLE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENABLE')) }}
										{{ Form::radio('cus_rating_status','0',($getvendor->dm_customer_rating==0)?true:false  ,['id' => 'disable1','required']) }}

										{{ Form::label('disable1',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DISABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DISABLE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DISABLE')) }}
										</div>
									</div>
									<div class="form-group">
										{{ Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_DATA_PROTECTION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_CUSTOMER_DATA_PROTECTION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_CUSTOMER_DATA_PROTECTION'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
										{{ Form::radio('cus_dataprotect_status','1',($getvendor->dm_cust_data_protect==1)?true:false,['id' => 'enable2','required']) }}
										{{ Form::label('enable2',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENABLE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENABLE')) }}
										{{ Form::radio('cus_dataprotect_status','0',($getvendor->dm_cust_data_protect==0)?true:false ,['id' => 'disable2','required']) }}
										{{ Form::label('disable2',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DISABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DISABLE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DISABLE')) }}
										</div>
									</div>

									<div class="form-group">
										{{ Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELIVERY_TYPE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELIVERY_TYPE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELIVERY_TYPE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
										{{ Form::radio('cus_delivery_type','auto',($getvendor->dm_delivery_type=='auto')?true:false,['id' => 'enable3','required']) }}
										{{ Form::label('enable3',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_AUTO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_AUTO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_AUTO')) }}
										{{ Form::radio('cus_delivery_type','manual',($getvendor->dm_delivery_type=='manual')?true:false,['id' => 'disable3','required']) }}
										{{ Form::label('disable3',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MANUAL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MANUAL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MANUAL')) }}
										</div>
									</div>
									<div class="form-group">
										{{ Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_COMM_DEL_FAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_COMM_DEL_FAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_COMM_DEL_FAIL'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
										{{ Form::radio('del_failed_type','1',($getvendor->dm_commission_status=='1')?true:false,['id' => 'enable4','required']) }}
										{{ Form::label('enable4',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENABLE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENABLE')) }}
										{{ Form::radio('del_failed_type','0',($getvendor->dm_commission_status=='0')?true:false,['id' => 'disable4','required']) }}
										{{ Form::label('disable4',(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DISABLE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DISABLE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DISABLE')) }}
										<a href="#" id="help-seo-title" data-placement="right" data-toggle="tooltip" title="" data-original-title="If it enable delivery boy will get commision,even the order has failed"><span class="glyphicon glyphicon-question-sign"></span></a>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2"></label>
										<div class="col-sm-6">
											<!-- <div class="panel-heading"> -->
										@php $saveBtn=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE') @endphp
										{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
										
										
									<!-- </div> -->
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
	<script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
	});
	</script>
	@endsection
@stop