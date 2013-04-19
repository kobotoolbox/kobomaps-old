<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* mapstyle.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 2013-03-29
*************************************************************/

echo '<h2>'.__('Add Map - Map Style').' - '.$map->title.'</h2>';
echo '<h3>'.__('Change the style of the map.').'</h3></br>';

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

//string shortcuts for map array
$elem = 'elementType';
$feat = 'featureType';
$vis = 'visibility';
$col = 'color';

echo Form::open(NULL, array('id'=>'edit_styles_form', 'enctype'=>'multipart/form-data'));
echo Form::hidden('action','edit', array('id'=>'action'));
echo Form::hidden('waterActive','false', array('id'=>'waterActive'));

?>

<div id="styleDiv" class="scroll_table">
	<table id="styleTable" style="width:450px">
	<thead>
		<th id="styleName" style="width:229px"> <?php echo __('Map Features')?> </th>
		<th id="styleType" style="width:155px"> <?php echo __('Style Types')?> </th>
		<th id="styleStyles" style="width:186px"> <?php echo __('Options')?> </th>
	</thead>
	<?php 	
	
		foreach($style as $sets){
			echo '<tr><td style="width:175px"><em>';
			if($sets[$elem] != 'geometry'){
				echo $sets[$feat];
			}
			echo '</em></td><td style="width:180px">';
			echo $sets[$elem];
			echo '</td><td style="width:370px">';
			
			//these values are encoded in seperate arrays for some reason
			foreach($sets['stylers'] as $stylers){
				foreach($stylers as $key=>$value){
					$colorId = $sets[$feat].'_'.$sets[$elem].'_colorId';
					$colorDiv = $sets[$feat].'_'.$sets[$elem].'_colorDiv';
					$color = $value;
						if($key == $vis){
							echo __('Visible?');
							echo Form::checkbox($sets[$feat].'_'.$sets[$elem].'_vis', null, $value == 'on');
						}
						if($key == $col){					
							echo '    '.__('Color').': ';
							echo '<input id="'.$colorId.'" class="color {valueElement: \''.$colorDiv.'\', pickerClosable:true}" style="width:30px">
								<input id="'.$colorDiv.'" name="'.$colorDiv.'" value="'.$color.'" style="display:none">';
						}
						if($sets[$feat] == 'landscape' AND $sets[$elem] == 'geometry' AND $key == 'hue'){
							echo '    '.__('Color').': ';
							echo '<input id="'.$colorId.'" class="color {valueElement: \''.$colorDiv.'\', pickerClosable:true}" style="width:30px">
								<input id="'.$colorDiv.'" name="'.$colorDiv.'" value="" style="display:none">';
						}
				}
			}
			
			echo '</br>';
			
			echo '</td></tr>';
		}
		
	?>
</table>
</div>

<?php 
echo Form::submit('edit', __('Save'), array('id'=>'save_button'));
echo Form::close();
?>



