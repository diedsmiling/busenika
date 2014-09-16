{* $Id: totals.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $order_info.return}
<tr>
	<td><strong>{$lang.rma_return}:&nbsp;</strong></td>
	<td><strong>{include file="common_templates/price.tpl" value=$order_info.return}</strong></td>
</tr>
{/if}