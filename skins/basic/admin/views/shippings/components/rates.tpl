{* $Id: rates.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}

<form action="{""|fn_url}" method="post" name="rates_form">
<input type="hidden" name="shipping_id" value="{$smarty.request.shipping_id}" />
<input type="hidden" name="rate_id" value="{$rate_data.rate_id}" />
<input type="hidden" name="destination_id" value="{$destination_id}" />
<input type="hidden" name="selected_section" value="shipping_charges" />

{if $shipping.rate_calculation == "M"}

{split data=$destinations size="6" assign="splitted_destinations"}

<div class="dashed-border">
	<p>{$lang.show_rate_for_destination}:</p>
	{foreach from=$splitted_destinations item="sdests"}
	<p>
	{foreach from=$sdests item="destination"}
		{if $destination}
			<span class="bull">&bull;</span>&nbsp;{if $destination_id == $destination.destination_id}<strong>{$destination.destination}</strong>{else}<a href="{"shippings.update?shipping_id=`$smarty.request.shipping_id`&amp;destination_id=`$destination.destination_id`&amp;selected_section=shipping_charges"|fn_url}">{$destination.destination}</a>{/if}&nbsp;{if $destination.rates_defined}(+){else}{/if}&nbsp;&nbsp;&nbsp;
		{else}
			&nbsp;
		{/if}
	{/foreach}
	</p>
	{/foreach}
</div>

{/if}
{include file="common_templates/subheader.tpl" title=$lang.cost_dependences}

<table cellpadding="0" cellspacing="0" width="100%"	border="0" class="table">
<tr class="cm-first-sibling">
	<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items-c" /></th>
	<th>{$lang.products_cost}</th>
	<th>{$lang.rate_value}</th>
	<th width="100%">{$lang.type}</th>
	<th>{hook name="shippings:cost_dependences_head"}{/hook}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$rate_data.rate_value.C item="rate" key="k" name="rdf"}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<input type="checkbox" name="delete_rate_data[C][{$k}]" value="Y" {if $smarty.foreach.rdf.first}disabled="disabled"{/if} class="checkbox cm-item-c cm-item" /></td>
	<td class="nowrap">
		{$lang.more_than}&nbsp;{$currencies.$primary_currency.symbol}
		{if $smarty.foreach.rdf.first}
			<input type="hidden" name="rate_data[C][0][amount]" value="0" />0
		{else}
			<input type="text" name="rate_data[C][{$k}][amount]" size="5" value="{$k}" class="input-text" />
		{/if}
	</td>
	<td>
		<input type="text" name="rate_data[C][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][value]" size="5" value="{$rate.value|default:"0"}" class="input-text" /></td>
	<td>
		<select name="rate_data[C][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][type]">
			<option value="F" {if $rate.type == "F"}selected="selected"{/if}>{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
			<option value="P" {if $rate.type == "P"}selected="selected"{/if}>{$lang.percent} (%)</option>
		</select></td>
	<td>{hook name="shippings:cost_dependences_body"}{/hook}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		{if !$smarty.foreach.rdf.first}
			<li><a class="cm-confirm" href="{"shippings.delete_rate_value?rate_type=C&amp;amount=`$k`&amp;shipping_id=`$smarty.request.shipping_id`&amp;destination_id=`$destination_id`&amp;rate_id=`$rate_data.rate_id`"|fn_url}">{$lang.delete}</a></li>
		{else}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$smarty.foreach.rdf.iteration tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="6">
	<input type="hidden" name="rate_data[C][0][amount]" value="0" />
	<input type="hidden" name="rate_data[C][0][value]" value="0" />
	<input type="hidden" name="rate_data[C][0][type]" value="F" />
	<p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="clear">
	{capture name="add_new_picker"}
	<div class="object-container">
		<table cellpadding="0" cellspacing="0" border="0" class="table">
		<tr id="box_upd_rate_celm">
			<td>
				{$lang.more_than}&nbsp;{$currencies.$primary_currency.symbol}&nbsp;<input type="text" name="add_rate_data[C][0][amount]" size="5" value="" class="input-text" />
				<input type="text" name="add_rate_data[C][0][value]" size="5" value="" class="input-text" />
				<select name="add_rate_data[C][0][type]">
					<option value="F">{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
					<option value="P">{$lang.percent} (%)</option>
				</select></td>
			<td>
			{hook name="shippings:cost_dependences_new"}
			{/hook}
			</td>
			<td>{include file="buttons/multiple_buttons.tpl" item_id="upd_rate_celm"}</td>
		</tr>
		</table>
	</div>

	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[shippings.update_rates]" cancel_action="close"}
	</div>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_cost_dependences" text=$lang.add_cost_dependences content=$smarty.capture.add_new_picker link_text=$lang.add_cost_dependences act="general"}
</div>

{include file="common_templates/subheader.tpl" title=$lang.weight_dependences}

<table cellpadding="0" cellspacing="0" width="100%"	border="0" class="table">
<tr class="cm-first-sibling">
	<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items-w" /></th>
	<th width="150">{$lang.products_weight}</th>
	<th>{$lang.rate_value}</th>
	<th>{$lang.type}</th>
	<th>{$lang.per|replace:"[object]":$settings.General.weight_symbol}</th>
	<th>{hook name="shippings:weight_dependences_head"}{/hook}</th>
	<th width="100%">&nbsp;</th>
</tr>
{foreach from=$rate_data.rate_value.W item="rate" key="k" name="rdf"}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<input type="checkbox" name="delete_rate_data[W][{$k}]" id="delete_checkbox_weight" value="Y" {if $smarty.foreach.rdf.first}disabled="disabled"{/if} class="checkbox cm-item-w cm-item" /></td>
	<td class="nowrap">
		{$lang.more_than}
		{if $smarty.foreach.rdf.first}
			<input type="hidden" name="rate_data[W][0][amount]" value="0" />0
		{else}
			<input type="text" name="rate_data[W][{$k}][amount]" size="5" value="{$k}" class="input-text" />
		{/if}
		{$settings.General.weight_symbol}
	</td>
	<td>
		<input type="text" name="rate_data[W][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][value]" size="5" value="{$rate.value|default:"0"}" class="input-text" /></td>
	<td>
		<select name="rate_data[W][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][type]">
			<option value="F" {if $rate.type == "F"}selected="selected"{/if}>{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
			<option value="P" {if $rate.type == "P"}selected="selected"{/if}>{$lang.percent} (%)</option>
		</select></td>
	<td class="center">
		<input type="hidden" name="rate_data[W][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][per_unit]" value="N" />
		<input type="checkbox" name="rate_data[W][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][per_unit]" value="Y" {if $rate.per_unit == "Y"}checked="checked"{/if} class="checkbox" /></td>
	<td>{hook name="shippings:weight_dependences_body"}{/hook}</td>
	<td class="nowrap right">
		{capture name="tools_items"}
		{if !$smarty.foreach.rdf.first}
			<li><a class="cm-confirm" href="{"shippings.delete_rate_value?rate_type=W&amp;amount=`$k`&amp;shipping_id=`$smarty.request.shipping_id`&amp;destination_id=`$destination_id`&amp;rate_id=`$rate_data.rate_id`"|fn_url}">{$lang.delete}</a></li>
		{else}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$smarty.foreach.rdf.iteration tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="7">
	<input type="hidden" name="rate_data[W][0][amount]" value="0" />
	<input type="hidden" name="rate_data[W][0][value]" value="0" />
	<input type="hidden" name="rate_data[W][0][type]" value="F" />
	<p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="clear">
	{capture name="add_new_picker"}
	<div class="object-container">
		<table cellpadding="1" cellspacing="0" border="0" class="table">
		<tr id="box_upd_rate_welm">
			<td>
				{$lang.more_than}&nbsp;<input type="text" name="add_rate_data[W][0][amount]" size="5" value="" class="input-text" />&nbsp;{$settings.General.weight_symbol}
				<input type="text" name="add_rate_data[W][0][value]" size="5" value="" class="input-text" />
				<select name="add_rate_data[W][0][type]">
					<option value="F">{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
					<option value="P">{$lang.percent} (%)</option>
				</select>
				<input type="hidden" name="add_rate_data[W][0][per_unit]" value="N" />
				<input type="checkbox" name="add_rate_data[W][0][per_unit]" value="Y" class="checkbox" />
			</td>
			<td>
			{hook name="shippings:weight_dependences_new"}
			{/hook}
			</td>
			<td>
				{include file="buttons/multiple_buttons.tpl" item_id="upd_rate_welm"}</td>
		</tr>
		</table>
	</div>

	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[shippings.update_rates]" cancel_action="close"}
	</div>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_weight_dependences" text=$lang.add_weight_dependences content=$smarty.capture.add_new_picker link_text=$lang.add_weight_dependences act="general"}
</div>

{include file="common_templates/subheader.tpl" title=$lang.items_dependences}

<table cellpadding="0" cellspacing="0" width="100%"	border="0" class="table">
<tr class="cm-first-sibling">
	<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items-i" /></th>
	<th width="150">{$lang.products_amount}</th>
	<th>{$lang.rate_value}</th>
	<th>{$lang.type}</th>
	<th>{$lang.per|replace:"[object]":$lang.item}</th>
	<th>{hook name="shippings:items_dependences_head"}{/hook}</th>
	<th width="100%">&nbsp;</th>
</tr>
{foreach from=$rate_data.rate_value.I item="rate" key="k" name="rdf"}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<input type="checkbox" name="delete_rate_data[I][{$k}]" id="delete_checkbox_items" value="Y" {if $smarty.foreach.rdf.first}disabled="disabled"{/if} class="checkbox cm-item-i cm-item" /></td>
	<td class="nowrap">
		{$lang.more_than}
		{if $smarty.foreach.rdf.first}
			<input type="hidden" name="rate_data[I][0][amount]" value="0" />0
		{else}
			<input type="text" name="rate_data[I][{$k}][amount]" size="5" value="{$k}" class="input-text" />
		{/if}
		{$lang.items}
	</td>
	<td>
		<input type="text" name="rate_data[I][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][value]" size="5" value="{$rate.value|default:"0"}" class="input-text" /></td>
	<td>
		<select name="rate_data[I][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][type]">
			<option value="F" {if $rate.type == "F"}selected="selected"{/if}>{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
			<option value="P" {if $rate.type == "P"}selected="selected"{/if}>{$lang.percent} (%)</option>
		</select></td>
	<td class="center">
		<input type="hidden" name="rate_data[I][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][per_unit]" value="N" />
		<input type="checkbox" name="rate_data[I][{if $smarty.foreach.rdf.first}0{else}{$k}{/if}][per_unit]" value="Y"  {if $rate.per_unit == "Y"}checked="checked"{/if} class="checkbox" /></td>
	<td>{hook name="shippings:items_dependences_body"}{/hook}</td>
	<td class="nowrap right">
		{capture name="tools_items"}
		{if !$smarty.foreach.rdf.first}
			<li><a class="cm-confirm" href="{"shippings.delete_rate_value?rate_type=I&amp;amount=`$k`&amp;shipping_id=`$smarty.request.shipping_id`&amp;destination_id=`$destination_id`&amp;rate_id=`$rate_data.rate_id`"|fn_url}">{$lang.delete}</a></li>
		{else}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$smarty.foreach.rdf.iteration tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="7">
	<input type="hidden" name="rate_data[I][0][amount]" value="0" />
	<input type="hidden" name="rate_data[I][0][value]" value="0" />
	<input type="hidden" name="rate_data[I][0][type]" value="F" />
	<p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="clear">
	{capture name="add_new_picker"}
	<div class="object-container">
		<table cellpadding="1" cellspacing="0" border="0" class="table">
		<tr id="box_upd_rate_ielm">
			<td>
				{$lang.more_than}&nbsp;<input type="text" name="add_rate_data[I][0][amount]" size="5" value="" class="input-text" />&nbsp;{$lang.items}
				<input type="text" name="add_rate_data[I][0][value]" size="5" value="" class="input-text" />
				<select name="add_rate_data[I][0][type]">
					<option value="F">{$lang.absolute} ({$currencies.$primary_currency.symbol})</option>
					<option value="P">{$lang.percent} (%)</option>
				</select>
				<input type="hidden" name="add_rate_data[I][0][per_unit]" value="N" />
				<input type="checkbox" name="add_rate_data[I][0][per_unit]" value="Y" class="checkbox" /></td>
			<td>
			{hook name="shippings:items_dependences_new"}
			{/hook}
			</td>
			<td>
				{include file="buttons/multiple_buttons.tpl" item_id="upd_rate_ielm"}</td>
		</tr>
		</table>
	</div>

	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[shippings.update_rates]" cancel_action="close"}
	</div>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_items_dependences" text=$lang.add_items_dependences content=$smarty.capture.add_new_picker link_text=$lang.add_items_dependences act="general"}
</div>

<div class="buttons-container buttons-bg">
	{capture name="tools_list"}
	<ul>
		<li><a name="dispatch[shippings.delete_rate_values]" class="cm-process-items cm-confirm" rev="rates_form">{$lang.delete_selected}</a></li>
	</ul>
	{/capture}
	{include file="buttons/save.tpl" but_name="dispatch[shippings.update_rates]" but_role="button_main"}
	{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
</div>

</form>
