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

<!-- <script type="text/javascript" src="<?php echo URL::base(); ?>media/js/dragresize.js"> </script> -->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"> </script>

<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/RainbowVis-JS-master/rainbowvis.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.navigate.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.tools.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery-ui.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.address-1.4.min.js"> </script>

<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/label.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/playback.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/colorProperties.js"></script>

<script type="text/javascript">


/*Todo: Make all these setings on the website:*/
var kmapInfodivHeight = 280;
//modifiers for the flot graphs
var kmapInfochartWidth = 315; //if this number is changed, the legend div (which contains the national graph) also needs to be adjusted 
var kmapInfochartBarHeight = 40; //these are numbers, not strings
var kmapInfochartXAxisMargin = 35;



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
 * global variables that hold the map color choices, just replaces previous values in code
 */
colorProperties.border_color = '#<?php echo $map->border_color ?>';
colorProperties.region_color = '#<?php echo $map->region_color ?>';
colorProperties.polygon_color = '<?php echo $map->polygon_color ?>';
colorProperties.graph_color = '#<?php echo $map->graph_bar_color ?>';
colorProperties.graph_select_color = '#<?php echo $map->graph_select_color ?>';


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
	   initialize_buttons();
	   init_legend_listener();
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
	parseJsonToGmap('<?php echo URL::base() .'uploads/templates/'. $template->file; ?>', '<?php echo URL::base() .'uploads/data/'. $map->json_file; ?>');
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
 * Use this awesome super sweet function to build up the HTML of the page
 * @param elementId string the ID of the element that this new level of indicator will be added to
 * @param indicators array List of indicators that are currently up for parsign
 * @param level the numeric level, so we know how far down the rabit hole we've gone.
 * @return int the furthest level down we've gone into the rabbit whole
 */
function parseIndicators(elementId, indicators, level)
{
	var newLevel = level; //used to store the deepest Level
	for(indicatorId in indicators)
	{
		var indicator = indicators[indicatorId];
		var newId = elementId + "_" + indicatorId;
		$("#indicatorId_"+elementId).append('<li class="indicator indicatorLevel_'+level+'"><span id="indicatorSpanId_'+newId+'" class="indicator indicatorLevel_'+level+'" >'+indicator.name+'</span><ul id="indicatorId_'+newId+'" class="indicator indicatorLevel_'+level+'"></ul></li>');
		//if we've hit data, stop digging down, otherwise keep going.
		if(typeof indicator.data == 'undefined')
		{
			newLevel = parseIndicators(newId, indicator.indicators, 1+level);
		} 
		//if we've hit data mark that
		else 
		{
			$("#indicatorSpanId_"+newId).addClass('dataLevel');
		}
	}//end for loop

	return newLevel;
}

/**
* used to create the sheet arrows and ability to scroll through them
* @param jsonDataUrl location of json data
*/
function parseJsonData(jsonDataUrl)
{
	//initiates a HTTP get request for the csv file
	$.get(jsonDataUrl, function(data) {

		mapData = data;

		var depthOfData = 0; //how far down till you reach the data

		var initialId=1;

		var sheetCount = 0;

		var lastSheetId = null;
		//loop over the sheets and create HTML for them
		for(sheetId in mapData.sheets)
		{
			var sheet = mapData.sheets[sheetId];			
			$("#questionsindicators").append('<li class="sheet" id="sheetli_'+sheetId+'"><span id="sheetspan_'+sheetId+'" class="sheet" style="display:none">'+sheet.sheetName+'</span><ul id="indicatorId_'+sheetId+'" class="sheet"></ul></li>');
			depthOfCurrentData = parseIndicators(sheetId, sheet.indicators, 1);
			
			if(depthOfCurrentData > depthOfData)
			{
				depthOfData = depthOfCurrentData;
			}

			//create html for sheets at bottom of the page 
			$("#sheetnames").append('<li class="sheet2"><span class="sheet2" id="sheetSelector_'+sheetId+'" onclick="sheetSelect('+sheetId+');">'+sheet.sheetName+'</span></li>');		

			
			if(initialId==1)
			{
				//used to open the first sheet as a default when the page loads
				initialId=sheetId;
			}

			lastSheetId = sheetId;

			sheetCount++;
		}

		//mark the last sheet so we use it in measurement later on
		$("#sheetSelector_"+lastSheetId).addClass('lastSheetSelector');

		if(sheetCount > 1)
		{
			$('#sheetlinks').show();
			setup_scrolling();
		}
		else
		{
			$('.playbackLabels').hide();
			$('#playBackButtons').hide();
			$('#mapSocialShare').width(206);
			$('#sheetnamesStartControl').hide();
			$('#sheetnamesLeftControl').hide();
			$('#sheetnamesWrapper').hide();
			$('#sheetnamesRightControl').hide();
			$('#sheetnamesEndControl').hide();
		}
		
		
		$('li.sheet').hide(); //This hides all ul level1 by default until they are toggled. Can also be defined in css.

		
		//control the clicking behavior of the indicator levels that lead to the data
		for(var i = 1; i < depthOfData; i++)
		{
			$('span.indicatorLevel_'+i).click(function (){
				midIndicatorClick($(this));
			});
			$('ul.indicatorLevel_'+i).hide(); //This hides all ul level1 by default until they are toggled. Can also be defined in css.			
		}
	
		//control the clicking behavior of the data level
		$('span.dataLevel').click(function (){	//originally was $('span.indicatorLevel_'+depthOfData).click(function (){
			dataIndicatorClick($(this));
		});

		//check if we're supposed to auto load the data for a particular indicator?
		var autoLoadIndicator = $.address.parameter("indicator");
		if( autoLoadIndicator != "" && typeof autoLoadIndicator !== "undefined" )
		{
			showByIndicator(autoLoadIndicator);
		}
		else
		{
			//Default selects first sheet 
			sheetSelect(initialId);
		}
		
		//hide the temporary loading text once the indicators are visible
		$('#loadingtext').remove();
		playback.onLoad();
	});		
}//end parseJsonData function



/**
* Allows for the scrolling between different sheets on the map
* @param int sheetId the id of the sheet that needs to be scrolled to
*/
function sheetSelect(sheetId)
{

 	var sheetButton = $("#sheetSelector_"+sheetId);
 	var sheetItem =  $("#sheetli_"+sheetId);

	if(!sheetButton.hasClass("active"))
	{


		//////////////////////////////////////////////
		//This hides, shows removes active, adds active
		//to all the necessary HTML elements
		////////////////////////////////////////////////
		//turn off active on all elements
		$('#sheetnames li.active').removeClass("active");

		$('#questionsindicators li.sheet').hide();
		sheetItem.addClass("active");
		sheetItem.show();
		
		$('#sheetnames li.sheet2 span').removeClass("active");		
		sheetButton.addClass("active");

		//allows the user to hit play and start at this sheet
		playback.setSheetStart(sheetId);

		///////////////////////////////////////////////////////
		//This next part tries to figure out if there is a
		//matching indicator on the new sheet
		//that should be switched on
		

		//get current map selection 
		var autoLoadIndicatorCurrent = $.address.parameter("indicator");
		//if nothing was previously selcted then bounce.
		if(autoLoadIndicatorCurrent == null || autoLoadIndicatorCurrent.length == 0)
		{
			return;
		}
		
		//grab the sheet ID of the current indicator
		var indicatorIndexesArray = autoLoadIndicatorCurrent.split('_');
		//make sure they didn't click on the same sheet
		if(indicatorIndexesArray[0] != sheetId)
		{
			//grab the data for the previously selected and newly selected sheets
			var newSheet = mapData.sheets[sheetId];
			var currentSheet = mapData.sheets[indicatorIndexesArray[0]];
			var newPtr = newSheet;
			var currentPtr = currentSheet;
			var newIndicatorIdString = sheetId + "_";

			//loop over the indicator indexes, until you get to the next to last
			for(var i = 1; i < indicatorIndexesArray.length - 1; i++)
			{
				//advance the ptrs
				newPtr = newPtr.indicators[indicatorIndexesArray[i]];
				currentPtr = currentPtr.indicators[indicatorIndexesArray[i]];

				newIndicatorIdString += indicatorIndexesArray[i] + "_";

				//make sure the indicators at this level match, if not, bounce.
				if(newPtr.name != currentPtr.name)
				{
					return;
				}
			}
			//now grab the name of the current indicator
			var currentIndicatorName = $("#indicatorSpanId_"+autoLoadIndicatorCurrent).text();
			//loop over the new indicator and see if any of the indicators match the name of the current indicator
			for(i in newPtr.indicators)
			{
				var name = newPtr.indicators[i].name;
				if(name == currentIndicatorName)
				{
					//we have a match show, this indicator
					newIndicatorIdString += i;
					showByIndicator(newIndicatorIdString);
					return;
				}
			}			
		}//end if the two sheets don't match
	}//end 	if(!sheetButton.hasClass("active"))
	
}//end function


/**
* Controls the clicking of the indicators on the left side of the page
* @param view indicatorItem is the indicator to turn on
* @param boolean forceOn if the map should be on
*/
function midIndicatorClick(indicatorItem, forceOn)
{
	if(forceOn != undefined && forceOn == false)
	{
		indicatorItem.removeClass("active"); //highlights active span
		indicatorItem.siblings("ul.indicator").hide(); //This hides the child ul level1 element
	}
	else if (forceOn != undefined && forceOn == true)
	{
		indicatorItem.addClass("active"); //highlights active span
		indicatorItem.siblings("ul.indicator").show(); //This shows the child ul level1 element
	}
	else if(indicatorItem.hasClass("active"))
	{
		indicatorItem.removeClass("active"); //highlights active span
		indicatorItem.siblings("ul.indicator").hide(); //This hides the child ul level1 element
	}
	else 
	{
		indicatorItem.addClass("active"); //highlights active span
		indicatorItem.siblings("ul.indicator").show(); //This shows the child ul level1 element
	}
}

  
/**
* Highlights this item and makes the sheets go to it
* @param div dataItem to be turned on
*/
function dataIndicatorClick(dataItem)
{		
	$('span.dataLevel').removeClass("active"); //removes highlight of any other level3 li element
	dataItem.addClass("active"); //highlights active span
	var id = dataItem.attr('id').substring(16); //grab the id for processing
	showByIndicator(id);
}

/**
 * Takes in an indicator string and then renders the map according to the data for that indicator
 * If the indicator doesn't exist it'll just exit gracefully
 * @param string indicator is the example (0_0_2) value of an indicator
 */
function showByIndicator(indicator)
{
	if(typeof indicator == 'undefined')
	{
		return;
	}
	
	var dataPtr = null;
	var ids = indicator.split("_"); //split up the ids
	var sheetId = ids[0];

	//make sure the appropriate sheet tab is highlighted
	//first remove the active class from all sheet tabs
	$("ul#sheetnames li.sheet2 span").removeClass("active");
	//now add active to the one sheet that needs it
	$("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).addClass("active");
	//scroll to the just highlighted sheet
	currentListTop = $("#sheetnames").offset().top;
	if($("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).offset() != null){
		var currentTopOfDisplay = $("#sheetlinks").offset().top;
	
		currentTopOfSelectedItem = $("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).offset().top;

		var delta = (-(currentTopOfSelectedItem-currentTopOfDisplay));
		if(0 != delta)
		{
			scrollSheets(delta);
		}
	}
	$("li.sheet").hide();
	$("li.sheet").removeClass("active");
	$("li.sheet#sheetli_"+sheetId).show();
	$("li.sheet#sheetli_"+sheetId).addClass("active");
	
	dataPtr = mapData.sheets[sheetId]; //get the sheet, because it's different
	
	var currentIndicator = sheetId; // stores the current indicator key as we built it up
	//loop over the remaining indicators
	for(i in ids)
	{
		//skip 0
		if(i!=0)
		{
			var id = ids[i];
			currentIndicator = currentIndicator + "_" + id;
			dataPtr = dataPtr['indicators'][id];

			//now make sure the indicators are shown
			$("span.indicatorLevel_"+i).removeClass("active");
			$("span.indicatorLevel_"+i+"#indicatorSpanId_"+currentIndicator).addClass("active");
			$("ul.indicatorLevel_"+i).hide();
			$("ul.indicatorLevel_"+i+"#indicatorId_"+currentIndicator).show();
		}
		
	}


	//now make sure
	
	
	if(dataPtr != undefined)
	{				
		var title = dataPtr["name"];
		var data =  new Array();
		//setup data to work with the way things used to be when this was CSV driven
		for(areaName in dataPtr.data)
		{
			data[areaName] = parseFloat(dataPtr.data[areaName].value);
		}
		
		var nationalAverage = dataPtr["total"]; 
		var unit = dataPtr["unit"];
		
		var totalLabel = "";
		if (typeof dataPtr["total_label"] != 'undefined')
		{
			totalLabel = dataPtr["total_label"];
		}

		UpdateAreaAllData(title, data, nationalAverage, indicator, unit, totalLabel);
		$.address.parameter("indicator", indicator);
	
		//check and see if there is a source to link to
		if(dataPtr["src"] == "" || dataPtr["src"] == "undefined")
		{
			//if there's no source then hide the source div
			$("#sourcetextspan").hide();
		}
		else
		{
			//update the source link and the source title
			$("#sourcetextspan").show();

			
			//make sure there's a valid link
			if(dataPtr["src_link"] == "" || dataPtr["src_link"] == "undefined")
			{
				//not a valid link
				$("#sourceURL").hide();
				$("#sourceNoURL").show();
				$("#sourceNoURL").text(dataPtr["src"]);
			}
			else
			{
				$("#sourceNoURL").hide();
				$("#sourceURL").show();
				$("#sourceURL").text(dataPtr["src"]);
				$("#sourceURL").attr("title", dataPtr["src"]);
				$("#sourceURL").attr("href", dataPtr["src_link"] );
			}
		}
		
	}
}


/**
* @param string jsonUrl location of the json translator
* @param string jsonDataUrl location of the json file for the map
*/
function parseJsonToGmap(jsonUrl, jsonDataUrl)
{	
	//initalizes our global county point array
	areaPoints = new Array(); 
	
	//initiates a HTTP get request for the json file
	$.getJSON(jsonUrl, function(data) {

		//loops over each entry in the json over "areas"
		for(areaIndex in data["areas"])
		{
			var areaData = data["areas"][areaIndex];
			

			//create an array entry for this county			
			areaName = areaData.area;
			areaPoints[areaName] = new Array();
			
			//creates a list of the place names we've encountered
			geographicAreaNames[areaName] = true;
			
			//now loops over every set of point in the json that defines an area.
			for(pointsSetIndex in areaData.points)
			{
				var pointsSetValue = areaData.points[pointsSetIndex];
				areaPoints[areaName][pointsSetIndex] = new Array();
				//now loop over every point in a set of points that defines an area
				for(pointsIndex in pointsSetValue)
				{
					var pointsValue = pointsSetValue[pointsIndex];
					areaPoints[areaName][pointsSetIndex][pointsIndex] = new google.maps.LatLng(pointsValue[0], pointsValue[1]);
				}
				
				
			}

			//save the center point
			areaCenterPoints[areaName] = new google.maps.LatLng(areaData.marker[0], areaData.marker[1]);
			
			var tempLabel = new Label({map: map});
			tempLabel.set('position', new google.maps.LatLng(areaData.marker[0], areaData.marker[1]));
			tempLabel.set('areaName', areaName);
			labels[areaName] = tempLabel;
		
			
		}
			//now loops over the array of points and creates polygons
			for (var areaName in areaPoints)
			{
				var points = areaPoints[areaName];
				//creates the polygon
					areaGPolygons[areaName] = new google.maps.Polygon({
						paths: points,
						strokeColor: colorProperties.border_color, //sets the line color to defined color
						strokeOpacity: 0.8, //sets the line color opacity to 0.8
						strokeWeight: 2, //sets the width of the line to 3
						fillColor: colorProperties.region_color, //sets the fill color
						fillOpacity: 0.75 //sets the opacity of the fill color
				});
					areaGPolygons[areaName] = new google.maps.Polygon({
						paths: points,
						strokeColor: colorProperties.border_color, //sets the line color to defined color
						strokeOpacity: 0.8, //sets the line color opacity to 0.8
						strokeWeight: 2, //sets the width of the line to 3
						fillColor: colorProperties.region_color, //sets the fill color
						fillOpacity: 0.75 //sets the opacity of the fill color
				});
				
				areaGPolygons[areaName].setMap(map); //places the polygon on the map
				
				//add mouse in
				google.maps.event.addListener(areaGPolygons[areaName], 'mouseover', function(event) {
					 this.setOptions({fillOpacity: 0.95}); 
				});
				
				//add mouse out
				google.maps.event.addListener(areaGPolygons[areaName], 'mouseout', function(event) {
					 this.setOptions({fillOpacity: 0.75}); 
				});
			}	

		parseJsonData(jsonDataUrl);		
	});
}




/**
* Used to update the color of an area given a percentage, min and spread
* @param string name is the name of the area being colored
* @param double percentage is the data of the area looking to be colored
* @param double min is the lowest value given in the data
* @param double spread is the spread from lowest to highest data points
* @param string unit is the unit designated by the user when they created the map
*/
function UpdateAreaPercentage(name, percentage, min, spread, unit)
{
	//calculate the color
	var color = colorProperties.calculateColor(percentage, min, spread);
	
	//update the polygon with this new color
	colorProperties.formatAreaOpacityColor(name, 0.75, color);
	
	//update the labels

	labels[name].set("areaValue", addCommas(percentage)+" "+unit);
	labels[name].draw();
}


/**
* Used to update the color and create the popup info window for a region
* @param string name is the name of the area being colored
* @param double percentage is the data of the area looking to be colored
* @param double min is the lowest value given in the data
* @param double spread is the spread from lowest to highest data points
* @param string unit is the unit designated by the user when they created the map
* @param string id is the id of the region that was clicked
*/
function UpdateAreaPercentageMessage(name, percentage, min, spread, message, unit, id)
{
	//first update the polygon and the marker
	UpdateAreaPercentage(name, percentage, min, spread, unit);
	
	//close all other info windows if they are open
	for(var windowName in infoWindows)
	{
		infoWindows[windowName].close();
	}
	
	//now make up some info windows and click handlers and such
	var infoWindow = new google.maps.InfoWindow({content: message});
	infoWindows[name] = infoWindow;
	
	
	//remove any old listeners
	google.maps.event.clearListeners(areaGPolygons[name], 'click');
	// Add a listener for the click event
	google.maps.event.addListener(areaGPolygons[name], 'click', function(event) {
		//close all other info windows if they are open
		for(var windowName in infoWindows)
		{
			infoWindows[windowName].close();
		}
		//set up the new info window and open it.
		infoWindow.setPosition(event.latLng);
		infoWindow.open(map);
		//draw the data chart inside the window
		DrawDataGraph(id, name);		
	});	
		
}
/**
* Draws graph of data from javascript.flot when user clicks on a location
* @param string id is the id of the region that was clicked
* @param string name is the name of the area being colored
*/
function DrawDataGraph(id, name){
	//remove "200_0_0_0_by_area_chart" from the end of the ID
	var fullId = id;
	var lengthToCut = id.length - "_by_area_chart".length;	
	id = id.substring(0,lengthToCut);
	
	var idArray = id.split("_");
	var regionData = null;

	var dataPtr = mapData.sheets[+idArray[0]];
	for(var i=1; i < idArray.length; i++){
		//that little plus down there converts the strings into actual integers to access data array
		dataPtr = dataPtr.indicators[+idArray[i]];
		if(i == idArray.length - 2){
			regionData = dataPtr;
		}
	}

	//this will account if there is only one level of indicator on the map and just pass in the original array instead of finding the lowest indicators
	var oneLevel = false;
	for(i in mapData.sheets[+idArray[0]].indicators){
		if(mapData.sheets[+idArray[0]].indicators[i].indicators.length == 0){
			oneLevel = true;
		}
		else{
			oneLevel = false;
			break;
		}
	}

	//make regionData the original arrray
	if(oneLevel){
		regionData = mapData.sheets[+idArray[0]];
	}
	
	
	//contains the path given by the id to access the data
	var dataPath = dataPtr.data;

	//console.log(dataPath);

    $("#iChartTabs").tabs();
	
	//draw the general chart
	drawGeneralChart(fullId, dataPath, name);

	//draw the region's response chart
	if(regionData != null){
  		drawRegionChart(regionData, name, idArray[idArray.length - 1]);
	}
}


/*
 * Creates tooltips when hovering over bars on any of the graphs on the map
 * @param int x is the x coordinate of the tooltip
 * @param int y is the y coordinate of the tooltip
 * @param string contents is the message that is displayed
 * @param string backColor is the background color
 * @param string fontColor is the font color of the tooltip
 */
function showTooltip(x, y, contents, backColor, fontColor) {
          $('<div id="tooltip">' + contents + '</div>').css( {
              position: 'absolute',
              display: 'none',
              top: y,
              left: x,
              'z-index': 1000,
              border: '1px solid #000',
              padding: '2px',
              'background-color': backColor,
              'font-color': fontColor,
              opacity: 1
          }).appendTo("body").fadeIn(200);
}


/**
* Draws the chart that is found on the second page of the popup window, showing only the selected regions data
* @param array regionData parsed from the mapData, contains all the values of the indicators
* @param string name of the region selected
* @param int indicatorIdNum to know that this is the region selected
*/
function drawRegionChart(regionData, name, indicatorIdNum){
	var graphYAxis = new Array();
	var selectedArea = new Array(); //Array to hold changes colors for selected area
	var selecY;
	var graphXData = new Array();	
	var tempXData = new Array();
	var tempYAxis = new Array();
	var selecX;
	var count = 1;
	var largest = 0;

	//last indicator level is the one we want the data from
	for(i in regionData.indicators){
		//data level
		for(j in regionData.indicators[i].data){
			if(j == name){
				var value = parseFloat(regionData.indicators[i].data[j].value);
				if(!isNaN(value)){
					if(value > largest){
						largest = value;
					}	
					tempYAxis[i] = regionData.indicators[i].name;
					tempXData[i] = value;
				}
				break;
			}
		}
		
	}
	count = Object.keys(tempYAxis).length;


	for(i in tempYAxis){
		graphYAxis.push([count, tempYAxis[i]]);
		graphXData.push([tempXData[i], count]);
		if(i == indicatorIdNum){
			selecY = count;
			selecX = tempXData[i];
		}	
		
		count --;
	}
			

	for(i=0; i < graphXData.length; i++){
		if(graphXData[i][1] == selecY){
			selecX = graphXData[i][0];
		}
	}
	//fixes an issue with hovertips being reversed, doesn't affect drawing of charts 
	graphXData.reverse();
	graphYAxis.reverse();

	
	var kmapInfochartHeight = calculateBarHeight(graphYAxis.length);

	var dimen = " height: " + kmapInfochartHeight + "px; ";
	var oldStyle = $("#iChartLocal").attr("style");

	$("#iChartLocal").attr("style", dimen + oldStyle);
	selectedArea = [[selecX, selecY]];
	 var bothData = [
	        	  {
				     data: graphXData,
				     bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: colorProperties.graph_color} ,
				     color: colorProperties.graph_color
			       },
			      {
				    data: selectedArea,
				    bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: colorProperties.graph_select_color} ,
			        color: colorProperties.graph_select_color
			      }
	  ];
	  
      /*
      * Size of the chart is controlled by the div tag where iChartFull is created
      */

     //check to see if the region has any data, if not, don't draw the graph
     var data = false;
     for(i in graphXData){
         if(!isNaN(graphXData[i][0])){
             data = true;
             break;
         }
     }
     if(data){
		 $.plot($("#iChartLocal"), bothData,  {
		    	bars: {show: true, horizontal: true, fill: true},
		    	grid: {hoverable: true},
		    	yaxis:{ticks: graphYAxis, position: "left", labelWidth: 72, labelHeight: 20, min:.45, max:graphXData.length + .55},
		    	xaxes:[{panRange: [0, largest]}],
		    	pan:  {interactive: false, cursor: 'move', frameRate: 20}
			}
		);
     }
     else{
		$('iChartLocal').text("No regional data to display.");
     }

	bindHoverTip("#iChartLocal", graphYAxis);

	
	
}

/**
* Draws the graph that is found on the first page of the popup window, detailing the data for all the regions
* @param string fullId is the indicator path to this indicator
* @param array dataPath is the parsed Mapdata array that contains the data for the general chart
* @param string name of the region selected
*/
function drawGeneralChart(fullId, dataPath, name){
	
	var graphYAxis = new Array();
	var selectedArea = new Array(); //Array to hold changes colors for selected area
	var selecY;
	var graphXData = new Array();	
	var selecX;
	var count = 1;
	var largest = 0;

	//create the graph data array and the array of yAxis Names
	for(i in dataPath){
		var value = parseFloat(dataPath[i].value);
		if(!isNaN(value))//make sure we're only dealing with real numbers, not Not-A-Number numbers
		{
			graphYAxis.push([count, i]);
			graphXData.push([value, count]);
			if(name == i){
				selecY = count;
				selecX = value;
			}
			if(value > largest){
				largest = value;
			}
			count++; //increment that counter
		}
	}

	//variables for javascript graph, ynames and xdata
	//add data to graphXData, i indicates location on graph
	for(i=0; i < graphXData.length; i++){
		if(graphXData[i][1] == selecY){
			selecX = graphXData[i][0];
		}
	}
	selectedArea = [[selecX, selecY]];
	var bothData = [
		        	  {
				        	data: graphXData,
				          	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: colorProperties.graph_color} ,
				          	color: colorProperties.graph_color
			        	  },
			        	  {
				        	data: selectedArea,
				        	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: colorProperties.graph_select_color} ,
			        		color: colorProperties.graph_select_color
			        	  }
		  ];
	  
      /*
      * Size of the chart is controlled by the div tag where iChartFull is created
      */

	 $.plot($("#iChartFull"+fullId), bothData,  {
	    	bars: {horizontal: true},
	    	grid: {hoverable: true},
	    	yaxis:{ticks: graphYAxis, labelWidth: 72, min:.45, max:graphXData.length + .55}	    
		}
	);
	bindHoverTip("#iChartFull" + fullId,graphYAxis);
}


/**
* Draws the charts if there are totals present
* @param string indicator is the path to the indicator selected on the left side of the map
*/
//used to draw the national total data chart
function drawTotalChart(indicator){
	var id = indicator.split("_");
	var totalData = new Array();
	var selecY;
	var selecX;
	var tempYAxis = new Array();
	var tempXData = new Array();
	var graphXData = new Array();
	var graphYAxis = new Array();
	var selectedArea = new Array();

	var dataPtr = mapData.sheets[+id[0]];
	for(var i=1; i < id.length; i++){
		//that little plus down there converts the strings into actual integers to access data array
		dataPtr = dataPtr.indicators[+id[i]];
		if(i == id.length - 2){
			totalData = dataPtr;
		}
	}

	if(dataPtr.total == null){
		$("#nationalChartScrollDiv").hide();
		return;
	}

	else{
		$("#nationalChartScrollDiv").show();
	//fill temp arrays with data
		for(i in totalData.indicators){
			var total = parseFloat(totalData.indicators[i].total);
			if(!isNaN(total)){
				tempYAxis.push(totalData.indicators[i].name);
				tempXData.push(total);
			}
		}
	
		count = tempYAxis.length;
	//fill in full arrays so that the chart is as large as it needs to be
		for(i = 0; i < tempYAxis.length; i++){
			graphYAxis.push([count, tempYAxis[i]]);
			graphXData.push([tempXData[i], count]);
			if(i == id[id.length - 1]){
				selecY = count;
				selecX = tempXData[i];
			}	
			
			count --;
		}
	
		//fixes tooltip issue
		graphXData.reverse();
		graphYAxis.reverse();
	
		//attempt to change height and width of nationalIndicatorChart div
		var kmapInfochartHeight = calculateBarHeight(graphYAxis.length);
		//add in a new chart
		$("#nationalIndicatorChart").empty();
		$("#nationalIndicatorChart").height(kmapInfochartHeight);
	
		
		selectedArea = [[selecX, selecY]];
		var bothData = [
			        	  {
				        	data: graphXData,
				          	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: colorProperties.graph_color} ,
				          	color: colorProperties.graph_color
			        	  },
			        	  {
				        	data: selectedArea,
				        	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: colorProperties.graph_select_color} ,
			        		color: colorProperties.graph_select_color
			        	  }
			  ];

		if(graphYAxis.length != 0){
			//$("#nationalIndicatorChart").empty();
			$.plot($("#nationalIndicatorChart"), bothData,  {
		    	bars: {show: true, horizontal: true, fill: true},
		    	grid: {hoverable: true},
		    	yaxis:{ticks: graphYAxis, position: "left", labelWidth: 60, labelHeight: 20, min:.45, max:graphXData.length + .55},
		    	xaxes:[{}],
		    	pan:  {interactive: false, cursor: 'move', frameRate: 20}
				}
			);
		
			bindHoverTip("#nationalIndicatorChart", graphYAxis);
		}
		else{
			$("#nationalChartScrollDiv").hide();
		}
	}
}



/**
* Creates the message and binds a tooltip to the graphs using the name from the array
* @param int id of the bar to hover over
* @param array graphYAxis the data that created the y axis on the graphs
*/
function bindHoverTip(id, graphYAxis){
	$(id).unbind("plothover");
	$(id).bind("plothover", function (event, pos, item) {
		  if (item) { 
	            $("#tooltip").remove(); 				
	            //datapoint is minus 1 as graphYAxis is 0 indexed and datapoint is 1 indexed
	            var hoverName = graphYAxis[item.datapoint[1] - 1][1];
	            
	            showTooltip(pos.pageX, pos.pageY - 27, 'The value of ' + hoverName + ' is ' + item.datapoint[0] + '.', '#FFF', '#000');
		   }
		    else { 
	             $("#tooltip").remove(); 
	           } 
		});
}

/**
* Returns a created height for the divs that contain the graphs to make bars look uniform
* @param int barCount
* @return int what the height should be for the div
*/
function calculateBarHeight(barCount){
	return (barCount * (parseInt(kmapInfochartBarHeight)));
}


/**
* First step in the process for updating data in the title
* @param string Name: Area's name as defined in the JSON that defines areas and their bounds
* @param double Percentage: percentage of X in the given area
* @param double Min: Minimum value of percentages across all areas for baselining the color scale
* @param double Spread: Spread from min to max of percentages across all areas for making the ceiling of the color scale
* @param string Title: Title of the question
* @param array Data: associative array of the percentages keyed by Area names as defined in the JSON that defines areas and their bounds
* @param string indicator is the example (0_0_2) string of the indicators
* @param string unit is the unit designated by the user when the map was created
*/
function UpdateAreaPercentageTitleData(name, percentage, min, spread, title, data, indicator, unit)
{
	var message = '<div class="chartHolder" style="height:'+kmapInfodivHeight+'px">' + createHTMLChart(name, title, data, indicator+"_by_area_chart");
	message += "</div>";
	
	//now call the next method that does work
	UpdateAreaPercentageMessage(name, percentage, min, spread, message, unit, indicator+"_by_area_chart");
}

/**
* Creates the html within the popup window that contains the tabs and the graphs
* @param string Name: Area's name as defined in the JSON that defines areas and their bounds
* @param string Title: Title of the question
* @param array Data: associative array of the percentages keyed by Area names as defined in the JSON that defines areas and their bounds
* @param int id: id of the region currently selected
* @return string chartStr that contains the html used to create the tabs and graphs
*/
function createHTMLChart(name,title, data, id)
{
	
	//now loop through the data and build the rest of the URL
	var count = 0; 
	for(areaName in data)
	{
		//handle non-numbers
		var t = data[areaName]; 
		if(isNaN(t) || typeof t == "undefined" || t == Number.NEGATIVE_INFINITY || t == Number.POSITIVE_INFINITY)
		{
			continue;
		}
		count++;
		//areaName = encodeURIComponent(areaName).replace(/ /g, "+");
	}

	var kmapInfochartHeight = calculateBarHeight(count);
	
	//creates the tab html that contains the chart ids
	var chartStr = '<div id="'+ id + '" class="infowindow"><p class="bubbleheader">' + name + " - " + title +": " + data[name]
	+'</p>' +
	'<div id = "iChartTabs" style= "width: 350px; height: 200px">' +
	  		'<ul>' +
	  			'<li> <a href="#iChartFull">' + title + ' </a> </li>' + 
				'<li> <a href="#iChartLocalTab">' + name + '</a> </li>' +
	  		'</ul>' +
	  		'<div id= "iChartFull" style="height: 140px; overflow-y: auto; overflow-x: hidden">'+
	  			'<div id="iChartFull' + id + '"  style=" width:300px; height:'+kmapInfochartHeight+'px">' + 
	  				'</div>' +
	  			'</div>' +
	  			'<div id="iChartLocalTab" style="height: 140px; overflow-y: auto; overflow-x: hidden">' +
	  		'<div id="iChartLocal" style = " width : 300px; position: relative; padding: 0px">' +
	  			'</div> </div>' + 
	  	'</div> ';
		

	//now put all of that together
	chartStr += '</div>';
	//console.log(chartStr);
	return chartStr;
}

/**
* This takes in a set of data and finds the min and max,
* then uses super complex math to figure out the optimal min and max, 
* like round to nice managable numbers and decide if we should baseline
* off of zero or not
* @param array data is the array that contains all the data points for the map
* @return array that contains the min and span
*/
function calculateMinSpread(data)
{
	//loop over the data to pre process it and figure out the below:
	var min = Infinity; // because we're using percentages we can assume that they'll never be above 100, so 101 is safe
	var max = -Infinity; 
	for(areaName in data)
	{
		data[areaName] = data[areaName];
		//check for min
		if(data[areaName] < min)
		{
			min = data[areaName];
		}
		//check for max
		if(data[areaName] > max)
		{
			max = data[areaName];
		}
	}
	//console.log("max: " + max + " min: " + min);
	//figure out the order of magnitude of max
	var maxMagnitude = calculateMagnitude(max);
	if (maxMagnitude < 1)
	{
		if(max < 0)
		{
			max = 0;
		}
		else
		{
			max = maxMagnitude * 10;
		}
	}
	else if(maxMagnitude == 1)
	{
		max = 10;
	}
	else if(max%maxMagnitude != 0)
	{
		max = (Math.floor(max/maxMagnitude)+1) * maxMagnitude;
	}
	
	//figure out the order of magnitude of max	
	var minMagnitude = calculateMagnitude(min);
	
	if(min == 0)
	{
		min = 0;
	}
	else if (minMagnitude < 1)
	{
		min = Math.floor(min/minMagnitude)*minMagnitude;
		min = parseFloat(min.toFixed(Math.log(10)/Math.log((1.0/minMagnitude)))); //making up for crappy float rounding errors
	}
	else if(minMagnitude == 1)
	{
		min = 0;
	}
	else if(min%minMagnitude != 0)
	{
		min = Math.floor(min/minMagnitude) * minMagnitude;
	}

	//now we decide if we want to base line off zero
	//We don't baseline off zero if the min is negative and the max is positive
	if(!(min < 0 && max > 0))
	{
		//now we want to figure out if max or min is closer to zero
		var closerToZero = min;
		if(max < 0)
		{ //max is closer
			closerToZero = max;
		}
		//now we see if the number closer to zero, is further from zero than max is from min. and if the absolute
		//value of the number involved are more than 1000
		
		if(!(Math.abs(closerToZero) > Math.abs(max-min) && Math.abs(closerToZero) > 1000))
		{
			if(max > 0)
			{
				min = 0;
			}
			else
			{
				max = 0;
			}
		}
		
	}
	
	//calculate the spread
	var spread = max - min;
	
	if(Math.abs(spread) < 1)
	{
		round = false;
	}
	else
	{
		round = true;
	}
	
	var retVal = new Array();
	retVal["min"] = min;
	retVal["spread"] = spread;

	return retVal;
}

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
function UpdateAreaAllData(title, data, nationalAverage, indicator, unit, totalLabel)
{
	
	//first zero out all the current map data. Do this incase certain indicators don't apply to all geographic areas
	zeroOutMap();

	var minSpread = calculateMinSpread(data);
	var min = minSpread["min"];
	var spread = minSpread["spread"];
	
	//loop over all our data
	for(areaName in data)
	{
		if(!isNaN(data[areaName]) && typeof labels[areaName] != 'undefined')
		{
			UpdateAreaPercentageTitleData(areaName, data[areaName], min, spread, title, data, indicator, unit);
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

		//total label -- defaults to "Total"
		if(typeof totalLabel == "undefined")
		{
			totalLabel = "";
		}
		//totalLabel = "Total";
		
		updateNationalAverage(min, spread, nationalAverage, unit, indicator, totalLabel);
	}
	else
	{
		$("#nationalaveragediv").hide();
		$("#nationalIndicatorChart").hide();
	}

	//draw the javascript graph of the totals of the selected indicator stack
	drawTotalChart(indicator);
	
	if(title == "<?php __("Please select an indicator to display its data.") ?>" )
	{
		$('#sourcetextspan').hide()
		//Show the gradient div
		//$('#legend_gradient').show();
	}
}

/**
* Figures out the magnitude 
* @param int num number to be calculated
* @return float magnitude that was calculated
*/
function calculateMagnitude(num)
{
	if(isNaN(num) || num == Number.POSITIVE_INFINITY || num == Number.NEGATIVE_INFINITY)
	{
		return 0;
	}
		
	num = Math.abs(num);
	//is this number equal to or greater than 1?
	if(num == 0)
		return 0;
	
	else if(num >= 1)
	{
		var magnitude = 0;
		for (var i = 1; i <= num; i = i * 10)
		{
			if(num - i < ((i*10)-(1*i)))
			{				
				magnitude = i;
				break;
			}
		}
		return magnitude;
	}
	else
	{ //it's a decimal value
		var magnitude = 0.1;
		for (magnitude = 0.1; (num - magnitude) < 0; magnitude = magnitude / 10)
		{
			if(magnitude < 0.00000001)
				{
					break;
				}
		}		
		return magnitude;
	}
}

/**
* This takes in the min score, the spread between the min and the max, and the national average
* and then updates the nationalaveragediv element
* @param double Min: Minimum value of percentages across all areas for baselining the color scale
* @param double Spread: Spread from min to max of percentages across all areas for making the ceiling of the color scale
* @param double nationalAverage: average of the values for the selected region
* @param string unit is the unit designated by the user when the map was made
* @param string indicator is the example (0_0_2) string of the indicator path
* @param string totalLabel is the name for the totals that is designated by the user
*/
function updateNationalAverage(min, spread, nationalAverage, unit, indicator, totalLabel)
{
	////////////////////////////////////////////////////////////////
	//displays the box container
	$('#nationalaveragediv').show();

	////////////////////////////////////////////////////////////////
	//updates the key
	////////////////////////////////////////////////////////////////
	//set the color
	var color = colorProperties.calculateColor(nationalAverage, min, spread);
    
	$("#nationalaveragediv").css("background-color", color);
	$("#nationalaverageimg").text(addCommas(nationalAverage)+" "+htmlDecode(unit));

	$("#nationalaveragelabel").html(totalLabel);

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
			colorProperties.formatAreaOpacityColor(areaName, 0.75, colorProperties.region_color);
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

/**
* Used to convert decimal numbers to hex
* @param double d is the decimal to be converted
* @param int padding to be included
* @return string hex that was created
*/
function decimalToHex(d, padding) {
	d = Math.round(d);
	var hex = Number(d).toString(16);
	padding = typeof (padding) === "undefined" || padding === null ? padding = 2 : padding;

	while (hex.length < padding) {
		hex = "0" + hex;
	}

	return hex;
}

/**
* Encodes a string to html
* @param string value to be encoded
* @return string of html
*/
function htmlEncode(value){
	var retVal = value;
	// the .text() method escapes everything nice and neat for us.
	return $('<div/>').text(retVal).html();
}

/**
* Decodes a string from html
* @param string value to be decoded
* @return string text that was decoded
*/
function htmlDecode(value){
	var retVal = value;
	// the .text() method escapes everything nice and neat for us.
	return $('<div/>').html(retVal).text();
}

//function needs to be escaped to work with Drupal where the $ character is reserved

/**
* Starts upon load and starts the indicator where last seen
*/
(function(j) { 
		j(function() {
			$.address.externalChange(function(event) {  
				var indicator = $.address.parameter("indicator");
				if(indicator != undefined && typeof mapData !== "undefined" && typeof mapData.sheets !== "undefined")
				{
					showByIndicator(indicator);					
				}

			});  
		});
})(jQuery);


/**
* Adds commas into strings
* @param string nStr to be parsed
* @return new string that has commas
*/
function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}


/**
 * Sets up scrolling of the longitudenal data, AKA sheets
 * 
 * Also setup these two global variables for storing how far down we've scrolled
 */
 
 var sheetsHeight = 0;
function setup_scrolling()
{
	sheetsHeight = ($("#sheetnames").offset().top)-($(".lastSheetSelector").offset().top);
	if(sheetsHeight < 0) //only show all of this if there's somewhere to scroll to
	{
	$("#sheetnamesRightControl a").click(function(){scrollSheets(-$("#sheetnames").height()); return false;});
	$("#sheetnamesLeftControl a").click(function(){scrollSheets($("#sheetnames").height()); return false;});

	$("#sheetnamesStartControl a").click(function(){
		var currentTopOfDispaly = $("#sheetlinks").offset().top;
		var currentListTop = $("#sheetnames").offset().top;
		var actualOffset = currentListTop - currentTopOfDispaly;
		var newOffset = -actualOffset;
		scrollSheets(newOffset);
		 return false;
	});

	
	$("#sheetnamesEndControl a").click(function(){
		var currentTopOfDispaly = $("#sheetlinks").offset().top;
		var currentListTop = $("#sheetnames").offset().top;
		var actualOffset = currentListTop - currentTopOfDispaly;
		var newOffset = sheetsHeight - actualOffset;
		scrollSheets(newOffset);
		return false;
	});
		
	scrollSheets(0); //initialize things
	}
	else
	{
		$("#sheetnamesRightControl a").hide();
		$("#sheetnamesLeftControl a").hide();
		$("#sheetnamesStartControl a").hide();
		$("#sheetnamesEndControl a").hide();
	}
}


/**
 * Handle scrolling the list of longitudenal data, or sheets, down.
 * @param delta int - How far up or down to scroll
 */
function scrollSheets(delta)
{
	//Get some base lines
	var increment = $("#sheetnames").height();
	var currentTopOfDisplay = $("#sheetlinks").offset().top;
	var currentListTop = $("#sheetnames").offset().top;
	var actualOffset = currentListTop - currentTopOfDisplay;
	
	//make sure actualOffset is a multiple of increment
	if(actualOffset % increment != 0)
	{
		actualOffset = Math.round(actualOffset/increment);
	}
	
	if(delta > 0 && (actualOffset + delta) <= 0 ) //scrolling up
	{
		actualOffset += delta;
		$("#sheetnames").animate({top:actualOffset},300);
	}
	else if (delta < 0 && (actualOffset + delta) >= sheetsHeight) //scrolling down
	{
		actualOffset += delta;
		$("#sheetnames").animate({top:actualOffset},300);
	}


	
	//make the scroll up, button inactive if need be
	if(actualOffset == 0)
	{
		$("#sheetnamesLeftControl a").addClass('inactive');
		$("#sheetnamesStartControl a").addClass('inactive');
	}
	else
	{
		$("#sheetnamesLeftControl a").removeClass('inactive');
		$("#sheetnamesStartControl a").removeClass('inactive');
	}
	if(actualOffset == sheetsHeight)
	{
		$("#sheetnamesRightControl a").addClass('inactive');
		$("#sheetnamesEndControl a").addClass('inactive');
	}
	else
	{
		$("#sheetnamesRightControl a").removeClass('inactive');
		$("#sheetnamesEndControl a").removeClass('inactive');
	}
	

}

//will change the heights of the maplinks div on the left side of the map page
function resizeMaplinks(){
	var height = $(window).height();

	if($("#siteHeader").is(':visible')){
		$("#maplinks").height(parseInt(height - 280));
		$("#nationalChartScrollDiv").height(parseInt(height * .20));
		$("#questionsindicators").height(parseInt(height * .19));
		if(<?php echo strlen($map->description)?> > 0){
			$("#descriptionText").height(parseInt(height * .10));
		}
	}
	else{
		$("#maplinks").height(height);
		$("#nationalChartScrollDiv").height(parseInt(height * .30));
		$("#questionsindicators").height(parseInt(height * .33));
		if(<?php echo strlen($map->description)?> > 0){
			$("#descriptionText").height(parseInt(height * .15));
		}
	}
}

/**
 * This function is called to initialize the event handlers for the buttons on this page,
 * like the share button and the fullscreen button
 */
 var headerOffset = 0;
function initialize_buttons()
{	
	//handle toggling between full screen and normal view
	$("#fullScreenButton").click(function(){
			$("#siteHeader").toggle();
			resizeMaplinks();
			return false;
		});

	//handle turning off and on the labels on the map
	$("#turnOffLabelsButton").click(function(){
		Label.renderLabelNames = !Label.renderLabelNames; 
		$("#turnOffLabelsButton").toggleClass("active");

		if($("#turnOffLabelsButton").text() == 'Hide Labels'){
			$("#turnOffLabelsButton").text("<?php echo __("Show Labels"); ?>");
		}
		else{
			$("#turnOffLabelsButton").text("<?php echo __("Hide Labels"); ?>");
		}
		//redraw all the labels 
		for(i in labels)
		{
			labels[i].draw();
		} 
		return false;
		});

	//hanndle turning on and off values on the map
	$("#turnOffValuesButton").click(function(){
		Label.renderLabelVals = !Label.renderLabelVals; 
		$("#turnOffValuesButton").toggleClass("active");
		
		if($("#turnOffValuesButton").text() == 'Hide Values'){
			$("#turnOffValuesButton").text("<?php echo __("Show Values"); ?>");
		}
		else{
			$("#turnOffValuesButton").text("<?php echo __("Hide Values"); ?>");
		}
		//redraw all the labels 
		for(i in labels)
		{
			labels[i].draw();
		}
		return false;
		});

	$("#turnOffLabelsButton").tooltip( {
		position:{
			my: "left+45 center-20",
			at: "center top"	
		}
	});
	$("#turnOffValuesButton").tooltip( {
		position:{
			my: "left+45 center-20",
			at: "center top"	
		}
	});
	$("#commentButton").tooltip( {
		position:{
			my: "left+15 center-19",
			at: "center top"	
		}
	});
	$("#shareButton").tooltip( {
		position:{
			my: "left+14 center-19",
			at: "center top"	
		}
	});
	$("#fullScreenButton").tooltip( {
		position:{
			my: "left+14 center-19",
			at: "center top"	
		}
	});


	$("#playButton").tooltip( {
		position:{
			my: "left+14 center-19",
			at: "center top"	
		}
	});

	$("#pauseButton").tooltip( {
		position:{
			my: "left-134 center-19",
			at: "center top"	
		}
	});
	
	$("#speedVal").tooltip( {
		position:{
			my: "left+24 center-22",
			at: "center top"	
		}
	});
	


	//initialize the apple overlay effect
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
	
}

/**
* creates the listener on the +/- sign on the legend to minimize it or not
*/
function init_legend_listener(){
	$("#minButtonLegend").click(function(){
		if($("#legendMinDiv").is(":visible")){
			$("#legendMinDiv").toggle();
			$("#minButtonLegend").html("+");
		}
		else {
			$("#legendMinDiv").toggle();
			$("#minButtonLegend").html("-");
		}
	});	
	$(window).resize(function(){
		resizeMaplinks();
	});
	
	resizeMaplinks();
}

</script>
	
<?php 
	$facebook_js = new View('js/facebook');
	$shareCenter = new View('js/shareEdit');
	echo $facebook_js; 
	echo $shareCenter;
?>
