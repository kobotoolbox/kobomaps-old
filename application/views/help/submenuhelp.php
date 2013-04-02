<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* submenuhelp.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-29
* Help users with creating a submenu
*************************************************************/


	echo '<p><strong>';
		echo __('This is the help page for creating submenus.');
	echo '</strong></p>';
	echo '<p>';
		echo __('This page should only be available to administrators of the website and is used to create submenus for any custom pages or any of the four default pages');
		echo ': '.__('main').', '.__('about').', '.__('help').', '.__('and').' '.__('support');
	echo '</p>';
	echo '<p>';
		echo __('The drop down bar on the left side the list of current pages that can support submenus, if you have no custom pages created, only the four default pages will be visible.');
		echo __('Each of these options has either New Submenu or a submenu that has already been created. Just select the option you want to create or edit.');
	echo '</p>';
	echo '<p><em>';
		echo __('Create menu item in page').':</em>';
		echo __('This will be automatically filled out for you and is just to make sure you are creating a submenu on the page that you are trying to.');
	echo '</p>';
	echo '<p><em>';
		echo __('Title of menu item').'</em>: ';
		echo __('This is the text that will appear below the icon, indicating what it should link to, such as the "Help making submenus" description that links to this page.');
	echo '</p>';
	echo '<p><em>';
		echo __('Menu URL').':</em>&nbsp;';
		echo __('This is the link that will be used by the icon, when clicked on will lead the user to the page listed in this URL. As with all URLs, it cannot contain @, ", spaces, etc. At the moment, the site will only have submenus that link to other pages in the kobomaps site, future updates could make this any url.');
	echo '</p>';
	echo '<p><em>';
		echo __('Icon').' (.jpeg, .png, .bmp):</em> ';
		echo __('This is the image that will be visible for the submenu item that you are creating, needs to be an image file, so extensions such as .jpeg, .jpg, .png, .bmp are required.');
	echo '</p>';
	

?>