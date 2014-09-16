{* $Id: categories_picker.tpl 10006 2010-07-07 14:02:09Z klerik $ *}

{assign var="data_id" value=$data_id|default:"categories_list"}
{math equation="rand()" assign="rnd"}
{assign var="data_id" value="`$data_id`_`$rnd`"}
{assign var="view_mode" value=$view_mode|default:"mixed"}

{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}

{if $item_ids == ""}
	{assign var="item_ids" value=null}
{/if}

{if $item_ids && $multiple && !$item_ids|is_array}
	{assign var="item_ids" value=","|explode:$item_ids}
{/if}

{if !$extra_var && $view_mode != "button"}
	{if $multiple}
	<p id="{$data_id}_no_item" class="no-items{if $item_ids} hidden{/if}">{$no_item_text|default:$lang.no_items}</p>
	{/if}

	<div id="{$data_id}" class="{if $multiple && !$item_ids}hidden{elseif !$multiple}cm-display-radio float-left{/if}">
	<input id="{if $input_id}{$input_id}{else}c{$data_id}_ids{/if}" type="hidden" class="cm-picker-value" name="{$input_name}" value="{if $item_ids|is_array}{","|implode:$item_ids}{else}{$item_ids}{/if}" />
		{if $multiple}
		{include file="pickers/js_category.tpl" category_id="`$ldelim`category_id`$rdelim`" holder=$data_id input_name=$input_name clone=true hide_link=$hide_link hide_delete_button=$hide_delete_button position_field=$positions position="0"}
		{/if}

		{foreach from=$item_ids item="c_id" name="items"}
			{include file="pickers/js_category.tpl" category_id=$c_id holder=$data_id input_name=$input_name hide_link=$hide_link hide_delete_button=$hide_delete_button first_item=$smarty.foreach.items.first position_field=$positions position=$smarty.foreach.items.iteration}
		{foreachelse}
			{if !$multiple}
				{include file="pickers/js_category.tpl" category_id="" holder=$data_id input_name=$input_name hide_link=$hide_link hide_delete_button=$hide_delete_button}
			{/if}
		{/foreach}
	</div>
{/if}

{if $view_mode != "list"}

	{if !$no_container}<div class="{if !$multiple}choose-icon{else}buttons-container{/if}">{/if}
		{if $multiple}
			{assign var="but_text" value=$lang.add_categories}
			{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$but_text but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="add" but_meta="text-button"}
		{else}
			{assign var="but_text" value=$lang.choose}
			<img src="{$images_dir}/icons/icon_choose_object.gif" width="19" height="18" border="0" class="hand" id="opener_picker_{$data_id}" onclick="jQuery.show_picker('picker_{$data_id}', this.id); return false;" alt="{$lang.choose}" title="{$lang.choose}" />
		{/if}
	{if !$no_container}</div>{/if}

	{capture name="picker_content"}
		{capture name="iframe_url"}{$index_script}?dispatch=categories.picker{if !$multiple}&amp;display=radio{/if}{if $extra_var}&amp;extra={$extra_var|escape:url}{/if}{if $default_name}&amp;root={$default_name}{/if}{if $checkbox_name}&amp;checkbox_name={$checkbox_name}{/if}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>
		<div class="buttons-container">
			{if !$multiple}
				<span class="submit-button cm-button-main">
				<input type="button" onclick="jQuery.submit_picker('#iframe_{$data_id}', '#add_item')" value="{$lang.choose}" />
				</span>
			{else}
				{if !$extra_var}
				<span class="submit-button">
				<input type="button" onclick="jQuery.submit_picker('#iframe_{$data_id}', '#add_item_close')" value="{$lang.add_categories_and_close}" />
				</span>
				{/if}
				<span class="submit-button cm-button-main">
				<input type="button" onclick="jQuery.submit_picker('#iframe_{$data_id}', '#add_item')" value="{$lang.add_categories}" />
				</span>
			{/if}
			{$lang.or}&nbsp;&nbsp;&nbsp;<a class="cm-popup-switch cm-cancel-link">{$lang.cancel}</a>
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