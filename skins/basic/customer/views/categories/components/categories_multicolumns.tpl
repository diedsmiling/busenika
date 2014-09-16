{* $Id: categories_multicolumns.tpl 10536 2010-08-30 07:03:01Z 2tl $ *}

{split data=$categories size=$columns|default:"3" assign="splitted_categories"}
{math equation="floor(100/x)" x=$columns|default:"3" assign="cell_width"}

<table cellpadding="0" cellspacing="3" border="0" width="100%">
{foreach from=$splitted_categories item="scats"}
<tr valign="bottom">
{foreach from=$scats item="category"}
	{if $category}
	<td class="center product-item-image" width="{$cell_width}%">
		<a href="{"categories.view?category_id=`$category.category_id`"|fn_url}">{include file="common_templates/image.tpl" show_detailed_link=false object_type="category" images=$category.main_pair no_ids=true show_thumbnail="Y" image_width=$settings.Thumbnails.category_lists_thumbnail_width image_height=$settings.Thumbnails.category_lists_thumbnail_height}</a>
	</td>
	{else}
	<td width="{$cell_width}%">&nbsp;</td>
	{/if}
{/foreach}
</tr>
<tr>
{foreach from=$scats item="category"}
	{if $category}
	<td class="center" valign="top" width="{$cell_width}%">
		<p class="margin-bottom"><a href="{"categories.view?category_id=`$category.category_id`"|fn_url}" class="strong">{$category.category}</a></p>
	</td>
	{else}
	<td width="{$cell_width}%">&nbsp;</td>
	{/if}
{/foreach}
</tr>
{/foreach}
</table>

{capture name="mainbox_title"}{$title}{/capture}