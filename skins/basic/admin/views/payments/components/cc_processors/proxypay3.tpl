{* $Id: proxypay3.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

{assign var="url" value="`$config.http_location`"}
<p>{$lang.text_proxypay_notice|replace:"[cart_url]":$url}</p>
<hr />

<div class="form-field">
	<label for="merchantid">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchantid]" id="merchantid" value="{$processor_params.merchantid}" class="input-text" />
</div>

<div class="form-field">
	<label for="url">{$lang.payment} {$lang.url}:</label>
	<input type="text" name="payment_data[processor_params][url]" id="url" value="{$processor_params.url|default:"eptest.eurocommerce.gr/proxypay/apacs"}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="details">{$lang.payment_details}:</label>
	<input type="text" name="payment_data[processor_params][details]" id="details" value="{$processor_params.details}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="0840" {if $processor_params.currency == "0840"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="0978" {if $processor_params.currency == "0978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="0300" {if $processor_params.currency == "0300"}selected="selected"{/if}>{$lang.currency_9}</option>
	</select>
</div>
