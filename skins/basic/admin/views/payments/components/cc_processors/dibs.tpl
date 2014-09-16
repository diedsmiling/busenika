{* $Id: dibs.tpl 9907 2010-06-30 06:44:18Z klerik $ *}

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant]" id="merchant_id" value="{$processor_params.merchant}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="key1">{$lang.key1_for_md5}:</label>
	<input type="text" name="payment_data[processor_params][key1]" id="key1" value="{$processor_params.key1}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="key2">{$lang.key2_for_md5}:</label>
	<input type="text" name="payment_data[processor_params][key2]" id="key2" value="{$processor_params.key2}" class="input-text" size="60" />
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
		<option value="208"{if $processor_params.currency == '208'} selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="978"{if $processor_params.currency == '978'} selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="840"{if $processor_params.currency == '840'} selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="826"{if $processor_params.currency == '826'} selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="752"{if $processor_params.currency == '752'} selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="036"{if $processor_params.currency == '036'} selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="124"{if $processor_params.currency == '124'} selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="352"{if $processor_params.currency == '352'} selected="selected"{/if}>{$lang.currency_code_isk}</option>
		<option value="392"{if $processor_params.currency == '392'} selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="554"{if $processor_params.currency == '554'} selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="578"{if $processor_params.currency == '578'} selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="756"{if $processor_params.currency == '756'} selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="949"{if $processor_params.currency == '949'} selected="selected"{/if}>{$lang.currency_code_try}</option>
	</select>
</div>

<div class="form-field">
	<label for="lang">{$lang.default_language}:</label>
	<select name="payment_data[processor_params][lang]" id="lang">
		<option value="da"{if $processor_params.lang == 'da'} selected="selected"{/if}>{$lang.danish}</option>
		<option value="sv"{if $processor_params.lang == 'sv'} selected="selected"{/if}>{$lang.swedish}</option>
		<option value="no"{if $processor_params.lang == 'no'} selected="selected"{/if}>{$lang.norway}</option>
		<option value="en"{if $processor_params.lang == 'en'} selected="selected"{/if}>{$lang.english}</option>
		<option value="nl"{if $processor_params.lang == 'nl'} selected="selected"{/if}>{$lang.dutch}</option>
		<option value="de"{if $processor_params.lang == 'de'} selected="selected"{/if}>{$lang.german}</option>
		<option value="fr"{if $processor_params.lang == 'fr'} selected="selected"{/if}>{$lang.french}</option>
		<option value="fi"{if $processor_params.lang == 'fi'} selected="selected"{/if}>{$lang.finnish}</option>
		<option value="es"{if $processor_params.lang == 'es'} selected="selected"{/if}>{$lang.spanish}</option>
		<option value="it"{if $processor_params.lang == 'it'} selected="selected"{/if}>{$lang.italian}</option>
		<option value="fo"{if $processor_params.lang == 'fo'} selected="selected"{/if}>{$lang.faroese}</option>
		<option value="pl"{if $processor_params.lang == 'pl'} selected="selected"{/if}>{$lang.polish}</option>
	</select>
</div>

<div class="form-field">
	<label for="color">{$lang.color}:</label>
	<select name="payment_data[processor_params][color]" id="color">
		<option value="blue"{if $processor_params.color == 'blue'} selected="selected"{/if}>Blue</option>
		<option value="sand"{if $processor_params.color == 'sand'} selected="selected"{/if}>Sand</option>
		<option value="grey"{if $processor_params.color == 'grey'} selected="selected"{/if}>Grey</option>
	</select>
</div>

<div class="form-field">
	<label for="decorator">{$lang.decorator}:</label>
	<select name="payment_data[processor_params][decorator]" id="decorator">
		<option value="default"{if $processor_params.decorator == 'default'} selected="selected"{/if}>Default</option>
		<option value="basal"{if $processor_params.decorator == 'basal'} selected="selected"{/if}>Basal</option>
		<option value="rich"{if $processor_params.decorator == 'rich'} selected="selected"{/if}>Rich</option>
	</select>
</div>

<div class="form-field">
	<label for="skiplastpage">{$lang.skiplastpage}:</label>
	<select name="payment_data[processor_params][skiplastpage]" id="skiplastpage">
		<option value="yes"{if $processor_params.skiplastpage == 'yes'} selected="selected"{/if}>Yes</option>
		<option value="no"{if $processor_params.skiplastpage == 'no'} selected="selected"{/if}>No</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>