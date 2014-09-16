{* $Id: cart_status.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $smarty.session.cart.gift_certificates}
	{if $smarty.session.cart.products}
		<li class="delim">&nbsp;</li>
	{/if}
	
	{foreach from=$smarty.session.cart.gift_certificates item="gift" key="gift_key" name="f_gift_certificates"}
	<li class="clear">
		<a href="{"gift_certificates.update?gift_cert_id=`$gift_key`"|fn_url}">{$lang.gift_certificate}</a>
		<p>
			{include file="common_templates/price.tpl" value=$gift.display_subtotal span_id="subtotal_gc_`$gift_key`" class="none"}
		</p>
	</li>
	
	{if !$smarty.foreach.f_gift_certificates.last}
		<li class="delim">&nbsp;</li>
	{/if}
	{/foreach}
{/if}
