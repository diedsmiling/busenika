{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

<p>{$lang.text_sitemap_link}</p>

<form action="{""|fn_url}" method="post" name="links_form">
<input type="hidden" name="section_id" value="{$smarty.request.section_id}" />

<table cellpadding="0" cellspacing="0" width="100%"	border="0" class="table">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="4%">{$lang.position_short}</th>
	<th width="45%">{$lang.name}</th>
	<th width="50%">{$lang.url}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$links item=link key=keys}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<input type="checkbox" name="link_ids[]" value="{$link.link_id}" class="checkbox cm-item" /></td>
	<td>
		<input type="text" name="link_data[{$link.link_id}][position]" size="2" value="{$link.position}" class="input-text-short" /></td>
	<td>
		<input type="text" name="link_data[{$link.link_id}][link]" size="25" value="{$link.link}" class="input-text" /></td>
	<td>
		<input type="text" name="link_data[{$link.link_id}][link_href]" size="35" value="{$link.link_href}" class="input-text-large" /></td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"sitemap.delete_link?section_id=`$smarty.request.section_id`&amp;link_id=`$link.link_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$link.link_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $links}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[sitemap.delete_links]" class="cm-process-items cm-confirm" rev="links_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[sitemap.update_links]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
	{capture name="tools"}
		{capture name="add_new_picker"}
			<form action="{""|fn_url}" method="post" name="add_links_form">
			<input type="hidden" name="section_id" value="{$smarty.request.section_id}" />
			<div class="object-container">
			<table cellpadding="0" cellspacing="0" border="0" class="add-new-table">
			<tr class="cm-first-sibling">
				<th>{$lang.position_short}</th>
				<th>{$lang.name}</th>
				<th>{$lang.url}</th>
				<th width="100%">&nbsp;</th>
			</tr>			 
			<tr id="box_add_link">
				<td>
					<input type="text" name="add_link_data[0][position]" size="2" value="" class="input-text-short" /></td>
				<td>
					<input type="text" name="add_link_data[0][link]" size="25" value="" class="input-text" /></td>
				<td>
					<input type="text" name="add_link_data[0][link_href]" size="35" value="" class="input-text" /></td>
				<td>
					{include file="buttons/multiple_buttons.tpl" item_id="add_link"}</td>
			</tr>
			</table>
			</div>
			
			<div class="buttons-container">
				{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[sitemap.add_links]" cancel_action="close"}
			</div>
			</form>
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_campaigns" text=$lang.add_new_sitemap_section_link content=$smarty.capture.add_new_picker link_text=$lang.add_link act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_campaigns" text=$lang.add_new_sitemap_section_link link_text=$lang.add_link act="general"}
	</div>
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.section_links content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}