{* $Id: paypoint.tpl 8650 2010-01-20 14:44:54Z 2tl $ *}

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="true" {if $processor_params.mode == "true"}selected="selected"{/if}>{$lang.test}: {$lang.approved}</option>
		<option value="false" {if $processor_params.mode == "false"}selected="selected"{/if}>{$lang.test}: {$lang.declined}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
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
	<label for="deferred">{$lang.deferred}:</label>
	<select name="payment_data[processor_params][deferred]" id="deferred">
		<option value="full" {if $processor_params.deferred == "full"}selected="selected"{/if}>{$lang.full}</option>
		<option value="true" {if $processor_params.deferred == "true"}selected="selected"{/if}>{$lang.true}</option>
		<option value="reuse" {if $processor_params.deferred == "reuse"}selected="selected"{/if}>{$lang.reuse}</option>
		<option value="" {if !$processor_params.deferred}selected="selected"{/if}>{$lang.do_not_use}</option>
	</select>
</div>

<div class="form-field">
	<label for="mail_subject">{$lang.mail_subject}:</label>
	<input type="text" name="payment_data[processor_params][mail_subject]" id="mail_subject" value="{$processor_params.mail_subject}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="mail_message">{$lang.mail_message}:</label>
	<textarea name="payment_data[processor_params][mail_message]" id="mail_message" class="input-textarea-long" cols="80" rows="7">{$processor_params.mail_message}</textarea>
</div>
