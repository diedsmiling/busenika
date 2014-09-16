{* $Id: westpac.tpl 8264 2009-11-18 12:12:16Z zeke $ *}

{assign var="prepayment" value="`$config.http_location`/"}
{assign var="return" value="`$config.http_location`/payments/westpac.php"}
{assign var="notify" value="`$config.https_location`/payments/westpac.php"}
<p>{$lang.text_payway_notice|replace:"[prepayment]":$prepayment|replace:"[return]":$return|replace:"[notify]":$notify}</p>
<hr />

<div class="form-field">
	<label for="elm_merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="elm_merchant_id" value="{$processor_params.merchant_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="elm_biller_code">{$lang.biller_code}:</label>
	<input type="text" name="payment_data[processor_params][biller_code]" id="elm_biller_code" value="{$processor_params.biller_code}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="elm_encryption_key">{$lang.encryption_key}:</label>
	<input type="text" name="payment_data[processor_params][encryption_key]" id="elm_encryption_key" value="{$processor_params.encryption_key}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
