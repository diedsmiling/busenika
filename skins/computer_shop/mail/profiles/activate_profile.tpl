{* $Id: activate_profile.tpl 10534 2010-08-27 08:40:57Z 2tl $ *}

{include file="letter_header.tpl"}

{$lang.hello},<br /><br />{assign var="_url" value="profiles.update?user_id=`$user_data.user_id`"|fn_url:'A':'http':'&'}
{if $settings.General.use_email_as_login == "Y"}
	{assign var="user_login" value=$user_data.email}
{else}
	{assign var="user_login" value=$user_data.user_login}
{/if}
{$lang.text_new_user_activation|replace:"[user_login]":$user_login|replace:"[url]":"<a href=\"`$_url`\">`$_url`</a>"}

{include file="letter_footer.tpl" user_type='A'}
