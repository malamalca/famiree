<?php
	echo $javascript->link('tinymce/tiny_mce.js');
?>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
	    theme : "advanced",
	    mode: "exact",
	    elements : "PostBody",
	    theme_advanced_toolbar_location : "top",
	    theme_advanced_buttons1 : "formatselect,separator,bold,italic,underline,strikethrough,separator,"
	    + "bullist,numlist,"
		+ "link,unlink,image,separator,"
	    + "undo,redo,cleanup,code",
	    theme_advanced_buttons2 : "",
	    theme_advanced_buttons3 : "",
	    verify_html : true,
	    height:"350px",
	    width: "500px",
	    theme_advanced_resizing_min_height : 320,
	    theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,
		theme_advanced_statusbar_location : "bottom",
		document_base_url : "<?php echo Router::url('/', true); ?>",
		relative_urls : false,
		remove_linebreaks : false,
		remove_script_host : true,
		preformatted : true
		content_css : "<?php echo Router::url('/', true); ?>lil_blogs/css/wysiwyg.css",
		theme_advanced_blockformats : "h1,h2,h3,blockquote,pre"
	});
</script>
