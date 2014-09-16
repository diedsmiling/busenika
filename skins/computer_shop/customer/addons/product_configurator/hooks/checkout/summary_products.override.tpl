{* $Id: summary_products.override.tpl 9805 2010-06-16 14:04:53Z alexions $ *}

{if $cart.products.$key.extra.configuration}
{if $smarty.capture.prods}
	<hr class="dark-hr" />
{else}
	{capture name="prods"}Y{/capture}
{/if}

<div class="clear">
	<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>
	<p class="step-complete-wrapper">{$lang.code}: {$product.product_code}</p>
	{include file="common_templates/options_info.tpl" product_options=$product.product_options no_block=true}

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
</div>
{/if}