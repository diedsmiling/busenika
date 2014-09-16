{* $Id: payjunction.tpl 6560 2008-12-15 11:41:36Z zeke $ *}

<p>{$lang.text_payjunction_notice}</p>
<hr />

<div class="form-field">
	<label for="username">{$lang.username}:</label>
	<input type="text" name="payment_data[processor_params][username]" id="username" value="{$processor_params.username}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="password" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="type">{$lang.type}:</label>
	<select name="payment_data[processor_params][type]" id="type">
		<option value="AUTORIZATION" {if $processor_params.type eq "AUTORIZATION"} selected="selected"{/if}>{$lang.authorization}</option>
		<option value="AUTORIZATION_CAPTURE" {if $processor_params.type eq "AUTORIZATION_CAPTURE"} selected="selected"{/if}>{$lang.authorization} + {$lang.capture}</option>
	</select>
</div>
