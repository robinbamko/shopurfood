@extends('Front.layouts.default')
  @section('content')
    <style type="text/css">
    .row .panel-heading{
      margin-bottom: 10px;
    }
  </style> 

		<div class="profile-sidebar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 profile-sidebar-sec">
						<!-- Sidebar -->
						@include('Front.includes.profile_sidebar')
						<!-- Sidebar -->
					</div>
				</div>
			</div>
		</div>
                  
            
          <div class="section9-inner">
              <div class="container userContainer">
                <div class="row"> 
					<!--<div class="col-lg-12">
						<h5 class="sidebar-head">             
								@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')              
						</h5>
					</div>-->	
				<div class="userContainer-bg row">					
                  
                  <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div"> 
                    <div class="row panel-heading">
                      
                      <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <div class="table-responsive">
                          <table class="table table-hover ">
                            <thead>
                              <tr>
                                <th scope="col">@lang(Session::get('front_lang_file').'.FRONT_PRODUCT_OR_ITEM_NAMES')</th>
                                <th scope="col">@lang(Session::get('front_lang_file').'.FRONT_UNIT_PRICE')</th>
                                <th scope="col">@lang(Session::get('front_lang_file').'.FRONT_STOCK')</th>
                                <th scope="col">@lang(Session::get('front_lang_file').'.FRONT_ACTION')</th>
                              </tr>
                            </thead>
                            <tbody>
                              @if(count($wishlist_detail) > 0)
                                @foreach($wishlist_detail as $wish_val)
                                <tr>
                                  <td>{{ substr($wish_val->pro_item_name,0,20) }} @if(strlen($wish_val->pro_item_name) > 20) ... @endif</td>
                                  <td>{{$wish_val->pro_currency}}&nbsp;@if($wish_val->pro_has_discount=='yes') {{$wish_val->pro_discount_price}} @else {{ $wish_val->pro_original_price }} @endif </td>
                                  <td>
                                    @if($wish_val->pro_quantity > $wish_val->pro_no_of_purchase)
                                    <span class="avail-pdt"> @lang(Session::get('front_lang_file').'.FRONT_AVAILABLE')</span>
                                    @else 
                                     <span class="soldout-pdt"> @lang(Session::get('front_lang_file').'.FRONT_SOLDOUT')</span>
                                    @endif
                                 </td>
                                  <td>
								  {{--@if($wish_val->pro_quantity > $wish_val->pro_no_of_purchase)--}}
										@if($wish_val->ws_type == 1)
											<a href="{{ url('product-details').'/'.base64_encode($wish_val->pro_id) }}" title="View details" data-toggle="tooltip" data-placement="bottom">@lang(Session::get('front_lang_file').'.FRONT_VIEW_DETAILS')</a>
										@else
										  <a href="{{ url('').'/'.$wish_val->store_slug.'/item-details/'.$wish_val->pro_item_slug}}" title="View details" data-toggle="tooltip" data-placement="bottom">@lang(Session::get('front_lang_file').'.FRONT_VIEW_DETAILS')</a>
										@endif
										{{--@endif--}}
                                    <a href="{{ url('remove_wish_product').'/'.base64_encode($wish_val->ws_id) }}" title="Cancel this order" data-toggle="tooltip" data-placement="bottom">
                                       @lang(Session::get('front_lang_file').'.ADMIN_REMOVE')
                                    </a>
                                      {{--<a href="{{ url('remove_wish_product')}}" title="Cancel this order" data-toggle="tooltip" data-placement="bottom">--}}
                                          {{--@lang(Session::get('front_lang_file').'.ADMIN_REMOVE')--}}
                                      {{--</a>--}}
                                  </td>
                                </tr>
                                @endforeach
                                @else
                                   <tr>
                                    <td></td>
                                    <td>
                                      @lang(Session::get('front_lang_file').'.NO_DATA_FOUND')
                                    </td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                               @endif
                              
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div> 
				</div>
              </div>
            </div>
          </div>          
		

@section('script')
@endsection
@stop

