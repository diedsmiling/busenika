{* $Id: payout_search.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form name="payout_search_form" action="{""|fn_url}" method="get">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
		<label for="elm_partner_id">{$lang.affiliate}:</label>
		<div class="break">
			<select name="partner_id" id="elm_partner_id">
				<option value="0" {if !$search.partner_id}selected="selected"{/if}> -- </option>
				{html_options options=$partner_list selected=$search.partner_id}
			</select>
		</div>
	</td>
	<td class="search-field">
		<label for="elm_status">{$lang.status}:</label>
		<div class="break">
			<select name="status" id="elm_status">
				<option value=""> -- </option>
				<option value="O" {if $search.status == "O"}selected="selected"{/if}>{$lang.open}</option>
				<option value="S" {if $search.status == "S"}selected="selected"{/if}>{$lang.successful}</option>
			</select>
		</div>
	</td></td>
	<td class="search-field nowrap">
		<label for="amount_from">{$lang.amount} ({$currencies.$primary_currency.symbol}):</label>
		<div class="break">
			<input type="text" name="amount_from" size="7" value="{$search.amount_from}" class="input-text" id="amount_from" />&nbsp;&ndash;&nbsp;
			<input type="text" name="amount_to" size="7" value="{$search.amount_to}" class="input-text" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[payouts.manage]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="payout_search_form" time_from=$search.time_from time_to=$search.time_to}
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="payouts.manage" view_type="payouts"}

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
