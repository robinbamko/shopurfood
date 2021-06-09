
@extends('Front.layouts.default')
  @section('content')
    @if(count($result)>0)
      @foreach($result as $cms)
         {{--@if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en') --}}
               {{--@php--}}
                  {{--$cp_title        = 'page_title_en';--}}
                  {{--$cp_description  = 'description_en';                --}}
                {{--@endphp--}}
           {{--@else--}}
                {{--@php --}}
                  {{--$cp_title        = 'page_title_'.Session::get('front_lang_code');--}}
                 {{--$cp_description   = 'description_'.Session::get('front_lang_code'); --}}
               {{--@endphp    --}}
           {{--@endif  --}}
			<div class="main-sec">
                <div class="section9">                          
                     <h1>{!! $cms->cp_title !!}</h1>
                </div>   
                <div class="section9-inner">
                <div class="container">
                  <div class="row">        
                    <div class="col-md-12">                                
                      <p>{!! stripslashes($cms->cp_description) !!}</p>
                    </div> 
                  </div>
                </div>
                </div>   
			</div>
          @endforeach
    @endif

@section('script')
  
@endsection
@stop

