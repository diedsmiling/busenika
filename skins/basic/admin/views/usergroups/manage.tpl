{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{script src="js/tabs.js"}
{script src="js/picker.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="usergroups_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="5%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="20%">{$lang.usergroup}</th>
	<th width="20%">{$lang.type}</th>
	<th width="20%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$usergroups item=usergroup}
<tr {cycle values="class=\"table-row\", "}>
	<td width="1%">
		<input type="checkbox" name="usergroup_ids[]" value="{$usergroup.usergroup_id}" class="checkbox cm-item" /></td>
	<td>
		{$usergroup.usergroup}{if $usergroup.type == "A"} - <a href="{"usergroups.assign_privileges?usergroup_id=`$usergroup.usergroup_id`"|fn_url}" class="tool-link">{$lang.privileges}</a>{/if}
	</td>
	<td>
		{if $usergroup.type == "C"}{$lang.customer}{/if}
		{if $usergroup.type == "A"}{$lang.administrator}{/if}
	</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$usergroup.usergroup_id status=$usergroup.status hidden=true object_id_name="usergroup_id" table="usergroups"}
	</td>
	<td class="nowrap right">
		{if $usergroup.type == "A"}
			{assign var="_href" value="usergroups.assign_privileges?usergroup_id=`$usergroup.usergroup_id`"}
			{assign var="_link_text" value=$lang.privileges}
		{else}
			{assign var="_href" value=""}
			{assign var="_link_text" value=""}
		{/if}

		{capture name="tools_items"}
			<li><a class="cm-confirm" href="{"usergroups.delete?usergroup_id=`$usergroup.usergroup_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" popup=true id="group`$usergroup.usergroup_id`" text=$usergroup.usergroup act="edit" href="usergroups.update?usergroup_id=`$usergroup.usergroup_id`" prefix=$usergroup.usergroup_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $usergroups}
	<div class="float-left">
		{include file="buttons/button.tpl" but_name="dispatch[usergroups.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
	</div>
	{/if}
	
	<div class="float-right">
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="views/usergroups/update.tpl" usergroup="" mode="add"}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_usergroups" text=$lang.add_new_usergroups content=$smarty.capture.add_new_picker link_text=$lang.add_usergroup act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_usergroups" text=$lang.add_new_usergroups link_text=$lang.add_usergroup act="general"}
	</div>
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.usergroups content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}