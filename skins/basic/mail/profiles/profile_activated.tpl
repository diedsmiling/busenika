{* $Id: profile_activated.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.hello}&nbsp;{if $user_data.firstname}{$user_data.firstname}{else}{$user_data.user_type|fn_get_user_type_description|lower|escape}{/if},<br /><br />
{$lang.text_profile_activated}

{include file="letter_footer.tpl"}
