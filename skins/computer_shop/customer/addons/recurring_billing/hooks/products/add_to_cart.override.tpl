{* $Id: buttons_block.override.tpl 6672 2008-12-24 13:53:05Z isergi $ *}

{if $product.recurring_plans && !$product.recurring_plans.0 && !($smarty.const.CONTROLLER == "products" && ($smarty.const.MODE == "view" || $smarty.const.MODE == "options")) && $wishlist|fn_cart_is_empty}
	<{if $separate_buttons}div class="buttons-container"{else}span{/if}>
		{include file="buttons/button.tpl" but_text=$lang.subscribe but_role="text" but_href="products.view?product_id=`$product.product_id`"}
	</{if $separate_buttons}div{else}span{/if}>
{elseif $product.recurring_plans && $smarty.const.CONTROLLER == "products" && ($smarty.const.MODE == "view" || $smarty.const.MODE == "options") && $subscription_object_id}
	<{if $separate_buttons}div class="buttons-container"{else}span{/if}>
		<input type="hidden" name="product_data[{$product.product_id}][cart_id]" value="{$subscription_object_id}" />
		<input type="hidden" name="product_data[{$product.product_id}][return_mode]" value="{$return_mode}" />
		{include file="buttons/save.tpl" but_name="dispatch[checkout.add]" but_meta="cm-no-ajax"}
	</{if $separate_buttons}div{else}span{/if}>
{/if}