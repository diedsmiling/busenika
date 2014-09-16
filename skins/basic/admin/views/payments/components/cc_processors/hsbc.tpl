{* $Id: hsbc.tpl 7497 2009-05-19 10:41:21Z zeke $ *}

{assign var="url" value="`$config.http_location`"}
<p>{$lang.text_hsbc_notice|replace:"[cart_url]":$url|replace:"[cart_dir]":$smarty.const.DIR_ROOT}</p>
<hr />

<div class="form-field">
	<label for="store_id">{$lang.client_id}:</label>
	<input type="text" name="payment_data[processor_params][store_id]" id="store_id" value="{$processor_params.store_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="cpihashkey">{$lang.cpi_hash_key}:</label>
	<input type="text" name="payment_data[processor_params][cpihashkey]" id="cpihashkey" value="{$processor_params.cpihashkey}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="T" {if $processor_params.mode == "T"}selected="selected"{/if}>{$lang.test}</option>
		<option value="P" {if $processor_params.mode == "P"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="826" {if $processor_params.currency == "826"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="978" {if $processor_params.currency == "978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="840" {if $processor_params.currency == "840"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="344" {if $processor_params.currency == "344"}selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="392" {if $processor_params.currency == "392"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
	</select>
</div>
