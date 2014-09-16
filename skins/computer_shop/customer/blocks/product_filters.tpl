{* $Id: product_filters.tpl 10055 2010-07-14 10:15:19Z klerik $ *}
{** block-description:original **}

<!--dynamic:filters-->
{if $items && !$smarty.request.advanced_filter}

{if $smarty.server.QUERY_STRING|strpos:"dispatch=" !== false}
	{assign var="curl" value=$config.current_url}
	{assign var="filter_qstring" value=$curl|fn_query_remove:"result_ids":"filter_id":"view_all":"req_range_id":"advanced_filter":"features_hash":"subcats":"page"}
{else}
	{assign var="filter_qstring" value="products.search"}
{/if}

{assign var="reset_qstring" value="products.search"}

{if $smarty.request.category_id}
	{assign var="filter_qstring" value=$filter_qstring|fn_link_attach:"subcats=Y"}
	{assign var="reset_qstring" value=$reset_qstring|fn_link_attach:"subcats=Y"}
{/if}

{assign var="has_selected" value=false}
{foreach from=$items item="filter" name="filters"}

<h4>{$filter.filter}</h4>
<ul class="product-filters" id="content_product_more_filters_{$block.block_id}_{$filter.filter_id}">
{foreach from=$filter.ranges name="ranges" item="range"}
	<li {if $smarty.foreach.ranges.iteration > $smarty.const.FILTERS_RANGES_COUNT}class="hidden"{/if}>
		{strip}
		{if $range.selected == true}
			{assign var="fh" value=$smarty.request.features_hash|fn_delete_range_from_url:$range:$filter.field_type}
			{if $fh}
				{assign var="attach_query" value="features_hash=`$fh`"}
			{/if}
			{if $filter.feature_type == "E" && $range.range_id == $smarty.request.variant_id}
				{assign var="reset_lnk" value=$reset_qstring}
			{else}
				{assign var="reset_lnk" value=$filter_qstring}
			{/if}
			{assign var="has_selected" value=true}
			<a class="extra-link filter-delete" href="{if $fh}{$reset_lnk|fn_link_attach:$attach_query|fn_url}{else}{$reset_lnk|fn_url}{/if}" title="{$lang.remove}"><img src="{$images_dir}/icons/delete_icon.gif" width="12" height="11" border="0" alt="{$lang.remove}" align="bottom" /></a>{$filter.prefix}{$range.range_name|fn_text_placeholders}{$filter.suffix}

			{if $filter.other_variants}
			<ul id="other_variants_{$block.block_id}_{$filter.filter_id}" class="hidden">
			{foreach from=$filter.other_variants item="r"}
			<li>
				{assign var="filter_query_elm" value=$fh|fn_add_range_to_url_hash:$r:$filter.field_type}
				{if $fh}
					{assign var="cur_features_hash" value="&amp;features_hash=`$fh`"}
				{/if}
				<a href="{if $r.feature_type == "E" && !$r.simple_link && $controller == "product_features"}{"product_features.view?variant_id=`$r.range_id``$cur_features_hash`"|fn_url}{else}{$filter_qstring|fn_link_attach:"features_hash=`$filter_query_elm`"|fn_url}{/if}">{$filter.prefix}{$r.range_name|fn_text_placeholders}{$filter.suffix}</a>&nbsp;<span class="details">&nbsp;({$r.products})</span>
			</li>
			{/foreach}
			</ul>
			<p><a id="sw_other_variants_{$block.block_id}_{$filter.filter_id}" class="extra-link cm-combination">{$lang.choose_other}</a></p>
			{/if}
		{else}
			{assign var="filter_query_elm" value=$smarty.request.features_hash|fn_add_range_to_url_hash:$range:$filter.field_type}
			{if $smarty.request.features_hash}
				{assign var="cur_features_hash" value="&amp;features_hash=`$smarty.request.features_hash`"}
			{/if}
			<a href="{if $filter.feature_type == "E" && !$filter.simple_link}{"product_features.view?variant_id=`$range.range_id``$cur_features_hash`"|fn_url}{else}{$filter_qstring|fn_link_attach:"features_hash=`$filter_query_elm`"|fn_url}{/if}">{$filter.prefix}{$range.range_name|fn_text_placeholders}{$filter.suffix}</a>&nbsp;<span class="details">&nbsp;({$range.products})</span>
		{/if}
		{/strip}
	</li>
{/foreach}

{if $smarty.foreach.ranges.iteration > $smarty.const.FILTERS_RANGES_COUNT}
	<li class="right">
		<a href="{"`$filter_qstring`?filter_id=`$filter.filter_id`&amp;more_filters=Y"|fn_url}" onclick="$('#content_product_more_filters_{$block.block_id}_{$filter.filter_id} li').show(); $('#view_all_{$block.block_id}_{$filter.filter_id}').show(); $(this).hide(); return false;" class="extra-link">{$lang.more}</a>
	</li>
{/if}

{if $filter.more_cut}
	{capture name="q"}{$filter_qstring|unescape}&filter_id={$filter.filter_id}&{if $smarty.request.features_hash}&features_hash={$smarty.request.features_hash|fn_delete_range_from_url:$range:$filter.field_type}{/if}{/capture}
	<li id="view_all_{$block.block_id}_{$filter.filter_id}" class="right hidden">
		{assign var="capture_q" value=$smarty.capture.q|escape:url}
		<a href="{"product_features.view_all?q=`$capture_q`"|fn_url}" class="extra-link">{$lang.view_all}</a>
	</li>
{/if}

<li class="delim">&nbsp;</li>

</ul>

{/foreach}

<div class="clear filters-tools">
	<div class="float-right"><a {if "FILTER_CUSTOM_ADVANCED"|defined}href="{"products.search?advanced_filter=Y"|fn_url}"{else}href="{$filter_qstring|fn_link_attach:"advanced_filter=Y"|fn_url}"{/if} class="secondary-link lowercase">{$lang.advanced}</a></div>
	{if $has_selected}
	<a href="{if $smarty.request.category_id}{"categories.view?category_id=`$smarty.request.category_id`"|fn_url}{else}{$index_script|fn_url}{/if}" class="reset-filters">{$lang.reset}</a>
	{/if}
</div>
{/if}
<!--/dynamic-->