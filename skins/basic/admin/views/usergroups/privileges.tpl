{* $Id: privileges.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="privileges_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.privilege}</th>
	<th width="100%" class="center">{$lang.description}</th>
</tr>			 

{foreach from=$privileges key=section_id item=privilege}
<tr>
	<td colspan="3"><input size="25" type="text" class="input-text-long" name="section_name[{$section_id}]" value="{$privilege.0.section}" /></td>
</tr>

{foreach from=$privilege item=p}
<tr {cycle values="class=\"table-row\", "}>
	<td width="1%">
		{if $p.is_default == "Y"}&nbsp;{else}<input type="checkbox" name="delete[{$p.privilege}]" id="delete_checkbox" class="checkbox cm-item" value="Y" />{/if}</td>
	<td>{$p.privilege}</td>
	<td><input type="text" class="input-text" size="35" name="privilege_descr[{$p.privilege}]" value="{$p.description}" /></td>
</tr>
{/foreach}
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[usergroups.privileges.update]" but_role="button_main"}
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.translate_privileges content=$smarty.capture.mainbox select_languages=true}
