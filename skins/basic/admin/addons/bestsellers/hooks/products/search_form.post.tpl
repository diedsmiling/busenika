{* $Id: search_form.post.tpl 7745 2009-07-21 07:15:15Z alexions $ *}

<div class="search-field">
	<label for="sales_amount_from">{$lang.sales_amount}:</label>
	<input type="text" name="sales_amount_from" id="sales_amount_from" value="{$search.sales_amount_from}" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="sales_amount_to" value="{$search.sales_amount_to}" onfocus="this.select();" class="input-text" />
</div>