{* $Id: stat_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="general_stats_search_form" method="get">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
		<label for="partner_id">{$lang.affiliate}:</label>
		<div class="break">
			<select name="partner_id" id="partner_id">
				<option value="0" {if !$search.partner_id}selected="selected"{/if}> -- </option>
				{html_options options=$partner_list selected=$search.partner_id}
			</select>
			{*<input type="text" name="partner_name" id="partner_id" value="{$search.partner_name}" class="input-text-medium" />*}
		</div>
	</td>
	<td class="search-field">
		<label for="plan_id">{$lang.plan}:</label>
		<div class="break">
			<select name="plan_id" id="plan_id">
				<option value="0" {if !$search.plan_id}selected="selected"{/if}> -- </option>
				{html_options options=$list_plans selected=$search.plan_id}
			</select>
		</div>
	</td>
	<td class="search-field nowrap">
		<label for="amount_from">{$lang.amount} ({$currencies.$primary_currency.symbol}):</label>
		<div class="break">
			<input type="text" name="amount_from" id="amount_from" value="{$search.amount_from}" size="6" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="amount_to" value="{$search.amount_to}" size="6" class="input-text" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$controller`.`$mode`]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="general_stats_search_form" time_from=$search.start_date time_to=$search.end_date}
</div>

<div class="search-field clear">
	<label>{$lang.action}:</label>
	<div class="float-left">
		{html_checkboxes options=$payout_options name="payout_id" selected=$search.payout_id columns=4}
	</div>
</div>

<div class="search-field">
	<label for="zero_actions">{$lang.show_zero_actions}:</label>
	<select name="zero_actions" id="zero_actions">
		<option value="0" {if !$search.zero_actions}selected="selected"{/if}>{$lang.not_show}</option>
		<option value="S" {if $search.zero_actions == "S"}selected="selected"{/if}>{$lang.show}</option>
		<option value="Y" {if $search.zero_actions == "Y"}selected="selected"{/if}>{$lang.only_zero_actions}</option>
	</select>
</div>

<div class="search-field clear">
	<label>{$lang.status}:</label>
	<div class="float-left">
		{html_checkboxes options=$status_options name="status" selected=$search.status columns=3}
	</div>
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="aff_stats"}

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
