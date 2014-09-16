{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="manage_recurring_plans_form">

{include file="common_templates/pagination.tpl"}


<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.title}</th>
	<th class="center">{$lang.rb_recurring_period}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{if $recurring_plans}
{foreach from=$recurring_plans item="rec_plan"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
   		<input type="checkbox" name="plan_ids[]" value="{$rec_plan.plan_id}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"recurring_plans.update?plan_id=`$rec_plan.plan_id`"|fn_url}">{$rec_plan.name}</a></td>
   	<td class="center">
   		{$rec_plan.period|fn_get_recurring_period_name}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$rec_plan.plan_id status=$rec_plan.status hidden="" object_id_name="plan_id" table="recurring_plans"}
	</td>
   	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"recurring_plans.delete?plan_id=`$rec_plan.plan_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$rec_plan.plan_id tools_list=$smarty.capture.tools_items href="recurring_plans.update?plan_id=`$rec_plan.plan_id`"}
   	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $recurring_plans}
	<div class="float-left">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[recurring_plans.delete]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
	</div>
	{/if}

	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="recurring_plans.add" prefix="bottom" hide_tools="true" link_text=$lang.add_plan}
	</div>
</div>
</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="recurring_plans.add" prefix="top" hide_tools="true" link_text=$lang.add_plan}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.rb_recurring_plans content=$smarty.capture.mainbox tools=$smarty.capture.tools}
