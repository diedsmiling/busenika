{* $Id: po.tpl 6645 2008-12-22 20:21:37Z zeke $ *}

<div class="form-field">
	<label for="elm_po_number" class="cm-required">{$lang.po_number}:</label>
	<input id="elm_po_number" size="35" type="text" name="payment_info[po_number]" value="{$cart.payment_info.po_number}" class="input-text">
</div>

<div class="form-field">
	<label for="elm_company_name" class="cm-required">{$lang.company_name}:</label>
	<input id="elm_company_name" size="35" type="text" name="payment_info[company_name]" value="{$cart.payment_info.company_name}" class="input-text">
</div>

<div class="form-field">
	<label for="elm_buyer_name" class="cm-required">{$lang.buyer_name}:</label>
	<input id="elm_buyer_name" size="35" type="text" name="payment_info[buyer_name]" value="{$cart.payment_info.buyer_name}" class="input-text">
</div>

<div class="form-field">
	<label for="elm_position" class="cm-required">{$lang.position}:</label>
	<input id="elm_position" size="35" type="text" name="payment_info[position]" value="{$cart.payment_info.position}" class="input-text-short">
</div>
