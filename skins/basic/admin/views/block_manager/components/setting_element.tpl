{* $Id: setting_element.tpl 8248 2009-11-17 11:36:32Z lexa $ *}

{if $option.force_open}
<script type="text/javascript">
	$('#additional_{$set_id}').show();
</script>
{/if}
<div class="form-field">
{if !$option.hide_label}
	<label for="spec_{$set_name}_{$set_id}"{if $option.required} class="cm-required"{/if}>{if $option.option_name}{$lang[$option.option_name]}{else}{$lang.$set_name}{/if}:</label>
{/if}
{** Checkbox **}
{if $option.type == "checkbox"}
	<input type="hidden" name="block[{$set_name}]" value="N" />
	<input type="checkbox" class="checkbox" name="block[{$set_name}]" value="Y" id="spec_{$set_name}_{$set_id}" {if $block.properties.$set_name && $block.properties.$set_name == "Y" || !$block.properties.$set_name && $option.default_value == "Y"}checked="checked"{/if} />
{** Selectbox **}
{elseif $option.type == "selectbox"}
	<select id="spec_{$set_name}_{$set_id}" name="block[{$set_name}]">
	{foreach from=$option.values key="k" item="v"}
		<option value="{$k}" {if $block.properties.$set_name && $block.properties.$set_name == $k || !$block.properties.$set_name && $option.default_value == $k}selected="selected"{/if}>{if $option.no_lang}{$v}{else}{$lang.$v}{/if}</option>
	{/foreach}
	</select>
{elseif $option.type == "input" || $option.type == "input_long"}
	<input id="spec_{$set_name}_{$set_id}" class="input-text{if $option.type == "input_long"}-long{/if}" name="block[{$set_name}]" value="{if $block.properties.$set_name}{$block.properties.$set_name}{else}{$option.default_value}{/if}" />

{elseif $option.type == "multiple_checkboxes"}

	{html_checkboxes name="block[`$set_name`]" options=$option.values columns=4 selected=$block.properties.$set_name}
{elseif $option.type == "text" || $option.type == "simple_text"}
	<textarea id="spec_{$set_name}_{$set_id}" name="block[{$set_name}]" cols="55" rows="8" class="input-textarea-long">{$block.properties.$set_name}</textarea>
	{if $option.type == "text"}
	{include file="common_templates/wysiwyg.tpl" id="spec_`$set_name`_`$set_id`"}
	{/if}
{/if}
</div>