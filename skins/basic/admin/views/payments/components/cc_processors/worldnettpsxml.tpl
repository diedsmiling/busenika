{* $Id: worldpay.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<div class="form-field">
	<label for="terminal_id">{$lang.terminal_id}:</label>
	<input type="text" name="payment_data[processor_params][terminal_id]" id="terminal_id" value="{$processor_params.terminal_id}" class="input-text"  size="10" />
</div>

<div class="form-field">
	<label for="shared_secret">{$lang.shared_secret}:</label>
	<input type="password" name="payment_data[processor_params][shared_secret]" id="shared_secret" value="{$processor_params.shared_secret}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="1" {if $processor_params.test == "1"}selected="selected"{/if}>{$lang.test}</option>
		<option value="0" {if $processor_params.test == "0"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="avs">{$lang.avs}:</label>
	<select name="payment_data[processor_params][avs]" id="avs">
		<option value="1" {if $processor_params.avs == "1"}selected="selected"{/if}>{$lang.enabled}</option>
		<option value="0" {if $processor_params.avs == "0"}selected="selected"{/if}>{$lang.disabled}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
	</select>
</div>
