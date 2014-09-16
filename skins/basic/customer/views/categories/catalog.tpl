{* $Id: catalog.tpl 7430 2009-05-06 06:42:29Z zeke $ *}

{hook name="categories:catalog"}
{include file="views/categories/components/categories_multicolumns.tpl" categories=$root_categories}
{/hook}