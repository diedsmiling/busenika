{* $Id: products_list.tpl 10228 2010-07-27 13:09:03Z angel $ *}

{if $products}

{script src="js/exceptions.js"}

{if !$no_pagination}
	{include file="common_templates/pagination.tpl"}
{/if}
{if !$no_sorting}
	{include file="views/products/components/sorting.tpl"}
{/if}
{foreach from=$products item=product key=key name="products"}
{capture name="capt_options_vs_qty"}{/capture}
{hook name="products:product_block"}
{assign var="obj_id" value=$product.product_id}
{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
{include file="common_templates/product_data.tpl" product=$product min_qty=true}
<div class="product-container clear">
	{assign var="form_open" value="form_open_`$obj_id`"}
	{$smarty.capture.$form_open}
	{if $bulk_addition}
	<div class="float-right">
		<input class="cm-item" type="checkbox" id="bulk_addition_{$obj_prefix}{$product.product_id}" name="product_data[{$product.product_id}][amount]" value="{if $js_product_var}{$product.product_id}{else}1{/if}" {if ($product.zero_price_action == "R" && $product.price == 0)}disabled="disabled"{/if} />
	</div>
	{/if}
	
	<div class="float-left product-item-image center">
		{if !$hide_links}
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
		{/if}
			<span class="cm-reload-{$obj_prefix}{$obj_id} image-reload" id="list_image_update_{$obj_prefix}{$obj_id}">
				<input type="hidden" name="image[list_image_update_{$obj_prefix}{$obj_id}]" value="{$obj_id_prefix},{$settings.Thumbnails.product_lists_thumbnail_width},{$settings.Thumbnails.product_lists_thumbnail_height},product" />
				{include file="common_templates/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width obj_id=$obj_id_prefix images=$product.main_pair object_type="product" show_thumbnail="Y" image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height}
			<!--list_image_update_{$obj_prefix}{$obj_id}--></span>
		{if !$hide_links}
		</a>
		{/if}
		
		{assign var="rating" value="rating_$obj_id"}
		{$smarty.capture.$rating}
	</div>
	<div class="product-info">
		{if $js_product_var}
			<input type="hidden" id="product_{$obj_prefix}{$product.product_id}" value="{$product.product}" />
		{/if}
		{if $item_number == "Y"}<strong>{$smarty.foreach.products.iteration}.&nbsp;</strong>{/if}
		{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
		{assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku}
		
		<div class="float-right right add-product">
			{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
			{$smarty.capture.$add_to_cart}
		</div>
		
		<div class="prod-info">
			<div class="prices-container clear">
				<div class="float-left product-prices">
					{assign var="old_price" value="old_price_`$obj_id`"}
					{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
					
					{assign var="price" value="price_`$obj_id`"}
					{$smarty.capture.$price}
					
					{assign var="clean_price" value="clean_price_`$obj_id`"}
					{$smarty.capture.$clean_price}
					
					{assign var="list_discount" value="list_discount_`$obj_id`"}
					{$smarty.capture.$list_discount}
				</div>
				<div class="float-left">
					{assign var="discount_label" value="discount_label_`$obj_id`"}
					{$smarty.capture.$discount_label}
				</div>
			</div>
			{if $settings.Appearance.in_stock_field == "N"}
				{assign var="product_amount" value="product_amount_`$obj_id`"}
				{$smarty.capture.$product_amount}
			{/if}
			<div class="product-descr">
				<div class="strong">{assign var="product_features" value="product_features_`$obj_id`"}{$smarty.capture.$product_features}</div>
				{assign var="prod_descr" value="prod_descr_`$obj_id`"}{$smarty.capture.$prod_descr}
			</div>
			{if $settings.Appearance.in_stock_field == "Y"}
				{assign var="product_amount" value="product_amount_`$obj_id`"}
				{$smarty.capture.$product_amount}
			{/if}
			
			{if !$smarty.capture.capt_options_vs_qty}
			{assign var="product_options" value="product_options_`$obj_id`"}
			{$smarty.capture.$product_options}
			
			{assign var="qty" value="qty_`$obj_id`"}
			{$smarty.capture.$qty}
			{/if}
			
			{assign var="advanced_options" value="advanced_options_`$obj_id`"}
			{$smarty.capture.$advanced_options}
			
			{assign var="min_qty" value="min_qty_`$obj_id`"}
			{$smarty.capture.$min_qty}
			
			{assign var="product_edp" value="product_edp_`$obj_id`"}
			{$smarty.capture.$product_edp}
		</div>
		
	</div>
	{if $bulk_addition}
	<script type="text/javascript">
	//<![CDATA[
		$('#opt_' + '{$obj_prefix}{$product.product_id} :input').each(function () {$ldelim}
			$(this).attr("disabled", true);
		{$rdelim});
	//]]>
	</script>
	{/if}
	{assign var="form_close" value="form_close_`$obj_id`"}
	{$smarty.capture.$form_close}
</div>
{if !$smarty.foreach.products.last}
<hr />
{/if}
{/hook}
{/foreach}

{if $bulk_addition}
{literal}
<script type="text/javascript">
//<![CDATA[
	$('.cm-item').click(function () {
		(this.checked) ? disable = false : disable = true;
		
		$('#opt_' + $(this).attr('id').replace('bulk_addition_', '')).switchAvailability(disable, false);
	});
//]]>
</script>
{/literal}
{/if}

{if !$no_pagination}
	{include file="common_templates/pagination.tpl"}
{/if}

{/if}

{capture name="mainbox_title"}{$title}{/capture}