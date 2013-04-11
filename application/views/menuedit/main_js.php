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
	
   
  /// $('#delete_button').click(function(){
	//	deletePage();
  // });
   $('#menu_save').click(function(){
		saveMenu();
  });
   $('#submenu_save').click(function(){
	   saveSub();
  });
   $('#all_save').click(function(){
	   allSave();
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

/*
 * Saves the new menu with the given title
 */
function saveMenu(){
	if($('#title').val() == ''){
		alert('<?php echo __('Name of the menu cannot be empty.')?>');
	}
	else{
		$('#action').val('subSave');
		$('#edit_menu_form').submit();
	}
}
/*
 * Save submenus
 */
function saveSub(){
	if($('#text').val() == '' || $('#item_url').val() == ''){
		alert('<?php echo __('Title or URL cannot be empty.')?>');
	}
	else{
		if($('#file').val() == ''){
			if(confirm("<?php echo __('Save without an image?');?>")){
				$('#action').val('saveSub');
				$('#edit_menu_form').submit();
			}
		}
		else {
			$('#action').val('saveSub');
			$('#edit_menu_form').submit();
		}
	}
}

/*
 * Save the changes in the table for menus
 */
 function allSave(){
	$('#action').val('saveAll');
	$('#edit_menu_form').submit();
 }



</script>