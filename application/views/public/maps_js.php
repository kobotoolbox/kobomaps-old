<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<script type="text/javascript">
	
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
				//for the search auto complete
				$( "input#q" ).autocomplete({
				      source: "<?php echo URL::base()?>public/search",
				      minLength: 2,
				    });

				//for sharing
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
</script>
