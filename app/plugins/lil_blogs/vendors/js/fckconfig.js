FCKConfig.ToolbarSets["LilBlogs"] = [
	['Style','-','Bold','Italic','Underline','StrikeThrough','-','OrderedList','UnorderedList','-','Link','Unlink','Image','-','Undo','Redo','RemoveFormat']
] ;
FCKConfig.EditorAreaStyles = 'body, * { font-size: 14px; }' ;
FCKConfig.ToolbarCanCollapse = false;

FCKConfig.ImageBrowser = false ;
FCKConfig.ImageUpload = false ;
FCKConfig.ImageDlgHideAdvanced = true ;
FCKConfig.ImageDlgHideLink = true ;

FCKConfig.LinkBrowser = false ;
FCKConfig.LinkUpload = false ;
FCKConfig.LinkDlgHideAdvanced = true ;

FCKConfig.CustomStyles = {
	'Paragraph' : { Element : 'p' },
	'Heading 1' : {Element :'h1', Styles : {'font-size': '18px', 'font-weight': 'bold'} },
	'Heading 2' : {Element :'h2', Styles : {'font-size': '16px', 'font-weight': 'bold'} },
	'Heading 3' : {Element :'h3', Styles : {'font-size': '14px', 'font-weight': 'bold'} },
	'Code' : {Element :'code'},
	'Citation' : {Element :'blockquote', Styles : {'font-style': 'italic'}}
};

FCKConfig.StylesXmlPath = null;