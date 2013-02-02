<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* exceed_limit.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 2013-02-01
* This tells the user that they've exceeded their usage limits
*************************************************************/
?>
<div style="text-align:center;">		
<h2><?php echo __('Map Limit Exceeded');?></h2>


<img src="<?php echo URL::base();?>media/img/big_error.png"/>
<h2>
	<?php 
	echo __('We\'re sorry, but you have').' '.$current_items. ' ';
	echo __('maps and your account only allows you to have').' '. $user_max_items.__('SENTANCE_END'). '<br/>';
	echo __('If you want to add a new map you must remove'). ' '. ($current_items - ($user_max_items - 1)).' ';
	echo __('maps.');
	?>
</h2>
</div>
<a href="<?php echo URL::base();?>mymaps/"><?php echo __('Back to my maps')?></a>


