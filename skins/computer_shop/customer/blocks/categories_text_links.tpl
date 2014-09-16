{* $Id: categories_text_links.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{** block-description:text_links **}

{if $items}
<ul>
	{foreach from=$items item="category"}
	<li><a href="{"categories.view?category_id=`$category.category_id`"|fn_url}">{$category.category}</a></li>
	{/foreach}
</ul>
{/if}