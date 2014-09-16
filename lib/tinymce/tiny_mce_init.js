function fn_init_wysiwyg()
{
	var support_langs = ['ar', 'be', 'bn', 'bs', 'ch', 'cy', 'de', 'el', 'es', 'fa', 'fr', 'gu', 'hi', 'hu', 'id', 'is', 'ja', 'lt', 'mk', 'mn', 'nb', 'nn', 'pl', 'ro', 'sc', 'si', 'sl', 'sr', 'ta', 'th', 'tt', 'uk', 'zh', 'az', 'bg', 'br', 'ca', 'cs', 'da', 'dv', 'en', 'et', 'fi', 'gl', 'he', 'hr', 'ia', 'ii', 'it', 'ko', 'lv', 'ml', 'ms', 'nl', 'no', 'pt', 'ru', 'se', 'sk', 'sq', 'sv', 'te', 'tr', 'tw', 'vi'];
	tinyMCE.init({
		mode : 'none',
		plugins : 'safari,pagebreak,style,table,advhr,advimage,advlink,preview,media,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,inlinepopups',
		theme_advanced_buttons1 : 'newdocument,|,cut,copy,paste,|,undo,redo,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,forecolor,backcolor,styleprops',
		theme_advanced_buttons2 : 'formatselect,fontselect,fontsizeselect,|,link,unlink,anchor,image,media,cleanup,|,sub,sup,charmap',
		theme_advanced_buttons3 : 'tablecontrols,|,removeformat,visualaid,|,advhr,visualchars,nonbreaking,|,preview,fullscreen',
		theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,
		language: (jQuery.inArray(tiny_lang, support_langs) == -1 ? 'en' : tiny_lang),
		theme : 'advanced',
		skin: 'cart',
		inlinepopups_skin: 'cart',
		strict_loading_mode: true,
		convert_urls: false,
		remove_script_host: false,
		index_script: index_script
	});
}