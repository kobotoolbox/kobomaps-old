<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo request::initial()->action() == 'index' ? __('Templates') : __('My Templates'); ?></h2>
<p><?php echo __('Templates are the base maps from which custom maps are made.');?></p>


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
<div class="searchBigDiv">
<?php
	echo Form::open(NULL, array('id'=>'searchPublicMapForm', 'method' => 'get'));
	echo Form::input('q', isset($_GET['q']) ? $_GET['q'] : null, array('id'=>'q', 'style'=>'width:600px;'));
	echo Form::submit('search', __('Search Templates'), array('id'=>'search_button'));
	echo Form::close();
?>
</div>
<?php if(request::initial()->action() != 'mine'){?>
<h4> <?php echo __('Official templates provided by Kobomaps. These templates were gathered from GADM.org.')?></h4>
<table class="list_table" >
	<thead>
		<tr class="header">
			<th style="width:200px;">
				<?php echo __('Map');?>
			</th>
			<th style="width:370px;">
				<?php echo __('Description');?>
			</th>
			<th style="width:150px;">
				<?php echo __('Actions');?>
			</th>
			<th style="width:100px;">
				<?php echo __('User');?>
			</th>
		</tr>
	</thead>
	<tbody style="height:300px;">
	<?php
		$j = 0;
		foreach($templates as $template){
			if(!$template->is_official){
				continue;
			}
			if($template->id != 0)	//ignore template
			{
				$j++;
				$odd_row = ($j % 2) == 0 ? 'class="odd_row"' : '';
		?>

	<tr <?php echo $odd_row; ?> style="height:50px;">
		<td style="width:200px;">
			<?php echo '<strong>';?>
			<?php if(!$is_admin AND $user->id != $template->user_id){$action='view';}else{$action='edit';}?>
			<a href="<?php echo url::base(); ?>templates/<?php echo $action.'?id='.$template->id;?>" ><?php echo $template->title; ?></a>
			<?php echo '</strong>';?>
		</td>
		<td style="width:370px;">
			<?php echo $template->description; ?>
		</td>
		<td style="width:150px;" class="templateTasks">
			<ul>
			<?php if(!$is_admin AND ($template->is_official == 1 OR $template->user_id != $user->id)){?>
				<li>
					<a href="<?php echo url::base(); ?>templates/view?id=<?php echo $template->id;?>" >
						<img class="viewTemplate" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View');?>
					</a>
				</li>
			<?php }?>
			<li>
				<a href="<?php echo url::base(); ?>templates/copy?id=<?php echo $template->id;?>" >
					<img class="copy" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Copy');?>
				</a>
			</li>
			</ul>
		</td>
		<td style="width:100px;">
			<?php echo $template->user_id == 1 ? __('Kobomaps') : ($template->username . ($is_admin ? ' - '. $template->user_id : ''));?>
		</td>
	</tr>
	<?php 
			}
		}
		?>
	</tbody>
</table>
</br>
<?php }?>
<h4> <?php echo __('Personal and Publically owned templates.')?></h4>
<table class="list_table" >
	<thead>
		<tr class="header">
			<th style="width:200px;">
				<?php echo __('Map');?>
			</th>
			<th style="width:370px;">
				<?php echo __('Description');?>
			</th>
			<th style="width:150px;">
				<?php echo __('Actions');?>
			</th>
			<th style="width:100px;">
				<?php echo __('User');?>
			</th>
		</tr>
	</thead>
	<tbody style="height:300px;">
	<?php
		if(count($templates) == 0)
		{
			echo '<tr><td colspan="4" style="text-align:center;width:860px;">'.__('you have no templates').'</td></tr>';
		}
		$i = 0;
		foreach($templates as $template){
			if($template->is_official){
				continue;
			}
			if($template->id != 0)	//ignore template
			{
				$i++;
				$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
		?>

	<tr <?php echo $odd_row; ?> style="height:50px;">
		<td style="width:200px;">
			<?php echo $template->is_official == 1 ? '<strong>':'';?>
			<?php if(!$is_admin AND $user->id != $template->user_id){$action='view';}else{$action='edit';}?>
			<a href="<?php echo url::base(); ?>templates/<?php echo $action.'?id='.$template->id;?>" ><?php echo $template->title; ?></a>
			<?php echo $template->is_official == 1 ? '</strong>':'';?>
		</td>
		<td style="width:370px;">
			<?php echo $template->description; ?>
		</td>
		<td style="width:150px;" class="templateTasks">
			<ul>
			<?php if(!$is_admin AND ($template->is_official == 1 OR $template->user_id != $user->id)){?>
				<li>
					<a href="<?php echo url::base(); ?>templates/view?id=<?php echo $template->id;?>" >
						<img class="viewTemplate" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View');?>
					</a>
				</li>
			<?php }else{?>
			<li>
				<a href="<?php echo url::base(); ?>templates/edit?id=<?php echo $template->id;?>" >
					<img class="editTemplate" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Edit');?>
				</a>
			</li>
			<li>
				<a href="#" onclick="deleteTemplate(<?php echo $template->id?>);">
					<img class="delete" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Delete');?>
				</a>
			</li>
			<?php }?>
			<li>
				<a href="<?php echo url::base(); ?>templates/copy?id=<?php echo $template->id;?>" >
					<img class="copy" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Copy');?>
				</a>
			</li>
			</ul>
		</td>
		<td style="width:100px;">
			<?php echo $template->user_id == 1 ? __('Kobomaps') : ($template->username . ($is_admin ? ' - '. $template->user_id : ''));?>
		</td>
	</tr>
	<?php 
			}
		}
		?>
	</tbody>
</table>

<?php
echo Form::open(NULL, array('id'=>'edit_template_form')); 
echo Form::hidden('action','edit', array('id'=>'action'));
echo Form::hidden('template_id','0', array('id'=>'template_id'));
echo Form::close();
?>


