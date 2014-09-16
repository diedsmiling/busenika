{* $Id: new_notification.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{$user.firstname} {$user.lastname}<br />
<br />
{$lang.revisions_history}: <a href="{$history_link|replace:'&amp;':'&'}">{$history_link|replace:'&amp;':'&'}</a><br />
<br />
{$lang.date}: {$time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}<br />
<br />
{if $revision.note}
{$lang.declined_by}: {$puser.firstname} {$puser.lastname}<br />
<br />
{$lang.decline_reason}: {$revision.note}
{else}
{$lang.created_by}: {$puser.firstname} {$puser.lastname}
{/if}