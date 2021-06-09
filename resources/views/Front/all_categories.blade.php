@extends('Front.layouts.default')
	@section('content')
	<style> 
	.catgList
	{
		position:relative;
	}
	
	input.hidden {
		display: none;
	}	
	
	.woof_list_checkbox {
		display: flex;
		flex-wrap: wrap;
	}
	
	.woof_list_checkbox li {
		margin: 0 3px 0 3px !important;
		flex-basis: calc(50% - 6px);
	}
	
	/*.woof_checkbox_label{
	    display: block;
		max-width: 238px;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	} */
	.ktn {
		cursor: pointer;
		/* background: #ff5215; */
		color: #fff;
		border-radius: 50px;
		border: 0;
		display: inline-block;
		font-weight: 400;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		border: 1px solid transparent;
		padding: .375rem .75rem;
		font-size: 1rem;
		line-height: 1.5;
		border-radius: .25rem;
		transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	}
	.woof_checkbox_label input{  margin-right:10px; } 
	#itemSearch {
		*width: 200px;
	}
	#clearSearch {
		position: absolute;
		right: 5px;
		top: 0;
		bottom: 0;
		height: 14px;
		margin: auto;
		font-size: 14px;
		cursor: pointer;
		color: #afafaf;;
		display:none;
	}
	.multi-checkbox {
		display: block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 13px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	.multi-checkbox input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}
	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 18px;
		width: 18px;
		background-color: #ccc;
	}
	.multi-checkbox:hover input ~ .checkmark {
		background-color: #ccc;
	}

	.multi-checkbox input:checked ~ .checkmark {
		background-color: #ff5215;
	}
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}
	.multi-checkbox input:checked ~ .checkmark:after {
		display: block;
	}
	.multi-checkbox .checkmark:after {
		left: 7px;
		top: 3px;
		width: 5px;
		height: 10px;
		border: solid white;
		border-width: 0 3px 3px 0;
		-webkit-transform: rotate(45deg);
		-ms-transform: rotate(45deg);
		transform: rotate(45deg);
	}
	.btn-success:not(:disabled):not(.disabled).active, .btn-success:not(:disabled):not(.disabled):active, .show>.btn-success.dropdown-toggle {
		color: #fff;
		background-color: #ff5215;
		border-color: #ff5215;
	}
	.btn-success:not(:disabled):not(.disabled).active:focus, .btn-success:not(:disabled):not(.disabled):active:focus, .show>.btn-success.dropdown-toggle:focus {
		box-shadow: none !important;
	}
	</style>
	
	@php  
		$uri = Request::segment(2); 
		$page_count_max = $page_max;
		$searched_cuisine = '';
		if(base64_decode(\Request::segment(2))!=''){ 
			$searched_cuisine = base64_decode(\Request::segment(2));
		} 
	@endphp
	<div class="filter-menuoverall">
		
		<div class="col-md-4 col-lg-4 filter-menu">
			<div class="" style="margin-bottom: 15px;border-bottom: 1px solid #ccc;padding-bottom: 10px;"><span>@lang(Session::get('front_lang_file').'.ADMIN_FILTER')</span><button id="close_filter" style="float:right"><i class="fa fa-times"></i></button></div>
			<form class="form-horizontal">
				@if(count($all_categories) > 0 )
					<h4>@lang(Session::get('front_lang_file').'.FRONT_CUISINES')</h4>
					<ul class="woof_list woof_list_checkbox">
						@foreach($all_categories as $all_cate)
							<li>
								<div class="checkbox">
									<label class="multi-checkbox woof_checkbox_label">{{$all_cate->category_name}}
										<input type="checkbox" name="cuisines[]" value="{{$all_cate->cate_id}}" @if($all_cate->cate_id==$searched_cuisine) checked="checked" @endif>
										<span class="checkmark"></span>

								</label>
							</div>
						</li>
						
					@endforeach
				</ul>
				
				<hr />
				@endif
				<div class="form-group"> 
						<div class="col-sm-offset-2 col-sm-10">
						  <button type="button" class="ktn btn-primary" onclick="clear_cuisines();">@lang(Session::get('front_lang_file').'.ADMIN_CLR')</button>
						  <button type="button" class="ktn btn-success" onclick="filter_fun();">@lang(Session::get('front_lang_file').'.ADMIN_SH_REST')</button>
						</div>
				  </div>
			</form>
			
		</div>
	</div>
	<!-- <div class="overlay"></div> -->
	
	<div class="main-sec">
		<div class="section10">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-4 section8-search" style="">
						<div class="btn-group" style="width:100%">
							<input id="itemSearch" type="text" class="form-control" Placeholder="@lang(Session::get('front_lang_file').'.FRONT_SEARCH_RESTAURANT')">
							<span id="clearSearch" class="fa fa-times-circle" onclick="clearItemSearch();"></span>
						</div>
					</div>
					
					<div class="col-lg-8 col-md-8 col-sm-8 section8-filter">
						<span class="button-checkbox"><button type="button" class="btn btn-default" data-color="success" style="padding: 3px 12px 3px 0px;"><i class="state-icon "></i>&nbsp;<i class="fa fa-tag" style="color: #00800a;"></i> @lang(Session::get('front_lang_file').'.FRONT_TOP_OFFER')</button><input type="checkbox" name="top_discount_check" value="2" class="hidden"></span>
						
						<span class="button-checkbox"><button type="button" class="btn btn-default" data-color="success" style="padding: 3px 12px 3px 0px;"><i class="state-icon "></i>&nbsp;<i class="fa fa-truck" style="color: #c51600"></i>@lang(Session::get('front_lang_file').'.FRONT_DELI_TIME')</button><input type="checkbox" name="delivery_time_filter" value="1" class="hidden"></span>
						
						<span class="button-checkbox"><button type="button" class="btn btn-default" data-color="danger" style="padding: 3px 12px 3px 0px;"><i class="state-icon "></i>&nbsp;<i class="fa fa-star" style="color: #ff5215;"></i> @lang(Session::get('front_lang_file').'.FRONT_RATINGS')</button><input type="checkbox" name="rating_filter" value="2" class="hidden"></span>
						@if(count($all_categories) > 0 )
						<button data-toggle="modal" data-target="#about-modal" id="filter_toggle"><i class="fa fa-filter" style="color: #dc3545;"></i> @lang(Session::get('front_lang_file').'.ADMIN_FILTER')</button>
						@endif
					</div>
				</div>
			</div>
		</div>
 
	 
		<div class="section10-inner">
			<div class="container">
				<div class="row catgList">
					<div id="load_data" class="row">
						{{-- load the ajax data here --}}
					</div>
				</div>
			</div>
		</div>
     
		<div class="ajax-loading"><p> @lang(Session::get('front_lang_file').'.LOAD') </p></div>
	</div>
	
@section('script')

	<script>				
		var page = 1; //track user scroll as page number, right now page number is 1
		load_more(page); //initial content load
		var page_count = 1;
		$(window).scroll(function() { //detect page scroll
			if($(window).scrollTop() + $(window).height() >= $(document).height()) { //if user scrolled from top to bottom of the page 
				if(page <= page_count)
				{
					page++; //page number increment
					load_more(page); //load content   
				}   
			}
		});     
		
		function load_more(page){
			if($("input[name='top_discount_check']").is(":checked")){ var top_disc_filter = 1; } else{ var top_disc_filter = 0; }
			if($("input[name='delivery_time_filter']").is(":checked")){ var del_time_filter = 1; } else{ var del_time_filter = 0; }
			if($("input[name='rating_filter']").is(":checked")){ var rate_filter = 1; } else{ var rate_filter = 0; }
			var cusinesID = $("input[name='cuisines[]']:checked").map(function(){
				return $(this).val();
			}).get(); 
			if(cusinesID!='') { var cuisines = cusinesID; } else { var cuisines = ''; } 
			var text = document.getElementById('itemSearch').value;
			
			$.ajax({
				url: '?page=' + page,
				data : {'top_disc_filter' : top_disc_filter,'del_time_filter' :del_time_filter,'rate_filter' : rate_filter,'cuisines' : cuisines,'text':text},
				type: "get",
				datatype: "html",
				beforeSend: function()
				{
					$('.ajax-loading').show();
				}
			}).done(function(data){
				var details = data.split("~`");
				//alert(details[0].length);
				if(details[0].length == 0)
				{
					//$("#load_data").html(details[0]); 
					$('.ajax-loading').show();
					//notify user if nothing to load
					$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
					return;
				}
				$('.ajax-loading').hide();
				$("#load_data").html(details[0]); //add data into #results element
				$('[data-toggle="tooltip"]').tooltip(); 
				page_count = details[1];
				/*if(data.length == 0){
					//console.log(data.length);
					$('.ajax-loading').hide();
					return;
				}
				$('.ajax-loading').hide(); //hide loading animation once data is received
				$("#load_data").append(data); //append data into #results element    
				page_count = '<?php echo $page_count_max; ?>';*/
			}).fail(function(jqXHR, ajaxOptions, thrownError){
				//alert('No response from server');
			});
		}
	</script>

	<script type="text/javascript">

	$(document).ready(function(){
		$('#all-cate').click(function(){
			$('#cate-list').slideToggle(500);
			if($('#all-cate').hasClass('rota')){ 
				$('#all-cate').removeClass('rota');
			}else{
				$('#all-cate').addClass('rota');
			}
		});
	});
	</script>
	<script>
		$(document).ready(function(){
			$('.button-checkbox').each(function () {
				// Settings
				var $widget = $(this),
					$button = $widget.find('button'),
					$checkbox = $widget.find('input:checkbox'),
					color = $button.data('color'),
					settings = {
						on: {
							icon: ''//glyphicon glyphicon-check
						},
						off: {
							icon: ''//glyphicon glyphicon-unchecked
						}
					};

				// Event Handlers
				$button.on('click', function () {
					$checkbox.prop('checked', !$checkbox.is(':checked'));
					$checkbox.triggerHandler('change');
					updateDisplay();
				});
				$checkbox.on('change', function () {
					updateDisplay();
					filter_fun();
				});

				// Actions
				function updateDisplay() {
					var isChecked = $checkbox.is(':checked');

					// Set the button's state
					$button.data('state', (isChecked) ? "on" : "off");

					// Set the button's icon
					$button.find('.state-icon')
						.removeClass()
						.addClass('state-icon ' + settings[$button.data('state')].icon);

					// Update the button's color
					if (isChecked) {
						$button
							.removeClass('btn-default')
							.addClass('btn-' + color + ' active');
					}
					else {
						$button
							.removeClass('btn-' + color + ' active')
							.addClass('btn-default');
					}
					//
				}

				// Initialization
				function init() {
					updateDisplay();
					// Inject the icon if applicable
					if ($button.find('.state-icon').length == 0) {
						$button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
					}
				}
				init();
			});
			
			
			$('#itemSearch').bind('keyup change', function () {
				if($(this).val().length > 0){
					$('#clearSearch').show();
				}
				filter_fun();
			});
		});
		
		function clearItemSearch()
		{
			$('#itemSearch').val('');
			$('#clearSearch').hide();
			filter_fun();
		}
		
		function filter_fun(){
			if($("input[name='top_discount_check']").is(":checked")){ var top_disc_filter = 1; } else{ var top_disc_filter = 0; }
			if($("input[name='delivery_time_filter']").is(":checked")){ var del_time_filter = 1; } else{ var del_time_filter = 0; }
			if($("input[name='rating_filter']").is(":checked")){ var rate_filter = 1; } else{ var rate_filter = 0; }
			var cusinesID = $("input[name='cuisines[]']:checked").map(function(){
				return $(this).val();
			}).get(); 
			if(cusinesID!='') { 
				var cuisines = cusinesID; 
				$('#filter_toggle').css({'background-color': '#b3d237','color':'white'});
			} else { 
				var cuisines = ''; 
				$('#filter_toggle').css({'background-color': 'transparent','color':'#282828'});
			} 
			var text = document.getElementById('itemSearch').value;
			
			$.ajax({
				url: '?page=1',
				data : {'top_disc_filter' : top_disc_filter,'del_time_filter' :del_time_filter,'rate_filter' : rate_filter,'cuisines' : cuisines,'text':text},
				type: "get",
				datatype: "html",
				beforeSend: function()
				{
					$('.ajax-loading').show();
				}
			})
			.done(function(data){
				var details = data.split("~`");
				//alert(details[2]);
				if(details[0].length == 0)
				{
					//console.log(details[0].length);
					$("#load_data").html(details[0]); 
					$('.ajax-loading').show();
					//notify user if nothing to load
					$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
					return;
				}
				$('.ajax-loading').hide();
				$("#load_data").html(details[0]); //add data into #results element
				$('[data-toggle="tooltip"]').tooltip(); 
				page_count = details[1];
			})
			.fail(function(jqXHR, ajaxOptions, thrownError){
				//alert('No response from server');
			});
		}
		
		function clear_cuisines(){
			$("input[name='cuisines[]']").each(function(){
				$(this).prop("checked", false);
				$('#close_filter').show();
			});
			filter_fun();
		}
		if($("input[name='cuisines[]']:checked").length > 0){
			$('#filter_toggle').css({'background-color': '#b3d237','color':'white'});
			$('#close_filter').hide();
		} else{
			$('#filter_toggle').css({'background-color': 'transparent','color':'#282828'});
			$('#close_filter').show();
		}
		/* hide close button if category is selected */
		$("input[name='cuisines[]']").change(function () {
		   if($("input[name='cuisines[]']:checked").length > 0)
		   {
			$('#close_filter').hide();
		   } 
		   else
		   {
			$('#close_filter').show();
		   }
		 });
	</script>
	 @endsection
@stop


