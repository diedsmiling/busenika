{* $Id: notifications.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<div id="content_notifications">

<p>{$lang.text_notification_to_inviteees}</p>

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
<tr {cycle values=",class=\"table-row\""}>
	<td class="center">
		<input type="checkbox" name="event_recipients[]" value="{$s.email}" class="checkbox cm-item" /></td>
	<td class="nowrap">{$s.name}&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td class="nowrap"><a href="mailto:{$s.email|escape:url}">{$s.email}</a></td>
</tr>
{foreachelse}
<tr>
	<td colspan="3"><p class="no-items">{$lang.no_invitees_found}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="3">&nbsp;</td>
</tr>
</table>

{if $event_data.subscribers}
	<div class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.send_notification but_name="dispatch[events.send_notifications]" but_meta="cm-process-items"}
	</div>
{/if}
</form>

</div>
