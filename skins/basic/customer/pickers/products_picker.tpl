{* $Id: products_picker.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

{assign var="view_mode" value=$view_mode|default:"mixed"}
{if !$display}
	{assign var="display" value="options"}
{/if}

{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}

{if $view_mode != "button"}
{if $type == "table"}
	<p id="{$data_id}_no_item" class="no-items{if $item_ids} hidden{/if}">{$no_item_text|default:$lang.no_items}</p>

	<table id="{$data_id}" class="table{if !$item_ids} hidden{/if} cm-picker-options" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th width="80%">{$lang.name}</th>
		<th>{$lang.quantity}</th>
	</tr>
	{include file="pickers/js_product.tpl" clone=true options="`$ldelim`options`$rdelim`" root_id=$data_id product="`$ldelim`product`$rdelim`" delete_id="`$ldelim`delete_id`$rdelim`" amount=1 amount_input="text" input_name="`$input_name`[`$ldelim`product_id`$rdelim`]"}
	{if $item_ids}
		{foreach from=$item_ids item="product" key="product_id"}
			{capture name="product_options"}
				{assign var="prod_opts" value=$product.product_id|fn_get_product_options}
				{if $prod_opts && !$product.product_options}
					<strong>{$lang.options}: </strong>&nbsp;{$lang.any_option_combinations}
				{else}
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
				{assign var="product_name" value=$product.product_id|fn_get_product_name|escape|default:$lang.deleted_product}
			{/if}
			{include file="pickers/js_product.tpl" options=$smarty.capture.product_options root_id=$data_id product=$product_name delete_id=$product_id product_id=$product.product_id amount=$product.amount amount_input="text" input_name="`$input_name`[`$product.product_id`]" options_array=$product.product_options}
		{/foreach}
	{/if}
	</table>
{/if}
{/if}

{if $view_mode != "list"}

	{assign var="but_text" value=$but_text|default:$lang.add_products}
	<p>{include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_text=$but_text but_onclick="jQuery.show_picker('picker_`$data_id`', this.id);" but_role="text"}</p>

	{capture name="picker_content"}
		{capture name="iframe_url"}{$index_script}?dispatch=products.picker{if $display}&amp;display={$display}{/if}{if $holder_name}&amp;holder_name={$holder_name}{/if}{if $extra_var}&amp;extra={$extra_var|escape:url}{/if}{/capture}
		<div class="cm-picker-data-container" id="iframe_container_{$data_id}"></div>
		<div class="buttons-container">
			{if !$extra_var}
			<span class="submit-button">
			<input type="button" onclick="jQuery.submit_picker('#iframe_{$data_id}', '#add_item_close')" value="{$lang.add_products_and_close}" />
			</span>
			{/if}
			<span class="submit-button cm-button-main">
			<input type="button" onclick="jQuery.submit_picker('#iframe_{$data_id}', '#add_item')" value="{$lang.add_products}" />
			</span>
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