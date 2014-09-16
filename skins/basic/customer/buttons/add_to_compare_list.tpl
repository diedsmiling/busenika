{* $Id: add_to_compare_list.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $settings.DHTML.ajax_comparison_list == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

{if  !$hide_compare_list_button}
	{assign var="c_url" value=$config.current_url|escape:url}
	{include file="buttons/button.tpl" but_text=$lang.add_to_compare_list but_href="product_features.add_product?product_id=$product_id&redirect_url=$c_url" but_role="text" but_rev="comparison_list" but_meta=$ajax_class}
{/if}
