<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* custompagehelp.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-29
* Help users with creating a custom page
*************************************************************/


	echo '<p><strong>';
		echo __('This is the help page for creating your own custom pages for kobomaps.');
	echo '</strong></p>';
	echo '<p>';
		echo __('This page should only be available to administrators for the website and is used to create custom html webpages for the website.');
	echo '</p>';
	echo '<p>';
		echo __('The list of the current pages that can be edited is found on the left side of the page, selecting');
		echo '&nbsp;<em>'.__('New Page').'</em>';
		echo __('will allow you to create a brand new page.');
	echo '</p>';
	echo '<p>';
		echo __('Be aware the pages marked with underscores (__HOME__) cannot be deleted as they are needed for the website regardless, but can be edited as you wish.');
	echo '</p>';
	echo '<p><em>';
		echo __('Title of Page').':</em> ';
		echo __('This is the slug that you will use to navigate to the page in the URL or submenu items you create later. It needs to be a unique slug that is not used anywhere else on the website. As it is a URL, it cannot contain characters such as @, ", spaces, etc.');
	echo '</p>';
	echo '<p><em>';
		echo __('Content of Page').':&nbsp;</em>';
		echo __('This is a tool that easily helps you create webpages created in HTML, but you do not need any knowledge of HTML to use it.');
		echo __('It has the basic tools for').'&nbsp;
			<strong>'.__('Bold').',</strong>&nbsp;
			<em>'.__('Italics').',</em>&nbsp;
			<span style="text-decoration: underline;">'.__('Underline').'</span>, '.
			__('formatting, changing colors and fonts. It also has some advanced options that are found by clicking the Show/Hide Toolbars button found in the upper right.').
			__('These allow for links to other sites, table creation, and even CSS can be found here.');
	echo '</p>';
	echo '<p><strong>';
		echo __('Be sure to save the page that you are working on before leaving, you can hit either ctrl+s or the Save button.');
	echo '</strong></p>';
	
?>
	
