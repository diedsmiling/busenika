{* $Id: nochex.tpl 7166 2009-03-31 13:29:22Z zeke $ *}

<div class="form-field">
	<label for="">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchantid]" value="{$processor_params.merchantid}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="">{$lang.payment_details}:</label>
	<input type="text" name="payment_data[processor_params][payment_description]" value="{$processor_params.payment_description}" class="input-text"  size="60" />
</div>
