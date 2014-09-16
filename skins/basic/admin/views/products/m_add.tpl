{* $Id: m_add.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="products_m_addition_form">

<table cellpadding="0" cellspacing="0" width="100%"	border="0" class="table">
<tr class="cm-first-sibling">
	<th>{$lang.main_category}</th>
	<th>{$lang.product_name}</th>
	<th>{$lang.price}</th>
	<th>{$lang.list_price}</th>
{*	<th>{$lang.short_description}</th> *}
	<th>{$lang.position_short}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>

<tr class="table-row" id="box_new_product">
	<td width="20%">
		{if "categories"|fn_show_picker:$smarty.const.CATEGORY_SHOW_ALL}
			{include file="pickers/categories_picker.tpl" data_id="location_category" input_name="products_data[0][main_category]" item_ids=$default_category_id hide_link=true hide_delete_button=true}
		{else}
		<select	name="products_data[0][main_category]">
			{foreach from=0|fn_get_plain_categories_tree:false item="cat"}
			<option	value="{$cat.category_id}">{$cat.category|escape|indent:$cat.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
			{/foreach}
		</select>
		{/if}
	</td>
	<td width="50%"><input type="text" name="products_data[0][product]" value="" class="input-text-long" /></td>
	<td><input type="text" name="products_data[0][price]" size="4" value="0.00" class="input-text-medium" /></td>
	<td><input type="text" name="products_data[0][list_price]" size="4" value="0.00" class="input-text-medium" /></td>
	<td><input type="text" name="products_data[0][position]" size="3" value="0" class="input-text-short" /></td>
	<td>
		<select name="products_data[0][status]">
			<option value="A">{$lang.active}</option>
			<option value="H">{$lang.hidden}</option>
			<option value="D">{$lang.disabled}</option>
		</select>
	</td>
	<td>
		{include file="buttons/multiple_buttons.tpl" item_id="new_product"}</td>
</tr>
</table>

<div class="buttons-container buttons-bg">
	{include file="buttons/create.tpl" but_name="dispatch[products.m_add]" but_role="button_main"}
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.add_products content=$smarty.capture.mainbox}

