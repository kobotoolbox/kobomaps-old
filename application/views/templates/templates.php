<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo __("Map Templates"); ?></h2>
<p><?php echo __("These are the map templates that are used to create maps");?></p>


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
<a class="button" id="add_template_button" href="<?php echo URL::base();?>templates/edit"><?php echo __('Add A Template');?></a>
</p>
<table class="list_table" >
	<thead>
		<tr class="header">
			<th style="width:200px;">
				<?php echo __('Map');?>
			</th>
			<th style="width:400px;">
				<?php echo __('Description');?>
			</th>
			<th style="width:200px;">
				<?php echo __('actions');?>
			</th>
		</tr>
	</thead>
	<tbody style="height:700px;">
	<?php
		if(count($maps) == 0)
		{
			echo '<tr><td colspan="3">'.__('you have no templates').'</td></tr>';
		}
		$i = 0;
		foreach($maps as $map){
			$i++;
			$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
		?>

	<tr <?php echo $odd_row; ?> style="height:50px;">
		<td style="width:200px;">
			<a href="<?php echo url::base(); ?>templates/edit?id=<?php echo $map->id;?>" ><?php echo $map->title; ?></a>
		</td>
		<td style="width:400px;">
			<?php echo $map->description; ?>
		</td>
		<td style="width:200px;">
			<a href="<?php echo url::base(); ?>templates/edit?id=<?php echo $map->id;?>" > <?php echo __('edit');?></a>
			<a href="#" onclick="deleteTemplate(<?php echo $map->id?>);"> <?php echo __('delete');?></a>
		</td>
	</tr>
	<?php }?>
	</tbody>
</table>
<?php
echo Form::open(NULL, array('id'=>'edit_template_form')); 
echo Form::hidden('action','edit', array('id'=>'action'));
echo Form::hidden('template_id','0', array('id'=>'template_id'));
echo Form::close();
?>


