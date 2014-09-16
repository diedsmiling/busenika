{* $Id: without_image.tpl 10550 2010-08-31 06:30:37Z 2tl $ *}

<{if $block.properties.item_number == "Y"}ol{else}ul{/if} class="compact">

{foreach from=$items item="product" name="products"}
{assign var="obj_id" value=$product.product_id}
{include file="common_templates/product_data.tpl" product=$product}
<li>
	{assign var="form_open" value="form_open_`$obj_id`"}
	{$smarty.capture.$form_open}

	{if $product.manufacturer}<strong>{$product.manufacturer}</strong>{/if}
	{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}

	<div class="margin-top">
		{assign var="old_price" value="old_price_`$obj_id`"}
		{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
		
		{assign var="price" value="price_`$obj_id`"}
		{$smarty.capture.$price}
	</div>
	
	{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
	{if $smarty.capture.$add_to_cart|trim}<p>{$smarty.capture.$add_to_cart}</p>{/if}

	{assign var="form_close" value="form_close_`$obj_id`"}
	{$smarty.capture.$form_close}
</li>
{if !$smarty.foreach.products.last}
	<li class="delim">&nbsp;</li>
{/if}
{/foreach}
</{if $block.properties.item_number == "Y"}ol{else}ul{/if}>
