<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo __("Add Map - Page 1") ?></h2>
<p><?php echo __("First we'll grab some basic info about your data");?></p>

<a class="button" id="add_back_to_forms" href="<?php echo url::base(); ?>mymaps"><?php echo __('back to My Maps');?></a>

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

<div id="category_editor">
<?php 	
	echo Form::open(NULL, array('id'=>'edit_maps_form', 'enctype'=>'multipart/form-data')); 
	echo Form::hidden('action','edit', array('id'=>'action'));
	echo Form::hidden('user_id',$data['user_id'], array('id'=>'user_id'));
	echo '<table><tr><td>';
	echo Form::label('title', __('Map Title').": ");
	echo '</td><td>';
	echo Form::input('title', $data['title'], array('id'=>'title', 'style'=>'width:300px;'));
	echo '</td></tr><tr><td>';
	echo Form::label('description', __('Map Description').": ");
	echo '</td><td>';
	echo Form::textarea('description', $data['description'], array('id'=>'description', 'style'=>'width:600px;'));
	echo '</td></tr><tr><td>';
	
	echo Form::label('description', __('Should this map be private').": ");
	echo '</td><td>';
	echo Form::checkbox('is_private', null, 1==$data['is_private'], array('onchange'=>"toggle_id('private_password_row')"));
	echo '</td></tr>';
	
	if($data['is_private'] != 1)
	{
		$password_style = "display:none";		
	}
	else {
		$password_style = "";
	}
	
	echo '<tr id="private_password_row" style="'.$password_style.'"><td>';
	echo Form::label('private_password', __('Password (if private)').": ");
	echo '</td><td>';
	echo Form::password('private_password', $data['private_password'], array('id'=>'private_password', 'style'=>'width:300px;'));
	echo '</td></tr><tr><td>';
	echo Form::label('filetype', __('Is the data source').": ");
	echo '</td><td>';
	echo Form::radio('filetype', 'excel', true, array('onclick'=>"toggle_id('googledoc');toggle_id('excelfile')")). ' '.__('Excel File') . ' '.
		Form::radio('filetype', 'google',false, array('onclick'=>"toggle_id('googledoc');toggle_id('excelfile')")). ' '.__('Google Spreadsheet');

	echo '</td></tr><tr id="excelfile"><td>';
	
	echo Form::label('file', __('Spreadsheet (.xls, .xlsx)').": ");
	echo '</td><td>';
	echo Form::file('file', array('id'=>'file', 'style'=>'width:300px;'));	

	echo '</td></tr><tr id="googledoc" style="display:none;"><td>';
	
	echo Form::label('googleurl', __('Google Spreadsheet').": ");
	echo '</td><td>';
	echo '<input type="button" id="authorizeButton" value="Authorize" onclick="authorizeKoboMaps();"/>';
	?>
		<img src="<?php echo url::base();?>media/img/loading.gif" id="googlewaiter" style="display:none;"/>
		<div id="googleFileListHolder">
			<table id="googleFilesList">
				<tr>
					<th></th><th><?php echo __('Name')?></th><th><?php echo __('Owner')?></th><th><?php echo __('Date')?></th>
				</tr>
			</table>
		</div>
	
	<?php 
	echo Form::hidden('googleid','',array('id'=>'googleid'));
	echo Form::hidden('googlelink','',array('id'=>'googlelink'));
	echo Form::hidden('googletoken','',array('id'=>'googletoken'));
	echo '</td></tr>';
	
	//do we ultimately want to clean this up a bit?
	echo '<tr style="display:none"><td>';
	echo Form::label('lat', __('Latitude of center of map').": ");
	echo '</td><td>';
	echo Form::input('lat', $data['lat'], array('id'=>'lat', 'style'=>'width:300px;'));
	
	echo '</td></tr><tr style="display:none"><td>';
	echo Form::label('lon', __('Longitude of center of map').": ");
	echo '</td><td>';
	echo Form::input('lon', $data['lon'], array('id'=>'lon', 'style'=>'width:300px;'));
	
	echo '</td></tr><tr style="display:none"><td>';
	echo Form::label('zoom', __('Default map zoom').": ");
	echo '</td><td>';
	echo Form::select('zoom', array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18),$data['zoom'], array('id'=>'zoom', 'style'=>'width:300px;'));
	
	
	echo '</td></tr><tr><td>';
	echo Form::label('advanced_options', __('Show advanced options').": ");
	echo '</td><td>';
	echo Form::input('advanced_options', __('Advanced'), array('type'=>'button', 'id'=>'advanced_options', 'onclick'=>"toggle_class('advanced'); return false;", 'style'=>'width:600px;'));
	
	
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('CSS', __('Map CSS').": ");
	echo '</td><td>';
	echo Form::label('CSS_description', __('<br/>CSS can be used to edit the style of the map menubar and legend. <br /> To learn more about the use of CSS see: '));
	echo Form::label('CSS_description','<a href="http://www.w3schools.com/css/">http://www.w3schools.com/css/</a> <br />');
	echo Form::textarea('CSS', $data['CSS'], array('id'=>'CSS', 'style'=>'width:600px;'));
	
	
	echo '</td></tr><tr class="advanced"  style="display:none"><td>';
	echo Form::label('map_style', __('Map Style').": ");
	echo '</td><td>';
	echo Form::label('map_style_description', __('<br/>The map styles can be used to edit the style of the background map. <br /> To learn more about the use of map styles see: '));
	echo Form::label('map_style_description', '<br/><a href="https://developers.google.com/maps/documentation/javascript/styling">https://developers.google.com/maps/documentation/javascript/styling</a> <br />');
	echo Form::textarea('map_style', $data['map_style'], array('id'=>'map_style', 'style'=>'width:600px;'));
	
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('default_map_style', __('Revert to default map style').": ");
	echo '</td><td>';
	echo Form::button('default_map_style', __('Default'), array('type'=>'button', 'id'=>'default_map_button', 'onclick'=>'set_default_map_style("map_style"); return false'));
	
	echo '</td></tr><tr><td>';
	echo Form::submit('edit', __('Continue'), array('id'=>'edit_button'));
	echo '</td><td></td></tr></table>';
	echo Form::close();
?>

</div>





