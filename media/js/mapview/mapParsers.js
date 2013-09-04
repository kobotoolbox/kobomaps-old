/***********************************************************
* mapParsers.js - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> 
* Started on 2013-08-05
* Javascript code for all parsing dealt with by the map
*************************************************************/



 //Constructor for mapParsers
var mapParsers = (function(){
	

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
				$("#sheetnames").append('<li class="sheet2"><span class="sheet2" id="sheetSelector_'+sheetId+'" onclick="sheetControl.sheetSelect('+sheetId+');">'+sheet.sheetName+'</span></li>');		

				
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
				sheetControl.setup_scrolling();
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
					sheetControl.midIndicatorClick($(this));
				});
				$('ul.indicatorLevel_'+i).hide(); //This hides all ul level1 by default until they are toggled. Can also be defined in css.			
			}
		
			//control the clicking behavior of the data level
			$('span.dataLevel').click(function (){	//originally was $('span.indicatorLevel_'+depthOfData).click(function (){
				sheetControl.dataIndicatorClick($(this));
			});

			//check if we're supposed to auto load the data for a particular indicator?
			var autoLoadIndicator = $.address.parameter("indicator");
			if( autoLoadIndicator != "" && typeof autoLoadIndicator !== "undefined" )
			{
				sheetControl.showByIndicator(autoLoadIndicator);
			}
			else
			{
				//Default selects first sheet 
				sheetControl.sheetSelect(initialId);
			}
			
			//hide the temporary loading text once the indicators are visible
			$('#loadingtext').remove();
			playback.onLoad();
		});		
	}//end parseJsonData function
	
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
			console.log(data);
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
							strokeColor: colorProperties.getBorder(), //sets the line color to defined color
							strokeOpacity: 0.8, //sets the line color opacity to 0.8
							strokeWeight: 2, //sets the width of the line to 3
							fillColor: colorProperties.getRegion(), //sets the fill color
							fillOpacity: 0.75 //sets the opacity of the fill color
					});
						areaGPolygons[areaName] = new google.maps.Polygon({
							paths: points,
							strokeColor: colorProperties.getBorder(), //sets the line color to defined color
							strokeOpacity: 0.8, //sets the line color opacity to 0.8
							strokeWeight: 2, //sets the width of the line to 3
							fillColor: colorProperties.getRegion(), //sets the fill color
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


	//return so that the functions are called when using class.function()	 
	return {parseJsonToGmap:parseJsonToGmap, decimalToHex:decimalToHex, htmlEncode:htmlEncode, htmlDecode:htmlDecode, addCommas:addCommas};
	  
})();
 
