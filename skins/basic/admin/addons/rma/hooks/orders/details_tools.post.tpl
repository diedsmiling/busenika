{* $Id: details_tools.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $order_info.allow_return}
	&nbsp;|&nbsp;{include file="buttons/button.tpl" but_text=$lang.return_registration but_href="rma.create_return?order_id=`$order_info.order_id`" but_role="tool"}
{/if}
{if $order_info.isset_returns}
	&nbsp;|&nbsp;{include file="buttons/button.tpl" but_text=$lang.order_returns but_href="rma.returns?order_id=`$order_info.order_id`" but_role="tool"}
{/if}
