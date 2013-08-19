<?php
/***********************************************************
* header.php - View
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/ 
?>
<div id="header">

	<div id="userMenu">
		<?php
			echo Helper_Menus::make_user_menu($menu_page, $user);
		?>
	</div>
	<a id="headerImage" href="<?php echo URL::base();if(isset($user) AND $user != null){echo 'home';}?>">
		<img  src="<?php echo URL::base();?>media/img/kobo_logo.jpg"/>
	</a>
	
</div>
<div id="mainMenu"><?php echo Helper_Menus::make_menu($menu_page, $user);?></div>
<div id="subMenu"><?php echo Helper_Menus::make_submenu($menu_page, $user);?></div>

