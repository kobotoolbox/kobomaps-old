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
<style> <?php echo $map->CSS?></style>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.min.js"> </script>

<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/dragresize.js"> </script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/label.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/flot/jquery.flot.navigate.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.tools.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery-ui.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.address-1.4.min.js"> </script>
<script type="text/javascript">


/* link to the stylesheet for the tabs used in chart windows *
 <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
 */

/*Todo: Make all these setings on the website:*/
var kmapInfodivHeight = 280;
//modify the base setting for the Google chart here (if necessary)
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

//add a title to the map 
	$(document).ready(function() {
	   $("#kmapTitle").html(<?php echo json_encode($map->title);?>);
	   
	   initialize_map();
	   initialize_buttons();
	});


//itintializes everything, both the mandatory google maps stuff, and our totally awesome json to gPolygon code
function initialize_map() {

	  
	  //setup drag stuff for the key
	  var dragresize = new DragResize('dragresize',
			  { allow_resize: false, minLeft: 350, minTop:40});
	  
	  
	  dragresize.isElement = function(elm)
	  {
	   if (elm.className && elm.className.indexOf('drsElement') > -1) return true;
	  };
	  dragresize.isHandle = function(elm)
	  {
	   if (elm.className && elm.className.indexOf('drsMoveHandle') > -1) return true;
	  };
	  
	  dragresize.apply(document);
	  //set the key to be 48 pixels from the bottom like it used to be. 
	  //we can't use bottom when dragging. We can only use top
	  var height = $("#topbar").height();
	  var screenHeight = $(window).height();
	   var top = screenHeight - (height + 48); 
	   $("#topbar").css("top", top+"px");
	  
	  
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
	});		
}//end parseJsonData function




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
					//we have a match show this indicator
					newIndicatorIdString += i;
					showByIndicator(newIndicatorIdString);
					return;
				}
			}			
		}//end if the two sheets don't match
	}//end 	if(!sheetButton.hasClass("active"))
	
}//end function



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
		currentTopOfSelectedItem = $("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).offset().top
		scrollSheets(-(currentTopOfSelectedItem-currentListTop));
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
				strokeColor: "#00CC00", //sets the line color to red
				strokeOpacity: 0.8, //sets the line color opacity to 0.8
				strokeWeight: 2, //sets the width of the line to 3
				fillColor: "#aaaaaa", //sets the fill color
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
* Function to be called from the HTML to specify a new opacity and/or color value for a county
* countyName - name of the county as defined in the json file
* opacityValue - number between 1.0 and 0.0
* colorValue - html color value, in the form "#RRGGBB" such as "#ff0000" which is red
*/
function formatAreaOpacityColor(name, opacityValue, colorValue)
{
	if(typeof areaGPolygons[name] != "undefined")
	{
		areaGPolygons[name].setOptions({
					fillColor: colorValue,
					fillOpacity: opacityValue
				});
	}
}

/**
* Given the percentage in question, the min percentage value, and the spread between
* the min percentage and the max, this function returns back your color as a
* string in the form "#RRGGBB"
*/
function calculateColor(percentage, min, spread)
{
	//calculate the color
	var red = 255;
	var blue = 255 - ((percentage-min)*(1/spread)*255);
	var green = 255 - ((percentage-min)*(1/spread)*255);
	var color = "#"+decimalToHex(red,2)+decimalToHex(green,2)+decimalToHex(blue,2);
	
	return color;
}


/**
Used to update the color of an area given a percentage, min and spread
*/
function UpdateAreaPercentage(name, percentage, min, spread, unit)
{
	//calculate the color
	var color = calculateColor(percentage, min, spread);
	
	//update the polygon with this new color
	formatAreaOpacityColor(name, 0.6, color);
	
	//update the labels

	labels[name].set("areaValue", addCommas(percentage)+" "+unit);
	labels[name].draw();

}

/**
Used to update the color and info window of an area
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
		DrawDataGraph(id, name);		
	});	
		
}
/**
Draws graph of data from javascript.flot when user clicks on a location
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

/* Default setting for tooltips: not showing */
//var toolTipShow = false;

/*
 * Controls the information that appears when clicking on a bar within the pop-up graph
 */
function showTooltip(x, y, contents) {
          $('<div id="tooltip">' + contents + '</div>').css( {
              position: 'absolute',
              display: 'none',
              top: y - 27,
              left: x,
              'z-index': 1000,
              border: '1px solid #000',
              padding: '2px',
              'background-color': '#FFF',
              opacity: 1
          }).appendTo("body").fadeIn(200);
}

//regionData should be the second to last indicator
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

	//last indicator level
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
		          	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: "rgb(34,57,83)"} ,
		          	color: "rgb(34,57,83)"
	        	  },
	        	  {
		        	data: selectedArea,
		        	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: "rgb(215, 24, 24)"} ,
	        		color: "rgb(215, 24, 24)"
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
		    	yaxis:{ticks: graphYAxis, position: "left", labelWidth: 60, labelHeight: 20, min:0, max:graphXData.length+1},
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
			          	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: "rgb(34,57,83)"} ,
			          	color: "rgb(34,57,83)"
		        	  },
		        	  {
			        	data: selectedArea,
			        	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: "rgb(215, 24, 24)"} ,
		        		color: "rgb(215, 24, 24)"
		        	  }
		  ];
	  
      /*
      * Size of the chart is controlled by the div tag where iChartFull is created
      */

	 $.plot($("#iChartFull"+fullId), bothData,  {
	    	bars: {horizontal: true},
	    	grid: {hoverable: true},
	    	yaxis:{ticks: graphYAxis, labelWidth: 60, min:0, max: graphXData.length + 1}	    
		}
	);
	bindHoverTip("#iChartFull" + fullId,graphYAxis);
}


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
		$("#nationalChartScrollDiv").height(130);
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
				          	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: "rgb(34,57,83)"} ,
				          	color: "rgb(34,57,83)"
			        	  },
			        	  {
				        	data: selectedArea,
				        	bars: {show: true, barWidth: .80, align: "center", fill:true, fillColor: "rgb(215, 24, 24)"} ,
			        		color: "rgb(215, 24, 24)"
			        	  }
			  ];
	
		//$("#nationalIndicatorChart").empty();
		$.plot($("#nationalIndicatorChart"), bothData,  {
	    	bars: {show: true, horizontal: true, fill: true},
	    	grid: {hoverable: true},
	    	yaxis:{ticks: graphYAxis, position: "left", labelWidth: 60, labelHeight: 20, min:0, max:graphXData.length},
	    	xaxes:[{}],
	    	pan:  {interactive: false, cursor: 'move', frameRate: 20}
			}
		);
	
		bindHoverTip("#nationalIndicatorChart", graphYAxis);
	}
}



//used by all charts to create the hover tooltip that finds the bar that is being hovered over
function bindHoverTip(id, graphYAxis){
	$(id).unbind("plothover");
	$(id).bind("plothover", function (event, pos, item) {
		  if (item) { 
	            $("#tooltip").remove(); 				
	            //datapoint is minus 1 as graphYAxis is 0 indexed and datapoint is 1 indexed
	            var hoverName = graphYAxis[item.datapoint[1] - 1][1];
	            
	            showTooltip(pos.pageX, pos.pageY, 'The value of ' + hoverName + ' is ' + item.datapoint[0] + '.');
		   }
		    else { 
	             $("#tooltip").remove(); 
	           } 
		});
}


function calculateBarHeight(barCount){
	var temp = (barCount * (parseInt(kmapInfochartBarHeight))) ;
	return temp; //(barCount * (parseInt(kmapInfochartBarHeight)));
}
/**
* Name: Area's name as defined in the JSON that defines areas and their bounds
* Percentage: percentage of X in the given area
* Min: Minimum value of percentages across all areas for baselining the color scale
* Spread: Spread from min to max of percentages across all areas for making the ceiling of the color scale
* Title: Title of the question
* Data: associative array of the percentages keyed by Area names as defined in the JSON that defines areas and their bounds
*/
function UpdateAreaPercentageTitleData(name, percentage, min, spread, title, data, indicator, unit)
{
	//var num
	
	var message = '<div class="chartHolder" style="height:'+kmapInfodivHeight+'px">' + createHTMLChart(name, title, data, indicator+"_by_area_chart");
		
	//create the chart by for all the indicators of the given question, assuming there's more than one
	//createChartByIndicators(title, data);
	
	message += "</div>";
	
	//now call the next method that does work
	UpdateAreaPercentageMessage(name, percentage, min, spread, message, unit, indicator+"_by_area_chart");
	
}


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
	var chartStr = '<div id="'+ id + '" class="infowindow"><p class="bubbleheader">' + name + " - " + title +": "+data[name]
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
/*function createChart(title, data, highLightName, id, unit, min, spread){
	var blues = "";
	var reds = "";
	var numberDelim = ",";
	 
	var nameDelim = "|";
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

		
		if(count > 1) //if we're doing this more than once
		{
			blues += numberDelim;
			reds += numberDelim;
		}
		//handle the special case of the named area being the area who's data we're looping over
		if(areaName.toUpperCase() == highLightName.toUpperCase())
		{
			blues += "0";
			reds += data[areaName];			
		}
		else
		{			
			blues += data[areaName];
			reds += "0";
		}
		
		//for whatever reason the data names and the data values are in reverse order
		areaName = encodeURIComponent(areaName).replace(/ /g, "+");
		
		names = nameDelim + areaName + names;
	}
		
	//setup the height
	var kmapInfochartHeight = (count * (parseInt(kmapInfochartBarHeight) + parseInt(kmapInfochartBarHeightMargin))) + Math.round(parseInt(kmapInfochartchxsFont) * 1.7);


	var kmapInfochart_temp = kmapInfochart.replace("<HEIGHT>", kmapInfochartHeight);
	//setup the range
	var kmapInfoChartRange = "0,100,0,100";
	if(unit != "%" || (unit == "%" && min < 0))
	{
		kmapInfoChartRange = min+","+(min+spread)+","+min+","+(min+spread);
	}
	
	var kmapInfochart_temp = kmapInfochart_temp.replace("<RANGE>", kmapInfoChartRange);
	
	//setup the range labels
	var kampInfoChartRangeLabels ="0|25|50|75|100";
	if(unit != "%" || (unit == "%" && min < 0))
	{
		kampInfoChartRangeLabels = min+"|"+
			(min+(spread*.25))+"|"+
			(min+(spread*.5))+"|"+
			(min+(spread*.75))+"|"+
			(min+spread);
		//toFixed(Math.log(10)/Math.log((1.0/minMagnitude)))
	}
	else
	{
		//not sure what's supposed to go here
	}
	
	var kmapInfochart_temp = kmapInfochart_temp.replace("<RANGE_LABELS>", kampInfoChartRangeLabels);

	
	var chartStr = '<div id="'+id+'" class="infowindow"><p class="bubbleheader">'+title
		+'</p><div id="iChart' + id + '"  style=" width:300px; height:'+kmapInfochartHeight+'px"></div>';

		the code below is the code that's needed to create the image based charts
		chartStr += '<img src="'+kmapInfochart_temp; //This is the base of the Google Chart API graph (without the data part). Needs to be defined in the container file.
		chartStr += names + '&chd=t:' + blues + nameDelim + reds + '" height="' + kmapInfochartHeight + '" width="' + kmapInfochartWidth + '" />
		
	//now put all of that together
	chartStr += '</div>';
	//console.log(chartStr);
	return chartStr;
	
}
*/

/**
* This takes in a set of data and finds the min and max,
* then uses super complex math to figure out the optimal min and max, 
* like round to nice managable numbers and decide if we should baseline
* off of zero or not
* then returns back an array with keys span and min
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
	//console.log("min: "+min + " Spread: "+spread);
	return retVal;
}

/**
* Updates all data for all areas
* Data: associative array of the percentages keyed by Area names as defined in the JSON that defines areas and their bounds
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
	updateKey(min, spread, title, unit);
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
	
	if(title == "Please select an indicator to display its data.")
	{
		$('#sourcetextspan').hide()
		//Show the gradient div
		//$('#legend_gradient').show();
	}

	

}

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
	var color = calculateColor(nationalAverage, min, spread);	
	$("#nationalaveragediv").css("background-color", color);
	$("#nationalaverageimg").text(addCommas(nationalAverage)+" "+htmlDecode(unit));

	$("#nationalaveragelabel").html(totalLabel);
/*
	////////////////////////////////////////////////////////////////
	//updates the national average chart
	////////////////////////////////////////////////////////////////
	//first check if there's more than one answer to the given question
	if($("#bottom_level_"+indicator).siblings().length == 0)
	{
		//clear out the National Chart
		$("#nationalIndicatorChart").html("");
		return;
	}
	//there is more than one answer ...as so many questions have.
	
	//get the data for those questions
	var dataForNational = new Array();
	var mainIndicatorText = $("#bottom_level_"+indicator).text(); 
	var questionText = $("#bottom_level_"+indicator).parents("li.level2").children("span.level2").text();
	//get the data for the indicator we're focused on
	
	dataForNational[mainIndicatorText] = indicatorsToUpdateParams[indicator]["nationalAverage"];
	
	//get the rest of the data
	$.each($("#bottom_level_"+indicator).siblings(), function() {			
		var otherIndicator = $(this);
		var otherIndicatorId = otherIndicator.attr("id").substring(13);
		var indicatorText = otherIndicator.text();
		var otherNationalAverage = indicatorsToUpdateParams[otherIndicatorId]["nationalAverage"]; 	
		if(!isNaN(otherNationalAverage))
		{
			dataForNational[indicatorText] =indicatorsToUpdateParams[otherIndicatorId]["nationalAverage"]; 	
		}
	});
	
		
	//calculate the min and spread for the national graph specific
	var spreadMin = calculateMinSpread(dataForNational);
	var min = spreadMin["min"];
	var spread = spreadMin["spread"];
	//var nationalChart = createHTMLChart(questionText + ' ('+kmapAllAdminAreas+')', dataForNational, indicator+"_by_indicator_national_chart");
	
	//TODO: fix the national chart
	//$("#nationalIndicatorChart").html(nationalChart);
*/
}

//changed this to take another input 
//TODO -> make sure this doesn't mess anything up
function updateKey(min, span, title, unit)
{ 
	if(isNaN(min) || isNaN(span)){
		$('#legend_gradient').hide();
	}
	else
	{
		$("#percentleft").attr("title", addCommas(min)+" "+htmlDecode(unit));
		$("#percentleft").text(addCommas(min)+" "+htmlDecode(unit));
		
		$("#percentright").attr("title", addCommas((min+span))+" "+htmlDecode(unit));
		$("#percentright").text(addCommas((min+span))+" "+htmlDecode(unit));

		$('#legend_gradient').show();
	}

	$("#spanLegendText").html(title);
}

/**
This little function just goes through and whipes clean the areas on the map
and their charts and so forth
*/
function zeroOutMap()
{
	//loop over the polygons and set the colors to not-set
	for(areaName in areaGPolygons)
	{
		//set the polygon back to default colors
		formatAreaOpacityColor(areaName, 0.75, "#aaaaaa");
		//set the label to blank("")

		labels[areaName].set("areaValue", "");
		<?php if($map->show_empty_name == 1){?>
		labels[areaName].set('show_empty_name'	, true);
		<?php }else{ ?>
		labels[areaName].set('show_empty_name', false);
		<?php }?>
		labels[areaName].draw();
		//remove any old listeners pop-up listeners
		google.maps.event.clearListeners(areaGPolygons[areaName], 'click');
		
	}
}

/**
Used to conver decimal numbers to hex
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

function htmlEncode(value){
	var retVal = value;
	// the .text() method escapes everything nice and neet for us.
	return $('<div/>').text(retVal).html();
}

function htmlDecode(value){
	var retVal = value;
	// the .text() method escapes everything nice and neet for us.
	return $('<div/>').html(retVal).text();
}

//function needs to be escaped to work with Drupal where the $ character is reserved


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

/*
function stripString(str) 
{
  return str.replace(/^\s+|\s+$/g, '');
};


function is_array(input)
{
	return typeof(input)=='object'&&(input instanceof Array);
}
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
	var currentTopOfDispaly = $("#sheetlinks").offset().top;
	var currentListTop = $("#sheetnames").offset().top;
	var actualOffset = currentListTop - currentTopOfDispaly;
	
	//make sure actualOffset is a multiple of increment
	if(actualOffset % increment != 0)
	{
		actualOffset = (actualOffset/increment).round();
	}
	if(actualOffset == delta)
	{
		//do nothing
		return;
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


/**
 * This function is called to initialize the event handlers for the buttons on this page,
 * like the share button and the fullscreen button
 */
 var headerOffset = 0;
function initialize_buttons()
{
	//hanlde toggling between full screen and normal view
	$("#fullScreenButton").click(function(){$("#siteHeader").toggle(); return false;});

	//handle turning off and on the labels on the map
	$("#turnOffLabelsButton").click(function(){
		Label.renderLabelNames = !Label.renderLabelNames; 
		$("#turnOffLabelsButton").toggleClass("active");
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
		//redraw all the labels 
		for(i in labels)
		{
			labels[i].draw();
		}
		return false;
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



</script>
	
<?php 
	$facebook_js = new View('js/facebook');
	echo $facebook_js; 
?>
