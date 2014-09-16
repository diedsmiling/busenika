{* $Id: one_news.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
<div class="search-result">
	<strong>{$n.result_number}.</strong> <a href="{"news.update?news_id=`$n.news_id`#`$n.news_id`"|fn_url}"" class="list-product-title">{$n.news|unescape}</a>
	
	<p>
	{$lang.date_added}: {$n.date|date_format:"`$settings.Appearance.date_format`"}<br />
	{assign var="news_link" value="news.update?news_id=`$n.news_id`#`$n.news_id"|fn_url}
	{$n.description|unescape|strip_tags|truncate:280:"<a href=\"`$news_link`\" class=\"underlined\">`$lang.more_link`</a>"}</p>
</div>