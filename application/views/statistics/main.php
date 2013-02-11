<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-23
* Show user their stats
*************************************************************/
?>
	
<h1> <?php echo __('Statistics')?></h1>

<?php if($maps == null) {
	echo __('You have no maps to view statistics for.');
}
else {?>
<div id="statChartWrapper"> <p> <?php echo __('Dates on the graph that are shaded grey correspond to weekends.')?> </p> 
<div id="statChart" style= "width:700px; height:500px"></div></div>
<div id="statsControls">
	<?php echo __('Select Maps')?> <br/>
	<?php echo Form::select('maps',$maps,null, array('id'=>'maps', 'multiple'=>'multiple'));?>
	
	<p><?php echo __('Start Date')?>: <input type="text" id="startDate" value="<?php 
			echo date('m/d/Y', time() - (24 * 60 * 60 * 30));
		?>"/>
	</p>
	<p><?php echo __('End Date')?>: <input type="text" id="endDate" value="<?php echo date('m/d/Y', time());?>" /></p>
	<input type="button" value="<?php echo __('Submit')?>" onclick="updateGraph()"/>
</div>

<?php }?>