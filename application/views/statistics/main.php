<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-01-23
* Show user their stats
*************************************************************/
?>
	
<h1 style="font-size: 20px"> <?php echo __('Statistics')?></h1>

<?php if($maps == null) {
	echo __('You have no maps to view statistics for.');
}
else {?>
<div id="statChartWrapper"> <p> <?php echo __('Dates on the graph that are shaded grey correspond to weekends.')?> </p> 
<p> <?php echo __('This does not count map views by the map\'s owner.')?> </p>
<div id="statChart"></div></div>
<div id="statsControls">
	<?php echo __('Select Maps')?> <br/>
	<?php echo Form::select('maps',$maps,null, array('id'=>'maps', 'multiple'=>'multiple'));?>
	
	<p><?php echo __('Start Date')?>: <input type="text" id="startDate" value="<?php 
			echo date('m/d/Y', time() - (24 * 60 * 60 * 30));
		?>"/>
	</p>
	<p><?php echo __('End Date')?>: <input type="text" id="endDate" value="<?php echo date('m/d/Y', time());?>" /></p>
	<input type="button" value="<?php echo __('Submit')?>" onclick="updateGraph()"/>
	<input type="button" value="<?php echo __('Export to CSV')?>" onclick="createCSV()"/>
</div>
</br>
<div id="legend_holder" style="display:none"> <h3><?php echo __('Legend').':'?> </h3>
	<div id="legend"></div>
</div>

<form method=POST action="<?php echo URL::base()?>/statistics/csvexport" id="csvform">
	<input type="hidden" name="data" id="csvdata"/></form>
<?php }?>