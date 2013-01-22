<h2><?php echo __('Log Out'); ?></h2>
<p><?php echo __('log out explanation');?></p>

<?php if(count($errors) > 0 )
{
?>
	<ul class="errors">
<?php 
	foreach($errors as $error)
	{
?>
	<li> <?php echo $error; ?></li>
<?php
	} 
	?>
	</ul>
<?php 
}
?>



