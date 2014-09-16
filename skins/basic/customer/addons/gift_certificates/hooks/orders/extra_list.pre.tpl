{* $Id: extra_list.pre.tpl 10402 2010-08-12 08:18:09Z klerik $ *}
{if $order_info.gift_certificates}
{foreach from=$order_info.gift_certificates item="gift" key="gift_key"}
{cycle values=",table-row" name="class_cycle" assign="_class"}
{assign var="_colspan" value="4"}
<tr class="{$_class}">
	<td>
		<div class="clear">
			<strong class="float-left">{$lang.gift_certificate}</strong>
			<div class="float-right">{include file="buttons/button_popup_link.tpl" but_href="gift_certificates.print?order_id=`$order_info.order_id`&gift_cert_cart_id=`$gift_key`" but_text=$lang.print_card but_role="text" width="750" height="350"}</div>
		</div>
		{if $gift.gift_cert_code}
		<div class="product-list-field">
			<label>{$lang.code}:</label>
			<a href="{"gift_certificates.verify?verify_code=`$gift.gift_cert_code`"|fn_url}">{$gift.gift_cert_code}</a>
		</div>
		{/if}
		<p>
			&nbsp;<img src="{$images_dir}/icons/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_gift_{$gift_key}" class="hand cm-combination" /><img src="{$images_dir}/icons/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_gift_{$gift_key}" class="hand cm-combination hidden" /><a class="cm-combination" id="sw_gift_{$gift_key}">{$lang.details_upper}</a>
		</p>
	</td>
	<td class="right nowrap">{if !$gift.extra.exclude_from_calculate}{include file="common_templates/price.tpl" value=$gift.display_subtotal}{else}{$lang.free}{/if}</td>
	<td class="center">&nbsp;1</td>
	{if $order_info.use_discount}
	{assign var="_colspan" value=$_colspan+1}
	<td class="right">-</td>
	{/if}
	{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
	{assign var="_colspan" value=$_colspan+1}
	<td class="center">-</td>
	{/if}
	<td class="right">&nbsp;<strong>{if !$gift.extra.exclude_from_calculate}{include file="common_templates/price.tpl" value=$gift.display_subtotal}{else}{$lang.free}{/if}</strong></td>
</tr>
<tr class="{$_class} hidden" id="gift_{$gift_key}">
	<td colspan="{$_colspan}">
	<div class="box">
		<div class="form-field product-list-field">
			<label>{$lang.gift_cert_to}:</label>
			<span>{$gift.recipient}</span>
		</div>
		<div class="form-field product-list-field">
			<label>{$lang.gift_cert_from}:</label>
			<span>{$gift.sender}</span>
		</div>
		<div class="form-field product-list-field">
			<label>{$lang.amount}:</label>
			<span>{include file="common_templates/price.tpl" value=$gift.amount}</span>
		</div>
		<div class="form-field product-list-field">
			<label>{$lang.send_via}:</label>
			<span>{if $gift.send_via == "E"}{$lang.email}{else}{$lang.postal_mail}{/if}</span>
		</div>
		{if $gift.products && $addons.gift_certificates.free_products_allow == "Y"}
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
		{foreach from=$order_info.items item="product" key="key"}
		{if $product.extra.parent.certificate && $product.extra.parent.certificate == $gift_key}
		<tr {cycle values=",class=\"table-row\"" name="gc_`$gift_key`"} valign="top">
			<td>
				{if $product.product}
					{if !$product.deleted_product}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{/if}{$product.product|truncate:50:"...":true}{if !$product.deleted_product}</a>{/if}
				{else}
					{$lang.deleted_product}
				{/if}
				{hook name="orders:product_info"}
				{if $product.product_code}
				<p>{$lang.code}:&nbsp;{$product.product_code}</p>
				{/if}
				{/hook}
				{if $product.product_options}<p>{include file="common_templates/options_info.tpl" product_options=$product.product_options}</p>{/if}
			</td>
			<td class="center nowrap">
				{include file="common_templates/price.tpl" value=$product.original_price}</td>
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
		{/if}
		</div>
	</td>
</tr>
{/foreach}

{/if}
