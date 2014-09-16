{* $Id: pages_picker.tpl 10233 2010-07-28 06:25:33Z angel $ *}

{assign var="data_id" value=$data_id|default:"pages_list"}
{math equation="rand()" assign="rnd"}
{assign var="data_id" value="`$data_id`_`$rnd`"}
{assign var="view_mode" value=$view_mode|default:"mixed"}
{assign var="miltiple" value=$multiple|default:true}
{assign var="start_pos" value=$start_pos|default:0}

{script src="js/picker.js"}

{if ($item_ids) && !$item_ids|is_array}
	{assign var="item_ids" value=","|explode:$item_ids}
{/if}

{if $view_mode != "button"}
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
	<input id="a{$data_id}_ids" type="hidden" class="cm-picker-value" name="{$input_name}" value="{if $item_ids}{","|implode:$item_ids}{/if}" />
	{if $multiple}
			</td>
		</tr>
	{/if}
	{if $multiple}
		{include file="pickers/js_page.tpl" page_id="`$ldelim`page_id`$rdelim`" holder=$data_id input_name=$input_name clone=true hide_link=$hide_link hide_delete_button=$hide_delete_button hide_input=true position_field=$positions position="0"}
	{/if}

	{if $item_ids}
	{foreach name="items" from=$item_ids item="p_id"}
		{include file="pickers/js_page.tpl" page_id=$p_id holder=$data_id input_name=$input_name hide_link=$hide_link hide_delete_button=$hide_delete_button hide_input=true first_item=$smarty.foreach.items.first position_field=$positions position=$smarty.foreach.items.iteration+$start_pos}
	{/foreach}
	{elseif !$multiple}
		{include file="pickers/js_page.tpl" page_id="" holder=$data_id input_name=$input_name hide_link=$hide_link hide_delete_button=$hide_delete_button}
	{/if}

	{if $multiple}
	</tbody>
	<tbody id="{$data_id}_no_item"{if $item_ids} class="hidden"{/if}>
	<tr class="no-items">
		<td colspan="{if $positions}3{else}2{/if}"><p>{$no_item_text|default:$lang.no_items}</p></td>
	</tr>
	</tbody>
	</table>
	{else}
	</div>
	{/if}
{/if}

{if $view_mode != "list"}
	{if !$no_container}<div class="{if !$multiple}choose-icon{else}buttons-container{/if}">{/if}
		{if $multiple}
			{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$add_text|default:$lang.add_pages but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="add" but_meta="text-button"}
		{else}
			<img src="{$images_dir}/icons/icon_choose_object.gif" width="19" height="18" border="0" class="hand" id="opener_picker_{$data_id}" onclick="jQuery.show_picker('picker_{$data_id}', this.id); return false;" alt="{$lang.choose}" title="{$lang.choose}" />
		{/if}
	{if !$no_container}</div>{/if}

	{capture name="picker_content"}
		{capture name="iframe_url"}{$index_script}?dispatch=pages.picker{if !$multiple}&amp;display=radio{/if}{if $extra_var}&amp;extra={$extra_var|escape:url}{/if}{if $checkbox_name}&amp;checkbox_name={$checkbox_name}{/if}{if $default_name}&amp;root={$default_name}{/if}{if $except_id}&amp;except_id={$except_id}{/if}{$extra_url|default:"&amp;get_tree=multi_level"}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>
		<div class="buttons-container">
			{assign var="extra_buttons" value="extra_buttons_$rnd"}
			{if !$multiple}
				{assign var="_but_text" value=$lang.choose}
				{assign var="_act" value="#add_item"}
			{else}
				{if !$extra_var}
					{assign var="_but_text" value=$add_close_text|default:$lang.add_pages_and_close}
					{assign var="_act" value="#add_item_close"}
					{capture name=$extra_buttons}
						{include file="buttons/button.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '#add_item')" but_text=$add_text|default:$lang.add_pages}
					{/capture}
				{else}
					{assign var="_but_text" value=$add_text|default:$lang.add_pages}
					{assign var="_act" value="#add_item"}
				{/if}
			{/if}
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '`$_act`')" but_text=$_but_text extra=$smarty.capture.$extra_buttons cancel_action="close"}
		</div>
	{/capture}
	{include file="pickers/picker_skin.tpl" picker_content=$smarty.capture.picker_content data_id=$data_id but_text=$add_text|default:$lang.add_pages}
	<script type="text/javascript">
	//<![CDATA[
		iframe_urls['{$data_id}'] = '{$smarty.capture.iframe_url|fn_url|escape:"javascript"}';
		{if $extra_var}
		iframe_extra['{$data_id}'] = '{$extra_var|escape:"javascript"}';
		{/if}
	//]]>
	</script>
{/if}