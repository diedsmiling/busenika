{* $Id: linkpoint_api.tpl 7497 2009-05-19 10:41:21Z zeke $ *}

{assign var="processor" value="LinkPoint API"}
<p>{$lang.text_linkpoint_notice|replace:"[processor]":$processor}</p>
<hr />

<div class="form-field">
	<label for="store">{$lang.store_number}:</label>
	<input type="text" name="payment_data[processor_params][store]" id="store" value="{$processor_params.store}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="secure_host">{$lang.secure_host}:</label>
	<input type="text" name="payment_data[processor_params][secure_host]" id="secure_host" value="{$processor_params.secure_host}" class="input-text" size="60" />&nbsp;:&nbsp;<input type="text" name="payment_data[processor_params][secure_port]" value="{$processor_params.secure_port}" class="input-text" size="5" />
</div>

<div class="form-field">
	<label for="cert_path">{$lang.certificate_filename}:</label>
	{$smarty.const.DIR_ROOT}/payments/certificates/<input type="text" name="payment_data[processor_params][cert_path]" id="cert_path" value="{$processor_params.cert_path}" class="input-text" size="32" />
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="transaction_type">{$lang.transaction_type}:</label>
	<select name="payment_data[processor_params][transaction_type]" id="transaction_type">
		<option value="SALE" {if $processor_params.transaction_type == "SALE"}selected="selected"{/if}>{$lang.sale}</option>
		<option value="PREAUTH" {if $processor_params.transaction_type == "PREAUTH"}selected="selected"{/if}>{$lang.preauth}</option>
		<option value="POSTAUTH" {if $processor_params.transaction_type == "POSTAUTH"}selected="selected"{/if}>{$lang.postauth}</option>
	</select>
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="DECLINE" {if $processor_params.test == "DECLINE"}selected="selected"{/if}>{$lang.test}: {$lang.declined}</option>
		<option value="GOOD" {if $processor_params.test == "GOOD"}selected="selected"{/if}>{$lang.test}: {$lang.approved}</option>
		<option value="LIVE" {if $processor_params.test == "LIVE"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="cvm_check">{$lang.cvm_check}:</label>
	<select name="payment_data[processor_params][cvm_check]" id="cvm_check">
		<option value="not_provided" {if $processor_params.cvm_check == "not_provided"}selected="selected"{/if}>{$lang.cvm_check_no}</option>
		<option value="provided" {if $processor_params.cvm_check == "provided"}selected="selected"{/if}>{$lang.cvm_check_yes}</option>
		<option value="illegible" {if $processor_params.cvm_check == "illegible"}selected="selected"{/if}>{$lang.cvm_check_unread}</option>
		<option value="not_present" {if $processor_params.cvm_check == "not_present"}selected="selected"{/if}>{$lang.cvm_check_no_cvm}</option>
	</select>
</div>
