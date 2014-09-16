{* $Id: eway_form.tpl 9072 2010-03-12 14:30:03Z klerik $ *}

<p>{$lang.text_eway_form_notice}</p>
<hr />

<div class="form-field">
	<label for="client_id">{$lang.client_id}:</label>
	<input type="text" name="payment_data[processor_params][client_id]" id="client_id" value="{$processor_params.client_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="Y" {if $processor_params.test == "Y"}selected="selected"{/if}>{$lang.test}</option>
		<option value="N" {if $processor_params.test == "N"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
