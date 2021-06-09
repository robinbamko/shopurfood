@if(session()->get('val_errors')!='')
	<div class="alert alert-danger text-center animated fadeIn">
		<!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button> -->
		<strong>
			{!! session()->get('val_errors') !!}
		</strong>
	</div>
@else
	@if (session()->has('errors'))
		@if ($errors->any())
			<div class="alert alert-danger text-center animated fadeIn">
				<strong>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
				@endforeach
				</strong>
			</div>
		@endif
	@endif
@endif			