{* $Id: access_key.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.hello},<br /><br />

<table border="0" cellspacing="1" cellpadding="2">
<tr>
	<td class="table-head">{$lang.event}</td>
	<td class="table-head">{$lang.owner}</td>
	<td class="table-head">{$lang.access_key}</td>
	<td class="table-head">{$lang.link}</td>
</tr>
{if $owner_events}
<tr>
	<td colspan="4">
		<b>{$lang.text_your_events}:</b></td>
</tr>
{foreach from=$owner_events item=e}
<tr {cycle values='class="table-row", '}>
	<td>{$e.title}</td>
	<td>{$e.owner}</td>
	<td>{$e.ekey}</td>
	<td><a href="{"events.update?access_key=`$e.ekey`"|fn_url:'C':'http':'&'}">{$lang.open}</a></td>
</tr>
{/foreach}
{/if}
{if $subscriber_events}
<tr>
	<td colspan="4">
		<b>{$lang.text_events_you_subscribed}:</b></td>
</tr>
{foreach from=$subscriber_events item=e}
<tr {cycle values='class="table-row", '}>
	<td>{$e.title}</td>
	<td>{$e.name}</td>
	<td>{$e.ekey}</td>
	<td><a href="{"events.details?access_key=`$e.ekey`"|fn_url:'C':'http':'&'}">{$lang.open}</a></td>
</tr>
{/foreach}
{/if}
</table>

{include file="letter_footer.tpl"}