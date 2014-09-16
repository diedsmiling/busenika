{* $Id: gift_certificates_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="gift_certificates_search_form" method="get">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
		<label for="sender">{$lang.gift_cert_from}:</label>
		<div class="break">
			<input type="text" name="sender" id="sender" value="{$search.sender}" size="30" class="input-text" />
		</div>
	</td>
	<td class="search-field">
		<label for="recipient">{$lang.gift_cert_to}:</label>
		<div class="break">
			<input type="text" name="recipient" id="recipient" value="{$search.recipient}" size="30" class="input-text" />
		</div>
	</td>
	<td class="search-field">
		<label for="email">{$lang.email}:</label>
		<div class="break">
			<input type="text" name="email" id="email" value="{$search.email}" size="30" class="input-text" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[gift_certificates.manage]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label for="gift_cert_code">{$lang.gift_cert_code}:</label>
	<input type="text" name="gift_cert_code" id="gift_cert_code" value="{$search.gift_cert_code}" size="30" class="input-text" />
</div>

<div class="search-field">
	<label>{$lang.gift_certificate_status}:</label>
	{include file="common_templates/status.tpl" status=$search.status display="checkboxes" name="status" status_type=$smarty.const.STATUSES_GIFT_CERTIFICATE}
</div>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="gift_certificates_search_form"}
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="gift_certificates.manage" view_type="gift_certs"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
