{* $Id: sales_reports_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" method="post" name="report_form_{$report.report_id}">
<input type="hidden" name="report_id" value="{$report.report_id}" />
<input type="hidden" name="selected_section" value="" />

{include file="common_templates/period_selector.tpl" period=$period form_name="orders_search_form" display="form" but_name="dispatch[sales_reports.reports_view.update_report]"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
