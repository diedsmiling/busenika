{* $Id: invoice.tpl 10436 2010-08-17 11:58:43Z angel $ *}

{if $order_info}

{assign var="order_header" value=$lang.invoice}
{if $status_settings.appearance_type == "I" && $order_info.doc_ids[$status_settings.appearance_type]}
	{assign var="doc_id_text" value="`$lang.invoice` #`$order_info.doc_ids[$status_settings.appearance_type]`"}
{elseif $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
	{assign var="doc_id_text" value="`$lang.credit_memo` #`$order_info.doc_ids[$status_settings.appearance_type]`"}
	{assign var="order_header" value=$lang.credit_memo}
{elseif $status_settings.appearance_type == "O"}
	{assign var="order_header" value=$lang.order_details}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="main-table" style="height: 100%; background-color: #f4f6f8; font-size: 12px; font-family: Arial;">
<tr>
	<td align="center" style="width: 100%; height: 100%;">
	<table cellpadding="0" cellspacing="0" border="0" style=" width: 602px; table-layout: fixed; margin: 24px 0 24px 0;">
	<tr>
		<td style="background-color: #ffffff; border: 1px solid #e6e6e6; margin: 0px auto 0px auto; padding: 0px 44px 0px 46px; text-align: left;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 27px 0px 0px 0px; border-bottom: 1px solid #868686; margin-bottom: 8px;">
			<tr>
				<td align="left" style="padding-bottom: 3px;" valign="middle"><img src="{if $manifest.Mail_logo.vendor}{$config.images_path}{else}{$images_dir}/{/if}{$manifest.Mail_logo.filename}" width="{$manifest.Mail_logo.width}" height="{$manifest.Mail_logo.height}" border="0" alt="{$manifest.Mail_logo.alt}" /></td>
				<td width="100%" valign="bottom" style="text-align: right;  font: bold 26px Arial; text-transform: uppercase;  margin: 0px;">{$order_header|default:$lang.invoice_title}</td>
			</tr>
			</table>
		
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr valign="top">
				<td style="width: 50%; padding: 14px 0px 0px 2px; font-size: 12px; font-family: Arial;">
					<h2 style="font: bold 12px Arial; margin: 0px 0px 3px 0px;">{$settings.Company.company_name}</h2>
					{$settings.Company.company_address}<br />
					{$settings.Company.company_city} {$settings.Company.company_state_descr} {$settings.Company.company_zipcode}<br />
					{$settings.Company.company_country_descr}
					<table cellpadding="0" cellspacing="0" border="0">
					{if $settings.Company.company_phone}
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px;	white-space: nowrap;">{$lang.phone1_label}:</td>
						<td width="100%" style="font-size: 12px; font-family: Arial;">{$settings.Company.company_phone}</td>
					</tr>
					{/if}
					{if $settings.Company.company_phone_2}
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.phone2_label}:</td>
						<td width="100%" style="font-size: 12px; font-family: Arial;">{$settings.Company.company_phone_2}</td>
					</tr>
					{/if}
					{if $settings.Company.company_fax}
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.fax}:</td>
						<td width="100%" style="font-size: 12px; font-family: Arial;">{$settings.Company.company_fax}</td>
					</tr>
					{/if}
					{if $settings.Company.company_website}
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.web_site}:</td>
						<td width="100%" style="font-size: 12px; font-family: Arial;">{$settings.Company.company_website}</td>
					</tr>
					{/if}
					{if $settings.Company.company_orders_department}
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.email}:</td>
						<td width="100%" style="font-size: 12px; font-family: Arial;"><a href="mailto:{$settings.Company.company_orders_department}">{$settings.Company.company_orders_department}</a></td>
					</tr>
					{/if}
					</table>
				</td>
				
				<td style="padding-top: 14px;">
					<h2 style="font: bold 17px Tahoma; margin: 0px;">{if $doc_id_text}{$doc_id_text} <br />{/if}{$lang.order}&nbsp;#{$order_info.order_id}</h2>
					<table cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.status}:</td>
						<td width="100%" style="font-size: 12px; font-family: Arial;">{include file="common_templates/status.tpl" status=$order_info.status display="view"}</td>
					</tr>
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.date}:</td>
						<td style="font-size: 12px; font-family: Arial;">{$order_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
					</tr>
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.payment_method}:</td>
						<td style="font-size: 12px; font-family: Arial;">{$order_info.payment_method.payment}</td>
					</tr>
					{if $order_info.shipping}
					<tr valign="top">
						<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.shipping_method}:</td>
						<td style="font-size: 12px; font-family: Arial;">
							{foreach from=$order_info.shipping item="shipping" name="f_shipp"}
								{$shipping.shipping}{if !$smarty.foreach.f_shipp.last}, {/if}
								{if $shipping.tracking_number}{assign var="tracking_number_exists" value="Y"}{/if}
							{/foreach}</td>
					</tr>
					{if $tracking_number_exists}
						<tr valign="top">
							<td style="font-size: 12px; font-family: verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.tracking_number}:</td>
							<td style="font-size: 12px; font-family: Arial;">
								{foreach from=$order_info.shipping item="shipping" name="f_shipp"}
									{if $shipping.tracking_number}{$shipping.tracking_number}{if !$smarty.foreach.f_shipp.last},{/if}{/if}
								{/foreach}</td>
						</tr>
					{/if}
					{/if}
					</table>
				</td>
			</tr>
			</table>
		
			{assign var="profile_fields" value='I'|fn_get_profile_fields}
			{if $profile_fields}
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 32px 0px 24px 0px;">
			<tr valign="top">
				{if $profile_fields.C}
				{assign var="profields_c" value=$profile_fields.C|fn_fields_from_multi_level:"field_name":"field_id"}
				<td width="33%" style="font-size: 12px; font-family: Arial;">
					<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.customer}:</h3>
					<p style="margin: 2px 0px 3px 0px;">{if $profields_c.firstname}{$order_info.firstname}&nbsp;{/if}{if $profields_c.lastname}{$order_info.lastname}{/if}</p>
					{if $profields_c.email}<p style="margin: 2px 0px 3px 0px;"><a href="mailto:{$order_info.email|escape:url}">{$order_info.email}</a></p>{/if}
					{if $profields_c.phone}<p style="margin: 2px 0px 3px 0px;"><span style="text-transform: uppercase;">{$lang.phone}:</span>&nbsp;{$order_info.phone}</p>{/if}
					{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.C}
				</td>
				{/if}
				{if $profile_fields.B}
				{assign var="profields_b" value=$profile_fields.B|fn_fields_from_multi_level:"field_name":"field_id"}
				<td width="34%" style="font-size: 12px; font-family: Arial; {if $profile_fields.S}padding-right: 10px;{/if} {if $profile_fields.C}padding-left: 10px;{/if}">
					<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.bill_to}:</h3>
					{if $order_info.b_firstname && $profields_b.b_firstname || $order_info.b_lastname && $profields_b.b_lastname}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_b.b_firstname}{$order_info.b_firstname} {/if}{if $profields_b.b_lastname}{$order_info.b_lastname}{/if}
					</p>
					{/if}
					{if $order_info.b_address && $profields_b.b_address || $order_info.b_address_2 && $profields_b.b_address_2}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_b.b_address}{$order_info.b_address} {/if}{if $profields_b.b_address_2}<br />{$order_info.b_address_2}{/if}
					</p>
					{/if}
					{if $order_info.b_city && $profields_b.b_city || $order_info.b_state_descr && $profields_b.b_state || $order_info.b_zipcode && $profields_b.b_zipcode}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_b.b_city}{$order_info.b_city} {/if}{if $profields_b.b_state}{$order_info.b_state_descr} {/if}{if $profields_b.b_zipcode}{$order_info.b_zipcode}{/if}
					</p>
					{/if}
					{if $order_info.b_country_descr && $profields_b.b_country}
					<p style="margin: 2px 0px 3px 0px;">
						{$order_info.b_country_descr}
					</p>
					{/if}
					{if $order_info.b_phone && $profields_b.b_phone}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_b.b_phone}{$order_info.b_phone} {/if}
					</p>
					{/if}
					{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.B}
				</td>
				{/if}
				{if $profile_fields.S}
				{assign var="profields_s" value=$profile_fields.S|fn_fields_from_multi_level:"field_name":"field_id"}
				<td width="33%" style="font-size: 12px; font-family: Arial;">
					<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.ship_to}:</h3>
					{if $order_info.s_firstname && $profields_s.s_firstname || $order_info.s_lastname && $profields_s.s_lastname}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_s.s_firstname}{$order_info.s_firstname} {/if}{if $profields_s.s_lastname}{$order_info.s_lastname}{/if}
					</p>
					{/if}
					{if $order_info.s_address && $profields_s.s_address || $order_info.s_address_2 && $profields_s.s_address_2}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_s.s_address}{$order_info.s_address} {/if}{if $profields_s.s_address_2}<br />{$order_info.s_address_2}{/if}
					</p>
					{/if}
					{if $order_info.s_city && $profields_s.s_city || $order_info.s_state_descr && $profields_s.s_state || $order_info.s_zipcode && $profields_s.s_zipcode}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_s.s_city}{$order_info.s_city} {/if}{if $profields_s.s_state}{$order_info.s_state_descr} {/if}{if $profields_s.s_zipcode}{$order_info.s_zipcode}{/if}
					</p>
					{/if}
					{if $order_info.s_country_descr && $profields_s.s_country}
					<p style="margin: 2px 0px 3px 0px;">
						{$order_info.s_country_descr}
					</p>
					{/if}
					{if $order_info.s_phone && $profields_s.s_phone}
					<p style="margin: 2px 0px 3px 0px;">
						{if $profields_s.s_phone}{$order_info.s_phone} {/if}
					</p>
					{/if}
					{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.S}
				</td>
				{/if}
			</tr>
			</table>
			{/if}
			{* Customer info *}
		
		
			{* Ordered products *}
		
			<table width="100%" cellpadding="0" cellspacing="1" style="background-color: #dddddd;">
			<tr>
				<th width="70%" style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.product}</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.quantity}</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.unit_price}</th>
				{if $order_info.use_discount}
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.discount}</th>
				{/if}
				{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.tax}</th>
				{/if}
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.subtotal}</th>
			</tr>
			{foreach from=$order_info.items item="oi"}
			{hook name="orders:items_list_row"}
				{if !$oi.extra.parent}
				<tr>
					<td style="padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
						{$oi.product|unescape|default:$lang.deleted_product}
						{hook name="orders:product_info"}
						{if $oi.product_code}<p style="margin: 2px 0px 3px 0px;">{$lang.code}: {$oi.product_code}</p>{/if}
						{/hook}
						{if $oi.product_options}<br/>{include file="common_templates/options_info.tpl" product_options=$oi.product_options}{/if}
						{if $settings.Suppliers.enable_suppliers == "Y" && $oi.company_id && $settings.Suppliers.display_supplier == "Y"}
							<p style="margin: 2px 0px 3px 0px;">{$lang.supplier}: {$s_companies[$oi.company_id].company}</p>
						{/if}
					</td>
					<td style="padding: 5px 10px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;">{$oi.amount}</td>
					<td style="padding: 5px 10px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.original_price}{/if}</td>
					{if $order_info.use_discount}
					<td style="padding: 5px 10px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.extra.discount|floatval}{include file="common_templates/price.tpl" value=$oi.extra.discount}{else}&nbsp;-&nbsp;{/if}</td>
					{/if}
					{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
						<td style="padding: 5px 10px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.tax_value}{include file="common_templates/price.tpl" value=$oi.tax_value}{else}&nbsp;-&nbsp;{/if}</td>
					{/if}
		
					<td style="padding: 5px 10px; background-color: #ffffff; text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.display_subtotal}{/if}</b>&nbsp;</td>
				</tr>
				{/if}
			{/hook}
			{/foreach}
			{hook name="orders:extra_list"}
			{/hook}
			</table>
		
			{hook name="orders:ordered_products"}
			{/hook}
			{* /Ordered products *}
		
			{* Order totals *}
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td align="right">
				<table border="0" style="padding: 3px 0px 12px 0px;">
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.subtotal}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.display_subtotal}</td>
				</tr>
				{if $order_info.discount|floatval}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.including_discount}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">
						{include file="common_templates/price.tpl" value=$order_info.discount}</td>
				</tr>
				{/if}

			
				{if $order_info.subtotal_discount|floatval}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.order_discount}:</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">
						{include file="common_templates/price.tpl" value=$order_info.subtotal_discount}</td>
				</tr>
				{/if}

				{if $order_info.coupons}
				{foreach from=$order_info.coupons item="coupon" key="key"}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.coupon}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$key}</td>
				</tr>
				{/foreach}
				{/if}
				{if $order_info.taxes}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.taxes}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">&nbsp;</td>
				</tr>
				{foreach from=$order_info.taxes item=tax_data}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$tax_data.description}&nbsp;{include file="common_templates/modifier.tpl" mod_value=$tax_data.rate_value mod_type=$tax_data.rate_type}{if $tax_data.price_includes_tax == "Y" && ($settings.Appearance.cart_prices_w_taxes != "Y" || $settings.General.tax_calculation == "subtotal")}&nbsp;{$lang.included}{/if}{if $tax_data.regnumber}&nbsp;({$tax_data.regnumber}){/if}:&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$tax_data.tax_subtotal}</td>
				</tr>
				{/foreach}
				{/if}
				{if $order_info.tax_exempt == 'Y'}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.tax_exempt}</b></td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">&nbsp;</td>
				</tr>
				{/if}
			
				{if $order_info.payment_surcharge|floatval}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.payment_surcharge}:&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{include file="common_templates/price.tpl" value=$order_info.payment_surcharge}</b></td>
				</tr>
				{/if}
			
			
				{if $order_info.shipping}
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.shipping_cost}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.display_shipping_cost}</td>
				</tr>
				{/if}
				{hook name="orders:totals"}
				{/hook}
				
				<tr>
					<td colspan="2"><hr style="border: 0px solid #d5d5d5; border-top-width: 1px;" /></td>
				</tr>
				<tr>
					<td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;">{$lang.total_cost}:&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;"><strong style="font: bold 17px Tahoma;">{include file="common_templates/price.tpl" value=$order_info.total}</strong></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
		
			{* /Order totals *}
		
			{if $order_info.notes}
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr valign="top">
				<td style="font-size: 12px; font-family: Arial;"><strong>{$lang.notes}:</strong></td>
				<td width="100%"><div style="padding-left: 7px; padding-bottom: 15px; overflow-x: auto; clear: both; width: 464px; height: 100%; padding-bottom: 20px; overflow-y: hidden; font-size: 12px; font-family: Arial;">{$order_info.notes|wordwrap:85:"\n":false|nl2br}</div></td>
			</tr>
			</table>
			{/if}
		
			{if $content == "invoice"}
			<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
			<tr>
				<td>
					{include file="buttons/continue_shopping.tpl" but_href=$continue_url|default:$index_script but_arrow="on" skin_area="customer"}</td>
				<td align="right">
					{include file="buttons/button_popup.tpl" but_text=$lang.print_invoice but_href="orders.print_invoice?order_id=`$order_info.order_id`" width="800" height="600"  skin_area="customer"}</td>
			</tr>
			</table>	
			{/if}
		{/if}
		
		{hook name="orders:invoice"}
		{/hook}
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
