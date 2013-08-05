/***********************************************************
* colorProperties.js - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> 
* Started on 2013-08-05
* Javascript code for color options for the map
*************************************************************/



 //Constructor for colorProperties
var colorProperties = (function(){
	
	var border_color = '';
	var region_color = '';
	var polygon_color = '';
	var graph_color = '';
	var graph_select_color = '';
	
	
	/**
	 * Function to init colors
	 * @param string border - color of the region borders
	 * @param string region - color of the regions
	 * @param string polygon - color of highlighted regions
	 * @param string graph - color of graph bars
	 * @param string graph_select - color of selected graph bars
	 */
	function setColors(border, region, polygon, graph, graph_select){
		border_color = border;
		region_color = region;
		polygon_color = polygon;
		graph_color = graph;
		graph_select_color = graph_select;
	}
	
	//Getters for the all the colors
	function getBorder(){
		return border_color;
	}
	function getRegion(){
		return region_color;
	}
	function getPolygon(){
		return polygon_color;
	}
	function getGraph(){
		return graph_color;
	}
	function getGraphS(){
		return graph_select_color;
	}
	
	
	/**
	* Function to be called from the HTML to specify a new opacity and/or color value for a county
	* @param string countyName - name of the county as defined in the json file
	* @param double opacityValue - number between 1.0 and 0.0
	* @param string colorValue - html color value, in the form "#RRGGBB" such as "#ff0000" which is red
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
	* This uses a Library called Rainbow that creates a gradient color scheme
	* @param double percentage is the data of the area looking to be colored
	* @param double min is the lowest value given in the data
	* @param double spread is the spread from lowest to highest data points
	* @return string color in hex format
	*/
	function calculateColor(percentage, min, spread)
	{
		var gradient = new Rainbow();
		var color;
		var colorPerct = (percentage-min)*(1/spread);
		var first = polygon_color.substring(0,6);
		var second = polygon_color.substring(7, 13);
		if(second == ''){
			second = '#FFFFFF';
		}

		gradient.setSpectrum(second, first);
		gradient.setNumberRange(min, min+spread);
		//return the hex color of the percentage from min -> spread
		color = "#" + gradient.colourAt(colorPerct * (min + spread));
		
		return color;
	}
	
	/**
	* Changes the key bar in the legend to the value of the current total
	* @param double Min: Minimum value of percentages across all areas for baselining the color scale
	* @param double span: Spread from min to max of percentages across all areas for making the ceiling of the color scale
	* @param string Title: Title of the question
	* @param string unit is the unit designated by the user when the map was made
	*/
	function updateKey(min, span, title, unit)
	{ 
		if(isNaN(min) || isNaN(span)){
			$('#legend_gradient').hide();
		}
		else
		{
			var canvas = document.getElementById('legend_canvas');
		    var context = canvas.getContext('2d');
		    context.rect(0, 0, 298, 140);

		    // add linear gradient
		    var grd = context.createLinearGradient(0, 0, 298, 19);
		    var first = '#' + polygon_color.substring(0, 6);
		    var second = '#' + polygon_color.substring(7,13);
		    if(second == '#'){
				second = '#FFFFFF';
		    }
		    grd.addColorStop(0, second);   
		    grd.addColorStop(1, first);
		    context.fillStyle = grd;
		    context.fill();
		      
			$("#percentleft").attr("title", mapParsers.addCommas(min)+" "+mapParsers.htmlDecode(unit));
			$("#percentleft").text(mapParsers.addCommas(min)+" "+mapParsers.htmlDecode(unit));
			
			$("#percentright").attr("title", mapParsers.addCommas((min+span))+" "+mapParsers.htmlDecode(unit));
			$("#percentright").text(mapParsers.addCommas((min+span))+" "+mapParsers.htmlDecode(unit));

			$('#legend_gradient').show();
		}

		$("#spanLegendText").html(title);
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
		var color = calculateColor(percentage, min, spread);
		
		//update the polygon with this new color
		formatAreaOpacityColor(name, 0.75, color);
		
		//update the labels
		labels[name].set("areaValue", mapParsers.addCommas(percentage)+" "+unit);
		labels[name].draw();
	}


	//return so that the functions are called when using class.function()	 
	return {formatAreaOpacityColor:formatAreaOpacityColor, calculateColor:calculateColor, updateKey:updateKey, UpdateAreaPercentage:UpdateAreaPercentage, setColors:setColors,
		getBorder:getBorder, getRegion:getRegion, getPolygon:getPolygon, getGraph:getGraph, getGraphS:getGraphS};
	  
})();
 
