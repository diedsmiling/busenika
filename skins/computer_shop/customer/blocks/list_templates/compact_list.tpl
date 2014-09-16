{* $Id: compact_list.tpl 9783 2010-06-10 10:24:09Z lexa $ *}

{if $products}

{script src="js/exceptions.js"}

{if !$no_pagination}
	{include file="common_templates/pagination.tpl"}
{/if}
{if !$no_sorting}
	{include file="views/products/components/sorting.tpl"}
{/if}

<form {if $settings.DHTML.ajax_add_to_cart == "Y"}class="cm-ajax"{/if} action="{""|fn_url}" method="post" name="short_list_form{$obj_prefix}">
<input type="hidden" name="result_ids" value="cart_status,wish_list" />

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
<tr>
	<th>&nbsp;</th>
	<th>{$lang.product}</th>
	<th>{$lang.product_code}</th>
	<th>{$lang.price}</th>
	{if $show_add_to_cart}<th>&nbsp;</th>{/if}
</tr>

{foreach from=$products item="product" key="key" name="products"}
	{assign var="obj_id" value=$product.product_id}
	{assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
	{include file="common_templates/product_data.tpl" product=$product}
	{hook name="products:product_compact_list"}
	<tr {cycle values=",class=\"table-row\""} valign="middle">
		<td class="product-image">
			<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width="40" images=$product.main_pair object_type="product" obj_id=$obj_id_prefix show_thumbnail="Y"}</a>
		</td>
		<td class="compact">
			{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
		</td>
		<td class="center">
			{assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku}
		</td>
		<td class="center">
			{assign var="price" value="price_`$obj_id`"}{$smarty.capture.$price}
		</td>
		{if $show_add_to_cart}
		<td class="center nowrap">
			{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}{$smarty.capture.$add_to_cart}
		</td>
		{/if}
	</tr>
	{/hook}
{/foreach}
</table>

</form>
{if !$no_pagination}
	{include file="common_templates/pagination.tpl"}
{/if}

{/if}