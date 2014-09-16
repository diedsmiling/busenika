{* $Id: ordered_products.post.tpl 7703 2009-07-13 10:36:45Z angel $ *}

{if $order_info.returned_products}
{if $order_info.items}<p></p>{/if}
	<table cellpadding="2" cellspacing="1" border="0" width="100%" bgcolor="#000000">
	<tr>
		<td width="10%" align="center" bgcolor="#dddddd"><b>{$lang.sku}</b></td>
		<td width="60%" bgcolor="#dddddd"><b>{$lang.returned_product}</b></td>
		<td width="10%" align="center" bgcolor="#dddddd"><b>{$lang.amount}</b></td>
		<td width="10%" align="center" bgcolor="#dddddd"><b>{$lang.subtotal}</b></td>
	</tr>	
	{foreach from=$order_info.returned_products item='oi'}
	<tr>
		<td bgcolor="#ffffff">{$oi.product_code|default:"&nbsp;"}</td>
		<td bgcolor="#ffffff">
			{$oi.product}
			{hook name="orders:product_info"}
			{/hook}
			{if $oi.product_options}<div style="padding-top: 1px; padding-bottom: 2px;">{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}</td>
		<td bgcolor="#ffffff" align="center">{$oi.amount}</td>
		<td align="right" bgcolor="#ffffff"><b>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.subtotal}{/if}</b>&nbsp;</td>
	</tr>
	{/foreach}
	</table>
{/if}