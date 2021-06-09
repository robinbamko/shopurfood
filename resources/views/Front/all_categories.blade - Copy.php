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
	
	.woof_checkbox_label{
	    display: block;
		max-width: 238px;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
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
	</style>
	
	@php  $uri = Request::segment(2); 
		  $page_count_max = $page_max ;
	@endphp
	
	<div class="col-md-4 col-lg-4 filter-menu">
		<div class="" style="margin-bottom: 15px;border-bottom: 1px solid #ccc;padding-bottom: 10px;"><button><i class="fa fa-times"></i></button></div>
		<form class="form-horizontal">
		@if(count($all_categories) > 0 )
			<h4>Cuisines</h4>
			<ul class="woof_list woof_list_checkbox">
				@foreach($all_categories as $all_cate)
					<li><div class="checkbox"><label class="woof_checkbox_label"><input name="cuisines[]" type="checkbox" value="{{$all_cate->cate_id}}">{{$all_cate->category_name}}</label></div></li>
				@endforeach
			</ul>
			
			<hr />
			<div class="form-group"> 
				<div class="col-sm-offset-2 col-sm-10">
				  <button type="button" class="ktn btn-primary" onclick="clear_cuisines();">Clear</button>
				  <button type="button" class="ktn btn-success" onclick="show_restaurant();">Show Restaurants</button>
				</div>
		  </div>
		@endif
		</form>
		
	</div>
	
	<div class="main-sec">

		<div class="section10">
		<div class="container">
			<div class="row">
				
				
				
				
				<div class="col-lg-6 col-md-6 col-sm-6 section8-search" style="">
					<input type="text" placeholder="Search Restaurants" id="itemSearch">
					<i class="fa fa-times" style="" id="clearSearch"><a href="javascript:clearItemSearch();">Clear</a></i>					
				</div>
				
				<div class="col-lg-6 col-md-6 col-sm-6 section8-filter">

					
					<span class="button-checkbox"><button type="button" class="btn btn-default" data-color="success" style="padding: 3px 12px 3px 0px;"><i class="state-icon "></i>&nbsp;<i class="fa fa-tag" style="color: #00800a;"></i> Top Offers</button><input type="checkbox" name="top_discount_check" value="2" class="hidden"></span>
					
					<span class="button-checkbox"><button type="button" class="btn btn-default" data-color="success" style="padding: 3px 12px 3px 0px;"><i class="state-icon "></i>&nbsp;<i class="fa fa-truck" style="color: #c51600"></i>Delivery Time</button><input type="checkbox" name="food_type_filter[]" value="1" class="hidden"></span>
					
					<span class="button-checkbox"><button type="button" class="btn btn-default" data-color="danger" style="padding: 3px 12px 3px 0px;"><i class="state-icon "></i>&nbsp;<i class="fa fa-star" style="color: #ff5215;"></i>  &nbsp; Ratings</button><input type="checkbox" name="food_type_filter[]" value="2" class="hidden"></span>
					@if(count($all_categories) > 0 )
					<button data-toggle="modal" data-target="#about-modal" id="filter_toggle"><i class="fa fa-filter" style="color: #dc3545;"></i>Filter</button>
					@endif
					<!--<div class="rest-about-popup"><a href="#" data-toggle="modal" data-target="#about-modal">About</a></div>-->
				</div>
				</div>
		</div>
	</div> 
	 
	 
	<div class="section10-inner">
		<div class="container">
			<?php /*
			<div class="row">
				<div class="col-md-12 section10-inn-heading">

<!-- <div>
	<input id="autocomplete" onchange="res_page_redirect()" title="type &quot;a&quot;">
</div> -->
						
		


						

			<!-- 			<div class="autocomplete"  style="width:300px;">
    <input id="myInput" type="text" name="myCountry" placeholder="Country">
  </div> -->
					<h2>@lang(Session::get('front_lang_file').'.FRONT_FAMOUSE_HOTEL')</h2>
					<p><img src="{{url('')}}/public/front/images/location-icon.png">{{Session::get('search_location')}}<a href=""data-toggle="modal" data-target="#centralModalLGInfoDemo">@lang(Session::get('front_lang_file').'.FRONT_CHANGE')</a></p>
					
					<hr>
				</div>
			</div>
			*/ ?>
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
    	//alert(page);
         if(page <= page_count)
    	{
	        page++; //page number increment
	        load_more(page); //load content   
    	}   
    }
});     
function load_more(page){
  $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            beforeSend: function()
            {
            	
                $('.ajax-loading').show();
            }
        })
        .done(function(data)
        {
            if(data.length == 0){
            console.log(data.length);
               
                //notify user if nothing to load
               // $('.ajax-loading').html("<p>@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')</p>");
               $('.ajax-loading').hide();
                return;
            }
            $('.ajax-loading').hide(); //hide loading animation once data is received
            $("#load_data").append(data); //append data into #results element    
            page_count = '<?php echo $page_count_max; ?>';
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              //alert('No response from server');
        });
 }
	</script>
	
	<script>
	/*function list_restaurant(id)
	{
		$.ajax({
			'data' : 'id='+ id,
			'type' : 'get',
			'url'  : '<?php echo url('all-categories')?>',
			success:function(data)
			{
				$("#load_data").append(data); //append data into #results element   
			}

		});
	}*/
	</script>

<script type="text/javascript">
	
$(document).ready(function(){

	$('#all-cate').click(function(){
		$('#cate-list').slideToggle(500);
		if($('#all-cate').hasClass('rota')){ $('#all-cate').removeClass('rota')}
			else{$('#all-cate').addClass('rota')}
		//$(this).addClass('rota');

	});
});


</script>




	 @endsection
@stop


