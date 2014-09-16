{* $Id: product_option_content.override.tpl 9887 2010-06-28 12:56:20Z alexions $ *}

{if $product.product_type == "C" && !$product.configuration_mode && $smarty.const.CONTROLLER != "wishlist" && $smarty.const.MODE == "view"}
	&nbsp;
{/if}