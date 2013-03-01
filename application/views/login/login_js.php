<script type="text/javascript">
	$(document).ready(function() 
	{
		
		$("a[rel]").overlay({
			mask: 'grey',
			effect: 'apple'
		});
		
    });
    
    
    function submit_reset()
    {
		var email = $("#reset_email").val();
		$("#reset_spinner").show();
		
		$.post('<?php echo url::base();?>login/reset?email=' + encodeURIComponent(email),		
			function(data) {			
				$("#reset_response").html(data);
				$("#reset_spinner").hide();
				$("#reset_response").slideDown('slow', function(){});
		});
		
	}
</script>
