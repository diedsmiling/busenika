{* $Id: pagination.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $pagination}
{assign var="id" value=$div_id|default:"pagination_contents"}
{assign var="qstring" value=$smarty.server.QUERY_STRING|fn_query_remove:"page":"result_ids"}

{if $smarty.capture.pagination_open != "Y"}
	{if $settings.DHTML.admin_ajax_based_pagination == "Y" && $pagination.total_pages > 1}
		{script src="js/jquery.history.js"}
	{/if}
<div id="{$id}">

{if $save_current_page}
	<input type="hidden" name="page" value="{$search.page|default:$smarty.request.page|default:1}" />
{/if}

{if $save_current_url}
	<input type="hidden" name="redirect_url" value="{$config.current_url}" />
{/if}

{/if}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}
{if !$disable_history}
	{assign var="history_class" value=" cm-history"}
{else}
	{assign var="history_class" value=" cm-ajax-cache"}
{/if}

<div class="pagination clear cm-pagination-wraper{if $smarty.capture.pagination_open != "Y"} top-pagination{/if}">
	{if $pagination.total_pages > 1}
	<div class="float-left">
		<label>{$lang.go_to_page|escape:html}:</label>
		<input type="text" class="input-text-short valign cm-pagination{$history_class}" value="{if $smarty.request.page > $pagination.total_pages}1{else}{$smarty.request.page|default:1}{/if}" />
		<img src="{$images_dir}/icons/pg_right_arrow.gif" class="pagination-go-button hand cm-pagination-button" alt="{$lang.go|escape:html}" title="{$lang.go|escape:html}" />
	</div>
	{/if}

	<div class="float-right">
	{if $pagination.current_page != "full_list" && $pagination.total_pages > 1}
		<span class="lowercase"><a name="pagination" class="{if $pagination.prev_page}{$ajax_class}{/if}{$history_class}" {if $pagination.prev_page}href="{"`$index_script`?`$qstring`&amp;page=`$pagination.prev_page`"|fn_url}" rel="{$pagination.prev_page}" rev="{$id}"{/if}>&laquo;&nbsp;{$lang.previous}</a></span>

		{foreach from=$pagination.navi_pages item="pg" name="f_pg"}
			{if $smarty.foreach.f_pg.first && $pg > 1 }
			<a name="pagination" class="{$ajax_class}{$history_class}" href="{"`$index_script`?`$qstring`&amp;page=1`"|fn_url}" rel="1" rev="{$id}">1</a>
			{if $pg != 2}<a name="pagination" class="{if $pagination.prev_range}{$ajax_class}{/if} prev-range{$history_class}" {if $pagination.prev_range}href="{"`$index_script`?`$qstring`&amp;page=`$pagination.prev_range`"|fn_url}" rel="{$pagination.prev_range}" rev="{$id}"{/if}>&nbsp;...&nbsp;</a>{/if}
			{/if}
			{if $pg != $pagination.current_page}<a name="pagination" class="{$ajax_class}{$history_class}" href="{"`$index_script`?`$qstring`&amp;page=`$pg`"|fn_url}" rel="{$pg}" rev="{$id}">{$pg}</a>{else}<strong>{$pg}</strong>{/if}
			{if $smarty.foreach.f_pg.last && $pg < $pagination.total_pages}
			{if $pg != $pagination.total_pages-1}<a name="pagination" class="{if $pagination.next_range}{$ajax_class}{/if} next-range{$history_class}" {if $pagination.next_range}href="{"`$index_script`?`$qstring`&amp;page=`$pagination.next_range`"|fn_url}" rel="{$pagination.next_range}" rev="{$id}"{/if}>&nbsp;...&nbsp;</a>{/if}<a name="pagination" class="{$ajax_class}{$history_class}" href="{"`$index_script`?`$qstring`&amp;page=`$pagination.total_pages`"|fn_url}" rel="{$pagination.total_pages}" rev="{$id}">{$pagination.total_pages}</a>
			{/if}
		{/foreach}

		<span class="lowercase"><a name="pagination" class="{if $pagination.next_page}{$ajax_class}{/if}{$history_class}" {if $pagination.next_page}href="{"`$index_script`?`$qstring`&amp;page=`$pagination.next_page`"|fn_url}" rel="{$pagination.next_page}" rev="{$id}"{/if}>{$lang.next}&nbsp;&raquo;</a></span>
	{/if}
	{if $pagination}
		{if $pagination.total_items}
			&nbsp;{$lang.total_items}:&nbsp;<strong>{$pagination.total_items}&nbsp;/</strong>
			
			{capture name="pagination_list"}
				<ul>
					<li class="strong">{$lang.items_per_page}:</li>
					{assign var="range_url" value=$qstring|fn_query_remove:"items_per_page"}
					{foreach from=$pagination.per_page_range item="step"}
						<li><a name="pagination" class="{$ajax_class}" href="{"`$index_script`?`$range_url`&amp;items_per_page=`$step`"|fn_url}" rev="{$id}">{$step}</a></li>
					{/foreach}
				</ul>
			{/capture}
			{math equation="rand()" assign="rnd"}
			{include file="common_templates/tools.tpl" prefix="pagination_`$rnd`" hide_actions=true tools_list=$smarty.capture.pagination_list display="inline" link_text=$pagination.items_per_page override_meta="pagination-selector"}
		{/if}
	{/if}
	</div>
</div>

{if $smarty.capture.pagination_open == "Y"}
	<!--{$id}--></div>
	{capture name="pagination_open"}N{/capture}
{elseif $smarty.capture.pagination_open != "Y"}
	{capture name="pagination_open"}Y{/capture}
{/if}

{/if}