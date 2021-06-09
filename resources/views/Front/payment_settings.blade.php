@extends('Front.layouts.default')
@section('content')
	<style type="text/css">
		.row .panel-heading{
			margin-bottom: 10px;
		}

		.rdo-btn {
			display: inline-block;
			position: relative;
			padding-left: 25px;
			padding-right: 20px;
			margin-bottom: 12px;
			cursor: pointer;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		.rdo-btn input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		.checkmark {
			position: absolute;
			top: 1px;
			left: 0;
			height: 15px;
			width: 15px;
			background-color: #ddd;
			border-radius: 50%;
		}

		.rdo-btn:hover input ~ .checkmark {
			background-color: #ccc;
		}
		
		.rdo-btn input:checked ~ .payment-radio-label{color:#ff5215!important;}

		.rdo-btn input:checked ~ .checkmark {
			background-color: #ff5215;
		}

		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}

		.rdo-btn input:checked ~ .checkmark:after {
			display: block;
		}

		.rdo-btn .checkmark:after {
			top: 5px;
			left: 5px;
			width: 5px;
			height: 5px;
			border-radius: 50%;
			background: white;
		}

	</style>

	
	
		 <div class="profile-sidebar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 profile-sidebar-sec">
						<!-- Sidebar -->
						@include('Front.includes.profile_sidebar')
						<!-- Sidebar -->
					</div>
				</div>
			</div>
		</div>
	
		<div class="section9-inner">
			<div class="container userContainer">
				<div class="row">
					<!--<div class="col-lg-12">
						<h5 class="sidebar-head">
							@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')
						</h5>
					</div>-->
				
					<div class="userContainer-bg row">
						
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div">
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
							 
							<div class="box-body spaced" >
								<div id="location_form">

									<div class="row-fluid well">
										<div class="help-block"> <i class="fa fa-exclamation-circle fa-lg" aria-hidden="true"></i>{{(Lang::has(Session::get('front_lang_file').'.FRONT_PMT_HINTS')) ? trans(Session::get('front_lang_file').'.FRONT_PMT_HINTS') : trans($this->FRONT_LANGUAGE.'.FRONT_PMT_HINTS')}}  </div>
										{{-- ,'onSubmit' => 'return check_publish_status();' //don't force to fill paypal or netbanking details --}}
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'user-payment-settings','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
										<div class="row panel-heading payment-text-div">

											{{--PAYNAMICS --}}
											<?php /*
											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PAYNAMICS')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYNAMICS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PAYNAMICS')}}</h5>
											</div>

											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:
											</span>
												</div>
											</div>
											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 payment-radio-sec">
												<div class="form-group">
													<label class="form-check-label rdo-btn">{!! Form::radio('cus_paynamics_status', 'Publish',($getvendor->cus_paynamics_status=='Publish') ? true : false)  !!} <span class="payment-radio-label">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH')}}</span>
														<span class="checkmark"></span>
													</label>
													<label class="form-check-label rdo-btn">{!! Form::radio('cus_paynamics_status', 'Unpublish',($getvendor->cus_paynamics_status=='Unpublish' || $getvendor->cus_paynamics_status == '') ? true : false) !!}<span class="payment-radio-label">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_UNPUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_UNPUBLISH')}}</span>
														<span class="checkmark"></span>
													</label>
												</div>
											</div>

											<div id="paynamicsDiv" class="row panel-heading" style="@if($getvendor->cus_paynamics_status!='Publish') display:none @endif">
											
											
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID')}}:
													</label>
													</div>
													<div class="profile-right-input">														
															{!! Form::text('cus_paynamics_clientid',$getvendor->cus_paynamics_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID'),'id' => 'cus_paynamics_clientid','maxlength'=>'100']) !!}
													</div>
												</div>
												
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID')}}:
													</label>														
													</div>
													<div class="profile-right-input">
															{!! Form::text('cus_paynamics_secretid',$getvendor->cus_paynamics_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID'),'id' => 'cus_paynamics_secretid','maxlength'=>'100']) !!}
													</div>
												</div>						
												
											

												<input type="hidden" name="cus_paynamics_mode" id="cus_paynamics_mode" value="Live" />
                                                
										<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
											<div class="form-group">
												<span class="panel-title">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('front_lang_file').'.ADMIN_PMTMODE') : trans($this->FRONT_LANGUAGE.'.ADMIN_PMTMODE')}}:
												</span>
											</div>
										</div>
										<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
											<div class="form-group">
												<label class="form-check-label rdo-btn">{!! Form::radio('cus_paynamics_mode', 'Live',($getvendor->cus_paynamics_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('front_lang_file').'.ADMIN_LIVE') : trans($this->FRONT_LANGUAGE.'.ADMIN_LIVE')}}
													<span class="checkmark"></span>
												</label>
												<label class="form-check-label rdo-btn">{!! Form::radio('cus_paynamics_mode', 'Sandbox',($getvendor->cus_paynamics_mode=='Sandbox' || $getvendor->cus_paynamics_mode == '') ? true : false) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('front_lang_file').'.ADMIN_SANDBOX') : trans($this->FRONT_LANGUAGE.'.ADMIN_SANDBOX')}}
													<span class="checkmark"></span>
												</label>
											</div>
										</div>

											</div>
											*/ ?>

											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PAYMAYA')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYMAYA') : trans($this->FRONT_LANGUAGE.'.ADMIN_PAYMAYA')}}</h5>
											</div>

											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:
											</span>
												</div>
											</div>
											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 payment-radio-sec">
												<div class="form-group">
													<label class="form-check-label rdo-btn">{!! Form::radio('cus_paymaya_status', 'Publish',($getvendor->cus_paymaya_status=='Publish') ? true : false,['id' => 'cus_paymaya_status']) !!} <span class="payment-radio-label">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH')}}</span>
														<span class="checkmark"></span>
													</label>
													<label class="form-check-label rdo-btn">{!! Form::radio('cus_paymaya_status', 'Unpublish',($getvendor->cus_paymaya_status=='Unpublish' || $getvendor->cus_paymaya_mode == '') ? true : false) !!}<span class="payment-radio-label">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_UNPUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_UNPUBLISH')}}</span>
														<span class="checkmark"></span>
													</label>
												</div>
											</div>

											<div id="paymayaDiv" class="row panel-heading" style="@if($getvendor->cus_paymaya_status!='Publish') display:none @endif">
											
											
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID')}}:
													</label>														
													</div>
													<div class="profile-right-input">
															{!! Form::text('cus_paymaya_clientid',$getvendor->cus_paymaya_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_CLIENTID')) ? trans(Session::get('front_lang_file').'.ADMIN_CLIENTID') : trans($this->FRONT_LANGUAGE.'.ADMIN_CLIENTID'),'id' => 'cus_paymaya_clientid','maxlength'=>'100']) !!}
													</div>
												</div>
												{{--  paypal email is enough for payment , so hide secretkey--}}
												<div class="col-lg-12 col-md-12 form-group" style="display:none">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID')}}:
													</label>													
													</div>
													<div class="profile-right-input">
															{!! Form::text('cus_paymaya_secretid',$getvendor->cus_paymaya_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_SECRETID')) ? trans(Session::get('front_lang_file').'.ADMIN_SECRETID') : trans($this->FRONT_LANGUAGE.'.ADMIN_SECRETID'),'id' => 'cus_paymaya_secretid','maxlength'=>'100']) !!}
													</div>
												</div>
												
												<input type="hidden" name="cus_paymaya_mode" id="cus_paymaya_mode" value="Live" />
                                                <?php /*
										<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
											<div class="form-group">
												<span class="panel-title">
													{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PMTMODE')) ? trans(Session::get('front_lang_file').'.ADMIN_PMTMODE') : trans($this->FRONT_LANGUAGE.'.ADMIN_PMTMODE')}}:
												</span>
											</div>
										</div>
										<div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
											<div class="form-group">
												<label class="form-check-label rdo-btn">{!! Form::radio('cus_paymaya_mode', 'Live',($getvendor->cus_paymaya_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('front_lang_file').'.ADMIN_LIVE')) ? trans(Session::get('front_lang_file').'.ADMIN_LIVE') : trans($this->FRONT_LANGUAGE.'.ADMIN_LIVE')}}
													<span class="checkmark"></span>
												</label>
												<label class="form-check-label rdo-btn">{!! Form::radio('cus_paymaya_mode', 'Sandbox',($getvendor->cus_paymaya_mode=='Sandbox' || $getvendor->cus_paymaya_mode == '') ? true : false) !!}{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SANDBOX')) ? trans(Session::get('front_lang_file').'.ADMIN_SANDBOX') : trans($this->FRONT_LANGUAGE.'.ADMIN_SANDBOX')}}
													<span class="checkmark"></span>
												</label>
											</div>
										</div>
										*/?>
											</div>

											{{--NET BANKING --}}
											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_NETBANKING')) ? trans(Session::get('front_lang_file').'.ADMIN_NETBANKING') : trans($this->FRONT_LANGUAGE.'.ADMIN_NETBANKING')}}</h5>
											</div>

											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<div class="form-group">
											<span class="panel-title">
												{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH_STATUS')}} <span class="impt">*</span>:
											</span>
												</div>
											</div>
											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 payment-radio-sec">
												<div class="form-group">
													<label class="form-check-label rdo-btn">{!! Form::radio('cus_netbank_status', 'Publish',($getvendor->cus_netbank_status=='Publish') ? true : false) !!} <span class="payment-radio-label">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_PUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_PUBLISH')}}</span>
														<span class="checkmark"></span>
													</label>
													<label class="form-check-label rdo-btn">{!! Form::radio('cus_netbank_status', 'Unpublish',($getvendor->cus_netbank_status=='Unpublish' || $getvendor->cus_netbank_status == '') ? true : false) !!}<span class="payment-radio-label">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNPUBLISH')) ? trans(Session::get('front_lang_file').'.ADMIN_UNPUBLISH') : trans($this->FRONT_LANGUAGE.'.ADMIN_UNPUBLISH')}}</span>
														<span class="checkmark"></span>
													</label>
												</div>
											</div>

											<div id="netBankDiv" class="row panel-heading" style="@if($getvendor->cus_netbank_status!='Publish') display:none @endif">
											
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('front_lang_file').'.ADMIN_BANKNAME') : trans($this->FRONT_LANGUAGE.'.ADMIN_BANKNAME')}}:
													</label>													
													</div>
													<div class="profile-right-input">													
														{!! Form::text('cus_bank_name',$getvendor->cus_bank_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_BANKNAME')) ? trans(Session::get('front_lang_file').'.ADMIN_BANKNAME') : trans($this->FRONT_LANGUAGE.'.ADMIN_BANKNAME'),'id' => 'cus_bank_name','maxlength'=>'100']) !!}
													</div>
												</div>
												
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('front_lang_file').'.ADMIN_BRANCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_BRANCH')}}:
													</label>													
													</div>
													<div class="profile-right-input">													
														{!! Form::text('cus_branch',$getvendor->cus_branch,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_BRANCH')) ? trans(Session::get('front_lang_file').'.ADMIN_BRANCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_BRANCH'),'id' => 'cus_branch','maxlength'=>'100']) !!}
													</div>
												</div>
												
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('front_lang_file').'.ADMIN_ACCNO') : trans($this->FRONT_LANGUAGE.'.ADMIN_ACCNO')}}:
													</label>													
													</div>
													<div class="profile-right-input">													
														{!! Form::text('cus_bank_accno',$getvendor->cus_bank_accno,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_ACCNO')) ? trans(Session::get('front_lang_file').'.ADMIN_ACCNO') : trans($this->FRONT_LANGUAGE.'.ADMIN_ACCNO'),'id' => 'cus_bank_accno','maxlength'=>'100']) !!}
													</div>
												</div>
												
												<div class="col-lg-12 col-md-12 form-group">
													<div class="profile-left-label">
													<label class="panel-title">
														{{(Lang::has(Session::get('front_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('front_lang_file').'.ADMIN_IFSC') : trans($this->FRONT_LANGUAGE.'.ADMIN_IFSC')}}:
													</label>													
													</div>
													<div class="profile-right-input">													
														{!! Form::text('cus_ifsc',$getvendor->cus_ifsc,['class'=>'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.ADMIN_IFSC')) ? trans(Session::get('front_lang_file').'.ADMIN_IFSC') : trans($this->FRONT_LANGUAGE.'.ADMIN_IFSC'),'id' => 'cus_ifsc','maxlength'=>'100']) !!}
													</div>
												</div>							
												
											


											</div><!-- end div-->

											<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
												<div class="form-group formBtn">
													<span id="publish_err" style="display:none;color:red;font-size:16px">
														
														{{(Lang::has(Session::get('front_lang_file').'.FRONT_FILL_PAY_NET')) ? trans(Session::get('front_lang_file').'.FRONT_FILL_PAY_NET') : trans($FRONT_LANGUAGE.'.FRONT_FILL_PAY_NET')}}
													</span></br>
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
		</div>
	

@section('script')

	<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
	<script type="text/javascript">
        $(document).ready(function(){

            $("input[name='cus_paynamics_status']").click(function(){
                if($(this).val()=='Publish')
                {
                    $('#paynamicsDiv').show();
                }
                else
                {
                    $('#paynamicsDiv').hide();
                }
            });
            $("input[name='cus_paymaya_status']").click(function(){
                if($(this).val()=='Publish')
                {
                    $('#paymayaDiv').show();
                }
                else
                {
                    $('#paymayaDiv').hide();
                }
            });
            $("input[name='cus_netbank_status']").click(function(){
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

        function check_publish_status()
        {
        	var paypal_status = $('input[name=cus_paymaya_status]:checked').val();
        	var offline_status = $('input[name=cus_netbank_status]:checked').val();
        	//alert(paypal_status); return false;
        	if(paypal_status.trim() == "Unpublish" && offline_status.trim() == "Unpublish")
        	{
        		$('#publish_err').show();
        		return false;
        	}
        	else
        	{
        		return true;
        	}
        }

        $("#customer_form").validate({
            //onkeyup: true,
            onfocusout: function (element) {
                this.element(element);
            },
            rules: {
                cus_paymaya_status: "required",
                cus_paymaya_clientid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_paymaya_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_paymaya_clientid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_paymaya_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_paymaya_secretid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_paymaya_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_paynamics_status: "required",
                cus_paynamics_clientid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_paynamics_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_paynamics_secretid: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_paynamics_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_bank_name: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_branch: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_bank_accno: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
                cus_ifsc: {
                    required: {
                        depends: function(element) {
                            if($('input[name=cus_netbank_status]:checked').val()=='Publish'){ return true; } else { return false; }
                        }
                    }
                },
            },
            messages: {
                cus_paymaya_status:"{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SEL_PAYMAYA_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_SEL_PAYMAYA_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_SEL_PAYMAYA_STATUS')}}",
                cus_paymaya_clientid: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_CLIENT') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYMA_CLIENT')}}",
                cus_paymaya_secretid: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_SECRET')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYMA_SECRET') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYMA_SECRET')}}",
                cus_paynamics_status: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SEL_PAYNAMICS_STATUS')) ? trans(Session::get('front_lang_file').'.ADMIN_SEL_PAYNAMICS_STATUS') : trans($this->FRONT_LANGUAGE.'.ADMIN_SEL_PAYNAMICS_STATUS')}}",
                cus_paynamics_clientid: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_CLIENT') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYNA_CLIENT')}}",
                cus_paynamics_secretid: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_SECRET')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PAYNA_SECRET') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PAYNA_SECRET')}}",
                cus_bank_name: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_BANK')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_BANK') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_BANK')}}",
                cus_branch: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_BRANCH')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_BRANCH') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_BRANCH')}}",
                cus_bank_accno: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_ACCNO')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_ACCNO') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_ACCNO')}}",
                cus_ifsc: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_IFSC')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_IFSC') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_IFSC')}}",
            }
        });
	</script>
@endsection
@stop

