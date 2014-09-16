{* $Id: search_results.override.tpl 6967 2009-03-04 09:26:06Z angel $ *}

{if $result.object == "news"}
	{assign var=n value=$result}
	{include file="addons/news_and_emails/views/news/components/one_news.tpl" n=$result}
{/if}