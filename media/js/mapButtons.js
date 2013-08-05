/***********************************************************
* mapButtons.js - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> 
* Started on 2013-08-05
* Javascript code for the buttons found on the map
*************************************************************/



 //Constructor for mapMath
var mapButtons = (function(){
	
	//will change the heights of the maplinks div on the left side of the map page
	function resizeMaplinks(description){
		var height = $(window).height();

		if($("#siteHeader").is(':visible')){
			$("#maplinks").height(parseInt(height - 280));
			$("#nationalChartScrollDiv").height(parseInt(height * .20));
			$("#questionsindicators").height(parseInt(height * .19));
			if(description > 0){
				$("#descriptionText").height(parseInt(height * .10));
			}
		}
		else{
			$("#maplinks").height(height);
			$("#nationalChartScrollDiv").height(parseInt(height * .30));
			$("#questionsindicators").height(parseInt(height * .33));
			if(description > 0){
				$("#descriptionText").height(parseInt(height * .15));
			}
		}
	}
	
	/**
	 * This function is called to initialize the event handlers for the buttons on this page,
	 * like the share button and the fullscreen button
	 */
	 var headerOffset = 0;
	function initialize_buttons(description, showL, hideL, showV, hideV)
	{	
		//handle toggling between full screen and normal view
		$("#fullScreenButton").click(function(){
				$("#siteHeader").toggle();
				resizeMaplinks(description);
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
	function init_legend_listener(description){
		$("#minButtonLegend").click(function(){
			if($("#legendMinDiv").is(":visible")){
				$("#legendMinDiv").toggle();
				$("#minButtonLegend").html("+");
			}
			else {
				$("#legendMinDiv").toggle();
				$("#minButtonLegend").html("-");
			}
		});	
		$(window).resize(function(){
			resizeMaplinks(description);
		});
		
		resizeMaplinks(description);
	}

	
	//return so that the functions are called when using class.function()	 
	return {initialize_buttons:initialize_buttons, init_legend_listener:init_legend_listener};
	  
})();
 
