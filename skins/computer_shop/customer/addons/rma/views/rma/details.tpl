{* $Id: details.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

<div align="right">
	<ul class="action-bullets">
		<li><a href="{"orders.details?order_id=`$return_info.order_id`"|fn_url}">{$lang.related_order}</a></li>
	</ul>
</div>

{if $return_info}
<div class="right">{include file="buttons/button_popup.tpl" but_text=$lang.print_slip but_href="rma.print_slip?return_id=`$return_info.return_id`" width="800" height="600"}</div>

<form action="{""|fn_url}" method="post" name="return_info_form" />
<input type="hidden" name="return_id" value="{$smarty.request.return_id}" />
<input type="hidden" name="order_id" value="{$return_info.order_id}" />
<input type="hidden" name="total_amount" value="{$return_info.total_amount}" />
<input type="hidden" name="return_status" value="{$return_info.status}" />


<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td>
		<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td><strong>{$lang.rma_return}</strong>:&nbsp;</td><td>#{$return_info.return_id}</td>
		</tr>
		<tr>
			<td><strong>{$lang.date}</strong>:&nbsp;</td><td>{$return_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
		</tr>
		<tr>
			<td><strong>{$lang.action}</strong>:&nbsp;</td><td>{assign var="action_id" value=$return_info.action}{$actions.$action_id.property}</td>
		</tr>
		{hook name="orders:return_info"}
		<tr>
			<td><strong>{$lang.status}</strong>:&nbsp;</td><td>{include file="common_templates/status.tpl" status=$return_info.status display="view" name="update_return[status]" status_type=$smarty.const.STATUSES_RETURN}</td>
		</tr>
		{/hook}
		</table>
	</td>
</tr>
</table>

{capture name="tabsbox"}
{** RETURN PROSUCTS SECTION **}
	<div id="content_return_products">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="100%">{$lang.product}</th>
			<th>{$lang.price}</th>
			<th>{$lang.quantity}</th>
			<th>{$lang.reason}</th>
		</tr>
		{foreach from=$return_info.items[$smarty.const.RETURN_PRODUCT_ACCEPTED] item="ri" key="key"}
		<tr {cycle values=",class=\"table-row\""}>
			<td>&nbsp;{if !$ri.deleted_product}<a href="{"products.view?product_id=`$ri.product_id`"|fn_url}">{/if}{$ri.product}{if !$ri.deleted_product}</a>{/if}
				{if $ri.product_options}
					{include file="common_templates/options_info.tpl" product_options=$ri.product_options}
				{/if}</td>
			<td class="right nowrap">
				{if !$ri.price}{$lang.free}{else}{include file="common_templates/price.tpl" value=$ri.price}{/if}</td>
			<td class="center">{$ri.amount}</td>
			<td class="nowrap">
				{assign var="reason_id" value=$ri.reason}
				&nbsp;{$reasons.$reason_id.property}&nbsp;</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="6"><p class="no-items">{$lang.text_no_products_found}</p></td>
		</tr>
		{/foreach}
		<tr class="table-footer">
			<td colspan="6">&nbsp;</td>
		</tr>
		</table>
	</div>
{** /RETURN PROSUCTS SECTION **}

{** DECLINED PROSUCTS SECTION **}
	<div id="content_declined_products" class="hidden">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="100%">{$lang.product}</th>
			<th>{$lang.price}</th>
			<th>{$lang.quantity}</th>
			<th>{$lang.reason}</th>
		</tr>
		{foreach from=$return_info.items[$smarty.const.RETURN_PRODUCT_DECLINED] item="ri" key="key"}
		<tr {cycle values=",class=\"table-row\""}>
			<td>
				&nbsp;{if !$ri.deleted_product}<a href="{"products.view?product_id=`$ri.product_id`"|fn_url}">{/if}{$ri.product}{if !$ri.deleted_product}</a>{/if}
				{if $ri.product_options}
					{include file="common_templates/options_info.tpl" product_options=$ri.product_options}
				{/if}</td>
			<td class="right nowrap">
				{if !$ri.price}{$lang.free}{else}{include file="common_templates/price.tpl" value=$ri.price}{/if}</td>
			<td class="center">{$ri.amount}</td>
			<td class="nowrap">
				{assign var="reason_id" value=$ri.reason}
				&nbsp;{$reasons.$reason_id.property}&nbsp;</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="6"><p class="no-items">{$lang.text_no_products_found}</p></td>
		</tr>
		{/foreach}
		<tr class="table-footer">
			<td colspan="5">&nbsp;</td>
		</tr>
		</table>
	</div>
{** /DECLINED PROSUCTS SECTION **}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}

{if $return_info.comment}
	{include file="common_templates/subheader.tpl" title=$lang.comments}
	{$return_info.comment|nl2br}
{/if}

</form>
{/if}

{capture name="mainbox_title"}{$lang.return_info}{/capture}