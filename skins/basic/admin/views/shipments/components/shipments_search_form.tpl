{* $Id: shipments_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="shipments_search_form" method="get">

{if $smarty.request.redirect_url}
<input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}
{if $selected_section != ""}
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
{/if}

{$extra}

<table cellpadding="10" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label for="cname">{$lang.customer}:</label>
		<div class="break">
			<input type="text" name="cname" id="cname" value="{$search.cname}" size="30" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name=$dispatch}
		</div>
	</td>
	<td class="search-field">
		<label for="order_id">{$lang.order_id}:</label>
		<div class="break">
			<input type="text" name="order_id" id="order_id" value="{$search.order_id}" size="15" class="input-text" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.shipment_date}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.shipment_period form_name="shipments_search_form" prefix="shipment_"}
</div>

<div class="search-field">
	<label>{$lang.order_date}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.order_period form_name="shipments_search_form" prefix="order_"}
</div>

<div class="search-field">
	<label>{$lang.shipped_products}:</label>
	{include file="pickers/search_products_picker.tpl"}
</div>

{hook name="shipments:search_form"}
{/hook}

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="shipments"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
