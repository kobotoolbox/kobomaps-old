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
<div id="maplinks"	>
	<div style="height:60px;width:10px;"></div>
	<p id="mapHelpText"><?php echo __('Click on a section name to display the questions, then click on the questions to show the indicator(s). Click on the indicator to display its data on the map.')?></p>
	<?php if(strlen($map->description) > 0){?>
	<p id="descriptionText"><?php echo $map->description;?></p>
	<?php }?>
		<ul id="questionsindicators" class="questionsindicators" >	</ul>
	<p id="loadingtext">
	<?php echo __('Please be patient while the map is loading.')?>  
	</p>
</div>


<?php
//Bar at the bottom to select between different sheets
?>
<div id="sheetlinks">
	<div id="mapName"><?php echo $map->title;?></div>
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
				<a id="shareButton" rel="#overlay" href="<?php echo url::base(); ?>share/window?id=<?php echo $map->id;?>" >
					<img class="share" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>		
				</a>
			</li>
			<li>
				<a id="fullScreenButton" href="">
					<img class="fullscreen" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>
				</a>
			</li>
			<li>
				<img class="toggleLabels" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/>
				<ul id="toggleTextButtons">
					<li>
						<a href="" id="turnOffLabelsButton"><?php echo __('Toggle Labels')?></a>
					</li>
					<li>
						<a href="" id="turnOffValuesButton"><?php echo __('Toggle Values')?></a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	
</div>

<?php
//The background element containing the actual map
?>
<div id="map_canvas"></div>

<?php
//The legend
?>
<div id="topbar" class="drsElement drsMoveHandle" style="left:355px; top: 400px;">
	 
	<div id="legend">

		<div id="legendtext">
			<span id="spanLegendText"><?php echo __('Please select an indicator to display its data.')?></span>
		</div>
		<div id="legend_gradient">
			<div id="percentleft">
				
			</div>
			<div id="percentright">
				
			</div>
		</div>
		<div id="nationalaveragediv">
			<span id="nationalaveragelabel"></span>
			<span id="nationalaverageimg" ></span>
		</div>
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


<div class="apple_overlay" id="overlay">
	<div class="contentWrap">
		<img class="contentWrapWaiter" src="<?php echo URL::base();?>media/img/waiter_barber.gif"/>
	</div>
</div>




<div id='fb-root'></div>

</body> 
</html> 
