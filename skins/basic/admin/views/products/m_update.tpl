{* $Id: m_update.tpl 10598 2010-09-03 11:59:33Z 2tl $ *}

<script type="text/javascript">
//<![CDATA[

	var images_dir = '{$images_dir|escape:javascript}';
	{literal}
	function fn_sw_elements(elm, click_elm)
	{
		var cl;
		elms = document.forms['override_form'].elements;

		for (i=0;i<elms.length;i++) {
			if (elms[i].id.indexOf(elm) == 0) {
				elms[i].disabled = !elms[i].disabled;
				if ($(elms[i]).is(':radio') || $(elms[i]).is(':checkbox') || $(elms[i]).is('select')) {
					cl = 'elm-disabled';
				} else {
					cl = 'input-text-disabled';
				}

				if (elms[i].disabled) {
					$(elms[i]).addClass(cl);
				} else {
					$(elms[i]).removeClass(cl);
				}
			}
		}

		if (click_elm) {
			var ids_elm = $('.cm-picker-value', $(click_elm).parents('tr:first'));
			if (ids_elm.length) {
				if (ids_elm.is(':disabled')) {
					ids_elm.removeAttr('disabled');
				} else {
					ids_elm.attr('disabled', 'disabled');
				}
			}
		}
	}

	function fn_generate_scroller()
	{
		elm_orig = document.getElementById('scrolled_div');
		elm_scroller = document.getElementById('scrolled_div_top');
		elm_scroller.innerHTML = '<img src="' + images_dir + '/spacer.gif" width="'+ elm_orig.scrollWidth +'" height="1" />';
		elm_scroller.style.height = '25px';
		elm_scroller.onscroll = function(){document.getElementById('scrolled_div').scrollLeft = document.getElementById('scrolled_div_top').scrollLeft}
		elm_orig.onscroll = function(){document.getElementById('scrolled_div_top').scrollLeft = document.getElementById('scrolled_div').scrollLeft}
	}
	{/literal}

	$(document).ready( function(){ldelim}
		fn_generate_scroller();
	{rdelim});
//]]>
</script>

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{assign var="all_categories_list" value=0|fn_get_plain_categories_tree:false}
{capture name="mainbox"}

{capture name="extra_tools"}
	{include file="buttons/button.tpl" but_text=$lang.override_product_data but_onclick="$('#override_box').toggle()" but_role="tool"}
{/capture}

<div id="override_box" class="hidden">

<form action="{""|fn_url}" method="post" name="override_form" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="redirect_url" value="{"products.m_update"|fn_url}" />

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table-fixed">
<tr>
	<td width="100%">
		<div class="scroll-x">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			{foreach from=$filled_groups item=v}
			<th>&nbsp;</th>
			{/foreach}
			{foreach from=$field_names item="field_name" key="field_key"}
			{if $field_key == "company_id" && $smarty.const.PRODUCT_TYPE == "PROFESSIONAL" && $settings.Suppliers.enable_suppliers == "Y"}
			<th>{$lang.supplier}</th>
			{else}
			<th>{if $field_name|is_array}{$field_key|fn_get_lang_var}{else}{$field_name}{/if}</th>
			{/if}
			{/foreach}
		</tr>
		<tr {cycle values="class=\"table-row\", "}>
			{foreach from=$filled_groups item=v key=type}
			<td valign="top">
			{if $type != "L" || $type == "L" && $localizations}
				<table cellpadding="0" cellspacing="0">
				{foreach from=$field_groups.$type item=name key=field}
				{if $v.$field}
				<tr>
					<td valign="top" class="nowrap {if $field == "product"}strong{/if}"><input type="checkbox" name="" value="Y" onclick="fn_sw_elements('field_{$field}__'{if $type == "E"}, this{/if});" />{$v.$field}:&nbsp;</td>
					<td valign="top">
						{if $type == "A"}
						<input id="field_{$field}__" type="text" value="" class="input-text input-text-disabled" name="override_{$name}[{$field}]" disabled="disabled" />
						{elseif $type == "B"}
						<input id="field_{$field}__" type="text" value="" class="input-text input-text-disabled" size="3" name="override_{$name}[{$field}]" disabled="disabled" />
						{elseif $type == "C"}
						<input id="field_{$field}__h" type="hidden" name="override_{$name}[{$field}]" value="N" disabled="disabled" />
						<input id="field_{$field}__" type="checkbox" class="elm-disabled" name="override_{$name}[{$field}]" value="Y" disabled="disabled" />
						{elseif $type == "D"}
						<textarea id="field_{$field}__" class="input-text input-text-disabled" name="override_{$name}[{$field}]" rows="3" cols="40" disabled="disabled"></textarea>
						{elseif $type == "S"}
						<select id="field_{$field}__" name="override_{$name.name}[{$field}]" class="elm-disabled" disabled="disabled">
						{foreach from=$name.variants key=v_id item=v_name}
						<option value="{$v_id}">{$lang.$v_name}</option>
						{/foreach}
						</select>
						{elseif $type == "T"}
							<div class="correct-picker-but">
							{if $field == "timestamp"}
							{include file="common_templates/calendar.tpl" date_id="field_`$field`__date" date_name="override_$name[$field]" date_val=$smarty.const.TIME start_year=$settings.Company.company_start_year extra=" disabled=\"disabled\"" date_meta="input-text-disabled"}
							{elseif $field == "avail_since"}
							{include file="common_templates/calendar.tpl" date_id="field_`$field`__date" date_name="override_$name[$field]" date_val=$smarty.const.TIME start_year=$settings.Company.company_start_year extra=" disabled=\"disabled\"" date_meta="input-text-disabled"}
							{/if}
							</div>
						{elseif $type == "L"}
							{include file="views/localizations/components/select.tpl" no_div=true disabled=true id="field_`$field`__" data_name="override_products_data[localization]"}
						{elseif $type == "E"} {* Categories *}
						<div class="clear">
							<div class="correct-picker-but">
							{if $field == "add_categories"}
								{include file="pickers/categories_picker.tpl" input_name="override_$name[$field]" item_ids=$product.$field multiple=true single_line=true extra="disabled=\"disabled\""}
							{else}
								{include file="pickers/categories_picker.tpl" data_id="main_category" input_name="override_$name[$field]" item_ids=$product.$field hide_link=true hide_delete_button=true extra="id=\"field_`$field`__\" disabled=\"disabled\"" extra_class=" input-text-disabled"}
							{/if}
							</div>
						{elseif $type == "W"} {* Product details layout *}
							<select id="field_{$field}__" name="override_{$name}[{$field}]" class="elm-disabled" disabled="disabled">
							{foreach from=$product_data.product_id|fn_get_product_details_views key="layout" item="item"}
								<option value="{$layout}">{$item}</option>
							{/foreach}
							</select>
						{/if}
					</td>
				</tr>
				{/if}
				{/foreach}
				</table>
			{/if}
			</td>
			{/foreach}


			{foreach from=$field_names key="field" item=v}
			<td valign="top">
			{if $field != "localization" || $field == "localization" && $localizations}
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td valign="top">{if $field != "main_pair" && $field != "features"}<input type="checkbox" name="" value="Y" onclick="fn_sw_elements('field_{$field}__');" />{else}&nbsp;{/if}</td>
					<td valign="top">
					{if $field == "main_category"}
					<select	id="field_{$field}__" name="override_products_categories[M]" class="elm-disabled" disabled="disabled">
						{foreach from=$all_categories_list item="cat"}
						<option	value="{$cat.category_id}">{$cat.category|escape|indent:$cat.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
						{/foreach}
					</select>
					{elseif $field == "main_pair"}
						<table width="320">
						<tr>
							<td>{include file="common_templates/attach_images.tpl" image_name="product_main" image_object_type="product" image_type="M" no_thumbnail=true}</td>
						</tr>
						</table>
					{elseif $field == "tracking"}
						<select	id="field_{$field}__" name="override_products_data[{$field}]" class="elm-disabled" disabled="disabled">
							<option value="O">{$lang.track_with_options}</option>
							<option value="B">{$lang.track_without_options}</option>
							<option value="D">{$lang.dont_track}</option>
						</select>
					{elseif $field == "zero_price_action"}
						<select id="field_{$field}__" name="override_products_data[{$field}]" class="elm-disabled" disabled="disabled">
							<option value="R">{$lang.zpa_refuse}</option>
							<option value="P">{$lang.zpa_permit}</option>
							<option value="A">{$lang.zpa_ask_price}</option>
						</select>
					{elseif $field == "taxes"}
						<input id="field_{$field}__h" type="hidden" name="override_products_data[tax_ids]" value="" disabled="disabled" />
						{foreach from=$taxes item="tax"}
						<div class="select-field nowrap no-padding">
							<input type="checkbox" name="override_products_data[tax_ids][{$tax.tax_id}]" id="field_{$field}__{$tax.tax_id}" class="checkbox" value="{$tax.tax_id}" disabled="disabled" />
							<label for="field_{$field}__{$tax.tax_id}">{$tax.tax}</label>
						</div>
						{/foreach}
					{elseif $field == "features"}
						{if $all_product_features}
						<table cellpadding="1" cellspacing="1" border="0" width="100%">
						{foreach from=$all_product_features item="pf"}
						{if $pf.feature_type !== "G"}
						<tr>
							<td><input type="checkbox" onclick="fn_sw_elements('field_{$field}__{$pf.feature_id}_');{if $pf.feature_type == "C"} $('#field_{$field}__{$pf.feature_id}_copy').attr('disabled', !this.value);{/if}" />&nbsp;{$pf.description}:&nbsp;</td>
							<td>
								{include file="views/products/components/products_m_update_feature.tpl" feature=$pf data_name="override_products_data" over=true}
							</td>
						</tr>
						{/if}
						{/foreach}
						{foreach from=$all_product_features item="pf"}
						{if $pf.subfeatures}
						<tr>
							<td colspan="2"><strong>{$pf.description}</strong></td>
						</tr>
						{foreach from=$pf.subfeatures item="subfeature"}
						<tr>
							<td width="100%" class="nowrap"><input type="checkbox" onclick="fn_sw_elements('field_{$field}__{$subfeature.feature_id}_');{if $subfeature.feature_type == "C"} fn_sw_elements('field_{$field}__{$subfeature.feature_id}_copy');{/if}" />&nbsp;{$subfeature.description}:</td>
							<td>
								{include file="views/products/components/products_m_update_feature.tpl" feature=$subfeature data_name="override_products_data" over=true}
							</td>
						</tr>
						{/foreach}
						{/if}
						{/foreach}
						</table>
						{/if}
					{elseif $field == "timestamp"}
						<div class="correct-picker-but">
						{include file="common_templates/calendar.tpl" date_id="field_`$field`" date_name="override_products_data[`$field`]" date_val=$smarty.const.TIME extra=" disabled=\"disabled\"" start_year=$settings.Company.company_start_year}
						</div>
					{elseif $field == "localization"}
						{include file="views/localizations/components/select.tpl" no_div=true data_name="products_data[`$product.product_id`][localization]" data_from=$product.localization}
					{elseif $field == "usergroup_ids"}
						{include file="common_templates/select_usergroups.tpl" id="field_`$field`_" name="override_products_data[`$field`]" usergroups="C"|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids="" input_extra=" disabled=\"disabled\"" list_mode=true}
					{elseif $field == "company_id" && $smarty.const.PRODUCT_TYPE == "PROFESSIONAL" && $settings.Suppliers.enable_suppliers == "Y"}
						{include file="views/products/components/products_m_update_supplier.tpl" override_box="Y"}
					{else}
						{hook name="products:update_fields"}
							{hook name="products:update_fields_inner"}
								<input id="field_{$field}__" type="text" value="" class="input-text input-text-disabled" name="override_products_data[{$field}]" disabled="disabled" />
							{/hook}
						{/hook}
					{/if}
					</td>
				</tr>
				</table>
			{/if}
			</td>
			{/foreach}
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.apply but_name="dispatch[products.m_override]" but_role="button_main"}
</div>

</form>
</div>
{* ================================ *}

<form action="{""|fn_url}" method="post" name="products_m_update_form" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="redirect_url" value="{"products.m_update"|fn_url}" />

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table-fixed">
<tr>
	<td width="100%">
		<div id="scrolled_div_top" class="scroll-x">&nbsp;</div>
		<div id="scrolled_div" class="scroll-x">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			{foreach from=$filled_groups item=v}
			<th>&nbsp;</th>
			{/foreach}
			{foreach from=$field_names item="field_name" key=field_key}
			{if $field_key == "company_id" && $smarty.const.PRODUCT_TYPE == "PROFESSIONAL" && $settings.Suppliers.enable_suppliers == "Y"}
			<th>{$lang.supplier}</th>
			{else}
			<th>{if $field_name|is_array}{$field_key|fn_get_lang_var}{else}{$field_name}{/if}</th>
			{/if}
			{/foreach}
		</tr>
		{foreach from=$products_data item="product"}
		<tr {cycle values="class=\"table-row\", "}>
			{foreach from=$filled_groups item=v key=type}
			<td valign="top">
			{if $type != "L" || $type == "L" && $localizations}
				<table cellpadding="0" cellspacing="0" border="0">
				{foreach from=$field_groups.$type item=name key=field}
				{if $v.$field}
				<tr>
					<td valign="top" class="nowrap {if $field == "product"}strong{/if}">{$v.$field}:&nbsp;</td>
					<td valign="top">
						{if $type == "A"}
							<input type="text" value="{$product.$field}" class="input-text" name="{$name}[{$product.product_id}][{$field}]" />
						{elseif $type == "B"}
							<input type="text" value="{$product.$field|default:0}" class="input-text" size="3" name="{$name}[{$product.product_id}][{$field}]" />
						{elseif $type == "C"}
							<input type="hidden" name="{$name}[{$product.product_id}][{$field}]" value="N" />
						<input type="checkbox" name="{$name}[{$product.product_id}][{$field}]" value="Y" {if $product.$field == "Y"}checked="checked"{/if} />
						{elseif $type == "D"}
							<textarea class="input-text" name="{$name}[{$product.product_id}][{$field}]" rows="3" cols="40">{$product.$field}</textarea>
						{elseif $type == "S"}
							<select name="{$name.name}[{$product.product_id}][{$field}]">
								{foreach from=$name.variants key=v_id item=v_name}
								<option value="{$v_id}" {if $product.$field == $v_id}selected="selection"{/if}>{$lang.$v_name}</option>
								{/foreach}
							</select>
						{elseif $type == "T"}
							<div class="correct-picker-but">
							{if $field == "timestamp"}
							{include file="common_templates/calendar.tpl" date_id="date_timestamp_holder_`$product.product_id`" date_name="$name[`$product.product_id`][$field]" date_val=$product.$field start_year=$settings.Company.company_start_year}
							{elseif $field == "avail_since"}
							{include file="common_templates/calendar.tpl" date_id="date_avail_holder_`$product.product_id`" date_name="$name[`$product.product_id`][$field]" date_val=$product.$field start_year=$settings.Company.company_start_year}
							{/if}
							</div>
						{elseif $type == "L"}
							{include file="views/localizations/components/select.tpl" no_div=true data_from=$product.localization data_name="products_data[`$product.product_id`][localization]"}
						{elseif $type == "E"} {* Categories *}
							<div class="correct-picker-but">
							<input type="hidden" name="{$name}[{$product.product_id}]{if $field == "add_categories"}[A][]{else}[M]{/if}" value="" />
							{if $field == "add_categories"}
								{include file="pickers/categories_picker.tpl" input_name="$name[`$product.product_id`][add_categories]" item_ids=$product.$field multiple=true single_line=true}
							{else}
								{include file="pickers/categories_picker.tpl" data_id="main_category" input_name="$name[`$product.product_id`][main_category]" item_ids=$product.$field hide_link=true hide_delete_button=true input_id="main_category_id_`$product.product_id`"}
							{/if}
							</div>
						{elseif $type == "W"} {* Product details layout *}
							<select name="{$name}[{$product.product_id}][{$field}]">
							{foreach from=$product_data.product_id|fn_get_product_details_views key="layout" item="item"}
								<option {if $product.details_layout == $layout}selected="selected"{/if} value="{$layout}">{$item}</option>
							{/foreach}
							</select>
						{/if}
					</td>
				</tr>
				{/if}
				{/foreach}
				</table>
			{/if}
			</td>
			{/foreach}

			{foreach from=$field_names key="field" item=v}
			{if $field != "product_id" && ($field != "localization" || $field == "localization" && $localizations)}
			<td valign="top">
					{if $field == "main_pair"}
						<table width="320"><tr><td>{include file="common_templates/attach_images.tpl" image_name="product_main" image_key=$product.product_id image_pair=$product.main_pair image_object_id=$product.product_id image_object_type="product" image_type="M" no_thumbnail=true}</td></tr></table>
					{elseif $field == "tracking"}
						<select	name="products_data[{$product.product_id}][{$field}]">
							<option value="O" {if $product.tracking == "O"}selected="selected"{/if}>{$lang.track_with_options}</option>
							<option value="B" {if $product.tracking == "B"}selected="selected"{/if}>{$lang.track_without_options}</option>
							<option value="D" {if $product.tracking == "D"}selected="selected"{/if}>{$lang.dont_track}</option>
						</select>
					{elseif $field == "zero_price_action"}
						<select name="products_data[{$product.product_id}][{$field}]">
							<option value="R" {if $product.zero_price_action == "R"}selected="selected"{/if}>{$lang.zpa_refuse}</option>
							<option value="P" {if $product.zero_price_action == "P"}selected="selected"{/if}>{$lang.zpa_permit}</option>
							<option value="A" {if $product.zero_price_action == "A"}selected="selected"{/if}>{$lang.zpa_ask_price}</option>
						</select>
					{elseif $field == "taxes"}
						<input type="hidden" name="products_data[{$product.product_id}][tax_ids]" value="" />
						{foreach from=$taxes item="tax"}
						<div class="select-field nowrap">
							<input type="checkbox" name="products_data[{$product.product_id}][tax_ids][{$tax.tax_id}]" id="products_taxes_{$product.product_id}_{$tax.tax_id}" {if $tax.tax_id|in_array:$product.taxes}checked="checked"{/if} class="checkbox" value="{$tax.tax_id}" />
							<label for="products_taxes_{$product.product_id}_{$tax.tax_id}">{$tax.tax}</label>
						</div>
						{/foreach}
					{elseif $field == "features"}
						{if $product.product_features}
						<table cellpadding="1" cellspacing="1" border="0" width="100%">
						{foreach from=$product.product_features item="pf" key="feature_id"}
						{if $pf.feature_type != "G"}
						<tr>
							<td>{$pf.description}:</td>
							<td width="100%">
								{include file="views/products/components/products_m_update_feature.tpl" feature=$pf data_name="products_data[`$product.product_id`]" pid=$product.product_id}
							</td>
						</tr>
						{/if}
						{/foreach}
						{foreach from=$product.product_features item="pf" key="feature_id"}
						{if $pf.feature_type == "G" && $pf.subfeatures}
						<tr>
							<td colspan="2"><strong>{$pf.description}</strong></td>
						</tr>
						{foreach from=$pf.subfeatures item=subfeature}
						<tr>
							<td>{$subfeature.description}:</td>
							<td>{include file="views/products/components/products_m_update_feature.tpl" feature=$subfeature data_name="products_data[`$product.product_id`]" pid=$product.product_id}</td>
						</tr>
						{/foreach}
						{/if}
						{/foreach}
						</table>
						<input type="hidden" name="products_data[{$product.product_id}][features_exist]" value="Y" />
						{/if}
					{elseif $field == "timestamp"}
						<div class="correct-picker-but">
						{include file="common_templates/calendar.tpl" date_id="prod_date" date_name="products_data[`$product.product_id`][$field]" date_val=$product.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
						</div>
					{elseif $field == "localization"}
						{include file="views/localizations/components/select.tpl" no_div=true data_name="products_data[`$product.product_id`][localization]" data_from=$product.localization}
					{elseif $field == "usergroup_ids"}
						{include file="common_templates/select_usergroups.tpl" id="product_ug_`$product.product_id`" name="products_data[`$product.product_id`][`$field`]" usergroups="C"|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$product.usergroup_ids input_extra="" list_mode=true}
					{elseif $field == "company_id" && $smarty.const.PRODUCT_TYPE == "PROFESSIONAL" && $settings.Suppliers.enable_suppliers == "Y"}
						{include file="views/products/components/products_m_update_supplier.tpl"}
					{else}
						{hook name="products:update_fields_extra"}
							{hook name="products:update_fields_inner_extra"}
								<input type="text" value="{$product.$field}" class="input-text" name="products_data[{$product.product_id}][{$field}]" />
							{/hook}
						{/hook}
					{/if}
			</td>
			{/if}
			{/foreach}
		</tr>
		{/foreach}
		</table>
		</div>
	</td>
</tr>
</table>

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[products.m_update]" but_role="button_main"}
</div>

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.update_products content=$smarty.capture.mainbox select_languages=true extra_tools=$smarty.capture.extra_tools}
