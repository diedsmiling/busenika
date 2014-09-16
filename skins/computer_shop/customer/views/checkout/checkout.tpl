{* $Id: checkout.tpl 10248 2010-07-28 12:51:29Z angel $ *}

{script src="js/exceptions.js"}
{script src="js/cc_validator.js"}

<script type="text/javascript">
//<![CDATA[
	{if $edit_steps}
	{assign var="c_step" value=$edit_steps|implode:""}	
	$(document).ready(function() {$ldelim}
		jQuery.scrollToElm($('#{$c_step}'));
	{$rdelim});
	{/if}
//]]>
</script>

{if $settings.General.one_page_checkout != "Y"}
	{if $cart_products}
	{include file="views/checkout/components/progressbar.tpl"}
	{/if}

	{include file="views/checkout/components/checkout_steps.tpl"}
	{capture name="mainbox_title"}<span class="secure-page-title classic-checkout-title">{$lang.secure_checkout}</span>{/capture}
{else}
	{$smarty.capture.checkout_error_content}
	<a name="checkout_top"></a>
	{include file="views/checkout/components/checkout_steps.tpl"}

	{capture name="mainbox_title"}<span class="secure-page-title">{$lang.secure_checkout}</span>{/capture}
{/if}
