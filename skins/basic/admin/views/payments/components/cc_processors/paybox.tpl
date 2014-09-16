{* $Id: paybox.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="callback_url" value="payment_notification.result?payment=paybox"|fn_url:'C':'http'}
<p>{$lang.text_paybox_notice|replace:"[callback_url]":$callback_url}</p>
<hr />

<div class="form-field">
	<label for="site_num">{$lang.site_number}:</label>
	<input type="text" name="payment_data[processor_params][site_num]" id="site_num" size="60" value="{$processor_params.site_num}" class="input-text" />
</div>

<div class="form-field">
	<label for="rank_num">{$lang.rank_number}:</label>
	<input type="text" name="payment_data[processor_params][rank_num]" id="rank_num" size="60" value="{$processor_params.rank_num}" class="input-text" />
</div>

<div class="form-field">
	<label for="rank_num">{$lang.identifier}:</label>
	<input type="text" name="payment_data[processor_params][identifier]" id="rank_num" size="60" value="{$processor_params.identifier}" class="input-text" />
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="250"{if $processor_params.currency eq "250"} selected="selected"{/if}>{$lang.currency_code_frf}</option>
		<option value="978"{if $processor_params.currency eq "978"} selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="840"{if $processor_params.currency eq "840"} selected="selected"{/if}>{$lang.currency_code_usd}</option>
	</select>
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="FRA"{if $processor_params.language eq "FRA"} selected="selected"{/if}>{$lang.french}</option>
		<option value="GBR"{if $processor_params.language eq "GBR"} selected="selected"{/if}>{$lang.english}</option>
		<option value="DEU"{if $processor_params.language eq "DEU"} selected="selected"{/if}>{$lang.german}</option>
		<option value="ESP"{if $processor_params.language eq "ESP"} selected="selected"{/if}>{$lang.spanish}</option>
		<option value="ITA"{if $processor_params.language eq "ITA"} selected="selected"{/if}>{$lang.italian}</option>
		<option value="NLD"{if $processor_params.language eq "NLD"} selected="selected"{/if}>{$lang.dutch}</option>
		<option value="SWE"{if $processor_params.language eq "SWE"} selected="selected"{/if}>{$lang.swedish}</option>
	</select>
</div>
