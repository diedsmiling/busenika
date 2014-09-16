{* $Id: products_search_form.tpl 6971 2009-03-05 09:28:18Z zeke $ *}

{split data=$filter_features size="3" assign="splitted_filter" preverse_keys=true}
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table-filters">
{foreach from=$splitted_filter item="filters_row" name="filters_row"}
<tr>
{foreach from=$filters_row item="filter"}
	<th>{$filter.filter|default:$filter.description}</th>
{/foreach}
</tr>
<tr valign="top"{if ($splitted_filter|sizeof > 1) && $smarty.foreach.filters_row.first} class="delim"{/if}>
{foreach from=$filters_row item="filter"}
	<td width="33%">
		{if $filter.feature_type == "S" || $filter.feature_type == "E" || $filter.feature_type == "M" || $filter.feature_type == "N" && !$filter.filter_id}
		<div class="scroll-y">
			{assign var="filter_ranges" value=$filter.ranges|default:$filter.variants}
			{foreach from=$filter_ranges item="range"}
				{assign var="range_id" value=$range.range_id|default:$range.variant_id}
				<div class="select-field"><input type="checkbox" class="checkbox" name="{if $filter.feature_type == "M"}multiple_{/if}variants[]" id="{$prefix}variants_{$range_id}" value="{if $filter.feature_type == "M"}{$range_id}{else}[V{$range_id}]{/if}" {if "[V`$range_id`]"|in_array:$search.variants || $range_id|in_array:$search.multiple_variants}checked="checked"{/if} /><label for="variants_{$range_id}">{$filter.prefix}{$range.variant}{$filter.suffix}</label></div>
			{/foreach}
		</div>
		{elseif $filter.feature_type == "O" || $filter.feature_type == "N" && $filter.filter_id || $filter.feature_type == "D" || $filter.condition_type == "D" || $filter.condition_type == "F"}
			<div class="scroll-y">
				{if $filter.condition_type}
					{assign var="el_id" value="field_`$filter.filter_id`"}
				{else}
					{assign var="el_id" value="feature_`$filter.feature_id`"}
				{/if}

				<div class="select-field"><input type="radio" name="variants[{$el_id}]" id="{$prefix}no_ranges_{$el_id}" value="" checked="checked" class="radio" /><label for="{$prefix}no_ranges_{$el_id}">{$lang.none}</label></div>
				{assign var="filter_ranges" value=$filter.ranges|default:$filter.variants}
				{assign var="_type" value=$filter.field_type|default:"R"}
				{foreach from=$filter_ranges item="range"}
					{assign var="range_id" value=$range.range_id|default:$range.variant_id}
					{assign var="range_name" value=$range.range_name|default:$range.variant}
					<div class="select-field"><input type="radio" class="radio" name="variants[{$el_id}]" id="{$prefix}ranges_{$el_id}{$range_id}" value="{$_type}{$range_id}" {if $search.variants.$el_id == "`$_type``$range_id`"}checked="checked"{/if} /><label for="{$prefix}ranges_{$el_id}{$range_id}">{$range_name|fn_text_placeholders}</label></div>
				{/foreach}
			</div>
			
			{if $filter.condition_type != "F"}
			<p><input type="radio" name="variants[{$el_id}]" id="{$prefix}select_custom_{$el_id}" value="O" {if $search.variants[$el_id] == "O"}checked="checked"{/if} class="radio" /><label for="{$prefix}select_custom_{$el_id}">{$lang.your_range}</label></p>
			
			<div class="select-field">
				{if $filter.feature_type == "D"}
				{if $search.custom_range[$filter.feature_id].from || $search.custom_range[$filter.feature_id].to}
					{assign var="date_extra" value=""}
				{else}
					{assign var="date_extra" value="disabled="\disabled\""}
				{/if}
				{include file="common_templates/calendar.tpl" date_id="`$prefix`range_`$el_id`_from" date_name="custom_range[`$filter.feature_id`][from]" date_val=$search.custom_range[$filter.feature_id].from extra=$date_extra start_year=$settings.Company.company_start_year}
				{include file="common_templates/calendar.tpl" date_id="`$prefix`range_`$el_id`_to" date_name="custom_range[`$filter.feature_id`][to]" date_val=$search.custom_range[$filter.feature_id].to extra=$date_extra start_year=$settings.Company.company_start_year}
				<input type="hidden" name="custom_range[{$filter.feature_id}][type]" value="D" />
				{else}
				<input type="text" name="{if $filter.field_type}field_range[{$filter.field_type}]{else}custom_range[{$filter.feature_id}]{/if}[from]" id="{$prefix}range_{$el_id}_from" size="3" class="input-text-short" value="{$search.custom_range[$filter.feature_id].from|default:$search.field_range[$filter.field_type].from}" {if $search.variants[$el_id] != "O"}disabled="disabled"{/if} />
				&nbsp;-&nbsp;
				<input type="text" name="{if $filter.field_type}field_range[{$filter.field_type}]{else}custom_range[{$filter.feature_id}]{/if}[to]" size="3" class="input-text-short" value="{$search.custom_range[$filter.feature_id].to|default:$search.field_range[$filter.field_type].to}" id="{$prefix}range_{$el_id}_to" {if $search.variants[$el_id] != "O"}disabled="disabled"{/if} />
				{/if}
			</div>
			{/if}
			<script type="text/javascript">
			//<![CDATA[
			$(":radio[name='variants[{$el_id}]']").change(function() {ldelim}
				var el_id = '{$el_id}';
				$('#{$prefix}range_' + el_id + '_from').attr('disabled', this.value !== 'O');
				$('#{$prefix}range_' + el_id + '_to').attr('disabled', this.value !== 'O');
				{if $filter.feature_type == "D"}
				$('#{$prefix}range_' + el_id + '_from_but').attr('disabled', this.value !== 'O');
				$('#{$prefix}range_' + el_id + '_to_but').attr('disabled', this.value !== 'O');
				{/if}
			{rdelim});
			//]]>
			</script>
		{elseif $filter.feature_type == "C" || $filter.condition_type == "C"}
			{if $filter.condition_type}
				{assign var="el_id" value=$filter.field_type}
			{else}
				{assign var="el_id" value=$filter.feature_id}
			{/if}
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="{$prefix}ranges_{$el_id}_none" value="" {if !$search.ch_filters[$el_id]}checked="checked"{/if} />
				<label for="{$prefix}ranges_{$el_id}_none">{$lang.none}</label>
			</div>
			
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="{$prefix}ranges_{$el_id}_yes" value="Y" {if $search.ch_filters[$el_id] == "Y"}checked="checked"{/if} />
				<label for="{$prefix}ranges_{$el_id}_yes">{$lang.yes}</label>
			</div>
			
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="{$prefix}ranges_{$el_id}_no" value="N" {if $search.ch_filters[$el_id] == "N"}checked="checked"{/if} />
				<label for="{$prefix}ranges_{$el_id}_no">{$lang.no}</label>
			</div>
			
			{if !$filter.condition_type}
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="{$prefix}ranges_{$el_id}_any" value="A" {if $search.ch_filters[$el_id] == "A"}checked="checked"{/if} />
				<label for="{$prefix}ranges_{$el_id}_any">{$lang.any}</label>
			</div>
			{/if}
			
		{elseif $filter.feature_type == "T"}
			<div class="select-field nowrap">
				{$filter.prefix}<input type="text" name="tx_features[{$filter.feature_id}]" class="input-text" value="{$search.tx_features[$filter.feature_id]}" />{$filter.suffix}
			</div>
		{/if}
	</td>
{/foreach}
</tr>
{/foreach}
</table>