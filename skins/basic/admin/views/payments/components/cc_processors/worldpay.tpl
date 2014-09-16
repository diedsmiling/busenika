{* $Id: worldpay.tpl 7719 2009-07-16 04:46:38Z zeke $ *}

{assign var="return_url" value="`$config.http_location`/payments/worldpay.php"}
<p>{$lang.text_worldpay_notice|replace:"[return_url]":$return_url}</p>
<hr />

<div class="form-field">
	<label for="account_id">{$lang.installation_id}:</label>
	<input type="text" name="payment_data[processor_params][account_id]" id="account_id" value="{$processor_params.account_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="callback_password">{$lang.callback_password}:</label>
	<input type="text" name="payment_data[processor_params][callback_password]" id="callback_password" value="{$processor_params.callback_password}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="101" {if $processor_params.test == "101"}selected="selected"{/if}>{$lang.test}: {$lang.declined}</option>
		<option value="100" {if $processor_params.test == "100"}selected="selected"{/if}>{$lang.test}: {$lang.approved}</option>
		<option value="0" {if $processor_params.test == "0"}selected="selected"{/if}>{$lang.live}</option>
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
	<label for="type">{$lang.type}:</label>
 	<select name="payment_data[processor_params][authmode]" id="type">
		<option value="A" {if $processor_params.authmode == "A"}selected="selected"{/if}>{$lang.fullauth}</option>
		<option value="E" {if $processor_params.authmode == "E"}selected="selected"{/if}>{$lang.preauth}</option>
	</select>
</div>