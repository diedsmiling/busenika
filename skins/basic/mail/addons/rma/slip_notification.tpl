{* $Id: slip_notification.tpl 7703 2009-07-13 10:36:45Z angel $ *}

{include file="letter_header.tpl"}

{$lang.dear} {$order_info.firstname},<br /><br />

{$return_status.email_header|unescape}<br /><br />

<b>{$lang.packing_slip}:</b><br />

{include file="addons/rma/slip.tpl"}

{include file="letter_footer.tpl"}