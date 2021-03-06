{* $Id: invoice.tpl 10436 2010-08-17 11:58:43Z angel $ *}



{assign var="order_header" value=$lang.invoice}
{if $status_settings.appearance_type == "I" && $order_info.doc_ids[$status_settings.appearance_type]}
	{assign var="doc_id_text" value="`$lang.invoice` #`$order_info.doc_ids[$status_settings.appearance_type]`"}
{elseif $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
	{assign var="doc_id_text" value="`$lang.credit_memo` #`$order_info.doc_ids[$status_settings.appearance_type]`"}
	{assign var="order_header" value=$lang.credit_memo}
{elseif $status_settings.appearance_type == "O"}
	{assign var="order_header" value=$lang.order_details}
{/if}

<table class="main-table" width="100%" style="background-color: #f4f6f8; font-size: 12px; font-family: Arial;" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" valign="top">
		
		<table width="602" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: #333333 1px solid;">
			<tr>
				<td width="370" height="80" style="border-bottom:solid #868686 1.0pt;"><img src="http://korzin.net/images/korzin_net_logo.jpg" border="0"/></td>
				<td align="right" valign="bottom" style="border-bottom:solid #868686 1.0pt;">
				
				<table width="92%" border="0" cellpadding="0" cellspacing="0" style="margin-right:8px; margin-bottom:6.75pt;">
					<tr>
						<td width="33%"><b>{$lang.call_center}</b>:</td>
						<td>+7 (495) 943-55-04</td>
					</tr>
					<tr>
						<td colspan="2">{$lang.working_time}</td>
					</tr>
					<tr>
						<td width="33%"><b>{$lang.web_site}</b>:</td>
						<td>{$settings.Company.company_website}</td>
					</tr>					
					<tr>
						<td><b>e-mail:</b></td>
						<td>kz@korzin.net</td>
					</tr>
				</table>
				
			  	</td>
			</tr>
			<tr>
				<td height="80" colspan="2">
				
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>
  
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left:6.75pt; margin-bottom:6.75pt;">
						<tr>
						<td height="50" colspan="2"><b><span style="font-size:13.0pt;font-family:Tahoma; margin-bottom:10px; margin-top:10px;">{$lang.order}&nbsp;#{$order_info.order_id}</span></b></p></td>
						</tr>
						
						<tr>
						<td align="left">Дата доставки: {$order_info.date_to_delivery|date_format:"`$settings.Appearance.date_format`"}</td>
						<td align="left">Время доставки: {$order_info.time_to_delivery}</td>
						</tr>						
						<tr>
						<td align="left">{$lang.payment_method}: {$order_info.payment_method.payment}</td>
						<td align="left">{$lang.shipping_method}: 				{foreach from=$order_info.shipping item="shipping" name="f_shipp"}
													{$shipping.shipping}{if !$smarty.foreach.f_shipp.last}, {/if}
													{if $shipping.tracking_number}{assign var="tracking_number_exists" value="Y"}{/if}
												{/foreach}</td>
						</tr>
						</table>
						
						
						</td>
					</tr>
				</table>
				
				</td>
			</tr>
			<tr>
				<td colspan="2">
				{assign var="profile_fields" value='I'|fn_get_profile_fields}
				{assign var="profields_s" value=$profile_fields.S|fn_fields_from_multi_level:"field_name":"field_id"}				
				<table height="80" border="0" cellpadding="0" cellspacing="0" style="margin-left:6.75pt; margin-bottom:6.75pt;">
					<tr>
						<td width="222" align="left"><b>{$lang.customer}:</b></td>
						<td width="734" align="left">{$order_info.firstname}&nbsp;{$order_info.lastname}</td>
					</tr>
					<tr>
					  <td align="left"><b>Метро:</b></td>
						<td align="left">{$order_info.metro}</td>
					</tr>
					<tr>
					  <td align="left"><b>Название компании:</b></td>
						<td align="left">{$order_info.firma}</td>
					</tr>
					<tr>
					  <td align="left"><b>{$lang.shipping_address}:</b></td>
						<td align="left">{$order_info.s_address}</td>
					</tr>
					{include file="profiles/profiles_invoice_fields.tpl" nometro=1 fields=$profile_fields.S}
				</table>

				</td>
			</tr>
			<tr>
				<td colspan="2">
				
			<table width="100%" cellpadding="0" cellspacing="1" style="background-color: #dddddd;">
			<tr>
				
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.pos_iteration}</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">Фото</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.inv_art}</th>				
				<th width="70%" style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.product}</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.inv_quantity}</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.inv_price}</th>
				<th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.subtotal}</th>
			</tr>
			{foreach from=$order_info.items key=k item="oi" name=fe}
			{hook name="orders:items_list_row"}
				{if !$oi.extra.parent}
				<tr>
					<td style="padding: 5px 5px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;">{$smarty.foreach.fe.iteration}</td>
					<td style="padding: 5px 5px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;"><img src="{$oi.img.image_path}" width="50"/></td>
					<td style="padding: 5px 5px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;">{if $oi.product_code}{$oi.product_code}{/if}</td>
					<td align="left" style="padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
						{$oi.product|unescape|default:$lang.deleted_product}
						
						
						{if $oi.product_options}<br/>
						{include file="common_templates/options_info.tpl" product_options=$oi.product_options}{/if}
						{if $settings.Suppliers.enable_suppliers == "Y" && $oi.company_id && $settings.Suppliers.display_supplier == "Y"}
							<p style="margin: 2px 0px 3px 0px;">{$lang.supplier}: {$s_companies[$oi.company_id].company}</p>
						{/if}
				  	</td>
					<td style="padding: 5px 5px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;">{$oi.amount}</td>
					<td style="padding: 5px 5px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.original_price}{/if}</td>
					<td style="padding: 5px 10px; background-color: #ffffff; text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.display_per_subtotal}{/if}</b>&nbsp;</td>
				</tr>
				{/if}
			{/hook}
			{/foreach}
			{hook name="orders:extra_list"}
			{/hook}
			</table>				
				
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right" valign="top">
				
				<table border="0" style="padding: 3px 0px 12px 0px;">
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.subtotal}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.display_per_subtotal}</td>
				</tr>
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.order_discount}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.subtotal_discount}</td>
				</tr>
				<tr>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{$lang.shipping_cost}:</b>&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.display_shipping_cost}</td>
				</tr>
				<tr>
					<td colspan="2"><hr style="border: 0px solid #d5d5d5; border-top-width: 1px;" /></td>
				</tr>
				<tr>
					<td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;">{$lang.total_cost}:&nbsp;</td>
					<td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;">
						<strong style="font: bold 17px Tahoma;">
							{assign var="modifiedPrice" value=$order_info.total-$order_info.display_shipping_cost}
							{ * include file="common_templates/price.tpl" value=$modifiedPrice *}
							{include file="common_templates/price.tpl" value=$order_info.total}
						</strong>
					</td>
				</tr>
				</table>				
				
				</td>
			</tr>
			<tr>
				<td colspan="2" align="left" style="padding:6.75pt;">
				
<div style='border:none;border-bottom:solid windowtext 0.8pt;padding:0cm 0cm 1.0pt 0cm'>

</div>				
				
				</td>
			</tr>
		<!--	<tr>
				<td colspan="2" align="left" style="padding:6.75pt;">
				
<p style="font-size: 14px; margin-top: -5px"><b>Стоимость доставки вашего заказа составляет - {include file="common_templates/price.tpl" value=$order_info.display_shipping_cost}</b></p> 

<div style='border:none;border-bottom:solid windowtext 0.8pt;padding:0cm 0cm 1.0pt 0cm'>				
				</td>
			</tr>-->
			<tr> 
				<td colspan="2" align="left" style="padding:6.75pt;">
				
<div style='border:none;border-bottom:solid windowtext 1.5pt;padding:0cm 0cm 1.0pt 0cm'>

<p style='margin-top:1.5pt;margin-right:0cm;margin-bottom:2.25pt;
margin-left:0cm;line-height:normal;border:none;padding:0cm'>

<span
style='font-size:9.0pt;font-family:Arial'>{$lang.description_accepted1} _______________________
/ _______________________{*$order_info.firstname}&nbsp;{$order_info.lastname*}/</span></p>

<p style='margin-top:1.5pt;margin-right:0cm;margin-bottom:2.25pt;
margin-left:0cm;line-height:normal;border:none;padding:0cm'><span lang=EN-US
style='font-size:9.0pt;font-family:Arial'>&nbsp;</span></p>

</div>				
				
                </td>
            </tr> 				
			<tr> 
	       <td>  
		

        </td>
            </tr>       
      </table>        
		</td>
	</tr>
</table>
