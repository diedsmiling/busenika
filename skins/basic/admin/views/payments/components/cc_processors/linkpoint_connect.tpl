{* $Id: linkpoint_connect.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<p>{$lang.text_linkpointc_notice|replace:"[return_url]":"<strong>`$config.http_location`/`$config.customer_index`</strong>"}</p>

<hr />

<div class="form-field">
	<label for="store">{$lang.store_number}:</label>
	<input type="text" name="payment_data[processor_params][store]" id="store" value="{$processor_params.store}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][prefix]" id="prefix" value="{$processor_params.prefix}" class="input-text"  size="60" />
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
		<option value="TEST" {if $processor_params.test == "TEST"}selected="selected"{/if}>{$lang.test}</option>
		<option value="LIVE" {if $processor_params.test == "LIVE"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

{*
<div class="form-field">
	<label for="cvm_check">{$lang.cvm_check}:</label>
	<select name="payment_data[processor_params][cvm_check]" id="cvm_check">
		<option value="not_provided" {if $processor_params.cvm_check == "not_provided"}selected="selected"{/if}>{$lang.cvm_check_no}</option>
		<option value="provided" {if $processor_params.cvm_check == "provided"}selected="selected"{/if}>{$lang.cvm_check_yes}</option>
		<option value="illegible" {if $processor_params.cvm_check == "illegible"}selected="selected"{/if}>{$lang.cvm_check_unread}</option>
		<option value="not_present" {if $processor_params.cvm_check == "not_present"}selected="selected"{/if}>{$lang.cvm_check_no_cvm}</option>
	</select>
</div>*}
