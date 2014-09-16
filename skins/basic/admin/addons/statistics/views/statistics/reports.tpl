{* $Id: reports.tpl 6101 2008-10-06 14:21:23Z angel $ *}

{script src="lib/amcharts/swfobject.js"}

<div id="content_{$reports_group}">
{capture name="mainbox"}
	{include file="addons/statistics/views/statistics/components/search_form.tpl" key=$action dispatch="statistics.reports"}
 	{include file="addons/statistics/views/statistics/components/reports/`$reports_group`.tpl" report_data=$statistics_data}
{/capture}

{include file="common_templates/mainbox.tpl" title="`$lang.statistics`: "|cat:$lang.$reports_group content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
<!--content_{$reports_group}--></div>