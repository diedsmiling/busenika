{* $Id: update_group.tpl 10273 2010-07-29 12:24:20Z alexions $ *}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="configurator_group_products_form" class="cm-form-highlight" enctype="multipart/form-data">
<input type="hidden" name="group_id" value="{$configurator_group.group_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

{capture name="tabsbox"}

<input type="hidden" name="configurator_group_data[configurator_group_type]" value="{$configurator_group.configurator_group_type}" />

<div id="content_general">
<fieldset>
	<div class="form-field">
		<label class="cm-required" for="configurator_group_name">{$lang.name}:</label>
		<div class="float-left">
			<input type="text" name="configurator_group_data[configurator_group_name]" id="configurator_group_name" value="{$configurator_group.configurator_group_name}" class="input-text-large main-input" size="25" />
			{assign var="pair" value=$configurator_group.image_pairs}
		</div>
	</div>
	
	<div class="form-field">
		<label>{$lang.images}:</label>
		<div class="float-left">
			{include file="common_templates/attach_images.tpl" image_name="configurator_main" image_object_type="conf_group" image_pair=$configurator_group.main_pair image_object_id=$configurator_group.group_id no_thumbnail=true}
		</div>
	</div>
	
	<div class="form-field">
		<label for="group_full_descr">{$lang.full_description}:</label>
		<textarea id="group_full_descr" name="configurator_group_data[full_description]" cols="55" rows="8" class="input-textarea-long">{$configurator_group.full_description}</textarea>
		{include file="common_templates/wysiwyg.tpl" id="group_full_descr"}
	</div>
	
	<div class="form-field">
		<label for="step_id">{$lang.step}:</label>
		<select name="configurator_group_data[step_id]" id="step_id">
			<option value="0">--{$lang.none}--</option>
			{foreach from=$steps item="step"}
				<option value="{$step.step_id}" {if $configurator_group.step_id == $step.step_id}selected="selected"{/if}>{$step.step_name}</option>
			{/foreach}
		</select>
	</div>
	
	<div class="form-field">
		<label for="configurator_group_type">{$lang.display_type}:</label>
		<select name="configurator_group_data[configurator_group_type]" id="configurator_group_type">
			<option value="S" {if $configurator_group.configurator_group_type == "S"}selected="selected"{/if}>{$lang.selectbox}</option>
			<option value="R" {if $configurator_group.configurator_group_type == "R"}selected="selected"{/if}>{$lang.radiogroup}</option>
			<option value="C" {if $configurator_group.configurator_group_type == "C"}selected="selected"{/if}>{$lang.checkbox}</option>
		</select>
	</div>
	
	{include file="common_templates/select_status.tpl" input_name="configurator_group_data[status]" id="configurator_group_data" obj=$configurator_group}
</fieldset>
<!--id="content_general"--></div>

<div id="content_products">
	{include file="pickers/products_picker.tpl" item_ids=$configurator_group.product_ids data_id="added_products" input_name="configurator_group_data[product_ids]" type="links"}
<!--id="content_products"--></div>

<div class="buttons-container buttons-bg">
	{if $mode == "add_group"}
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[configurator.update_group_properties]"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[configurator.update_group_properties]"}
	{/if}
</div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

</form>
{/capture}
{if $mode == "add_group"}
	{assign var="title" value=$lang.new_group}
{else}
	{assign var="title" value="`$lang.editing_group`: `$configurator_group.configurator_group_name`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
