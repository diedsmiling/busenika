{* $Id: manage.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{** text_banners section **}

{capture name="mainbox"}

{capture name="tabsbox"}

<div id="content_{$link_to}">

<form action="{""|fn_url}" method="post" name="manage_banners_form_{$link_to}">
<input type="hidden" name="page" value="{$smarty.request.page}" />
<input type="hidden" name="banner_type" value="{$banner_type}" />
<input type="hidden" name="link_to" value="{$link_to}" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.title}</th>
	{if $banner_type == "T"}
	<th>{$lang.show_title}</th>
	{/if}
	{if $banner_type == "T" || $banner_type == "P"}
	<th>{$lang.width}</th>
	<th>{$lang.height}</th>
	{/if}
	{if $banner_type != "P"}
	<th width="70%">{if $link_to == "G"}{$lang.product_groups}{elseif $link_to == "C"}{$lang.categories}{elseif $link_to == "P"}{$lang.products}{else}{$lang.url}{/if}</th>
	{/if}
	<th>{$lang.new_window}</th>
	{if $banner_type == "P"}
	<th width="1%">{$lang.add_to_cart}</th>
	{/if}
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{if $banners}
{foreach from=$banners item=c_banner}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
   		<input type="checkbox" name="banner_ids[]" value="{$c_banner.banner_id}" class="checkbox cm-item" /></td>
	<td class="nowrap">
		<a href="{"banners_manager.update?banner_id=`$c_banner.banner_id`"|fn_url}" title="{$c_banner.title}">{$c_banner.title|truncate:30}</a></td>
	{if $banner_type == "T"}
	<td class="center">
		<input type="hidden" name="banners_data[{$c_banner.banner_id}][show_title]" value="N" />
   		<input type="checkbox" name="banners_data[{$c_banner.banner_id}][show_title]" {if $c_banner.show_title == "Y"}checked="checked"{/if} value="Y" class="checkbox" /></td>
	{/if}
	{if $banner_type == "T" || $banner_type == "P"}
   	<td>
   		<input type="text" name="banners_data[{$c_banner.banner_id}][width]" value="{$c_banner.width}" size="10" class="input-text" /></td>
   	<td>
   		<input type="text" name="banners_data[{$c_banner.banner_id}][height]" value="{$c_banner.height}" size="10" class="input-text" /></td>
	{/if}

	{if $banner_type != "P"}
	<td>
   		{if $link_to == "C"}
	   		{foreach from=$c_banner.categories key="item_id" item="item_name" name="fe"}
   			<a href="{"categories.update?category_id=`$item_id`"|fn_url}">{$item_name}</a>{if !$smarty.foreach.fe.last}, {/if}
   			{/foreach}

		{elseif $link_to == "P"}
	   		{foreach from=$c_banner.product_ids item="item_id" name="fe"}
   			<a href="{"products.update?product_id=`$item_id`"|fn_url}">{$item_id|fn_get_product_name|escape}</a>{if !$smarty.foreach.fe.last}, {/if}
   			{/foreach}

   		{elseif $link_to == "G"}
   			<a href="{"product_groups.update?group_id=`$c_banner.group_id`"|fn_url}">{$c_banner.group_name}</a>

   		{else}
   			<a href="{$c_banner.url|fn_url}" title="{$c_banner.url}">{$c_banner.url|truncate:50}</a>
   		{/if}
   	</td>
	{/if}
	<td class="center">
		<input type="hidden" name="banners_data[{$c_banner.banner_id}][new_window]" value="N" />
   		<input type="checkbox" name="banners_data[{$c_banner.banner_id}][new_window]" {if $c_banner.new_window == "Y"}checked="checked"{/if} value="Y" class="checkbox" /></td>
	{if $banner_type == "P"}
	<td class="center">
		<input type="hidden" name="banners_data[{$c_banner.banner_id}][to_cart]" value="N" />
   		<input type="checkbox" name="banners_data[{$c_banner.banner_id}][to_cart]" {if $c_banner.to_cart == "Y"}checked="checked"{/if} value="Y" class="checkbox" /></td>
	{/if}
	<td>
		{include file="common_templates/select_popup.tpl" id=$c_banner.banner_id status=$c_banner.status hidden="" object_id_name="banner_id" table="aff_banners"}
	</td>
   	<td class="nowrap">
		{capture name="tools_items"}
		{hook name="products:list_extra_links"}
		<li><a class="cm-confirm" href="{"banners_manager.delete?banner_id=`$c_banner.banner_id`&amp;banner_type=`$banner_type`&amp;link_to=`$link_to`"|fn_url}">{$lang.delete}</a></li>
		{/hook}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$c_banner.banner_id tools_list=$smarty.capture.tools_items href="banners_manager.update?banner_id=`$c_banner.banner_id`"}
   	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="9"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

<div class="buttons-container buttons-bg">
	{if $banners}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[banners_manager.delete]" class="cm-process-items cm-confirm" rev="manage_banners_form_{$link_to}">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[banners_manager.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="banners_manager.add?banner_type=`$banner_type`&link_to=`$link_to`" prefix="bottom" hide_tools="true" link_text=$lang.add_banner}
	</div>
</div>

</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="banners_manager.add?banner_type=`$banner_type`&link_to=`$link_to`" prefix="top" hide_tools="true" link_text=$lang.add_banner}
{/capture}


<!--content_{$link_to}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$link_to}

{/capture}

{if $banner_type == "T"}
	{assign var="title" value=$lang.text_banners}
{elseif $banner_type == "G"}
	{assign var="title" value=$lang.graphic_banners}
{else}
	{assign var="title" value=$lang.product_banners}
{/if}

{include file="common_templates/mainbox.tpl" title="`$lang.banners`: `$title`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}

{** text_banners section **}
