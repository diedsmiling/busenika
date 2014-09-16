{* $Id: 365.tpl 7719 2009-07-16 04:46:38Z zeke $ *}

{assign var="r_url" value="`$config.http_location`/payments/365.php"}
<p>{$lang.text_365_notice|replace:"[return_url]":$r_url}</p>
<hr />

<div class="form-field">
	<label for="site_id">{$lang.site_id}:</label>
	<input type="text" name="payment_data[processor_params][site_id]" id="site_id" value="{$processor_params.site_id}" class="input-text"  size="10" />
</div>

<div class="form-field">
	<label for="cryptext">{$lang.encryption_key}:</label>
	<input type="text" name="payment_data[processor_params][cryptext]" id="cryptext" value="{$processor_params.cryptext}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="cryptint">{$lang.cryptint}:</label>
	<input type="text" name="payment_data[processor_params][cryptint]" id="cryptint" value="{$processor_params.cryptint}" class="input-text"  size="60" />
</div>
