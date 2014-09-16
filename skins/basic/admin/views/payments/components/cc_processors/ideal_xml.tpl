{* $Id: ideal_xml.tpl 7186 2009-04-02 12:47:28Z zeke $ *}

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_key">{$lang.secret_key}:</label>
	<input type="text" name="payment_data[processor_params][merchant_key]" id="merchant_key" value="{$processor_params.merchant_key}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="description">{$lang.description}:</label>
	<input type="text" name="payment_data[processor_params][description]" id="description" value="{$processor_params.description}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="NL"{if $processor_params.language == "NL"} selected="selected"{/if}>{$lang.dutch}
		<option value="EN"{if $processor_params.language == "EN"} selected="selected"{/if}>{$lang.english}
		<option value="DE"{if $processor_params.language == "DE"} selected="selected"{/if}>{$lang.german}
		<option value="FR"{if $processor_params.language == "FR"} selected="selected"{/if}>{$lang.french}
	</select>
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="0" {if $processor_params.test == "0"}selected="selected"{/if}>{$lang.live}</option>
		<option value="1" {if $processor_params.test == "1"}selected="selected"{/if}>{$lang.test}</option>
	</select>
</div>

<div class="form-field">
	<label for="payment_type">{$lang.payment_type}:</label>
	<select name="payment_data[processor_params][payment_type]" id="payment_type">
		<option value="bn"{if $processor_params.payment_type == "bn"} selected="selected"{/if}>{$lang.bank}
		<option value="cc"{if $processor_params.payment_type == "cc"} selected="selected"{/if}>{$lang.credit_card}
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
	</select>
</div>
