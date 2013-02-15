/***********************************************************
* playback_js.php - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-02-15
* Javascript code for playback on sheets
*************************************************************/


 
 function Playback(){
	 this.sheetTimer = null;
	 this.sheetSpeed = 2000;
	 this.currentPlaybackIndex = 0;
	 this.sheetArray = new Array();
	 
	 if(this.sheetArray.length == 0){
			for(sheet in mapData.sheets){
				this.sheetArray.push(+sheet);
			}
		}
	 
	 this.progressSheet = playbackProgressSheet;
	 this.setSpeed = playbackSetSpeed;
	 
	 var clearTimer = clearInterval(this.sheetTimer);
	 
	 var This = this;
	 
	 $("#speedVal").change(This.setSpeed);
	 
	 $("#playButton").click(
			This.progressSheet(This.currentPlaybackIndex)
		);
	 
	 $("#pauseButton").click(clearTimer);
	 
 }
 

//start the timer progressing through the sheets and changing the screen
function playbackProgressSheet(index){

	if(this.sheetTimer != null){
		clearInterval(this.sheetTimer);
	}

	if(index >= this.sheetArray.length)
	{
		this.currentPlaybackIndex = 0;
		return;
	}
	this.currentPlaybackIndex = index;
	sheetSelect(this.sheetArray[this.currentPlaybackIndex]);
	this.sheetTimer = setTimeout(this.progressSheet(index+1), this.sheetSpeed);
	
}

//sets speed of progression through charts
function playbackSetSpeed(){
	this.sheetSpeed = 1000 * parseFloat($("#speedVal").val());
}
