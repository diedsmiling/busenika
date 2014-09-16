{* $Id: track.tpl 10055 2010-07-14 10:15:19Z klerik $ *}
                                                   
{include file="letter_header.tpl"}

{$lang.hello},<br /><br />

{$lang.text_track_request}<br /><br />

{if $o_id}
{$lang.text_track_view_order|replace:"[order]":$o_id}<br />
<a href="{"orders.track?ekey=`$access_key`&amp;o_id=`$o_id`"|fn_url:'C':'http':'&'}">{"orders.track?ekey=`$access_key`&amp;o_id=`$o_id`"|fn_url:'C':'http':'&'}</a><br />
<br />
{/if}

{$lang.text_track_view_all_orders}<br />
<a href="{"orders.track?ekey=`$access_key`"|fn_url:'C':'http':'&'}">{"orders.track?ekey=`$access_key`"|fn_url:'C':'http':'&'}</a><br />

{include file="letter_footer.tpl"}