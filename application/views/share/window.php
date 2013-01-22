<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* window.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-21
*************************************************************/
?>
	
<div class="shareWindow" id="shareWindow_<?php echo $map->id;?>">
<h2><?php echo __('Sharing Settings:');?> <?php echo $map->title;?></h2>
<?php echo __('Sharing Settings:');?>


	<?php echo __('Code to embed map')?><br/>
	<input readonly="readonly" type="text" value="<iframe src=&quot;<?php echo URL::site(NULL, TRUE)?>public/view?id=<?php echo $map->id?>&quot; width=&quot;800&quot; height=&quot;600&quot;/>"/>
	<br/>
	<br/>

	<?php echo __('Link to share map')?><br/>
	<input readonly="readonly" type="text" value="<?php echo URL::site(NULL, TRUE)?>public/view?id=<?php echo $map->id?>"/>
	<?php
		$body = __('I want to share this map with you:').' '.URL::site(NULL, TRUE).'public/view?id='.$map->id;
		$body = rawurlencode($body); 
		$subject = rawurlencode(__('Sharing'). ' '.$map->title. ' '.__('map'));
	?>
	<ul class="sharingTasks">
		<li>
			<a href="mailto:?subject=<?php echo $subject;?>&body=<?php echo $body;?>">
				<img class="emailShare" src="/kobomaps/media/img/img_trans.gif" width="1" height="1">
			</a>
		</li>
		<li>
			<a href="https://www.facebook.com/dialog/feed?app_id=230846520384211&amp;link=https://developers.facebook.com/docs/reference/dialogs/&amp;picture=http://fbrell.com/f8.jpg&amp;name=Facebook%20Dialogs&amp;caption=Reference%20Documentation&amp;description=Using%20Dialogs%20to%20interact%20with%20users.&amp;redirect_uri=https://mighty-lowlands-6381.herokuapp.com/">here</a>
		</li>
	</ul>
	
	<br/>
	<br/>
	<div id="indicatorLink" style="display:none;">
		<?php echo __('Link to share this indicator')?><br/>
		<input readonly="readonly" type="text" value="<?php echo URL::site(NULL, TRUE)?>public/view?id=<?php echo $map->id?>#/?indicator="/>
		<ul class="sharingTasks">
		<li>
		<a href="" id="indicatorEmail">
			<img class="emailShare" src="/kobomaps/media/img/img_trans.gif" width="1" height="1">
		</a>
		</li>
		</ul>
	</div>
	
	<script type="text/javascript">
		if( typeof $.address != 'undefined')
		{
			var indicator = $.address.parameter("indicator");
			if(typeof indicator != 'undefined')
			{
				var indicatorLink = $("#indicatorLink input").val() + indicator;
				$("#indicatorLink input").val(indicatorLink);
				$("#indicatorLink").show();
				var body = encodeURIComponent("<?php echo __('I want to share this map with you:');?> " + indicatorLink);
				var subject = "<?php echo rawurlencode(__('Sharing'). ' '.$map->title. ' '.__('map')); ?>";
				$("#indicatorEmail").attr("href","mailto:?subject="+subject+"&body="+body);
			}
			
		}
	</script> 

</div>