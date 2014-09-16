{* $Id: one_page.tpl 9700 2010-06-02 11:57:29Z lexa $ *}

<div class="search-result">
	<strong>{$page.result_number}.</strong> <a href="{if $page.page_type == $smarty.const.PAGE_TYPE_LINK && $page.link != ""}{$page.link|fn_url}{else}{"pages.view&page_id=`$page.page_id`"|fn_url}{/if}" class="product-title">{$page.page}</a>
	<p>{$page.description|unescape|strip_tags|truncate:380:"..."}</p>
</div>