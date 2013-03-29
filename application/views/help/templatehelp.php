<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* templatehelp.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-26
* Help users with creating a template
*************************************************************/



echo '<p><span style="text-decoration: underline;"><strong>';
	echo __('Help for creating a template').'</strong></span></p>';

echo '<p>'.__('There is one easy page for creating a template and this is how you do it').':</p>';
echo '<ol>';
	echo '<li><em>';
		echo __('Template Title').':</em> ';
		echo __('This is the title of the template, this is how you will access and use the template.');
	echo '</li>';
	echo '<li><em>';
		echo __('Template Description').':</em>';
		echo __('This should be how you explain what the template should be used for and can be as long as you need.');
	echo '</li>';
	echo '<li><em>';
		echo __('Visibility').':&nbsp;</em>';
		echo __('Decides if only you or everyone can use this template.');
	echo '</li>';
	echo '<li><em>';
		echo __('File').':</em> ';
		echo __('This is the file that creates the template, needs to be .kml or .kmz.');
	echo '</li>';
	echo '<li><em>';
		echo __('Admin Level').':</em> ';
		echo __('What level of admin is required to use the template.');
	echo '</li>';
	echo '<li><em>';
		echo __('How many decimal places to round to').':</em> ';
		echo __('How accurate the borders on the regions are, the lower the rounding, the faster the template loads, but the less accurate it will be.');
	echo '</li>';
	echo '<li><em>';
		echo __('How far back to remember rounding').':</em>&nbsp;';
	echo '</li>';
	echo '<li><em>';
		echo __('By default, what should the center point latitude be').':</em> ';
		echo __('This is the latitude for the template to focus on and first appear at.');
	echo '</li>';
	echo '<li><em>';
		echo __('By default, what should the center point longitude be').':</em> ';
		echo __('This is the longitude for the template to focus on and first appear at.');
	echo '</li>';
	echo '<li><em>';
		echo __('By default what should this map zoom to').':</em> ';
		echo __('This is how closs the template should zoom automatically, the small the number, the farther zoomed out the template is.');
	echo '</li>';
echo '</ol>';
