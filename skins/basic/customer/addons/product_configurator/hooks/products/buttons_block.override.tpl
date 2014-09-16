{* $Id: buttons_block.override.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $product.product_type == "C" && !$product.configuration_mode}
	<div class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.configure but_role="text" but_href="products.view?product_id=`$product.product_id`"}
	</div>
{/if}