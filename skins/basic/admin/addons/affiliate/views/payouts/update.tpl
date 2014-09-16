{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}

{if $mode == "add"}
{literal}
<script type="text/javascript">
	//<![CDATA[
	var payout_amounts = new Array();
	var _payout_amounts = new Array();
	var action_amounts = new Array();

	function fn_replace_payment_amount(partner_id, action_id, check_status)
	{
		if (typeof(payout_amounts[partner_id]) != 'undefined' && typeof(action_amounts[action_id]) != 'undefined') {
			payout_amounts[partner_id] += (check_status) ? action_amounts[action_id] : -action_amounts[action_id];
			$('#id_td_amount_'+partner_id).html(jQuery.formatNum(payout_amounts[partner_id] / currencies.secondary.coefficient, 2, false));
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
	<a href="{"partners.update?user_id=`$user_id`"|fn_url}">{$payout.partner.firstname} {$payout.partner.lastname}</a>
</div>

<div class="form-field">
	<label>{$lang.email}:</label>
	{$payout.partner.email}
</div>

{if $payout.plan && $mode == "add"}
<div class="form-field">
	<label>{$lang.plan}:</label>
	<a href="{"affiliate_plans.update?plan_id=`$payout.plan.plan_id`"|fn_url}">{$payout.plan.name}</a>
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

<div class="form-field">
	<label>{$lang.amount}:</label>
	<span id="id_td_amount_{$user_id}">{include file="common_templates/price.tpl" value=$payout.amount}</span>
</div>

{include file="common_templates/subheader.tpl" title=$lang.actions}

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" class="table sortable">
<tr>
	{if $mode == "add"}
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" onclick="payout_amounts[{$user_id}] = (this.checked) ? _payout_amounts[{$user_id}] : 0.00; document.getElementById('id_td_amount_{$user_id}').innerHTML = jQuery.formatNum(payout_amounts[{$user_id}], 2, true)" title="{$lang.check_uncheck_all}" checked="checked" class="checkbox cm-check-items" /></th>
	{/if}
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "action"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=action&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.action}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "cost"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=cost&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.cost}</a></th>
	<th width="20%">
		<ul>
			<li>{$lang.customer}</li>
			<li>({$lang.ip_address})</li>
		</ul></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "banner"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=banner&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.banner}</a></th>
	<th width="30%">{$lang.additional_data}</th>
	<th width="7%">{$lang.status}</th>
</tr>
{if $payout.actions}
{foreach from=$payout.actions key="action_id" item="action"}
<tr {cycle values="class=\"table-row\", "}>
	{if $mode == "add"}
	<td>
		<script type="text/javascript">
			//<![CDATA[
			action_amounts[{$action_id}]={$action.amount};
			//]]>
		</script>
   		<input type="checkbox" name="action_ids[{$user_id}][{$action_id}]" value="Y" onclick="fn_replace_payment_amount({$user_id}, {$action_id}, this.checked);" checked="checked" class="checkbox cm-item" /></td>
   	{/if}
	<td>&nbsp;<strong>{$action.title}</strong></td>
	<td>{$action.date|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>{include file="common_templates/price.tpl" value=$action.amount|round:2}</td>
	<td>
		<ul>
			{if $action.customer_firstname || $action.customer_lastname}
				<li><a href="{"profiles.update?user_id=`$action.customer_id`"|fn_url}">{$action.customer_firstname} {$action.customer_lastname}</a></li>{/if}
			{if $action.ip}<li><em>({$action.ip})</em></li>{/if}
		</ul></td>
	<td>{if $action.banner}<a href="{"`banners_manager.update?banner_id=`action.banner_id`"|fn_url}">{$action.banner}</a>{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
	<td>
		{include file="addons/affiliate/views/payouts/components/additional_data.tpl" data=$action.data assign="additional_data"}{if $additional_data|trim}{$additional_data}{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
	</td>
	<td>{if $action.payout_id}{$lang.paidup}{elseif $action.approved == "Y"}{$lang.approved}{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="{if $mode == "add"}8{else}7{/if}"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{if $payout.actions && $mode == "add"}
	{include file="common_templates/table_tools.tpl" href="#payouts"}
{/if}

{include file="common_templates/pagination.tpl"}

{if $mode == "add"}
	<div class="buttons-container buttons-bg">
		{include file="buttons/save_cancel.tpl" but_text=$lang.add_payout hide_second_button=true but_name="dispatch[payouts.m_add_payouts]"}
	</div>
</form>
{/if}
{if !$smarty.foreach.for_payouts.last}<hr />{/if}
{/foreach}

{/if}
{/capture}
{if $mode != "add"}
	{include file="common_templates/view_tools.tpl" url="payouts.update?payout_id="}
{/if}
{include file="common_templates/mainbox.tpl" title=$lang.payout content=$smarty.capture.mainbox tools=$smarty.capture.view_tools}