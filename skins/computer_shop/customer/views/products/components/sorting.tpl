{* $Id: sorting.tpl 9887 2010-06-28 12:56:20Z alexions $ *}

<!--dynamic:product_sorting-->
{if $settings.DHTML.customer_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax cm-ajax-force"}
{/if}

{assign var="curl" value=$config.current_url|fn_query_remove:"sort_by":"sort_order":"result_ids":"layout"}
{assign var="sorting" value=""|fn_get_products_sorting:"false"}
{assign var="layouts" value=""|fn_get_products_views:false:false}
{assign var="pagination_id" value=$id|default:"pagination_contents"}

{if $search.sort_order == "asc"}
	{capture name="sorting_text"}
		<a class="sort-asc">{$sorting[$search.sort_by].description}</a>
	{/capture}
{else}
	{capture name="sorting_text"}
		<a class="sort-desc">{$sorting[$search.sort_by].description}</a>
	{/capture}
{/if}

{if $search.sort_order == "asc"}
	{assign var="layout_sort_order" value="desc"}
{else}
	{assign var="layout_sort_order" value="asc"}
{/if}

{if !(($category_data.selected_layouts|count == 1) || ($category_data.selected_layouts|count == 0 && ""|fn_get_products_views:true|count <= 1)) && !$hide_layouts}
<div class="float-left">
<strong>{$lang.view_as}:</strong>&nbsp;
{capture name="tools_list"}
	<ul>
	{foreach from=$layouts key="layout" item="item"}
		{if ($category_data.selected_layouts.$layout) || (!$category_data.selected_layouts && $item.active)}
			<li><a class="{$ajax_class} {if $layout == $selected_layout}active{/if}" rev="{$pagination_id}" href="{"`$curl`&amp;sort_by=`$search.sort_by`&amp;sort_order=`$layout_sort_order`&amp;layout=`$layout`"|fn_url}" rel="nofollow" name="layout_callback">{$item.title}</a></li>
		{/if}
	{/foreach}
	</ul>
{/capture}
{include file="common_templates/tools.tpl" tools_list=$smarty.capture.tools_list suffix="view_as" link_text=$layouts.$selected_layout.title}
</div>
{/if}

<div class="right">
	<a href="" class="no_paginate_link" style="float: left; margin-left: 117px; font-weight: bold;"> Показать все! </a>
<strong>{$lang.sort_by}:</strong>&nbsp;
{capture name="tools_list"}
	<ul>
		{foreach from=$sorting key="option" item="value"}
			{if $search.sort_by == $option}
				{assign var="sort_order" value=$search.sort_order}
			{else}
				{if $value.default_order}
					{assign var="sort_order" value=$value.default_order}
				{else}
					{assign var="sort_order" value="asc"}
				{/if}
			{/if}
			<li><a class="{$ajax_class} {if $search.sort_by == $option}active{/if}" rev="{$pagination_id}" href="{"`$curl`&amp;sort_by=`$option`&amp;sort_order=`$sort_order`"|fn_url}" rel="nofollow" name="sorting_callback">{$value.description}{if $search.sort_by == $option}&nbsp;{if $search.sort_order == "asc"}<img src="{$images_dir}/icons/sort_asc.gif" width="7" height="6" border="0" alt="" />{else}<img src="{$images_dir}/icons/sort_desc.gif" width="7" height="6" border="0" alt="" />{/if}{/if}</a>
			</li>
		{/foreach}
	</ul>
{/capture}
{include file="common_templates/tools.tpl" tools_list=$smarty.capture.tools_list suffix="sort_by" link_text=$smarty.capture.sorting_text no_link=true}
</div>

<hr />
<!--/dynamic-->
