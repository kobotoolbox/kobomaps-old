<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
 * private_password.php - View
* This software is copy righted by Etherton Technologies Ltd. 2012
* Writen by Willy Douglas <willy@ethertontech.com>
* Started on 12/03/2012
*************************************************************/

?>

<span><a href="<?php echo URL::base();?>mymaps"><?php echo __('Back to My Maps')?></a></span>

<h2><?php echo __("This map is private. ");?></h2>
<p><?php echo __("Please login as the map owner, or input the password:");?></p>


<?php 
echo Form::open(NULL, array('id'=>'private_password_check', 'enctype'=>'multipart/form-data'));
echo Form::hidden('action','submit', array('id'=>'action'));
//echo Form::hidden('user_id',$data['user_id'], array('id'=>'user_id'));
echo Form::password('private_password');
echo Form::submit('submit', __('Submit'), array('id'=>'submit_button'));
echo Form::close();
?>
