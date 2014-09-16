{* $Id: menu_items.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{strip}
{assign var="foreach_name" value="cats_$cid"}
{foreach from=$items item="category" name=$foreach_name}
<li {if $category.subcategories}class="dir"{/if}>
	{if $category.subcategories}
		<ul>
			{include file="views/categories/components/menu_items.tpl" items=$category.subcategories separated=true submenu=true cid=$category.category_id}
		</ul>
	{/if}
	<a href="{"categories.view?category_id=`$category.category_id`"|fn_url}">{$category.category}</a>
</li>
{if $separated && !$smarty.foreach.$foreach_name.last}
<li class="h-sep">&nbsp;</li>
{/if}
{/foreach}
{/strip}
