{* $Id: products_picker.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

{math equation="rand()" assign="rnd"}
{assign var="data_id" value="`$data_id`_`$rnd`"}
{assign var="view_mode" value=$view_mode|default:"mixed"}
{assign var="start_pos" value=$start_pos|default:0}

{script src="js/picker.js"}

{if $item_ids && !$item_ids|is_array && $type != "table"}
	{assign var="item_ids" value=","|explode:$item_ids}
{/if}

{if $view_mode != "button"}
{if $type == "links"}
	<input type="hidden" id="p{$data_id}_ids" name="{$input_name}" value="{if $item_ids}{","|implode:$item_ids}{/if}" />
	{capture name="products_list"}
	{if $picker_view}<div class="object-container">{/if}
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		{if $positions}<th>{$lang.position_short}</th>{/if}
		<th width="100%">{$lang.name}</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tbody id="{$data_id}"{if !$item_ids} class="hidden"{/if}>
	{include file="pickers/js_product.tpl" clone=true product="`$ldelim`product`$rdelim`" root_id=$data_id delete_id="`$ldelim`delete_id`$rdelim`" type="product" position_field=$positions position="0"}
	{if $item_ids}
	{foreach from=$item_ids item="product" name="items"}
		{include file="pickers/js_product.tpl" product=$product|fn_get_product_name|default:$lang.deleted_product root_id=$data_id delete_id=$product type="product" first_item=$smarty.foreach.items.first position_field=$positions position=$smarty.foreach.items.iteration+$start_pos}
	{/foreach}
	{/if}
	</tbody>
	<tbody id="{$data_id}_no_item"{if $item_ids} class="hidden"{/if}>
	<tr class="no-items">
		<td colspan="{if $positions}4{else}3{/if}"><p>{$no_item_text|default:$lang.no_items}</p></td>
	</tr>
	</tbody>
	</table>
	{if $picker_view}</div>{/if}
	{/capture}
	{if $picker_view}
		{include file="common_templates/popupbox.tpl" id="inner_`$data_id`" link_text=$item_ids|count act="edit" content=$smarty.capture.products_list text="`$lang.editing_defined_products`:" link_class="text-button-edit" picker_meta="cm-bg-close"}{$lang.defined_items}
	{else}
		{$smarty.capture.products_list}
	{/if}

{elseif $type == "table"}

	<table class="table" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th width="80%">{$lang.name}</th>
		<th>{$lang.quantity}</th>
		{hook name="product_picker:table_header"}
		{/hook}
		<th>&nbsp;</th>
	</tr>
	<tbody id="{$data_id}" class="{if !$item_ids}hidden{/if} cm-picker-options">
	{hook name="product_picker:table_rows"}
	{if $item_ids}
	{foreach from=$item_ids item="product" key="product_id"}
		{capture name="product_options"}
			{assign var="prod_opts" value=$product.product_id|fn_get_product_options}
			{if $prod_opts && !$product.product_options}
				<strong>{$lang.options}: </strong>&nbsp;{$lang.any_option_combinations}
			{elseif $product.product_options}
				{if $product.product_options_value}
					{include file="common_templates/options_info.tpl" product_options=$product.product_options_value}
				{else}
					{include file="common_templates/options_info.tpl" product_options=$product.product_options|fn_get_selected_product_options_info}
				{/if}
			{/if}
		{/capture}
		{if $product.product}
			{assign var="product_name" value=$product.product}
		{else}
			{assign var="product_name" value=$product.product_id|fn_get_product_name|default:$lang.deleted_product}
		{/if}
		{include file="pickers/js_product.tpl" product=$product_name root_id=$data_id delete_id=$product_id input_name="`$input_name`[`$product_id`]" amount=$product.amount amount_input="text" type="options" options=$smarty.capture.product_options options_array=$product.product_options product_id=$product.product_id product_info=$product}
	{/foreach}
	{/if}
	{/hook}
	{include file="pickers/js_product.tpl" clone=true product="`$ldelim`product`$rdelim`" root_id=$data_id delete_id="`$ldelim`delete_id`$rdelim`" input_name="`$input_name`[`$ldelim`product_id`$rdelim`]" amount="1" amount_input="text" type="options" options="`$ldelim`options`$rdelim`" product_id=""}
	</tbody>
	<tbody id="{$data_id}_no_item"{if $item_ids} class="hidden"{/if}>
	<tr class="no-items">
		<td colspan="{$colspan|default:"3"}"><p>{$no_item_text|default:$lang.no_items}</p></td>
	</tr>
	</tbody>
	</table>
	{if !$display}
		{assign var="display" value="options"}
	{/if}
{/if}
{/if}

{if $view_mode != "list"}

	{assign var="but_text" value=$but_text|default:$lang.add_products}
	{if !$no_container}<div class="buttons-container">{/if}
		{if $picker_view}[{/if}
		{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$but_text but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="add" but_meta="text-button"}
		{if $picker_view}]{/if}
	{if !$no_container}</div>{/if}

	{capture name="picker_content"}
		
		{capture name="iframe_url"}{$index_script}?dispatch=products.picker{if $display}&amp;display={$display}{/if}{if $holder_name}&amp;holder_name={$holder_name}{/if}{if $extra_var}&amp;extra={$extra_var|escape:url}{/if}{if $checkbox_name}&amp;checkbox_name={$checkbox_name}{/if}{if $aoc}&amp;aoc=1{/if}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>
		<div class="buttons-container">
			{assign var="extra_buttons" value="extra_buttons_$rnd"}
			{if !$extra_var}
				{assign var="_but_text" value=$lang.add_products_and_close}
				{assign var="_act" value="#add_item_close"}
				{capture name=$extra_buttons}
					{include file="buttons/button.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '#add_item')" but_text=$lang.add_products}
				{/capture}
			{else}
				{assign var="_but_text" value=$lang.add_products}
				{assign var="_act" value="#add_item"}
			{/if}
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="jQuery.submit_picker('#iframe_`$data_id`', '`$_act`')" but_text=$_but_text cancel_action="close" extra=$smarty.capture.$extra_buttons}
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