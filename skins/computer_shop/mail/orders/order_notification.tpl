{* $Id: order_notification.tpl 9153 2010-03-25 10:02:32Z lexa $ *}
                                                   
{include file="letter_header.tpl"}

{$lang.dear} {$order_info.firstname},<br /><br />

{$order_status.email_header|unescape}<br /><br />

{assign var="order_header" value=$lang.invoice}
{if $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
	{assign var="order_header" value=$lang.credit_memo}
{elseif $status_settings.appearance_type == "O"}
	{assign var="order_header" value=$lang.order_details}
{/if}

<b>{$order_header}:</b><br />

{include file="orders/invoice.tpl"}

{include file="letter_footer.tpl"}