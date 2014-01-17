<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* totalchart_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-12-16
* JS for the national total charts
*************************************************************/
?>

<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/graphCreator.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/colorProperties.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/mapParsers.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.navigate.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.tools.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery-ui.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.address-1.4.min.js"> </script>



<script type="text/javascript">

var mapData;

/*
* Controls the content of the divs by asking for the information for the page that was selected
*/
$(document).ready(function(){
	$.get('<?php echo URL::base() .'uploads/data/'.ORM::factory('User', $map->user_id)->username.'/'. $map->json_file; ?>', function(data){
		mapData = data;
		
		if('<?php echo $_GET['indicator']?>' != undefined && typeof mapData !== "undefined" && typeof mapData.sheets !== "undefined")
		{
			graphCreator.drawTotalChart('<?php echo $_GET['indicator']?>','#<?php echo $map->graph_bar_color ?>', '#<?php echo $map->graph_select_color ?>');
		}
	});
});


</script>