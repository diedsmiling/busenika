{* $Id: file_browser.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

<div id="view_box_server_upload" class="popup-edit-content cm-popup-box cm-picker cm-bg-close">
	<div class="cm-popup-content-header">
		<div class="float-right"><img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="{$lang.close}" class="hand cm-popup-switch" /></div>
		<h3>{$lang.file_upload}</h3>
	</div>
	<div class="cm-popup-content-footer">
		<div class="object-container">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td>
					<div id="server_file_tree" class="file-browser panel-design"></div></td>
				<td width="100%">
					<h5>{$lang.preview}</h5>
					<div class="cm-preview-wrap">
						<div id="preview">
							<img src="{$images_dir}/no_image.gif" id="fo_img" onerror="this.src = '{$images_dir}/no_image.gif';" class="hidden" align="middle" alt="{$lang.no_preview_available}" />
							<textarea cols="30" rows="12" id="fo_preview" class="hidden"></textarea>
							<div id="fo_no_preview">{$lang.no_preview_available}</div>
						</div>
					</div>
				</td>
			</tr>
			</table>
			<p>{$lang.text_click_to_select}</p>
		</div>

		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_text=$lang.select_file but_onclick="$(window['last_clicked_item']).parent().trigger('dblclick')" but_type="button" cancel_action="close"}
		</div>
	</div>
</div>

{if !$smarty.capture.file_browser_loaded}
{capture name="file_browser_loaded"}Y{/capture}

{script src="js/picker.js"}
{script src="js/fileuploader_scripts.js"}
{script src="js/jqueryFileTree.js"}

<script type="text/javascript">
//<![CDATA[
	$(document).ready( function() {ldelim}
		$('#server_file_tree').file_tree({ldelim} root: '', script: '{"file_browser.browse"|fn_url:'A':'rel':'&'}' {rdelim}, function(file) {ldelim}
			jQuery.ajaxRequest('{"file_browser.get_content"|fn_url:'A':'rel':'&'}', {ldelim}data:{ldelim}file: escape(file){rdelim}, callback: fileuploader.get_content_callback, method: 'post'{rdelim});
		{rdelim});
	{rdelim});
//]]>
</script>
{/if}