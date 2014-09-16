{* $Id: assign_privileges.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="usergroups_form">
<input type="hidden" name="usergroup_id" value="{$smarty.request.usergroup_id}" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-group">
<tr>
	<th width="1%" class="table-group-checkbox">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%" colspan="5">{$lang.privilege}</th>
</tr>

{foreach from=$privileges item=privilege}
<tr class="table-group-header">
	<td colspan="6">{$privilege.0.section}</td>
</tr>

{split data=$privilege size=3 assign="splitted_privilege"}
{math equation="floor(100/x)" x=3 assign="cell_width"}
{foreach from=$splitted_privilege item=sprivilege}
<tr class="object-group-elements">
	{foreach from=$sprivilege item="p"}
		{assign var="pr_id" value=$p.privilege}
		{if $p.description}
			<td width="1%" class="table-group-checkbox">
				<input type="checkbox" name="set_privileges[{$pr_id}]" value="Y" {if $usergroup_privileges.$pr_id}checked="checked"{/if} class="checkbox cm-item" id="set_privileges_{$pr_id}" /></td>
			<td width="{$cell_width}%"><label for="set_privileges_{$pr_id}">{$p.description}</label></td>
		{else}
			<td colspan="2">&nbsp;</td>
		{/if}
	{/foreach}
</tr>
{/foreach}
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[usergroups.assign_privileges]" but_role="button_main"}
</div>

</form>

{/capture}
{assign var="_title" value="`$lang.privileges`&nbsp;(`$usergroup_name`)"}
{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox}