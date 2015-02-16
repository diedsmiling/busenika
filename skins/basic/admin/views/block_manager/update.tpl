{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}
{assign var="id" value=$block.block_id|default:"0"}
{assign var="block_type" value=$block.block_type|default:$block_type}
<div id="content_group{$id}{$block_type}_{$location}">
<form action="{""|fn_url}" method="post" class="cm-form-highlight" name="block_{$location}_{$id}{$block_type}_update_form">
<input type="hidden" name="block[block_type]" value="{$block_type}" />
{assign var="js_param" value="false"}
{if $add_block}
	{assign var="js_param" value="true"}
	<input type="hidden" name="add_selected_section" id="add_selected_section_{$id}{$block_type}" value="{$location|default:"all_pages"}" />
{else}
	<input type="hidden" name="block[block_id]" value="{$id}" />
	<input type="hidden" name="redirect_location" value="{$location}" />
	<input type="hidden" name="block[location]" value="{$block.location}" />


	<script type="text/javascript">
	//<![CDATA[
	block_properties['{$location}_{$id}{$block_type}_'] = {$block.properties|to_json};
	block_location['{$location}_{$id}{$block_type}_'] = '{$block.location}';
	block_properties_used['{$location}_{$id}{$block_type}_'] = false;
	//]]>
	</script>
{/if}
{if $redirect_url}
	<input type="hidden" name="redirect_url" value="{$redirect_url}" />
{/if}

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li class="cm-js cm-active"><a>{$lang.general}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content">
	<fieldset>

		<div class="form-field">
			<label for="{$location}_{$id}{$block_type}_block_name" class="cm-required">{$lang.name}:</label>
			<input type="text" name="block[description]" id="{$location}_{$id}{$block_type}_block_name" size="25" value="{$block.description}" class="input-text main-input" />
		</div>

	{if $block.text_id != "central_content"}
		{if $block_type == "B"}
		<div class="form-field float-left">
			<label for="{$location}_{$id}{$block_type}_block_object">{$lang.block_content}:</label>
			<select name="block[list_object]" id="{$location}_{$id}{$block_type}_block_object" onchange="fn_check_block_params({$js_param}, '{$location}', {$id}, this, '{$block_type}'); fn_get_specific_settings(this.value, {$id}, 'list_object', '{$block_type}');">
			<optgroup label="{$lang.list_objects}">
				{foreach from=$block_settings.dynamic key="object_name" item="listed_block"}
					<option value="{$object_name}"{if $block.properties.list_object == $object_name} selected="selected"{/if}>{if $listed_block.object_description}{$lang[$listed_block.object_description]}{else}{$lang.$object_name}{/if}</option>
				{/foreach}
			</optgroup>
			<optgroup label="{$lang.standard_sidebox}">
				{foreach from=$block_settings.static item="static_block"}
					<option value="{$static_block.template}" {if $block.properties.list_object == $static_block.template}selected="selected"{/if}>{$static_block.name}</option>
				{/foreach}
			</optgroup>
			{foreach from=$block_settings.additional key="section" item="section_data"}
				<optgroup label="{$lang.$section}">
				{foreach from=$section_data.items key="object_name" item="additional_block"}
					<option value="{$additional_block.template}" {if $block.properties.list_object == $additional_block.template}selected="selected"{/if}>{$additional_block.name}</option>
				{/foreach}
				</optgroup>
			{/foreach}
			</select>
		</div>
		{assign var="index" value=$block.properties.list_object|default:"products"}
		{include file="views/block_manager/specific_settings.tpl" spec_settings=$specific_settings.list_object[$index] s_set_id="`$id``$block_type`_list_object"}

		<div class="form-field float-left">
			<label for="{$location}_{$id}{$block_type}_id_fillings">{$lang.filling}:</label>
			<select name="block[fillings]" id="{$location}_{$id}{$block_type}_id_fillings" onchange="fn_check_block_params({$js_param}, '{$location}', {$id}, this, '{$block_type}');">
			</select>
		</div>

		{assign var="index" value=$block.properties.fillings|default:"manually"}
		{include file="views/block_manager/specific_settings.tpl" spec_settings=$specific_settings.fillings[$index] s_set_id="`$id``$block_type`_fillings"}
		{/if}

		{if $location != "product_details" && $block.text_id == ""}
			<div class="form-field">
				<label for="{$location}_{$id}{$block_type}_id_positions">{$lang.group}:</label>
				<select name="block[group_id]" id="{$location}_{$id}{$block_type}_id_positions"{if !$add_block} onchange="fn_check_block_parent({$id}, this, '{$location}', {$object_id});"{/if}>
				{foreach from=$avail_positions item="pos"}
					{if !$pos.parent_id}
					<option value="{$pos.block_id}"{if $block_parent == $pos.block_id} selected="selected"{/if}>{$pos.description}</option>
						{if $block_type == "B"}
						{foreach from=$avail_positions item="pos_child"}
							{if $pos_child.parent_id == $pos.block_id}
							<option	value="{$pos_child.block_id}"{if $block_parent == $pos_child.block_id} selected="selected"{/if}>{$pos_child.description|indent:1:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
							{/if}
						{/foreach}
						{/if}
					{/if}
				{/foreach}
				</select>
				{if !$add_block}
				<input id="{$location}_{$id}{$block_type}_id_positions_rewrite" type="hidden" name="block[rewrite_positions]" value="N" />
				{/if}
			</div>
		{/if}

		{if $block_type == "B"}
		<div class="form-field float-left">
			<label for="{$location}_{$id}{$block_type}_id_appearances">{$lang.appearance_type}:</label>
			<select name="block[appearances]" id="{$location}_{$id}{$block_type}_id_appearances" onchange="fn_get_specific_settings(this.value, {$id}, 'appearances', '{$block_type}');">
			</select>
		</div>

		{assign var="index" value=$block.properties.appearances|default:"blocks/products_text_links.tpl"}
		{include file="views/block_manager/specific_settings.tpl" spec_settings=$specific_settings.appearances[$index] s_set_id="`$id``$block_type`_appearances"}

		{else}
		<div class="form-field">
			<label for="{$location}_{$id}{$block_type}_id_block_order">{$lang.block_order}:</label>
			<select name="block[block_order]" id="{$location}_{$id}{$block_type}_id_block_order">
				<option value="H"{if $block.properties.block_order == "H"} selected="selected"{/if}>{$lang.horizontal}</option>
				<option value="V"{if $block.properties.block_order == "V"} selected="selected"{/if}>{$lang.vertical}</option>
			</select>
		</div>
		{/if}
	{/if}

		<div class="form-field">
			<label for="{$location}_{$id}{$block_type}_id_wrapper">{$lang.wrapper}:</label>
			<select name="block[wrapper]" id="{$location}_{$id}{$block_type}_id_wrapper">
				<option value="">--</option>
				{foreach from=$block_settings.wrappers item="w"}
				<option value="{$w}" {if $block.properties.wrapper == $w}selected="selected"{/if}>{$w}</option>
				{/foreach}
			</select>
		</div>

		{if $block_type == "B"}
		<div class="form-field">
			<label for="{$location}_{$id}{$block_type}_block_width">{$lang.block_width}:</label>
			<input type="text" name="block[width]" id="{$location}_{$id}{$block_type}_block_width" size="25" value="{$block.properties.width}" class="input-text-short cm-value-integer" />
			<select name="block[width_unit]">
				<option value="P"{if $block.properties.width_unit == "P"} selected="selected"{/if}>{$lang.percents}</option>
				<option value="A"{if $block.properties.width_unit == "A"} selected="selected"{/if}>{$lang.pixels}</option>
			</select>
		</div>
		{/if}
        {if $id==49}
        <div class="form-field">
            <label for="auto_add">{$lang.auto_add}:</label>
            <input type="hidden" name="block[auto_add]" value="N" />
            <input type="checkbox" class="checkbox" name="block[auto_add]" value="Y" id="auto_add" {if $block.properties.auto_add == "Y"}checked="checked"{/if} />
        </div>
        {/if}
		{hook name="block_manager:settings"}
		{/hook}
	</fieldset>
	</div>
</div>

{if $block.text_id != "central_content"}
<script type="text/javascript">
//<![CDATA[
fn_check_block_params({$js_param}, '{$location}', {$id}, null, '{$block_type}');
//]]>
</script>
{/if}
<div class="buttons-container">
	{if $add_block}
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[block_manager.add]" cancel_action="close"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[block_manager.update]" cancel_action="close"}
	{/if}
</div>
</form>
<!--content_group{$id}{$block_type}_{$location}--></div>
