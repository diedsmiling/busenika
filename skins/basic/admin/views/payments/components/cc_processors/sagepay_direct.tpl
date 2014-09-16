{* $Id: sagepay_direct.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

<p>{$lang.text_sagepay_dir_notice}</p>
<hr />

<div class="form-field">
	<label for="vendor">{$lang.vendor_name}:</label>
	<input type="text" name="payment_data[processor_params][vendor]" id="vendor" value="{$processor_params.vendor}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="transaction_type">{$lang.transaction_type}:</label>
	<select name="payment_data[processor_params][transaction_type]" id="transaction_type">
		<option value="AUTHENTICATE" {if $processor_params.transaction_type == "AUTHENTICATE"}selected="selected"{/if}>AUTHENTICATE</option>
		<option value="DEFERRED" {if $processor_params.transaction_type == "DEFERRED"}selected="selected"{/if}>DEFERRED</option>
		<option value="PAYMENT" {if $processor_params.transaction_type == "PAYMENT"}selected="selected"{/if}>PAYMENT</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
	</select>
</div>

<div class="form-field">
	<label for="testmode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][testmode]" id="testmode">
		<option value="Y" {if $processor_params.testmode == "Y"}selected="selected"{/if}>{$lang.test}</option>
		<option value="N" {if $processor_params.testmode == "N"}selected="selected"{/if}>{$lang.live}</option>
		<option value="S" {if $processor_params.testmode == "S"}selected="selected"{/if}>DEV</option>
	</select>
</div>
