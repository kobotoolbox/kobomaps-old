<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo __("Add Map - Page 2") ?></h2>
<ul class="context_menu">
	<li>
		<a class="button" id="back_to_maps" href="<?php echo url::base(); ?>mymaps"><?php echo __('Back to My Maps');?></a>
	</li>
</ul>

<h3><?php echo $map->title;?></h3>
<p><?php echo $map->description;?></p>
<p><?php echo __("Now tell us about the structure of you data");?></p>
<ul>
	<li>
		<strong><?php echo __('Header Rows');?></strong> - <?php echo __('Tell us which row is used as the header. This is the row that denotes the geographic areas. 
				You can only specify one row as a header.')?>
	</li>
	<li>
		<strong><?php echo __('Data Rows');?></strong> - <?php echo __('Tell us which rows are used to store data for an indicator. 
				You can specify as many data rows as needed, but there must be at least one.')?>
	</li>
	<li>
		<strong><?php echo __('Indicator Columns');?></strong> - <?php echo __('Tell us which columns are used to specify the indicators or questions that the data in the rest 
				of the spreadsheet shows. You can have mutliple columns that represent mulitple levels. You can specify multiple indicator columns')?>
	</li>
	<li>
		<strong><?php echo __('Region Columns');?></strong> - <?php echo __('Tell us which columns are used to hold the information that pertains to a specific geographic region.
				You can speicfy mulitple region columns.')?>
	</li>
	<li>
		<strong><?php echo __('Total Column - Optional');?></strong> - <?php echo __('Which column represents the total for all geographic regions.
				You can only specify one total column')?>
	</li>
	<li>
		<strong><?php echo __('Total Label Column - Optional');?></strong> - <?php echo __('Which column presents the lable for the total column for each indicator (for example, this would not have to be labeled "total"). ')?>
	</li>
	<li>
		<strong><?php echo __('Unit Column - Optional');?></strong> - <?php echo __('Which column stores the units for each indicator. You can only specify
				one unit column.')?>
	</li>
	<li>
		<strong><?php echo __('Source Column - Optional');?></strong> - <?php echo __('Which column stores the name of the source for each indicator.
				You can only specify one source column.')?>
	</li>
	<li>
		<strong><?php echo __('Source Link Column - Optional');?></strong> - <?php echo __('Which column stores the link to the source for each indicator.
				You can only specify one source link column.')?>
	</li>
	<li>
		<strong><?php echo __('Ignore - Optional');?></strong> - <?php echo __('Set any column or row to ignore if it should not be taken into consideration when 
				rendering the map. You can set multiple columns and rows to ignore.')?>
	</li>
</ul>



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

<div>
<?php 	


	echo Form::open(NULL, array('id'=>'add_map_form', 'enctype'=>'multipart/form-data')); 
	echo Form::hidden('action','edit', array('id'=>'action'));
	echo Form::hidden('map_id',$map_id, array('id'=>'map_id'));
	foreach($sheets as $sheet_model)
	{
		echo '<h2>'.__('Sheet').': '.$sheet_model->name.'</h2>';
		echo '<div class="data_specify_div" id="data_specify_div_'.$sheet_model->id.'">';
		echo '<table class="data_specify_table" id="data_specify_table_'.$sheet_model->id.'">';
	
		$sheet = $sheet_data[$sheet_model->name];
		
		foreach($sheet as $row_index=>$row)
		{
			//do this if it's the first row
			if($row_index == 1)
			{
				echo '<tr><th></th>';
				foreach($row as $column_index=>$column)
				{
					echo '<th class="header">';
					echo $column_index . '<br/>';
					echo Form::select('column['.$sheet_model->id.']['.$column_index.']', $column_types, $data['column'][$sheet_model->id][$column_index], array('id'=>'column_'.$sheet_model->id.'_'.$column_index));
					echo '</th>';
				}
				echo '</tr>';
			}
			echo '<tr><td class="header">'.$row_index. ' ';
			echo Form::select('row['.$sheet_model->id.']['.$row_index.']', $row_types, $data['row'][$sheet_model->id][$row_index], array('id'=>'row_'.$sheet_model->id.'_'.$row_index));
			echo '</td>';
			foreach($row as $column_index=>$column)
			{
				echo '<td class="sheet_'.$sheet_model->id.' row_'.$row_index.' column_'.$column_index.'">'.$column.'</td>';
			}
			echo '</tr>';
		}
		
		echo '</table>';
		echo '</div>';
	}
	echo Form::submit('Submit', 'Submit');
	echo Form::close();
?>

</div>





