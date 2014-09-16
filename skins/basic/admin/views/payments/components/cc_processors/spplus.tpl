{* $Id: spplus.tpl 7636 2009-06-30 07:03:06Z zeke $ *}

{*<p>{$lang.text_spplus_notice}</p>
<hr />
*}

<div class="form-field">
	<label for="elm_clent">CLENT:</label>
	<input type="text" name="payment_data[processor_params][clent]" id="elm_clent" value="{$processor_params.clent}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="post_url">{$lang.post_url}:</label>
	<input type="text" name="payment_data[processor_params][post_url]" id="post_url" value="{$processor_params.post_url}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="FR" {if $processor_params.language == "FR"}selected="selected"{/if}>{$lang.french}</option>
		<option value="EN" {if $processor_params.language == "EN"}selected="selected"{/if}>{$lang.english}</option>
		<option value="IT" {if $processor_params.language == "IS"}selected="selected"{/if}>{$lang.italian}</option>
		<option value="ES" {if $processor_params.language == "ES"}selected="selected"{/if}>{$lang.spanish}</option>
		<option value="DE" {if $processor_params.language == "DE"}selected="selected"{/if}>{$lang.german}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="978" {if $processor_params.currency == "978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="840" {if $processor_params.currency == "840"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="826" {if $processor_params.currency == "826"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="124" {if $processor_params.currency == "124"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text"  size="60" />
</div>
