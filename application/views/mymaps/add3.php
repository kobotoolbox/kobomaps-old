<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>


		
<h2><?php echo __('Add Map - Validation') . ' - '.$map->title; ?></h2>

<h3><?php echo __('Confirm that the below is true. If it is continue on, otherwise go back to the Data Structure page(s) and make corrections.');?></h3>



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

<?php if(count($errors) > 0){?>
<div class="errors">
	 <?php echo '<h2>'.__('Errors').'</h2>';?>
	<ul>
	<?php
		foreach($errors as $error)
		{
			echo '<li>'.$error.'</li>';
		}	 
	?>
	</ul>
</div>
<?php }?>

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
		echo '<h2>'.$sheet->name.'</h2>';
		echo '<h3><a href="'.URL::base().'mymaps/add2?id='.$sheet->map_id.'&sheet='.$sheet->position.'">'.__('Regions in sheet').' '.$sheet->name.'</a></h2>';
		echo '<ul>';
		foreach($sheet_regions[$sheet->id] as $region)
		{
			echo '<li>'.$region.'</li>';
		}
		echo '</ul>';
		echo '<p>'. __('If the regions above are not correct please check the row that you set as the header and the columns you set as denoting regions.').'</p>';
		echo '<h3><a href="'.URL::base().'mymaps/add2?id='.$sheet->map_id.'&sheet='.$sheet->position.'">'.__('Indicators in sheet').' '.$sheet->name.'</a></h3>';
		echo "\n";
		echo $sheet_indicators[$sheet->id];
		echo '<p>'. __('If the indicators above are not correct please check the rows that you set as data and the columns you set as denoting indicators.').'</p>';
 	
	}

	echo Form::open(NULL, array('id'=>'add_map_form', 'enctype'=>'multipart/form-data'));
	echo Form::hidden('action','edit', array('id'=>'action'));
	echo Form::hidden('map_id',$map_id, array('id'=>'map_id'));
	echo Form::submit('Submit', __('Continue'));
	echo Form::close();
	
?>

</div>




