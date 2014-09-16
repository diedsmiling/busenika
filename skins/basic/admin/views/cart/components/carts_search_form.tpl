{* $Id: carts_search_form.tpl 9908 2010-06-30 07:00:40Z alexions $ *}

<form action="{""|fn_url}" name="carts_search_form" method="get">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field nowrap">
		<label for="cname">{$lang.customer}:</label>
		<div class="break">
			<input type="text" name="cname" id="cname" value="{$search.cname}" size="30" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="cart.cart_list"}
		</div>
	</td>
	<td class="search-field">
		<label for="email">{$lang.email}:</label>
		<div class="break">
			<input type="text" name="email" id="email" value="{$search.email}" size="30" class="input-text" />
		</div>
	</td>
	<td class="search-field nowrap">
		<label for="total_from">{$lang.total}&nbsp;({$currencies.$primary_currency.symbol}):</label>
		<div class="break">
			<input type="text" name="total_from" id="total_from" value="{$search.total_from}" size="3" class="input-text-price" />&nbsp;-&nbsp;<input type="text" name="total_to" value="{$search.total_to}" size="3" class="input-text-price" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[cart.cart_list]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.content}:</label>
	<div class="select-field">
		{if $search.product_type_c != "Y" && $search.product_type_w != "Y"}
			{assign var="check_all" value=true}
		{/if}
		{hook name="cart:search_form"}
		<input type="checkbox" value="Y" {if $search.product_type_c == "Y" || $check_all}checked="checked"{/if} name="product_type_c" id="cb_product_type_c" onclick="if (!this.checked) document.getElementById('cb_product_type_w').checked = true;" disabled="disabled" class="checkbox" />
		<label for="cb_product_type_c">{$lang.cart}</label>

		<input type="checkbox" value="Y" name="product_type_w" {if $search.product_type_w == "Y" || $check_all}checked="checked"{/if} id="cb_product_type_w" onclick="if (!this.checked) document.getElementById('cb_product_type_c').checked = true;" disabled="disabled" class="checkbox" />
		<label for="cb_product_type_w">{$lang.wishlist}</label>
		{/hook}
	</div>
</div>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="carts_search_form"}
</div>

<div class="search-field">
	<label for="users_type">{$lang.users_type}:</label>
	<select name="users_type" id="users_type">
		<option value="A" {if $search.users_type == "A"}selected="selected"{/if}>{$lang.any}</option>
		<option value="R" {if $search.users_type == "R"}selected="selected"{/if}>{$lang.registered}</option>
		<option value="G" {if $search.users_type == "G"}selected="selected"{/if}>{$lang.guest}</option>
	</select>
</div>

<div class="search-field">
	<label for="with_info_only">{$lang.with_contact_information}:</label>
	<input type="checkbox" id="with_info_only" name="with_info_only" value="Y" class="checkbox" {if $search.with_info_only}checked="checked"{/if} />
</div>

<div class="search-field">
	<label for="online_only">{$lang.online_only}:</label>
	<input type="checkbox" id="online_only" name="online_only" value="Y" class="checkbox" {if $search.online_only}checked="checked"{/if} />
</div>

<div class="search-field">
	<label>{$lang.products_in_cart}:</label>
	{include file="pickers/search_products_picker.tpl"}
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="cart.cart_list" view_type="carts"}

</form>
