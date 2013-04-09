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
  echo Form::input('menuString', $data['menuString'], array('id'=>'menuString', 'style' => 'display:none'));
	
	echo '<div id="pageTable" style="float:left; width:200px; height:500px;">';
	echo Form::label('page_descr', __('This is the list of your current pages.'));
	echo '</br></br>';
	echo Form::select('pages', $menus, $data['id'], array('id'=>'pages', 'style' => 'width: 175px; height: 22px'));

	echo '</div>';
  
  echo '<div id="menuEdit" style="float:right; width:630px; height 500px;">';
  ?>
  <div class="scroll_table">
	  <table class="list_table" style="width:550px; height:400px">
	  <thead>
	  <tr class="header">
	  			<th class="menuName" style="width:80px">
	  				<?php echo __('Menu');?>
	  			</th>
	  			<th class="menuItems" style="width:300px">
	  				<?php echo __('Items');?>
	  			</th>
	  			<th class="lastColumn" style="width:70px">
	  				<?php echo __('Pages');?>
	  			</th>
	  		</tr>
	  	</thead>
	  	<tbody style="height: 360px">
	  	<?php
	  		if(count($menus) == 0)
	  		{
	  			echo '<tr><td colspan="4" style="width:880px;text-align:center;">'.__('You have no maps').'</td></tr>';
	  		}
	  		$i = 0;
	  		foreach($menus as $title=>$menu){
				$i++;
	  			$odd_row = ($i % 2) == 0 ? 'class="odd_row"' : '';
	  		?>
	  
	  	<tr <?php echo $odd_row; ?>>
	  		
	  		<td class="menuName" style="width: 80px">
	  			<?php echo $title;?>
	  		</td>
	  		<td class="menuItems" style="width:300px">
	  			<ul>
	  			<?php 
	  				foreach($menu as $m){
					?>
						<li>
							<a href="
								<?php echo $m->item_url?>">
								<div>
	            					<img class="<?php echo $m->item_url?>" src="<?php echo $m->image_url?>" width="1" height="1"/><br/><?php echo $m->text;?>
	          					</div>
       					 	</a>
      					</li>
					<?php }	
	  			?>
	  			</ul>
	  		</td>
	  		<td class="lastColumn" style="width:65px">
	  		</td>
	  	</tr>
	  	<?php }?>
	  	</tbody>
	  </table>
  </div>
  </br>
  <?php 
  echo '<table style="width:630px"><tr><td>';
  echo Form::label('menuPage', __('Create menu item in menu').':');
  echo '</td><td>';
  echo Form::input('menuPage', '', array('id'=>'menuPage', 'style' => 'width: 180px;', 'readonly' => 'readonly'));
  echo '</td></tr><tr><td>';
 
  echo Form::label('title', __('Title of menu item'));
  echo '</td><td>';
  echo Form::input('text', $data['text'], array('id'=>'text', 'style'=>'width:180px;', 'maxlength' => '60'));
  echo '</td></tr><tr><td>';
 
  echo Form::label('link', __('Menu URL').':');
  echo '</td><td style="width:400px">';
  echo 'kobomaps/';
  echo Form::input('item_url', $data['item_url'], array('id'=>'item_url', 'style'=>'width:250px;', 'maxlength' => '256'));
  echo '</td></tr><tr><td>';
  
  echo Form::label('image_url', __('Icon').' (.jpeg, .png, .bmp):');
  echo '</td><td>';
  echo Form::file('file', array('id'=>'file', 'style'=>'width:300px;'));
  //echo $data['image_url'];
  echo '</td></tr>';
   
  echo '</table>';
  
  echo Form::submit('edit', __('Save'), array('id'=>'edit_button'));
  echo '<div id ="delete_button" style="float:right">'.__('Delete').'</div></div>';
  echo '</div>';
  
  
  echo Form::close();
  ?>


<div style="clear:both"></div>
