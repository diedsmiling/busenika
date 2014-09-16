{* $Id: view.post.tpl 10220 2010-07-27 09:09:00Z alexions $ *}

{if $wishlist.gift_certificates}

{foreach from=$wishlist.gift_certificates item="gift" key="gift_key" name="gift_certificates"}

{if $show_hr}
<hr />
{else}
	{assign var="show_hr" value=true}
{/if}

<form action="{""|fn_url}" {if $settings.DHTML.ajax_add_to_cart == "Y"}class="cm-ajax"{/if} method="post" name="{$form_prefix}gift_cert_form_{$gift_key}">

<input type="hidden" value="cart_status,wish_list" name="result_ids" />
<input type="hidden" name="gift_cert_data[send_via]" value="{$gift.send_via}" />
<input type="hidden" name="gift_cert_data[amount_type]" value="{$gift.amount_type}" />
<input type="hidden" name="gift_cert_data[amount]" value="{$gift.amount}" />
<input type="hidden" name="gift_cert_data[correct_amount]" value="N" />
<input type="hidden" name="gift_cert_data[recipient]" value="{$gift.recipient}" />
<input type="hidden" name="gift_cert_data[sender]" value="{$gift.sender}" />
<input type="hidden" name="gift_cert_data[message]" value="{$gift.message}" />
{if $gift.email}<input type="hidden" name="gift_cert_data[email]" value="{$gift.email}" />{/if}
{if $gift.title}<input type="hidden" name="gift_cert_data[title]" value="{$gift.title}" />{/if}
{if $gift.firstname}<input type="hidden" name="gift_cert_data[firstname]" value="{$gift.firstname}" />{/if}
{if $gift.lastname}<input type="hidden" name="gift_cert_data[lastname]" value="{$gift.lastname}" />{/if}
{if $gift.address}<input type="hidden" name="gift_cert_data[address]" value="{$gift.address}" />{/if}
{if $gift.city}<input type="hidden" name="gift_cert_data[city]" value="{$gift.city}" />{/if}
{if $gift.country}<input type="hidden" name="gift_cert_data[country]" value="{$gift.country}" />{/if}
{if $gift.state}<input type="hidden" name="gift_cert_data[state]" value="{$gift.state}" />{/if}
{if $gift.zipcode}<input type="hidden" name="gift_cert_data[zipcode]" value="{$gift.zipcode}" />{/if}

<div class="product-container">
	<div class="product-image" style="width: {$settings.Thumbnails.product_lists_thumbnail_width}px;">
		<p><a href="{"gift_certificates.update?gift_cert_wishlist_id=`$gift_key`"|fn_url}"><img src="{$images_dir}/icons/gift_certificates_cart_icon.gif" width="73" height="76" border="0" alt="{$lang.gift_certificate}" title="{$lang.gift_certificate}" /></a></p>

		<p class="center">{include file="buttons/button.tpl" but_text=$lang.edit but_href="gift_certificates.update?gift_cert_wishlist_id=$gift_key" but_role="text"}</p>
	</div>
	<div class="product-description">
		<a href="{"gift_certificates.update?gift_cert_wishlist_id=`$gift_key`"|fn_url}" class="product-title">{$lang.gift_certificate}</a>&nbsp;<a href="{"gift_certificates.wishlist_delete?gift_cert_wishlist_id=`$gift_key`"|fn_url}"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="{$lang.remove}" title="{$lang.remove}" align="bottom" /></a>
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

		<p><strong>{$lang.free_products}:</strong></p>
		
		<div class="product-options">
		{assign var="gift_price" value=""}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="50%">{$lang.product}</th>
			<th width="10%">{$lang.price}</th>
			<th width="10%">{$lang.quantity}</th>
			<th class="right" width="10%">{$lang.subtotal}</th>
		</tr>
		{foreach from=$products item="_product" key="key_cert_prod"}

		{if $wishlist.products.$key_cert_prod.extra.parent.certificate == $gift_key}

		<input type="hidden" name="gift_cert_data[products][{$key_cert_prod}][product_id]" value="{$wishlist.products.$key_cert_prod.product_id}" />
		<input type="hidden" name="gift_cert_data[products][{$key_cert_prod}][amount]" value="{$wishlist.products.$key_cert_prod.amount}" />

		{math equation="item_price + gift_" item_price=$_product.subtotal|default:"0" gift_=$gift_price|default:"0" assign="gift_price"}
		<tr {cycle values=",class=\"table-row\""}>
			<td>
				<a href="{"products.view?product_id=`$_product.product_id`"|fn_url}">{$_product.product}</a>
				{if $_product.product_options}
					{include file="common_templates/options_info.tpl" product_options=$_product.product_options fields_prefix="gift_cert_data[products][`$key_cert_prod`][product_options]"}
				{/if}
			</td>
			<td class="center">
				{include file="common_templates/price.tpl" value=$_product.price}</td>
			<td class="center nowrap">
				{$gift.products.$key_cert_prod.amount}</td>
			<td class="right nowrap">
				{math equation="item_price*amount" item_price=$_product.price|default:"0" assign="subtotal" amount=$gift.products.$key_cert_prod.amount}
				{math equation="subtotal + gift_" subtotal=$subtotal|default:"0" gift_=$gift_price|default:"0" assign="gift_price"}
				{include file="common_templates/price.tpl" value=$subtotal}</td>
		</tr>
		{/if}

		{/foreach}
		<tr class="table-footer">
			<td colspan="4">&nbsp;</td>
		</tr>
		</table>
		</div>

		<div class="form-field product-list-field">
			<label>{$lang.price_summary}:</label>
			<span>{math equation="item_price + gift_" item_price=$gift_price|default:"0" gift_=$gift.amount|default:"0" assign="gift_price"}
				<strong>{include file="common_templates/price.tpl" value=$gift_price}</strong></span>
		</div>
		{/if}

		<div class="buttons-container">
			{include file="buttons/add_to_cart.tpl" but_name="dispatch[gift_certificates.add]"}
		</div>
	</div>
</div>

</form>

{/foreach}

{/if}