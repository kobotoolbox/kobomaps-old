<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-01-23
* Show user their stats
*************************************************************/
?>

<script type="text/javascript">

function updatePageInfo(){
	var page_id = $("#pages").val();
	if(page_id != 0){
		var name = $('#pages option:selected').text();
		$.post("<?php echo URL::base(); ?>custompage/getpage", { 'page': page_id }).done(
				function(data) {
					$("#slug").css('border-color', '');
					$('#slug').val(name);
					$('textarea.tinymce').val(data);
				});
	}	
	else{
		$("#slug").css('border-color', '');
		$("#slug").val('');
		$('textarea.tinymce').val('');
	}
}

function checkSlug(){
	$("#slug").css('border-color', '');
	$.post("<?php echo URL::base(); ?>custompage/checkslug", { "slug": $("#slug").val(), 'id': $("#pages").val() }).done(
			function(response) {
				response = JSON.parse(response);

				if(response.status == 'valid'){
					$("#slug").css('border-color', 'green');
					$("#slug").val(response.slug);
				}
				else if(response.status == 'illegal'){
					$("#slug").css('border-color', 'red');
					alert('<?php echo __('Your slug had illegal characters, they have been replaced.')?>');
					$("#slug").val(response.slug);
				}
				else if(response.status == 'notUnique'){
					$("#slug").css('border-color', 'red');
					alert('<?php echo __('You slug has already been used. Please choose another.')?>');
				}
			});
}

$(document).ready(function(){
	$("#slug").change(function(){
		checkSlug();
	});
});

//all options for the tinymce editor in the textarea
$(function() {
    $('textarea.tinymce').tinymce({
    	
     	script_url : '<?php echo URL::base();?>media/js/tiny_mce/tiny_mce.js',
     
        // General options
        theme : "advanced",
        skin : "o2k7",
        skin_variant : "silver",
        plugins : "pdw, pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect, |,|, pdw_toggle,|,fullscreen",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,forecolor,backcolor, fontselect,fontsizeselect,",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl, ",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak, | ,insertdate,inserttime,preview, |,link,unlink,anchor,image,cleanup,help,code,",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false,

        // Example content CSS (should be your site CSS)
        content_css : '<?php echo URL::base();?>media/css/style.css',

        //plugin for the toggle on the toolbars
        pdw_toggle_on: 1,
        pdw_toggle_toolbars: "3, 4",

        //height: '610',
        width: '600',

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "lists/link_list.js",
        external_image_list_url : "lists/image_list.js",
        media_external_list_url : "lists/media_list.js",
    });

  	//writing the onchange prevents it from being called when typing a new slug, which was calling it for some reason
   $('#pages').change(function(){
		updatePageInfo();
   });

  	//loading may perhaps be on a different page than the default, find the data for the loaded page
   if($("#pages").val() != 0){
	   updatePageInfo();
   }
});

</script>