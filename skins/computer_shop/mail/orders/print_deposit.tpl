{* $Id: print_packing_slip.tpl 7354 2009-04-24 14:00:06Z alexions $ *}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head></head>

<body><br/>
  <label>
    Номер линдера <input type="text" name="textfield1" id="textfield1" /><input name="" value="Жми" type="button" onclick="send1()"/>
  </label>
  <label>
     Номер корзины <input type="text" name="textfield2" id="textfield2" /><input name="" value="Жми" type="button" onclick="send2()"/>
  </label>
  {literal}
<script>
function send1(){
	var id=document.getElementById("textfield1").value
	
	window.location.href="http://lindero.ru/z/"+id+".sst";
	}
	function send2(){
	var id=document.getElementById("textfield2").value
	
	window.location.href="http://korzin.net/z/"+id+".sst";
	}
	

	function chekrow(id){
		var p=getCookie("p"+id);
		if(p!=""){
			setCookie("p"+id,"");
			document.getElementById("row"+id).style.backgroundColor="#fff";
		}else{
			setCookie("p"+id,"1");
			document.getElementById("row"+id).style.backgroundColor="#ccc";
		}
			
	}
	function setrow(id){
		var p=getCookie("p"+id);
		console.log(p);
		if(p==""){
			
			document.getElementById("row"+id).style.backgroundColor="#fff";
		}else{
			
			document.getElementById("row"+id).style.backgroundColor="#ccc";
		}
			
	}
	
	function setCookie (name, value, expires, path, domain, secure) {

	var expdate = new Date(); //Создаем объект даты
	var  monthFromNow =  expdate.getTime() + (24*60*60*1000);
	expdate.setTime(monthFromNow); //Устанавливаем  значение даты

	document.cookie = name + "=" + escape(value) +
	((expires) ? "; expires=" + expires : "; expires=" +expdate.toGMTString()) +
	((path) ? "; path=" + path : "") +
	((domain) ? "; domain=" + domain : "") +
	((secure) ? "; secure" : "");
}
	function getCookie(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end == -1) c_end = document.cookie.length;
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return "";
}
</script>
  {/literal}
  <br/><br/>
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
				<td height="80" colspan="2">
				
				<table width="70%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>
  
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left:6.75pt; margin-bottom:6.75pt;">
						<tr>
						<td width="29%" height="50" colspan="2"><b><span style="font-size:13.0pt;font-family:Tahoma; margin-bottom:10px; margin-top:10px;">{$lang.order}&nbsp;#{$order_info.order_id}</span></b></p></td>
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
					
					
					{include file="profiles/profiles_invoice_fields_lite.tpl" shotr=1 fields=$profile_fields.S}
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
				<tr id="row{$oi.product_code}" onclick="chekrow('{$oi.product_code}')" style="background-color: #ffffff;">
					<td style="padding: 5px 5px;  text-align: center; font-size: 12px; font-family: Arial;">{$smarty.foreach.fe.iteration}</td>
					<td style="padding: 5px 5px;  text-align: center; font-size: 12px; font-family: Arial;"><img src="{$oi.img.image_path}" width="50"/></td>
					<td style="padding: 5px 5px;  text-align: center; font-size: 12px; font-family: Arial;">{if $oi.product_code}{$oi.product_code}{/if}</td>
					<td align="left" style="padding: 5px 10px;  font-size: 12px; font-family: Arial;">
						{$oi.product|unescape|default:$lang.deleted_product}
						
						
						{if $oi.product_options}<br/>
						{include file="common_templates/options_info.tpl" product_options=$oi.product_options}{/if}
						{if $settings.Suppliers.enable_suppliers == "Y" && $oi.company_id && $settings.Suppliers.display_supplier == "Y"}
							<p style="margin: 2px 0px 3px 0px;">{$lang.supplier}: {$s_companies[$oi.company_id].company}</p>
						{/if}
						{$oi.sklad}
				  	</td>
					<td style="padding: 5px 5px;  text-align: center; font-size: 12px; font-family: Arial;">{$oi.amount}</td>
					<td style="padding: 5px 5px;  text-align: right; font-size: 12px; font-family: Arial;">{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.original_price}{/if}</td>
					<td style="padding: 5px 10px;  text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.display_per_subtotal}{/if}</b>&nbsp;</td>
				</tr>
				<script>setrow('{$oi.product_code}');</script>
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
					<td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;"><strong style="font: bold 17px Tahoma;">{include file="common_templates/price.tpl" value=$order_info.total}</strong></td>
				</tr>
				</table>				
				
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




{/if}

</body>
</html>
