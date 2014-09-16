{* $Id: update.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

{capture name="mainbox"}

{capture name="tabsbox"}
<div id="content_general">
<form action="{""|fn_url}" method="post" name="subscription_form" class="cm-form-highlight">
<input type="hidden" name="subscription_id" value="{$subscription.subscription_id}" />
<input type="hidden" name="order_id" value="{$subscription.order_id}" />
<input type="hidden" name="selected_section" id="selected_section" value="{$smarty.request.selected_section}" />

	<div class="form-field">
		<label>{$lang.rb_creation_date}:</label>
		{$subscription.timestamp|date_format:$settings.Appearance.date_format}
	</div>

	<div class="form-field">
		<label for="subs_status">{$lang.status}:</label>
		<select name="status" id="subs_status">
			<option value="A"{if $subscription.status == "A"} selected="selected"{/if}>{$lang.active}</option>
			<option value="D"{if $subscription.status == "D"} selected="selected"{/if}>{$lang.disabled}</option>
			<option value="U"{if $subscription.status == "U"} selected="selected"{/if}>{$lang.rb_unsubscribed}</option>
			<option value="C"{if $subscription.status == "C"} selected="selected"{/if}>{$lang.completed}</option>
		</select>
	</div>

	<div class="form-field">
		<label>{$lang.rb_recurring_plan}:</label>
		<a href="{"recurring_plans.update?plan_id=`$subscription.plan_id`"|fn_url}">#{$subscription.plan_id}</a>
	</div>

	<div class="form-field">
		<label>{$lang.order}:</label>
		<a href="{"orders.details?order_id=`$subscription.order_id`"|fn_url}">#{$subscription.order_id}</a>
	</div>

	<div class="form-field">
		<label>{$lang.end_date}:</label>
		{$subscription.end_timestamp|date_format:$settings.Appearance.date_format}
	</div>

	<div class="form-field">
		<label>{$lang.last_order}:</label>
		{$subscription.last_timestamp|date_format:$settings.Appearance.date_format}
	</div>

	<div class="form-field">
		<label>{$lang.rb_recurring_period}:</label>
		{$subscription.plan_info.period|fn_get_recurring_period_name|escape}{if $subscription.plan_info.period == "P"} - {$subscription.plan_info.by_period} {$lang.days}{/if}
	</div>

	<div class="form-field">
		<label>{$lang.customer}:</label>
		<strong>{if $subscription.order_info.title_descr}{$subscription.order_info.title_descr}&nbsp;{/if}{$subscription.order_info.firstname}&nbsp;{$subscription.order_info.lastname}</strong>, <a href="mailto:{$subscription.order_info.email|escape:url}">{$subscription.order_info.email}</a>
	</div>

	<div class="form-field">
		<label>{$lang.payment_method}:</label>
		{$subscription.order_info.payment_method.payment}&nbsp;{if $subscription.order_info.payment_method.description}({$subscription.order_info.payment_method.description}){/if}
	</div>

	<h2 class="subheader">{$lang.shipping_information}</h2>	

	{foreach from=$subscription.order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
	<div class="form-field">
		<label>{$lang.method}:</label>
		{$shipping.shipping}
	</div>

	<div class="form-field">
		<label for="tracking_number">{$lang.tracking_number}:</label>
		<input id="tracking_number" type="text" class="input-text-medium" name="update_shipping[{$shipping_id}][tracking_number]" size="45" value="{$shipping.tracking_number}" />
	</div>
	<div class="form-field">
		<label for="carrier_key">{$lang.carrier}:</label>
		<select id="carrier_key" name="update_shipping[{$shipping_id}][carrier]">
			<option value="">--</option>
			<option value="USP" {if $shipping.carrier == "USP"}selected="selected"{/if}>{$lang.usps}</option>
			<option value="UPS" {if $shipping.carrier == "UPS"}selected="selected"{/if}>{$lang.ups}</option>
			<option value="FDX" {if $shipping.carrier == "FDX"}selected="selected"{/if}>{$lang.fedex}</option>
			<option value="AUP" {if $shipping.carrier == "AUP"}selected="selected"{/if}>{$lang.australia_post}</option>
			<option value="DHL" {if $shipping.carrier == "DHL" || $subscription.order_info.carrier == "ARB"}selected="selected"{/if}>{$lang.dhl}</option>
			<option value="CHP" {if $shipping.carrier == "CHP"}selected="selected"{/if}>{$lang.chp}</option>
		</select>
	</div>
	{/foreach}

	<div class="select-field notify-customer">
		<input type="checkbox" name="notify_user" id="notify_user" value="Y" class="checkbox" />
		<label for="notify_user">{$lang.notify_customer}</label>
	</div>

	<div class="buttons-container buttons-bg">
		{include file="buttons/save_cancel.tpl" but_name="dispatch[subscriptions.update]"}
	</div>
</form>
</div>

<div id="content_linked_products" class="hidden">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th>{$lang.product}</th>
		<th width="5%">{$lang.rb_price}</th>
		<th width="5%">{$lang.quantity}</th>
		{if $subscription.order_info.use_discount}
		<th width="5%">{$lang.discount}</th>
		{/if}
		{if $subscription.order_info.taxes}
		<th width="5%">&nbsp;{$lang.tax}</th>
		{/if}
		<th width="7%" class="right">&nbsp;{$lang.subtotal}</th>
	</tr>
	{assign var="order_info" value=$subscription.order_info}
	{foreach from=$order_info.items item="oi" key="key"}
	{if !$oi.extra.parent && $oi.extra.recurring_plan_id == $subscription.plan_id&& $oi.extra.recurring_duration == $subscription.orig_duration}
	{hook name="orders:items_list_row"}
	<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
		<td>
			{if !$oi.deleted_product}<a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{/if}{$oi.product|unescape}{if !$oi.deleted_product}</a>{/if}
			{hook name="orders:product_info"}
			{if $oi.product_code}</p>{$lang.sku}:&nbsp;{$oi.product_code}</p>{/if}
			{/hook}
			{if $oi.product_options}<div class="options-info">{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}
		</td>
		<td class="nowrap">
			{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.base_price}{/if}</td>
		<td class="center">&nbsp;{$oi.amount}</td>
		{if $order_info.use_discount}
		<td class="nowrap">
			{if $oi.extra.discount|floatval}{include file="common_templates/price.tpl" value=$oi.extra.discount}{else}-{/if}</td>
		{/if}
		{if $order_info.taxes}
		<td class="nowrap">
			{if $oi.tax_value|floatval}{include file="common_templates/price.tpl" value=$oi.tax_value}{else}-{/if}</td>
		{/if}
		<td class="right">&nbsp;<strong>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.display_subtotal}{/if}</strong></td>
	</tr>
	{/hook}
	{/if}
	{/foreach}
	</table>
</div>

<div id="content_paids" class="hidden">
	<script type="text/javascript">
	//<![CDATA[
	function fn_download_recurring_paid()
	{$ldelim}
		jQuery.ajaxRequest('{"orders.manage?order_id=`$subscription.order_ids`"|fn_url:'A':'rel':'&'}', {$ldelim}result_ids: 'pagination_contents,orders_total', callback: function() {$ldelim}$('#paids_tools').show();{$rdelim}{$rdelim});
		$('#paids').unbind('click', fn_download_recurring_paid);
	{$rdelim}

	$('#paids').bind('click', fn_download_recurring_paid);
	//]]>
	</script>
	
	<form action="{""|fn_url}" method="post" target="_self" name="orders_list_form">

	<div id="pagination_contents"></div>
	<div id="orders_total" class="clear" align="right"></div>

	<div class="buttons-container buttons-bg hidden" id="paids_tools">
		<div class="float-left">
			{include file="buttons/button.tpl" but_text=$lang.bulk_print but_name="dispatch[orders.bulk_print]" but_meta="cm-process-items cm-new-window" but_role="button_main"}
		</div>
	</div>
	</form>
</div>
{/capture}

{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

{/capture}
{assign var="title" value="`$lang.rb_viewing_subscription`: #`$subscription.subscription_id`"}
{include file="common_templates/view_tools.tpl" url="subscriptions.update?subscription_id="}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.view_tools}
