{* $Id: event.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.hello} {$recipient.name},<br /><br />

{$lang.text_event_subscriber|replace:"[owner]":$event.owner}<br /><br />
{assign var="recipient_email" value=$recipient.email|escape:url}
{if $access_key}
<a href="{"events.view?access_key=`$access_key`"|fn_url:'C':'http':'&'}">{$lang.view_event_details}</a><br />
<a href="{"events.unsubscribe?access_key=`$access_key`&amp;email=`$recipient_email`"|fn_url:'C':'http':'&'}">{$lang.unsubscribe}</a><br /><br />
{else}
<a href="{"events.view?event_id=`$event.event_id`"|fn_url:'C':'http':'&'}">{$lang.view_event_details}</a><br />
<a href="{"events.unsubscribe?event_id=`$event.event_id`&amp;email=`$recipient_email`"|fn_url:'C':'http':'&'}">{$lang.unsubscribe}</a><br /><br />
{/if}
{include file="letter_footer.tpl"}