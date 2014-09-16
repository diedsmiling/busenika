{* $Id: products_qty.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $product.max_qty}
<div class="form-field product-list-field">
	{if ($product.min_qty)}
		<label>{$lang.allow_qty}:</label>
		<span id="product_min_max_qty_{$product.product_id}">
		{$product.min_qty}&nbsp;-&nbsp;{$product.max_qty}&nbsp;{$lang.items}
	{else}
		<label>{$lang.allow_qty}:</label>
		<span id="product_min_max_qty_{$product.product_id}">
		{$product.quantity}&nbsp;{$product.max_qty}&nbsp;{$lang.qty_at_most}&nbsp;{$lang.items}
	{/if}
	</span>
</div>
{/if}
