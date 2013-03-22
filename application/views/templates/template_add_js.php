<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* template_add_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<?php if($template != null){?>
<link href="<?php echo  URL::base() ?>media/css/templatePreview.css" type="text/css" rel="stylesheet"> 
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"> </script>
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/label.js"> </script>
<script type="text/javascript">

var dontUpdate = false;

/**
 *  gives us a list of names for geographicAreas
 */
var geographicAreaNames = new Array();

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
 * The map object
 */
 var map = null;

 /**
  * global variable holding the polygons for each area. areaGPolygons["bomi"] 
  * would return the polygon for Bomi
  */
 var areaGPolygons = new Array();

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

	var myOptions = {
			zoom: <?php echo $template->zoom?>, 	//creates the initial zoom level. This is defined in the container file as it is country-specific
			center: new google.maps.LatLng(<?php echo $template->lat?>,<?php echo $template->lon?>), //creates the coordiantes that will center the map. This is defined in the container file as it is country-specific
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

	$(document).ready(function() {
		//creates the map by looking for the "map_canvas" item in the HTML below. the map will fill in the "map_canvas" div
		map = new google.maps.Map(document.getElementById("map_div"), myOptions);

		/*Creates the options for our custom map type*/
		var styledMapOptions = {
			name: "Default",
			alt: "View the map in KoBoMaps default theme"
		};
		/*Adds new map and sets it to default*/
		var kmapsMapType = new google.maps.StyledMapType(mapStyles, styledMapOptions);
		map.mapTypes.set('kmaps', kmapsMapType); 
		map.setMapTypeId('kmaps');

		//add a pan listener
		 google.maps.event.addListener(map, 'center_changed', function() {
			 if(dontUpdate)
			 {
				 dontUpdate = false;
				 return;
			 }

		    var center = map.getCenter();
		    var lat = center.lat();
		    var lon = center.lng();
		    //keep the lon from wrapping
		    lon = lon % 360;
			if(lon > 180)
			{
				lon = lon -360; 
			}
			else if(lon < -180)
			{
				lon = lon +360;
			}
			else
			{
				lon = lon;
			}
		    
		    $("#lat").val(lat);
		    $("#lon").val(lon);
		  });
		 //add zoom listener
		 google.maps.event.addListener(map, 'zoom_changed', function() {
			    var zoom = map.getZoom();			    
			    $("#zoom").val(zoom);
			  });

		//now parse the json

		//Calling the boundaries and data files. The variables need to be defined in the container file as they are country-specific
		parseJsonToGmap('<?php echo URL::base().'uploads/templates/'.$template->file; ?>');
	});


	/**
	* @param string jsonUrl of the file to be parsed
	*/
	function parseJsonToGmap(jsonUrl)
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
			
		});
		

	 }//end parse json
		

	 function changeZoom(){
		 var zoom = parseInt($("#zoom").val());
		 map.setZoom(zoom);
	 }

	 function latChanged()
	 {
		 var latStr = $("#lat").val();
		 var lat = parseFloat(latStr);
		 if(!isNaN(lat) && lat < 90 && lat > -90 && latStr.substring(latStr.length - 1) != '.')
		 {
			dontUpdate = true;
		 	var latLon = new google.maps.LatLng(lat,map.getCenter().lng());
		 	map.setCenter(latLon);
		 }
	 }

	 function lonChanged()
	 {
		 var lonStr = $("#lon").val();
		 var lon = parseFloat(lonStr);
		 if(!isNaN(lon) && lon <= 180 && lon >= -180 && lonStr.substring(lonStr.length - 1) != '.')
		 {
			dontUpdate = true;
		 	var latLon = new google.maps.LatLng(map.getCenter().lat(), lon);
		 	map.setCenter(latLon);
		 }
		 return true;
	 }

</script>
<?php } ?>