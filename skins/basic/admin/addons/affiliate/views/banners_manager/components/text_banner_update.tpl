{* $Id: text_banner_update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

<form action="{""|fn_url}" method="post" name="banner_form" class="cm-form-highlight">
<input type="hidden" name="banner_id" value="{$banner.banner_id}" />
<input type="hidden" name="banner[type]" value="T" />
<input type="hidden" name="banner[link_to]" value="{$link_to}" />

<fieldset>

<div class="form-field">
	<label for="title" class="cm-required">{$lang.title}:</label>
	<input type="text" name="banner[title]" id="title" value="{$banner.title}" size="50" class="input-text-large main-input" />
</div>

<div class="form-field">
	<label for="show_title">{$lang.show_title}:</label>
	<input type="hidden" name="banner[show_title]" value="N" />
	<input type="checkbox" name="banner[show_title]" id="show_title" {if $banner.show_title == "Y" || !$banner}checked="checked"{/if} value="Y" class="checkbox" />
</div>

<div class="form-field">
	<label for="width">{$lang.width}&nbsp;({$lang.pixels}):</label>
	<input type="text" name="banner[width]" id="width" value="{$banner.width}" size="10" class="input-text-short" />
</div>

<div class="form-field">
	<label for="height">{$lang.height}&nbsp;({$lang.pixels}):</label>
	<input type="text" name="banner[height]" id="height" value="{$banner.height}" size="10" class="input-text-short" />
</div>

<div class="form-field">
	<label for="id_banner_content" class="cm-required">{$lang.content}:</label>
	<textarea id="id_banner_content" name="banner[content]" cols="50" rows="5" class="input-textarea-long">{$banner.content}</textarea>
</div>

<div class="form-field">
	<label for="new_window">{$lang.open_in_new_window}:</label>
	<input type="hidden" name="banner[new_window]" value="N" />
	<input type="checkbox" name="banner[new_window]" id="new_window" {if $banner.new_window == "Y"}checked="checked"{/if} value="Y" class="checkbox" />
</div>

<div class="form-field">
	<label for="show_url">{$lang.show_url}:</label>
	<input type="hidden" name="banner[show_url]" value="N" />
	<input type="checkbox" name="banner[show_url]" id="show_url" {if $banner.show_url == "Y" || !$banner}checked="checked"{/if} value="Y" class="checkbox" />
</div>

{include file="common_templates/select_status.tpl" input_name="banner[status]" id="banner" obj=$banner}

{if $link_to == "G"}
	<div class="form-field">
		<label for="group_id">{$lang.product_group}:</label>
		<select name="banner[data]" id="group_id">
			<option	value="0">{$lang.none}</option>
			{if $all_groups_list}
				{foreach from=$all_groups_list item="group"}
					<option	value="{$group.group_id}" {if $banner.group_id == $group.group_id}selected="selected"{/if}>{$group.name}</option>
				{/foreach}
			{/if}
		</select>
	</div>

{elseif $link_to == "C"}

	{include file="common_templates/subheader.tpl" title=$lang.categories}
	{if $banner.categories}
		{assign var="c_ids" value=$banner.categories|array_keys}
	{/if}

	{include file="pickers/categories_picker.tpl" input_name="banner[data]" item_ids=$c_ids multiple=true}

{elseif $link_to == "P"}

	{include file="common_templates/subheader.tpl" title=$lang.products}

	{include file="pickers/products_picker.tpl" item_ids=$banner.data data_id="added_products" input_name="banner[data]" type="links"}

{else}
	<div class="form-field">
		<label for="url" class="cm-required">{$lang.url}:</label>
		<input type="text" name="banner[data]" id="url" value="{$banner.url}" size="50" class="input-text" />
	</div>

{/if}

</fieldset>

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[banners_manager.update]"}
</div>

</form>
