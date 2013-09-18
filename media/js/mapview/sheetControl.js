/***********************************************************
* sheetControl.js - Script
* This software is copy righted by Etherton Technologies Ltd. 2013
* Written by Dylan Gillespie <dylan@ethertontech.com> 
* Started on 2013-08-05
* Javascript code for controlling the changing of sheets
*************************************************************/



 //Constructor for playbacks
var sheetControl = (function(){


	/**
	* Allows for the scrolling between different sheets on the map
	* @param int sheetId the id of the sheet that needs to be scrolled to
	*/
	function sheetSelect(sheetId)
	{

	 	var sheetButton = $("#sheetSelector_"+sheetId);
	 	var sheetItem =  $("#sheetli_"+sheetId);

		if(!sheetButton.hasClass("active"))
		{


			//////////////////////////////////////////////
			//This hides, shows removes active, adds active
			//to all the necessary HTML elements
			////////////////////////////////////////////////
			//turn off active on all elements
			$('#sheetnames li.active').removeClass("active");

			$('#questionsindicators li.sheet').hide();
			sheetItem.addClass("active");
			sheetItem.show();
			
			$('#sheetnames li.sheet2 span').removeClass("active");		
			sheetButton.addClass("active");

			//allows the user to hit play and start at this sheet
			playback.setSheetStart(sheetId);

			///////////////////////////////////////////////////////
			//This next part tries to figure out if there is a
			//matching indicator on the new sheet
			//that should be switched on
			

			//get current map selection 
			var autoLoadIndicatorCurrent = $.address.parameter("indicator");
			//if nothing was previously selcted then bounce.
			if(autoLoadIndicatorCurrent == null || autoLoadIndicatorCurrent.length == 0)
			{
				return;
			}
			
			//grab the sheet ID of the current indicator
			var indicatorIndexesArray = autoLoadIndicatorCurrent.split('_');
			//make sure they didn't click on the same sheet
			if(indicatorIndexesArray[0] != sheetId)
			{
				//grab the data for the previously selected and newly selected sheets
				var newSheet = mapData.sheets[sheetId];
				var currentSheet = mapData.sheets[indicatorIndexesArray[0]];
				var newPtr = newSheet;
				var currentPtr = currentSheet;
				var newIndicatorIdString = sheetId + "_";

				//loop over the indicator indexes, until you get to the next to last
				for(var i = 1; i < indicatorIndexesArray.length - 1; i++)
				{
					//advance the ptrs
					newPtr = newPtr.indicators[indicatorIndexesArray[i]];
					currentPtr = currentPtr.indicators[indicatorIndexesArray[i]];

					newIndicatorIdString += indicatorIndexesArray[i] + "_";

					//make sure the indicators at this level match, if not, bounce.
					if(newPtr.name != currentPtr.name)
					{
						return;
					}
				}
				//now grab the name of the current indicator
				var currentIndicatorName = $("#indicatorSpanId_"+autoLoadIndicatorCurrent).text();
				//loop over the new indicator and see if any of the indicators match the name of the current indicator
				for(i in newPtr.indicators)
				{
					var name = newPtr.indicators[i].name;
					if(name == currentIndicatorName)
					{
						//we have a match show, this indicator
						newIndicatorIdString += i;
						showByIndicator(newIndicatorIdString);
						return;
					}
				}
				//clear the page if nothing was found
				//loop over the polygons and set the colors to not-set
				for(areaName in areaGPolygons)
				{
					colorProperties.formatAreaOpacityColor(areaName, 0.75, colorProperties.getRegion());
					//set the label to blank("")
					$('#minButtonLegend').click();
					labels[areaName].set('areaValue', '');
					labels[areaName].draw();
					//remove any old pop-up listeners
					google.maps.event.clearListeners(areaGPolygons[areaName], 'click');
					
				}
			}//end if the two sheets don't match
		}//end 	if(!sheetButton.hasClass("active"))
		
	}//end function
	

	/**
	* Controls the clicking of the indicators on the left side of the page
	* @param view indicatorItem is the indicator to turn on
	* @param boolean forceOn if the map should be on
	*/
	function midIndicatorClick(indicatorItem, forceOn)
	{
		if(forceOn != undefined && forceOn == false)
		{
			indicatorItem.removeClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").hide(); //This hides the child ul level1 element
		}
		else if (forceOn != undefined && forceOn == true)
		{
			indicatorItem.addClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").show(); //This shows the child ul level1 element
		}
		else if(indicatorItem.hasClass("active"))
		{
			indicatorItem.removeClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").hide(); //This hides the child ul level1 element
		}
		else 
		{
			indicatorItem.addClass("active"); //highlights active span
			indicatorItem.siblings("ul.indicator").show(); //This shows the child ul level1 element
		}
	}
	  
	/**
	* Highlights this item and makes the sheets go to it
	* @param div dataItem to be turned on
	*/
	function dataIndicatorClick(dataItem)
	{		
		$('span.dataLevel').removeClass("active"); //removes highlight of any other level3 li element
		dataItem.addClass("active"); //highlights active span
		var id = dataItem.attr('id').substring(16); //grab the id for processing
		showByIndicator(id);
	}
	
	/**
	 * Takes in an indicator string and then renders the map according to the data for that indicator
	 * If the indicator doesn't exist it'll just exit gracefully
	 * @param string indicator is the example (0_0_2) value of an indicator
	 */
	function showByIndicator(indicator)
	{
		if(typeof indicator == 'undefined')
		{
			return;
		}
		
		var dataPtr = null;
		var ids = indicator.split("_"); //split up the ids
		var sheetId = ids[0];

		//make sure the appropriate sheet tab is highlighted
		//first remove the active class from all sheet tabs
		$("ul#sheetnames li.sheet2 span").removeClass("active");
		//now add active to the one sheet that needs it
		$("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).addClass("active");
		//scroll to the just highlighted sheet
		currentListTop = $("#sheetnames").offset().top;
		if($("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).offset() != null){
			var currentTopOfDisplay = $("#sheetlinks").offset().top;
		
			currentTopOfSelectedItem = $("ul#sheetnames li.sheet2 span#sheetSelector_"+sheetId).offset().top;

			var delta = (-(currentTopOfSelectedItem-currentTopOfDisplay));
			if(0 != delta)
			{
				scrollSheets(delta);
			}
		}
		$("li.sheet").hide();
		$("li.sheet").removeClass("active");
		$("li.sheet#sheetli_"+sheetId).show();
		$("li.sheet#sheetli_"+sheetId).addClass("active");
		
		var extend_data = mapData;
		dataPtr = mapData.sheets[sheetId]; //get the sheet, because it's different
		
		var currentIndicator = sheetId; // stores the current indicator key as we built it up
		//console.log(dataPtr);
		//loop over the remaining indicators
		for(i in ids)
		{
			//skip 0
			if(i!=0)
			{
				var id = ids[i];
				currentIndicator = currentIndicator + "_" + id;
				dataPtr = dataPtr['indicators'][id];

				//now make sure the indicators are shown
				$("span.indicatorLevel_"+i).removeClass("active");
				$("span.indicatorLevel_"+i+"#indicatorSpanId_"+currentIndicator).addClass("active");
				$("ul.indicatorLevel_"+i).hide();
				$("ul.indicatorLevel_"+i+"#indicatorId_"+currentIndicator).show();
			}
			
		}


		//now make sure
		
		console.log(dataPtr);
		if(dataPtr != undefined)
		{				
			var title = dataPtr["name"];
			var data =  new Array();
			//setup data to work with the way things used to be when this was CSV driven
			for(areaName in dataPtr.data)
			{
				data[areaName] = parseFloat(dataPtr.data[areaName].value);
			}
			var nationalAverage = dataPtr["total"]; 
			var unit = dataPtr["unit"];
			
			var totalLabel = "";
			if (typeof dataPtr["total_label"] != 'undefined')
			{
				totalLabel = dataPtr["total_label"];
			}
			if(!extend_range){
				UpdateAreaAllData(title, data, nationalAverage, indicator, unit, totalLabel, null);
			}
			else{
				UpdateAreaAllData(title, data, nationalAverage, indicator, unit, totalLabel, extend_data);
			}
			$.address.parameter("indicator", indicator);
		
			//check and see if there is a source to link to
			if(dataPtr["src"] == "" || dataPtr["src"] == "undefined")
			{
				//if there's no source then hide the source div
				$("#sourcetextspan").hide();
			}
			else
			{
				//update the source link and the source title
				$("#sourcetextspan").show();

				
				//make sure there's a valid link
				if(dataPtr["src_link"] == "" || dataPtr["src_link"] == "undefined")
				{
					//not a valid link
					$("#sourceURL").hide();
					$("#sourceNoURL").show();
					$("#sourceNoURL").text(dataPtr["src"]);
				}
				else
				{
					$("#sourceNoURL").hide();
					$("#sourceURL").show();
					$("#sourceURL").text(dataPtr["src"]);
					$("#sourceURL").attr("title", dataPtr["src"]);
					$("#sourceURL").attr("href", dataPtr["src_link"] );
				}
			}
			
		}
		else{
			console.log('Here');
			//loop over the polygons and set the colors to not-set
			for(areaName in areaGPolygons)
			{
				colorProperties.formatAreaOpacityColor(areaName, 0.75, colorProperties.getRegion());
				//set the label to blank("")
		
				labels[areaName].set("areaValue", "");
				labels[areaName].draw();
				//remove any old pop-up listeners
				google.maps.event.clearListeners(areaGPolygons[areaName], 'click');
			}
		}
	}


	//function needs to be escaped to work with Drupal where the $ character is reserved

	/**
	* Starts upon load and starts the indicator where last seen
	*/
	(function(j) { 
			j(function() {
				$.address.externalChange(function(event) {  
					var indicator = $.address.parameter("indicator");
					if(indicator != undefined && typeof mapData !== "undefined" && typeof mapData.sheets !== "undefined")
					{
						showByIndicator(indicator);					
					}

				});  
			});
	})(jQuery);


	/**
	 * Sets up scrolling of the longitudenal data, AKA sheets
	 * 
	 * Also setup these two global variables for storing how far down we've scrolled
	 */
	 
	 var sheetsHeight = 0;
	function setup_scrolling()
	{
		sheetsHeight = ($("#sheetnames").offset().top)-($(".lastSheetSelector").offset().top);
		if(sheetsHeight < 0) //only show all of this if there's somewhere to scroll to
		{
		$("#sheetnamesRightControl a").click(function(){scrollSheets(-$("#sheetnames").height()); return false;});
		$("#sheetnamesLeftControl a").click(function(){scrollSheets($("#sheetnames").height()); return false;});

		$("#sheetnamesStartControl a").click(function(){
			var currentTopOfDispaly = $("#sheetlinks").offset().top;
			var currentListTop = $("#sheetnames").offset().top;
			var actualOffset = currentListTop - currentTopOfDispaly;
			var newOffset = -actualOffset;
			scrollSheets(newOffset);
			 return false;
		});

		
		$("#sheetnamesEndControl a").click(function(){
			var currentTopOfDispaly = $("#sheetlinks").offset().top;
			var currentListTop = $("#sheetnames").offset().top;
			var actualOffset = currentListTop - currentTopOfDispaly;
			var newOffset = sheetsHeight - actualOffset;
			scrollSheets(newOffset);
			return false;
		});
			
		scrollSheets(0); //initialize things
		}
		else
		{
			$("#sheetnamesRightControl a").hide();
			$("#sheetnamesLeftControl a").hide();
			$("#sheetnamesStartControl a").hide();
			$("#sheetnamesEndControl a").hide();
		}
	}

	/**
	 * Handle scrolling the list of longitudenal data, or sheets, down.
	 * @param delta int - How far up or down to scroll
	 */
	function scrollSheets(delta)
	{
		//Get some base lines
		var increment = $("#sheetnames").height();
		var currentTopOfDisplay = $("#sheetlinks").offset().top;
		var currentListTop = $("#sheetnames").offset().top;
		var actualOffset = currentListTop - currentTopOfDisplay;
		
		//make sure actualOffset is a multiple of increment
		if(actualOffset % increment != 0)
		{
			actualOffset = Math.round(actualOffset/increment);
		}
		
		if(delta > 0 && (actualOffset + delta) <= 0 ) //scrolling up
		{
			actualOffset += delta;
			$("#sheetnames").animate({top:actualOffset},300);
		}
		else if (delta < 0 && (actualOffset + delta) >= sheetsHeight) //scrolling down
		{
			actualOffset += delta;
			$("#sheetnames").animate({top:actualOffset},300);
		}

		//make the scroll up, button inactive if need be
		if(actualOffset == 0)
		{
			$("#sheetnamesLeftControl a").addClass('inactive');
			$("#sheetnamesStartControl a").addClass('inactive');
		}
		else
		{
			$("#sheetnamesLeftControl a").removeClass('inactive');
			$("#sheetnamesStartControl a").removeClass('inactive');
		}
		if(actualOffset == sheetsHeight)
		{
			$("#sheetnamesRightControl a").addClass('inactive');
			$("#sheetnamesEndControl a").addClass('inactive');
		}
		else
		{
			$("#sheetnamesRightControl a").removeClass('inactive');
			$("#sheetnamesEndControl a").removeClass('inactive');
		}
	}

	//return so that the functions are called when using class.function()	 
	return {sheetSelect:sheetSelect, midIndicatorClick:midIndicatorClick, showByIndicator:showByIndicator, setup_scrolling:setup_scrolling, dataIndicatorClick:dataIndicatorClick};
	  
})();
 
