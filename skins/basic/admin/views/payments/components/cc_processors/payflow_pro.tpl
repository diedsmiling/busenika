{* $Id: payflow_pro.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<hr />

<div class="form-field">
	<label for="username">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][username]" id="username" size="60" value="{$processor_params.username}" class="input-text" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" size="60" value="{$processor_params.password}" class="input-text" />
</div>

<div class="form-field">
	<label for="vendor">{$lang.vendor}:</label>
	<input type="text" name="payment_data[processor_params][vendor]" id="vendor" size="60" value="{$processor_params.vendor}" class="input-text" />
</div>

<div class="form-field">
	<label for="partner">{$lang.partner}:</label>
	<input type="text" name="payment_data[processor_params][partner]" id="partner" size="60" value="{$processor_params.partner}" class="input-text" />
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" size="60" value="{$processor_params.order_prefix}" class="input-text" />
</div>

<div class="form-field">
	<label for="country">{$lang.server_being_used}:</label>
	<select name="payment_data[processor_params][country]" id="country">
		<option value="AU" {if $processor_params.country eq "AU"} selected="selected"{/if}>AU</option>
		<option value="US" {if $processor_params.country eq "US"} selected="selected"{/if}>US</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test"{if $processor_params.mode eq "test"} selected="selected"{/if}>{$lang.test}</option>
		<option value="live"{if $processor_params.mode eq "live"} selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
