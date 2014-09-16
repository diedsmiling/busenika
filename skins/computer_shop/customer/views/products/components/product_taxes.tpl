{* $Id: product_taxes.tpl 5626 2008-07-21 07:47:04Z brook $ *}

// Product taxes
{if $product.taxes}
	tax_data[{$id}] = {$product.taxes|@to_json};
{/if}
// /Product taxes
