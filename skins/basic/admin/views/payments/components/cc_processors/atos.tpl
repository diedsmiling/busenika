{* $Id: atos.tpl 10055 2010-07-14 10:15:19Z klerik $ *}
{assign var="home_path" value=$processor_params.atos_files}
{if $processor_params.atos_files|strstr:$smarty.const.DIR_ROOT}
{assign var="path_len" value=$smarty.const.DIR_ROOT|strlen}
{assign var="http_path_" value=$processor_params.atos_files|substr:$path_len}
{assign var="http_path" value="`$config.current_location`$http_path_"}
{/if}
{assign var="auto_url" value="payment_notification.result?payment=atos"|fn-url:'C':'http'}
{assign var="ok_url" value="payment_notification.process?payment=atos"|fn_url:'C':'http'}
<p>{$lang.text_atos_notice|replace:"[home_path]":$home_path|replace:"[http_path]":$http_path|replace:"[auto_url]":$auto_url|replace:"[ok_url]":$ok_url}</p>

{if $processor_params.atos_files|default:"`$smarty.const.DIR_ROOT`/payments/atos_files/"|strlen > 59}
<p><strong>{$lang.text_atos_warning}</strong></p>
{/if}
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text"  size="60" />
</div>

<div class="form-field">
	<label for="country">{$lang.country}:</label>
	<select name="payment_data[processor_params][country]" id="country">
		<option value="fr"{if $processor_params.country == "fr"} selected="selected"{/if}>France</option>
		<option value="be"{if $processor_params.country == "be"} selected="selected"{/if}>Belgium</option>
		<option value="de"{if $processor_params.country == "de"} selected="selected"{/if}>Germany</option>
		<option value="it"{if $processor_params.country == "it"} selected="selected"{/if}>Italy</option>
		<option value="es"{if $processor_params.country == "es"} selected="selected"{/if}>Spain</option>
	</select>
</div>

<div class="form-field">
	<label for="currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency">
		<option value="978"{if $processor_params.currency == "978"} selected="selected"{/if}>Euro</option>
		<option value="840"{if $processor_params.currency == "840"} selected="selected"{/if}>American Dollar</option>
	</select>
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="fr"{if $processor_params.language == "fr"} selected="selected"{/if}>French</option>
		<option value="en"{if $processor_params.language == "en"} selected="selected"{/if}>English</option>
		<option value="ge"{if $processor_params.language == "ge"} selected="selected"{/if}>German</option>
	</select>
</div>

<div class="form-field">
	<label for="atos_files">{$lang.path_to_files}:</label>
	<input type="text" name="payment_data[processor_params][atos_files]" id="atos_files" value="{$processor_params.atos_files|default:"`$smarty.const.DIR_ROOT`/payments/atos_files"}" class="input-text" size="60" />
</div>
