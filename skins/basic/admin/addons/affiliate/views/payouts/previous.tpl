{* $Id: previous.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{capture name="mainbox"}

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	{if $settings.General.use_email_as_login == "Y"}
	<th width="10%"><a class="{$ajax_class}{if $sort_by == "email"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=email&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.email}</a></th>
	{else}
	<th width="10%"><a class="{$ajax_class}{if $sort_by == "username"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=username&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.username}</a></th>
	{/if}
	<th width="20%"><a class="{$ajax_class}{if $sort_by == "name"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=name&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.affiliate}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $sort_by == "balance"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=balance&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.balance_account}</a></th>
	<th width="10%">{$lang.last_payout}</th>
	<th width="10%"><a class="{$ajax_class}{if $sort_by == "avg"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=avg&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.avg_payout}</a></th>
	<th width="20%" colspan="2"><a class="{$ajax_class}{if $sort_by == "total"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=total&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.total_payouts}</a></th>
	<th width="15%">&nbsp;</th>
</tr>
{if $payouts}
{foreach from=$payouts key="user_id" item="payouts_data"}
<tr {cycle values="class=\"table-row\", "}>
	{if $settings.General.use_email_as_login == "Y"}
	<td><a href="{"partners.update?user_id=`$payouts_data.partner_id`"|fn_url}">{$payouts_data.email}</a></td>
	{else}
	<td><a href="{"partners.update?user_id=`$payouts_data.partner_id`"|fn_url}">{$payouts_data.user_login}</a></td>
	{/if}
	<td><a href="{"partners.update?user_id=`$payouts_data.partner_id`"|fn_url}">{$payouts_data.firstname} {$payouts_data.lastname}</a></td>
	<td>{include file="common_templates/price.tpl" value=$payouts_data.balance}</td>
	<td>{include file="common_templates/price.tpl" value=$payouts_data.last_amount}</td>
	<td>{include file="common_templates/price.tpl" value=$payouts_data.avg_amount}</td>
	<td width="10%">{include file="common_templates/price.tpl" value=$payouts_data.total_amount}</td>
	<td>
		{math equation="amount/max*width_perc" width_perc=100 max=$max_amount amount=$payouts_data.total_amount assign="w" format="%d"}
		{include file="views/sales_reports/components/graph_bar.tpl" bar_width="70px" value_width=$w}
	</td>
	<td class="right nowrap">
		{include file="buttons/button.tpl" but_role="tool" but_text=$lang.view_history but_href="payouts.manage?partner_id=`$payouts_data.partner_id`"}</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="8"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{include file="common_templates/pagination.tpl"}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.accounting_history content=$smarty.capture.mainbox}
