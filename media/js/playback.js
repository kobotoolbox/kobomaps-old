/***********************************************************
* playback_js.php - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-02-15
* Javascript code for playback on sheets
*************************************************************/


 //Constructor for playbacks
var playback = (function(){
	
	var sheetTimer = null;
	var sheetSpeed = 2000;
	var currentPlaybackIndex = 0;
	var sheetArray = new Array();
	 
	function onLoad(){
		//setup the sheet array
		if(mapData.sheets.length < 3){
			return;
		}
		else{
			if(sheetArray.length == 0){
				for(sheet in mapData.sheets){
					sheetArray.push(+sheet);
				}
			}
			//set click and change listerns
			$("#speedVal").change(function(){SetSpeed();});
			$("#pauseButton").click(function(){
				$("#playButton").removeClass('active');
				clearTimer();
			});
			$("#playButton").click(function(){
				$("#playButton").addClass('active');
				progressSheet(currentPlaybackIndex);
			});
		}
	 }
		 
	//start incrementing the sheets and creating a timer to increment within set speed
	function progressSheet(index){
		if(sheetTimer != null){
			clearInterval(sheetTimer);
		}

		if(index >= sheetArray.length)
		{
			currentPlaybackIndex = 0;
			$("#playButton").removeClass('active');
			clearTimer();
			return;
		}
		currentPlaybackIndex = index;
		//the indicators somehow start at -1 sometimes and this prevents 
		//an null error when hitting the play button on the first sheet without clicking any previous sheets
		if(currentPlaybackIndex == -1){
			sheetSelect(sheetArray[currentPlaybackIndex + 1]);
			currentPlaybackIndex ++;
		}
		else{
			sheetSelect(sheetArray[currentPlaybackIndex]);
		}
		sheetTimer = setTimeout(function(){progressSheet(index+1);}, sheetSpeed);
		return false;
	}
		 
	//stops timers
	function clearTimer(){
		clearInterval(sheetTimer);
		sheetTimer = null;
	}
	  
	//set by the speed selector within the submenu
	function SetSpeed(){
		sheetSpeed = 1000 * parseFloat($("#speedVal").val());
		return false;
	}
	
	//listens to the sheet that is auto selected when a map is loaded, or if a user selects a sheet while playback is in effect
	function setSheetStart(sheetId){
		if(sheetArray.indexOf(sheetId) == currentPlaybackIndex){
			return;
		}
		currentPlaybackIndex = sheetArray.indexOf(sheetId);
		if(sheetTimer != null){
			clearTimer();
			progressSheet(currentPlaybackIndex);
		}
	}
		 
	return {onLoad:onLoad, setSheetStart:setSheetStart};
	  
})();
 
