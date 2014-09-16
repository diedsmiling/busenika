{* $Id: cresecure.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

<p>{$lang.text_cresecure_notice}</p>
<hr />

<div class="form-field">
	<label for="cresecureid">{$lang.CRESecureID}:</label>
	<input type="text" name="payment_data[processor_params][cresecureid]" id="cresecureid" value="{$processor_params.cresecureid}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="cresecureapitoken">{$lang.CRESecureAPIToken}:</label>
	<input type="text" name="payment_data[processor_params][cresecureapitoken]" id="cresecureapitoken" value="{$processor_params.cresecureapitoken}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="test">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][test]" id="test">
		<option value="test"{if $processor_params.test == 'test'} selected="selected"{/if}>{$lang.test}</option>
		<option value="live"{if $processor_params.test == 'live'} selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		{foreach from=""|fn_get_simple_currencies key="code" item="currency"}
			<option value="{$code}"{if $processor_params.currency == $code} selected="selected"{/if}>{$currency|escape}</option>
		{/foreach}
	</select>
</div>

<fieldset>
{include file="common_templates/subheader.tpl" title="`$lang.cresecure_allowed_types`"}
<div class="form-field">
	<label for="allowed_types_visa">Visa:</label>
	<input type="checkbox" name="payment_data[processor_params][allowed_types][visa]" id="allowed_types_visa" value="Visa"{if $processor_params.allowed_types.visa} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="allowed_types_mastercard">MasterCard:</label>
	<input type="checkbox" name="payment_data[processor_params][allowed_types][mastercard]" id="allowed_types_mastercard" value="MasterCard"{if $processor_params.allowed_types.mastercard} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="allowed_types_amx">American Express:</label>
	<input type="checkbox" name="payment_data[processor_params][allowed_types][amx]" id="allowed_types_amx" value="American Express"{if $processor_params.allowed_types.amx} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="allowed_types_discover">Discover:</label>
	<input type="checkbox" name="payment_data[processor_params][allowed_types][discover]" id="allowed_types_discover" value="Discover"{if $processor_params.allowed_types.discover} checked="checked"{/if} />
</div>

</fieldset>