{* $Id: totals_extra.post.tpl 6613 2008-12-19 12:46:16Z angel $ *}

{if $cart.affiliate.partner_id}
<div class="form-field">
	<label>{$lang.affiliate}:</label>
	{$cart.affiliate.firstname} {$cart.affiliate.lastname}
</div>
{/if}

{if $addons.affiliate.show_affiliate_code == "Y" || ($cart.order_id && $cart.affiliate.is_payouts != "Y")}
<div class="form-field">
	<label for="affiliate_code">{$lang.affiliate_code}:</label>
	<input type="text" name="affiliate_code" id="affiliate_code" value="{$cart.affiliate.code}" class="input-text" size="10" maxlength="10" />
</div>
{/if}