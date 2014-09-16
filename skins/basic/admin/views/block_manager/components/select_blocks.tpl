{* $Id: select_blocks.tpl 10473 2010-08-20 12:05:47Z andyye $ *}

{if $blocks}
	<div class="clear">
		<div id="content_block_manager_blocks" class="listmania-lists">
			{foreach from=$blocks item="block" name="block_list"}
				&nbsp;<span class="bull">&bull;</span>&nbsp;{if $selected_block.block_id == $block.block_id}<span class="strong">{else}
				{capture name="_href"}{$index_script}?dispatch={$smarty.const.CONTROLLER}.{$smarty.const.MODE}{if $location}&amp;page_section={$location}{/if}{if $selected_block.object_id && $object_id}&amp;{$selected_block.object_id}={$object_id}{/if}&amp;selected_section={if $location}{$location}_{/if}blocks&amp;selected_block_id={$block.block_id}{/capture}
				<a href="{$smarty.capture._href|fn_url}">{/if}{$block.description}{if $selected_block.block_id == $block.block_id}</span>{else}</a>{/if}{if $lm_list.use == "Y"}&nbsp;(+){else}{/if}&nbsp;&nbsp;&nbsp;&nbsp;
			{/foreach}
		</div>
	</div>

{if $selected_block.properties.fillings == "manually"}
	{assign var="_view_mode" value="mixed"}
	{assign var="_hide_delete_button" value=false}
{else}
	{assign var="_view_mode" value="list"}
	{assign var="_hide_delete_button" value=true}
{/if}
{if $selected_block.item_ids.block_text || $selected_block.properties.per_object}
	<div class="listed-items">
	{include file="common_templates/subheader.tpl" title=$lang.html_block}
	
	{if !$selected_block.properties.per_object}
		{$selected_block.item_ids.block_text|unescape}
	{else}
		<fieldset>
			<input type="hidden" name="{$data_name}[block_id]" value="{$selected_block.block_id}" />
			<textarea id="block_text" name="{$data_name}[add_items][block_data][block_text]" cols="65" rows="8" class="input-textarea">{$selected_block.item_ids.block_text}</textarea>
			{include file="common_templates/wysiwyg.tpl" id="block_text"}
		<fieldset>
	{/if}
	</div>
{/if}
	<div class="clear">
		{if $selected_block.properties.fillings && $block_properties[$selected_block.properties.list_object].picker_props.picker}
		<div class="listed-items">
			{include file="common_templates/subheader.tpl" title=$lang.listed_items}
			<input type="hidden" name="{$data_name}[block_id]" value="{$selected_block.block_id}" />
			{if $selected_block.properties.fillings == "manually"}
				{assign var="show_position" value=true}
			{else}
				{assign var="show_position" value=false}
			{/if}

			{include file=$block_properties[$selected_block.properties.list_object].picker_props.picker data_id="added_`$selected_block.block_id`" input_name="`$data_name`[add_items]" item_ids=$selected_block.item_ids positions=$show_position view_mode=$_view_mode hide_delete_button=$_hide_delete_button params_array=$block_properties[$selected_block.properties.list_object].picker_props.params}
		</div>
		{/if}

		<div class="general-items">
			{include file="common_templates/subheader.tpl" title=$lang.general}
			<div class="form-field">
				<label>{$lang.block_name}:</label>
				<a href="{"block_manager.manage?selected_section=`$section`"|fn_url}">{$selected_block.description}</a>
			</div>

			<div class="form-field">
				<label>{$lang.filling}:</label>
				{if $selected_block.properties.fillings}{$selected_block.properties.fillings|fn_get_lang_var}{else}{$lang.static_block}{/if}
			</div>

			<div class="form-field">
				<label for="enable_block_{$selected_block.block_id}">{$lang.enable_for_this_page}:</label>
				{if $object_id}
					{assign var="location_url" value="&amp;location=`$smarty.const.CONTROLLER`&amp;object_id=`$object_id`"}
				{elseif $location}
					{assign var="location_url" value="&amp;location=`$location`"}
				{/if}
				<input id="enable_block_{$selected_block.block_id}" type="checkbox" name="enable_block_{$selected_block.block_id}" value="Y" {if $selected_block.assigned == "Y"}checked="checked"{/if} onclick="jQuery.ajaxRequest('{"block_manager.enable_disable?block_id=`$selected_block.block_id``$location_url`&amp;enable="|fn_url:'A':'rel':'&'}' + (this.checked ? this.value : 'N'), {literal}{method: 'POST', cache: false}{/literal});" />
			</div>
			{if $selected_block.disabled}
				<div class="form-field">
					<label>{$lang.disabled}:</label>
					{$selected_block.disabled}
				</div>
			{/if}
		</div>
	</div>

{else}
	<p class="no-items">{$lang.no_blocks_defined} <a href="{"block_manager.manage?selected_section=`$section`"|fn_url}">{$lang.manage_custom_blocks} &raquo;</a></p>
{/if}
