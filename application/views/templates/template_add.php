<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo $data['id'] == 0 ? __("Add a Template") : __("Edit Template"). ' - '.$data['title'] ; ?></h2>
<p><?php echo __("Create a template to use with your maps");?></p>
<p>
			You can find KML and KMZ files for various countries here: 
			<a href="http://www.gadm.org/country" target="_blank">http://www.gadm.org/country</a>. 
			The files there will work with this converter.
</p>
<?php if($map_count != -1){?>
<p>
	<strong>
		<?php echo $map_count.' '.__('maps use this template.')?>
	</strong>
</p>
<p>
<a class="button" id="download_kml_button" href="<?php echo url::base(); ?>uploads/templates/<?php echo $data['kml_file'];?>"><?php echo __('Download KML File');?></a>
</p>
<p>
<a class="button" id="copy_template_button" href="<?php echo url::base(); ?>templates/copy?id=<?php echo $data['id'];?>"><?php echo __('Copy Template');?></a>
</p>
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
<?php
$is_add =  $data['id']==0;
if($data['id']!=0){?>
<div id="map_div" style="width:900px; height:400px;"></div>
<?php }?>

<div id="category_editor">
<?php 	
	echo Form::open(NULL, array('id'=>'edit_maps_form', 'enctype'=>'multipart/form-data')); 
	echo Form::hidden('action','edit', array('id'=>'action'));
	echo Form::hidden('id',$data['id'], array('id'=>'id'));
	//echo Form::hidden('user_id',$data['user_id'], array('id'=>'user_id'));
	echo '<table><tr><td>';
	echo Form::label('title', __('Template Title').": ");
	echo '</td><td>';
	echo Form::input('title', $data['title'], array('id'=>'title', 'style'=>'width:300px;'));
	echo '</td></tr><tr><td>';
	echo Form::label('description', __('Template Description').": ");
	echo '</td><td>';
	echo Form::textarea('description', $data['description'], array('id'=>'description', 'style'=>'width:600px;'));
	echo '</td></tr><tr><td>';
	echo Form::label('file', __('Visibility').": ");
	echo '</td><td>';
	$visbility_options = array('0'=>__('Public'), '1'=>__('Private'));
	echo Form::select('is_private', $visbility_options,$data['is_private']). '<br/>';
	echo __('Private templates can only be used by you. Public templates will be made available for other users to copy and use in their maps.');
	echo '</td></tr><tr><td>';
	if($is_admin){
		echo Form::label('file', __('Is an official template').": ");
		echo '</td><td>';
		echo Form::checkbox('is_official', null, 1==$data['is_official'] );
		echo '</td></tr><tr><td>';
	}
	echo Form::label('file', __('File (.kml, .kmz)').": ");
	echo '</td><td>';
	echo Form::file('file', array('id'=>'file', 'style'=>'width:300px;'));	
	echo '</td></tr><tr><td>';
	echo Form::label('admin_level', __('Admin Level').": ");
	echo '</td><td>';
	$admin_levels = array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8);
	echo Form::select('admin_level', $admin_levels, $data['admin_level'], array('style'=>'width:100px'));
	echo '</td></tr><tr><td>';
	echo Form::label('decimals', __('How many decimal places to round to').": ");
	echo '</td><td>';
	$rounding_options = array('-1'=>'Do not round', 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8);
	echo Form::select('decimals', $rounding_options, $data['decimals']);
	echo '</td></tr><tr><td>';
	echo Form::label('round_mem', __('How far back to remember rounding').": ");
	echo '</td><td>';
	$round_mem_options = array(1=>1, 5=>5, 50=>50, 500=>500, 5000=>5000);
	echo Form::select('round_mem', $round_mem_options);
	echo '</td></tr><tr><td>';
	echo Form::label('lat', __('By default, what should the center point latitude be').": ");
	echo '</td><td>';
	$lat_options = array('id'=>'lat', 'style'=>'width:300px;');
	if(!$is_add){
		$lat_options['onkeyup'] = 'latChanged();';
		$lat_options['onchange'] = 'latChanged();';
	}
	echo Form::input('lat', $data['lat'],$lat_options );
	echo '</td></tr><tr><td>';
	echo Form::label('lon', __('By default, what should the center point longitude be').": ");
	echo '</td><td>';
	$lon_options = array('id'=>'lon', 'style'=>'width:300px;');
	if(!$is_add){
		$lon_options['onkeyup'] = 'lonChanged();';
		$lon_options['onchange'] = 'lonChanged();';
	}
	echo Form::input('lon', $data['lon'], $lon_options);
	echo '</td></tr><tr><td>';
	echo Form::label('zoom', __('By Default what should this map zoom to').": ");
	echo '</td><td>';
	$zoom_options = array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 11=>11, 12=>12, 13=>13, 14=>14, 15=>15, 16=>16, 17=>17, 18=>18, 19=>19);
	$zoom_params = array('id'=>'zoom');
	if(!$is_add){
		$zoom_params['onchange']='changeZoom();';
	}
	echo Form::select('zoom', $zoom_options, $data['zoom'],$zoom_params);
	echo '</td></tr><tr><td>';
	$i = 0;
	foreach($data['regions'] as $r_id=>$r_title)
	{
		$i++;
		echo Form::label('regions['.$r_id.']', __('Region')." $i: ");
		echo '</td><td>';
		echo Form::input('regions['.$r_id.']', $r_title, array('id'=>'regions['.$r_id.']', 'style'=>'width:300px;'));
		echo '</td></tr><tr><td>';
	}
	
	echo Form::submit('edit', $data['id']==0?__('Add'): __('Edit'), array('id'=>'edit_button'));
	echo '</td><td></td></tr></table>';
	echo Form::close();
?>
</table>
</div>





