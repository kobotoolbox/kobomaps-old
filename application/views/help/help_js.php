<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
 * help_js.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-22
*************************************************************/
?>

<script type="text/javascript">

	function toggleClass(elem){
		$('.'+elem).toggle('slow');
	}

	function openHelp(elem){
		$('.helpGroup').hide('slow');
		if(typeof(elem.name) != null){ 
			$('.' + elem.name).show('slow');
			window.location.hash=elem.href;
		}
		console.log(window.location.hash);
	}
	
</script>