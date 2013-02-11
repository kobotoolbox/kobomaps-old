<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-25
* Javascript way of showing users their stats
*************************************************************/
?>


<script type="text/javascript"> 

	function deleteMessage(id)
	{
		if (confirm("<?php echo __('Are you sure you want to delete this message');?>"))
		{
			$("#message_id").val(id);
			$("#action").val('delete');
			$("#edit_messages_form").submit();
		}
	}
	
	$(document).ready(function(){
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
				$('input[id^="message_check_"]').prop('checked', this.checked);
									
			});

			//setup the Delete Selected button handler
			$(".deleteSelectedBtn").click(function(){
				$("#action").val('delete_selected');
				$("#edit_messages_form").submit();
				return false;
			});
	});
</script>
