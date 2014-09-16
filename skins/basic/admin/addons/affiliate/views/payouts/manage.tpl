{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="addons/affiliate/views/payouts/components/payout_search.tpl"}

<form action="{""|fn_url}" method="post" name="payouts_form">

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}

{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="55%"><a class="{$ajax_class}{if $search.sort_by == "partner"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=partner&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.affiliate}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "amount"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.amount}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>
{if $payouts}
{foreach from=$payouts key="payout_id" item="payout"}
<tr {cycle values="class=\"table-row\", "}>
	<td width="1%" class="center">
		<input type="checkbox" name="payout_ids[]" value="{$payout.payout_id}" class="checkbox cm-item" /></td>
	<td><a href="{"partners.update?user_id=`$payout.partner_id`"|fn_url}">{$payout.lastname} {$payout.firstname}</a></td>
	<td><input type="hidden" name="payouts[{$payout_id}][amount]" value="{$payout.amount}" />{include file="common_templates/price.tpl" value=$payout.amount}</td>
	<td class="center">{$payout.date|date_format:"`$settings.Appearance.date_format` `$settings.Appearance.time_format`"}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$payout_id status=$payout.status items_status="O: `$lang.open`, S: `$lang.successful`" object_id_name="payout_id" table="affiliate_payouts"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"payouts.delete?payout_id=`$payout.payout_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$payout_id tools_list=$smarty.capture.tools_items href="payouts.update?payout_id=`$payout_id`" link_text=$lang.view}
	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="7"><p>{$lang.no_data}</p></td>
</tr>
{/if}
</table>

{if $payouts}
	{include file="common_templates/table_tools.tpl" href="#payouts"}
{/if}

{include file="common_templates/pagination.tpl"}

{if $payouts}
	<div class="buttons-container buttons-bg">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[payouts.do_delete]" but_role="button_main" but_meta="cm-process-items cm-confirm"}
	</div>
{/if}

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.payouts content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
