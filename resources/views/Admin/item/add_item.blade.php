@extends('Admin.layouts.default')
@section('PageTitle')
	{{$pagetitle}}
@endsection
@section('content')
	<!-- MAIN -->
	<style>
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
		input#pro_original_price {
			float: left;
		}

	</style>
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
			float: left;
			top: 83px;
			right: 408px;
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
	</style>
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
								
								{{-- Display error message--}}
								@if ($errors->any())
									<div class="alert alert-warning alert-dismissible">
										<a href="#" class="close" data-dismiss="alert" aria-label="close" style="float:right">&times;</a>
										<ul>
											@foreach($errors->all() as $error)
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
										{!! Form::open(['method' => 'post','class' => 'form-horizontal form-auth-small','url' => $action,'id'=>'profile_form','enctype'=>'multipart/form-data']) !!}
										{!! Form::hidden('pro_id',$getstore->pro_id)!!}
										{!! Form::hidden('pro_currency',$default_currency)!!}
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_REST')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_REST') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_REST')}} <span class="impt">*</span> :</label>

											<div class="col-sm-6">
												@if(count($restaurant_list) > 0)

													{{ Form::select('pro_store_id',$restaurant_list,$getstore->pro_store_id,['class' => 'form-control' , 'style' => 'width:100%','id' => 'pro_store_id'] ) }}

												@else
													{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS') }}
												@endif
												@if ($errors->has('pro_store_id') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_store_id') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_CATE')}} <span class="impt">*</span> :</label>
											<div class="col-sm-6">
												@if(count($category_list) > 0)

													{{ Form::select('pro_category_id',$category_list,$getstore->pro_category_id,['class' => 'form-control' , 'style' => 'width:100%','onchange'=>'getSubCat(this.value)'] ) }}

												@else
													{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO_DETAILS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO_DETAILS') }}
												@endif
												@if ($errors->has('pro_category_id') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_category_id') }}</p>
												@endif
											</div>
										</div>
										<!-- SUB CATEGORY -->

										@if($id!='')
											@php $subCatArray = $subcategory_list; @endphp
										@else
											@php $subCatArray = array(); @endphp
										@endif
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{{ Form::select('pro_sub_cat_id',$subCatArray,$getstore->pro_sub_cat_id,['class' => 'form-control' , 'style' => 'width:100%','id'=>'pro_sub_cat_id','required'] ) }}

												@if ($errors->has('pro_sub_cat_id') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_sub_cat_id') }}</p>
												@endif
											</div>
										</div>
										<!--ITEMCODE-->
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CODE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('pro_item_code',$getstore->pro_item_code,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE_SAMPLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CODE_SAMPLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CODE_SAMPLE'),'id' => 'pro_item_code','maxlength' => '100']) !!}
												@if ($errors->has('pro_item_code') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_item_code') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_NAME')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('pro_item_name',$getstore->pro_item_name,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_NAME'),'id' => 'pro_item_name','maxlength' => '200']) !!}
												@if ($errors->has('pro_item_name') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_item_name') }}</p>
												@endif
											</div>
										</div>
										@if(count($Admin_Active_Language) > 0)
											@foreach($Admin_Active_Language as $lang)
												@php $item_name = 'pro_item_name_'.$lang->lang_code @endphp
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_NAME')}}&nbsp; (In {{$lang->lang_name}})<span class="impt">*</span>:</label>
													<div class="col-sm-6">
														{!! Form::text('pro_item_name_'.$lang->lang_code.'',$getstore->$item_name,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_NAME'),'class'=>'form-control','maxlength'=>'100')) !!}

													</div>
												</div>
											@endforeach
										@endif

									<!--ITEM CONTENT -->
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CONTENT')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('pro_per_product',$getstore->pro_per_product,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CONTENT'),'id' => 'pro_per_product']) !!}
												<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT_HELPBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT_HELPBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CONTENT_HELPBLOCK')}}</span>
												@if ($errors->has('pro_per_product') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_per_product') }}</p>
												@endif
											</div>
										</div>
										@if(count($Admin_Active_Language) > 0)
											@foreach($Admin_Active_Language as $lang)
												@php $item_name = 'pro_per_product_'.$lang->lang_code @endphp
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CONTENT')}}&nbsp; (In {{$lang->lang_name}})<span class="impt">*</span>:</label>
													<div class="col-sm-6">
														{!! Form::text('pro_per_product_'.$lang->lang_code.'',$getstore->$item_name,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_CONTENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_CONTENT'),'class'=>'form-control','maxlength'=>'100')) !!}

													</div>
												</div>
											@endforeach
										@endif
									<!-- EOF ITEM CONTENT-->
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_QTY')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												{!! Form::text('pro_quantity',$getstore->pro_quantity,['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_QTY'),'id' => 'pro_quantity','onkeypress'=>"return onlyNumbers(event);"]) !!}
												@if ($errors->has('pro_quantity') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_quantity') }}</p>
												@endif
											</div>
										</div>
										<div class="form-group">
											<label for="c2" class="control-label col-sm-2" >{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORIGINAL_PRICE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												<div class="input-group">
													<span class="input-group-addon">{{$default_currency}}</span>
													{!! Form::text('pro_original_price',$getstore->pro_original_price,['class'=>'form-control priceClass','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORIGINAL_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ORIGINAL_PRICE'),'id' => 'pro_original_price','onkeypress'=>"return onlyNumbersWithDot(event);"]) !!}
												</div>
												@if ($errors->has('pro_original_price') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_original_price') }}</p>
												@endif
												<div class="priceClassError"></div>
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_HAS_DIS')) ? trans(Session::get('admin_lang_file').'.ADMIN_HAS_DIS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_HAS_DIS')}} <span class="impt">*</span> :</label>
											<div class="col-sm-6">
												@php $hasDiscArray = array('' => 'Select Discount status','yes'=>'Yes','no'=>'No'); @endphp
												{{ Form::select('pro_has_discount',$hasDiscArray,$getstore->pro_has_discount,['class' => 'form-control' , 'style' => 'width:100%','onchange'=>'toggleDiscountDiv(this.value)','id'=>'pro_has_discount'] ) }}
												@if ($errors->has('pro_has_discount') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_has_discount') }}</p>
												@endif
											</div>
										</div>

										<div class="form-group" id="discountDiv" style="@if($getstore->pro_has_discount!='yes') display:none; @endif">
											<label for="c2" class="control-label col-sm-2" >{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISC_PRICE')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												<div class="input-group">
													<span class="input-group-addon">{{$default_currency}}</span>
													{!! Form::text('pro_discount_price',$getstore->pro_discount_price,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DISC_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DISC_PRICE'),'class'=>'form-control col-md-7 col-xs-12 priceClass','autocomplete'=>'off','id'=>'pro_discount_price','onkeypress'=>"return onlyNumbersWithDot(event);")) !!}
												</div>
												@if ($errors->has('pro_discount_price') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_discount_price') }}</p>
												@endif
												<div class="priceClassError"></div>
											</div>
										</div>

										<?php $veg=''; $Nonveg='';
										if($getstore->pro_veg==1){
											 $Nonveg=false; $veg=true;
										 }
										else{
											 $Nonveg=true; $veg=false;
										 }
										?>
										<div class="form-group">
											<label for="text2" class="control-label col-sm-2">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_VEG_NON')) ? trans(Session::get('admin_lang_file').'.ADMIN_VEG_NON') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VEG_NON') }}<span class="impt">*</span></label>
											<div class="col-sm-6">
												<div class="form-check form-check-inline">
													<label class="form-check-label">{!! Form::radio('pro_veg', '1',$veg) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Veg')) ? trans(Session::get('admin_lang_file').'.ADMIN_Veg') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Veg')}} </label>
													<label class="form-check-label">{!! Form::radio('pro_veg', '2',$Nonveg) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Non-veg')) ? trans(Session::get('admin_lang_file').'.ADMIN_Non-veg') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Non-veg')}}</label>
													@if ($errors->has('pro_veg') )
														<p class="error-block" style="color:red;">{{ $errors->first('pro_veg') }}</p>
													@endif
												</div>
											</div>
										</div>

										@if($getstore->pro_had_tax=='Yes')
											@php $hadTaxYes=true; $hadTaxNo=false; @endphp
										@elseif($getstore->pro_had_tax=='No')
											@php $hadTaxYes=false; $hadTaxNo=true; @endphp
										@else
											@php $hadTaxYes=false; $hadTaxNo=true; @endphp
										@endif

										<div class="form-group">
											<label for="text2" class="control-label col-sm-2">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TAX')}}<span class="impt">*</span></label>
											<div class="col-sm-6">
												<div class="form-check form-check-inline">
													<label class="form-check-label">{!! Form::radio('pro_had_tax', 'Yes',$hadTaxYes) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_YES')) ? trans(Session::get('admin_lang_file').'.ADMIN_YES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_YES')}} </label>
													<label class="form-check-label">{!! Form::radio('pro_had_tax', 'No',$hadTaxNo) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO')}}</label>
													@if ($errors->has('pro_had_tax') )
														<p class="error-block" style="color:red;">{{ $errors->first('pro_had_tax') }}</p>
													@endif
												</div>
											</div>
										</div>
										<div class="form-group" id="taxDiv" style="@if($getstore->pro_had_tax!='Yes') display:none; @endif">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TAX_NAME')}}<span class="impt">*</span> :</label>

											<div class="col-sm-3">
												{!! Form::text('pro_tax_name',$getstore->pro_tax_name,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TAX_NAME'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off','id'=>'pro_tax_name')) !!}
												@if ($errors->has('pro_tax_name') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_tax_name') }}</p>
												@endif
											</div>
											<div class="col-sm-3">
												{!! Form::text('pro_tax_percent',$getstore->pro_tax_percent,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX_PERCENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TAX_PERCENT'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off','id'=>'pro_tax_percent','onkeypress'=>"return onlyNumbersWithDot(event);",'maxlength' => '6')) !!}
												@if ($errors->has('pro_tax_percent') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_tax_percent') }}</p>
												@endif
											</div>
										</div>

										@if(count($Admin_Active_Language) > 0)
											@foreach($Admin_Active_Language as $lang)
												@php $taxname = 'pro_tax_name_'.$lang->lang_code @endphp
												<div class="form-group" id="taxDivlang" style="@if($getstore->pro_had_tax!='Yes') display:none; @endif">
													<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TAX_NAME')}}(In {{$lang->lang_name}})<span class="impt">*</span> :</label>

													<div class="col-sm-3">
														{!! Form::text('pro_tax_name_'.$lang->lang_code.'',$getstore->$taxname,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_TAX_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TAX_NAME'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off','id'=>'pro_tax_name_lang')) !!}
														@if ($errors->has('pro_tax_name_'.$lang->lang_code) )
															<p class="error-block" style="color:red;">{{ $errors->first('pro_tax_name') }}</p>
														@endif
													</div>
												</div>
											@endforeach
										@endif

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION')}}<span class="impt">*</span> :</label>

											<div class="col-sm-6">
												{!! Form::textarea('pro_desc',$getstore->pro_desc,array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION'),'class'=>'form-control summernote')) !!}
												@if ($errors->has('pro_desc') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_desc') }}</p>
												@endif
											</div>

										</div>
									<div id="item_error" style="margin-left: 20%"></div>
										@if(count($Admin_Active_Language) > 0)
											@foreach($Admin_Active_Language as $lang)
												@php $desc = 'pro_desc_'.$lang->lang_code @endphp
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION')}}&nbsp; (In {{$lang->lang_name}})<span class="impt">*</span>:</label>
													<div class="col-sm-6">
														{!! Form::textarea('pro_desc_'.$lang->lang_code.'',$getstore->$desc,array('required','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION'),'class'=>'form-control summernote')) !!}

													</div>
												</div>
											@endforeach
										@endif

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS')}} :</label>

											<div class="col-sm-6">
												{!! Form::textarea('pro_meta_keyword',$getstore->pro_meta_keyword,array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS'),'class'=>'form-control')) !!}
												@if ($errors->has('pro_meta_keyword') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_meta_keyword') }}</p>
												@endif
											</div>
										</div>

										@if(count($Admin_Active_Language) > 0)
											@foreach($Admin_Active_Language as $lang)
												@php $desc = 'pro_meta_keyword_'.$lang->lang_code @endphp
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS')}}&nbsp; (In {{$lang->lang_name}}):</label>
													<div class="col-sm-6">
														{!! Form::textarea('pro_meta_keyword_'.$lang->lang_code.'',$getstore->$desc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_KEYWORDS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_KEYWORDS'),'class'=>'form-control')) !!}

													</div>
												</div>
											@endforeach
										@endif

										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION')}} :</label>

											<div class="col-sm-6">
												{!! Form::textarea('pro_meta_desc',$getstore->pro_meta_desc,array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION'),'class'=>'form-control')) !!}
												@if ($errors->has('pro_meta_desc') )
													<p class="error-block" style="color:red;">{{ $errors->first('pro_meta_desc') }}</p>
												@endif
											</div>
										</div>

										@if(count($Admin_Active_Language) > 0)
											@foreach($Admin_Active_Language as $lang)
												@php $desc = 'pro_meta_desc_'.$lang->lang_code @endphp
												<div class="form-group">
													<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION')}}&nbsp; (In {{$lang->lang_name}}):</label>
													<div class="col-sm-6">
														{!! 	Form::textarea('pro_meta_desc_'.$lang->lang_code.'',$getstore->$desc,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_META_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_META_DESCRIPTION'),'class'=>'form-control')) !!}
													</div>
												</div>
											@endforeach
										@endif

										@if($getstore->pro_had_choice=='1')
											@php $hadchoiceYes=true; $hadchoiceNo=false; @endphp
										@elseif($getstore->pro_had_choice=='2')
											@php $hadchoiceYes=false; $hadchoiceNo=true; @endphp
										@else
											@php $hadchoiceYes=false; $hadchoiceNo=true; @endphp
										@endif
										<div class="form-group">
											<label class="control-label col-sm-2" for="email">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CHOOSE_CHOICES')) ? trans(Session::get('admin_lang_file').'.ADMIN_CHOOSE_CHOICES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CHOOSE_CHOICES')}} <span class="impt">*</span>:</label>
											<div class="col-sm-6">
												<div class="form-check form-check-inline">
													<label class="form-check-label">{!! Form::radio('pro_had_choice', '1',$hadchoiceYes) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_YES')) ? trans(Session::get('admin_lang_file').'.ADMIN_YES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_YES')}} </label>
													<label class="form-check-label">{!! Form::radio('pro_had_choice', '2',$hadchoiceNo) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO')}}</label>
													@if ($errors->has('pro_had_choice') )
														<p class="error-block" style="color:red;">{{ $errors->first('pro_had_choice') }}</p>
													@endif
												</div>
											</div>
										</div>
										@php
											$oldchoices = old('choices');
                                            if($oldchoices!= '' && count($entered_choice) <= 0)
                                            {
                                                $oldEnteredChoices= array_flip($oldchoices);
                                            }
                                            else
                                            {
                                                $oldEnteredChoices=$entered_choice;
                                            }
										@endphp
										<div class="form-group">
											<label class="col-sm-2"></label>
											<div class="col-sm-6">
												<div  id="choicesDiv" style="@if($getstore->pro_had_choice!='1') display:none; @endif ">
													<h3>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CHOICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CHOICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_CHOICE')}}</h3>
													<div class="choiceErrorClass"></div>
													@if(count($choices_list) > 0)
														@foreach($choices_list as $choice)
															<div class="row" style="margin-left: 0px;">
																<div class="form-group">
																	<div class="col-sm-6">
																		<label class="fancy-checkbox"><input type="checkbox" class="choiceCheckBox" name="choices[]" value="{{$choice->ch_id}}" onchange="showPriceDiv({{$choice->ch_id}})" id="choiceChkBox_{{$choice->ch_id}}" @if(array_key_exists($choice->ch_id,$oldEnteredChoices)) checked @endif >
																			<span>{{$choice->ch_name}}</span>
																		</label>
																	</div>
																	<div class="col-sm-4" style="@if(!array_key_exists($choice->ch_id,$oldEnteredChoices)) display:none @endif " id="choicePriceDiv_{{$choice->ch_id}}" >

																		@if(array_key_exists($choice->ch_id,$entered_choice)) @php $priceValue = $entered_choice[$choice->ch_id];@endphp @else @php $priceValue='' @endphp @endif
																		{!! Form::text('pro_choice_price['.$choice->ch_id.']',$priceValue,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRICE'),'class'=>'form-control col-md-7 resForm-control col-xs-12','autocomplete'=>'off','onkeypress'=>"return onlyNumbersWithDot(event);")) !!}
																		<div class="input-group-append">
																			<span class="input-group-text" id="basic-addon2">{{$default_currency}}</span>
																		</div>
																	</div>
																</div>
															</div>
														@endforeach
													@endif
												</div>
											</div>
										</div>
										<?php /* 
										<div  id="choicesDiv" style="@if($getstore->pro_had_choice!='1') display:none; @endif margin-left:50px;">
											<h3>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_CHOICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_CHOICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_CHOICE')}}</h3>
											@if(count($choices_list) > 0)
												@foreach($choices_list as $choice)
													<div class="row">
														<div class="form-group">
															<label class="col-sm-2"></label>
															{{--<div class="col-sm-3">--}}
																{{--<label class="fancy-checkbox"><input type="checkbox" name="choices[]" value="{{$choice->ch_id}}" onchange="showPriceDiv({{$choice->ch_id});" id="choiceChkBox_{{$choice->ch_id}}" @if(array_key_exists($choice->ch_id,$oldEnteredChoices)) checked @endif >--}}
																	{{--<span>{{$choice->ch_name}}</span>--}}
																{{--</label>--}}
															{{--</div>--}}

															<div class="col-sm-6">
																<label class="fancy-checkbox"><input type="checkbox" class="choiceCheckBox" name="choices[]" value="{{$choice->ch_id}}" onchange="showPriceDiv({{$choice->ch_id}})" id="choiceChkBox_{{$choice->ch_id}}" @if(array_key_exists($choice->ch_id,$oldEnteredChoices)) checked @endif >
																	<span>{{$choice->ch_name}}</span>
																</label>

															</div>
															<div class="col-sm-2" style="@if(!array_key_exists($choice->ch_id,$oldEnteredChoices)) display:none @endif " id="choicePriceDiv_{{$choice->ch_id}}" >

																@if(array_key_exists($choice->ch_id,$entered_choice))
																@php $priceValue = $entered_choice[$choice->ch_id];@endphp @else @php $priceValue='' @endphp @endif
																{!! Form::text('pro_choice_price['.$choice->ch_id.']',$priceValue,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PRICE'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off','onkeypress'=>"return onlyNumbersWithDot(event);")) !!}
																<div class="input-group-append">
																	<span class="input-group-text" id="basic-addon2">{{$default_currency}}</span>
																</div>
															</div>
														</div>
													</div>
												@endforeach
											@endif
										</div> */ ?>
										@if($getstore->pro_had_spec=='1')
											@php $hadspecYes=true; $hadspecNo=false; @endphp
										@elseif($getstore->pro_had_spec=='2')
											@php $hadspecYes=false; $hadspecNo=true; @endphp
										@else
											@php $hadspecYes=false; $hadspecNo=true; @endphp
										@endif
										<div class="form-group">
											<label for="text2" class="control-label col-sm-2">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WANT_TO_ADD_SPECIFICATION')) ? trans(Session::get('admin_lang_file').'.ADMIN_WANT_TO_ADD_SPECIFICATION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WANT_TO_ADD_SPECIFICATION')}}<span class="impt">*</span></label>
											<div class="col-sm-6">
												<div class="form-check form-check-inline">
													<label class="form-check-label">{!! Form::radio('pro_had_spec', '1',$hadspecYes) !!} {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_YES')) ? trans(Session::get('admin_lang_file').'.ADMIN_YES') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_YES')}} </label>
													<label class="form-check-label">{!! Form::radio('pro_had_spec', '2',$hadspecNo) !!}{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NO')) ? trans(Session::get('admin_lang_file').'.ADMIN_NO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NO')}}</label>
													@if ($errors->has('pro_had_spec') )
														<p class="error-block" style="color:red;">{{ $errors->first('pro_had_spec') }}</p>
													@endif
												</div>
											</div>
										</div>
										<div id="specDiv" style="@if($getstore->pro_had_spec!='1') display:none @endif">
											@if($id=='')
												<div class="form-group">
													<label for="text2" class="control-label col-lg-2">&nbsp;</label>
													<div class="col-sm-3">
														{!! Form::text('spec_title[]','',array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SPEC_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SPEC_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SPEC_TITLE'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off')) !!}
													</div>
													<div class="col-sm-5">
														{!! Form::textarea('spec_desc[]','',array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION'),'class'=>'form-control summernote','rows'=>2)) !!}
													</div>
													<div class="col-sm-2">
														<a onclick="addspecificationFormField();" class="btn btn-success"> {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MORE')}} </a>
														<input type="hidden" id="specificationcount" value="1" />
													</div>
												</div>
											@else
												@if(count($entered_spec) > 0)
													@php $spec_count=1 @endphp
													@foreach($entered_spec as $key=>$value)
														<div class="form-group specaddmoreClass" id="newspec{{$spec_count}}">
															<label for="text2" class="control-label col-lg-2">&nbsp;</label>
															<div class="col-sm-3">
																{!! Form::text('spec_title[]',$value,array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SPEC_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SPEC_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SPEC_TITLE'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off')) !!}
															</div>
															<div class="col-sm-5">
																{!! Form::textarea('spec_desc[]',$key,array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION'),'class'=>'form-control summernote','rows'=>2)) !!}
															</div>

															<div class="col-sm-2">
																@if($spec_count=='1')
																	<a onclick="addspecificationFormField();" class="btn btn-success"> {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MORE')}} </a>
																@else
																	<a onclick="removespecificationFormField({{$spec_count}});" class="btn btn-danger"> Remove   </a>
																@endif
															</div>
														</div>
														@php $spec_count++; @endphp
													@endforeach
													<input type="hidden" id="specificationcount" value="{{count($entered_spec)+1}}" />

												@else
													<div class="form-group">
														<label for="text2" class="control-label col-lg-2">&nbsp;</label>
														<div class="col-sm-3">
															{!! Form::text('spec_title[]','',array('placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_SPEC_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SPEC_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SPEC_TITLE'),'class'=>'form-control col-md-7 col-xs-12','autocomplete'=>'off')) !!}
														</div>
														<div class="col-sm-5">
															{!! Form::textarea('spec_desc[]','',array('placeholder'=> (Lang::has(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DESCRIPTION'),'class'=>'form-control summernote','rows'=>2)) !!}
														</div>
														<div class="col-sm-2">
															<a onclick="addspecificationFormField();" class="btn btn-success"> {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MORE')}} </a>
															<input type="hidden" id="specificationcount" value="1" />
														</div>
													</div>
												@endif
											@endif
											<div id="appendSpec"></div>
										</div>
										@php $item_image = explode('/**/',$getstore->pro_images);   @endphp
										@if($id=='')
											<div class="form-group">
												<label class="control-label col-sm-2" for="email">{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_IMAGE')}} <span class="impt">*</span>:</label>

												<div class="col-sm-4">
													{{ Form::file('item_img[]',array('class' => 'form-control upload_file','accept'=>'image/*','id'=>'item_img1','onchange'=>'Upload(this.id,"800","800");')) }}
													<input type="hidden" id="fileCount" name="fileCount" value="1" />
												</div>
												<div class="col-sm-4 add-item-sec">
													<span style="display:block" id="add_more_bt">{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MORE'),['class' =>'btn btn-success btn-sm add-item-btn','id' => 'add_file'])!!}</span>
													<span>{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAX_5')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAX_5') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAX_5')}}&nbsp;({{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIMEN800x800_VAL')}})</span>
												</div>
											</div>
										@else
											{!! Form::hidden('old_images',$getstore->pro_images,['id' => 'old_images']) !!}
											@if(count($item_image) > 0)
												@php $itemImageCount=1;  @endphp
												@foreach($item_image as $itimg)
													@if($itimg!='')
														<div class="form-group" id="fileDiv_{{$itemImageCount}}">
															<label class="control-label col-sm-2" for="email">@if($itemImageCount==1) {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITEM_IMAGE')}} <span class="impt">*</span>: @endif</label>
															<div class="col-sm-1">
																@php $filename = public_path('images/restaurant/items/').$itimg;  @endphp
																@if(file_exists($filename))
																	<img class="item_image" id="myImg_{{$itemImageCount}}" src="{{url('public/images/restaurant/items/'.$itimg)}}"  alt="{{$itimg}}" style="width: 85px;height: 85px;cursor:pointer">
																@else
																	<img class="item_image" id="myImg_{{$itemImageCount}}" src="{{url('public/images/noimage/'.$no_item)}}"  alt="{{$itimg}}" style="width: 85px;height: 85px;cursor:pointer">
																@endif
															</div>
															<div class="col-sm-4">
																{{ Form::file('item_img[]',array('class' => 'form-control upload_file','accept'=>'image/*','id'=>'item_img'.$itemImageCount,'onchange'=>'Upload(this.id,"800","800");')) }}
															</div>
															<div class="col-sm-3">
																@if($itemImageCount==1)
																	<span style="display:block" id="add_more_bt">{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_ADD_MORE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADD_MORE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADD_MORE'),['class' =>'btn btn-success btn-sm','id' => 'add_file'])!!}</span>
																	{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_MAX_5')) ? trans(Session::get('admin_lang_file').'.ADMIN_MAX_5') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MAX_5')}}&nbsp;{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIMEN800x800_VAL')}}
																@else
																	<a href="javascript:removeFile({{$itemImageCount}},'/**/{{$itimg}}')" class="btn btn-danger" alt="Remove">Remove </a>
																@endif
															</div>
														</div>
														@php $itemImageCount++; @endphp
													@endif
												@endforeach
												<input type="hidden" name="fileCount" id="fileCount" value="{{count($item_image)+1}}" />
											@endif

										@endif
										<span id="file"></span>
										
										<div class="form-group">
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
											@if($id!='')
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
											@else
												@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
											@endif
											@php $url = url('manage-item')@endphp
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											{!! Form::button('Cancel',['class' => 'btn btn-warning' ,'onclick'=>"javascript:window.location.href='$url'"])!!}
											</div>
										</div>
										
										{!! Form::close() !!}
									</div>
								</div>
								{{-- Add page ends--}}

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
	<div id="myModal" class="modal">
		<span class="close">&times;</span>
		<img class="modal-content" id="img01">
		<div id="caption"></div>
	</div>
@section('script')
	<script src="{{url('')}}/public/admin/assets/scripts/summernote.js"></script>
	<script>
        $(document).ready(function() {
            $('.summernote').summernote();
        });
        function summernoteCall()
        {
            $('.summernote').summernote();
        }
        //GET SUB CATEGORY, IF VALIDATION FAILURE
        if($('select[name=pro_category_id]').val()!=0)
        {
            getSubCat($('select[name=pro_category_id]').val());
        }
        //HIDE OR SHOW TAX DIV, IF VALIDATION FAILURE
        var value = $( 'input[name=pro_had_tax]:checked' ).val();
        if(value=='Yes')
        {
            $('#taxDiv').slideDown();
            $('#taxDivlang').slideDown();

        }
        else
        {
            $('#taxDiv').slideUp();
            $('#taxDivlang').slideUp();
        }
        //HIDE OR SHOW DISCOUNT PRICE, IF VALIDATION FAILURE
        if($('#pro_has_discount').val()=='yes')
        {
            $('#discountDiv').show();
        }
        else
        {
            $('#discountDiv').hide();
        }
        //HIDE OR SHOW SPECIFICATION DETAILS, IF VALIDATION FAILURE
        if( $( 'input[name=pro_had_spec]:checked' ).val()==1)
        {
            $('#specDiv').show();
        }
        else
        {
            $('#specDiv').hide();
        }
        //HIDE OR SHOW CHOICE DETAILS, IF VALIDATION FAILURE
        if($( 'input[name=pro_had_choice]:checked' ).val()==1)
        {
            $('#choicesDiv').slideDown();
        }
        else
        {
            $('#choicesDiv').slideUp();
        }

        function getSubCat(gotVal)
        {
            if(gotVal!='0')
            {
                $.ajax({
                    type: 'get',
                    url: "{{url('get_sub_category')}}",
                    data: {pro_main_id : gotVal},
                    success: function(response){
                        $('#pro_sub_cat_id').html(response);
                        var old_input = '{{ old('pro_sub_cat_id') }}';
                        var entered_input = '{{$getstore->pro_sub_cat_id}}';
                        if(old_input!='' && entered_input=='')
                        {
                            var changeInput = old_input;
                        }
                        else
                        {
                            var changeInput = entered_input;
                        }
                        $('#pro_sub_cat_id').val(changeInput);
                    }
                });
            }
            else
            {
                $('#pro_sub_cat_id').html('<option value="" selected="selected">Select Subcategory</option>');
            }
        }
        function toggleDiscountDiv(gotVal)
        {
            if(gotVal=='yes')
            {
                $('#discountDiv').slideDown();
            }
            else
            {
                $('#discountDiv').slideUp();
            }
        }
        function addspecificationFormField()
        {
            var newCount = document.getElementById("specificationcount").value;
            $('#appendSpec').append('<div class="form-group specaddmoreClass" id="newspec'+newCount+'"><label for="text2" class="control-label col-lg-2">&nbsp;</label><div class="col-sm-3">{!! Form::text("spec_title[]","",array("placeholder"=>(Lang::has(Session::get("admin_lang_file").".ADMIN_SPEC_TITLE")) ? trans(Session::get("admin_lang_file").".ADMIN_SPEC_TITLE") : trans($ADMIN_OUR_LANGUAGE.".ADMIN_SPEC_TITLE"),"class"=>"form-control col-md-7 col-xs-12","autocomplete"=>"off")) !!}</div><div class="col-sm-5">{!! Form::textarea("spec_desc[]","",array("required","placeholder"=> (Lang::has(Session::get("admin_lang_file").".ADMIN_DESCRIPTION")) ? trans(Session::get("admin_lang_file").".ADMIN_DESCRIPTION") : trans($ADMIN_OUR_LANGUAGE.".ADMIN_DESCRIPTION"),"class"=>"form-control summernote","rows"=>2)) !!}</div><div class="col-sm-2"><a onclick="removespecificationFormField('+newCount+');" class="btn btn-danger"> {{ (Lang::has(Session::get("admin_lang_file").".ADMIN_REMOVE")) ? trans(Session::get("admin_lang_file").".ADMIN_REMOVE") : trans($ADMIN_OUR_LANGUAGE.".ADMIN_REMOVE")}}   </a></div></div>');
            newCount = parseInt(newCount)+1;
            document.getElementById("specificationcount").value=newCount;
            summernoteCall();
        }
        function removespecificationFormField(id)
        {
            $('#newspec'+id).remove();
        }
        function showPriceDiv(ch_id)
        {
            if($("#choiceChkBox_"+ch_id).prop('checked') == true){
                $('#choicePriceDiv_'+ch_id).slideDown();
            }
            else
            {
                $('#choicePriceDiv_'+ch_id).slideUp();
            }
        }

        //$("#choiceChkBox_
        $('input[name=pro_had_choice]').change(function(){
            var value = $( 'input[name=pro_had_choice]:checked' ).val();
            if(value==1)
            {
                $('#choicesDiv').slideDown();
            }
            else
            {
                $('#choicesDiv').slideUp();
            }
        });
        $('input[name=pro_had_spec]').change(function(){
            var value = $( 'input[name=pro_had_spec]:checked' ).val();
            if(value==1)
            {
                $('#specDiv').slideDown();
            }
            else
            {
                $('#specDiv').slideUp();
            }
        });
        $('input[name=pro_had_tax]').change(function(){
            var value = $( 'input[name=pro_had_tax]:checked' ).val();
            if(value=='Yes')
            {
                $('#taxDiv').slideDown();
                $('#taxDivlang').slideDown();
            }
            else
            {
                $('#taxDiv').slideUp();
                $('#taxDivlang').slideUp();
            }
        });

        $('#add_file').on("click",function(){
            var count = $('#fileCount').val();
            var newcount = $('.upload_file').length;
            if(newcount<=4)
            {

                $('#file').append('<div class="form-group" id="fileDiv_'+count+'"><label class="control-label col-sm-2" for="email">&nbsp;</label><div class="col-sm-4"><input class="form-control upload_file" accept="image/*" id="item_img'+count+'" onchange="Upload(this.id,\'800\',\'800\');" name="item_img[]" type="file"></div> <div class="col-sm-3"> <a href="javascript:removeFile('+count+',\'\')" class="btn btn-danger">{{ (Lang::has(Session::get("admin_lang_file").".ADMIN_REMOVE")) ? trans(Session::get("admin_lang_file").".ADMIN_REMOVE") : trans($ADMIN_OUR_LANGUAGE.".ADMIN_REMOVE")}} </a></div></div>');
                newCount = parseInt(count)+1;
                document.getElementById("fileCount").value=newCount;
            }
            if(newcount == 4)
            {
                $('#add_more_bt').hide();
            }
        });

        function removeFile(id,removeFileName)
        {
            var oldFile = $('#old_images').val();
            var added_count = $('.upload_file').length;
            if(removeFileName != '')
            {
                var res = oldFile.replace(removeFileName,'');
                $('#old_images').val(res);
                $('#fileDiv_'+id).remove();
            }
            else
            {
                $('#fileDiv_'+id).remove();
            }
            if(added_count<=5)
            {
                $('#add_more_bt').show();
            }
        }

        $.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg !== value;
        }, "Value must not equal arg.");

		$.validator.addMethod('minImageWidth', function(value, element, minWidth) {
			return ($(element).data('imageWidth') || 0) > minWidth;
		}, function(minWidth, element) {
			var imageWidth = $(element).data('imageWidth');
			return (imageWidth) ? ("Your image's width must be greater than " + minWidth + "px"): "Selected file is not an image.";
		});
        $("#profile_form").validate({
            //onkeyup: true,
            onfocusout: function (element) {
                this.element(element);
            },
            rules: {
                "pro_store_id": { valueNotEquals: "0" },
                "pro_category_id": { valueNotEquals: "0" },
                "pro_sub_cat_id": { valueNotEquals: "0" },
                "pro_item_code": "required",
                "pro_item_name": "required",
                "pro_per_product": "required",
                "pro_quantity": "required",
                "pro_original_price": "required",
                "pro_has_discount": "required",
                "pro_discount_price": {
                    required: {
                        depends: function(element) {

                            if($('#pro_has_discount').val()=='yes'){  return true; } else { return false; }
                        }
                    },
                },
                "pro_desc":"required",
                "pro_had_choice":"required",
                "choices[]": {
                    required: {
                        depends: function(element) {
                            if($('input[name=pro_had_choice]:checked').val()=='1'){ return true; } else { return false; }
                        }
                    }
                },
                "pro_had_tax":"required",
                "pro_tax_name": {
                    required: {
                        depends: function(element) {
                            if($('input[name=pro_had_tax]:checked').val()=='Yes'){ return true; } else { return false; }
                        }
                    }
                },
				@if(count($Admin_Active_Language) > 0)
					@foreach($Admin_Active_Language as $lang)
						@php $taxname = 'pro_tax_name_'.$lang->lang_code @endphp
						"{{$taxname}}": {
							required: {
								depends: function(element) {
									if($('input[name=pro_had_tax]:checked').val()=='Yes'){ return true; } else { return false; }
								}
							}
						},
					@endforeach
				@endif
					
                "pro_tax_percent": {
                    required: {
                        depends: function(element) {
                            if($('input[name=pro_had_tax]:checked').val()=='Yes'){ return true; } else { return false; }
                        }
                    }
                },
                "pro_had_spec":"required",
                "spec_title[]": {
                    required: {
                        depends: function(element) {
                            if($('input[name=pro_had_spec]:checked').val()=='1'){ return true; } else { return false; }
                        }
                    }
                },
                "spec_desc[]": {
                    required: {
                        depends: function(element) {
                            if($('input[name=pro_had_spec]:checked').val()=='1'){ return true; } else { return false; }
                        }
                    }
                },
				"item_img[]": {
                    required: {
                        depends: function(element) {
                            if($('input[name=pro_id]').val()==''){ return true; } else { return false; }
                        }
                    },
					//minImageWidth:800
                },
				//"item_img[]": { minImageWidth: "800" },
                //"item_img[]":"required"
            },
            messages: {
                "pro_store_id": { valueNotEquals: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RESTAURANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RESTAURANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_RESTAURANT')}}" },
                "pro_category_id": { valueNotEquals: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PL_SL_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PL_SL_CATE')}}" },
                "pro_sub_cat_id": { valueNotEquals: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE')}}" },
                "pro_item_code": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ITEM_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ITEM_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ITEM_CODE')}}",
                "pro_item_name": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ITEM_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ITEM_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ITEM_NAME')}}",
                "pro_per_product": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ITEM_CONTENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ITEM_CONTENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ITEM_CONTENT')}}",
                "pro_quantity": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_QUANTITY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_QUANTITY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_QUANTITY')}}",
                "pro_original_price": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_ORIGINAL_PRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_ORIGINAL_PRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_ORIGINAL_PRICE')}}",
                "pro_has_discount": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADDISC')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADDISC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADDISC')}}",
                "pro_discount_price": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DISCPRICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DISCPRICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DISCPRICE')}}",
                "pro_desc": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DESC')}}",
                "pro_had_choice": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADCHOICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADCHOICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADCHOICE')}}",
                "choices[]": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_CHOICE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_CHOICE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_CHOICE')}}",
                "pro_had_tax":"{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_TAX')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_TAX') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_TAX')}}",
				
                "pro_tax_name":"{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_NAME')}}",
				@if(count($Admin_Active_Language) > 0)
					@foreach($Admin_Active_Language as $lang)
						@php 
							$taxname = 'pro_tax_name_'.$lang->lang_code; 
							$errName = (Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_NAME');
							$errName .=' (In '.$lang->lang_name.')';
						@endphp
						 "{{$taxname}}":"{{$errName}}",
					@endforeach
				@endif
                "pro_tax_percent":"{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_PERCENT')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TAX_PERCENT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TAX_PERCENT')}}",
                "pro_had_spec": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_HADSPEC')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_HADSPEC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_HADSPEC')}}",
                "spec_title[]": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_SPECTITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_SPECTITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_SPECTITLE')}}",
                "spec_desc[]": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_DESC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_DESC')}}",
                "item_img[]": "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_IMAGE')}}",
				 //"item_img[]": { valueNotEquals: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEL_SUBCATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEL_SUBCATE')}}" },
            },
            errorPlacement: function(error, element)
            {
                if ( $(element).hasClass("priceClass") )
                {
                    //alert($(element).parent().next().attr('class'));priceClassError
                    error.appendTo( $(element).parent().next());
                    //error.insertAfter($(element).parents('div').prev($('.pre_order_label')));
                }
                else
                { // This is the default behavior
                    error.insertAfter( element );
                }
            }
        });



        // Get the modal
	</script>
	<script type="text/javascript">
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
        function onlyNumbers(evt) {
            var e = event || evt; // for trans-browser compatibility
            var charCode = e.which || e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
	</script>
	<script>
        $('#profile_form').each(function () {
            if ($(this).data('validator'))
                $(this).data('validator').settings.ignore = ".note-editor *";
        });
        /*$(document).ready(function(){
            $('.item_image').click(function(){
            var modal = document.getElementById('myModal');
            var img = document.getElementById(this.id);
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
            });
            $('.close').click(function(){
            var modal = document.getElementById('myModal');
            modal.style.display = "none";
            });
            });
        */
        $('#pro_discount_price').change(function(){
            if(parseFloat($('#pro_discount_price').val()) > parseFloat($('#pro_original_price').val()))
            {
                alert('Please enter less than original price!');
                $('#pro_discount_price').val('');
            }
        });

         $(document).ready(function(){
            $(".summernote").summernote({

                height: "200",
                callbacks: {
                    onChange: function(e) {
                        var limiteCaracteres = 150;
                        var caracteres = $(".note-editable").text();
                        var totalCaracteres = caracteres.length;

                        //Update value
                        $("#total-caracteres").text(totalCaracteres);

                        //Check and Limit Charaters
                        if(totalCaracteres <= limiteCaracteres){
                            $('#item_error').css("display", "block");
                            $('#item_error').css("color", "red");
						$('#item_error').html('Descript shold be minimum 100 characters');
						// alert('hi');
                            return false;
                        }else{
                        	
                        	$('#item_error').css("display", "none");
						$('#item_error').html('');
                        }
                    }
                }
            });
        });
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
	<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();   
	});
	</script>
@endsection
@stop