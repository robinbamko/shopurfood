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
			<h1 class="page-header">{{$pagetitle}}</h1>
			<div class="container-fluid add-country">
				<div class="row">
					<div class="container right-container">
						<div class="r-btn">
						</div>
						<div class="col-md-12">
							<div class="location panel">
								<div class="panel-heading p__title">
									{{$pagetitle}}
									<a id="click-here" class="btn btn-default fa fa-bars" href="javascript:;" role="button" @if($id!='')  onclick="new_change()" @else onclick="change()" @endif style="float:right" data-toggle="collapse" data-target = "#location_form"> {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_BANNER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_BANNER')}}</a>
								</div>


								{{-- Display error message--}}
								<div class="err-alrt-msg">
									@if ($errors->any())
										<div class="alert alert-warning alert-dismissible">
											<a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i></a>
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
								</div>
								@endif


								{{-- Add/Edit page starts--}}
								<div class="box-body spaced box-body-padding">
									<div id="location_form" class="collapse @php  if($id!=''){ echo 'in';} @endphp panel-body">
										{{--Edit page values--}}
										@php $image_title = $image_text = $banner_image = $banner_type =  $banner_status = $ba_id ='';
										@endphp
										@if($id != '' && empty($image_detail) === false)
											@php
												$image_title = $image_detail->image_title;
                                                $image_text = $image_detail->image_text;
                                                $banner_image = $image_detail->banner_image;
                                                $banner_type = $image_detail->banner_type;
                                                $banner_status = $image_detail->banner_status;
                                                $ba_id = $image_detail->id;
											@endphp
										@endif
										{{--Edit page values--}}
										<div class="row-fluid well">
											@if($id != '' && empty($image_detail) === false)
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-banner','id'=>'manage_banner_settings','enctype'=>'multipart/form-data']) !!}
												{!! Form::hidden('banner_id',$ba_id,array('id'=>'banner_id'))!!}
											@else
												{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-banner','id'=>'manage_banner_settings','enctype'=>'multipart/form-data']) !!}
												{!! Form::hidden('banner_id',$ba_id,array('id'=>'banner_id'))!!}
											@endif
											{{--<div class="row panel-heading">--}}
												{{--<div class="col-md-4">--}}
												{{--<span class="panel-title">--}}
													{{--{{ Form::label('image_title_label', (Lang::has(Session::get('admin_lang_file').'.ADMIN_IMAGE_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMAGE_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IMAGE_TITLE'),array('class'=>'control-label col-sm-12 starclass')) }}--}}

												{{--</span>--}}
												{{--</div>--}}
												{{--<div class="col-md-8">--}}
													{{--@php $image_title = ''; @endphp--}}
													{{--@if($id != '' && empty($image_detail) === false)--}}
														{{--@php--}}
															{{--$image_title = $image_detail->image_title;--}}
														{{--@endphp--}}
													{{--@endif--}}
													{{--{!! Form::text('image_title',$image_title,['class'=>'form-control','id' => 'image_title','required','maxlength'=>100]) !!}--}}
													{{--<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>--}}
												{{--</div>--}}
											{{--</div>--}}

											<div class="row panel-heading">
												<div class="col-md-4">
												<span class="panel-title">
													{{ Form::label('image_text_label', (Lang::has(Session::get('admin_lang_file').'.ADMIN_IMAGE_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMAGE_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IMAGE_TEXT'),array('class'=>'control-label col-sm-12 starclass')) }}
												</span>
												</div>
												<div class="col-md-8">
													{!! Form::text('image_text',$image_text,['class'=>'form-control','id' => 'image_text','required','maxlength'=>100]) !!}
												</div>
											</div>
											<div class="row panel-heading">
												<div class="col-md-4">
												<span class="panel-title">
													{{ Form::label('banner_imagelabel', (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_IMAGE'),array('class'=>'control-label col-sm-12 starclass')) }}
												</span>
												</div>
												<div class="col-md-8">
													@php $banner_image = null; @endphp
													@if($id != '' && empty($image_detail) === false)
														{{ Form::file('banner_image',array('class' => 'form-control','accept'=>'image/*','id'=>'banner_image','onchange'=>'Upload2(this.id,"1366","500","1500","500");')) }}
														<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_IMAGE_VAL')}}</p>
														
														<img src="{{url('public/images/banner/'.$image_detail->banner_image)}}" width="140px" height="30px">
														<input type="hidden" name="oldBanner" value="{{$image_detail->banner_image}}" />
													@else
														{{ Form::file('banner_image',array('class' => 'form-control','id'=>'banner_image','accept'=>'image/*','onchange'=>'Upload2(this.id,"1366","500","1500","500");')) }}
														
														
														<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_IMAGE_VAL')}}</p>
													
													@endif


												</div>
											</div>
											<div class="row panel-heading" style="display:none;">
												<div class="col-md-4">
												<span class="panel-title">
													{{ Form::label('banner_typeLabel', (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_TYPE'),array('class'=>'control-label col-sm-12 starclass')) }}
												</span>
												</div>
												<div class="col-md-8">
													@php $banner_type = null; @endphp
													@if($id != '' && empty($image_detail) === false)
														@php
															$banner_type = $image_detail->banner_type;
														@endphp
													@endif

													@php
														if($id=='')
                                                        {
                                                            $banner_type_array = array(''=>'--select--','1'=>'Store','2'=>'Restaurant');
                                                        }
                                                        else
                                                        {
                                                            $banner_type_array = array(''=>'--select--','1'=>'Store','2'=>'Restaurant');
                                                        }
													@endphp
													{!! Form::select('banner_type',$banner_type_array,2,['class'=>'form-control','id' => 'curr_code','required']) !!}
												</div>
											</div>

											<div class="panel-heading col-md-offset-4">

												@if($id!='')
													@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
												@else
													@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
												@endif

												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												@if($id!='')
													<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('admin-banner-settings'); ?>'">
												@else
													{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'data-toggle'=>"collapse", 'data-target'=>"#location_form"])!!}
												@endif
											</div>
											{!! Form::close() !!}
										</div>
									</div>
								</div>
								{{-- Add page ends--}}
								{{-- Manage list starts--}}
								<div class="panel-body location-scroll" id="location_table">
									{{--
                                    <div class="panel-heading p__title">
                                        Manage List
                                    </div>
                                    --}}
									<table class="table table-striped table-bordered table-hover" id="dataTables-example1" style="text-align:center">
										<thead>
										<tr>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_IMAGE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_IMAGE_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_IMAGE_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IMAGE_TYPE')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
											</th>
											<th>
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
											</th>
										</tr>
										</thead>
										<tbody>
										@if(count($all_details) > 0)
											@php $i = ($all_details->currentpage()-1)*$all_details->perpage()+1;
										$co_name = 'co_name';
											@endphp
											@foreach($all_details as $details)

												<tr>
													<td width="9%">{{$i}}</td>
													{{--<td width="35%">{{ ucfirst($details->image_title) }}</td>--}}
													@if($details->banner_image != '')

														<td><img style="width:120px; height:40px;" src="{{ url('')}}/public/images/banner/{{ $details->banner_image }}" /></td>
													@endif
													<td width="20%">@if($details->banner_type == 1) {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_STORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STORE')}}  @elseif($details->banner_type == 2) {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTS')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTS')}}  @endif</td>
													<td width="12%"><a href="{!! url('edit_banner').'/'.base64_encode($details->id) !!}"><i class="fa fa-pencil tooltip-demo" aria-hidden="true" title="Edit"></i></a></td>
													<td width="12%">
														@if($details->banner_status == 1)  {{--0-block, 1- active --}}
														<a href="{!! url('banner_status').'/'.$details->id.'/0' !!}"><i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i></a>
														@else
															<a href="{!! url('banner_status').'/'.$details->id.'/1' !!}"><i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" onClick="change_status($details->id)"></i></a>
														@endif
													</td>
													<td width="12%">

														<a href= "{!! url('banner_status').'/'.$details->id.'/2' !!}" title="delete" class="tooltip-demo">
															<i class="fa fa-trash tooltip-demo" aria-hidden="true" ></i>

														</a>
													</td>
												</tr>
												@php $i++; @endphp
											@endforeach
										@else
											{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS')}}
										@endif
										</tbody>
									</table>
									@if(count($all_details) > 0)
										{!! $all_details->render() !!}
									@endif
								</div>
								{{-- Manage list ends--}}
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
	<script>
        $(document).ready(function() {


            table = $('#dataTables-example1').DataTable({
                "scrollX": true,
                "paging": false
            });

        });
	</script>
	<script>
        $(document).ready(function(){
            //$("#location_form").hide();
            /*$("#click-here").click(function(){
                $("#location_form").toggle();
                //$("#location_table").toggle();
         });*/
            var tech = getUrlParameter('addnow');
            if(tech==1)
            {
                $('#location_form').addClass('in');
            }

        });

        // function change() {
        // document.getElementById("click-here").text="Manage List";
        // }

        function change() {
            var elem = document.getElementById("click-here");
            var add = "{{trans(Session::get('admin_lang_file').'.ADMIN_ADD_BANNER')}}";
            var manage = "{{trans(Session::get('admin_lang_file').'.ADMIN_MNGE_LIST')}}";
            //alert(add);
            if (elem.text== add)
            {
                elem.text = " Manage List";
            }
            else if (elem.text==manage)
            {
                elem.text = add;
            }
            else {
                elem.text = manage;
        	}
        }
        function new_change()
        {
            location.href='{{url('admin-banner-settings?addnow=1')}}';

        }
	</script>
	<script type="text/javascript">
        $('#cnty_name').keyup(function(){
            var key = $('#cnty_name').val();
            $('#cnty_code').val('');
            $('#curr_sym').val('');
            $('#curr_code').val('');
            $('#tel_code').val('');
            if(key == '')
            {
                return;
            }

            $.ajax({
                type: 'get',
                url: '{{url('array_search_country')}}',
                data: {searched_country : key},
                success: function(response){
                    myContent=response;

                    result=$(myContent).text();

                    if($.trim(result)==''){

                        $("#suggesstion-box").show();
                        $("#suggesstion-box").text("No country found!");
                        return false;
                    }

                    // alert(response);
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(response);
                    $("#cnty_name").css("background","#FFF");

                }
            });
        });

        function selectCountry(val) {

            $("#cnty_name").val(val);
            $("#suggesstion-box").hide();
            var searched_country_name=val;

            $.ajax({
                type: 'get',
                url: '{{url('add_searched_country')}}',
                data: {'searched_country_name': searched_country_name},

                success: function(response){
                    //alert(response); return false;
                    id_numbers = response.split('||');
                    var Country_code=id_numbers[0];
                    var Country_name=id_numbers[1];
                    var currency_symbol=id_numbers[2];
                    var currency_code=id_numbers[3];
                    var dial_code=id_numbers[4];

                    $('#cnty_code').val(Country_code);
                    $('#curr_sym').val(currency_symbol);
                    $('#curr_code').val(currency_code);
                    $('#tel_code').val('+'+dial_code);

                }
            });
        }


        $('#cnty_name').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^a-z]/g,'') ); }
        );
	</script>
	<script>
        function make_default(val)
        {
            $.ajax({
                type: 'get',
                url: 'country_default',
                data: {'co_id': val},
                success: function(response){
                    location.reload();

                }
            });
        }

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };
		
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

	<!-- <script type="text/javascript">
        $("#manage_banner_settings").validate({
            //onkeyup: true,
            onfocusout: function (element) {
                this.element(element);
            },
            rules: {
                image_title: "required",
                image_text: "required",
                "banner_image": {
                    required: {
                        depends: function(element) {
                            console.log('hi');
                            if($('#banner_id').val()==''){ return true; } else { return false; }
                        }
                    }
                },

                banner_type: "required",

            },
            messages: {
                image_title: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_IMAGE_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_IMAGE_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_IMAGE_TITLE')}}",
                image_text: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_IMAGE_TEXT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_IMAGE_TEXT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_IMAGE_TEXT')}}",
                banner_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_BANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_BANNER_IMAGE')}}",
                banner_type: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_BANNER_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_BANNER_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_BANNER_TYPE')}}",

            }
        });

	</script> -->

@endsection
@stop