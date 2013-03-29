<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* maphelp.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-26
* Help users with creating a map
*************************************************************/


	echo '<p><strong>'.__('Help on how to create a map.').'</strong>';
	echo '</p>';
	echo '<p>'.__('This page will go over how to create a map using Kobomaps.').'</p>';
	echo '<p><strong>'.__('Basic Set-Up').':</strong>';
	echo '</p>';
	
	echo '<ol>';
  		echo '<li>';
    		echo '<em>'.__('Map Title').'</em>: '.__('This is the title for how you will find and use your map, this cannot be more than 156 characters long.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Map Slug').'</em>: '.__('This is the url name that you can type into the address and link right to the map. Be careful though, since it is used in a url, you cannot use characters such as @, %, \", /, etc.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Map Description').'</em>: '.__('This is the description to help you and others know what the information on the map means. You can make this very descriptive and long.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Should this map be hidden from public view?').':</em>&nbsp;'.__('Checking this box will mean that only you and users you allow will be able to see this map.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Is the data source').':&nbsp;</em>'.__('Kobomaps can be created with either an Excel Spreadsheet or a GoogleDoc Spreadsheet, just choose which file you are using.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Spreadsheet (.xls, .xlsx)').'</em>: '.__('Use this to load the file.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Show advanced options').':&nbsp;</em>'.__('Clicking on this bar will reveal more advanced options, seen below.');
  		echo '</li>';
	echo '</ol>';
	
echo '<p>&nbsp; &nbsp; &nbsp;<strong>'.__('Advanced Options').':</strong></p>';

	echo '<ol>';
  		echo '<li>';
    		echo '<em>'.__('Show All Labels').':</em> '.__('If this box is checked, this map will show all the region names, even if there was no data submitted for them. Such as not having data for Colorado, USA, the name Colorado will still appear if this box is checked.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Zoom level to show labels').':&nbsp;</em>'.__('This number is the zoom level within Googlemaps when the labels and names will start to appear on the map.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Font size of region names').':</em>&nbsp;'.__('Controls the font size of the names of all the regions.');
  		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Font size of data values').':</em> '.__('Controls the font size of the data labels of all the regions.');
 		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Color of region borders').':&nbsp;</em>'.__('This color is the border lines between regions.');
	  	echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Default color of regions').':&nbsp;</em>'.__('This is the color of the regions that have not been colored by the data shading.');
	    echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Make regions have a gradient?').'</em>: '.__('Checking this box will give you the option to set the lower gradient, the default color for the graident is from the specified color into white.');
		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Color of region shading').':</em> '.__('This is the color that will be more prominent on the higher values of the data for regions that have data. The second color is the gradient end color, default is white. This will be prominent on the lower values of the data.');
		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Color of bars in graphs').':</em> '.__('This will be the basic color of the bar graphs present on the maps.');
		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Color of selected regions in graphs').':</em> '.__('The bar color for the selected indicator and region, helps the data stand out that you are focused on.');
		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Map CSS').':&nbsp;</em>'.__('You can enter your own CSS to change the colors and fonts and font size that were not covered by this setup.');
		echo '</li>';
  		echo '<li>';
    		echo '<em>'.__('Map style').':&nbsp;</em>'.('This is the Googlemaps developer code for changing the map style.');
  		echo '</li>';
	echo '</ol>';
	
echo '<p>&nbsp;</p><p><strong>'.__('Data Stucture').'</strong></p>';
	echo '<p style="padding-left: 30px;">'.__('This page should have most of the explainations required for itself, but double check the selected column and row match what you want, they are looked at very carefully by the program that designs the map.').'</p>';

echo '<p><strong>'.__('Validation').'</strong></p>';
	echo '<p style="padding-left: 30px;">'.__('On this page you check and make sure that the program has looked at your spreadsheet correctly, if names or units are too long, the page will warn you as it will make the map over-extend its formatting.').'</p>';

echo '<p><strong>'.__('Geo Set-up').'</strong></p>';
	echo '<p style="padding-left: 30px;">'.__('On this page you choose a template to display your information on. Ideally you have already created a template, or you can use a template that is available publically.').'&nbsp;</p>';

echo '<p>&nbsp;<strong>'.__('Geo Matching').'</strong></p>';
	echo '<p style="padding-left: 30px;">'.__('On this page you match all the regions that have been found in the template with the regions that were entered in the spreadsheet. The program will attempt to fill in as many regions as it can find that are simliar.').
	'<span style="font-size: small;"><strong> '.__('You cannot use a region more than once.').'</strong></span>';
echo '</p>';

echo '<p><strong>&nbsp;'.__('And that should be it to complete your map! When you hit submit on the Geo Matching page, your map will be created and you will be able to use it.');
echo '</strong></p>';

?>