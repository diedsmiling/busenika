{* $Id: product_features_short_list.tpl 8327 2009-11-27 09:11:44Z angel $ *}

{if $features}
{strip}
{if !$no_container}<p class="features-list description">{/if}
	{foreach from=$features name=features_list item=feature}
	{if $feature.prefix}{$feature.prefix}{/if}
	{if $feature.feature_type == "D"}{$feature.value_int|date_format:"`$settings.Appearance.date_format`"}
	{elseif $feature.feature_type == "M"}
		{foreach from=$feature.variants item="v" name="ffev"}
		{$v.variant|default:$v.value}{if !$smarty.foreach.ffev.last}, {/if}
		{/foreach}
	{elseif $feature.feature_type == "S" || $feature.feature_type == "N" || $feature.feature_type == "E"}
		{$feature.variant|default:$feature.value}
	{elseif $feature.feature_type == "C"}
		{$feature.description}
	{elseif $feature.feature_type == "O"}
		{$feature.value_int}
	{else}
		{$feature.value}
	{/if}
	{if $feature.suffix}{$feature.suffix}{/if}
		{if !$smarty.foreach.features_list.last} / {/if}
	{/foreach}
{if !$no_container}</p>{/if}
{/strip}
{/if}