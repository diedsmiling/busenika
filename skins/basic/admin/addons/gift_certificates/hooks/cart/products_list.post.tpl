{* $Id: products_list.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $product.item_type == "G"}
	{$lang.gift_certificate}
{/if}
{if $product.item_type == "C"}
	<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a>
{/if}