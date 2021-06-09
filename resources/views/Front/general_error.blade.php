
@extends('Front.layouts.default')
@section('content')
	<div class="main-sec">
		<div id="mySuxesMsg"></div>
		<div class="section13">
			<div class="container">
				<div>
					<div class="row row-top">

						<div class="col-sm-6">
							<div class="back-to-shop">
								<a href="<?php echo url('');?>"><i class="fa fa-shopping-bag fa-lg" aria-hidden="true"></i> BACK TO SHOPPING</a>
							</div>
						</div> 
						<div class="emt-cart">
							<img src="<?php echo url('');?>/public/front/images/no_result.png" alt="">
							<div><?php echo $error_msg;?></div>
						</div>
						
					</div>
				</div>
			</div>
		</div>


	</div>


@section('script')


	<script>
       
	</script>





@endsection
@stop