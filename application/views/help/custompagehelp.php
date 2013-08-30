<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* custompagehelp.php - view
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-26
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
	
echo '<div id="customHelp">';
	echo '<div class="sections">'.__('customHelp Title').'</div>';
	echo '</br>';
	echo '<div>'.__('customHelp Desc').'</div>';
	echo '</br></br>';
	
	echo '<div class="sections">'.__('Creating a custom page').': </div>';
		echo '<ul>';
			echo '<li>';
				echo '<span id="currentPages" class="title">'.__('currentPages').': </span>';
				echo __('currentPages Desc');
				echo '</br><img src="'.url::base().'media/img/Help/customPageCurrentPages.png"></br>';
				echo __('DeletingWarning');
			echo '</li></br>';
			echo '<li>';
			echo '<span id="customPageTitle" class="title">'.__('customPage Title').': </span>';
				echo __('customTitle Desc');
				echo '</br><img src="'.url::base().'media/img/Help/customPageTitle.png"></br>';
			echo '</li>';
			echo '<li>';
				echo '<span id="customPageSlug" class="title">'.__('customPage Slug').': </span>';
				echo __('customSlug Desc');
				echo '</br><img src="'.url::base().'media/img/Help/customPageSlug.png"></br>';
			echo '</li>';
			echo '<li>';
				echo '<span id="customPageSub" class="title">'.__('customPage Sub').': </span>';
				echo __('customSub Desc');
				echo '</br</br>><img src="'.url::base().'media/img/Help/customPageSub.png"></br>';
			echo '</li>';
			echo '<li>';
				echo '<span id="customPageHelp" class="title">'.__('customPage Help').': </span>';
				echo __('customHelp Desc');
				echo '</br><img src="'.url::base().'media/img/Help/customPageHelp.png"></br>';
			echo '</li>';
			echo '<li>';
				echo '<span id="customPageContent" class="title">'.__('customPage Content').': </span>';
				echo __('customContent Desc');
				echo '</br></br><img src="'.url::base().'media/img/Help/customPageContent1.png"></br>';
				echo '<ol class="contentList">';
				//now make the list that describes the buttons for the content maker
					for($i = 1; $i < 77; $i++){
						echo '<li><span class="title">'.__('customContentTitle'.$i).':</span> '.__('customPage'.$i).'</li>';
					}
				echo '</ol>';
			echo '</li></ul></br></br></br>';
			
			echo '<div class="sections">'.__('customPage End').'</div>';
echo '</div>';
	?>
<div style="clear:both"></div>