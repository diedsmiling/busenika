{* $Id: product_tabs_group_element.tpl 8248 2009-11-17 11:36:32Z lexa $ *}
{foreach from=$blocks item="block_data"}
{if ($block_data.text_id == $blocks_target || $block_data.block_id == $blocks_target) && $block_data.block_type == "G"}
	{if $block_data.text_id}
	<div id="{$blocks_target}_column_holder"{if $main_class} class="{$main_class}"{/if}>
		<div class="block-manager">
			{if $block_data.text_id == "product_details"}
				<input type="hidden" name="block_positions[]" class="block-position" value="{$block_data.block_id}" />
				{assign var="def_id" value="block_content_`$block_data.block_id`"}
			{else}
				{assign var="def_id" value=$block_data.text_id}
			{/if}
			<h2>{$block_data.description}</h2>
			<input type="hidden" name="group_id_{$def_id}" value="{$block_data.block_id}" />
			<div id="{$def_id}" class="cm-sortable-items grab-items{if $block_data.text_id == "product_details"} cm-disallowed-group cm-product-details{/if}">
	{else}
	<div id="blocks_group_{$blocks_target}" class="cm-list-box cm-group-box block-container base-block">
		<div class="block-manager">
			<input type="hidden" name="block_positions[]" class="block-position" value="{$block_data.block_id}" />
			<h4 class="group-header">
				<strong>
				{assign var="block_content_id" value="block_content_`$block_data.block_id`"}
				<img src="{$images_dir}/icons/icon_show.gif" width="13" height="13" border="0" alt="" id="on_{$block_content_id}" class="cm-combination cm-save-state{if $smarty.cookies.$block_content_id} hidden{/if}" /><img src="{$images_dir}/icons/icon_hide.gif" width="13" height="13" border="0" alt="" id="off_{$block_content_id}" class="cm-combination cm-save-state{if !$smarty.cookies.$block_content_id} hidden{/if}" />
				{$block_data.description}
			</strong></h4>
			<input type="hidden" name="group_id_{$block_content_id}" value="{$block_data.block_id}" />
			<div id="{$block_content_id}" class="{if !$smarty.cookies.$block_content_id}hidden{/if} cm-sortable-items grab-items group-content cm-disallowed-group">
	{/if}
			<div class="cm-list-box list-box-invisible"></div>
			{assign var="_not_empty" value=false}
			{foreach from=$blocks item="inner_block"}
				{if $inner_block.group_id == $block_data.block_id}
					{if $inner_block.block_type == "B"}
						{if $inner_block.text_id == "central_content"}
							<div class="cm-list-box central-content">
								<h3>{$inner_block.description}</h3>
								<input type="hidden" name="block_positions[]" class="block-position" value="{$inner_block.block_id}" />
								<div class="block-content clear">
								{if $inner_block.properties.wrapper}
									<p><label>{$lang.wrapper}:</label>
									{$inner_block.properties.wrapper}</p>
								{/if}

								{include file="common_templates/object_group.tpl" content=$smarty.capture.update_block id="`$inner_block.block_id``$inner_block.block_type`_`$location`" no_table=true but_name="dispatch[block_manager.update]" href="block_manager.update?block_id=`$inner_block.block_id`&amp;location=`$location`&amp;object_id=`$object_id`&amp;r_url=`$redirect_url`" header_text="`$lang.editing_block`: `$inner_block.description`"}
								</div>
							</div>
							{foreach from=$blocks item="_block"}
								{if $_block.text_id == "product_details"}
									{include file="views/products/components/product_tabs_group_element.tpl" blocks_target=$_block.block_id main_class="product-tabs"}
								{/if}
							{/foreach}
						{else}
							{include file="views/products/components/product_tabs_element.tpl" block_data=$inner_block}
						{/if}
					{elseif $inner_block.text_id != "product_details"}
					{include file="views/products/components/product_tabs_group_element.tpl" blocks_target=$inner_block.block_id main_class=""}
					{/if}
					{if $inner_block.block_type == "B" && !$inner_block.disabled || $inner_block.block_type == "G"}
						{assign var="_not_empty" value=true}
					{/if}
				{/if}
			{/foreach}

			<p class="no-items{if $_not_empty} hidden{/if}">{$lang.no_blocks}</p>
			<div class="cm-list-box list-box-invisible"></div>
		</div>
	</div>
</div>
{/if}
{/foreach}