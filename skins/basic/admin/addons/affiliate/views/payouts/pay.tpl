{* $Id: pay.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

{capture name="filter_section"}
<form action="{""|fn_url}" method="get" name="filter_form">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
	<tr valign="top">
		<td class="search-field nowrap">
			<label for="elm_amount_from">{$lang.payment_amount} ({$currencies.$primary_currency.symbol}):</label>
			<div class="break nowrap">
				<input type="text" name="amount_from" id="elm_amount_from" value="{$search.amount_from}" size="8" class="input-text-price" />
				&nbsp;&ndash;&nbsp;
				<input type="text" name="amount_to" value="{$search.amount_to}" size="8" class="input-text-price" />
			</div>
		</td>
		<td class="search-field nowrap">
			<label for="elm_min_payment">{$lang.checking_min_payment}:</label>
			<div class="break center">
				<input type="checkbox" name="min_payment" id="elm_min_payment" value="Y" {if $search.min_payment == "Y"}checked="checked"{/if} class="checkbox" />
			</div>
		</td>
		<td class="search-field nowrap">
			<label for="elm_last_payout">{$lang.checking_payment_period} ({$period_name}):</label>
			<div class="break center">
				<input type="checkbox" name="last_payout" id="elm_last_payout" value="Y" {if $search.last_payout == "Y"}checked="checked"{/if} class="checkbox" />
			</div>
		</td>
		<td class="buttons-container">
			{include file="buttons/search.tpl" but_name="dispatch[payouts.pay]" but_role="submit"}
		</td>
	</tr>
</table>

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.filter_section}

<form action="{""|fn_url}" method="post" name="pay_affiliates_form">

{include file="common_templates/pagination.tpl"}

<table cellpadding="1" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	{if $settings.General.use_email_as_login == "Y"}
	<th><a class="{$ajax_class}{if $search.sort_by == "email"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=email&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.email}</a></th>
	{else}
	<th><a class="{$ajax_class}{if $search.sort_by == "username"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=username&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.username}</a></th>
	{/if}
	<th><a class="{$ajax_class}{if $search.sort_by == "partner"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=partner&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.affiliate}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "amount"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.amount_of_approved_actions}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "awaiting_amount"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=awaiting_amount&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.amount_of_awaiting_approval_actions}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date_of_last_payment}</a></th>
	<th>&nbsp;</th>
</tr>
{if $partner_balances}
{foreach from=$partner_balances key="user_id" item="partner"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="partner_ids[]" value="{$user_id}" class="checkbox cm-item" /></td>
	{if $settings.General.use_email_as_login == "Y"}
	<td><a href="{"partners.update?user_id=`$partner.partner_id`"|fn_url}">{$partner.email}</a></td>
	{else}
	<td><a href="{"partners.update?user_id=`$partner.partner_id`"|fn_url}">{$partner.user_login}</a></td>
	{/if}
	<td><a href="{"partners.update?user_id=`$partner.partner_id`"|fn_url}">{$partner.firstname} {$partner.lastname}</a></td>
	<td>{include file="common_templates/price.tpl" value=$partner.amount} (<a href="{"aff_statistics.approve?partner_id=`$partner.partner_id`&amp;status[]=A"|fn_url}">{$lang.details}</a>)</td>
	<td>{include file="common_templates/price.tpl" value=$partner.awaiting_amount}{if $partner.awaiting_amount} (<a href="{"aff_statistics.approve?partner_id=`$partner.partner_id`&amp;status[]=N"|fn_url}">{$lang.details}</a>){/if}</td>
	<td>{if $partner.last_payout_date}{$partner.last_payout_date|date_format:$settings.Appearance.date_format}{else}&nbsp;---{/if}</td>
	<td class="right">
		{include file="buttons/button.tpl" but_role="edit" but_text=$lang.details but_href="payouts.add?partner_ids[]=$user_id"}
	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="7"><p>{$lang.no_data}</p></td>
</tr>
{/if}
</table>

{if $partner_balances}
	{include file="common_templates/table_tools.tpl" href="#pay_affiliates"}
{/if}

{include file="common_templates/pagination.tpl"}

{if $partner_balances}
	<div class="buttons-container buttons-bg">
		{include file="buttons/button.tpl" but_text=$lang.process_selected but_name="dispatch[payouts.do_m_pay_affiliates]" but_meta="cm-process-items" but_role="button_main"}
	</div>
{/if}

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.pay_affiliates content=$smarty.capture.mainbox}
