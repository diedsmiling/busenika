{* $Id: list.tpl 9743 2010-06-08 08:18:11Z lexa $ *}

{include file="addons/affiliate/views/payouts/components/payout_search.tpl"}

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $sort_order == "asc"}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8595;"}
{else}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8593;"}
{/if}
{if $settings.DHTML.customer_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	{if $settings.General.use_email_as_login != "Y"}
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=username&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.username}</a>{if $sort_by == "username"}{$sort_sign}{/if}</th>
	{/if}
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=partner&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.affiliate}</a>{if $sort_by == "partner"}{$sort_sign}{/if}</th>
	<th width="15%"><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=amount&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.amount}</a>{if $sort_by == "amount"}{$sort_sign}{/if}</th>
	<th width="15%"><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=date&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.date}</a>{if $sort_by == "date"}{$sort_sign}{/if}</th>
	<th width="20%"><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=status&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.status}</a>{if $sort_by == "status"}{$sort_sign}{/if}</th>
	<th width="10%">&nbsp;</th>
</tr>
{foreach from=$payouts key="payout_id" item="payout"}
<tr {cycle values=",class=\"table-row\""}>
	{if $settings.General.use_email_as_login != "Y"}
	<td>{$payout.user_login}</td>
	{/if}
	<td>{$payout.lastname} {$payout.firstname}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$payout.amount}</td>
	<td class="center">{$payout.date|date_format:"`$settings.Appearance.date_format` `$settings.Appearance.time_format`"}</td>
	<td class="center">
			{if $payout.status=="O"}{$lang.open}{else}{$lang.successful}{/if}
	</td>
	<td class="right">
		{include file="buttons/button.tpl" but_text=$lang.details but_href="`$controller`.update?payout_id=`$payout_id`" but_role="text"}
	</td>
</tr>
{foreachelse}
<tr>
	<td colspan="{if $settings.General.use_email_as_login != "Y"}5{else}6{/if}"><p class="no-items">{$lang.no_payouts_found}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="6">&nbsp;</td>
</tr>
</table>

{include file="common_templates/pagination.tpl"}

{capture name="mainbox_title"}{$lang.payouts}{/capture}
