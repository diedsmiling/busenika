{* $Id: items_list_row.override.tpl 9767 2010-06-08 14:32:08Z lexa $ *}

{if $product.extra.buy_together}
	{assign var="conf_price" value=$product.price|default:"0"}
	{assign var="conf_subtotal" value=$product.display_subtotal|default:"0"}
	{assign var="conf_discount" value=$product.extra.discount|default:"0"}
	{assign var="conf_tax" value=$product.tax_value|default:"0"}

	{assign var="_colspan" value=4}
	{assign var="c_product" value=$product}
	{foreach from=$order_info.items item="sub_oi"}
		{if $sub_oi.extra.parent.buy_together && $sub_oi.extra.parent.buy_together == $product.cart_id}
			{capture name="is_conf"}1{/capture}
			{math equation="item_price * amount + conf_price" amount=$sub_oi.extra.min_qty|default:"1" item_price=$sub_oi.price|default:"0" conf_price=$conf_price assign="conf_price"}
			{math equation="discount + conf_discount" discount=$sub_oi.extra.discount|default:"0" conf_discount=$conf_discount assign="conf_discount"}
			{math equation="tax + conf_tax" tax=$sub_oi.tax_value|default:"0" conf_tax=$conf_tax assign="conf_tax"}
			{math equation="subtotal + conf_subtotal" subtotal=$sub_oi.display_subtotal|default:"0" conf_subtotal=$conf_subtotal assign="conf_subtotal"}	
		{/if}
	{/foreach}

	{cycle values=",table-row" name="class_cycle" assign="_class"}
	<tr class="{$_class}" valign="top">
		<td valign="top">
			<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>
			{if $product.extra.is_edp == "Y"}
			<div class="right"><a href="{"orders.order_downloads?order_id=`$order_info.order_id`"|fn_url}"><strong>[{$lang.download}]</strong></a></div>
			{/if}
			{if $product.product_code}
			<p>{$lang.code}:&nbsp;{$product.product_code}</p>
			{/if}
			{hook name="orders:product_info"}
			{if $product.product_options}{include file="common_templates/options_info.tpl" product_options=$product.product_options}{/if}
			{/hook}
			
			{if $smarty.capture.is_conf}
			<p><img src="{$images_dir}/icons/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_conf_{$key}" class="hand cm-combination" /><img src="{$images_dir}/icons/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_conf_{$key}" class="hand cm-combination hidden" /><a class="cm-combination" id="sw_conf_{$key}">{$lang.buy_together}</a></p>
			{/if}
		</td>
		<td class="right">{include file="common_templates/price.tpl" value=$conf_price}</td>
		<td class="center">&nbsp;{$product.amount}</td>
		{if $order_info.use_discount}
		{assign var="_colspan" value=$_colspan+1}
		<td class="right">
			{include file="common_templates/price.tpl" value=$conf_discount}</td>
		{/if}
		{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
		{assign var="_colspan" value=$_colspan+1}
		<td class="center">
			{include file="common_templates/price.tpl" value=$conf_tax}</td>
		{/if}
		<td class="right">&nbsp;<strong>{include file="common_templates/price.tpl" value=$conf_subtotal}</strong></td>
	</tr>
	{if $smarty.capture.is_conf}
	<tr class="{$_class} hidden" id="conf_{$key}">
		<td colspan="{$_colspan}">
		<div class="box">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
			<tr>
				<th>{$lang.product}</th>
				<th>{$lang.price}</th>
				<th>{$lang.quantity}</th>
				{if $order_info.use_discount}
				<th>{$lang.discount}</th>
				{/if}
				{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
				<th>{$lang.tax}</th>
				{/if}
				<th>{$lang.subtotal}</th>
			</tr>
			{foreach from=$order_info.items item="product" key="sub_key"}
			{if $product.extra.parent.buy_together && $product.extra.parent.buy_together == $c_product.cart_id}
			<tr {cycle values=",class=\"table-row\"" name="gc_`$gift_key`"} valign="top">
				<td>
					<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape|truncate:50:"...":true}</a>&nbsp;
					{if $product.product_code}
					<p>{$lang.code}:&nbsp;{$product.product_code}</p>
					{/if}
					{hook name="orders:product_info"}
					{if $product.product_options}
						{include file="common_templates/options_info.tpl" product_options=$product.product_options}
					{/if}
					{/hook}
				</td>
				<td class="center nowrap">
					{include file="common_templates/price.tpl" value=$product.price}</td>
				<td class="center nowrap">
					{$product.amount}</td>
				{if $order_info.use_discount}
				<td class="right nowrap">
					{if $product.extra.discount|floatval}{include file="common_templates/price.tpl" value=$product.extra.discount}{else}-{/if}</td>
				{/if}
				{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
				<td class="center nowrap">
					{include file="common_templates/price.tpl" value=$product.tax_value}</td>
				{/if}
				<td class="right nowrap">
					{include file="common_templates/price.tpl" value=$product.display_subtotal}</td>
			</tr>
			{/if}
			{/foreach}
			<tr class="table-footer">
				<td colspan="10">&nbsp;</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	{/if}
{/if}
