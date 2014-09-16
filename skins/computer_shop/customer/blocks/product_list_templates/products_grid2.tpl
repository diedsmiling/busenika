{* $Id: products.tpl 8327 2009-11-27 09:11:44Z angel $ *}
{** template-description:products_grid2 **}

{include file="blocks/list_templates/products_grid2.tpl" 
show_trunc_name=true 
show_sku=true 
show_rating=true 
show_old_price=true 
show_price=true 
show_clean_price=true 
show_add_to_cart=$show_add_to_cart|default:true 
show_list_buttons=true 
but_role="action" 
show_qty=true 
separate_buttons=true 
no_pagination=$no_pagination}
