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
		var dataSet = null;
		
		$.post("<?php echo URL::base(); ?>statistics/getData", { "map": maps, "start": startDate, "end": endDate },
		function(data) {
			console.log(data);
		}, "json");

		
		
	}

	
	function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
	}
	
</script>