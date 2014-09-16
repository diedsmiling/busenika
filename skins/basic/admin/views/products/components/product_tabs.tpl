{* $Id: one_product.tpl 7394 2009-04-29 11:43:22Z zeke $ *}

{include file="views/block_manager/components/scripts.tpl"}
{include file="common_templates/popupbox.tpl" text=$lang.editing_block content=$content id="edit_block_picker" edit_picker=true}

{assign var="cur_url" value=$config.current_url|fn_query_remove:"selected_section"}
{assign var="cur_url" value="`$cur_url`&selected_section=blocks"}
{assign var="c_url" value=$cur_url|escape:url}

<div class="block-manager products-block-manager">
	{include file="views/products/components/product_tabs_group_element.tpl" blocks_target="top" main_class="" redirect_url=$c_url}
	<div class="clear">
	{hook name="block_manager:columns"}
		{include file="views/products/components/product_tabs_group_element.tpl" blocks_target="left" main_class="float-left" redirect_url=$c_url}
		{include file="views/products/components/product_tabs_group_element.tpl" blocks_target="central" main_class="float-left" redirect_url=$c_url non_editable=true}
		{include file="views/products/components/product_tabs_group_element.tpl" blocks_target="right" main_class="float-left" redirect_url=$c_url}
	{/hook}
	</div>
	{include file="views/products/components/product_tabs_group_element.tpl" blocks_target="bottom" main_class="" redirect_url=$c_url}
</div>

{include file="views/block_manager/components/sortable_scripts.tpl"}

{capture name="add_new_block"}
	{include file="views/block_manager/update.tpl" add_block=true block_type="B" block=null redirect_url=$cur_url}
{/capture}
{capture name="add_new_group"}
	{include file="views/block_manager/update.tpl" add_block=true block_type="G" block=null redirect_url=$cur_url}
{/capture}

<div class="buttons-container">
	<div class="float-right">
		{include file="common_templates/popupbox.tpl" id="add_new_block" text=$lang.new_block content=$smarty.capture.add_new_block link_text=$lang.add_block act="general"}
		{include file="common_templates/popupbox.tpl" id="add_new_group" text=$lang.new_group content=$smarty.capture.add_new_group link_text=$lang.add_group act="general"}
	</div>
</div>