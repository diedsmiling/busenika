{* $Id: assist.tpl 0012 2009-05-17 11:44:04Z murzik $ *}

<div> 
{$lang.text_assist_notice}
</div>
<hr />

<div class="form-field">
	<label for="shop_idp">{$lang.shop_id}:</label>
	<input type="text" name="payment_data[processor_params][shop_idp]" id="shop_idp" value="{$processor_params.shop_idp}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="0" {if $processor_params.language == "0"}selected="selected"{/if}>{$lang.russian}</option>
		<option value="1" {if $processor_params.language == "1"}selected="selected"{/if}>{$lang.english}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="T" {if $processor_params.mode == "T"}selected="selected"{/if}>{$lang.test}</option>
		<option value="L" {if $processor_params.mode == "L"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="secret_key">{$lang.secret_key}:</label>
	<input type="text" name="payment_data[processor_params][secret_key]" id="secret_key" value="{$processor_params.secret_key}" class="input-text" />
	<p class="description">{$lang.text_secret_key_notice}</p>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>
