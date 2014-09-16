{* $Id: totalwebpp.tpl 8930 2010-02-25 13:05:59Z 2tl $ *}

{assign var="url" value=$config.https_location}
<p>{$lang.text_totalweb_notice|replace:"[cart_url]":$url}</p>
<hr />

<div class="form-field">
	<label for="vendor">{$lang.vendor_name}:</label>
	<input type="text" id="vendor" name="payment_data[processor_params][vendor]" value="{$processor_params.vendor}" class="input-text" size="60" />
</div>
<div class="form-field">
	<label for="password">{$lang.encryption} {$lang.password}:</label>
	<input type="password" id="password" name="payment_data[processor_params][password]" value="{$processor_params.password}" class="input-text" size="60" />
</div>
<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>
<div class="form-field">
	<label for="testmode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][testmode]" id="testmode">
		<option value="Y" {if $processor_params.testmode == "Y"}selected="selected"{/if}>{$lang.test}</option>
		<option value="N" {if $processor_params.testmode == "N"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="GBP" {if $processor_params.currency == "826"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="EUR" {if $processor_params.currency == "978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="USD" {if $processor_params.currency == "840"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="AUD" {if $processor_params.currency == "036"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
	</select>
</div>
