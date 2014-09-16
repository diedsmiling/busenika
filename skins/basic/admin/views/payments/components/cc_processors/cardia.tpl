{* $Id: cardia.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="r_url" value="payment_notification.notify?payment=cardia"|fn_url:'C':'http'}
<p>{$lang.text_cardia_notice|replace:"[return_url]":$r_url}</p>
<input type="hidden" name="payment_data[processor_params][postbackurl]" value="{$r_url}" />
<hr />

<div class="form-field">
	<label for="merchant_token">{$lang.merchant_token}:</label>
	<input type="text" name="payment_data[processor_params][merchanttoken]" id="merchant_token" value="{$processor_params.merchanttoken}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="store">{$lang.store_name}:</label>
	<input type="text" name="payment_data[processor_params][store]" id="store" value="{$processor_params.store}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="NOK"{if $processor_params.currency eq "NOK"} selected="selected"{/if}>{$lang.currency_code_nok}
		<option value="SEK"{if $processor_params.currency eq "SEK"} selected="selected"{/if}>{$lang.currency_code_sek}
		<option value="DKK"{if $processor_params.currency eq "DKK"} selected="selected"{/if}>{$lang.currency_code_dkk}
		<option value="EUR"{if $processor_params.currency eq "EUR"} selected="selected"{/if}>{$lang.currency_code_eur}
		<option value="GBP"{if $processor_params.currency eq "GBP"} selected="selected"{/if}>{$lang.currency_code_gbp}
		<option value="USD"{if $processor_params.currency eq "USD"} selected="selected"{/if}>{$lang.currency_code_usd}
	</select>
</div>

<div class="form-field">
	<label for="skip_first_page">{$lang.skip_first_page}:</label>
	<input type="hidden" name="payment_data[processor_params][skipFirstPage]" value="false" />
	<input type="checkbox" name="payment_data[processor_params][skipFirstPage]" id="skip_first_page" value="true" {if $processor_params.skipFirstPage == "true"} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="skip_last_page">{$lang.skip_last_page}:</label>
	<input type="hidden" name="payment_data[processor_params][skipLastPage]" value="false" />
	<input type="checkbox" name="payment_data[processor_params][skipLastPage]" id="skip_last_page" value="true" {if $processor_params.skipLastPage == "true"} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="is_on_hold">{$lang.is_on_hold}:</label>
	<input type="hidden" name="payment_data[processor_params][isOnHold]" value="false" />
	<input type="checkbox" name="payment_data[processor_params][isOnHold]" id="is_on_hold" value="true" {if $processor_params.isOnHold == "true"} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="use_third_party_security">{$lang.use_third_party_security}:</label>
	<input type="hidden" name="payment_data[processor_params][useThirdPartySecurity]" value="false" />
	<input type="checkbox" name="payment_data[processor_params][useThirdPartySecurity]" id="use_third_party_security" value="true" {if $processor_params.useThirdPartySecurity == "true"} checked="checked" {/if} />
</div>
