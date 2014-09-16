{* $Id: innovative.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<p>{$lang.text_innovative_notice}</p>
<hr />

<div class="form-field">
	<label for="username">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][username]" id="username" value="{$processor_params.username}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{$lang.test}: {$lang.approved}</option>
		<option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>
