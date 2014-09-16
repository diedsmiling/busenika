{* $Id: select.tpl 6986 2009-03-10 13:35:00Z zeke $ *}

{if $workflow.object == "products"}
	{include file="pickers/products_picker.tpl" input_name="workflow_data[elements]" data_id="added_products" item_ids=$workflow.elements_data type="links" no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.products}
{elseif $workflow.object == "categories"}
	{include file="pickers/categories_picker.tpl" input_name="workflow_data[elements]" data_id="added_categories" multiple=true item_ids=$workflow.elements_data no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.categories}
{elseif $workflow.object == "pages"}
	{include file="pickers/pages_picker.tpl" input_name="workflow_data[elements]" data_id="added_pages" multiple=true item_ids=$workflow.elements_data no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.pages}
{/if}
{hook name="revisions_workflow:objects"}
{/hook}
