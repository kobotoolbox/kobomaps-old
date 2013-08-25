<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* maphelp.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-22
*************************************************************/

echo '<div style="clear:both"><div class="helpSidebar" style="height:auto;width:350px;">';
echo '<ul>';
echo '<span class="ToC">'.__('ToC').'</span>';
foreach($table as $key=>$section){
	if(is_array($section)){
		echo '<li>'.$key;
		echo '<ul>';
		foreach($section as $label=>$part){
			if($label != 'class'){
				echo '<li><a name="'.$section['class'].'" onclick="openHelp(this)" href="#'.$label.'">'.$part.'</a></li>';
			}
		}
		echo '</ul></li>';
	}
	else{
		echo '<li><a onclick="openHelp(this)" href="#'.$key.'">'.$section.'</a></li>';
	}
}
echo '</ul></div>';

	echo '<div id="help" style="float:right;">';
		echo '<div class="sections">'.__('Help on how to create a map.').'</div>';
		echo '<div>'.__('This page will go over how to create a map using Kobomaps.').'</div></br>';
		
		//start the basic sections
		echo '<div class="sections"><a href="#Basic" onclick="toggleClass(this.id)" id="Basic">'.__('Basic Set-Up').'  </a>';
		echo '</div>';
		echo '<ul class="Basic helpGroup" style="display:none">';
			echo '<li><div id="MapTitle"><span class="title">'.__('Map Title').': </span>';
				echo __('mapTitleDesc');
				echo '</br><img src="'.url::base().'media/img/Help/map_title.png" width="608" height="36" alt="'.__('Example of map title.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapSlug"><span class="title">'.__('Map Slug').': </span>';
				echo __('mapSlugDesc');
				echo '</br><img src="'.url::base().'media/img/Help/map_slug_good.png" width="634" height="308" alt="'.__('Slug examples.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapDesc"><span class="title">'.__('Map Description').': </span>';
				echo __('mapDescDesc');
				echo '</br><img src="'.url::base().'media/img/Help/map_description.png" alt="'.__('Description example.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapHidden"><span class="title">'.__('Map Hidden').': </span>';
				echo __('mapHiddenDesc');
				echo '</br><img src="'.url::base().'media/img/Help/private.png" width="515" height="48" alt="'.__('Checkbox').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapData"><span class="title">'.__('Is the data source').': </span>';
				echo __('mapDataDesc');
			echo '</div></li>';
			echo '<li><div id="MapSpread"><span class="title">'.__('Spreadsheet (.xls, .xlsx)').': </span>';
				echo __('mapSpreadDesc');
			echo '</div></li>';
			echo '<li><div id="MapAdvanced"><span class="title">'.__('Show advanced options').': </span>';
				echo __('mapAdvancedDesc');
			echo '</div></li>';
		echo '</ul>';
		//start of advanced section
		echo '</br><a href="#Advanced" class="sections" onclick="toggleClass(this.id)" id="Advanced">'.__('Advanced Options').'</a>';
		echo '<ul class="Advanced helpGroup" style="display:none">';
			echo '<li><div id="MapLabel"><span class="title">'.__('Map Label').': </span>';
				echo __('mapLabelDesc');
				echo '</br><img src="'.url::base().'media/img/Help/show_region.png" width="819" height="310" alt="'.__('Example of labels.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapZoom"><span class="title">'.__('Map Zoom').': </span>';
				echo __('mapZoomDesc');
				echo '</br><img src="'.url::base().'media/img/Help/zoom_level.png" width="809" height="362" alt="'.__('Example of zoom levels.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapRegionFont"><span class="title">'.__('Map Region Font').': </span>';
				echo __('mapRegionFontDesc');
				echo '</br><img src="'.url::base().'media/img/Help/region_font.png" width="819" height="368" alt="'.__('Example of region font sizes.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapDataFont"><span class="title">'.__('Map Data Font').': </span>';
				echo __('mapDataFontDesc');
				echo '</br><img src="'.url::base().'media/img/Help/data_font.png" width="819" height="368" alt="'.__('Example of data font sizes.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapBorderColor"><span class="title">'.__('Map Border Color').': </span>';
				echo __('mapBorderColorDesc');
				echo '</br><img src="'.url::base().'media/img/Help/border.png" width="786" height="160" alt="'.__('Example of default border color.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapRegionColor"><span class="title">'.__('Map Region Color').': </span>';
				echo __('mapRegionColorDesc');
				echo '</br><img src="'.url::base().'media/img/Help/region_color.png" width="796" height="200" alt="'.__('Example of default region color.').'"/>';
			echo '</div></li>';
			echo '<li><div id="MapGradient"><span class="title">'.__('Map Gradient').': </span>';
				echo __('mapGradientDesc');
				echo '</br><img src="'.url::base().'media/img/Help/gradient.png" width="692" height="110"/>';
			echo '</div></li>';
			echo '<li><div id="MapShading"><span class="title">'.__('Map Shading').': </span>';
				echo __('mapShadingDesc');
				echo '</br><img style="position:relative; left:375px" src="'.url::base().'media/img/Help/gradient_region.png" width="451" height="246"/>';
			echo '</div></li>';
			echo '<li><div id="MapBar"><span class="title">'.__('Map Bar').': </span>';
				echo __('mapBarDesc');
				echo '</br><img style="position:relative; left:550px" src="'.url::base().'media/img/Help/bar_color.png" width="240" height="130"/>';
			echo '</div></li>';
			echo '<li><div id="MapSelected"><span class="title">'.__('Map Selected').': </span>';
				echo __('mapSelectedDesc');
				echo '</br><img style="position:relative; left:500px" src="'.url::base().'media/img/Help/selected_bar_color.png" width="338" height="148"/>';
			echo '</div></li>';
			echo '<li><div id="MapCSS"><span class="title">'.__('Map CSS').': </span>';
				echo __('mapCSSDesc');
			echo '</div></li>';
		echo '</ul></br></br>';
			
		echo '<div class="sections" id="DataStructure">'.__('Data Structure').'</div>';
		echo __('dataDesc');
		echo '</br></br>';
		echo '<div class="sections" id="Validation">'.__('Validation').'</div>';
		echo __('validationDesc');
		echo '</br></br>';
		echo '<div class="sections" id="GeoSetup">'.__('Geo Set-up').'</div>';
		echo __('geoSetDesc');
		echo '</br></br>';
		echo '<div class="sections" id="GeoMatching">'.__('Geo Matching').'</div>';
		echo __('geoMatchDesc');
		echo '</br></br>';
		echo '<div class="sections"><a id="Style" href="#Style" onclick="toggleClass(this.id)">'.__('Map Style').'</a></div>';
		echo '<div class="Style helpGroup" style="display:none">';
			echo __('styleDesc');
			echo '<ul>';
				echo '<li><div id="AdminProv"><span class="title">'.__('AdminProv').': </span>';
					echo __('adminProvDesc');
					echo '</br><img src="'.url::base().'media/img/Help/admin_label.png" width="689" height="334"/>';
				echo '</div></li>';
				echo '<li><div id="AdminLoc"><span class="title">'.__('AdminLoc').': </span>';
					echo __('adminLocDesc');
					echo '</br><img src="'.url::base().'media/img/Help/admin_local.png" width="708" height="172"/>';
				echo '</div></li>';
				echo '<li><div id="POI"><span class="title">'.__('POI').': </span>';
					echo __('poiDesc');
					echo '</br><img src="'.url::base().'media/img/Help/poi.png" width="587" height="196"/>';
				echo '</div></li>';
				echo '<li><div id="Road"><span class="title">'.__('Road').': </span>';
					echo __('roadDesc');
					echo '</br><img src="'.url::base().'media/img/Help/road.png" width="566" height="172"/>';
				echo '</div></li>';
				echo '<li><div id="Landscape"><span class="title">'.__('Landscape').': </span>';
					echo __('landscapeDesc');
					echo '</br><img src="'.url::base().'media/img/Help/landscape.png" width="607" height="174"/>';
				echo '</div></li>';
				echo '<li><div id="Water"><span class="title">'.__('Water').': </span>';
					echo __('waterDesc');
					echo '</br><img src="'.url::base().'media/img/Help/water.png" width="745" height="178"/>';
				echo '</div></li>';
		echo '</ul></div></br></br></br>';
		
		echo __('mapHelpClosing');
	echo '</div><div style="clear:both"></div></div>';?>
