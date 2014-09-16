{* $Id: details_bullets.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $order_info.allow_return}
	<li><a href="{"rma.create_return?order_id=`$order_info.order_id`"|fn_url}">{$lang.return_registration}</a></li>
{/if}
{if $order_info.isset_returns}
	<li><a href="{"rma.returns?order_id=`$order_info.order_id`"|fn_url}">{$lang.order_returns}</a></li>
{/if}