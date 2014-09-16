{* $Id: banners_list.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<form action="{""|fn_url}" method="post" name="manage_{$prefix}_banners_form">
<input type="hidden" name="selected_section" id="id_selected_section" value="" />
<input type="hidden" name="page" value="{$smarty.request.page}" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="35%">{$lang.banner}</th>
	<th width="30%">{$lang.banner_code}</th>
	{if $smarty.request.banner_type != "P"}
	<th width="35%">
	{$lang.$prefix}</th>{/if}
</tr>
{if $banners.$prefix}
{foreach from=$banners.$prefix item=c_banner}
<tr {cycle values=" ,class=\"table-row\""} valign="top">
	<td class="wysiwyg-content">
		{assign var="banner_id" value=$c_banner.banner_id}
		{$js_banners.$banner_id.example|unescape}
		{if $js_banners.$banner_id.url}<p><a href="{$js_banners.$banner_id.url}" onclick="return false;">{$js_banners.$banner_id.url}</a></p>{/if}
	</td>
	<td>
		<span class="product-details-title">{$c_banner.title}</span>
		{if $c_banner.icon.image_x || $c_banner.icon.image_y}<p>{$c_banner.icon.image_x}x{$c_banner.icon.image_y}</p>{/if}
		<textarea cols="70" rows="5" class="input-textarea">{$js_banners.$banner_id.code|unescape}</textarea>
		{if $smarty.request.banner_type == "P"}<a href="{"banners_manager.select_product?banner_id=`$banner_id`"|fn_url}">{$lang.banner_code_for_some_products}</a>{/if}
	</td>
	{if $smarty.request.banner_type != "P"}
	<td>
		{include file="addons/affiliate/views/banners_manager/components/`$prefix`_list.tpl" list_data=$c_banner.$prefix}
	</td>
	{/if}
</tr>
{/foreach}
{else}
<tr>
	<td colspan="3"><p class="no-items">{$lang.text_no_banners_found}</p></td>
</tr>
{/if}
<tr class="table-footer">
	<td colspan="3">&nbsp;</td>
</tr>
</table>

</form>

