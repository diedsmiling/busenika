{* $Id: moneybookers.tpl 10399 2010-08-11 13:42:54Z klerik $ *}

{assign var="register_url" value="http://www.moneybookers.com/partners/cscart/"}
<p>{$lang.text_moneybookers_notice|replace:'[register_url]':$register_url}</p>
<hr />

<div class="form-field">
	<label for="pay_to_email_{$payment_id}">{$lang.pay_to_email}:</label>
	<input type="text" name="payment_data[processor_params][pay_to_email]" id="pay_to_email_{$payment_id}" value="{$processor_params.pay_to_email}" class="input-text" size="60" onchange="$('#validate_email_{$payment_id}').attr('href', '{"payment_notification.validate_email?payment=moneybookers&payment_id=`$payment_id`&email="|fn_url:'A':'rel':'&'}' +  $(this).val()); return false;" />&nbsp;<a href="{$index_script}?dispatch=payment_notification.validate_email&amp;payment=moneybookers&amp;payment_id={$payment_id}&amp;email={$processor_params.pay_to_email|escape:url}" onclick="jQuery.ajaxRequest($(this).attr('href'), {ldelim}method: 'GET', callback: fn_get_validate_email_{$payment_id}{rdelim}); return false;" id="validate_email_{$payment_id}">{$lang.validate_email}</a>
</div>

<div class="form-field">
	<label for="customer_id_{$payment_id}">{$lang.moneybookers_customer_id}:</label>
	<input type="text" name="payment_data[processor_params][customer_id]" id="customer_id_{$payment_id}" value="{$processor_params.customer_id}" class="input-text" size="60" />
</div>
<p>&nbsp;</p>
<div class="form-field">
	<label for="platform_{$payment_id}">{$lang.platform}:</label>
	<input type="text" name="payment_data[processor_params][platform]" id="platform_{$payment_id}" value="{$processor_params.platform}" class="input-text" size="60" /><span>&nbsp;({$lang.platform_eg})</span>
</div>

<div class="form-field">
	<label for="merchant_firstname_{$payment_id}">{$lang.merchant_firstname}:</label>
	<input type="text" name="payment_data[processor_params][merchant_firstname]" id="merchant_firstname_{$payment_id}" value="{$processor_params.merchant_firstname}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="merchant_lastname_{$payment_id}">{$lang.merchant_lastname}:</label>
	<input type="text" name="payment_data[processor_params][merchant_lastname]" id="merchant_lastname_{$payment_id}" value="{$processor_params.merchant_lastname}" class="input-text" size="60" />
</div>

<script type="text/javascript" language="javascript 1.2">
//<![CDATA[
function fn_get_validate_email_{$payment_id}(data)
{ldelim}
	$('#customer_id_{$payment_id}').val( data.customer_id_{$payment_id} );
{rdelim}
//]]>
</script>

<div class="form-field">
	<label>&nbsp;</label>
	<p>
	<a onclick="jQuery.ajaxRequest('{"payment_notification.activate?payment=moneybookers&payment_id=`$payment_id`&email="|fn_url:'A':'rel':'&'}' + $('#pay_to_email_{$payment_id}').val() + '&cust_id=' + $('#customer_id_{$payment_id}').val() + '&platform=' + $('#platform_{$payment_id}').val() + '&merchant_firstname=' + $('#merchant_firstname_{$payment_id}').val() + '&merchant_lastname=' + $('#merchant_lastname_{$payment_id}').val(), {ldelim}method: 'GET'{rdelim}); return false;" href="{$index_script}?dispatch=payment_notification.activate&amp;payment=moneybookers&amp;payment_id={$payment_id}&amp;email={$processor_params.pay_to_email|escape:url}&amp;cust_id={$processor_params.customer_id}&amp;platform={$processor_params.platform}">{$lang.activate_moneybookers_merchant_tools}</a>
	</p>
	<p>&nbsp;</p>
	<p>
	{$lang.text_moneybookers_activate_quick_checkout_short_explanation}
	</p>
	<p>
	{$lang.text_moneybookers_activate_quick_checkout_short_explanation_2}
	</p>
	<p>
	{$lang.text_mb_quick_checkout_payments}
	</p>
</div>

<div class="form-field">
	<label for="secret_word_{$payment_id}">{$lang.secret_word}:</label>
	<input type="text" name="payment_data[processor_params][secret_word]" id="secret_word_{$payment_id}" value="{$processor_params.secret_word}" class="input-text" size="60" onchange="$('#validate_secret_word_{$payment_id}').attr( 'href', '{"payment_notification.validate_secret_word?payment=moneybookers&payment_id=`$payment_id`&email="|fn_url:'A':'rel':'&'}' + $('#pay_to_email_{$payment_id}').val() + '&cust_id=' + $('#customer_id_{$payment_id}').val() + '&secret=' + $(this).val()); return false;" />&nbsp;<a href="{$index_script}?dispatch=payment_notification.validate_secret_word&amp;payment=moneybookers&amp;payment_id={$payment_id}&amp;email={$processor_params.pay_to_email|escape:url}&amp;cust_id={$processor_params.customer_id}&amp;secret={$processor_params.secret_word}" onclick="jQuery.ajaxRequest($(this).attr('href'), {ldelim}method: 'GET', callback: fn_get_validate_secret_word_{$payment_id}{rdelim}); return false;" id="validate_secret_word_{$payment_id}">{$lang.validate_secret_word}</a>

	<script type="text/javascript" language="javascript 1.2">
	//<![CDATA[
	function fn_get_validate_secret_word_{$payment_id}(data)
	{ldelim}
		$('#secret_word_{$payment_id}').val(data.secret_word_{$payment_id});
	{rdelim}
	//]]>
	</script>
	<p class="description">{$lang.text_moneybookers_secred_word_notice}</p>
</div>

<div class="form-field">
	<label for="quick_checkout_{$payment_id}">{$lang.forbid_moneybookers_ewallet}:</label>
	<select name="payment_data[processor_params][quick_checkout]" id="quick_checkout_{$payment_id}">
		<option value="N" {if $processor_params.quick_checkout == 'N'}selected="selected"{/if}>{$lang.no}</option>
		<option value="Y" {if $processor_params.quick_checkout == 'Y'}selected="selected"{/if}>{$lang.yes}</option>
	</select>
	<span>{$lang.text_use_moneybookers_ewallet_description}</span>
</div>

<div class="form-field">
	<label for="recipient_description_{$payment_id}">{$lang.recipient_description}:</label>
	<input type="text" name="payment_data[processor_params][recipient_description]" id="recipient_description_{$payment_id}" value="{$processor_params.recipient_description}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="language_{$payment_id}">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language_{$payment_id}">
		<option value="EN" {if $processor_params.language == 'EN'}selected="selected"{/if}>{$lang.english}</option>
		<option value="DE" {if $processor_params.language == 'DE'}selected="selected"{/if}>{$lang.german}</option>
		<option value="ES" {if $processor_params.language == 'ES'}selected="selected"{/if}>{$lang.spanish}</option>
		<option value="FR" {if $processor_params.language == 'FR'}selected="selected"{/if}>{$lang.french}</option>
		<option value="IT" {if $processor_params.language == 'IS'}selected="selected"{/if}>{$lang.italian}</option>

		<option value="PL" {if $processor_params.language == 'PL'}selected="selected"{/if}>{$lang.polish}</option>
		<option value="GR" {if $processor_params.language == 'GR'}selected="selected"{/if}>{$lang.greek}</option>
		<option value="RO" {if $processor_params.language == 'RO'}selected="selected"{/if}>{$lang.romanian}</option>
		<option value="RU" {if $processor_params.language == 'RU'}selected="selected"{/if}>{$lang.russian}</option>
		<option value="TR" {if $processor_params.language == 'TR'}selected="selected"{/if}>{$lang.turkish}</option>
		<option value="CN" {if $processor_params.language == 'CN'}selected="selected"{/if}>{$lang.chinese}</option>
		<option value="CZ" {if $processor_params.language == 'CZ'}selected="selected"{/if}>{$lang.czech}</option>
		<option value="NL" {if $processor_params.language == 'NL'}selected="selected"{/if}>{$lang.dutch}</option>
		<option value="DA" {if $processor_params.language == 'DA'}selected="selected"{/if}>{$lang.danish}</option>
		<option value="SV" {if $processor_params.language == 'SV'}selected="selected"{/if}>{$lang.swedish}</option>
		<option value="FI" {if $processor_params.language == 'FI'}selected="selected"{/if}>{$lang.finnish}</option>
	</select>
</div>

<div class="form-field">
	<label for="currency_{$payment_id}">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="currency_{$payment_id}">
		<option value="EUR"{if $processor_params.currency eq "EUR"} selected="selected"{/if}>{$lang.currency_code_eur}</option>
		<option value="USD"{if $processor_params.currency eq "USD"} selected="selected"{/if}>{$lang.currency_code_usd}</option>
		<option value="GBP"{if $processor_params.currency eq "GBP"} selected="selected"{/if}>{$lang.currency_code_gbp}</option>
		<option value="HKD"{if $processor_params.currency eq "HKD"} selected="selected"{/if}>{$lang.currency_code_hkd}</option>
		<option value="SGD"{if $processor_params.currency eq "SGD"} selected="selected"{/if}>{$lang.currency_code_sgd}</option>
		<option value="JPY"{if $processor_params.currency eq "JPY"} selected="selected"{/if}>{$lang.currency_code_jpy}</option>
		<option value="CAD"{if $processor_params.currency eq "CAD"} selected="selected"{/if}>{$lang.currency_code_cad}</option>
		<option value="AUD"{if $processor_params.currency eq "AUD"} selected="selected"{/if}>{$lang.currency_code_aud}</option>
		<option value="CHF"{if $processor_params.currency eq "CHF"} selected="selected"{/if}>{$lang.currency_code_chf}</option>
		<option value="DKK"{if $processor_params.currency eq "DKK"} selected="selected"{/if}>{$lang.currency_code_dkk}</option>
		<option value="SEK"{if $processor_params.currency eq "SEK"} selected="selected"{/if}>{$lang.currency_code_sek}</option>
		<option value="NOK"{if $processor_params.currency eq "NOK"} selected="selected"{/if}>{$lang.currency_code_nok}</option>
		<option value="ILS"{if $processor_params.currency eq "ILS"} selected="selected"{/if}>{$lang.currency_code_ils}</option>
		<option value="MYR"{if $processor_params.currency eq "MYR"} selected="selected"{/if}>Malaysian Ringgit</option>
		<option value="NZD"{if $processor_params.currency eq "NZD"} selected="selected"{/if}>{$lang.currency_code_nzd}</option>
		<option value="TRY"{if $processor_params.currency eq "TRY"} selected="selected"{/if}>{$lang.currency_code_try}</option>
		<option value="TWD"{if $processor_params.currency eq "TWD"} selected="selected"{/if}>Taiwan Dollar</option>
		<option value="THB"{if $processor_params.currency eq "THB"} selected="selected"{/if}>{$lang.currency_code_thb}</option>
		<option value="CZK"{if $processor_params.currency eq "CZK"} selected="selected"{/if}>{$lang.currency_code_czk}</option>
		<option value="HUF"{if $processor_params.currency eq "HUF"} selected="selected"{/if}>{$lang.currency_code_huf}</option>
		<option value="SKK"{if $processor_params.currency eq "SKK"} selected="selected"{/if}>{$lang.currency_code_skk}</option>
		<option value="EEK"{if $processor_params.currency eq "EEK"} selected="selected"{/if}>Estonian Kroon</option>
		<option value="BGN"{if $processor_params.currency eq "BGN"} selected="selected"{/if}>Bulgarian Leva</option>
		<option value="PLN"{if $processor_params.currency eq "PLN"} selected="selected"{/if}>{$lang.currency_code_pln}</option>
		<option value="ISK"{if $processor_params.currency eq "ISK"} selected="selected"{/if}>Iceland Krona</option>
		<option value="INR"{if $processor_params.currency eq "INR"} selected="selected"{/if}>Indian Rupee</option>
		<option value="LVL"{if $processor_params.currency eq "LVL"} selected="selected"{/if}>{$lang.currency_code_lvl}</option>
		<option value="KRW"{if $processor_params.currency eq "KRW"} selected="selected"{/if}>{$lang.currency_code_krw}</option>
		<option value="ZAR"{if $processor_params.currency eq "ZAR"} selected="selected"{/if}>{$lang.currency_code_zar}</option>
		<option value="RON"{if $processor_params.currency eq "RON"} selected="selected"{/if}>Romanian Leu New</option>
		<option value="HRK"{if $processor_params.currency eq "HRK"} selected="selected"{/if}>Croatian Kuna</option>
		<option value="LTL"{if $processor_params.currency eq "LTL"} selected="selected"{/if}>{$lang.currency_code_ltl}</option>
	</select>
	{assign var="cur_man" value="currencies.manage"|fn_url}
	{assign var="text_moneybookers_currs_notice" value=$lang.text_moneybookers_currs_notice|replace:"[link]":$cur_man}
	<p class="description">{$text_moneybookers_currs_notice}</p>
</div>

<div class="form-field">
	<label for="order_prefix_{$payment_id}">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix_{$payment_id}" value="{$processor_params.order_prefix}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="payment_methods_{$payment_id}">{$lang.payment_methods}:</label>
	<input type="hidden" name="payment_data[processor_params][payment_methods]" value="{$processor_params.payment_methods}" id="txtpm_{$payment_id}" />
	<select name="payment_data[processor_params][_payment_methods]" size="10" multiple="multiple" id="pm_{$payment_id}" onchange="fn_get_selected_values('pm_{$payment_id}', 'txtpm_{$payment_id}');">
		<option value="IDL">Ideal</option>
		<option value="PWY">Przelewy24</option>
		<option value="PWY5">ING Bank Śląski</option>
		<option value="PWY6">PKO BP (PKO Inteligo)</option>
		<option value="PWY7">Multibank (Multitransfer)</option>
		<option value="PWY14">Lukas Bank</option>
		<option value="PWY15">Bank BPH</option>
		<option value="PWY37">Kredyt Bank</option>
		<option value="PWY17">InvestBank</option>
		<option value="PWY18">PeKaO S.A.</option>
		<option value="PWY19">Citibank handlowy</option>
		<option value="PWY20">Bank Zachodni WBK (Przelew24)</option>
		<option value="PWY21">BGŻ</option>
		<option value="PWY22">Millenium</option>
		<option value="PWY26">Płacę z Inteligo</option>
		<option value="PWY25">mBank (mTransfer)</option>
		<option value="PWY28">Bank Ochrony Środowiska</option>
		<option value="PWY32">Nordea</option>
		<option value="PWY33">Fortis Bank</option>
		<option value="PWY36">Deutsche Bank PBC S.A,.</option>
		<option value="VSA">VISA</option>
		<option value="MSC">MASTERCARD</option>
		<option value="VSD">DELTA / VISA DEBIT</option>
		<option value="VSE">VISA ELECTRON</option>
		<option value="AMX">AMERICAN EXPRESS</option>
		<option value="DIN">DINERS</option>
		<option value="JCB">JCB</option>
		<option value="MAE">MAESTRO</option>
		<option value="LSR">LASER</option>
		<option value="SLO">SOLO</option>
		<option value="GCB">Carte Bleue</option>
		<option value="SFT">Sofortueberweisung</option>
		<option value="DID">direct debit</option>
		<option value="GIR">Giropay</option>
		<option value="ENT">Enets</option>
		<option value="EBT">Solo sweden</option>
		<option value="SO2">Solo finland</option>
		<option value="NPY">eps (NetPay)</option>
		<option value="PLI">POLi</option>
		<option value="DNK">Dankort</option>
		<option value="CSI">CartaSi</option>
		<option value="PSP">Postepay</option>
		<option value="EPY">ePay Bulgaria</option>
		<option value="BWI">BWI</option>
	</select>
	{$lang.multiple_selectbox_notice}
</div>

<script type="text/javascript" language="javascript 1.2">
//<![CDATA[

{literal}
function fn_get_selected_values(id, txtid)
{
	var txtSelectedValuesObj = document.getElementById(txtid);
	var selectedArray = new Array();
	var selObj = document.getElementById(id);
	var i;
	var count = 0;
	for (i = 0; i < selObj.options.length; i++) {
		if (selObj.options[i].selected) {
			selectedArray[count] = selObj.options[i].value;
			count++;
		}
	}
	txtSelectedValuesObj.value = selectedArray;
}

function fn_set_selected_values(id, txtid)
{
	var txtSelectedValuesObj = document.getElementById(txtid);
	var pm_str = txtSelectedValuesObj.value;
	pm_array = pm_str.split(',');
	var selectedArray = new Array();
	var selObj = document.getElementById(id);
	var i;
	var count = 0;
	for (i = 0; i < selObj.options.length; i++) {
		if (in_array(selObj.options[i].value, pm_array)) {
			selObj.options[i].selected = true;
		}
	}
}

function fn_set_all_values (id, txtid)
{
	var txtSelectedValuesObj = document.getElementById(txtid);
	var pm_str = txtSelectedValuesObj.value;
	pm_array = pm_str.split(',');
	var selectedArray = new Array();
	var selObj = document.getElementById(id);
	var i;
	var count = 0;
	for (i = 0; i < selObj.options.length; i++) {
		selObj.options[i].selected = true;
	}
}

function in_array(what, where) {
	var a = false;
	for (var i = 0; i < where.length; i++) {
		if (what == where[i]) {
			a = true;
			break;
		}
	}
	return a;
}
{/literal}

{if $processor_params && $processor_params|is_array}
fn_set_selected_values ('pm_{$payment_id}', 'txtpm_{$payment_id}');
{else}
fn_set_all_values ('pm_{$payment_id}', 'txtpm_{$payment_id}');
{/if}
fn_get_selected_values ('pm_{$payment_id}', 'txtpm_{$payment_id}')

//]]>
</script>

<div class="form-field">
	<label for="do_not_pass_logo">{$lang.do_not_pass_logo}:</label>
	<input type="hidden" name="payment_data[processor_params][do_not_pass_logo]" value="N" />
	<input type="checkbox" name="payment_data[processor_params][do_not_pass_logo]" value="Y" id="do_not_pass_logo" class="checkbox" {if $processor_params.do_not_pass_logo == "Y"}checked="checked"{/if} />
	<p class="description">{$lang.text_moneybookers_logo_notice}</p>
</div>

<div class="form-field">
	<label for="return_url_target_{$payment_id}">{$lang.return_url_target}:</label>
	<select name="payment_data[processor_params][return_url_target]" id="return_url_target_{$payment_id}">
		<option value="1" {if $processor_params.return_url_target == '1'}selected="selected"{/if}>_top</option>
		<option value="2" {if $processor_params.return_url_target == '2'}selected="selected"{/if}>_parent</option>
		<option value="3" {if $processor_params.return_url_target == '3'}selected="selected"{/if}>_self</option>
		<option value="4" {if $processor_params.return_url_target == '4'}selected="selected"{/if}>_blank</option>
	</select>
</div>

<div class="form-field">
	<label for="cancel_url_target_{$payment_id}">{$lang.cancel_url_target}:</label>
	<select name="payment_data[processor_params][cancel_url_target]" id="cancel_url_target_{$payment_id}">
		<option value="1" {if $processor_params.cancel_url_target == '1'}selected="selected"{/if}>_top</option>
		<option value="2" {if $processor_params.cancel_url_target == '2'}selected="selected"{/if}>_parent</option>
		<option value="3" {if $processor_params.cancel_url_target == '3'}selected="selected"{/if}>_self</option>
		<option value="4" {if $processor_params.cancel_url_target == '4'}selected="selected"{/if}>_blank</option>
	</select>
</div>

<div class="form-field">
	<label>&nbsp;</label>
	<p>&nbsp;</p>
	<p>
	{$lang.text_moneybookers_support}
	</p>
</div>
