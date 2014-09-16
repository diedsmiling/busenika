{* $Id: results.tpl 6966 2009-03-04 06:42:39Z angel $ *}
{capture name="mainbox"}

<hr width="100%" />

{if $search_results}

{include file="common_templates/pagination.tpl"}
<p>&nbsp;</p>
{foreach from=$search_results item=result}
{if !$result.first}
<hr />
{/if}

{hook name="search:search_results"}
{if $result.object == "products"}
	{include file="views/products/components/one_product.tpl" product=$result key=$result.id}

{elseif $result.object == "pages"}
	{include file="views/pages/components/one_page.tpl" page=$result}
{/if}
{/hook}

{/foreach}

<p>&nbsp;</p>
{include file="common_templates/pagination.tpl"}

{else}
	<p class="no-items">{$lang.text_no_matching_results_found}</p>
{/if}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.search_results content=$smarty.capture.mainbox}