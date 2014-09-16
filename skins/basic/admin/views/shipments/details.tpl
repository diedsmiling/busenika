{* $Id: details.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

{capture name="mainbox"}

{capture name="extra_tools"}
	{hook name="shipments:details_tools"}
	{include file="buttons/button.tpl" but_text="`$lang.order`&nbsp;#`$order_info.order_id`" but_href="orders.details?order_id=`$order_info.order_id`" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button_popup.tpl" but_text=$lang.print_packing_slip but_href="shipments.packing_slip?shipment_ids[]=`$shipment.shipment_id`" width="900" height="600" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button.tpl" but_text=$lang.delete but_href="shipments.delete?shipment_ids[]=`$shipment.shipment_id`" but_role="tool"}
	{/hook}
{/capture}

{capture name="tabsbox"}

<div id="content_general">

	<div class="item-summary clear center">
		<div class="float-left">
		{$lang.shipment}&nbsp;&nbsp;<span>#{$shipment.shipment_id}</span>&nbsp;
		{$lang.on}&nbsp;{$order_info.timestamp|date_format:"`$settings.Appearance.date_format`"}&nbsp;
		{$lang.by}&nbsp;<strong>{$shipment.shipping}</strong>{if $shipment.tracking_number}&nbsp;({$shipment.tracking_number}){/if}{if $shipment.carrier}&nbsp;({$shipment.carrier}){/if}
		</div>
		
		{hook name="shipments:customer_shot_info"}
		{/hook}
	</div>
	
	{* Customer info *}
	{include file="views/profiles/components/profiles_info.tpl" user_data=$order_info location="I"}
	{* /Customer info *}

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th>{$lang.product}</th>
		<th width="5%">{$lang.quantity}</th>
	</tr>
	{foreach from=$order_info.items item="oi" key="key"}
	{if $oi.amount > 0}
	<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
		<td>
			{if !$oi.deleted_product}<a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{/if}{$oi.product|unescape}{if !$oi.deleted_product}</a>{/if}
			{hook name="shipments:product_info"}
			{if $oi.product_code}</p>{$lang.sku}:&nbsp;{$oi.product_code}</p>{/if}
			{/hook}
			{if $oi.product_options}<div class="options-info">{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}
		</td>
		<td class="center">
			&nbsp;{$oi.amount}<br />
		</td>
	</tr>
	{/if}
	{/foreach}
	</table>

	{* text_no_items_found*}

	<div class="clear order-notes">
		<div class="float-left">
			<h3><label for="notes">{$lang.comments}:</label></h3>
			<textarea class="input-textarea" cols="40" rows="5" readonly="readonly">{$shipment.comments}</textarea>
		</div>
	</div>
	
<!--content_general--></div>
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

{/capture}
{capture name="mainbox_title"}
	{$lang.shipment_details}
{/capture}

{include file="common_templates/view_tools.tpl" url="shipments.details?shipment_id="}

{include file="common_templates/mainbox.tpl" title=$smarty.capture.mainbox_title content=$smarty.capture.mainbox tools=$smarty.capture.view_tools extra_tools=$smarty.capture.extra_tools}