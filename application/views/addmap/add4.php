<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<div id="addmapMenu"><?php echo Helper_AddmapSubmenu::make_addmap_menu(4);?></div>	
		
<h2><?php echo __("Add Map - Page 4") ?></h2>
<ul class="context_menu">
	<li>
		<a class="button" id="back_to_maps" href="<?php echo url::base(); ?>mymaps/add3?id=<?php echo $map->id?>"><?php echo __('Back to page 3');?></a>
	</li>
</ul>

<h3><?php echo $map->title;?></h3>
<p><?php echo $map->description;?></p>
<p><?php echo __("Select which map template you want to use for your map");?></p>



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

<?php 
	echo Form::open(NULL, array('id'=>'add_map_form', 'enctype'=>'multipart/form-data', 'onsubmit'=>'setMapViewSettings()')); 
	echo Form::hidden('action','edit', array('id'=>'action'));
	echo Form::hidden('map_id',$map_id, array('id'=>'map_id'));
	
	//pull the map object from the DB
	$map = ORM::factory('Map', $map_id);
	echo Form::hidden('lat', $map->lat, array('id'=>'lat'));
	echo Form::hidden('lon', $map->lon, array('id'=>'lon'));
	echo Form::hidden('zoom', $map->zoom, array('id'=>'zoom'));
?>


<div class="data_specify_div">
	<table >
		<tr>
			<th><?php echo __('Select')?></th>
			<th><?php echo __('Title')?></th>
			<th><?php echo __('Description')?></th>
			<th><?php echo __('Admin Level')?></th>
			<th><?php echo __('Decimals')?></th>
		</tr>	
	
<?php 	
	
	if(count($templates) == 0)
	{
		echo '<tr><td colspan="4">'.__('There are no templates').'</td></tr>';
	}
	foreach($templates as $template)
	{
		if($template->id != 0){
			echo '<tr>';
			echo '<td>';
			echo Form::checkbox('template_id', $template->id, $data['template_id'] == $template->id);
			echo '</td>';
			echo '<td><a href="#" onclick="renderMap(\''.$template->file.'\','.$template->lat.','.$template->lon.','.$template->zoom.'); return false;">'.$template->title.'</a></td>';
			echo '<td>'.$template->description.'</td>';
			echo '<td>'.$template->admin_level.'</td>';
			echo '<td>'.($template->decimals == -1 ? 'No Rounding':$template->decimals).'</td>';
			echo '</tr>';
		}
	}

?>
	</table>
</div>


<!-- map display -->
<br />
<p> Adjust the default view of the map</p>
<div class="mapWLoading">
	<div id="map_div"></div>	
	<div id="map_loading"></div>
</div>



<?php
	echo Form::submit('Submit', 'Submit', array('onmouseover'=>'setMapViewSettings()'));	//, array('onmouseover'=>'setMapViewSettings()')
	echo Form::close();
?>

