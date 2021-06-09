
<footer>
	<div class="container-fluid">
		<p class="copyright"> &copy; {{ date('Y') }} {{$FOOTERNAME}} . All Rights Reserved.</p>
	</div>
</footer>


</div>
<!-- Javascript -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->

<script src="{{url('')}}/public/admin/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="{{url('')}}/public/admin/assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="{{url('')}}/public/admin/assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
<!--<script src="{{url('')}}/public/admin/assets/vendor/chartist/js/chartist.min.js"></script>-->
<script src="{{url('')}}/public/admin/assets/scripts/klorofil-common.js"></script>


<script src="{{url('')}}/public/admin/assets/scripts/jquery.dataTables.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/dataTables.bootstrap.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/metisMenu.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/dataTables.responsive.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/sb-admin-2.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
<script>
//load_notification();
	$(function () {
		$('.tooltip-demo').tooltip();
	});
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	function validate_phone(gotId) {
		var defaultDial = '{{$default_dial}}';
		var element = document.getElementById(gotId);
		if(element.value=='' || element.value.length < defaultDial.trim().length)
		{
			
			$('#'+gotId).val('{{$default_dial}}');
			
		}
		element.value = element.value.replace(/[^0-9 +]+/, '');
	}
	$(function(){
		    $("[data-hide]").on("click", function(){
		        $(this).closest("." + $(this).attr("data-hide")).hide();
		    });
		});

	setTimeout(function() {
							  load_notification();
							}, 5000);
	function load_notification()
	{	//alert($('#grant_total_notify').text()); 
		var old_alert = $('#grant_total_notify').text();
		$.ajax({
				'type' : 'POST',
				'url'	: '{{ url('refresh_delboy_notification') }}',				
				'data'	: {'_token' : '{{csrf_token()}}'},
				success:function(response)
				{ //alert(response);
					var data = response.split('`');
					$('#grant_total_notify').html(data[0]);	
					$('#reject_count').html(data[1]);	
					$('#new_or_count').html(data[2]);	
					$('#accept_or_count').html(data[3]);	
					$('#or_count').html(data[4]);
					
					//alert(old_alert+'//'+data[0]);
					//alert(old_alert < data[0]);	
					if((old_alert.trim() != '') && old_alert.trim() < data[0])
					{
						$(".notify_alert").show();
					}
					 //$(".notify_alert").show();
					},
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);					
				}
			});
	}

</script>
