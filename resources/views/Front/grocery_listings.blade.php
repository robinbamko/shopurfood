	
@extends('Front.layouts.default')
	@section('content')

	
	<style>
		#loading {
		   width: 100%;
		   height: 100%;
		   top: 0;
		   left: 0;
		   position: fixed;
		   display: block;
		   text-align:center;
		   background-color: #fff;
		   z-index: 99;
		   
		}

		#loading div {
		  width: 100%;
			text-align: center;
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
		}
		
		
	</style>
	
	<!--<div id="loading">
		  <div><img id="loading-image" src="public/front/images/loading.gif" alt="Loading..." /></div>
	 </div>-->
	  <div class="main-sec">
	 <div class="banner-slider">	
			<section class="regular slider your-carousel">
			@if(count($banner_details) > 0)
				@foreach($banner_details as $banner)
				@php $filename = public_path('images/banner/').$banner->banner_image; 
					 $path = url('').'/public/images/noimage/'.$no_list_banner;
				@endphp
				@if(file_exists($filename) && $banner->banner_image != '')
				 @php $path = url('').'/public/images/banner/'.$banner->banner_image; @endphp
				@endif
				<div style="background: url('{{$path}}'); background-size: cover; background-position: center;width:100%;height:100%;">	
					<div class="container">
						<div class="row">
							<div class="slider-caption">
								<h5>{{$banner->image_title}}</h5>
								<p>{{$banner->image_text}}</p>
							</div>
						</div>
					</div>
				</div>
				
				@endforeach
			@else
			<div style="background: url('{{url('public/images/noimage').'/'.$no_list_banner}}'); background-size: cover; background-position: center;width:100%;height:100%;">	
					<div class="container">
						<div class="slider-caption">
								
							</div>
					</div>
				</div>
			@endif
			</section>
		
		</div>
	  
	  {{-- <div class="featured-slider">
		<div class="container">
			<div class="col-md-12 featured-heading">
				<h4>@lang(Session::get('front_lang_file').'.FRONT_FEATURED_RESTAURANTS')</h4>
			</div>
			<section class="regular1 slider">
				<div>
				  <img src="{{url('')}}/public/front/images/logo-1.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-2.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-3.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-4.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-5.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-1.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-2.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-3.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-4.png">
				</div>
				<div>
				  <img src="{{url('')}}/public/front/images/logo-5.png">
				</div>				
			  </section>
		</div>
	  </div> --}}
	  
	  
	  <div class="all-slider">
		<div class="container">
			<div class="row">
				<div class="col-md-12 all-heading store-all-heading">
					<div class="row">
						<div class="col-md-10">
							<h4>@lang(Session::get('front_lang_file').'.FRONT_ALL_STORES')</h4>

						</div>
						<div class="col-md-2">
							@if(count($all_stores) > 5)
							<a href="{{url('all-grocery-categories')}}"><h5>@lang(Session::get('front_lang_file').'.FRONT_SEE_ALL')<span>&gt;</span></h5> </a>
							@endif
						</div>
					</div>
				</div>
				<section class="regular2 slider your-carousel">
					@if(count($all_stores) > 0)
					@foreach($all_stores as $store)
					 @php $filename = public_path('images/store/').$store->st_logo; @endphp
					<div>
						<a href="{{url('store').'/'.str_slug($store->st_name,'-').'/'.base64_encode($store->id)}}" title="{{ucfirst($store->st_name)}}">
						@if($store->st_logo != '' && file_exists($filename))
					  	<img src="{{url('')}}/public/images/store/{{$store->st_logo}}">
					  	@else
					  	<img src="{{url('')}}/public/images/noimage/{{$no_shop_logo}}">	
					 	 @endif
					 	</a>
					</div>
					@endforeach
					@else
					<p>@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')</p>
					@endif				
				  </section>
				</div>
		</div>
	  </div> 
	  {{-- initialize max page count --}}
	  @php $page_count_max = 1 ; @endphp
	  @if(count($all_categories) > 0)
	 	 @php $page_count_max = $all_categories->lastPage() ; @endphp
	  @endif
	  <div id="load_data"></div>
	  <div class="ajax-loading"><!-- <p> @lang(Session::get('front_lang_file').'.LOAD') </p> --><img src="{{url('')}}/public/front/images/load.gif"></div>
	  <div class="section7" >
		<div class="container">
			@if(count($all_stores) > 0)
			<div class="row">
				<div class="col-md-12">
					<a href="{{url('all-grocery-categories')}}">@lang(Session::get('front_lang_file').'.FRONT_ALL_CATEGORIES')<span>&gt;&gt;</span></a>
				</div>
			</div>
			@endif
		</div>
	  </div>

	</div>

@section('script')

	
	 <script src="{{url('')}}/public/front/js/slick.js" type="text/javascript" charset="utf-8"></script>
	 
	 
	 <script type="text/javascript">
		$(document).on('ready', function() {
			$(".regular").slick({
			dots: true,
			infinite: true,
			autoplay:true,
			slidesToShow: 1,
			slidesToScroll: 1
		  });
		  
		  $(".regular1").slick({
			dots: true,
			infinite: true,
			autoplay:true,
			slidesToShow: 5,
			slidesToScroll: 3,
			responsive: [
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: 3,
				slidesToScroll: 3,
				infinite: true,
				dots: true
			  }
			},
			{
			  breakpoint: 576,
			  settings: {
				slidesToShow: 2,
				slidesToScroll: 3,
				infinite: true,
				dots: true
			  }
			},
			
			]
		  });
		  
		  $(".regular2").slick({
			dots: true,
			infinite: true,
			autoplay:true,
			slidesToShow: 6,
			slidesToScroll: 1,
			responsive: [
			{
			  breakpoint: 992,
			  settings: {
				slidesToShow: 4,
				slidesToScroll: 1,
				infinite: true,
				dots: true
			  }
			},
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: 3,
				slidesToScroll: 1,
				infinite: true,
				dots: true
			  }
			},
			{
			  breakpoint: 576,
			  settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				infinite: true,
				dots: true
			  }
			},
			
			]
		  });
		  
		  $(".regular3").not('.slick-initialized').slick({
			dots: true,
			infinite: true,
			slidesToShow: 3,
			slidesToScroll: 3,
			responsive: [
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: 2,
				slidesToScroll: 2,
				infinite: true,
				dots: true
			  }
			},
			{
			  breakpoint: 576,
			  settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				infinite: true,
				dots: true
			  }
			},			
			]
		  });
		  
		  
		});
	 </script>

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
               //$('#allcategoryDiv').hide();
                //notify user if nothing to load
               // $('.ajax-loading').html("<p>@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')</p>");
               $('.ajax-loading').hide();
                return;
            }
            $('.ajax-loading').hide(); //hide loading animation once data is received
            $("#load_data").append(data); //append data into #results element  
            page_count = '<?php echo $page_count_max; ?>';  
            
            callme();
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
 }

 function callme()
 {
 	
 	$(".regular3").not('.slick-initialized').slick({
			dots: true,
			infinite: true,
			slidesToShow: 3,
			slidesToScroll: 3,
			responsive: [
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: 2,
				slidesToScroll: 2,
				infinite: true,
				dots: true
			  }
			},
			{
			  breakpoint: 576,
			  settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				infinite: true,
				dots: true
			  }
			},			
			]
		  })
 }
</script>


		<script>
			 $(window).load(function() {
			 $('#loading').hide();
		  });
		</script>


	 @endsection
@stop