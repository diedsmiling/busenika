{* $Id: shipment_products.tpl 8441 2009-12-22 13:12:13Z alexions $ *}

{include file="letter_header.tpl"}

{$lang.dear} {$order_info.firstname},<br /><br />

{$lang.products_were_sent}<br /><br />

<strong>{$lang.order_id}</strong>:&nbsp;#{$order_info.order_id}<br />
<strong>{$lang.shipping_method}</strong>:&nbsp;{$shipment.shipping}<br />
<strong>{$lang.shipment_date}</strong>:&nbsp;{$shipment.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}<br />
{if $shipment.carrier}
	<strong>{$lang.carrier}</strong>:&nbsp;{$shipment.carrier}<br />
{/if}
{if $shipment.tracking_number}
	<strong>{$lang.tracking_number}</strong>:&nbsp;{$shipment.tracking_number}<br /><br />
{/if}

<strong>{$lang.products}:</strong>
<p>
{foreach from=$shipment.items key="hash" item="amount"}
	{if $amount > 0}
		{$amount}&nbsp;x&nbsp;{$order_info.items.$hash.product}<br />
		{if $order_info.items.$hash.product_options}
			{include file="common_templates/options_info.tpl" product_options=$order_info.items.$hash.product_options}<br />
		{/if}
		<br />
	{/if}
{/foreach}
</p>

{if $shipment.comments}
<br /><br />
<strong>{$lang.comments}</strong>:
{$shipment.comments}
{/if}

{include file="letter_footer.tpl"}