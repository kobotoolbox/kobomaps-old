<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add4_js.php - View
* Written by Etherton Technologies Ltd. 2012
* Started on 12/06/2011
*************************************************************/
?>
	

<script type="text/javascript">


function toggle_id(elem_id)
{
	$('#'+elem_id).toggle('slow');
}

function toggle_class(elem_class)
{
	$('.'+elem_class).toggle('slow');
}


function set_default_map_style(elem_id)
{
	$('#'+elem_id).val("<?php echo Model_Map::get_style_default_js(); ?>");
	
}


</script>