{* $Id: invoice.tpl 10436 2010-08-17 11:58:43Z angel $ *}

{literal}
<style>
 p{ font:12px Arial}
.rvts8{font:12px Arial; font-weight:bold;}
</style>
{/literal}

{assign var="order_header" value=$lang.invoice}
{if $status_settings.appearance_type == "I" && $order_info.doc_ids[$status_settings.appearance_type]}
    {assign var="doc_id_text" value="`$lang.invoice` #`$order_info.doc_ids[$status_settings.appearance_type]`"}
{elseif $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
    {assign var="doc_id_text" value="`$lang.credit_memo` #`$order_info.doc_ids[$status_settings.appearance_type]`"}
    {assign var="order_header" value=$lang.credit_memo}
{elseif $status_settings.appearance_type == "O"}
    {assign var="order_header" value=$lang.order_details}
{/if}

<table class="main-table" width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center" valign="top">
        
        <table width="602" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border: #333333 1px solid;">
            <tr>
                <td width="370" height="80" style="border-bottom:solid #868686 1.0pt;"><img src="http://korzin.net/images/korzin_net_logo.jpg" border="0"/></td>
                <td align="right" valign="bottom" style="border-bottom:solid #868686 1.0pt;">
                
                <table width="92%" border="0" cellpadding="0" cellspacing="0" style="margin-right:8px; margin-bottom:6.75pt;white-space: nowrap; font-size: 12px; font-family: Arial;">
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
                
                <table width="70%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
  
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left:6.75pt; margin-bottom:6.75pt;white-space: nowrap; font-size: 12px; font-family: Arial;">
                        <tr>
                        <td width="29%" height="50" colspan="2"><b><span style="font-size:13.0pt;font-family:Arial; margin-bottom:10px; margin-top:10px;">{$lang.order}&nbsp;#{$order_info.order_id}</span></b></p></td>
                        </tr>
                        <tr>
                        <td width="29%" align="left">{$lang.date_of_delivery}:</td>
                        <td width="71%" align="left">{$order_info.date_to_delivery|date_format:"`$settings.Appearance.date_format`"}</td>
                        </tr>                        
                        <tr>
                        <td align="left">{$lang.payment_method}:</td>
                        <td align="left">{$order_info.payment_method.payment}</td>
                        </tr>
                        <tr>
                        <td align="left">{$lang.shipping_method}:</td>
                        <td align="left">{foreach from=$order_info.shipping item="shipping" name="f_shipp"}{$shipping.shipping}{if !$smarty.foreach.f_shipp.last}, {/if}
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
                <table height="80" border="0" cellpadding="0" cellspacing="0" style="margin-left:6.75pt; margin-bottom:6.75pt;white-space: nowrap; font-size: 12px; font-family: Arial;">
                    <tr>
                        <td width="222" align="left" ><b>{$lang.customer}:</b></td>
                        <td width="734" align="left" >{$order_info.firstname}&nbsp;{$order_info.lastname}</td>
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
                    {include file="profiles/profiles_invoice_fields.tpl" fields=$profile_fields.S}
                </table>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                
            <table width="100%" cellpadding="0" cellspacing="1" style="background-color: #dddddd;">
            <tr>
                <th style="background-color: #eeeeee; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{$lang.pos_iteration}</th>
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
                    <td style="padding: 5px 5px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;">{if $oi.product_code}{$oi.product_code}{/if}</td>
                    <td align="left" style="padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
                        {$oi.product|unescape|default:$lang.deleted_product}
                        <br/>
                        {$oi.short_description|unescape}
                        
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
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>Сумма:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.display_per_subtotal}</td>
                </tr>
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>Скидка на заказ:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.subtotal_discount}</td>
                </tr>
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>Стоимость доставки:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common_templates/price.tpl" value=$order_info.display_shipping_cost}</td>
                </tr>
                <tr>
                    <td colspan="2"><hr style="border: 0px solid #d5d5d5; border-top-width: 1px;" /></td>
                </tr>
                <tr>
                    <td style="text-align: right; white-space: nowrap; font: 15px Arial text-align: right;">Итоговая стоимость:&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font: 15px Arial text-align: right;"><strong style="font: bold 17px Arial;">{include file="common_templates/price.tpl" value=$order_info.total}</strong></td>
                </tr>
                </table>                
                
                </td>
            </tr>
     
            </table>
            <table class="main-table" width="600px"  border="0" cellspacing="0" cellpadding="0">                
            <tr> 
           <td><p><b>Если вы заказываете товар на организацию, и Вам требуются документы для отчетности (ТТН, ЧЕКИ, ГАРАНТИЙНОЕ ПИСЬМО), ОБЯЗАТЕЛЬНО уведомите об этом менеджера! </br>При оплате по безналичному расчету, высылайте свои РЕКВИЗИТЫ нам на почту, с указанием номера вашего заказа.</b></p>
<p>Если с товаром что-то не так или вы недовольны обслуживанием, обращайтесь в службу контроля по тел.: <b>+7 (905) 724 50 05</b> (Кирилл Александрович) пн-пт. с 10.00 до 21.00 или по электронной почте круглосуточно с указанием номера заказа.</p>
<p> Поделитесь своим мнением о нашем магазине в гостевой книге по этой ссылке: <a href="http://korzin.net/index.php?dispatch=pages.view&page_id=6">http://korzin.net/index.php?dispatch=pages.view&page_id=6</a></p>

<p>Благодарим Вас за посещение нашего магазина. </p> 
  <hr style="color:#000000; background-color:#000000;"/>  
        </td>
            </tr>  
                 
      </table>        
        </td>
    </tr>
</table>
