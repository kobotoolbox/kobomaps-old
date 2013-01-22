<h2><?php echo __('Sign Up'); ?></h2>
<p><?php echo __('Fill out this form to sign up for Kobo Maps');?></p>


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
				<?php echo Form::label('email_address', __('email address'));  ?>
			</td>
			<td>
				<?php echo Form::input('email', $data['email']);?>
			</td>
			
			<td>
				<?php echo Form::label('user_name', __('user name'));  ?>
			</td>
			<td>
				<?php echo Form::input('username',$data['username']);?>
			</td>
			
		</tr>
		<tr>			
			<td>
				<?php echo Form::label('first_name', __('first name'));  ?>
			</td>
			<td>
				<?php echo Form::input('first_name', $data['first_name']);?>
			</td>
	
			<td>
				<?php echo Form::label('last_name', __('last name'));  ?>
			</td>
			<td>
				<?php echo Form::input('last_name', $data['last_name']);?>
			</td>
					
		</tr>
		<tr>
			<td>
				<?php echo Form::label('password', __('password'));  ?>
			</td>
			<td>
				<?php echo Form::password('password', $data['password']);?>
			</td>
			
			<td>
				<?php echo Form::label('password_again', __('password again'));  ?>
			</td>
			<td>
				<?php echo Form::password('password_confirm', $data['password_confirm']);?>
			</td>
			
		</tr>
		
	</table>
	<br/>
	<?php  echo Form::checkbox('terms'); echo Form::label('read_terms_of_use', __('Agree to terms of use'));  ?>
	<br/>
	<br/>
	<?php echo Form::submit("registration_form",  __('Sign Up')); ?>
			
<?php echo Kohana_Form::close(); ?>
