{* $Id: totals.post.tpl 9450 2010-05-14 11:13:57Z angel $ *}

{if $cart.use_gift_certificates}
<input type="hidden" name="cert_code" value="" />
	<li>
		<em>{$lang.gift_certificate}:</em>
		<strong>&nbsp;</strong>
	</li>
{foreach from=$cart.use_gift_certificates item="ugc" key="ugc_key"}
	<li>
		<em><a href="{"gift_certificates.update?gift_cert_id=`$ugc.gift_cert_id`"|fn_url}">{$ugc_key}</a><a href="{"order_management.delete_use_certificate?gift_cert_code=`$ugc_key`"|fn_url}"><img src="{$images_dir}/icons/delete_icon.gif" width="12" height="11" border="0" alt="{$lang.delete}" /></a>:</em>
		<strong>{include file="common_templates/price.tpl" value=$ugc.cost}</strong>
	</li>
{/foreach}
{/if}