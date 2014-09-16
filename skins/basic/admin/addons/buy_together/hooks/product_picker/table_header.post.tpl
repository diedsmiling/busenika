{* $Id: table_header.post.tpl 8344 2009-12-02 09:06:26Z alexions $ *}

{if $controller == "buy_together" || $extra_mode == "buy_together"}
	<th>{$lang.price}</th>
	<th>{$lang.discount}</th>
	<th>{$lang.value}</th>
	<th>{$lang.discounted_price}</th>
{/if}