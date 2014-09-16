{* $Id: categories_m_viewadded.tpl 9940 2010-07-01 14:26:12Z 2tl $ *}

{* NOTE: This template doesn\'t used for direct display
   It will store in the session and then display into notification box
   ---------------------------------------------------------------
   So, it is STRONGLY recommended to use strip tags in such templates
*}

{strip}
<p>{$lang.text_categories_added}</p>
{foreach from=$added_categories item=category}
<p><a href="{"categories.update?category_id=`$category.category_id`"|fn_url}">{$category.category}</a></p>
{/foreach}
{/strip}
