{* $Id: extra_list.post.tpl 10051 2010-07-13 15:49:25Z alexions $ *}

{if $cart.gift_certificates}
{foreach from=$cart.gift_certificates item="gift" key="gift_key"}
{assign var="_colspan" value="4"}
<tr>
	<td class="nowrap">
		<strong>{$lang.gift_certificate}</strong>&nbsp;&nbsp;
		<a href="{"order_management.delete_certificate?gift_cert_cart_id=`$gift_key`"|fn_url}"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="" align="bottom" /></a>
		<p>
			{if $gift.gift_cert_code}{$lang.code}:&nbsp;{$gift.gift_cert_code}{/if}
		</p>
		<p>
			<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_gc_{$gift_key}" class="hand cm-combination" />
			<img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_gc_{$gift_key}" class="hand hidden cm-combination" />
			<a class="cm-combination" id="sw_gc_{$gift_key}">{$lang.details_upper}</a>
		</p>
	</td>
	<td class="nowrap">
		{if !$gift.extra.exclude_from_calculate}{include file="common_templates/price.tpl" value=$gift.subtotal}{else}{$lang.free}{/if}</td>
	<td class="nowrap">1</td>
	{if $cart.use_discount}
	{assign var="_colspan" value=$_colspan+1}
	<td class="nowrap">-</td>
	{/if}
	{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
	{assign var="_colspan" value=$_colspan+1}
	<td class="nowrap">-</td>
	{/if}
	<td class="right nowrap">
		{if !$gift.extra.exclude_from_calculate}{include file="common_templates/price.tpl" value=$gift.subtotal}{else}{$lang.free}{/if}</td>
</tr>
<tr class="hidden" id="gc_{$gift_key}" >
	<td class="nowrap" colspan="{$_colspan}">
		<div class="box">
			<div class="form-field product-list-field">
				<label><strong>{$lang.gift_cert_to}:</strong></label>
				<span>{$gift.recipient}</span>
			</div>
			<div class="form-field product-list-field">
				<label><strong>{$lang.gift_cert_from}:</strong></label>
				<span>{$gift.sender}</span>
			</div>
			<div class="form-field product-list-field">
				<label><strong>{$lang.amount}:</strong></label>
				<span>{include file="common_templates/price.tpl" value=$gift.amount}</span>
			</div>
			<div class="form-field product-list-field">
				<label><strong>{$lang.send_via}:</strong></label>
				<span>{if $gift.send_via == "E"}{$lang.email}{else}{$lang.postal_mail}{/if}</span>
			</div>
			{if $gift.products && $addons.gift_certificates.free_products_allow == "Y"}
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
			<tr>
				<th width="50%">{$lang.product}</th>
				<th width="10%">{$lang.price}</th>
				<th width="10%">{$lang.amount}</th>
				{if $cart.use_discount}
				<th width="10%">{$lang.discount}</th>
				{/if}
				{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
				<th width="10%">{$lang.tax}</th>
				{/if}
				<th class="right" width="10%">{$lang.subtotal}</th>
			</tr>
			{foreach from=$cart_products item="cp" key="key"}
			{if $cart.products.$key.extra.parent.certificate == $gift_key}
			<tr {cycle values=",class=\"table-row\"" name="gc_`$gift_key`"}>
				<td>
					<input type="hidden" name="cart_products[{$key}][amount]" value="{$cp.amount}" />
					<input type="hidden" name="cart_products[{$key}][product_id]" value="{$cp.product_id}" />
					<a href="{"products.view?product_id=`$cp.product_id`"|fn_url}" class="sidebox-link">{$cp.product|truncate:40:"...":true}</a>

					{hook name="order_management:product_info"}
					{if $cp.product_code}
					<p>{$lang.sku}:&nbsp;{$cp.product_code}</p>
					{/if}
					{/hook}
					{if $cp.product_options}<div class="options-info">&nbsp;{include file="common_templates/options_info.tpl" product_options=$cp.product_options}</div>{/if}
				</td>
				<td class="nowrap">
					{include file="common_templates/price.tpl" value=$cp.base_price}</td>
				<td class="nowrap">
					{$cp.amount}</td>
				{if $cart.use_discount}
				<td class="nowrap">
					{if $cp.discount|floatval}{include file="common_templates/price.tpl" value=$cp.discount}{else}-{/if}</td>
				{/if}
				{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
				<td class="nowrap">
					{include file="common_templates/price.tpl" value=$cp.tax_summary.total}</td>
				{/if}
				<td class="right nowrap">
					{include file="common_templates/price.tpl" value=$cp.display_subtotal}</td>
			</tr>
				{/if}
			{/foreach}
			</table>
			{/if}
		</div>
		</td>
</tr>
{/foreach}

{/if}
