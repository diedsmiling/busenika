{* $Id: userlog.tpl 10028 2010-07-09 11:17:28Z 2tl $ *}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $sort_order == "asc"}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8595;"}
{else}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8593;"}
{/if}
{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}

{/if}
{include file="common_templates/pagination.tpl"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="15%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=timestamp&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a>{if $sort_by == "timestamp"}{$sort_sign}{/if}</th>
	<th width="10%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.points}</a>{if $sort_by == "amount"}{$sort_sign}{/if}</th>
	<th width="75%">{$lang.reason}</th>
</tr>
{foreach from=$userlog item="ul"}
<tr {cycle values=",class=\"table-row\""}>
	<td valign="top">{$ul.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td class="right"  valign="top">{$ul.amount}</td>
	<td  valign="top">
		{if $ul.action == $smarty.const.CHANGE_DUE_ORDER}
			{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:true:true:true}
			{assign var="reason" value=$ul.reason|unescape|unserialize}
			{assign var="order_exist" value=$reason.order_id|fn_get_order_name}
			{$lang.order}&nbsp;{if $order_exist}<a href="{"orders.details?order_id=`$reason.order_id`"|fn_url}" class="underlined">{/if}<strong>#{$reason.order_id}</strong>{if $order_exist}</a>{/if}:&nbsp;{$statuses[$reason.from]}&nbsp;&#8212;&#8250;&nbsp;{$statuses[$reason.to]}{if $reason.text}&nbsp;({$reason.text|fn_get_lang_var}){/if}
		{elseif $ul.action == $smarty.const.CHANGE_DUE_USE}
			{assign var="order_exist" value=$ul.reason|fn_get_order_name}
			{$lang.text_points_used_in_order}: {if $order_exist}<a href="{"orders.details?order_id=`$ul.reason`"|fn_url}">{/if}<strong>#{$ul.reason}</strong>{if $order_exist}</a>{/if}
		{elseif $ul.action == $smarty.const.CHANGE_DUE_ORDER_DELETE}
			{assign var="reason" value=$ul.reason|unescape|unserialize}
			{$lang.order} <strong>#{$reason.order_id}</strong>: {$lang.deleted}
		{elseif $ul.action == $smarty.const.CHANGE_DUE_ORDER_PLACE}
			{assign var="reason" value=$ul.reason|unescape|unserialize}
			{assign var="order_exist" value=$reason.order_id|fn_get_order_name}
			{$lang.order} {if $order_exist}<a href="{"orders.details?order_id=`$reason.order_id`"|fn_url}" class="underlined">{/if}<strong>#{$reason.order_id}</strong>{if $order_exist}</a>{/if}: {$lang.placed}
		{else}
			{hook name="reward_points:userlog"}
			{$ul.reason}
			{/hook}
		{/if}
	</td>
</tr>
{foreachelse}
<tr>
	<td colspan="3"><p class="no-items">{$lang.no_items}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="3">&nbsp;</td>
</tr>
</table>
{include file="common_templates/pagination.tpl"}
{** / userlog description section **}

{capture name="mainbox_title"}{$lang.reward_points_log}{/capture}
