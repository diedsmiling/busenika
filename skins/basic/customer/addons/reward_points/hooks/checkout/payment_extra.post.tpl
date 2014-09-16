{* $Id: payment_extra.post.tpl 10349 2010-08-04 12:56:49Z alexions $ *}

{if $mode == "checkout" && $cart_products && $cart.points_info.total_price && $user_info.points > 0}
<div class="right">
	{include file="addons/reward_points/hooks/checkout/payment_options.post.tpl"}
</div>
{/if}