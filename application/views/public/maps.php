<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo __('Publicly viewable maps');?></h2>


<?php if(count($errors) > 0 )
{
?>
	<div class="errors">
	<?php echo __('error'); ?>
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
<div class="searchBigDiv">
<?php
	echo Form::open(NULL, array('id'=>'searchPublicMapForm', 'method' => 'get'));
	echo Form::input('q', isset($_GET['q']) ? $_GET['q'] : null, array('id'=>'q', 'style'=>'width:600px;'));
	echo Form::submit('search', __('Search Maps'), array('id'=>'search_button'));
	echo Form::close();
?>
</div>
<div class="scroll_table">
<table class="list_table" >
	<thead>
		<tr class="header">			
			<th class="mapName" style="width:500px;">
				<?php echo __('Map');?>
			</th>
			<th class="mapTasks" style="width:355px;">
				<?php echo __('Tasks');?>
			</th>			
		</tr>
	</thead>
	<tbody>
	<?php
		if(count($maps) == 0)
		{
			echo '<tr><td colspan="3">'.__('You have no maps').'</td></tr>';
		}
		$i = 0;
		foreach($maps as $id=>$map){
			$slug = $map['slug'];
			$i++;
			$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
			$permission = $map['permission'];
		?>

	<tr <?php echo $odd_row; ?>>		
		<td class="mapName" style="width:500px;">
			<a href="<?php echo url::base(); ?><?php echo $slug;?>" >
				<?php echo substr($map['title'], 0, 40); echo strlen($map['title']) > 40 ? '...' : ''; echo $permission != null ? '('.$permission.')' : ''?>
			</a>
		</td>
		<td class="mapTasks" style="width:355px;">
			<ul>
			<?php if($permission == Model_Sharing::$owner OR $permission == Model_Sharing::$edit){?>
			<li>
				<a href="<?php echo url::base(); ?>mymaps/add1/?id=<?php echo $id;?>" > 
					<img class="edit" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Edit');?>
				</a>
			</li>
		
			<li>
				<a href="<?php echo url::base(); ?>mymaps/copy/?id=<?php echo $id;?>" > 
					<img class="copy" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Copy');?>
				</a>
			</li>
			<?php }?>
			<li>
				<a href="<?php echo url::base(); ?><?php echo $slug;?>" >
					<img class="view" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View');?>
				</a>
			</li>
			<li>
				<a rel="#overlay" href="<?php echo url::base(); ?>share/window?id=<?php echo $id;?>" >
					<img class="share" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Share');?>
				</a>
			</li>
			<?php if($permission == Model_Sharing::$owner){?>
			<li>
				<a href="#" onclick="deleteMap(<?php echo $id?>);">
					<img class="delete" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Delete');?>
				</a>
			</li>			
			<?php }?>
			</ul>
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


	<div class="apple_overlay" id="overlay">
		<div class="contentWrap">
			<img class="contentWrapWaiter" src="<?php echo URL::base();?>media/img/waiter_barber.gif"/>
		</div>
	</div>
	
	<div id="fb-root"></div>


