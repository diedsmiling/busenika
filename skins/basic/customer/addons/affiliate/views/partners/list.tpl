{* $Id: list.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{include file="common_templates/subheader.tpl" title=$lang.affiliate_information}

<div class="form-field">
	<label>{$lang.status}:</label>
	{if $partner.approved=="A"}{$lang.approved}{elseif $partner.approved=="D"}{$lang.declined}{else}{$lang.awaiting_approval}{/if}
</div>

{if $partner.plan}
<div class="form-field">
	<label>{$lang.affiliate_plan}:</label>
	<a href="{"affiliate_plans.list"|fn_url}">{$partner.plan}</a>
</div>
{/if}

<div class="form-field">
	<label>{$lang.balance_account}:</label>
	{include file="common_templates/price.tpl" value=$partner.balance}
</div>

<div class="form-field">
	<label>{$lang.total_payouts}:</label>
	{include file="common_templates/price.tpl" value=$partner.total_payouts}{if $partner.total_payouts} (<a href="{"payouts.list"|fn_url}">{$lang.view}</a>){/if}
</div>

{include file="common_templates/subheader.tpl" title=$lang.commissions_of_last_periods}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
{foreach from=$last_payouts item=period}
<tr>
	<td>{if $period.amount > 0}
	    {capture name="_href"}aff_statistics.commissions?action=search&amp;statistic_search%5Bpartner_id%5D={$partner.user_id}&amp;statistic_search%5Bstatus%5D%5BA%5D=A&amp;statistic_search%5Bstatus%5D%5BP%5D=P&amp;statistic_search%5Bperiod%5D=C&amp;from_Month={'m'|date:$period.range.start}&amp;from_Day={'d'|date:$period.range.start}&amp;from_Year={'Y'|date:$period.range.start}&amp;to_Month={'m'|date:$period.range.end}&amp;to_Day={'d'|date:$period.range.end}&amp;to_Year={'Y'|date:$period.range.end}{/capture}
	    <a href="{$smarty.capture._href|fn_url}">{/if}{$period.range.start|date_format:$settings.Appearance.date_format}{if $period.amount > 0}</a>{/if}
	</td>
	<td>{include file="common_templates/graph_bar.tpl" bar_width=300 value_width=$period.amount/$max_amount*100|round}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$period.amount}</td>
</tr>
{/foreach}
<tr>
	<td colspan="3" class="right">{$lang.total_commissions}:&nbsp;<strong>{include file="common_templates/price.tpl" value=$total_commissions}</strong></td>
</tr>
</table>

{include file="addons/affiliate/views/partners/components/partner_tree_root.tpl" partners=$partners}

{capture name="mainbox_title"}{$lang.balance_account}{/capture}