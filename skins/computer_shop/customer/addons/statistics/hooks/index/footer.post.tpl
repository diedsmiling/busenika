{* $Id: footer.post.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){$ldelim}
	jQuery.ajaxRequest('{"statistics.collect"|fn_url:'C':'rel':'&'}', {$ldelim}
		method: 'post',
		data: {$ldelim}
			've[url]': location.href,
			've[title]': document.title,
			've[browser_version]': jQuery.ua.version,
			've[browser]': jQuery.ua.browser,
			've[os]': jQuery.ua.os,
			've[client_language]': jQuery.ua.language,
			've[referrer]': document.referrer,
			've[screen_x]': (screen.width || null),
			've[screen_y]': (screen.height || null),
			've[color]': (screen.colorDepth || screen.pixelDepth || null),
			've[time_begin]': {$smarty.const.MICROTIME}
		{$rdelim},
		hidden: true
	{$rdelim});
{$rdelim});
//]]>
</script>

<noscript>
{capture name="statistics_link"}statistics.collect?ve[url]={$smarty.const.REAL_URL|escape:url}&amp;ve[title]={if $page_title}{$page_title|escape:url}{else}{$location_data.page_title|escape:url}{foreach from=$breadcrumbs item=i name="bkt"}{if $smarty.foreach.bkt.index == 1} - {/if}{if !$smarty.foreach.bkt.first}{$i.title|escape:url}{if !$smarty.foreach.bkt.last} :: {/if}{/if}{/foreach}{/if}&amp;ve[referrer]={$smarty.server.HTTP_REFERER|escape:url}&amp;ve[time_begin]={$smarty.const.MICROTIME}{/capture}
<object data="{$smarty.capture.statistics_link|fn_url}" width="0" height="0"></object>
</noscript>