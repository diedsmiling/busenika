{* $Id: tabs_extra.post.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

<div id="content_configurator_groups" class="hidden cm-hide-save-button">
<form action="{""|fn_url}" method="post" name="configurator_products_form">
<input type="hidden" name="product_id" value="{$product_data.product_id}" />
<input type="hidden" name="selected_section" value="configurator_groups" />

{include file="common_templates/subheader.tpl" title=$lang.product_groups}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="5%">{$lang.position_short}</th>
	<th width="20%">{$lang.step}</th>
	<th width="20%">{$lang.group_name}</th>
	<th>{$lang.default_configuration_products}</th>
	<th width="1%">{$lang.required}</th>
</tr>
{foreach from=$conf_product_groups item="po"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
		<input type="checkbox" name="group_ids[]" value="{$po.group_id}" class="checkbox cm-item" /></td>
	<td class="center">
		<input type="text" name="conf_product_groups[{$po.group_id}][position]" value="{$po.position}" size="3" class="input-text-short" /></td>
	<td class="nowrap">{$po.step_name}</td>
	<td class="nowrap">{$po.configurator_group_name}</td>
	<td class="nowrap">
			{if $po.configurator_group_type == "S" || $po.configurator_group_type == "R"}
				{if $po.products}
					<select name="conf_product_groups[{$po.group_id}][default_product_ids][]" id="products_{$po.group_id}">
						<option value="0">{$lang.none}</option>
						{foreach from=$po.products item="group_product"}
						<option value="{$group_product.product_id}" {if $group_product.default == "Y"} selected="selected" {/if}>{$group_product.product}</option>
						{/foreach}
					</select>
				{else}
					{$lang.text_no_items_defined|replace:"[items]":$lang.products}
				{/if}
			{elseif $po.configurator_group_type == "C"}
				{if $po.products}
					<select name="conf_product_groups[{$po.group_id}][default_product_ids][]" multiple="multiple" id="products_{$po.group_id}">
						{foreach from=$po.products item="group_product"}
						<option value="{$group_product.product_id}" {if $group_product.default == "Y"} selected="selected" {/if}>{$group_product.product}</option>
						{/foreach}
					</select>
				{else}
					{$lang.text_no_items_defined|replace:"[items]":$lang.products}
				{/if}
			{/if}	
	</td>
	<td class="center" width="1%">
		<input type="hidden" name="conf_product_groups[{$po.group_id}][required]" value="N" />
		<input type="checkbox" id="required_{$po.group_id}" name="conf_product_groups[{$po.group_id}][required]" value="Y" {if $po.required == "Y"}checked="checked"{/if} class="checkbox" /></td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="6"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

	<div class="buttons-container buttons-bg">
	{if $conf_product_groups}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[products.delete_configurator_groups]" class="cm-process-items cm-confirm" rev="configurator_products_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[products.update_conf_groups]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	{if $configurator_groups}	
	<div class="float-right">
		{capture name="add_new_picker"}
		<div class="object-container">
		<fieldset>
			<div class="form-field">
				<label for="add_group_id">{$lang.group}:</label>
				<select name="add_group_id" id="add_group_id">
					{foreach from=$configurator_groups item="group_" key="group_id"}
					<option value="{$group_.group_id}">{$group_.configurator_group_name}</option>
					{/foreach}
				</select>
			</div>
		</fieldset>
		</div>

		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_text=$lang.add but_name="dispatch[products.apply_conf_group]" cancel_action="close"}
		</div>
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_pconf_group" text=$lang.add_product_group link_text=$lang.add_product_group content=$smarty.capture.add_new_picker act="general"}
	</div>
	{/if}
	</div>


</form>

</div>
{** /Product groups section **}
