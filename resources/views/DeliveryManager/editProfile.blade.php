@extends('DeliveryManager.layouts.default')
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

		@media only screen and (max-width:767px)
		{
			.box-body{ padding:5px!important;}
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
								<div class="box-body spaced" style="padding:20px">

									<div class="">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => 'delivery-managerprofile','id'=>'profile_form','enctype'=>'multipart/form-data']) !!}
                                        <?php //print_r($getvendor); exit; ?>
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_REG_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_REG_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_REG_NAME'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::text('dm_name',$getvendor->dm_name,array('required','placeholder'=> (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_REG_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_REG_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_REG_NAME'),'class'=>'form-control','maxlength'=>'100','id' => 'dm_name')) !!}
											</div>
										</div>
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_REG_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_REG_MAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_REG_MAIL'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::email('dm_email',$getvendor->dm_email,array('required','placeholder'=> (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_REG_MAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_REG_MAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_REG_MAIL'),'class'=>'form-control','maxlength'=>'250','id' => 'dm_email')) !!}
												{!! Form::hidden('old_email',$getvendor->dm_email)!!}
											</div>
										</div>

										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PHONE'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::text('dm_phone',$getvendor->dm_phone,array('required','placeholder'=> (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PHONE'),'class'=>'form-control','maxlength'=>'50','id' => 'dm_phone','onkeyup'=>'validate_phone(\'dm_phone\');')) !!}
											</div>
										</div>


										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PROFILE_PHOTO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PROFILE_PHOTO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PROFILE_PHOTO')}}:</label>
											<div class="col-sm-6">
												{!! Form::file('photo',array('class'=>'form-control col-md-7 col-xs-12','accept'=>'image/*')) !!}
												{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DIMENSION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DIMENSION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DIMENSION')}} : 300 * 300
												{{ Form::hidden('old_profile_foto', $getvendor->dm_imge) }}
												@if ($errors->has('profile_photo') )
													<p class="error-block" style="color:red;">{{ $errors->first('profile_photo') }}</p>
												@endif
											</div>
										</div>
										@if($getvendor->dm_imge!='')
											<div class="form-group">
												<label class="control-label col-sm-2">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPLOADED_PHOTO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPLOADED_PHOTO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UPLOADED_PHOTO')}}:</label>
												<div class="col-sm-6">
													<img id="myImg" src="{{url('public/images/delivery_manager/'.$getvendor->dm_imge)}}"  alt="{{$getvendor->dm_name}}" style="width:20%;">
													<!-- The Modal -->
													<div id="myModal" class="modal">
														<span class="close">&times;</span>
														<img class="modal-content" id="img01">
														<div id="caption"></div>
													</div>

												</div>

											</div>
										@endif


										<div class="form-group">
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
												@php $saveBtn=(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_UPDATE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_UPDATE') @endphp
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}

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
	<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
	<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
	<script>
        $('#category_name').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^a-z]/g,'') ); }
        );


        $("#profile_form").validate({
            onfocusout: function (element) {
                this.element(element);
            },
            rules: {
                name: "required",
                email: {required: true,email: true},
                phone: "required"
            },
            messages: {
                mer_fname: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_NAME')}}",
                mer_email: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_EMAIL')}}",
                mer_phone: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_PHONE')}}"
            }
        });
        var modal = document.getElementById('myModal');
		@if($getvendor->dm_imge!='')
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var img = document.getElementById('myImg');
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");
        img.onclick = function(){
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }
		@endif
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        function validate() {

            var element = document.getElementById('dm_phone');
            if(element.value=='')
            {

                $('#dm_phone').val('{{$default_dial}}');

            }
            element.value = element.value.replace(/[^0-9 +]+/, '');
        }
        $(document).ready(function(){
            var element = document.getElementById('dm_phone');
            if(element.value=='')
            {
                $('#dm_phone').val('{{$default_dial}}');
            }
        });
	</script>
	<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
	<script type="text/javascript">
        //$("#mer_phone").intlTelInput();
        $("#dm_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
	</script>
@endsection
@stop