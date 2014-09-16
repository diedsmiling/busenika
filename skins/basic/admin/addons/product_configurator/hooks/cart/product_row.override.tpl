{* $Id: product_row.override.tpl 10172 2010-07-22 11:45:57Z alexions $ *}

{if $product.item_type == "P" && $product.extra.extra.configuration}
	<tr>
		<td>
			<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a>
		</td>
		{if $show_price}
			<td class="center">{$product.amount}</td>
			<td class="right">{include file="common_templates/price.tpl" value=$product.price span_id="c_`$customer.user_id`_$product.item_id"}</td>
		{/if}
	</tr>
	<tr><td {if $show_price}colspan="3"{/if}>
		<table cellpadding="0" cellspacing="0" border="0" width="90%" class="table margin-bottom" align="center">
		<tr>
			<th width="100%">{$lang.product}</th>
			{if $show_price}
				<th class="center">{$lang.quantity}</th>
				<th class="right">{$lang.price}</th>
			{/if}
		</tr>
		{foreach from=$products item="_product"}
			{if $_product.extra.extra.parent.configuration && $_product.extra.extra.parent.configuration == $product.item_id}
			<tr>
				<td>
					<a href="{"products.update?product_id=`$_product.product_id`"|fn_url}">{$_product.product|unescape}</a>
				</td>
				{if $show_price}
					<td class="center">{$_product.amount}</td>
					<td class="right">{include file="common_templates/price.tpl" value=$_product.price span_id="c_`$customer.user_id`_$_product.item_id"}</td>
				{/if}
			</tr>
			{/if}
		{/foreach}
		</table>
	</td></tr>
{/if}