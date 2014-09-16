{* $Id: categories_picker.tpl 10233 2010-07-28 06:25:33Z angel $ *}

{assign var="data_id" value=$data_id|default:"categories_list"}
{math equation="rand()" assign="rnd"}
{assign var="data_id" value="`$data_id`_`$rnd`"}
{assign var="view_mode" value=$view_mode|default:"mixed"}
{assign var="start_pos" value=$start_pos|default:0}

{script src="js/picker.js"}

{if $item_ids == ""}
	{assign var="item_ids" value=null}
{/if}

{if $item_ids && $multiple && !$item_ids|is_array}
	{assign var="item_ids" value=","|explode:$item_ids}
{/if}

{if !$extra_var && $view_mode != "button"}
	{if $multiple}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			{if $positions}<th>{$lang.position_short}</th>{/if}
			<th width="100%">{$lang.name}</th>
			<th>&nbsp;</th>
		</tr>
		<tbody id="{$data_id}"{if !$item_ids} class="hidden"{/if}>
	{else}
		<div id="{$data_id}" class="{if $multiple && !$item_ids}hidden{elseif !$multiple}cm-display-radio float-left{/if}">
	{/if}
	{if $multiple}
		<tr class="hidden">
			<td colspan="{if $positions}3{else}2{/if}">
	{/if}
			<input id="{if $input_id}{$input_id}{else}c{$data_id}_ids{/if}" type="hidden" class="cm-picker-value" name="{$input_name}" value="{if $item_ids|is_array}{","|implode:$item_ids}{else}{$item_ids}{/if}" {$extra} />
	{if $multiple}
			</td>
		</tr>
	{/if}
		{if $multiple}
			{include file="pickers/js_category.tpl" category_id="`$ldelim`category_id`$rdelim`" holder=$data_id input_name=$input_name clone=true hide_link=$hide_link hide_delete_button=$hide_delete_button position_field=$positions position="0"}
		{/if}
		{foreach from=$item_ids item="c_id" name="items"}
			{include file="pickers/js_category.tpl" category_id=$c_id holder=$data_id input_name=$input_name hide_link=$hide_link hide_delete_button=$hide_delete_button first_item=$smarty.foreach.items.first position_field=$positions position=$smarty.foreach.items.iteration+$start_pos}
		{foreachelse}
			{if !$multiple}
				{include file="pickers/js_category.tpl" category_id="" holder=$data_id input_name=$input_name hide_link=$hide_link hide_delete_button=$hide_delete_button}
			{/if}
		{/foreach}
	{if $multiple}
		</tbody>
		<tbody id="{$data_id}_no_item"{if $item_ids} class="hidden"{/if}>
		<tr class="no-items">
			<td colspan="{if $positions}3{else}2{/if}"><p>{$no_item_text|default:$lang.no_items}</p></td>
		</tr>
		</tbody>
	</table>
	{else}</div>{/if}
{/if}

{if $view_mode != "list"}

	{if !"COMPANY_ID"|defined || $smarty.const.CONTROLLER != "companies"}
	{if !$no_container}<div class="{if !$multiple}choose-icon{else}buttons-container{/if}">{/if}
		{if $multiple}
			{assign var="but_text" value=$lang.add_categories}
			{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$but_text but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="add" but_meta="text-button"}
		{else}
			{assign var="but_text" value=$lang.choose}
			<img src="{$images_dir}/icons/icon_choose_object.gif" width="19" height="18" border="0" class="hand" id="opener_picker_{$data_id}" onclick="jQuery.show_picker('picker_{$data_id}', this.id); return false;" alt="{$lang.choose}" title="{$lang.choose}" />
		{/if}
	{if !$no_container}</div>{/if}
	{/if}

	{capture name="picker_content"}
		{capture name="iframe_url"}{$index_script}?dispatch=categories.picker{if !$multiple}&amp;display=radio{/if}{if $extra_var}&amp;extra={$extra_var|escape:url}{/if}{if $default_name}&amp;root={$default_name}{/if}{if $checkbox_name}&amp;checkbox_name={$checkbox_name}{/if}{if $except_id}&amp;except_id={$except_id}{/if}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>
		<div class="buttons-container">
			{assign var="extra_buttons" value="extra_buttons_$rnd"}
			{if !$multiple}
				{assign var="_but_text" value=$lang.choose}
				{assign var="_act" value="#add_item"}
			{else}
				{if !$extra_var}
					{assign var="_but_text" value=$lang.add_categories_and_close}
					{assign var="_act" value="#add_item_close"}
					{capture name=$extra_buttons}
						{include file="buttons/button.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '#add_item')" but_text=$lang.add_categories}
					{/capture}
				{else}
					{assign var="_but_text" value=$lang.add_categories}
					{assign var="_act" value="#add_item"}
				{/if}
			{/if}
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '`$_act`')" but_text=$_but_text extra=$smarty.capture.$extra_buttons cancel_action="close"}
		</div>
	{/capture}
	{include file="pickers/picker_skin.tpl" picker_content=$smarty.capture.picker_content data_id=$data_id but_text=$but_text}

	<script type="text/javascript">
	//<![CDATA[
		iframe_urls['{$data_id}'] = '{$smarty.capture.iframe_url|fn_url|escape:"javascript"}';
		{if $extra_var}
		iframe_extra['{$data_id}'] = '{$extra_var|escape:"javascript"}';
		{/if}
	//]]>
	</script>
{/if}
