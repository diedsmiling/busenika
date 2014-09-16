{* $Id: pages_child.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{** block-description:child_pages **}

{if $items}
	{hook name="pages:page_children"}
	<ul class="subpages-list">
		{foreach from=$items item=child_page}
			<li><span class="main-info">{$child_page.timestamp|date_format:$settings.Appearance.date_format}&nbsp;<a href="{if $child_page.page_type == $smarty.const.PAGE_TYPE_LINK}{$child_page.link|fn_url}{else}{"pages.view?page_id=`$child_page.page_id`"|fn_url}{/if}"{if $child_page.new_window} target="_blank"{/if}>{$child_page.page}</a></span></li>
		{/foreach}
	</ul>
	{/hook}
{/if}