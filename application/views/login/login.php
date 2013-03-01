<h2><?php echo __('Log In'); ?></h2>
<p><?php echo __('Enter your password and username below to log in');?></p>

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
	<table id="logintable">
		<tr>
			<td>
				<?php echo __('user name');  ?>
			</td>
			<td>
				<?php echo Form::input('username', null, array('id'=>'username'));?>
			</td>
			<td colspan="2" style="border-left: solid 1px #aaa;padding-left:15px;text-align:center;">
				<h3><?php echo __('OpenID Login')?></h3>				
			</td>
		</tr>
		<tr>
			<td>
				<?php echo __('password');  ?>
			</td>
			<td>
				<?php echo Form::password('password', null, array('id'=>'password'));?>
			</td>
			<td  style="border-left: solid 1px #aaa;padding-left:15px;">
				<?php echo __('Email');  ?>
			</td>
			<td>
				<?php echo Form::input('email', null, array('id'=>'email'));?>
			</td>
			
		</tr>
		<tr>
			<td>
				<br/>
				<a rel="#overlay" href="" ><?php echo __("Forgot password?");  ?></a>
			</td>
			<td>			
			</td>
		</tr>
	</table>
	<br/>
	<br/>
	<?php echo Form::submit("login_form",  __("Log In")); ?>
<?php echo Kohana_Form::close(); ?>	

	
	<div class="apple_overlay" id="overlay">
		<div class="contentWrap">
			<h2><?php echo __('Forgot Password'); ?></h2>
			<p><?php echo __('Enter your email address and instructions to reset your password will be emailed to you');?></p>
			<?php echo __('email address'); ?> <input type="text" style="width:200px;" name="reset_email" id="reset_email">
			<br/>
			<?php echo Form::button("rest_form",  __('Submit'), array('onclick'=>"submit_reset(); return false;")); ?> <img id="reset_spinner" style="display:none;" src="<?php echo url::base(); ?>media/img/wait16trans.gif"/>
			<br/><br/>
			<div id="reset_response" style="display:none;">
			</div>
		</div>
	</div>
	
