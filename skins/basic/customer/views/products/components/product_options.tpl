{* $Id: product_options.tpl 10500 2010-08-24 13:07:07Z alexions $ *}

{script src="js/jquery.simpletip-1.3.1.js"}

{if ($settings.General.display_options_modifiers == "Y" && ($auth.user_id  || ($settings.General.allow_anonymous_shopping != "P" && !$auth.user_id)))}
{assign var="show_modifiers" value=true}
{/if}

<input type="hidden" name="appearance[details_page]" value="{$details_page}" />
{foreach from=$product.detailed_params key="param" item="value"}
	<input type="hidden" name="additional_info[{$param}]" value="{$value}" />
{/foreach}

{if $product_options}
{if $obj_prefix}
	<input type="hidden" name="appearance[obj_prefix]" value="{$obj_prefix}" />
{/if}

{if $location == "cart" || $product.object_id}
	<input type="hidden" name="{$name}[{$id}][object_id]" value="{$id|default:$obj_id}" />
{/if}

<div id="opt_{$obj_prefix}{$id}">
	{foreach name="product_options" from=$product_options item="po"}
	
	{assign var="selected_variant" value=""}
	<div class="form-field{if !$capture_options_vs_qty} product-list-field{/if} clear" id="opt_{$obj_prefix}{$id}_{$po.option_id}">
		{if !("SRC"|strpos:$po.option_type !== false && !$po.variants && $po.missing_variants_handling == "H")}
		<label for="option_{$obj_prefix}{$id}_{$po.option_id}" class="{if $po.required == "Y"}cm-required{/if} {if $po.regexp}cm-regexp{/if}">{$po.option_name}{if $po.description}&nbsp;{capture name="tooltip"}{$po.description}{/capture}{include file="common_templates/tooltip.tpl" tooltip=$smarty.capture.tooltip}{/if}:</label>
		{if $po.option_type == "S"} {*Selectbox*}
			{if $po.variants}
				{if $po.disabled && !$po.not_required}<input type="hidden" value="" id="option_{$obj_prefix}{$id}_{$po.option_id}" />{/if}
				<select name="{$name}[{$id}][product_options][{$po.option_id}]" {if !$po.disabled}id="option_{$obj_prefix}{$id}_{$po.option_id}"{/if} onchange="{if $product.options_update}fn_change_options('{$obj_prefix}{$id}', '{$id}', '{$po.option_id}');{/if} fn_change_varian_image('{$obj_prefix}{$id}', '{$po.option_id}');" {if $product.exclude_from_calculate && !$product.aoc || $po.disabled}disabled="disabled" class="disabled"{/if}>
				{if $product.options_type == "S"}<option value="">{if $po.disabled}{$lang.select_option_above}{else}{$lang.please_select_one}{/if}</option>{/if}
					{foreach from=$po.variants item="vr" name=vars}
						{if !$po.disabled || ($po.disabled && $po.value && $po.value == $vr.variant_id)}
							<option value="{$vr.variant_id}" {if $po.value == $vr.variant_id}{assign var="selected_variant" value=$vr.variant_id}selected="selected"{/if}>{$vr.variant_name} {if $show_modifiers}{hook name="products:options_modifiers"}{if $vr.modifier|floatval}({include file="common_templates/modifier.tpl" mod_type=$vr.modifier_type mod_value=$vr.modifier display_sign=true}){/if}{/hook}{/if}</option>
						{/if}
					{/foreach}
				</select>
			{else}
				<input type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value}" id="option_{$obj_prefix}{$id}_{$po.option_id}" />
				{$lang.na}
			{/if}
		{elseif $po.option_type == "R"} {*Radiobutton*}
			{if $po.variants}
				<ul id="option_{$obj_prefix}{$id}_{$po.option_id}_group">
					<li class="hidden"><input type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value}" id="option_{$obj_prefix}{$id}_{$po.option_id}" /></li>
					{if !$po.disabled}
						{foreach from=$po.variants item="vr" name="vars"}
							<li><input type="radio" class="radio" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$vr.variant_id}" {if $po.value == $vr.variant_id }{assign var="selected_variant" value=$vr.variant_id}checked="checked"{/if} onclick="{if $product.options_update}fn_change_options('{$obj_prefix}{$id}', '{$id}', '{$po.option_id}');{/if} fn_change_varian_image('{$obj_prefix}{$id}', '{$po.option_id}', '{$vr.variant_id}');" {if $product.exclude_from_calculate && !$product.aoc || $po.disabled}disabled="disabled"{/if} />
							<span id="option_description_{$obj_prefix}{$id}_{$po.option_id}_{$vr.variant_id}">{$vr.variant_name}&nbsp;{if  $show_modifiers}{hook name="products:options_modifiers"}{if $vr.modifier|floatval}({include file="common_templates/modifier.tpl" mod_type=$vr.modifier_type mod_value=$vr.modifier display_sign=true}){/if}{/hook}{/if}</span></li>
						{/foreach}
					{/if}
				</ul>
				{if !$po.value && $product.options_type == "S" && !$po.disabled}<p class="description clear-both">{$lang.please_select_one}</p>{elseif !$po.value && $product.options_type == "S" && $po.disabled}<p class="description clear-both">{$lang.select_option_above}</p>{/if}
			{else}
				<input type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value}" id="option_{$obj_prefix}{$id}_{$po.option_id}" />
				{$lang.na}
			{/if}

		{elseif $po.option_type == "C"} {*Checkbox*}

			{foreach from=$po.variants item="vr"}
			{if $vr.position == 0}
				<input id="unchecked_option_{$obj_prefix}{$id}_{$po.option_id}" type="hidden" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$vr.variant_id}" {if $po.disabled}disabled="disabled"{/if} />
			{else}
				<input id="option_{$obj_prefix}{$id}_{$po.option_id}" type="checkbox" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$vr.variant_id}" class="checkbox" {if $po.value == $vr.variant_id}checked="checked"{/if} {if $product.exclude_from_calculate && !$product.aoc || $po.disabled}disabled="disabled"{/if} {if $product.options_update}onclick="fn_change_options('{$obj_prefix}{$id}', '{$id}', '{$po.option_id}');"{/if}/>
				{if $show_modifiers}{hook name="products:options_modifiers"}{if $vr.modifier|floatval}({include file="common_templates/modifier.tpl" mod_type=$vr.modifier_type mod_value=$vr.modifier display_sign=true}){/if}{/hook}{/if}
			{/if}
			{/foreach}

		{elseif $po.option_type == "I"} {*Input*}
			<input id="option_{$obj_prefix}{$id}_{$po.option_id}" type="text" name="{$name}[{$id}][product_options][{$po.option_id}]" value="{$po.value|default:$po.inner_hint}" {if $product.exclude_from_calculate && !$product.aoc}disabled="disabled"{/if} class="valign input-text{if $po.inner_hint && $po.value == ""} cm-hint{/if}{if $product.exclude_from_calculate && !$product.aoc} disabled{/if}" />
		{elseif $po.option_type == "T"} {*Textarea*}
			<textarea id="option_{$obj_prefix}{$id}_{$po.option_id}" class="input-textarea-long{if $po.inner_hint && $po.value == ""} cm-hint{/if}{if $product.exclude_from_calculate && !$product.aoc} disabled{/if}" rows="3" name="{$name}[{$id}][product_options][{$po.option_id}]" {if $product.exclude_from_calculate && !$product.aoc}disabled="disabled"{/if}>{$po.value|default:$po.inner_hint}</textarea>
		{elseif $po.option_type == "F"} {*File*}
			<div class="clear">
				{include file="common_templates/fileuploader.tpl" images=$product.extra.custom_files[$po.option_id] var_name="`$name`[`$po.option_id``$id`]" multiupload=$po.multiupload hidden_name="`$name`[custom_files][`$po.option_id``$id`]" hidden_value="`$id`_`$po.option_id`" label_id="option_`$obj_prefix``$id`_`$po.option_id`" prefix=$obj_prefix}
			</div>
		{/if}
		{/if}

		{if $po.comment}
			<p class="description clear-both">{$po.comment}</p>
		{/if}

		{if $po.regexp && !$no_script}
			<script type="text/javascript">
			//<![CDATA[
				regexp['option_{$obj_prefix}{$id}_{$po.option_id}'] = {$ldelim}regexp: "{$po.regexp|escape:"javascript"}", message: "{$po.incorrect_message}"{$rdelim};
			//]]>
			</script>
		{/if}

		{capture name="variant_images"}
			{if !$po.disabled}
				{foreach from=$po.variants item="var"}
					{if $var.image_pair.image_id}
						{if $var.variant_id == $selected_variant}{assign var="_class" value="product-variant-image-selected"}{else}{assign var="_class" value="product-variant-image-unselected"}{/if}
						{include file="common_templates/image.tpl" class="hand $_class" show_thumbnail="Y" images=$var.image_pair object_type="product_option" image_width="50" obj_id="variant_image_`$obj_prefix``$id`_`$po.option_id`_`$var.variant_id`" image_onclick="fn_set_option_value('`$obj_prefix``$id`', '`$po.option_id`', '`$var.variant_id`'); void(0);"}
					{/if}
				{/foreach}
			{/if}
		{/capture}
		{if $smarty.capture.variant_images|trim}<div class="product-variant-image clear-both">{$smarty.capture.variant_images}</div>{/if}
	</div>
	{/foreach}
</div>
{if $product.show_exception_warning == "Y"}
	<p id="warning_{$obj_prefix}{$id}" class="cm-no-combinations{if $location != "cart"}-{$obj_prefix}{$id}{/if} price">{$lang.nocombination}</p>
{/if}
{/if}

{if !$no_script}
<script type="text/javascript">
//<![CDATA[
function fn_form_pre_{$form_name|default:"product_form_`$obj_prefix``$id`"}()
{$ldelim}
{if $location == "cart"}
	warning_class = '.cm-no-combinations';
{else}
	warning_class = '.cm-no-combinations-{$obj_prefix}{$id}';
{/if}
{literal}
	if ($(warning_class).length) {
		jQuery.showNotifications({'notification': {'type': 'W', 'title': lang.warning, 'message': lang.cannot_buy, 'save_state': false}});
		return false;
	} else {
		
		return true;
	}
{/literal}
{$rdelim};

//]]>
</script>
{/if}