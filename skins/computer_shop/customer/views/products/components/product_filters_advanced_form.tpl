{* $Id: product_filters_advanced_form.tpl 9910 2010-06-30 08:22:42Z angel $ *}

{if $filter_features}

{split data=$filter_features size="3" assign="splitted_filter" preverse_keys=true}

{capture name="filtering"}
<input type="hidden" name="advanced_filter" value="Y" />
{if $smarty.request.category_id}
<input type="hidden" name="category_id" value="{$smarty.request.category_id}" />
<input type="hidden" name="subcats" value="Y" />
{/if}

{if $smarty.request.variant_id}
<input type="hidden" name="variant_id" value="{$smarty.request.variant_id}" />
{/if}

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
		{if $filter.feature_type == "S" || $filter.feature_type == "E" || $filter.feature_type == "M"}
		<div class="scroll-y">
			{foreach from=$filter.ranges item="range"}
				<div class="select-field"><input type="checkbox" class="checkbox" name="{if $filter.feature_type == "M"}multiple_{/if}variants[]" id="variants_{$range.range_id}" value="{if $filter.feature_type == "M"}{$range.range_id}{else}[V{$range.range_id}]{/if}" {if "[V`$range.range_id`]"|in_array:$search.variants || $range.range_id|in_array:$search.multiple_variants}checked="checked"{/if} /><label for="variants_{$range.range_id}">{$filter.prefix}{$range.range_name}{$filter.suffix}</label></div>
			{/foreach}
		</div>
		{elseif $filter.feature_type == "O" || $filter.feature_type == "N" || $filter.feature_type == "D" || $filter.condition_type == "D" || $filter.condition_type == "F"}
			<div class="scroll-y">
				{if $filter.condition_type}
					{assign var="el_id" value="field_`$filter.filter_id`"}
				{else}
					{assign var="el_id" value="feature_`$filter.feature_id`"}
				{/if}
				<div class="select-field"><input type="radio" name="variants[{$el_id}]" id="no_ranges_{$el_id}" value="" checked="checked" class="radio" /><label for="no_ranges_{$el_id}">{$lang.none}</label></div>
				{foreach from=$filter.ranges item="range"}
					{assign var="_type" value=$filter.field_type|default:"R"}
					<div class="select-field"><input type="radio" class="radio" name="variants[{$el_id}]" id="ranges_{$el_id}{$range.range_id}" value="{$_type}{$range.range_id}" {if $search.variants.$el_id == "`$_type``$range.range_id`"}checked="checked"{/if} /><label for="ranges_{$el_id}{$range.range_id}">{$range.range_name|fn_text_placeholders}</label></div>
				{/foreach}
			</div>
			
			{if $filter.condition_type != "F"}
			<p><input type="radio" name="variants[{$el_id}]" id="select_custom_{$el_id}" value="O" {if $search.variants[$el_id] == "O"}checked="checked"{/if} class="radio" /><label for="select_custom_{$el_id}">{$lang.your_range}</label></p>
			
			<div class="select-field">
				{if $filter.feature_type == "D"}
				{if $search.custom_range[$filter.feature_id].from || $search.custom_range[$filter.feature_id].to}
					{assign var="date_extra" value=""}
				{else}
					{assign var="date_extra" value="disabled="\disabled\""}
				{/if}
				{include file="common_templates/calendar.tpl" date_id="range_`$el_id`_from" date_name="custom_range[`$filter.feature_id`][from]" date_val=$search.custom_range[$filter.feature_id].from extra=$date_extra start_year=$settings.Company.company_start_year}
				{include file="common_templates/calendar.tpl" date_id="range_`$el_id`_to" date_name="custom_range[`$filter.feature_id`][to]" date_val=$search.custom_range[$filter.feature_id].to extra=$date_extra start_year=$settings.Company.company_start_year}
				<input type="hidden" name="custom_range[{$filter.feature_id}][type]" value="D" />
				{else}
				<input type="text" name="{if $filter.field_type}field_range[{$filter.field_type}]{else}custom_range[{$filter.feature_id}]{/if}[from]" id="range_{$el_id}_from" size="3" class="input-text-short{if $search.variants[$el_id] != "O"} disabled{/if}" value="{$search.custom_range[$filter.feature_id].from|default:$search.field_range[$filter.field_type].from}" {if $search.variants[$el_id] != "O"}disabled="disabled"{/if} />
				&nbsp;-&nbsp;
				<input type="text" name="{if $filter.field_type}field_range[{$filter.field_type}]{else}custom_range[{$filter.feature_id}]{/if}[to]" size="3" class="input-text-short{if $search.variants[$el_id] != "O"} disabled{/if}" value="{$search.custom_range[$filter.feature_id].to|default:$search.field_range[$filter.field_type].to}" id="range_{$el_id}_to" {if $search.variants[$el_id] != "O"}disabled="disabled"{/if} />
				{/if}
			</div>
			{/if}
			<script type="text/javascript">
			//<![CDATA[
			$(":radio[name='variants[{$el_id}]']").change(function() {ldelim}
				var el_id = '{$el_id}';
				$('#range_' + el_id + '_from').attr('disabled', this.value !== 'O');
				$('#range_' + el_id + '_to').attr('disabled', this.value !== 'O');
				{if $filter.feature_type == "D"}
				$('#range_' + el_id + '_from_but').attr('disabled', this.value !== 'O');
				$('#range_' + el_id + '_to_but').attr('disabled', this.value !== 'O');
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
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="ranges_{$el_id}_none" value="" {if !$search.ch_filters[$el_id]}checked="checked"{/if} />
				<label for="ranges_{$el_id}_none">{$lang.none}</label>
			</div>
			
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="ranges_{$el_id}_yes" value="Y" {if $search.ch_filters[$el_id] == "Y"}checked="checked"{/if} />
				<label for="ranges_{$el_id}_yes">{$lang.yes}</label>
			</div>
			
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="ranges_{$el_id}_no" value="N" {if $search.ch_filters[$el_id] == "N"}checked="checked"{/if} />
				<label for="ranges_{$el_id}_no">{$lang.no}</label>
			</div>
			
			{if !$filter.condition_type}
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[{$el_id}]" id="ranges_{$el_id}_any" value="A" {if $search.ch_filters[$el_id] == "A"}checked="checked"{/if} />
				<label for="ranges_{$el_id}_any">{$lang.any}</label>
			</div>
			{/if}
			
		{elseif $filter.feature_type == "T"}
			<div class="select-field nowrap">
			{$filter.prefix}<input type="text" name="tx_features[{$filter.feature_id}]" class="input-text{if $filter.prefix || $filter.suffix}-medium{/if}" value="{$search.tx_features[$filter.feature_id]}" />{$filter.suffix}
			</div>
		{/if}
	</td>
{/foreach}
</tr>
{/foreach}
</table>
{/capture}

{if $separate_form}

{capture name="section"}
<form action="{""|fn_url}" method="get" name="advanced_filter_form">

{$smarty.capture.filtering}

<div class="buttons-container">
	{include file="buttons/button.tpl" but_name="dispatch[`$smarty.request.dispatch`]" but_text=$lang.submit}
	&nbsp;{$lang.or}&nbsp;&nbsp;<a class="tool-link cm-reset-link">{$lang.reset_filter}</a>
</div>

</form>
{/capture}

{if $search.variants}
	{assign var="_collapse" value=true}
{else}
	{assign var="_collapse" value=false}
{/if}
{include file="common_templates/section.tpl" section_title=$lang.advanced_filter section_content=$smarty.capture.section collapse=$_collapse}

{else}

{include file="common_templates/subheader.tpl" title=$lang.advanced_filter}
{$smarty.capture.filtering}

{/if}

{elseif $search.features_hash}
	<input type="hidden" name="features_hash" value="{$search.features_hash}" />
{/if}


