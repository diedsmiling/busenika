{* $Id: checkout_totals.post.tpl 10081 2010-07-15 13:25:17Z alexions $ *}

{if $cart.use_gift_certificates}
{foreach from=$cart.use_gift_certificates item="ugc" key="ugc_key"}
	<li>
		<span>{$lang.gift_certificate}:</span>
		<strong>&nbsp;</strong>
	</li>
	<li>
	<span><a href="{"gift_certificates.verify?verify_code=`$ugc_key`"|fn_url}">{$ugc_key}</a>
		{if $settings.General.one_page_checkout == "Y"}
			&nbsp;<a {if $use_ajax}class="cm-ajax"{/if} href="{"checkout.delete_use_certificate?gift_cert_code=`$ugc_key`&amp;redirect_mode=`$mode`"|fn_url}" rev="checkout_totals,cart_items,cart_status,checkout_steps{$additional_ids}"><img src="{$images_dir}/icons/delete_icon.gif" width="10" height="8" border="0" alt="{$lang.delete}" title="{$lang.delete}" /></a>&nbsp;
		{/if}
	:</span>
	<strong>{include file="common_templates/price.tpl" value=$ugc.cost}</strong>
	</li>
{/foreach}
{/if}
