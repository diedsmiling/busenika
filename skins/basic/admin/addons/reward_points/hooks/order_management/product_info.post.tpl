{* $Id: product_info.post.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $cart.points_info.total_price}
	<p>{$lang.price_in_points}:&nbsp;{$cart.products.$key.extra.points_info.price|default:"-"}</p>
{/if}
