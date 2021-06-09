
@extends('Admin.layouts.default')
@section('PageTitle')
    @if(isset($pagetitle))
        {{$pagetitle}}
    @endif
@stop
@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MANAGE_ADVERTISE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MANAGE_ADVERTISE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MANAGE_ADVERTISE')}}</h1>

            <div class="container-fluid">
                <div class="row">
                    <div class="container right-container">
                        <div class="r-btn">
                        </div>
                        <div class="col-md-12">
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

                                @php $ad_id = $ad_title = $ad_desc = $ad_image = $ad_link =  '';@endphp
                                @if(count($adv_details) > 0)
                                   @foreach($adv_details as $det)
                                       @php $ad_id = $det->ad_id;
                                      $ad_title = $det->ad_title;
                                      $ad_link = $det->ad_link;
                                      $ad_desc =  $det->ad_desc;
                                      $ad_image = $det->ad_image;
                                       @endphp

                                    <div class="panel-body">
										{{ Form::open(array('url' => 'admin-advertisement-submit','method' => 'post','id'=>'advertisement_form','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}

                                        {{ Form::hidden('adv_id',$ad_id,array('id'=>'adv_id','class' => 'form-control')) }}

                                        <div class="form-group">
                                            {{ Form::label('advertisement_title', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE'),array('class'=>'control-label col-sm-2 starclass')) }} 
                                            <div class="col-sm-6">
                                                {{ Form::text('advertisement_title',$ad_title,array('class' => 'form-control','maxlength'=>'30','minlength'=>'5','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE'))) }}</div>
                                        </div>


                                        @if(count($Admin_Active_Language) > 0)
                                            @foreach($Admin_Active_Language as $lang)
                                                @php $add_title = 'ad_title_'.$lang->lang_code @endphp
                                                <div class="form-group">
                                                    <label class="control-label col-sm-2 starclass" for="advertisement_title">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE')}}&nbsp; (In {{$lang->lang_name}})</label>
                                                    <div class="col-sm-6">
                                                        {!! Form::text('advertisement_title_'.$lang->lang_code.'',$det->$add_title,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE'),'class'=>'form-control','maxlength'=>'300','minlength'=>'5','required')) !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="form-group">
                                            {{ Form::label('advertisement_link', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_LINK'),array('class'=>'control-label col-sm-2')) }} 
                                            <div class="col-sm-6">
                                                {{ Form::text('advertisement_link',$ad_link,array('class' => 'form-control','maxlength'=>'60','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_LINK'))) }}</div>
                                        </div>



                                        <div class="form-group">
                                            {{ Form::label('advertisement_desc', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC'),array('class'=>'control-label col-sm-2 starclass')) }} 
                                            <div class="col-sm-6">
                                                {{ Form::textarea('advertisement_desc',$ad_desc,array('class' => 'form-control','maxlength'=>'200','minlength'=>'80','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC'))) }}</div>
                                        </div>

                                        @if(count($Admin_Active_Language) > 0)
                                            @foreach($Admin_Active_Language as $lang)
                                                @php $add_desc = 'ad_desc_'.$lang->lang_code @endphp
                                                <div class="form-group">
                                                    <label class="control-label col-sm-2" for="advertisement_desc">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC')}}&nbsp; (In {{$lang->lang_name}}) <span class="impt">*</span></label> 
                                                    <div class="col-sm-6">
                                                        {!! Form::textarea('advertisement_desc_'.$lang->lang_code.'',$det->$add_desc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC'),'class'=>'form-control','maxlength'=>'200','minlength'=>'80','required')) !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        <!-- ------------Advertise image start------------- -->
                                        <div class="form-group">
                                            {{ Form::label('advertisement_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_IMAGE'),array('class'=>'control-label col-sm-2 starclass')) }} 
                                            <div class="col-sm-6">
                                                {{ Form::file('advertisement_image',array('class' => 'form-control','accept'=>'image/*','id'=>'add_image','onchange'=>'Upload(this.id,"1360","390","1370","400");')) }}
                                                <span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_IMAGE_VAL')}}</span>
                                                @if($ad_image != '')
                                                    <img src="{{url('public/front/frontImages/').'/'.$ad_image}}" width="140px" height="50px">
                                                @endif
                                            </div>

                                            <input type="hidden" name="pre_adv_image" id="pre_adv_image" value="{{ $ad_image }}">

                                            <div class="radioError"></div>
                                        </div>
                                        <!-- ------------Advertise image end------------- -->

                                        <div class="form-group">
                                            <div class="col-sm-2"></div>
                                            <div class="col-sm-4">
                                                {{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
                                            </div>
                                        </div>

                                        {{ Form::close() }}
                                    </div>
                                        @endforeach
                                    @else

                                <div class="panel-body">
                                    {{ Form::open(array('url' => 'admin-advertisement-submit','method' => 'post','id'=>'advertisement_form','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}

                                    {{ Form::hidden('adv_id','',array('id'=>'adv_id','class' => 'form-control')) }}

                                    <div class="form-group">
                                        {{ Form::label('advertisement_title', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE'),array('class'=>'control-label col-sm-2 starclass')) }}
                                        <div class="col-sm-6">
                                            {{ Form::text('advertisement_title','',array('class' => 'form-control','maxlength'=>'30','minlength'=>'5','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE'))) }}</div>
                                    </div>

                                    @if(count($Admin_Active_Language) > 0)
                                        @foreach($Admin_Active_Language as $lang)

                                            <div class="form-group">
                                                <label class="control-label col-sm-2 starclass" for="advertisement_title">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE')}}&nbsp; (In {{$lang->lang_name}})</label>
                                                <div class="col-sm-6">
                                                    {!! Form::text('advertisement_title_'.$lang->lang_code.'','',array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_TITLE'),'class'=>'form-control','maxlength'=>'300','minlength'=>'5',)) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="form-group">
                                        {{ Form::label('advertisement_link', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_LINK'),array('class'=>'control-label col-sm-2')) }} 
                                        <div class="col-sm-6">
                                            {{ Form::text('advertisement_link',$ad_link,array('class' => 'form-control','maxlength'=>'60','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_LINK'))) }}</div>
                                    </div>

                                    <div class="form-group">
                                        {{ Form::label('advertisement_desc', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC'),array('class'=>'control-label col-sm-2 starclass')) }} 
                                        <div class="col-sm-6">
                                            {{ Form::textarea('advertisement_desc','',array('class' => 'form-control','maxlength'=>'200','minlength'=>'80','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC'))) }}</div>
                                    </div>

                                    @if(count($Admin_Active_Language) > 0)
                                        @foreach($Admin_Active_Language as $lang)

                                            <div class="form-group">
                                                <label class="control-label col-sm-2 starclass" for="advertisement_desc">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC')}}&nbsp; (In {{$lang->lang_name}}) </label>
                                                <div class="col-sm-6">
                                                    {!! Form::textarea('advertisement_desc_'.$lang->lang_code.'','',array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_DESC'),'class'=>'form-control','maxlength'=>'200','minlength'=>'80',)) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <!-- ------------Advertise image start------------- -->
                                    <div class="form-group">
                                        {{ Form::label('advertisement_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_IMAGE'),array('class'=>'control-label col-sm-2 starclass')) }} 
                                        <div class="col-sm-6">
                                            {{ Form::file('advertisement_image',array('class' => 'form-control','accept'=>'image/*','id'=>'add_image','onchange'=>'Upload(this.id,"1360","390","1370","400");')) }}
                                            <span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_IMAGE_VAL')}}</span>
											<input type="hidden" name="pre_adv_image" id="pre_adv_image" value=""><!-- FOR VALIDATION-->
                                        </div>



                                        <div class="radioError"></div>
                                    </div>
                                    <!-- ------------Advertise image end------------- -->

                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-4">
                                            {{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
                                        </div>
                                    </div>

                                    {{ Form::close() }}
                                </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('script')
        <script type="text/javascript">
		/*	$.validator.addMethod("jsPhoneValidation", function(value, element) { 
		//return value.trim().length != 0; 
		var defaultDial = '{{Config::get('config_default_dial')}}';
		return value.substr(0, (defaultDial.trim().length)) != value.trim()
	}, "No space please and don't leave it empty");*/
			jQuery.extend(jQuery.validator.messages, {
				minlength: jQuery.validator.format('Please enter at least {0} characters.'),
				maxlength: jQuery.validator.format('Please enter below {0} characters.'),
			});

            $("#advertisement_form").validate({
                onfocusout: function (element) {
                    this.element(element);
                },
                rules: {
                    advertisement_link : {
                        // required: true,
                       url: true,
                   },
                    "advertisement_title":{
                        required: true,
						minlength:5,
						maxlength:30,
                    },
					@if(count($Admin_Active_Language) > 0)
                        @foreach($Admin_Active_Language as $lang)
							"advertisement_title_{{$lang->lang_code}}":{
								required: true,
								minlength:5,
								maxlength:300,
							},
						@endforeach
					@endif
                    "advertisement_desc":{
                        required: true,
						minlength:80,
						maxlength:200,
						
                    },
					@if(count($Admin_Active_Language) > 0)
                        @foreach($Admin_Active_Language as $lang)
							"advertisement_desc_{{$lang->lang_code}}":{
								required: true,
								minlength:80,
								maxlength:200,
							},
						@endforeach
					@endif
					advertisement_image: {
						required: {
							depends: function(element) {
								if($('input[name=pre_adv_image]').val()==''){ return true; } else { return false; } 
							}
						}
					},

                },
                messages: {
                    advertisement_link: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_VALID_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_VALID_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_VALID_LINK')}}",
					/*advertisement_title: {
						required: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_TITLE_REC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_TITLE_REC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADVERTISE_TITLE_REC')}}",
						minlength: $.format("Enter at least {0} characters"),
						maxlength: $.format("Enter below {0} characters")
					},
                   // advertisement_title: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_TITLE_REC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_TITLE_REC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADVERTISE_TITLE_REC')}}",
					@if(count($Admin_Active_Language) > 0)
                        @foreach($Admin_Active_Language as $lang)
						@php 
							$err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_TITLE_REC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_TITLE_REC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADVERTISE_TITLE_REC');
							$err_msg .=' ('.$lang->lang_name.') ';
						@endphp
							advertisement_title_{{$lang->lang_code}}: "{{$err_msg}}",
						@endforeach
					@endif

                    advertisement_desc: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_DESC_REC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_DESC_REC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADVERTISE_DESC_REC')}}",
					
					@if(count($Admin_Active_Language) > 0)
                        @foreach($Admin_Active_Language as $lang)
						@php 
							$err_msg = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_DESC_REC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADVERTISE_DESC_REC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADVERTISE_DESC_REC');
							$err_msg .=' ('.$lang->lang_name.') ';
						@endphp
							advertisement_desc_{{$lang->lang_code}}: "{{$err_msg}}",
						@endforeach
					@endif*/
					
					advertisement_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_IMAGE')}}",
                }
            });
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
    @endsection
@stop
