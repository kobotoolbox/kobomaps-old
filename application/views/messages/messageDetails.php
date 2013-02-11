<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* messageDetails.php - View
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-08
*************************************************************/
?>

<div class="messageDetails" id="messageDetails_<?php echo $message->map_id?>">

	<p style="font-weight: bold">  <?php echo __('Date submitted:');?> <?php echo $message->date;?> </p>
	
	<a style="font-weight: bold" href="<?php echo URL::base()?>public/view/?id=<?php echo $message->map_id?>">  <?php echo __('Map');?>: <?php echo $map->title;?> </a>

	<p> <?php echo __('From')?>:
	<?php if($message->poster_name == ""){
			echo __('No name given.');
		}
		else{
			echo $message->poster_name;
		}
	?>
	</p>
	
	<p> <?php echo __('Email')?>:
	<?php if($message->poster_email == ""){
			echo __('No email given.');
		}
		else{
			echo $message->poster_email;
		}
	?>
	</p>
	</br>
	<p> <?php echo __('Comment')?>: </br></br> <?php echo $message->message;?></p>

</div>

<script type ="text/javascript">
	//changes weight of message text to read
	$("#messageRow<?php echo $message->id ?>").removeClass("unread");
</script>