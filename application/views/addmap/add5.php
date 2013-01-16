<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

		
<h2><?php echo __("Add Map - Page 5") ?></h2>


<h3><?php echo $map->title;?></h3>
<p><?php echo $map->description;?></p>
<p><?php echo __('Select how the regions specified in your data match up to the regions in the template you have chosen. A drop down box surrouned in <strong><span style="color:#ff9900;">orange</span></strong> need to be set since no match was deteced for them.');?></p>



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
	echo Form::open(NULL, array('id'=>'add_map_form', 'enctype'=>'multipart/form-data')); 
	echo Form::hidden('action','edit', array('id'=>'action'));
	echo Form::hidden('map_id',$map_id, array('id'=>'map_id'));
	echo Form::hidden('sheet_position',$sheet_position, array('id'=>'sheet_position'));
	//keep track of how many sheets
	
	echo '<p>'.__('Number of regions that couldn\'t be automatically matched:'). ' <span id="notAutoMatchedCount"></span></p>';

	foreach($sheets as $sheet)
	{
		echo '<h2>'.__('Sheet').': '.$sheet->name.'</h2>';
		echo '<table>';
		$not_matched_count = 0;
		foreach($region_columns[$sheet->id] as $column)
		{
			$header_row = $header_rows[$sheet->id];
			$sheet_val = $sheet_data[$sheet->id];
			$row_data = $sheet_val[$header_row->name];
			$name = $row_data[$column->name];
			echo '<tr><td><strong>'.$name. '</strong> '. __('Maps to the templates region'). ': </td><td>';
			$selected = $data[$sheet->id][$column->id];
			//now that $selected is initialized, see if we can guess at 
			if($selected == null) //then guess
			{
				foreach($map_regions as $key=>$value)
				{
					if(strcmp(strtolower(trim($value)), strtolower(trim($name))) === 0)
					{
						$selected = $key;
						break;
					}
				}
			}
			//define the things like style and ID that are needed for our drop downs
			$extras_array = array('id'=>'region['.$sheet->id.']['.$column->id.']', 'style'=>'width:300px;');
			
			//if after all that we're still not selected
			if($selected == null)
			{

				$selected = 0;
				$extras_array['class'] = 'needstobemapped';
				$not_matched_count++;
			}

			echo Form::select('region['.$sheet->id.']['.$column->id.']', $map_regions,$selected, $extras_array);
			echo '</td></tr>';
			
		}
		
		//extend the run time if need be
		if(count($region_columns[$sheet->id]) > 51)
		{
			set_time_limit(30);
		}
		echo '</table>';
			
	}
	echo "<br/>";
	echo __("All following sheets have the region settings?");
	echo Form::checkbox('same_settings', null, false, array('id'=>'same_settings' ));
	echo "<br/>";
	echo "<br/>";
	echo Form::submit('Submit', 'Submit');
	echo Form::close();
	if($not_matched_count > 0)
	{
		echo '<script type="text/javascript">$(document).ready(function(){
	   		$("#notAutoMatchedCount").text("'.$not_matched_count.'");});</script>';
	}
?>

