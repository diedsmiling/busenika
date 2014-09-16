{* $Id: qbms.tpl 7719 2009-07-16 04:46:38Z zeke $ *}

{assign var="r_url" value="`$config.https_location`/payments/qbms_response.php"}
<p>{$lang.text_qbms_notice|replace:"[response_url]":$r_url}</p>
<hr />

<div class="form-field">
	<label for="app_login">{$lang.application_login}:</label>
	<input type="text" name="payment_data[processor_params][app_login]" id="app_login" value="{$processor_params.app_login}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="app_id">{$lang.application_id}:</label>
	<input type="text" name="payment_data[processor_params][app_id]" id="app_id" value="{$processor_params.app_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="certificate_filename">{$lang.certificate_filename}:</label>
	{$smarty.const.DIR_ROOT}/payments/certificates/<input type="text" name="payment_data[processor_params][certificate_filename]" id="certificate_filename" value="{$processor_params.certificate_filename}" class="input-text" size="30" />
</div>

<div class="form-field">
	<label for="p">{$lang.connection_ticket}:</label>
	<input type="hidden" name="payment_data[processor_params][connection_ticket]" value="{$processor_params.connection_ticket}" />
	<input type="text" name="p" id="p" value="{$processor_params.connection_ticket}" class="input-text" size="60" disabled="disabled" />
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
