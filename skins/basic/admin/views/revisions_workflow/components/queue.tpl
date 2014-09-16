{* $Id: queue.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.position_short}</th>
	<th>{$lang.from}</th>
	<th>{$lang.action}</th>
	<th>{$lang.to}</th>
	<th>{$lang.notify_by_email}</th>
	<th width="100%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$queue item="row" name="feq"}
<tr id="box_queue_{$it}">
	<td>
		<input type="hidden" name="workflow_data[queue][{$smarty.foreach.feq.iteration}][id]" value="{$row.id}" />
		<input type="text" name="workflow_data[queue][{$smarty.foreach.feq.iteration}][position]" size="3" value="{$row.position}" class="input-text-short" /></td>
	<td>
		<select name="workflow_data[queue][{$smarty.foreach.feq.iteration}][from_user]">
		{foreach from=$users_data item="user"}
			<option value="{$user.user_id}"{if $user.user_id == $row.from_user} selected="selected"{/if}>{$user.firstname}{if $user.firstname && $user.lastname} {/if}{$user.lastname}</option>
		{/foreach}
			<option value="0"{if $row.from_user == 0} selected="selected"{/if}>{$lang.revision_to_system}</option>
		</select></td>
	<td>
		<select name="workflow_data[queue][{$smarty.foreach.feq.iteration}][action]">
			<option value="C"{if $row.action == "C"} selected="selected"{/if}>{$lang.revision_action_create}</option>
			<option value="A"{if $row.action == "A"} selected="selected"{/if}>{$lang.revision_action_approve}</option>
		</select></td>
	<td>
		<select name="workflow_data[queue][{$smarty.foreach.feq.iteration}][to_user]">
			{foreach from=$users_data item="user"}
				<option value="{$user.user_id}"{if $user.user_id == $row.to_user} selected="selected"{/if}>{$user.firstname}{if $user.firstname && $user.lastname} {/if}{$user.lastname}</option>
			{/foreach}
			<option value="0"{if $row.to_user == 0} selected="selected"{/if}>{$lang.revision_to_system}</option>
		</select></td>
	<td class="center">
		<input type="hidden" name="workflow_data[queue][{$smarty.foreach.feq.iteration}][send_email]" value="N" />
		<input type="checkbox" class="checkbox" name="workflow_data[queue][{$smarty.foreach.feq.iteration}][send_email]" value="Y"{if $row.send_email == "Y"} checked="checked"{/if} /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$row.id status=$row.status hidden="" object_id_name="id" table="revisions_workflows_queue"}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"revisions_workflow.delete_queue?workflow_id=`$workflow.workflow_id`&amp;queue_id=`$row.id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$smarty.foreach.feq.iteration tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{/foreach}

{assign var="it" value=$smarty.foreach.feq.iteration+1}
<tr id="box_queue_{$it}">
	<td>
		<input type="text" name="workflow_data[queue][{$it}][position]" size="3" value="" class="input-text-short" /></td>
	<td>
		<select id="elm_from_user_{$it}" name="workflow_data[queue][{$it}][from_user]">
			<option value="">--</option>
			{foreach from=$users_data item="user"}
				<option value="{$user.user_id}">{$user.firstname}{if $user.firstname && $user.lastname} {/if}{$user.lastname}</option>
			{/foreach}
			<option value="0">{$lang.revision_to_system}</option>
		</select></td>
	<td>
		<select id="elm_action_{$it}" name="workflow_data[queue][{$it}][action]">
			<option value="">--</option>
			<option value="C">{$lang.revision_action_create}</option>
			<option value="A">{$lang.revision_action_approve}</option>
		</select></td>
	<td>
		<select id="elm_to_user_{$it}" name="workflow_data[queue][{$it}][to_user]">
			<option value="">--</option>
			{foreach from=$users_data item="user"}
				<option value="{$user.user_id}">{$user.firstname}{if $user.firstname && $user.lastname} {/if}{$user.lastname}</option>
			{/foreach}
			<option value="0">{$lang.revision_to_system}</option>
		</select></td>
	<td class="center">
		<input type="hidden" name="workflow_data[queue][{$it}][send_email]" value="N" />
		<input type="checkbox" class="checkbox" name="workflow_data[queue][{$it}][send_email]" value="Y" /></td>
	<td>
		{include file="common_templates/select_status.tpl" display="select" input_name="workflow_data[queue][`$it`][status]" input_id="elm_status_`$it`"}</td>
	<td width="100%">{include file="buttons/multiple_buttons.tpl" item_id="queue_`$it`" tag_level="1"}</td>
</tr>

</table>
