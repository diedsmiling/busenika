{* $Id: declined_body.tpl 8505 2010-01-04 15:35:06Z 2tl $ *}

{include file="letter_header.tpl"}

{$lang.dear} {$user_data.firstname},<br /><br />

{$lang.email_declined_notification_header}<br /><br />

{if $reason_declined}
<b>{$lang.reason}:</b><br />
{$reason_declined|nl2br}<br /><br />
{/if}

{include file="letter_footer.tpl"}
