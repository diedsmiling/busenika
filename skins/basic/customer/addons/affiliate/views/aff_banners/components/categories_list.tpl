{* $Id: categories_list.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<table cellpadding="0" cellspacing="0" border="0" width="100%">
{foreach from=$banner_categories key="category_id" item="category" name="b_categories"}
<tr>
	<td valign="top" class="center product-image">{if $category.main_pair.image_id != "0"}{include file="common_templates/image.tpl" category_data=$category object_type="category" images=$category.main_pair}{else}&nbsp;{/if}</td>
	<td valign="top" width="100%">
		<span class="subcategories"><a href="{"categories.view?category_id=`$category.category_id`"|fn_url}">{$category.category}</a></span>
		<p><span class="category-description">{$category.description}</span></p>
	</td>
</tr>
{if !$smarty.foreach.b_categories.last}
<tr>
	<td colspan="3"><hr /></td>
</tr>
{/if}
{/foreach}
</table>

{capture name="mainbox_title"}{$lang.categories}{/capture}