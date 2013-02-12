<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* map_state.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* This shows the status of the map's privacy state
* Started on 2013-01-21
*************************************************************/
?>
	<div id="accessList">
		<table>
			<?php foreach($colaborators as $colaborator){?>
			<tr>
				<td><?php echo $colaborator->first_name;?></td>
				<?php if($colaborator->permission == Model_Sharing::$owner){?>
					<td><?php echo __(Model_Sharing::$owner);?></td>
					<td></td>
				<?php } else {?>
					<td><?php echo __('can').' '.Form::select('colab_perm_'.$colaborator->id, 
									$permissions, 
									$colaborator->permission, 
									array('id'=>'colab_perm_'.$colaborator->id,'onchange'=>'updateUser('.$colaborator->id.'); return false;')).' '.__('this map');?></td>
					<td><a href="#" id="delColab_<?php echo $colaborator->id;?>" onclick="delColab(<?php echo $colaborator->id;?>); return false;" title="<?php echo __('Delete Colaborator');?>">X</a></td>
				<?php }?>
				 
			</tr>
			<?php }?>
		</table>			
	</div>