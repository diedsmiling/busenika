{* $Id: pay_read.tpl 10271 2010-07-29 11:46:43Z klerik $ *}

<hr />

<div class="form-field">
	<label for="agent_id">{$lang.agent_id}:</label>
	<input type="text" name="payment_data[processor_params][agent_id]" id="agent_id" value="{$processor_params.agent_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="key_1">{$lang.key} 1:</label>
	<input type="text" name="payment_data[processor_params][key_1]" id="key_1" value="{$processor_params.key_1}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="key_2">{$lang.key} 2:</label>
	<input type="text" name="payment_data[processor_params][key_2]" id="key_2" value="{$processor_params.key_2}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="true" {if $processor_params.test == "true"}selected="selected"{/if}>{$lang.test}</option>
		<option value="false" {if $processor_params.test == "false"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="SV" {if $processor_params.language == "SV"}selected="selected"{/if}>{$lang.sweden}</option>
		<option value="EN" {if $processor_params.language == "EN"}selected="selected"{/if}>{$lang.english}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="SEK" {if $processor_params.currency == "SEK"}selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="CAD" {if $processor_params.currency == "CAD"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="CHF" {if $processor_params.currency == "CHF"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="FRF" {if $processor_params.currency == "FRF"}selected="selected"{/if}>{$lang.currency_code_frf}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="HKD" {if $processor_params.currency == "HKD"}selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="JPY" {if $processor_params.currency == "JPY"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="NZD" {if $processor_params.currency == "NZD"}selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="SGD" {if $processor_params.currency == "SGD"}selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="ZAR" {if $processor_params.currency == "ZAR"}selected="selected"{/if}>{$lang.currency_code_zar}</option>
		<option value="AUD" {if $processor_params.currency == "AUD"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="WST" {if $processor_params.currency == "WST"}selected="selected"{/if}>{$lang.currency_code_wst}</option>
		<option value="VUV" {if $processor_params.currency == "VUV"}selected="selected"{/if}>{$lang.currency_code_vuv}</option>
		<option value="TOP" {if $processor_params.currency == "TOP"}selected="selected"{/if}>{$lang.currency_code_top}</option>
		<option value="SBD" {if $processor_params.currency == "SBD"}selected="selected"{/if}>{$lang.currency_code_sbd}</option>
		<option value="PNG" {if $processor_params.currency == "PNG"}selected="selected"{/if}>{$lang.currency_code_png}</option>
		<option value="MYR" {if $processor_params.currency == "MYR"}selected="selected"{/if}>{$lang.currency_code_myr}</option>
		<option value="KWD" {if $processor_params.currency == "KWD"}selected="selected"{/if}>{$lang.currency_code_kwd}</option>
		<option value="FJD" {if $processor_params.currency == "FJD"}selected="selected"{/if}>{$lang.currency_code_fjd}</option>
	</select>
</div>
