{* $Id: sage_payment.tpl 9491 2010-05-18 13:52:02Z klerik $ *}

<p>{$lang.text_sage_payment_notice}</p>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="merchant_key">{$lang.merchant_key}:</label>
	<input type="text" name="payment_data[processor_params][merchant_key]" id="merchant_key" value="{$processor_params.merchant_key}" class="input-text" size="60" />
</div>
