{* $Id: cart_list.tpl 10311 2010-08-03 07:40:13Z alexions $ *}

{capture name="mainbox"}

{capture name="section"}
	{include file="views/cart/components/carts_search_form.tpl"}
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}

{if $settings.Appearance.calendar_date_format == "month_first"}
	{assign var="date_format" value="%m/%d/%Y"}
{else}
	{assign var="date_format" value="%d/%m/%Y"}
{/if}

<form action="{""|fn_url}" method="post" target="" name="carts_list_form">

{include file="common_templates/pagination.tpl" save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="1%" class="center">
		&nbsp;<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_carts" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-carts" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_carts" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-carts" /></th>
	<th width="50%"><a class="{$ajax_class}{if $search.sort_by == "customer"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th width="25%" class="center">{$lang.cart_content}</th>
	{hook name="cart:items_list_header"}
	{/hook}
</tr>
{foreach from=$cart_list item="customer"}
<tr class="table-row">
	<td class="center">
		<input type="checkbox" name="user_ids[]" value="{$customer.user_id}" class="checkbox cm-item" /></td>
	<td>
		<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_user_{$customer.user_id}" class="hand cm-combination-carts" onclick="jQuery.ajaxRequest('{"cart.cart_list?user_id=`$customer.user_id`"|fn_url:'A':'rel':'&'}', {$ldelim}result_ids: 'cart_products_{$customer.user_id}, wishlist_products_{$customer.user_id}', caching: true{$rdelim});" />
		<img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_user_{$customer.user_id}" class="hand hidden cm-combination-carts" />
	</td>
	<td>
		{if $customer.firstname || $customer.lastname}<a href="{"profiles.update?user_id=`$customer.user_id`"|fn_url}" class="underlined">{$customer.lastname} {$customer.firstname}</a>{else}{$lang.unregistered_customer}{/if}
	</td>
	<td>
		{$customer.date|date_format:"`$date_format`"}
	</td>
	<td class="center">{$customer.cart_products|default:"0"} {$lang.product_s}</td>
	{hook name="cart:items_list"}
	{/hook}
</tr>
<tbody id="user_{$customer.user_id}" class="hidden">
<tr>
	<td colspan="2">&nbsp;</td>
	<td valign="top" colspan="2">
		<div id="cart_products_{$customer.user_id}">
		{if $customer.user_id == $sl_user_id}
			{assign var="products" value=$cart_products}
			{assign var="show_price" value=true}
			{if $cart_products}
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
			<tr>
				<th width="100%">{$lang.product}</th>
				<th class="center">{$lang.quantity}</th>
				<th class="right">{$lang.price}</th>
			</tr>
			
			{foreach from=$cart_products item="product" name="products"}
			{hook name="cart:product_row"}
			{if !$product.extra.extra.parent}
			<tr>
				<td>
				{if $product.item_type == "P"}
					{if $product.product}
					<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a>
					{else}
					{$lang.deleted_product}
					{/if}
				{/if}
				{hook name="cart:products_list"}
				{/hook}
				</td>
				<td class="center">{$product.amount}</td>
				<td class="right">{include file="common_templates/price.tpl" value=$product.price span_id="c_`$customer.user_id`_$product.item_id"}</td>
			</tr>
			{/if}
			{/hook}
			{/foreach}
			<tr>
				<td class="right"><strong>{$lang.total}:</strong></td>
				<td class="center"><strong>{$customer.cart_all_products}</strong></td>
				<td class="right"><strong>{include file="common_templates/price.tpl" value=$customer.total span_id="u_$customer.user_id"}</strong></td>
			</tr>
			</table>
			{else}
			&nbsp;
			{/if}
		{else}
			&nbsp;
		{/if}
		<!--cart_products_{$customer.user_id}--></div>
		{if $customer.user_data}
			{assign var="user_data" value=$customer.user_data}
			<div id="user_info_{$customer.user_id}">
				{include file="common_templates/subheader2.tpl" title=$lang.user_info}
				<div class="form-field">
					<label>{$lang.email}</label>
					{$user_data.email}
				</div>
				<div class="form-field">
					<label>{$lang.first_name}</label>
					{$user_data.firstname}
				</div>
				<div class="form-field">
					<label>{$lang.last_name}</label>
					{$user_data.lastname}
				</div>
				
				{include file="common_templates/subheader2.tpl" title=$lang.billing_address}
				<div class="form-field">
					<label>{$lang.first_name}</label>
					{$user_data.b_firstname}
				</div>
				<div class="form-field">
					<label>{$lang.last_name}</label>
					{$user_data.b_lastname}
				</div>
				<div class="form-field">
					<label>{$lang.address}</label>
					{$user_data.b_address}
				</div>
				<div class="form-field">
					<label>{$lang.address_2}</label>
					{$user_data.b_address_2}
				</div>
				<div class="form-field">
					<label>{$lang.city}</label>
					{$user_data.b_city}
				</div>
				<div class="form-field">
					<label>{$lang.state}</label>
					{$user_data.b_state_descr}
				</div>
				<div class="form-field">
					<label>{$lang.country}</label>
					{$user_data.b_country_descr}
				</div>
				<div class="form-field">
					<label>{$lang.zip_postal_code}</label>
					{$user_data.b_zipcode}
				</div>
				
				{include file="common_templates/subheader2.tpl" title=$lang.shipping_address}
				<div class="form-field">
					<label>{$lang.first_name}</label>
					{$user_data.s_firstname}
				</div>
				<div class="form-field">
					<label>{$lang.last_name}</label>
					{$user_data.s_lastname}
				</div>
				<div class="form-field">
					<label>{$lang.address}</label>
					{$user_data.s_address}
				</div>
				<div class="form-field">
					<label>{$lang.address_2}</label>
					{$user_data.s_address_2}
				</div>
				<div class="form-field">
					<label>{$lang.city}</label>
					{$user_data.s_city}
				</div>
				<div class="form-field">
					<label>{$lang.state}</label>
					{$user_data.s_state_descr}
				</div>
				<div class="form-field">
					<label>{$lang.country}</label>
					{$user_data.s_country_descr}
				</div>
				<div class="form-field">
					<label>{$lang.zip_postal_code}</label>
					{$user_data.s_zipcode}
				</div>
			<!--user_info_{$customer.user_id}--></div>
		{/if}
	</td>
	{hook name="cart:items_list_row"}
	{/hook}
</tr>
</tbody>
{foreachelse}
<tr class="no-items">
	<td colspan="6"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>
{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	<div class="float-left cm-skip-confirmation">
		{capture name="tools_list"}
		<ul>
			<li><a class="cm-confirm" name="dispatch[cart.m_all_delete]" rev="carts_list_form">{$lang.delete_all_found}</a></li>
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_text=$lang.delete_selected but_name="dispatch[cart.m_delete]" but_meta="cm-confirm cm-process-items" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
</div>

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.users_carts content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
