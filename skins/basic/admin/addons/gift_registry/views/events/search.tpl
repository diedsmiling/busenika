{* $Id: search.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="addons/gift_registry/views/events/components/events_search_form.tpl"}

<form action="{""|fn_url}" method="post" name="delete_events_form">

{include file="common_templates/pagination.tpl" save_current_url=true}

<table cellpadding="0" cellspacing="0" width="100%" border="0" class="table">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></td>
	<th width="50%">{$lang.title}</th>
	<th width="15%">{$lang.start_date}</th>
	<th width="15%">{$lang.end_date}</th>
	<th width="10%">{$lang.status}</th>
	<th width="10%">{$lang.type}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$events item=event}
<tr {cycle values="class=\"table-row\","}>
	<td width="1%" class="center">
		<input type="checkbox" name="event_ids[]" value="{$event.event_id}" class="checkbox cm-item" /></td>
	<td><a href="{"events.update?event_id=`$event.event_id`"|fn_url}">{$event.title}</a></td>
	<td>{$event.start_date|date_format:$settings.Appearance.date_format}</td>
	<td>{$event.end_date|date_format:$settings.Appearance.date_format}</td>
	<td>{if $event.status == "A"}{$lang.awaiting}{elseif $event.status == "P"}{$lang.in_progress}{else}{$lang.finished}{/if}</td>
	<td>{if $event.type == "P"}{$lang.public}{elseif $event.type == "U"}{$lang.private}{else}{$lang.disabled}{/if}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"events.delete_events?event_id=`$event.event_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$event.event_id tools_list=$smarty.capture.tools_items href="events.update?event_id=`$event.event_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="7"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $events}
	<div class="float-left">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[events.delete_events]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="events.add" prefix="bottom" hide_tools="true" link_text=$lang.add_event}
	</div>
</div>
</form>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="events.add" prefix="top" hide_tools="true" link_text=$lang.add_event}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.events content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools}
