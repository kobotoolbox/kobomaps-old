<h2><?php echo __("Profile"); ?></h2>
<p><?php echo __("Set your profile as needed");?></p>


<?php if(count($errors) > 0 )
{
?>
	<div class="errors">
	<?php echo __("Error"); ?>
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

<?php echo Kohana_Form::open(); ?>
	<table>
		<tr>
			<td>
				<?php echo Form::label('email_address', __("email address"));  ?>
			</td>
			<td>
				<?php echo Form::input('email', $data['email']);?>
			</td>
			
			<td>
				<?php echo Form::label('user_name', __("user name"));  ?>
			</td>
			<td>
				<?php echo Form::input('username',$data['username']);?>
			</td>
			
		</tr>
		<tr>			
			<td>
				<?php echo Form::label('first_name', __("first name"));  ?>
			</td>
			<td>
				<?php echo Form::input('first_name', $data['first_name']);?>
			</td>
	
			<td>
				<?php echo Form::label('last_name', __("last name"));  ?>
			</td>
			<td>
				<?php echo Form::input('last_name', $data['last_name']);?>
			</td>
					
		</tr>
		<tr>
			<td>
				<?php echo Form::label('password', __("password"));  ?>
			</td>
			<td>
				<?php echo Form::password('password');?>
			</td>
			
			<td>
				<?php echo Form::label('password_again', __("password again"));  ?>
			</td>
			<td>
				<?php echo Form::password('password_confirm');?>
			</td>
			
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php echo Form::label('email_alerts', __('Receive email alerts'));  ?>
			</td>
			<td>
				<?php echo Form::checkbox('email_alerts', '1', 1==intval($data['email_alerts'])); ?>
			</td>
			<td>
				<?php echo Form::label('email_warnings', __('Receive email warnings'));  ?>
			</td>
			<td>
				<?php echo Form::checkbox('email_warnings','1', 1==intval($data['email_warnings'])); ?>
			</td>
		</tr>
		
	</table>

	<br/>
	<?php echo Form::submit("registration_form",  __("Submit")); ?>
			
<?php echo Kohana_Form::close(); ?>
