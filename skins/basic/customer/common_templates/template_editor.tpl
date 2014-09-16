{* $Id: template_editor.tpl 6962 2009-03-02 14:40:38Z angel $ *}

<div id="template_list_menu"><div></div><ul class="float-left"><li></li></ul></div>

<div class="popup-content cm-popup-box cm-picker hidden" id="template_editor">
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="{$lang.close}" title="{$lang.close}" class="hand cm-popup-switch" />
		</div>
		<h3>{$lang.template_editor}:</h3>
	</div>
	<div class="cm-popup-content-footer">
		<div id="template_editor_content">
			<table width="100%" cellpadding="0" cellspacing="0" class="editor-table">
				<tr valign="top" class="max-height">
					<td class="templates-tree max-height">
						<div>
						<h4>{$lang.templates_tree}</h4>
						<ul id="template_list"><li></li></ul></div>
					</td>
					<td>
						<textarea id="template_text"></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div class="buttons-container">
			<span class="submit-button cm-button-main">
			<input type="button" class="cm-popup-switch" onclick="fn_save_template();" value="{$lang.save}" />
			</span>
			<input type="button" class="cm-popup-switch" onclick="fn_restore_template();" value="{$lang.restore_from_repository}" />
			{$lang.or}&nbsp;&nbsp;&nbsp;<a class="cm-popup-switch">{$lang.cancel}</a>
		</div>
	</div>
</div>

{script src="js/jquery.easydrag.js"}
{script src="js/picker.js"}
{script src="js/design_mode.js"}
{script src="lib/editarea/edit_area_loader.js"}

<script type="text/javascript">
//<![CDATA[
var current_url = '{$config.current_url}';
lang.text_page_changed = '{$lang.text_page_changed|escape:"javascript"}';
lang.text_restore_question = '{$lang.text_restore_question|escape:"javascript"}';
lang.text_template_changed = '{$lang.text_template_changed|escape:"javascript"}';
//]]>
</script>