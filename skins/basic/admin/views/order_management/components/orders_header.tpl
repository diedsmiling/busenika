{* $Id: orders_header.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<div class="pb-container" align="center">

<span class="{if $mode == "products"}active{else}complete{/if}">
	<em>1</em>
	{if $mode != "products"}<a href="{"order_management.products"|fn_url}">{/if}{$lang.products}{if $mode != "products"}</a>{/if}
</span>

<img src="{$images_dir}/icons/pb_arrow.gif" width="25" height="7" border="0" alt="&rarr;" />

<span class="{if $mode == "customer_info"}active{elseif $mode != "customer_info"}complete{/if}">
	<em>2</em>
	{if $mode != "customer_info"}<a href="{"order_management.customer_info"|fn_url}">{/if}{$lang.customer_details}{if $mode != "customer_info"}</a>{/if}
</span>

<img src="{$images_dir}/icons/pb_arrow.gif" width="25" height="7" border="0" alt="&rarr;" />

<span class="{if $mode == "totals"}active{elseif $mode == "summary"}complete{/if}">
	<em>3</em>
	{if $mode == "summary"}<a href="{"order_management.totals"|fn_url}">{/if}{$lang.totals}{if $mode == "summary"}</a>{/if}
</span>

<img src="{$images_dir}/icons/pb_arrow.gif" width="25" height="7" border="0" alt="&rarr;" />

<span class="{if $mode == "summary"}active{/if}">
	<em>4</em>
	{$lang.summary}
</span>

</div>

{if $cart.order_id}
{capture name="extra_tools"}
	{include file="buttons/button.tpl" but_href="orders.details?order_id=`$cart.order_id`" but_text="`$lang.order` #`$cart.order_id`" but_role="tool"}
{/capture}
{/if}