{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}

{capture name="tabsbox"}

<form action="{""|fn_url}" method="post" name="tax_form" class="cm-form-highlight">
<input type="hidden" name="tax_id" value="{$tax.tax_id}" />
<input type="hidden" name="destination_id" value="{$destination_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<div id="content_general">
<fieldset>
	<div class="form-field">
		<label for="tax" class="cm-required">{$lang.name}:</label>
		<input type="text" name="tax_data[tax]" id="tax" size="30" value="{$tax.tax}" class="input-text-large main-input" />
	</div>
	
	<div class="form-field">
		<label for="regnumber">{$lang.regnumber}:</label>
		<input type="text" name="tax_data[regnumber]" id="regnumber" size="30" value="{$tax.regnumber}" class="input-text" />
	</div>
	
	<div class="form-field">
		<label for="priority">{$lang.priority}:</label>
		<input type="text" name="tax_data[priority]" id="priority" size="5" value="{$tax.priority}" class="input-text" />
	</div>
	
	<div class="form-field">
		<label for="address_type" class="cm-required">{$lang.rates_depend_on}:</label>
		<select name="tax_data[address_type]" id="address_type">
			<option value="S" {if $tax.address_type == "S"}selected="selected"{/if}>{$lang.shipping_address}</option>
			<option value="B" {if $tax.address_type == "B"}selected="selected"{/if}>{$lang.billing_address}</option>
		</select>
	</div>
	
	{include file="common_templates/select_status.tpl" input_name="tax_data[status]" id="tax_data" obj=$tax}
	
	<div class="form-field">
		<label for="price_includes_tax">{$lang.price_includes_tax}:</label>
		<input type="hidden" name="tax_data[price_includes_tax]" value="N" />
		<input type="checkbox" name="tax_data[price_includes_tax]" id="price_includes_tax" value="Y" {if $tax.price_includes_tax == "Y"}checked="checked"{/if} class="checkbox" />
	</div>
</fieldset>
<!-- id="content_general" --></div>

<div id="content_tax_rates">

<table cellpadding="0" cellspacing="0" border="0" class="table">
<tr>
	<th>{$lang.location}</th>
	<th>{$lang.rate_value}</th>
	<th>{$lang.type}</th>
</tr>
{foreach from=$destinations item=destination}
{assign var="d_id" value=$destination.destination_id}
<tr {cycle values="class=\"table-row\", "}>
	<td>{$destination.destination}</td>
	<td><input type="hidden" name="tax_data[rates][{$d_id}][rate_id]" value="{$rates.$d_id.rate_id}" />
		<input type="text" name="tax_data[rates][{$d_id}][rate_value]" value="{$rates.$d_id.rate_value}" class="input-text" /></td>
	<td>
		<select name="tax_data[rates][{$d_id}][rate_type]">
			<option value="F" {if $rates.$d_id.rate_type == "F"}selected="selected"{/if}>{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
			<option value="P" {if $rates.$d_id.rate_type == "P"}selected="selected"{/if}>{$lang.percent} (%)</option>
		</select>
	</td>
</tr>
{/foreach}
</table>
<!-- id="content_tax_rates" --></div>

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[taxes.update]"}
</div>

</form>
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox track=true active_tab=$smarty.request.selected_section}

{/capture}

{if $mode == "add"}
	{assign var="title" value=$lang.new_tax}
{else}
	{assign var="title" value="`$lang.editing_tax`:&nbsp;`$tax.tax`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
