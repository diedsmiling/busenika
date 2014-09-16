{* $Id: default.tpl 10402 2010-08-12 08:18:09Z klerik $ *}
<html>
<head></head>
<body>
{literal}
	<style type="text/css">
	body,th,td,tt,p,div,span {
		color: #000000;
		font-family: tahoma, verdana, arial, sans-serif;
		font-size: 11px;
	}
	a.product-link:link, a.product-link:visited, a.product-link:active, a.product-link:hover {
		text-decoration: underline;
		color: #1555b5;
		font: bold 12px Tahoma;
		line-height: 20px;
	}

	a.product-link:active, a.product-link:hover {
		text-decoration: none;
	}
	.value {
		font: bold 18px Tahoma;
		color: #323232;
	}
	.action-text-button:link, .action-text-button:visited, .action-text-button:hover, .action-text-button:active {
		white-space: nowrap;
		margin-right: 1px;
		font: bold 11px tahoma, verdana, arial, sans-serif;
		text-decoration: underline;
		padding: 2px 5px 2px 0px;
		color: #ff5400;

	}
	.action-text-button:hover, .action-text-button:active {
		text-decoration: none;
	}
	</style>
{/literal}

<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
<tr>
	<td width="100%">
		<div style="width: 637px; padding: 4px; background-color: #d4eafa; border: 1px solid #909090; margin: 0px auto;">
			<table style="background-color:#ffffff;" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="370"><img src="{$images_dir}/{$manifest.Gift_certificate_logo.filename}" width="{$manifest.Gift_certificate_logo.width}" height="{$manifest.Gift_certificate_logo.height}" border="0" alt="{$manifest.Gift_certificate_logo.alt}" style="margin: 12px 0px 8px 13px;" /></td>
				<td>
					<span style="font: bold 14px Tahoma; color: #bb2300;">{$lang.gift_cert_code}:</span><br /><br />
					<span style="font: bold 18px Tahoma; color: #323232;">{$gift_cert_data.gift_cert_code}</span>
				</td>
			</tr>
			</table>
	
			<div style="padding: 16px 15px 15px 15px;">
	
				<div style="margin-left: 8px">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="397"><h1 style="padding: 0; margin: 0; color: #0c3f6a; font: normal 40px Tahoma;">{$lang.gift_certificate}</h1></td>
						<td width="232" align="right"><h1 style="padding: 0; margin: 0; color: #0c3f6a; font: bold 40px Tahoma;">{include file="common_templates/price.tpl" value=$gift_cert_data.amount}</h1></td>
					</tr>
					</table>
				</div>
	
				<div style="margin-left: 10px">
	
					{*
					<table cellpadding="0" cellspacing="0" style="margin-top: 11px;">
					<tr>
						<td width="395"><span style="font: bold 14px Tahoma; color: #bb2300;">In the amount of:&nbsp;&nbsp;</span><span style="font: bold 18px Tahoma; color: #323232;">{include file="common_templates/price.tpl" value=$gift_cert_data.amount class="value"}</span>
						</td>
						<td width="232" align="right"><span style="color: #0c3f6a; font: bold 14px Tahoma;">{include file="common_templates/price.tpl" value=50} left</span>&nbsp;<span style="color: #327c00; font: bold 14px Tahoma;">({include file="common_templates/price.tpl" value=150} spent)</span></td>
					</tr>
					</table>
					*}
	
	
					<table cellspacing="0" cellpadding="0" style="margin-top: 6px;">
					<tr>
						<td valign="top" width="298" style="color: #0c3f6a; font: bold 14px Tahoma;">
							<span style="font: bold 14px Tahoma; color: #bb2300;">{$lang.gift_cert_to}:&nbsp;</span>{$gift_cert_data.recipient}<br />
							{if $gift_cert_data.send_via == 'P'}
							{$gift_cert_data.address}<br />
							{if $gift_cert_data.address_2}{$gift_cert_data.address_2}<br />{/if}
							{if $gift_cert_data.phone}{$gift_cert_data.phone}<br />{/if}
							{$gift_cert_data.city},&nbsp;{$gift_cert_data.descr_country},&nbsp;{$gift_cert_data.descr_state}<br />
							{$gift_cert_data.zipcode}
							{/if}
						</td>
						<td valign="top" width="298" align="right" style="color: #0c3f6a; font: bold 14px Tahoma;">
							<span style="font: bold 14px Tahoma; color: #bb2300;">{$lang.gift_cert_from}:&nbsp;</span>{$gift_cert_data.sender}
						</td>
					</tr>
					</table>
				</div>
	
				{if $gift_cert_data.message}
				<div style="color: #0c3f6a; font: normal 12px Tahoma; background-color: #ebf6fe; padding: 10px; margin-top: 10px;">
					{$gift_cert_data.message|unescape}
				</div>
				{/if}
	
				{if $gift_cert_data.products && $addons.gift_certificates.free_products_allow == 'Y'}
				<div style="margin: 12px 0px 0px 10px;">
					<h5 style="padding: 0; margin: 0; font: bold 12px Tahoma; color: #bb2300;">{$lang.free_products}:</h5>
					<table border="0" cellpadding="0" cellspacing="0">
					{foreach from=$gift_cert_data.products item="product"}
					<tr valign="top">
					<td><span style="color: #1555b5;font: bold 12px Tahoma; line-height: 20px;">{$product.amount}</span>&nbsp;<b>X</b>&nbsp;</td><td><a href="{"products.view?product_id=`$product.product_id`"|fn_url:'C':'http':'&'}" class="product-link" style="color: #1555b5; font: bold 12px Tahoma; line-height: 20px;">{$product.product}</a><br />{include file="common_templates/options_info.tpl" product_options=$product.product_options_value}<br /></td>
					{/foreach}
					</tr>
					</table>
				</div>
				{/if}
			</div>
	
			<table cellpadding="0" cellspacing="0" style="background: url('{$images_dir}/gift_cert_bottom_bg.gif') bottom repeat-x;" width="100%">
				<tr>
					<td style="padding-left: 27px;"><img src="{$images_dir}/gift_cert_bottom.gif" width="160" height="62"></td>
					<td></td>
					<td style="padding: 18px 15px 0px 0px;" align="right"><b>{$lang.shop_now}:</b>&nbsp;<a href="{$config.http_location|fn_url:'C'}" target="_blank" class="action-text-button" style="white-space: nowrap; margin-right: 1px; font: bold 11px tahoma, verdana, arial, sans-serif; text-decoration: underline; padding: 2px 5px 2px 0px; color: #ff5400;">{$config.http_location|fn_url:'C'}</a></td>
				</tr>
			</table>
		</div>
	</td>
</tr>
</table>
</body>

</html>