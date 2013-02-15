<h2><?php echo __('Password Reset'); ?></h2>
<p><?php //echo __('Enter your password and username below to log in');?></p>

<?php if(count($errors) > 0 )
{
?>
	<div class="errors">
	<?php echo __('Error'); ?>
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

<?php echo Kohana_Form::open(); ?>
	<table>
		<tr>
			<td>
				<?php echo __('password');  ?>
			</td>
			<td>
				<?php echo Form::password('password', null, array('id'=>'password'));?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo __('password confirm');  ?>
			</td>
			<td>
				<?php echo Form::password('password_confirm', null, array('id'=>'password_confirm'));?>
			</td>
		</tr>		
	</table>
	<br/>
	<br/>
	<?php echo Form::submit('reset_form',  __("Reset Password")); ?>
<?php echo Kohana_Form::close(); ?>	

	
