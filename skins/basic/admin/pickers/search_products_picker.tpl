{* $Id: search_products_picker.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{if $search.p_ids}
	{assign var="product_ids" value=","|explode:$search.p_ids}
{/if}
<div class="info-line">
	{$lang.any_of}&nbsp;
	{include file="pickers/products_picker.tpl" data_id="added_products" but_text=$lang.add item_ids=$product_ids input_name="p_ids" type="links" no_container=true picker_view=true}
	{assign var="views" value="products"|fn_get_views}
	{if $views}
	{$lang.or_saved_search}:&nbsp;
	<select name="product_view_id">
		<option value="0">--</option>
		{foreach from=$views item="f"}
			<option value="{$f.view_id}" {if $search.product_view_id == $f.view_id}selected="selected"{/if}>{$f.name|escape}</option>
		{/foreach}
	</select>
	{/if}
</div>
