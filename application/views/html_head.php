<meta charset="utf-8">
	<title><?php echo __("site name")." :: ".$title ?></title>
	<?php foreach ($script_files as $file) echo HTML::script($file), PHP_EOL ?>
	<?php foreach ($script_views as $view) echo $view; ?>
	<?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), PHP_EOL ?>
