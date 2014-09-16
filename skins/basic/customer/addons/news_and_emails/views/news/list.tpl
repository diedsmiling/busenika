{* $Id: list.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $news}

{include file="common_templates/pagination.tpl"}

{foreach from=$news item=n}
<a name="{$n.news_id}"></a>
<h5 class="info-field-title">
	<em class="float-right">{$lang.date_added}: {$n.date|date_format:"`$settings.Appearance.date_format`"}</em>
	{$n.news}
</h5>
<div class="info-field-body wysiwyg-content">
{if $n.separate == "Y"}
	<a href="{"news.view?news_id=`$n.news_id`"|fn_url}">{$lang.more_w_ellipsis}</a>
{else}
	{hook name="news:list"}
		{$n.description|unescape}
	{/hook}
{/if}
</div>
{/foreach}

{include file="common_templates/pagination.tpl"}

{/if}

{capture name="mainbox_title"}{$lang.site_news}{/capture}