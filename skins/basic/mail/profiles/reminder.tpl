{* $Id: reminder.tpl 0 2009-12-28 09:27:03Z 2tl $ *}

{include file="letter_header.tpl"}

{$lang.dear} {if $user_data.firstname}{$user_data.firstname}{else}{$user_data.user_type|fn_get_user_type_description|lower|escape}{/if},<br><br>

{$lang.change_password_notification_body|replace:"[days]":$days|replace:"[store]":$config.http_location}<br><br>

<a href="{$link|replace:'&amp;':'&'}">{$link|replace:'&amp;':'&'}</a><br><br>

{include file="letter_footer.tpl"}
