/***********************************************************
* mapButtons.js - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> 
* Started on 2013-08-05
* Javascript code for the buttons found on the map
*************************************************************/



 //Constructor for mapMath
var mapButtons = (function(){
	var legend = '';
	//will change the heights of the maplinks div on the left side of the map page
	function resizeMaplinks(){
		var height = $(window).height();

		if($("#siteHeader").is(':visible')){
			$("#maplinks").height(parseInt(height - 280));
			$("#nationalChartScrollDiv").height(parseInt(height * .20));
			$("#questionsindicators").height(parseInt(height * .19));
			
		}
		else{
			$("#maplinks").height(height);
			$("#nationalChartScrollDiv").height(parseInt(height * .30));
			$("#questionsindicators").height(parseInt(height * .33));
		}
	}
	
	/**
	 * This function is called to initialize the event handlers for the buttons on this page,
	 * like the share button and the fullscreen button
	 */
	function initialize_buttons(showL, hideL, showV, hideV, makeFullText, closeFullText)
	{	
		//handle toggling between full screen and normal view
		$("#fullScreenButton").click(function(){
				$("#siteHeader").toggle();
				if($('.fullscreen').data("ui-tooltip-title") != closeFullText){
					$('.fullscreen').data("ui-tooltip-title", closeFullText);
				}
				else{
					$('.fullscreen').data("ui-tooltip-title", makeFullText);
				}
				resizeMaplinks();
				return false;
			});

		//handle turning off and on the labels on the map
		$("#turnOffLabelsButton").click(function(){
			Label.renderLabelNames = !Label.renderLabelNames; 
			$("#turnOffLabelsButton").toggleClass("active");

			if($("#turnOffLabelsButton").text() == 'Hide Labels'){
				$("#turnOffLabelsButton").text(showL);
			}
			else{
				$("#turnOffLabelsButton").text(hideL);
			}
			//redraw all the labels 
			for(i in labels)
			{
				labels[i].draw();
			} 
			return false;
			});

		//hanndle turning on and off values on the map
		$("#turnOffValuesButton").click(function(){
			Label.renderLabelVals = !Label.renderLabelVals; 
			$("#turnOffValuesButton").toggleClass("active");
			
			if($("#turnOffValuesButton").text() == 'Hide Values'){
				$("#turnOffValuesButton").text(showV);
			}
			else{
				$("#turnOffValuesButton").text(hideV);
			}
			//redraw all the labels 
			for(i in labels)
			{
				labels[i].draw();
			}
			return false;
			});
		
		$('#iframeBlocker').click(function(){
			//$('#totalChartFrame').attr('src', "<?php echo url::base()?>mymaps/totalchart?id=<?php echo $map->id?>&indicator=<?php echo isset($_GET['indicator'])? $_GET['indicator'] : ''?>");
			$('#totalChartHREF').click();
		});

		$("#turnOffLabelsButton").tooltip( {
			position:{
				my: "left+45 center-20",
				at: "center top"	
			}
		});
		$("#turnOffValuesButton").tooltip( {
			position:{
				my: "left+45 center-20",
				at: "center top"	
			}
		});
		$("#commentButton").tooltip( {
			position:{
				my: "left+15 center-19",
				at: "center top"	
			}
		});
		$("#shareButton").tooltip( {
			position:{
				my: "left+14 center-19",
				at: "center top"	
			}
		});
		$("#fullScreenButton").tooltip( {
			position:{
				my: "left+14 center-19",
				at: "center top"	
			}
		});


		$("#playButton").tooltip( {
			position:{
				my: "left+14 center-19",
				at: "center top"	
			}
		});

		$("#pauseButton").tooltip( {
			position:{
				my: "left-134 center-19",
				at: "center top"	
			}
		});
		
		$("#speedVal").tooltip( {
			position:{
				my: "left+24 center-22",
				at: "center top"	
			}
		});
		


		//initialize the apple overlay effect
		$("a[rel]").overlay({
			mask: 'grey',
			effect: 'apple',
			onBeforeLoad: function() {
				 
	            // grab wrapper element inside content
	            var wrap = this.getOverlay().find(".contentWrap");
	 
	            // load the page specified in the trigger
	            wrap.load(this.getTrigger().attr("href"));
	        }
		});
		
	}
	
	/**
	* creates the listener on the +/- sign on the legend to minimize it or not
	*/
	function init_legend_listener(legendString){
		legend = legendString;
		$("#minButtonLegend").click(function(){
			var height = $(window).height();

			if($("#siteHeader").is(':visible')){
				$("#questionsindicators").height(parseInt(height * .19));
			}
			else{
				$("#questionsindicators").height(parseInt(height * .33));
			}
			
			if($("#legendMinDiv").is(":visible")){
				$("#legendMinDiv").toggle();
				$("#minButtonLegend").html("+");
				var tempString = $('#spanLegendText').html();
				$('#spanLegendText').html(legend);
				$('#spanLegendText').prop('title', tempString);
			}
			else {
				$("#legendMinDiv").toggle();
				$("#minButtonLegend").html("-");
				$('#spanLegendText').html($('#spanLegendText').prop('title'));
				if($("#questionsindicators").height() > 400){
					if($("#siteHeader").is(':visible')){
						$("#questionsindicators").height(parseInt(height * .3));
					}
					else{
						$("#questionsindicators").height(parseInt(height * .56));
					}
				}
			}
			
			if(!$('#descriptionText').is(":visible") && !$("#legendMinDiv").is(":visible")){
				if($("#siteHeader").is(':visible')){
					$("#questionsindicators").height(parseInt(height * .56));
				}
				else{
					$("#questionsindicators").height(parseInt(height * .86));
				}
			}
		});
		$("#minButtonDesc").click(function(){
			if($("#descriptionText").is(":visible")){
				$("#descriptionText").toggle();
				$("#minButtonDesc").html("+");
				$("#descriptionMin").toggle();
				$("#descTitle").toggle();
			}
			else {
				$("#descriptionText").toggle();
				$("#minButtonDesc").html("-");
				$("#descriptionMin").toggle();
				$("#descTitle").toggle();
			}
			if(!$('#descriptionText').is(":visible") && !$("#legendMinDiv").is(":visible")){
				$('#questionsindicators').height(440);
			}
		});	
		$(window).resize(function(){
			resizeMaplinks();
		});
		
		resizeMaplinks();
	}

	
	//return so that the functions are called when using class.function()	 
	return {initialize_buttons:initialize_buttons, init_legend_listener:init_legend_listener};
	  
})();
 
