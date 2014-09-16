{* $Id: returns.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="addons/rma/views/rma/components/rma_search_form.tpl"}

<form action="{""|fn_url}" method="post" target="" enctype="multipart/form-data" name="rma_list_form">

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="10%" align="center"><a class="{$ajax_class}{if $search.sort_by == "return_id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=return_id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.id}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th width="30%"><a class="{$ajax_class}{if $search.sort_by == "customer"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a></th>
	<th width="20%"><a class="{$ajax_class}{if $search.sort_by == "timestamp"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=timestamp&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "action"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=action&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.action}</a></th>
	<th width="10%" align="center"><a class="{$ajax_class}{if $search.sort_by == "order_id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=order_id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.order}&nbsp;{$lang.id}</a></th>
	<th width="10%" align="center"><a class="{$ajax_class}{if $search.sort_by == "amount"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.quantity}</a></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$return_requests item="request"}
<tr {cycle values="class=\"table-row\", "}>
	<td align="center">
		<input type="checkbox" name="return_ids[]" value="{$request.return_id}" class="checkbox cm-item" /></td>
	<td><a href="{"rma.details?return_id=`$request.return_id`"|fn_url}" class="underlined">&nbsp;<strong>#{$request.return_id}</strong>&nbsp;</a></td>
	<td>
		{include file="common_templates/status.tpl" status=$request.status display="view" name="return_statuses[`$request.return_id`]" status_type=$smarty.const.STATUSES_RETURN}
	</td>
	<td>{$request.firstname} {$request.lastname}</td>
	<td><a href="{"rma.details?return_id=`$request.return_id`"|fn_url}" class="underlined">{$request.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>
	<td>{$request.action}</td>
	<td align="center"><a href="{"orders.details?order_id=`$request.order_id`"|fn_url}" class="underlined">&nbsp;<strong>#{$request.order_id}</strong>&nbsp;</a></td>
	<td align="center">{$request.total_amount}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"rma.delete?return_id=`$request.return_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$request.return_id tools_list=$smarty.capture.tools_items href="rma.details?return_id=`$request.return_id`"}
	</td>
<tr>
{foreachelse}
<tr class="no-items">
	<td colspan="8"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if $return_requests}
	{include file="common_templates/table_tools.tpl" href="#rma_returns"}
{/if}

{include file="common_templates/pagination.tpl"}
	
{if $return_requests}
	<div class="buttons-container buttons-bg">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[rma.delete_returns]" class="cm-process-items cm-confirm" rev="rma_list_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_text=$lang.bulk_print but_name="dispatch[rma.bulk_slip_print]" but_role="button_main" but_meta="cm-process-items"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
{/if}
</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.return_requests content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}