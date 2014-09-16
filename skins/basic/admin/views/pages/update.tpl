{* $Id: update.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

{capture name="tabsbox"}

<form action="{""|fn_url}" method="post" name="page_update_form" class="cm-form-highlight">
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
<input type="hidden" name="page_data[page_id]" value="{$page_data.page_id}" />
<input type="hidden" name="page_data[page_type]" id="page_type" size="55" value="{$page_type}" class="input-text" />

<div id="content_basic">

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.information}

	<div class="form-field">	
		<label class="cm-required" for="elm_parent_id">{$lang.parent_page}:</label>
		{if "pages"|fn_show_picker:$smarty.const.PAGE_THRESHOLD}
			{include file="pickers/pages_picker.tpl" data_id="location_page" input_name="page_data[parent_id]" item_ids=$page_data.parent_id|default:"0" hide_link=true hide_delete_button=true show_root=true default_name=$lang.root_level display_input_id="elm_parent_id" except_id=$page_data.page_id}
		{else}
			<select	name="page_data[parent_id]" id="elm_parent_id">
				<option	value="0">- {$lang.root_page} -</option>
				{foreach from=""|fn_get_pages_plain_list item="page"}
					{if ($page.id_path|strpos:"`$page_data.id_path`/" === false && $page_data.page_id != $page.page_id) || $mode == 'add'}
					<option	value="{$page.page_id}" {if $page.page_id == $smarty.request.parent_id || $page.page_id == $page_data.parent_id}selected="selected"{/if}>{$page.page|escape|indent:$page.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
					{/if}
				{/foreach}
			</select>
		{/if}
	</div>
	
	<div class="form-field">
		<label for="page" class="cm-required">{$lang.name}:</label>
		<input type="text" name="page_data[page]" id="page" size="55" value="{$page_data.page}" class="input-text-large main-input" />
	</div>

	{include file="views/companies/components/company_field.tpl" title=$lang.vendor name="page_data[company_id]" id="page_data_company_id" selected=$page_data.company_id}
	
	{if $page_type != $smarty.const.PAGE_TYPE_LINK}
	<div class="form-field">
		<label for="page_descr">{$lang.description}:</label>
		<textarea id="page_descr" name="page_data[description]" cols="55" rows="8" class="input-textarea-long">{$page_data.description}</textarea>
		{include file="common_templates/wysiwyg.tpl" id="page_descr"}
	</div>
	{/if}
	
	{if $page_type == $smarty.const.PAGE_TYPE_LINK}
		{include file="views/pages/components/pages_link.tpl"}
	{/if}

	{include file="common_templates/select_status.tpl" input_name="page_data[status]" id="page_data" obj=$page_data hidden=true}

</fieldset>

{if $page_type != $smarty.const.PAGE_TYPE_LINK}
<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.seo_meta_data}

	<div class="form-field">
		<label for="page_page_title">{$lang.page_title}:</label>
		<input type="text" name="page_data[page_title]" id="page_page_title" size="55" value="{$page_data.page_title}" class="input-text-large" />
	</div>

	<div class="form-field">
		<label for="page_meta_descr">{$lang.meta_description}:</label>
		<textarea name="page_data[meta_description]" id="page_meta_descr" cols="55" rows="2" class="input-textarea-long">{$page_data.meta_description}</textarea>
	</div>

	<div class="form-field">
		<label for="page_meta_keywords">{$lang.meta_keywords}:</label>
		<textarea name="page_data[meta_keywords]" id="page_meta_keywords" cols="55" rows="2" class="input-textarea-long">{$page_data.meta_keywords}</textarea>
	</div>

</fieldset>
{/if}

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.availability}
	<div class="form-field">
		<label>{$lang.usergroups}:</label>
			<div class="select-field">
				{include file="common_templates/select_usergroups.tpl" id="ug_id" name="page_data[usergroup_ids]" usergroups="C"|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$page_data.usergroup_ids input_extra="" list_mode=false}
			</div>
	</div>
	<div class="form-field">
		<label for="page_date">{$lang.created_date}:</label>
		{include file="common_templates/calendar.tpl" date_id="page_date" date_name="page_data[timestamp]" date_val=$page_data.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
	</div>

	{include file="views/localizations/components/select.tpl" data_name="page_data[localization]" data_from=$page_data.localization}
	
	<div class="form-field">
		<label for="use_avail_period">{$lang.use_avail_period}:</label>
		<div class="select-field float-left nowrap">
			<input type="hidden" name="page_data[use_avail_period]" value="N" /><input type="checkbox" name="page_data[use_avail_period]" id="use_avail_period" {if $page_data.use_avail_period == "Y"}checked="checked"{/if} value="Y" class="checkbox" onclick="fn_activate_calendar(this);"/>
		</div>
	</div>
	
	{capture name="calendar_disable"}{if $page_data.use_avail_period != "Y"}disabled="disabled"{/if}{/capture}
	
	<div class="form-field">
		<label for="avail_from">{$lang.avail_from}:</label>
		{include file="common_templates/calendar.tpl" date_id="avail_from" date_name="page_data[avail_from_timestamp]" date_val=$page_data.avail_from_timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
	</div>
	
	<div class="form-field">
		<label for="avail_till">{$lang.avail_till}:</label>
		{include file="common_templates/calendar.tpl" date_id="avail_till" date_name="page_data[avail_till_timestamp]" date_val=$page_data.avail_till_timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
	</div>

</fieldset>

{literal}
<script language="javascript">
//<![CDATA[
function fn_activate_calendar(el)
{
	$('#avail_from').attr('disabled', !el.checked);
	$('#avail_till').attr('disabled', !el.checked);
}
//[[>
</script>
{/literal}

</div>

{if $mode != "add"}
	<div id="content_blocks">
		{include file="views/block_manager/components/select_blocks.tpl" object_id=$page_data.page_id data_name="page_data" section="pages"}
	</div>
{/if}

<div id="content_addons">
{if $page_type != $smarty.const.PAGE_TYPE_LINK}
{hook name="pages:detailed_content"}
{/hook}
{/if}
</div>

{hook name="pages:tabs_content"}
{/hook}

<div class="buttons-container cm-toggle-button buttons-bg">
	{if $mode == "add"}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[pages.add]"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[pages.update]"}
	{/if}
</div>

</form>

{hook name="pages:tabs_extra"}
{/hook}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox track=true}

{/capture}

{if $mode == "add"}
	{assign var="_title" value=$lang[$page_type_data.new_name]}
{else}
	{assign var="_title" value=$lang[$page_type_data.edit_name]|cat:":&nbsp;`$page_data.page`"}
	{assign var="select_languages" value=true}
	{if $page_type != $smarty.const.PAGE_TYPE_LINK}
		{notes title=$lang.preview}
			{assign var="view_uri" value="pages.view?page_id=`$page_data.page_id`"}
			{assign var="view_uri_escaped" value="`$view_uri`&amp;action=preview"|fn_url:'C':'http':'&':$smarty.const.DESCR_SL|escape:"url"}
			<p>{$lang.txt_page_access_link}: <a target="_blank" title="{$view_uri|fn_url:'C':'http':'&':$smarty.const.DESCR_SL}" href="{$view_uri|fn_url:'C':'http':'&amp;':$smarty.const.DESCR_SL}">{$view_uri|fn_url:'C':'rel':'&':$smarty.const.DESCR_SL|fn_compact_value:28}</a></p>
			<p>{$lang.txt_access_link_as_admin}: <a target="_blank" title="{$view_uri|fn_url:'C':'http':'&':$smarty.const.DESCR_SL}" href="{"profiles.act_as_user?user_id=`$auth.user_id`&amp;area=C&amp;redirect_url=`$view_uri_escaped`"|fn_url}">{$view_uri|fn_url:'C':'rel':'&':$smarty.const.DESCR_SL|fn_compact_value:28}</a></p>
		{/notes}
	{/if}
{/if}

{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox}
