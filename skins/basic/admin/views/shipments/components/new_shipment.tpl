{* $Id: new_shipment.tpl 10468 2010-08-20 07:09:28Z alexions $ *}

<form action="{""|fn_url}" method="post" name="shipments_form">
<input type="hidden" name="shipment_data[order_id]" value="{$order_info.order_id}" />

<div class="object-container">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.product}</th>
	<th width="5%">{$lang.quantity}</th>
</tr>

{assign var="shipment_products" value=false}

{foreach from=$order_info.items item="product" key="key"}
	{if $product.shipment_amount > 0}
	{assign var="shipment_products" value=true}
	
	<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
		<td>
			{if $product.deleted_product}{$lang.deleted_product}{else}<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a>{/if}
			{if $product.product_code}<p>{$lang.sku}:&nbsp;{$product.product_code}</p>{/if}
			{if $product.product_options}<div class="options-info">{include file="common_templates/options_info.tpl" product_options=$product.product_options}</div>{/if}
		</td>
		<td class="center" nowrap="nowrap">
				{math equation="amount + 1" amount=$product.shipment_amount assign="loop_amount"}
				{if $loop_amount <= 100}
					<select name="shipment_data[products][{$key}]">
						<option value="0">0</option>
					{section name=amount start=1 loop=$loop_amount}
						<option value="{$smarty.section.amount.index}" {if $smarty.section.amount.last}selected="selected"{/if}>{$smarty.section.amount.index}</option>
					{/section}
					</select>
				{else}
					<input type="text" class="input-text" size="3" name="shipment_data[products][{$key}]" value="{$product.shipment_amount}" />&nbsp;of&nbsp;{$product.shipment_amount}
				{/if}
		</td>
	</tr>
	{/if}
{/foreach}

{if !$shipment_products}
	<tr>
		<td colspan="2">{$lang.no_products_for_shipment}</td>
	</tr>
{/if}

</table>

{include file="common_templates/subheader.tpl" title=$lang.options}

<fieldset>
	<div class="form-field">
		<label for="shipping_name">{$lang.shipping_method}:</label>
		<select	name="shipment_data[shipping_id]" id="shipping_name">
			{foreach from=$shippings item="shipping"}
				<option	value="{$shipping.shipping_id}">{$shipping.shipping}</option>
			{/foreach}
		</select>
	</div>
	
	<div class="form-field">
		<label for="tracking_number">{$lang.tracking_number}:</label>
		<input type="text" name="shipment_data[tracking_number]" id="tracking_number" size="10" value="" class="input-text-medium" />
	</div>
	
	<div class="form-field">
		<label for="carrier_key">{$lang.carrier}:</label>
		<select id="carrier_key" name="shipment_data[carrier]">
			<option value="">--</option>
			<option value="USP">{$lang.usps}</option>
			<option value="UPS">{$lang.ups}</option>
			<option value="FDX">{$lang.fedex}</option>
			<option value="AUP">{$lang.australia_post}</option>
			<option value="DHL">{$lang.dhl}</option>
			<option value="CHP">{$lang.chp}</option>
		</select>
	</div>
	
	<div class="form-field">
		<label for="shipment_comments">{$lang.comments}:</label>
		<textarea id="shipment_comments" name="shipment_data[comments]" cols="55" rows="8" class="input-textarea-long"></textarea>
	</div>
	
	<div class="form-field">
		<label for="order_status">{$lang.order_status}:</label>
		<select id="order_status" name="shipment_data[order_status]">
			<option value="">{$lang.do_not_change}</option>
			{foreach from=$smarty.const.STATUSES_ORDER|fn_get_statuses:true key="key" item="status"}
				<option value="{$key}">{$status}</option>
			{/foreach}
		</select>
		<p class="description">
			{$lang.text_order_status_notification}
		</p>
	</div>
</fieldset>

<div class="cm-toggle-button">
	<div class="select-field notify-customer">
		<input type="checkbox" name="notify_user" id="shipment_notify_user" value="Y" class="checkbox" />
		<label for="shipment_notify_user">{$lang.send_shipment_notification_to_customer}</label>
	</div>
</div>


</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[shipments.add]" cancel_action="close"}
</div>

</form>
