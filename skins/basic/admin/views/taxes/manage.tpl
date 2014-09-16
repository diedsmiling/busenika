{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="taxes_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.name}</th>
	<th>{$lang.regnumber}</th>
	<th>{$lang.priority}</th>
	<th>{$lang.rates_depend_on}</th>
	<th>{$lang.price_includes_tax}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$taxes item=tax}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
		<input type="checkbox" name="tax_ids[]" value="{$tax.tax_id}" class="checkbox cm-item" /></td>
	<td class="nowrap">
		<input type="text" name="tax_data[{$tax.tax_id}][tax]" size="20" value="{$tax.tax}" class="input-text" /></td>
	<td>
		<input type="text" name="tax_data[{$tax.tax_id}][regnumber]" size="10" value="{$tax.regnumber}" class="input-text" /></td>
	<td class="center">
		<input type="text" name="tax_data[{$tax.tax_id}][priority]" size="3" value="{$tax.priority}" class="input-text" /></td>
	<td><select name="tax_data[{$tax.tax_id}][address_type]">
			<option value="S" {if $tax.address_type == "S"}selected="selected"{/if}>{$lang.shipping_address}</option>
			<option value="B" {if $tax.address_type == "B"}selected="selected"{/if}>{$lang.billing_address}</option>
		</select>
	</td>
	<td class="center">
		<input type="hidden" name="tax_data[{$tax.tax_id}][price_includes_tax]" value="N" />
		<input type="checkbox" name="tax_data[{$tax.tax_id}][price_includes_tax]" value="Y" {if $tax.price_includes_tax == "Y"}checked="checked"{/if} class="checkbox" />
	</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$tax.tax_id status=$tax.status hidden="" object_id_name="tax_id" table="taxes"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"taxes.delete?tax_id=`$tax.tax_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$tax.tax_id tools_list=$smarty.capture.tools_items href="taxes.update?tax_id=`$tax.tax_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="8"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $taxes}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a class="cm-process-items" name="dispatch[taxes.apply_selected_taxes]" rev="taxes_form">{$lang.apply_tax_to_products}</a></li>
			<li><a class="cm-process-items" name="dispatch[taxes.unset_selected_taxes]" rev="taxes_form">{$lang.unset_tax_to_products}</a></li>
			<li><a class="cm-confirm cm-process-items" name="dispatch[taxes.delete]" rev="taxes_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[taxes.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
	{include file="common_templates/tools.tpl" tool_href="taxes.add" prefix="bottom" hide_tools=true link_text=$lang.add_tax}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="taxes.add" prefix="top" hide_tools=true link_text=$lang.add_tax}
{/capture}

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.taxes content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
