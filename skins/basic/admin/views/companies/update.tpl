{* $Id: update.tpl 9000 2010-03-24 12:17:56Z 2tl $ *}

{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
{assign var="lang_available_for_vendor_supplier" value=$lang.available_for_vendor}
{assign var="lang_new_vendor_supplier" value=$lang.new_vendor}
{assign var="lang_editing_vendor_supplier" value=$lang.editing_vendor}
{else}
{assign var="lang_available_for_vendor_supplier" value=$lang.available_for_supplier}
{assign var="lang_new_vendor_supplier" value=$lang.new_supplier}
{assign var="lang_editing_vendor_supplier" value=$lang.editing_supplier}
{/if}


{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{include file="views/profiles/components/profiles_scripts.tpl"}

{capture name="mainbox"}

{capture name="tabsbox"}
{** /Item menu section **}

<form action="{""|fn_url}" method="post" name="logotypes_form" enctype="multipart/form-data"> {* company update form *}
{* class="cm-form-highlight"*}
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="selected_section" id="selected_section" value="{$smarty.request.selected_section}" />
<input type="hidden" name="company_id" value="{$company_data.company_id}" />



{** General info section **}
<div id="content_detailed"> {* content detailed *}

<fieldset>

{include file="common_templates/subheader.tpl" title=$lang.information}

{hook name="companies:general_information"}
<div class="form-field">
	<label for="company_description_company" class="cm-required">{$lang.company}:</label>
	<input type="text" name="company_data[company]" id="company_description_company" size="32" value="{$company_data.company}" class="input-text" />
</div>

{include file="common_templates/select_status.tpl" input_name="company_data[status]" id="company_data" obj=$company_data}

{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
	<div class="form-field">
		<label for="company_description_vendor_admin" class="cm-required">{$lang.create_administrator_account}:</label>
		<input type="checkbox" class="checkbox" checked="checked" name="company_data[vendor_admin]" value="Y">
	</div>
	{if $settings.General.use_email_as_login == 'N'}
	<div class="form-field">
		<label for="company_description_username">{$lang.account_name}:</label>
		<input type="text" name="company_data[admin_username]" id="company_description_admin_username" size="32" value="{$company_data.admin_username}" class="input-text" />
	</div>
	{/if}	
{/if}
{/hook}

{include file="common_templates/subheader.tpl" title=$lang.contact_information}

<div class="form-field">
	<label for="company_description_email" class="cm-required cm-email">{$lang.email}:</label>
	<input type="text" name="company_data[email]" id="company_description_email" size="32" value="{$company_data.email}" class="input-text" />
</div>

<div class="form-field">
	<label for="company_description_phone" class="cm-required">{$lang.phone}:</label>
	<input type="text" name="company_data[phone]" id="company_description_phone" size="32" value="{$company_data.phone}" class="input-text" />
</div>

<div class="form-field">
	<label for="company_description_url" class="cm-required">{$lang.url}:</label>
	<input type="text" name="company_data[url]" id="company_description_url" size="32" value="{$company_data.url}" class="input-text" />
</div>

<div class="form-field">
	<label for="company_description_fax">{$lang.fax}:</label>
	<input type="text" name="company_data[fax]" id="company_description_fax" size="32" value="{$company_data.fax}" class="input-text" />
</div>

{include file="common_templates/subheader.tpl" title=$lang.shipping_address}

<div class="form-field">
	<label for="company_address_address" class="cm-required">{$lang.address}:</label>
	<input type="text" name="company_data[address]" id="company_address_address" size="32" value="{$company_data.address}" class="input-text" />
</div>

<div class="form-field">
	<label for="company_address_address_2">{$lang.address_2}:</label>
	<input type="text" name="company_data[address_2]" id="company_address_address_2" size="32" value="{$company_data.address_2}" class="input-text" />
</div>

<div class="form-field">
	<label for="company_address_city" class="cm-required">{$lang.city}:</label>
	<input type="text" name="company_data[city]" id="company_address_city" size="32" value="{$company_data.city}" class="input-text" />
</div>

<div class="form-field">
	<label for="company_address_country" class="cm-required cm-country cm-location-shipping">{$lang.country}:</label>
	{assign var="_country" value=$company_data.country|default:$settings.General.default_country}
	<select id="company_address_country" name="company_data[country]">
		<option value="">- {$lang.select_country} -</option>
		{foreach from=$countries item=country}
		<option {if $_country == $country.code}selected="selected"{/if} value="{$country.code}">{$country.country}</option>
		{/foreach}
	</select>
</div>

<div class="form-field">
	{assign var="country_code" value=$company_data.country|default:$settings.General.default_country}
	{assign var="state_code" value=$company_data.state|default:$settings.General.default_state}
	<label for="company_address_state" class="cm-required cm-state cm-location-shipping">{$lang.state}:</label>
	<select id="company_address_state" name="company_data[state]" {if !$states.$country_code}class="hidden"{/if}>
		<option value="">- {$lang.select_state} -</option>
		{* Initializing default states *}
		{if $states.$country_code}
			{foreach from=$states.$country_code item=state}
				<option {if $state_code == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
			{/foreach}
		{/if}
	</select>
	<input type="text" id="company_address_state_d" name="company_data[state]" size="32" maxlength="64" value="{$company_data.state}" {if $states.$country_code}disabled="disabled"{/if} class="input-text {if $states.$country_code}hidden{/if} cm-skip-avail-switch" />
</div>

<div class="form-field">
	<label for="company_address_zipcode" class="cm-required">{$lang.zip_postal_code}:</label>
	<input type="text" name="company_data[zipcode]" id="company_address_zipcode" size="32" value="{$company_data.zipcode}" class="input-text" />
</div>

<span>&nbsp;
<script type="text/javascript">
//<![CDATA[
default_state['shipping'] = '{$company_data.state|default:$settings.General.default_state|escape:javascript}';
//]]>
</script>
</span>

</fieldset>
</div> {* /content detailed *}
{** /General info section **}



{** Company description section **}
<div id="content_description" class="hidden"> {* content description *}
<fieldset>
<div class="form-field">
	<label for="company_description">{$lang.description}:</label>
	<textarea id="company_description" name="company_data[company_description]" cols="55" rows="8" class="input-textarea-long">{$company_data.company_description}</textarea>
	{include file="common_templates/wysiwyg.tpl" id="company_description"}
</div>
</fieldset>
</div> {* /content description *}
{** /Company description section **}



{** Company logos section **}
<div id="content_logos" class="hidden"> {* content logos *}
&nbsp;
{foreach from=$manifest_definition key="a" item="m" name="fel"}
{assign var="sa" value="skin_name_`$m.skin`"}
<p>{$lang[$m.text]}</p>
<div class="clear">
	<div class="float-left">
		{include file="common_templates/fileuploader.tpl" var_name="logotypes[`$a`]"}
	</div>
	<div class="float-left attach-images-alt logo-image">
		<img class="solid-border" src="{if $manifests[$m.skin][$m.name].vendor}{$config.images_path}{$manifests[$m.skin][$m.name].filename}{else}{$config.current_path}/skins/{$settings.$sa}/{$m.path}/images/{$manifests[$m.skin][$m.name].filename}{/if}" width="{$manifests[$m.skin][$m.name].width}" height="{$manifests[$m.skin][$m.name].height}" />
		<label for="alt_text_{$a}">{$lang.alt_text}:</label>
		<input type="text" class="input-text cm-image-field" id="alt_text_{$a}" name="logo_alt[{$a}]" value="{$manifests[$m.skin][$m.name].alt}" />
	</div>
</div>
{if !$smarty.foreach.fel.last}
<hr />
{/if}
{/foreach}
</div> {* /content logos *}
{** /Company logos section **}



{** Company categories section **}
<div id="content_categories" class="hidden"> {* content categories *}
	{include file="pickers/categories_picker.tpl" multiple=true input_name="company_data[categories]" item_ids=$company_data.categories data_id="category_ids" no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.categories}
</div> {* /content categories *}
{** /Company categories section **}


{if !"COMPANY_ID"|defined}
{** Shipping methods section **}
<div id="content_shipping_methods" class="hidden"> {* shipping_methods *}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="50%">{$lang.shipping_methods}</th>
			<th class="center">{$lang_available_for_vendor_supplier}</th>
		</tr>
		{if $company_data.shippings}
			{assign var="shippings_ids" value=","|explode:$company_data.shippings}
		{/if}
		{foreach from=$shippings item="shipping" key="shipping_id"}
		<tr {cycle values="class=\"table-row\", "}>
			<td><a href="{"shippings.update?shipping_id=`$shipping_id`"|fn_url}">{$shipping}</a></td>
			<td class="center">
				<input type="checkbox" class="checkbox"{if !$company_data.company_id || $shipping_id|in_array:$shippings_ids} checked="checked"{/if} name="company_data[shippings][]" value="{$shipping_id}">
			</td>
		</tr>
		{foreachelse}
		<tr class="no-items">
			<td colspan="2"><p>{$lang.no_items}</p></td>
		</tr>
		{/foreach}
		</table>
</div> {* /content shipping_methods *}
{** /Shipping methods section **}
{/if}

{hook name="companies:detailed_content"}{/hook}

{** Form submit section **}

<div class="buttons-container cm-toggle-button buttons-bg">
	{if $mode == "add"}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[companies.add]"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[companies.update]"}
	{/if}
</div>
{** /Form submit section **}

</form> {* /product update form *}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox group_name=$controller active_tab=$smarty.request.selected_section track=true}

{/capture}

{if $mode == "add"}
	{include file="common_templates/mainbox.tpl" title=$lang_new_vendor_supplier content=$smarty.capture.mainbox}
{else}
	{include file="common_templates/mainbox.tpl" title="`$lang_editing_vendor_supplier`:&nbsp;`$company_data.company`"|unescape content=$smarty.capture.mainbox select_languages=true}
{/if}
