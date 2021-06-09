@extends('sitemerchant.layouts.default')
@section('PageTitle')
	@if(isset($pagetitle))
		{{$pagetitle}}
	@endif
@stop
@section('content')
	<style>
		input[type=file] {
			display: inline-block;
		}
		.banner {
			display: inline-block;
			float: left;
			width: 40%;
			margin-right: 5px;
		}
	</style>

	<!-- MAIN -->
	<script src="<?php echo URL::to('/'); ?>/public/js/jquery.timepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/public/css/jquery.timepicker.css" />
	<script src="<?php echo URL::to('/'); ?>/public/js/moment.min.js"></script>
	<script src="<?php echo URL::to('/'); ?>/public/js/datepair.js"></script>
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
									<span style="float:right;color:#373756;font-size:15px">
										@php 
										if( $getrestaurant->st_status == '1'){
											echo "Restaurant Status : Active";
										}else{
											echo "Restaurant Status : block";
										}
										@endphp
										</span>
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
								<div class="box-body spaced box-body-padding" >
									<div id="location_form" class="panel-body">
										<div class="row-fluid well">

											{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => $url,'id'=>'validate_form','enctype' => 'multipart/form-data']) !!}
											{!! Form::hidden('rs_id',$getrestaurant->id,['id' => 'store_id'])!!}

											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_SEL_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SEL_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SEL_CATE')}}* :</label>
												<div class="col-sm-6">
													@if($getrestaurant->st_category == '')  {{-- no category selected --}}
													{!! Form::radio('select','list','',['id' => 'select','onClick' => 'show("list")'])!!}&nbsp;{!!Form::label('select',(Lang::has(Session::get('mer_lang_file').'.ADMIN_SELECT_DROP')) ? trans(Session::get('mer_lang_file').'.ADMIN_SELECT_DROP') : trans($MER_OUR_LANGUAGE.'.ADMIN_SELECT_DROP'))!!}
													{!! Form::radio('select','text','',['id' => 'custom','onClick' => 'show("text")'])!!}&nbsp;{!!Form::label('custom',(Lang::has(Session::get('mer_lang_file').'.ADMIN_CUSTOM')) ? trans(Session::get('mer_lang_file').'.ADMIN_CUSTOM') : trans($MER_OUR_LANGUAGE.'.ADMIN_CUSTOM'))!!}
													@elseif($getrestaurant->st_category != '') {{-- category selected --}}
													@php $category = get_details('gr_category',['cate_id' => $getrestaurant->st_category],'cate_name'); @endphp
													{!! Form::text('cate_name',(empty($category) === false) ? $category->cate_name : '',['disabled','class' => 'form-control'])!!}
													@endif
													<div></div>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-2"> </label>
												<div class="col-sm-6">
												<span id="list" style="display:none">
													@if(count($category_list) > 0)

														{{ Form::select('cate_name1',($category_list),$getrestaurant->st_category,['class' => 'form-control' , 'style' => 'width:100%'] ) }}
													@else
														{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_NO_DETAILS') : trans($MER_OUR_LANGUAGE.'.ADMIN_NO_DETAILS') }}
													@endif
													@if ($errors->has('cate_name1') )
														<p class="error-block" style="color:red;">{{ $errors->first('cate_name1') }}</p>
													@endif
												</span>
													<span id="text" style="display:none">
													@if($getrestaurant->st_category == '')	{{-- category not  selected --}}
														{!!Form::text('cate_name','',['class' => 'form-control','id' => 'cate_name','maxlength' => '100'])!!}
														@endif
												</span>
												</div>
											</div>
                                            <div class="form-group">
                                                {!! Form::label('', (Lang::has(Session::get('mer_lang_file').'.ADMIN_MERCHANT_COMMISSION')) ? trans(Session::get('mer_lang_file').'.ADMIN_MERCHANT_COMMISSION').'*:' : trans($MER_OUR_LANGUAGE.'.ADMIN_MERCHANT_COMMISSION').'*:',['class' => 'control-label col-sm-2 require']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::text('mer_commission',(empty($mer_commission)== false)? $mer_commission :'' ,['disabled','class' => 'form-control'])!!}
                                                </div>
                                            </div>
                                            @if(empty($details) === false)
                                            @if($details->license == '')
                                            <div class="form-group">
												{!! Form::label('', (Lang::has(Session::get('mer_lang_file').'.ADMIN_RES_LICENSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_RES_LICENSE').'*:' : trans($MER_OUR_LANGUAGE.'.ADMIN_RES_LICENSE').'*:',['class' => 'control-label col-sm-2 require']) !!}
												<div class="col-sm-6">
													{!! Form::file('license',['class' => 'form-control','id' => 'license','onchange'=>'Upload(this.id,"300","300","500","500");','required','accept' => 'image/*'])!!}<br>
                                                    {!!(Lang::has(Session::get('mer_lang_file').'.ADMIN_MIN_MAX')) ? trans(Session::get('mer_lang_file').'.ADMIN_MIN_MAX') : trans($MER_OUR_LANGUAGE.'.ADMIN_MIN_MAX')!!}
												</div>
											</div>
                                            @endif
                                            @if($details->idproof == '')
                                            <div class="form-group">
                                                {!! Form::label('', (Lang::has(Session::get('mer_lang_file').'.ADMIN_ID_PROOF')) ? trans(Session::get('mer_lang_file').'.ADMIN_ID_PROOF').'*:' : trans($MER_OUR_LANGUAGE.'.ADMIN_ID_PROOF').'*:',['class' => 'control-label col-sm-2 require']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::file('id_proof',['id' => 'id_proof','class' => 'form-control','onchange'=>'Upload(this.id,"300","300","500","500");','required','accept' => 'image/*'])!!}<br>
                                                    {!!(Lang::has(Session::get('mer_lang_file').'.ADMIN_MIN_MAX')) ? trans(Session::get('mer_lang_file').'.ADMIN_MIN_MAX') : trans($MER_OUR_LANGUAGE.'.ADMIN_MIN_MAX')!!}
                                                </div>
                                            </div>
                                            @endif
                                            @endif
											<div class="form-group">
												{!! Form::label('', (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME').'*:' : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME').'*:',['class' => 'control-label col-sm-2 require']) !!}
												<div class="col-sm-6">
													{!! Form::text('rs_name',$getrestaurant->st_store_name,array('required','placeholder'=> (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME'),'class'=>'form-control','maxlength'=>'100','id' => 'rs_name','onchange'=>'check_name_exist("st_store_name",this.value)')) !!}
													<span class="help-block error" id="st_store_name_error"></span>
												</div>
											</div>
											@if(count($Mer_Active_Language) > 0)
												@foreach($Mer_Active_Language as $lang)
													@php $name = 'st_store_name_'.$lang->lang_code @endphp
													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME')}}&nbsp; (In {{$lang->lang_name}})*:</label>
														<div class="col-sm-6">
															{!! Form::text('restaurant_'.$lang->lang_code.'',$getrestaurant->$name,array('required','placeholder'=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME'),'class'=>'form-control','maxlength'=>'100','onchange'=>'check_name_exist("st_store_name_'.$lang->lang_code.'",this.value)')) !!}
															<span class="help-block error" id="st_store_name_{{$lang->lang_code}}_error"></span>
														</div>
													</div>
												@endforeach
											@endif
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_MINIMUM_ORDER_COUNT')}}:</label>
												<div class="col-sm-2">
													{!! Form::text('curr_code',$default_currency,['class'=>'form-control','readonly'])!!}
												</div>
												<div class="col-sm-4">
													{!! Form::text('min_order',($getrestaurant->st_minimum_order=='')?'0.00':$getrestaurant->st_minimum_order,array('placeholder'=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT')) ? trans(Session::get('mer_lang_file').'.ADMIN_MINIMUM_ORDER_COUNT') : trans($MER_OUR_LANGUAGE.'.ADMIN_MINIMUM_ORDER_COUNT'),'class'=>'form-control','maxlength'=>'15','id' => 'min_order','onkeypress'=>"return onlyNumbersWithDot(event);")) !!}

												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PRE_ORDER_OPTION')) ? trans(Session::get('mer_lang_file').'.ADMIN_PRE_ORDER_OPTION') : trans($MER_OUR_LANGUAGE.'.ADMIN_PRE_ORDER_OPTION')}}* :</label>
												<div class="col-sm-6">
													{{ Form::radio('pre_order', '1',($getrestaurant->st_pre_order == '1') ? 'selected' : '', ['class' => 'field','id'=>'yes','required']) }}
													{{ Form::label('yes', (Lang::has(Session::get('mer_lang_file').'.ADMIN_YES')) ? trans(Session::get('mer_lang_file').'.ADMIN_YES') : trans($MER_OUR_LANGUAGE.'.ADMIN_YES'),null, ['class' => 'field']) }}
													{{ Form::radio('pre_order', '0',($getrestaurant->st_pre_order == '0') ? 'selected' : '', ['class' => 'field','id'=>'no','required']) }}
													{{ Form::label('no',(Lang::has(Session::get('mer_lang_file').'.ADMIN_NO')) ? trans(Session::get('mer_lang_file').'.ADMIN_NO') : trans($MER_OUR_LANGUAGE.'.ADMIN_NO'),null, ['class' => 'field']) }}
													<div></div>
												</div>
												<div class="form-group"><label class="control-label col-sm-2"></label><div class="col-sm-6 pre_order_label"></div></div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_DELIVERY_TIME')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELIVERY_TIME') : trans($MER_OUR_LANGUAGE.'.ADMIN_DELIVERY_TIME')}}* :</label>

												<div class="col-sm-4">
													{!! Form::text('del_time',$getrestaurant->st_delivery_time,array('required','placeholder'=> (Lang::has(Session::get('mer_lang_file').'.ADMIN_DELIVERY_TIME')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELIVERY_TIME') : trans($MER_OUR_LANGUAGE.'.ADMIN_DELIVERY_TIME'),'class'=>'form-control','maxlength'=>'2','id' => 'deli_time')) !!}

												</div>
												<div class="col-sm-2">
													@php
														$delivery_time_array = array('hours'=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_HOUR')) ? trans(Session::get('mer_lang_file').'.ADMIN_HOUR') : trans($MER_OUR_LANGUAGE.'.ADMIN_HOUR'),'minutes'=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_MIN')) ? trans(Session::get('mer_lang_file').'.ADMIN_MIN') : trans($MER_OUR_LANGUAGE.'.ADMIN_MIN')); @endphp
													{{ Form::select('deli_duration',$delivery_time_array,$getrestaurant->st_delivery_duration,['class' => 'form-control' , 'style' => 'width:100%','required'] ) }}

												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ABOUT_RESTAURANT')}}* :</label>
												<div class="col-sm-6">
													{!! Form::textarea('rs_desc',$getrestaurant->st_desc,array('required','placeholder'=> (Lang::has(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ABOUT_RESTAURANT'),'class'=>'form-control summernote')) !!}
												</div>
											</div>
											@if(count($Mer_Active_Language) > 0)
												@foreach($Mer_Active_Language as $lang)
													@php $desc = 'st_desc_'.$lang->lang_code @endphp
													<div class="form-group">
														<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ABOUT_RESTAURANT')}}&nbsp; (In {{$lang->lang_name}}):</label>
														<div class="col-sm-6">
															{!! Form::textarea('rs_desc_'.$lang->lang_code,$getrestaurant->$desc,array('placeholder'=>(Lang::has(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ABOUT_RESTAURANT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ABOUT_RESTAURANT'),'class'=>'form-control summernote')) !!}
														</div>
													</div>
												@endforeach
											@endif
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_DEL_RADIUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DEL_RADIUS') : trans($MER_OUR_LANGUAGE.'.ADMIN_DEL_RADIUS')}} (In {{(Lang::has(Session::get('mer_lang_file').'.ADMIN_IN_KM')) ? trans(Session::get('mer_lang_file').'.ADMIN_IN_KM') : trans($MER_OUR_LANGUAGE.'.ADMIN_IN_KM')}} )* :</label>
												<div class="col-sm-4">
													{!! Form::text('del_radius',$getrestaurant->st_delivery_radius,array('required','placeholder'=> (Lang::has(Session::get('mer_lang_file').'.ADMIN_DEL_RADIUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DEL_RADIUS') : trans($MER_OUR_LANGUAGE.'.ADMIN_DEL_RADIUS'),'class'=>'form-control','maxlength'=>'10','id' => 'deli_radius','onkeypress'=>"return onlyNumbersWithDot(event);")) !!}
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_REST_ADDR')) ? trans(Session::get('mer_lang_file').'.ADMIN_REST_ADDR') : trans($MER_OUR_LANGUAGE.'.ADMIN_REST_ADDR')}}* :</label>
												<div class="col-sm-6">
													{!! Form::text('rs_addr',$getrestaurant->st_address,array('required','placeholder'=> (Lang::has(Session::get('mer_lang_file').'.ADMIN_ST_ADDR_EX')) ? trans(Session::get('mer_lang_file').'.ADMIN_ST_ADDR_EX') : trans($MER_OUR_LANGUAGE.'.ADMIN_ST_ADDR_EX'),'class'=>'form-control','id' => 'us3-address','required')) !!}
												</div>
											</div>

											<div class="form-group">
												<div class="control-label col-sm-2">
												</div>
												<div class="col-sm-6">
													<div id="us3" style="width: 100%; height: 400px;"></div>
												</div>
											</div>
											<div class="form-group">
												<div class="control-label col-sm-2">
												</div>
												<div class="col-sm-6">
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_LATITUDE')) ? trans(Session::get('mer_lang_file').'.ADMIN_LATITUDE') : trans($MER_OUR_LANGUAGE.'.ADMIN_LATITUDE')}}&nbsp;*

													{!! Form::text('rs_lat',$getrestaurant->st_latitude,['class'=>'form-control','id' => 'us3-lat','required','readonly']) !!}
													<br>
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_LONGITUDE')) ? trans(Session::get('mer_lang_file').'.ADMIN_LONGITUDE') : trans($MER_OUR_LANGUAGE.'.ADMIN_LONGITUDE')}}&nbsp;*

													{!! Form::text('rs_long',$getrestaurant->st_longitude,['class'=>'form-control','id' => 'us3-lon','required','readonly']) !!}
												</div>
											</div>

											<div class="form-group">

												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_REST_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_REST_LOGO') : trans($MER_OUR_LANGUAGE.'.ADMIN_REST_LOGO')}}* :</label>
												<div class="col-sm-6">
													@if($getrestaurant->st_logo != '')
														{{ Form::file('rs_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'rs_logo','onchange'=>'Upload(this.id,"300","300","500","500");')) }}
														{!! Form::hidden('old_logo',$getrestaurant->st_logo,['id' => 'old_logo']) !!}
														@php $filename = public_path('images/restaurant/').$getrestaurant->st_logo;  @endphp
														@if(file_exists($filename))
															{{ Form::image(url('public/images/restaurant/'.$getrestaurant->st_logo), 'alt text', array('class' => '','width'=>'75px','height'=>'50px')) }}
														@else
															{{ Form::image(url('public/images/noimage/'.$no_shop_logo), 'alt text', array('class' => '','width'=>'75px','height'=>'50px')) }}
														@endif
													@else
														{{ Form::file('rs_logo',array('class' => 'form-control','required','id'=>'rs_logo','onchange'=>'Upload(this.id,"300","300","500","500");')) }}
													@endif

													<p>({{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_MIN300MAX500_VAL')) ? trans(Session::get('mer_lang_file').'.ADMIN_MIN300MAX500_VAL') : trans($MER_OUR_LANGUAGE.'.ADMIN_MIN300MAX500_VAL')}} )</p>
												</div>
											</div>

											<div class="form-group">

												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_REST_BANNER')) ? trans(Session::get('mer_lang_file').'.ADMIN_REST_BANNER') : trans($MER_OUR_LANGUAGE.'.ADMIN_REST_BANNER')}}* :</label>
												@if($getrestaurant->st_banner != '')
													<div class="col-sm-6">
														@php $banner = explode('/**/',$getrestaurant->st_banner,-1);@endphp
														{!! Form::hidden('old_banner',$getrestaurant->st_banner,['id' => 'old_banners']) !!}
														{!!Form::hidden('count',count($banner),['id' => 'count_id'])!!}
														@for($i=0;$i<count($banner); $i++)
															<div class="add-store-img-sec">
																{{ Form::file('rs_banner[]',array('class' => 'form-control upload_file banner','accept'=>'image/*',"id"=>"rs_banner'.$i.'",'onchange'=>'Upload(this.id,"1366","300","1500","500");')) }}
																{{ Form::image(url('public/images/restaurant/banner/'.$banner[$i]), 'alt text', array('class' => 'add-store-img','width'=>'150px','height'=>'50px','id' => 'file_name')) }}
																@if($i>0)
																	{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_REMOVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_REMOVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_REMOVE'),['class' =>'btn btn-danger btn-sm','onClick' => "remove_file('$banner[$i]','$getrestaurant->id');",'style' =>'float: right;margin-top: 10px;'])!!}
																@elseif($i == 0)
																	{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD'),['class' =>'btn btn-success btn-sm','id' => 'add_file','style' =>'float: right;margin-top: 10px;'])!!}
																@endif
															</div>
														@endfor
														<p>({{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_MAX_6')) ? trans(Session::get('mer_lang_file').'.ADMIN_MAX_6') : trans($MER_OUR_LANGUAGE.'.ADMIN_MAX_6')}}&nbsp;
														
														{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTURANT_IMG_VAL')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTURANT_IMG_VAL') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTURANT_IMG_VAL')}}) 
														</p>
													</div>
												@else
													<div class="col-sm-4">
														{!!Form::hidden('count',1,['id' => 'count_id'])!!}
														{{ Form::file('rs_banner[]',array('class' => 'form-control upload_file','required','accept'=>'image/*','id'=>'rs_banner0','onchange'=>'Upload(this.id,"1366","300","1500","500");')) }}
														<p>({{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_MAX_6')) ? trans(Session::get('mer_lang_file').'.ADMIN_MAX_6') : trans($MER_OUR_LANGUAGE.'.ADMIN_MAX_6')}}&nbsp;with min 1366*300 to max 1500*500)</p>
													</div>
													<div class="col-sm-2">
														{!! Form::button((Lang::has(Session::get('mer_lang_file').'.ADMIN_ADD')) ? trans(Session::get('mer_lang_file').'.ADMIN_ADD') : trans($MER_OUR_LANGUAGE.'.ADMIN_ADD'),['class' =>'btn btn-success btn-sm','id' => 'add_file'])!!}
													</div>
												@endif

											</div>
											<span id="file"></span>
											<div class="row"><h3>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_WORKING_HOURS')) ? trans(Session::get('mer_lang_file').'.ADMIN_WORKING_HOURS') : trans($MER_OUR_LANGUAGE.'.ADMIN_WORKING_HOURS')}}</h3></div>
											@for($i =1; $i<=7;$i++)
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{!! Form::label('wk_day_'.$i,(Lang::has(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'')) ? trans(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'') : trans($MER_OUR_LANGUAGE.'.ADMIN_DAY'.$i.''))!!} : </label>
													@if(count($wk_hours) > 0)
														<div id="{{$i}}_div">
															<div class="col-md-1 col-sm-1">
																{{ Form::checkbox('closed'.$i, '1',($wk_hours[$i-1]->wk_closed==1)?true:false, ['class' => 'field','id'=>'closed'.$i,'onchange'=>'closedFun(\''.$i.'\')']) }}
																{{ Form::label('closed'.$i, (Lang::has(Session::get('mer_lang_file').'.MER_CLOSED')) ? trans(Session::get('mer_lang_file').'.MER_CLOSED') : trans($MER_OUR_LANGUAGE.'.MER_CLOSED'),null, ['class' => 'field']) }}
																<div class="closedErrDiv"></div>
															</div>
															<div class="col-md-3 col-sm-3">
																{!! Form::text('wk_start_'.$i,$wk_hours[$i-1]->wk_start_time,['class'=> 'form-control time start','required','style'=>($wk_hours[$i-1]->wk_closed==1)? 'display:none' :'display:block','id'=>'wk_start_'.$i])!!}
															</div>
															<div class="col-md-3 col-sm-3">
																{!! Form::text('wk_end_'.$i,$wk_hours[$i-1]->wk_end_time,['class'=> 'form-control time end','required','style'=>($wk_hours[$i-1]->wk_closed==1)? 'display:none' :'display:block','id'=>'wk_end_'.$i])!!}
															</div>
														</div>
													@else
														<div id="{{$i}}_div">
															<div class="col-md-1 col-sm-1">
																{{ Form::checkbox('closed'.$i, '1',null, ['class' => 'field','id'=>'closed'.$i,'onchange'=>'closedFun(\''.$i.'\')']) }}
																{{ Form::label('closed'.$i, (Lang::has(Session::get('mer_lang_file').'.MER_CLOSED')) ? trans(Session::get('mer_lang_file').'.MER_CLOSED') : trans($MER_OUR_LANGUAGE.'.MER_CLOSED'),null, ['class' => 'field']) }}
																<div class="closedErrDiv"></div>
															</div>
															<div class="col-md-3 col-sm-3">
																{!! Form::text('wk_start_'.$i,'',['class'=> 'form-control time start','id'=>'wk_start_'.$i,'required'])!!}
															</div>
															<div class="col-md-3 col-sm-3">
																{!! Form::text('wk_end_'.$i,'',['class'=> 'form-control time end','id'=>'wk_end_'.$i,'required'])!!}
															</div>
														</div>
													@endif
													<script>
                                                        $('#{{$i}}_div .time').timepicker({
                                                            'showDuration': true,
                                                            'timeFormat': 'g:ia'
                                                        });

                                                        var timeOnlyExampleEl = document.getElementById('{{$i}}_div');
                                                        var timeOnlyDatepair = new Datepair(timeOnlyExampleEl);
													</script>
												</div>
											@endfor
                                            <?php /*
											<div class="row"><h3>{{ (Lang::has(Session::get('mer_lang_file').'.ADMIN_WORKING_HOURS')) ? trans(Session::get('mer_lang_file').'.ADMIN_WORKING_HOURS') : trans($MER_OUR_LANGUAGE.'.ADMIN_WORKING_HOURS')}}</h3></div>
											<div class="row">
												@for($i =1; $i<=7;$i++)
												<div class="col-sm-6">
													<div class="form-group">
														<label for="new_name" class="col-sm-4 control-label">{!! Form::label('wk_day_'.$i,(Lang::has(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'')) ? trans(Session::get('mer_lang_file').'.ADMIN_DAY'.$i.'') : trans($MER_OUR_LANGUAGE.'.ADMIN_DAY'.$i.''))!!} : </label>
														@if(count($wk_hours) > 0)
														<div class="col-sm-6" id="{{$i}}_div">
															{!! Form::text('wk_start_'.$i,$wk_hours[$i-1]->wk_start_time,['class'=> 'form-control time start', 'style'=>'width:48%;float:left;margin:1px','required'])!!}
															{!! Form::text('wk_end_'.$i,$wk_hours[$i-1]->wk_end_time,['class'=> 'form-control time end', 'style'=>'width:48%;float:left;margin:1px','required'])!!}
														</div>
														@else
														<div class="col-sm-6" id="{{$i}}_div">
															{!! Form::text('wk_start_'.$i,'',['class'=> 'form-control time start', 'style'=>'width:48%;float:left;margin:1px','required'])!!}
															{!! Form::text('wk_end_'.$i,'',['class'=> 'form-control time end', 'style'=>'width:48%;float:left;margin:1px','required'])!!}
														</div>
														@endif
														<script>
															$('#{{$i}}_div .time').timepicker({
																'showDuration': true,
																'timeFormat': 'g:ia'
															});

															var timeOnlyExampleEl = document.getElementById('{{$i}}_div');
															var timeOnlyDatepair = new Datepair(timeOnlyExampleEl);
														</script>

													</div>
												</div>
												@endfor
											</div> */ ?>
											<div class="form-group">
												<div class="col-sm-2"></div>

												@if(isset($id))
													@php
														$saveBtn = (Lang::has(Session::get('mer_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPDATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
												@else
													@php
														$saveBtn=(Lang::has(Session::get('mer_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_SAVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
												@endif
												<div class="col-sm-6">
													{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
													@if(isset($id))
														<input type="hidden" id="hidRestId" value="{{$id}}" />
														<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('mer-manage-restaurant'); ?>'">
													@else
														<input type="hidden" id="hidRestId" value="" />
														<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('mer-manage-restaurant'); ?>'">
													@endif
												</div>
											</div>
											{!! Form::close() !!}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Add/Edit page ends--}}


@section('script')
	
	<script type="text/javascript" src='https://maps.google.com/maps/api/js?sensor=false&libraries=places&key={{$MAP_KEY}}'></script>
	<script src="{{url('')}}/public/admin/assets/scripts/summernote.js"></script>
	<script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>
	<script>
        $(document).ready(function() {
            $('.summernote').summernote();
            $('#validate_form').each(function () {
                if ($(this).data('validator'))
                    $(this).data('validator').settings.ignore = ".note-editor *";
            })
        });
        $('#rs_name').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^A-Z a-z , & ! ' " \- _]/g,'') );
        });

        $('#min_order').bind('keyup blur',function(){ 
			var node = $(this);
			var gotVal = node.val().replace(/[^0-9 .]/g,'');
			if(gotVal=='') { replaceVal='0.00'; } else { replaceVal = gotVal; } 
			node.val(replaceVal); }
		);
		/*$('#min_order,#deli_radius,#deli_time').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^0-9]/g,'') ); }
        );*/
	</script>

	<script>
        $.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg !== value;
        }, "Value must not equal arg.");

        $("#validate_form").validate({
            rules: {
                "cate_name":{
                    required: {
                        depends: function(element) {

                            if($('#select').val()=='text'){  return true; } else { return false; }
                        }
                    },
                },
                id_proof: "required",
                license: "required",
                restaurant: "required",
                select: "required",
                //cate_name1: { valueNotEquals: "0" },
                "cate_name1":{
                    required: {
                        depends: function(element) {

                            if($('#select').val()=='list'){  return true; } else { return false; }
                        }
                    },
                },
                rs_name: "required",
                del_time: "required",
                rs_desc: {
	                	required: true,
			            minlength:150
		                },
                del_radius: "required",
                rs_addr: "required",
                "rs_logo": {
                    required: {
                        depends: function(element) {
                            if($('#store_id').val()==''){ return true; } else { return false; }
                        }
                    }
                },
                "rs_banner[]": {
                    required: {
                        depends: function(element) {
                            if($('#store_id').val()==''){ return true; } else { return false; }
                        }
                    }
                },
                "closed1": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_1").val() == '' && $("#wk_end_1").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_1": {
                    required: {
                        depends: function(element) {
                            if($("#closed1").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_1": {
                    required: {
                        depends: function(element) {
                            if($("#closed1").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "closed2": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_2").val() == '' && $("#wk_end_2").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_2": {
                    required: {
                        depends: function(element) {
                            if($("#closed2").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_2": {
                    required: {
                        depends: function(element) {
                            if($("#closed2").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "closed3": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_3").val() == '' && $("#wk_end_3").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_3": {
                    required: {
                        depends: function(element) {
                            if($("#closed3").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_3": {
                    required: {
                        depends: function(element) {
                            if($("#closed3").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "closed4": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_4").val() == '' && $("#wk_end_4").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_4": {
                    required: {
                        depends: function(element) {
                            if($("#closed4").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_4": {
                    required: {
                        depends: function(element) {
                            if($("#closed4").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "closed5": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_5").val() == '' && $("#wk_end_5").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_5": {
                    required: {
                        depends: function(element) {
                            if($("#closed5").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_5": {
                    required: {
                        depends: function(element) {
                            if($("#closed5").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "closed6": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_6").val() == '' && $("#wk_end_6").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_6": {
                    required: {
                        depends: function(element) {
                            if($("#closed6").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_6": {
                    required: {
                        depends: function(element) {
                            if($("#closed6").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "closed7": {
                    required: {
                        depends: function(element) {
                            if($("#wk_start_7").val() == '' && $("#wk_end_7").val() == ''){ return true; } else { return false; }
                        }
                    }
                },
                "wk_start_7": {
                    required: {
                        depends: function(element) {
                            if($("#closed7").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
                "wk_end_7": {
                    required: {
                        depends: function(element) {
                            if($("#closed7").prop('checked') == false){ return true; } else { return false; }
                        }
                    }
                },
            },
            messages: {

                cate_name: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_SL_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE')}}",
                id_proof: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_UPLOAD_ID_PROOF')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPLOAD_ID_PROOF') : trans($MER_OUR_LANGUAGE.'.ADMIN_UPLOAD_ID_PROOF')}}",
                license: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_UPLOAD_LICENSE')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPLOAD_LICENSE') : trans($MER_OUR_LANGUAGE.'.ADMIN_UPLOAD_LICENSE')}}",
                restaurant: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME_VAL')) ? trans(Session::get('mer_lang_file').'.ADMIN_RESTAURANT_NAME_VAL') : trans($MER_OUR_LANGUAGE.'.ADMIN_RESTAURANT_NAME_VAL')}}",
                select: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_SL_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE')}}",
                cate_name1: { valueNotEquals: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_SL_CATE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE')}}"},
                st_name: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_ST_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_ST_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_ENTR_ST_NAME')}}",
                rs_desc:{
                	required : "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_ST_DESC')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_ST_DESC') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_ENTR_ST_DESC')}}",
                	minlength: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_MIN_150_CHAR')) ? trans(Session::get('mer_lang_file').'.ADMIN_MIN_150_CHAR') : trans($MER_OUR_LANGUAGE.'.ADMIN_MIN_150_CHAR')}}"
                	},
                del_radius: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_DEL_RAIUS')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_DEL_RAIUS') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_DEL_RAIUS')}}",
                rs_addr: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_REST_ADDR')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_REST_ADDR') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_REST_ADDR')}}",
                rs_logo: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_LOGO') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_ENTR_LOGO')}}",
                rs_banner: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_BANNER')) ? trans(Session::get('mer_lang_file').'.ADMIN_PL_ENTR_BANNER') : trans($MER_OUR_LANGUAGE.'.ADMIN_PL_ENTR_BANNER')}}",
            },
            errorPlacement: function(error, element)
            {
                if ( element.is(":radio") )
                {
					//alert($(element).next().next().next().next().html());
                    error.appendTo( $(element).next().next().next().next() );
                    //error.insertAfter($(element).parents('div').prev($('.pre_order_label')));
                }
                else if(element.is(':checkbox'))
                {
                    error.appendTo($(element).next().next($('.closedErrDiv')));
                }
                else
                { // This is the default behavior
                    error.insertAfter( element );
                }
            }
        });

	</script>
	
	<script type="text/javascript">
    var map;

    function initialize_restaurant_loc() 
    {
    	var ip_lat = '';
    	var ip_long = '';
        <?php if($getrestaurant->st_address == '' && old('rs_lat') =='' && old('rs_long') =='')
        {?>
        	ip_lat = 46.15242437752303;
        	ip_long = 2.7470703125;
        <?php } 
        else if($getrestaurant->st_address == '' && (old('rs_lat') !='' || old('rs_long') !=''))
        { ?>
        	ip_lat = {{old('rs_lat')}};
        	ip_long = {{old('rs_long')}};
        <?php }
        else
        { ?>
        	ip_lat = {{$getrestaurant->st_latitude}};
        	ip_long = {{$getrestaurant->st_longitude}};
        <?php } ?>
        var myLatlng = new google.maps.LatLng(ip_lat,ip_long);
        var mapOptions = {
			           		zoom 			: 15,
			                center 			: myLatlng,
			                disableDefaultUI: true,
			                panControl 		: true,
			                zoomControl 	: true,
			                mapTypeControl 	: true,
			                streetViewControl: true,
			                mapTypeId 		: google.maps.MapTypeId.ROADMAP,
			                fullscreenControl: true
				        };
        map = new google.maps.Map(document.getElementById('us3'),mapOptions);
        var marker = new google.maps.Marker({position: myLatlng,
									         map: map,
									         draggable:true,    
									        }); 
        google.maps.event.addListener(marker, 'dragend', function(e) 
        {
            var lat = this.getPosition().lat();
            var lng = this.getPosition().lng();
            $('#us3-lat').val(lat);
            $('#us3-lon').val(lng); 
        });
        var input = document.getElementById('us3-address');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        google.maps.event.addListener(autocomplete, 'place_changed', function () 
        {
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            if (place.geometry.viewport) 
            {
                map.fitBounds(place.geometry.viewport);
                var myLatlng = place.geometry.location; 
                var latlng = new google.maps.LatLng(lat, lng);
                marker.setPosition(latlng);
            }
            else {
                map.setCenter(place.geometry.location); 
                map.setZoom(17);
            }
            $('#us3-lat').val(lat);
            $('#us3-lon').val(lng);
        });
   }
	//initialize_restaurant_loc();
	
    google.maps.event.addDomListener(window, 'load', initialize_restaurant_loc);
    </script>
	<script>
        $('#add_file').on("click",function(){
            var count = $('.upload_file').length;
            var co = document.getElementById('count_id').value;

            if(count<6)
            {
                $('#file').append('<div class="form-group" id="remove'+co+'"><label class="control-label col-sm-2" for="email">&nbsp;</label><div class="col-sm-4"><input type="file" class="form-control upload_file" name="rs_banner[]" required  id="rs_banner'+co+'" onchange="Upload(this.id,1366,300,1500,500);"></div><div class="col-sm-2" id="remove'+co+'"><input type="button" value="<?php echo (Lang::has(Session::get('mer_lang_file').'.ADMIN_REMOVE')) ? trans(Session::get('mer_lang_file').'.ADMIN_REMOVE') : trans($MER_OUR_LANGUAGE.'.ADMIN_REMOVE'); ?>" class="btn btn-danger btn-sm"  onClick="remove_input('+count+');"></div></div>');
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
                'url' : '<?php echo url('remove_mer_restaurant_banner'); ?>',
                success:function(response)
                {
                    alert("<?php echo (Lang::has(Session::get('mer_lang_file').'.ADMIN_REMOVE_SUCCESS')) ? trans(Session::get('mer_lang_file').'.ADMIN_REMOVE_SUCCESS') : trans($MER_OUR_LANGUAGE.'.ADMIN_REMOVE_SUCCESS'); ?>");
                    var x = parseInt(co) - 1;
                    $('#count_id').val(x);
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
        }

        function show(id)
        {//alert(id);
            if(id == "list")
            {
                $('#'+id).show();
                $('#text').hide();
            }
            else if(id == "text")
            {
                $('#'+id).show();
                $('#list').hide();
            }
        }
        function closedFun(gotVal)
        {
            if($("#closed"+gotVal).prop('checked') == false){
                $('#wk_start_'+gotVal).val('').show();
                $('#wk_end_'+gotVal).val('').show();

            }
            else
            {
                $('#wk_start_'+gotVal).val('12:00am').hide();
                $('#wk_end_'+gotVal).val('12:00am').hide();
                //$('#wk_start_'+gotVal+'-error').hide();
                //$('#wk_end_'+gotVal+'-error').hide();
            }
            $('#wk_start_'+gotVal+'-error').hide();
            $('#wk_end_'+gotVal+'-error').hide();
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
		function onlyNumbersWithDot(e) {
            var charCode;
            if (e.keyCode > 0) {
                charCode = e.which || e.keyCode;
            }
            else if (typeof (e.charCode) != "undefined") {
                charCode = e.which || e.keyCode;
            }
            if (charCode == 46)
                return true
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
		$(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();   
		});
		function check_name_exist(column,column_value){
			//alert(column+'\n'+column_value);
			var hidRestId = $('#hidRestId').val();
			$.ajax({
				'type' : 'get',
				'data' : {'hidRestId':hidRestId,'column':column,'column_value' : column_value},
				'url' : '<?php echo url('check_restName_exists'); ?>',
				success:function(response)
				{
					$('#'+column+'_error').html(response);
				}
			});
		}
	</script>
@endsection
@stop