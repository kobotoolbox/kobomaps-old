<h2><?php echo __("login"); ?></h2>
<p><?php echo __("login explanation");?></p>

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

<?php echo Kohana_Form::open(); ?>
	<table>
		<tr>
			<td>
				<?php echo __("user name");  ?>
			</td>
			<td>
				<?php echo Form::input('username', null, array('id'=>'username'));?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo __("password");  ?>
			</td>
			<td>
				<?php echo Form::password('password', null, array('id'=>'password'));?>
			</td>
		</tr>
		<tr>
			<td>
				<br/>
				<a rel="#overlay" href="" ><?php echo __("forgot password");  ?></a>
			</td>
			<td>			
			</td>
		</tr>
	</table>
	<br/>
	<br/>
	<?php echo Form::submit("login_form",  __("login")); ?>
<?php echo Kohana_Form::close(); ?>	
	
	<div class="apple_overlay" id="overlay">
		<div class="contentWrap">
			<h2><?php echo __('forgot password'); ?></h2>
			<p><?php echo __('reset instructions');?></p>
			<?php echo __('email address'); ?> <input style="width:200px;" name="reset_email" id="reset_email">
			<br/>
			<?php echo Form::button("rest_form",  __("reset"), array('onclick'=>"submit_reset(); return false;")); ?> <img id="reset_spinner" style="display:none;" src="<?php echo url::base(); ?>media/img/wait16trans.gif"/>
			<br/><br/>
			<div id="reset_response" style="display:none;">
			</div>
		</div>
	</div>
	
