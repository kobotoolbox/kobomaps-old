<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* templatehelp.php - view
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-23
*************************************************************/


echo '<div class="helpSidebar  helpLink">';
	echo '<span class="ToC">'.__('ToC').'</span></br>';
		echo '<ul>';
	foreach($table as $key=>$section){
		if(is_array($section)){
			echo '<li><span  class="title">'.$key.'</span>';
			echo '<ul>';
			foreach($section as $label=>$part){
				if($label != 'class'){
					echo '<li><a href="#'.$label.'">'.$part.'</a></li>';
				}
			}
			echo '</ul></li>';
		}
		else{
			echo '<li><a href="#'.$key.'">'.$section.'</a></li>';
		}
	}
echo '</ul></div>';
	
echo '<div id="tempHelp">';
	echo '<div class="sections">'.__('Help on how to create a template.').'</div></br>';
	echo __('templateHelpDesc');
	echo '</br></br></br><span class="sections warning">'.__('Warning').': </span>'.__('warningDesc');
	echo '</br></br>';
	
	echo '</br>';
	echo '<div id="NoTemp" class="sections">'.__('No Template').' </div>';
	echo __('noTempDesc');
	
	echo '</br></br>';
	
	echo '<div id="signUp"><p>	<img src="http://groups.google.com/intl/en/images/logos/groups_logo_sm.gif" height=30 width=140 alt="Google Groups">	</p>';
	echo '<p>	<form action="http://groups.google.com/group/kobo-users/boxsubscribe">	<span>Email: </span>	<input type=text name=email>';
	echo '<input type=submit name="sub" value="Subscribe">	</form>	</p></div>';
	echo '</br></br></br>';
	
	echo '<div id="AllTemps" class="sections">'.__('AllTemplates').': </div>';
	echo __('AllTempDesc');
	echo '</br><img src="'.url::base().'media/img/Help/templateTable.png" width="558" height="488" />';
	
	echo '</br></br></br>';
	echo '<div id="MyTemps" class="sections">'.__('MyTemplates').': </div>';
	echo __('MyTempDesc');
	
	echo '</br></br></br>';
	
	echo '<div class="sections">'.__('CreateTemps').'</div>';
	echo __('CreateTempsDesc');
	echo '<ul id="CreateTemps">';
	
		echo '<li><div id="TempTitle"><span class="title">'.__('Temp Title').': </span>';
			echo __('tempTitleDesc');
			echo '</br></br><img src="'.url::base().'media/img/Help/templateTitle.png" width="497" height="44"/></br></br>';
		echo '</div></li>';
		
		echo '<li><div id="TempDesc"><span class="title">'.__('Temp Desc').': </span>';
			echo __('tempDescDesc');
			echo '</br></br><img src="'.url::base().'media/img/Help/templateDescription.png" width="688" height="143"/></br></br>';
		echo '</div></li>';
		
		echo '<li><div id="TempVis"><span class="title">'.__('Temp Vis').': </span>';
			echo __('tempVisDesc');
			echo '</br></br><img src="'.url::base().'media/img/Help/templateVisible.png" width="270" height="54"/></br></br>';
		echo '</div></li>';
		
		echo '<li><div id="TempFile"><span class="title">'.__('Temp File').': </span>';
			echo __('tempFileDesc');
			echo '</br></br><img src="'.url::base().'media/img/Help/templateFile.png" width="537" height="54"/></br></br>';
		echo '</div></li>';
		
		echo '<li><div id="TempAdmin"><span class="title">'.__('Temp Admin').': </span>';
			echo __('tempAdminDesc');
			echo '</br></br>'.__('Liberia Admin').'</br><img src="'.url::base().'media/img/Help/templateAdmin.png" width="278" height="238"/></br></br>';
		echo '</div></li>';
		
		echo '<li><div id="TempDec"><span class="title">'.__('Temp Dec').': </span>';
			echo __('tempDecDesc');
			echo '</br></br><img src="'.url::base().'media/img/Help/templateRounding.png" width="559" height="508"/></br></br>';
		echo '</div></li>';
		
		echo '<li><div id="TempLat"><span class="title">'.__('Temp Lat').': </span>';
			echo __('tempLatDesc');
		echo '</div></li>';
		
		echo '<li><div id="TempLong"><span class="title">'.__('Temp Long').': </span>';
			echo __('tempLongDesc');
		echo '</div></li>';
		
		echo '<li><div id="TempZoom"><span class="title">'.__('Temp Zoom').': </span>';
			echo __('tempZoomDesc');
			echo '</br></br><img src="'.url::base().'media/img/Help/templateZoom.png" width="540" height="378"/></br></br>';
		echo '</div></li></ul>';
	
		echo __('TempContinue');
		echo '</br></br><img src="'.url::base().'media/img/Help/templateFull.png" width="644" height="720"/></br></br>';
		
		echo __('TemplateEnd');
echo '</div></div>';
?>
<div style="clear:both"></div>