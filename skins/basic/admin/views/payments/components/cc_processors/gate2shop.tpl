{* $Id: gate2shop.tpl 0000 2010-03-01 13:17:46Z 2tl $ *}

{assign var="r_url" value="`$config.current_location`/payments/gate2shop.php"}
{assign var="text_gate2shop_notice" value=$lang.text_gate2shop_notice|replace:"[result_url]":$r_url}
{assign var="b_url" value="`$config.current_location`/`$config.customer_index`"}
{assign var="text_gate2shop_notice" value=$text_gate2shop_notice|replace:"[back_url]":$b_url}

<div> 
	{$text_gate2shop_notice}
</div> 
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_site_id">{$lang.merchant_site_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_site_id]" id="merchant_site_id" value="{$processor_params.merchant_site_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="secret_string">{$lang.secret_string}:</label>
	<input type="text" name="payment_data[processor_params][secret_string]" id="secret_string" value="{$processor_params.secret_string}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="EUR" {if $processor_params.currency == "EUR" || $processor_params.currency == ""}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>
