{* $Id: search_results.override.tpl 6966 2009-03-04 06:42:39Z angel $ *}

{if $result.object == "news"}
	{include file="addons/news_and_emails/views/news/components/one_news.tpl" n=$result}
{/if}