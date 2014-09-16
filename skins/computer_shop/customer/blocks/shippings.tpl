{* $Id: shippings.tpl 8062 2009-10-08 05:01:55Z angel $ *}
{** block-description:shipping_methods **}
{assign var="shippings_images" value=""|fn_get_shipping_images}
{if $shippings_images}
<p class="center image-border">
	{foreach from=$shippings_images item=image}
		<img src="{$image.image_path}" width="{$image.image_x}" height="{$image.image_y}" alt="{$image.alt}" />
	{/foreach}
</p>
{/if}