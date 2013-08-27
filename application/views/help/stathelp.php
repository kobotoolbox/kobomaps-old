<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* stathelp.php - view
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-26
*************************************************************/

/*
echo '<div class="helpSidebar">';
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
	*/
echo '<div id="statHelp">';
	echo '<span style="font-weight:bold">'.__('StatHelp Title').'</span></br></br>';
	echo '<div>'.__('StatHelp Desc').'</div>';
	echo '</br>';
	echo '<img src="'.url::base().'media/img/Help/statHelp.png" width="952" height="431"/>';
echo '</div>';
	?>
