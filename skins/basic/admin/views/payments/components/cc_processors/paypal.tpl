{* $Id: paypal.tpl 8176 2009-11-05 09:20:20Z zeke $ *}

<div class="form-field">
	<label for="account">{$lang.account}:</label>
	<input type="text" name="payment_data[processor_params][account]" id="account" value="{$processor_params.account}" class="input-text" />
</div>

<div class="form-field">
	<label for="item_name">{$lang.paypal_item_name}:</label>
	<input type="text" name="payment_data[processor_params][item_name]" id="item_name" value="{$processor_params.item_name}" class="input-text" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="AUD" {if $processor_params.currency == "AUD"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CAD" {if $processor_params.currency == "CAD"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="JPY" {if $processor_params.currency == "JPY"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="NZD" {if $processor_params.currency == "NZD"}selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="CHF" {if $processor_params.currency == "CHF"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="HKD" {if $processor_params.currency == "HKD"}selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="SGD" {if $processor_params.currency == "SGD"}selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="SEK" {if $processor_params.currency == "SEK"}selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="DKK" {if $processor_params.currency == "DKK"}selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="PLN" {if $processor_params.currency == "PLN"}selected="selected"{/if}>{$lang.currency_code_pln}</option>
		<option value="NOK" {if $processor_params.currency == "NOK"}selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="HUF" {if $processor_params.currency == "HUF"}selected="selected"{/if}>{$lang.currency_code_huf}</option>
		<option value="CZK" {if $processor_params.currency == "CZK"}selected="selected"{/if}>{$lang.currency_code_czk}</option>
		<option value="ILS" {if $processor_params.currency == "ILS"}selected="selected"{/if}>{$lang.currency_code_ils}</option>
		<option value="MXN" {if $processor_params.currency == "MXN"}selected="selected"{/if}>{$lang.currency_code_mxn}</option>
		<option value="THB" {if $processor_params.currency == "THB"}selected="selected"{/if}>{$lang.currency_code_thb}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>

<p>
{$lang.see_demo}: <a href="http://www.paypal-marketing.com/html/partner/portal/standard.html">http://www.paypal-marketing.com/html/partner/portal/standard.html</a>
</p>
