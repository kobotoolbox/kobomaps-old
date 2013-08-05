/***********************************************************
* graphCreator_js.php - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> 
* Started on 2013-08-05
* Javascript code for the graphs throughout the map
*************************************************************/



 //Constructor for playbacks
var graphCreator = (function(){
	
	
	//return so that the functions are called when using class.function()
	return {formatAreaOpacityColor:formatAreaOpacityColor, calculateColor:calculateColor, updateKey:updateKey};
	  
})();
 
