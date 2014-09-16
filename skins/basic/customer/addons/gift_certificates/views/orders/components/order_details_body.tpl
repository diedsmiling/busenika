{* $Id: order_details_body.tpl 5805 2008-08-24 20:31:37Z zeke $ *}

{if $product.extra.in_use_certificate}
<div>({$lang.gift_certificate}:{foreach from=$product.extra.in_use_certificate item="c" key="c_key" name="f_fciu"}&nbsp;{$c_key}{if !$smarty.foreach.f_fciu.last},{/if}{/foreach})</div>
{/if}
