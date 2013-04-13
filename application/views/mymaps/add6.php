<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* mapstyle.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 2013-03-29
*************************************************************/
?>

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

<div>
<?php 	
	foreach($style as $s){
		echo 'featureType:'.$s->featureType.' elementType:'.$s->elementType.'</br>';
		print_r($s->stylers);
	}
	
?>

</div>





