{* $Id: news.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{** block-description:site_news **}

{if $items}
<ul class="site-news">
{foreach from=$items item="news" name="site_news"}
	<li>
		<strong>{$news.date|date_format:$settings.Appearance.date_format}</strong>
		<a href="{"news.view?news_id=`$news.news_id`#`$news.news_id`"|fn_url}">{$news.news}</a>
	</li>
	{if !$smarty.foreach.site_news.last}
	<li class="delim"></li>
	{/if}
{/foreach}
</ul>

<p class="right">
	<a href="{"news.list"|fn_url}" class="extra-link">{$lang.view_all}</a>
</p>
{/if}
