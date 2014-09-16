{* $Id: extra_list.post.tpl 10224 2010-07-27 11:16:17Z alexions $ *}

{if $cart.gift_certificates}

{foreach from=$cart.gift_certificates item="gift" key="gift_key" name="f_gift_certificates"}
{if $smarty.capture.prods}
	<hr class="dark-hr" />
{else}
	{capture name="prods"}Y{/capture}
{/if}
<div class="clear">
	{if $mode == "cart"}
	<div class="product-image center" style="width: {$settings.Thumbnails.product_lists_thumbnail_width}px;">
		{if !$gift.extra.exclude_from_calculate}
			<a href="{"gift_certificates.update?gift_cert_id=`$gift_key`"|fn_url}">
			<img src="{$images_dir}/icons/gift_certificates_cart_icon.gif" width="73" height="76" border="0" alt="{$lang.gift_certificate}" title="{$lang.gift_certificate}" />
			</a>
			<p class="center">{include file="buttons/button.tpl" but_text=$lang.edit but_href="gift_certificates.update?gift_cert_id=$gift_key" but_role="text"}</p>
		{else}
			<img src="{$images_dir}/icons/gift_certificates_cart_icon.gif" width="73" height="76" border="0" alt="{$lang.gift_certificate}" title="{$lang.gift_certificate}" />
		{/if}
	</div>
	{/if}
	<div class="product-description">
		{if !$gift.extra.exclude_from_calculate}
			<a href="{"gift_certificates.update?gift_cert_id=`$gift_key`"|fn_url}" class="product-title">{$lang.gift_certificate}</a>&nbsp;
			{if $use_ajax == true && $cart.amount != 1}
				{assign var="ajax_class" value="cm-ajax"}
			{/if}
			<a class="{$ajax_class}" href="{"gift_certificates.delete?gift_cert_id=`$gift_key`&redirect_mode=`$mode`"|fn_url}" rev="cart_items,cart_status,checkout_totals,checkout_steps"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="{$lang.remove}" title="{$lang.remove}" align="bottom" /></a>
		{else}
			<strong>{$lang.gift_certificate}</strong>
		{/if}
		<div class="form-field product-list-field">
			<label class="valign">{$lang.gift_cert_to}:</label>{$gift.recipient}
		</div>
		<div class="form-field product-list-field">
			<label class="valign">{$lang.gift_cert_from}:</label>{$gift.sender}
		</div>
		<div class="form-field product-list-field">
			<label class="valign">{$lang.amount}:</label>{include file="common_templates/price.tpl" value=$gift.amount}
		</div>
		<div class="form-field product-list-field">
			<label class="valign">{$lang.send_via}:</label>{if $gift.send_via == "E"}{$lang.email}{else}{$lang.postal_mail}{/if}
		</div>
		{if $gift.products && $addons.gift_certificates.free_products_allow == "Y" && !$gift.extra.exclude_from_calculate}
		
		<p><a id="sw_gift_products_{$gift_key}" class="cm-combo-on cm-combination">{$lang.free_products}</a></p>

		<div id="gift_products_{$gift_key}" class="product-options hidden">
			<table cellpadding="0" cellspacing="0" border="0" class="table fixed-layout" width="100%">
			<tr>
				<th width="40%">{$lang.product}</th>
				<th width="15%">{$lang.price}</th>
				<th width="15%">{$lang.quantity}</th>
				{if $cart.use_discount}
				<th width="15%">{$lang.discount}</th>
				{/if}
				{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
				<th width="15%">{$lang.tax}</th>
				{/if}
				<th class="right" width="16%">{$lang.subtotal}</th>
			</tr>
			{foreach from=$cart_products item="product" key="key"}
			{if $cart.products.$key.extra.parent.certificate == $gift_key}
			<tr {cycle values=",class=\"table-row\""}>
				<td width="30%">
					<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" title="{$product.product|unescape}">{$product.product|unescape|strip_tags|truncate:70:"...":true}</a>
					{if $use_ajax == true}
						{assign var="ajax_class" value="cm-ajax"}
					{/if}
					<a class="{$ajax_class}" href="{"checkout.delete?cart_id=`$key`&amp;redirect_mode=`$mode`"|fn_url}" rev="cart_items,checkout_totals,cart_status,checkout_steps"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="" align="bottom" /></a>
					<p>{include file="common_templates/options_info.tpl" product_options=$cart.products.$key.product_options|fn_get_selected_product_options_info fields_prefix="cart_products[`$key`][product_options]"}</p>
					{hook name="checkout:product_info"}{/hook}
					<input type="hidden" name="cart_products[{$key}][extra][parent][certificate]" value="{$gift_key}" /></td>
				<td class="center">
					{include file="common_templates/price.tpl" value=$product.original_price}</td>
				<td class="center">
					<input type="text" size="3" name="cart_products[{$key}][amount]" value="{$product.amount}" class="input-text-short" {if $product.is_edp == "Y"}readonly="readonly"{/if} />
					<input type="hidden" name="cart_products[{$key}][product_id]" value="{$product.product_id}" /></td>
				{if $cart.use_discount}
				<td class="center">
					{if $product.discount|floatval}{include file="common_templates/price.tpl" value=$product.discount}{else}-{/if}</td>
				{/if}
				{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
				<td class="center">
					{include file="common_templates/price.tpl" value=$product.tax_summary.total}</td>
				{/if}
				<td class="right">
					{include file="common_templates/price.tpl" value=$product.display_subtotal}</td>
			</tr>
			{/if}
			{/foreach}
			<tr class="table-footer">
				<td colspan="{if $cart.use_discount && $cart.taxes && $settings.General.tax_calculation != "subtotal"}6{elseif $cart.use_discount || $cart.taxes && $settings.General.tax_calculation != "subtotal"}5{else}4{/if}">&nbsp;</td>
			</tr>
			</table>
			<div class="form-field product-list-field float-right nowrap">
				<p><label class="valign">{$lang.price_summary}:</label>
				{if !$gift.extra.exclude_from_calculate}{include file="common_templates/price.tpl" value=$gift.display_subtotal class="price"}{else}<span class="price">{$lang.free}</span>{/if}</p>
			</div>
		</div>
		{/if}
	</div>
</div>
{/foreach}
{/if}
