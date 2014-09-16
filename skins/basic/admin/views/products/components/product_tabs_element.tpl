{* $Id: block_element.tpl 7164 2009-03-31 11:09:21Z lexa $ *}

{if $block_data && !$block_data.disabled}
<div class="cm-list-box{if $block_data.properties.static_block} cm-tabs-block{/if} base-block">
	<input type="hidden" name="block_positions[]" class="block-position" value="{$block_data.block_id}" />
	{strip}
	{if $block_data.location == "products" && !$block_data.properties.static_block}
	<a class="float-right cm-confirm delete-block" href="{"block_manager.delete?selected_section=`$location`&amp;block_id=`$block_data.block_id`&amp;redirect_url=`$redirect_url`"|fn_url}"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" title="{$lang.delete}" alt="{$lang.delete}" /></a>
	{/if}
	<h4{if $block_data.location != "products"} class="cm-fixed-block"{/if}><strong>
	{assign var="block_content_id" value="block_content_`$block_data.block_id`"}
	{if $block_data.location == "products" && !$block_data.properties.static_block}
	<img src="{$images_dir}/icons/icon_show.gif" width="13" height="13" border="0" alt="" id="on_{$block_content_id}" class="cm-combination cm-save-state{if $smarty.cookies.$block_content_id} hidden{/if}" /><img src="{$images_dir}/icons/icon_hide.gif" width="13" height="13" border="0" alt="" id="off_{$block_content_id}" class="cm-combination cm-save-state{if !$smarty.cookies.$block_content_id} hidden{/if}" />
	{/if}
	{$block_data.description}
	</strong></h4>
	{/strip}

	<div id={$block_content_id} class="block-container clear{if !$smarty.cookies.$block_content_id || $block_data.location != "products"} hidden{/if}">
		<div class="block-content">
			{if $block_data.location == "products" && !$block_data.properties.static_block}
				<p><label>{$lang.filling}:</label>
				{if $block_data.properties.fillings}{$block_data.properties.fillings|fn_get_lang_var}{else}{$lang.static_block}{/if}</p>
	
				<p><label for="enable_block_{$block_data.block_id}">{$lang.enable_for_this_page}:</label>
				<input id="enable_block_{$block_data.block_id}" type="checkbox" name="enable_block_{$block_data.block_id}" value="Y" {if $block_data.assigned == "Y"}checked="checked"{/if} onclick="jQuery.ajaxRequest('{"block_manager.enable_disable?location=`$location`&amp;object_id=`$object_id`&amp;block_id=`$block_data.block_id`&amp;enable="|fn_url:'A':'rel':'&'}' + (this.checked ? this.value : 'N'), {literal}{method: 'POST', cache: false}{/literal});" /></p>

				{if $block_data.properties.fillings == "manually" && $block_settings.dynamic[$block_data.properties.list_object].picker_props.picker || $block_data.properties.per_object}
					<div class="info-line">
						{if !$block_data.properties.per_object}
							<div class="float-right">
								{include file=$block_settings.dynamic[$block_data.properties.list_object].picker_props.picker data_id="`$block_data.block_id``$block_data.block_type`_`$block_data.location`" checkbox_name="block_items" extra_var="dispatch=block_manager.add_items&block_id=`$block_data.block_id`&block_location=`$block_data.location`&object_id=`$object_id`&redirect_location=`$location`&redirect_url=`$redirect_url`" no_container=true view_mode="button" params_array=$block_settings.dynamic[$block_data.properties.list_object].picker_props.params}
							</div>
							{$lang.items_in_block}:&nbsp;
							{assign var="info_line_button_text" value="&nbsp;&nbsp;`$block_data.items_count`&nbsp;"}
							{assign var="additional_url_params" value=""}
						{else}
							{assign var="info_line_button_text" value=$lang.edit}
							{assign var="additional_url_params" value="&product_id=`$object_id`"}
						{/if}

						{include file="buttons/button.tpl" but_text=$info_line_button_text but_href="block_manager.manage_items?block_id=`$block_data.block_id`&amp;location=`$location`&object_id=`$object_id`&redir_url=`$redirect_url``$additional_url_params`" but_role="link" but_onclick="jQuery.ajaxRequest(this.href, `$ldelim`callback: fn_show_block_picker, result_ids: 'content_edit_block_picker', caching: true`$rdelim`)" but_meta="text-button"}
					</div>
				{/if}
			{/if}
		</div>
	</div>

	<div class="block-bottom"><p class="no-margin">&nbsp;</p></div>
</div>
{/if}