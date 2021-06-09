<footer>
	<div class="container-fluid">
		<!--<p class="copyright">&copy; {{ date('Y') }} . All Rights Reserved.</p>-->
		<p class="copyright">©<span>{{ $SITEFOOTERTEXT }}</span>. {{(Lang::has(Session::get('mer_lang_file').'.MER_ALL_RIGHTS')) ? trans(Session::get('mer_lang_file').'.MER_ALL_RIGHTS') : trans($MER_OUR_LANGUAGE.'.MER_ALL_RIGHTS')}}</p>
	</div>
</footer>
</div>
<!-- Javascript -->

<script src="{{url('')}}/public/admin/assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script src="{{url('')}}/public/admin/assets/scripts/klorofil-common.js"></script>


<script src="{{url('')}}/public/admin/assets/scripts/jquery.dataTables.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/dataTables.bootstrap.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/metisMenu.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/dataTables.responsive.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/sb-admin-2.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
<script>
	
	$(function () {
		
		$('.tooltip-demo').tooltip({placement:'left'})
		//$('#centralModalInfo').modal('show');
		/*$('#centralModalLGInfoDemo').modal({
			backdrop: 'static',
			keyboard: false
		})*/
	})
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$(function(){
		    $("[data-hide]").on("click", function(){
		        $(this).closest("." + $(this).attr("data-hide")).hide();
		    });
		});
	setTimeout(function() {
							  load_notification();
							}, 5000);
	function load_notification()
	{	//alert();
		var old_alert = $('#grant_tot_notify').text();
		$.ajax({
				'type' : 'POST',
				'url'	: '{{ url('refresh_mer_notification') }}',				
				'data'	: {'_token' : '{{csrf_token()}}'},
				success:function(response)
				{ 
					var data = response.split('`');
					$('#grant_tot_notify').html(data[0]);	
					$('#item_notify').html(data[1]);	
					$('#ord_notify').html(data[2]);	
					if(old_alert.trim() != '' && old_alert.trim() < data[0])
					{
						$(".notify_alert").show();
					}
					},
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);					
				}
			});
	}
</script>
