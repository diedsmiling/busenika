{* $Id: thaiepay.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="r_url" value="payment_notification.finish?payment=thaiepay"|fn_url:'C':'http'}
{assign var="text_thaiepay_notice" value=$lang.text_thaiepay_notice|replace:"[return_url]":$r_url}
{assign var="p_url" value="payment_notification.notify?payment=thaiepay"|fn_url:'C':'http'}
{assign var="text_thaiepay_notice" value=$text_thaiepay_notice|replace:"[postback_url]":$p_url}

<div> 
	{$text_thaiepay_notice}
</div> 
<hr /> 

<div class="form-field">
	<label for="merchantid">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchantid]" id="merchantid" value="{$processor_params.merchantid}" class="input-text" />
</div>

<div class="form-field">
	<label for="details">{$lang.payment_details}:</label>
	<input type="text" name="payment_data[processor_params][details]" id="details" value="{$processor_params.details}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="00" {if $processor_params.currency == "00"}selected="selected"{/if}>{$lang.currency_code_thb}</option>
		<option value="01" {if $processor_params.currency == "01"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="02" {if $processor_params.currency == "02"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="03" {if $processor_params.currency == "03"}selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="04" {if $processor_params.currency == "04"}selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="05" {if $processor_params.currency == "05"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="06" {if $processor_params.currency == "06"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="07" {if $processor_params.currency == "07"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="08" {if $processor_params.currency == "08"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
	</select>
</div>

<div class="form-field">
	<label for="add_param_name">Additional parameter Name:</label>
	<input type="text" name="payment_data[processor_params][add_param_name]" id="add_param_name" value="{$processor_params.add_param_name}" class="input-text" />
</div>

<div class="form-field">
	<label for="add_param_value">Additional parameter Value:</label>
	<input type="text" name="payment_data[processor_params][add_param_value]" id="add_param_value" value="{$processor_params.add_param_value}" class="input-text" />
</div>