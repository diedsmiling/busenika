{* $Id: payments.tpl 8062 2009-10-08 05:01:55Z angel $ *}
{** block-description:payment_methods **}

{assign var="payment_images" value=""|fn_get_payment_methods_images}
{if $payment_images}
<p class="center image-border">
	{foreach from=$payment_images item=image}
		<img src="{$image.image_path}" width="{$image.image_x}" height="{$image.image_y}" alt="{$image.alt}" />
	{/foreach}
</p>
{/if}