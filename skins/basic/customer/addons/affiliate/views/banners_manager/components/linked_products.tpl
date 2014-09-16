{* $Id: linked_products.tpl 2474 2006-11-05 11:39:30Z zeke $ *}

<div id="content_linked_products">
<a name="linked_products"></a>

{*$lang.linked_products*}

{if $linked_products}
<form action="{""|fn_url}" method="post" name="linked_products_form">
<input type="hidden" name="banner_id" value="{$banner_id}" />
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table margin-top">
<tr>
	<th><input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.product_name}</th>
	<th>{$lang.url}</th>
</tr>
{foreach from=$linked_products item=product}
	<tr {cycle values=",class=\"table-row\""}>
		<td><input type="checkbox" class="checkbox cm-item" name="delete[]" value="{$product.product_id}" /></td>
		<td>
			{include file="common_templates/popupbox.tpl" id="product_`$product.product_id`" link_text=$product.product text=$lang.product href="banner_products.view?product_id=`$product.product_id`"}</td>
		<td>
			{$product.url}</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="3"><p class="no-items">{$lang.text_all_items_included|replace:"[items]":$lang.products}</p></td>
	</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="3">&nbsp;</td>
</tr>
</table>

{if $linked_products}
<div class="buttons-container">
	{include file="buttons/button.tpl" but_name="dispatch[banners_manager.do_delete_linked]" but_text=$lang.delete_selected}
</div>
</form>
{/if}

{include file="pickers/products_picker.tpl" view_mode="button" but_text=$lang.add_products_to_section extra_var="dispatch=banners_manager.do_add_linked&banner_id=`$banner_id`" display="affiliate" data_id="linked_products_list"}
</div>