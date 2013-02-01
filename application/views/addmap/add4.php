<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

		
<h2><?php echo __('Add Map - Geo Set-up') . ' - '.$map->title ?></h2>
<h3><?php echo __('Select which map template you want to use for your map');?></h3>
<p><?php echo __('Templates in bold, and marked official, are provided by KoBo staff.')?></p>
<p><?php  echo __('If you want to add your own template click'). ' <a href="'.URL::base().'templates/edit">'.__('here').'</a>' ;?></p>



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


<div class="data_specify template_select">

	<div class="q2">
		<table class="data_specify_table">
			<thead>
				<tr>
					<th class="selectColumn"><div><?php echo __('Select')?></div></th>
					<th style="width:300px;"><div><?php echo __('Title')?></div></th>
					<th style="width:250px;"><div><?php echo __('Description')?></div></th>
					<th style="width:50px;"><div><?php echo __('Admin Level')?></div></th>
					<th style="width:90px;"><div><?php echo __('Decimals')?></div></th>
					<th style="width:60px;"><div><?php echo __('Official')?></div></th>
				</tr>	
			</thead>
		</table>
	</div>
	<div class="q4">
		<table class="data_specify_table">
	<?php 	
		
		if(count($templates) == 0)
		{
			echo '<tr><td colspan="5">'.__('There are no templates').'</td></tr>';
		}
		foreach($templates as $template)
		{
			
			if($template->id != 0){
				//handle truncating long descriptions
				$description = strlen($template->description) < 50 ? $template->description : substr($template->description,0,50) . '...';
				echo '<tr>';
				echo '<td class="selectColumn">';
				echo Form::radio('template_id', $template->id, $data['template_id'] == $template->id, array('onchange'=>'renderMap("'.$template->file.'",'.$template->lat.','.$template->lon.','.$template->zoom.'); return false;'));
				echo '</td>';
				echo '<td style="width:300px;">';
				echo $template->is_official == 1 ? '<strong>':'';
				echo '<a href="#" onclick="renderMap(\''.$template->file.'\','.$template->lat.','.$template->lon.','.$template->zoom.'); return false;">'.$template->title.'</a>';
				echo $template->is_official == 1 ? '</strong>':'';
				echo '</td>';
				echo '<td style="width:250px;"><span title="'.$template->description.'">'.$description.'</span></td>';
				echo '<td style="width:50px;">'.$template->admin_level.'</td>';
				echo '<td style="width:90px;">'.($template->decimals == -1 ? __('No Rounding'):$template->decimals).'</td>';
				echo '<td style="width:60px;text-align:center;">'.($template->is_official == 1 ? 'X':'').'</td>';
				echo '</tr>';
			}
		}
	
	?>
		</table>
	</div>
</div>


<!-- map display -->
<br />
<h3> <?php echo __('Pan and zoom the map to adjust what the default view will be for the map')?></h3>
<div class="mapWLoading">
	<div id="map_div"></div>	
	<div id="map_loading"></div>
</div>



<?php
	echo Form::submit('Submit', __('Continue'), array('onsubmit'=>'setMapViewSettings()'));	//, array('onmouseover'=>'setMapViewSettings()')
	echo Form::close();
?>

