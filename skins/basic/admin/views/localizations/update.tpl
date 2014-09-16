{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}

{*include file="common_templates/prev_next_links.tpl"*}

{if $prev_id || $next_id}<br />{/if}

{capture name="tabsbox"}

<form action="{""|fn_url}" method="post" class="cm-form-highlight" name="localization_update_form">
<input type="hidden" name="localization_id" value="{$localization.data.localization_id}" />

<div id="content_general">
<fieldset>
	<div class="form-field">
		<label for="localization_name" class="cm-required">{$lang.name}:</label>
		<input type="text" name="localization_data[localization]" id="localization_name" size="25" value="{$localization.data.localization}" class="input-text-large main-input" />
	</div>

	<div class="form-field">
		<label for="is_default">{$lang.default}:</label>

		{if $localization.data.is_default == "Y" || !$default_localization}
		<input type="hidden" name="localization_data[is_default]" value="N" />
		<input type="checkbox" name="localization_data[is_default]" class="checkbox" id="is_default" value="Y" {if $localization.data.is_default == "Y"}checked="checked"{/if} />
		{else}
		<a href="{"localizations.update?localization_id=`$default_localization.localization_id`"|fn_url}">{$default_localization.localization}</a>
		{/if}
	</div>
	

	<div class="form-field">
		<label for="sw_weight_settings">{$lang.use_custom_weight_settings}:</label>
		<input type="hidden" name="localization_data[custom_weight_settings]" value="N" />
		<input id="sw_weight_settings" type="checkbox" name="localization_data[custom_weight_settings]" class="checkbox cm-combination" value="Y" {if $localization.data.custom_weight_settings == "Y"}checked="checked"{/if} />
	</div>
	
	<div id="weight_settings" {if $localization.data.custom_weight_settings != "Y"}class="hidden"{/if}>
		<div class="form-field">
			<label for="weight_symbol">{$lang.weight_symbol}:</label>
			<input type="text" id="weight_symbol" name="localization_data[weight_symbol]" value="{$localization.data.weight_symbol}" class="input-text" />
		</div>
		<div class="form-field">
			<label for="weight_unit">{$lang.grams_in_the_unit_of_weight}:</label>
			<input type="text" id="weight_unit" name="localization_data[weight_unit]" value="{$localization.data.weight_unit}" class="input-text" />
		</div>
	</div>
</fieldset>
</div>

<div id="content_details">
	<table cellpadding="5" cellspacing="0" border="0" width="100%">
	<tr>
		<td><div class="margin-top center strong">{$lang.selected_items}</div></td>
		<td>&nbsp;</td>
		<td><div class="margin-top center strong">{$lang.available_items}</div></td>
	</tr>
	</table>
	
	{* Countries list *}
	
	{include file="common_templates/double_selectboxes.tpl"
		title=$lang.countries
		first_name="localization_data[countries]"
		first_data=$localization.countries
		second_name="all_countries"
		second_data=$localization_countries}
	
	{* Currencies list *}
	
	{include file="common_templates/double_selectboxes.tpl"
		title=$lang.currencies
		first_name="localization_data[currencies]"
		first_data=$localization.currencies
		second_name="all_currencies"
		second_data=$localization_currencies}
	
	{* Languages list *}
	
	{include file="common_templates/double_selectboxes.tpl"
		title=$lang.languages
		first_name="localization_data[languages]"
		first_data=$localization.languages
		second_name="all_languages"
		second_data=$localization_languages}
</div>

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[localizations.update]"}
</div>

</form>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}

{/capture}
{if $mode == "add"}
	{assign var="title" value=$lang.new_localization}
{else}
	{assign var="title" value="`$lang.editing_localization`: `$localization.data.localization`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}