{* $Id: print_packing_slip.tpl 7354 2009-04-24 14:00:06Z alexions $ *}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head></head>

<body>
HEY
{if $order_info}
{literal}
<style type="text/css" media="screen,print">
body,p,div {
	color: #000000;
	font: 12px Arial;
}
body {
	padding: 0;
	margin: 0;
}
a, a:link, a:visited, a:hover, a:active {
	color: #000000;
	text-decoration: underline;
}
a:hover {
	text-decoration: none;
}
</style>
<style media="print">
body {
	background-color: #ffffff;
}
.scissors {
	display: none;
}
</style>
{/literal}

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f6f8; height: 100%;">
<tr>
	<td align="center" style="width: 100%; height: 100%; padding: 24px 0;">
	<div style="background-color: #ffffff; border: 1px solid #e6e6e6; margin: 0px auto; padding: 0px 44px 0px 46px; width: 510px; text-align: left;">
		{assign var="profile_fields" value='I'|fn_get_profile_fields}
		{split data=$profile_fields.C size=2 assign="contact_fields" simple=true}
		{if $profile_fields.S}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding-top: 32px;">
		<tr valign="top">
			<td width="100%" align="center" style="border-bottom: 1px dashed #000000; padding-bottom: 20px;">
				<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.ship_to}:</h3>
				{if $order_info.s_firstname || $order_info.s_lastname}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_firstname} {$order_info.s_lastname}
				</p>
				{/if}
				{if $order_info.s_address || $order_info.s_address_2}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_address} {$order_info.s_address_2}
				</p>
				{/if}
				{if $order_info.s_city || $order_info.s_state_descr || $order_info.s_zipcode}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_city} {$order_info.s_state_descr} {$order_info.s_zipcode}
				</p>
				{/if}
				{if $order_info.s_country_descr}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_country_descr}
				</p>
				{/if}
				{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.S}
			</td>
		</tr>
		<tr valign="top" class="scissors">
			<td width="100%" style="padding-left: 20px;">
				<img src="{$images_dir}/scissors.gif" border="0" />
			</td>
		</tr>
		</table>
		
		{/if}
		{* Customer info *}
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td style="width: 50%; padding: 14px 0px 0px 2px;">
				<h2 style="font: bold 12px Arial; margin: 0px 0px 3px 0px;">HEY HEY HEY{$settings.Company.company_name}</h2>
				{$settings.Company.company_address}<br />
				{$settings.Company.company_city} {$settings.Company.company_state_descr} {$settings.Company.company_zipcode}<br />
				{$settings.Company.company_country_descr}
				<table cellpadding="0" cellspacing="0" border="0">
				{if $settings.Company.company_phone}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px;	white-space: nowrap;">{$lang.phone1_label}:</td>
					<td width="100%">{$settings.Company.company_phone}</td>
				</tr>
				{/if}
				{if $settings.Company.company_phone_2}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.phone2_label}:</td>
					<td width="100%">{$settings.Company.company_phone_2}</td>
				</tr>
				{/if}
				{if $settings.Company.company_fax}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.fax}:</td>
					<td width="100%">{$settings.Company.company_fax}</td>
				</tr>
				{/if}
				{if $settings.Company.company_website}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.web_site}:</td>
					<td width="100%">{$settings.Company.company_website}</td>
				</tr>
				{/if}
				{if $settings.Company.company_orders_department}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.email}:</td>
					<td width="100%"><a href="mailto:{$settings.Company.company_orders_department}">{$settings.Company.company_orders_department}</a></td>
				</tr>
				{/if}
				</table>
			</td>
			
			<td style="padding-top: 14px;" valign="top">
				<h2 style="font: bold 17px Tahoma; margin: 0px;">{$lang.packing_slip_for_order}&nbsp;#{$order_info.order_id}</h2>
				<table cellpadding="0" cellspacing="0" border="0">
				{if $settings.General.use_shipments == "Y" && $shipment}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.order_date}:</td>
					<td>{$order_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
				</tr>
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.shipment_date}:</td>
					<td>{$shipment.shipment_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
				</tr>
				{else}
				<tr valign="top">
					<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.date}:</td>
					<td>{$order_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
				</tr>
				{/if}
				</table>
			</td>
		</tr>
		</table>
		
		{if $profile_fields}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 20px 0px 24px 0px;">
		<tr valign="top">
			{if $profile_fields.B}
			<td width="54%">
				<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.bill_to}:</h3>
				{if $order_info.b_firstname || $order_info.b_lastname}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.b_firstname} {$order_info.b_lastname}
				</p>
				{/if}
				{if $order_info.b_address || $order_info.b_address_2}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.b_address} {$order_info.b_address_2}
				</p>
				{/if}
				{if $order_info.b_city || $order_info.b_state_descr || $order_info.b_zipcode}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.b_city} {$order_info.b_state_descr} {$order_info.b_zipcode}
				</p>
				{/if}
				{if $order_info.b_country_descr}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.b_country_descr}
				</p>
				{/if}
				{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.B}
			</td>
			{/if}
			{if $profile_fields.S}
			<td width="54%">
				<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.ship_to}:</h3>
				{if $order_info.s_firstname || $order_info.s_lastname}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_firstname} {$order_info.s_lastname}
				</p>
				{/if}
				{if $order_info.s_address || $order_info.s_address_2}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_address} {$order_info.s_address_2}
				</p>
				{/if}
				{if $order_info.s_city || $order_info.s_state_descr || $order_info.s_zipcode}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_city} {$order_info.s_state_descr} {$order_info.s_zipcode}
				</p>
				{/if}
				{if $order_info.s_country_descr}
				<p style="margin: 2px 0px 3px 0px;">
					{$order_info.s_country_descr}
				</p>
				{/if}
				{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.S}
			</td>
			{/if}
		</tr>
		</table>
		{/if}
		{* Customer info *}
		
		<table cellpadding="0" cellspacing="0" border="0">
		<tr valign="top">
			<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.status}:</td>
			<td width="100%">{include file="common_templates/status.tpl" status=$order_info.status display="view"}</td>
		</tr>
		<tr valign="top">
			<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.payment_method}:</td>
			<td valign="bottom">{$order_info.payment_method.payment}</td>
		</tr>
		{if $order_info.shipping || $shipment}
		<tr valign="top">
			<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.shipping_method}:</td>
			<td valign="bottom">
				{if !$shipment}
					{foreach from=$order_info.shipping item="shipping" name="f_shipp"}
						{$shipping.shipping}
						{if !$smarty.foreach.f_shipp.last}, {/if}
						{if $shipping.tracking_number}{assign var="tracking_number_exists" value="Y"}{/if}
					{/foreach}
				{else}
					{$shipment.shipping}
					{if $shipment.tracking_number}
					&nbsp;({$lang.tracking_number}: {$shipment.tracking_number})
					{/if}
				{/if}
			</td>
		</tr>
		{if $tracking_number_exists}
			<tr valign="top">
				<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.tracking_number}:</td>
				<td valign="bottom">
					{foreach from=$order_info.shipping item="shipping" name="f_shipp"}
						{if $shipping.tracking_number}{$shipping.tracking_number}
							{if !$smarty.foreach.f_shipp.last},{/if}
						{/if}
					{/foreach}</td>
			</tr>
		{/if}
		{/if}
		</table>
		
		{* Ordered products *}
	
		<table width="100%" cellpadding="0" cellspacing="1" style="background-color: #dddddd; margin-top: 20px;">
		<tr>
			<th width="70%" style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.product}</th>
			<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.product_code}</th>
			<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.quantity}</th>
		</tr>
		{foreach from=$order_info.items item="oi"}
			{if $oi.amount > 0}
			<tr>
				<td style="padding: 5px 10px; background-color: #ffffff;">
					{$oi.product|unescape|default:$lang.deleted_product}
					{if $oi.product_options}<br/>{include file="common_templates/options_info.tpl" product_options=$oi.product_options skip_modifiers=true}{/if}
				</td>
				<td style="padding: 5px 10px; background-color: #ffffff; text-align: left;">{$oi.product_code}</td>
				<td style="padding: 5px 10px; background-color: #ffffff; text-align: center;">{$oi.amount}</td>
			</tr>
			{/if}
		{/foreach}
		</table>
		
		{* /Ordered products *}
		
		{if $settings.General.use_shipments != "Y" && $shipment}
		{if $order_info.notes}
			<div style="float: left; padding-top: 20px;"><strong>{$lang.notes}:</strong></div>
			<div style="padding-left: 7px; padding-bottom: 15px; overflow-x: auto; clear: both; width: 505px; height: 100%; padding-bottom: 20px; overflow-y: hidden;">{$order_info.notes|wordwrap:90:"\n":true|nl2br}</div>
		{/if}
		{else}
		{if $shipment.comments}
			<div style="float: left; padding-top: 20px;"><strong>{$lang.comments}:</strong></div>
			<div style="padding-left: 7px; padding-bottom: 15px; overflow-x: auto; clear: both; width: 505px; height: 100%; padding-bottom: 20px; overflow-y: hidden;">{$shipment.comments|wordwrap:90:"\n":true|nl2br}</div>
		{/if}
		{/if}
		
		{hook name="orders:invoice"}
		{/hook}
	</div>
	</td>
</tr>
</table>
{/if}

</body>
</html>
