{* $Id: top_quick_links.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{hook name="index:top_links"}
<p class="quick-links">&nbsp;
	{foreach from=$quick_links item="link"}
		<a href="{$link.param|fn_url}">{$link.descr}</a>
	{/foreach}
</p>
{/hook}