{* $Id: manage.tpl 8919 2010-02-24 14:47:56Z lexa $ *}

{include file="views/profiles/components/profiles_scripts.tpl"}

{capture name="mainbox"}

{*include file="views/profiles/components/companies_search_form.tpl" dispatch="companies.manage"*}

{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
{$lang.text_list_of_vendors}
{assign var="lang_add_vendor_supplier" value=$lang.add_vendor}
{assign var="lang_vendors_suppliers" value=$lang.vendors}
{assign var="lang_view_vendor_supplier_products" value=$lang.view_vendor_products}
{else}
{$lang.text_list_of_suppliers}
{assign var="lang_add_vendor_supplier" value=$lang.add_supplier}
{assign var="lang_vendors_suppliers" value=$lang.suppliers}
{assign var="lang_view_vendor_supplier_products" value=$lang.view_supplier_products}
{/if}

<form action="{$index_script}" method="post" name="userlist_form" id="userlist_form">
<input type="hidden" name="fake" value="1" />

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "id"} sort-link-{$search.sort_order}{/if}" href="{$c_url}&amp;sort_by=id&amp;sort_order={$search.sort_order}" rev="pagination_contents">{$lang.id}</a></th>
	<th width="25%"><a class="{$ajax_class}{if $search.sort_by == "company"} sort-link-{$search.sort_order}{/if}" href="{$c_url}&amp;sort_by=company&amp;sort_order={$search.sort_order}" rev="pagination_contents">{$lang.name}</a></th>
	<th width="25%"><a class="{$ajax_class}{if $search.sort_by == "email"} sort-link-{$search.sort_order}{/if}" href="{$c_url}&amp;sort_by=email&amp;sort_order={$search.sort_order}" rev="pagination_contents">{$lang.email}</a></th>
	<th width="20%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{$c_url}&amp;sort_by=date&amp;sort_order={$search.sort_order}" rev="pagination_contents">{$lang.registered}</a></th>
	<th width="5%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{$c_url}&amp;sort_by=status&amp;sort_order={$search.sort_order}" rev="pagination_contents">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$companies item=company}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="company_ids[]" value="{$company.company_id}" class="checkbox cm-item" /></td>
	<td><a href="{"companies.update?company_id=`$company.company_id`"|fn_url}">&nbsp;<strong>{$company.company_id}</strong>&nbsp;</a></td>
	<td><a href="{"companies.update?company_id=`$company.company_id`"|fn_url}">{$company.company}</a></td>
	<td width="25%"><a href="mailto:{$company.email}">{$company.email}</a></td>
	<td>{$company.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$company.company_id status=$company.status object_id_name="company_id" table="companies" hide_for_vendor="COMPANY_ID"|defined}{*notify=true notify_text=$lang.notify_vendor*} 
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		{hook name="companies:list_extra_links"}
			{assign var="return_current_url" value=$config.current_url|escape:url}
			<li><a class="cm-confirm" href="{"companies.delete?company_id=`$company.company_id`&amp;redirect_url=`$return_current_url`"|fn_url}">{$lang.delete}</a></li>
			<li><a href="{"products.manage?company_id=`$company.company_id`"|fn_url}">{$lang_view_vendor_supplier_products}</a></li>
		{/hook}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$company.company_id tools_list=$smarty.capture.tools_items href="companies.update?company_id=`$company.company_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="9"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if $companies}
	{include file="common_templates/table_tools.tpl" href="#companies"}
{/if}

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $companies}
	<div class="float-left">
		{*capture name="tools_list"}
		<ul>
			<li><a class="cm-process-items" name="dispatch[profiles.export_range]" rev="userlist_form">{$lang.export_selected}</a></li>
		</ul>
		{/capture*}
		{include file="buttons/button.tpl" but_text=$lang.delete_selected but_name="dispatch[companies.m_delete]" but_meta="cm-confirm cm-process-items" but_role="button_main"}
		{*include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action*}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="companies.add" prefix="bottom" hide_tools=true link_text=$lang_add_vendor_supplier}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="companies.add" prefix="top" hide_tools=true link_text=$lang_add_vendor_supplier}
{/capture}

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang_vendors_suppliers content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools}
