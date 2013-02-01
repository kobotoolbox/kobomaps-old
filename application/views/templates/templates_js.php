<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<script type="text/javascript">
	
	function deleteTemplate(id)
	{
		if (confirm("<?php echo __("Are you sure you want to delete this template? \r\n\r\n You will break any maps that use this template.");?>"))
		{
			$("#template_id").val(id);
			$("#action").val('delete');
			$("#edit_template_form").submit();
		}
	}
</script>
