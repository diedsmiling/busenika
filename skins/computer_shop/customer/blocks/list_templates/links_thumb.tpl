{* $Id: links_thumb.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{foreach from=$products item="product" name="products"}
	{assign var="obj_id" value=$product.product_id}
	{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
	{include file="common_templates/product_data.tpl" product=$product}
	<div class="center product-item-image compact margin-bottom">
		{assign var="form_open" value="form_open_`$obj_id`"}
		{$smarty.capture.$form_open}
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width="70" images=$product.main_pair obj_id=$obj_id_prefix object_type="product" show_thumbnail="Y" no_ids=true}</a>
		<p>{if $block.properties.item_number == "Y"}{$smarty.foreach.products.iteration}.&nbsp;{/if}
		{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}</p>

		{assign var="old_price" value="old_price_`$obj_id`"}
		{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
		
		{assign var="price" value="price_`$obj_id`"}
		{$smarty.capture.$price}

		<div class="buttons-container">
			{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
			{$smarty.capture.$add_to_cart}
		</div>
		{assign var="form_close" value="form_close_`$obj_id`"}
		{$smarty.capture.$form_close}
	</div>
{/foreach}
