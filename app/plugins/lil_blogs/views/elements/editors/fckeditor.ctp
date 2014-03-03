<?php
	echo $javascript->link('fckeditor/fckeditor.js');
?>
<script type="text/javascript">
window.onload = function() {
	
	var oFCKeditor = new FCKeditor( 'PostBody' );
	oFCKeditor.BasePath	= '<?php echo Router::url('/', true); ?>js/fckeditor/';
	
	oFCKeditor.Config['CustomConfigurationsPath'] = '<?php echo Router::url('/', true); ?>lil_blogs/js/fckconfig.js?' + ( new Date() * 1 )  ;
	oFCKeditor.Config['BaseHref'] = '<?php echo Router::url('/', true); ?>';
	
	oFCKeditor.Width = 500;
	oFCKeditor.Height = 350;
	
	oFCKeditor.ToolbarSet = 'LilBlogs';
	oFCKeditor.ReplaceTextarea();
}
</script>
