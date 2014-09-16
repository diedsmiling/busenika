{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}

{if $group}
	{assign var="link_to" value=$group.link_to}
{else}
	{assign var="link_to" value=$smarty.request.link_to}
{/if}

<form action="{""|fn_url}" method="post" name="{$prefix}_group_form" class="cm-form-highlight">
<input type="hidden" name="selected_section" id="selected_section" value="{$prefix}" />
<input type="hidden" name="page" id="page" value="{$current_page}" />
<input type="hidden" name="group[link_to]" value="{$link_to}" />
<input type="hidden" name="group_id" value="{$group.group_id}" />

{include file="common_templates/subheader.tpl" title=$lang.general}

<fieldset>

<div class="form-field">
	<label for="group_name" class="cm-required">{$lang.name}:</label>
	<input type="text" name="group[name]" id="group_name" value="{$group.name}" size="50" class="input-text-large main-input" />
</div>

{include file="common_templates/select_status.tpl" input_name="group[status]" id="group" obj=$group}

{if $link_to == "C"}
	{include file="common_templates/subheader.tpl" title=$lang.categories}
	{if $group.categories}
		{assign var="c_ids" value=$group.categories|array_keys}
	{/if}

	{include file="pickers/categories_picker.tpl" input_name="group[data]" item_ids=$c_ids multiple=true}
	
	{assign var="_link_text" value=$lang.add_group_for_categories}

{elseif $link_to == "P"}
	{include file="common_templates/subheader.tpl" title=$lang.products}

	{include file="pickers/products_picker.tpl" item_ids=$group.product_ids data_id="added_products" input_name="group[data]" type="links"}
	
	{assign var="_link_text" value=$lang.add_group_for_products}

{elseif $link_to == "U"}

	<div class="form-field">
		<label for="url" class="cm-required">{$lang.url}:</label>
		<input type="text" name="group[data]" id="url" value="{$group.url}" size="50" class="input-text" />
	</div>
	{assign var="_link_text" value=$lang.add_url_group}
{/if}

</fieldset>

<div class="buttons-container buttons-bg">
	{if $mode == "add"}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[product_groups.update]"}
	{else}
		<div class="float-left">
			{include file="buttons/save_cancel.tpl" but_name="dispatch[product_groups.update]"}
		</div>
		<div class="float-right">
			{include file="common_templates/tools.tpl" tool_href="product_groups.add?link_to=`$link_to`" prefix="bottom" link_text=$_link_text hide_tools=true}
		</div>
	{/if}
</div>

</form>
{/capture}
{if $mode == "add"}
	{include file="common_templates/mainbox.tpl" title=$lang.new_group content=$smarty.capture.mainbox}
{else}
	{capture name="tools"}
		{include file="common_templates/tools.tpl" tool_href="product_groups.add?link_to=`$link_to`" prefix="top" link_text=$_link_text hide_tools=true}
	{/capture}
	{include file="common_templates/mainbox.tpl" title="`$lang.update`:&nbsp;`$group.name`" content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
{/if}
