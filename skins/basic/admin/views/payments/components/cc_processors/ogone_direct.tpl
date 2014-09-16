{* $Id: ogone_direct.tpl 9843 2010-06-23 09:18:15Z klerik $ *}

<p>{$lang.text_ogonedirect_notice}</p>
<hr />

<div class="form-field">
	<label for="pspid">{$lang.pspid}:</label>
	<input type="text" name="payment_data[processor_params][pspid]" id="pspid" size="60" value="{$processor_params.pspid}" class="input-text" />
</div>

<div class="form-field">
	<label for="userid">{$lang.user_id}:</label>
	<input type="text" name="payment_data[processor_params][userid]" id="userid" size="60" value="{$processor_params.userid}" class="input-text" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="password" name="payment_data[processor_params][password]" id="password" size="60" value="{$processor_params.password}" class="input-text" />
</div>

<div class="form-field">
	<label for="sha_sign">{$lang.sha_sign}:</label>
	<input type="password" name="payment_data[processor_params][sha_sign]" id="sha_sign" size="60" value="{$processor_params.sha_sign}" class="input-text" />
</div>

<div class="form-field">
	<label for="use_new_sha_method">{$lang.use_new_sha_method}:</label>
	<input type="hidden" name="payment_data[processor_params][use_new_sha_method]" value="N" />
	<input type="checkbox" name="payment_data[processor_params][use_new_sha_method]" id="use_new_sha_method" value="Y"{if $processor_params.use_new_sha_method eq Y} checked="checked"{/if} />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="AUD"{if $processor_params.currency eq "AUD"} selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CAD"{if $processor_params.currency eq "CAD"} selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="CHF"{if $processor_params.currency eq "CHF"} selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="CZK"{if $processor_params.currency eq "CZK"} selected="selected"{/if}>{$lang.currency_code_czk}</option>
		<option value="DKK"{if $processor_params.currency eq "DKK"} selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="EUR"{if $processor_params.currency eq "EUR"} selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="FRF"{if $processor_params.currency eq "FRF"} selected="selected"{/if}>{$lang.currency_code_frf}</option>
		<option value="GBP"{if $processor_params.currency eq "GBP"} selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="HKD"{if $processor_params.currency eq "HKD"} selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="HUF"{if $processor_params.currency eq "HUF"} selected="selected"{/if}>{$lang.currency_code_huf}</option>
		<option value="ILS"{if $processor_params.currency eq "ILS"} selected="selected"{/if}>{$lang.currency_code_ils}</option>
		<option value="JPY"{if $processor_params.currency eq "JPY"} selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="LTL"{if $processor_params.currency eq "LTL"} selected="selected"{/if}>{$lang.currency_code_ltl}</option>
		<option value="LVL"{if $processor_params.currency eq "LVL"} selected="selected"{/if}>{$lang.currency_code_lvl}</option>
		<option value="MXN"{if $processor_params.currency eq "MXN"} selected="selected"{/if}>{$lang.currency_code_mxn}</option>
		<option value="NOK"{if $processor_params.currency eq "NOK"} selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="NZD"{if $processor_params.currency eq "NZD"} selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="PLN"{if $processor_params.currency eq "PLN"} selected="selected"{/if}>{$lang.currency_code_pln}</option>
		<option value="RUR"{if $processor_params.currency eq "RUR"} selected="selected"{/if}>{$lang.currency_code_rur}</option>
		<option value="SEK"{if $processor_params.currency eq "SEK"} selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="SGD"{if $processor_params.currency eq "SGD"} selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="SKK"{if $processor_params.currency eq "SKK"} selected="selected"{/if}>{$lang.currency_code_skk}</option>
		<option value="THB"{if $processor_params.currency eq "THB"} selected="selected"{/if}>{$lang.currency_code_thb}</option>
		<option value="TRY"{if $processor_params.currency eq "TRY"} selected="selected"{/if}>{$lang.currency_code_try}</option>
		<option value="USD"{if $processor_params.currency eq "USD"} selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="ZAR"{if $processor_params.currency eq "ZAR"} selected="selected"{/if}>{$lang.currency_code_zar}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="test"{if $processor_params.mode eq "test"} selected="selected"{/if}>{$lang.test}</option>
		<option value="live"{if $processor_params.mode eq "live"} selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" size="60" value="{$processor_params.order_prefix}" class="input-text" />
</div>
