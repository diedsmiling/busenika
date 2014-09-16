{* $Id: update.tpl 10397 2010-08-11 13:30:26Z alexions $ *}

{assign var="id" value=$option_id|default:0}

<div id="content_group_product_option_{$id}">

<form action="{""|fn_url}" method="post" name="option_form_{$id}" class="form-highlight cm-disable-empty-files" enctype="multipart/form-data">
<input type="hidden" name="option_id" value="{$option_id}" />
{if $smarty.request.product_id}
{if !$option_data}
<input type="hidden" name="option_data[product_id]" value="{$smarty.request.product_id}" />
{/if}
<input type="hidden" name="product_id" value="{$smarty.request.product_id}" />
{/if}

<div class="object-container">

<div class="tabs cm-j-tabs">
	<ul>
		<li id="tab_option_details_{$id}" class="cm-js cm-active"><a>{$lang.general}</a></li>
		{if $option_data.option_type == "S" || $option_data.option_type == "R" || $option_data.option_type == "C" || !$option_data}
			<li id="tab_option_variants_{$id}" class="cm-js"><a>{$lang.variants}</a></li>
		{/if}
	</ul>
</div>
<div class="cm-tabs-content" id="tabs_content_{$id}">
	<div id="content_tab_option_details_{$id}">
	<fieldset>
		<div class="form-field">
			<label for="name_{$id}" class="cm-required">{$lang.name}:</label>
			<input type="text" name="option_data[option_name]" id="name_{$id}" value="{$option_data.option_name}" class="input-text-large main-input" />
		</div>

		<div class="form-field">
			<label for="position_{$id}">{$lang.position}:</label>
			<input type="text" name="option_data[position]" id="position_{$id}" value="{$option_data.position}" size="3" class="input-text-short" />
		</div>

		<div class="form-field">
			<label for="inventory_{$id}">{$lang.inventory}:</label>
			<input type="hidden" name="option_data[inventory]" value="N" />
			{if "SRC"|strpos:$option_data.option_type !== false}
				<input type="checkbox" name="option_data[inventory]" id="inventory_{$id}" value="Y" {if $option_data.inventory == "Y"}checked="checked"{/if} class="checkbox" />
			{else}
				&nbsp;-&nbsp;
			{/if}
		</div>
		
		{include file="views/companies/components/company_field.tpl" title=$lang.vendor name="option_data[company_id]" id="option_data_`$id`" selected=$option_data.company_id}

		<div class="form-field">
			<label for="option_type_{$id}">{$lang.type}:</label>
			{include file="views/product_options/components/option_types.tpl"  name="option_data[option_type]" value=$option_data.option_type display="select" tag_id="option_type_`$id`" check=true}
		</div>
		
		<div class="form-field">
			<label for="description_{$id}">{$lang.description}:</label>
			<textarea id="description_{$id}" name="option_data[description]" cols="55" rows="8" class="input-textarea-long">{$option_data.description}</textarea>
			{include file="common_templates/wysiwyg.tpl" id="description_`$id`"}
		</div>
		
		<div class="form-field">
			<label for="comment_{$id}">{$lang.comment}:</label>
			<input type="text" name="option_data[comment]" id="comment_{$id}" value="{$option_data.comment}" class="input-text-large" />
			<p class="description">{$lang.comment_hint}</p>
		</div>
		
		<div class="form-field">
			<label for="file_required_{$id}">{$lang.required}:</label>
			<input type="hidden" name="option_data[required]" value="N" /><input type="checkbox" id="file_required_{$id}" name="option_data[required]" value="Y" {if $option_data.required == "Y"}checked="checked"{/if} class="checkbox" />
		</div>
		
		{if !$option_data.option_type || "SRC"|strpos:$option_data.option_type !== false}
			<div class="form-field">
				<label for="file_required_{$id}">{$lang.missing_variants_handling}:</label>
				{if "SRC"|strpos:$option_data.option_type !== false}
					<select name="option_data[missing_variants_handling]">
						<option value="M" {if $option_data.missing_variants_handling == "M"}selected="selected"{/if}>{$lang.display_message}</option>
						<option value="H" {if $option_data.missing_variants_handling == "H"}selected="selected"{/if}>{$lang.hide_option_completely}</option>
					</select>
				{else}
					&nbsp;-&nbsp;
				{/if}
			</div>
		{/if}
		
		<div id="extra_options_{$id}" {if $option_data.option_type != "I" && $option_data.option_type != "T"}class="hidden"{/if}>
			<div class="form-field">
				<label for="regexp_{$id}">{$lang.regexp}:</label>
				<input type="text" name="option_data[regexp]" id="regexp_{$id}" value="{$option_data.regexp|unescape}" class="input-text-large" />
				<p class="description">{$lang.regexp_hint}</p>
			</div>
			
			<div class="form-field">
				<label for="inner_hint_{$id}">{$lang.inner_hint}:</label>
				<input type="text" name="option_data[inner_hint]" id="inner_hint_{$id}" value="{$option_data.inner_hint}" class="input-text-large" />
			</div>
			
			<div class="form-field">
				<label for="incorrect_message_{$id}">{$lang.incorrect_filling_message}:</label>
				<input type="text" name="option_data[incorrect_message]" id="incorrect_message_{$id}" value="{$option_data.incorrect_message}" class="input-text-large" />
			</div>
		</div>
		
		<div id="file_options_{$id}" {if $option_data.option_type != "F"}class="hidden"{/if}>
			<div class="form-field">
				<label for="allowed_extensions_{$id}">{$lang.allowed_extensions}:</label>
				<input type="text" name="option_data[allowed_extensions]" id="allowed_extensions_{$id}" value="{$option_data.allowed_extensions}" class="input-text-large" />
				<p class="description">{$lang.allowed_extensions_hint}</p>
			</div>
			<div class="form-field">
				<label for="max_uploading_file_size_{$id}">{$lang.max_uploading_file_size}:</label>
				<input type="text" name="option_data[max_file_size]" id="max_uploading_file_size_{$id}" value="{$option_data.max_file_size}" class="input-text-large" />
				<p class="description">{$lang.max_uploading_file_size_hint}</p>
			</div>
			<div class="form-field">
				<label for="multiupload_{$id}">{$lang.multiupload}:</label>
				<input type="hidden" name="option_data[multiupload]" value="N" /><input type="checkbox" id="multiupload_{$id}" name="option_data[multiupload]" value="Y" {if $option_data.multiupload == "Y"}checked="checked"{/if} class="checkbox" />
			</div>
		</div>
		
		{hook name="product_options:properties"}
		{/hook}
	</fieldset>
	<!--content_tab_option_details_{$id}--></div>

 	<div class="hidden" id="content_tab_option_variants_{$id}">
 	<fieldset>
		<table cellpadding="0" cellspacing="0" class="table">
		<tbody>
		<tr class="first-sibling">
			<th class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">{$lang.position_short}</th>
			<th class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">{$lang.name}</th>
			<th>{$lang.modifier}&nbsp;/&nbsp;{$lang.type}</th>
			<th>{$lang.weight_modifier}&nbsp;/&nbsp;{$lang.type}</th>
			<th class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">{$lang.status}</th>
			<th><img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st_{$id}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-options-{$id}" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st_{$id}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-options-{$id}" /></th>
			<th class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">&nbsp;</th>
		</tr>
		</tbody>
		{foreach from=$option_data.variants item="vr" name="fe_v"}
		{assign var="num" value=$smarty.foreach.fe_v.iteration}
		<tbody class="hover cm-row-item" id="option_variants_{$id}_{$num}">
		<tr>
			<td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				<input type="text" name="option_data[variants][{$num}][position]" value="{$vr.position}" size="3" class="input-text-short" /></td>
			<td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				<input type="text" name="option_data[variants][{$num}][variant_name]" value="{$vr.variant_name}" class="input-text-medium main-input" /></td>
			<td class="nowrap">
				<input type="text" name="option_data[variants][{$num}][modifier]" value="{$vr.modifier}" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][{$num}][modifier_type]">
					<option value="A" {if $vr.modifier_type == "A"}selected="selected"{/if}>{$currencies.$primary_currency.symbol}</option>
					<option value="P" {if $vr.modifier_type == "P"}selected="selected"{/if}>%</option>
				</select>
			</td>
			<td class="nowrap">
				<input type="text" name="option_data[variants][{$num}][weight_modifier]" value="{$vr.weight_modifier}" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][{$num}][weight_modifier_type]">
					<option value="A" {if $vr.weight_modifier_type == "A"}selected="selected"{/if}>{$settings.General.weight_symbol}</option>
					<option value="P" {if $vr.weight_modifier_type == "P"}selected="selected"{/if}>%</option>
				</select>
			</td>
			<td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				{include file="common_templates/select_status.tpl" input_name="option_data[variants][`$num`][status]" display="select" obj=$vr}</td>
			<td class="nowrap">
				<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_option_variants_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-options-{$id}" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_option_variants_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-options-{$id}" /><a id="sw_extra_option_variants_{$id}_{$num}" class="cm-combination-options-{$id}">{$lang.extra}</a>
				<input type="hidden" name="option_data[variants][{$num}][variant_id]" value="{$vr.variant_id}" />
			 </td>
			 <td class="right cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				{include file="buttons/multiple_buttons.tpl" item_id="option_variants_`$id`_`$num`" tag_level="3" only_delete="Y"}
			</td>
		</tr>
		<tr id="extra_option_variants_{$id}_{$num}" class="cm-ex-op hidden">
			<td colspan="7">
				{hook name="product_options:edit_product_options"}
				<div class="form-field cm-non-cb">
					<label>{$lang.icon}:</label>
					{include file="common_templates/attach_images.tpl" image_name="variant_image" image_key=$num hide_titles=true no_detailed=true image_object_type="variant_image" image_type="V" image_pair=$vr.image_pair prefix=$id}
				</div>				
				{/hook}
				</div>
			</td>
		</tr>
		</tbody>
		{/foreach}

		{math equation="x + 1" assign="num" x=$num|default:0}{assign var="vr" value=""}
		<tbody class="hover cm-row-item {if $option_data.option_type == "C"}hidden{/if}" id="box_add_variant_{$id}">
		<tr>
			<td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				<input type="text" name="option_data[variants][{$num}][position]" value="" size="3" class="input-text-short" /></td>
			<td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				<input type="text" name="option_data[variants][{$num}][variant_name]" value="" class="input-text-medium main-input" /></td>
			<td>
				<input type="text" name="option_data[variants][{$num}][modifier]" value="" size="5" class="input-text" />&nbsp;/
				<select name="option_data[variants][{$num}][modifier_type]">
					<option value="A">{$currencies.$primary_currency.symbol}</option>
					<option value="P">%</option>
				</select>
			</td>
			<td>
				<input type="text" name="option_data[variants][{$num}][weight_modifier]" value="" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][{$num}][weight_modifier_type]">
					<option value="A">{$settings.General.weight_symbol}</option>
					<option value="P">%</option>
				</select>
			</td>
			<td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				{include file="common_templates/select_status.tpl" input_name="option_data[variants][`$num`][status]" display="select"}</td>
			<td>
				<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_option_variants_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-options-{$id}" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_option_variants_{$id}_{$num}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-options-{$id}" /><a id="sw_extra_option_variants_{$id}_{$num}" class="cm-combination-options-{$id}">{$lang.extra}</a>
			</td>
			<td class="right cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
				{include file="buttons/multiple_buttons.tpl" item_id="add_variant_`$id`" tag_level="2"}
			</td>
		</tr>
		<tr id="extra_option_variants_{$id}_{$num}" class="cm-ex-op hidden">
			<td colspan="7">
				{hook name="product_options:edit_product_options"}
				<div class="form-field cm-non-cb">
					<label>{$lang.icon}:</label>
					{include file="common_templates/attach_images.tpl" image_name="variant_image" image_key=$num hide_titles=true no_detailed=true image_object_type="variant_image" image_type="V" prefix=$id}
				</div>
				{/hook}
			</td>
		</tr>
		</tbody>
		</table>
	</fieldset>
	<!--content_tab_option_variants_{$id}--></div>
</div>

</div>

<div class="buttons-container">
	{if $mode == "add"}
		{assign var="_but_text" value=$lang.create}
	{else}
		{assign var="_but_text" value=""}
	{/if}
	{include file="buttons/save_cancel.tpl" but_text=$_but_text but_name="dispatch[product_options.update]" cancel_action="close" extra=""}
</div>

</form>

<!--content_group_product_option_{$id}--></div>