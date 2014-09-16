{* $Id: update.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}

{capture name="mainbox"}

{include file="common_templates/subheader.tpl" title=$lang.general}

{if !"COMPANY_ID"|defined}
<form action="{""|fn_url}" method="post" name="destinations_form" class="cm-form-highlight">
{/if}
<input type="hidden" name="destination_id" value="{$smarty.request.destination_id}" />

<div class="form-field">
	<label for="destination" class="cm-required">{$lang.name}:</label>
	<input type="text" name="destination_data[destination]" id="destination" size="25" value="{$destination.destination}" class="input-text-large main-input" />
</div>

{include file="views/localizations/components/select.tpl" data_name="destination_data[localization]" data_from=$destination.localization}

{include file="common_templates/select_status.tpl" input_name="destination_data[status]" id="destination_data" obj=$destination}

{notes}
	{$lang.multiple_selectbox_notice}
{/notes}

{* Countries list *}
{include file="common_templates/subheader.tpl" title=$lang.countries}
<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td width="48%">
		<label for="dest_countries" class="hidden cm-all"></label>
		<select name="destination_data[countries][]" id="dest_countries" size="10" value="" multiple="multiple" class="input-text cm-expanded">
			{foreach from=$destination_data.countries item=country}
			<option	value="{$country.code}">{$country.country}</option>
			{/foreach}
		</select>
		</td>
	<td class="center valign" width="4%">
		<p><img src="{$images_dir}/icons/to_left_icon.gif" width="11" height="11" onclick="$('#all_countries').moveOptions('#dest_countries');" class="hand" /></p>
		<p><img src="{$images_dir}/icons/to_right_icon.gif" width="11" height="11" onclick="$('#dest_countries').moveOptions('#all_countries');" class="hand" /></p>
	</td>
	<td width="48%">
		<select name="all_countries" id="all_countries" size="10" value="" multiple="multiple" class="input-text cm-expanded">
			{foreach from=$countries item=country}
			<option	value="{$country.code}">{$country.country}</option>
			{/foreach}
		</select>
		</td>
</tr>
</table>

{* States list *}
{include file="common_templates/subheader.tpl" title=$lang.states}
<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td width="48%">
		<label for="destination_states" class="hidden cm-all"></label>
		<select name="destination_data[states][]" id="destination_states" size="10" value="" multiple="multiple" class="input-text cm-expanded">
			{foreach from=$destination_data.states item=state}
			<option	value="{$state.state_id}">{$state.country}: {$state.state}</option>
			{/foreach}
		</select>
		</td>
	<td class="center valign" width="4%">
		<p><img src="{$images_dir}/icons/to_left_icon.gif" width="11" height="11" onclick="$('#all_states').moveOptions('#destination_states');" class="hand" /></p>
		<p><img src="{$images_dir}/icons/to_right_icon.gif" width="11" height="11" onclick="$('#destination_states').moveOptions('#all_states');" class="hand" /></p>
	</td>

	<td width="48%">
		<select name="all_states" id="all_states" size="10" value="" multiple="multiple" class="input-text cm-expanded">
			{foreach from=$states item=state}
			<option	value="{$state.state_id}">{$state.country}: {$state.state}</option>
			{/foreach}
		</select>
	</td>
</tr>
</table>

{* Zipcodes list *}
{include file="common_templates/subheader.tpl" title=$lang.zipcodes}
<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td width="48%">
		<textarea name="destination_data[zipcodes]" id="destination_zipcodes" rows="8" class="input-text cm-expanded">{$destination_data.zipcodes}</textarea></td>
	<td>&nbsp;</td>

	<td width="48%">{$lang.text_zipcodes_wildcards}</td>
</tr>
</table>

{* Cities list *}
{include file="common_templates/subheader.tpl" title=$lang.cities}
<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td width="48%">
		<textarea name="destination_data[cities]" id="destination_cities" rows="8" class="input-text cm-expanded">{$destination_data.cities}</textarea></td>
	<td>&nbsp;</td>

	<td width="48%">{$lang.text_cities_wildcards}</td>
</tr>
</table>

{* Addresses list *}
{include file="common_templates/subheader.tpl" title=$lang.addresses}
<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td width="48%">
		<textarea name="destination_data[addresses]" id="destination_cities" rows="8" class="input-text cm-expanded">{$destination_data.addresses}</textarea></td>
	<td>&nbsp;</td>

	<td width="48%">{$lang.text_addresses_wildcards}</td>
</tr>
</table>

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[destinations.update]"}
</div>

{if !"COMPANY_ID"|defined}
</form>
{/if}
{/capture}

{if $mode == "add"}
	{assign var="title" value=$lang.new_location}
{else}
	{assign var="title" value="`$lang.editing_location`:&nbsp;`$destination.destination`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
