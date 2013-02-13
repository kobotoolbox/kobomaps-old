<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-25
* Javascript way of showing users their stats
*************************************************************/
?>
	
<script type="text/javascript">
	$(function(){
		$("#startDate").datepicker();
		$("#endDate").datepicker();

		var startDate = parseDates($("#startDate").val());
		var endDate = parseDates($("#endDate").val());


		var data = [[startDate, 0], [endDate, 0]];
		//draw default chart before values are selected
		$.plot($("#statChart"), [data],  {
			series:{
	    		lines: {show: false, lineWidth: 1.5},
	    		points: {show: false}
			},
	    	grid: {markings: weekendAreas},
	    	yaxis:{position: "left", labelWidth: 60, labelHeight: 20, min:0, max:16},
	    	xaxis: {mode: 'time', timezone: 'browser'}
			}
		);
	});

	function updateGraph(){
		var startDate = $("#startDate").val();
		var endDate = $("#endDate").val();
		var maps = $("#maps").val();
		
		$.post("<?php echo URL::base(); ?>statistics/getdata", { "map": maps, "start": startDate, "end": endDate }).done(
		function(data) {
			drawGraph(parseData(data));
		});	

	}


	//color the weekends in the stat chart
	 function weekendAreas(axes) {
	        var markings = [];
	        var d = new Date(axes.xaxis.min);
	        // go to the first Saturday
	        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
	        d.setUTCSeconds(0);
	        d.setUTCMinutes(0);
	        d.setUTCHours(0);
	        var i = d.getTime();
	        do {
	            // when we don't set yaxis, the rectangle automatically
	            // extends to infinity upwards and downwards
	            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
	            i += 7 * 24 * 60 * 60 * 1000;
	        } while (i < axes.xaxis.max);

	        return markings;
	}

	    
	function drawGraph(data){
		
		$.plot($("#statChart"), data,  {
			series:{
	    		lines: {show: true, lineWidth: 1.5},
	    		points: {show: true}
			},
	    	grid: {hoverable: true, autoHighlight: true, markings: weekendAreas},
	    	yaxis:{position: "left", labelWidth: 60, labelHeight: 20, min:0},
	    	xaxis: {mode: 'time', timezone: 'browser'}
			}
		);
		
		bindHoverTip("#statChart");
	}

	function parseDates(date){
		var returnVal = new Date(date);
		returnVal.setUTCHours(0);
		return returnVal.valueOf();
	}

	function parseData(data){
		var startDate = parseDates($("#startDate").val());
		var endDate = parseDates($("#endDate").val());
		var dayLength = 24 * 60 * 60 * 1000;

		if(data[0].data == null){
			alert("<?php echo __('Please chooose a map')?>");
		}
		
		else {
			for(maps in data){
			//for the days between startDate and endDate go into the data arrays
			var currentDay = startDate;
			for(var j = 0; j <= Math.round((endDate - startDate) / dayLength); j++){
				var containsDay = false;
				//check to see if this date is not already in the array
					for(var k = 0; k < data[maps].data.length; k++){
						if(data[maps].data[k][0] == currentDay){
							containsDay = true;
							break;
						}
					}
					//if no day, put a day with value of 0
					if(!containsDay){
						data[maps].data.push([currentDay, 0]);
						currentDay += dayLength;
					}
					else currentDay += dayLength;
						
					}
			//sort the array to draw in proper sequence	
			data[maps].data.sort(function(a,b){return a[0]-b[0]});	
			}
		}

		return data;
	}
	function bindHoverTip(id){
		$(id).unbind("plothover");
		$(id).bind("plothover", function (event, pos, item) {
			  if (item) { 
		            $("#statsTooltip").remove(); 
		            var myDate = new Date(item.datapoint[0]);
		            showTooltip(item.series.color, pos.pageX, pos.pageY, 
				            item.series.label + ' <?php echo __('was visited')?> ' + item.datapoint[1] + 
				            ' <?php echo __('times on')?> ' + (myDate.getUTCMonth() + 1) + '/' + myDate.getUTCDate() + '/' + myDate.getUTCFullYear() + '.');
			   }
			    else { 
		             $("#statsTooltip").remove(); 
		           } 
			});
	}
	
	function showTooltip(color, x, y, contents) {
        $('<div id="statsTooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y - 25,
            left: x + 5,
            border: '1px solid ' + color,
            padding: '2px',
            'background-color': color,
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
	}
	
</script>
	
	