{* $Id: js_product.tpl 9910 2010-06-30 08:22:42Z angel $ *}

<tr {if !$clone}id="{$root_id}_{$delete_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
<td>
	<ul>
		<li>{$product}
			{if !$view_only}
				<a href="javascript: jQuery.delete_js_item('{$root_id}', '{$delete_id}', 'p');"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="" /></a>
			{/if}
		</li>
		{if $options}
		<li>{$options}</li>
		{/if}
	</ul>
	{if $options_array|is_array}
		{foreach from=$options_array item="option" key="option_id"}
		<input type="hidden" name="{$input_name}[product_options][{$option_id}]" value="{$option}"{if $clone} disabled="disabled"{/if} />
		{/foreach}
	{/if}
	{if $product_id}
		<input type="hidden" name="{$input_name}[product_id]" value="{$product_id}"{if $clone} disabled="disabled"{/if} />
	{/if}
	{if $amount_input == "hidden"}
	<input type="hidden" name="{$input_name}[amount]" value="{$amount}"{if $clone} disabled="disabled"{/if} />
	{/if}
</td>
	{if $amount_input == "text"}
<td class="center">
	<input type="text" name="{$input_name}[amount]" value="{$amount}" size="3" class="input-text-short{if $clone} disabled{/if}"{if $clone} disabled="disabled"{/if} />
</td>
	{/if}
</tr>