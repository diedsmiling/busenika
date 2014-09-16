{* $Id: reports_list.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="reports_list">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center"><input type="checkbox" name="check_all" value="Y" class="checkbox cm-check-items" /></th>
	<th>{$lang.position_short}</th>
	<th>{$lang.name}</th>
	<th width="100%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$reports item=report}
<input type="hidden" name="update_reports[{$report.report_id}][report_id]" value="{$report.report_id}" />
<tr {cycle values="class=\"table-row\", "}>
	<td class="center"><input type="checkbox" name="report_ids[]" value="{$report.report_id}" class="checkbox cm-item" /></td>
	<td class="center"><input type="text" name="update_reports[{$report.report_id}][position]" value="{$report.position}" size="3" class="input-text-short" /></td>
	<td><input type="text" name="update_reports[{$report.report_id}][description]" value="{$report.description}" size="40" class="input-text" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$report.report_id status=$report.status hidden="" object_id_name="report_id" table="statistics_reports"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"sales_reports.reports_list.delete?report_id=`$report.report_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$report.report_id tools_list=$smarty.capture.tools_items href="sales_reports.report.edit?report_id=`$report.report_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $reports}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[sales_reports.reports_list.delete]" class="cm-process-items cm-confirm" rev="reports_list">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[sales_reports.reports_list.update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="views/sales_reports/reports_add.tpl"}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_reports" text=$lang.add_report content=$smarty.capture.add_new_picker link_text=$lang.add_report act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_reports" text=$lang.add_report link_text=$lang.add_report act="general"}
	</div>
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.reports content=$smarty.capture.mainbox tools=$smarty.capture.tools}
