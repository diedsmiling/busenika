{* $Id:	select_product.tpl	0 2006-07-14 19:49:30Z	seva $	*}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="30%">{$lang.banner}</th>
	<th width="70%">{$lang.banner_code}</th>
</tr>
<tr>
	<td valign="top">{$banner.example|unescape}</td>
	<td valign="top">
		<span class="product-details-title">{$banner.title}</span>
		<p>{$lang.banner_code_for_some_products}:
		<textarea cols="70" rows="5" class="input-textarea">{$banner.code|unescape}</textarea></p>
	</td>
</tr>
<tr class="table-footer">
	<td colspan="2">&nbsp;</td>
</tr>
</table>

{include file="addons/affiliate/views/banners_manager/components/linked_products.tpl"}

{capture name="mainbox_title"}{$lang.select_products}{/capture}