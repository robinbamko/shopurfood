@extends('Admin.layouts.default')
  
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
							<div class="box-body spaced">
								<div id="location_form" class="collapse in panel-body">
									{{--Edit page values--}}
									@php $faq_ques = $faq_ans = $faq_id = ''; 
									@endphp
									@if($id != '' && empty($faq_detail) === false)
									@php 
									$faq_ques = $faq_detail->faq_name_en;
									$faq_ans = $faq_detail->faq_ans_en;
									$faq_id = $faq_detail->id;
									@endphp
									@endif
									{{--Edit page values--}}
									<div class="">
										@if($id != '' && empty($faq_detail) === false)
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-faq','enctype'=>'multipart/form-data','id'=>'faq_form']) !!}
										{!! Form::hidden('faq_id',$faq_id)!!}
										@else
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-faq','enctype'=>'multipart/form-data','id'=>'faq_form']) !!}
										{!! Form::hidden('faq_id',$faq_id)!!}
										@endif
										<div class="row panel-heading">
											<label class="col-sm-2">
												<span class="">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_QUESTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_QUESTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_QUESTION')}}&nbsp;*
												</span>
											</label>
											<div class="col-sm-6">
												
												{!! Form::text('faq_name_en',$faq_ques,['class'=>'form-control','id' => 'cus_name','required']) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
											@if(count($Admin_Active_Language) > 0)
												@foreach($Admin_Active_Language as $lang)
													@php
														$faq_lang = '';
                                                        $quest_lang = 'faq_name_'.$lang->lang_code;
													@endphp
													@if($id != '' && empty($faq_detail) === false)
														@php
															$faq_lang = $faq_detail->$quest_lang;
														@endphp
													@endif

													<div class="row panel-heading">
														<label class="col-sm-2">
												<span class="">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_QUESTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_QUESTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_QUESTION')}}(In {{$lang->lang_name}})*
												</span>
														</label>
														<div class="col-sm-6">

															{!! Form::text('faq_name_'.$lang->lang_code.'',$faq_lang,['class'=>'form-control','id' => 'cfaq_name','required']) !!}
															<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
														</div>
													</div>

													<!-- end -->
												@endforeach
											@endif


										
										<div class="row panel-heading">
											<label class="col-sm-2">
												<span class="">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ANSWER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ANSWER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ANSWER')}}&nbsp;*
												</span>
											</label>
											<div class="col-sm-6">
												{!! Form::textarea('faq_ans_en',$faq_ans,['class'=>'form-control summernote','required']) !!}
											</div>
										</div>

											@if(count($Admin_Active_Language) > 0)
												@foreach($Admin_Active_Language as $lang)
													@php
														$faqans_lang = '';
                                                        $ans_lang = 'faq_ans_'.$lang->lang_code;
													@endphp
													@if($id != '' && empty($faq_detail) === false)
														@php
															$faqans_lang = $faq_detail->$ans_lang;
														@endphp
													@endif

													<div class="row panel-heading">
														<label class="col-sm-2">
												<span class="">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ANSWER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ANSWER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ANSWER')}} (In {{$lang->lang_name}})*
												</span>
														</label>
														<div class="col-sm-6">
															{!! Form::textarea('faq_ans_'.$lang->lang_code.'',$faqans_lang,['class'=>'form-control summernote','required']) !!}
														</div>
													</div>

													<!-- end -->
												@endforeach
											@endif
										
										<div class="row panel-heading">
											<div class="form-group">
												<div class="col-sm-2"></div>
												<div class="col-sm-6">
												@if($id!='')
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
												@else
													@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
												@endif
												
												{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
												@if($id!='')
												<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-faq'); ?>'">
												@else
													<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-faq'); ?>'">
												@endif
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
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>
@section('script')
 <script src="{{url('')}}/public/admin/assets/scripts/summernote.js"></script>
<script type="text/javascript">
	function strip_tags(str) {
		str = str.toString();
		return str.replace(/<\/?[^>]+>/gi, '');
	}
	jQuery.validator.addMethod("noSpace", function(value, element) { 
	  return strip_tags(value).trim().length != 0; 
	}, "No space please and don't leave it empty");
 	$("#faq_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			faq_name_en: "required",
			faq_ans_en : { noSpace : true }
				
		},
		messages: {
			faq_name_en: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FAQ')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FAQ') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_FAQ')}}",
			faq_ans_en: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ANSWER')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ANSWER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_ANSWER')}}",
				
		}
	});
 </script>
 
 		<script>
	        $(document).ready(function() {
	            $('.summernote').summernote();
				$('#faq_form').each(function () {
					if ($(this).data('validator'))
						$(this).data('validator').settings.ignore = ".note-editor *";
				});
	        });
		</script>
@endsection
@stop