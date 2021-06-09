
	@extends('Front.layouts.default')
	@section('content')

 	<?php echo "Content"; exit; ?>
	@if(empty($shop_details) === false)	
	{{ Form::hidden('st_id',$shop_details->id,['id' => 'store_id'])}}
	@if($shop_details->store_closed == "Avail")
	<div class="banner-slider">	
		@php $banner_image = explode('/**/',$shop_details->st_banner,-1); @endphp
			
			<section class="regular slider your-carousel" >
				@php $path = url('').'/public/images/noimage/'.$no_reStoreDetailbanner; @endphp
				@if(count($banner_image) > 0) 
				@for($i=0;$i<count($banner_image); $i++)
				@if(isset($banner_image[$i]))
				@php $filename = public_path('images/restaurant/banner/').$banner_image[$i]; 
				@endphp
				@if(file_exists($filename))
				 @php $path = url('').'/public/images/restaurant/banner/'.$banner_image[$i]; @endphp
				@endif
				@endif
				<div style="background: url('{{$path}}'); background-size: cover; background-position: center;width:100%;
				height:100%;">	
					<div class="container">
						<div class="row">
							<div class="slider-caption">
								
							</div>
						</div>
					</div>
				</div>
				
				@endfor
				@else
					<div style="background: url('{{$path}}'); background-size: cover; background-position: center;width:100%;
				height:100%;">	
					<div class="container">
						<div class="row">
							<div class="slider-caption">
								
							</div>
						</div>
					</div>
				</div>
				@endif
			</section>
		
	</div>
	@else
		<div class="closed-bg" style="background:url({{url('/public/front/images/wood.jpg')}}); background-size: cover; height:300px; background-position:center;">		
		</div>
	@endif
	
	<div class="section8">
		<div class="container">
			<div class="row">
				<div class="col-md-6 section8-heading">
					<p>{{ucfirst($shop_details->st_name)}}</p>
					<a href="#" data-toggle="modal" data-target="#about-modal">@lang(Session::get('front_lang_file').'.ABOUT')</a>
				</div>
				<div class="col-md-6 section8-search">
					<input type="text" Placeholder="Search Item" id="itemSearch">
					<i class="fa fa-times" style="display:none" id="clearSearch"><a href="javascript:clearItemSearch();">Clear</a></i>
				</div>
			</div>
		</div>
	</div> 
	<div class="container">
		<div class="row">
			<div id="wrapper">				
					
				
				<!-- Sidebar -->
				<div id="sidebar-wrapper">				
					{{-- <button>@lang(Session::get('front_lang_file').'.FRONT_ORDER_CATEGORY')</button> --}}
					<ul class="sidebar-nav nav nav-tabs" id="sidebar-nav">	
						
						<li class="sidebar-brand sidebar-head">							
								@lang(Session::get('front_lang_file').'.FRONT_ORDER_CATEGORY')							
						</li>
						@php $mainCatName=''; $maincatId=''; @endphp
						@if(count($get_category_details) > 0) {{-- Main category --}}
						@php $mainCatCount = 0 @endphp
						@foreach($get_category_details as $main_cate)
							@if($mainCatCount==0)
								@php $maincatId = $main_cate->pro_mc_id; $mainCatName = ucfirst($main_cate->mc_name); @endphp 
							@endif
							<li>
								<a href="javascript:;" id="mainCatSelId_{{$main_cate->pro_mc_id}}" onClick = "set_main_cate({{$main_cate->pro_mc_id}},'{{$main_cate->mc_name}}');" class="maincatA  @if($mainCatCount==0) active @endif">{{ucfirst($main_cate->mc_name)}}</a>
									@php $get_sub_cate_details = get_sub_categories($main_cate->pro_mc_id,$shop_details->id,1); @endphp
									@if(count($get_sub_cate_details) > 0) {{-- Sub category --}}
										<i class="fa fa-angle-double-right"></i>
										<ul class="sub-menu">
											@foreach($get_sub_cate_details as $sub) 
												@if($sub->pro_main_id == $main_cate->pro_mc_id)
												<li><a href="javascript:;" id="subCatSelId_{{$sub->pro_main_id}}_{{$sub->pro_sc_id}}" onClick = "set_sub_cate({{$sub->pro_main_id}},{{$sub->pro_sc_id}},'{{$main_cate->mc_name}}','{{ucfirst($sub->sc_name)}}');" class="subcatA">{{ucfirst($sub->sc_name)}}</a></li>
												@endif	
											@endforeach								
										</ul> 
									@endif
									
							</li>
							@php $mainCatCount++ @endphp
						@endforeach
					@else
						<li>
							<a href="#seafood">@lang(Session::get('front_lang_file').'.FRONT_NO_CATE_FOUND')</a>
						</li>
					@endif
					{{ Form::text('cate_id',$maincatId,['id' =>'category_id'])}}
					{{ Form::text('sub_cate_id','',['id' =>'sub_category_id'])}}
					</ul>
				</div>
				<!-- /#sidebar-wrapper -->

				<!-- Page Content -->
				<div class="tab-content">
				<div class="tab-pane active" id="seafood">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12 item-head">
								<h5 id="cate_name">{{$mainCatName}}</h5>
								{{ Form::select('sort',['all' => 'Sort by','new' => 'By Newest','a_z' => 'By A-Z','z_a' => 'By Z-A','low_high' => 'By Low-High','high_low' => 'By High-Low'],'',['id' =>'sort_by','onChange' => 'load_more(1);'])}}
							</div>
							
							<div class="col-md-12 item-list">
								<div class="row" id="load_data"></div>
							</div>
							<div>
								<span class="ajax-loading"/> Loading...</span>
							</div>
						</div>
					</div>
				</div>
				
				
						
		<!-- About Modal Popup -->
		<div id="about-modal" class="modal fade about-modal-rest" role="dialog">
			
			
			  
		  <div class="modal-dialog">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			<!-- Modal content-->
			<div class="modal-content">			 
			  <div class="modal-body">
				<div class="about-modal-img">
					@php $path = url('').'/public/images/noimage/'.$no_shop_logo; 
						 $filename = public_path('images/restaurant/').$shop_details->st_logo; 
					 @endphp
					@if(file_exists($filename) && $shop_details->st_logo != '')
					@php $path = url('').'/public/images/restaurant/'.$shop_details->st_logo;
					@endphp
					@endif
					<img src="{{$path}}" alt="{{$shop_details->st_name}}">				
				</div>
				<div class="about-tab">
				<ul class="tabs">
					<li class="tab-link current" data-tab="tab-1">
						@lang(Session::get('front_lang_file').'.FRONT_INFO')
					</li>
					<li class="tab-link" data-tab="tab-2">
						@lang(Session::get('front_lang_file').'.FRONT_WKING_HRS')
					</li>
					<li class="tab-link" data-tab="tab-3">
						@lang(Session::get('front_lang_file').'.FRONT_REVIEW')
					</li>					
				</ul>
				<div id="tab-1" class="tab-content current">
					<p>
						{!!ucfirst($shop_details->st_desc)!!}
					</p><br>
					<table>
						<tr>
						<td>
						@lang(Session::get('front_lang_file').'.FRONT_MIN_ORDER') : </td>
						<td>
						@if($shop_details->st_minimum_order != '') {{ $shop_details->st_currency}} &nbsp; {{ $shop_details->st_minimum_order}} @else {{"-"}} @endif
					</td>
					</tr>
					<tr>
						<td>
						@lang(Session::get('front_lang_file').'.FRONT_PRE_ORDER') : </td>
						<td>
						{{ ($shop_details->st_pre_order != 'yes') ? __(Session::get('front_lang_file').'.FRONT_YES') : __(Session::get('front_lang_file').'.FRONT_NO')  }}
					</td>
					</tr>
					<tr>
						<td>
						@lang(Session::get('front_lang_file').'.FRONT_DELI_TIME') : </td>
						<td>
						@if($shop_details->st_delivery_time != '') {{ $shop_details->st_delivery_time}}&nbsp; {{ $shop_details->st_delivery_duration }} @else {{ "-"}} @endif
						</td>
					</tr>
					<tr>
						<td>
						@lang(Session::get('front_lang_file').'.FRONT_RATINGS') : </td>
						<td>
						@for($i=1;$i<=$shop_details->st_rating;$i++)
						 <i class="fa fa-star checked"></i>
						@endfor
						</td>
					</tr>
					<tr>
						<td>
						@lang(Session::get('front_lang_file').'.FRONT_CANCEL_POLICY') : </td>
						<td>
						{!! ($shop_details->mer_cancel_policy != '') ? ucfirst($shop_details->mer_cancel_policy)  : "-" !!}
					</td>
					</tr>
					<tr>
						<td>
						@lang(Session::get('front_lang_file').'.FRONT_REFUND') : </td>
						<td>
						{{ ($shop_details->refund_status != '') ? ucfirst($shop_details->refund_status)  : "-" }}
						</td>
					</tr>
					</table>
				</div>
				<div id="tab-2" class="tab-content">
					<table>
					 @if(count($get_wk_hrs) > 0)
					 	@for($i = 1;$i<=7;$i++)
					 		
							<tr>
							<td>
							@lang(Session::get('front_lang_file').'.ADMIN_DAY'.$i) </td>
					 		<td>{{$get_wk_hrs[$i-1]->wk_start_time}}&nbsp;{{"-"}}&nbsp;{{$get_wk_hrs[$i-1]->wk_end_time}}</td>
							</tr>
							
					 		
					 	@endfor
					 @else
					 	@lang(Session::get('front_lang_file').'.FRONT_NO_DETAILS_FOUND')
					 @endif
					 </table>
				</div>
				<div id="tab-3" class="tab-content">
					 @if(count($all_reviews) > 0)
					 	@foreach($all_reviews as $review)
					 	<div class="row">
					 		<div class="col-md-3">
					 			@php $path = url('').'/public/images/noimage/user.png';
					 					@endphp
					 			@if($review->cus_image != '')
					 				@php  $filename = public_path('images/customer/').$review->cus_image; 
					 				@endphp
					 				@if(file_exists($filename))
					 					@php $path = url('').'/public/images/customer/'.$review->cus_image;
					 					@endphp
					 				@endif
					 			@endif
					 			<img src="{{$path}}" width="50px" height= "50px">
					 		</div>
					 		<div class="col-md-9">
					 			<p>{{ucfirst($review->review_comments)}}</p>
					 			@if($review->review_rating != '')
					 				@for($i=1;$i<=$review->review_rating;$i++)
					 					<i class="fa fa-star checked"></i>
					 				@endfor
					 			@endif
					 			<h6>{{ucfirst($review->cus_fname)}}</h6>

					 		</div>
					 	</div>
					 	@endforeach
					 @else
					 	@lang(Session::get('front_lang_file').'.FRONT_NO_DETAILS_FOUND')
					 @endif
					 {{ $all_reviews->render()}}
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
	
	
	@endif
	
	
	
	@section('script')
	<link rel="stylesheet" href="{{url('')}}/public/front/css/auto-complete.css">
	<script src="{{url('')}}/public/front/js/auto-complete.js"></script>
	
	<!--<script src="{{url('')}}/public/front/js/bootstrap-typeahead.js"></script>-->
	<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    $('#sidebar-nav a').click(function (e) {
  		e.preventDefault()
  		$(this).tab('show');
	})	
	
	$("#sidebar-wrapper button").click(function(){
		$(".sidebar-nav").toggle();
	});
	
	
	if($(window).width() <= 767){

	$('.sub-menu').hide();

	$("#sidebar-nav li:has(ul)").click(function(){

	$("ul",this).toggle('');
	});
	}
    </script>
	
	<!-- ABOUT MODAL POPUP -->
	
	<script>
	$(document).ready(function(){
	
		$('#about-modal ul.tabs li').click(function(){
			var tab_id = $(this).attr('data-tab');

			$('#about-modal ul.tabs li').removeClass('current');
			$('#about-modal .tab-content').removeClass('current');

			$(this).addClass('current');
			$("#"+tab_id).addClass('current');
		});
		/*AUTO COMPLETE*/
		 //autocomplete_fun();
		/*$("#itemSearch").autocomplete({
		  source: function(request, response) {
			$.getJSON("{{url('')}}/getItemName", { ca_id: ca_id }, 
					  response);
		  },
		  minLength: 2,
		  select: function(event, ui){
			//action
			
		  }
		});*/
		/*$( "#itemSearch" ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "search.php",
          dataType: "jsonp",
          data: {
            term: request.term
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        log( "Selected: " + ui.item.value + " aka " + ui.item.id );
      }
    } );*/
	//$('#itemSearch').keyup(function(){
	$('#itemSearch').bind('keyup change', function () {
		//alert('s');
		//load_more(1);
		var ca_id = document.getElementById('category_id').value;
		var sub_ca_id = document.getElementById('sub_category_id').value;
		var st_id = document.getElementById('store_id').value;
		var sortby = document.getElementById('sort_by').value;
		var text = document.getElementById('itemSearch').value;
		//alert(text);
		console.log(ca_id+'\n'+sub_ca_id+'\n'+text);
		autoCompleteFun(ca_id,st_id,sortby,sub_ca_id,text)
		/*$.ajax({
			url: '?page=1',
			data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'text':text},
			type: "get",
			datatype: "html",
			beforeSend: function()
			{
				
				$('.ajax-loading').show();
			}
		})
		.done(function(data){
			var details = data.split("~`");
			if(details[0].length == 0)
			{
				console.log(details[0].length);
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
			alert('No response from server');
		});*/
	});
		/*$('#itemSearch').typeahead({
			ajax: '{{url('')}}/getItemName?ca_id='+ca_id+'&st_id='+st_id+'&sub_ca_id='+sub_ca_id,
			onSelect: displayResult
		});*/
		/*EOF AUTO COMPLETE*/
	});
	function autoCompleteFun(ca_id,st_id,sortby,sub_ca_id,term)
	{
		var demo1 = new autoComplete({
            selector: '#itemSearch',
            minChars: 1,
            source: function(term, suggest){
                term = term.toLowerCase();
				$.getJSON('<?php echo url('').'/getItemName?';?>'+'ca_id='+ca_id+'&st_id='+st_id+'&sortby='+sortby+'&sub_ca_id='+sub_ca_id, { query: term }, function(data){ 
					suggest(data); 
				});
            },
			renderItem: function (item, search){
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&amp;');
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
               // return '<div class="autocomplete-suggestion auto_search_class" data-itemname="'+item[0]+'" data-val="'+search+'"><img src="'+item[1]+'" width="60>'+item[0].replace(re, "<b>$1</b>")+'</div>';
			    return '<div class="autocomplete-suggestion" data-itemname="'+item[0]+'" >'+item[0].replace(re, "<b>$1</b>")+'</div>';
            },
            onSelect: function(e, term, item){
                console.log('Item "'+item.getAttribute('data-langname')+' ('+item.getAttribute('data-lang')+')" selected by '+(e.type == 'keydown' ? 'pressing enter' : 'mouse click')+'.');
                document.getElementById('itemSearch').value = item.getAttribute('data-itemname');
				$('#clearSearch').show();
				load_more(1);
            }
        });
	}
	/*function autocomplete_fun(){
		alert('s');
		var demo1 = new autoComplete({
			selector: '#itemSearch',
			minChars: 1,
			source: function(term, suggest){
				term = term.toLowerCase();
				var ca_id = document.getElementById('category_id').value;
				var sub_ca_id = document.getElementById('sub_category_id').value;
				var st_id = document.getElementById('store_id').value;
				$.getJSON('{{url('')}}/getItemName?ca_id='+ca_id+'&st_id='+st_id+'&sub_ca_id='+sub_ca_id, { query: term }, function(data){ 
					suggest(data); 
				});
			},
			renderItem: function (item, search){
				search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&amp;');
				var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
				return '<div class="autocomplete-suggestion" data-langname="'+item[0]+'" data-lang="'+item[1]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
			},
			onSelect: function(e, term, item){
				console.log('Item "'+item.getAttribute('data-langname')+' ('+item.getAttribute('data-lang')+')" selected by '+(e.type == 'keydown' ? 'pressing enter' : 'mouse click')+'.');
				document.getElementById('food_type_'+menu_id).value = item.getAttribute('data-langname');
				document.getElementById('food_type_ph_'+menu_id).value=item.getAttribute('data-lang');
			}
		});
	}*/
	function displayResult(item) {
		//item.value
		$('#clearSearch').show();
		load_more(1);
	}
	function clearItemSearch()
	{
		$('#itemSearch').val('');
		$('#clearSearch').hide();
		load_more(1);
	}
	</script>
	
	<!-- ADD/MINUS PLUGIN -->
	<script>
		
		$('.quantity').bind('keyup blur',function(){ 
			var node = $(this);
			node.val(node.val().replace(/[^0-9]/g,'') ); 
			});
	 	function plus(i,max){
			var countEl = document.getElementById("count"+i).value;
			if(countEl < max )
			{
			countEl++;
			}
			$('#count'+i).val(countEl);
		}
		function minus(i){
			var countEl = document.getElementById("count"+i).value;
			if (countEl > 1) { 
			countEl--;
			$('#count'+i).val(countEl);
		  }  
		}

		function chk_qty(i)
			{ 	
				var max = document.getElementById("max_qty"+i).value;
				var countEl = document.getElementById("count"+i).value;
				if(countEl == 0 || countEl == '')
				{	
					$('#count'+i).css({'border' : '1px solid red'});
					return false;
				}
				else if(parseInt(countEl) > parseInt(max))
				{
					$('#count'+i).css({'border' : '1px solid red'});
					$('#err'+i).html("@lang(Session::get('front_lang_file').'.FRONT_EXCEED_LIMIT')");
					$('#err'+i).css({'color' : 'red'});
					return false;
				}
			}
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
function load_more(page1){
	page = page1;

	var ca_id = document.getElementById('category_id').value;
	var sub_ca_id = document.getElementById('sub_category_id').value;
	var st_id = document.getElementById('store_id').value;
	var sortby = document.getElementById('sort_by').value;
	var text = document.getElementById('itemSearch').value;
  $.ajax(
        {
            url: '?page=' + page,
            data : {'mc_id' : ca_id,'st_id' :st_id,'sortby' : sortby,'sc_id' : sub_ca_id,'text':text},
            type: "get",
            datatype: "html",
            beforeSend: function()
            {
            	
                $('.ajax-loading').show();
            }
        })
        .done(function(data)
        {
            
            $('.ajax-loading').hide(); //hide loading animation once data is received
            var details = data.split("~`");
            if(details[0].length == 0)
			{
				console.log(details[0].length);
				$('.ajax-loading').show();
				//notify user if nothing to load
				$('.ajax-loading').html("@lang(Session::get('front_lang_file').'.FRONT_NO_RECORDS')");
				return;
			}
            if(page == 1)
            {
            	$("#load_data").html(details[0]); //add data into #results element
            }    
            else
            {
            	$("#load_data").append(details[0]);	//append data into #results element
            }
            $('[data-toggle="tooltip"]').tooltip(); 
			page_count = details[1];
			
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
 }	
 	/** clear quantity **/
 	function clear_data(i)
		{
			$('#count'+i).val('1');
			$('#count'+i).css({'border' : '1px solid #eee'});
			$('#err'+i).html('');
		}
	/** set main category **/
	function set_main_cate(mc_id,mc_name)
	{
		$('#category_id').val(mc_id);
		$('#sub_category_id').val('');
		$('#cate_name').html(mc_name);
		$('.maincatA').removeClass('active');
		$('#mainCatSelId_'+mc_id).addClass('active');
		load_more(1);   //load items basec on main category
		//autocomplete_fun();
	}

	function set_sub_cate(mc_id,sc_id,mc_name,sc_name)
	{	
		$('#category_id').val(mc_id);
		$('#cate_name').html(mc_name+' '+'<i class="fa fa-angle-double-right"></i>'+' '+sc_name);
		$('#sub_category_id').val(sc_id);
		$('.maincatA').removeClass('active');
		$('.subcatA').removeClass('active');
		$('#mainCatSelId_'+mc_id).addClass('active');
		$('#subCatSelId_'+mc_id+'_'+sc_id).addClass('active');
		load_more(1);   //load items basec on main category
		//autocomplete_fun();
	}

	
	</script>


	<script>
		$(".regular").slick({
			dots: true,
			infinite: true,
			autoplay:true,
			slidesToShow: 1,
			slidesToScroll: 1
		  });
	</script>
					
	
@endsection
@stop
	
	
	
    