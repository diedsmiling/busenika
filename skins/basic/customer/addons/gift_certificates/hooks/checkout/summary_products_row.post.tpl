{* $Id: summary_products_row.post.tpl 10208 2010-07-26 13:22:36Z angel $ *}

{if $cart.gift_certificates}
<tr {cycle values=",class=\"table-row\""}><td colspan="2">
{foreach from=$cart.gift_certificates item="gift" key="gift_key" name="f_gift_certificates"}
<div class="clear">
	<div class="product-description">
		{if !$gift.extra.exclude_from_calculate}
			<a href="{"gift_certificates.update?gift_cert_id=`$gift_key`"|fn_url}" class="product-title">{$lang.gift_certificate}</a>&nbsp;
		{else}
			<strong>{$lang.gift_certificate}</strong>
		{/if}
		<div class="form-field product-list-field">
			<strong>{$lang.gift_cert_to}</strong>: {$gift.recipient},
			<strong>{$lang.gift_cert_from}</strong>: {$gift.sender},
			<strong>{$lang.amount}</strong>: {include file="common_templates/price.tpl" value=$gift.amount},
			<strong>{$lang.send_via}</strong>: {if $gift.send_via == "E"}{$lang.email}{else}{$lang.postal_mail}{/if}
		</div>
		
		{if $gift.products && $addons.gift_certificates.free_products_allow == "Y" && !$gift.extra.exclude_from_calculate}
		
		<p><a id="sw_gift_products_{$gift_key}" class="cm-combo-on cm-combination">{$lang.free_products}</a></p>

		<div id="gift_products_{$gift_key}" class="product-options hidden">
			<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
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
					<p>{include file="common_templates/options_info.tpl" product_options=$cart.products.$key.product_options|fn_get_selected_product_options_info fields_prefix="cart_products[`$key`][product_options]"}</p>
				<td class="center">
					{include file="common_templates/price.tpl" value=$product.original_price}</td>
				<td class="center">
					{$product.amount}
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
</td></tr>
{/if}