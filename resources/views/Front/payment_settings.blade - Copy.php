@extends('Front.layouts.default')
@section('content')
<style type="text/css">
    .row .panel-heading{
	margin-bottom: 10px;
    }
</style> 




<div class="section9-inner">
	<div class="container userContainer">
		<div class="row">    
			<div class="col-lg-3 col-sm-12 col-md-4 col-xs-12" style="margin-bottom: 20px;">
				<!-- Sidebar -->
				@include('Front.includes.profile_sidebar')
				<!-- Sidebar -->
			</div>
			<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12"> 
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
				{{-- Add/Edit page starts--}}
				<div class="box-body spaced" >
					<div id="location_form">
						
						<div class="row-fluid well">
							{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'user-payment-settings','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
							<div class="row panel-heading">
								
								{{--PAYNAMICS --}}
								<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
									<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PAYNAMICS')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYNAMICS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PAYNAMICS')}}</h5>
								</div>
								
								<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
								<div class="form-group">
								<span class="panel-title">
								{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:
								</span>
								</div>
								</div>
								<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
								<div class="form-group">
								<label class="form-check-label">{!! Form::radio('cus_paynamics_status', 'Publish',($getvendor->cus_paynamics_status=='Publish') ? true : false)  !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
								<label class="form-check-label">{!! Form::radio('cus_paynamics_status', 'Unpublish',($getvendor->cus_paynamics_status=='Unpublish') ? true : false) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_UNPUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
								</div>
								</div>
								
								<div id="paynamicsDiv" class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="@if($getvendor->cus_paynamics_status!='Publish') display:none @endif">
								
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('mer_paynamics_clientid',$getvendor->cus_paynamics_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID'),'id' => 'cus_paynamics_clientid','maxlength'=>'100']) !!}
									</div>
									</div>
								
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('mer_paynamics_secretid',$getvendor->cus_paynamics_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID'),'id' => 'cus_paynamics_secretid','maxlength'=>'100']) !!}
									</div>
									</div>
								
								
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('front_lang_file').'.ADMIN_PMTMODE') : trans($this->FRONT_LANGUAGE.'.ADMIN_PMTMODE')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									<label class="form-check-label">{!! Form::radio('cus_paynamics_mode', 'Live',($getvendor->cus_paynamics_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('front_lang_file').'.ADMIN_LIVE') : trans($this->FRONT_LANGUAGE.'.ADMIN_LIVE')}} </label>
									<label class="form-check-label">{!! Form::radio('cus_paynamics_mode', 'Sandbox',($getvendor->cus_paynamics_mode=='Live') ? true : true) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('front_lang_file').'.ADMIN_SANDBOX') : trans($this->FRONT_LANGUAGE.'.ADMIN_SANDBOX')}}</label>
									</div>
									</div>
								
								</div>
								
								
								<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
									<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PAYMAYA')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYMAYA') : trans($this->FRONT_LANGUAGE.'.ADMIN_PAYMAYA')}}</h5>
								</div>
								
								<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
								<div class="form-group">
								<span class="panel-title">
								{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:
								</span>
								</div>
								</div>
								<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
								<div class="form-group">
								<label class="form-check-label">{!! Form::radio('cus_paymaya_status', 'Publish',($getvendor->cus_paymaya_status=='Publish') ? true : false,['id' => 'cus_paymaya_status']) !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
								<label class="form-check-label">{!! Form::radio('cus_paymaya_status', 'Unpublish',($getvendor->cus_paymaya_status=='Unpublish') ? true : true) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_UNPUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
								</div>
								</div>
								
								<div id="paymayaDiv" style="@if($getvendor->cus_paymaya_status!='Publish') display:none @endif">
									
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('cus_paymaya_clientid',$getvendor->cus_paymaya_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID'),'id' => 'cus_paymaya_clientid','maxlength'=>'100']) !!}
									</div>
									</div>
									
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('cus_paymaya_secretid',$getvendor->cus_paymaya_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID'),'id' => 'cus_paymaya_secretid','maxlength'=>'100']) !!}
									</div>
									</div>
									
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('front_lang_file').'.ADMIN_PMTMODE') : trans($this->FRONT_LANGUAGE.'.ADMIN_PMTMODE')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									<label class="form-check-label">{!! Form::radio('cus_paymaya_mode', 'Live',($getvendor->cus_paymaya_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('front_lang_file').'.ADMIN_LIVE') : trans($this->FRONT_LANGUAGE.'.ADMIN_LIVE')}} </label>
									<label class="form-check-label">{!! Form::radio('cus_paymaya_mode', 'Sandbox',($getvendor->cus_paymaya_mode=='Sandbox') ? true : true) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('front_lang_file').'.ADMIN_SANDBOX') : trans($this->FRONT_LANGUAGE.'.ADMIN_SANDBOX')}}</label>
									</div>
									</div>
								
								</div>

								{{--NET BANKING --}}
								<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
									<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('front_lang_file').'.ADMIN_NETBANKING') : trans($this->FRONT_LANGUAGE.'.ADMIN_NETBANKING')}}</h5>
								</div>
								
								<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
								<div class="form-group">
								<span class="panel-title">
								{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:
								</span>
								</div>
								</div>
								<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
								<div class="form-group">
								<label class="form-check-label">{!! Form::radio('cus_netbank_status', 'Publish',($getvendor->cus_netbank_status=='Publish') ? true : false) !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH')}} </label>
								<label class="form-check-label">{!! Form::radio('cus_netbank_status', 'Unpublish',($getvendor->cus_netbank_status=='Unpublish') ? true : true) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_UNPUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_UNPUBLISH')}}</label>
								</div>
								</div>
								
								<div id="netBankDiv" style="@if($getvendor->cus_netbank_status!='Publish') display:none @endif">
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('front_lang_file').'.ADMIN_BANKNAME') : trans($this->FRONT_LANGUAGE.'.ADMIN_BANKNAME')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('cus_bank_name',$getvendor->cus_bank_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('front_lang_file').'.ADMIN_BANKNAME') : trans($this->FRONT_LANGUAGE.'.ADMIN_BANKNAME'),'id' => 'cus_bank_name','maxlength'=>'100']) !!}
									</div>
									</div>
									
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('front_lang_file').'.ADMIN_BRANCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_BRANCH')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('cus_branch',$getvendor->cus_branch,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('front_lang_file').'.ADMIN_BRANCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_BRANCH'),'id' => 'cus_branch','maxlength'=>'100']) !!}
									</div>
									</div>
									
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('front_lang_file').'.ADMIN_ACCNO') : trans($this->FRONT_LANGUAGE.'.ADMIN_ACCNO')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('cus_bank_accno',$getvendor->cus_bank_accno,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('front_lang_file').'.ADMIN_ACCNO') : trans($this->FRONT_LANGUAGE.'.ADMIN_ACCNO'),'id' => 'cus_bank_accno','maxlength'=>'100']) !!}
									</div>
									</div>
									
									<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
									<div class="form-group">
									<span class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('front_lang_file').'.ADMIN_IFSC') : trans($this->FRONT_LANGUAGE.'.ADMIN_IFSC')}}:
									</span>
									</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
									<div class="form-group">
									{!! Form::text('cus_ifsc',$getvendor->cus_ifsc,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('front_lang_file').'.ADMIN_IFSC') : trans($this->FRONT_LANGUAGE.'.ADMIN_IFSC'),'id' => 'cus_ifsc','maxlength'=>'100']) !!}
									</div>
									</div>
									
									
								</div><!-- end div-->
	
								<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12"> 
									<div class="form-group formBtn">
										@php $saveBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE') : trans($FRONT_LANGUAGE.'.ADMIN_UPDATE'); @endphp 
										{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!} 
									</div>
								</div>  
							</div>
							{!! Form::close() !!}
						</div>
					</div>
				</div>
				{{-- Add page ends--}}
			</div>
			
		</div>
	</div>
</div>          


@section('script')

<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		
		$("input[name='mer_paynamics_status']").click(function(){
			alert($(this).val());
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
	
	$("#customer_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
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
</script>
@endsection
@stop

