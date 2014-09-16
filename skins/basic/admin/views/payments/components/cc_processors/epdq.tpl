{* $Id: epdq.tpl 7719 2009-07-16 04:46:38Z zeke $ *}

{assign var="hash" value=`$processor_params.client_id``$processor_params.password`}
{assign var="hash_md" value=$hash|md5}
{assign var="notice" value=$lang.text_epdq_notice|replace:"[return_url]":"<strong>`$config.current_location`/`$config.customer_index`</strong>"}
<p>{$notice|replace:"[post_url]":"<strong>`$config.current_location`/payments/epdq.php?hash=$hash_md</strong>"}</p>
<hr />

<div class="form-field">
	<label for="client_id">{$lang.client_id}:</label>
	<input type="text" name="payment_data[processor_params][client_id]" id="client_id" value="{$processor_params.client_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.passphrase}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_name">{$lang.merchant_name}:</label>
	<input type="text" name="payment_data[processor_params][merchant_name]" id="merchant_name" value="{$processor_params.merchant_name}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="logo">{$lang.logo_link}:</label>
	<input type="text" name="payment_data[processor_params][logo]" id="logo" value="{$processor_params.logo}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="036" {if $processor_params.currency == "036"}selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="124" {if $processor_params.currency == "124"}selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="196" {if $processor_params.currency == "196"}selected="selected"{/if}>{$lang.currency_code_cyr}</option>
		<option value="203" {if $processor_params.currency == "203"}selected="selected"{/if}>{$lang.currency_code_czk}</option>
		<option value="208" {if $processor_params.currency == "208"}selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="233" {if $processor_params.currency == "233"}selected="selected"{/if}>{$lang.currency_code_eek}</option>
		<option value="978" {if $processor_params.currency == "978"}selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="344" {if $processor_params.currency == "344"}selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="348" {if $processor_params.currency == "348"}selected="selected"{/if}>{$lang.currency_code_huf}</option>
		<option value="352" {if $processor_params.currency == "352"}selected="selected"{/if}>{$lang.currency_code_isk}</option>
		<option value="356" {if $processor_params.currency == "356"}selected="selected"{/if}>{$lang.currency_code_inr}</option>
		<option value="376" {if $processor_params.currency == "376"}selected="selected"{/if}>{$lang.currency_code_ils}</option>
		<option value="392" {if $processor_params.currency == "392"}selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="428" {if $processor_params.currency == "428"}selected="selected"{/if}>{$lang.currency_code_lvl}</option>
		<option value="440" {if $processor_params.currency == "440"}selected="selected"{/if}>{$lang.currency_code_ltl}</option>
		<option value="554" {if $processor_params.currency == "554"}selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="578" {if $processor_params.currency == "578"}selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="985" {if $processor_params.currency == "985"}selected="selected"{/if}>{$lang.currency_code_pln}</option>
		<option value="702" {if $processor_params.currency == "702"}selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="703" {if $processor_params.currency == "703"}selected="selected"{/if}>{$lang.currency_code_skk}</option>
		<option value="705" {if $processor_params.currency == "705"}selected="selected"{/if}>{$lang.currency_code_sit}</option>
		<option value="410" {if $processor_params.currency == "410"}selected="selected"{/if}>{$lang.currency_code_krw}</option>
		<option value="752" {if $processor_params.currency == "752"}selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="756" {if $processor_params.currency == "756"}selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="826" {if $processor_params.currency == "826"}selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="840" {if $processor_params.currency == "840"}selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="682" {if $processor_params.currency == "682"}selected="selected"{/if}>{$lang.currency_code_sar}</option>
		<option value="710" {if $processor_params.currency == "710"}selected="selected"{/if}>{$lang.currency_code_zar}</option>
		<option value="764" {if $processor_params.currency == "764"}selected="selected"{/if}>{$lang.currency_code_thb}</option>
		<option value="784" {if $processor_params.currency == "784"}selected="selected"{/if}>{$lang.currency_code_aed}</option>
	</select>
</div>
