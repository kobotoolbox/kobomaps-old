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


<script type="text/javascript">

var mapData;

/*
* Controls the content of the divs by asking for the information for the page that was selected
*/
$(document).ready(function(){
	console.log('Im loading');
	$.get('<?php echo URL::base() .'uploads/data/'.ORM::factory('user', $map->user_id)->username.'/'. $map->json_file; ?>', function(data){
		mapData = data;

		console.log($);
		var indicator = $.address.parameter("indicator");
		/*
		if(indicator != undefined && typeof mapData !== "undefined" && typeof mapData.sheets !== "undefined")
		{
			graphCreator.createTotalChart(indicator);
			console.log('true');					
		}
		*/
		console.log(mapData);
	});
	
	
});


</script>