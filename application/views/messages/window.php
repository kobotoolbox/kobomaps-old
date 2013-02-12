<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* window.php - View
* This software is copy righted by Kobo 2013
* Written by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-06
*************************************************************/
?>

<h3> <?php echo __('These are the comments that have been submitted for your maps.')?></h3>
<?php echo Form::open(NULL, array('id'=>'edit_messages_form')); ?> 
<div class="messageTable" scroll="auto">
	</br>
	<a href="#" class="deleteSelectedBtn"><?php echo __('Delete Selected'); ?></a>
	<table border="3" class="list_table">
		<thead>
		<tr class="header">
			<th style="width: 60px"> <?php echo __('Select').'</br>'.Form::checkbox('select_all', null, false, array('id'=>'selectAll'));?></th>
			<th style="width: 167px"> <?php echo __('Date')?></th>
			<th style="width: 285px"><?php echo __('Name')?></th> 
			<th style="width: 285px"> <?php echo __('Email Address')?></th>
			<th style="width: 90px"> <?php echo __('Tasks')?> </th>
		</thead>
		<tbody>
			<?php 
			if(count($messages) == 0)
			{
				echo '<tr><td colspan="5" style="width:880px;text-align:center;">'.__('You have no messages').'</td></tr>';
			}
			$i = 0;
				foreach($messages as $message){
					$i++;
					$odd_row = ($i % 2) == 0 ? ' odd_row' : '';
					
					echo '<tr ';
					
					if($message->unread == 1){
						echo 'class="unread'.$odd_row.'" id="messageRow'.$message->id.'">';
					}
					else {
						echo 'class="'.$odd_row.'" id="messageRow'.$message->id.'">';
					}
					
					
					echo '<td style="width:65px">';
					echo Form::checkbox('message_check['.$message->id.']', null, false, array('id'=>'message_check_'.$message->id));
					echo '</td><td style="width: 175px">';
					
					
					echo $message->date;
					echo '</td><td style="width: 300px">';
					//end of date column
					
					echo '<a class="messageData" rel="#overlay" href="'.url::base().'comment/messageDetails?id='.$message->id.'">';
						
					if($message->poster_name == ""){
						echo __('No name given.');
					}
					else {
						echo $message->poster_name;
					}
					echo '</td><td style="width: 300px">';

					echo '<a class="messageData" rel="#overlay" href="'.url::base().'comment/messageDetails?id='.$message->id.'">';
						
					if($message->poster_email == ""){
						echo __('No email given.');
					}
					else {
						echo $message->poster_email;
					}
					echo '</td></a><td style="width: 75px" class="messageTasks">';
					//tasks stuff
					echo '<a href="#" onclick="deleteMessage('.$message->id.')"><img class="delete" src="'.URL::base().'media/img/img_trans.gif" width="1" height="1"/></br>'. __('Delete');
					
					echo '</a></td></tr>';
				}?>
		</tbody>
	</table>
	<a href="#" class="deleteSelectedBtn"><?php echo __('Delete Selected'); ?></a>
</div>

<?php 
echo Form::hidden('action','edit', array('id'=>'action'));
echo Form::hidden('message_id','0', array('id'=>'message_id'));
echo Form::close();
?>

<div class="apple_overlay" id="overlay">
		<div class="contentWrap">
			<img class="contentWrapWaiter" src="<?php echo URL::base();?>media/img/waiter_barber.gif"/>
		</div>
</div>