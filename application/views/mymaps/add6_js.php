<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add6_js.php - View
* Written by Etherton Technologies Ltd. 2012
* Started on 04/19/2013
*************************************************************/
?>

<script type='text/javascript'>

//see if the water color was ever changed in any way
$(document).ready(function(){
	$('#water_geometry_colorDiv').change(function(){
		console.log('change');
		$('#waterActive').val('true');
	});
});


</script>