{* $Id: manage.tpl 10365 2010-08-05 14:18:33Z klerik $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

{capture name="tabsbox"}

<div id="content_translations">

{include file="views/languages/components/langvars_search_form.tpl"}

<form action="{""|fn_url}" method="post" name="language_variables_form">
<input type="hidden" name="q" value="{$smarty.request.q}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

{include file="common_templates/pagination.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table">
<tr>
	<th width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="35%">{$lang.language_variable}</th>
	<th width="64%">{$lang.value}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$lang_data item="var" key="key"}
<tr {cycle values="class=\"table-row\", " name="2"} valign="top">
	<td width="1%">
		<input type="checkbox" name="names[]" value="{$var.name}" class="checkbox cm-item" /></td>
	<td width="29%">
		<input type="hidden" name="lang_data[{$key}][name]" value="{$var.name}" />
		<p><strong>{$var.name}</strong></p></td>
	<td width="70%">
		<textarea name="lang_data[{$key}][value]" cols="55" rows="3" class="input-text">{$var.value}</textarea></td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"languages.delete_variable?name=`$var.name`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$var.name tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

{if $lang_data}
	<div class="buttons-container buttons-bg">
		<div class="float-left">
			{capture name="tools_list"}
			<ul>
				<li><a name="dispatch[languages.delete_variables]" class="cm-process-items cm-confirm" rev="language_variables_form">{$lang.delete_selected}</a></li>
			</ul>
			{/capture}
			{include file="buttons/save.tpl" but_name="dispatch[languages.update_variables]" but_role="button_main"}
			{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
		</div>
		
		<div class="float-right">
			{capture name="tools"}
				{include file="common_templates/popupbox.tpl" id="add_language" text=$lang.add_language link_text=$lang.add_language content=$smarty.capture.add_language act="general"}
				{include file="common_templates/popupbox.tpl" id="add_langvar" text=$lang.add_language_variable link_text=$lang.add_language_variable content=$smarty.capture.add_langvar act="general"}
			{/capture}
			{include file="common_templates/popupbox.tpl" id="add_language" text=$lang.add_language link_text=$lang.add_language act="general"}
			{include file="common_templates/popupbox.tpl" id="add_langvar" text=$lang.add_language_variable link_text=$lang.add_language_variable act="general"}
		</div>
	</div>
{/if}
</form>

{capture name="add_langvar"}

<form action="{""|fn_url}" method="post" name="lang_add_var">
<input type="hidden" name="page" value="{$smarty.request.page}" />
<input type="hidden" name="q" value="{$smarty.request.q}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<div class="object-container">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="add-new-table">
<tr class="cm-first-sibling">
	<th width="35%">{$lang.language_variable}</th>
	<th width="64%">{$lang.value}</th>
	<th width="1%">&nbsp;</th>
</tr>
<tr id="box_new_lang_tag" valign="top">
	<td>
		<input type="text" size="30" name="new_lang_data[0][name]" class="input-text" /></td>
	<td>
		<textarea name="new_lang_data[0][value]" cols="48" rows="2" class="input-textarea-long"></textarea></td>
	<td>
		{include file="buttons/multiple_buttons.tpl" item_id="new_lang_tag"}</td>
</tr>
</table>

</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[languages.add_variables]" cancel_action="close"}
</div>

</form>

{/capture}

</div>

<div class="hidden" id="content_languages">

{capture name="add_language"}
<form action="{""|fn_url}" method="post" name="add_language_form">
<input type="hidden" name="page" value="{$smarty.request.page}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />


<div class="object-container">

<fieldset>
	<div class="form-field">
		<label for="elm_lng_code" class="cm-required">{$lang.language_code}:</label>
		<input id="elm_lng_code" type="text" name="new_language[lang_code]" value="" size="6" maxlength="2" class="input-text" />
	</div>

	<div class="form-field">
		<label for="elm_lng_name" class="cm-required">{$lang.name}:</label>
		<input id="elm_lng_name" type="text" name="new_language[name]" value="" maxlength="64" class="input-text" />
	</div>

	{include file="common_templates/select_status.tpl" display="radio" input_name="new_language[status]" hidden=false}

</fieldset>

</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[languages.add_languages]" cancel_action="close"}
</div>

</form>
{/capture}

<form action="{""|fn_url}" method="post" name="languages_form">
<input type="hidden" name="page" value="{$smarty.request.page}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table">
<tr class="cm-first-sibling">
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.language_code}</th>
	<th>{$lang.name}</th>
	<th width="100%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{if $langs|count == 1}
	{assign var="disable_change" value=true}
{/if}
{foreach from=$langs item="language"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
		<input type="checkbox" name="lang_codes[]" value="{$language.lang_code}" {if $language.lang_code == "EN"}disabled="disabled"{/if} class="checkbox cm-item" /></td>
	<td>
		<strong>{$language.lang_code}</strong></td>
	<td>
		<input type="text" name="update_language[{$language.lang_code}][name]" value="{$language.name}" maxlength="64" class="input-text" /></td>
	<td>
		{if $disable_change}
			{assign var="lang_id" value=""}
		{else}
			{assign var="lang_id" value=$language.lang_code}
		{/if}
		{include file="common_templates/select_popup.tpl" id=$lang_id prefix="lng" status=$language.status hidden="" object_id_name="lang_code" table="languages"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		{if $language.lang_code == "EN"}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
		{else}
			<li><a class="cm-confirm" href="{"languages.delete_language?lang_code=`$language.lang_code`"|fn_url}">{$lang.delete}</a></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$language.lang_code tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{/foreach}
</table>
<div class="buttons-container buttons-bg">
		<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[languages.delete_languages]" class="cm-process-items cm-confirm" rev="languages_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[languages.update_languages]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
		<div class="float-right">
		{capture name="tools"}
			{include file="common_templates/popupbox.tpl" id="add_language" text=$lang.add_language link_text=$lang.add_language content=$smarty.capture.add_language act="general"}
			{include file="common_templates/popupbox.tpl" id="add_langvar" text=$lang.add_language_variable link_text=$lang.add_language_variable content=$smarty.capture.add_langvar act="general"}
		{/capture}

				{include file="common_templates/popupbox.tpl" id="add_language" text=$lang.add_language link_text=$lang.add_language act="general"}
				{include file="common_templates/popupbox.tpl" id="add_langvar" text=$lang.add_language_variable link_text=$lang.add_language_variable act="general"}
	</div>
</div>

</form>

</div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.languages content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
