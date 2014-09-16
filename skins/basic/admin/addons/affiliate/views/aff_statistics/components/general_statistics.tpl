{* $Id: general_statistics.tpl 6893 2009-02-10 14:09:11Z angel $ *}

<p>
	{include file="buttons/button.tpl" but_text=$lang.general_statistics but_role="text" but_meta="text-button cm-combination" but_id="sw_general_statistics"}
</p>

<div id="general_statistics" class="hidden">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="40%">{$lang.action}</th>
	<th width="15%" class="right">{$lang.count}</th>
	<th class="right" width="15%">{$lang.sum}</th>
	<th class="right" width="15%">{$lang.avg}</th>
	<th class="right" width="15%">{$lang.affiliates}</th>
</tr>
{if $general_stats}
{foreach from=$payout_types key="payout_id" item="a"}
	{assign var="payout" value=$general_stats.$payout_id}
	{assign var="payout_var" value=$a.title}
<tr {cycle values="class=\"table-row\", "}>
	<td><strong>{$lang.$payout_var}</strong></td>
	<td class="right">{$payout.count|default:"0"}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$payout.sum|round:2}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$payout.avg|round:2}</td>
	<td class="right">{$payout.partners|default:"0"}</td>
</tr>
{/foreach}
{if $general_stats.total}
	{assign var="payout" value=$general_stats.total}
<tr class="manage-root-row strong">
	<td>{$lang.total}</td>
	<td class="right">{$payout.count|default:"0"}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$payout.sum|round:2}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$payout.avg|round:2}</td>
	<td class="right">{$payout.partners|default:"0"}</td>
</tr>
{/if}
{else}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{if $additional_stats}
	{foreach from=$additional_stats key="a_stats_name" item="a_stats_value"}
		<div class="form-field">
			<label>{$lang.$a_stats_name}:</label>
			{$a_stats_value}
		</div>
	{/foreach}
{/if}

</div>
