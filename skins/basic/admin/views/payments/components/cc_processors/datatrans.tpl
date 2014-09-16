{* $Id: datatrans.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="post_url" value="payment_notification.notify?payment=datatrans"|fn_url:'C':'http'}
<p>{$lang.text_datatrans_notice|replace:"[post_url]":$post_url}</p>
<hr />


<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="sign">{$lang.datatrans_sign}:</label>
	<input type="text" name="payment_data[processor_params][sign]" id="sign" value="{$processor_params.sign}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="trans_type">{$lang.transaction_type}:</label>
	<select name="payment_data[processor_params][transaction_type]" id="trans_type">
		<option value="NOA" {if $processor_params.transaction_type == "NOA"}selected="selected"{/if}>{$lang.datatrans_noa}</option>
		<option value="CAA" {if $processor_params.transaction_type == "CAA"}selected="selected"{/if}>{$lang.datatrans_caa}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="CHF" {if $processor_params.currency == "CHF"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="AUD" {if $processor_params.currency == "AUD"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CAD" {if $processor_params.currency == "CAD"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="DKK" {if $processor_params.currency == "DKK"}selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="HKD" {if $processor_params.currency == "HKD"}selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="JPY" {if $processor_params.currency == "JPY"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="NZD" {if $processor_params.currency == "NZD"}selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="SGD" {if $processor_params.currency == "SGD"}selected="selected"{/if}>{$lang.currency_code_sgd}</option>
	</select>
</div>

<div class="form-field">
	<label for="test_live">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="test_live">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
