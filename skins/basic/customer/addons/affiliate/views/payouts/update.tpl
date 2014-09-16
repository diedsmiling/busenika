{* $Id: update.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{if $mode == "add"}
{literal}
<script type="text/javascript">
	//<![CDATA[
	var payout_amounts = new Array();
	var _payout_amounts = new Array();
	var action_amounts = new Array();

	function fn_replace_payment_amount(partner_id, action_id, check_status)
	{
		if (typeof(payout_amounts[partner_id]) != undefined && typeof(action_amounts[action_id]) != undefined) {
			payout_amounts[partner_id] += (check_status)?action_amounts[action_id]:-action_amounts[action_id];
			document.getElementById('id_td_amount_'+partner_id).innerHTML = jQuery.formatNum(payout_amounts[partner_id] / currencies.secondary.coefficient, 2, false);
		}
	}
	//]]>
</script>
{/literal}
{/if}

{if $payouts}
{foreach from=$payouts key="user_id" item="payout" name="for_payouts"}

{if $mode == "add"}
<script type="text/javascript">
	//<![CDATA[
	payout_amounts[{$user_id}] = {$payout.amount};
	_payout_amounts[{$user_id}] = {$payout.amount};
	//]]>
</script>
<form action="{""|fn_url}" method="POST" name="payout_{$user_id}_form">
{/if}

<div class="form-field">
	<label>{$lang.affiliate}:</label>
	{$payout.partner.firstname} {$payout.partner.lastname}
</div>

<div class="form-field">
	<label>{$lang.email}:</label>
	{$payout.partner.email}
</div>

{if $payout.plan && $mode=="add"}
<div class="form-field">
	<label>{$lang.affiliate_plan}:</label>
	<a href="affiliate_plans.manage">{$payout.plan.name}</a>
</div>

<div class="form-field">
	<label>{$lang.minimum_commission_payment}:</label>
	{include file="common_templates/price.tpl" value=$payout.plan.min_payment}
</div>
{/if}

<div class="form-field">
	<label>{$lang.chart_period}:</label>
	{$payout.date_range.min|date_format:$settings.Appearance.date_format}&nbsp;-&nbsp;{$payout.date_range.max|date_format:$settings.Appearance.date_format}
</div>

<div class="form-field" id="id_td_amount_{$user_id}">
	<label>{$lang.amount}:</label>
	{include file="common_templates/price.tpl" value=$payout.amount}
</div>

{include file="common_templates/subheader.tpl" title=$lang.actions}

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

<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
<tr>
	{if $mode=="add"}
	<td width="1%">
		<input type="checkbox" name="check_all" value="Y" onclick="payout_amounts[{$user_id}] = (this.checked) ? _payout_amounts[{$user_id}] : 0.00; document.getElementById('id_td_amount_{$user_id}').innerHTML = jQuery.formatNum(payout_amounts[{$user_id}], 2, true)" title="{$lang.check_uncheck_all}" checked="checked" class="checkbox cm-check-items"/></td>
	{/if}
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=action&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.action}</a>{if $sort_by == "action"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=date&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.date}</a>{if $sort_by == "date"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=cost&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.cost}</a>{if $sort_by == "cost"}{$sort_sign}{/if}</th>
	<th>{$lang.customer}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=banner&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.banner}</a>{if $sort_by == "banner"}{$sort_sign}{/if}</th>
	<th>{$lang.additional_data}</th>
	<th>{$lang.status}</th>
</tr>
{foreach from=$payout.actions key="action_id" item="action"}
<tr {cycle values=",class=\"table-row\""}>
	{if $mode=="add"}
	<td>
		<script type="text/javascript">
			//<![CDATA[
			action_amounts[{$action_id}]={$action.amount};
			//]]>
		</script>
   		<input type="checkbox" name="action_ids[{$user_id}][{$action_id}]" value="Y" onclick="fn_replace_payment_amount({$user_id}, {$action_id}, this.checked);" checked="checked" class="check-item"/></td>
   	{/if}
	<td class="strong">&nbsp;{$action.title}</td>
	<td>{$action.date|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>{include file="common_templates/price.tpl" value=$action.amount|round:2}</td>
	<td>
		{if $action.customer_firstname || $action.customer_lastname}{$action.customer_firstname} {$action.customer_lastname}{/if}</td>
	<td>{if $action.banner}{$action.banner}{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
	<td>
		{include file="addons/affiliate/views/aff_statistics/components/additional_data.tpl" data=$action.data}</td>
	<td>{if $action.payout_id}{$lang.paidup}{elseif $action.approved=="Y"}{$lang.approved}{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="8"><p class="no-items">{$lang.text_no_items_found|replace:"[items]":$lang.actions}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="8">&nbsp;</td>
</tr>
</table>

{include file="common_templates/pagination.tpl"}

{if $mode=="add"}
	<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.add_payout but_name="dispatch[payouts.do_m_add_payouts]" but_meta="cm-process-items" but_role="action"}
	</div>
</form>
{/if}
{if !$smarty.foreach.for_payouts.last}<hr />{/if}
{/foreach}

{/if}

{capture name="mainbox_title"}{$lang.payout}{/capture}
