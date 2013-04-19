<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add2_js.php - View
* Written by Etherton Technologies Ltd. 2012
* Started on 12/06/2011
*************************************************************/
?>
	

<script type="text/javascript">
  /**
  * Used to toggle specific divs
  * @param string elem_id generic id to toggle divs
  */
  function toggleTable(elem_id)
  {
  $('#'+elem_id).siblings('.data_specify_div').slideToggle('slow');
  }


  $(document).ready(function(){
  $('.q4').bind('scroll', fnscroll);
  $('.q2').html($('.q4').html());
  $('.q3').html($('.q4').html());
  $('.q2 .firstCol, .q2 td.header, .q3 thead, .q4 thead, .q4 tr td.header, .q4 tr th.header').remove();
  });

  function fnscroll(){
  $('.q2').scrollLeft($('.q4').scrollLeft());
  $('.q3').scrollTop($('.q4').scrollTop());
  }


</script>