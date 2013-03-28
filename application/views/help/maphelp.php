<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* maphelp.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-26
* Help users with creating a map
*************************************************************/
?>

<p>
  <strong>Help on how to create a map.</strong>
</p>
<p>This page will go over how to create a map using Kobomaps.</p>
<p>&nbsp;</p>
<p>
  <strong>Basic Set-Up:</strong>
</p>
<ol>
  <li>
    <em>Map Title</em>: This is the title for how you will find and use your map, this cannot be more than 156 characters long.
  </li>
  <li>
    <em>Map Slug</em>: This is the url name that you can type into the address and link right to the map. Be careful though, since it is used in a url, you cannot use characters such as @, %, ", /, etc.
  </li>
  <li>
    <em>Map Description</em>: This is the description to help you and others know what the information on the map means. You can make this very descriptive and long.
  </li>
  <li>
    <em>Should this map be hidden from public view?:</em>&nbsp;Checking this box will mean that only you and users you allow will be able to see this map.
  </li>
  <li>
    <em>Is the data source:&nbsp;</em>Kobomaps can be created with either an Excel Spreadsheet or a GoogleDoc Spreadsheet, just choose which file you are using.
  </li>
  <li>
    <em>Spreadsheet</em>: Use this to load the file.
  </li>
  <li>
    <em>Show advanced options:&nbsp;</em>Clicking on this bar will reveal more options, seen below.
  </li>
</ol>
<p>
  &nbsp; &nbsp; &nbsp;<strong>Advanced Options:</strong>
</p>
<ol>
  <li>
    <em>Show All Labels:</em> If this box is checked, this map will show all the region names, even if there was no data submitted for them. Such as not having data for Colorado, USA, the name Colorado will still appear if this box is checked.
  </li>
  <li>
    <em>Zoom level to show labels:&nbsp;</em>This number is the zoom level within Googlemaps when the labels and names will start to appear on the map.
  </li>
  <li>
    <em>Font size of region names:</em>&nbsp;Controls the font size of the names of all the regions.
  </li>
  <li>
    <em>Font size of data values:</em> Controls the font size of the data labels of all the regions.
  </li>
  <li>
    <em>Color of region borders:&nbsp;</em>This color is the border lines between regions.
  </li>
  <li>
    <em>Default color of regions:&nbsp;</em>This is the color of the regions that have not been colored by the data shading.
  </li>
  <li>
    <em>Make regions have a gradient?</em>: Checking this box will give you the option to set the lower gradient, the default color for the graident is from the specified color into white.
  </li>
  <li>
    <em>Color of region shading:</em> This is the color that will be more prominent on the higher values of the data for regions that have data. The second color is the gradient end color, default is white. This will be prominent on the lower values of the data.
  </li>
  <li>
    <em>Color of bars in graphs:</em> This will be the basic color of the bar graphs present on the maps.
  </li>
  <li>
    <em>Color of selected regions in graphs:</em> The bar color for the selected indicator and region, helps the data stand out that you are focused on.
  </li>
  <li>
    <em>Map CSS:&nbsp;</em>You can enter your own CSS to change the colors and fonts and font size that were not covered by this setup.
  </li>
  <li>
    <em>Map style:&nbsp;</em>This is the Googlemaps developer code for changing the maps.
  </li>
</ol>
<p>&nbsp;</p>
<p>
  <strong>Data</strong>&nbsp;<strong>Stucture</strong>
</p>
<p style="padding-left: 30px;">This page should have most of the explainations required for itself, but double check the selected column and row match what you want, they are looked at very carefully by the program that designs the&nbsp;</p>
<p>
  <strong>Validation</strong>
</p>
<p style="padding-left: 30px;">On this page you check and make sure that the program has looked at your spreadsheet correctly, if names or units are too long, the page will warn you as it will make the map over-extend its formatting.</p>
<p>
  <strong>Geo Set-up</strong>
</p>
<p style="padding-left: 30px;">On this page you choose a template to display your information on. Ideally you have already created a template, or you can use a template that is available publically.&nbsp;</p>
<p>
  &nbsp;<strong>Geo Matching</strong>
</p>
<p style="padding-left: 30px;">
  On this page you match all the regions that have been found in the template with the regions that were entered in the spreadsheet. The program will attempt to fill in as many regions as it can find simliar.<span style="font-size: small;">
    <strong> You cannot use a region more than once.</strong>
  </span>
</p>
<p>
  <strong>&nbsp;And that should be it to complete your map! When you hit submit on the Geo Matching page, your map will be created and you will be able to see it.</strong>
</p>