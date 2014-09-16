{* $Id: check.tpl 8894 2010-02-22 07:30:32Z klerik $ *}

<div class="form-field">
	<label for="customer_signature" class="cm-required">{$lang.customer_signature}:</label>
	<input id="customer_signature" size="35" type="text" name="payment_info[customer_signature]" value="{$cart.payment_info.customer_signature}" class="input-text" autocomplete="off" />
</div>
<div class="form-field">
	<label for="checking_account_number" class="cm-required">{$lang.checking_account_number}:</label>
	<input id="checking_account_number" size="35" type="text" name="payment_info[checking_account_number]" value="{$cart.payment_info.checking_account_number}" class="input-text" autocomplete="off" />
</div>
<div class="form-field">
	<label for="bank_routing_number" class="cm-required">{$lang.bank_routing_number}:</label>
	<input id="bank_routing_number" size="35" type="text" name="payment_info[bank_routing_number]" value="{$cart.payment_info.bank_routing_number}" class="input-text" autocomplete="off" />
</div>
