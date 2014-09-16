{* $Id: file_browser_standalone.tpl 10055 2010-07-14 10:15:19Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
<title>{$lang.file_browser}</title>
{include file="common_templates/styles.tpl" include_file_tree=true}
{include file="common_templates/scripts.tpl"}
{script src="js/jqueryFileTree.js"}
{script src="lib/tinymce/tiny_mce_popup.js"}
{script src="js/fileuploader_scripts.js"}
{script src="js/core.js"}

<script type="text/javascript">
//<![CDATA[
{literal}

function fn_init_file_browser()
{
	if (jQuery.browser.msie) {
		// The following code fixes the bug: IE did not bubble submit event after document.body.innerHTML was rewritten.
		$(document.body).html($(document.body).html());
	}{/literal}
	$('#server_file_tree').file_tree({ldelim}root: '', script: '{"file_browser.browse"|fn_url:'A':'rel':'&'}', thumb_list_id: 'thumb_list', select_mode_class: 'fb-mode'{$rdelim}, null, function(file) {ldelim}
	{literal}
		fileuploader.set_file(file, true);
	});
	$(window).resize(function(){
		var tl_offset = $('#thumb_list').offset();
		$('#thumb_list').height($(window).height() - $('#file_uploader').height() - $('.buttons-container:first').height() - 87);
		$('#server_file_tree').height($(window).height() - $('#file_uploader').height() - $('.buttons-container:first').height() - 87);
	});
	if ($('#translate_box').length) {
		$('#translate_box').easydrag();
	}

	$('.cm-cancel').click(function () {
		tinyMCEPopup.close();
	});
};

tinyMCEPopup.executeOnLoad('fn_init_file_browser();');

function fn_form_post_upload_form(data)
{
	if (data.refresh) {
		$('.cm-selected-mode').trigger('click');
	}
}

{/literal}
//]]>
</script>
</head>

<body>
{**[LOADING_MESSAGE]**}
{include file="common_templates/loading_box.tpl"}
{**[/LOADING_MESSAGE]**}

<div class="fb-content">
	<h2>
		<div class="float-right">
			<a class="fb-mode cm-selected-mode" rel="thumbs_view" id="thumbs_view">{$lang.thumbnails}</a>
			<a class="fb-mode" rel="list_view" id="list_view">{$lang.text_list}</a>
		</div>
		{$lang.file_tree}
	</h2>
	<table id="fb_holder" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="200">
			<div id="server_file_tree" class="tiny-file-browser panel-design"></div>
		</td>
		<td>
			<div id="thumb_list" class="panel-design"></div>
		</td>
	</tr>
	</table>

	<div id="file_uploader" class="clear">
		<div class="float-left">
			<form name="upload_form" action="{""|fn_url}" method="POST" enctype="multipart/form-data" onsubmit="window.focus();" class="cm-ajax">
			{include file="common_templates/fileuploader.tpl" var_name="upload_file[0]" hide_server=true}
			<div class="float-left" style="margin-top: 50px;">
				{include file="buttons/button.tpl" but_text=$lang.upload_file but_name="dispatch[file_browser.file_upload]" but_role="submit"}
			</div>
			</form>
		</div>
	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_text=$lang.select_file but_type="button" but_onclick="$('#thumb_list .cm-clicked').trigger('dblclick')" cancel_action="close"}
</div>
{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}

</body>

</html>
