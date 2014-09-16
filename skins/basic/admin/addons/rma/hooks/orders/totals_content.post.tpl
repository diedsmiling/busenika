{* $Id: totals_content.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $order_info.return}
	<li>
		<em>{$lang.rma_return}:&nbsp;</em>
		<strong>{include file="common_templates/price.tpl" value=$order_info.return}</strong>
	</li>
{/if}