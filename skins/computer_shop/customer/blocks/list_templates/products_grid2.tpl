{* $Id$ *}

{if $products}

{script src="js/exceptions.js"}

{if !$no_pagination}
	{include file="common_templates/pagination.tpl"}
{/if}
{if !$no_sorting}
	{include file="views/products/components/sorting.tpl"}
{/if}

{if $products|sizeof < $columns}
{assign var="columns" value=$products|@sizeof}
{/if}
{split data=$products size=$columns|default:"3" assign="splitted_products"}
{math equation="100 / x" x=$columns|default:"3" assign="cell_width"}
{if $item_number == "Y"}
	{assign var="cur_number" value=1}
{/if}
<table cellspacing="0" cellpadding="0" width="100%" border="0" class="fixed-layout multicolumns-list">
{foreach from=$splitted_products item="sproducts" name="sprod"}
<tr>
{foreach from=$sproducts item="product" name="sproducts"}
{capture name="capt_options_vs_qty"}{/capture}

	{assign var="obj_id" value=$product.product_id}
	{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
	{include file="common_templates/product_data.tpl" product=$product}
	<td class="product-spacer">&nbsp;</td>
	<td class="center image-border compact{if !$smarty.foreach.sprod.last} border-bottom{/if}" valign="top" width="{$cell_width}%">
	{if $product}
		{assign var="form_open" value="form_open_`$obj_id`"}
		{$smarty.capture.$form_open}
		{hook name="products:product_multicolumns_list"}
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" obj_id=$obj_id_prefix images=$product.main_pair object_type="product" show_thumbnail="Y" image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height product_title = $product.product}</a>

		<p>
		{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
		{assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku}
		</p>
		
		{assign var="old_price" value="old_price_`$obj_id`"}
		{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
		
		{assign var="price" value="price_`$obj_id`"}
		{$smarty.capture.$price}
		
		{assign var="clean_price" value="clean_price_`$obj_id`"}
		{$smarty.capture.$clean_price}
		
		{$smarty.capture.capt_options_vs_qty}
			{if $settings.Appearance.in_stock_field == "Y"}
				{assign var="product_amount" value="product_amount_`$obj_id`"}
				{$smarty.capture.$product_amount}
			{/if}
	
			{if !$smarty.capture.capt_options_vs_qty}
			{assign var="product_options" value="product_options_`$obj_id`"}
			<div class="dev_greed_pr">
			<center>				
				{$smarty.capture.$product_options}
			
				{assign var="qty" value="qty_`$obj_id`"}
				{$smarty.capture.$qty}
			
			
			{assign var="advanced_options" value="advanced_options_`$obj_id`"}
			{$smarty.capture.$advanced_options}
			
			{assign var="min_qty" value="min_qty_`$obj_id`"}
			{$smarty.capture.$min_qty}
			</center>
			</div>
			{/if}
			{assign var="product_edp" value="product_edp_`$obj_id`"}
			{$smarty.capture.$product_edp}
	
		<div class="buttons-container">
			{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
			{$smarty.capture.$add_to_cart}
		</div>
		{/hook}
		{assign var="form_close" value="form_close_`$obj_id`"}
		{$smarty.capture.$form_close}
	{/if}
	</td>
	<td class="product-spacer">&nbsp;</td>
{/foreach}
</tr>
{/foreach}
</table>

{if !$no_pagination}
	{include file="common_templates/pagination.tpl"}
{/if}

{/if}

{capture name="mainbox_title"}{$title}{/capture}
