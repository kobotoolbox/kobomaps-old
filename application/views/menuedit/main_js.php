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
	
	$("a[rel]").overlay({
		mask: 'grey',
		effect: 'apple',
		onBeforeLoad: function() {
			 
            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");
 
            // load the page specified in the trigger
            wrap.load(this.getTrigger().attr("href"));
        }
	
	}); 
  
});


/**
 * Call this when you want to drop a complete menu
 * and all the kids
 */
function deleteSubMenu(subMenuId)
{
	if (confirm("<?php echo __('Are you sure you want to delete this menu');?>"))
	{
		$("#action").val('delete_sub_menu');
		$("#submenu_id").val(subMenuId);
		$("#edit_menu_form").submit();
	}
}

/**
 * Run to put the title of a menu into the title text
 * box so the user can edit it.
 */
function editSubMenu(subMenuId, title)
{
	$("#action").val('edit_submenu');
	$("#submenu_id").val(subMenuId);
	$("#title").val(title);
}


/**
 * Use this to edit submenu items.
 * It's called by the save button for editing menu items
 */
function editSubMenuItem(id, menuId){
	$("#action").val('edit_submenu_item');
	$("#submenu_id").val(menuId);
	$("#submenu_item_id").val(id);
	$("#edit_menu_form").submit();
}


/**
 * Called by the delete submenu item link
 */
function deleteSubMenuItem(id){
	if (confirm("<?php echo __('Are you sure you want to delete this menu item');?>")){
		$("#action").val('delete_submenu_item');
		$("#submenu_item_id").val(id);
		$("#edit_menu_form").submit();
	}
}

/**
 * Called to close the edit submenu item dialog
 */
function cancelSubMenuItem(){
	$("#overlay a.close").click();
}




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
		$('#action').val('saveMenu');
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