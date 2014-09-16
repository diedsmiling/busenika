{* $Id: small_items.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<ul>
{foreach from=$products item="product" name="products"}
	{assign var="obj_id" value=$product.product_id}
	{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
	{include file="common_templates/product_data.tpl" product=$product}
	<li class="compact">
		{assign var="form_open" value="form_open_`$obj_id`"}
		{$smarty.capture.$form_open}
			<div class="item-image product-item-image">
				<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width="40" images=$product.main_pair obj_id=$obj_id_prefix show_thumbnail="Y" no_ids=true}</a>
			</div>
			<div class="item-description">
				{if $block.properties.item_number == "Y"}{$smarty.foreach.for_products.iteration}.&nbsp;{/if}
				{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}

				<div class="margin-top">
					{assign var="old_price" value="old_price_`$obj_id`"}
					{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
					
					{assign var="price" value="price_`$obj_id`"}
					{$smarty.capture.$price}
				</div>

				{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
				{if $smarty.capture.$add_to_cart|trim}<p>{$smarty.capture.$add_to_cart}</p>{/if}
			</div>
		{assign var="form_close" value="form_close_`$obj_id`"}
		{$smarty.capture.$form_close}
	</li>
	{if !$smarty.foreach.products.last}
	<li class="delim">&nbsp;</li>
	{/if}
{/foreach}
</ul>
