{* $Id: product_info.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $oi.extra.in_use_certificate}
	<div>({$lang.gift_certificates}:&nbsp;{foreach from=$oi.extra.in_use_certificate item="c" key="c_key" name="f_fciu"}&nbsp;{$c_key}{if !$smarty.foreach.f_fciu.last},{/if}{/foreach})</div>
{/if}
