{* $Id: manage.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="shippings_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="1%" class="center">{$lang.position_short}</th>
	<th width="100%">{$lang.name}</th>
	<th>{$lang.delivery_time}</th>
	<th>{$lang.weight_limit}&nbsp;({$settings.General.weight_symbol})</th>
	<th>{$lang.usergroups}</th>
	<th width="5%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$shippings item=shipping}
<tr {cycle values="class=\"table-row\", "} valign="top">
	<td width="1%" class="center">
		<input type="checkbox" name="shipping_ids[]" value="{$shipping.shipping_id}" class="checkbox cm-item" /></td>
	<td class="center">
		<input type="text" name="shipping_data[{$shipping.shipping_id}][position]" size="3" value="{$shipping.position}" class="input-text-short" /></td>
	<td>
		<input type="text" name="shipping_data[{$shipping.shipping_id}][shipping]" size="30" value="{$shipping.shipping}" class="input-text" />{include file="views/companies/components/company_name.tpl" company_id=$shipping.company_id}</td>
	<td>
		<input type="text" name="shipping_data[{$shipping.shipping_id}][delivery_time]" size="20" value="{$shipping.delivery_time}" class="input-text" /></td>
	<td class="center nowrap">
		<input type="text" name="shipping_data[{$shipping.shipping_id}][min_weight]" size="4" value="{$shipping.min_weight}" class="input-text" />&nbsp;-&nbsp;<input type="text" name="shipping_data[{$shipping.shipping_id}][max_weight]" size="4" value="{if $shipping.max_weight != "0.00"}{$shipping.max_weight}{/if}" class="input-text" /></td>
	<td>
		{include file="common_templates/select_usergroups.tpl" id="ship_data_`$shipping.shipping_id`" name="shipping_data[`$shipping.shipping_id`][usergroup_ids]" usergroups=$usergroups usergroup_ids=$shipping.usergroup_ids input_extra="" list_mode=true}
	</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$shipping.shipping_id status=$shipping.status hidden="" object_id_name="shipping_id" table="shippings"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		{if !"COMPANY_ID"|defined || ("COMPANY_ID"|defined && $shipping.company_id == $smarty.const.COMPANY_ID)}
		<li><a class="cm-confirm" href="{"shippings.delete_shipping?shipping_id=`$shipping.shipping_id`"|fn_url}">{$lang.delete}</a></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$shipping.shipping_id tools_list=$smarty.capture.tools_items href="shippings.update?shipping_id=`$shipping.shipping_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="8"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $shippings}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[shippings.delete_shippings]" class="cm-process-items cm-confirm" rev="shippings_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[shippings.update_shippings]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="shippings.add" prefix="bottom" hide_tools=true link_text=$lang.add_shipping_method}
	</div>
</div>
</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="shippings.add" prefix="top" hide_tools=true link_text=$lang.add_shipping_method}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.manage_shippings content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}

