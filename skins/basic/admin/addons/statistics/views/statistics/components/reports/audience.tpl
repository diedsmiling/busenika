{* $Id: audience.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{capture name="tabsbox"}

<div id="content_{$report_data.report}">
	{if $report_data.data}
	{capture name="table_chart"}
	
	<table cellpadding="2" cellspacing="1" border="0">
	{foreach from=$report_data.data item="row" key="key"}
	<tr {cycle values=",class=\"manage-row\""}>
		<td valign="top">
			{$row.label|default:$lang.undefined}
			{if $report_data.report == "page_load_speed"}

				{if $smarty.request.load_speed_details}
				<div id="content_stat_pages_{$key}">

				<div class="object-container">

					<table class="table" cellpadding="0" cellspacing="1" border="0" width="400">
					{foreach from=$row.pages item="_page"}
					<tr>
						<td><div class="no-scroll"><a href="{$_page.url|fn_url}">{$_page.url}</a></div></td>
						{math equation="loadtime/1000000" assign="time" loadtime=$_page.loadtime}
						<td align="right">&nbsp;&nbsp;{$time|string_format:"%07.6f"}</td>
					</tr>
					{foreachelse}
					<tr>
						<td colspan="2" class="no-items"><p>{$lang.no_data}</p></td>
					</tr>
					{/foreach}
					</table>
				</div>

				<!--content_stat_pages_{$key}--></div>
				{/if}

				{script src="js/picker.js"}
				{include file="common_templates/table_tools_list.tpl" prefix=$key tools_list=$smarty.capture.tools_items id="stat_pages_`$key`" text=$lang.pages link_text=$lang.view_pages act="edit" href="statistics.reports?reports_group=audience&amp;report=page_load_speed&amp;load_speed_details=`$key`" link_class="tool-link" popup=true}
			{/if}
			{include file="views/sales_reports/components/graph_bar.tpl" bar_width="400px" value_width=$row.percent|round}
		</td>
		{if $report_data.report == "page_load_speed"}
		<td align="right"><span class="small-note">+</span>{$row.sum_count}
			<p class="small-note">{$row.sum_percent}%</p></td>
		{/if}
		<td align="right">
			{if $report_data.report == "site_attendance" || $report_data.report == "page_load_speed"}
				{if $report_data.report == "site_attendance"}{assign var="object_code" value=$row.hour}{else}{assign var="object_code" value=$row.label}{/if}
				<a href="{"statistics.visitors?section=audience&amp;report=`$report_data.report`&amp;object_code=`$object_code`"|fn_url}">{$row.count}</a>				
			{else}
				{$row.count}
			{/if}
			
			{if $report_data.report != "site_attendance"}<p class="small-note">{$row.percent}%</p>{/if}</td>
	</tr>
	{/foreach}
	{if $report_data.report == "total_pages_viewed"}
	<tr>
		<td>{$lang.average_depth}:&nbsp;</td>
		<td align="right"><strong>{$report_data.average_depth}</strong></td>
	</tr>
	{elseif $report_data.report == "stat_visit_time"}
	<tr>
		<td>{$lang.average_duration}:&nbsp;</td>
		<td align="right"><strong>{$report_data.average_duration|date_format:$settings.Appearance.time_format}</strong></td>
	</tr>
	{elseif $report_data.report == "site_attendance"}
	<tr>
		<td>{$lang.total}:&nbsp;</td>
		<td align="right"><strong>{$data.total|default:"0"}</strong></td>
	</tr>
	{/if}
	</table>
	
	{/capture}
	{include file="addons/statistics/views/statistics/components/select_charts.tpl" chart_table=$smarty.capture.table_chart chart_type=$chart_type applicable_charts="bar,pie"}
	{else}
		<p class="no-items">{$lang.no_data}</p>
	{/if}
<!--content_{$report_data.report}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section|default:$report_data.report track=true}
