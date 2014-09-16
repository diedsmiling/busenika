{* $Id: product_data.post.tpl 9806 2010-06-17 08:17:19Z alexions $ *}

{if $bt_chain || $bt_id}
	<div class="object-container cm-reload-{$obj_prefix}{$obj_id}" id="buy_together_options_update_{$bt_chain}_{$bt_id}">
		{assign var="product_options" value="product_options_`$obj_id`"}
		{$smarty.capture.$product_options}
	<!--buy_together_options_update_{$bt_chain}_{$bt_id}--></div>
{/if}