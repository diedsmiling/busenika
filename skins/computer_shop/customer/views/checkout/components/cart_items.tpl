{* $Id: cart_items.tpl 10291 2010-08-02 07:33:30Z angel $ *}

{capture name="cartbox"}

<div id="cart_items">
{if $mode == "checkout"}
	{if $cart.coupons|floatval}<input type="hidden" name="c_id" value="" />{/if}
	{hook name="checkout:form_data"}
	{/hook}
{/if}

{if $cart_products}

{assign var="prods" value=false}
{foreach from=$cart_products item="product" key="key" name="cart_products"}
{assign var="obj_id" value=$product.object_id|default:$key}
{hook name="checkout:items_list"}
{if !$cart.products.$key.extra.parent}
<div class="clear">
	{if $smarty.capture.prods}
		<hr class="dark-hr" />
	{else}
		{capture name="prods"}Y{/capture}
	{/if}
	{if $mode == "cart" || $show_images}
	<div class="product-image cm-reload-{$obj_id}" id="product_image_update_{$obj_id}">
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
		{include file="common_templates/image.tpl" obj_id=$key images=$product.main_pair object_type="product" show_thumbnail="Y" image_width=$settings.Thumbnails.product_cart_thumbnail_width image_height=$settings.Thumbnails.product_cart_thumbnail_height}</a>
	<!--product_image_update_{$obj_id}--></div>
	{/if}
	<div class="product-description">
		{if $use_ajax == true && $cart.amount != 1}
			{assign var="ajax_class" value="cm-ajax"}
		{/if}
		
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>{if !$product.exclude_from_calculate}&nbsp;<a class="{$ajax_class}" href="{"checkout.delete&amp;cart_id=`$key`&amp;redirect_mode=`$mode`"|fn_url}" rev="cart_items,checkout_totals,cart_status,checkout_steps"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="{$lang.remove}" align="bottom" title="{$lang.remove}" /></a>{/if}
		
		<p class="sku{if !$product.product_code} hidden{/if}" id="sku_{$key}">
			{$lang.sku}: <span class="cm-reload-{$obj_id}" id="product_code_update_{$obj_id}">{$product.product_code}<!--product_code_update_{$obj_id}--></span>
		</p>
		
		<div class="quantity cm-reload-{$obj_id}{if $settings.Appearance.quantity_changer == "Y"} changer{/if}" id="quantity_update_{$obj_id}">
			<input type="hidden" name="cart_products[{$key}][product_id]" value="{$product.product_id}" />
			{if $product.exclude_from_calculate}<input type="hidden" name="cart_products[{$key}][extra][exclude_from_calculate]" value="{$product.exclude_from_calculate}" />{/if}

			<label for="amount_{$key}">{$lang.qty}:</label>
			{if $product.qty_content && $product.is_edp != "Y"}
			<select name="cart_products[{$key}][amount]" id="amount_{$key}" onchange="fn_change_options({$obj_id})">
			{foreach from=$product.qty_content item="var"}
				<option value="{$var}"{if $product.amount == $var} selected="selected"{/if}>{$var}</option>
			{/foreach}
			</select>
			{else}
				{if $settings.Appearance.quantity_changer == "Y"}
				<div class="center valign cm-value-changer">
				<a class="cm-increase"><img src="{$images_dir}/icons/up_arrow.gif" width="11" height="5" border="0" /></a>
				{/if}
				<input type="text" size="3" id="amount_{$key}" name="cart_products[{$key}][amount]" value="{$product.amount}" class="input-text-short cm-amount{if $product.is_edp == "Y" || $product.exclude_from_calculate} disabled{/if}" {if $product.is_edp == "Y" || $product.exclude_from_calculate}disabled="disabled"{/if} />
				{if $settings.Appearance.quantity_changer == "Y"}
				<a class="cm-decrease"><img src="{$images_dir}/icons/down_arrow.gif" width="11" height="5" border="0" /></a>
				</div>
				{/if}
			{/if}
			{if $product.is_edp == "Y" || $product.exclude_from_calculate}
				<input type="hidden" name="cart_products[{$key}][amount]" value="{$product.amount}" />
			{/if}
			{if $product.is_edp == "Y"}
				<input type="hidden" name="cart_products[{$key}][is_edp]" value="Y" />
			{/if}
			x&nbsp;{include file="common_templates/price.tpl" value=$product.display_price span_id="product_price_`$key`" class="sub-price"}
			&nbsp;=&nbsp;&nbsp;&nbsp;{include file="common_templates/price.tpl" value=$product.display_subtotal span_id="product_subtotal_`$key`" class="price"}
			{if $product.zero_price_action == "A"}
				<input type="hidden" name="cart_products[{$key}][price]" value="{$product.base_price}" />
			{/if}
		<!--quantity_update_{$obj_id}--></div>
		
		{assign var="name" value="product_options_$key"}
		{capture name=$name}
		{if $product.product_options}
			<div class="cm-reload-{$obj_id}" id="options_update_{$obj_id}">
			{include file="views/products/components/product_options.tpl" product_options=$product.product_options product=$product name="cart_products" id=$key location="cart" disable_ids=$disable_ids form_name="checkout_form"}
			<!--options_update_{$obj_id}--></div>
		{/if}

		{capture name="product_info_update"}
		{hook name="checkout:product_info"}
		    {if $product.exclude_from_calculate}
				<strong><span class="price">{$lang.free}</span></strong>
			{elseif $product.discount|floatval || $product.taxes}
				<table class="table" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th>{$lang.price}</th>
					<th>{$lang.quantity}</th>
					{if $product.discount|floatval}<th>{$lang.discount}</th>{/if}
					{if $product.taxes && $settings.General.tax_calculation != "subtotal"}<th>{$lang.tax}</th>{/if}
					<th>{$lang.subtotal}</th>
				</tr>
				<tr>
					<td>{include file="common_templates/price.tpl" value=$product.base_price span_id="original_price_`$key`" class="none"}</td>
					<td class="center">{$product.amount}</td>
					{if $product.discount|floatval}<td>{include file="common_templates/price.tpl" value=$product.discount span_id="discount_subtotal_`$key`" class="none"}</td>{/if}
					{if $product.taxes && $settings.General.tax_calculation != "subtotal"}<td>{include file="common_templates/price.tpl" value=$product.tax_summary.total span_id="tax_subtotal_`$key`" class="none"}</td>{/if}
					<td>{include file="common_templates/price.tpl" span_id="product_subtotal_2_`$key`" value=$product.display_subtotal class="none"}</td>
				</tr>
				<tr class="table-footer">
					<td colspan="5">&nbsp;</td>
				</tr>
				</table>
			{/if}
			{include file="views/companies/components/product_company_data.tpl" company_id=$product.company_id}
		{/hook}
		{/capture}
		{if $smarty.capture.product_info_update|trim}
			<div class="cm-reload-{$obj_id}" id="product_info_update_{$obj_id}">
				{$smarty.capture.product_info_update}
			<!--product_info_update_{$obj_id}--></div>
		{/if}
		{/capture}
		
		{if $smarty.capture.$name|trim}
		<p><a id="sw_options_{$key}" class="cm-combo-on cm-combination">{$lang.text_click_here}</a></p>

		<div id="options_{$key}" class="product-options hidden">
			{$smarty.capture.$name}
		</div>
		{/if}
	</div>
</div>
{/if}
{/hook}
{/foreach}
{/if}

{hook name="checkout:extra_list"}
{/hook}

<!--cart_items--></div>
{/capture}
{include file="common_templates/mainbox_cart.tpl" title=$lang.cart_items content=$smarty.capture.cartbox}
