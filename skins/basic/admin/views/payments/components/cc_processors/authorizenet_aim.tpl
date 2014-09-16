{* $Id: authorizenet_aim.tpl 7333 2009-04-22 07:14:55Z zeke $ *}

<div class="form-field">
	<label for="login">{$lang.login}:</label>
	<input type="text" name="payment_data[processor_params][login]" id="login" value="{$processor_params.login}" class="input-text" />
</div>

<div class="form-field">
	<label for="transaction_key">{$lang.transaction_key}:</label>
	<input type="text" name="payment_data[processor_params][transaction_key]" id="transaction_key" value="{$processor_params.transaction_key}" class="input-text" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="USD"{if $processor_params.currency == "USD"} selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="EUR"{if $processor_params.currency == "EUR"} selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="AUD"{if $processor_params.currency == "AUD"} selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CAD"{if $processor_params.currency == "CAD"} selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="CHF"{if $processor_params.currency == "CHF"} selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="CZK"{if $processor_params.currency == "CZK"} selected="selected"{/if}>{$lang.currency_code_czk}</option>
		<option value="DKK"{if $processor_params.currency == "DKK"} selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="FRF"{if $processor_params.currency == "FRF"} selected="selected"{/if}>{$lang.currency_code_frf}</option>
		<option value="GBP"{if $processor_params.currency == "GBP"} selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="HKD"{if $processor_params.currency == "HKD"} selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="HUF"{if $processor_params.currency == "HUF"} selected="selected"{/if}>{$lang.currency_code_huf}</option>
		<option value="ILS"{if $processor_params.currency == "ILS"} selected="selected"{/if}>{$lang.currency_code_ils}</option>
		<option value="JPY"{if $processor_params.currency == "JPY"} selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="LTL"{if $processor_params.currency == "LTL"} selected="selected"{/if}>{$lang.currency_code_ltl}</option>
		<option value="LVL"{if $processor_params.currency == "LVL"} selected="selected"{/if}>{$lang.currency_code_lvl}</option>
		<option value="MXN"{if $processor_params.currency == "MXN"} selected="selected"{/if}>{$lang.currency_code_mxn}</option>
		<option value="NOK"{if $processor_params.currency == "NOK"} selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="NZD"{if $processor_params.currency == "NZD"} selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="PLN"{if $processor_params.currency == "PLN"} selected="selected"{/if}>{$lang.currency_code_pln}</option>
		<option value="RUR"{if $processor_params.currency == "RUR"} selected="selected"{/if}>{$lang.currency_code_rur}</option>
		<option value="SEK"{if $processor_params.currency == "SEK"} selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="SGD"{if $processor_params.currency == "SGD"} selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="SKK"{if $processor_params.currency == "SKK"} selected="selected"{/if}>{$lang.currency_code_skk}</option>
		<option value="THB"{if $processor_params.currency == "THB"} selected="selected"{/if}>{$lang.currency_code_thb}</option>
		<option value="TRY"{if $processor_params.currency == "TRY"} selected="selected"{/if}>{$lang.currency_code_try}</option>
		<option value="KPW"{if $processor_params.currency == "KPW"} selected="selected"{/if}>{$lang.currency_code_kpw}</option>
		<option value="KRW"{if $processor_params.currency == "KRW"} selected="selected"{/if}>{$lang.currency_code_krw}</option>
		<option value="ZAR"{if $processor_params.currency == "ZAR"} selected="selected"{/if}>{$lang.currency_code_zar}</option>
	</select>
</div>

<div class="form-field">
	<label for="md5_hash_value">{$lang.md5_hash_value}:</label>
	<input type="text" name="payment_data[processor_params][md5_hash_value]" id="md5_hash_value" value="{$processor_params.md5_hash_value}" class="input-text" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="developer" {if $processor_params.mode == "developer"}selected="selected"{/if}>{$lang.test} (dev)</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="transaction_type">{$lang.transaction_type}:</label>
	<select name="payment_data[processor_params][transaction_type]" id="transaction_type">
		<option value="P" {if $processor_params.transaction_type == "P"}selected="selected"{/if}>{$lang.authorize_capture}</option>
		<option value="A" {if $processor_params.transaction_type == "A"}selected="selected"{/if}>{$lang.authorize_only}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>

