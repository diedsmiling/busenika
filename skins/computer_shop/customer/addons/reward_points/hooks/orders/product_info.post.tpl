{* $Id: product_info.post.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $order_info.points_info.price && $product}
	<div class="product-list-field">
		<label>{$lang.price_in_points}:</label>
		{$product.extra.points_info.price}
	</div>
{/if}