{* $Id: object_group.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}

{if !$no_table}
<div class="object-group{$element} clear cm-row-item {$additional_class}">
	<div class="float-right delete">
		{capture name="tool_items"}
			{if $tool_items}
			{$tool_items}
			{/if}
			{if $href_delete && !$skip_delete}
			<li><a href="{$href_delete|fn_url}" rev="{$rev_delete}" class="cm-ajax cm-delete-row cm-confirm lowercase">{$lang.delete}</a></li>
			{elseif $links}
			<li>{$links}</li>
			{else !$href_delete && !$links}
			<li class="undeleted-element"><span>{$lang.delete}</span></li>
			{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" separate=true tools_list=$smarty.capture.tool_items prefix=$id href=""}
	</div>
	<div class="float-right">
{/if}

	{if !$non_editable}
		{include file="common_templates/popupbox.tpl" id="group`$id_prefix``$id`" edit_onclick=$onclick text=$header_text act=$act|default:"edit" picker_meta=$picker_meta}
	{else}	
		<span class="unedited-element block">{$link_text|default:$lang.edit}</span>
	{/if}

{if !$no_table}
	</div>
	{if $status}
	<div class="float-right">
		{include file="common_templates/select_popup.tpl" id=$id status=$status hidden=$hidden object_id_name=$object_id_name table=$table}
	</div>
	{/if}
	<div class="object-name">
		{if $checkbox_name}
			<input type="checkbox" name="{$checkbox_name}" value="{$checkbox_value|default:$id}"{if $checked} checked="checked"{/if} class="checkbox cm-item" />
		{/if}
		<a class="cm-external-click{if $non_editable} no-underline{/if}{if $main_link} link{/if}"{if !$non_editable && !$no_rev} rev="opener_group{$id_prefix}{$id}"{/if}{if $main_link} href="{$main_link|fn_url}"{/if}>{$text}</a><span class="object-group-details">{$details}</span>
	</div>
</div>
{/if}
