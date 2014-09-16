{* $Id: items_list.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{foreach from=$items item="item"}
<tr {if $item.type == "aab" || $item.type == "ipb"}class="manage-root-row"{else}{cycle values="class=\"table-row\", "}{/if}>
	<td class="center" width="1%">
		{if $disable_host_ip}
			{assign var="disable_host_ip" value="N"}
		{/if}
		{if !$disable_host_ip && $addons.access_restrictions.admin_reverse_ip_access == "Y" && $selected_section == "admin_area" && $host_ip == $item.ip_from && $host_ip == $item.ip_to}
			{assign var="disable_host_ip" value="Y"}
		{/if}
		<input type="checkbox" name="item_ids[]" value="{$item.item_id}" {if $disable_host_ip == "Y"}disabled="disabled"{/if} class="checkbox cm-item" /></td>
	{if $selected_section == "ip" || $selected_section == "admin_panel"}
	<td class="nowrap">{$item.ip_from|long2ip}{if $item.ip_from != $item.ip_to} - {$item.ip_to|long2ip}{/if}</td>
	{else}
	<td class="nowrap">{$item.value}</td>
	{/if}
	<td><input type="input" name="items_data[{$item.item_id}][reason]" value="{$item.reason}" class="input-text-long" /></td>
	<td class="nowrap">&nbsp;{$item.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>
		<input type="hidden" name="items_data[{$item.item_id}][type]" value="{$item.type}" />
		{include file="common_templates/select_popup.tpl" id=$item.item_id status=$item.status hidden="" object_id_name="item_id" table="access_restriction"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"access_restrictions.delete?item_id=`$item.item_id`&amp;selected_section=`$selected_section`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$item.item_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{/foreach}
