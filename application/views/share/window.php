<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* window.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-21
*************************************************************/
?>
	
<div class="shareWindow" id="shareWindow_<?php echo $map->id;?>">
<h2><?php echo __('Sharing Settings:');?> <?php echo $map->title;?></h2>
<?php echo __('Sharing Settings:');?>
<p>
	<?php echo __('Link to share map')?><br/>
	<input readonly="readonly" type="text" value="<?php echo URL::site(NULL, TRUE)?>public/view?id=<?php echo $map->id?>"/>
	<br/>
	<?php
		$body = __('I want to share this map with you:').' '.URL::site(NULL, TRUE).'public/view?id='.$map->id;
		$body = rawurlencode($body); 
		$subject = rawurlencode(__('Sharing'). ' '.$map->title. ' '.__('map'));
	?>
	<a href="mailto:?subject=<?php echo $subject;?>&body=<?php echo $body;?>"><?php echo __('share email'); ?></a> 
</p>
</div>