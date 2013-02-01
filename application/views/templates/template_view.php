<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>
		
<h2><?php echo __('View Template'). ' - '.$data['title'] ; ?></h2>
<?php if($map_count != -1){?>
<p>
	<strong>
		<?php echo $map_count.' '.__('maps use this template.')?>
	</strong>
</p>
<p>
<a class="button" id="download_kml_button" href="<?php echo url::base(); ?>uploads/templates/<?php echo $data['kml_file'];?>"><?php echo __('Download KML File');?></a>
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
<?php if($data['id']!=0){?>
<div id="map_div" style="width:900px; height:400px;"></div>
<?php }?>
<br/>
<div >
<?php 	
	echo '<table><tr><td>';
	echo Form::label('title', __('Template Title').": ");
	echo '</td><td>';
	echo $data['title'];
	echo '</td></tr><tr><td>';
	echo Form::label('description', __('Template Description').": ");
	echo '</td><td>';
	echo $data['description'];
	echo '</td></tr><tr><td>';
	echo Form::label('file', __('Visibility').": ");
	echo '</td><td>';
	$visbility_options = array('0'=>__('Public'), '1'=>__('Private'));
	echo $visbility_options[$data['is_private']];	
	echo '</td></tr><tr><td>';
	echo Form::label('file', __('Is an official template').": ");
	echo '</td><td>';
	echo 1==$data['is_official'] ? 'X':'';
	echo '</td></tr><tr><td>';	
	echo Form::label('admin_level', __('Admin Level').": ");
	echo '</td><td>';
	echo $data['admin_level'];
	echo '</td></tr><tr><td>';
	echo Form::label('decimals', __('Decimal places rounded to').": ");
	echo '</td><td>';
	$rounding_options = array('-1'=>'Do not round', 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8);
	echo $rounding_options[$data['decimals']];
	echo '</td></tr><tr><td>';
	echo Form::label('lat', __('Center point latitude').": ");
	echo '</td><td>';
	echo $data['lat'];
	echo '</td></tr><tr><td>';
	echo Form::label('lon', __('Center point longitude').": ");
	echo '</td><td>';
	echo $data['lon'];
	echo '</td></tr><tr><td>';
	echo Form::label('zoom', __('Default zoom level').": ");
	echo '</td><td>';
	echo $data['zoom'];
	echo '</td></tr><tr><td><br/>';
	echo '</td><td><br/>';
	echo '</td></tr><tr><td>';
	$i = 0;
	foreach($data['regions'] as $r_id=>$r_title)
	{
		$i++;
		echo Form::label('regions['.$r_id.']', __('Region')." $i: ");
		echo '</td><td>';
		echo $r_title;
		echo '</td></tr><tr><td>';
	}	
	echo '</td><td></td></tr></table>';
?>
</table>
</div>





