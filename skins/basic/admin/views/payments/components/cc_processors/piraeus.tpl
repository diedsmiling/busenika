{* $Id: piraeus.tpl 10589 2010-09-03 08:37:57Z klerik $ *}

{assign var="website_url" value="`$config.http_location`/`$config.customer_index`"}
{assign var="referrer_url" value="`$config.http_location`/`$config.customer_index`"}
{assign var="success_url" value="`$config.http_location`/`$config.customer_index`?dispatch=payment_notification.notify&payment=piraeus"}
{assign var="failure_url" value="`$config.http_location`/`$config.customer_index`?dispatch=payment_notification.notify&payment=piraeus"}
{assign var="backlink_url" value="`$config.http_location`/`$config.customer_index`?dispatch=payment_notification.cancel&payment=piraeus"}

{assign var="ip_address" value=`$smarty.server.SERVER_ADDR`}
{assign var="response_method" value="POST"}

<p>{$lang.text_piraeus_notice|replace:"[website_url]":$website_url|replace:"[referrer_url]":$referrer_url|replace:"[success_url]":$success_url|replace:"[failure_url]":$failure_url|replace:"[backlink_url]":$backlink_url|replace:"[ip_address]":$ip_address|replace:"[response_method]":$response_method}</p>
<hr />

<div class="form-field">
	<label for="acquirerid">{$lang.acquirerid}:</label>
	<input type="text" name="payment_data[processor_params][acquirerid]" id="acquirerid" value="{$processor_params.acquirerid}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchantid">{$lang.merchantid}:</label>
	<input type="text" name="payment_data[processor_params][merchantid]" id="merchantid" value="{$processor_params.merchantid}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="posid">{$lang.posid}:</label>
	<input type="text" name="payment_data[processor_params][posid]" id="posid" value="{$processor_params.posid}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="username">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][username]" id="username" value="{$processor_params.username}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="requesttype">{$lang.requesttype}:</label>
	<select name="payment_data[processor_params][requesttype]" id="requesttype">
		<option value="02" {if $processor_params.requesttype == "02"}selected="selected"{/if}>{$lang.sale}</option>
		<option value="00" {if $processor_params.requesttype == "00"}selected="selected"{/if}>{$lang.preauthorization}</option>
	</select>
</div>

<div class="form-field">
	<label for="expirepreauth">{$lang.expirepreauth}:</label>
	<input type="text" name="payment_data[processor_params][expirepreauth]" id="expirepreauth" value="{$processor_params.expirepreauth}" class="input-text" size="60" />
	<p class="description">{$lang.expirepreauth_description}</p>
</div>

<div class="form-field">
	<label for="currencycode">{$lang.currencycode}:</label>
	<select name="payment_data[processor_params][currencycode]" id="currencycode">
		<option value="978" {if $processor_params.currencycode == "978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
	</select>
</div>

<div class="form-field">
	<label for="languagecode">{$lang.language}:</label>
	<select name="payment_data[processor_params][languagecode]" id="languagecode">
		<option value="el-GR" {if $processor_params.languagecode == "el-GR"}selected="selected"{/if}>{$lang.greek}</option>
		<option value="en-US" {if $processor_params.languagecode == "en-US"}selected="selected"{/if}>{$lang.english}</option>
	</select>
</div>
