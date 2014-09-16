{* $Id: totals.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $order_info.use_gift_certificates}
{foreach from=$order_info.use_gift_certificates item="certificate" key="code" name="certs"}
<tr>
	<td>{if $order_info.payment_id == 0 && $smarty.foreach.certs.first}<strong>{$lang.payment_method}:</strong>{/if}&nbsp;</td>
	<td class="nowrap">
	{$lang.gift_certificate}: <a href="{"gift_certificates.verify?verify_code=`$code`"|fn_url}">{$code}</a> ({include file="common_templates/price.tpl" value=$certificate.cost})
	</td>
</tr>
{/foreach}
{/if}
