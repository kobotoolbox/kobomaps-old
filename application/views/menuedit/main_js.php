<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-22
* Javascript for menu editing
*************************************************************/
?>


<script type="text/javascript">


$(document).ready(function() {
	$('#pages').change(function(){
		var sub = $("#pages option:selected").text();
		var val = $("#pages").val();
		$.post("<?php echo URL::base(); ?>menuedit/getmenu", { 'sub': sub, 'val' : val }).done(
			function(data){
				data = jQuery.parseJSON(data);
				$("#text").val(data.text);
				$("#item_url").val(data.url);
				$("#menuString").val(sub);

				if(val == 0){
					var len = 'New Submenu in '.length;
					var menu = sub.substring(len);

					$("#menuPage").val(menu);
				}
				else{
					var test = $('option', '#pages').map(function(){
						return this.text;
					}).get();
					console.log(test);
				}		
		});
   });

});






</script>