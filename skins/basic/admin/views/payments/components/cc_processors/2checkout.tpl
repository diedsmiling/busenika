{* $Id: 2checkout.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="r_url" value="payment_notification.notify?payment=2checkout"|fn_url:'C':'http'}
<p>{$lang.text_2checkout_notice|replace:"[return_url]":$r_url}</p>
<hr />

<div class="form-field">
	<label for="account_number">{$lang.account_number}:</label>
	<input type="text" name="payment_data[processor_params][account_number]" id="account_number" value="{$processor_params.account_number}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="secret_word">{$lang.secret_word}:</label>
	<input type="text" name="payment_data[processor_params][secret_word]" id="secret_word" value="{$processor_params.secret_word}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
