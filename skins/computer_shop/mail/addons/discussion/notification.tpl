{* $Id: notification.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.hello},<br /><br />

{$lang.text_new_post_notification}&nbsp;<b>{$lang.$object_name}</b>:&nbsp;{$object_data.description}
<br /><br />
<b>{$lang.name}</b>:&nbsp;{$post_data.name}<br />
{if $post_data.rating_value}
<b>{$lang.rating}</b>:&nbsp;{if $post_data.rating_value == "5"}{$lang.excellent}{elseif $post_data.rating_value == "4"}{$lang.very_good}{elseif $post_data.rating_value == "3"}{$lang.average}{elseif $post_data.rating_value == "2"}{$lang.fair}{elseif $post_data.rating_value == "1"}{$lang.poor}{/if}
<br />
{/if}

{if $post_data.message}
<b>{$lang.message}</b>:<br />
{$post_data.message|nl2br}
<br /><br />
{/if}

{if $post_data.status == 'N'}
<b>{$lang.text_approval_notice}</b>
<br />
{/if}
{$lang.view}:<br />
<a href="{$url|replace:'&amp;':'&'}">{$url|replace:'&amp;':'&'}</a>

{include file="letter_footer.tpl"}