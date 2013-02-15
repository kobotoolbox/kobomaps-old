/***********************************************************
* playback_js.php - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-02-15
* Javascript code for playback on sheets
*************************************************************/


 
var playback = (function(){
	
	var sheetTimer = null;
	var sheetSpeed = 2000;
	var currentPlaybackIndex = 0;
	var sheetArray = new Array();
	 
	function onLoad(){
		//setup the sheet array
		if(sheetArray.length == 0){
			for(sheet in mapData.sheets){
				sheetArray.push(+sheet);
			}
		}
		//set click and change listerns
		$("#speedVal").change(function(){SetSpeed();});
		$("#pauseButton").click(function(){clearTimer();});
		$("#playButton").click(function(){progressSheet(currentPlaybackIndex);});
	 }
		 
	function progressSheet(index){
	
		if(sheetTimer != null){
			clearInterval(sheetTimer);
		}
	
		if(index >= sheetArray.length)
		{
			currentPlaybackIndex = 0;
			return;
		}
		currentPlaybackIndex = index;
		sheetSelect(sheetArray[currentPlaybackIndex]);
		sheetTimer = setTimeout(function(){progressSheet(index+1);}, sheetSpeed);
	}
		 
	function clearTimer (){
		clearInterval(sheetTimer);
	}
	  
	function SetSpeed(){
		sheetSpeed = 1000 * parseFloat($("#speedVal").val());
	}
		 
	return {onLoad:onLoad};
	  
})();
 

//start the timer progressing through the sheets and changing the screen


//sets speed of progression through charts
