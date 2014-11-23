{* $Id: update.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

{include file="common_templates/file_browser.tpl"}

{capture name="mainbox"}

{if $mode == "update"}
{capture name="tabsbox"}

<div id="content_general">
{/if}

<form action="{""|fn_url}" method="post" name="shippings_form" enctype="multipart/form-data" class="cm-form-highlight">
<input type="hidden" name="shipping_id" value="{$smarty.request.shipping_id}" />

<fieldset>
<div class="form-field">
	<label for="ship_descr_shipping" class="cm-required">{$lang.name}:</label>
	<input type="text" name="shipping_data[shipping]" id="ship_descr_shipping" size="30" value="{$shipping.shipping}" class="input-text-large main-input" />
</div>

{include file="views/companies/components/company_field.tpl" title=$lang.vendor name="shipping_data[company_id]" id="shipping_data_`$smarty.request.shipping_id`" selected=$shipping.company_id}

<div class="form-field">
	<label>{$lang.icon}:</label>
	{include file="common_templates/attach_images.tpl" image_name="shipping" image_object_type="shipping" image_pair=$shipping.icon no_detailed="Y" hide_titles="Y" image_object_id=$smarty.request.shipping_id}
</div>

<div class="form-field">
	<label for="delivery_time">{$lang.delivery_time}:</label>
	<input type="text" name="shipping_data[delivery_time]" id="delivery_time" size="30" value="{$shipping.delivery_time}" class="input-text" />
</div>

<div class="form-field">
	<label for="min_weight">{$lang.weight_limit}&nbsp;({$settings.General.weight_symbol}):</label>
	<input type="text" name="shipping_data[min_weight]" id="min_weight" size="4" value="{$shipping.min_weight}" class="input-text" />&nbsp;-&nbsp;<input type="text" name="shipping_data[max_weight]" size="4" value="{if $shipping.max_weight != "0.00"}{$shipping.max_weight}{/if}" class="input-text" />
</div>

<div class="form-field">
	<label>{$lang.rate_calculation}:</label>
	<div class="float-left">
		<div class="select-field">
			<input type="radio" name="shipping_data[rate_calculation]" id="rate_calculation_M" value="M" {if $shipping.rate_calculation == "M" || ! $shipping.rate_calculation}checked="checked"{/if} onclick="document.getElementById('service').disabled=true;" class="radio" />
			<label for="rate_calculation_M">{$lang.rate_calculation_manual}</label>
		</div>
		<div class="select-field">
			<input type="radio" name="shipping_data[rate_calculation]" id="rate_calculation_R" value="R" {if $shipping.rate_calculation == "R"}checked="checked"{/if} onclick="document.getElementById('service').disabled=false;" class="radio" />
			<label for="rate_calculation_R">{$lang.rate_calculation_realtime}</label>
		</div>
	</div>
</div>

<div class="form-field">
	<label for="service">{$lang.shipping_service}:</label>
	<div class="float-left">
		<select name="shipping_data[service_id]" id="service" {if $shipping.rate_calculation == "M"}disabled="disabled"{/if}>
			<option	value="">--</option>
			{foreach from=$services item=service}
				<option	value="{$service.service_id}" {if $shipping.service_id == $service.service_id}selected="selected"{/if}>{$service.description}</option>
			{/foreach}
		</select>
		&nbsp;&nbsp;<strong>{$lang.test}</strong>: &nbsp;{$lang.weight} ({$settings.General.weight_symbol})&nbsp;
		<input id="weight" type="text" class="input-text" size="3" name="weight" value="0" />
		{include file="buttons/button_popup.tpl" but_text=$lang.test but_href="shippings.test?service_id=" href_extra="document.getElementById('service').value + '&weight='+ document.getElementById('weight').value" width="500" height="230" scrollbars="no" toolbar="no" menubar="no" but_role="text"}
	</div>
</div>

<div class="form-field">
	<label for="products_tax_id">{$lang.taxes}:</label>
	<div class="select-field">
		{foreach from=$taxes item="tax"}
			<input type="checkbox"	name="shipping_data[tax_ids][{$tax.tax_id}]" id="shippings_taxes_{$tax.tax_id}" {if $tax.tax_id|in_array:$shipping.tax_ids}checked="checked"{/if} class="checkbox" value="{$tax.tax_id}" />
			<label for="shippings_taxes_{$tax.tax_id}">{$tax.tax}</label>
		{foreachelse}
			&ndash;
		{/foreach}
	</div>
</div>

{hook name="shippings:update"}
{/hook}
<div class="form-field">
	<label>{$lang.usergroups}:</label>
	<div class="select-field">
		{include file="common_templates/select_usergroups.tpl" id="ship_data_usergroup_id" name="shipping_data[usergroup_ids]" usergroups=$usergroups usergroup_ids=$shipping.usergroup_ids input_extra="" list_mode=false}
	</div>
</div>
{include file="views/localizations/components/select.tpl" data_name="shipping_data[localization]" data_from=$shipping.localization}

{include file="common_templates/select_status.tpl" input_name="shipping_data[status]" id="shipping_data" obj=$shipping}

<div class="form-field">
    <label for="products_tax_id">{$lang.self_service}:</label>
    <div class="select-field">
            <input type="hidden"	name="shipping_data[self_service]" id="self_service_hidden" {if $shipping.self_service}checked="checked"{/if} class="checkbox" value="0" />
            <input type="checkbox"	name="shipping_data[self_service]" id="self_service"        {if $shipping.self_service}checked="checked"{/if} class="checkbox" value="1" />
            <label for="self_service">{$lang.self_service}</label>
    </div>
</div>
</fieldset>

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[shippings.update_shipping]"}
</div>

</form>

{if $mode == "update"}
	<!--content_general--></div>

	<div id="content_shipping_charges">
	{include file="views/shippings/components/rates.tpl"}
	<!--content_shipping_charges--></div>

	{/capture}
	{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}
{/if}
{/capture}

{if $mode == "add"}
	{assign var="title" value=$lang.new_shipping_method}
{else}
	{assign var="title" value="`$lang.editing_shipping_method`: `$shipping.shipping`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
