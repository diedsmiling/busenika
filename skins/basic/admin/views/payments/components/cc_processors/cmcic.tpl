{* $Id: cmcic.tpl 7719 2009-07-16 04:46:38Z zeke $ *}

{assign var="r_url" value="<strong>`$config.http_location`/payments/cmcic.php</strong>"}
<p>{$lang.text_cmcic_notice|replace:"[postback_url]":$r_url}</p>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id} ({$lang.tpe}):</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="key">{$lang.key}:</label>
	<input type="text" name="payment_data[processor_params][key]" id="key" value="{$processor_params.key}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="societe">{$lang.company}:</label>
	<input type="text" name="payment_data[processor_params][societe]" id="societe" value="{$processor_params.societe}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="bank">{$lang.bank}:</label>
	<select name="payment_data[processor_params][bank]" id="bank">
		<option value="CM" {if $processor_params.bank == "CM"}selected="selected"{/if}>{$lang.bank_cm}</option>
		<option value="CIC" {if $processor_params.bank == "CIC"}selected="selected"{/if}>{$lang.bank_cic}</option>
		<option value="OBS" {if $processor_params.bank == "OBS"}selected="selected"{/if}>{$lang.bank_obc}</option>
		<option value="CAIXA" {if $processor_params.bank == "CAIXA"}selected="selected"{/if}>{$lang.bank_caixa}</option>
	</select>
</div>

<div class="form-field">
	<label for="payment_desc">{$lang.payment_details}:</label>
	<input type="text" name="payment_data[processor_params][payment_desc]" id="payment_desc" value="{$processor_params.payment_desc}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="FR" {if $processor_params.language == "FR"}selected="selected"{/if}>{$lang.french}</option>
		<option value="EN" {if $processor_params.language == "EN"}selected="selected"{/if}>{$lang.english}</option>
		<option value="IT" {if $processor_params.language == "IS"}selected="selected"{/if}>{$lang.italian}</option>
		<option value="ES" {if $processor_params.language == "ES"}selected="selected"{/if}>{$lang.spanish}</option>
		<option value="DE" {if $processor_params.language == "DE"}selected="selected"{/if}>{$lang.german}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="CHF" {if $processor_params.currency == "CHF"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="NOK" {if $processor_params.currency == "NOK"}selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="SEK" {if $processor_params.currency == "SEK"}selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="DKK" {if $processor_params.currency == "DKK"}selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="AUD" {if $processor_params.currency == "AUD"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CAD" {if $processor_params.currency == "CAD"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
