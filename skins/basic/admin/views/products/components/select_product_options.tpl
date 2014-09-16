{* $Id: select_product_options.tpl 9228 2010-04-05 08:27:56Z alexions $ *}

{if $product_options}
<span class="cm-reload-{$id}" id="product_options_update_{$id}">
<input type="hidden" name="appearance[id]" value="{$id}" />
<input type="hidden" name="appearance[name]" value="{$name}" />

{assign var="id" value=$product.object_id|default:$id}

{if $name == "cart_products"}
	<input type="hidden" name="{$name}[{$id}][object_id]" value="{$id}" />
	<input type="hidden" name="{$name}[{$id}][product_id]" value="{$product.product_id}" />
{/if}

<p class="strong">{$lang.product_options}:</p>

<div id="opt_{$id}">
{foreach from=$product_options item="po"}
<div id="opt_{$id}_{$po.option_id}" class="form-field{if $additional_class} {$additional_class}{/if}">
	<label for="option_{$id}_{$po.option_id}" id="option_description_{$id}_{$po.option_id}" class="{if $po.required == "Y"}cm-required{/if} {if $po.regexp}cm-regexp{/if}">{$po.option_name}:</label>
	{if $po.option_type == "S"} {*Selectbox*}
		{if $po.variants}
			<select id="option_{$id}_{$po.option_id}" name="{$name}[{$id}][product_options][{$po.option_id}]" id="option_{$id}_{$po.option_id}" {if $product.options_update}onchange="fn_change_options('{$id}', '{$po.option_id}');"{/if} {if $cp.exclude_from_calculate && !$product.aoc || $po.disabled}disabled="disabled"{/if} {if $po.disabled}class="cm-skip-avail-switch"{/if}>
			{if !$po.value && $product.options_type == "S"}<option value="">{if $po.disabled}{$lang.select_option_above}{else}{$lang.please_select_one}{/if}</option>{/if}
			{foreach from=$po.variants item="vr"}
				<option value="{$vr.variant_id}" {if $po.value == $vr.variant_id}selected="selected"{/if}>{$vr.variant_name}{if $settings.General.display_options_modifiers == "Y"}{if $vr.modifier|floatval} ({include file="common_templates/modifier.tpl" mod_type=$vr.modifier_type mod_value=$vr.modifier display_sign=true}){/if}{hook name="products:select_options"}{/hook}{/if}</option>
			{/foreach}
			</select>
		{else}
			<input type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value}" id="option_{$id}_{$po.option_id}" />
			{$lang.na}
		{/if}
	{elseif $po.option_type == "R"} {*Radiobutton*}
		{if $po.variants}
			<div id="{$id}_option_{$po.option_id}" class="select-field">
				<input type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value}" id="option_{$id}_{$po.option_id}" />
				{foreach from=$po.variants item="vr" name="vars"}
					<input id="{$id}_variant_{$vr.variant_id}" type="radio" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$vr.variant_id}" {if $po.value == $vr.variant_id}checked="checked"{/if} {if $product.options_update}onclick="fn_change_options('{$c_obj|default:$id}', '{$po.option_id}');"{/if} {if $cp.exclude_from_calculate && !$product.aoc || $po.disabled}disabled="disabled"{/if} {if $po.disabled}class="cm-skip-avail-switch"{/if} />
					<label id="option_description_{$id}_{$po.option_id}_{$vr.variant_id}">
					{$vr.variant_name}&nbsp;{if $settings.General.display_options_modifiers == "Y"}{if $vr.modifier|floatval}({include file="common_templates/modifier.tpl" mod_type=$vr.modifier_type mod_value=$vr.modifier display_sign=true}){/if}{hook name="products:select_options"}{/hook}{/if}</label>
				{/foreach}
				{if !$po.value && $product.options_type == "S" && !$po.disabled}<p class="description clear-both">{$lang.please_select_one}</p>{/if}
			</div>
		{else}
			<input type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value}" id="option_{$id}_{$po.option_id}" />
			{$lang.na}
		{/if}
	{elseif $po.option_type == "C"} {*Checkbox*}

		{foreach from=$po.variants item="vr"}
		{if $vr.position == 0}
			<input id="unchecked_{$id}_option_{$po.option_id}" type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$vr.variant_id}" />
		{else}
			<div class="select-field">
				<input id="{$id}_option_{$po.option_id}" type="checkbox" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$vr.variant_id}" {if $po.value == $vr.variant_id}checked="checked"{/if} {if $product.options_update}onclick="fn_change_options('{$c_obj|default:$id}', '{$po.option_id}');"{/if} {if $cp.exclude_from_calculate && !$product.aoc || $po.disabled}disabled="disabled"{/if} {if $po.disabled}class="cm-skip-avail-switch"{/if} />

				<label>{if $settings.General.display_options_modifiers == "Y"}{if $vr.modifier|floatval}&nbsp;({include file="common_templates/modifier.tpl" mod_type=$vr.modifier_type mod_value=$vr.modifier display_sign=true}){/if}{hook name="products:select_options"}{/hook}{/if}</label>
			</div>
		{/if}
		{/foreach}

	{elseif $po.option_type == "I"} {*Input*}
		<input id="option_{$id}_{$po.option_id}" type="text" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value|default:$po.inner_hint}" {if $cp.exclude_from_calculate && !$product.aoc}disabled="disabled"{/if} class="input-text {if $po.inner_hint && $po.value == ""}cm-hint{/if}" />
	{elseif $po.option_type == "T"} {*Textarea*}
		<textarea id="option_{$id}_{$po.option_id}" name="{$name}[{$id}][product_options][{$po.option_id}]" {if $cp.exclude_from_calculate}disabled="disabled"{/if} class="input-textarea-long {if $po.inner_hint && $po.value == ""}cm-hint{/if}">{$po.value|default:$po.inner_hint}</textarea>
	{elseif $po.option_type == "F"} {*File*}
		<div class="clear">
			{include file="common_templates/fileuploader.tpl" images=$product.extra.custom_files[$po.option_id] var_name="`$name`[`$po.option_id``$id`]" multiupload=$po.multiupload hidden_name="`$name`[custom_files][`$po.option_id``$id`]" hidden_value="`$id`_`$po.option_id`" label_id="option_`$id`_`$po.option_id`" hide_server=true}
		</div>
	{/if}
	
	{if $po.regexp}
		<script type="text/javascript">
		//<![CDATA[
			regexp['option_{$id}_{$po.option_id}'] = {$ldelim}regexp: "{$po.regexp}", message: "{$po.incorrect_message}"{$rdelim};
		//]]>
		</script>
	{/if}
</div>
{/foreach}
</div>
{if $show_aoc}
<input type="hidden" name="appearance[show_aoc]" value="{$show_aoc}" />
<div class="select-field">
	<input id="option_{$id}_AOC" type="checkbox" name="{$name}[{$id}][product_options][AOC]" class="checkbox" value="N" onclick="options_routine.disable('opt_{$id}', this);"/><label for="option_{$id}_AOC">{$lang.any_option_combinations}</label>
</div>
{/if}
<!--product_options_update_{$id}--></span>
{/if}
