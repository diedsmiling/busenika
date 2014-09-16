{* $Id: dps_access.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<div class="form-field">
	<label for="elm_client_id">{$lang.client_id}:</label>
	<input type="text" name="payment_data[processor_params][client_id]" id="elm_client_id" value="{$processor_params.client_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="elm_password" value="{$processor_params.password}" class="input-text" />
</div>

<div class="form-field">
	<label for="elm_order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="elm_order_prefix" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="elm_test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="elm_test">
		<option value="Y" {if $processor_params.test == "Y"}selected="selected"{/if}>{$lang.test}</option>
		<option value="N" {if $processor_params.test == "N"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
