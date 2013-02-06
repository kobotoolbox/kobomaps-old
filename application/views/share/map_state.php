<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* map_state.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* This shows the status of the map's privacy state
* Started on 2013-01-21
*************************************************************/
?>
	<div id="mapState">
			<a href="#" style="float:right;" onclick="changeState(<?php echo $map->id;?>); return false;"><?php echo __('Change to'). ' '. (intval($map->is_private == 1) ? __('Public') : __('Private'))?></a>
			<span style="float:right;" id="stateWaiting"></span>
			<?php echo intval($map->is_private == 0) ?__('This map is public - Anyone can view it') : __('This map is private - Only the people below can view it'); ?>			
		</div>