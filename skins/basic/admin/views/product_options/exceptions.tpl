{* $Id: exceptions.tpl 10176 2010-07-23 05:38:29Z angel $ *}

{** Options exceptions section **}

{script src="js/picker.js"}

{capture name="mainbox"}

{notes}
	{$lang.text_exception_note}
{/notes}

<form action="{""|fn_url}" method="post" name="exceptions_form">
<input type="hidden" name="product_id" value="{$product_id}" />

{include file="common_templates/pagination.tpl"}

<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.combination}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$exceptions item="i"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center"><input type="checkbox" name="exception_ids[]" value="{$i.exception_id}" class="checkbox cm-item" /></td>
	<td>
		<table>
		{foreach from=$i.combination item="c" key="k"}
		{if ($product_options.$k.option_type == "S") || ($product_options.$k.option_type == "R") || ($product_options.$k.option_type == "C")}
		<tr>
			<td>{$product_options.$k.option_name}:</td>
			<td><strong>
				{if $product_options.$k.option_type == "C"}
					{if ($c == "-2")}- {$lang.exception_disabled} -
					{elseif ($c == "-1")}- {$lang.exception_disregard} -
					{else}[{if $product_options.$k.variants.$c.position == "1"}{$lang.yes}{else}{$lang.no}{/if}]{/if}
				{else}
					{if ($c == "-2")}- {$lang.exception_disabled} -
					{elseif ($c == "-1")}- {$lang.exception_disregard} -
					{else}{$product_options.$k.variants.$c.variant_name}{/if}
				{/if}
				</strong>
			</td>
		</tr>
		{/if}
		{/foreach}
		</table>
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"product_options.delete_exception?exception_id=`$i.exception_id`&amp;product_id=`$product_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$i.exception_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="3"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $exceptions}
	<div class="float-left">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[product_options.delete_exceptions]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/popupbox.tpl" id="add_new_combination" text=$lang.add_new_combination link_text=$lang.add_combination act="general"}
	</div>
</div>

</form>


{capture name="tools"}
	{capture name="add_new_picker"}
		<form action="{""|fn_url}" method="post" name="new_exception_form">
		<input type="hidden" name="product_id" value="{$product_id}" />

		<div class="object-container">
		<table cellpadding="0" cellspacing="0" border="0" class="add-new-table">
		<tr class="cm-first-sibling">
			<th>{$lang.combination}</th>
			<th>&nbsp;</th>
		</tr>
		<tr id="box_new_item">
			<td>
				<table>
				{foreach from=$product_options item="option" name="add_inv_fe"}
				<tr class="no-border">
					<td>{$option.option_name}</td>
					<td>{if $option.option_type == "C" }
							<select name="add_options_combination[0][{$option.option_id}]">
								{foreach from=$option.variants item="variant"}
									<option value="{$variant.variant_id}">{if $variant.position == 0}{$lang.no}{else}{$lang.yes}{/if}</option>
								{/foreach}
								<option value="-1">- {$lang.exception_disregard} -</option>
								<option value="-2">- {$lang.exception_disabled} -</option>
							</select>
						{else}
							<select name="add_options_combination[0][{$option.option_id}]">
								{foreach from=$option.variants item="variant"}
								<option value="{$variant.variant_id}">{$variant.variant_name}</option>
									{/foreach}
								<option value="-1">- {$lang.exception_disregard} -</option>
								<option value="-2">- {$lang.exception_disabled} -</option>
							</select>
						{/if}
					</td>
				</tr>
				{/foreach}
				</table>
			</td>
			<td valign="top"><p>{include file="buttons/multiple_buttons.tpl" item_id="new_item"}</p></td>
		</tr>
		</table>
		</div>
		<div class="buttons-container">
			{include file="buttons/create.tpl" but_name="dispatch[product_options.add_exceptions]" but_role="button_main"}
		</div>

		</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_combination" text=$lang.add_new_combination content=$smarty.capture.add_new_picker link_text=$lang.add_combination act="general"}
{/capture}

{/capture}

{if $product_data.exceptions_type == "F"}
	{assign var="except_title" value=$lang.forbidden_combinations}
{else}
	{assign var="except_title" value=$lang.allowed_combinations}
{/if}

{include file="common_templates/mainbox.tpl" title=$except_title content=$smarty.capture.mainbox tools=$smarty.capture.tools}

{** /Options exceptions section **}
