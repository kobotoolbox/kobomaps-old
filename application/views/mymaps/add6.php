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
//trying to substring out of the $data['style'] will be moved to the controller
	$string = $data['style'];
	$ppos = strpos($string, '{');
	$rpos1 = strpos($string, '}');
	$rpos2 = strpos($string, '}', $rpos1 + 1) + 1;
	$sub = substr($string, $ppos, $rpos2 - $ppos);
	$string = substr($string, 1 + $rpos2 - $ppos);
	echo $sub;
	echo "</br>";
	$ppos = strpos($string, '{');
	$rpos1 = strpos($string, '}');
	$rpos2 = strpos($string, '}', $rpos1 + 1) + 1;
	$sub = substr($string, $ppos, $rpos2 - $ppos);
	$string = substr($string, 1 + $rpos2 - $ppos);
	echo $sub;
	echo "</br>";
	$ppos = strpos($string, '{');
	$rpos1 = strpos($string, '}');
	$rpos2 = strpos($string, '}', $rpos1 + 1) + 1;
	$sub = substr($string, $ppos, $rpos2 - $ppos);
	$string = substr($string, 1 + $rpos2 - $ppos);
	echo $sub;
	echo "</br>";
	$ppos = strpos($string, '{');
	$rpos1 = strpos($string, '}');
	$rpos2 = strpos($string, '}', $rpos1 + 1) + 1;
	$sub = substr($string, $ppos, $rpos2 - $ppos);
	$string = substr($string, 1 + $rpos2 - $ppos);
	echo $sub;
	echo "</br>";
	$ppos = strpos($string, '{');
	$rpos1 = strpos($string, '}');
	$rpos2 = strpos($string, '}', $rpos1 + 1) + 1;
	$sub = substr($string, $ppos, $rpos2 - $ppos);
	$string = substr($string, 1 + $rpos2 - $ppos);
	echo $sub;
	echo "</br>";

	
?>

</div>





