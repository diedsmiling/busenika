{* $Id: google_checkout.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{assign var="r_url" value="`$config.https_location`/payments/google_checkout_response.php"}
<p>{$lang.text_google_notice|replace:"[return_url]":$r_url}</p>
<hr />
<fieldset>
<div class="form-field">
	<label for="agreement">{$lang.accept_google_policy} :</label>
 	<input type="hidden" name="payment_data[processor_params][policy_agreement]" value="N" />
 	<input type="checkbox" name="payment_data[processor_params][policy_agreement]" value="Y" {if $processor_params.policy_agreement == "Y"}checked="checked"{/if} id="agreement" />
</div>

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_key">{$lang.merchant_key}:</label>
	<input type="text" name="payment_data[processor_params][merchant_key]" id="merchant_key" value="{$processor_params.merchant_key}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
	</select>
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="N" {if $processor_params.test == "N"}selected="selected"{/if}>{$lang.live}</option>
		<option value="Y" {if $processor_params.test == "Y"}selected="selected"{/if}>{$lang.test}</option>
	</select>
</div>

<div class="form-field">
	<label for="button_type">{$lang.button_type}:</label>
 	<select name="payment_data[processor_params][button_type]" id="button_type">
		<option value="white" {if $processor_params.button_type == "white"}selected="selected"{/if}>{$lang.white}</option>
		<option value="trans" {if $processor_params.button_type == "trans"}selected="selected"{/if}>{$lang.transparent}</option>
	</select>
</div>

<div class="form-field">
	<label for="free_shipping">{$lang.enable_default_free_shipping}:</label>
	<input type="hidden" name="payment_data[processor_params][free_shipping]" value="N" />
    <input type="checkbox" name="payment_data[processor_params][free_shipping]" id="free_shipping" value="Y" {if $processor_params.free_shipping == "Y"}checked="checked"{/if} />
</div>
</fieldset>

<fieldset>
{include file="common_templates/subheader.tpl" title="`$lang.default_values`: `$lang.shipping`"}
	{foreach from="0"|fn_get_shippings item="_ship" key="ship_id"}
	<div class="form-field">
		<label for="shipping_{$ship_id}">{$_ship.shipping|escape}:</label>
		{assign var="ship_id" value=$_ship.shipping_id}
		{$currencies.$primary_currency.symbol} <input type="text" name="payment_data[processor_params][default_shippings][{$ship_id}]" value="{if $processor_params.default_shippings}{$processor_params.default_shippings.$ship_id}{/if}" id="shipping_{$ship_id}" class="input-text"  size="10" />
	</div>
	{foreachelse}
	<p class="no-items">{$lang.no_data}</p>
	{/foreach}
</fieldset>

<fieldset>
{include file="common_templates/subheader.tpl" title="`$lang.default_values`: `$lang.taxes`"}
{assign var="tax_exist" value=false}
{foreach from=""|fn_get_taxes item="_tax" key="tax_id"}
{if $_tax.price_includes_tax != "Y"}
{assign var="tax_exist" value=true}
<div class="form-field">
	<label for="tax_{$tax_id}">{$lang.tax} [{$_tax.tax|escape}]</label>
	{assign var="tax_id" value=$_tax.tax_id}
    <input type="text" name="payment_data[processor_params][default_taxes][{$tax_id}]" value="{if $processor_params.default_taxes}{$processor_params.default_taxes.$tax_id}{/if}" class="input-text" size="10" id="tax_{$tax_id}" /> %
</div>
{/if}
{/foreach}

{if !$tax_exist}
<p class="no-items">{$lang.no_data}</p>
{/if}
</fieldset>

<input type="hidden" name="payment_data[processor_params][button]" value='<input type="image" name="Google Checkout" alt="Google Checkout"   src="http://checkout.google.com/buttons/checkout.gif?merchant_id=1234567890&w=160&h=43&style=white&variant=text&loc=en_US" height="43" width="160" />' />
