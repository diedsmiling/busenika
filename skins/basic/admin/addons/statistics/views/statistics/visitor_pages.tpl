{* $Id: visitor_pages.tpl 7165 2009-03-31 12:38:30Z angel $ *}

{capture name="tabsbox"}

<div id="content_visitors_pages">
{capture name="mainbox"}

{include file="addons/statistics/views/statistics/components/visitors.tpl" no_sort="Y" no_paginate=true}

{include file="common_templates/subheader.tpl" title=$lang.route}

{include file="common_templates/pagination.tpl" div_id="stat_requests"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="10%">{$lang.date}</th>
	<th>{$lang.page}</th>
</tr>
{foreach from=$requests item="req"}
<tr {cycle values=",class=\"table-row\"" name="n"}>
	<td class="nowrap">{$req.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>
		<div><a href="{if $req.https == "Y"}{$config.https_location}{else}{$config.http_location}{/if}{$req.url}" target="_blank">{$req.url}</a></div>
		{$req.title}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="2"><p>{$lang.no_data}</p></td>
</tr>	
{/foreach}
</table>

{include file="common_templates/pagination.tpl" div_id="stat_requests"}

{/capture}
{capture name="title"}{if $smarty.request.client_type == "B"}{$lang.robot_path}{else}{$lang.visitor_path}{/if}{/capture}
{include file="common_templates/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox}
<!--content_visitors_pages--></div>


{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}

{*/if*}