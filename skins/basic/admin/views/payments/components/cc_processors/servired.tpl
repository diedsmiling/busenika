{* $Id: servired.tpl 8674 2010-01-25 08:21:22Z 2tl $ *}

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="terminal">{$lang.terminal}:</label>
	<input type="text" name="payment_data[processor_params][terminal]" id="terminal" value="{$processor_params.terminal}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="clave">{$lang.secret_key}:</label>
	<input type="text" name="payment_data[processor_params][clave]" id="clave" value="{$processor_params.clave}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="N" {if $processor_params.test == "N"}selected="selected"{/if}>{$lang.live}</option>
		<option value="Y" {if $processor_params.test == "Y"}selected="selected"{/if}>{$lang.test}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="978"{if $processor_params.currency eq "978"} selected="selected"{/if}>{$lang.currency_code_eur}
		<option value="840"{if $processor_params.currency eq "840"} selected="selected"{/if}>{$lang.currency_code_usd}
	</select>
</div>
