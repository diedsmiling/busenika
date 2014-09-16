{* $Id: manage.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{script src="js/template_editor_scripts.js"}
<script type="text/javascript">
	//<![CDATA[
	lang.text_enter_filename = '{$lang.text_enter_filename|escape:"javascript"}';
	lang.text_are_you_sure_to_proceed = '{$lang.text_are_you_sure_to_proceed|escape:"javascript"}';

	$(document).ready(function(){$ldelim}
		template_editor.refresh();
	{$rdelim});
	//]]>
</script>

{capture name="mainbox"}

{notes title=$lang.legend}
	<table cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td class="nowrap"><img src="{$images_dir}/icons/icon_folder_c.gif" width="15" height="13" alt="" border="0" />&nbsp;&nbsp;-&nbsp;</td>
		<td>{$lang.legend_customer_directory}</td>
	</tr>
	<tr valign="top">
		<td class="nowrap"><img src="{$images_dir}/icons/icon_folder_a.gif" width="15" height="13" alt="" border="0" />&nbsp;&nbsp;-&nbsp;</td>
		<td>{$lang.legend_admin_directory}</td>
	</tr>
	<tr valign="top">
		<td class="nowrap"><img src="{$images_dir}/icons/icon_folder_ac.gif" width="15" height="13" alt="" border="0" />&nbsp;&nbsp;-&nbsp;</td>
		<td>{$lang.legend_all_areas_directory}</td>
	</tr>
	</table>
{/notes}


<div id="error_box" class="hidden">
	<div align="center" class="notification-e">
		<div id="error_status"></div>
	</div>
</div>

<div id="status_box" class="hidden">
	<div class="notification-n" align="center">
		<div id="status"></div>
	</div>
</div>

<div class="items-container">
<div class="editor-tools clear">
	<div class="float-left"><strong>{$lang.current_path}</strong>:&nbsp;&nbsp;<span id="path"></span></div>
	<div class="select-field float-right">
		<input type="checkbox" name="show_active_skins_only" id="show_active_skins_only" value="Y" {if $show_active_skins_only == "Y"}checked="checked"{/if} onclick="jQuery.ajaxRequest('{"template_editor.active_skins?show_active_skins_only="|fn_url:'A':'rel':'&'}'+(this.checked ? 'Y' : ''), {literal}{callback: [template_editor, 'refresh'], cache: false}{/literal});" class="checkbox" />
		<label for="show_active_skins_only">{$lang.show_active_skins_only}</label>
	</div>
</div>

<div id="filelist">{$lang.loading}</div>

<div class="editor-tools clear" id="actions_table">
	<ul>
		<li><a href="javascript: template_editor.delete_file();">{$lang.delete}</a></li>
		<li>|<a href="javascript: template_editor.rename();">{$lang.rename}</a></li>
		<li>|<a href="javascript: template_editor.restore_file();">{$lang.restore_from_repository}</a></li>
	{if 1||$smarty.const.IS_WINDOWS == false}
	<li>|
	{*<a href="javascript: template_editor.show_perms_dialog();">{$lang.change_permissions}</a>*}
	{capture name="chmod"}
		{include file="views/template_editor/components/chmod.tpl"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="chmod" text=$lang.change_permissions content=$smarty.capture.chmod link_text=$lang.change_permissions act="edit" edit_onclick="template_editor.parse_permissions();" link_text=$lang.change_permissions}
	</li>
	{/if}
	</ul>

	<ul id="file_actions">
		<li>|<a href="javascript: template_editor.show_content('');">{$lang.edit}</a></li>
	</ul>
</div>
</div>

<div class="buttons-container">
	{capture name="upload_file"}
		<form name="upload_form" action="{""|fn_url}" method="post" enctype="multipart/form-data" class="cm-form-highlight">
		<div class="object-container">
			<div class="form-field">
				<label class="cm-required">{$lang.select}:</label>
				<input type="hidden" name="fake" value="1" />
				{include file="common_templates/fileuploader.tpl" var_name="uploaded_data[0]"}
			</div>
		</div>
		
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_text=$lang.upload but_name="dispatch[template_editor.upload_file]" cancel_action="close"}
		</div>
		</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="upload_file" text=$lang.upload_file content=$smarty.capture.upload_file link_text=$lang.upload_file act="general"}
	
	{capture name="add_new_folder"}
		<form name="create_directory" onsubmit="template_editor.create_file(document.getElementById('new_directory').value, true); return false;" class="cm-form-highlight">
		<div class="object-container">
			<div class="form-field">
				<label for="new_directory" class="cm-required">{$lang.name}:</label>
					<input class="input-text main-input" type="text" name="new_directory" id="new_directory" value="" size="30" />
			</div>
		</div>
		
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" create=true but_onclick="template_editor.create_file(document.getElementById('new_directory').value, true)" cancel_action="close"}
		</div>
		</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_folder" text=$lang.create_folder content=$smarty.capture.add_new_folder link_text=$lang.create_folder act="general"}
	
	{capture name="add_new_file"}
		<form name="create_file" onsubmit="template_editor.create_file(document.getElementById('new_file').value, false); return false;" class="cm-form-highlight">
		<div class="object-container">
			<div class="form-field">
				<label for="new_file" class="cm-required">{$lang.name}:</label>
				<input class="input-text main-input" type="text" name="new_file" id="new_file" value="" size="30" />
			</div>
		</div>
		
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" create=true but_onclick="template_editor.create_file(document.getElementById('new_file').value, false)" cancel_action="close"}
		</div>
		</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_file" text=$lang.create_file content=$smarty.capture.add_new_file link_text=$lang.create_file act="general"}
</div>

{include file="views/template_editor/components/template_editor_picker.tpl"}

{/capture}
{include file="common_templates/mainbox.tpl" content=$smarty.capture.mainbox title=$lang.template_editor}
