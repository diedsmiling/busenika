{* $Id: order_sms.tpl 6626 2008-12-22 08:25:14Z zeke $ *}

{$lang.order} #{$order_id} {$lang.sms_for_the_sum} {include file="common_templates/price.tpl" value=$total} {$lang.sms_order_placed}{if $send_info} {$lang.payment_info}: {$order_payment_info}{/if}{if $send_email} {$lang.customer_email}: {$order_email}{/if}