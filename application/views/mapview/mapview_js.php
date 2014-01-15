<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* mapview_js.php - View
* Writen by Etherton Technologies Ltd. 2012
* Started on 2012-11-27
*************************************************************/
?>
	
	

<link href="<?php echo  URL::base() ?>media/css/templatePreview.css" type="text/css" rel="stylesheet">
<link href="<?php echo  URL::base() ?>media/css/largemap.css" type="text/css" rel="stylesheet">  

<link rel="stylesheet" href="<?php echo URL::base(); ?>media/css/jquery-ui.css" />
<style> <?php echo $map->CSS?>
	.countylabelname {
		font-size: <?php echo $map->region_label_font.'px'?>;
	}
	.areaVal{
		font-size: <?php echo $map->value_label_font.'px'?>;
	}
</style>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.min.js"> </script>

<!-- Let the sheetControl know if it should extend range -->
<script type="text/javascript">
var extend_range = <?php echo $map->extend_range;?>;
</script>

<!-- <script type="text/javascript" src="<?php echo URL::base(); ?>media/js/dragresize.js"> </script> -->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"> </script>

<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/RainbowVis-JS-master/rainbowvis.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.navigate.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.tools.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery-ui.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.address-1.4.min.js"> </script>

<!-- These contain all of the js classes that used to form this page -->
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/label.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/playback.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/colorProperties.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/graphCreator.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/mapMath.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/mapParsers.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/sheetControl.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/mapview/mapButtons.js"></script>

<script type="text/javascript">


//this code uses jquery (http://jquery.com)
//and the jquery Address plugin (http://www.asual.com/jquery/address/)

/**
 * global variable that holds the map
 */
var map; 

/**
 * global variable that holds the data for the map
 */
var mapData;


/**
 *  gives us a list of names for geographicAreas
 */
var geographicAreaNames = new Array();

/**
 * global variable holding an array of points for each area so areaPoints["bomi"] 
 * would return an array of all the points for Bomi
 */
var areaPoints = new Array(); 
  
/**
 * global variable holding the polygons for each area. areaGPolygons["bomi"] 
 * would return the polygon for Bomi
 */
var areaGPolygons = new Array();

/**
 * global variable holding the center point lat,lon for each area, this is 
 * where the marker will go
 */
var areaCenterPoints = new Array(); 

/**
 * global variable holding all the Labels for each area
 */
var labels = new Array();

/**
 * global variable that holds all of the info windows
 */
var infoWindows = new Array(); 

var areaNamesToNumbers = new Array();
/**
 * global array that maps the unqiue string indicator to the parameters that would 
 * be fed into UpdateAreaAllData(title, data, nationalAverage). This way we can 
 * use indicators to call the update method to redraw the map
 */   
var indicatorsToUpdateParams = new Array();

/**
 * Sets if we should round the values or not to whole integers
 */
var round = true;

//add a title to the map and start the listeners and buttons
	$(document).ready(function() {
	   $("#kmapTitle").html(<?php echo json_encode($map->title);?>);
	   
	   initialize_map();
	   mapButtons.initialize_buttons("<?php echo __("Show Labels"); ?>", "<?php echo __("Hide Labels"); ?>",
			   "<?php echo __("Show Values"); ?>","<?php echo __("Hide Values"); ?>", "<?php echo __("Make this map fullscreen.")?>", "<?php echo __("Close fullscreen.")?>");
	   mapButtons.init_legend_listener('<?php echo __('legendString')?>');

	   if($('#descriptionText p').height() < 100){
			$('#descriptionText').height($('#descriptionText p').height() + 20);
			$('#descriptionText').css('overflow-y', 'hidden');
	   }
	   var fs = <?php echo isset($_GET['fullscreen']) ? 'true' : 'false'; ?>;
	   if(fs == 'true'){
			$('#fullScreenButton').click();
	   }
	});


//itintializes everything, both the mandatory google maps stuff, and our totally awesome json to gPolygon code
function initialize_map() {
	  
	//creates the options for defining the zoom level, map type, and center of the google map
	var myOptions = {
		zoom: <?php echo $map->zoom;?>, 	//creates the initial zoom level. This is defined in the container file as it is country-specific
		center: new google.maps.LatLng(<?php echo $map->lat;?>,<?php echo $map->lon;?>), //creates the coordiantes that will center the map. This is defined in the container file as it is country-specific
		streetViewControl: false,
		panControl: false,
		mapTypeControl: true,
		mapTypeControlOptions: {
			  position: google.maps.ControlPosition.RIGHT_BOTTOM,
			  mapTypeIds: [google.maps.MapTypeId.TERRAIN, google.maps.MapTypeId.HYBRID, 'kmaps'],
			  style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
		},		
		zoomControlOptions: {
			position: google.maps.ControlPosition.RIGHT_CENTER
		}
		
	};

	$('#maplinks').resizable({minWidth:350});
	$('#questionsindicator').resizable({minHeight:50, maxHeight: 375});
	$('#maplinks').resize(function(){
			if($('#descriptionText p').height() < 90){
				$('#descriptionText').height($('#descriptionText p').height() + 20);
				$('#descriptionText').css('overflow-y', 'hidden');
   			}});

	//Set the colors with the database
	colorProperties.setColors('#<?php echo $map->border_color?>','#<?php echo $map->region_color ?>', 
			'<?php echo $map->polygon_color ?>','#<?php echo $map->graph_bar_color ?>', '#<?php echo $map->graph_select_color ?>');
	
	<?php 
	if(empty($map->map_style))
	{
		echo 'var mapStyles = [];';
	}
	else
	{
		echo "var mapStyles =". $map->map_style; 
	}?>


	//creates the map by looking for the "map_canvas" item in the HTML below. the map will fill in the "map_canvas" div
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	/*Creates the options for our custom map type*/
	var styledMapOptions = {
		name: "Default",
		alt: "View the map in KoBoMaps default theme"
	};
	/*Adds new map and sets it to default*/
	var kmapsMapType = new google.maps.StyledMapType(mapStyles, styledMapOptions);
	map.mapTypes.set('kmaps', kmapsMapType); 
	map.setMapTypeId('kmaps');

	<?php 
		//check if the template exists, if it doesn't this has happened because a user, or careless admin
		//deleted it out from under our poor user. If this has happened show an error
		if($template->loaded())
		{	
	?>	
	//Calling the boundaries and data files. The variables need to be defined in the container file as they are country-specific
	mapParsers.parseJsonToGmap('<?php echo URL::base() .'uploads/templates/'. $template->file; ?>', '<?php echo URL::base() .'uploads/data/'.ORM::factory('user', $map->user_id)->username.'/'. $map->json_file; ?>');
	<?php }else{
		//the template is missing :-( show an error message
	?>

	$("#missingTemplate").overlay({
		mask: 'grey',
		effect: 'apple',
	    // disable this for modal dialog-type of overlays
	    closeOnClick: false,
	    // load it immediately after the construction
	    load: true
	    });
				
	<?php }?>

	if(map.getZoom() < <?php echo $map->label_zoom_level?>){
		Label.renderLabelNames = false;
		Label.renderLabelVals = false;
	}

	var previousZoom = map.getZoom();
	google.maps.event.addListener(map, 'zoom_changed', function() {
		var	mapZoom = <?php echo $map->label_zoom_level?>;
	
		if((previousZoom >= mapZoom &&  map.getZoom() < mapZoom))
		{
			//Label.renderLabels = true;
			Label.renderLabelNames = false;
			Label.renderLabelVals = false;

			$("#turnOffLabelsButton").addClass("active");
			$("#turnOffValuesButton").addClass("active");

		}
		else if(previousZoom < mapZoom &&  map.getZoom() >= mapZoom)
		{
			//Label.renderLabels = true;
			Label.renderLabelNames = true;
			Label.renderLabelVals = true;
			$("#turnOffLabelsButton").removeClass("active");
			$("#turnOffValuesButton").removeClass("active");	
		}

		previousZoom = map.getZoom();
	});

	
};


/**
* Updates all data for all areas
* @param string Title: Title of the question
* @param array Data: associative array of the percentages keyed by Area names as defined in the JSON that defines areas and their bounds
* @param double nationalAverage: average of the values for the selected region
* @param string indicator is the example (0_0_2) string of the indicator path
* @param string unit is the unit designated by the user when the map was made
* @param string totalLabel is the name for the totals that is designated by the user
* Note: All of this assumes positive numbers. 
*/
function UpdateAreaAllData(title, data, nationalAverage, indicator, unit, totalLabel, extend_data)
{
	//first zero out all the current map data. Do this incase certain indicators don't apply to all geographic areas
	zeroOutMap();

	var min, spread;

		if(extend_data === null){
			var minSpread = mapMath.calculateMinSpread(data, null);
			min = minSpread["min"];
			spread = minSpread["spread"];
		}
		else{
			minSpread = mapMath.calculateMinSpread(extend_data, indicator);
			min = minSpread["min"];
			spread = minSpread["spread"];
		}
	
	//loop over all our data
	for(areaName in data)
	{
		if(!isNaN(data[areaName]) && typeof labels[areaName] != 'undefined')
		{
			graphCreator.UpdateAreaPercentageTitleData(areaName, data[areaName], min, spread, title, data, indicator, unit, '<?php echo __('Current Indicator')?>');
		}
	}
	
	//update the key
	colorProperties.updateKey(min, spread, title, unit);
	//console.log("National Average: " + nationalAverage);
	
	//update the national average
	if(typeof nationalAverage !== "undefined" && !isNaN(nationalAverage))
	{
		$("#nationalaveragediv").show();
		$("#nationalIndicatorChart").show();
		$('#expandDiv').show();

		//total label -- defaults to "Total"
		if(typeof totalLabel == "undefined")
		{
			totalLabel = "";
		}
		//totalLabel = "Total";
		
		graphCreator.updateNationalAverage(min, spread, nationalAverage, unit, indicator, totalLabel);
	}
	else
	{
		$("#nationalaveragediv").hide();
		$("#nationalIndicatorChart").hide();
	}

	//draw the javascript graph of the totals of the selected indicator stack
	//graphCreator.drawTotalChart(indicator, null);
	
	if(title == "<?php __("Please select an indicator to display its data.") ?>" )
	{
		$('#sourcetextspan').hide();
	}
}


/**
* This little function just goes through and wipes clean the areas on the map
* and their charts and so forth
*/
function zeroOutMap()
{
		//loop over the polygons and set the colors to not-set
		for(areaName in areaGPolygons)
		{
			colorProperties.formatAreaOpacityColor(areaName, 0.75, colorProperties.getRegion());
			//set the label to blank("")
	
			labels[areaName].set("areaValue", "");
			<?php if($map->show_empty_name == 1){?>
			labels[areaName].set('show_empty_name'	, true);
			<?php }else{ ?>
			labels[areaName].set('show_empty_name', false);
			<?php }?>
			labels[areaName].draw();
			//remove any old pop-up listeners
			google.maps.event.clearListeners(areaGPolygons[areaName], 'click');
			
		}
}

</script>
	
<?php 
	$facebook_js = new View('js/facebook');
	$shareCenter = new View('js/shareEdit');
	echo $facebook_js; 
	echo $shareCenter;
?>
