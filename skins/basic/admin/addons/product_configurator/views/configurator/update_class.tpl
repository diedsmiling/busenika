{* $Id: update_class.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="class_products_form" class="cm-form-highlight">
<input type="hidden" name="class_id" value="{$product_class.class_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

{capture name="tabsbox"}

<div id="content_general">
<fieldset>
	<div class="form-field">
		<label class="cm-required" for="class_name">{$lang.name}:</label>
		<input type="text" name="update_class_data[class_name]" id="class_name" value="{$product_class.class_name}" class="input-text-large main-input" size="25" />
	</div>
	
	<div class="form-field">
		<label class="cm-required" for="group_id">{$lang.group}:</label>
		<select name="update_class_data[group_id]" id="group_id">
			<option value="0">-&nbsp;{$lang.none}&nbsp;-</option>
			{foreach from=$groups item=group}
				<option value="{$group.group_id}" {if $product_class.group_id == $group.group_id}selected="selected"{/if}>{$group.configurator_group_name}</option>
			{/foreach}
		</select>
	</div>
	
	{if $classes}
	<div class="form-field">
		<label>{$lang.compatible_classes}:</label>
		{html_checkboxes name="update_class_data[compatible_classes]" options=$classes selected=$product_class.compatible_classes columns=3}
	</div>
	{/if}
	
	{include file="common_templates/select_status.tpl" input_name="update_class_data[status]" id="update_class_data" obj=$product_class}
</fieldset>
<!--id="content_general"--></div>

<div id="content_products">
{include file="pickers/products_picker.tpl" item_ids=$product_class.product_ids data_id="added_products" input_name="update_class_data[product_ids]" type="links"}
<!--id="content_products"--></div>

<div class="buttons-container buttons-bg">
	{if $mode == "add_class"}
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[configurator.update_class_properties]"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[configurator.update_class_properties]"}
	{/if}
</div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

</form>
{/capture}
{if $mode == "add_class"}
	{assign var="title" value=$lang.new_class}
{else}
	{assign var="title" value="`$lang.editing_class`: `$product_class.class_name`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
