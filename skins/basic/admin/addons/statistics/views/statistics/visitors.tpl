{* $Id: visitors.tpl 8601 2010-01-14 09:57:19Z lexa $ *}

{capture name="tabsbox"}

<div id="content_visitors_log">
{capture name="mainbox"}

{capture name="extra"}
<input type="hidden" name="client_type" value="{$search.client_type}" />
<input type="hidden" name="section" value="{$smarty.request.section}" />
{/capture}
{include file="addons/statistics/views/statistics/components/search_form.tpl" key="visitors" extra=$smarty.capture.extra report_data=$statistics_data dispatch="statistics.visitors"}

{if $text_conditions}
	{include file="common_templates/subheader.tpl" title=$lang.conditions}
	{foreach from=$text_conditions key="lang_var" item="cond"}
		<div class="form-field">
			<label>{$lang.$lang_var}</label>
			{if $clear_conditions.$lang_var}
				{assign var="clear_url" value=$config.current_url|fn_query_remove:$clear_conditions.$lang_var}
			{else}
				{assign var="clear_url" value=$config.current_url|fn_query_remove:"report":"object_code"}
				{foreach from=$clear_conditions item="cond_sign"}
					{assign var="clear_url" value=$clear_url|fn_query_remove:$cond_sign}
				{/foreach}
			{/if}
			{$cond|unescape} <a href="{$clear_url}"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" class="hand" align="top" /></a>
		</div>
	{/foreach}
{/if}

{include file="addons/statistics/views/statistics/components/visitors.tpl" visitors_log=$statistics_data.visitors_log}

{/capture}
{capture name="title"}{if $search.client_type == "B"}{$lang.robots_log}{else}{$lang.visitors_log}{/if}{/capture}
{include file="common_templates/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
<!--content_visitors_log--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}