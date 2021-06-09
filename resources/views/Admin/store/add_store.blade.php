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
							<div class="panel-heading p__title">
								{{$pagetitle}}
								
							</div>
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
							<div class="box-body spaced box-body-padding">
								<div id="location_form" class="panel-body">
									<div class="row-fluid well">
										
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => $url,'id'=>'validate_form','enctype' => 'multipart/form-data']) !!}
										{!! Form::hidden('st_id',$getstore->id,['id' => 'store_id'])!!}
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_MER')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_MER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_MER'),['class' => 'control-label col-sm-2 require']) !!} 
											
											<div class="col-sm-6">
												@if(count($merchant_list) > 0)
												
												{{ Form::select('mer_fname',($merchant_list),$getstore->st_mer_id,['class' => 'form-control' , 'style' => 'width:100%','required','id' => 'select_merchant'] ) }}
												@elseif($getstore->st_mer_id != '')
												@php $merchant = get_details('gr_merchant',['id' => $getstore->st_mer_id],'mer_fname'); @endphp
												{!! Form::text('mer_fname',(empty($merchant) === false) ? $merchant->mer_fname : '',['disabled','class' => 'form-control'])!!}
												@else
												{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS') }}
												@endif 
												@if ($errors->has('mer_fname') ) 
												<p class="error-block" style="color:red;">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_CATE') }}</p> 
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_CATE')}}* :</label>
											<div class="col-sm-6">
												@if(count($category_list) > 0)
												
												{{ Form::select('cate_name',($category_list),$getstore->st_category,['class' => 'form-control' , 'style' => 'width:100%','required'] ) }}
												@elseif($getstore->st_category != '')
												@php $category = get_details('gr_category',['cate_id' => $getstore->st_category],'cate_name'); @endphp
												{!! Form::text('cate_name',(empty($category) === false) ? $category->cate_name : '',['disabled','class' => 'form-control'])!!}
												@else
												{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS') }}
												@endif 
												@if ($errors->has('cate_name') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('cate_name') }}</p> 
												@endif
											</div>
										</div>
										<div class="form-group">
											{!! Form::label('', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_NAME'),['class' => 'control-label col-sm-2 require']) !!}
											<div class="col-sm-6">
												{!! Form::text('st_name',$getstore->st_store_name,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_NAME'),'class'=>'form-control','maxlength'=>'100','id' => 'st_name')) !!}
											</div>
										</div>
										@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
										@php $name = 'st_store_name_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_NAME')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! Form::text('st_name_'.$lang->lang_code.'',$getstore->$name,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_NAME'),'class'=>'form-control','maxlength'=>'100')) !!}
												
											</div>
										</div>
										@endforeach
										@endif
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MINIMUM_ORDER_COUNT')}}:</label>
											<div class="col-sm-2">
												{!! Form::text('curr_code',$default_currency,['class'=>'form-control','readonly'])!!}
											</div>
											<div class="col-sm-4">
												{!! Form::text('min_order_amt',$getstore->st_minimum_order,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MINIMUM_ORDER_COUNT'),'class'=>'form-control','maxlength'=>'3','id' => 'min_order')) !!}
												
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ABOUT_ST')}}* :</label>
											
											<div class="col-sm-6">
												{!! Form::textarea('st_desc',$getstore->st_desc,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ABOUT_ST'),'class'=>'form-control summernote','required')) !!}
												@if ($errors->has('st_desc') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('st_desc') }}</p> 
												@endif
											</div>
										</div>
										@if(count($Admin_Active_Language) > 0)
										@foreach($Admin_Active_Language as $lang)
										@php $desc = 'st_desc_'.$lang->lang_code @endphp
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ABOUT_ST')}}&nbsp; (In {{$lang->lang_name}}):</label>
											<div class="col-sm-6">
												{!! Form::textarea('st_desc_'.$lang->lang_code.'',$getstore->$desc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST')) ? trans(Session::get('admin_lang_file').'.ADMIN_ABOUT_ST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ABOUT_ST'),'class'=>'form-control summernote')) !!}
												
											</div>
										</div>
										@endforeach
										@endif
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELIVERY_TIME')}} *:</label>
											
											<div class="col-sm-4">
												{!! Form::text('del_time',$getstore->st_delivery_time,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_DELIVERY_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELIVERY_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELIVERY_TIME'),'class'=>'form-control','maxlength'=>'2','id' => 'deli_time')) !!}
												
											</div>
											<div class="col-sm-2">
												@php 
												$delivery_time_array = array('hours'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_HOUR')) ? trans(Session::get('admin_lang_file').'.ADMIN_HOUR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_HOUR'),'minutes'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_MIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_MIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MIN')); @endphp
												{{ Form::select('deli_duration',$delivery_time_array,$getstore->st_delivery_duration,['class' => 'form-control' , 'style' => 'width:100%','required'] ) }}
												
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_RADIUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_RADIUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEL_RADIUS')}}* :</label>
											
											<div class="col-sm-4">
												{!! Form::text('del_radius',$getstore->st_delivery_radius,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_RADIUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_RADIUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEL_RADIUS'),'class'=>'form-control','maxlength'=>'2','id' => 'del_radius','required')) !!}
												@if ($errors->has('del_radius') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('del_radius') }}</p> 
												@endif
											</div>
											<div class="col-sm-2">
												{!! Form::text('',(Lang::has(Session::get('admin_lang_file').'.ADMIN_IN_KM')) ? trans(Session::get('admin_lang_file').'.ADMIN_IN_KM') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_IN_KM'),['class'=>'form-control','disabled']) !!}
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_ADDR')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_ADDR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_ADDR')}}* :</label>
											
											<div class="col-sm-6">
												{!! Form::text('st_addr',$getstore->st_address,array('required','placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_ADDR_EX')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_ADDR_EX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_ADDR_EX'),'class'=>'form-control','id' => 'us3-address','required')) !!}
												@if ($errors->has('st_addr') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('st_addr') }}</p> 
												@endif
											</div>
											
										</div>
										<div class="form-group">
											<div class="control-label col-sm-2">
											</div>
											<div class="col-sm-6">
												<div id="us3"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="control-label col-sm-2">
											</div>
											<div class="col-sm-6">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LATITUDE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LATITUDE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LATITUDE')}}&nbsp;*
												
												{!! Form::text('st_lat',$getstore->st_latitude,['class'=>'form-control','id' => 'us3-lat','required','readonly']) !!}
												@if ($errors->has('st_lat') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('st_lat') }}</p> 
												@endif
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LONGITUDE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LONGITUDE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LONGITUDE')}}&nbsp;*
												
												{!! Form::text('st_long',$getstore->st_longitude,['class'=>'form-control','id' => 'us3-lon','required','readonly']) !!}
												@if ($errors->has('st_long') ) 
												<p class="error-block" style="color:red;">{{ $errors->first('st_long') }}</p> 
												@endif
											</div>
										</div>
										<div class="form-group">
											
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_LOGO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_LOGO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_LOGO')}}* :</label>
											<div class="col-sm-6">
												@if($getstore->st_logo != '')
													{{ Form::file('st_logo',array('class' => 'form-control','required','accept'=>'image/*','id'=>'st_logo','onchange'=>'Upload(this.id,"300","300","500","500");')) }} 
													{!! Form::hidden('old_logo',$getstore->st_logo,['id' => 'old_logo']) !!}
													@php $filename = public_path('images/store/').$getstore->st_logo;  @endphp
													
													@if(file_exists($filename))
														{{ Form::image(url('public/images/store/'.$getstore->st_logo), 'alt text', array('class' => '','width'=>'75px','height'=>'50px')) }}
													@else
														{{ Form::image(url('public/images/noimage/'.$no_shop_logo), 'alt text', array('class' => '','width'=>'75px','height'=>'50px')) }}
													@endif
												@else
													{{ Form::file('st_logo',array('class' => 'form-control','required','accept'=>'image/*','id'=>'st_logo','onchange'=>'Upload(this.id,"300","300","500","500");')) }} 
												@endif
												<p>({{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MIN300MAX500_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_MIN300MAX500_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MIN300MAX500_VAL')}} )</p>
											</div>
										</div>
										<div class="form-group">
											
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_BANNER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_BANNER')}}* :</label>
											
											@if($getstore->st_banner != '')
											
												<div class="col-sm-6">
													@php $banner = explode('/**/',$getstore->st_banner,-1);@endphp
													{!! Form::hidden('old_banner',$getstore->st_banner,['id' => 'old_banners']) !!}
													{!!Form::hidden('count',count($banner),['id' => 'count_id'])!!}
													
													@for($i=0;$i<count($banner); $i++)
														<div class="add-store-img-sec">
														{{ Form::file('st_banner[]',array('class' => 'form-control upload_file banner','required','accept'=>'image/*',"id"=>"st_banner'.$i.'",'onchange'=>'Upload(this.id,"1366","300","1500","500");')) }}
														@php $filename = public_path('images/store/banner/').$banner[$i];  @endphp
														@if(file_exists($filename))
															{{ Form::image(url('public/images/store/banner/'.$banner[$i]), 'alt text', array('class' => 'add-store-img','width'=>'150px','height'=>'50px','id' => 'file_name')) }}
														@else
															{{ Form::image(url('public/images/noimage/'.$no_reStoreDetailbanner), 'alt text', array('class' => 'add-store-img','width'=>'150px','height'=>'50px','id' => 'file_name')) }}
														@endif

														@if($i>0)
															{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_REMOVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_REMOVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REMOVE'),['class' =>'btn btn-danger btn-sm','onClick' => "remove_file('$banner[$i]','$getstore->id');",'style' =>'float: right;margin-top: 10px;'])!!}
														@elseif($i == 0)
															{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD'),['class' =>'btn btn-success btn-sm','id' => 'add_file','style' =>'float: right;margin-top: 10px;'])!!}
														@endif
														</div>
													@endfor
													<p>({{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAX_6')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAX_6') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAX_6')}}&nbsp;with min 1366*300 to max 1500*500)</p>
												</div>
												@else
													
												<div class="col-sm-4">
													{!!Form::hidden('count',1,['id' => 'count_id'])!!}
													{{ Form::file('st_banner[]',array('class' => 'form-control upload_file','required','accept'=>'image/*','id'=>'st_banner0','onchange'=>'Upload(this.id,"1366","300","1500","500");')) }}
													<p>({{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAX_6')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAX_6') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAX_6')}}&nbsp;with min 1366*300 to max 1500*500)</p>
												</div>
												
												<div class="col-sm-2">
													{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD'),['class' =>'btn btn-success btn-sm','id' => 'add_file'])!!}
													
												</div>
												
												@endif
												
												
											</div>
											<span id="file"></span>
											<div class="form-group">
												<label class="col-sm-2"></label>
												<div class="col-sm-4">
													<!-- <div class="panel-heading col-md-offset-3"> -->
												
												@if($getstore->id !='')
												@php 
												$saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
												@else
												@php 
												$saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
												@endif
												@php $url = url('manage-store')@endphp
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												{!! Form::button('Cancel',['class' => 'btn btn-warning','onclick' => "javascript:window.location.href='$url'"])!!}
											<!-- </div> -->
												</div>
											</div>
											
											{!! Form::close() !!}
										</div>
									</div>
								</div>
								{{-- Add/Edit page ends--}}
								
								
								@section('script')
								
								<script type="text/javascript" src='https://maps.google.com/maps/api/js?libraries=places&key={{$MAP_KEY}}&language=en'></script>
								<script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>
								<script src="{{url('')}}/public/admin/assets/scripts/summernote.js"></script>
								<script>
									$(document).ready(function() {
										$('.summernote').summernote();
											$('#validate_form').each(function () {
												if ($(this).data('validator'))
													$(this).data('validator').settings.ignore = ".note-editor *";
											})
									});
									$('#st_name').bind('keyup blur',function(){ 
										var node = $(this);
									node.val(node.val().replace(/[^A-Z a-z & ! ' " \- _]/g,'') ); }
									);
									
									$('#min_order,#del_radius').bind('keyup blur',function(){ 
										var node = $(this);
									node.val(node.val().replace(/[^0-9]/g,'') ); }
									);
									/*$('#select_merchant').on("change",function(){
										var id = document.getElementById('select_merchant').value;
										
									});*/
									$('#add_file').on("click",function(){
										var count = $('.upload_file').length;
										var co = document.getElementById('count_id').value;
										
										if(count<6)
										{
											$('#file').append('<div class="form-group" id="remove'+co+'"><label class="control-label col-sm-2" for="email">&nbsp;</label><div class="col-sm-4"><input type="file" class="form-control upload_file" name="st_banner[]" required accept="image/*"  id="st_banner'+co+'" onchange="Upload(this.id,1366,300,1500,500);"></div><div class="col-sm-2" id="remove'+co+'"><input type="button" value="<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_REMOVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_REMOVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REMOVE'); ?>" class="btn btn-danger btn-sm"  onClick="remove_input('+count+');"></div></div>');
											var x = parseInt(co) + 1;
											$('#count_id').val(x);
											
										}
										if(count>4)
										{
											$('#add_file').hide();
										}
									});
									function remove_file(file,id){
										var old = document.getElementById('old_banners').value;
										var co = document.getElementById('count_id').value;
										var added_count = $('.upload_file').length;
										//alert(file);
										$.ajax({
											'type' : 'get',
											'data' : {'file':file,'id':id,'old_ban' : old},
											'url' : '<?php echo url('remove_store_banner'); ?>',
											success:function(response)
											{
												alert("<?php echo (Lang::has(Session::get('admin_lang_file').'.ADMIN_REMOVE_SUCCESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_REMOVE_SUCCESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_REMOVE_SUCCESS'); ?>");
												var x = parseInt(co) - 1;
												$('#count_id').val(x);
												//alert(x);
												location.reload();
												
											}
										});
										if(added_count<=6)
										{
											$('#add_file').show();
										}
									}
									
									function remove_input(id) {
										var added_count = $('.upload_file').length;
										$("#remove"+id).remove();
										var co = document.getElementById('count_id').value;
										var x = parseInt(co) - 1;
										$('#count_id').val(x);
										if(added_count<=6)
										{
											$('#add_file').show();
										}
										//alert(x);
									}
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
								
								<script>
									$.validator.addMethod("valueNotEquals", function(value, element, arg){
										return arg !== value;
									}, "Value must not equal arg.");
									
									
									$("#validate_form").validate({
										rules: {
											mer_fname: { valueNotEquals: "0" },
											cate_name: { valueNotEquals: "0" },
											st_name: "required",
											st_desc: "required",
											del_time : "required",
											del_radius: "required",
											st_addr: "required",
											"st_logo": {
												required: {
													depends: function(element) {
														if($('#store_id').val()==''){ return true; } else { return false; } 
													}
												}
											},
											"st_banner[]": {
												required: {
													depends: function(element) {
														if($('#store_id').val()==''){ return true; } else { return false; } 
													}
												}
											},
										},
										messages: {
											mer_fname: { valueNotEquals: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_MER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_MER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_MER')}}"},
											cate_name: { valueNotEquals: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE')}}"},
											st_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_ST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_ENTR_ST_NAME')}}",
											st_desc: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_ST_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_ST_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_ENTR_ST_DESC')}}",
											del_time : "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_DELIVERY_TIME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_DELIVERY_TIME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_DELIVERY_TIME')}}",
											del_radius: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_DEL_RAIUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_DEL_RAIUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_DEL_RAIUS')}}",
											st_addr: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_STORE_ADDR')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_STORE_ADDR') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_STORE_ADDR')}}",
											st_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_LOGO')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_LOGO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_ENTR_LOGO')}}",
											"st_banner[]": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_ENTR_BANNER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_ENTR_BANNER')}}",
										}
									});
									
								</script>	
								@if($getstore->st_address == '' && old('st_lat') =='' && old('st_long') =='')
								<script>
									$('#us3').locationpicker({
										location: {
											latitude: 46.15242437752303,
											longitude: 2.7470703125
										},
										radius: 300,
										inputBinding: {
											latitudeInput: $('#us3-lat'),
											longitudeInput: $('#us3-lon'),
											radiusInput: $('#us3-radius'),
											locationNameInput: $('#us3-address')
										},
										enableAutocomplete: true,
										onchanged: function (currentLocation, radius, isMarkerDropped) {
											// Uncomment line below to show alert on each Location Changed event
											//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
										}
									});
								</script>
								@elseif($getstore->st_address == '' && (old('st_lat') !='' || old('st_long') !=''))
								<script>
								$('#us3').locationpicker({
									location: {
										latitude: {{old('st_lat')}},
										longitude: {{old('st_long')}}
									},
									radius: 300,
									inputBinding: {
										latitudeInput: $('#us3-lat'),
										longitudeInput: $('#us3-lon'),
										radiusInput: $('#us3-radius'),
										locationNameInput: $('#us3-address')
									},
									enableAutocomplete: true,
									onchanged: function (currentLocation, radius, isMarkerDropped) {
										// Uncomment line below to show alert on each Location Changed event
										//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
									}
								});
							</script>
							@else
							<script>
									$('#us3').locationpicker({
										location: {
											latitude: <?php echo $getstore->st_latitude; ?>,
											longitude: <?php echo $getstore->st_longitude; ?>
										},
										radius: 300,
										inputBinding: {
											latitudeInput: $('#us3-lat'),
											longitudeInput: $('#us3-lon'),
											radiusInput: $('#us3-radius'),
											locationNameInput: $('#us3-address')
										},
										enableAutocomplete: true,
										onchanged: function (currentLocation, radius, isMarkerDropped) {
											// Uncomment line below to show alert on each Location Changed event
											//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
										}
									});
								</script>
								@endif
								@endsection
							@stop							