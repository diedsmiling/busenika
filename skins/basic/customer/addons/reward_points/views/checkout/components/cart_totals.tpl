{* $Id: cart_totals.tpl 6759 2009-01-14 14:36:35Z angel $ *}

{if $cart.points_info.reward}
<li>
	<span>{$lang.points}:</span>
	<strong>{$cart.points_info.reward}</strong>
</li>
{/if}
