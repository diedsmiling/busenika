{* $Id: condition.tpl 7426 2009-05-06 05:39:30Z angel $ *}

{assign var="l" value="promotion_cond_`$condition_data.condition`"}

<div class="form-field cm-row-item">
	<div class="float-right">
		{include file="buttons/remove_item.tpl" but_class="cm-delete-row" simple=true}
	</div>
	<label>{$lang.$l}:&nbsp;</label>

	{if $schema.conditions[$condition_data.condition].type == "mixed"}
	{assign var="p_md" value=$prefix|md5}
	{assign var="condition_element" value=$condition_data.condition_element}
	<select name="{$prefix}[condition_element]" onchange="fn_promotion_rebuild_mixed_data(this.value, '{$p_md}', '{$condition_data.value}');">
	{assign var="items" value=$schema.conditions[$condition_data.condition].conditions_function|fn_get_promotion_variants}
	{foreach from=$items key="_k" item="v"}
	{if $v.is_group}
	<optgroup label="{$v.group}">
	{foreach from=$v.items key="__k" item="__v"}
		{if !$condition_element}
		{assign var="condition_element" value=$__k}
		{/if}
		<option value="{$__k}" {if $__k == $condition_data.condition_element}selected="selected"{/if}>{$__v.value}</option>
	{/foreach}
	</optgroup>
	{else}
	{if !$condition_element}
	{assign var="condition_element" value=$__k}
	{/if}
	<option value="{$_k}" {if $_k == $condition_data.condition_element}selected="selected"{/if}>{$v.value}</option>
	{/if}
	{/foreach}
	</select>

	<script type="text/javascript">
	//<![CDATA[
	var mixed_data_{$p_md} = {$items|to_json};

	$(document).ready(function(){$ldelim}
		fn_promotion_rebuild_mixed_data('{$condition_element}', '{$p_md}', '{$condition_data.value}');		
	{$rdelim});
	//]]>
	</script>

	{/if}

	{if $schema.conditions[$condition_data.condition].type != "list" && $schema.conditions[$condition_data.condition].type != "statement"}
	<select name="{$prefix}[operator]">
	{foreach from=$schema.conditions[$condition_data.condition].operators item="op"}
	{assign var="l" value="promotion_op_`$op`"}
	<option value="{$op}" {if $op == $condition_data.operator}selected="selected"{/if}>{$lang.$l}</option>
	{/foreach}
	</select>
	{/if}

	<input type="hidden" name="{$prefix}[condition]" value="{$condition_data.condition}" />

	{if $schema.conditions[$condition_data.condition].type == "input"}
	<input type="text" name="{$prefix}[value]" value="{$condition_data.value}" class="input-text" />

	{elseif $schema.conditions[$condition_data.condition].type == "select"}
	<select name="{$prefix}[value]">
	{foreach from=$schema.conditions[$condition_data.condition].variants|default:$schema.conditions[$condition_data.condition].variants_function|fn_get_promotion_variants key="_k" item="v"}
	<option value="{$_k}" {if $_k == $condition_data.value}selected="selected"{/if}>{if $schema.conditions[$condition_data.condition].variants_function}{$v}{else}{$lang.$v}{/if}</option>
	{/foreach}
	</select>

	{elseif $schema.conditions[$condition_data.condition].type == "picker"}
	
		{assign var="_z" value="params_$zone"}
		{if $schema.conditions[$condition_data.condition].picker_props.$_z}
			{assign var="params" value=$schema.conditions[$condition_data.condition].picker_props.$_z}
		{else}
			{assign var="params" value=$schema.conditions[$condition_data.condition].picker_props.params}		
		{/if}

		{include file=$schema.conditions[$condition_data.condition].picker_props.picker data_id="objects_`$elm_id`" input_name="`$prefix`[value]" item_ids=$condition_data.value params_array=$params}

	{elseif $schema.conditions[$condition_data.condition].type == "list"}
		<input type="hidden" name="{$prefix}[operator]" value="in" />
		<input type="hidden" name="{$prefix}[value]" value="{$condition_data.value}" />

		{$condition_data.value|default:$lang.no_data}

	{elseif $schema.conditions[$condition_data.condition].type == "statement"}
		<input type="hidden" name="{$prefix}[operator]" value="eq" />
		<input type="hidden" name="{$prefix}[value]" value="Y" />

		{$lang.yes}
	
	{elseif $schema.conditions[$condition_data.condition].type == "mixed"}
		<select id="mixed_select_{$p_md}" name="{$prefix}[value]" class="hidden"></select>
		<input id="mixed_input_{$p_md}" type="text" name="{$prefix}[value]" value="{$condition_data.value}" class="hidden" />
	{/if}
</div>
