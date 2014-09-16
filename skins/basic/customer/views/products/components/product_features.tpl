{* $Id: product_features.tpl 8726 2010-01-28 14:07:34Z angel $ *}

{foreach from=$product_features item="feature"}
	{if $feature.feature_type != "G"}
		<div class="form-field">
		{if $feature.full_description|trim}{include file="common_templates/help.tpl" text=$feature.description content=$feature.full_description|unescape id=$feature.feature_id}{/if}
		<label>{$feature.description|unescape}:</label>

		{if $feature.feature_type == "M"}
			{assign var="hide_prefix" value=true}
		{else}
			{assign var="hide_prefix" value=false}
		{/if}

		{strip}
		{if $feature.prefix && !$hide_prefix}{$feature.prefix}{/if}
		{if $feature.feature_type == "C"}
			<img src="{$images_dir}/icons/checkbox_{if $feature.value != "Y"}un{/if}ticked.gif" width="13" height="13" alt="{$feature.value}" align="top" />
		{elseif $feature.feature_type == "D"}
			{$feature.value_int|date_format:"`$settings.Appearance.date_format`"}
		{elseif $feature.feature_type == "M" && $feature.variants}
			<ul class="no-markers no-margin">
			{foreach from=$feature.variants item="var"}
			{if $var.selected}<li><img src="{$images_dir}/icons/checkbox_ticked.gif" width="13" height="13" alt="{$var.variant}" />&nbsp;{if $hide_prefix}{$feature.prefix}{/if}{$var.variant}{if $hide_prefix}{$feature.suffix}{/if}</li>{/if}
			{/foreach}
			</ul>
		{elseif $feature.feature_type == "S" || $feature.feature_type == "E"}
			{foreach from=$feature.variants item="var"}
				{if $var.selected}{$var.variant}{/if}
			{/foreach}
		{elseif $feature.feature_type == "N" || $feature.feature_type == "O"}
			{$feature.value_int|default:"-"}
		{else}
			{$feature.value|default:"-"}
		{/if}
		{if $feature.suffix && !$hide_prefix}{$feature.suffix}{/if}
		{/strip}
		</div>
	{/if}
{/foreach}

{foreach from=$product_features item="feature"}
	{if $feature.feature_type == "G" && $feature.subfeatures}
		{include file="common_templates/subheader.tpl" title=$feature.description notes=$feature.full_description|unescape notes_id=$feature.feature_id text=$feature.description}
		{include file="views/products/components/product_features.tpl" product_features=$feature.subfeatures}
	{/if}
{/foreach}
