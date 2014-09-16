{* $Id: simple_list.tpl 10045 2010-07-13 12:58:26Z alexions $ *}

{if $product}
{assign var="obj_id" value=$obj_id|default:$product.product_id}
{include file="common_templates/product_data.tpl" obj_id=$obj_id product=$product}
<div class="product-container clear">
	{assign var="form_open" value="form_open_`$obj_id`"}
	{$smarty.capture.$form_open}
		{if $item_number == "Y"}<strong>{$smarty.foreach.products.iteration}.&nbsp;</strong>{/if}
		{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
		{assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku}
		{assign var="rating" value="rating_`$obj_id`"}{$smarty.capture.$rating}
		
		<div class="prices-container clear">
		{if $show_old_price || $show_clean_price || $show_list_discount}
			<div class="float-left product-prices">
				{assign var="old_price" value="old_price_`$obj_id`"}
				{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
		{/if}
		
		{if !$smarty.capture.$old_price|trim || $details_page}<p>{/if}
				{assign var="price" value="price_`$obj_id`"}
				{$smarty.capture.$price}
		{if !$smarty.capture.$old_price|trim || $details_page}</p>{/if}

		{if $show_old_price || $show_clean_price || $show_list_discount}
				{assign var="clean_price" value="clean_price_`$obj_id`"}
				{$smarty.capture.$clean_price}
				
				{assign var="list_discount" value="list_discount_`$obj_id`"}
				{$smarty.capture.$list_discount}
			</div>
		{/if}
		{if $show_discount_label}
			<div class="float-left">
				{assign var="discount_label" value="discount_label_`$obj_id`"}
				{$smarty.capture.$discount_label}
			</div>
		{/if}
		</div>

		{if $capture_options_vs_qty}{capture name="product_options"}{/if}
		{assign var="product_amount" value="product_amount_`$obj_id`"}
		{$smarty.capture.$product_amount}
		
		{if $show_features || $show_descr}
			<p class="product-descr"><strong>{assign var="product_features" value="product_features_`$obj_id`"}{$smarty.capture.$product_features}</strong>{assign var="prod_descr" value="prod_descr_`$obj_id`"}{$smarty.capture.$prod_descr}</p>
		{/if}
		
		{assign var="product_options" value="product_options_`$obj_id`"}
		{$smarty.capture.$product_options}
		
		{assign var="qty" value="qty_`$obj_id`"}
		{$smarty.capture.$qty}
		
		{assign var="advanced_options" value="advanced_options_`$obj_id`"}
		{$smarty.capture.$advanced_options}
		{if $capture_options_vs_qty}{/capture}{/if}
		
		{assign var="min_qty" value="min_qty_`$obj_id`"}
		{$smarty.capture.$min_qty}
		
		{assign var="product_edp" value="product_edp_`$obj_id`"}
		{$smarty.capture.$product_edp}

		{if $capture_buttons}{capture name="buttons"}{/if}
		<div class="buttons-container">
			{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
			{$smarty.capture.$add_to_cart}
			
			{assign var="list_buttons" value="list_buttons_`$obj_id`"}
			{$smarty.capture.$list_buttons}
		</div>
		{if $capture_buttons}{/capture}{/if}
	{assign var="form_close" value="form_close_`$obj_id`"}
	{$smarty.capture.$form_close}
</div>

{/if}