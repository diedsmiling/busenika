{* $Id: one_news.tpl 10336 2010-08-04 07:17:04Z klerik $ *}

<div>
	<strong>{$n.result_number}.</strong> <a href="{"news.view?news_id=`$n.news_id`#`$n.news_id`"|fn_url}" class="product-title">{$n.news}</a>
	<p>{$lang.date_added}: {$n.date|date_format:"`$settings.Appearance.date_format`"}</p>
	<p>
		{$n.description|unescape|strip_tags|truncate:280:"..."}{if $n.description|strlen > 280}<a href="{"news.view?news_id=`$n.news_id`#`$n.news_id`"|fn_url}">{$lang.more_link}</a>{/if}
	</p>
</div>