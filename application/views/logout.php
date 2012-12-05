<h2><?php echo __("logout"); ?></h2>
<p><?php echo __("logout explanation");?></p>

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

<?php echo __("come on back now");?>

