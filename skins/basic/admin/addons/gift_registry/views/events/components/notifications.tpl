{* $Id: notifications.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<div class="cm-hide-save-button" id="content_notifications">

{capture name="local_notes"}
	<p>{$lang.text_notification_to_inviteees}</p>
{/capture}

{include file="common_templates/subheader.tpl" title=$lang.list_of_event_invitees notes=$smarty.capture.local_notes notes_id="event_invitees"}

<form action="{""|fn_url}" method="post" name="event_notifications_form" >
<input type="hidden" name="event_id" value="{$event_id}" />

{if $access_key}
<input type="hidden" name="access_key" value="{$access_key}" />
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="50%" class="table">
<tr>
	<th width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.name}</th>
	<th>{$lang.email}</th>
</tr>
{foreach from=$event_data.subscribers item=s}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="event_recipients[]" id="notify_checkbox" value="{$s.email}" class="checkbox cm-item" /></td>	
	<td class="nowrap">{$s.name}&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td class="nowrap"><a href="mailto:{$s.email|escape:url}" class="underlined">{$s.email}</a></td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="3"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>


{if $event_data.subscribers}
<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_text=$lang.send_notification but_name="dispatch[events.send_notifications]" but_meta="cm-process-items" but_role="button_main"}
</div>
{/if}
</form>

</div>