{* $Id: categories_tree.tpl 10552 2010-08-31 06:44:29Z angel $ *}
{if $parent_id}
<div class="hidden" id="cat_{$parent_id}">
{/if}
{foreach from=$categories_tree item=category}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-fixed">
{if $header && !$parent_id}
{assign var="header" value=""}
<tr>
	<th class="center" width="3%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="5%">{$lang.position_short}</th>
	<th width="62%">
		{if $show_all}
		<div class="float-left">
			<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="on_cat" class="hand cm-combinations{if $expand_all} hidden{/if}" />
			<img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="off_cat" class="hand cm-combinations{if !$expand_all} hidden{/if}" />
		</div>
		{/if}
		&nbsp;{$lang.name}
	</th>
	<th class="right" width="10%">{$lang.products}</th>
	<th width="10%">{$lang.status}</th>
	<th width="10%">&nbsp;</th>
</tr>
{/if}
<tr {if $category.level > 0}class="multiple-table-row"{/if}>
   	{math equation="x*14" x=$category.level|default:"0" assign="shift"}
	<td class="center" width="3%">
		<input type="checkbox" name="category_ids[]" value="{$category.category_id}" class="checkbox cm-item" /></td>
	<td width="5%">
		<input type="text" name="categories_data[{$category.category_id}][position]" value="{$category.position}" size="3" class="input-text-short" /></td>
	<td width="62%">
	{strip}
		<span style="padding-left: {$shift}px;">
			{if $category.has_children || $category.subcategories}
				{if $show_all}
					<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_cat_{$category.category_id}" class="hand cm-combination {if $expand_all}hidden{/if}" />
				{else}
					<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_cat_{$category.category_id}" class="hand cm-combination" onclick="if (!$('#cat_{$category.category_id}').children().get(0)) jQuery.ajaxRequest('{"categories.manage?category_id=`$category.category_id`"|fn_url:'A':'rel':'&'}', {$ldelim}result_ids: 'cat_{$category.category_id}'{$rdelim})" />
				{/if}
				<img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_cat_{$category.category_id}" class="hand cm-combination{if !$expand_all || !$show_all} hidden{/if}" />&nbsp;
			{/if}
			<a href="{"categories.update?category_id=`$category.category_id`"|fn_url}"{if $category.status == "N"} class="manage-root-item-disabled"{/if}{if !$category.subcategories} style="padding-left: 14px;"{/if} >{$category.category}</a>{if $category.status == "N"}&nbsp;<span class="small-note">-&nbsp;[{$lang.disabled}]</span>{/if}
		</span>
	{/strip}
	</td>
	<td width="10%" class="nowrap right">
		<a href="{"products.manage?cid=`$category.category_id`"|fn_url}">{if "COMPANY_ID"|defined}{$lang.manage_products}{else}<u>&nbsp;{$category.product_count}&nbsp;</u>{/if}</a>&nbsp;
		{include file="buttons/button.tpl" but_text=$lang.add but_href="products.add?category_id=`$category.category_id`" but_role="add"}
	</td>
	<td width="10%">
		{include file="common_templates/select_popup.tpl" id=$category.category_id status=$category.status hidden=true object_id_name="category_id" table="categories"}
	</td>
	<td width="10%" class="nowrap">
		<center>
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"categories.delete?category_id=`$category.category_id`"|fn_url}"><img src="/images/cross.png"></a></li>
		{/capture}
		<a href="zenon.php?dispatch=categories.update&category_id={$category.category_id}"><img src="/images/edit.png"></a>
		<a onclick="return confirm('Are you sure you want to delete?')" href="zenon.php?dispatch=categories.delete&category_id={$category.category_id}"><img src="/images/cross.png"></a>
		</center>
	</td>
</tr>
</table>
{if $category.has_children || $category.subcategories}
	<div{if !$expand_all} class="hidden"{/if} id="cat_{$category.category_id}">
	{if $category.subcategories}
		{include file="views/categories/components/categories_tree.tpl" categories_tree=$category.subcategories parent_id=false}
	{/if}
	<!--cat_{$category.category_id}--></div>
{/if}
{/foreach}
{if $parent_id}<!--cat_{$parent_id}--></div>{/if}
