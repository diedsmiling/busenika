{* $Id: manage.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

{$lang.text_sitemap_page}

<form action="{""|fn_url}" method="post" name="sitemap_form">

<table cellpadding="0" cellspacing="0" width="100%"	border="0" class="table">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="5%">{$lang.position_short}</th>
	<th width="25%">{$lang.section_name}</th>
	<th width="70%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$sitemap_sections item=section}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="section_ids[]" value="{$section.section_id}" class="checkbox cm-item" /></td>
	<td>
		<input type="text" name="section_data[{$section.section_id}][position]" size="2" value="{$section.position}" class="input-text-short" /></td>
	<td>
		<input type="text" name="section_data[{$section.section_id}][section]" size="30" value="{$section.section}" class="input-text" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$section.section_id status=$section.status hidden="" object_id_name="section_id" table="sitemap_sections"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"sitemap.delete_sitemap_section?section_id=`$section.section_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$section.section_id tools_list=$smarty.capture.tools_items href="sitemap.update?section_id=`$section.section_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $sitemap_sections}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[sitemap.delete_sitemap_sections]" class="cm-process-items cm-confirm" rev="sitemap_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[sitemap.update_sitemap_sections]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
	{capture name="tools"}
		{capture name="add_new_picker"}
		<form action="{""|fn_url}" method="post" name="add_sitemap_sections_form" class="cm-form-highlight">
		<div class="object-container">
			<div class="tabs cm-j-tabs">
				<ul>
					<li id="tab_sitemap_new" class="cm-js cm-active"><a>{$lang.general}</a></li>
				</ul>
			</div>

			<div class="cm-tabs-content" id="content_tab_sitemap_new">
			<fieldset>
				<div class="form-field">
					<label class="cm-required">{$lang.name}:</label>
					<input type="text" name="add_section_data[0][section]" size="30" value="" onfocus="this.value = ''" class="input-text-large main-input" />
				</div>

				<div class="form-field">
					<label>{$lang.position}:</label>
					<input type="text" name="add_section_data[0][position]" size="2" value="" class="input-text-short" />
				</div>

				{include file="common_templates/select_status.tpl" input_name="add_section_data[0][status]" id="add_section_data"}
			</fieldset>
			</div>
		</div>

		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[sitemap.add_sitemap_sections]" cancel_action="close"}
		</div>

		</form>
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_site_map_section" text=$lang.add_new_site_map_section content=$smarty.capture.add_new_picker link_text=$lang.add_site_map_section act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_site_map_section" text=$lang.add_new_site_map_section link_text=$lang.add_site_map_section act="general"}
	</div>
</div>
</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.sitemap content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
