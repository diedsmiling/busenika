{* $Id: manage.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{** groups section **}

{capture name="mainbox"}

{capture name="tabsbox"}

<div id="content_{$link_to}">

<form action="{""|fn_url}" method="post" name="manage_groups_form_{$link_to}">
<input type="hidden" name="link_to" value="{$link_to}" />

{include file="common_templates/pagination.tpl" div_id="pagination_contents_$link_to"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="50%">{$lang.name}</th>
	<th width="45%">{if $link_to == "C"}{$lang.categories}{elseif $link_to == "P"}{$lang.products}{else}{$lang.url}{/if}</th>
	<th width="5%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{if $groups}
{foreach from=$groups item=c_group}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
   		<input type="checkbox" name="group_ids[]" value="{$c_group.group_id}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"product_groups.update?group_id=`$c_group.group_id`"|fn_url}">{$c_group.name}</a></td>
   	<td>
   		{if $link_to == "C"}
	   		{foreach from=$c_group.categories key="item_id" item="item_name" name="fe"}
   			<a href="{"categories.update?category_id=`$item_id`"|fn_url}">{$item_name}</a>{if !$smarty.foreach.fe.last}, {/if}
   			{/foreach}

		{elseif $link_to == "P"}
	   		{foreach from=$c_group.product_ids item="item_id" name="fe"}
   			<a href="{"products.update?product_id=`$item_id`"|fn_url}">{$item_id|fn_get_product_name|escape}</a>{if !$smarty.foreach.fe.last}, {/if}
   			{/foreach}

		{else}
   			<a href="{$c_group.url|fn_url}">{$c_group.url}</a>
   		{/if}
   	</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$c_group.group_id status=$c_group.status hidden="" object_id_name="group_id" table="aff_groups"}
	</td>
   	<td class="nowrap">
   		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"product_groups.delete?group_id=`$c_group.group_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$c_group.group_id tools_list=$smarty.capture.tools_items href="product_groups.update?group_id=`$c_group.group_id`"}
   		</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{include file="common_templates/pagination.tpl" div_id="pagination_contents_$link_type"}

{if $link_to == "C"}
	{assign var="link_text" value=$lang.add_group_for_categories}
{elseif $link_to == "P"}
	{assign var="link_text" value=$lang.add_group_for_products}
{elseif $link_to == "U"}
	{assign var="link_text" value=$lang.add_url_group}
{/if}
<div class="buttons-container buttons-bg">
	{if $groups}
	<div class="float-left">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[product_groups.delete]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="product_groups.add?link_to=$link_to" prefix="bottom" hide_tools="true" link_text=$link_text}
	</div>	
</div>

</form>


<!--content_{$link_to}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$link_to}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.product_groups content=$smarty.capture.mainbox}

{** groups section **}
