{* $Id: products.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{script src="js/exceptions.js"}

{notes}
{$lang.text_om_checkbox_notice}
{/notes}

{capture name="mainbox"}

{include file="views/order_management/components/orders_header.tpl"}

<form action="{""|fn_url}" method="post" name="om_cart_products" enctype="multipart/form-data">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.product}</th>
	<th>{$lang.price}</th>
	{if $cart.use_discount}
	<th width="10%">{$lang.discount}</th>
	{/if}
	<th class="right">{$lang.quantity}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$cart_products item="cp" key="key"}
{hook name="order_management:items_list_row"}
<tr {if $cp.product_options}class="no-border"{/if}>
	<td class="center">
		<input type="checkbox" name="cart_ids[]" value="{$key}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"products.update?product_id=`$cp.product_id`"|fn_url}">{$cp.product|unescape}</a></td>
	<td class="no-padding">
	{if $cp.exclude_from_calculate}
		{$lang.free}
	{else}
		<table cellpadding="0" cellspacing="0" border="0" class="table-fixed" width="135">
		<col width="35" />
		<col width="100" />
		<tr>
			<td>
			<input type="hidden" name="cart_products[{$key}][stored_price]" value="N" />
			<input type="checkbox" name="cart_products[{$key}][stored_price]" value="Y" {if $cp.stored_price == "Y"}checked="checked"{/if} onclick="$('#db_price_{$key},#manual_price_{$key}').toggle();" class="checkbox" />
			</td>
			<td class="data-block" valign="middle">
			<span {if $cp.stored_price == "Y"}class="hidden"{/if} id="db_price_{$key}">{include file="common_templates/price.tpl" value=$cp.original_price}</span>
			<span {if $cp.stored_price != "Y"}class="hidden"{/if} id="manual_price_{$key}">{$currencies.$primary_currency.symbol}&nbsp;<input type="text" class="input-text" size="5" name="cart_products[{$key}][price]" value="{$cp.base_price}" /></span>
			</td>
		</tr>
		</table>
	{/if}
	</td>
	{if $cart.use_discount}
	<td class="no-padding">
	{if $cp.exclude_from_calculate}
		{include file="common_templates/price.tpl" value=""}
	{else}
		{if $cart.order_id}
		<input type="hidden" name="cart_products[{$key}][stored_discount]" value="Y" />
		{$currencies.$primary_currency.symbol}&nbsp;<input type="text" class="input-text" size="5" name="cart_products[{$key}][discount]" value="{$cp.discount}" />
		{else}
		{include file="common_templates/price.tpl" value=$cp.discount}
		{/if}
	{/if}
	</td>
	{/if}
	<td class="center">
		<input type="hidden" name="cart_products[{$key}][product_id]" value="{$cp.product_id}" />
		{if $cp.exclude_from_calculate}
		<input type="hidden" size="3" name="cart_products[{$key}][amount]" value="{$cp.amount}" />
		{/if}
		<span class="cm-reload-{$key}" id="amount_update_{$key}">
			<input class="input-text" type="text" size="3" name="cart_products[{$key}][amount]" value="{$cp.amount}" {if $cp.exclude_from_calculate}disabled="disabled"{/if} />
		<!--amount_update_{$key}--></span>
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"order_management.delete?cart_id=`$key`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$cp.product_id tools_list=$smarty.capture.tools_items href="products.update?product_id=`$cp.product_id`"}
	</td>
</tr>
{if $cp.product_options}
<tr>
	<td>&nbsp;</td>
	<td colspan="{if $cart.use_discount}5{else}4{/if}">
		<div class="float-left">{include file="views/products/components/select_product_options.tpl" product_options=$cp.product_options name="cart_products" id=$key use_exceptions="Y" product=$cp additional_class="option-item"}</div>
		<div id="warning_{$key}" class="float-left notification-title-e hidden">&nbsp;&nbsp;&nbsp;{$lang.nocombination}</div>

	</td>
</tr>
{/if}
{/hook}
{foreachelse}
<tr class="no-items">
	<td colspan="{if $cart.use_discount}6{else}5{/if}"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{if $cart_products}
<p class="right"><strong>{$lang.subtotal}:</strong>&nbsp;{include file="common_templates/price.tpl" value=$cart.subtotal}</p>
{/if}


<div class="buttons-container center buttons-bg">
	{if $cart_products}
		<div class="float-left">
			{capture name="tools_list"}
			<ul>
				<li><a name="dispatch[order_management.delete]" class="cm-process-items cm-confirm" rev="om_cart_products">{$lang.delete_selected}</a></li>
			</ul>
			{/capture}
			{include file="buttons/save.tpl" but_name="dispatch[order_management.update]" but_role="button_main"}
			{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
		</div>
	{/if}

	<div class="float-right">
		{include file="pickers/products_picker.tpl" display="options_price" extra_var="dispatch=order_management.add" data_id="om" no_container=true}
		
	</div>
	
	{if $cart_products}
		{include file="buttons/button.tpl" but_text=$lang.proceed_to_the_next_step but_name="dispatch[order_management.update.continue]" but_role="big"}
	{/if}
</div>

</form>
{/capture}
{if $cart.order_id == ""}
	{assign var="_title" value=$lang.create_new_order}
{else}
	{assign var="_title" value="`$lang.editing_order`:&nbsp;#`$cart.order_id`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools}
