{* $Id: virtual_merchant.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="user_id">{$lang.user_id}:</label>
	<input type="text" name="payment_data[processor_params][user_id]" id="user_id" value="{$processor_params.user_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="user_pin">{$lang.user_pin}:</label>
	<input type="text" name="payment_data[processor_params][user_pin]" id="user_pin" value="{$processor_params.user_pin}" class="input-text" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>

<div class="form-field">
	<label for="cvv2">{$lang.cvv2}:</label>
	<input type="hidden" name="payment_data[processor_params][cvv2]" value="N" />
	<input type="checkbox" name="payment_data[processor_params][cvv2]" id="cvv2" value="Y" {if $processor_params.cvv2 == "Y"}checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="avs">{$lang.avs}:</label>
	<input type="hidden" name="payment_data[processor_params][avs]" value="N" />
	<input type="checkbox" name="payment_data[processor_params][avs]" id="avs" value="Y" {if $processor_params.avs == "Y"}checked="checked"{/if} />
</div>
