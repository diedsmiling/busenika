{* $Id: product_info.post.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $order_info.points_info.price && $oi}
<p>{$lang.price_in_points}:{$oi.extra.points_info.price}</p>
{/if}