{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

{capture name="section"}
<form action="{""|fn_url}" name="search_objects_form" method="get">
<input type="hidden" name="object" value="{$smarty.request.object}">

<table class="search-header" cellspacing="0" border="0" width="100%">
<tr>
	<td class="search-field nowrap">
		<label>{$lang.name}:</label>
		<div class="break">
			<input type="text" name="q_description" size="20" value="{$params.q_description|stripslashes}" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="revisions_workflow.manage"}&nbsp;
		</div>
	</td>
	<td class="search-field nowrap">
		<label>{$lang.object_type}:</label>
		<div class="break">
			<select name="object">
				<option value="">--</option>
			{foreach from=$objects_data item="object_data"}
				<option value="{$object_data.object}" {if $object_data.object == $params.object}selected="selected"{/if}>{$object_data.title}</option>
			{/foreach}
			</select>
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[revisions_workflow.manage]" but_role="submit"}
	</td>
</tr>
</table>

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}

<form action="{""|fn_url}" method="post" name="update_workflows_form">
{include file="common_templates/pagination.tpl"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th><input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.object_type}</th>
	<th>{$lang.name}</th>
	<th width="100%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$workflows item="row"}
<tr {cycle values="class=\"table-row\","}>
	<td class="center" valign="top">
		<input type="checkbox" name="workflow_ids[]" value="{$row.workflow_id}" class="checkbox cm-item" /></td>
	<td align="left" class="nowrap">
		{$row.object_name} ({if $row.elements}{$row.elements}{else}{$lang.all}{/if})</td>
	<td>
		<input type="text" name="update_data[{$row.workflow_id}][description]" value="{$row.description}" size="60" class="input-text" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$row.workflow_id status=$row.status hidden="" object_id_name="workflow_id" table="revisions_workflows"}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"revisions_workflow.delete?workflow_id=`$row.workflow_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$row.workflow_id tools_list=$smarty.capture.tools_items href="revisions_workflow.update?workflow_id=`$row.workflow_id`"}
		</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>
{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $workflows}
		<div class="float-left">
			{capture name="tools_list"}
			<ul>
				<li><a name="dispatch[revisions_workflow.m_delete]" class="cm-process-items cm-confirm" rev="update_workflows_form">{$lang.delete_selected}</a></li>
			</ul>
			{/capture}
			{include file="buttons/button.tpl" but_text=$lang.save but_name="dispatch[revisions_workflow.m_update]" but_role="button_main"}
			{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
		</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="revisions_workflow.add" prefix="bottom" hide_tools=true link_text=$lang.add_workflow}
	</div>
</div>

</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="revisions_workflow.add" prefix="top" hide_tools=true link_text=$lang.add_workflow}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.workflow content=$smarty.capture.mainbox tools=$smarty.capture.tools}
