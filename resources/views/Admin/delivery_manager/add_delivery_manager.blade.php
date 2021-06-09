@extends('Admin.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')
<!-- MAIN -->
<style>
	.banner {
    display: inline-block;
    float: left;
    width: 40%;
    margin-right: 5px;
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
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
								<i class="fa fa-times-circle"></i>{{ Session::get('message') }}
							</div>
							@endif
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:20px">
								<div id="location_form" class="panel-body">
									<div class="">
										
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => $url,'id'=>'validate_form','enctype' => 'multipart/form-data']) !!}
										{!! Form::hidden('id',$details->dm_id)!!}
										
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_NAME'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::text('name',$details->dm_name,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_NAME'),'class'=>'form-control','maxlength'=>'100','id' => 'name')) !!}
											</div>
										</div>
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_MAIL'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::email('dm_email',$details->dm_email,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_MAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_MAIL'),'class'=>'form-control','maxlength'=>'250','id' => 'dm_email')) !!}
												{!! Form::hidden('old_email',$details->dm_email)!!}	
											</div>
										</div>
										{!! Form::hidden('old_password',$details->dm_real_password)!!}
										@if($details->dm_password == '')
											<div class="form-group">
												{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_PASS'),['class' => 'control-label col-sm-2 require']) !!}
												<div class="col-sm-6">
													{!! Form::text('password','',array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_PASS'),'class'=>'form-control','maxlength'=>'50','id' => 'password')) !!}
												</div>
											</div>
										@else
											<div class="form-group">
												{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_PASS'),['class' => 'control-label col-sm-2 require']) !!}
												<div class="col-sm-6">
													{!! Form::text('password',$details->dm_real_password,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_REG_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REG_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REG_PASS'),'class'=>'form-control','maxlength'=>'50','id' => 'password')) !!}
												</div>
											</div>
										@endif
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::text('phone',$details->dm_phone,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PHONE'),'class'=>'form-control','maxlength'=>'50','id' => 'phone','onkeyup'=>'validate_phone(\'phone\');')) !!}
											</div>
										</div>
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_PROFILE_PHOTO')) ? trans(Session::get('admin_lang_file').'.ADMIN_PROFILE_PHOTO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PROFILE_PHOTO'),['class' => 'control-label col-sm-2']) !!}
											<div class="col-sm-6">
												@if($details->dm_imge != '')
												{!! Form::file('photo',array('class'=>'form-control','id' => 'photo','accept'=>'image/*','onchange'=>'Upload2(this.id,"300","300","500","500");')) !!}
												<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELMNGR_PROFILE_PHOTO')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELMNGR_PROFILE_PHOTO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELMNGR_PROFILE_PHOTO')}}</p>
												{!! Form::hidden('old_photo',$details->dm_imge) !!}
												{{ Form::image(url('public/images/delivery_manager/'.$details->dm_imge), 'alt text', array('class' => '','width'=>'100px','height'=>'100px','id' => 'photo')) }}
												
												@else
												{!! Form::file('photo',array('class'=>'form-control','id' => 'photo','accept'=>'image/*','onchange'=>'Upload2(this.id,"300","300","500","500");')) !!}
												
												
												<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELMNGR_PROFILE_PHOTO')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELMNGR_PROFILE_PHOTO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELMNGR_PROFILE_PHOTO')}}</p>
												
												@endif
											</div>
										</div>
										
										<div class="form-group">
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
												@if($details->dm_id !='')
												@php 
												$saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
												@else
												@php 
												$saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
												@endif
												@php $url = url('manage-delivery-manager')@endphp
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												{!! Form::button('Cancel',['class' => 'btn btn-warning','onclick' => "javascript:window.location.href='$url'"])!!}
											</div>
										</div>
										
										{!! Form::close() !!}
									</div>
								</div>
							</div>
							{{-- Add/Edit page ends--}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	
							
							@section('script')
							
							<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
							<script>
								$('#name').bind('keyup blur',function(){ 
									var node = $(this);
								node.val(node.val().replace(/[^A-Z a-z]/g,'') ); }
								);
								
								/*$('#phone').bind('keyup blur',function(){ 
									var node = $(this);
									node.val(node.val().replace(/[^0-9]/g,'') ); }
								);*/
								$(document).ready(function() {
									$('#d-table').DataTable({
										responsive: true
									});
									var element = document.getElementById('phone');
									if(element.value=='')
									{
										$('#phone').val('{{$default_dial}}');
									}
									
									
								});
								
							</script>
							
							<script>
								$.validator.addMethod("jsPhoneValidation", function(value, element) { 
									var defaultDial = '{{Config::get('config_default_dial')}}';
									return value.substr(0, (defaultDial.trim().length)) != value.trim()
								}, "No space please and don't leave it empty");
								$("#validate_form").validate({
									rules: {
										name: "required",
										dm_email: "required",
										password: "required",
										phone : { jsPhoneValidation : true  },
										//phone: "required",
									},	
									messages: {
										name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_FNAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_FNAME')}}",
										dm_email: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
										password: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PASS_VAL')}}",
										phone: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
									}
									
								});
								function Upload2(files,widthParam,heightParam,maxwidthparam,maxheightparam)
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
								$("#phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
							</script>
							@endsection
						@stop						