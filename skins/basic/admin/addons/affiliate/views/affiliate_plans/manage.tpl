{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="manage_affiliate_plans_form">

{include file="common_templates/pagination.tpl"}


<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.name}</th>
	<th class="center">{$lang.affiliates}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{if $affiliate_plans}
{foreach from=$affiliate_plans item="aff_plan"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
   		<input type="checkbox" name="plan_ids[]" value="{$aff_plan.plan_id}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"`$controller`.update?plan_id=`$aff_plan.plan_id`"|fn_url}">{$aff_plan.name}</a></td>
   	<td class="center">{$aff_plan.count_partners}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$aff_plan.plan_id status=$aff_plan.status hidden="" object_id_name="plan_id" table="affiliate_plans"}
	</td>
   	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"affiliate_plans.delete?plan_id=`$aff_plan.plan_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$aff_plan.plan_id tools_list=$smarty.capture.tools_items href="affiliate_plans.update?plan_id=`$aff_plan.plan_id`"}
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
	{if $affiliate_plans}
	<div class="float-left">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[affiliate_plans.delete]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="affiliate_plans.add" prefix="bottom" hide_tools="true" link_text=$lang.add_plan}
	</div>
</div>
</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="affiliate_plans.add" prefix="top" hide_tools="true" link_text=$lang.add_plan}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.plans content=$smarty.capture.mainbox tools=$smarty.capture.tools}
