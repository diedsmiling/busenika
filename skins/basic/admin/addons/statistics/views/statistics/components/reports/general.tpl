{* $Id: general.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{capture name="table_chart"}

{include file="common_templates/pagination.tpl" div_id="general_pagination_content"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.date}</th>
	<th class="right">{$lang.total}</th>
	<th class="right">{$lang.robots}</th>
	<th class="right">{$lang.visitors}</th>
	<th class="right">{$lang.visitor_hosts}</th>
</tr>
{foreach from=$report_data.data key="date" item="stat"}
<tr {cycle values="class=\"table-row\","}>
	<td>
		{if $statistic_period == $smarty.const.STAT_PERIOD_DAY}
			{$stat.time_from|date_format:$settings.Appearance.date_format}
		{elseif $statistic_period == $smarty.const.STAT_PERIOD_HOUR}
			{$stat.time_from|date_format:"`$settings.Appearance.time_format`, `$settings.Appearance.date_format`"}
		{/if}
	</td>
	<td class="right">{$stat.total}</td>
	<td class="right">{if $stat.robots}<a href="{"statistics.visitors?section=general&amp;report=general&amp;time_from=`$stat.time_from`&amp;period=`$statistic_period`&amp;client_type=B"|fn_url}">{/if}{$stat.robots}{if $stat.robots}</a>{/if}</td>
	<td class="right">{if $stat.visitors}<a href="{"statistics.visitors?section=general&amp;report=general&amp;time_from=`$stat.time_from`&amp;period=`$statistic_period`&amp;client_type=U"|fn_url}">{/if}{$stat.visitors}{if $stat.visitors}</a>{/if}</td>
	<td class="right">{$stat.hosts}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl" div_id="general_pagination_content"}

{/capture}
{include file="addons/statistics/views/statistics/components/select_charts.tpl" chart_table=$smarty.capture.table_chart chart_type=$chart_type applicable_charts="line"}
