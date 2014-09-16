{* $Id: block_element.tpl 10510 2010-08-25 13:41:54Z 2tl $ *}

{if $block_data && !$block_data.disabled}
<div class="cm-list-box{if $block_data.properties.static_block} cm-tabs-block{/if} base-block">
	<input type="hidden" name="block_positions[]" class="block-position" value="{$block_data.block_id}" />

	{strip}
	{if ($location == "all_pages" || $block_data.location != "all_pages") && !$block_data.properties.static_block}
	<div><a class="float-right cm-confirm delete-block" href="{"block_manager.delete?selected_section=`$location`&amp;block_id=`$block_data.block_id`"|fn_url}"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" title="{$lang.delete}" alt="{$lang.delete}" /></a></div>
	{/if}
	<h4><strong>
	{assign var="block_content_id" value="block_content_`$block_data.block_id`"}
	{if !$block_data.properties.static_block}
	<img src="{$images_dir}/icons/icon_show.gif" width="13" height="13" border="0" alt="" id="on_{$block_content_id}" class="cm-combination cm-save-state{if $smarty.cookies.$block_content_id} hidden{/if}" /><img src="{$images_dir}/icons/icon_hide.gif" width="13" height="13" border="0" alt="" id="off_{$block_content_id}" class="cm-combination cm-save-state{if !$smarty.cookies.$block_content_id} hidden{/if}" />
	{/if}
	{$block_data.description}
	</strong></h4>
	{/strip}
	
	<div id="{$block_content_id}" class="block-container clear{if !$smarty.cookies.$block_content_id} hidden{/if}">
		<div class="block-content">
		{if !$block_data.properties.static_block}
			{if $block_data.properties.list_object}
				<p><label>{$lang.content}:</label>
				<span class="lowercase">{$block_data.properties.content_name|default:$block_data.properties.list_object}</span></p>
			{/if}
	
			{if $block_data.properties.fillings}
				<p><label>{$lang.filling}:</label>
				{$block_data.properties.fillings}</p>
			{/if}
	
			{if $block_data.properties.appearances}
				<p><label>{$lang.appearance}:</label>
				{$block_data.properties.appearances}</p>
			{/if}
	
			{if $block_data.properties.wrapper}
				<p><label>{$lang.wrapper}:</label>
				{$block_data.properties.wrapper}</p>
			{/if}
	
			{if $block_data.properties.fillings == "manually" && $block_data.location|fn_check_static_location}
				<div class="info-line">
					<div class="float-right">
						{include file=$block_settings.dynamic[$block_data.properties.list_object].picker_props.picker data_id="`$block_data.block_id``$block_data.block_type`_`$block_data.location`" checkbox_name="block_items" extra_var="dispatch=block_manager.add_items&block_id=`$block_data.block_id`&block_location=`$block_data.location`&redirect_location=$location" no_container=true view_mode="button" params_array=$block_settings.dynamic[$block_data.properties.list_object].picker_props.params}
					</div>
					{$lang.items_in_block}:&nbsp;
					{include file="buttons/button.tpl" but_text="&nbsp;&nbsp;`$block_data.items_count`&nbsp;" but_href="block_manager.manage_items?block_id=`$block_data.block_id`&amp;location=`$location`" but_role="link" but_onclick="jQuery.ajaxRequest(this.href, `$ldelim`callback: fn_show_block_picker, result_ids: 'content_edit_block_picker', caching: true`$rdelim`)" but_meta="text-button"}
				</div>
			{/if}

			{if !$block_data.location|fn_check_static_location}
				<div class="info-line">
					{assign var="but_text" value=$lang.manage_products|replace:"products":$block_data.location}
					{$lang.text_switch_to_details_page|replace:"[product]":$block_data.location}.
					<p>
					{if $block_data.assigned_to}
						<a href="{"`$block_data.location`.manage?b_id=`$block_data.block_id`"|fn_url}">{$lang.assigned_to_objects|replace:"[objects]":$block_data.assigned_to}</a>
					{else}
						{$lang.assigned_to_objects|replace:"[objects]":$block_data.assigned_to}
					{/if}
					</p>
					<p class="right">
						{include file="buttons/button.tpl" but_text=$lang.assign_to_all but_href="block_manager.bulk_actions.assign_to_all?block_id=`$block_data.block_id`&amp;selected_section=`$smarty.request.selected_section`" but_role="simple"}&nbsp;/{include file="buttons/button.tpl" but_text=$lang.remove_from_all but_href="block_manager.bulk_actions.remove_from_all?block_id=`$block_data.block_id`&amp;selected_section=`$smarty.request.selected_section`" but_role="simple"}
					</p>
					<p class="right">{include file="buttons/button.tpl" but_text=$but_text but_href="`$block_data.location`.manage" but_role="simple"}</p>
				</div>
			{/if}
	
			<div class="break">
				<div class="float-right">
				{if $block_data.location == "all_pages" && $smarty.request.selected_section && $smarty.request.selected_section != "all_pages"}
					{if $block_data.status == "A"}
						{assign var="_block_id" value=$block_data.block_id}
					{else}
						{assign var="_block_id" value=0}
					{/if}
	
					{if $block_data.disabled_locations|strpos:$smarty.request.selected_section === false && $block_data.status == "A"}
						{assign var="status" value="A"}
					{else}
						{assign var="status" value="D"}
					{/if}
	
					{include file="common_templates/select_popup.tpl" id=$_block_id status=$status object_id_name="block_id" table="blocks" update_controller="block_manager" items_status="A: `$lang.active`, D: `$lang.disabled`" extra="&amp;selected_location=`$smarty.request.selected_section`&amp;block_location=`$block_data.location`"}
				{else}
					{include file="common_templates/select_popup.tpl" id=$block_data.block_id status=$block_data.status object_id_name="block_id" table="blocks"}
				{/if}
				</div>
	
				{include file="common_templates/object_group.tpl" content=$smarty.capture.update_block id="`$block_data.block_id``$block_data.block_type`_`$location`" no_table=true but_name="dispatch[block_manager.update]" href="block_manager.update?block_id=`$block_data.block_id`&amp;location=$location&amp;selected_section=`$location`" header_text="`$lang.editing_block`: `$block_data.description`" opener_ajax_class="cm-ajax" link_class="cm-ajax-force" picker_meta="cm-clear-content"}
			</div>
		{/if}
		</div>
	</div>

	<div class="block-bottom"><p class="no-margin">&nbsp;</p></div>
</div>
{/if}