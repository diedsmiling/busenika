{* $Id: manage.tpl 6369 2008-11-20 10:54:05Z zeke $ *}

{foreach from=$items item="item"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
{if $header}
{assign var="header" value=""}
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
	</th>
	<th>{$lang.position_short}</th>
	<th>
		<div class="float-left">
			<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="on_item" class="hand cm-combinations" />
			<img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="off_item" class="hand cm-combinations hidden" />
		</div>
		&nbsp;{$lang.name}
	</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{/if}
<tr class="{if $item.level > 0}multiple-table-row{/if} cm-row-item">
	<td class="center" width="1%">
		<input type="checkbox" name="static_data_ids[]" value="{$item.param_id}" class="checkbox cm-item" />
	</td>
	<td>
		<input type="text" name="static_data[{$item.param_id}][position]" value="{$item.position}" size="3" class="input-text-short" />
	</td>
	<td width="100%">
		<span style="padding-left: {math equation="x*14" x=$item.level|default:0}px;">
			{if $item.subitems}
			<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_item_{$item.param_id}" class="hand cm-combination" />
			<img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_item_{$item.param_id}" class="hand cm-combination hidden" />{/if}
			{$item.descr}
		</span>
	</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$item.param_id status=$item.status hidden=true object_id_name="param_id" table="static_data"}
	</td>
	<td class="nowrap">
		{include file="common_templates/popupbox.tpl" act="edit" text=$lang[$section_data.edit_title]|cat:": `$item.descr`" link_text=$lang.edit id="group`$item.param_id`" link_class="tool-link" href="static_data.update?param_id=`$item.param_id`&amp;section=$section"}

		{capture name="tools_items"}
		<ul>
		{hook name="static_data:list_extra_links"}
		<li><a class="cm-confirm cm-ajax cm-delete-row" rev="static_data_list" href="{"static_data.delete?param_id=`$item.param_id`&amp;section=`$section`"|fn_url}">{$lang.delete}</a></li>
		{/hook}
		</ul>
		{/capture}
		{if $smarty.capture.tools_items|strpos:"<li>"}&nbsp;&nbsp;|
			{include file="common_templates/tools.tpl" prefix=$item.param_id hide_actions=true tools_list=$smarty.capture.tools_items display="inline" link_text=$lang.more link_meta="lowercase"}
		{/if}
	</td>
</tr>
</table>
{if $item.subitems}
<div id="item_{$item.param_id}" class="hidden">
	{include file="views/static_data/components/multi_list.tpl" items=$item.subitems header=false}
</div>
{/if}

{foreachelse}
	<p class="no-items">{$lang.no_data}</p>
{/foreach}