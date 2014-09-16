{* $Id: js_order.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<tr {if !$clone}id="{$holder}_{$order_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
	<td>
		<a href="{"orders.details?order_id=`$order_id`"|fn_url}">&nbsp;<strong>#{$order_id}</strong>&nbsp;</a></td>
	<td>{*<input type="hidden" name="origin_statuses[{$order_id}]" value="{$status}" />*}{if $clone}{$status}{else}{include file="common_templates/status.tpl" status=$status display="view" name="order_statuses[`$order_id`]"}{/if}</td>
	<td>{$customer}</td>
	<td>
		<a href="{"orders.details?order_id=`$order_id`"|fn_url}" class="underlined">{if $clone}{$timestamp}{else}{$timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}{/if}</a></td>
	<td class="right">
		{if $clone}{$total}{else}{include file="common_templates/price.tpl" value=$total}{/if}</td>
	{if !$view_only}
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a onclick="jQuery.delete_js_item('{$holder}', '{$order_id}', 'o'); return false;">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$order_id tools_list=$smarty.capture.tools_items href="orders.details?order_id=`$order_id`"}
	</td>
	{/if}
</tr>