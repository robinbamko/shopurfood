@extends('Front.layouts.default')
@section('content')
<style>
	.panel-heading {
		padding: 15px 10px;
		//border-bottom: 1px solid #000;
		background-color: #eee;
	}
	
	.panel.panel-default {
		margin-bottom: 20px;
	}
	
	.panel-heading h4 {
		margin: 0;
	}
	
	.collapse-group {
		padding: 10px;
		width: 100%;
		margin-bottom: 10px;
	}
	
	.panel-title .trigger:before {
		content: '\e082';
		content: '\f106' !important;
		font-family: FontAwesome;
		*font-family: 'Glyphicons Halflings';
		vertical-align: text-bottom;
		float:right;
	}
	
	.panel-title .trigger.collapsed:before {
		content: '\e081';
		content: '\f107' !important;
		font-family: FontAwesome;
		float:right;
	}
	.panel-body
	{
		border-bottom: 1px solid #eee;
		border-left: 1px solid #eee;
		border-right: 1px solid #eee;
		text-align: justify;
		padding: 30px 30px 30px 30px;
	}
	.section9-inner h5 
	{
		text-align:left;
	}
	
</style>

<div class="main-sec">
<div class="section9">                          
	<h1>{!! ucfirst($pagetitle) !!}</h1>
</div>
<div class="section9-inner">
<div class="container">
    <div class="row"> 
		<div class="collapse-group">
			@if(count($faq_details)>0) 
				@php $i =  ($faq_details->currentpage()-1)*$faq_details->perpage()+1 ; $j=1; @endphp 
				@foreach($faq_details as $details)
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="heading{{$i}}">
							<h5 class="panel-title">

								<a role="button" data-toggle="collapse" href="#collapse{{$i}}" aria-controls="collapse{{$i}}" class="trigger @if($j==1) collapsed @endif" aria-expanded="false">
									{{$i}}.&nbsp;{!! ($details->que) !!}
								</a>
								<!--<span><a href="http://192.168.0.65/edison_grocery_v1/cart">Edit Cart </a></span>-->
							</h5>
							
						</div>
						<div id="collapse{{$i}}" class="panel-collapse collapse @if($j==1) show @endif " role="tabpanel" aria-labelledby="heading{{$i}}" style="">
							<div class="panel-body">
								{!! ucfirst($details->ans) !!}
							</div>
						</div>
					</div>
				@php $i++; $j++; @endphp              
				@endforeach
			@else
				<p>@lang(Session::get('front_lang_file').'.ADMIN_NO_DETAILS')</p>
			@endif
		</div>
	</div>
	@if(count($faq_details)>0)
		{!! $faq_details->render() !!}
	@endif
</div>
</div>
</div>
@stop

