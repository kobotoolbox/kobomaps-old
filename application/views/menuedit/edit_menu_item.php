<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* edit_menu_item.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton
* Started on 2013-04-14
* Lets users edit a menu item
*************************************************************/
?>



<?php 

  /***********  Create submenu  ****************/
  echo '<div id="createSubmenu" >';
  echo '<table><tr><td><strong>';
  echo Form::label('subCreate', __('Create a new menu item.'));
  echo '</strong></td><td></td></tr><tr><td>';

  echo '</td><td>';

  echo '</td></tr><tr><td>';
 
  echo Form::label('title', __('Title of menu item'));
  echo '</td><td>';
  echo Form::input('text', $data['text'], array('id'=>'text', 'maxlength' => '60'));
  echo '</td></tr><tr><td>';
 
  echo Form::label('link', __('Menu URL').':');
  echo '</td><td class="wideCreateSubmenuTable">';
  echo url::base(true, true);
  echo Form::input('item_url', $data['item_url'], array('id'=>'item_url', 'maxlength' => '256'));
  echo '</td></tr><tr><td>';
  
  echo Form::label('image_url', __('Icon').' (.jpeg, .png, .bmp):');
  echo '</td><td>';
  echo Form::file('file', array('id'=>'file'));
  //echo $data['image_url'];
  echo '</td></tr><tr><td>'; 
  echo Form::label('admin_onlyText', __('Only visible by Admins?'));
  echo '</td><td>';
  echo Form::checkbox('admin_only', null, 0);
  echo '</td></tr><tr><td>';
  echo '<input type="button" name="edit_submenu_item" value="'.__('Save').'" onclick="editSubMenuItem('.$data['id'].','.$data['menu_id'].');" />';
  echo '<input type="button" name="delete_submenu_item" value="'.__('Delete').'" onclick="deleteSubMenuItem('.$data['id'].');" />';
  echo '<br/>';
  echo '<input type="button" name="cancel_submenu_item" value="'.__('Cancel').'" onclick="cancelSubMenuItem();" />';
  echo '</td></tr>';
  echo '</table>';
  
  echo '</div></div>';
  ?>