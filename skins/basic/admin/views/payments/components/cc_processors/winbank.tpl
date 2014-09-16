{* $Id: winbank.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="main_url" value="`$config.http_location`/`$config.customer_index`"|fn_url:'C'}
{assign var="ok_url" value="payment_notification.notify?payment=winbank"|fn_url:'C':'http':'&'}
<p>{$lang.text_winbank_notice|replace:"[main_url]":$main_url|replace:"[ok_url]":$ok_url|replace:"[ip]":$smarty.server.REMOTE_ADDR}</p>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="pos_id">{$lang.pos_id}:</label>
	<input type="text" name="payment_data[processor_params][pos_id]" id="pos_id" value="{$processor_params.pos_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="user">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][user]" id="user" value="{$processor_params.user}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="0" {if $processor_params.language == "0"}selected="selected"{/if}>{$lang.greek}</option>
		<option value="1" {if $processor_params.language == "1"}selected="selected"{/if}>{$lang.english}</option>
		<option value="2" {if $processor_params.language == "2"}selected="selected"{/if}>{$lang.german}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="978" {if $processor_params.currency == "978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
	</select>
</div>
