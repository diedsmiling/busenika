{* $Id: chronopay_form.tpl 9343 2010-04-29 14:40:33Z imac $ *}

<p>{$lang.text_chronopay_form_notice}</p>
<hr />

<div class="form-field">
	<label for="product_id">{$lang.product_id}:</label>
	<input type="text" name="payment_data[processor_params][product_id]" id="product_id" value="{$processor_params.product_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="encrypt">{$lang.sharedsec}:</label>
	<input type="text" name="payment_data[processor_params][sharedsec]" id="encrypt" value="{$processor_params.sharedsec}" class="input-text" size="60" />
</div>
{*
<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
	</select>
</div>
*}

