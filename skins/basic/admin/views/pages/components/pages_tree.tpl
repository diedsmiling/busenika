{* $Id: pages_tree.tpl 10055 2010-07-14 10:15:19Z klerik $ *}
{if !$checkbox_name}{assign var="checkbox_name" value="page_ids"}{/if}

{if $parent_id}<div {if !$expand_all}class="hidden"{/if} id="page_{$parent_id}">{/if}

{foreach from=$pages_tree item=page}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
{if $header && !$hide_header}
{assign var="header" value=""}
<tr>
	<th class="center" width="1%">
	{if $display != "radio"}
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
	{/if}
	</th>
	{if !$picker}
	<th class="center">{$lang.position_short}</th>
	{/if}
	<th width="100%">
		{if !$hide_show_all && !$search.paginate}
		<div class="float-left">
			<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="on_page" class="hand cm-combinations-pages{if $expand_all} hidden{/if}" />
			<img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="off_page" class="hand cm-combinations-pages{if !$expand_all} hidden{/if}" />
		</div>
		&nbsp;
		{/if}
		{$lang.name}
	</th>
	{if !$picker}
	<th>{$lang.status}</th>
	{/if}
	{if !$hide_delete_button}<th>&nbsp;</th>{/if}
</tr>
{/if}
<tr {if $page.level > 0 && !$search.paginate}class="multiple-table-row"{/if}>
	<td class="center" width="1%">
		{if $display == "radio"}
		<input type="radio" name="{$checkbox_name}" value="{$page.page_id}" class="radio cm-item" />
		{else}
		<input type="checkbox" name="{$checkbox_name}[]" id="delete_checkbox" value="{$page.page_id}" class="checkbox cm-item" />
		{/if}
	</td>
	{if !$picker}
	<td>
		<input type="text" name="pages_data[{$page.page_id}][position]" size="3" maxlength="10" value="{$page.position}" class="input-text-short" /></td>
	{/if}
	<td width="100%">
		{strip}
		<div class="float-left" {if !$search.paginate}style="padding-left: {math equation="x*14" x=$page.level|default:0}px;"{/if}>
			{if $page.subpages || $page.has_children}
			{assign var="_dispatch" value=$dispatch|default:"pages.manage"}
			{if $except_id}
				{assign var="except_url" value="&except_id=`$except_id`"}
			{/if}
			<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_page_{$page.page_id}" class="hand cm-combination-pages {if $expand_all && !$hide_show_all}hidden{/if}" {if $page.has_children}onclick="jQuery.ajaxRequest('{"$_dispatch?parent_id=`$page.page_id`&get_tree=multi_level`$except_url`&display=`$display`&checkbox_name=`$checkbox_name`"|fn_url:'A':'rel':'&'}', {$ldelim}result_ids: 'page_{$page.page_id}', caching: true{$rdelim});"{/if} />
			<img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_page_{$page.page_id}" class="hand cm-combination-pages {if !$expand_all || $hide_show_all}hidden{/if}" />
			{elseif !$search.paginate}
			<span style="padding-left: 14px;">&nbsp;</span>
			{/if}

			{if !$picker}<a href="{"pages.update?page_id=`$page.page_id`"|fn_url}" {if $page.status == "N"}class="manage-root-item-disabled"{/if} id="page_title_{$page.page_id}">{else}<span id="page_title_{$page.page_id}">{/if}
				{$page.page}
			{if !$picker}</a>{else}</span>{/if}

			{if $page.page_type}
			{assign var="pt" value=$page_types[$page.page_type]}
			&nbsp;<span class="small-note lowercase">({$lang[$pt.single]})</span>
			{/if}
		</div>
		{/strip}
	</td>
	{if !$picker}
	<td>
		{include file="common_templates/select_popup.tpl" id=$page.page_id status=$page.status hidden=true object_id_name="page_id" table="pages"}
	</td>
	{/if}
	{if !$hide_delete_button}
	<td class="nowrap">
		<input type="hidden" name="pages_data[{$page.page_id}][parent_id]" size="3" maxlength="10" value="{$page.parent_id}" />
		{capture name="tools_items"}
		{if $search.get_tree}
			{assign var="$suffix" value="&amp;multi_level=Y"}
		{/if}
		<li><a class="cm-confirm" href="{"pages.delete?page_type=`$page.page_type`&amp;page_id=`$page.page_id``$suffix`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{if !$picker}
			{assign var="_href" value="pages.update?page_id=`$page.page_id`"}
		{/if}
		{include file="common_templates/table_tools_list.tpl" prefix=$promotion.promotion_id tools_list=$smarty.capture.tools_items href=$_href}
	</td>
	{/if}
</tr>
</table>

{if $page.subpages || $page.has_children}
	{include file="views/pages/components/pages_tree.tpl" pages_tree=$page.subpages parent_id=$page.page_id}
{/if}
{foreachelse}
	{if !$hide_show_all}
		<p class="no-items">{$lang.no_data}</p>
	{/if}
{/foreach}

{if $parent_id}<!--page_{$parent_id}--></div>{/if}
