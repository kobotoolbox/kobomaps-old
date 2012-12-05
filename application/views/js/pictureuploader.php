  <script type="text/javascript">
  $(document).ready(function() {
	  
	  var uploader = new qq.FileUploader({
          element: document.getElementById('<?php echo $element_id; ?>'),
          action: '<?php echo url::base();?>home/wish/imageuploader/?wish_id=<?php echo $wish_id; ?>',
          debug: false,
          allowedExtensions: <?php echo isset($extensions) ?  '['.implode(',',$extensions). ']' : '[]';?>,
          onComplete: function(id, fileName, responseJSON){
				if(responseJSON['success'])
				{
					var imageHtml = '<div id="image_'+responseJSON['id']+'" style="height:90px;" class="image_thumb"><div style="float:left;">';
					imageHtml += '<img src="'+responseJSON['thumbnail'] + '"';
					imageHtml += ' style="margin:3px;"></div>'+responseJSON['title'];
					imageHtml += '<span style="float:right;"><a href="#" onclick="deletePic('+responseJSON['id']+'); return false;">';
					imageHtml += '<?php echo __('delete picture'); ?></a></span>';				
					imageHtml += '<br/><?php echo __('insert'); ?> -- <a href="#" onclick="insertLink(\''+responseJSON['fullsize']+'\', \''+responseJSON['title']+'\'); return false;"><?php echo __('link');?> </a>, ';
					imageHtml += '<a href="#" onclick="insertImage(\''+responseJSON['thumbnail']+'\', \''+responseJSON['fullsize']+'\'); return false;"><?php echo __('thumbnail');?></a>, ';
					imageHtml += '<a href="#" onclick="insertImage(\''+responseJSON['passport']+'\', \''+responseJSON['fullsize']+'\'); return false;"><?php echo __('passport');?></a>, ';
					imageHtml += '<a href="#" onclick="insertImage(\''+responseJSON['fullsize']+'\', \''+responseJSON['fullsize']+'\'); return false;"><?php echo __('full size');?></a>';
					imageHtml +='</div>';							
					$("#images").append(imageHtml);
				}
				$(".qq-upload-list").delay(1000).fadeOut(500);
				$("li.qq-upload-success").delay(1050).remove();
				$("li.qq-upload-fail").delay(1050).remove();
			  },
		onSubmit:function(id, fileName){$(".qq-upload-list").fadeIn(100);}
      });           
    
  });
  
  function deletePic(id)
  {
	  $.post("<?php echo url::base();?>home/wish/deleteimage", {'id':id}, function (data){
			if(data.status == "success")
			{
				$("#image_"+id).fadeOut(500);
			}
		  }, 'json');
  }
  </script>
