<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* mapview_js.php - View
* Writen by Etherton Technologies Ltd. 2012
* Started on 2012-11-27
*************************************************************/
?>
	
	

<link href="<?php echo  URL::base() ?>media/css/templatePreview.css" type="text/css" rel="stylesheet">
<link href="<?php echo  URL::base() ?>media/css/largemap.css" type="text/css" rel="stylesheet">  
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jquery.address-1.4.min.js"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/dragresize.js"> </script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/label.js"> </script>
<script type="text/javascript">



/*Todo: Make all these setings on the website:*/
var kmapInfodivHeight = 300;
//modify the base setting for the Google chart here (if necessary)
var kmapInfochartWidth = 315; //if this number is changed, the legend div (which contains the national graph) also needs to be adjusted 
var kmapInfochartBarHeight = 10; //these are numbers, not strings
var kmapInfochartBarHeightMargin = 2;
var kmapInfochartchxsFont = 10;
var kmapInfochart = 'http://chart.apis.google.com/chart?'
+ 'chxs=0,676767,'+kmapInfochartchxsFont+',2,l,676767|1,393939,'+kmapInfochartchxsFont+',1,l,676767'
+ '&chxt=x,y'
+ '&chbh='+kmapInfochartBarHeight+','+kmapInfochartBarHeightMargin+',0'
+ '&chs='+kmapInfochartWidth+'x<HEIGHT>'
+ '&cht=bhs'
+ '&chco=3E4E6E,CC0000'
+ '&chds=<RANGE>'
+ '&chts=000000,13'
+ '&chxl=0:|<RANGE_LABELS>|1:';





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
	   $("#kmapTitle").html('<?php echo $map->title;?>');
	   
	   initialize_map();
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
	  // var top = screenHeight - (height + 48); 
	  // $("#topbar").css("top", top+"px");
	  
	  
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


	var mapStyles = 
		[
		  {
			featureType: "administrative.province",
			elementType: "all",
			stylers: [
			  { visibility: "off" }
			]
		  },{
			featureType: "poi",
			elementType: "all",
			stylers: [
			  { visibility: "off" }
			]
		  },{
			featureType: "road",
			elementType: "all",
			stylers: [
			  { visibility: "off" }
			]
		  },{
			featureType: "landscape",
			elementType: "geometry",
			stylers: [
			  { lightness: -60 },
			  { hue: "#91ff00" },
			  { visibility: "on" },
			  { saturation: -60 }
			]
		  },{
			featureType: "administrative.locality",
			elementType: "all",
			stylers: [
			  { saturation: -50 },
			  { invert_lightness: true },
			  { lightness: 52 }
			]
		  }
		]; 



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

	
	//Calling the boundaries and data files. The variables need to be defined in the container file as they are country-specific
	parseJsonToGmap('<?php echo URL::base() .'uploads/templates/'. $template->file; ?>', '<?php echo URL::base() .'uploads/data/'. $map->json_file; ?>');
	


	
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

		//loop over the sheets and create HTML for them
		for(sheetId in mapData.sheets)
		{
			var sheet = mapData.sheets[sheetId];			
			$("#questionsindicators").append('<li class="sheet"><span class="sheet" >'+sheet.sheetName+'</span><ul id="indicatorId_'+sheetId+'" class="sheet"></ul></li>');
			depthOfData = parseIndicators(sheetId, sheet.indicators, 1);
		}
		
		
		
		// Controling the click behavior of the sheets
		$('span.sheet').click(function (){
			sheetClick($(this));
		});
		$('ul.sheet').hide(); //This hides all ul level1 by default until they are toggled. Can also be defined in css.

		//control the clicking behavior of the indicator levels that lead to the data
		for(var i = 1; i < depthOfData; i++)
		{
			$('span.indicatorLevel_'+i).click(function (){
				midIndicatorClick($(this));
			});
			$('ul.indicatorLevel_'+i).hide(); //This hides all ul level1 by default until they are toggled. Can also be defined in css.			
		}
		//control the clicking behavior of the data level
		$('span.indicatorLevel_'+depthOfData).click(function (){
			dataIndicatorClick($(this));
		});
		
		//this should be done in CSS for sure	
		$("li span").hover(function () {
			$(this).addClass("hover");
		}, function () {
			$(this).removeClass("hover");
		});
		
		//check if we're supposed to auto load the data for a particular indicator?
		var autoLoadIndicator = $.address.parameter("indicator");
		if( autoLoadIndicator != "")
		{
			showByIndicator(autoLoadIndicator);
		}
		
		//hide the temporary loading text once the indicators are visible
		$('#loadingtext').remove();
	});		
}//end parseCSV function

	function sheetClick(sheetItem, forceOn)
	{
		if(forceOn != undefined && forceOn == false)
		{
			sheetItem.removeClass("active"); //highlights active span
			sheetItem.siblings("ul.sheet").hide(); //This shows the child ul level1 element
		}
		else if (forceOn != undefined && forceOn == true)
		{
			sheetItem.addClass("active"); //highlights active span
			sheetItem.siblings("ul.sheet").show(); //This shows the child ul level1 element
		}
		else if(sheetItem.hasClass("active"))
		{
			sheetItem.removeClass("active"); //highlights active span
			sheetItem.siblings("ul.sheet").hide(); //This shows the child ul level1 element
		}
		else 
		{
			sheetItem.addClass("active"); //highlights active span
			sheetItem.siblings("ul.sheet").show(); //This shows the child ul level1 element
		}
	}

	function midIndicatorClick(indicatorItem, forceOn)
	{
		if(forceOn != undefined && forceOn == false)
		{
			indicatorItem.removeClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").hide(); //This shows the child ul level1 element
		}
		else if (forceOn != undefined && forceOn == true)
		{
			indicatorItem.addClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").show(); //This shows the child ul level1 element
		}
		else if(indicatorItem.hasClass("active"))
		{
			indicatorItem.removeClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").hide(); //This shows the child ul level1 element
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
	 * If the idicator doesn't exist it'll just exit gracefully
	 */
	function showByIndicator(indicator)
	{
		if(typeof indicator == 'undefined')
		{
			return;
		}
		
		var dataPtr = null;
		var ids = indicator.split("_"); //split up the ids
		dataPtr = mapData.sheets[ids[0]]; //get the sheet, because it's different
		//loop over the remaining indicators
		for(i in ids)
		{
			//skip 0
			if(i!=0)
			{
				var id = ids[i];
				dataPtr = dataPtr['indicators'][id];
			}
			
		}
		
		
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
			
			var totalLabel = dataPtr["total_label"];
			console.log(totalLabel);
			
			UpdateAreaAllData(title, data, nationalAverage, indicator, unit, totalLabel);
			
			$.address.parameter("indicator", indicator);
			/*
			var level1Item = $("#bottom_level_"+indicator).parents("li.level1").children("span.level1");
			var level2Item = $("#bottom_level_"+indicator).parents("li.level2").children("span.level2");
			var level3Item = $("#bottom_level_"+indicator);
		
			
			level1Click(level1Item, true); //set "forceOn" to true to force it to show, even if it is already showing
			level2Click(level2Item, true); //set "forceOn" to true to force it to show, even if it is already showing
			level3Click(level3Item);
			*/
			
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
			
			//Show the national average and gradient divs
			$('#legend_gradient').show();
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
	areaGPolygons[name].setOptions({
				fillColor: colorValue,
				fillOpacity: opacityValue
			});
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
	
	//update the label

	labels[name].set("areaValue", addCommas(percentage)+" "+unit);
	labels[name].draw();
	

}

/**
Used to update the color and info window of an area
*/
function UpdateAreaPercentageMessage(name, percentage, min, spread, message, unit)
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
	});
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
	var message = '<div class="chartHolder" style="height:'+kmapInfodivHeight+'px">' + createChart(title, data, name, indicator+"_by_area_chart",unit, min, spread);
		
	//create the chart by for all the indicators of the given question, assuming there's more than one
	message = createChartByIndicators(message, indicator, name, unit);
	
	message += "</div>";
	
	//now call the next method that does work
	UpdateAreaPercentageMessage(name, percentage, min, spread, message, unit);
	
}


/**
* Creates the URL for the chart that shows the spread over indicator for a given question for
* both area and overal average
* 
* message: the message string as it currently stands
* indicator: the indicator we're looking at
* name: the name of the current geographical area
*/
function createChartByIndicators(message, indicator, name, unit)
{
	//first check if there's more than one answer to the given question
	if($("#bottom_level_"+indicator).siblings().length == 0)
	{
    //clear out the National Chart
    $("#nationalIndicatorChart").html("");
			return message;
	}
	//there is more than one answer ...as so many questions have.
	
	//get the data for those questions
	var dataForArea = new Array();
	var mainIndicatorText = $("#bottom_level_"+indicator).text(); 
	var questionText = $("#bottom_level_"+indicator).parents("li.level2").children("span.level2").text();
	//get the data for the indicator we're focused on
	
	dataForArea[mainIndicatorText] = indicatorsToUpdateParams[indicator]["data"][name];

	
	//get the rest of the data
	$.each($("#bottom_level_"+indicator).siblings(), function() {			
		var otherIndicator = $(this);
		var otherIndicatorId = otherIndicator.attr("id").substring(13);
		var indicatorText = otherIndicator.text();
		dataForArea[indicatorText] =  indicatorsToUpdateParams[otherIndicatorId]["data"][name];
	});
	
	//calculate the min and spread for the area specific graph
	var spreadMin = calculateMinSpread(dataForArea);
	var min = spreadMin["min"];
	var spread = spreadMin["spread"];
	
	//build the freaking chart this is not that much fun. I should write a JS library that does this for me.
	//that's a really good idea. I should find someone to pay me to do that. You know it's probably already been done.
	//it's been done in like every language but javasript, so I just made the below function.
	message += createChart(name + ": " + questionText, dataForArea, mainIndicatorText, indicator+"_by_indicator_area_chart", unit, min, spread);
	
	
	return message;
}


function createChart(title, data, highLightName, id, unit, min, spread)
{
	
	//now loop through the data and build the rest of the URL
	var names = "";
	var blues = "";
	var reds = "";
	var nameDelim = "|";
	var numberDelim = ",";
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
		console.log("shouldn't be here: Min: "+min+ " Spread: "+spread + " UNIT: "+unit);
	}
	
	var kmapInfochart_temp = kmapInfochart_temp.replace("<RANGE_LABELS>", kampInfoChartRangeLabels);
	
	var chartStr = '<div id="'+id+'" class="infowindow"><p class="bubbleheader">'+title
		+'</p><img src="'+kmapInfochart_temp; //This is the base of the Google Chart API graph (without the data part). Needs to be defined in the container file.
	
	//now put all of that together
	chartStr += names + '&chd=t:' + blues + nameDelim + reds + '" height="' + kmapInfochartHeight + '" width="' + kmapInfochartWidth + '" /></div>';
	//console.log(chartStr);
	return chartStr;
	
}

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
		UpdateAreaPercentageTitleData(areaName, data[areaName], min, spread, title, data, indicator, unit);
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
	var nationalChart = createChart(questionText + ' ('+kmapAllAdminAreas+')', dataForNational, mainIndicatorText, indicator+"_by_indicator_national_chart", unit, min, spread);
	$("#nationalIndicatorChart").html(nationalChart);
	
}

//changed this to take another input 
//TODO -> make sure this doesn't mess anything up
function updateKey(min, span, title, unit)
{
	$("#percentleft").attr("title", addCommas(min)+" "+htmlDecode(unit));
	$("#percentleft").text(addCommas(min)+" "+htmlDecode(unit));
	
	$("#percentright").attr("title", addCommas((min+span))+" "+htmlDecode(unit));
	$("#percentright").text(addCommas((min+span))+" "+htmlDecode(unit));
	
	
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
				if(indicator != undefined)
				{
					showByIndicator(indicator);
				}

			});  
		});
})(jQuery);


function stripString(str) 
{
  return str.replace(/^\s+|\s+$/g, '');
};

function is_array(input)
{
	return typeof(input)=='object'&&(input instanceof Array);
}


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




</script>