{* $Id: search.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{capture name="section"}
	{include file="views/orders/components/orders_search_form.tpl"}
{/capture}
{include file="common_templates/section.tpl" section_title=$lang.search section_content=$smarty.capture.section class="search-form"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $search.sort_order == "asc"}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8595;"}
{else}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8593;"}
{/if}
{if $settings.DHTML.customer_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}

{/if}

{include file="common_templates/pagination.tpl"}
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
<tr>
	<th width="10%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=order_id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.id}</a>{if $search.sort_by == "order_id"}{$sort_sign}{/if}</th>
	<th width="15%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a>{if $search.sort_by == "status"}{$sort_sign}{/if}</th>
	<th width="25%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a>{if $search.sort_by == "customer"}{$sort_sign}{/if}</th>
	<th width="25%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a>{if $search.sort_by == "date"}{$sort_sign}{/if}</th>
	<th width="24%" class="right"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=total&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.total}</a>{if $search.sort_by == "total"}{$sort_sign}{/if}</th>
</tr>
{foreach from=$orders item="o"}
<tr {cycle values=",class=\"table-row\""}>
	<td class="center"><a href="{"orders.details?order_id=`$o.order_id`"|fn_url}"><strong>#{$o.order_id}</strong></a></td>
	<td>{include file="common_templates/status.tpl" status=$o.status display="view"}</td>
	<td>
		<ul class="no-markers">
			<li>{$o.firstname} {$o.lastname}</li>
			<li><a href="mailto:{$o.email|escape:url}">{$o.email}</a></li>
		</ul>
	</td>
	<td><a href="{"orders.details&amp;order_id=`$o.order_id`"|fn_url}">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>
	<td class="right">{include file="common_templates/price.tpl" value=$o.total}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="7"><p class="no-items">{$lang.text_no_orders}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="5">&nbsp;</td>
</tr>
</table>

{include file="common_templates/pagination.tpl"}

{capture name="mainbox_title"}{$lang.orders}{/capture}
