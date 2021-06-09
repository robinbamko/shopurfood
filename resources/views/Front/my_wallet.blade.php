@extends('Front.layouts.default')
@section('content')
<style>
	.userContainer .row.panel-heading {
		text-transform: none;
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
					
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 my-order-table">
                        <div class="table-responsive form-group">
                        	<table class="table table-hover" id="dataTables-example">
								<thead>
									<tr>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SNO')) ? trans(Session::get('front_lang_file').'.ADMIN_SNO') : trans($FRONT_LANGUAGE.'.ADMIN_SNO')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.FRONT_REFERRAL_USER')) ? trans(Session::get('front_lang_file').'.FRONT_REFERRAL_USER') : trans($FRONT_LANGUAGE.'.FRONT_REFERRAL_USER')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.FRONT_OFFER_PER')) ? trans(Session::get('front_lang_file').'.FRONT_OFFER_PER') : trans($FRONT_LANGUAGE.'.FRONT_OFFER_PER')}}</th>
										<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.FRONT_AMT_ADDED')) ? trans(Session::get('front_lang_file').'.FRONT_AMT_ADDED') : trans($FRONT_LANGUAGE.'.FRONT_AMT_ADDED')}}</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>
						</div>


					</div>
					@if(count($refered_details) > 0)
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<div class="use_wallet-section" style="">	
							<div class="payment-wallet-sec">
								<ul>
									<li>
										<div class="payment-wallet-sec-head" >
											@lang(Session::get('front_lang_file').'.FRONT_TO_WALLWT')
											<div class="wallet-wave"><img src="{{url('')}}/public/front/images/wave.png"></div>
										</div>
										<div class="payment-wallet-sec-body">
												{{number_format($refered_details[0]->cus_wallet,2)}}
										</div>
										
									</li>
									<li>
										<div class="payment-wallet-attr">
											<span>-</span>
										</div>
									</li>
									<li>
										<!--<a href="javascript:;" class="tooltip-demo" data-toggle="tooltip" title ="@lang(Session::get('front_lang_file').'.FRONT_VIEW_DETAILS')" id="toggle-sliding">-->
										<div class="payment-wallet-sec-head tooltip-demo" style="cursor:pointer;" data-toggle="tooltip" title ="@lang(Session::get('front_lang_file').'.FRONT_VIEW_DETAILS')" id="toggle-sliding">
											@lang(Session::get('front_lang_file').'.ADMIN_UESD_WALLET') <i class="fa fa-caret-down pull-right"></i>
											<div class="wallet-wave"><img src="{{url('')}}/public/front/images/wave.png"></div>
										</div>
										<div class="payment-wallet-sec-body">
											{{number_format($refered_details[0]->used_wallet,2)}}
										</div>
										<!--</a>-->
									</li>
									

									<li>
										<div class="payment-wallet-attr">
											<span>=</span>
										</div>
									</li>
									<li>
										<div class="payment-wallet-sec-head" >
											@lang(Session::get('front_lang_file').'.FRONT_BAL_WALLET')
											<div class="wallet-wave"><img src="{{url('')}}/public/front/images/wave.png"></div>
										</div>
										<div class="payment-wallet-sec-body">
											{{number_format(($refered_details[0]->cus_wallet - $refered_details[0]->used_wallet),2)}}
										</div>
										
									</li>
								</ul>
							</div>
						</div>
						
						@if(count($refered_details) > 0)
						{{-- view used wallet --}}
						<div class="collapse" id="view_wallet" style="width:100%">
							<div class="table-responsive form-group">
								
								<table class="table table-hover" id="dataTables-wallet">
						
									<thead>
										<tr>
											<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SNO')) ? trans(Session::get('front_lang_file').'.ADMIN_SNO') : trans($FRONT_LANGUAGE.'.ADMIN_SNO')}}</th>
											<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_ID') : trans($FRONT_LANGUAGE.'.ADMIN_ORDER_ID')}}</th>
											<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UESD_WALLET')) ? trans(Session::get('front_lang_file').'.ADMIN_UESD_WALLET') : trans($FRONT_LANGUAGE.'.ADMIN_UESD_WALLET')}}</th>
											<th scope="col">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_DATE') : trans($FRONT_LANGUAGE.'.ADMIN_ORDER_DATE')}}</th>
										</tr>
									</thead>
									<tbody>
											
									</tbody>
								</table>
						  </div>
						
						</div>
						{{-- view used wallet ends --}}
						@endif
						
					</div>
					@endif
				</div>
			</div> 
			</div>
		</div>
	</div>
</div>

@section('script')
<script>
$(document).ready(function () {
		table = $('#dataTables-example').DataTable({
					"processing": false,	
					//"scrollX":true,
					"serverSide": true,
					"bLengthChange": false,
					"bAutoWidth": false, 
					"searching": false,
					//
					"info": false,
					"dom": 'l<"toolbar">frtip',
					"ajax":{
						"url": "{{ url('user-wallet') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}"}
					},
					
					"columns": [
								{ "data": "sno", sWidth: '5%' },
								{ "data": "name", sWidth: '45%' },
								{ "data": "offer", sWidth: '20%' },
								{ "data": "amount", sWidth: '30%' },
								],
					"order": [ 0, 'desc' ],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		//table.draw();
	

		table1 = $('#dataTables-wallet').DataTable({
					"processing": false,	
					//"scrollX":true,
					"serverSide": true,
					"bLengthChange": false,
					"bAutoWidth": false, 
					"searching": false,
					
					"info": false,
					"dom": 'l<"toolbar">frtip',
					"ajax":{
						"url": "{{ url('wallet-used') }}",
						"dataType": "json",
						"type": "POST",
						"data":{ _token: "{{csrf_token()}}"}
					},
					"columns": [
								{ "data": "sno", sWidth: '5%' },
								{ "data": "id", sWidth: '45%' },
								{ "data": "amount", sWidth: '20%' },
								{ "data": "date", sWidth: '30%' },
								],
					"order": [ 3, 'desc' ],
					"fnDrawCallback": function (oSettings) {
						$('.tooltip-demo').tooltip({placement:'left'})
					}
				});
		//table1.draw();
	});

	$('#toggle-sliding').click(function(){
            $('#view_wallet').slideToggle('slow');
        });
</script>
@endsection
@stop