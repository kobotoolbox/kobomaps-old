<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<script type="text/javascript">
	
  /**
  * @param int id of the map to delete
  */
	function deleteMap(id)
	{
		if (confirm("<?php echo __('are you sure you want to delete this map');?>"))
		{
			$("#map_id").val(id);
			$("#action").val('delete');
			$("#edit_map_form").submit();
		}
	}


	$(document).ready(function() 
			{
			//make the apple overlay work for sharing purposes
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

				//make the select all/deselect all check box work
				$("#selectAll").change(function(){
					$('input[id^="map_check_"]').prop('checked', this.checked);
										
				});

				//setup the Delete Selected button handler
				$(".deleteSelectedBtn").click(function(){
					if (confirm("<?php echo __('are you sure you want to delete the selected maps');?>"))
					{
						$("#action").val('delete_selected');
						$("#edit_map_form").submit();
					}
					return false;
					});
				
		    });


	
	   
</script>
