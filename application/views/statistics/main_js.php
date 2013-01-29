<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-25
* Javascript way of showing users their stats
*************************************************************/
?>
	
<script type="text/javascript">
	$(function(){
		$("#startDate").datepicker();
		$("#endDate").datepicker();
	});

	function updateGraph(){
		var startDate = $("#startDate").val();
		var endDate = $("#endDate").val();
		var maps = $("#maps").val();
		
		$.post("<?php echo URL::base(); ?>statistics/getData", { "map": maps, "start": startDate, "end": endDate }).done(
		function(data) {
			drawGraph(parseData(data));
		});	

	}

	function hash_colors(id)
	{
		var	name = 65;
		var r = 255;
		var g = 255;
		var b = 255;
		if(name % 5 == 0){
			b = name % 5;
		}
		else if(name % 3 == 0){
			g = name % 3;
		}
		else if(name % 2 == 0){
			r = name % 2;
		}
		return 'rgb(' + r + ',' + g + ',' + b + ')';
	}

	function drawGraph(data){
		var names = new Array();
		
		$.plot($("#statChart"), data,  {
			series:{
	    		lines: {show: true, lineWidth: 1.5},
	    		points: {show: true}
			},
	    	grid: {hoverable: true, autoHighlight: true},
	    	yaxis:{position: "left", labelWidth: 60, labelHeight: 20, min:0},
	    	xaxis: {mode: 'time', timeformat: '%m/%d/%y',
	    		timezone: 'browser'}
			}
		);
		
		bindHoverTip("#statChart");
	}

	function parseData(data){
		console.log(data);
		var today = new Date();
		today = Date.parse(today);
		var monthAgo = new Date();
		var dayLength = 24 * 60 * 60 * 1000;
		monthAgo = Date.parse(monthAgo) - (24 * 60 * 60 * 30);
		
		for(maps in data){
			if(data[maps].data.length == 0){
				var currentDay = monthAgo;
				for(var i = 0; i < 31; i++){
					data[maps].data.push([currentDay, 0]);
					currentDay -= dayLength;
				}
			}
			else{
				for(i in data[maps].data){
					if(data[maps].data[+i + 1] != null){
						if(data[maps].data[+i + 1][0] - data[maps].data[+i][0] > dayLength){
							var tempDay = data[maps].data[+i][0];
							var temp = data[maps].data[+i + 1][0] - data[maps].data[+i][0];
							for(var i = 0; i < Math.round(temp / dayLength); i ++){
								data[maps].data.splice((+i), 0, [tempDay + dayLength, 0]);
							}
						} 
					}
					var currentDay = monthAgo;
					//for(var i = 0; i < 31; i++){
					//	data[maps].data.push([currentDay, 0]);
					//	currentDay -= dayLength;
					//}
				}
			}

			//make a new array to push all data into after checking if days apart
			//change the 31 in the for loop above to be variable
			console.log(data);
		}
		return data;
	}
	function bindHoverTip(id){
		$(id).unbind("plothover");
		$(id).bind("plothover", function (event, pos, item) {
			  if (item) { 
		            $("#tooltip").remove(); 
		            var myDate = new Date(item.datapoint[0]);
		            showTooltip(item.series.color, pos.pageX, pos.pageY, 
				            item.series.label + ' was visited ' + item.datapoint[1] + 
				            ' times on ' + (myDate.getMonth() + 1) + '/' + myDate.getDate() + '/' + myDate.getFullYear() + '.');
			   }
			    else { 
		             $("#tooltip").remove(); 
		           } 
			});
	}
	
	function showTooltip(color, x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
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
	
	