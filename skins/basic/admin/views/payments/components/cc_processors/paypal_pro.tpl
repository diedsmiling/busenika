{* $Id: paypal_pro.tpl 8613 2010-01-15 08:56:24Z 2tl $ *}

<div class="form-field">
	<label for="username">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][username]" id="username" value="{$processor_params.username}" class="input-text" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" />
</div>

<div class="form-field">
	<label>{$lang.paypal_authentication_method}:</label>
	<div class="select-field">
		<input id="elm_payment_auth_method_cert" class="radio" type="radio" value="cert" name="payment_data[processor_params][authentication_method]" {if $processor_params.authentication_method == "cert" || !$processor_params.authentication_method} checked="checked"{/if}/>
		<label for="elm_payment_auth_method_cert">{$lang.certificate}</label>
		<input id="elm_payment_auth_method_signature" class="radio" type="radio" value="signature" name="payment_data[processor_params][authentication_method]" {if $processor_params.authentication_method == "signature"} checked="checked"{/if}/>
		<label for="elm_payment_auth_method_signature">{$lang.signature}</label>
	</div>
</div>

<div class="form-field">
	<label for="certificate_filename">{$lang.certificate_filename}:</label>
	{$smarty.const.DIR_ROOT}/payments/certificates/<input type="text" name="payment_data[processor_params][certificate_filename]" id="certificate_filename" value="{$processor_params.certificate_filename}" class="input-text" />
</div>

<div class="form-field">
	<label for="api_signature">{$lang.signature}:</label>
	<input type="text" name="payment_data[processor_params][signature]" id="api_signature" value="{$processor_params.signature}" class="input-text" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="AUD" {if $processor_params.currency == "AUD"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CAD" {if $processor_params.currency == "CAD"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="JPY" {if $processor_params.currency == "JPY"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>

<h2 class="subheader">{$lang.3d_secure}</h2>

<div class="select-field">
	<input type="hidden" value="" name="payment_data[processor_params][use_cardinal]" />
	<input id="use_cardinal" class="hidden" type="checkbox" value="Y" name="payment_data[processor_params][use_cardinal]" {if $processor_params.use_cardinal == "Y"} checked="checked"{/if} onclick="$('#use_cardinal_block').toggle();"/>

	<p id="on_use_cardinal_sw"{if $processor_params.use_cardinal == "Y"} class="hidden"{/if}>
	<a class="cm-combination dashed" onclick="$('#use_cardinal').click();">{$lang.use_cardinal}</a><br />
	</p>
	
	<p id="off_use_cardinal_sw"{if $processor_params.use_cardinal != "Y"} class="hidden"{/if}>
	<a class="cm-combination dashed" onclick="$('#use_cardinal').click();">{$lang.dont_use_cardinal}</a><br />
	</p>
</div>

<div id="use_cardinal_block"{if $processor_params.use_cardinal != "Y"} class="hidden"{/if}>
<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="processor_id">{$lang.processor_id}:</label>
	<input type="text" name="payment_data[processor_params][processor_id]" id="processor_id" value="{$processor_params.processor_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="transaction_password">{$lang.transaction_password}:</label>
	<input type="text" name="payment_data[processor_params][transaction_password]" id="transaction_password" value="{$processor_params.transaction_password}" class="input-text" />
</div>

<div class="form-field">
	<label for="transaction_url">{$lang.transaction_url}:</label>
	<input type="text" name="payment_data[processor_params][transaction_url]" id="transaction_url" value="{$processor_params.transaction_url}" class="input-text" />
</div>
</div>

<p class="description"><a href="https://www.paypal-business.co.uk/3Dsecure.asp" target="_blank">{$lang.read_more_3d_secure}</a></p>

