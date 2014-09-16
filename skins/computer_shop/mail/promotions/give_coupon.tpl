{* $Id: give_coupon.tpl 7031 2009-03-13 09:34:25Z zeke $ *}

{include file="letter_header.tpl"}

{$lang.hello} {$order_info.firstname}<br /><br />

{$lang.text_applied_promotions}<br />

{$promotion_data.name}<br /><br />
{$promotion_data.detailed_description|unescape}<br />

<b>{$bonus_data.coupon_code}</b>

{include file="letter_footer.tpl"}
