{* $Id: feature_comparison.tpl 10486 2010-08-23 07:05:50Z andyye $ *}
{** block-description:feature_comparison **}

<!--dynamic:feature_comparison-->
<div id="comparison_list">

{if $compared_products}
<ul class="bullets-list">
	{foreach from=$compared_products item="product"}
		<li><a {if $product.product_id == $new_product}id="blinking_elm{$block.block_id}"{/if} href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="underlined">{$product.product|unescape}</a></li>
	{/foreach}
</ul>

<div class="clear">
	<div class="float-left">
		{include file="buttons/button.tpl" but_text=$lang.compare but_href="product_features.compare" but_role="text"}
	</div>

	<div class="float-right">
		{if $settings.DHTML.ajax_comparison_list == "Y"}
			{assign var="ajax_class" value="cm-ajax"}
		{/if}
		{assign var="c_url" value=$config.current_url|escape:url}
		{if $mode == "compare"}
			{include file="buttons/button.tpl" but_text=$lang.clear_list but_href="product_features.clear_list?redirect_url=$index_script" but_role="text"}
		{else}
			{include file="buttons/button.tpl" but_text=$lang.clear_list but_href="product_features.clear_list?redirect_url=$c_url" but_rev="comparison_list" but_meta=$ajax_class but_role="text"}
		{/if}
	</div>
</div>
{else}
{capture name="hide_wrapper"}Y{/capture}
{/if}

<!--comparison_list--></div>
<!--/dynamic-->