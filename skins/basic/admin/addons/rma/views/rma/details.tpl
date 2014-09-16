{* $Id: details.tpl 10581 2010-09-02 12:46:41Z 2tl $ *}

{capture name="extra_tools"}
	{hook name="rma:details_tools"}
	{include file="buttons/button.tpl" but_text=$lang.related_order but_href="orders.details?order_id=`$return_info.order_id`" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button.tpl" but_text=$lang.delete_this_return but_href="rma.delete?return_id=`$return_info.return_id`" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button_popup.tpl" but_text=$lang.print_slip but_href="rma.print_slip?return_id=`$return_info.return_id`" width="800" height="600" but_role="tool"}
	{/hook}
{/capture}

{capture name="mainbox"}

{if $return_info}
<form action="{""|fn_url}" method="post" name="return_info_form" />
<input type="hidden" name="change_return_status[order_id]" value="{$return_info.order_id}" />
<input type="hidden" name="change_return_status[action]" value="{$return_info.action}" />
<input type="hidden" name="change_return_status[status_from]" value="{$return_info.status}" />
<input type="hidden" name="change_return_status[return_id]" value="{$smarty.request.return_id}" />
<input type="hidden" name="total_amount" value="{$return_info.total_amount}" />

<div class="item-summary clear center">
	<div class="float-left">
	{$lang.rma_return}&nbsp;&nbsp;<span>#{$return_info.return_id}</span>&nbsp;
	{$lang.by}&nbsp;&nbsp;<strong>{if $return_info.user_id}<a href="{"profiles.update?user_id=`$return_info.user_id`"|fn_url}">{/if}{$return_info.user_id|fn_get_user_name}{if $return_info.user_id}</a>{/if}</strong>&nbsp;
		{assign var="time_from" value=$return_info.timestamp|date_format:$settings.Appearance.date_format|escape:url}
		{assign var="time_to" value=$return_info.timestamp|date_format:$settings.Appearance.date_format|escape:url}
		{$lang.on}&nbsp;<a href="{"rma.returns?period=C&amp;time_from=`$time_from`&amp;time_to=`$time_to`"|fn_url}">{$return_info.timestamp|date_format:"`$settings.Appearance.date_format`"}</a>,&nbsp;&nbsp;{$return_info.timestamp|date_format:"`$settings.Appearance.time_format`"}
	</div>
</div>

{hook name="rma:items_content"}
{/hook}

{capture name="tabsbox"}
{** RETURN PROSUCTS SECTION **}
	<div id="content_return_products">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="1%" class="center">
				{hook name="rma:returned_items_header"}
				<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}"{if $return_info.status != $smarty.const.RMA_DEFAULT_STATUS} disabled="disabled"{/if} class="checkbox cm-check-items" />
				{/hook}
			</th>
			<th width="100%">{$lang.product}</th>
			<th>{$lang.price}</th>
			<th>{$lang.quantity}</th>
			<th>{$lang.reason}</th>
		</tr>
		{foreach from=$return_info.items[$smarty.const.RETURN_PRODUCT_ACCEPTED] item="ri" key="key"}
		<tr {cycle values="class=\"table-row\", "}>
			<td width="1%" class="center">
				{hook name="rma:returned_items_content"}
				<input type="checkbox" name="accepted[{$ri.item_id}][chosen]" value="Y"{if $return_info.status != $smarty.const.RMA_DEFAULT_STATUS} disabled="disabled"{/if} class="checkbox cm-item" />
				{/hook}
			</td>
			<td>{if !$ri.deleted_product}<a href="{"products.update?product_id=`$ri.product_id`"|fn_url}">{/if}{$ri.product|default:$ri.product}{if !$ri.deleted_product}</a>{/if}
			{if $ri.product_options}<div class="options-info">&nbsp;{include file="common_templates/options_info.tpl" product_options=$ri.product_options}</div>{/if}
			</td>
			<td class="nowrap">
				{if !$ri.price}{$lang.free}{else}{include file="common_templates/price.tpl" value=$ri.price}{/if}</td>
			<td>
				<input type="hidden" name="accepted[{$ri.item_id}][previous_amount]" value="{$ri.amount}" />
				{hook name="rma:returned_items_amount"}
				<select name="accepted[{$ri.item_id}][amount]"{if $return_info.status != $smarty.const.RMA_DEFAULT_STATUS} disabled="disabled"{/if}>
				{/hook}
				{section name=$key loop=$ri.amount+1 start="1" step="1"}
						<option value="{$smarty.section.$key.index}" {if $smarty.section.$key.index == $ri.amount}selected="selected"{/if}>{$smarty.section.$key.index}</option>
				{/section}
				</select></td>
			<td class="nowrap">
				{assign var="reason_id" value=$ri.reason}
				&nbsp;{$reasons.$reason_id.property}&nbsp;</td>
		</tr>
		{foreachelse}
		<tr class="no-items">
			<td colspan="5"><p>{$lang.no_items}</p></td>
		</tr>
		{/foreach}
		</table>

		{if $return_info.items[$smarty.const.RETURN_PRODUCT_ACCEPTED]}
			<div class="buttons-container buttons-bg">
			{capture name="tools_list"}
			<ul>
				<li><a name="dispatch[rma.create_gift_certificate]" class="cm-process-items cm-confirm" rev="return_info_form">{$lang.create_gift_certificate}</a></li>
			</ul>
			{/capture}

			{if $return_info.status == $smarty.const.RMA_DEFAULT_STATUS}
				{include file="buttons/button.tpl" but_text=$lang.decline_products but_name="dispatch[rma.decline_products]" but_role="button_main" but_meta="cm-process-items"}
				{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
			{else}
				{include file="buttons/button.tpl" but_text=$lang.create_gift_certificate but_name="dispatch[rma.create_gift_certificate]" but_role="button_main" but_meta="cm-process-items cm-confirm"}
			{/if}
			
			</div>
		{/if}
	</div>
{** /RETURN PROSUCTS SECTION **}

{** DECLINED PROSUCTS SECTION **}
	<div id="content_declined_products" class="hidden">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th>
				<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" {if $return_info.status != $smarty.const.RMA_DEFAULT_STATUS}disabled="disabled"{/if} class="checkbox cm-check-items" /></th>
			<th width="100%">{$lang.product}</th>
			<th>{$lang.price}</th>
			<th>{$lang.quantity}</th>
			<th>{$lang.reason}</th>
		</tr>
		{foreach from=$return_info.items[$smarty.const.RETURN_PRODUCT_DECLINED] item="ri" key="key"}
		<tr {cycle values="class=\"table-row\", "}>
			<td width="1%" class="center">
				<input type="checkbox" name="declined[{$ri.item_id}][chosen]" value="Y" {if $return_info.status != $smarty.const.RMA_DEFAULT_STATUS}disabled="disabled"{/if} class="checkbox cm-item" /></td>
			<td>{if !$ri.deleted_product}<a href="{"products.update?product_id=`$ri.product_id`"|fn_url}">{/if}{$ri.product}{if !$ri.deleted_product}</a>{/if}
			{if $ri.product_options}<div class="options-info">&nbsp;{include file="common_templates/options_info.tpl" product_options=$ri.product_options}</div>{/if}
			</td>
			<td class="nowrap">
				{if !$ri.price}{$lang.free}{else}{include file="common_templates/price.tpl" value=$ri.price}{/if}</td>
			<td>
				<input type="hidden" name="declined[{$ri.item_id}][previous_amount]" value="{$ri.amount}" />
				<select name="declined[{$ri.item_id}][amount]" {if $return_info.status != $smarty.const.RMA_DEFAULT_STATUS}disabled="disabled"{/if}>
				{section name=$key loop=$ri.amount+1 start="1" step="1"}
						<option value="{$smarty.section.$key.index}" {if $smarty.section.$key.index == $ri.amount}selected="selected"{/if}>{$smarty.section.$key.index}</option>
				{/section}
				</select></td>
			<td class="nowrap">
				{assign var="reason_id" value=$ri.reason}
				&nbsp;{$reasons.$reason_id.property}&nbsp;</td>
		</tr>
		{foreachelse}
		<tr class="no-items">
			<td colspan="5"><p>{$lang.no_items}</p></td>
		</tr>
		{/foreach}
		</table>
		{if $return_info.items[$smarty.const.RETURN_PRODUCT_DECLINED] && $return_info.status == $smarty.const.RMA_DEFAULT_STATUS}
			<div class="buttons-container">
				{include file="buttons/button.tpl" but_text=$lang.accept_products but_name="dispatch[rma.accept_products]" but_role="button_main" but_meta="cm-process-items"}
			</div>
		{/if}
	</div>
{** /DECLINED PROSUCTS SECTION **}


<div id="content_comments" class="cm-hide-save-button hidden">
<fieldset>
<textarea name="comment" cols="55" rows="8" class="input-textarea-long">{$return_info.comment}</textarea>
</fieldset>
{* Customer info *}
<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.customer_information but_onclick="$('#customer_info').toggle();" but_role="text" but_meta="text-button"}
</div>
<div id="customer_info" class="hidden">{include file="views/profiles/components/profiles_info.tpl" user_data=$order_info location="I"}</div>
{* /Customer info *}

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[rma.update_details]" but_role="button_main"}
</div>
</div>

<div id="content_actions" class="cm-hide-save-button hidden">

<fieldset>
<div class="select-field">
	<label for="elm_status">{$lang.status}:</label>
	{include file="common_templates/status.tpl" select_id="elm_status" status=$return_info.status display="select" name="change_return_status[status_to]" status_type=$smarty.const.STATUSES_RETURN}
</div>

<div class="select-field">
	<input id="elm_recalc_order" type="radio" name="change_return_status[recalculate_order]" value="R" />
	<label for="elm_recalc_order">{$lang.recalculate_order}</label>
</div>

{if $is_refund == "Y"}
<div class="select-field">
	<input id="elm_recalc_manually" type="radio" name="change_return_status[recalculate_order]" value="M" />
	<label for="elm_recalc_manually">{$lang.manually_recalculate_order}</label>
</div>
{/if}

<div class="select-field">
	<input id="elm_recalc_skip" type="radio" name="change_return_status[recalculate_order]" value="D" checked="checked" />
	<label for="elm_recalc_skip">{$lang.dont_recalculate_order}</label>
</div>

<div class="select-field notify-customer">
	<input type="checkbox" name="change_return_status[notify_user]" id="notify_user" value="Y" class="checkbox" />
	<label for="notify_user">{$lang.notify_customer}</label>
</div>

<div class="select-field notify-department">
	<input type="checkbox" name="change_return_status[notify_department]" id="notify_department" value="Y" class="checkbox" />
	<label for="notify_department">{$lang.notify_orders_department}</label>
</div>

{if $settings.Suppliers.enable_suppliers == "Y"}
<div class="select-field notify-department">
	<input type="checkbox" name="change_return_status[notify_supplier]" id="notify_supplier" value="Y" class="checkbox" />
	<label for="notify_supplier">{$lang.notify_supplier}</label>
</div>
{/if}
</fieldset>

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[rma.update_details]" but_role="button_main"}
</div>
</div>
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}


</form>
{/if}

{/capture}

{include file="common_templates/view_tools.tpl" url="rma.details?return_id="}

{include file="common_templates/mainbox.tpl" title=$lang.return_info content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools tools=$smarty.capture.view_tools}