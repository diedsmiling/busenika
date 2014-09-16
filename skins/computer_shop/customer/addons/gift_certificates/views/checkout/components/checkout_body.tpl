{* $Id: checkout_body.tpl 6458 2008-11-28 13:47:01Z angel $ *}

{if $cart.products.$key.extra.in_use_certificate}
	({$lang.gift_certificate}:{foreach from=$cart.products.$key.extra.in_use_certificate item="c" key="c_key" name="f_gciu"}&nbsp;{$c_key}{if !$smarty.foreach.f_gciu.last},{/if}{/foreach})
{/if}
