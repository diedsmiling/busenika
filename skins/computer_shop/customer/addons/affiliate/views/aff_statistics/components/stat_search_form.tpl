{* $Id:	search_form.tpl	0 2006-07-28 19:49:30Z	seva $	*}

{capture name="section"}

<form action="{""|fn_url}" name="general_stats_search_form" method="get">

{include file="common_templates/period_selector.tpl" period=$statistic_search.period form_name="general_stats_search_form" tim_from=$statistic_search.start_date time_to=$statistic_search.end_date}

<div class="form-field">
	<label>{$lang.action}:</label>
	{html_checkboxes options=$payout_options name="statistic_search[payout_id]" selected=$statistic_search.payout_id columns=4}
</div>

<div class="form-field">
	<label>{$lang.amount} ({$currencies.$primary_currency.symbol}):</label>
	<input type="text" name="statistic_search[amount_from]" value="{$statistic_search.amount_from}" size="6" class="input-text-short" />&nbsp;-&nbsp;<input type="text" name="statistic_search[amount_to]" value="{$statistic_search.amount_to}" size="6" class="input-text-short" />
</div>

<div class="form-field">
	<label>{$lang.status}:</label>
	{html_checkboxes options=$status_options name="statistic_search[status]" selected=$statistic_search.status columns=3}
</div>

<div class="buttons-container">{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[$controller.$mode/search]"}</div>
</form>

{/capture}
{include file="common_templates/section.tpl" section_title=$lang.search section_content=$smarty.capture.section}
