{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="destinations_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="80%">{$lang.name}</th>
	<th width="20%">{$lang.status}</th>
	<th>&nbsp;</th>
</tr>

{foreach from=$destinations item=destination}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input name="destination_ids[]" type="checkbox" value="{$destination.destination_id}" {if $destination.destination_id == 1}disabled="disabled"{/if} class="checkbox cm-item" /></td>
	<td>
		<input type="text" name="destinations[{$destination.destination_id}][destination]" size="50" value="{$destination.destination}" class="input-text" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$destination.destination_id status=$destination.status hidden="" object_id_name="destination_id" table="destinations"}
	</td>
	<td class="nowrap" >
		{capture name="tools_items"}
		{if $destination.destination_id != 1}
			<li><a class="cm-confirm" href="{"destinations.delete?destination_id=`$destination.destination_id`"|fn_url}">{$lang.delete}</a></li>
		{else}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$destination.destination_id tools_list=$smarty.capture.tools_items href="destinations.update?destination_id=`$destination.destination_id`"}</td>

</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $destinations}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[destinations.delete]" class="cm-process-items cm-confirm" rev="destinations_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[destinations.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="destinations.add" prefix="bottom" hide_tools="true" link_text=$lang.add_location}
	</div>
</div>

</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="destinations.add" prefix="top" hide_tools="true" link_text=$lang.add_location}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.locations content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
