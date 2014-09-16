{* $Id: items_list.override.tpl 10543 2010-08-30 13:04:31Z alexions $ *}

{if $wishlist.products.$key.extra.configuration}
	<form {if $settings.DHTML.ajax_add_to_cart == "Y" && !$no_ajax}class="cm-ajax"{/if} action="{""|fn_url}" method="post" name="{$form_prefix}productform_{$key}" enctype="multipart/form-data">
	<input type="hidden" name="result_ids" value="cart_status" />
	<input type="hidden" name="product_data[{$key}][product_id]" value="{$product.product_id}" />
	<input type="hidden" name="product_data[{$key}][amount]" value="1" />

	{foreach from=$wishlist.products.$key.extra.configuration key="g_id" item="p_id"}
	{if $p_id|is_array}
	{foreach from=$p_id item="p"}
	<input type="hidden" name="product_data[{$key}][configuration][{$g_id}][]" value="{$p}" />
	{/foreach}
	{else}
	<input type="hidden" name="product_data[{$key}][configuration][{$g_id}]" value="{$p_id}" />
	{/if}
	{/foreach}

	{if $show_hr}
	<hr />
	{else}
		{assign var="show_hr" value=true}
	{/if}

	<div class="product-container clear">
		<div class="product-image">
			<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height obj_id=$key images=$product.main_pair object_type="product" show_thumbnail="Y"}</a>
		</div>
		<div class="product-description">
			<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>&nbsp;<a href="{"wishlist.delete?cart_id=`$key`"|fn_url}"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="{$lang.remove}" title="{$lang.remove}" align="bottom" /></a>

			<p class="sku{if !$product.product_code} hidden{/if}" id="sku_{$key}">{$lang.sku}: <span class="cm-reload-{$key}" id="product_code_update_{$key}">{$product.product_code}<!--product_code_update_{$key}--></span></p>

			<span class="cm-reload-{$key}" id="product_options_update_{$key}">
				<input type="hidden" name="appearance[wishlist]" value="1" />
				{include file="views/products/components/product_options.tpl" product_options=$product.product_options product=$product name="product_data" id=$key location="cart"}
			<!--product_options_update_{$key}--></span>

			<p><strong>{$lang.configuration}:</strong></p>
			
			<span class="cm-reload-{$key}" id="configuration_update_{$key}">
			<table cellpadding="0" cellspacing="0" border="0" width="85%" class="table">
			<tr>
				<th width="50%">{$lang.product}</th>
				<th width="10%">{$lang.price}</th>
			</tr>
			{foreach from=$products item="product_conf" key="key_conf"}
			{if $wishlist.products.$key_conf.extra.parent.configuration == $key}
			<tr {cycle values=",class=\"table-row\""}>
				<td><a href="{"products.view?product_id=`$product_conf.product_id`"|fn_url}">{$product_conf.product}</a></td>
				<td class="center nowrap">
					{include file="common_templates/price.tpl" value=$product_conf.price}&nbsp;&nbsp;</td>
				{math equation="item_price + conf_" item_price=$product_conf.price|default:"0" conf_=$conf_price|default:"0" assign="conf_price"}
			</tr>
			{/if}
			{/foreach}
			<tr {cycle values="class=\"table-row\","}>
				<td colspan="4"><hr /></td>
			</tr>
			<tr {cycle values=",class=\"table-row\""}>
				<td><strong>{$lang.product_summary}:</strong></td>
				<td class="center">
					{assign var="obj_id" value=$key}
					{hook name="products:prices_block"}
						{math equation="item_price + conf_" item_price=$product.price|default:"0" conf_=$conf_price|default:"0" assign="conf_price"}
						<strong>{include file="common_templates/price.tpl" value=$conf_price}</strong>
					{/hook}
				</td>
			</tr>
			<tr class="table-footer">
				<td colspan="2">&nbsp;</td>
			</tr>
			</table>

			{hook name="products:product_option_content"}
			{/hook}
			<!--configuration_update_{$key}--></span>

			{if !($product.zero_price_action == "R" && $product.price == 0) && !($settings.General.inventory_tracking == "Y" && $product.amount <= 0 && $product.is_edp != "Y" && $product.tracking == "B")}
			<div class="buttons-container">
				{include file="buttons/add_to_cart.tpl" but_name="dispatch[checkout.add]" but_role="action"}
			</div>
			{/if}
		</div>
	</div>
	</form>
{/if}