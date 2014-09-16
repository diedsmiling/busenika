{* $Id: update.tpl 10043 2010-07-13 07:59:56Z angel $ *}

<script type="text/javascript">
//<![CDATA[
lang.no_products_defined = '{$lang.text_no_products_defined|escape:"javascript"}';
{literal}
function fn_check_amount()
{
	var max = parseInt((parseFloat(max_amount) / parseFloat(currencies.secondary.coefficient))*100)/100;
	var min = parseInt((parseFloat(min_amount) / parseFloat(currencies.secondary.coefficient))*100)/100;

	is_check = ($('input:checked[name="gift_cert_data[amount_type]"]').val() == 'I') ? true : false;
	if(is_check && $('#gift_cert_amount')){
		var amount = parseFloat($('#gift_cert_amount').val());
		if(amount < min || isNaN(amount) || amount > max){
			$('#gift_cert_amount').removeClass('input-text');
			$('#gift_cert_amount').addClass('failed-field');
			alert(amount_alert);
		}else{
			$('#gift_cert_amount').removeClass('failed-field');
			$('#gift_cert_amount').addClass('input-text');
		}
		return ((amount <= max) && (amount >= min) && !isNaN(amount)) ? true : false;
	}
	return true;
}
function fn_giftcert_form_elements_disable(dsbl, enbl)
{
	if(!$('form[name="gift_certificates_form"]').get(0)){
		return false;
	}
	$(':input', '#'+dsbl).attr('disabled', 'disabled');
	$(':input', '#'+dsbl).addClass('disabled');
	$(':input', '#'+enbl).removeAttr('disabled');
	$(':input', '#'+enbl).removeClass('disabled');
}
{/literal}
//]]>
</script>
{assign var="min_amount" value=$addons.gift_certificates.min_amount|escape:javascript|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:$currencies.$secondary_currency.decimals_separator:$currencies.$secondary_currency.thousands_separator:$currencies.$secondary_currency.coefficient}
{assign var="max_amount" value=$addons.gift_certificates.max_amount|escape:javascript|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:$currencies.$secondary_currency.decimals_separator:$currencies.$secondary_currency.thousands_separator:$currencies.$secondary_currency.coefficient}

{include file="views/profiles/components/profiles_scripts.tpl"}

<script type="text/javascript">
//<![CDATA[
var default_state = {$ldelim}'billing':'{$gift_cert_data.state|default:$settings.General.default_state|escape:javascript}'{$rdelim};
var text_no_products = '{$lang.text_no_products_defined}';
var max_amount = '{$addons.gift_certificates.max_amount|escape:javascript}';
var min_amount = '{$addons.gift_certificates.min_amount|escape:javascript}';
var amount_alert = '{$lang.text_gift_cert_amount_higher|escape:javascript} {$max_amount|escape:javascript} {$lang.text_gift_cert_amount_less|escape:javascript} {$min_amount|escape:javascript}';
//]]>
</script>
{script src="js/profiles_scripts.js"}

{** Gift certificates section **}

<div class="clear">
	<div class="float-left">
		{$lang.text_mandatory_fields}
		<p>{$lang.text_gift_cert_amount_higher}&nbsp;{include file="common_templates/price.tpl" value=$addons.gift_certificates.max_amount}&nbsp;{$lang.text_gift_cert_amount_less}&nbsp;{include file="common_templates/price.tpl" value=$addons.gift_certificates.min_amount}</p>
	</div>
	{include file="addons/gift_certificates/views/gift_certificates/components/gift_certificates_verify.tpl"}
</div>

<form {if $settings.DHTML.ajax_add_to_cart == "Y" && !$no_ajax && $mode != "update"}class="cm-ajax" {/if}action="{""|fn_url}" method="post" target="_self" name="gift_certificates_form">
{if $mode == "update"}
<input type="hidden" name="gift_cert_id" value="{$gift_cert_id}" />
<input type="hidden" name="type" value="{$type}" />
{/if}

<div class="form-field">
	<label for="gift_cert_recipient" class="cm-required">{$lang.gift_cert_to}:</label>
	<input type="text" id="gift_cert_recipient" name="gift_cert_data[recipient]" class="input-text" size="50" maxlength="255" value="{$gift_cert_data.recipient}" />
</div>

<div class="form-field">
	<label for="gift_cert_sender" class="cm-required">{$lang.gift_cert_from}:</label>
	<input type="text" id="gift_cert_sender" name="gift_cert_data[sender]" class="input-text" size="50" maxlength="255" value="{$gift_cert_data.sender}" />
</div>

<div class="form-field">
	<label for="radio_at" class="cm-required">{$lang.amount}:</label>

	<input type="radio" name="gift_cert_data[amount_type]" value="I" id="radio_at" onclick="fn_giftcert_form_elements_disable('select_block', 'input_block');" {if $mode == "add" || $gift_cert_data.amount_type == "I"}checked="checked"{/if} class="radio{if !$amount_variants} hidden{/if}" />
	<span id="input_block">
		{if $currencies.$secondary_currency.after != "Y"}<span class="valign">{$currencies.$secondary_currency.symbol|unescape}</span>{/if}
		<input type="text" id="gift_cert_amount" name="gift_cert_data[amount]" class="valign input-text-short inp-el" size="5" value="{if $gift_cert_data && $gift_cert_data.amount_type == "I"}{$gift_cert_data.amount|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:".":"":$currencies.$secondary_currency.coefficient}{else}{$addons.gift_certificates.min_amount|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:".":"":$currencies.$secondary_currency.coefficient}{/if}" />
		{if $currencies.$secondary_currency.after == "Y"}<span class="valign">{$currencies.$secondary_currency.symbol|unescape}</span>{/if}
	</span>

	{if $amount_variants}

	&nbsp;&nbsp;&nbsp;<input type="radio" name="gift_cert_data[amount_type]" value="S" id="radio_at2" onclick="fn_giftcert_form_elements_disable('input_block', 'select_block');" {if $gift_cert_data.amount_type == "S"}checked="checked"{/if} class="radio" />
	<span id="select_block">
		<select	id="gift_cert_amount2" name="gift_cert_data[amount]" class="valign sel-el" >
			{foreach from=$amount_variants item="av"}
			{if $av == $gift_cert_data.amount}{assign var="av_isset" value="Y"}{/if}
			{if !$av_isset && $mode == "update" && $av > $gift_cert_data.amount}
				{assign var="av_isset" value="Y"}
				
				<option value="{$gift_cert_data.amount|fn_format_price}" {if $gift_cert_data.amount_type == "S"}selected="selected"{/if}>{include file="common_templates/price.tpl" value=$gift_cert_data.amount}</option>
				{/if}
				
				<option value="{$av|fn_format_price}" {if ($av == $gift_cert_data.amount && $gift_cert_data.amount_type == "S" && $gift_cert_data) || (!$gift_cert_data && $addons.gift_certificates.min_amount == $av)}selected="selected"{/if}>{include file="common_templates/price.tpl" value=$av}</option>
			{/foreach}
		</select>
	</span>
	{/if}
</div>

<div class="form-field">
	<label for="gift_cert_message">{$lang.message}:</label>
	<textarea id="gift_cert_message" name="gift_cert_data[message]" cols="72" rows="5" class="input-textarea" {if $is_text == "Y"}readonly="readonly"{/if}>{$gift_cert_data.message}</textarea>
</div>

<h5 class="info-field-title">
	<input type="radio" name="gift_cert_data[send_via]" value="E" onclick="fn_giftcert_form_elements_disable('post_block', 'email_block');" {if $mode == "add" || $gift_cert_data.send_via == "E"}checked="checked"{/if} class="radio" id="send_via_email" /><label for="send_via_email" class="valign">{$lang.send_via_email}</label>
</h5>
<div class="info-field-body" id="email_block">
	<div class="form-field">
		<label for="gift_cert_email" class="cm-required cm-email">{$lang.email}:</label>
		<input type="text" id="gift_cert_email" name="gift_cert_data[email]" class="input-text" size="50" maxlength="128" value="{$gift_cert_data.email}" />
	</div>
</div>

<h5 class="info-field-title">
	<input type="radio" name="gift_cert_data[send_via]" value="P" onclick="fn_giftcert_form_elements_disable('email_block', 'post_block');" {if $gift_cert_data.send_via == "P"}checked="checked"{/if} class="valign radio" id="send_via_post" /><label for="send_via_post" class="radio">{$lang.send_via_postal_mail}</label>
</h5>
<div class="info-field-body" id="post_block">

	<div class="form-field">
		<label for="gift_cert_address" class="cm-required">{$lang.address}:</label>
		<input type="text" id="gift_cert_address" name="gift_cert_data[address]" class="input-text" size="50" value="{$gift_cert_data.address}" />
	</div>

	<div class="form-field">
		<label for="gift_cert_address_2">{$lang.address_2}:</label>
		<input type="text" id="gift_cert_address_2" name="gift_cert_data[address_2]" class="input-text" size="50" value="{$gift_cert_data.address_2}" />
	</div>

	<div class="form-field">
		<label for="gift_cert_city" class="cm-required">{$lang.city}:</label>
		<input type="text" id="gift_cert_city" name="gift_cert_data[city]" class="input-text" size="50" value="{$gift_cert_data.city}" />
	</div>

	<div class="form-field">
		<label for="gift_cert_country" class="cm-required cm-country cm-location-billing">{$lang.country}:</label>
		{assign var="_country" value=$gift_cert_data.country|default:$settings.General.default_country}
		<select id="gift_cert_country" name="gift_cert_data[country]" class="cm-location-billing" >
			<option value="">- {$lang.select_country} -</option>
			{foreach from=$countries item=country}
			<option {if $_country == $country.code}selected="selected"{/if} value="{$country.code}">{$country.country}</option>
			{/foreach}
		</select>
	</div>

	<div class="form-field">
		<label for="gift_cert_state" class="cm-required cm-state cm-location-billing">{$lang.state}:</label>
		<input type="text" id="gift_cert_state_d" name="gift_cert_data[state]" class="input-text hidden" size="50" maxlength="64" value="{$value}" disabled="disabled"  />
		<select id="gift_cert_state" name="gift_cert_data[state]">
			<option value="">- {$lang.select_state} -</option>
			{if $states}
				{foreach from=$states.$_country item=state}
					<option value="{$state.code}">{$state.state}</option>
				{/foreach}
			{/if}
		</select>
	</div>

	<div class="form-field">
		<label for="gift_cert_zipcode" class="cm-required">{$lang.zip_postal_code}:</label>
		<input type="text" id="gift_cert_zipcode" name="gift_cert_data[zipcode]" class="input-text" size="50" value="{$gift_cert_data.zipcode}" />
	</div>

	<div class="form-field">
		<label for="gift_cert_phone">{$lang.phone}:</label>
		<input type="text" id="gift_cert_phone" name="gift_cert_data[phone]" class="input-text" size="50" value="{$gift_cert_data.phone}" />
	</div>

</div>

{if $addons.gift_certificates.free_products_allow == "Y"}
<h5 class="info-field-title">{$lang.free_products}</h5>
<div class="info-field-body product-options">
	{include file="pickers/products_picker.tpl" data_id="free_products" item_ids=$gift_cert_data.products input_name="gift_cert_data[products]" type="table" no_item_text=$lang.text_no_products_defined holder_name="gift_certificates"}
</div>
{/if}

<div class="buttons-container">

{if $mode == "add"}
<input type="hidden" name="result_ids" value="cart_status" />
	{hook name="gift_certificates:buttons"}
		{include file="buttons/add_to_cart.tpl" but_name="dispatch[gift_certificates.add]" but_onclick="return fn_check_amount();" but_role="action"}
	{/hook}
{else}
	{include file="buttons/save.tpl" but_name="dispatch[gift_certificates.update]" but_onclick="return fn_check_amount();"}
{/if}
{if $templates}
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	{if $templates|@sizeof > 1}
	<select id="gift_cert_template" name="gift_cert_data[template]">
		{foreach from=$templates item="name" key="file"}
		<option value="{$file}">{$name}</option>
		{/foreach}
	</select>
	{else}
		{foreach from=$templates item="name" key="file"}
		<input id="gift_cert_template" type="hidden" name="gift_cert_data[template]" value="{$file}" />
		{/foreach}
	{/if}
	{include file="buttons/button.tpl" but_text=$lang.preview but_name="dispatch[gift_certificates.preview]" but_meta="cm-new-window" but_role="text"}
{/if}
</div>

</form>

<script type="text/javascript">
//<![CDATA[
	fn_giftcert_form_elements_disable({if $mode == "add" || $gift_cert_data.amount_type == "I"}'select_block', 'input_block'{else}'input_block', 'select_block'{/if});
	fn_giftcert_form_elements_disable({if $mode == "add" || $gift_cert_data.send_via == "E"}'post_block', 'email_block'{else}'email_block', 'post_block'{/if});
//]]>
</script>
{** / Gift certificates section **}

{capture name="mainbox_title"}{if $mode == "add"}{$lang.purchase_gift_certificate}{else}{$lang.gift_certificate}{/if}{/capture}
