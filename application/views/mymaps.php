<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<p><?php echo __("Select a map to edit or create a new one");?></p>


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
<p>

</p>
<div class="scroll_table">
<table class="list_table" >
	<thead>
		<tr class="header">
			<th class="selectColumn">
				<?php echo __('Select');?>
			</th>
			<th class="mapName">
				<?php echo __('Map');?>
			</th>
			<th class="mapTasks">
				<?php echo __('Tasks');?>
			</th>
			<th class="lastColumn">
				<?php echo __('Public');?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
		if(count($maps) == 0)
		{
			echo '<tr><td colspan="3">'.__('you have no maps').'</td></tr>';
		}
		$i = 0;
		foreach($maps as $map){
			$i++;
			$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
		?>

	<tr <?php echo $odd_row; ?>>
		<td class="selectColumn">
			X
		</td>
		<td class="mapName">
			<a href="<?php echo url::base(); ?>public/view/?id=<?php echo $map->id;?>" >
				<?php echo substr($map->title, 0, 40); echo strlen($map->title) > 40 ? '...' : ''; ?>
			</a>
		</td>
		<td class="mapTasks">
			<a href="<?php echo url::base(); ?>mymaps/add1/?id=<?php echo $map->id;?>" > <?php echo __('edit');?></a>
			<a href="#" onclick="deleteMap(<?php echo $map->id?>);"> <?php echo __('delete');?></a>
		</td>
		<td class="lastColumn">
			X
		</td>
	</tr>
	<?php }?>
	</tbody>
</table>
</div>
<?php
echo Form::open(NULL, array('id'=>'edit_map_form')); 
echo Form::hidden('action','edit', array('id'=>'action'));
echo Form::hidden('map_id','0', array('id'=>'map_id'));
echo Form::close();
?>


