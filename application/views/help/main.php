<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* help main.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-26
*************************************************************/

echo '<div id="mainHelp">';
	echo '<h3>'.__('Help Welcome').'</h3>';
	echo __('Help Desc');

echo '</div></br></br>';

echo '<div class="mainToC  helpLink">';
	echo '<span class="ToC">'.__('ToC').'</span></br>';
		echo '<ul>';
		foreach($table as $key=>$section){
			if(is_array($section)){
				echo '<li><span  class="title">'.$key.'</span>';
				echo '<ul>';
				if(count($section) != 0){
					foreach($section as $part=>$url){
						echo '<li><a href="'.$url.'">'.$part.'</a></li>';
					}
				}
				echo '</ul></li>';
			}
			else{
				echo '<li><a href="'.$section.'">'.$key.'</a></li>';
			}
		}
echo '</ul></div>';

echo '</br></br><div>'.__('koboUserGroup').'</div></br>';

echo '<div id="signUp"><p>	<img src="http://groups.google.com/intl/en/images/logos/groups_logo_sm.gif" height=30 width=140 alt="Google Groups">	</p>';
echo '<p>	<form action="http://groups.google.com/group/kobo-users/boxsubscribe">	<span>Email: </span>	<input type=text name=email>';
echo '<input type=submit name="sub" value="Subscribe">	</form>	</p></div>';

echo '</br></br><img src="'.url::base().'media/img/Help/mainHelp.png" width="825">';
