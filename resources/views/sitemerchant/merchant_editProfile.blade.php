@extends('sitemerchant.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')		
<style>


#myImg {
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
}

/* Caption of Modal Image */
#caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
}

/* Add Animation */
.modal-content, #caption {    
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {-webkit-transform:scale(0)} 
    to {-webkit-transform:scale(1)}
}

@keyframes zoom {
    from {transform:scale(0)} 
    to {transform:scale(1)}
}

/* The Close Button */
.close {
    position: absolute;
    *top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
    .modal-content {
        width: 100%;
    }
}
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
					<div class="col-md-12">
						<div class="location panel">
							
							{{-- Display error message--}}

							@if ($errors->any()) 
							<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
								<i class="fa fa-check-circle"></i>
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
								
								<div class="">
									{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => 'merchant_profile','id'=>'profile_form','enctype'=>'multipart/form-data','onSubmit' => 'return check_publish_status();']) !!}
									<?php //print_r($getvendor); exit; ?>										
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_FNAME')) ? trans(Session::get('mer_lang_file').'.MER_FNAME') : trans($MER_OUR_LANGUAGE.'.MER_FNAME')}}:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_fname',$getvendor->mer_fname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_FNAME')) ? trans(Session::get('mer_lang_file').'.MER_FNAME') : trans($MER_OUR_LANGUAGE.'.MER_FNAME'),'id' => 'mer_fname','maxlength' => 50]) !!}
											@if ($errors->has('mer_fname') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_fname') }}</p> 
											@endif
										</div>
									</div>
													
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_LNAME')) ? trans(Session::get('mer_lang_file').'.MER_LNAME') : trans($MER_OUR_LANGUAGE.'.MER_LNAME')}}:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_lname',$getvendor->mer_lname,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_LNAME')) ? trans(Session::get('mer_lang_file').'.MER_LNAME') : trans($MER_OUR_LANGUAGE.'.MER_LNAME'),'id' => 'mer_lname','maxlength' => 50]) !!}
											@if ($errors->has('mer_lname') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_lname') }}</p> 
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_EMAIL') : trans($MER_OUR_LANGUAGE.'.MER_EMAIL')}}:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_email',$getvendor->mer_email,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_EMAIL') : trans($MER_OUR_LANGUAGE.'.MER_EMAIL'),'id' => 'mer_email','required','maxlength' => 200]) !!}
											@if ($errors->has('mer_email') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_email') }}</p> 
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_MOBILENO')) ? trans(Session::get('mer_lang_file').'.MER_MOBILENO') : trans($MER_OUR_LANGUAGE.'.MER_MOBILENO')}}:</label>
										<div class="col-sm-6">
											{!! Form::text('mer_phone',$getvendor->mer_phone,array('required','placeholder'=>(Lang::has(Session::get('mer_lang_file').'.MER_MOBILENO')) ? trans(Session::get('mer_lang_file').'.MER_MOBILENO') : trans($MER_OUR_LANGUAGE.'.MER_MOBILENO'),'class'=>'form-control col-md-7 col-xs-12','maxlength'=>'15','autocomplete'=>'off','id'=>'mer_phone','onkeyup'=>'validate();')) !!}
											@if ($errors->has('mer_phone') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_phone') }}</p> 
											@endif
										</div>
									</div>
									
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_CANCEL_POLICY')) ? trans(Session::get('mer_lang_file').'.MER_CANCEL_POLICY') : trans($MER_OUR_LANGUAGE.'.MER_CANCEL_POLICY')}}:</label>
										<div class="col-sm-6">
											{!! Form::textarea('mer_cancel_policy',$getvendor->mer_cancel_policy,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_CANCEL_POLICY')) ? trans(Session::get('mer_lang_file').'.MER_CANCEL_POLICY') : trans($MER_OUR_LANGUAGE.'.MER_CANCEL_POLICY'),'id' => 'mer_cancel_policy','required']) !!}
											@if ($errors->has('mer_cancel_policy') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_cancel_policy') }}</p> 
											@endif
										</div>
									</div>
									<input type="hidden" name="refund_status" id="refund_status" value="Yes" />
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_CANCEL_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_CANCEL_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_CANCEL_STATUS')}}:</label>
										<div class="col-sm-6">
											<div class="form-check form-check-inline">
												<label class="form-check-label">{!! Form::radio('cancel_status', 'Yes',($getvendor->cancel_status=='Yes') ? true : false) !!} {{(Lang::has(Session::get('mer_lang_file').'.MER_ALLOW')) ? trans(Session::get('mer_lang_file').'.MER_ALLOW') : trans($MER_OUR_LANGUAGE.'.MER_ALLOW')}} </label>
												<label class="form-check-label">{!! Form::radio('cancel_status', 'No',($getvendor->cancel_status=='No' || $getvendor->cancel_status == '') ? true : false) !!}{{(Lang::has(Session::get('mer_lang_file').'.MER_NOT_ALLOW')) ? trans(Session::get('mer_lang_file').'.MER_NOT_ALLOW') : trans($MER_OUR_LANGUAGE.'.MER_NOT_ALLOW')}}</label>
												@if ($errors->has('cancel_status') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('cancel_status') }}</p> 
												@endif
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_COMMISSION')) ? trans(Session::get('mer_lang_file').'.MER_COMMISSION') : trans($MER_OUR_LANGUAGE.'.MER_COMMISSION')}}:</label>
										<div class="col-sm-2">
											{!! Form::text('mer_commission',$getvendor->mer_commission,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_COMMISSION')) ? trans(Session::get('mer_lang_file').'.MER_COMMISSION') : trans($MER_OUR_LANGUAGE.'.MER_COMMISSION'),'id' => 'mer_commission','required','readonly']) !!}
											<div class="input-group-append">
												<span class="input-group-text" id="basic-addon2">%</span>
											 </div>
											@if ($errors->has('mer_commission') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('mer_commission') }}</p> 
											@endif
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ID_PROOF')) ? trans(Session::get('mer_lang_file').'.ADMIN_ID_PROOF') : trans($MER_OUR_LANGUAGE.'.ADMIN_ID_PROOF')}}:</label>
										<div class="col-sm-2">
											@if($getvendor->idproof != '')
												{!! Form::file('idproof',array('class'=>'form-control','id' => 'idproof','accept'=>'image/*')) !!}
												@lang(Session::get('mer_lang_file').'.ADMIN_MIN_MAX')
												
												{{ Form::image(url('public/images/merchant/'.$getvendor->idproof), 'alt text', array('class' => '','width'=>'100px','height'=>'100px','id' => 'idproof')) }}
                                            @else
												{!! Form::file('idproof',array('class'=>'form-control','id' => 'idproof','accept'=>'image/*')) !!}
												@lang(Session::get('mer_lang_file').'.ADMIN_MIN_MAX')
											@endif
											{!! Form::hidden('old_idproof',$getvendor->idproof,['id' => 'old_idproof']) !!}
											<span id="idErr"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_RES_LICENSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_RES_LICENSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_RES_LICENSE')}}:</label>
										<div class="col-sm-2">
											@if($getvendor->license != '')
												{!! Form::file('license',array('class'=>'form-control','id' => 'license','accept'=>'image/*')) !!}
												@lang(Session::get('mer_lang_file').'.ADMIN_MIN_MAX')
												
												{{ Form::image(url('public/images/merchant/'.$getvendor->license), 'alt text', array('class' => '','width'=>'100px','height'=>'100px','id' => 'license')) }}

											@else
												{!! Form::file('license',array('class'=>'form-control','id' => 'license','accept'=>'image/*')) !!}
												@lang(Session::get('mer_lang_file').'.ADMIN_MIN_MAX')
											@endif
											{!! Form::hidden('old_license',$getvendor->license,['id' => 'old_license']) !!}
											<span id="liErr"></span>
										</div>
									</div>
									<?php /* 
									<div class="form-group">
										<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_PROFILE_PHOTO')) ? trans(Session::get('mer_lang_file').'.MER_PROFILE_PHOTO') : trans($MER_OUR_LANGUAGE.'.MER_PROFILE_PHOTO')}}:</label>
										<div class="col-sm-6">
											{!! Form::file('profile_photo',array('class'=>'form-control col-md-7 col-xs-12','accept'=>'image/*','id'=>'profile_photo','onchange'=>'Upload(this.id,"300","300");')) !!}
											{{(Lang::has(Session::get('mer_lang_file').'.MER_DIMENSION')) ? trans(Session::get('mer_lang_file').'.MER_DIMENSION') : trans($MER_OUR_LANGUAGE.'.MER_DIMENSION')}} : 300 * 300
											{{ Form::hidden('old_profile_foto', $getvendor->mer_profile_img) }}
											@if ($errors->has('profile_photo') ) 
											<p class="error-block" style="color:red;">{{ $errors->first('profile_photo') }}</p> 
											@endif
										</div>
									</div> */ ?>
									{{ Form::hidden('old_profile_foto', $getvendor->mer_profile_img) }}
									@if($getvendor->mer_profile_img!='')
									<div class="form-group">
										<label class="control-label col-sm-2">{{(Lang::has(Session::get('mer_lang_file').'.MER_UPLOADED_PHOTO')) ? trans(Session::get('mer_lang_file').'.MER_UPLOADED_PHOTO') : trans($MER_OUR_LANGUAGE.'.MER_UPLOADED_PHOTO')}}:</label>
										<div class="col-sm-6">
											<img id="myImg" src="{{url('public/images/vendor_photos/'.$getvendor->mer_profile_img)}}"  alt="{{$getvendor->mer_fname}}" style="width:20%;">
											<!-- The Modal -->
											<div id="myModal" class="modal">
											  <span class="close">&times;</span>
											  <img class="modal-content" id="img01">
											  <div id="caption"></div>
											</div>

										</div>
										
									</div>
									@endif
									{{--PAYNAMICS --}}
										
									<div class="row">
										<fieldset>
											<!--legend>{{(Lang::has(Session::get('mer_lang_file').'.MER_PAYNAMICS')) ? trans(Session::get('mer_lang_file').'.MER_PAYNAMICS') : trans($MER_OUR_LANGUAGE.'.MER_PAYNAMICS')}}</legend-->
												<legend>Stripe</legend>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_PUBLISH_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_PUBLISH_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_PUBLISH_STATUS')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paynamics_status', 'Publish',($getvendor->mer_paynamics_status=='Publish') ? true : false)  !!} {{(Lang::has(Session::get('mer_lang_file').'.MER_PUBLISH')) ? trans(Session::get('mer_lang_file').'.MER_PUBLISH') : trans($MER_OUR_LANGUAGE.'.MER_PUBLISH')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paynamics_status', 'Unpublish',($getvendor->mer_paynamics_status=='Unpublish'|| $getvendor->mer_paynamics_status == '') ? true : false) !!}{{(Lang::has(Session::get('mer_lang_file').'.MER_UNPUBLISH')) ? trans(Session::get('mer_lang_file').'.MER_UNPUBLISH') : trans($MER_OUR_LANGUAGE.'.MER_UNPUBLISH')}}</label>
													</div>
													@if ($errors->has('mer_paynamics_status') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_status') }}</p> 
													@endif
												</div>
											</div>
											<div id="paynamicsDiv" style="@if($getvendor->mer_paynamics_status!='Publish') display:none @endif">
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_CLIENTID')) ? trans(Session::get('mer_lang_file').'.MER_CLIENTID') : trans($MER_OUR_LANGUAGE.'.MER_CLIENTID')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_paynamics_clientid',$getvendor->mer_paynamics_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_CLIENTID')) ? trans(Session::get('mer_lang_file').'.MER_CLIENTID') : trans($MER_OUR_LANGUAGE.'.MER_CLIENTID'),'id' => 'mer_paynamics_clientid','maxlength' => 200]) !!}
														@if ($errors->has('mer_paynamics_clientid') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_clientid') }}</p> 
														@endif
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_SECRETID')) ? trans(Session::get('mer_lang_file').'.MER_SECRETID') : trans($MER_OUR_LANGUAGE.'.MER_SECRETID')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_paynamics_secretid',$getvendor->mer_paynamics_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_SECRETID')) ? trans(Session::get('mer_lang_file').'.MER_SECRETID') : trans($MER_OUR_LANGUAGE.'.MER_SECRETID'),'id' => 'mer_paynamics_secretid','maxlength' => 200]) !!}
														@if ($errors->has('mer_paynamics_secretid') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_paynamics_secretid') }}</p> 
														@endif
													</div>
												</div>
												<input type="hidden" name="mer_paynamics_mode" id="mer_paynamics_mode" value="Live" />
											</div>
											<?php /* <div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_PMTMODE')) ? trans(Session::get('mer_lang_file').'.MER_PMTMODE') : trans($MER_OUR_LANGUAGE.'.MER_PMTMODE')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paynamics_mode', 'Live',($getvendor->mer_paynamics_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('mer_lang_file').'.MER_LIVE')) ? trans(Session::get('mer_lang_file').'.MER_LIVE') : trans($MER_OUR_LANGUAGE.'.MER_LIVE')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paynamics_mode', 'Sandbox',($getvendor->mer_paynamics_mode=='Live' || $getvendor->mer_paynamics_mode == '') ? true : false) !!}{{(Lang::has(Session::get('mer_lang_file').'.MER_SANDBOX')) ? trans(Session::get('mer_lang_file').'.MER_SANDBOX') : trans($MER_OUR_LANGUAGE.'.MER_SANDBOX')}}</label>
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
											<!--legend>{{(Lang::has(Session::get('mer_lang_file').'.MER_PAYMAYA')) ? trans(Session::get('mer_lang_file').'.MER_PAYMAYA') : trans($MER_OUR_LANGUAGE.'.MER_PAYMAYA')}}</legend-->
											<legend>Paypal</legend>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_PUBLISH_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_PUBLISH_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_PUBLISH_STATUS')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paymaya_status', 'Publish',($getvendor->mer_paymaya_status=='Publish') ? true : false,['id' => 'mer_paymaya_status']) !!} {{(Lang::has(Session::get('mer_lang_file').'.MER_PUBLISH')) ? trans(Session::get('mer_lang_file').'.MER_PUBLISH') : trans($MER_OUR_LANGUAGE.'.MER_PUBLISH')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paymaya_status', 'Unpublish',($getvendor->mer_paymaya_status=='Unpublish' || $getvendor->mer_paymaya_status == '') ? true : false) !!}{{(Lang::has(Session::get('mer_lang_file').'.MER_UNPUBLISH')) ? trans(Session::get('mer_lang_file').'.MER_UNPUBLISH') : trans($MER_OUR_LANGUAGE.'.MER_UNPUBLISH')}}</label>
													</div>
													@if ($errors->has('mer_paymaya_status') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_status') }}</p> 
													@endif
												</div>
											</div>
											<div id="paymayaDiv" style="@if($getvendor->mer_paymaya_status!='Publish') display:none @endif">
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_CLIENTID')) ? trans(Session::get('mer_lang_file').'.MER_CLIENTID') : trans($MER_OUR_LANGUAGE.'.MER_CLIENTID')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_paymaya_clientid',$getvendor->mer_paymaya_clientid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_CLIENTID')) ? trans(Session::get('mer_lang_file').'.MER_CLIENTID') : trans($MER_OUR_LANGUAGE.'.MER_CLIENTID'),'id' => 'mer_paymaya_clientid','maxlength' => 200]) !!}
														@if ($errors->has('mer_paymaya_clientid') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_clientid') }}</p> 
														@endif
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_SECRETID')) ? trans(Session::get('mer_lang_file').'.MER_SECRETID') : trans($MER_OUR_LANGUAGE.'.MER_SECRETID')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_paymaya_secretid',$getvendor->mer_paymaya_secretid,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_SECRETID')) ? trans(Session::get('mer_lang_file').'.MER_SECRETID') : trans($MER_OUR_LANGUAGE.'.MER_SECRETID'),'id' => 'mer_paymaya_secretid','maxlength' => 200]) !!}
														@if ($errors->has('mer_paymaya_secretid') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_paymaya_secretid') }}</p> 
														@endif
													</div>
												</div>
											</div>
											<input type="hidden" name="mer_paymaya_mode" id="mer_paymaya_mode" value="Live" />
											<?php /* 
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_PMTMODE')) ? trans(Session::get('mer_lang_file').'.MER_PMTMODE') : trans($MER_OUR_LANGUAGE.'.MER_PMTMODE')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_paymaya_mode', 'Live',($getvendor->mer_paymaya_mode=='Live') ? true : false) !!} {{(Lang::has(Session::get('mer_lang_file').'.MER_LIVE')) ? trans(Session::get('mer_lang_file').'.MER_LIVE') : trans($MER_OUR_LANGUAGE.'.MER_LIVE')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_paymaya_mode', 'Sandbox',($getvendor->mer_paymaya_mode=='Sandbox' || $getvendor->mer_paymaya_mode == '') ? true : false) !!}{{(Lang::has(Session::get('mer_lang_file').'.MER_SANDBOX')) ? trans(Session::get('mer_lang_file').'.MER_SANDBOX') : trans($MER_OUR_LANGUAGE.'.MER_SANDBOX')}}</label>
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
											<legend>{{(Lang::has(Session::get('mer_lang_file').'.MER_NETBANKING')) ? trans(Session::get('mer_lang_file').'.MER_NETBANKING') : trans($MER_OUR_LANGUAGE.'.MER_NETBANKING')}}</legend>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_PUBLISH_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_PUBLISH_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_PUBLISH_STATUS')}}:</label>
												<div class="col-sm-6">
													<div class="form-check form-check-inline">
														<label class="form-check-label">{!! Form::radio('mer_netbank_status', 'Publish',($getvendor->mer_netbank_status=='Publish') ? true : false) !!} {{(Lang::has(Session::get('mer_lang_file').'.MER_PUBLISH')) ? trans(Session::get('mer_lang_file').'.MER_PUBLISH') : trans($MER_OUR_LANGUAGE.'.MER_PUBLISH')}} </label>
														<label class="form-check-label">{!! Form::radio('mer_netbank_status', 'Unpublish',($getvendor->mer_netbank_status=='Unpublish' || $getvendor->mer_netbank_status == '') ? true : false) !!}{{(Lang::has(Session::get('mer_lang_file').'.MER_UNPUBLISH')) ? trans(Session::get('mer_lang_file').'.MER_UNPUBLISH') : trans($MER_OUR_LANGUAGE.'.MER_UNPUBLISH')}}</label>
													</div>
													@if ($errors->has('mer_netbank_status') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_netbank_status') }}</p> 
													@endif
												</div>
											</div>
											<div id="netBankDiv" style="@if($getvendor->mer_netbank_status!='Publish') display:none @endif">
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_BANKNAME')) ? trans(Session::get('mer_lang_file').'.MER_BANKNAME') : trans($MER_OUR_LANGUAGE.'.MER_BANKNAME')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_bank_name',$getvendor->mer_bank_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_BANKNAME')) ? trans(Session::get('mer_lang_file').'.MER_BANKNAME') : trans($MER_OUR_LANGUAGE.'.MER_BANKNAME'),'id' => 'mer_bank_name','maxlength' => 200]) !!}
														@if ($errors->has('mer_bank_name') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_bank_name') }}</p> 
														@endif
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_BRANCH')) ? trans(Session::get('mer_lang_file').'.MER_BRANCH') : trans($MER_OUR_LANGUAGE.'.MER_BRANCH')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_branch',$getvendor->mer_branch,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_BRANCH')) ? trans(Session::get('mer_lang_file').'.MER_BRANCH') : trans($MER_OUR_LANGUAGE.'.MER_BRANCH'),'id' => 'mer_branch','maxlength' => 200]) !!}
														@if ($errors->has('mer_branch') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_branch') }}</p> 
														@endif
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_ACCNO')) ? trans(Session::get('mer_lang_file').'.MER_ACCNO') : trans($MER_OUR_LANGUAGE.'.MER_ACCNO')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_bank_accno',$getvendor->mer_bank_accno,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_ACCNO')) ? trans(Session::get('mer_lang_file').'.MER_ACCNO') : trans($MER_OUR_LANGUAGE.'.MER_ACCNO'),'id' => 'mer_bank_accno','maxlength' => 50]) !!}
														@if ($errors->has('mer_bank_accno') ) 
														<p class="error-block" style="color:red;">{{ $errors->first('mer_bank_accno') }}</p> 
														@endif
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_IFSC')) ? trans(Session::get('mer_lang_file').'.MER_IFSC') : trans($MER_OUR_LANGUAGE.'.MER_IFSC')}}:</label>
													<div class="col-sm-6">
														{!! Form::text('mer_ifsc',$getvendor->mer_ifsc,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_IFSC')) ? trans(Session::get('mer_lang_file').'.MER_IFSC') : trans($MER_OUR_LANGUAGE.'.MER_IFSC'),'id' => 'mer_ifsc','maxlength' => 50]) !!}
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
											<legend>{{(Lang::has(Session::get('mer_lang_file').'.MER_ADDRESS')) ? trans(Session::get('mer_lang_file').'.MER_ADDRESS') : trans($MER_OUR_LANGUAGE.'.MER_ADDRESS')}}</legend>
											
											<div class="form-group">
												<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_LOCATION')) ? trans(Session::get('mer_lang_file').'.MER_LOCATION') : trans($MER_OUR_LANGUAGE.'.MER_LOCATION')}}:</label>
												<div class="col-sm-6">
													<input type="hidden" name="street_number" id="street_number" />
													<input type="hidden" name="route" id="route" />
													<input type="hidden" name="postal_code" id="postal_code" />
													{!! Form::text('mer_location',$getvendor->mer_location,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_LOCATION')) ? trans(Session::get('mer_lang_file').'.MER_LOCATION') : trans($MER_OUR_LANGUAGE.'.MER_LOCATION'),'id' => 'mer_location','required','maxlength' => 50]) !!}
													@if ($errors->has('mer_location') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_location') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_COUNTRY')) ? trans(Session::get('mer_lang_file').'.MER_COUNTRY') : trans($MER_OUR_LANGUAGE.'.MER_COUNTRY')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('country',$getvendor->mer_country,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_COUNTRY')) ? trans(Session::get('mer_lang_file').'.MER_COUNTRY') : trans($MER_OUR_LANGUAGE.'.MER_COUNTRY'),'id' => 'country','required','maxlength' => 50]) !!}
													@if ($errors->has('country') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('country') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_STATE')) ? trans(Session::get('mer_lang_file').'.MER_STATE') : trans($MER_OUR_LANGUAGE.'.MER_STATE')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('mer_state',$getvendor->mer_state,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_STATE')) ? trans(Session::get('mer_lang_file').'.MER_STATE') : trans($MER_OUR_LANGUAGE.'.MER_STATE'),'id' => 'administrative_area_level_1','maxlength' => 50]) !!}
													@if ($errors->has('mer_state') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_state') }}</p> 
													@endif
												</div>
											</div>
											
											<div class="form-group">
												<label class="control-label col-sm-2 starclass" for="email">{{(Lang::has(Session::get('mer_lang_file').'.MER_CITY')) ? trans(Session::get('mer_lang_file').'.MER_CITY') : trans($MER_OUR_LANGUAGE.'.MER_CITY')}}:</label>
												<div class="col-sm-6">
													{!! Form::text('mer_city',$getvendor->mer_city,['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_CITY')) ? trans(Session::get('mer_lang_file').'.MER_CITY') : trans($MER_OUR_LANGUAGE.'.MER_CITY'),'id' => 'locality','required','maxlength' => 50]) !!}
													@if ($errors->has('mer_city') ) 
													<p class="error-block" style="color:red;">{{ $errors->first('mer_city') }}</p> 
													@endif
												</div>
											</div>
										</fieldset>
									</div>
									
									
									<div class="col-md-offset-2">
										<span id="publish_err" style="display:none;color:red;font-size:16px">
											{{(Lang::has(Session::get('mer_lang_file').'.MER_FILL_PAY_NET')) ? trans(Session::get('mer_lang_file').'.MER_FILL_PAY_NET') : trans($MER_OUR_LANGUAGE.'.MER_FILL_PAY_NET')}}
										</span>
										<br>
										@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.MER_UPDATE')) ? trans(Session::get('mer_lang_file').'.MER_UPDATE') : trans($MER_OUR_LANGUAGE.'.MER_UPDATE') @endphp
										{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
										
										
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
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{$MAP_KEY}}&libraries=places&callback=initAutocomplete"   async defer></script>
<script>
	
	$.validator.addMethod("jsPhoneValidation", function(value, element) { 
		var defaultDial = '{{$default_dial}}';
		return value.substr(0, (defaultDial.trim().length)) != value.trim()
	}, "No space please and don't leave it empty");
	$("#profile_form").validate({
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
			//mer_phone: "required",
			mer_phone : { jsPhoneValidation : true  },
			mer_cancel_policy: "required",
			refund_status: "required",
			cancel_status: "required",
			idproof: {
				required: {
					depends: function(element) {
						if($('#old_idproof').val()==''){ return true; } else { return false; } 
					}
				}
			},
			license: {
				required: {
					depends: function(element) {
						if($('#old_license').val()==''){ return true; } else { return false; } 
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
			mer_location: "required",
			country: "required",
			mer_state: "required",
			mer_city: "required",
		},
		messages: {
			mer_fname: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_FNAME')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_FNAME') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_FNAME')}}",
			mer_lname: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_LNAME')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_LNAME') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_LNAME')}}",
			mer_email: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_EMAIL') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_EMAIL')}}",
			mer_phone: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PHONE')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PHONE') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_PHONE')}}",
			mer_cancel_policy: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CANCELPOLICY')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CANCELPOLICY') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_CANCELPOLICY')}}",
			refund_status: "{{(Lang::has(Session::get('mer_lang_file').'.MER_SELECT_REFUND')) ? trans(Session::get('mer_lang_file').'.MER_SELECT_REFUND') : trans($MER_OUR_LANGUAGE.'.MER_SELECT_REFUND')}}",
			cancel_status: "{{(Lang::has(Session::get('mer_lang_file').'.MER_SEL_CANCEL_STATUS')) ? trans(Session::get('mer_lang_file').'.MER_SEL_CANCEL_STATUS') : trans($MER_OUR_LANGUAGE.'.MER_SEL_CANCEL_STATUS')}}",
			mer_paymaya_clientid: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_CLIENT')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_CLIENT') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_PAYMA_CLIENT')}}",
			mer_paymaya_secretid: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_SECRET')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYMA_SECRET') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_PAYMA_SECRET')}}",
			mer_paynamics_clientid: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_CLIENT')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_CLIENT') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_PAYNA_CLIENT')}}",
			mer_paynamics_secretid: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_SECRET')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_PAYNA_SECRET') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_PAYNA_SECRET')}}",
			mer_bank_name: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_BANK')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_BANK') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_BANK')}}",
			mer_branch: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_BRANCH')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_BRANCH') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_BRANCH')}}",
			mer_bank_accno: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_ACCNO')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_ACCNO') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_ACCNO')}}",
			mer_ifsc: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_IFSC')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_IFSC') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_IFSC')}}",
			mer_location: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_LOCATION')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_LOCATION') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_LOCATION')}}",
			country: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_COUNTRY')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_COUNTRY') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_COUNTRY')}}",
			mer_state: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_STATE')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_STATE') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_STATE')}}",
			mer_city: "{{(Lang::has(Session::get('mer_lang_file').'.MER_ENTER_CITY')) ? trans(Session::get('mer_lang_file').'.MER_ENTER_CITY') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_CITY')}}",
			
		},
		errorPlacement: function(error, element) 
		{
			if (element.attr("name") == "idproof") 
			{
				error.insertAfter($('#idErr'));
			}
			else if(element.attr("name") == "license")
			{
				error.insertAfter($('#liErr'));	
			}
			else 
			{ 
				error.insertAfter( element );
			}
			//error.insertAfter( element );
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
	// Get the modal
	/*var modal = document.getElementById('myModal');

	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var img = document.getElementById('myImg');
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");
	img.onclick = function(){
		modal.style.display = "block";
		modalImg.src = this.src;
		captionText.innerHTML = this.alt;
	}

	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];

	// When the user clicks on <span> (x), close the modal
	span.onclick = function() { 
		modal.style.display = "none";
	}*/
	
	function validate() {
		var defaultDial = '{{$default_dial}}';
		var element = document.getElementById('mer_phone');
		if(element.value=='' || element.value.length < defaultDial.trim().length)
		{

		$('#mer_phone').val('{{$default_dial}}');

		}
		element.value = element.value.replace(/[^0-9 +]+/, '');
	}
	$(document).ready(function(){
		var element = document.getElementById('mer_phone');
		if(element.value=='')
		{
			$('#mer_phone').val('{{$default_dial}}');
		}
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

	function check_publish_status()
	{
			var paypal_status = $('input[name=mer_paymaya_status]:checked').val();
        	var offline_status = $('input[name=mer_netbank_status]:checked').val();
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

	function Upload(files,widthParam,heightParam)
	{
		var fileUpload = document.getElementById(files);

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

						if (height < heightParam || width < widthParam)
						{
							//document.getElementById("image_valid_error").style.display = "inline";
							//$("#image_valid_error").fadeOut(9000);
							alert('Please select image above '+widthParam+'X'+heightParam);
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
		else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}
	}
</script>
<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
   <script type="text/javascript">
    //$("#mer_phone").intlTelInput();
	$("#mer_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
   </script>
@endsection
@stop