{* $Id: itransact.tpl 2421 2006-11-01 13:17:46Z dimarzio $ *}

<div class="form-field">
	<label for="vendor_id">{$lang.vendor_id}:</label>
	<input type="text" name="payment_data[processor_params][vendor_id]" id="vendor_id" value="{$processor_params.vendor_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="merchant_name">{$lang.merchant_name}:</label>
	<input type="text" name="payment_data[processor_params][merchant_name]" id="merchant_name" value="{$processor_params.merchant_name}" class="input-text" />
</div>

<div class="form-field">
	<label for="secret_key">{$lang.secret_key}:</label>
	<input type="text" name="payment_data[processor_params][secret_key]" id="secret_key" value="{$processor_params.secret_key}" class="input-text" />
	<p class="description">{$lang.text_secret_key_notice}</p>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>
