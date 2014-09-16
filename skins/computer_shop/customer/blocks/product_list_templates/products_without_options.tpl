{* $Id: products_without_options.tpl 8188 2009-11-09 10:02:02Z lexa $ *}
{** template-description:list_without_options **}

{if $settings.Appearance.in_stock_field == "N"}
	{assign var="show_product_amount" value=true}
{else}
	{assign var="show_product_amount" value=false}
{/if}
{include file="blocks/list_templates/products_list.tpl" 
show_name=true 
show_sku=true 
show_rating=true 
show_features=true 
show_prod_descr=true 
show_old_price=true 
show_price=true 
show_clean_price=true 
show_list_discount=true 
show_discount_label=true 
show_product_amount=$show_product_amount 
show_product_edp=true 
show_add_to_cart=true 
show_list_buttons=true 
show_descr=true 
but_role="action"
separate_buttons=true}
