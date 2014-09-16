{* $Id: rma_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="rma_search_form" method="get">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field nowrap">
		<label for="cname">{$lang.customer}:</label>
		<div class="break">
			<input type="text" name="cname" id="cname" value="{$search.cname}" size="30" class="{*search-*}input-text" />
			{*include file="buttons/search_go.tpl" search="Y" but_name="$controller/$mode/$action"*}
		</div>
	</td>
	<td class="search-field">
		<label for="email">{$lang.email}:</label>
		<div class="break">
			<input type="text" name="email" id="email" value="{$search.email}" size="30" class="input-text" />
		</div>
	</td>
	<td class="search-field nowrap">
		<label for="rma_amount_from">{$lang.quantity}:</label>
		<div class="break">
			<input type="text" name="rma_amount_from" id="rma_amount_from" value="{$search.rma_amount_from}" size="3" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="rma_amount_to" value="{$search.rma_amount_to}" size="3" class="input-text" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[rma.returns]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label for="return_id">{$lang.rma_return}&nbsp;{$lang.id}:</label>
	<input type="text" name="return_id" id="return_id" value="{$search.return_id}" size="30" class="input-text" />
</div>

{if $actions}
<div class="search-field">
	<label for="action">{$lang.action}:</label>
	<select name="action" id="action">
		<option value="0">{$lang.all_actions}</option>
		{foreach from=$actions item="action" key="action_id"}
			<option value="{$action_id}" {if $search.action == $action_id}selected="selected"{/if}>{$action.property}</option>
		{/foreach}
	</select>
</div>
{/if}

<div class="search-field clear">
	<label>{$lang.return_status}:</label>
	<div class="float-left">
		{include file="common_templates/status.tpl" status=$search.request_status display="checkboxes" name="request_status" status_type=$smarty.const.STATUSES_RETURN}
	</div>
</div>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="rma_search_form"}
</div>

{include file="common_templates/subheader.tpl" title=$lang.search_by_order}

<div class="search-field">
	<label>{$lang.order_status}:</label>
	<div class="float-left">{include file="common_templates/status.tpl" status=$search.order_status display="checkboxes" name="order_status"}</div>
</div>

<div class="search-field">
	<label for="order_id">{$lang.order}&nbsp;{$lang.id}:</label>
	<input type="text" name="order_id" id="order_id" value="{$search.order_id}" size="30" class="input-text" />
</div>

{include file="pickers/search_products_picker.tpl"}

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="rma.returns" view_type="rma"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
