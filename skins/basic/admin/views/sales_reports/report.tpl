{* $Id: report.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="statistics_form" class="cm-form-highlight">
<input type="hidden" name="report_id" value="{$report.report_id}" />

<div class="form-field">
	<label for="description" class="cm-required">{$lang.name}:</label>
	<input type="text" name="report_description[description]" id="description" value="{$report.description}" size="70" class="input-text-large main-input" />
</div>

<div class="form-field">
	<label for="position">{$lang.position}:</label>
	<input type="text" name="report[position]" id="position" value="{$report.position}" size="3" class="input-text-short" />
</div>

{include file="common_templates/select_status.tpl" input_name="report[status]" id="report" obj=$report}

{include file="common_templates/subheader.tpl" title=$lang.charts}
{* --------------- CHARTS --------------------*}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center" ><input type="checkbox" name="check_all" value="Y" class="checkbox cm-check-items" /></th>
	<th>{$lang.position_short}</th>
	<th width="70%">{$lang.name}</th>
	<th width="10%">{$lang.type}</th>
	<th width="20%">{$lang.value_to_display}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$report.tables item=table}
<input type="hidden" name="tables[{$table.table_id}][table_id]" value="{$table.table_id}" />
{assign var="table_id" value=$table.table_id}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center"><input type="checkbox" name="del[{$table.table_id}]" id="delete_checkbox" value="Y" class="checkbox cm-item" /></td>
	<td><input type="text" name="tables[{$table.table_id}][position]" value="{$table.position}" size="3" class="input-text-short" /></td>
	<td><input type="text" name="table_description[{$table.table_id}][description]" value="{$table.description}" class="input-text-long" /></td>
	<td>
	<select	name="tables[{$table.table_id}][type]">
		<option	value="T">{$lang.table}</option>
		<option	value="B" {if $table.type == "B"}selected="selected"{/if}>{$lang.graphic} [{$lang.bar}] </option>
		<option	value="P" {if $table.type == "P"}selected="selected"{/if}>{$lang.graphic} [{$lang.pie_3d}] </option>
		<option	value="C" {if $table.type == "C"}selected="selected"{/if}>{$lang.graphic} [{$lang.pie}] </option>
	</select></td>
	<td>
	<select	name="tables[{$table.table_id}][display]">
		{foreach from=$report_elements.values item=element}
		{assign var="element_id" value=$element.element_id}
		{assign var="element_name" value="reports_parameter_$element_id"}
		<option	value="{$element.code}" {if $table.display == $element.code}selected="selected"{/if}>{$lang.$element_name}</option>
		{/foreach}
	</select></td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"sales_reports.report.delete_table?table_id=`$table.table_id`&amp;report_id=`$report.report_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$promotion.promotion_id tools_list=$smarty.capture.tools_items href="sales_reports.table.edit?report_id=`$report_id`&table_id=`$table_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="6"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $report.tables}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[sales_reports.report.delete_table]" class="cm-process-items cm-confirm" rev="statistics_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[sales_reports.report.update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	{assign var="report_link" value="sales_reports.table.add?report_id=`$report_id`"|fn_url}
	<div class="float-right">	
		{include file="common_templates/tools.tpl" tool_onclick="jQuery.redirect('`$report_link`');" prefix="bottom" hide_tools=true link_text=$lang.add_chart}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_onclick="jQuery.redirect('`$report_link`');" prefix="top" hide_tools=true link_text=$lang.add_chart}
{/capture}

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.editing_report`:&nbsp;`$report.description`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
