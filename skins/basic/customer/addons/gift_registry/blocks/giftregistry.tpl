{* $Id: giftregistry.tpl 9452 2010-05-14 12:05:08Z lexa $ *}
{** block-description:events **}

<!--dynamic:events-->
{if $today_events}
<div><strong>{$lang.today_events}</strong>:</div>
<ul class="bullets-list">
{foreach from=$today_events item=event}
	<li><a href="{"events.view?event_id=`$event.event_id`"|fn_url}">{$event.title}</a></li>
{/foreach}
</ul>

{if $additional_link}
<div class="right"><a href="{"events.search?today_events=Y"|fn_url}">{$lang.more_w_ellipsis}</a></div>
{/if}
<div class="delim"></div>
{/if}
<ul class="arrows-list">
	<li><a href="{"events.search"|fn_url}">{$lang.search}</a></li>
	<li><a href="{"events.add"|fn_url}">{$lang.add_new}</a></li>
	<li><a href="{"events.access_key"|fn_url}">{$lang.private_events}</a></li>
</ul>
<!--/dynamic-->