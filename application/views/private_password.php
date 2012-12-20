<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
 * private_password.php - View
* This software is copy righted by Etherton Technologies Ltd. 2012
* Writen by Willy Douglas <willy@ethertontech.com>
* Started on 12/03/2012
*************************************************************/

//This is no longer used.
?>

<h2><?php echo __("You have chosen to make a private map. ");?></h2>
<p><?php echo __("Please input a password");?></p>


<?php 
echo Form::open(NULL, array('id'=>'private_password_form'));
echo Form::hidden('action','edit', array('id'=>'action'));
//echo Form::hidden('user_id',$data['user_id'], array('id'=>'user_id'));
echo Form::password(__('private_password'));
echo Form::submit('edit', __('Continue'), array('id'=>'edit_button'));
echo Form::close();
?>
