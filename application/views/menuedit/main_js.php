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
					var len = '<?php echo __('New Submenu in') ?>'.length + 1;
					var menu = sub.substring(len);
					$("#menuPage").val(menu);
				}
				else{
					$("#menuPage").val(data.menu);
				}		
		});
   });
   
   $('#delete_button').click(function(){
		deletePage();
   });

});

/*
* asks the user to confirm deletion and then submits the data
*/
function deletePage(){
	var page_id = $("#pages").val();
	if(page_id != 0){
		if (confirm("<?php echo __('Are you sure you want to delete this menu item');?>"))
		{
			$("#action").val('delete');
			$("#edit_menu_form").submit();
		}
	}
}






</script>