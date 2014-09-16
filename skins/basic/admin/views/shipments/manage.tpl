{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="views/shipments/components/shipments_search_form.tpl" dispatch="shipments.manage"}

<form action="{""|fn_url}" method="post" name="manage_shipments_form">

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center" width="5%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
	</th>
	<th width="5%">
		<a class="{$ajax_class}{if $search.sort_by == "id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.shipment_id}</a>
	</th>
	<th>
		<a class="{$ajax_class}{if $search.sort_by == "order_id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=order_id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.order_id}</a>
	</th>
	<th>
		<a class="{$ajax_class}{if $search.sort_by == "shipment_date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=shipment_date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.shipment_date}</a>
	</th>
	<th>
		<a class="{$ajax_class}{if $search.sort_by == "order_date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=order_date&amp;sort_order`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.order_date}</a>
	</th>
	<th width="40%">
		<a class="{$ajax_class}{if $search.sort_by == "customer"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a>
	</th>
	
	<th>&nbsp;</th>
</tr>
{foreach from=$shipments item=shipment}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
   		<input type="checkbox" name="shipment_ids[]" value="{$shipment.shipment_id}" class="checkbox cm-item" />
   	</td>
	<td>
		<a class="underlined" href="{"shipments.details?shipment_id=`$shipment.shipment_id`"|fn_url}"><strong>#{$shipment.shipment_id}</strong></a>
	</td>
	<td>
		<a class="underlined" href="{"orders.details?order_id=`$shipment.order_id`"|fn_url}"><strong>#{$shipment.order_id}</strong></a>
	</td>
	<td>
		{if $shipment.shipment_timestamp}{$shipment.shipment_timestamp|date_format:"`$settings.Appearance.date_format`"}{else}--{/if}
	</td>
	<td>
		{if $shipment.order_timestamp}{$shipment.order_timestamp|date_format:"`$settings.Appearance.date_format`"}{else}--{/if}
	</td>
	<td>
		{$shipment.s_lastname}&nbsp;{$shipment.s_firstname}
	</td>
	
	<td class="nowrap">
		<a class="tool-link" href="{"shipments.details?shipment_id=`$shipment.shipment_id`"|fn_url}">{$lang.view}</a>
		{capture name="tools_items"}
		<ul>
			{hook name="shipments:list_extra_links"}
			<li><a href="{"shipments.packing_slip?shipment_ids[]=`$shipment.shipment_id`"|fn_url}">{$lang.packing_slip}</a></li>
			{assign var="return_current_url" value=$config.current_url|escape:url}
			<li><a class="cm-confirm" href="{"shipments.delete?shipment_ids[]=`$shipment.shipment_id`&amp;redirect_url=`$return_current_url`"|fn_url}">{$lang.delete}</a></li>
			{/hook}
		</ul>
		{/capture}

		{if $smarty.capture.tools_items|strpos:"<li>"}&nbsp;&nbsp;|
			{include file="common_templates/tools.tpl" prefix=$shipment.shipment_id hide_actions=true tools_list=$smarty.capture.tools_items display="inline" link_text=$lang.more link_meta="lowercase"}
		{/if}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="6"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if $shipments}
	{include file="common_templates/table_tools.tpl" href="#shipments" visibility="Y"}
{/if}

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $shipments}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			{hook name="shipments:list_tools"}
			{/hook}
			<li><a class="cm-confirm cm-process-items" name="dispatch[shipments.delete]" rev="manage_shipments_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_text=$lang.bulk_print but_name="dispatch[shipments.packing_slip]" but_meta="cm-process-items cm-new-window" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
</div>

</form>
{/capture}

{capture name="title"}
	{strip}
	{$lang.shipments}
	{if $smarty.request.order_id}
		&nbsp;({$lang.order}&nbsp;#{$smarty.request.order_id})
	{/if}
	{/strip}
{/capture}
{include file="common_templates/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools}