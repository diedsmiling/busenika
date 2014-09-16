{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{** banners section **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="banners_form" class="cm-form-highlight" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" class="checkbox cm-check-items" /></th>
	<th>{$lang.banner}</th>
	<th>{$lang.type}</th>
	<th width="100%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$banners item=banner}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="banner_ids[]" value="{$banner.banner_id}" class="checkbox cm-item" /></td>
	<td>
		<input type="text" name="banners[{$banner.banner_id}][banner]" value="{$banner.banner}" size="20" class="input-text" /></td>
	<td class="nowrap">{if $banner.type == "G"}{$lang.graphic_banner}{else}{$lang.text_banner}{/if}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$banner.banner_id status=$banner.status hidden=true object_id_name="banner_id" table="banners"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"banners.delete?banner_id=`$banner.banner_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$banner.banner_id tools_list=$smarty.capture.tools_items href="banners.update?banner_id=`$banner.banner_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>
<div class="buttons-container buttons-bg">
	{if $banners}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[banners.delete]" class="cm-process-items cm-confirm" rev="banners_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[banners.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="banners.add" prefix="bottom" hide_tools="true" link_text=$lang.add_banner}
	</div>
</div>
</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="banners.add" prefix="top" hide_tools="true" link_text=$lang.add_banner}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.banners content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}

{** ad section **}
