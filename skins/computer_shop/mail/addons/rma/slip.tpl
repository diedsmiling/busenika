{* $Id: slip.tpl 9845 2010-06-23 10:59:32Z lexa $ *}

{if $order_info}
{literal}
<style type="text/css" media="screen,print">
body,p,div {
	color: #000000;
	font: 12px Arial;
}
body {
	background-color: #f4f6f8;
	padding-top: 24px;
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
body, .main-table {
	background-color: #ffffff !important;
}
</style>
{/literal}

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f6f8;">
<tr>
	<td width="100%" align="center">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td style="background-color: #ffffff; border: 1px solid #e6e6e6; margin: 0px auto; padding: 0px 40px 40px 40px; width: 510px;" align="left">
				<h1 style="text-align: right; font: bold 26px Arial; text-transform: uppercase; border-bottom: 1px solid #868686; padding: 10px 0px 8px 0px; margin-bottom: 0px;">
					<div style="float: left;"><img src="{$images_dir}/{$manifest.Mail_logo.filename}" width="{$manifest.Mail_logo.width}" height="{$manifest.Mail_logo.height}" border="0" alt="{$manifest.Mail_logo.alt}" /></div>
					{$lang.packing_slip}
				</h1>
			
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="top">
					<td style="width: 50%; padding: 14px 0px 0px 2px;">
						<h2 style="font: bold 12px Arial; margin: 0px 0px 3px 0px;">{$settings.Company.company_name}</h2>
						{$settings.Company.company_address}<br />
						{$settings.Company.company_city} {$settings.Company.company_state} {$settings.Company.company_zipcode}<br />
						{$settings.Company.company_country_descr}
						<table cellpadding="0" cellspacing="0" border="0">
						{if $settings.Company.company_phone}
						<tr>
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.phone1_label}:</td>
							<td width="100%">{$settings.Company.company_phone}</td>
						</tr>				
						{/if}		
						{if $settings.Company.company_phone_2}
						<tr>
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.phone2_label}:</td>
							<td width="100%">{$settings.Company.company_phone_2}</td>
						</tr>				
						{/if}
						{if $settings.Company.company_fax}
						<tr>
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.fax}:</td>
							<td width="100%">{$settings.Company.company_fax}</td>
						</tr>
						{/if}
						{if $settings.Company.company_website}
						<tr>
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.web_site}:</td>
							<td width="100%">{$settings.Company.company_website}</td>
						</tr>
						{/if}
						{if $settings.Company.company_orders_department}
						<tr>
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.email}:</td>
							<td width="100%">{$settings.Company.company_orders_department}</td>
						</tr>
						{/if}		
						</table>
					</td>
					<td style="padding-top: 14px;">
						<h2 style="font: bold 17px Tahoma; margin: 0px;">{$lang.rma_return}&nbsp;#{$return_info.return_id}</h2>
						<table cellpadding="0" cellspacing="0" border="0">
						<tr valign="top">
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.action}:</td>
							<td width="100%">{assign var="action_id" value=$return_info.action}{$actions.$action_id.property}</td>
						</tr>
						<tr valign="top">
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.status}:</td>
							<td width="100%">{include file="common_templates/status.tpl" status=$return_info.status display="view" type=$smarty.const.STATUSES_RETURN}</td>
						</tr>
						<tr valign="top">
							<td style="font: 12px verdana, helvetica, arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">{$lang.date}:</td>
							<td>{$return_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			
				{* Shipping info *}
				{assign var="profile_fields" value='I'|fn_get_profile_fields}
				{split data=$profile_fields.C size=2 assign="contact_fields" simple=true}
				{if $profile_fields}
				<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 32px 0px 24px 0px;">
				<tr valign="top">
					{if $profile_fields.C}
					<td width="33%">
						<h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{$lang.customer}:</h3>
						<p style="margin: 2px 0px 3px 0px;">{$order_info.firstname}&nbsp;{$order_info.lastname}</p>
						<p style="margin: 2px 0px 3px 0px;"><a href="mailto:{$order_info.email|escape:url}">{$order_info.email}</a></p>
						<p style="margin: 2px 0px 3px 0px;"><span style="text-transform: uppercase;">{$lang.phone}:</span>&nbsp;{$order_info.phone}</p>
						{include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.C}
					</td>
					{/if}
					{if $profile_fields.B}
					<td width="34%" style="{if $profile_fields.S}padding-right: 10px;{/if} {if $profile_fields.C}padding-left: 10px;{/if}">
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
					<td width="33%">
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
				{* /Shipping info *}
				
				{* Declined products *}
				<table cellpadding="0" cellspacing="1" border="0" width="100%" style="background-color: #dddddd;">
				<tr>
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.sku}</th>
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.product}</th>
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.price}</th>
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.amount}</th>
					<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;">{$lang.reason}</th>
				</tr>
				{if $return_info.items[$smarty.const.RETURN_PRODUCT_ACCEPTED]}
				{foreach from=$return_info.items[$smarty.const.RETURN_PRODUCT_ACCEPTED] item="ri" key="key"}
				<tr>
					<td style="padding: 5px 10px; background-color: #ffffff;">{$order_info.items.$key.product_code|default:"&nbsp;"}</td>
					<td style="padding: 5px 10px; background-color: #ffffff;">{$ri.product}
						{if $ri.product_options}<div style="padding-top: 1px; padding-bottom: 2px;">{include file="common_templates/options_info.tpl" product_options=$ri.product_options}</div>{/if}</td>
					<td align="center" style="padding: 5px 10px; background-color: #ffffff;">{if !$ri.price}{$lang.free}{else}{include file="common_templates/price.tpl" value=$ri.price}{/if}</td>		
					<td align="center" style="padding: 5px 10px; background-color: #ffffff;">{$ri.amount}</td>
					<td align="center" style="padding: 5px 10px; background-color: #ffffff;">
						{assign var="reason_id" value=$ri.reason}
						&nbsp;{$reasons.$reason_id.property}&nbsp;</td>
				</tr>
				{/foreach}
				{else}
					<tr>
						<td colspan="6" align="center" style="padding: 5px 10px; background-color: #ffffff;"><p style="margin: 2px 0px 3px 0px;"><b>{$lang.text_no_products_found}</b></p></td>
					</tr>	
				{/if}
				</table>
				{* /Declined products *}
			
				{if $return_info.comment}
					<p style="margin-top: 15px; font-weight: bold;">{$lang.comments}:</p>
					<div style="padding-left: 7px; padding-bottom: 15px; overflow-x: auto; overflow-y: hidden; clear: both; width: 505px;">{$return_info.comment|nl2br}</div>
				{/if}
				
				{if $content == 'invoice'}
				<br />
				<table cellpadding="0" cellspacing="0" border="0" width="620" align="center">
				<tr>
					<td>
						{include file="buttons/continue_shopping.tpl" but_href=$continue_url|default:$index_script but_arrow="on" skin_area="customer"}</td>
					<td align="right">
						{include file="buttons/button_popup.tpl" but_text=$lang.print_invoice but_href="orders.print_invoice?order_id=`$order_info.order_id`" width="800" height="600"  skin_area="customer"}</td>
				</tr>
				</table>	
				{/if}
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
{/if}