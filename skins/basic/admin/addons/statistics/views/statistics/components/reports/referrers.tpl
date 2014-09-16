{* $Id: referrers.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{capture name="tabsbox"}

<div id="content_{$report_data.report}">
	{if $report_data.data}
	{capture name="table_chart"}

	<table cellpadding="2" cellspacing="1" border="0">
	{foreach from=$report_data.data item="row" name="stat"}
	<tr {cycle values=",class=\"table-row\""}>
		<td>{$smarty.foreach.stat.iteration}</td>
		{if $report_data.report == "came_to" || $report_data.report == "came_from"}
		<td><a href="{$search_engines[$row.engine].url}" target="_blank">{$row.engine}</a></td>
		{/if}
		<td>
			<div class="no-scroll">
				{if $report_data.report == "all_referrers" || $report_data.report == "by_domain"}
					<a href="{$row.label}" target="_blank">{$row.label}</a>
				{elseif $report_data.report == "by_search_engine"}
					{assign var="field_value" value=$row.label}
					<a href="{$search_engines[$row.label].url}" target="_blank">{$row.label}</a>
				{else}
					{$row.label|unescape}
				{/if}
				{if $report_data.report == "all_referrers" && $row.phrase}<p class="small-note">{$lang.phrase}: {$row.phrase|unescape}</p>{/if}
			{include file="views/sales_reports/components/graph_bar.tpl" bar_width="400px" value_width=$row.percent|round}
			</div>
		</td>
		<td align="right">

			{if $report_data.report == "all_referrers" || $report_data.report == "by_domain"}
				{assign var="object_code" value=$row.label}
			{elseif $report_data.report == "by_search_engine"}
				{assign var="object_code" value=$row.engine_id}
			{elseif $report_data.report == "search_words"}
				{assign var="object_code" value=$row.phrase_id}
			{elseif $report_data.report == "came_to"}
				{assign var="object_code" value="`$row.label`&amp;engine_id=`$row.engine_id`"}
			{elseif $report_data.report == "came_from"}
				{assign var="object_code" value="`$row.phrase_id`&amp;engine_id=`$row.engine_id`"}
			{/if}

			<a href="{"statistics.visitors?section=referrers&amp;report=`$report_data.report`&amp;object_code=`$object_code`"|fn_url}">{$row.count}</a>
			<p class="small-note">{$row.percent}%</p></td>
	</tr>
	{/foreach}
	</table>

	{/capture}
	{include file="addons/statistics/views/statistics/components/select_charts.tpl" chart_table=$smarty.capture.table_chart chart_type=$chart_type applicable_charts="bar,pie"}
	{else}
		<p class="no-items">{$lang.no_data}</p>
	{/if}
<!--content_{$report_data.report}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section|default:$report_data.report track=true}
