{* $Id: pages_text_links.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{** block-description:text_links **}

{if $items}
<ul>
	{foreach from=$items item="page"}
	<li><a href="{if $page.page_type == $smarty.const.PAGE_TYPE_LINK}{$page.link|fn_url}{else}{"pages.view?page_id=`$page.page_id`"|fn_url}{/if}"{if $page.new_window} target="_blank"{/if}{if $block.properties.positions == "left" || $block.properties.positions == "right"} title="{$page.page}">{$page.page|unescape|strip_tags|truncate:40:"...":true}{else}>{$page.page|unescape}{/if}</a></li>
	{/foreach}
</ul>
{/if}