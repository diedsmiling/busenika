{* $Id: epassporte.tpl 7812 2009-08-13 08:27:59Z zeke $ *}

<div class="form-field">
	<label for="acct_num">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][acct_num]" id="acct_num" value="{$processor_params.acct_num}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="pi_code">{$lang.product_code}:</label>
	<input type="text" name="payment_data[processor_params][pi_code]" id="pi_code" value="{$processor_params.pi_code}" class="input-text"  size="60" />
</div>
