{* $Id: payment_info.post.tpl 9450 2010-05-14 11:13:57Z angel $ *}

{if $order_info.use_gift_certificates}
{if $order_info.payment_id == 0}
	{include file="common_templates/subheader.tpl" title=$lang.payment_information}
{/if}

<div class="form-field">
	<label>{$lang.method}:</label>
	{$lang.gift_certificate}
</div>

{foreach from=$order_info.use_gift_certificates item="certificate" key="code"}
<div class="form-field">
	<label>{$lang.code}:</label>
	<a href="{"gift_certificates.update?gift_cert_id=`$certificate.gift_cert_id`"|fn_url}">{$code}</a>
</div>

<div class="form-field">
	<label>{$lang.amount}:</label>
	{include file="common_templates/price.tpl" value=$certificate.cost}
</div>
{/foreach}
{/if}