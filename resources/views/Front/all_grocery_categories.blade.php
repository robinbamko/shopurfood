<?php echo "Cartlisting"; exit; ?>
@extends('Front.layouts.default')
@section('content')

@php  $uri = Request::segment(2);
		$page_count_max = $page_max ;
@endphp
<div class="main-sec">
<div class="section10">
	<div class="container">
		<div class="row">
			<div class="col-md-12 section10-list">
				<p id="all-cate">@lang(Session::get('front_lang_file').'.FRONT_ALL_CATEGORIES') <span><i class="fa fa-chevron-down" aria-hidden="true"></i></span></p>
			<ul id="cate-list">
					@if(count($all_categories) > 0)
						<li><a href="{{url('all-grocery-categories')}}" <?php echo ($uri == '') ? 'class="  store-list-active"' : '';?> >@lang(Session::get('front_lang_file').'.FRONT_ALL')</a></li>
							@foreach($all_categories as $categories)
								<li><a href="{{url('all-grocery-categories'.'/'.base64_encode($categories->cate_id))}}" <?php echo (base64_decode($uri) == $categories->cate_id) ? 'class="  store-list-active"' : '';?> >{{$categories->category_name}}</a></li>
							@endforeach
						@else
						<li>@lang(Session::get('front_lang_file').'.FRONT_NO_CATE_FOUND')</li>
					@endif
				</ul>
			</div>
		</div>
		
	</div>
</div>
	<div class="section10-inner">
		<div class="container">
			<div class="row catgList" >
				<div id="load_data">
					{{-- load the ajax data here --}}
				</div>
			</div>
		</div>
	</div>
	
	<div class="ajax-loading"><p>@lang(Session::get('front_lang_file').'.LOAD') </p></div>

	<!--<div class="section10-inner">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				<input type="button" class="btn btn-success" value="Load More" style="width:100%" id="loadmoreBtn" onclick="call_my_load();"/>
				</div>
			</div>
		</div>
	</div>-->
</div>




@section('script')

<script>
	
	var page = 1; //track user scroll as page number, right now page number is 1
	load_more(page); //initial content load
	var page_count = 1;
	//alert(page_count+'\n'+page);
	/*function call_my_load()
	{
		if(page <= page_count)
		{
			page++; //page number increment
			load_more(page); //load content   
			$('#loadmoreBtn').show();
		} 
		else
		{
			$('#loadmoreBtn').hide();
		}
	}*/
	
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
            	//$('#loadmoreBtn').val('@lang(Session::get('front_lang_file').'.LOAD')');
                $('.ajax-loading').show();
			}
		})
        .done(function(data)
        {//alert(data);
            if(data.length == 0){
				console.log(data.length);
				//$('#loadmoreBtn').hide();
                //notify user if nothing to load
               // $('.ajax-loading').html("<p>@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')</p>");
               $('.ajax-loading').hide();
                return;
			}
			$('.ajax-loading').hide(); //hide loading animation once data is received
			//$('#loadmoreBtn').val('Load More');
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
		'url'  : '<?php echo url('all-grocery-categories')?>',
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