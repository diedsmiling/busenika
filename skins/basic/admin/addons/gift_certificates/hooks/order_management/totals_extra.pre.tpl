{* $Id: totals_extra.pre.tpl 6613 2008-12-19 12:46:16Z angel $ *}

<div class="form-field">
	<label for="gift_cert_code">{$lang.gift_cert_code}:</label>
	<select name="gift_cert_code" id="gift_cert_code">
		<option value=""> -- </option>
		{foreach from=$gift_certificates item="code"}
			<option value="{$code}">{$code}</option>
		{/foreach}
	</select>
</div>