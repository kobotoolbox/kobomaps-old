<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<div id="addmapMenu"><?php echo Helper_AddmapSubmenu::make_addmap_menu(3);?></div>	
		
<h2><?php echo __("Add Map - Page 3") ?></h2>
<ul class="context_menu">
	<li>
		<a class="button" id="back_to_maps" href="<?php echo url::base(); ?>mymaps/add2?id=<?php echo $map->id?>"><?php echo __('Back to page 2');?></a>
	</li>
</ul>

<h3><?php echo $map->title;?></h3>
<p><?php echo $map->description;?></p>
<p><?php echo __("Confirm that the below is true. If it is continue on to page 4, otherwise go back to page 2 and make some corrections.");?></p>



<?php if(count($errors) > 0 )
{
?>
	<div class="errors">
	<?php echo __("error"); ?>
		<ul>
<?php 
	foreach($errors as $error)
	{
?>
		<li> <?php echo $error; ?></li>
<?php
	} 
	?>
		</ul>
	</div>
<?php 
}
?>

<?php if(count($messages) > 0 )
{
?>
	<div class="messages">
		<ul>
<?php 
	foreach($messages as $message)
	{
?>
		<li> <?php echo $message; ?></li>
<?php
	} 
	?>
		</ul>
	</div>
<?php 
}
?>

<div class="errors">
	<?php if(count($errors) > 0){ echo '<h2>'.__('Errors').'</h2>';}?>
	<ul>
	<?php
		foreach($errors as $error)
		{
			echo '<li>'.$error.'</li>';
		}	 
	?>
	</ul>
</div>

<div class="warnings">
	<?php if(count($warnings) > 0){ echo '<h2>'.__('Warnings').'</h2>';}?>
	<ul>
	<?php
		foreach($warnings as $warning)
		{
			echo '<li>'.$warning.'</li>';
		}	 
	?>
	</ul>
</div>

<div>
<?php 	

	foreach($sheets as $sheet)
	{
		if($sheet->is_ignored == 0)
	 	{	
			echo '<h2>'.$sheet->name.'</h2>';
			echo '<h3>'.__('Regions in sheet ').$sheet->name.'</h2>';
			echo '<ul>';
			foreach($sheet_regions[$sheet->id] as $region)
			{
				echo '<li>'.$region.'</li>';
			}
			echo '</ul>';
			echo '<p>'. __('If the regions above are not correct please check the row that you set as the header and the columns you set as denoting regions.').'</p>';
			echo '<h3>'.__('Indicators in sheet ').$sheet->name.'</h2>';
			echo "\n";
			echo $sheet_indicators[$sheet->id];
			echo '<p>'. __('If the indicators above are not correct please check the rows that you set as data and the columns you set as denoting indicators.').'</p>';
	 	}
	}

?>

</div>
<div id="bottom nav">
<h3 class="navback"><a href="<?php echo URL::base();?>mymaps/add2?id=<?php echo $map_id;?>"><?php echo __('Back to Page 2')?></a></h3> 
<h3 class="navforward"><a href="<?php echo URL::base();?>mymaps/add4?id=<?php echo $map_id;?>"><?php echo __('Forward to Page 4')?></a>
<div style="clear:both;"></div>
</div>



