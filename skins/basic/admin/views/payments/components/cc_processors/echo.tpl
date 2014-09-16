{* $Id: echo.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<p>{$lang.text_echo_notice}</p>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" size="60" value="{$processor_params.merchant_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="merchant_pin">{$lang.merchant_pin}:</label>
	<input type="text" name="payment_data[processor_params][merchant_pin]" id="merchant_pin" size="60" value="{$processor_params.merchant_pin}" class="input-text" />
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" size="60" value="{$processor_params.order_prefix}" class="input-text" />
</div>




