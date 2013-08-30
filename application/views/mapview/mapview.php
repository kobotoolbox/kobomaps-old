<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* mapview.php - View
* This software is copy righted by Etherton Technologies Ltd. 2012
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* Started on 11/06/2012
*************************************************************/
?>
	
<!DOCTYPE html> 
<html> 
<head> 
	
	<link href="<?php echo URL::base();?>media/css/style.css" type="text/css" rel="stylesheet">
	<?php echo $html_head;?>
	
	
<title>Kobomap::<?php echo $map->title;?></title>
</head> 
		
<body>
<?php 
	echo '<div id="siteHeader" ';
	if(isset($_GET['fullscreen'])){ echo 'style="display:none;"';}
	echo '>';
	$header = new View('header');
	$header->menu_page = $menu_page;
	$header->user = $user;
	echo $header;
	echo '</div>';
 
?>		

<?php
//Bar at the bottom to select between different sheets
//change the size of the title if it's very large
?>
<div id="sheetlinks">
	<div id="mapName" 
		<?php 
		if(strlen($map->title) > 24 && strlen($map->title) <= 30){
			echo 'style="font-size:17px;">';
			echo $map->title;
		}
		elseif(strlen($map->title) > 30 && strlen($map->title) <= 40){
			echo 'style="font-size:11px;">';
			echo $map->title;
		}
		elseif(strlen($map->title) > 40 && strlen($map->title) <= 58){
			echo 'style="font-size:10px;  padding: 0px 0px 0px 0px">';
			echo $map->title;
		}
		elseif(strlen($map->title) > 58){
			echo 'style="font-size:9px; padding: 0px 0px 0px 0px">';
			echo substr($map->title, 0, 64).' '.substr($map->title, 64);
		}
		else{
			echo '>';
			echo $map->title;
		}
	?>
	</div>
	<div id="sheetnamesStartControl" class="sheetScrollerControll"><a href="">&lt;&lt;</a></div>
	<div id="sheetnamesLeftControl" class="sheetScrollerControll"><a href="">&lt;</a></div>
	<div id="sheetnamesWrapper">		
		<ul id=sheetnames></ul>		
	</div>
	<div id="sheetnamesRightControl" class="sheetScrollerControll"><a href="">&gt;</a></div>
	<div id="sheetnamesEndControl" class="sheetScrollerControll"><a href="">&gt;&gt;</a></div>
	<div id="mapSocialShare">
		<ul>
			
			<li>
				<a id="commentButton" rel="#overlay" href="<?php echo url::base(); ?>message/submit?id=<?php echo $map->id;?>" >
					<img class="comment" title="<?php echo __('Comment on this map.')?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>		
				</a>
			</li>
			<li>
				<a id="shareButton" rel="#overlay" href="<?php echo url::base(); ?>share/window?id=<?php echo $map->id;?>" >
					<img class="share" title="<?php echo __('Share this map.')?>"  src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>		
				</a>
			</li>
			<li>
				<img class="playbackLabels" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>
				<ul id="playBackButtons">
					<li>
						<span id="playButton" title="<?php echo __('Play through the sheets.') ?>"><?php echo __('Play')?></a>
					</li>	
					<li id="pauseButton">
						<span title="<?php  echo __('Pause the sheets.')?>"> <?php echo __('Pause')?></a>		
					</li>
					<li>
					<?php 
					//work on adding a onchange listener to the speed adjuster
					?>
						<div id="setSpeed" style="color:white">
							<input id="speedVal" title="<?php echo __('Sets the speed in seconds of playback.')?>" 
							type="number" style="width: 35px" value="2" min="0.5" max="10" step="0.5"></input> 
							<?php echo __('Speed')?>
						</div>
					</li>
				</ul>
			</li>		
			<li>
				<a id="fullScreenButton" href="">
					<img class="fullscreen" title="<?php echo __('Make this map fullscreen.')?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>
				</a>
			</li>
			<li>
				<img class="toggleLabels" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>
				<ul id="toggleTextButtons">
					<li>
						<a href="" id="turnOffLabelsButton" title="<?php echo __('Turns on or off the region names.')?>"><?php echo __('Hide Labels')?></a>
					</li>
					<li>
						<a href="" id="turnOffValuesButton" title="<?php echo __('Turns on or off the data values.')?>"><?php echo __('Hide Values')?></a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	
</div>

<div style="height:40px;width:10px;"></div>
<div id="maplinks"	style="overflow-y:auto">
	
		<p id="loadingtext">
		<?php echo __('Please be patient while the map is loading.')?>  
		</p>
		
		
		<?php if(strlen($map->description) > 0){?>
		<div id="descriptionText" position="relative"><p ><?php echo $map->description;?></p>
		</div>
		<?php }?>
			<ul id="questionsindicators" class="questionsindicators" >	</ul>		
		
		<div id="legend" >
			<a id="minButtonLegend" > - </a>
			
				<div id="legendtext">
					<span id="spanLegendText"><?php echo __('Please select an indicator to display its data.')?></span>
				</div>
				<div id="legendMinDiv">
				
					<div id="legend_gradient">
						<canvas id="legend_canvas" style="width:121px; height:20px"></canvas>
						<div id="percentleft"></div>
						<div id="percentright"></div>
					</div>
					<div id="nationalaveragediv">
						<span id="nationalaveragelabel"></span>
						<span id="nationalaverageimg" ></span>
					</div>
					</br>
					<div id="nationalChartScrollDiv"  style=" width:320px; overflow-y: auto; overflow-x: hidden">
				       	<div id="nationalIndicatorChart" style="width: 300px"></div>		
				    </div>
				       <div id="sourcetext">
						<span id="sourcetextspan" style="display:none;"> Data Source:  
							<a id="sourceURL" href="" title=""></a>
							<span id="sourceNoURL"></span>
						</span>
			    	</div>
		        
			
				<?php
				//Powered by KoBoToolbox - Please be kind and leave a reference with a link to our website.
				?>
					<div id="poweredby">
					<a href="http://www.kobotoolbox.org" title="KoBoToolbox.org"><?php echo __('powered by KoboToolbox')?></a>
					</div>
				</div>
			</div>
		
	</div>




<?php
//The background element containing the actual map
?>
<div id="map_canvas"></div>

<?php
//The legend
?>

	
	<?php if(!$template->loaded()){?>
		<div class="apple_overlay" id="missingTemplate">
			<div class="contentWrap" style="position:relative;padding:30px;">
				<img src="<?php echo URL::base();?>media/img/big_error.png"/>
				<h2 >
					<?php echo __('We\'re sorry, but the template for this map is missing. We have alerted the map\'s owner.').'<br/><br/>'.__('Please check back soon.');?>
				</h2>
			</div>
		</div>
	<?php }?>
	
	<div class="apple_overlay" id="overlay" style="display:none">
		<div class="contentWrap">
			<img class="contentWrapWaiter" src="<?php echo URL::base();?>media/img/waiter_barber.gif"/>
		</div>
	</div>



<div id='fb-root'></div>

</body> 
</html> 
