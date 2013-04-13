<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-03-22
* Show menus to edit
*************************************************************/
?>


<h3><?php echo __('Create your own custom submenus')?></h3>


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

<?php 
	echo Form::open(NULL, array('id'=>'edit_menu_form', 'enctype'=>'multipart/form-data'));
	echo Form::hidden('action','edit', array('id'=>'action'));
	
	//echo '<div id="pageTable" style="float:left; width:200px; height:500px;">';
	//echo Form::label('page_descr', __('This is the list of your current pages.'));
	//echo '</br></br>';
	//echo Form::select('pages', $menus, $data['id'], array('id'=>'pages', 'style' => 'width: 175px; height: 22px'));

	//echo '</div>';
  
  echo '<div id="menuEdit" style="width:830px; height 500px;">';
  ?>
  <div class="scroll_table">
	  <table class="list_table" style="width:824px; height:400px">
	  <thead>
	  <tr class="header">
	  			<th class="menuDelete" style="width:58px">
	  				<?php echo __('Delete')?>
	  			</th>
	  			<th class="menuName" style="width:80px">
	  				<?php echo __('Submenu');?>
	  			</th>
	  			<th class="menuItems" style="width:520px">
	  				<?php echo __('Items');?>
	  			</th>
	  			<th class="pagesColumn" style="width:121px">
	  				<?php echo __('Page');?>
	  			</th>
	  			
	  		</tr>
	  	</thead>
	  	<tbody style="height: 360px">
	  	<?php
	  		if(count($submenus) == 0)
	  		{
	  			echo '<tr><td colspan="4" style="width:880px;text-align:center;">'.__('You have no menus').'</td></tr>';
	  		}
	  		$i = 0;
	  		foreach($submenus as $title=>$menu){
				$i++;
	  			$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
	  		?>
	  
	  	<tr <?php echo $odd_row; ?>>
	  		<td class="menuDelete" style="width:59px; text-align:center;">
	  			<?php echo Form::checkbox($title.'delete', null, 0, array('class' => 'delete_box', 'title' => __('Delete')))?>
	  		</td>
	  		<td class="menuName" style="width: 80px">
	  			<?php echo $title;
	  			?>
	  		</td>
	  		<td class="menuItems" style="width:520px">
	  			<ul>
	  			<?php 
	  				foreach($menu as $m){
					?>
						<li>
							<a href="/kobomaps/<?php echo $m->item_url?>">
								<div>
	            					<img class="customMenus" src="<?php echo $m->image_url?>" width="50" height="50"/><br/><?php echo $m->text;?>
	            					</br></a>
	            					<?php 
	            						echo Form::checkbox($m->id.'admin_only', null, 1==$data[$m->id.'admin_only'], array('title' => __('Admin only?'), 'class' => 'admin_box'));
	            						echo Form::checkbox($m->id.'delete', null, 0, array('title' => __('Delete'), 'class' => 'delete_box'));
	            					?>
	          					</div>
      					</li>
					<?php }	
	  			?>
	  			</ul>
	  		</td>
	  		<td class="pagesColumn" style="width:104px">
	  			<?php 
	  			echo Form::select($title.'pages', $pageSelector, (isset($data[$title.'pages']) ? $data[$title.'pages'] : 0), array('style' => 'width:97px; height:22px'));
	  			?>
	  		</td>
	  		
	  	</tr>
	  	<?php }?>
	  	</tbody>
	  </table>
  </div>
  </br>
  <?php 
  
  echo '<div class ="delete_button" id="all_save" style ="width: 50px">'.__('Save').'</div>';
  
  echo '</br></br>';
  
  /*********** Create menu ***************/
  echo '<div id="createMenu" style="float:left; width: 300px">';
  echo '<table style="width:330px"><tr><td><strong>';
  echo Form::label('menuCreate', __('Create a new menu.'));
  echo '</strong</td><td></td></tr><tr><td>';
  echo Form::label('menuName', __('Name of Menu'.':'));
  echo '</td><td>';
  echo Form::input('title', $data['title'], array('id'=>'title', 'style'=>'width:150px'));
  echo '</td></tr><tr><td>';
  echo '<div class ="delete_button" id="menu_save" style ="width: 50px">'.__('Save').'</div>';
  echo '</td></tr></table>';
  echo '</div>';
  
  /***********  Create submenu  ****************/
  echo '<div id="createSubmenu" style="float:right; width: 480px">';
  echo '<table style="width:630px"><tr><td><strong>';
  echo Form::label('subCreate', __('Create a new menu item.'));
  echo '</strong></td><td></td></tr><tr><td>';
  echo Form::label('menuPage', __('Create menu item in menu').':');
  echo '</td><td>';
  echo Form::select('submenu_menu', $menus, 0, array('style' => 'width:80px'));
  echo '</td></tr><tr><td>';
 
  echo Form::label('title', __('Title of menu item'));
  echo '</td><td>';
  echo Form::input('text', $data['text'], array('id'=>'text', 'style'=>'width:180px;', 'maxlength' => '60'));
  echo '</td></tr><tr><td>';
 
  echo Form::label('link', __('Menu URL').':');
  echo '</td><td style="width:400px">';
  echo 'kobomaps/';
  echo Form::input('item_url', $data['item_url'], array('id'=>'item_url', 'style'=>'width:150px;', 'maxlength' => '256'));
  echo '</td></tr><tr><td>';
  
  echo Form::label('image_url', __('Icon').' (.jpeg, .png, .bmp):');
  echo '</td><td>';
  echo Form::file('file', array('id'=>'file', 'style'=>'width:300px;'));
  //echo $data['image_url'];
  echo '</td></tr><tr><td>'; 
  echo Form::label('admin_onlyText', __('Only visible by Admins?'));
  echo '</td><td>';
  echo Form::checkbox('admin_only', null, 0);
  echo '</td></tr><tr><td>';
  echo '<div class ="delete_button" id="submenu_save" style ="width: 50px">'.__('Save').'</div>';
  echo '</td></tr>';
  echo '</table>';
  
  echo '</div></div>';
  
  
  echo Form::close();
  ?>


<div style="clear:both"></div>
