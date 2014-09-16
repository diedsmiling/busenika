{* $Id: event_products.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{script src="js/exceptions.js"}

<div class="cm-hide-save-button" id="content_products">

{capture name="local_notes"}
	<p>{$lang.text_gr_desired_products}</p>
{/capture}

{include file="common_templates/subheader.tpl" title=$lang.defined_desired_products notes=$smarty.capture.local_notes notes_id="desired_products"}

<form action="{""|fn_url}" method="post" name="event_products_form" >
<input type="hidden" name="event_id" value="{$event_id}" />
<input type="hidden" name="selected_section" value="products" />

{include file="common_templates/pagination.tpl"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.product}</th>
	<th>{$lang.price}</th>
	<th>{$lang.quantity}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$event_data.products item="cp" key="key"}
{cycle values="class=\"table-row\", " assign="class"}
<tr {$class}>
	<td class="center">
		<input type="checkbox" name="event_product_ids[]" value="{$key}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"products.update?product_id=`$cp.product_id`"|fn_url}">{$cp.product}</a>
		{if $cp.product_options}
			<span class="cm-reload-{$key}" id="event_options_update_{$key}">
				<input type="hidden" name="appearance[events]" value="1">
				<input type="hidden" name="appearance[event_id]" value="{$event_id}">
			<!--event_options_update_{$key}--></span>
		<p>{include file="views/products/components/select_product_options.tpl" product_options=$cp.product_options name="event_products" id=$key product=$cp}</p>
		{/if}
	</td>
	<td>
		{include file="common_templates/price.tpl" value=$cp.price}</td>
	<td class="center">
		<input type="hidden" name="event_products[{$key}][product_id]" value="{$cp.product_id}" />
		<input class="input-text" type="text" size="3" name="event_products[{$key}][amount]" value="{$cp.amount}" /></td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"events.delete_product?product_id=`$key`&amp;event_id=`$event_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$cp.product_id tools_list=$smarty.capture.tools_items href="products.update?product_id=`$cp.product_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $event_data.products}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[events.delete_products]" class="cm-process-items cm-confirm" rev="event_products_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[events.update_products]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="pickers/products_picker.tpl" display="options_amount" extra_var="dispatch=events.add_products&event_id=`$event_id`" data_id="events"}
	</div>
</div>


</form>

</div>
