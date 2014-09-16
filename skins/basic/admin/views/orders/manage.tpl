{* $Id: manage.tpl 10581 2010-09-02 12:46:41Z 2tl $ *}

{capture name="mainbox"}

{capture name="extra_tools"}
	{if $incompleted_view}
		{include file="buttons/button.tpl" but_text=$lang.view_all_orders but_href="orders.manage" but_role="tool"}
	{else}
		{include file="buttons/button.tpl" but_text=$lang.incompleted_orders but_href="orders.manage?skip_view=Y&status=`$smarty.const.STATUS_INCOMPLETED_ORDER`" but_role="tool"}
	{/if}
{/capture}

{if $mode == "new"}
	<p>{$lang.text_admin_new_orders}</p>
{/if}

{include file="views/orders/components/orders_search_form.tpl" dispatch="orders.manage"}

<form action="{""|fn_url}" method="post" target="_self" name="orders_list_form">

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table style="" border="0" cellpadding="0" cellspacing="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="2%"><a class="{$ajax_class}{if $search.sort_by == "order_id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=order_id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.id}</a></th>
	<th width="3%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th width="6%"><a class="{$ajax_class}{if $search.sort_by == "customer"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a></th>
	<th width="3%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a>	</th>
	<th width="3%"><a class="{$ajax_class}{if $search.sort_by == "deliv"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=deliv&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date_to_delivery2}</a>	</th>	
	<th width="3%"><a class="{$ajax_class}{if $search.sort_by == "total"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=total&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.subtotal}</a></th>
	<th width="6%"><a class="{$ajax_class}{if $search.sort_by == "payment"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=payment&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.method}</a></th>	
	<th width="5">Метро</th>
    <th width="2%">{$lang.orders_th_operations}</th>
</tr>
{if $incompleted_view}
	{assign var="page_title" value=$lang.incompleted_orders}
	{assign var="get_additional_statuses" value=true}
{else}
	{assign var="page_title" value=$lang.orders}
	{assign var="get_additional_statuses" value=false}
{/if}
{assign var="order_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:true:$get_additional_statuses:true}
{assign var="extra_status" value=$config.current_url|escape:"url"}
{assign var="order_statuses_data" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:false:$get_additional_statuses:true}
{foreach from=$orders item="o"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="order_ids[]" value="{$o.order_id}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"orders.details?order_id=`$o.order_id`"|fn_url}" class="underlined">&nbsp;<strong>#{$o.order_id}</strong>&nbsp; {include file="views/companies/components/company_name.tpl" company_id=$o.company_id}</a></td>
	<td>
		{include file="common_templates/select_popup.tpl" suffix="o" id=$o.order_id status=$o.status items_status=$order_status_descr update_controller="orders" notify=true notify_department=true notify_supplier=true status_rev="orders_total,pagination_contents" extra="&return_url=`$extra_status`"}	</td>
	<td>{if $o.user_id}<a href="{"profiles.update?user_id=`$o.user_id`"|fn_url}">{/if}{$o.lastname} {$o.firstname} {$o.firma}{if $o.user_id}</a>{/if}</td>
	<td class="nowrap">
		{assign var="date_to_delivery_def" value=date_format:"`$settings.Appearance.date_format`"} 
		{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td class="nowrap" valign="middle">{if $o.date_to_delivery != 0}{$o.date_to_delivery|date_format:"`$settings.Appearance.date_format`"}{else}{$o.timestamp|date_format:"`$settings.Appearance.date_format`"}{/if}</td>
	<td class="nowrap">{include file="common_templates/price.tpl" value=$o.total}</td>
	<td class="nowrap">{$o.payment}</td>
	<td class="nowrap">	<div class="spaaa">{$o.metro} </div></td>	
	<td class="nowrap" align="center" valign="middle">
		{*<a class="tool-link" href="{"orders.details?order_id=`$o.order_id`"|fn_url}">{$lang.viewing}</a>*}
		
		{capture name="tools_items"}
		<ul>
			{hook name="orders:list_extra_links"}
			<li><a href="{"order_management.edit?order_id=`$o.order_id`"|fn_url}">{$lang.edit}</a></li>
			{assign var="current_redirect_url" value=$config.current_url|escape:url}
			<li><a class="cm-confirm" href="{"orders.delete?order_id=`$o.order_id`&amp;redirect_url=`$current_redirect_url`"|fn_url}">{$lang.delete}</a></li>
			{/hook}
		</ul>
		{/capture}

		{if $smarty.capture.tools_items|strpos:"<li>"}
		<a class="cm-confirm" href="{"orders.delete?order_id=`$o.order_id`&amp;redirect_url=`$current_redirect_url`"|fn_url}" style="width: 16px; margin: 0px auto;"><img src="/images/delete-icon.png"></a>
			{* include file="common_templates/tools.tpl" prefix=$o.order_id hide_actions=true tools_list=$smarty.capture.tools_items display="inline" link_text=$lang.more link_meta="lowercase" *}
		{/if}
	</td>	
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="9"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if $orders}
	{include file="common_templates/table_tools.tpl" href="#orders"}
{/if}

{include file="common_templates/pagination.tpl"}
	
{if $orders}
	<div align="right" class="clear" id="orders_total">
		<ul class="statistic-list">
			{if $total_pages > 1 && $search.page != "full_list"}
			<li><strong>{$lang.for_this_page_orders}:</strong></li>
			<li>
				<em>{$lang.gross_total}:</em>
				<strong>{include file="common_templates/price.tpl" value=$display_totals.gross_total}</strong>
			</li>
			{if !$incompleted_view}
			<li>
				<em>{$lang.totally_paid}:</em>
				<strong>{include file="common_templates/price.tpl" value=$display_totals.totally_paid}</strong>
			</li>
			{/if}
			<hr />
			<li><strong>{$lang.for_all_found_orders}:</strong></li>
			{/if}
			<li>
				<em>{$lang.gross_total}:</em>
				<strong>{include file="common_templates/price.tpl" value=$totals.gross_total}</strong>
			</li>
			{hook name="orders:totals_stats"}
			{if !$incompleted_view}
			<li class="total">
				<em>{$lang.totally_paid}:</em>
				<strong>{include file="common_templates/price.tpl" value=$totals.totally_paid}</strong>
			</li>
			{/if}
			{/hook}
		</ul>
	<!--orders_total--></div>
{/if}
	
<div class="buttons-container buttons-bg">
	{if $orders}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a class="cm-process-items" name="dispatch[orders.remove_cc_info]" rev="orders_list_form">{$lang.remove_cc_info}</a></li>
			<li><a class="cm-process-items" name="dispatch[orders.export_range]" rev="orders_list_form">{$lang.export_selected}</a></li>
			<li><a class="cm-process-items" name="dispatch[orders.packing_slip]" rev="orders_list_form">{$lang.bulk_print} ({$lang.packing_slip})</a></li>
			<li><a class="cm-process-items" name="dispatch[orders.bulk_print..pdf]" rev="orders_list_form">{$lang.bulk_print} (PDF)</a></li>
			{hook name="orders:list_tools"}
			{/hook}
			<li><a class="cm-confirm cm-process-items" name="dispatch[orders.delete_orders]" rev="orders_list_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_text=$lang.bulk_print but_name="dispatch[orders.bulk_print]" but_meta="cm-process-items cm-new-window" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="order_management.new" prefix="bottom" hide_tools="true" link_text=$lang.add_order}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="order_management.new" prefix="top" hide_tools="true" link_text=$lang.add_order}
{/capture}

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$page_title content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools extra_tools=$smarty.capture.extra_tools}
