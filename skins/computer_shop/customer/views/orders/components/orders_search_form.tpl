{* $Id: orders_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<form action="{""|fn_url}" name="orders_search_form" method="get">

<div class="form-field">
	<label>{$lang.total}&nbsp;({$currencies.$primary_currency.symbol}):</label>
	<input type="text" name="total_from" value="{$search.total_from}" size="3" class="input-text-short" />&nbsp;-&nbsp;<input type="text" name="total_to" value="{$search.total_to}" size="3" class="input-text-short" />
</div>

<div class="form-field">
	<label>{$lang.order_status}:&nbsp;</label>
	{include file="common_templates/status.tpl" status=$search.status display="checkboxes" name="status"}
</div>

{include file="common_templates/period_selector.tpl" period=$search.period form_name="orders_search_form"}

{if $auth.user_id}
<div class="form-field">
	<label>{$lang.order_id}:</label>
	<input type="text" name="order_id" value="{$search.order_id}" size="10" class="input-text" />
</div>
{/if}

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[orders.search]"}
</div>
</form>
