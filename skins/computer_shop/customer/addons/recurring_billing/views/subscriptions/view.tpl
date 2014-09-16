{* $Id: view.tpl 6335 2008-11-14 12:01:03Z zeke $ *}

{capture name="tabsbox"}
	<div id="content_general">
		<form action="{""|fn_url}" method="post" name="subscription_form">
			<input type="hidden" name="subscription_id" value="{$subscription.subscription_id}" />

			<div class="form-field">
				<label>{$lang.rb_creation_date}:</label>
				{$subscription.timestamp|date_format:$settings.Appearance.date_format}
			</div>

			<div class="form-field">
				<label>{$lang.order_id}:</label>
				<a href="{"orders.details?order_id=`$subscription.order_id`"|fn_url}">{$subscription.order_id}</a>
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
				<label>{$lang.payment_method}:</label>
				{$subscription.order_info.payment_method.payment}&nbsp;{if $subscription.order_info.payment_method.description}({$subscription.order_info.payment_method.description}){/if}
			</div>

			{if $subscription.plan_info.allow_change_duration == "Y"}
			<div class="form-field">
				<label for="rb_duration">{$lang.rb_duration_short}:</label>
				<input id="rb_duration" type="text" name="update_duration[{$subscription.subscription_id}]" value="{$subscription.duration}" size="6" class="input-text-short" />
			</div>
			{/if}

			<div class="buttons-container">
			{assign var="update_action" value="action"}
			{if $subscription_pay_order_id}
				{include file="buttons/button.tpl" but_href="orders.details?order_id=`$subscription_pay_order_id`" but_text=$lang.rb_pay but_role="action"}&nbsp;
				{assign var="update_action" value="text"}
			{/if}
			{if $subscription.plan_info.allow_change_duration == "Y"}
				{include file="buttons/button.tpl" but_name="dispatch[subscriptions.update]" but_text=$lang.update but_role=$update_action}&nbsp;
			{/if}
			{if $subscription.plan_info.allow_unsubscribe == "Y"}
				{include file="buttons/button.tpl" but_name="dispatch[subscriptions.unsubscribe]" but_text=$lang.unsubscribe but_role="text"}
			{/if}
			</div>
		</form>
	</div>

	<div id="content_linked_products">
		<table cellpadding="0" cellspacing="0" border="0" class="table product-list" width="100%">
		<tr>
			<th>{$lang.product}</th>
			<th width="5%">{$lang.price}</th>
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
		{foreach from=$order_info.items item="product" key="key"}
		{if !$product.extra.parent && $product.extra.recurring_plan_id == $subscription.plan_id && $product.extra.recurring_duration == $subscription.orig_duration}
		{hook name="orders:items_list_row"}
		<tr {cycle values="class=\"table-row\", " name="class_cycle"} valign="top">
			<td>
				{if !$product.deleted_product}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{/if}{$product.product|unescape}{if !$product.deleted_product}</a>{/if}
				{hook name="orders:product_info"}
				{if $product.product_code}<p>{$lang.sku}:&nbsp;{$product.product_code}</p>{/if}
				{/hook}
				{if $product.product_options}<div class="options-info">{include file="common_templates/options_info.tpl" product_options=$product.product_options}</div>{/if}
			</td>
			<td class="nowrap">
				{if $product.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$product.base_price}{/if}</td>
			<td class="center">&nbsp;{$product.amount}</td>
			{if $order_info.use_discount}
			<td class="nowrap">
				{if $product.extra.discount|floatval}{include file="common_templates/price.tpl" value=$product.extra.discount}{else}-{/if}</td>
			{/if}
			{if $order_info.taxes}
			<td class="nowrap">
				{if $product.tax_value|floatval}{include file="common_templates/price.tpl" value=$product.tax_value}{else}-{/if}</td>
			{/if}
			<td class="right">&nbsp;<strong>{if $product.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$product.display_subtotal}{/if}</strong></td>
		</tr>
		{/hook}
		{/if}
		{/foreach}
		<tr class="table-footer">
			{assign var="colsp" value=4}
			{if $order_info.use_discount}{assign var="colsp" value=$colsp+1}{/if}
			{if $order_info.taxes}{assign var="colsp" value=$colsp+1}{/if}
			<td colspan="{$colsp}">&nbsp;</td>
		</tr>
		</table>
	</div>

	<div id="content_paids">
		<script type="text/javascript">
		//<![CDATA[

		function fn_download_recurring_paid()
		{$ldelim}
			jQuery.ajaxRequest('{"orders.search?order_id=`$subscription.order_ids`"|fn_url:'C':'rel':'&'}', {$ldelim}result_ids: 'pagination_contents'{$rdelim});
			$('#paids').unbind('click', fn_download_recurring_paid);
		{$rdelim}

		$('#paids').bind('click', fn_download_recurring_paid);

		//]]>
		</script>
		<div id="pagination_contents"></div>
	</div>
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}

{capture name="mainbox_title"}{$lang.rb_subscription} #{$subscription.subscription_id}{/capture}