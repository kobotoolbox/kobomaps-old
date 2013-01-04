<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<div id="addmapMenu"><?php echo Helper_AddmapSubmenu::make_addmap_menu(5);?></div>	
		
<h2><?php echo __("Add Map - Page 5") ?></h2>
<ul class="context_menu">
	<li>
		<a class="button" id="back_to_maps" href="<?php echo url::base(); ?>mymaps/add4?id=<?php echo $map->id?>"><?php echo __('Back to page 4');?></a>
	</li>
</ul>

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
	
	foreach($sheets as $sheet)
	{
		echo '<h2>'.__('Sheet').': '.$sheet->name.'</h2>';
		echo '<table>';
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
			}

			echo Form::select('region['.$sheet->id.']['.$column->id.']', $map_regions,$selected, $extras_array);
			echo '</td></tr>';
			
		}
		echo '</table>';
			
	}

	echo Form::submit('Submit', 'Submit');
	echo Form::close();
?>

