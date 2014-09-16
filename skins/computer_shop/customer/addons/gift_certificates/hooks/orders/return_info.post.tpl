{* $Id: return_info.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $return_info.extra.gift_certificates}
<tr>
	<td valign="top"><strong>{$lang.gift_certificates}</strong>:&nbsp;</td>
	<td>
		{foreach from=$return_info.extra.gift_certificates item="gift_cert" key="gift_cert_key"}
		<div><a href="{"gift_certificates.verify?verify_code=`$gift_cert.code`"|fn_url}">{$gift_cert.code}</a>&nbsp;({include file="common_templates/price.tpl" value=$gift_cert.amount})</div>
		{/foreach}
	</td>
</tr>
{/if}
