{* $Id: template_editor_picker.tpl 7497 2009-05-19 10:41:21Z zeke $ *}

<div class="popup-content cm-popup-box cm-picker hidden" id="template_editor">
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="{$lang.close}" title="{$lang.close}" class="hand cm-popup-switch" />
		</div>
		<h3>{$lang.template_editor}:</h3>
	</div>
	<div class="cm-popup-content-footer">
		<div id="template_editor_content">
			<textarea id="template_text" style="height: 350px; width: 100%;"></textarea>
		</div>
		<div class="buttons-container">
			{capture name="extra_buttons"}
				{include file="buttons/button.tpl" but_type="button" but_meta="cm-popup-switch" but_onclick="template_editor.restore_file()" but_text=$lang.restore_from_repository}
			{/capture}
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="template_editor.save_content()" cancel_action="close" extra=$smarty.capture.extra_buttons allow_href=true but_meta="cm-popup-switch"}
		</div>
	</div>
</div>

{script src="js/picker.js"}
{script src="js/design_mode.js"}
{script src="lib/editarea/edit_area_loader.js"}

<script type="text/javascript">
//<![CDATA[
lang.text_page_changed = '{$lang.text_page_changed|escape:"javascript"}';
lang.text_restore_question = '{$lang.text_restore_question|escape:"javascript"}';
lang.text_template_changed = '{$lang.text_template_changed|escape:"javascript"}';
//]]>
</script>