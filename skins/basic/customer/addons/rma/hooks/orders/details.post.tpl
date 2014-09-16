{* $Id: details.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $order_info.returned_products}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th>{$lang.sku}</th>
		<th>{$lang.returned_product}</th>
		<th width="5%" class="center">{$lang.quantity}</th>
		<th width="7%" class="center">{$lang.subtotal}</th>
	</tr>
	{foreach from=$order_info.returned_products item="product"}
	<tr {cycle values=",class=\"table-row\""}>
		<td>&nbsp;{$product.product_code}</td>
		<td>
			<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="manage-root-item">{$product.product|unescape}</a>
			{hook name="orders:product_details"}
				{if $product.product_options}
					{include file="common_templates/options_info.tpl" product_options=$product.product_options}
				{/if}
			{/hook}
		<td class="center">{$product.amount}</td>
		<td class="right"><strong>{if $product.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$product.subtotal}{/if}</strong></td>
	</tr>
	{/foreach}
	<tr class="table-footer">
		<td colspan="4">&nbsp;</td>
	</tr>
</table>
{/if}