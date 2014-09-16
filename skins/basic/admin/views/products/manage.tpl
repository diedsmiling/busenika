z{* $Id: manage.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

{include file="views/products/components/products_search_form.tpl" dispatch="products.manage"}

<form action="{""|fn_url}" method="post" name="manage_products_form">
<input type="hidden" name="category_id" value="{$search.cid}" />

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	{if $search.cid && $search.subcats != "Y"}
	<th><a class="{$ajax_class}{if $search.sort_by == "position"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=position&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.position_short}</a></th>
	{/if}
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "code"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=code&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.code}</a></th>
	<th width="50%"><a class="{$ajax_class}{if $search.sort_by == "product"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=product&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.name}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "price"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=price&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.price} ({$currencies.$primary_currency.symbol})</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "list_price"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=list_price&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.list_price} ({$currencies.$primary_currency.symbol})</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "amount"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.quantity}</a></th>
	<th>{hook name="products:manage_head"}{/hook}</th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$products item=product}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
   		<input type="checkbox" name="product_ids[]" value="{$product.product_id}" class="checkbox cm-item" /></td>
	{if $search.cid && $search.subcats != "Y"}
	<td>
		<input type="text" name="products_data[{$product.product_id}][position]" size="3" value="{$product.position}" class="input-text-short" /></td>
	{/if}
	<td>
		<input type="text" name="products_data[{$product.product_id}][product_code]" size="6" value="{$product.product_code}" class="input-text" /></td>
	<td width="100%">
		<div class="float-left">
				<input type="hidden" name="products_data[{$product.product_id}][product]" value="{$product.product}" />
				<a href="{"products.update?product_id=`$product.product_id`"|fn_url}" {if $product.status == "N"}class="manage-root-item-disabled"{/if}>{$product.product|unescape} {include file="views/companies/components/company_name.tpl" company_id=$product.company_id}</a></div>
		<div class="float-right">
		</div>
	</td>
	<td class="center">
		<input type="text" name="products_data[{$product.product_id}][price]" size="6" value="{$product.price}" class="input-text-medium" /></td>
	<td class="center">
		<input type="text" name="products_data[{$product.product_id}][list_price]" size="6" value="{$product.list_price}" class="input-text-medium" /></td>
	<td class="center">
		{if $product.tracking == "O"}
		{include file="buttons/button.tpl" but_text=$lang.edit but_href="product_options.inventory?product_id=`$product.product_id`" but_role="edit"}
		{else}
		<input type="text" name="products_data[{$product.product_id}][amount]" size="6" value="{$product.amount}" class="input-text-short" />
		{/if}
	</td>
	<td>{hook name="products:manage_body"}{/hook}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$product.product_id status=$product.status hidden=true object_id_name="product_id" table="products"}
	</td>
	<td class="nowrap">
		{if $product.hasimg == "1"}
		<img style="height: 12px; margin-bottom: 2px;" src="/images/picture_icon.png">
		{else}
		<img style="height: 12px; margin-bottom: 2px;" src="/images/picture_icon_desaturated.png">
		{/if}
		
		{if $product.hasimg_detailed == "1"}
		<img src="/images/picture_icon.png">
		{else}
		<img src="/images/picture_icon_desaturated.png">
		{/if}
		
		{capture name="tools_items"}
		{hook name="products:list_extra_links"}
		<li><a class="cm-confirm" href="{"products.delete?product_id=`$product.product_id`"|fn_url}""><img src="/images/cross.png"></a></li>
		{/hook}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$product.product_id tools_list=$smarty.capture.tools_items "}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="{if $search.cid && $search.subcats != "Y"}12{else}11{/if}"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if $products}
	{include file="common_templates/table_tools.tpl" href="#products" visibility="Y"}
{/if}

{include file="common_templates/pagination.tpl"}

{capture name="select_fields_to_edit"}
<div class="object-container">
	<p>{$lang.text_select_fields2edit_note}</p>
	{include file="views/products/components/products_select_fields.tpl"}
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_text=$lang.modify_selected but_name="dispatch[products.store_selection]" cancel_action="close"}
</div>
{/capture}

<div class="buttons-container buttons-bg">
	{if $products}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a class="cm-process-items" name="dispatch[products.m_clone]" rev="manage_products_form">{$lang.clone_selected}</a></li>
			<li><a class="cm-process-items" name="dispatch[products.export_range]" rev="manage_products_form">{$lang.export_selected}</a></li>
			<li><a class="cm-confirm cm-process-items" name="dispatch[products.m_delete]" rev="manage_products_form">{$lang.delete_selected}</a></li>
			<li><a onclick="if ($('input.cm-item[type=checkbox]:checked', $(this).parents('form:first')).length > 0) {$ldelim}jQuery.show_picker('select_fields_to_edit', '', '.object-container');{$rdelim} else {$ldelim}alert(window['lang'].error_no_items_selected);{$rdelim}">{$lang.edit_selected}</a></li>
		</ul>
		{/capture}

		{include file="buttons/save.tpl" but_name="dispatch[products.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}

		{include file="common_templates/popupbox.tpl" id="select_fields_to_edit" text=$lang.select_fields_to_edit content=$smarty.capture.select_fields_to_edit}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="products.add" prefix="bottom" link_text=$lang.add_product hide_tools=true}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="products.add" prefix="top" link_text=$lang.add_product hide_tools=true}
{/capture}

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.products content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools select_languages=true}
