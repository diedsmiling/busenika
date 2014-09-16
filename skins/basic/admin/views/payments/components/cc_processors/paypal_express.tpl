{* $Id: paypal_express.tpl 9784 2010-06-10 10:56:23Z klerik $ *}

<div class="form-field">
	<label for="username">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][username]" id="username" size="24" value="{$processor_params.username}" class="input-text"/>
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" size="24" value="{$processor_params.password}" class="input-text"/>
</div>

<div class="form-field">
	<label>{$lang.paypal_authentication_method}:</label>
	<div class="select-field">
		<input id="elm_payment_auth_method_cert" class="radio" type="radio" value="cert" name="payment_data[processor_params][authentication_method]" {if $processor_params.authentication_method == "cert" || !$processor_params.authentication_method} checked="checked"{/if}/>
		<label for="elm_payment_auth_method_cert">{$lang.certificate}</label>
		<input id="elm_payment_auth_method_signature" class="radio" type="radio" value="signature" name="payment_data[processor_params][authentication_method]" {if $processor_params.authentication_method == "signature"} checked="checked"{/if}/>
		<label for="elm_payment_auth_method_signature">{$lang.signature}</label>
	</div>
</div>

<div class="form-field">
	<label for="certificate">{$lang.certificate_filename}:</label>
	{$smarty.const.DIR_ROOT}/payments/certificates/<input type="text" name="payment_data[processor_params][certificate]" id="certificate" size="24" value="{$processor_params.certificate}" class="input-text" />
</div>

<div class="form-field">
	<label for="signature">{$lang.signature}:</label>
	<input type="text" name="payment_data[processor_params][signature]" id="signature" value="{$processor_params.signature}" class="input-text" />
</div>

<div class="form-field">
	<label for="send_adress">{$lang.send_shipping_address}:</label>
	<input type="checkbox" name="payment_data[processor_params][send_adress]" {if $processor_params.send_adress == "Y"}checked="checked"{/if} id="send_adress" value="Y" />
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
		<option value="MXN" {if $processor_params.currency == "MXN"}selected="selected"{/if}>{$lang.currency_code_mxn}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode eq "test"} selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode eq "live"} selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" size="36" value="{$processor_params.order_prefix}" class="input-text" />
</div>
