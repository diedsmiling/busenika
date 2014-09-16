{* $Id: manage.tpl 10588 2010-09-03 08:14:26Z 2tl $ *}

{include file="views/profiles/components/profiles_scripts.tpl"}

{capture name="mainbox"}

{include file="views/profiles/components/users_search_form.tpl" dispatch="profiles.manage"}

{if $user_type_description}
{$lang.text_list_of_user_accounts|replace:"[account]":$user_type_description}
{else}
{$lang.text_list_of_all_accounts}
{/if}

<form action="{""|fn_url}" method="post" name="userlist_form" id="userlist_form">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="user_type" value="{$smarty.request.user_type}" />

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}

{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.id}</a></th>
	{if $settings.General.use_email_as_login != "Y"}
	<th width="25%"><a class="{$ajax_class}{if $search.sort_by == "username"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=username&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.username}</a></th>
	{/if}
	<th width="25%"><a class="{$ajax_class}{if $search.sort_by == "name"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=name&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.name}</a></th>
	<th width="25%"><a class="{$ajax_class}{if $search.sort_by == "email"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=email&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.email}</a></th>
	<th width="20%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.registered}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "type"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=type&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.type}</a></th>
	<th width="5%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$users item=user}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="user_ids[]" value="{$user.user_id}" class="checkbox cm-item" /></td>
	<td><a href="{"profiles.update?user_id=`$user.user_id`"|fn_url}">&nbsp;<strong>{$user.user_id}</strong>&nbsp;</a></td>
	{if $settings.General.use_email_as_login != "Y"}
	<td><a href="{"profiles.update?user_id=`$user.user_id`"|fn_url}">{$user.user_login}</a></td>
	{/if}
	<td>{if $user.firstname || $user.lastname}<a href="{"profiles.update?user_id=`$user.user_id`"|fn_url}">{$user.lastname} {$user.firstname}</a>{else}-{/if}{include file="views/companies/components/company_name.tpl" company_id=$user.company_id}</td>
	<td width="25%"><a href="mailto:{$user.email|escape:url}">{$user.email}</a></td>
	<td>{$user.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>{if $user.user_type == "A"}{$lang.administrator}{elseif $user.user_type == "C"}{$lang.customer}{elseif $user.user_type == "P"}{$lang.affiliate}{/if}</td>
	<td>
		<input type="hidden" name="user_types[{$user.user_id}]" value="{$user.user_type}" />
		{if $user.user_id != 1}
			{assign var="u_id" value=$user.user_id}
		{else}
			{assign var="u_id" value=""}
		{/if}
		{include file="common_templates/select_popup.tpl" id=$u_id status=$user.status hidden="" update_controller="profiles" notify=true notify_text=$lang.notify_user}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		{hook name="profiles:list_extra_links"}
			{if $user.user_type == "C"}
				<li><a href="{"orders.manage?user_id=`$user.user_id`"|fn_url}">{$lang.view_all_orders}</a></li>
				<li><a href="{"profiles.act_as_user?user_id=`$user.user_id`"|fn_url}" target="_blank" >{$lang.act_on_behalf}</a></li>
			{/if}
			{assign var="return_current_url" value=$config.current_url|escape:url}
			<li><a class="cm-confirm" href="{"profiles.delete?user_id=`$user.user_id`&amp;redirect_url=`$return_current_url`"|fn_url}">{$lang.delete}</a></li>
		{/hook}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$user.user_id tools_list=$smarty.capture.tools_items href="profiles.update?user_id=`$user.user_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="9"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if $users}
	{include file="common_templates/table_tools.tpl" href="#users"}
{/if}

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $users}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			{hook name="profiles:list_tools"}
				<li><a class="cm-process-items" name="dispatch[profiles.export_range]" rev="userlist_form">{$lang.export_selected}</a></li>
			{/hook}
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_text=$lang.delete_selected but_name="dispatch[profiles.m_delete]" but_meta="cm-confirm cm-process-items" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
	{if $smarty.request.user_type}
		{include file="common_templates/tools.tpl" tool_href="profiles.add?user_type=`$smarty.request.user_type`" prefix="bottom" hide_tools=true link_text=$lang.add_user}
	{else}
		{if $settings.Suppliers.enable_suppliers == "Y"}
			{include file="common_templates/tools.tpl" tool_href="companies.add" prefix="bottom" hide_tools=true link_text=$lang.add_supplier}
		{/if}
		{foreach from=$user_types key="_k" item="_p"}
			{*if "COMPANY_ID"|defined && $_k == 'A'*}
			{include file="common_templates/tools.tpl" tool_href="profiles.add?user_type=`$_k`" prefix="bottom" hide_tools=true link_text=$lang.$_p}
			{*/if*}
		{/foreach}
	{/if}
	</div>
</div>

{capture name="tools"}
	{if $smarty.request.user_type}
	{include file="common_templates/tools.tpl" tool_href="profiles.add?user_type=`$smarty.request.user_type`" prefix="top" hide_tools=true link_text=$lang.add_user}
	{else}
	{if $settings.Suppliers.enable_suppliers == "Y"}
		{include file="common_templates/tools.tpl" tool_href="companies.add" prefix="bottom" hide_tools=true link_text=$lang.add_supplier}
	{/if}
	{foreach from=$user_types key="_k" item="_p"}
		{*if "COMPANY_ID"|defined && $_k == 'A'*}
		{include file="common_templates/tools.tpl" tool_href="profiles.add?user_type=`$_k`" prefix="top" hide_tools=true link_text=$lang.$_p}
		{*/if*}
	{/foreach}
	{/if}
{/capture}

</form>

{/capture}

{if $smarty.request.user_type}
	{assign var="_title" value=$smarty.request.user_type|fn_get_user_type_description:true}
{else}
	{assign var="_title" value=$lang.users}
{/if}
{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools}
