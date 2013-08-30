<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

		
<h2><?php echo __('Add Map - Basic Setup'); echo  $data['title'] == '' ? '' : ' - '.$data['title']?></h2>
<h3><?php echo __('First we will need some basic information for your map');?></h3>

<?php if($data['large_file']) { ?>
<div class="slug">
	<ul>
		<li>
			<?php echo __('Your map has a large data file, loading might be slow.');?>
		</li>
	</ul>
</div>
<?php }?>


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
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Map Title
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo Form::label('title', __('Map Title').": ");
	echo '</td><td>';
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Slug Box
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo Form::input('title', $data['title'], array('id'=>'title', 'style'=>'width:300px;', 'maxlength' => '128'));
	echo '</td></tr><tr><td>';
	echo Form::label('slug', __('Map Slug').": ");
	echo '</td><td>';
	echo URL::site(null,'HTTP').Form::input('slug', $data['slug'], array('id'=>'slug', 'style'=>'width:300px;', 'maxlength'=>128));
	echo '<br/>'.__('This will be the URL to access this map. It should be short and sweet');

	echo '</td></tr><tr><td>';
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Map description box
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo Form::label('description', __('Map Description').": ");
	echo '</td><td>';
	echo Form::textarea('description', $data['description'], array('id'=>'description', 'style'=>'width:600px;'));
	echo '</td></tr><tr><td>';
	
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Private checkbox
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo Form::label('description', __('Should this map be private').": ");
	echo '</td><td>';
	echo Form::checkbox('is_private', null, 1==$data['is_private'], array('checked' => 'checked'));
	
	echo '</td></tr><tr><td>';
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Data source and excel file
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo Form::label('filetype', __('Is the data source').": ");
	echo '</td><td>';
	echo Form::radio('filetype', 'excel', true, array('onclick'=>"toggle_id('googledoc');toggle_id('excelfile')")). ' '.__('Excel File') . ' '.
		Form::radio('filetype', 'google',false, array('onclick'=>"toggle_id('googledoc');toggle_id('excelfile')")). ' '.__('Google Spreadsheet');

	echo '</td></tr><tr id="excelfile"><td>';
	
	echo Form::label('file', __('Spreadsheet (.xls, .xlsx)').": ");
	echo '</td><td>';
	echo Form::file('file', array('id'=>'file', 'style'=>'width:300px;'));	

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                    Googledoc implementation
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr id="googledoc" style="display:none;"><td>';
	echo Form::label('googleurl', __('Google Spreadsheet').": ");
	echo '</td><td>';
	echo '<input type="button" id="authorizeButton" value="Authorize" onclick="authorizeKoboMaps();"/>';
	?>
		<img src="<?php echo url::base();?>media/img/loading.gif" id="googlewaiter" style="display:none;"/>
		<div id="googleFileListHolder">
			<table id="googleFilesList">
				<thead>
				<tr>
					<th></th><th><?php echo __('Name')?></th><th><?php echo __('Owner')?></th><th><?php echo __('Date Modified')?></th>
				</tr>
				</thead>
				<tbody>
				<tr id="blankGSrow">
					<td colspan="4" style="text-align:center;"><span style="color:#9a9a9a;">Empty</span></td>
				</tr>
				</tbody>
			</table>
		</div>
	
	<?php 
	echo Form::hidden('googleid','',array('id'=>'googleid'));
	echo Form::hidden('googlelink','',array('id'=>'googlelink'));
	echo Form::hidden('googletoken','',array('id'=>'googletoken'));
	echo '</td></tr>';
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Googledoc latitude
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '<tr style="display:none"><td>';
	echo Form::label('lat', __('Latitude of center of map').": ");
	echo '</td><td>';
	echo Form::input('lat', $data['lat'], array('id'=>'lat', 'style'=>'width:300px;'));
	
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                     Googledoc longitude
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr style="display:none"><td>';
	echo Form::label('lon', __('Longitude of center of map').": ");
	echo '</td><td>';
	echo Form::input('lon', $data['lon'], array('id'=>'lon', 'style'=>'width:300px;'));
	
	echo '</td></tr><tr style="display:none"><td>';
	echo Form::label('zoom', __('Default map zoom').": ");
	echo '</td><td>';
	echo Form::select('zoom', array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18),$data['zoom'], array('id'=>'zoom', 'style'=>'width:300px;'));
	
  /************************************************************************************************************************************************
                                          Advanced options section
  *************************************************************************************************************************************************/
	echo '</td></tr><tr><td>';
	echo Form::label('advanced_options', __('Show advanced options').": ");
	echo '</td><td>';
	echo Form::input('advanced_options', __('Advanced'), array('type'=>'button', 'id'=>'advanced_options', 'onclick'=>"toggle_class('advanced'); return false;", 'style'=>'width:600px;'));
	
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                     Show region names without data checkbox
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('show_label', '<br/>'.__('Show All Labels').": ");
	echo '</td><td></br>';
	echo Form::checkbox('show_empty_name', null, $data['show_names']==1);
	echo Form::label('show_label_description', __('Maps will show region names with no data.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    What level should labels zoom at select box
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('label_zoom_level', '</br>'.__('Zoom level to show labels').': ');
	$label_zoom_options = array('-1'=>'Always Visible', 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 11=>11, 12=>12, 13=>13, 14=>14, 15=>15, 16=>16, 17=>17, 18=>18, 19=>19);
	echo '</td><td></br>';
	echo Form::select('label_zoom_level', $label_zoom_options, $data['label_zoom_level']);
	echo Form::label('show_label_description', __('Level at which labels will begin to appear.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Font size for the Region names
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('region_label', '</br>'.__('Font size of region names').": ");
	$label_font_options = array(8=>'8 px', 10=>'10 px', 12=>'12 px', 14=>'14 px', 16=>'16 px', 18=>'18 px', 20=>'20 px', 22=>'22 px', 24=>'24 px', 26=>'26 px', 28=>'28 px', 
			30=>'30 px', 32=>'32 px', 34=>'34 px', 36=>'36 px', 38=>'38 px', 40=>'40 px', 42=>'42 px', 44=>'44 px', 46=>'46 px', 48=>'48 px');
	echo '</td><td></br>';
	echo Form::select('region_label_font', $label_font_options, $data['region_label_font']);
	echo Form::label('show_region_font', __('Font size of the region titles as seen in maps, in pixels.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Font size for the region data
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('data_label', '<br/>'.__('Font size of data values').": ");
	echo '</td><td></br>';
	echo Form::select('value_label_font', $label_font_options, $data['value_label_font']);
	echo Form::label('show_region_font', __('Font size of the data values as seen in maps, in pixels.'));
	
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Region border color picker
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('border_color_description', '<br/>'.__('Color of region borders').": ");
	echo '</td><td></br>';
	echo '<input id="border_color_pick" class="color {valueElement: border_color, pickerClosable:true}" style="width:30px"><input id="border_color" name="border_color" value="'.$data['border_color'].'" style="display:none">';
	echo Form::label('border_color_explain', '    '.__('Will change the color of the borders between regions.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Region color picker
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('region_color', '<br/>'.__('Default color of regions').": ");
	echo '</td><td></br>';
	echo '<input id="region_color_pick" class="color {valueElement: region_color, pickerClosable:true}" style="width:30px"><input id="region_color" name="region_color" value="'.substr($data['region_color'], 0, 6).'" style="display:none">';
	echo Form::label('region_color_explain', '    '.__('Color of regions that are not being affected by an indicator.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Gradient check box
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('gradient_label', '<br/>'.__('Make regions have a gradient?'));
	echo '</td><td></br>';
	echo Form::checkbox('gradient', null, $data['gradient']==1, array('id' => 'gradient', 'onclick' => 'openGradient()', 'value' => '"'.$data['gradient'].'"'));

	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Region shading color picker 1
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('polygon_color_shade', '<br/>'.__('Color of region shading').": ");
	echo '</td><td></br>';
	echo '<input id="polygon_color_pick" class="color {valueElement: polygon_color, pickerClosable:true, minS:0.8}" style="width:30px"><input id="polygon_color" name="polygon_color" value="'.substr($data['polygon_color'], 0, 6).'" style="display:none">';
	echo Form::label('polygon_color_explain', '    '.__('Color of regions that are being affected by an indicator.'));
	
	echo '</td></tr><td></td><td class="gradient_explain" style="display:none;">';
	$secondColor = '';
	if(strlen($data['polygon_color']) < 8){
		$secondColor = 'FFFFFF';
	}
	else{
		$secondColor = substr($data['polygon_color'], 7, 13);
	}
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Region shading color picker 2
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '<input id="region_two_picker" class="color {valueElement: regionTwo, pickerClosable:true}" style="width:30px"><input id="regionTwo" name="regionTwo" value="'.$secondColor.'" style="display:none">';
	echo  '  '.__('Regions will gradient into this color from the default color.');
	
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Graph default bar color picker
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('bar_color_description', '<br/>'.__('Color of bars in graphs').": ");
	echo '</td><td></br>';
	echo '<input id="bar_color_pick" class="color {valueElement: graph_bar_color, pickerClosable:true}" style="width:30px"><input id="graph_bar_color" name="graph_bar_color" value="'.$data['graph_bar_color'].'" style="display:none">';
	echo Form::label('graph_color_explain', '    '.__('Color of bars in all graphs.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Graph selected bar color picker
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('region_color', '<br/>'.__('Color of selected regions in graphs').": ");
	echo '</td><td></br>';
	echo '<input id="bar_select_color_pick" class="color {valueElement: graph_select_color, pickerClosable:true}" style="width:30px"><input id="graph_select_color" name="graph_select_color" value='.$data['graph_select_color'].' style="display:none">';
	echo Form::label('selected_color_explain', '    '.__('Color of bars in graphs that indicate the region selected currently.'));
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Map css text box
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('CSS', __('Map CSS').": ");
	echo '</td><td>';
	echo Form::label('CSS_description', '<br/>'.__('CSS can be used to edit the style of the map menubar and legend.').'<br/>'.__('To learn more about the use of CSS see: '));
	echo Form::label('CSS_description','<a href="http://www.w3schools.com/css/">http://www.w3schools.com/css/</a> <br />');
	echo Form::textarea('CSS', $data['CSS'], array('id'=>'CSS', 'style'=>'width:600px;'));
	
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                    Map style text box
  // Is going to be hidden as options are decided on another page now
	echo '</td></tr><tr style="display:none"><td>';
	echo Form::label('map_style', __('Map Style').": ");
	echo '</td><td>';
	echo Form::label('map_style_description', '<br/>'.__('The map styles can be used to edit the style of the background map.').'<br/>'.__('To learn more about the use of map styles see: '));
	echo Form::label('map_style_description', '<br/><a href="https://developers.google.com/maps/documentation/javascript/styling">https://developers.google.com/maps/documentation/javascript/styling</a> <br />');
	echo Form::textarea('map_style', $data['map_style'], array('id'=>'map_style', 'style'=>'width:600px;'));
	
	echo '</td></tr><tr class="advanced" style="display:none"><td>';
	echo Form::label('default_map_style', __('Revert to default map style').": ");
	echo '</td><td>';
	echo Form::button('default_map_style', __('Default'), array('type'=>'button', 'id'=>'default_map_button', 'onclick'=>'set_default_map_style("map_style"); return false'));
	
	
	//              End of page						
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo '</td></tr><tr><td>';
	echo Form::submit('edit', __('Continue'), array('id'=>'edit_button'));
	echo '</td><td></td></tr></table>';
	echo Form::close();
?>

</div>





