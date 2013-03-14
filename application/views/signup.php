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

<?php echo Kohana_Form::open(); 
//calculate disabled status
$attributes = array();
if($data['open_id_call'] != 0)
{
	$attributes['readonly'] = 1;
}
?>
	<table>
		<tr <?php if($data['open_id_call'] != 0){echo 'style="display:none;"';}?>>
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
		<tr <?php if($data['open_id_call'] != 0){echo 'style="display:none;"';}?>>
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
	<?php echo __('Terms of use').':'?>
	<div id="terms_of_use" readonly="readonly" style="height:300px; width:700px; overflow:auto; border-style:solid">
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis in urna in lectus pretium ultrices quis ut diam. 
				Aliquam imperdiet purus sit amet ante venenatis venenatis ornare quam imperdiet. Nullam tempus adipiscing tempus. Morbi erat leo, rhoncus cursus pellentesque ullamcorper, lacinia sit amet justo. In sed pellentesque libero. 
				Nullam rutrum turpis quis tellus fringilla imperdiet vitae ut lorem. Mauris tempus felis a justo malesuada tempor nec a enim. 
				Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut in nunc odio.
				Maecenas iaculis pharetra massa, id cursus mauris porta sit amet. Fusce elementum nisi vitae nibh interdum blandit vitae sed quam. Sed ornare lorem vitae ante hendrerit sit amet sollicitudin leo egestas. Integer lacinia dolor vel leo tincidunt tristique. 
				Nullam arcu ligula, consectetur et pretium eu, aliquam id nulla. Nulla et neque quis massa dapibus varius in vitae felis. Quisque varius lorem massa. Phasellus pellentesque ligula quis est sodales posuere. 
				Phasellus orci urna, dictum quis ultrices nec, imperdiet eu turpis. Nullam sit amet est enim, at convallis dolor. Nulla nec dolor turpis, vel euismod nibh. Proin interdum ligula in erat posuere tincidunt. Integer commodo pulvinar euismod. Vivamus vitae porttitor erat.
				 Vestibulum mollis volutpat massa at elementum. Nullam vitae justo sit amet orci imperdiet dictum nec et leo. Etiam eget dui a enim vehicula scelerisque. Proin interdum metus eu libero venenatis imperdiet. 
				Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
				Sed vel massa neque, vel consectetur ligula. Etiam et felis metus, sed vehicula tortor. Nullam sed tellus ligula. Donec et erat massa, a dignissim leo. Vestibulum sit amet elit sed magna fringilla viverra. 
				Maecenas eleifend, risus tristique placerat mattis, tellus nunc molestie augue, eget aliquet odio odio id augue. Maecenas commodo justo consectetur eros viverra venenatis.
				Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce suscipit consectetur ante vel ullamcorper. Morbi pretium fringilla dignissim. Suspendisse sodales sagittis nunc vel egestas. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
				Quisque est tellus, sagittis id sagittis vel, bibendum eu ligula. Morbi malesuada nibh vel tellus tempor laoreet. Ut sagittis sodales blandit.
				</div>
	<?php  echo Form::checkbox('terms'); echo Form::label('read_terms_of_use', __('Agree to terms of use'));  ?>
	<?php  echo Form::hidden('open_id_call', $data['open_id_call']); ?>
	<br/>
	<br/>
	<?php echo Form::submit("registration_form",  __('Sign Up')); ?>
			
<?php echo Kohana_Form::close(); ?>
