{* $Id: subscriptions_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="subscriptions_search_form" method="get">

<table cellpadding="10" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label for="cname">{$lang.customer}:</label>
		<div class="break">
			<input type="text" name="cname" id="cname" value="{$search.cname}" size="30" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name=$dispatch}
		</div>
	</td>
	<td class="search-field">
		<label for="email">{$lang.email}:</label>
		<div class="break">
			<input type="text" name="email" id="email" value="{$search.email}" size="30" class="input-text" />
		</div>
	</td>
	<td class="nowrap search-field">
		<label for="price_from">{$lang.rb_price}&nbsp;({$currencies.$primary_currency.symbol}):</label>
		<div class="break">
			<input type="text" name="price_from" id="price_from" value="{$search.price_from}" size="3" class="input-text-price" />&nbsp;&ndash;&nbsp;<input type="text" name="price_to" value="{$search.price_to}" size="3" class="input-text-price" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label for="status">{$lang.rb_subscription_status}:</label>
	<select name="status" id="status">
		<option value="">--</option>
		<option value="A"{if $search.status == "A"} selected="selected"{/if}>{$lang.active}</option>
		<option value="D"{if $search.status == "D"} selected="selected"{/if}>{$lang.disabled}</option>
		<option value="U"{if $search.status == "U"} selected="selected"{/if}>{$lang.rb_unsubscribed}</option>
	</select>
</div>

<div class="search-field">
	<label for="type_period">{$lang.rb_period_type}:</label>
	<select name="period_type" id="type_period">
		<option value="">--</option>
		<option value="D"{if $search.period_type == "D"} selected="selected"{/if}>{$lang.date}</option>
		<option value="L"{if $search.period_type == "L"} selected="selected"{/if}>{$lang.last_order}</option>
		<option value="E"{if $search.period_type == "E"} selected="selected"{/if}>{$lang.end_date}</option>
	</select>
</div>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="orders_search_form"}
</div>

<div class="search-field">
	<label for="order_id">{$lang.order_id}:</label>
	<input type="text" name="order_id" id="order_id" value="{$search.order_id}" size="10" class="input-text" />
</div>

<div class="search-field">
	<label for="plan_id">{$lang.rb_recurring_plan_id}:</label>
	<input type="text" name="plan_id" id="plan_id" value="{$search.plan_id}" size="10" class="input-text" />
</div>

<div class="search-field">
	<label>{$lang.rb_subscribed_products}:</label>
	{include file="pickers/search_products_picker.tpl"}
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="subscriptions"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
