{* $Id: e_gold.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<p>{$lang.text_e_gold_notice}</p>
<hr />

<div class="form-field">
	<label for="account_id">{$lang.account_id}:</label>
	<input type="text" name="payment_data[processor_params][account_id]" id="account_id" value="{$processor_params.account_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="account_name">{$lang.account_name}:</label>
	<input type="text" name="payment_data[processor_params][account_name]" id="account_name" value="{$processor_params.account_name}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.alternative_password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="85" {if $processor_params.currency == "85"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="1" {if $processor_params.currency == "1"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="2" {if $processor_params.currency == "2"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="33" {if $processor_params.currency == "33"}selected="selected"{/if}>{$lang.currency_code_frf}</option>
		<option value="41" {if $processor_params.currency == "41"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="44" {if $processor_params.currency == "44"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="81" {if $processor_params.currency == "81"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="96" {if $processor_params.currency == "96"}selected="selected"{/if}>{$lang.currency_code_eek}</option>
		<option value="97" {if $processor_params.currency == "97"}selected="selected"{/if}>{$lang.currency_code_ltl}</option>
	</select>
</div>
