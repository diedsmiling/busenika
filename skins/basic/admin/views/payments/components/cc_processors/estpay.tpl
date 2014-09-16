{* $Id: estpay.tpl 7139 2009-03-26 13:25:23Z zeke $ *}

<div class="form-field">
	<label for="merchant_name">{$lang.merchant_name}:</label>
	<input type="text" name="payment_data[processor_params][merchant_name]" id="merchant_name" value="{$processor_params.merchant_name}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="merchant_password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][merchant_password]" id="merchant_password" value="{$processor_params.merchant_password}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="client_id">{$lang.client_id}:</label>
	<input type="text" name="payment_data[processor_params][client_id]" id="client_id" value="{$processor_params.client_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="949"{if $processor_params.currency eq "949"} selected="selected"{/if}>{$lang.currency_code_try}</option>
		<option value="978"{if $processor_params.currency eq "978"} selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="840"{if $processor_params.currency eq "840"} selected="selected"{/if}>{$lang.currency_code_usd}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test"{if $processor_params.mode eq "test"} selected="selected"{/if}>{$lang.test}</option>
		<option value="live"{if $processor_params.mode eq "live"} selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
