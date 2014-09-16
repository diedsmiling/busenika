{* $Id: product_info.post.tpl 9196 2010-03-30 13:03:21Z lexa $ *}

{if $product.returns_info}
	{if !$return_statuses}{assign var="return_statuses" value=$smarty.const.STATUSES_RETURN|fn_get_statuses:true}{/if}
	
	<p><img src="{$images_dir}/icons/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_ret_{$key}" class="hand cm-combination" /><img src="{$images_dir}/icons/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_ret_{$key}" class="hand cm-combination hidden" /><a class="cm-combination" id="sw_ret_{$key}">{$lang.returns_info}</a></p>
	<div class="box hidden" id="ret_{$key}">
		{foreach from=$product.returns_info item="amount" key="status" name="f_rinfo"}
			<p><strong>{$return_statuses.$status|default:""}</strong>:&nbsp;{$amount} {$lang.items}</p>
		{/foreach}	
	</div>
{/if}