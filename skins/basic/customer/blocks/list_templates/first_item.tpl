{* $Id: first_item.tpl 9436 2010-05-13 10:15:04Z lexa $ *}

{foreach from=$products item="product" name="products"}
	{assign var="obj_id" value=$product.product_id}
	{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
	{include file="common_templates/product_data.tpl" product=$product}
	{if $smarty.foreach.products.first}
		{assign var="form_open" value="form_open_`$obj_id`"}
		{$smarty.capture.$form_open}
			<div class="clear compact">
				<div class="item-image product-item-image">
					<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width="50" images=$product.main_pair obj_id=$obj_id_prefix show_thumbnail="Y" no_ids=true}</a>
				</div>
				<div class="item-description">
					{if $block.properties.item_number == "Y"}{$smarty.foreach.for_products.iteration}.&nbsp;{/if}
					{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
				</div>
			</div>
	
			<p>
			{assign var="old_price" value="old_price_`$obj_id`"}
			{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
			
			{assign var="price" value="price_`$obj_id`"}
			{$smarty.capture.$price}
			</p>
	
			<div class="margin-bottom">
				{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
				{$smarty.capture.$add_to_cart}
			</div>
		{assign var="form_close" value="form_close_`$obj_id`"}
		{$smarty.capture.$form_close}
		<{if $block.properties.item_number == "Y"}ol start="2"{else}ul{/if} class="bullets-list compact">
	{else}
		<li>
			{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
		</li>
	{/if}
{/foreach}
</{if $block.properties.item_number == "Y"}ol{else}ul{/if}>
