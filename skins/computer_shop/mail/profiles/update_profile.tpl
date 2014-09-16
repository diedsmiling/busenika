{* $Id: update_profile.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.dear} {if $user_data.firstname}{$user_data.firstname}{else}{$user_data.user_type|fn_get_user_type_description|lower|escape}{/if},<br><br>

{$lang.update_profile_notification_header}<br><br>

{if $user_data.user_type == 'P' && $change_usertype == 'Y'}
{$lang.change_usertype_notification_header|replace:"[user_type]":$lang.affiliate}
<p>{$lang.affiliate_backend}:	{$config.http_location}/{$config.partner_index}<br />
{$lang.text_affiliate_create_profile}</p><br /><br />
{/if}

{include file="profiles/profiles_info.tpl"}

{include file="letter_footer.tpl"}
