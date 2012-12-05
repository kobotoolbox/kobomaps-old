<?php /** Handle Tiny MCE **/ ?>
<script type="text/javascript" src="<?php echo url::base(); ?>media/js/tiny_mce/jquery.tinymce.js"></script>
	<script type="text/javascript">
	$().ready(function() {
		$('textarea.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo url::base(); ?>media/js/tiny_mce/tiny_mce.js',
	
			// General options
			theme : "advanced",
			plugins : "lists,searchreplace,paste",
	
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,|,forecolor,backcolor",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : false,
	
			// Example content CSS (should be your site CSS)
			content_css : "<?php echo url::base();?>media/css/tinymce.css",
	
			
		});
	});		
	</script>
<?php /** End Tiny MCE **/ ?>