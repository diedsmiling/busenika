{* $Id: results.tpl 8905 2010-02-22 12:56:34Z 2tl $ *}

{assign var="title_extra" value="`$lang.total_results`:&nbsp;<strong>`$search_results_total`</strong>"}

{if $search_results}
	{include file="common_templates/pagination.tpl"}
	
	{foreach from=$search_results item="result"}
	
	{if !$result.first}<hr />{/if}
	
	{hook name="search:search_results"}
	{if $result.object == "products"}
		{include file="views/products/components/one_product.tpl" product=$result key=$result.id}
	
	{elseif $result.object == "pages"}
		{include file="views/pages/components/one_page.tpl" page=$result}
	{/if}
	{/hook}
	
	{/foreach}
	
	{include file="common_templates/pagination.tpl"}

{else}
	<p class="no-items">{$lang.text_no_matching_results_found}</p>
{/if}

{capture name="mainbox_title"}<span class="float-right">{$title_extra}</span>{$lang.search_results}{/capture}

