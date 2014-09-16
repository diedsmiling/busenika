{* $Id: invoice.tpl 8724 2010-01-28 13:56:27Z zeke $ *}

{if $order_info}

<style type="text/css">
body,th,td,tt,p,div,span {$ldelim}
	color: #000000;
	font-family: tahoma, verdana, arial, sans-serif;	
	font-size: 11px;
{$rdelim}
p,ul {$ldelim}
	margin-top:	6px;
	margin-bottom: 6px;
{$rdelim}
.form-field-caption {$ldelim}
	font-style:italic;
{$rdelim}
.form-title	{$ldelim}
	background-color: #ffffff;
	color: #141414;
	font-weight: bold;
{$rdelim}
</style>

<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td><img src="{$images_dir}/spacer.gif" width="1" height="1" border="0" alt="" /></td>
	<td width="600" style="border: #444444; border-style: solid; border-width: 2px" align="center">
		<table cellpadding="10" cellspacing="0" width="100%" border="0">
		<tr>
			<td>
			{* Customer info *}
			{assign var="profile_fields" value='I'|fn_get_profile_fields}
			{assign var="contact_fields" value="`$profile_fields.C`"|array_slice:0:4}
			<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" width="50%">
				<table>
				<tr>
					<td>
						<table>
							{include file="profiles/profile_fields_info.tpl" fields=$contact_fields title=$lang.contact_information user_data=$order_info}
						</table>
					</td>
				</tr>
				</table>
				</td>
				<td width="1%">&nbsp;</td>
				<td valign="top" width="49%">
					<table>
						{include file="profiles/profile_fields_info.tpl" fields=$profile_fields.S title=$lang.shipping_address user_data=$order_info}
					</table>
				</td>
			</tr>
			</table>
			<p></p><br />
			{* /Customer info *}

			{* Ordered products *}
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td valign="top">
			<table cellpadding="2" cellspacing="1" border="0" width="100%" bgcolor="#000000">
			<tr>
				<td width="10%" align="center" bgcolor="#dddddd"><b>{$lang.sku}</b></td>
				<td width="50%" bgcolor="#dddddd"><b>{$lang.product}</b></td>
				<td width="10%" align="center" bgcolor="#dddddd"><b>{$lang.amount}</b></td>
			</tr>
			{foreach from=$order_info.items item="oi"}
			{if $oi.company_id == $supplier_id}
			<tr>
				<td bgcolor="#ffffff">{$oi.product_code|default:"-"}</td>
				<td bgcolor="#ffffff">{$oi.product}
					{if $oi.product_options}<div style="padding-top: 1px; padding-bottom: 2px;">{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}</td>
				<td bgcolor="#ffffff" align="center">{$oi.amount}</td>
			</tr>
			{/if}
			{/foreach}
			</table>
			</td>
			</tr>
			</table>
			{* /Ordered products *}

			{* Order totals *}
			<div align="right">
			<table>
			<tr>
				<td align="right" nowrap="nowrap"><b>{$lang.shipping_cost}:</b>&nbsp;</td>
				<td align="right" nowrap="nowrap">{include file="common_templates/price.tpl" value=$shipping_cost}</td>
			</tr>
			</table><br />
			</div>
			{* /Order totals *}
			</td>
		</tr>
		</table>
	</td>
	<td><img src="{$images_dir}/spacer.gif" width="1" height="1" border="0" alt="" /></td>
</tr>
</table>

{/if}