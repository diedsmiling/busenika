{* $Id: pri_form.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<p>{$lang.text_pri_form_notice}</p>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="key">{$lang.key}:</label>
	<input type="text" name="payment_data[processor_params][key]" id="key" value="{$processor_params.key}" class="input-text"  size="60" />
</div>
