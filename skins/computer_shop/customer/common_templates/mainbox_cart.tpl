{* $Id: mainbox_cart.tpl 10291 2010-08-02 07:33:30Z angel $ *}
{if $anchor}
<a name="{$anchor}"></a>
{/if}
<div>
	<h2 class="mainbox-cart-title">
		<span class="float-left">&nbsp;</span>
		<span class="float-right">&nbsp;</span>
		{$title}
	</h2>
	<div class="mainbox-cart-body" {if $mainbox_id}id="{$mainbox_id}"{/if}>{$content}{if $mainbox_id}<!--{$mainbox_id}-->{/if}</div>
</div>