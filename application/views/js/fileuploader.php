  <script type="text/javascript">
  $(document).ready(function() {
	  
	  var uploader = new qq.FileUploader({
          element: document.getElementById('<?php echo $element_id; ?>'),
          action: '<?php echo url::base();?>home/wish/fileuploader/?wish_id=<?php echo $wish_id; ?>',
          debug: false,
          allowedExtensions: <?php echo isset($extensions) ?  '['.implode(',',$extensions). ']' : '[]';?>,
          onComplete: function(id, fileName, responseJSON){
				if(responseJSON['success'])
				{
					var fileHtml = '<li id="file_'+responseJSON['id']+'" class="file_thumb">';
					fileHtml += responseJSON['title'];
					fileHtml += '<span style="float:right;"><a href="#" onclick="deleteFile('+responseJSON['id']+'); return false;">';
					fileHtml += '<?php echo __('delete file'); ?></a></span><br/>';				
					fileHtml += '<?php echo __('insert'); ?> -- <a href="#" onclick="insertLink(\''+responseJSON['link']+'\', \''+responseJSON['title']+'\'); return false;"><?php echo __('link');?> </a>';
					fileHtml +='</li>';							
					$("#files").append(fileHtml);
				}
				$(".qq-upload-list").delay(1000).fadeOut(500);
				$("li.qq-upload-success").delay(1050).remove();
				$("li.qq-upload-fail").delay(1050).remove();
			  },
		onSubmit:function(id, fileName){$(".qq-upload-list").fadeIn(100);}
      });           
    
  });
  
  function deleteFile(id)
  {
	  $.post("<?php echo url::base();?>home/wish/deletefile", {'id':id}, function (data){
			if(data.status == "success")
			{
				$("#file_"+id).fadeOut(500);
			}
		  }, 'json');
  }
  </script>
