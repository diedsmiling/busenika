{* $Id: items_list.override.tpl 10543 2010-08-30 13:04:31Z alexions $ *}

{if $cart.products.$key.extra.configuration}
{if $smarty.capture.prods}
	<hr class="dark-hr" />
{else}
	{capture name="prods"}Y{/capture}
{/if}

<div class="clear">

	{if $smarty.const.MODE == "cart"}
	<div class="product-image">
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
		{include file="common_templates/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height obj_id=$key images=$product.main_pair object_type="product" show_thumbnail="Y"}
		</a>
		<p class="center">
			{include file="buttons/button.tpl" but_text=$lang.edit but_href="products.view?product_id=`$product.product_id`&amp;cart_id=`$key`" but_role="text"}
		</p>
	</div>
	{/if}

	<div class="product-description">
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>&nbsp;<a href="{"checkout.delete?cart_id=`$key`&amp;redirect_mode=`$smarty.const.MODE`"|fn_url}"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="{$lang.remove}" title="{$lang.remove}" align="bottom" /></a>
		<div class="sku{if !$product.product_code} hidden{/if}" id="sku_{$key}">{$lang.sku}: <span id="product_code_{$key}">{$product.product_code}</span></div>

		<div class="quantity cm-reload-{$key}" id="quantity_update_{$key}">
			<label for="amount_{$key}">{$lang.qty}:</label>
			<input type="text" size="3" id="amount_{$key}" name="cart_products[{$key}][amount]" value="{$product.amount}" class="input-text-short{if $product.is_edp == "Y" || $product.exclude_from_calculate} disabled{/if}"{if $product.is_edp == "Y" || $product.exclude_from_calculate} disabled="disabled"{/if} />
			<input type="hidden" name="cart_products[{$key}][product_id]" value="{$product.product_id}" />
			{if $product.is_edp == "Y" || $product.exclude_from_calculate}
				<input type="hidden" name="cart_products[{$key}][amount]" value="{$product.amount}" />
			{/if}
			x&nbsp;{include file="common_templates/price.tpl" value=$product.display_price span_id="product_conf_price_`$key`" class="sub-price"}
			&nbsp;=&nbsp;&nbsp;&nbsp;{include file="common_templates/price.tpl" value=$product.display_subtotal span_id="product_conf_subtotal_`$key`" class="price"}
		<!--quantity_update_{$key}--></div>

		<p><a id="sw_options_{$key}" class="cm-combo-on cm-combination">{$lang.text_click_here}</a></p>

		<div class="product-options hidden" id="options_{$key}">
			<div class="cm-reload-{$key}" id="options_update_{$key}">
				{include file="views/products/components/product_options.tpl" product_options=$product.product_options product=$product name="cart_products" id=$key location="cart"}
			<!--options_update_{$key}--></div>
			{hook name="checkout:product_info"}
			<div class="form-field">
				<label>{$lang.base_price}:</label>
				{include file="common_templates/price.tpl" value=$product.base_price span_id="product_price_`$key`" class="sub-price"}
			</div>

			{foreach from=$cart_products item="_product" key="key_conf"}
				{if $cart.products.$key_conf.extra.parent.configuration == $key}
					{capture name="is_conf_prod"}1{/capture}
				{/if}
			{/foreach}

			{if $smarty.capture.is_conf_prod}
				<p><strong>{$lang.configuration}:</strong></p>
				
				<table cellpadding="0" cellspacing="0" border="0" width="85%" class="table margin-top">
				<tr>
					<th width="50%">{$lang.product}</th>
					<th width="10%">{$lang.price}</th>
					<th width="10%">{$lang.quantity}</th>
					<th class="right" width="10%">{$lang.subtotal}</th>
				</tr>
				{foreach from=$cart_products item="_product" key="key_conf"}
				{if $cart.products.$key_conf.extra.parent.configuration == $key}
				<tr {cycle values=",class=\"table-row\""}>
					<td><a href="{"products.view?product_id=`$_product.product_id`"|fn_url}">{$_product.product}</a></td>
					<td class="center">
						{include file="common_templates/price.tpl" value=$_product.price}</td>
					<td class="center">
						<input type="hidden" name="cart_products[{$key_conf}][product_id]" value="{$_product.product_id}" />
						{$_product.amount}
					</td>
					<td class="right">
						{include file="common_templates/price.tpl" value=$_product.display_subtotal}</td>
				</tr>
				{/if}
				{/foreach}
				<tr class="table-footer">
					<td colspan="4">&nbsp;</td>
				</tr>
				</table>
			{/if}
			{/hook}
		</div>
	</div>
</div>
{/if}