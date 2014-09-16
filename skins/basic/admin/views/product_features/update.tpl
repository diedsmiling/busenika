{* $Id: update.tpl 10397 2010-08-11 13:30:26Z alexions $ *}

{if $mode == "add"}
	{if $is_group == true}
		{assign var="id" value="0G"}
	{else}
		{assign var="id" value=0}
	{/if}
{else}
	{assign var="id" value=$feature.feature_id}
{/if}

<div id="content_group{$id}">

<form action="{""|fn_url}" method="post" name="update_features_form_{$id}" class="cm-form-highlight cm-disable-empty-files" enctype="multipart/form-data">

<input type="hidden" name="redirect_url" value="{$smarty.request.return_url}" />
<input type="hidden" name="feature_id" value="{$id}" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_details_{$id}" class="cm-js cm-active"><a>{$lang.general}</a></li>
			<li id="tab_variants_{$id}" class="cm-js {if $feature.feature_type && "SMNE"|strpos:$feature.feature_type === false || !$feature}hidden{/if}"><a>{$lang.variants}</a></li>
			<li id="tab_categories_{$id}" class="cm-js {if $feature.parent_id} hidden{/if}"><a>{$lang.categories}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="tabs_content_{$id}">
		
		<div id="content_tab_details_{$id}">
		<fieldset>
			<div class="form-field">
				<label class="cm-required" for="feature_name_{$id}">{$lang.name}:</label>
				<input type="text" name="feature_data[description]" value="{$feature.description}" class="input-text-large main-input" id="feature_name_{$id}" />
			</div>

			<div class="form-field">
				<label for="feature_position_{$id}">{$lang.position}:</label>
				<input type="text" size="3" name="feature_data[position]" value="{$feature.position}" class="input-text-short" id="feature_position_{$id}" />
			</div>

			<div class="form-field">
				<label for="feature_description_{$id}">{$lang.description}:</label>
				<textarea name="feature_data[full_description]" cols="55" rows="4" class="input-textarea-long" id="feature_description_{$id}">{$feature.full_description}</textarea>
				{include file="common_templates/wysiwyg.tpl" id="feature_description_`$id`"}
			</div>

			{if $is_group || $feature.feature_type == "G"}
				<input type="hidden" name="feature_data[feature_type]" value="G" />
			{else}
			<div class="form-field">
				<label for="feature_type_{$id}" class="cm-required">{$lang.type}:</label>
				{if $feature.feature_type == "G"}{$lang.group}{else}
					<select name="feature_data[feature_type]" id="feature_type_{$id}"  onchange="fn_check_product_feature_type(this.value, 'tab_variants_{$id}');">
						<optgroup label="{$lang.checkbox}">
							<option value="C" {if $feature.feature_type == "C"}selected="selected"{/if}>{$lang.single}</option>
							<option value="M" {if $feature.feature_type == "M"}selected="selected"{/if}>{$lang.multiple}</option>
						</optgroup>
						<optgroup label="{$lang.selectbox}">
							<option value="S" {if $feature.feature_type == "S"}selected="selected"{/if}>{$lang.text}</option>
							<option value="N" {if $feature.feature_type == "N"}selected="selected"{/if}>{$lang.number}</option>
							<option value="E" {if $feature.feature_type == "E"}selected="selected"{/if}>{$lang.extended}</option>
						</optgroup>
						<optgroup label="{$lang.others}">
							<option value="T" {if $feature.feature_type == "T"}selected="selected"{/if}>{$lang.text}</option>
							<option value="O" {if $feature.feature_type == "O"}selected="selected"{/if}>{$lang.number}</option>
							<option value="D" {if $feature.feature_type == "D"}selected="selected"{/if}>{$lang.date}</option>
						</optgroup>
					</select>
				{/if}
			</div>

			<div class="form-field">
				<label for="feature_group_{$id}">{$lang.group}:</label>
				{if $feature.feature_type == "G"}-{else}
					<select name="feature_data[parent_id]" id="feature_group_{$id}" onchange="$('#tab_categories_{$id}').toggleBy(this.value != 0); $('#feature_display_on_product_{$id}, #feature_catalog_pages_{$id}').attr('disabled', this.value != 0 ? 'disabled' : '');">
						<option value="0">-{$lang.none}-</option>
						{foreach from=$group_features item="group_feature"}
							{if $group_feature.feature_type == "G"}
								<option value="{$group_feature.feature_id}"{if $group_feature.feature_id == $feature.parent_id}selected="selected"{/if}>{$group_feature.description}</option>
							{/if}
						{/foreach}
					</select>
				{/if}
			</div>
			{/if}

			<div class="form-field">
				<label for="feature_display_on_product_{$id}">{$lang.product}:</label>
				<input type="hidden" name="feature_data[display_on_product]" value="0" />
				<input type="checkbox" class="checkbox" name="feature_data[display_on_product]" value="1" {if $feature.display_on_product}checked="checked"{/if} id="feature_display_on_product_{$id}" {if $feature.parent_id}disabled="disabled"{/if} />
			</div>

			<div class="form-field">
				<label for="feature_catalog_pages_{$id}">{$lang.catalog_pages}:</label>
				<input type="hidden" name="feature_data[display_on_catalog]" value="0" />
				<input type="checkbox" class="checkbox" name="feature_data[display_on_catalog]" value="1" {if $feature.display_on_catalog}checked="checked"{/if} id="feature_catalog_pages_{$id}" {if $feature.parent_id}disabled="disabled"{/if}/>
			</div>

			{if (!$feature && !$is_group) || ($feature.feature_type && $feature.feature_type != "G")}
			<div class="form-field">
				<label for="feature_prefix_{$id}">{$lang.prefix}:</label>
				<input type="text" name="feature_data[prefix]" value="{$feature.prefix}" class="input-text-medium" id="feature_prefix_{$id}" />
			</div>

			<div class="form-field">
				<label for="feature_suffix_{$id}">{$lang.suffix}:</label>
				<input type="text" name="feature_data[suffix]" value="{$feature.suffix}" class="input-text-medium" id="feature_suffix_{$id}" />
			</div>
			{/if}
			
			{hook name="product_features:properties"}
			{/hook}
		</fieldset>
		<!--content_tab_details_{$id}--></div>

		<div class="hidden" id="content_tab_variants_{$id}">

		<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
		<tbody>
		<tr class="cm-first-sibling">
			<th>{$lang.position_short}</th>
			<th>{$lang.variant}</th>
			<th class="cm-extended-feature {if $feature.feature_type != "E"}hidden{/if}"><img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st_{$id}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-features-{$id}" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st_{$id}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-features-{$id}" /></th>
			<th>&nbsp;</th>
		</tr>
		</tbody>
		{foreach from=$feature.variants item="var" name="fe_f"}
		{assign var="num" value=$smarty.foreach.fe_f.iteration}
		<tbody class="hover" id="box_feature_variants_{$var.variant_id}">
		<tr class="cm-first-sibling {cycle values="table-row, "}">
			<td>
				<input type="hidden" name="feature_data[variants][{$num}][variant_id]" value="{$var.variant_id}">
				<input type="text" name="feature_data[variants][{$num}][position]" value="{$var.position}" size="4" class="input-text-short" /></td>
			<td>
				<input type="text" name="feature_data[variants][{$num}][variant]" value="{$var.variant}" class="input-text-large cm-feature-value {if $feature.feature_type == "N"}cm-value-integer{/if}" /></td>
			<td class="cm-extended-feature {if $feature.feature_type != "E"}hidden{/if}">
				<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_feature_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-features-{$id}" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_feature_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-features-{$id}" /><a id="sw_extra_feature_{$id}_{$num}" class="cm-combination-features-{$id}">{$lang.extra}</a>
			</td>
			<td class="right nowrap">
				{include file="buttons/multiple_buttons.tpl" item_id="feature_variants_`$var.variant_id`" tag_level="3" only_delete="Y"}
			</td>
		</tr>
		<tr class="hidden" id="extra_feature_{$id}_{$num}">
			<td colspan="4">

				<div class="form-field">
					<label for="elm_image_{$id}_{$num}">{$lang.image}</label>
					{include file="common_templates/attach_images.tpl" image_name="variant_image" image_key=$num hide_titles=true no_detailed=true image_object_type="feature_variant" image_type="V" image_pair=$var.image_pair prefix=$id}
				</div>

				<div class="form-field">
					<label for="elm_description_{$id}_{$num}">{$lang.description}</label>
					<textarea id="elm_description_{$id}_{$num}" name="feature_data[variants][{$num}][description]" cols="55" rows="8" class="input-textarea-long">{$var.description}</textarea>
					{include file="common_templates/wysiwyg.tpl" id="elm_description_`$id`_`$num`"}
				</div>

				<div class="form-field">
					<label for="elm_page_title_{$id}_{$num}">{$lang.page_title}:</label>
					<input type="text" name="feature_data[variants][{$num}][page_title]" id="elm_page_title_{$id}_{$num}" size="55" value="{$var.page_title}" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_url_{$id}_{$num}">{$lang.url}:</label>
					<input type="text" name="feature_data[variants][{$num}][url]" id="elm_url_{$id}_{$num}" size="55" value="{$var.url}" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_meta_description_{$id}_{$num}">{$lang.meta_description}:</label>
					<textarea name="feature_data[variants][{$num}][meta_description]" id="elm_meta_description_{$id}_{$num}" cols="55" rows="2" class="input-textarea-long">{$var.meta_description}</textarea>
				</div>

				<div class="form-field">
					<label for="elm_meta_keywords_{$id}_{$num}">{$lang.meta_keywords}:</label>
					<textarea name="feature_data[variants][{$num}][meta_keywords]" id="elm_meta_keywords_{$id}_{$num}" cols="55" rows="2" class="input-textarea-long">{$var.meta_keywords}</textarea>
				</div>

				{hook name="product_features:extended_feature"}{/hook}
			</td>
		</tr>
		</tbody>
		{/foreach}

		{math equation="x + 1" assign="num" x=$num|default:0}
		<tbody class="hover" id="box_add_variants_for_existing_{$id}">
		<tr>
			<td>
				<input type="text" name="feature_data[variants][{$num}][position]" value="" size="4" class="input-text-short" /></td>
			<td>
				<input type="text" name="feature_data[variants][{$num}][variant]" value="" class="input-text-large cm-feature-value {if $feature.feature_type == "N"}cm-value-integer{/if}" /></td>
			<td class="cm-extended-feature {if $feature.feature_type != "E"}hidden{/if}">
				<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_feature_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-features-{$id}" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_feature_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-features-{$id}" /><a id="sw_extra_feature_{$id}_{$num}" class="cm-combination-features-{$id}">{$lang.extra}</a>
			</td>
			<td class="right">
				{include file="buttons/multiple_buttons.tpl" item_id="add_variants_for_existing_`$id`" tag_level=2}</td>
		</tr>
		<tr class="hidden" id="extra_feature_{$id}_{$num}">
			<td colspan="4">

				<div class="form-field">
					<label for="elm_image_{$id}_{$num}">{$lang.image}</label>
					{include file="common_templates/attach_images.tpl" image_name="variant_image" image_key=$num hide_titles=true no_detailed=true image_object_type="feature_variant" image_type="V" image_pair="" prefix=$id}
				</div>

				<div class="form-field">
					<label for="elm_description_{$id}_{$num}">{$lang.description}</label>
					<textarea id="elm_description_{$id}_{$num}" name="feature_data[variants][{$num}][description]" cols="55" rows="8" class="input-textarea-long"></textarea>
					{include file="common_templates/wysiwyg.tpl" id="elm_description_`$id`_`$num`"}
				</div>

				<div class="form-field">
					<label for="elm_page_title_{$id}_{$num}">{$lang.page_title}:</label>
					<input type="text" name="feature_data[variants][{$num}][page_title]" id="elm_page_title_{$id}_{$num}" size="55" value="" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_url_{$id}_{$num}">{$lang.url}:</label>
					<input type="text" name="feature_data[variants][{$num}][url]" id="elm_url_{$id}_{$num}" size="55" value="" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_meta_description_{$id}_{$num}">{$lang.meta_description}:</label>
					<textarea name="feature_data[variants][{$num}][meta_description]" id="elm_meta_description_{$id}_{$num}" cols="55" rows="2" class="input-textarea-long"></textarea>
				</div>

				<div class="form-field">
					<label for="elm_meta_keywords_{$id}_{$num}">{$lang.meta_keywords}:</label>
					<textarea name="feature_data[variants][{$num}][meta_keywords]" id="elm_meta_keywords_{$id}_{$num}" cols="55" rows="2" class="input-textarea-long"></textarea>
				</div>

				{hook name="product_features:extended_feature"}{/hook}
			</td>
		</tr>
		</tbody>
		</table>
		<!--content_tab_variants_{$id}--></div>

		{if !$feature.parent_id}
		<div class="hidden" id="content_tab_categories_{$id}">
		{if $feature.categories_path}
			{assign var="items" value=","|explode:$feature.categories_path}
		{/if}
		{include file="pickers/categories_picker.tpl" multiple=true input_name="feature_data[categories_path]" item_ids=$items data_id="category_ids_`$id`" no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.categories}

		<!--content_tab_categories_{$id}--></div>
		{/if}

	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[product_features.update]" cancel_action="close"}
</div>


</form>

<!--content_group{$id}--></div>