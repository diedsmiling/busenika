{* $Id: ideal_basic.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="r_url" value="payment_notification?payment=ideal_basic"|fn_url:'C':'http'}
{assign var="e_url" value="payment_notification.result?payment=ideal_basic"|fn_url:'C':'http'}
<p>{$lang.text_ideal_basic_notice|replace:"[return_url]":$r_url|replace:"[error_url]":$e_url}</p>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_key">{$lang.secret_key}:</label>
	<input type="text" name="payment_data[processor_params][merchant_key]" id="merchant_key" value="{$processor_params.merchant_key}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="nl"{if $processor_params.language == "nl"} selected="selected"{/if}>{$lang.dutch}</option>
	</select>
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="FALSE" {if $processor_params.test == "FALSE"}selected="selected"{/if}>{$lang.live}</option>
		<option value="TRUE" {if $processor_params.test == "TRUE"}selected="selected"{/if}>{$lang.test}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
	</select>
</div>

{* <option value="EN"{if $processor_params.language == "EN"} selected="selected"{/if}>{$lang.english}
			<option value="DE"{if $processor_params.language == "DE"} selected="selected"{/if}>{$lang.german}
			<option value="FR"{if $processor_params.language == "FR"} selected="selected"{/if}>{$lang.french}  *}

