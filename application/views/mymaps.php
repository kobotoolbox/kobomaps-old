<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo __("My Maps"); ?></h2>
<p><?php echo __("These are the maps you have created");?></p>


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
<a class="button" id="add_map_button" href="<?php echo URL::base();?>mymaps/add1"><?php echo __('add map');?></a>
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
			echo '<tr><td colspan="3">'.__('you have no maps').'</td></tr>';
		}
		$i = 0;
		foreach($maps as $map){
			$i++;
			$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
		?>

	<tr <?php echo $odd_row; ?> style="height:50px;">
		<td style="width:200px;">
			<a href="<?php echo url::base(); ?>public/view/?id=<?php echo $map->id;?>" >
				<?php echo substr($map->title, 0, 40); echo strlen($map->title) > 40 ? '...' : ''; ?>
			</a>
		</td>
		<td style="width:400px;">
			<?php echo substr($map->description, 0, 50); echo strlen($map->description) > 50 ? '...' : ''; ?>
		</td>
		<td style="width:200px;">
			<a href="<?php echo url::base(); ?>mymaps/add1/?id=<?php echo $map->id;?>" > <?php echo __('edit');?></a>
			<a href="#" onclick="deleteForm(<?php echo $map->id?>);"> <?php echo __('delete');?></a>
		</td>
	</tr>
	<?php }?>
	</tbody>
</table>
<?php
echo Form::open(NULL, array('id'=>'edit_map_form')); 
echo Form::hidden('action','edit', array('id'=>'action'));
echo Form::hidden('map_id','0', array('id'=>'map_id'));
echo Form::close();
?>


