{* $Id: products_m_update_feature.tpl 8855 2010-02-16 10:25:10Z alexions $ *}

{if $feature.prefix}<strong>{$feature.prefix}</strong>{/if}
{if $feature.feature_type == "S" || $feature.feature_type == "N" || $feature.feature_type == "E"}
	<select name="{$data_name}[product_features][{$feature.feature_id}]" {if $over}id="field_{$field}__{$feature.feature_id}_" disabled="disabled" class="elm-disabled"{/if}>
		<option value="">-{$lang.none}-</option>
		{foreach from=$feature.variants item="var"}
		<option value="{$var.variant_id}" {if $var.variant_id == $feature.variant_id}selected="selected"{/if}>{$var.variant}</option>
		{/foreach}
	</select>
{elseif $feature.feature_type == "M"}
		<input type="hidden" name="{$data_name}[product_features][{$feature.feature_id}]" value="" {if $over}id="field_{$field}__{$feature.feature_id}_" disabled="disabled"{/if} />
		{foreach from=$feature.variants item="var"}
			<div class="select-field">
				<input type="checkbox" name="{$data_name}[product_features][{$feature.feature_id}][{$var.variant_id}]" value="{$var.variant_id}" class="checkbox{if $over} elm-disabled{/if}" {if $over}id="field_{$field}__{$feature.feature_id}_" disabled="disabled"{/if} {if $var.selected}checked="checked"{/if} />
				<label>{$var.variant}</label>
			</div>
		{/foreach}
{elseif $feature.feature_type == "C"}
	<input type="hidden" name="{$data_name}[product_features][{$feature.feature_id}]" value="N" {if $over}disabled="disabled" id="field_{$field}__{$feature.feature_id}_copy"{/if} />
	<input type="checkbox" name="{$data_name}[product_features][{$feature.feature_id}]" value="Y" {if $over}id="field_{$field}__{$feature.feature_id}_" disabled="disabled" class="elm-disabled"{/if} {if $feature.value == "Y"}checked="checked"{/if} />
{elseif $feature.feature_type == "D"}
	{if $over}
		{assign var="date_id" value="field_`$field`__`$feature.feature_id`_"}
		{assign var="date_extra" value=" disabled=\"disabled\""}
		{assign var="d_meta" value="input-text-disabled"}
	{else}
		{assign var="date_id" value="date_`$pid``$feature.feature_id`"}
		{assign var="date_extra" value=""}
		{assign var="d_meta" value=""}
	{/if}
	{$feature.value}{include file="common_templates/calendar.tpl" date_id=$date_id date_name="`$data_name`[product_features][`$feature.feature_id`]" date_val=$feature.value_int start_year=$settings.Company.company_start_year extra=$date_extra date_meta=$d_meta}
{else}
	<input type="text" name="{$data_name}[product_features][{$feature.feature_id}]" value="{if $feature.feature_type == "O"}{$feature.value_int}{else}{$feature.value}{/if}" {if $feature.feature_type == "O"}onkeyup="javascript: this.value = this.value.replace(/\D+/g, '');"{/if} {if $over} id="field_{$field}__{$feature.feature_id}_" class="input-text input-text-disabled" disabled="disabled"{else}class="input-text"{/if} />
{/if}
{if $feature.suffix}<strong>{$feature.suffix}</strong>{/if}
<input type="hidden" name="{$data_name}[active_features][]" value="{$feature.feature_id}" />
