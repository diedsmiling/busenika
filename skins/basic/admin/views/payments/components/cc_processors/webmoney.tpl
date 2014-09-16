{* $Id: webmoney.tpl 0002 2009-05-08 18:44:04Z murzik $ *}

<div>
<p>{$lang.text_webmoney_notice}</p>
</div>
<hr />

<div class="form-field">
	<label for="lmi_payee_purse">{$lang.lmi_payee_purse}:</label>
	<input type="text" name="payment_data[processor_params][lmi_payee_purse]" id="lmi_payee_purse" value="{$processor_params.lmi_payee_purse}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="lmi_secret_key">{$lang.secret_key}:</label>
	<input type="text" name="payment_data[processor_params][lmi_secret_key]" id="lmi_secret_key" value="{$processor_params.lmi_secret_key}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="lmi_mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][lmi_mode]" id="lmi_mode">
		<option value="0" {if $processor_params.lmi_mode == "0"}selected="selected"{/if}>{$lang.live}</option>
		<option value="1" {if $processor_params.lmi_mode == "1"}selected="selected"{/if}>{$lang.test}</option>
	</select>
</div>

<div class="form-field">
	<label for="lmi_sim_mode">{$lang.lmi_sim_mode}:</label>
	<select name="payment_data[processor_params][lmi_sim_mode]" id="lmi_sim_mode">
		<option value="0" {if $processor_params.lmi_sim_mode == "0"}selected="selected"{/if}>{$lang.wm_success_mode}</option>
		<option value="1" {if $processor_params.lmi_sim_mode == "1"}selected="selected"{/if}>{$lang.wm_error_mode}</option>
		<option value="2" {if $processor_params.lmi_sim_mode == "2"}selected="selected"{/if}>{$lang.wm_combine_mode}</option>
	</select>
</div>

<div class="form-field">
	<label for="lmi_payment_desc">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][lmi_payment_desc]" id="lmi_payment_desc" value="{$processor_params.lmi_payment_desc}" class="input-text" size="60" />
</div>
